<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\PFE\CreateTeamRequest;
use App\Http\Requests\PFE\UpdateTeamRequest;
use App\Http\Requests\PFE\TeamPreferenceRequest;
use App\Models\Team;
use App\Models\Subject;
use App\Models\User;
use App\Services\TeamService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TeamController extends Controller
{
    public function __construct(private TeamService $teamService)
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of teams
     */
    public function index(Request $request): View
    {
        $query = Team::with(['leader:id,first_name,last_name,department', 'members.user:id,first_name,last_name']);

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('department')) {
            $query->whereHas('leader', function ($q) use ($request) {
                $q->where('department', $request->department);
            });
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Role-based filtering
        $user = $request->user();
        if ($user->hasRole('student')) {
            $query->whereHas('members', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $teams = $query->paginate(15);

        return view('teams.index', [
            'teams' => $teams,
            'filters' => $request->only(['status', 'department', 'search'])
        ]);
    }

    /**
     * Show the form for creating a new team
     */
    public function create(): View
    {
        $this->authorize('create', Team::class);

        // Get available students for team formation
        $availableStudents = User::role('student')
            ->where('department', auth()->user()->department)
            ->whereDoesntHave('teamMemberships')
            ->where('id', '!=', auth()->id())
            ->select('id', 'first_name', 'last_name', 'student_id')
            ->get();

        return view('teams.create', [
            'availableStudents' => $availableStudents
        ]);
    }

    /**
     * Store a newly created team
     */
    public function store(CreateTeamRequest $request): RedirectResponse
    {
        $team = $this->teamService->createTeam(
            $request->validated(),
            $request->user()
        );

        return redirect()->route('teams.show', $team)
            ->with('success', 'Team created successfully');
    }

    /**
     * Display the specified team
     */
    public function show(Team $team): View
    {
        $this->authorize('view', $team);

        $team->load([
            'leader:id,first_name,last_name,email,student_id,department',
            'members.user:id,first_name,last_name,email,student_id',
            'subjectPreferences.subject:id,title,supervisor_id',
            'subjectPreferences.subject.supervisor:id,first_name,last_name',
            'project.subject:id,title'
        ]);

        return view('teams.show', [
            'team' => $team
        ]);
    }

    /**
     * Show the form for editing the specified team
     */
    public function edit(Team $team): View
    {
        $this->authorize('update', $team);

        return view('teams.edit', [
            'team' => $team
        ]);
    }

    /**
     * Update the specified team
     */
    public function update(UpdateTeamRequest $request, Team $team): RedirectResponse
    {
        $team->update($request->only(['name']));

        return redirect()->route('teams.show', $team)
            ->with('success', 'Team updated successfully');
    }

    /**
     * Add a member to the team
     */
    public function addMember(Request $request, Team $team): RedirectResponse
    {
        $this->authorize('manage', $team);

        $request->validate([
            'user_id' => 'required|integer|exists:users,id'
        ]);

        try {
            $this->teamService->addMember($team, $request->user_id);

            $user = User::find($request->user_id);
            return back()->with('success', $user->first_name . ' ' . $user->last_name . ' added to team');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove a member from the team
     */
    public function removeMember(Team $team, User $user): RedirectResponse
    {
        $this->authorize('manage', $team);

        try {
            $this->teamService->removeMember($team, $user->id);

            return back()->with('success', $user->first_name . ' ' . $user->last_name . ' removed from team');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show preferences form
     */
    public function showPreferences(Team $team): View
    {
        $this->authorize('manage', $team);

        $team->load('subjectPreferences.subject:id,title');
        $availableSubjects = Subject::where('status', 'published')
            ->whereDoesntHave('projects')
            ->with('supervisor:id,first_name,last_name')
            ->get();

        return view('teams.preferences', [
            'team' => $team,
            'availableSubjects' => $availableSubjects
        ]);
    }

    /**
     * Set team preferences
     */
    public function setPreferences(TeamPreferenceRequest $request, Team $team): RedirectResponse
    {
        // Clear existing preferences
        $team->subjectPreferences()->delete();

        // Add new preferences
        foreach ($request->preferences as $preference) {
            $team->subjectPreferences()->create([
                'subject_id' => $preference['subject_id'],
                'preference_order' => $preference['preference_order']
            ]);
        }

        return redirect()->route('teams.show', $team)
            ->with('success', 'Preferences saved successfully');
    }

    /**
     * Validate a team
     */
    public function validateTeam(Team $team): RedirectResponse
    {
        $this->authorize('validate', $team);

        try {
            $this->teamService->validateTeam($team);

            return back()->with('success', 'Team validated successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show user's team
     */
    public function myTeam(Request $request): View
    {
        $user = $request->user();
        $teamMembership = $user->teamMemberships()->with([
            'team.leader:id,first_name,last_name',
            'team.members.user:id,first_name,last_name,student_id',
            'team.project.subject:id,title'
        ])->first();

        return view('teams.my-team', [
            'teamMembership' => $teamMembership
        ]);
    }

    /**
     * Leave team
     */
    public function leave(Team $team): RedirectResponse
    {
        $user = auth()->user();

        // Check if user is team leader
        if ($team->leader_id === $user->id) {
            return back()->with('error', 'Team leader cannot leave the team');
        }

        try {
            $this->teamService->removeMember($team, $user->id);

            return redirect()->route('teams.my-team')
                ->with('success', 'You have left the team');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}