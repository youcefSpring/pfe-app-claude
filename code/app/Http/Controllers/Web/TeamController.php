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
    public function index(): View
    {
        $user = Auth::user();

        $query = Team::with(['members.user', 'project.subject']);

        // Filter based on user role
        switch ($user->role) {
            case 'student':
                // Students see all teams (for joining)
                break;
            case 'teacher':
            case 'department_head':
                // Teachers and dept heads see teams from their department
                $query->whereHas('members.user', function($q) use ($user) {
                    $q->where('department', $user->department);
                });
                break;
            // Admin sees all teams (no filter)
        }

        $teams = $query->latest()->paginate(12);

        return view('teams.index', compact('teams'));
    }

    /**
     * Show the form for creating a new team
     */
    public function create(): View
    {
        $this->authorize('create', Team::class);
        return view('teams.create');
    }

    /**
     * Store a newly created team
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Team::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:teams,name',
        ]);

        $user = Auth::user();

        // Check if user is already in a team
        if ($user->teamMember) {
            return redirect()->back()
                ->with('error', 'You are already a member of a team.');
        }

        DB::beginTransaction();
        try {
            // Create team
            $team = Team::create([
                'name' => $validated['name'],
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
                ->with('success', 'Team created successfully! You are now the team leader.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to create team: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified team
     */
    public function show(Team $team): View
    {
        $team->load(['members.user', 'project.subject.teacher']);

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
        $this->authorize('update', $team);
        return view('teams.edit', compact('team'));
    }

    /**
     * Update the specified team
     */
    public function update(Request $request, Team $team): RedirectResponse
    {
        $this->authorize('update', $team);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:teams,name,' . $team->id,
        ]);

        $team->update($validated);

        return redirect()->route('teams.show', $team)
            ->with('success', 'Team updated successfully!');
    }

    /**
     * Remove the specified team
     */
    public function destroy(Team $team): RedirectResponse
    {
        $this->authorize('delete', $team);

        if ($team->project) {
            return redirect()->back()
                ->with('error', 'Cannot delete team with an associated project.');
        }

        $team->delete();

        return redirect()->route('teams.index')
            ->with('success', 'Team deleted successfully!');
    }

    /**
     * Add a member to the team
     */
    public function addMember(Request $request, Team $team): RedirectResponse
    {
        $this->authorize('addMember', $team);

        $request->validate([
            'student_email' => 'required|email|exists:users,email'
        ]);

        $student = User::where('email', $request->student_email)
            ->where('role', 'student')
            ->first();

        if (!$student) {
            return redirect()->back()
                ->with('error', 'Student not found or user is not a student.');
        }

        if ($student->teamMember) {
            return redirect()->back()
                ->with('error', 'Student is already a member of another team.');
        }

        if ($team->members->count() >= 4) { // Max team size
            return redirect()->back()
                ->with('error', 'Team is full. Maximum 4 members allowed.');
        }

        TeamMember::create([
            'team_id' => $team->id,
            'student_id' => $student->id,
            'role' => 'member',
            'joined_at' => now()
        ]);

        return redirect()->back()
            ->with('success', 'Student added to team successfully!');
    }

    /**
     * Remove a member from the team
     */
    public function removeMember(Team $team, TeamMember $member): RedirectResponse
    {
        $this->authorize('removeMember', [$team, $member]);

        if ($member->role === 'leader' && $team->members->count() > 1) {
            return redirect()->back()
                ->with('error', 'Cannot remove team leader. Transfer leadership first or dissolve the team.');
        }

        $member->delete();

        // If leader left and team is empty, delete team
        if ($team->members->count() === 0) {
            $team->delete();
            return redirect()->route('teams.index')
                ->with('success', 'Team dissolved as the leader left.');
        }

        return redirect()->back()
            ->with('success', 'Member removed from team successfully!');
    }

    /**
     * Select a subject for the team
     */
    public function selectSubject(Request $request, Team $team): RedirectResponse
    {
        $this->authorize('selectSubject', $team);

        $request->validate([
            'subject_id' => 'required|exists:subjects,id'
        ]);

        $subject = Subject::find($request->subject_id);

        if ($subject->status !== 'validated') {
            return redirect()->back()
                ->with('error', 'Selected subject is not validated.');
        }

        if ($subject->projects()->exists()) {
            return redirect()->back()
                ->with('error', 'Subject is already taken by another team.');
        }

        if ($team->project) {
            return redirect()->back()
                ->with('error', 'Team already has a project assigned.');
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
            ->with('success', 'Subject selected successfully! Your project has been created.');
    }

    /**
     * Show form for external project submission
     */
    public function externalProjectForm(Team $team): View
    {
        $this->authorize('selectSubject', $team);

        if ($team->project) {
            return redirect()->route('teams.show', $team)
                ->with('error', 'Team already has a project assigned.');
        }

        return view('teams.external-project', compact('team'));
    }

    /**
     * Submit external project proposal
     */
    public function submitExternalProject(Request $request, Team $team): RedirectResponse
    {
        $this->authorize('selectSubject', $team);

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
                ->with('error', 'Team already has a project assigned.');
        }

        // Create external project
        $externalProject = $team->externalProject()->create($validated + [
            'status' => 'pending_approval',
            'submitted_at' => now()
        ]);

        return redirect()->route('teams.show', $team)
            ->with('success', 'External project submitted for approval!');
    }

    /**
     * Join a team (for students)
     */
    public function join(Team $team): RedirectResponse
    {
        $user = Auth::user();

        if ($user->role !== 'student') {
            return redirect()->back()
                ->with('error', 'Only students can join teams.');
        }

        if ($user->teamMember) {
            return redirect()->back()
                ->with('error', 'You are already a member of a team.');
        }

        if ($team->members->count() >= 4) {
            return redirect()->back()
                ->with('error', 'Team is full.');
        }

        TeamMember::create([
            'team_id' => $team->id,
            'student_id' => $user->id,
            'role' => 'member',
            'joined_at' => now()
        ]);

        return redirect()->route('teams.show', $team)
            ->with('success', 'You have joined the team successfully!');
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
                ->with('error', 'You are not a member of this team.');
        }

        if ($membership->role === 'leader' && $team->members->count() > 1) {
            return redirect()->back()
                ->with('error', 'You cannot leave as team leader. Transfer leadership first.');
        }

        $membership->delete();

        // If last member left, delete team
        if ($team->members->count() === 0) {
            $team->delete();
            return redirect()->route('teams.index')
                ->with('success', 'You left the team and it has been dissolved.');
        }

        return redirect()->route('teams.index')
            ->with('success', 'You have left the team.');
    }

    /**
     * Transfer leadership
     */
    public function transferLeadership(Request $request, Team $team): RedirectResponse
    {
        $this->authorize('transferLeadership', $team);

        $request->validate([
            'new_leader_id' => 'required|exists:team_members,student_id'
        ]);

        $currentLeader = $team->members->where('role', 'leader')->first();
        $newLeader = $team->members->where('student_id', $request->new_leader_id)->first();

        if (!$newLeader) {
            return redirect()->back()
                ->with('error', 'Selected member is not part of this team.');
        }

        DB::beginTransaction();
        try {
            // Update roles
            $currentLeader->update(['role' => 'member']);
            $newLeader->update(['role' => 'leader']);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Leadership transferred successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to transfer leadership.');
        }
    }
}
