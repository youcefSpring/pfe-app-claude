<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TeamController extends Controller
{
    /**
     * Display a listing of teams
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        $query = Team::with(['members.user', 'project.subject.teacher', 'project.subject.externalSupervisor']);

        // Apply search filter
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter based on user role
        switch ($user->role) {
            case 'student':
                // Students see all teams (for joining)
                break;
            case 'teacher':
                // Teachers see only teams that have chosen their subjects or external projects they supervise
                $query->where(function($q) use ($user) {
                    // Teams with internal projects using teacher's subjects
                    $q->whereHas('project.subject', function($subq) use ($user) {
                        $subq->where('teacher_id', $user->id);
                    })
                    // OR teams with external projects where teacher is external supervisor
                    ->orWhereHas('project.subject', function($subq) use ($user) {
                        $subq->where('external_supervisor_id', $user->id);
                    });
                });
                break;
            case 'department_head':
                // Department heads see teams from their department
                $query->whereHas('members.user', function($q) use ($user) {
                    $q->where('department', $user->department);
                });
                break;
            // Admin sees all teams (no filter)
        }

        $teams = $query->latest()->paginate(12)->appends($request->query());

        return view('teams.index', compact('teams'));
    }

    /**
     * Display the student's team
     */
    public function myTeam(): View
    {
        $user = Auth::user();
        $teamMember = $user->teamMember;

        if (!$teamMember) {
            // Student is not in a team, show option to create or join one
            return view('teams.my-team', [
                'team' => null,
                'availableTeams' => Team::whereHas('members', function($query) {
                    $query->havingRaw('COUNT(*) < 2'); // Teams with less than 2 members
                })->with(['members.user'])->get()
            ]);
        }

        $team = $teamMember->team->load(['members.user', 'subject.teacher', 'project.supervisor']);

        return view('teams.my-team', compact('team'));
    }

    /**
     * Show the form for creating a new team
     */
    public function create(): View
    {
        //$this->authorize('create', Team::class);

        $user = Auth::user();

        // Check if user is already in a team
        if ($user->teamMember) {
            return redirect()->route('teams.index')
                ->with('error', __('app.already_member_cannot_create'));
        }

        // Generate next team name
        $nextTeamName = $this->generateNextTeamName();

        return view('teams.create', compact('nextTeamName'));
    }

    /**
     * Store a newly created team
     */
    public function store(Request $request): RedirectResponse
    {
        //$this->authorize('create', Team::class);

        $user = Auth::user();

        // Check if user is already in a team
        if ($user->teamMember) {
            return redirect()->back()
                ->with('error', __('app.already_member_of_team'));
        }

        DB::beginTransaction();
        try {
            // Generate unique team name
            $teamName = $this->generateNextTeamName();

            // Create team
            $team = Team::create([
                'name' => $teamName,
                'status' => 'forming'
            ]);

            // Add creator as team leader
            TeamMember::create([
                'team_id' => $team->id,
                'student_id' => $user->id,
                'role' => 'leader',
                'joined_at' => now()
            ]);

            DB::commit();

            return redirect()->route('teams.show', $team)
                ->with('success', __('app.team_created_leader'));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', __('app.team_create_failed', ['message' => $e->getMessage()]));
        }
    }

    /**
     * Display the specified team
     */
    public function show(Team $team): View
    {
        $team->load(['members.user', 'project.subject.teacher', 'project.subject.externalSupervisor']);

        $user = Auth::user();
        $isMember = $team->members->contains('student_id', $user->id);
        $isLeader = $team->members->where('student_id', $user->id)->where('role', 'leader')->isNotEmpty();

        $availableSubjects = Subject::where('status', 'validated')
            ->whereDoesntHave('projects')
            ->get();

        return view('teams.show', compact('team', 'isMember', 'isLeader', 'availableSubjects'));
    }

    /**
     * Show the form for editing the specified team
     */
    public function edit(Team $team): View
    {
        // //$this->authorize('update', $team);
        return view('teams.edit', compact('team'));
    }

    /**
     * Update the specified team
     */
    public function update(Request $request, Team $team): RedirectResponse
    {
        // //$this->authorize('update', $team);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:teams,name,' . $team->id,
        ]);

        $team->update($validated);

        return redirect()->route('teams.show', $team)
            ->with('success', __('app.team_updated'));
    }

    /**
     * Remove the specified team
     */
    public function destroy(Team $team): RedirectResponse
    {
        //$this->authorize('delete', $team);

        if ($team->project) {
            return redirect()->back()
                ->with('error', __('app.cannot_delete_team_with_project'));
        }

        $team->delete();

        return redirect()->route('teams.index')
            ->with('success', __('app.team_deleted'));
    }

    /**
     * Add a member to the team
     */
    public function addMember(Request $request, Team $team): RedirectResponse
    {
        //$this->authorize('addMember', $team);

        $request->validate([
            'student_email' => 'required|email|exists:users,email'
        ]);

        $student = User::where('email', $request->student_email)
            ->where('role', 'student')
            ->first();

        if (!$student) {
            return redirect()->back()
                ->with('error', __('app.student_not_found'));
        }

        if ($student->teamMember) {
            return redirect()->back()
                ->with('error', __('app.student_already_in_team'));
        }

        if ($team->members->count() >= 4) { // Max team size
            return redirect()->back()
                ->with('error', __('app.team_full_max_members'));
        }

        TeamMember::create([
            'team_id' => $team->id,
            'student_id' => $student->id,
            'role' => 'member',
            'joined_at' => now()
        ]);

        return redirect()->back()
            ->with('success', __('app.student_added_to_team'));
    }

    /**
     * Remove a member from the team
     */
    public function removeMember(Team $team, TeamMember $member): RedirectResponse
    {
        //$this->authorize('removeMember', [$team, $member]);

        if ($member->role === 'leader' && $team->members->count() > 1) {
            return redirect()->back()
                ->with('error', __('app.cannot_remove_leader'));
        }

        $member->delete();

        // If leader left and team is empty, delete team
        if ($team->members->count() === 0) {
            $team->delete();
            return redirect()->route('teams.index')
                ->with('success', __('app.team_dissolved_leader_left'));
        }

        return redirect()->back()
            ->with('success', __('app.member_removed_from_team'));
    }

    /**
     * Select a subject for the team
     */
    public function selectSubject(Request $request, Team $team): RedirectResponse
    {
        //$this->authorize('selectSubject', $team);

        $request->validate([
            'subject_id' => 'required|exists:subjects,id'
        ]);

        $subject = Subject::find($request->subject_id);

        if ($subject->status !== 'validated') {
            return redirect()->back()
                ->with('error', __('app.subject_not_validated'));
        }

        if ($subject->projects()->exists()) {
            return redirect()->back()
                ->with('error', __('app.subject_already_taken'));
        }

        if ($team->project) {
            return redirect()->back()
                ->with('error', __('app.team_already_has_project'));
        }

        // Create project for the team
        $team->project()->create([
            'subject_id' => $subject->id,
            'supervisor_id' => $subject->teacher_id,
            'status' => 'active',
            'start_date' => now(),
        ]);

        $team->update(['status' => 'active']);

        return redirect()->back()
            ->with('success', __('app.subject_selected_project_created'));
    }

    /**
     * Show form for external project submission
     */
    public function externalProjectForm(Team $team): View
    {
        //$this->authorize('selectSubject', $team);

        if ($team->project) {
            return redirect()->route('teams.show', $team)
                ->with('error', __('app.team_already_has_project'));
        }

        return view('teams.external-project', compact('team'));
    }

    /**
     * Submit external project proposal
     */
    public function submitExternalProject(Request $request, Team $team): RedirectResponse
    {
        //$this->authorize('selectSubject', $team);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'company_name' => 'required|string|max:255',
            'supervisor_name' => 'required|string|max:255',
            'supervisor_email' => 'required|email|max:255',
            'supervisor_phone' => 'nullable|string|max:20',
            'project_duration' => 'required|integer|min:1|max:12',
            'technologies' => 'required|string|max:500',
            'objectives' => 'required|string',
        ]);

        if ($team->project) {
            return redirect()->back()
                ->with('error', __('app.team_already_has_project'));
        }

        // Create external project
        $externalProject = $team->externalProject()->create($validated + [
            'status' => 'pending_approval',
            'submitted_at' => now()
        ]);

        return redirect()->route('teams.show', $team)
            ->with('success', __('app.external_project_submitted'));
    }

    /**
     * Join a team (for students)
     */
    public function join(Team $team): RedirectResponse
    {
        $user = Auth::user();

        if ($user->role !== 'student') {
            return redirect()->back()
                ->with('error', __('app.only_students_join_teams'));
        }

        if ($user->teamMember) {
            return redirect()->back()
                ->with('error', __('app.already_member_of_team'));
        }

        if ($team->members->count() >= 4) {
            return redirect()->back()
                ->with('error', __('app.team_full'));
        }

        TeamMember::create([
            'team_id' => $team->id,
            'student_id' => $user->id,
            'role' => 'member',
            'joined_at' => now()
        ]);

        return redirect()->route('teams.show', $team)
            ->with('success', __('app.joined_team_success'));
    }

    /**
     * Leave a team (for students)
     */
    public function leave(Team $team): RedirectResponse
    {
        $user = Auth::user();
        $membership = $team->members->where('student_id', $user->id)->first();

        if (!$membership) {
            return redirect()->back()
                ->with('error', __('app.not_team_member'));
        }

        DB::beginTransaction();
        try {
            // If this is the leader and there are other members, transfer leadership to the first member
            if ($membership->role === 'leader' && $team->members->count() > 1) {
                $newLeader = $team->members()->where('student_id', '!=', $user->id)->first();
                if ($newLeader) {
                    $newLeader->update(['role' => 'leader']);
                }
            }

            // Remove the member
            $membership->delete();

            // If last member left, delete team
            if ($team->fresh()->members->count() === 0) {
                $team->delete();
                DB::commit();
                return redirect()->route('teams.index')
                    ->with('success', __('app.left_team_dissolved'));
            }

            DB::commit();
            return redirect()->route('teams.index')
                ->with('success', __('app.left_team_success'));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', __('app.leave_team_failed', ['message' => $e->getMessage()]));
        }
    }

    /**
     * Transfer leadership
     */
    public function transferLeadership(Request $request, Team $team): RedirectResponse
    {
        //$this->authorize('transferLeadership', $team);

        $request->validate([
            'new_leader_id' => 'required|exists:team_members,student_id'
        ]);

        $currentLeader = $team->members->where('role', 'leader')->first();
        $newLeader = $team->members->where('student_id', $request->new_leader_id)->first();

        if (!$newLeader) {
            return redirect()->back()
                ->with('error', __('app.member_not_in_team'));
        }

        DB::beginTransaction();
        try {
            // Update roles
            $currentLeader->update(['role' => 'member']);
            $newLeader->update(['role' => 'leader']);

            DB::commit();

            return redirect()->back()
                ->with('success', __('app.leadership_transferred'));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', __('app.transfer_leadership_failed'));
        }
    }

    /**
     * Generate the next available team name
     */
    private function generateNextTeamName(): string
    {
        // Find the highest team number
        $lastTeamNumber = Team::where('name', 'LIKE', 'team-%')
            ->get()
            ->map(function ($team) {
                // Extract number from team name (e.g., 'team-5' -> 5)
                if (preg_match('/team-(\d+)/', $team->name, $matches)) {
                    return (int) $matches[1];
                }
                return 0;
            })
            ->max();

        // Generate next team name
        $nextNumber = ($lastTeamNumber ?? 0) + 1;
        $teamName = "team-{$nextNumber}";

        // Ensure uniqueness (in case of race conditions)
        while (Team::where('name', $teamName)->exists()) {
            $nextNumber++;
            $teamName = "team-{$nextNumber}";
        }

        return $teamName;
    }
}
