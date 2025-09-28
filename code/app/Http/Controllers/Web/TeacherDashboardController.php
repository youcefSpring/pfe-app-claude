<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\PfeProject;
use App\Models\Deliverable;
use App\Models\Defense;
use App\Services\ReportingService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class TeacherDashboardController extends Controller
{
    public function __construct(private ReportingService $reportingService)
    {
        $this->middleware('auth');
        $this->middleware('role:teacher');
    }

    /**
     * Enhanced teacher dashboard
     */
    public function index(): View
    {
        $teacher = auth()->user();

        // Get comprehensive statistics
        $stats = $this->getTeacherStats($teacher);

        // Get recent subjects
        $mySubjects = Subject::where('supervisor_id', $teacher->id)
            ->latest()
            ->take(5)
            ->get();

        // Get supervised projects
        $supervisedProjects = PfeProject::where('supervisor_id', $teacher->id)
            ->with(['team.members.user', 'subject', 'deliverables'])
            ->latest()
            ->take(5)
            ->get();

        // Get pending deliverables for review
        $pendingDeliverables = Deliverable::whereHas('project', function($query) use ($teacher) {
            $query->where('supervisor_id', $teacher->id);
        })
        ->where('status', 'submitted')
        ->with(['project.team', 'project.subject'])
        ->latest('submitted_at')
        ->take(10)
        ->get();

        // Get upcoming defenses where teacher is jury member
        $upcomingDefenses = Defense::where(function($q) use ($teacher) {
            $q->where('jury_president_id', $teacher->id)
              ->orWhere('jury_examiner_id', $teacher->id)
              ->orWhere('jury_supervisor_id', $teacher->id);
        })
        ->where('defense_date', '>=', now())
        ->with(['project.team', 'project.subject', 'room'])
        ->orderBy('defense_date')
        ->take(5)
        ->get();

        // Get recent activities
        $recentActivities = $this->getRecentActivities($teacher);

        // Get quick insights
        $insights = $this->getTeacherInsights($teacher);

        return view('pfe.teacher.dashboard', [
            'stats' => $stats,
            'my_subjects' => $mySubjects,
            'supervised_projects' => $supervisedProjects,
            'pending_deliverables' => $pendingDeliverables,
            'upcoming_defenses' => $upcomingDefenses,
            'recent_activities' => $recentActivities,
            'insights' => $insights
        ]);
    }

    /**
     * Get comprehensive teacher statistics
     */
    private function getTeacherStats($teacher): array
    {
        // Subjects stats
        $totalSubjects = Subject::where('supervisor_id', $teacher->id)->count();
        $approvedSubjects = Subject::where('supervisor_id', $teacher->id)
            ->where('status', 'approved')->count();
        $publishedSubjects = Subject::where('supervisor_id', $teacher->id)
            ->where('status', 'published')->count();
        $pendingSubjects = Subject::where('supervisor_id', $teacher->id)
            ->where('status', 'submitted')->count();

        // Projects stats
        $totalProjects = PfeProject::where('supervisor_id', $teacher->id)->count();
        $activeProjects = PfeProject::where('supervisor_id', $teacher->id)
            ->whereIn('status', ['assigned', 'in_progress'])->count();
        $completedProjects = PfeProject::where('supervisor_id', $teacher->id)
            ->where('status', 'completed')->count();

        // Deliverables stats
        $pendingReviews = Deliverable::whereHas('project', function($query) use ($teacher) {
            $query->where('supervisor_id', $teacher->id);
        })->where('status', 'submitted')->count();

        $totalReviewed = Deliverable::whereHas('project', function($query) use ($teacher) {
            $query->where('supervisor_id', $teacher->id);
        })->where('reviewed_by', $teacher->id)->count();

        // Defense stats
        $upcomingDefenses = Defense::where(function($q) use ($teacher) {
            $q->where('jury_president_id', $teacher->id)
              ->orWhere('jury_examiner_id', $teacher->id)
              ->orWhere('jury_supervisor_id', $teacher->id);
        })->where('defense_date', '>=', now())->count();

        $completedDefenses = Defense::where(function($q) use ($teacher) {
            $q->where('jury_president_id', $teacher->id)
              ->orWhere('jury_examiner_id', $teacher->id)
              ->orWhere('jury_supervisor_id', $teacher->id);
        })->where('status', 'completed')->count();

        // Calculate workload
        $workload = $this->calculateWorkload($teacher);

        return [
            'subjects' => [
                'total' => $totalSubjects,
                'approved' => $approvedSubjects,
                'published' => $publishedSubjects,
                'pending' => $pendingSubjects,
                'approval_rate' => $totalSubjects > 0 ? ($approvedSubjects / $totalSubjects) * 100 : 0
            ],
            'projects' => [
                'total' => $totalProjects,
                'active' => $activeProjects,
                'completed' => $completedProjects,
                'completion_rate' => $totalProjects > 0 ? ($completedProjects / $totalProjects) * 100 : 0
            ],
            'deliverables' => [
                'pending_reviews' => $pendingReviews,
                'total_reviewed' => $totalReviewed,
                'avg_review_time' => $this->getAverageReviewTime($teacher)
            ],
            'defenses' => [
                'upcoming' => $upcomingDefenses,
                'completed' => $completedDefenses,
                'next_defense_date' => $this->getNextDefenseDate($teacher)
            ],
            'workload' => $workload
        ];
    }

    /**
     * Get recent activities for teacher
     */
    private function getRecentActivities($teacher): array
    {
        $activities = [];

        // Recent subject submissions
        $recentSubjects = Subject::where('supervisor_id', $teacher->id)
            ->where('created_at', '>=', now()->subWeek())
            ->get();

        foreach ($recentSubjects as $subject) {
            $activities[] = [
                'type' => 'subject_created',
                'title' => 'Created new subject',
                'description' => $subject->title,
                'timestamp' => $subject->created_at,
                'icon' => 'fas fa-book',
                'color' => 'blue'
            ];
        }

        // Recent deliverable reviews
        $recentReviews = Deliverable::whereHas('project', function($query) use ($teacher) {
            $query->where('supervisor_id', $teacher->id);
        })
        ->where('reviewed_by', $teacher->id)
        ->where('reviewed_at', '>=', now()->subWeek())
        ->with('project.subject')
        ->get();

        foreach ($recentReviews as $deliverable) {
            $activities[] = [
                'type' => 'deliverable_reviewed',
                'title' => 'Reviewed deliverable',
                'description' => $deliverable->title . ' - ' . $deliverable->project->subject->title,
                'timestamp' => $deliverable->reviewed_at,
                'icon' => 'fas fa-check-circle',
                'color' => 'green'
            ];
        }

        // Recent defense participations
        $recentDefenses = Defense::where(function($q) use ($teacher) {
            $q->where('jury_president_id', $teacher->id)
              ->orWhere('jury_examiner_id', $teacher->id)
              ->orWhere('jury_supervisor_id', $teacher->id);
        })
        ->where('defense_date', '>=', now()->subWeek())
        ->where('defense_date', '<=', now())
        ->with('project.subject')
        ->get();

        foreach ($recentDefenses as $defense) {
            $activities[] = [
                'type' => 'defense_participated',
                'title' => 'Participated in defense',
                'description' => $defense->project->subject->title,
                'timestamp' => $defense->defense_date,
                'icon' => 'fas fa-graduation-cap',
                'color' => 'purple'
            ];
        }

        // Sort by timestamp and return latest 10
        usort($activities, function($a, $b) {
            return $b['timestamp'] <=> $a['timestamp'];
        });

        return array_slice($activities, 0, 10);
    }

    /**
     * Get insights and recommendations for teacher
     */
    private function getTeacherInsights($teacher): array
    {
        $insights = [];

        // Check pending reviews
        $pendingCount = Deliverable::whereHas('project', function($query) use ($teacher) {
            $query->where('supervisor_id', $teacher->id);
        })->where('status', 'submitted')->count();

        if ($pendingCount > 5) {
            $insights[] = [
                'type' => 'warning',
                'title' => 'High number of pending reviews',
                'message' => "You have {$pendingCount} deliverables waiting for review.",
                'action' => 'Review deliverables',
                'url' => route('pfe.teacher.deliverables.index'),
                'priority' => 'high'
            ];
        }

        // Check subject approval rate
        $totalSubjects = Subject::where('supervisor_id', $teacher->id)->count();
        $approvedSubjects = Subject::where('supervisor_id', $teacher->id)
            ->where('status', 'approved')->count();

        if ($totalSubjects > 0 && ($approvedSubjects / $totalSubjects) < 0.7) {
            $insights[] = [
                'type' => 'info',
                'title' => 'Subject approval rate could be improved',
                'message' => 'Consider reviewing subject creation guidelines to improve approval rates.',
                'action' => 'View guidelines',
                'url' => '#',
                'priority' => 'medium'
            ];
        }

        // Check upcoming defenses
        $upcomingDefenses = Defense::where(function($q) use ($teacher) {
            $q->where('jury_president_id', $teacher->id)
              ->orWhere('jury_examiner_id', $teacher->id)
              ->orWhere('jury_supervisor_id', $teacher->id);
        })
        ->where('defense_date', '>=', now())
        ->where('defense_date', '<=', now()->addWeek())
        ->count();

        if ($upcomingDefenses > 0) {
            $insights[] = [
                'type' => 'info',
                'title' => 'Upcoming defenses this week',
                'message' => "You have {$upcomingDefenses} defenses scheduled this week.",
                'action' => 'View schedule',
                'url' => route('pfe.teacher.defenses.calendar'),
                'priority' => 'medium'
            ];
        }

        // Check project progress
        $atRiskProjects = PfeProject::where('supervisor_id', $teacher->id)
            ->where('status', 'at_risk')
            ->count();

        if ($atRiskProjects > 0) {
            $insights[] = [
                'type' => 'warning',
                'title' => 'Projects need attention',
                'message' => "{$atRiskProjects} projects are marked as at risk.",
                'action' => 'Review projects',
                'url' => route('pfe.teacher.supervision.index'),
                'priority' => 'high'
            ];
        }

        return $insights;
    }

    /**
     * Calculate teacher workload
     */
    private function calculateWorkload($teacher): array
    {
        $activeProjects = PfeProject::where('supervisor_id', $teacher->id)
            ->whereIn('status', ['assigned', 'in_progress'])->count();

        $pendingReviews = Deliverable::whereHas('project', function($query) use ($teacher) {
            $query->where('supervisor_id', $teacher->id);
        })->where('status', 'submitted')->count();

        $upcomingDefenses = Defense::where(function($q) use ($teacher) {
            $q->where('jury_president_id', $teacher->id)
              ->orWhere('jury_examiner_id', $teacher->id)
              ->orWhere('jury_supervisor_id', $teacher->id);
        })->where('defense_date', '>=', now())
        ->where('defense_date', '<=', now()->addMonth())
        ->count();

        $workloadScore = ($activeProjects * 10) + ($pendingReviews * 5) + ($upcomingDefenses * 3);

        $workloadLevel = 'Light';
        if ($workloadScore > 100) $workloadLevel = 'Overloaded';
        elseif ($workloadScore > 70) $workloadLevel = 'Heavy';
        elseif ($workloadScore > 40) $workloadLevel = 'Normal';

        return [
            'level' => $workloadLevel,
            'score' => $workloadScore,
            'factors' => [
                'active_projects' => $activeProjects,
                'pending_reviews' => $pendingReviews,
                'upcoming_defenses' => $upcomingDefenses
            ]
        ];
    }

    /**
     * Get average review time for teacher
     */
    private function getAverageReviewTime($teacher): string
    {
        $avgHours = Deliverable::whereHas('project', function($query) use ($teacher) {
            $query->where('supervisor_id', $teacher->id);
        })
        ->where('reviewed_by', $teacher->id)
        ->whereNotNull('reviewed_at')
        ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, submitted_at, reviewed_at)) as avg_hours')
        ->value('avg_hours');

        if (!$avgHours) return 'N/A';

        if ($avgHours < 24) return number_format($avgHours, 1) . ' hours';
        return number_format($avgHours / 24, 1) . ' days';
    }

    /**
     * Get next defense date for teacher
     */
    private function getNextDefenseDate($teacher): ?string
    {
        $nextDefense = Defense::where(function($q) use ($teacher) {
            $q->where('jury_president_id', $teacher->id)
              ->orWhere('jury_examiner_id', $teacher->id)
              ->orWhere('jury_supervisor_id', $teacher->id);
        })
        ->where('defense_date', '>=', now())
        ->orderBy('defense_date')
        ->first();

        return $nextDefense ? $nextDefense->defense_date->format('M d, Y') : null;
    }
}