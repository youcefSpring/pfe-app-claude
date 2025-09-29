<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use App\Models\TeamInvitation;
use App\Services\NotificationService;
use App\Services\TeamService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StudentTeamController extends Controller
{
    public function __construct(
        private NotificationService $notificationService,
        private TeamService $teamService
    ) {
        $this->middleware('auth');
        $this->middleware('role:student');
    }

    /**
     * Team management dashboard for students
     */
    public function index(): View
    {
        $student = auth()->user();
        $currentTeam = $this->getCurrentTeam($student);
        $invitations = $this->getPendingInvitations($student);
        $availableStudents = $this->getAvailableStudents($student);
        $teamFormationStats = $this->getTeamFormationStats();

        return view('pfe.student.teams.index', [
            'current_team' => $currentTeam,
            'invitations' => $invitations,
            'available_students' => $availableStudents,
            'formation_stats' => $teamFormationStats,
            'can_form_team' => $this->canFormTeam($student)
        ]);
    }

    /**
     * Show team creation form
     */
    public function create(): View
    {
        $student = auth()->user();

        if (!$this->canFormTeam($student)) {
            return redirect()->route('pfe.student.teams.index')
                ->withErrors(['error' => 'You cannot create a team at this time.']);
        }

        $availableStudents = $this->getAvailableStudents($student);
        $teamSizeConfig = $this->getTeamSizeConfiguration();

        return view('pfe.student.teams.create', [
            'available_students' => $availableStudents,
            'team_size_config' => $teamSizeConfig
        ]);
    }

    /**
     * Store new team
     */
    public function store(Request $request): RedirectResponse
    {
        $student = auth()->user();

        if (!$this->canFormTeam($student)) {
            return back()->withErrors(['error' => 'You cannot create a team at this time.']);
        }

        $request->validate([
            'name' => 'required|string|max:100|unique:teams,name',
            'description' => 'nullable|string|max:500',
            'member_ids' => 'nullable|array|max:3', // Configurable max size
            'member_ids.*' => 'exists:users,id',
            'project_preferences' => 'nullable|string|max:1000'
        ]);

        DB::transaction(function () use ($request, $student) {
            // Create team with current user as leader
            $team = Team::create([
                'name' => $request->name,
                'description' => $request->description,
                'leader_id' => $student->id,
                'status' => 'forming',
                'project_preferences' => $request->project_preferences,
                'formation_completed_at' => null
            ]);

            // Add leader as team member
            TeamMember::create([
                'team_id' => $team->id,
                'user_id' => $student->id,
                'role' => 'leader',
                'joined_at' => now(),
                'status' => 'active'
            ]);

            // Send invitations to selected members
            if ($request->member_ids) {
                foreach ($request->member_ids as $memberId) {
                    if ($memberId != $student->id) {
                        $this->sendTeamInvitation($team, $memberId);
                    }
                }
            }

            session()->flash('success', 'Team created successfully! Invitations sent to selected members.');
        });

        return redirect()->route('pfe.student.teams.show', Team::where('leader_id', $student->id)->latest()->first());
    }

    /**
     * Show team details
     */
    public function show(Team $team): View
    {
        $this->authorize('view', $team);

        $team->load([
            'members.user',
            'leader',
            'subjectPreferences.subject.supervisor',
            'project'
        ]);

        $pendingInvitations = TeamInvitation::where('team_id', $team->id)
            ->where('status', 'pending')
            ->with('invitee')
            ->get();

        $teamProgress = $this->calculateTeamProgress($team);
        $compatibilityScore = $this->calculateTeamCompatibility($team);

        return view('pfe.student.teams.show', [
            'team' => $team,
            'pending_invitations' => $pendingInvitations,
            'team_progress' => $teamProgress,
            'compatibility_score' => $compatibilityScore,
            'can_edit' => $this->canEditTeam($team)
        ]);
    }

    /**
     * Invite student to join team
     */
    public function inviteMember(Request $request, Team $team): RedirectResponse
    {
        $this->authorize('manage', $team);

        $request->validate([
            'user_id' => [
                'required',
                'exists:users,id',
                Rule::unique('team_invitations')->where(function ($query) use ($team) {
                    return $query->where('team_id', $team->id)->where('status', 'pending');
                })
            ],
            'message' => 'nullable|string|max:500'
        ]);

        $invitee = User::findOrFail($request->user_id);

        // Check if user is available
        if (!$this->isStudentAvailable($invitee)) {
            return back()->withErrors(['error' => 'This student is not available to join teams.']);
        }

        $this->sendTeamInvitation($team, $request->user_id, $request->message);

        return back()->with('success', "Invitation sent to {$invitee->first_name} {$invitee->last_name}");
    }

    /**
     * Handle team invitation response
     */
    public function respondToInvitation(Request $request, TeamInvitation $invitation): RedirectResponse
    {
        if ($invitation->invitee_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'action' => 'required|in:accept,decline',
            'message' => 'nullable|string|max:500'
        ]);

        DB::transaction(function () use ($request, $invitation) {
            if ($request->action === 'accept') {
                // Check team size limit
                $currentSize = $invitation->team->members()->count();
                $maxSize = config('pfe.max_team_size', 4);

                if ($currentSize >= $maxSize) {
                    throw new \Exception('Team is already at maximum capacity.');
                }

                // Check if student is still available
                if (!$this->isStudentAvailable(auth()->user())) {
                    throw new \Exception('You are no longer available to join teams.');
                }

                // Add to team
                TeamMember::create([
                    'team_id' => $invitation->team_id,
                    'user_id' => $invitation->invitee_id,
                    'role' => 'member',
                    'joined_at' => now(),
                    'status' => 'active'
                ]);

                $invitation->update([
                    'status' => 'accepted',
                    'responded_at' => now(),
                    'response_message' => $request->message
                ]);

                // Notify team leader
                $this->notificationService->notify(
                    $invitation->team->leader,
                    'invitation_accepted',
                    'Team invitation accepted',
                    auth()->user()->first_name . ' ' . auth()->user()->last_name . ' joined your team',
                    ['team_id' => $invitation->team_id]
                );

                // Check if team formation is complete
                $this->checkTeamFormationCompletion($invitation->team);

            } else {
                $invitation->update([
                    'status' => 'declined',
                    'responded_at' => now(),
                    'response_message' => $request->message
                ]);

                // Notify team leader
                $this->notificationService->notify(
                    $invitation->team->leader,
                    'invitation_declined',
                    'Team invitation declined',
                    auth()->user()->first_name . ' ' . auth()->user()->last_name . ' declined to join your team',
                    ['team_id' => $invitation->team_id]
                );
            }
        });

        $message = $request->action === 'accept' ? 'Invitation accepted! You have joined the team.' : 'Invitation declined.';
        return redirect()->route('pfe.student.teams.index')->with('success', $message);
    }

    /**
     * Leave team
     */
    public function leaveTeam(Request $request): RedirectResponse
    {
        $student = auth()->user();
        $teamMember = TeamMember::where('user_id', $student->id)
            ->where('status', 'active')
            ->first();

        if (!$teamMember) {
            return back()->withErrors(['error' => 'You are not currently in a team.']);
        }

        $team = $teamMember->team;

        $request->validate([
            'reason' => 'required|string|max:500',
            'confirm' => 'required|accepted'
        ]);

        DB::transaction(function () use ($teamMember, $team, $request, $student) {
            if ($team->leader_id === $student->id) {
                // If leader is leaving, transfer leadership or disband team
                $newLeader = $team->members()
                    ->where('user_id', '!=', $student->id)
                    ->where('status', 'active')
                    ->first();

                if ($newLeader) {
                    $team->update(['leader_id' => $newLeader->user_id]);
                    $newLeader->update(['role' => 'leader']);

                    // Notify new leader
                    $this->notificationService->notify(
                        $newLeader->user,
                        'leadership_transferred',
                        'Team leadership transferred',
                        'You are now the leader of team ' . $team->name,
                        ['team_id' => $team->id]
                    );
                } else {
                    // Disband team if no other members
                    $team->update(['status' => 'disbanded']);
                }
            }

            // Update member status
            $teamMember->update([
                'status' => 'left',
                'left_at' => now(),
                'leave_reason' => $request->reason
            ]);

            // Notify remaining team members
            foreach ($team->members()->where('user_id', '!=', $student->id)->get() as $member) {
                $this->notificationService->notify(
                    $member->user,
                    'member_left_team',
                    'Team member left',
                    $student->first_name . ' ' . $student->last_name . ' left the team',
                    ['team_id' => $team->id]
                );
            }
        });

        return redirect()->route('pfe.student.teams.index')
            ->with('success', 'You have successfully left the team.');
    }

    /**
     * Remove member from team (leader only)
     */
    public function removeMember(Request $request, Team $team, User $member): RedirectResponse
    {
        $this->authorize('manage', $team);

        if ($team->leader_id !== auth()->id()) {
            return back()->withErrors(['error' => 'Only team leaders can remove members.']);
        }

        if ($member->id === auth()->id()) {
            return back()->withErrors(['error' => 'Leaders cannot remove themselves. Transfer leadership first.']);
        }

        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $teamMember = TeamMember::where('team_id', $team->id)
            ->where('user_id', $member->id)
            ->where('status', 'active')
            ->first();

        if (!$teamMember) {
            return back()->withErrors(['error' => 'User is not an active member of this team.']);
        }

        $teamMember->update([
            'status' => 'removed',
            'left_at' => now(),
            'leave_reason' => $request->reason
        ]);

        // Notify the removed member
        $this->notificationService->notify(
            $member,
            'removed_from_team',
            'Removed from team',
            "You have been removed from team {$team->name}",
            ['team_id' => $team->id]
        );

        return back()->with('success', 'Member removed from team successfully.');
    }

    /**
     * Get current team for student
     */
    private function getCurrentTeam($student): ?Team
    {
        $teamMember = TeamMember::where('user_id', $student->id)
            ->where('status', 'active')
            ->with(['team.members.user', 'team.leader', 'team.subjectPreferences.subject'])
            ->first();

        return $teamMember?->team;
    }

    /**
     * Get pending invitations for student
     */
    private function getPendingInvitations($student)
    {
        return TeamInvitation::where('invitee_id', $student->id)
            ->where('status', 'pending')
            ->with(['team.leader', 'team.members.user'])
            ->get();
    }

    /**
     * Get available students for team formation
     */
    private function getAvailableStudents($currentStudent)
    {
        return User::role('student')
            ->where('id', '!=', $currentStudent->id)
            ->where('is_active', true)
            ->whereDoesntHave('teamMemberships', function($query) {
                $query->where('status', 'active');
            })
            ->select('id', 'first_name', 'last_name', 'email', 'student_id', 'department')
            ->get();
    }

    /**
     * Check if student can form a team
     */
    private function canFormTeam($student): bool
    {
        // Check if already in a team
        $hasActiveTeam = TeamMember::where('user_id', $student->id)
            ->where('status', 'active')
            ->exists();

        if ($hasActiveTeam) {
            return false;
        }

        // Check if team formation period is open
        $formationDeadline = config('pfe.team_formation_deadline');
        if ($formationDeadline && now()->isAfter($formationDeadline)) {
            return false;
        }

        return true;
    }

    /**
     * Check if student is available to join teams
     */
    private function isStudentAvailable($student): bool
    {
        return !TeamMember::where('user_id', $student->id)
            ->where('status', 'active')
            ->exists();
    }

    /**
     * Send team invitation
     */
    private function sendTeamInvitation($team, $inviteeId, $message = null): void
    {
        $invitation = TeamInvitation::create([
            'team_id' => $team->id,
            'inviter_id' => auth()->id(),
            'invitee_id' => $inviteeId,
            'message' => $message,
            'status' => 'pending',
            'expires_at' => now()->addDays(7) // 7 days to respond
        ]);

        $invitee = User::find($inviteeId);
        $this->notificationService->notify(
            $invitee,
            'team_invitation',
            'Team invitation received',
            "You've been invited to join team '{$team->name}'",
            ['invitation_id' => $invitation->id, 'team_id' => $team->id]
        );
    }

    /**
     * Calculate team progress
     */
    private function calculateTeamProgress($team): array
    {
        $steps = [
            'formation' => $team->status !== 'forming',
            'preferences' => $team->subjectPreferences()->count() > 0,
            'assignment' => $team->project()->exists(),
            'project_start' => $team->project && $team->project->status !== 'assigned'
        ];

        $completedSteps = count(array_filter($steps));
        $totalSteps = count($steps);

        return [
            'steps' => $steps,
            'completed' => $completedSteps,
            'total' => $totalSteps,
            'percentage' => ($completedSteps / $totalSteps) * 100
        ];
    }

    /**
     * Calculate team compatibility score
     */
    private function calculateTeamCompatibility($team): array
    {
        $members = $team->members()->with('user')->get();

        // Department diversity
        $departments = $members->pluck('user.department')->unique();
        $departmentScore = min(100, $departments->count() * 25);

        // Team size optimization
        $size = $members->count();
        $sizeScore = $size >= 3 && $size <= 4 ? 100 : max(0, 100 - abs(3.5 - $size) * 25);

        // Formation timing
        $formationDays = $team->created_at->diffInDays(now());
        $timingScore = max(0, 100 - $formationDays * 2);

        $overallScore = ($departmentScore + $sizeScore + $timingScore) / 3;

        return [
            'overall' => round($overallScore),
            'factors' => [
                'department_diversity' => round($departmentScore),
                'team_size' => round($sizeScore),
                'formation_timing' => round($timingScore)
            ]
        ];
    }

    /**
     * Check if team can be edited
     */
    private function canEditTeam($team): bool
    {
        return $team->leader_id === auth()->id() &&
               $team->status === 'forming' &&
               !$team->project()->exists();
    }

    /**
     * Check team formation completion
     */
    private function checkTeamFormationCompletion($team): void
    {
        $minSize = config('pfe.min_team_size', 2);
        $currentSize = $team->members()->where('status', 'active')->count();

        if ($currentSize >= $minSize && $team->status === 'forming') {
            $team->update([
                'status' => 'formed',
                'formation_completed_at' => now()
            ]);

            // Notify team members
            foreach ($team->members as $member) {
                $this->notificationService->notify(
                    $member->user,
                    'team_formation_complete',
                    'Team formation completed',
                    "Team {$team->name} is now ready for subject selection",
                    ['team_id' => $team->id]
                );
            }
        }
    }

    /**
     * Get team formation statistics
     */
    private function getTeamFormationStats(): array
    {
        $totalStudents = User::role('student')->where('is_active', true)->count();
        $studentsInTeams = TeamMember::where('status', 'active')->distinct('user_id')->count();
        $totalTeams = Team::whereIn('status', ['forming', 'formed', 'validated'])->count();

        return [
            'total_students' => $totalStudents,
            'students_in_teams' => $studentsInTeams,
            'students_without_teams' => $totalStudents - $studentsInTeams,
            'total_teams' => $totalTeams,
            'formation_rate' => $totalStudents > 0 ? ($studentsInTeams / $totalStudents) * 100 : 0
        ];
    }

    /**
     * Get team size configuration
     */
    private function getTeamSizeConfiguration(): array
    {
        return [
            'min_size' => config('pfe.min_team_size', 2),
            'max_size' => config('pfe.max_team_size', 4),
            'recommended_size' => config('pfe.recommended_team_size', 3)
        ];
    }

    /**
     * Show current student's team details
     */
    public function myTeam(): View
    {
        $student = auth()->user();
        $team = $this->getCurrentTeam($student);

        if (!$team) {
            return view('pfe.student.teams.my-team', [
                'team' => null
            ]);
        }

        // Get team leader
        $teamLeader = $team->members()->where('role', 'leader')->first()?->user;

        // Calculate team progress
        $teamProgress = $this->calculateTeamProgress($team);

        // Get completed and pending tasks
        $completedTasks = $this->getCompletedTasks($team);
        $pendingTasks = $this->getPendingTasks($team);

        // Get recent team activities
        $recentActivities = $this->getTeamActivities($team);

        // Check permissions
        $canInvite = $this->canInviteMembers($student, $team);
        $isLeader = $team->leader_id === $student->id;

        return view('pfe.student.teams.my-team', [
            'team' => $team,
            'teamLeader' => $teamLeader,
            'teamProgress' => $teamProgress,
            'completedTasks' => $completedTasks,
            'pendingTasks' => $pendingTasks,
            'recentActivities' => $recentActivities,
            'canInvite' => $canInvite,
            'isLeader' => $isLeader,
            'maxMembers' => config('pfe.max_team_size', 3)
        ]);
    }

    /**
     * Calculate team progress percentage
     */
    private function calculateTeamProgress($team): int
    {
        $totalSteps = 5; // Team formation, member recruitment, subject selection, project assignment, etc.
        $completedSteps = 1; // Team exists

        if ($team->members->count() >= config('pfe.min_team_size', 2)) {
            $completedSteps++;
        }

        if ($team->status === 'validated') {
            $completedSteps++;
        }

        if ($team->project) {
            $completedSteps += 2;
        }

        return intval(($completedSteps / $totalSteps) * 100);
    }

    /**
     * Get completed tasks for team
     */
    private function getCompletedTasks($team): int
    {
        $completed = 0;

        if ($team->members->count() >= config('pfe.min_team_size', 2)) {
            $completed++;
        }

        if ($team->status === 'validated') {
            $completed++;
        }

        if ($team->project) {
            $completed++;
        }

        return $completed;
    }

    /**
     * Get pending tasks for team
     */
    private function getPendingTasks($team): int
    {
        $pending = 0;

        if ($team->members->count() < config('pfe.max_team_size', 3)) {
            $pending++;
        }

        if ($team->status !== 'validated') {
            $pending++;
        }

        if (!$team->project) {
            $pending++;
        }

        return $pending;
    }

    /**
     * Get recent team activities
     */
    private function getTeamActivities($team): array
    {
        $activities = [];

        // Recent member joins
        $recentMembers = $team->members()->where('created_at', '>=', now()->subWeek())->with('user')->get();

        foreach ($recentMembers as $member) {
            $activities[] = [
                'member' => $member->user->first_name . ' ' . $member->user->last_name,
                'action' => 'joined the team',
                'date' => $member->created_at->diffForHumans(),
                'icon' => 'fa-user-plus',
                'color' => 'success'
            ];
        }

        // Sort by most recent
        usort($activities, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return array_slice($activities, 0, 5);
    }

    /**
     * Check if student can invite members
     */
    private function canInviteMembers($student, $team): bool
    {
        return $team->leader_id === $student->id &&
               $team->members->count() < config('pfe.max_team_size', 3) &&
               in_array($team->status, ['forming', 'formed']);
    }
}