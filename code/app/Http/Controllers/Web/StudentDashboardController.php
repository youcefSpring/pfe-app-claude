<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PfeProject;
use App\Models\TeamMember;
use App\Models\Subject;
use App\Models\Defense;
use App\Models\Deliverable;
use App\Models\DefensePreparation;
use App\Models\Team;
use App\Models\SubjectPreference;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StudentDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:student');
    }

    /**
     * Student main dashboard
     */
    public function index(): View
    {
        $student = auth()->user();

        // Get current team and project
        $teamMember = TeamMember::where('user_id', $student->id)
            ->where('status', 'active')
            ->with(['team.project.supervisor', 'team.project.subject', 'team.project.defense'])
            ->first();

        $team = $teamMember?->team;
        $project = $team?->project;

        // Dashboard statistics
        $stats = $this->getDashboardStats($student, $team, $project);

        // Recent activities
        $recentActivities = $this->getRecentActivities($student, $project);

        // Upcoming deadlines
        $upcomingDeadlines = $this->getUpcomingDeadlines($project);

        // Quick actions based on current status
        $quickActions = $this->getQuickActions($student, $team, $project);

        // Progress indicators
        $progressIndicators = $this->getProgressIndicators($team, $project);

        return view('pfe.student.dashboard', [
            'student' => $student,
            'team' => $team,
            'project' => $project,
            'stats' => $stats,
            'recentActivities' => $recentActivities,
            'upcomingDeadlines' => $upcomingDeadlines,
            'quickActions' => $quickActions,
            'progressIndicators' => $progressIndicators,
            'teamStatus' => $team ? ucfirst($team->status) : 'No Team',
            'projectStatus' => $project ? ucfirst($project->status) : 'No Project',
            'defenseDate' => $project?->defense?->scheduled_at?->format('M j, Y'),
            'projectProgress' => $stats['completion_percentage'],
            'milestones' => $this->getMilestones($project),
            'todos' => $this->getTodos($student, $project)
        ]);
    }

    /**
     * Academic year overview
     */
    public function yearOverview(): View
    {
        $student = auth()->user();
        $currentYear = now()->year;

        // Timeline of major milestones
        $timeline = $this->getAcademicTimeline($currentYear);

        // Student's progress through the year
        $yearProgress = $this->getYearProgress($student);

        // Important dates and deadlines
        $importantDates = $this->getImportantDates($currentYear);

        return view('pfe.student.dashboard.year-overview', [
            'timeline' => $timeline,
            'year_progress' => $yearProgress,
            'important_dates' => $importantDates,
            'current_year' => $currentYear
        ]);
    }

    /**
     * Team status and collaboration hub
     */
    public function teamHub(): View
    {
        $student = auth()->user();
        $teamMember = TeamMember::where('user_id', $student->id)
            ->where('status', 'active')
            ->with(['team.members.user', 'team.project'])
            ->first();

        if (!$teamMember) {
            return view('pfe.student.dashboard.no-team');
        }

        $team = $teamMember->team;

        // Team collaboration metrics
        $collaborationMetrics = $this->getCollaborationMetrics($team);

        // Team activities and contributions
        $teamActivities = $this->getTeamActivities($team);

        // Communication tools and shared resources
        $sharedResources = $this->getSharedResources($team);

        return view('pfe.student.dashboard.team-hub', [
            'team' => $team,
            'team_member' => $teamMember,
            'collaboration_metrics' => $collaborationMetrics,
            'team_activities' => $teamActivities,
            'shared_resources' => $sharedResources
        ]);
    }

    /**
     * Project progress and deliverables tracking
     */
    public function projectProgress(): View
    {
        $student = auth()->user();
        $project = $this->getCurrentProject($student);

        if (!$project) {
            return view('pfe.student.dashboard.no-project');
        }

        // Detailed project progress
        $progressDetails = $this->getDetailedProgress($project);

        // Deliverables status
        $deliverables = $this->getDeliverablesStatus($project);

        // Supervisor feedback and recommendations
        $supervisorFeedback = $this->getSupervisorFeedback($project);

        // Project timeline and milestones
        $projectTimeline = $this->getProjectTimeline($project);

        return view('pfe.student.dashboard.project-progress', [
            'project' => $project,
            'progress_details' => $progressDetails,
            'deliverables' => $deliverables,
            'supervisor_feedback' => $supervisorFeedback,
            'project_timeline' => $projectTimeline
        ]);
    }

    /**
     * Defense preparation dashboard
     */
    public function defensePreparation(): View
    {
        $student = auth()->user();
        $project = $this->getCurrentProject($student);

        if (!$project) {
            return view('pfe.student.dashboard.no-project');
        }

        $defense = $project->defense;

        // Defense preparation status
        $preparationStatus = $this->getDefensePreparationStatus($project);

        // Defense checklist and requirements
        $defenseChecklist = $this->getDefenseChecklist($project);

        // Practice sessions and preparation activities
        $preparationActivities = $this->getPreparationActivities($project);

        // Defense logistics and final preparations
        $defenseLogistics = $this->getDefenseLogistics($defense);

        return view('pfe.student.dashboard.defense-preparation', [
            'project' => $project,
            'defense' => $defense,
            'preparation_status' => $preparationStatus,
            'defense_checklist' => $defenseChecklist,
            'preparation_activities' => $preparationActivities,
            'defense_logistics' => $defenseLogistics
        ]);
    }

    /**
     * Student profile and academic record
     */
    public function profile(): View
    {
        $student = auth()->user();

        // Academic record and achievements
        $academicRecord = $this->getAcademicRecord($student);

        // Skills and competencies
        $skillsProfile = $this->getSkillsProfile($student);

        // Participation history
        $participationHistory = $this->getParticipationHistory($student);

        return view('pfe.student.dashboard.profile', [
            'student' => $student,
            'academic_record' => $academicRecord,
            'skills_profile' => $skillsProfile,
            'participation_history' => $participationHistory
        ]);
    }

    /**
     * Get dashboard statistics
     */
    private function getDashboardStats($student, $team, $project): array
    {
        $stats = [
            'team_status' => $team ? $team->status : 'no_team',
            'project_status' => $project ? $project->status : 'no_project',
            'completion_percentage' => 0,
            'days_to_defense' => null,
            'pending_tasks' => 0,
            'total_deliverables' => 0,
            'approved_deliverables' => 0
        ];

        if ($project) {
            // Calculate project completion
            $totalDeliverables = $project->deliverables()->count();
            $approvedDeliverables = $project->deliverables()->where('status', 'approved')->count();

            $stats['total_deliverables'] = $totalDeliverables;
            $stats['approved_deliverables'] = $approvedDeliverables;
            $stats['completion_percentage'] = $totalDeliverables > 0
                ? intval(($approvedDeliverables / $totalDeliverables) * 100)
                : 0;

            // Days to defense
            if ($project->defense && $project->defense->scheduled_at) {
                $stats['days_to_defense'] = Carbon::parse($project->defense->scheduled_at)
                    ->diffInDays(now(), false);
            }

            // Pending tasks
            $stats['pending_tasks'] = $project->deliverables()
                ->whereIn('status', ['pending', 'revision_requested'])
                ->count();
        }

        return $stats;
    }

    /**
     * Get recent activities
     */
    private function getRecentActivities($student, $project): array
    {
        $activities = [];

        if ($project) {
            // Recent deliverable submissions
            $recentDeliverables = $project->deliverables()
                ->where('submitted_by', $student->id)
                ->orderBy('updated_at', 'desc')
                ->limit(5)
                ->get();

            foreach ($recentDeliverables as $deliverable) {
                $activities[] = [
                    'type' => 'deliverable',
                    'action' => 'submitted',
                    'item' => $deliverable->title,
                    'date' => $deliverable->updated_at,
                    'status' => $deliverable->status
                ];
            }

            // Recent team activities
            if ($project->team) {
                $teamActivities = TeamMember::where('team_id', $project->team->id)
                    ->where('updated_at', '>=', now()->subDays(7))
                    ->with('user')
                    ->orderBy('updated_at', 'desc')
                    ->get();

                foreach ($teamActivities as $activity) {
                    if ($activity->user->id !== $student->id) {
                        $activities[] = [
                            'type' => 'team',
                            'action' => 'joined',
                            'item' => $activity->user->name . ' joined the team',
                            'date' => $activity->updated_at,
                            'status' => 'info'
                        ];
                    }
                }
            }
        }

        // Sort by date and limit
        usort($activities, function($a, $b) {
            return $b['date'] <=> $a['date'];
        });

        return array_slice($activities, 0, 10);
    }

    /**
     * Get upcoming deadlines
     */
    private function getUpcomingDeadlines($project): array
    {
        $deadlines = [];

        if ($project) {
            // Deliverable deadlines
            $upcomingDeliverables = $project->deliverables()
                ->where('due_date', '>', now())
                ->whereIn('status', ['pending', 'revision_requested'])
                ->orderBy('due_date')
                ->get();

            foreach ($upcomingDeliverables as $deliverable) {
                $deadlines[] = [
                    'type' => 'deliverable',
                    'title' => $deliverable->title,
                    'date' => $deliverable->due_date,
                    'days_remaining' => Carbon::parse($deliverable->due_date)->diffInDays(now()),
                    'priority' => $this->getDeadlinePriority($deliverable->due_date)
                ];
            }

            // Defense date
            if ($project->defense && $project->defense->scheduled_at) {
                $deadlines[] = [
                    'type' => 'defense',
                    'title' => 'Defense Presentation',
                    'date' => $project->defense->scheduled_at,
                    'days_remaining' => Carbon::parse($project->defense->scheduled_at)->diffInDays(now()),
                    'priority' => 'high'
                ];
            }
        }

        return $deadlines;
    }

    /**
     * Get quick actions based on status
     */
    private function getQuickActions($student, $team, $project): array
    {
        $actions = [];

        if (!$team) {
            $actions[] = [
                'title' => 'Create or Join Team',
                'description' => 'Form a team to start your PFE journey',
                'route' => 'pfe.student.teams.index',
                'icon' => 'users',
                'type' => 'primary'
            ];
        } elseif (!$project) {
            $actions[] = [
                'title' => 'Select Subject Preferences',
                'description' => 'Choose your preferred project subjects',
                'route' => 'pfe.student.subjects.index',
                'icon' => 'clipboard-list',
                'type' => 'primary'
            ];
        } else {
            // Project-specific actions
            $pendingDeliverables = $project->deliverables()
                ->whereIn('status', ['pending', 'revision_requested'])
                ->count();

            if ($pendingDeliverables > 0) {
                $actions[] = [
                    'title' => 'Submit Deliverables',
                    'description' => "You have {$pendingDeliverables} pending deliverable(s)",
                    'route' => 'pfe.student.projects.deliverables',
                    'icon' => 'upload',
                    'type' => 'warning'
                ];
            }

            if ($project->status === 'in_progress') {
                $actions[] = [
                    'title' => 'View Project Progress',
                    'description' => 'Track your project development',
                    'route' => 'pfe.student.projects.show',
                    'icon' => 'chart-line',
                    'type' => 'info'
                ];
            }

            if ($project->defense && $project->defense->status === 'scheduled') {
                $actions[] = [
                    'title' => 'Prepare for Defense',
                    'description' => 'Review defense requirements and checklist',
                    'route' => 'pfe.student.defense.preparation',
                    'icon' => 'presentation-chart-bar',
                    'type' => 'success'
                ];
            }
        }

        return $actions;
    }

    /**
     * Get progress indicators
     */
    private function getProgressIndicators($team, $project): array
    {
        $indicators = [
            'team_formation' => ['status' => 'pending', 'percentage' => 0],
            'subject_selection' => ['status' => 'pending', 'percentage' => 0],
            'project_development' => ['status' => 'pending', 'percentage' => 0],
            'defense_preparation' => ['status' => 'pending', 'percentage' => 0]
        ];

        // Team formation
        if ($team) {
            $indicators['team_formation'] = [
                'status' => $team->status === 'confirmed' ? 'completed' : 'in_progress',
                'percentage' => $team->status === 'confirmed' ? 100 : 75
            ];
        }

        // Subject selection and project development
        if ($project) {
            $indicators['subject_selection'] = ['status' => 'completed', 'percentage' => 100];

            // Project development progress
            $totalDeliverables = $project->deliverables()->count();
            $approvedDeliverables = $project->deliverables()->where('status', 'approved')->count();
            $developmentPercentage = $totalDeliverables > 0
                ? intval(($approvedDeliverables / $totalDeliverables) * 100)
                : 0;

            $indicators['project_development'] = [
                'status' => $developmentPercentage >= 100 ? 'completed' : 'in_progress',
                'percentage' => $developmentPercentage
            ];

            // Defense preparation
            if ($project->defense) {
                $preparation = DefensePreparation::where('project_id', $project->id)->first();
                $defensePercentage = $preparation ? $preparation->completion_percentage : 0;

                $indicators['defense_preparation'] = [
                    'status' => $defensePercentage >= 80 ? 'completed' : 'in_progress',
                    'percentage' => $defensePercentage
                ];
            }
        }

        return $indicators;
    }

    /**
     * Get current project for student
     */
    private function getCurrentProject($student): ?PfeProject
    {
        $teamMember = TeamMember::where('user_id', $student->id)
            ->where('status', 'active')
            ->with('team.project')
            ->first();

        return $teamMember?->team?->project;
    }

    /**
     * Get deadline priority
     */
    private function getDeadlinePriority($deadline): string
    {
        $daysRemaining = Carbon::parse($deadline)->diffInDays(now());

        if ($daysRemaining <= 3) return 'high';
        if ($daysRemaining <= 7) return 'medium';
        return 'normal';
    }

    /**
     * Get project milestones
     */
    private function getMilestones($project): array
    {
        if (!$project) return [];

        return [
            ['title' => 'Project Planning', 'completed' => true],
            ['title' => 'Literature Review', 'completed' => true],
            ['title' => 'Design Phase', 'completed' => false],
            ['title' => 'Implementation', 'completed' => false],
            ['title' => 'Testing', 'completed' => false],
            ['title' => 'Documentation', 'completed' => false]
        ];
    }

    /**
     * Get student todos
     */
    private function getTodos($student, $project): array
    {
        $todos = [];

        if (!$project) {
            return [
                ['task' => 'Create or join a team', 'priority' => 'high', 'completed' => false],
                ['task' => 'Browse available subjects', 'priority' => 'medium', 'completed' => false]
            ];
        }

        // Get pending deliverables
        $pendingDeliverables = $project->deliverables()
            ->whereIn('status', ['pending', 'revision_requested'])
            ->limit(3)
            ->get();

        foreach ($pendingDeliverables as $deliverable) {
            $todos[] = [
                'task' => 'Submit ' . $deliverable->title,
                'priority' => 'high',
                'completed' => false
            ];
        }

        if (empty($todos)) {
            $todos[] = ['task' => 'All tasks completed!', 'priority' => 'low', 'completed' => true];
        }

        return $todos;
    }

    // Additional helper methods (placeholder implementations)
    private function getAcademicTimeline($year): array { return []; }
    private function getYearProgress($student): array { return []; }
    private function getImportantDates($year): array { return []; }
    private function getCollaborationMetrics($team): array { return []; }
    private function getTeamActivities($team): array { return []; }
    private function getSharedResources($team): array { return []; }
    private function getDetailedProgress($project): array { return []; }
    private function getDeliverablesStatus($project): array { return []; }
    private function getSupervisorFeedback($project): array { return []; }
    private function getProjectTimeline($project): array { return []; }
    private function getDefensePreparationStatus($project): array { return []; }
    private function getDefenseChecklist($project): array { return []; }
    private function getPreparationActivities($project): array { return []; }
    private function getDefenseLogistics($defense): array { return []; }
    private function getAcademicRecord($student): array { return []; }
    private function getSkillsProfile($student): array { return []; }
    private function getParticipationHistory($student): array { return []; }
}