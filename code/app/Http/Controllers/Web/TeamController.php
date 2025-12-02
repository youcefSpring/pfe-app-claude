<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\TeamInvitation;
use App\Models\User;
use App\Models\Subject;
use App\Models\AllocationDeadline;
use App\Models\TeamSubjectPreference;
use App\Models\SubjectRequest;
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
                $q->where('name', 'like', '%' . $request->search . '%');
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

        // Check deadline restrictions
        $currentDeadline = AllocationDeadline::active()->first();
        $canModifyTeams = $currentDeadline && $currentDeadline->canStudentsChoose();

        return view('teams.index', compact('teams', 'currentDeadline', 'canModifyTeams'));
    }

    /**
     * Display the student's team
     */
    public function myTeam(): View
    {
        $user = Auth::user();
        $team = $user->getTeam();

        if (!$team) {
            // Student is not in a team, show option to create or join one
            $availableTeams = Team::withCount('members')
                ->having('members_count', '<', 2)
                ->with(['members.user'])
                ->get();

            return view('teams.my-team', [
                'team' => null,
                'availableTeams' => $availableTeams
            ]);
        }

        $team->load(['members.user', 'subject.teacher', 'project.supervisor']);

        return view('teams.my-team', compact('team'));
    }

    /**
     * Show the form for creating a new team
     */
    public function create(): View|RedirectResponse
    {
        $this->authorize('create', Team::class);

        $user = Auth::user();

        // Check deadline restrictions
        $currentDeadline = AllocationDeadline::active()->first();
        if (!$currentDeadline || !$currentDeadline->canStudentsChoose()) {
            return redirect()->route('teams.index')
                ->with('error', __('app.team_creation_period_ended'));
        }

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
        $this->authorize('create', Team::class);

        $user = Auth::user();

        // CHECK SETTINGS: Team formation enabled
        if (!\App\Services\SettingsService::isTeamFormationEnabled()) {
            return redirect()->back()
                ->with('error', __('app.team_formation_disabled'));
        }

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
        $team->load([
            'members.user',
            'project.subject.teacher',
            'project.subject.externalSupervisor',
            'externalProject.assignedSupervisor'
        ]);

        $user = Auth::user();
        $isMember = $team->members->contains('student_id', $user->id);
        $isLeader = $team->members->where('student_id', $user->id)->where('role', 'leader')->isNotEmpty();
        $hasLeader = $team->members->where('role', 'leader')->isNotEmpty();

        $subjectsQuery = Subject::where('status', 'validated')
            ->whereDoesntHave('projects');

        // Apply speciality filter only if speciality relationships exist
        $hasSpecialityRelationships = \DB::table('subject_specialities')->exists();

        if ($hasSpecialityRelationships) {
            // Get all team members' speciality IDs
            $teamSpecialityIds = $team->members()
                ->with('user')
                ->get()
                ->pluck('user.speciality_id')
                ->filter()
                ->unique();

            if ($teamSpecialityIds->isNotEmpty()) {
                $subjectsQuery->whereHas('specialities', function($q) use ($teamSpecialityIds) {
                    $q->whereIn('specialities.id', $teamSpecialityIds);
                });
            }
            // If team has no specialities but relationships exist, don't show any subjects
        }
        // If no speciality relationships exist, show all validated subjects

        $availableSubjects = $subjectsQuery->get();

        return view('teams.show', compact('team', 'isMember', 'isLeader', 'hasLeader', 'availableSubjects'));
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
            ->with('success', __('app.team_updated'));
    }

    /**
     * Remove the specified team
     */
    public function destroy(Team $team): RedirectResponse
    {
        $this->authorize('delete', $team);

        $user = Auth::user();

        // Only allow team deletion by admin or the sole team member
        if ($user->role !== 'admin') {
            $member = $team->members->where('student_id', $user->id)->first();

            if (!$member) {
                return redirect()->back()
                    ->with('error', __('app.not_authorized_to_delete_team'));
            }

            // Check if user is the only member and is the leader
            if ($team->members->count() > 1 || $member->role !== 'leader') {
                return redirect()->back()
                    ->with('error', __('app.cannot_delete_team_with_members'));
            }
        }

        // Check if team can be deleted
        if (!$team->canBeDeleted()) {
            return redirect()->back()
                ->with('error', __('app.cannot_delete_team'));
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
        $this->authorize('addMember', $team);

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

        // CHECK SETTINGS: Respect max team size based on student level
        $studentLevel = $student->student_level ?? 'licence_3';
        $maxTeamSize = \App\Services\SettingsService::getMaxTeamSize($studentLevel);

        if ($team->members->count() >= $maxTeamSize) {
            return redirect()->back()
                ->with('error', __('app.team_full_max_members') . " ($maxTeamSize)");
        }

        $user = Auth::user();

        // If admin, add directly without invitation
        if ($user->role === 'admin') {
            TeamMember::create([
                'team_id' => $team->id,
                'student_id' => $student->id,
                'role' => 'member',
                'joined_at' => now()
            ]);

            return redirect()->back()
                ->with('success', __('app.member_added_successfully'));
        }

        $invitation = TeamInvitation::createInvitation($team, $request->student_email, $user);

        if (!$invitation) {
            return redirect()->back()
                ->with('error', __('app.failed_to_send_invitation'));
        }

        return redirect()->back()
            ->with('success', __('app.invitation_sent_to_student', ['email' => $request->student_email]));
    }

    /**
     * Remove a member from the team
     */
    public function removeMember(Team $team, TeamMember $member): RedirectResponse
    {
        $this->authorize('removeMember', [$team, $member]);

        // Prevent removing leader if there are other members, unless admin
        if ($member->role === 'leader' && $team->members->count() > 1) {
            if (Auth::user()->role !== 'admin') {
                return redirect()->back()
                    ->with('error', __('app.cannot_remove_leader'));
            }

            // If admin removes leader, assign new leader automatically
            $newLeader = $team->members()->where('id', '!=', $member->id)->first();
            if ($newLeader) {
                $newLeader->update(['role' => 'leader']);
            }
        }

        $member->delete();

        // If leader left and team is empty, delete team
        if ($team->members()->count() === 0) {
            $team->delete();
            return redirect()->route('teams.index')
                ->with('success', __('app.team_dissolved_leader_left'));
        }

        return redirect()->back()
            ->with('success', __('app.member_removed_from_team'));
    }

    /**
     * Show the form for managing subject preferences for the team
     */
    public function selectSubjectForm(Team $team): View
    {
        $user = Auth::user();

        // Check if user is team member
        $member = $team->members->where('student_id', $user->id)->first();
        if (!$member) {
            abort(403, __('app.only_team_members_can_select_subjects'));
        }

        // Check deadline restrictions
        $currentDeadline = AllocationDeadline::active()->first();
        if (!$currentDeadline || !$currentDeadline->canStudentsChoose()) {
            return redirect()->route('teams.show', $team)
                ->with('error', __('app.subject_selection_period_ended'));
        }

        // Check if team can manage preferences
        if (!$team->canManagePreferences()) {
            // Get team leader's academic level to determine appropriate team size limits
            $leader = $team->members->where('role', 'leader')->first();
            $academicLevel = 'licence'; // default

            if ($leader && $leader->student) {
                $academicLevel = match($leader->student->student_level) {
                    'licence_3' => 'licence',
                    'master_1', 'master_2' => 'master',
                    default => 'licence'
                };
            }

            $minSize = config("team.sizes.{$academicLevel}.min", 1);
            $maxSize = config("team.sizes.{$academicLevel}.max", 4);
            $currentSize = $team->members->count();

            return redirect()->route('teams.show', $team)
                ->with('error', __('app.team_size_invalid_for_selection', [
                    'min' => $minSize,
                    'max' => $maxSize,
                    'current' => $currentSize
                ]));
        }

        // Get available validated subjects for the team's academic level
        $leader = $team->members->where('role', 'leader')->first();
        $targetGrade = 'license'; // default

        if ($leader && $leader->student) {
            $targetGrade = match($leader->student->student_level) {
                'licence_3' => 'license',
                'master_1', 'master_2' => 'master',
                default => 'license'
            };
        }

        $subjectsQuery = Subject::where('status', 'validated')
            ->where('target_grade', $targetGrade);

        // Apply speciality filter only if speciality relationships exist
        $hasSpecialityRelationships = \DB::table('subject_specialities')->exists();

        if ($hasSpecialityRelationships) {
            // Get all team members' speciality IDs
            $teamSpecialityIds = $team->members()
                ->with('user')
                ->get()
                ->pluck('user.speciality_id')
                ->filter()
                ->unique();

            if ($teamSpecialityIds->isNotEmpty()) {
                $subjectsQuery->whereHas('specialities', function($q) use ($teamSpecialityIds) {
                    $q->whereIn('specialities.id', $teamSpecialityIds);
                });
            }
            // If team has no specialities but relationships exist, don't show any subjects
        }
        // If no speciality relationships exist, show all validated subjects with matching grade

        $availableSubjects = $subjectsQuery->with('teacher')->get();

        // Load team with preferences and order by submission date
        $team->load(['subjectPreferences.subject.teacher']);
        $currentPreferences = $team->subjectPreferences ?? collect();
        if ($currentPreferences->isNotEmpty()) {
            $currentPreferences = $currentPreferences->sortByDesc('selected_at');
        }

        return view('teams.subject-preferences', compact('team', 'availableSubjects', 'currentPreferences', 'currentDeadline'));
    }

    /**
     * Redirect old subject selection to preferences system
     * @deprecated Use preference system instead
     */
    public function selectSubject(Request $request, Team $team): RedirectResponse
    {
        // Redirect to the preference management system
        return redirect()->route('teams.subject-preferences', $team)
            ->with('info', __('app.use_preference_system_instead'));
    }

    /**
     * Show subject preferences management page
     */
    public function subjectPreferences(Team $team): View
    {
        $user = Auth::user();
        $isMember = $team->hasMember($user);

        if (!$isMember && $user->role !== 'admin') {
            abort(403, __('app.not_authorized'));
        }

        $team->load(['subjectPreferences.subject.teacher', 'members.user']);

        // Order preferences by preference order (1 to 10)
        $currentPreferences = $team->subjectPreferences ?? collect();
        if ($currentPreferences->isNotEmpty()) {
            $currentPreferences = $currentPreferences->sortBy('preference_order');
        }

        $subjectsQuery = Subject::where('status', 'validated')
            ->whereNotIn('id', $currentPreferences->pluck('subject_id'));

        // Apply speciality filter only if speciality relationships exist
        $hasSpecialityRelationships = \DB::table('subject_specialities')->exists();

        if ($hasSpecialityRelationships) {
            // Get all team members' speciality IDs
            $teamSpecialityIds = $team->members()
                ->with('user')
                ->get()
                ->pluck('user.speciality_id')
                ->filter()
                ->unique();

            if ($teamSpecialityIds->isNotEmpty()) {
                $subjectsQuery->whereHas('specialities', function($q) use ($teamSpecialityIds) {
                    $q->whereIn('specialities.id', $teamSpecialityIds);
                });
            }
            // If team has no specialities but relationships exist, show all subjects (fallback)
        }
        // If no speciality relationships exist, show all validated subjects

        $availableSubjects = $subjectsQuery->with('teacher')->get();

        $canManage = $team->canManagePreferences();

        return view('teams.subject-preferences', compact('team', 'availableSubjects', 'currentPreferences', 'canManage'));
    }

    /**
     * Add a subject to team preferences
     */
    public function addSubjectPreference(Request $request, Team $team): RedirectResponse
    {
        $user = Auth::user();

        // CHECK SETTINGS: Preferences enabled
        if (!\App\Services\SettingsService::arePreferencesEnabled()) {
            return redirect()->back()
                ->with('error', __('app.preferences_disabled'));
        }

        // Check authorization
        if (!$team->hasMember($user) && $user->role !== 'admin') {
            return redirect()->back()
                ->with('error', __('app.not_authorized'));
        }

        // Check if team can manage preferences
        if (!$team->canManagePreferences()) {
            return redirect()->back()
                ->with('error', __('app.cannot_manage_preferences'));
        }

        // CHECK SETTINGS: Max preferences limit
        $maxPreferences = \App\Services\SettingsService::getMaxPreferences();

        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'preference_order' => 'nullable|integer|min:1|max:' . $maxPreferences
        ]);

        // Check if max preferences reached
        if ($team->subjectPreferences()->count() >= $maxPreferences) {
            return redirect()->back()
                ->with('error', __('app.max_preferences_reached') . " ($maxPreferences)");
        }

        $subject = Subject::find($request->subject_id);

        // Determine preference order
        $order = $request->preference_order ?? ($team->subjectPreferences()->count() + 1);

        if ($team->addSubjectPreference($subject, $order, $user)) {
            return redirect()->back()
                ->with('success', __('app.subject_added_to_preferences'));
        }

        return redirect()->back()
            ->with('error', __('app.failed_to_add_subject'));
    }

    /**
     * Remove a subject from team preferences
     */
    public function removeSubjectPreference(Team $team, Subject $subject): RedirectResponse
    {
        $user = Auth::user();

        // Check authorization
        if (!$team->hasMember($user) && $user->role !== 'admin') {
            return redirect()->back()
                ->with('error', __('app.not_authorized'));
        }

        // Check if team can manage preferences
        if (!$team->canManagePreferences()) {
            return redirect()->back()
                ->with('error', __('app.cannot_manage_preferences'));
        }

        if ($team->removeSubjectPreference($subject)) {
            return redirect()->back()
                ->with('success', __('app.subject_removed_from_preferences'));
        }

        return redirect()->back()
            ->with('error', __('app.failed_to_remove_subject'));
    }

    /**
     * Update preference order
     */
    public function updatePreferenceOrder(Request $request, Team $team): RedirectResponse
    {
        $user = Auth::user();

        // Check authorization
        if (!$team->hasMember($user) && $user->role !== 'admin') {
            return redirect()->back()
                ->with('error', __('app.not_authorized'));
        }

        // Check if team can manage preferences
        if (!$team->canManagePreferences()) {
            return redirect()->back()
                ->with('error', __('app.cannot_manage_preferences'));
        }

        // ✅ FIXED: Use dynamic max from SettingsService
        $maxPreferences = \App\Services\SettingsService::getMaxPreferences();

        $request->validate([
            'subject_ids' => "required|array|max:{$maxPreferences}",
            'subject_ids.*' => 'exists:subjects,id'
        ]);

        \Log::info('Updating preference order', [
            'team_id' => $team->id,
            'user_id' => $user->id,
            'subject_ids' => $request->subject_ids
        ]);

        if ($team->updatePreferenceOrder($request->subject_ids)) {
            return redirect()->back()
                ->with('success', __('app.preference_order_updated'));
        }

        return redirect()->back()
            ->with('error', __('app.failed_to_update_order'));
    }

    /**
     * Show form for external project submission
     */
    public function externalProjectForm(Team $team): View|RedirectResponse
    {
        $this->authorize('selectSubject', $team);

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
                ->with('error', __('app.team_already_has_project'));
        }

        // Check if external project already exists
        if ($team->externalProject) {
            return redirect()->back()
                ->with('error', __('app.team_already_has_external_project'));
        }

        try {
            // Use transaction to ensure data consistency
            \DB::transaction(function () use ($team, $validated) {
                // Map form fields to database columns
                $projectData = [
                    'company' => $validated['company_name'],
                    'contact_person' => $validated['supervisor_name'],
                    'contact_email' => $validated['supervisor_email'],
                    'contact_phone' => $validated['supervisor_phone'] ?? null,
                    'project_description' => $validated['title'] . "\n\n" . $validated['description'] . "\n\nObjectives:\n" . $validated['objectives'] . "\n\nDuration: " . $validated['project_duration'] . " month(s)",
                    'technologies' => $validated['technologies'],
                    'status' => 'submitted',
                ];

                // Create external project
                $team->externalProject()->create($projectData);
            });

            return redirect()->route('teams.show', $team)
                ->with('success', __('app.external_project_submitted'));
        } catch (\Exception $e) {
            \Log::error('Failed to submit external project: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', __('app.external_project_submission_failed'));
        }
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

        // ✅ FIXED: Use SettingsService instead of hardcoded value
        $leader = $team->leader;
        $studentLevel = $leader ? $leader->student_level : 'licence_3';
        $maxSize = \App\Services\SettingsService::getMaxTeamSize($studentLevel);

        if ($team->members->count() >= $maxSize) {
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
        $this->authorize('transferLeadership', $team);

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
     * Assign a leader when team has no leader
     */
    public function assignLeader(Request $request, Team $team): RedirectResponse
    {
        $user = Auth::user();

        // Authorization: Admin can always assign, or team members can assign if no leader exists
        $canAssign = $user->role === 'admin' || $team->members->contains('student_id', $user->id);

        if (!$canAssign) {
            return redirect()->back()
                ->with('error', __('You are not authorized to assign a leader'));
        }

        $request->validate([
            'member_id' => 'required|exists:team_members,student_id'
        ]);

        // Check if team already has a leader
        $existingLeader = $team->members->where('role', 'leader')->first();
        if ($existingLeader) {
            return redirect()->back()
                ->with('error', __('Team already has a leader. Use transfer leadership instead.'));
        }

        $newLeader = $team->members->where('student_id', $request->member_id)->first();

        if (!$newLeader) {
            return redirect()->back()
                ->with('error', __('Member not found in team'));
        }

        DB::beginTransaction();
        try {
            $newLeader->update(['role' => 'leader']);

            DB::commit();

            return redirect()->back()
                ->with('success', __('Leader assigned successfully'));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', __('Failed to assign leader'));
        }
    }

    /**
     * Remove leader role (demote to member)
     */
    public function removeLeader(Request $request, Team $team): RedirectResponse
    {
        $user = Auth::user();

        // Authorization: Only admin can remove leader
        if ($user->role !== 'admin') {
            return redirect()->back()
                ->with('error', __('Only administrators can remove leader role'));
        }

        $leader = $team->members->where('role', 'leader')->first();

        if (!$leader) {
            return redirect()->back()
                ->with('error', __('Team has no leader'));
        }

        DB::beginTransaction();
        try {
            $leader->update(['role' => 'member']);

            DB::commit();

            return redirect()->back()
                ->with('success', __('Leader role removed. Team now has no leader.'));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', __('Failed to remove leader role'));
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

    /**
     * Request a subject for the team
     */
    public function requestSubject(Request $request, Team $team): RedirectResponse
    {
        $user = Auth::user();

        // Check if user is team member
        $member = $team->members->where('student_id', $user->id)->first();
        if (!$member) {
            return redirect()->back()
                ->with('error', __('app.only_team_members_can_request_subjects'));
        }

        // Check deadline restrictions
        $currentDeadline = AllocationDeadline::active()->first();
        if (!$currentDeadline || !$currentDeadline->canStudentsChoose()) {
            return redirect()->back()
                ->with('error', __('app.subject_request_period_ended'));
        }

        // Check if team can select subjects (size validation)
        if (!$team->canSelectSubject()) {
            $minSize = config('team.sizes.licence.min', 2);
            $maxSize = config('team.sizes.licence.max', 3);
            $currentSize = $team->members->count();

            return redirect()->back()
                ->with('error', __('app.team_size_invalid_for_selection', [
                    'min' => $minSize,
                    'max' => $maxSize,
                    'current' => $currentSize
                ]));
        }

        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'request_message' => 'nullable|string|max:1000'
        ]);

        $subject = Subject::find($request->subject_id);

        // Check if subject is available
        if ($subject->status !== 'validated') {
            return redirect()->back()
                ->with('error', __('app.subject_not_available_for_request'));
        }

        // Check if team already has a pending request for this subject
        $existingRequest = $team->subjectRequests()
            ->where('subject_id', $subject->id)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return redirect()->back()
                ->with('error', __('app.already_requested_this_subject'));
        }

        // Check if team already has approved requests (limit to reasonable number)
        $approvedRequests = $team->subjectRequests()->where('status', 'approved')->count();
        if ($approvedRequests >= 3) {
            return redirect()->back()
                ->with('error', __('app.max_approved_requests_reached'));
        }

        // Get next priority order for this team
        $nextOrder = $team->subjectRequests()->max('priority_order') + 1;

        // Create the request
        SubjectRequest::create([
            'team_id' => $team->id,
            'subject_id' => $subject->id,
            'requested_by' => $user->id,
            'priority_order' => $nextOrder,
            'request_message' => $request->request_message,
            'requested_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', __('app.subject_request_submitted', ['subject' => $subject->title]));
    }

    /**
     * Show team's subject requests
     */
    public function subjectRequests(Team $team): View
    {
        $user = Auth::user();

        // Check if user is team member
        if (!$team->hasMember($user) && $user->role !== 'admin') {
            abort(403, __('app.not_authorized'));
        }

        $team->load(['subjectRequests.subject.teacher', 'subjectRequests.requestedBy', 'subjectRequests.respondedBy']);

        return view('teams.subject-requests', compact('team'));
    }

    /**
     * Cancel a pending subject request
     */
    public function cancelSubjectRequest(Team $team, SubjectRequest $subjectRequest): RedirectResponse
    {
        $user = Auth::user();

        // Check if user is team member
        $member = $team->members->where('student_id', $user->id)->first();
        if (!$member) {
            return redirect()->back()
                ->with('error', __('app.only_team_members_can_cancel_requests'));
        }

        // Check if request belongs to this team
        if ($subjectRequest->team_id !== $team->id) {
            abort(403);
        }

        // Only pending requests can be cancelled
        if (!$subjectRequest->isPending()) {
            return redirect()->back()
                ->with('error', __('app.can_only_cancel_pending_requests'));
        }

        $subjectRequest->delete();

        return redirect()->back()
            ->with('success', __('app.subject_request_cancelled'));
    }

    /**
     * Update the order of subject requests for a team
     */
    public function updateSubjectRequestOrder(Request $request, Team $team): RedirectResponse
    {
        $user = Auth::user();

        // Check if user is team member
        $member = $team->members->where('student_id', $user->id)->first();
        if (!$member) {
            return redirect()->back()
                ->with('error', __('app.only_team_members_can_reorder_requests'));
        }

        $request->validate([
            'request_ids' => 'required|array',
            'request_ids.*' => 'exists:subject_requests,id'
        ]);

        // Verify all requests belong to this team
        $requestIds = $request->request_ids;
        $teamRequests = $team->subjectRequests()->whereIn('id', $requestIds)->pluck('id')->toArray();

        if (count($requestIds) !== count($teamRequests)) {
            return redirect()->back()
                ->with('error', __('app.invalid_request_ids'));
        }

        // Update priority order
        foreach ($requestIds as $index => $requestId) {
            SubjectRequest::where('id', $requestId)
                ->where('team_id', $team->id)
                ->update(['priority_order' => $index + 1]);
        }

        return redirect()->back()
            ->with('success', __('app.request_order_updated'));
    }

    /**
     * Show all subject requests ordered by date
     */
    public function allSubjectRequests(): View
    {
        $user = Auth::user();

        // Build base query for SubjectRequests
        $query = SubjectRequest::with(['team.members.user', 'subject.teacher', 'requestedBy', 'respondedBy']);

        // Get team preferences for students to show their ranked subjects
        $teamPreferences = collect();

        // Filter based on user role
        switch ($user->role) {
            case 'student':
                // Students see requests from their team + their team's preferences
                $teamMember = $user->teamMember;
                if (!$teamMember) {
                    // Student not in a team, show empty results
                    $query->whereRaw('1 = 0');
                } else {
                    $query->where('team_id', $teamMember->team_id);

                    // Also get the team's subject preferences to show their ranked choices
                    $teamPreferences = $teamMember->team->subjectPreferences()
                        ->with(['subject.teacher'])
                        ->orderBy('preference_order', 'asc')
                        ->get();
                }
                break;
            case 'teacher':
                // Teachers see requests for their subjects
                $query->whereHas('subject', function($q) use ($user) {
                    $q->where('teacher_id', $user->id);
                });
                break;
            case 'admin':
            case 'department_head':
                // Admins and department heads see all requests
                break;
            default:
                // Other roles see nothing
                $query->whereRaw('1 = 0');
        }

        // Order by requested date ascending (oldest first)
        $subjectRequests = $query->orderBy('requested_at', 'asc')->paginate(20);

        return view('subject-requests.index', compact('subjectRequests', 'teamPreferences'));
    }

    /**
     * Show invitation details
     */
    public function showInvitation(string $token): View
    {
        $invitation = TeamInvitation::where('token', $token)->firstOrFail();

        if (!$invitation->canRespond()) {
            abort(404, __('app.invitation_expired_or_invalid'));
        }

        $invitation->load(['team.members.user', 'invitedBy']);

        return view('teams.invitation', compact('invitation'));
    }

    /**
     * Accept team invitation
     */
    public function acceptInvitation(string $token): RedirectResponse
    {
        $invitation = TeamInvitation::where('token', $token)->firstOrFail();

        if (!$invitation->canRespond()) {
            return redirect()->route('teams.index')
                ->with('error', __('app.invitation_expired_or_invalid'));
        }

        $user = Auth::user();
        if ($user->email !== $invitation->invited_email) {
            return redirect()->route('teams.index')
                ->with('error', __('app.invitation_not_for_you'));
        }

        if ($user->teamMember) {
            return redirect()->route('teams.index')
                ->with('error', __('app.already_member_of_team'));
        }

        if ($invitation->accept()) {
            return redirect()->route('teams.show', $invitation->team)
                ->with('success', __('app.invitation_accepted_successfully'));
        }

        return redirect()->route('teams.index')
            ->with('error', __('app.failed_to_accept_invitation'));
    }

    /**
     * Decline team invitation
     */
    public function declineInvitation(string $token): RedirectResponse
    {
        $invitation = TeamInvitation::where('token', $token)->firstOrFail();

        if (!$invitation->canRespond()) {
            return redirect()->route('teams.index')
                ->with('error', __('app.invitation_expired_or_invalid'));
        }

        $user = Auth::user();
        if ($user->email !== $invitation->invited_email) {
            return redirect()->route('teams.index')
                ->with('error', __('app.invitation_not_for_you'));
        }

        if ($invitation->decline()) {
            return redirect()->route('teams.index')
                ->with('success', __('app.invitation_declined_successfully'));
        }

        return redirect()->route('teams.index')
            ->with('error', __('app.failed_to_decline_invitation'));
    }

    /**
     * Show user's pending invitations
     */
    public function myInvitations(): View
    {
        $user = Auth::user();

        $invitations = TeamInvitation::where('invited_email', $user->email)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->with(['team.members.user', 'invitedBy'])
            ->latest()
            ->get();

        return view('teams.my-invitations', compact('invitations'));
    }

    /**
     * Search for available students for team invitation
     */
    public function searchStudents(Request $request): \Illuminate\Http\JsonResponse
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $students = User::where('role', 'student')
            ->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%")
                  ->orWhere('matricule', 'LIKE', "%{$query}%");
            })
            ->whereDoesntHave('teamMember')
            ->select('id', 'name', 'email', 'matricule')
            ->limit(10)
            ->get()
            ->map(function($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'email' => $student->email,
                    'student_id' => $student->matricule,
                    'display' => $student->name . ' (' . $student->email . ')',
                ];
            });

        return response()->json($students);
    }
}
