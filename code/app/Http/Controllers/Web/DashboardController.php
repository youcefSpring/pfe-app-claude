<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{

    /**
     * Display the main dashboard.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        // Redirect students to their specific dashboard with marks and alerts
        if ($user->role === 'student') {
            return redirect()->route('student.dashboard');
        }

        // Get role-specific dashboard data
        $dashboardData = $this->getDashboardDataForRole($user);

        return view('dashboard.index', [
            'user' => $user,
            'dashboardData' => $dashboardData,
        ]);
    }

    /**
     * Display student dashboard.
     */
    public function student(Request $request): View
    {
        $user = $request->user();
        $team = $user->teamMember?->team;

        $data = [
            'team' => $team?->load(['members.user', 'subject.teacher', 'project.supervisor']),
            'availableSubjects' => $team ? [] : \App\Models\Subject::where('status', 'validated')->whereDoesntHave('projects')->get(),
            'recentActivity' => $this->getRecentActivityForStudent($user),
            'upcomingDeadlines' => $this->getUpcomingDeadlines($user),
        ];

        return view('dashboard.student', compact('user', 'data'));
    }

    /**
     * Display teacher dashboard.
     */
    public function teacher(Request $request): View
    {
        $user = $request->user();

        $data = [
            'subjects' => $user->subjects()->get(),
            'supervisedProjects' => $user->supervisedProjects()
                ->with(['team.members.user', 'submissions'])
                ->get(),
            'pendingSubmissions' => $user->supervisedProjects()
                ->whereHas('submissions', function ($q) {
                    $q->where('status', 'submitted');
                })
                ->count(),
            'workloadStats' => $this->getTeacherWorkloadStats($user),
        ];

        return view('dashboard.teacher', compact('user', 'data'));
    }

    /**
     * Display department head dashboard.
     */
    public function departmentHead(Request $request): View
    {
        $user = $request->user();
        $department = $user->department;

        $data = [
            'pendingSubjects' => \App\Models\Subject::where('status', 'pending_validation')
                ->whereHas('teacher', function ($q) use ($department) {
                    $q->where('department', $department);
                })
                ->with('teacher')
                ->get(),
            'pendingConflicts' => [], // Implement if SubjectConflict model exists
            'departmentStats' => $this->getDepartmentStats($department),
            'recentValidations' => \App\Models\Subject::where('validated_by', $user->id)
                ->whereNotNull('validated_at')
                ->latest('validated_at')
                ->take(10)
                ->get(),
        ];

        return view('dashboard.department-head', compact('user', 'data'));
    }

    /**
     * Display admin dashboard.
     */
    public function admin(Request $request): View
    {
        $user = $request->user();

        $data = [
            'systemStats' => $this->getSystemStats(),
            'readyProjects' => \App\Models\Project::where('status', 'active')
                ->whereDoesntHave('defense')
                ->with(['team.members.user', 'supervisor'])
                ->get(),
            'recentDefenses' => \App\Models\Defense::latest()
                ->take(10)
                ->with(['project.team', 'room'])
                ->get(),
            'pendingApprovals' => $this->getPendingApprovals(),
        ];

        return view('dashboard.admin', compact('user', 'data'));
    }

    /**
     * Get dashboard data based on user role.
     */
    private function getDashboardDataForRole($user): array
    {
        switch ($user->role) {
            case 'student':
                return $this->getStudentDashboardData($user);

            case 'teacher':
                return $this->getTeacherDashboardData($user);

            case 'department_head':
                return $this->getDepartmentHeadDashboardData($user);

            case 'admin':
                return $this->getAdminDashboardData($user);

            default:
                return [];
        }
    }

    /**
     * Get student-specific dashboard data.
     */
    private function getStudentDashboardData($user): array
    {
        $team = $user->teamMember?->team;

        return [
            'hasTeam' => $team !== null,
            'teamStatus' => $team?->status,
            'teamProgress' => $team ? $this->calculateTeamProgress($team) : 0,
            'nextActions' => $this->getNextActionsForStudent($user, $team),
        ];
    }

    /**
     * Get teacher-specific dashboard data.
     */
    private function getTeacherDashboardData($user): array
    {
        return [
            'subjectsCount' => $user->subjects()->count(),
            'supervisedProjectsCount' => $user->supervisedProjects()->count(),
            'currentWorkload' => $user->supervisedProjects()->where('status', 'active')->count(),
            'pendingReviews' => $this->getPendingReviewsForTeacher($user),
        ];
    }

    /**
     * Get department head-specific dashboard data.
     */
    private function getDepartmentHeadDashboardData($user): array
    {
        $department = $user->department;

        return [
            'pendingValidations' => \App\Models\Subject::pendingValidation()
                ->whereHas('teacher', function ($q) use ($department) {
                    $q->where('department', $department);
                })
                ->count(),
            'pendingConflicts' => \App\Models\SubjectConflict::pending()
                ->whereHas('subject.teacher', function ($q) use ($department) {
                    $q->where('department', $department);
                })
                ->count(),
        ];
    }

    /**
     * Get admin-specific dashboard data.
     */
    private function getAdminDashboardData($user): array
    {
        return [
            'totalUsers' => \App\Models\User::count(),
            'activeProjects' => \App\Models\Project::where('status', 'in_progress')->count(),
            'scheduledDefenses' => \App\Models\Defense::where('status', 'scheduled')->count(),
            'systemHealth' => $this->getSystemHealthStatus(),
        ];
    }

    /**
     * Helper methods for calculating various metrics.
     */
    private function calculateTeamProgress($team): int
    {
        $progress = 0;

        // Team formation (20%)
        if ($team->status !== 'forming') {
            $progress += 20;
        }

        // Subject selection (30%)
        if ($team->subject_id || $team->externalProject) {
            $progress += 30;
        }

        // Project assignment (20%)
        if ($team->project) {
            $progress += 20;
        }

        // Project in progress (20%)
        if ($team->project && $team->project->status === 'in_progress') {
            $progress += 20;
        }

        // Defense scheduled/completed (10%)
        if ($team->project && $team->project->defense) {
            $progress += 10;
        }

        return $progress;
    }

    private function getNextActionsForStudent($user, $team): array
    {
        if (!$team) {
            return ['Create or join a team'];
        }

        $actions = [];

        if ($team->status === 'forming') {
            $actions[] = 'Complete team formation';
        } elseif ($team->status === 'complete' && !$team->subject_id && !$team->externalProject) {
            $actions[] = 'Select a subject or submit external project proposal';
        }

        return $actions;
    }

    private function getSystemHealthStatus(): array
    {
        return [
            'database' => 'healthy',
            'cache' => 'healthy',
            'storage' => 'healthy',
            'mail' => 'healthy',
        ];
    }

    private function getRecentActivityForStudent($user): array
    {
        return []; // Placeholder - implement based on activity tracking needs
    }

    private function getUpcomingDeadlines($user): array
    {
        return []; // Placeholder - implement based on deadline tracking needs
    }

    private function getTeacherWorkloadStats($user): array
    {
        return [
            'total_subjects' => $user->subjects()->count(),
            'active_projects' => $user->supervisedProjects()->where('status', 'active')->count(),
            'pending_submissions' => $user->supervisedProjects()
                ->whereHas('submissions', function($q) {
                    $q->where('status', 'submitted');
                })->count(),
            'upcoming_defenses' => 0 // Implement if needed
        ];
    }

    private function getDepartmentStats($department): array
    {
        return [
            'total_projects' => \App\Models\Project::whereHas('team.members.user', function($q) use ($department) {
                $q->where('department', $department);
            })->count(),
            'active_projects' => \App\Models\Project::where('status', 'active')
                ->whereHas('team.members.user', function($q) use ($department) {
                    $q->where('department', $department);
                })->count(),
            'completed_projects' => \App\Models\Project::where('status', 'completed')
                ->whereHas('team.members.user', function($q) use ($department) {
                    $q->where('department', $department);
                })->count(),
        ];
    }

    private function getSystemStats(): array
    {
        return [
            'total_users' => \App\Models\User::count(),
            'total_projects' => \App\Models\Project::count(),
            'active_projects' => \App\Models\Project::where('status', 'active')->count(),
            'completed_projects' => \App\Models\Project::where('status', 'completed')->count(),
            'total_subjects' => \App\Models\Subject::count(),
            'validated_subjects' => \App\Models\Subject::where('status', 'validated')->count(),
        ];
    }

    private function getPendingApprovals(): array
    {
        return [
            'subjects' => \App\Models\Subject::where('status', 'pending_validation')->count(),
            'external_projects' => 0, // Implement if external projects model exists
        ];
    }

    private function getPendingReviewsForTeacher($user): int
    {
        return $user->supervisedProjects()
            ->whereHas('submissions', function($q) {
                $q->where('status', 'submitted');
            })->count();
    }
}
