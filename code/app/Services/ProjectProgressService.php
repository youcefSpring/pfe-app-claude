<?php

namespace App\Services;

use App\Models\PfeProject;
use App\Models\ProjectMilestone;
use App\Models\Deliverable;
use App\Models\PfeNotification;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProjectProgressService
{
    /**
     * Create default milestones for a new project
     */
    public function createDefaultMilestones(PfeProject $project): void
    {
        $defaultMilestones = [
            [
                'title' => 'Project Planning & Analysis',
                'description' => 'Initial requirements gathering, analysis, and project planning',
                'weight_percentage' => 20.00,
                'order_sequence' => 1,
                'weeks_offset' => 2,
                'requirements' => ['Requirements document', 'Project plan', 'Risk analysis'],
                'completion_criteria' => ['All requirements documented', 'Timeline approved', 'Resources allocated']
            ],
            [
                'title' => 'System Design & Architecture',
                'description' => 'System architecture design and technical specifications',
                'weight_percentage' => 25.00,
                'order_sequence' => 2,
                'weeks_offset' => 6,
                'requirements' => ['System architecture', 'Database design', 'API specifications'],
                'completion_criteria' => ['Architecture approved', 'Design review completed', 'Technical stack finalized']
            ],
            [
                'title' => 'Development Phase 1 (Core Features)',
                'description' => 'Implementation of core system functionality',
                'weight_percentage' => 30.00,
                'order_sequence' => 3,
                'weeks_offset' => 12,
                'requirements' => ['Core modules implemented', 'Unit tests', 'Code documentation'],
                'completion_criteria' => ['Core features working', 'Tests passing', 'Code review passed']
            ],
            [
                'title' => 'Development Phase 2 (Advanced Features)',
                'description' => 'Implementation of advanced features and integrations',
                'weight_percentage' => 20.00,
                'order_sequence' => 4,
                'weeks_offset' => 18,
                'requirements' => ['Advanced features', 'System integration', 'Performance optimization'],
                'completion_criteria' => ['All features implemented', 'Integration tests passed', 'Performance benchmarks met']
            ],
            [
                'title' => 'Testing & Deployment',
                'description' => 'System testing, deployment, and final documentation',
                'weight_percentage' => 5.00,
                'order_sequence' => 5,
                'weeks_offset' => 22,
                'requirements' => ['Final testing', 'Deployment', 'User manual', 'Final report'],
                'completion_criteria' => ['System deployed', 'All tests passed', 'Documentation complete']
            ]
        ];

        foreach ($defaultMilestones as $milestone) {
            $expectedDate = $project->start_date->copy()->addWeeks($milestone['weeks_offset']);

            ProjectMilestone::create([
                'project_id' => $project->id,
                'title' => $milestone['title'],
                'description' => $milestone['description'],
                'expected_date' => $expectedDate,
                'status' => 'pending',
                'weight_percentage' => $milestone['weight_percentage'],
                'order_sequence' => $milestone['order_sequence'],
                'requirements' => $milestone['requirements'],
                'completion_criteria' => $milestone['completion_criteria']
            ]);
        }
    }

    /**
     * Calculate overall project progress
     */
    public function calculateProjectProgress(PfeProject $project): array
    {
        $milestones = $project->milestones()->orderBy('order_sequence')->get();

        if ($milestones->isEmpty()) {
            return [
                'overall_percentage' => 0.0,
                'completed_milestones' => 0,
                'total_milestones' => 0,
                'on_track' => true,
                'next_milestone' => null,
                'overdue_milestones' => 0
            ];
        }

        $totalProgress = 0.0;
        $completedMilestones = 0;
        $overdueMilestones = 0;
        $nextMilestone = null;

        foreach ($milestones as $milestone) {
            $milestoneProgress = $milestone->getProgressPercentage();
            $weightedProgress = ($milestoneProgress * $milestone->weight_percentage) / 100;
            $totalProgress += $weightedProgress;

            if ($milestone->isCompleted()) {
                $completedMilestones++;
            } elseif ($milestone->isOverdue()) {
                $overdueMilestones++;
            }

            if (!$nextMilestone && !$milestone->isCompleted()) {
                $nextMilestone = $milestone;
            }
        }

        $expectedProgress = $this->calculateExpectedProgress($project, $milestones);
        $onTrack = $totalProgress >= ($expectedProgress - 5); // 5% tolerance

        return [
            'overall_percentage' => round($totalProgress, 2),
            'completed_milestones' => $completedMilestones,
            'total_milestones' => $milestones->count(),
            'on_track' => $onTrack,
            'next_milestone' => $nextMilestone,
            'overdue_milestones' => $overdueMilestones,
            'expected_progress' => round($expectedProgress, 2)
        ];
    }

    /**
     * Update milestone status and progress
     */
    public function updateMilestoneProgress(ProjectMilestone $milestone, array $updates): ProjectMilestone
    {
        DB::transaction(function () use ($milestone, $updates) {
            $milestone->update($updates);

            if (isset($updates['status']) && $updates['status'] === 'completed') {
                $milestone->update(['completed_date' => now()]);
                $this->notifyMilestoneCompleted($milestone);
            }

            // Update project status if all milestones completed
            if ($this->areAllMilestonesCompleted($milestone->project)) {
                $milestone->project->update(['status' => 'ready_for_defense']);
                $this->notifyProjectReadyForDefense($milestone->project);
            }
        });

        return $milestone->fresh();
    }

    /**
     * Create deliverable tracking for sprint-based development
     */
    public function createSprintDeliverable(PfeProject $project, array $data): Deliverable
    {
        $sprintNumber = $this->getNextSprintNumber($project);

        return Deliverable::create([
            'project_id' => $project->id,
            'title' => $data['title'],
            'description' => $data['description'],
            'sprint_number' => $sprintNumber,
            'deadline' => $data['deadline'],
            'priority' => $data['priority'] ?? 'medium',
            'acceptance_criteria' => $data['acceptance_criteria'] ?? [],
            'submitted_by' => $data['submitted_by'],
            'status' => 'pending',
            'milestone_id' => $data['milestone_id'] ?? null
        ]);
    }

    /**
     * Process deliverable review with enhanced feedback
     */
    public function reviewDeliverable(Deliverable $deliverable, array $reviewData): Deliverable
    {
        $deliverable->update([
            'status' => $reviewData['status'],
            'reviewed_by' => $reviewData['reviewed_by'],
            'reviewed_at' => now(),
            'review_comments' => $reviewData['comments'],
            'feedback_summary' => $reviewData['feedback_summary'] ?? [],
            'revision_requested' => $reviewData['status'] === 'needs_revision',
            'approved_at' => $reviewData['status'] === 'approved' ? now() : null
        ]);

        $this->notifyDeliverableReviewed($deliverable);

        return $deliverable;
    }

    /**
     * Get project analytics for dashboards
     */
    public function getProjectAnalytics(PfeProject $project): array
    {
        $deliverables = $project->deliverables;
        $milestones = $project->milestones;

        return [
            'deliverables' => [
                'total' => $deliverables->count(),
                'submitted' => $deliverables->whereIn('status', ['submitted', 'under_review'])->count(),
                'approved' => $deliverables->where('status', 'approved')->count(),
                'needs_revision' => $deliverables->where('status', 'needs_revision')->count(),
                'overdue' => $deliverables->where('deadline', '<', now())->where('status', '!=', 'approved')->count()
            ],
            'milestones' => [
                'total' => $milestones->count(),
                'completed' => $milestones->where('status', 'completed')->count(),
                'pending' => $milestones->where('status', 'pending')->count(),
                'overdue' => $milestones->where('expected_date', '<', now())->where('status', '!=', 'completed')->count()
            ],
            'timeline' => [
                'start_date' => $project->start_date,
                'expected_end' => $project->expected_end_date,
                'actual_end' => $project->actual_end_date,
                'days_remaining' => $project->expected_end_date->diffInDays(now(), false),
                'progress_percentage' => $this->calculateProjectProgress($project)['overall_percentage']
            ]
        ];
    }

    /**
     * Generate progress report for supervisors
     */
    public function generateProgressReport(PfeProject $project): array
    {
        $progress = $this->calculateProjectProgress($project);
        $analytics = $this->getProjectAnalytics($project);

        $recentDeliverables = $project->deliverables()
            ->where('submitted_at', '>=', now()->subWeeks(2))
            ->with('submittedBy')
            ->orderBy('submitted_at', 'desc')
            ->limit(10)
            ->get();

        $upcomingDeadlines = $project->deliverables()
            ->where('deadline', '>=', now())
            ->where('deadline', '<=', now()->addWeeks(2))
            ->where('status', '!=', 'approved')
            ->orderBy('deadline')
            ->get();

        return [
            'project' => $project,
            'progress' => $progress,
            'analytics' => $analytics,
            'recent_deliverables' => $recentDeliverables,
            'upcoming_deadlines' => $upcomingDeadlines,
            'risks' => $this->identifyProjectRisks($project, $progress, $analytics),
            'recommendations' => $this->generateRecommendations($project, $progress, $analytics)
        ];
    }

    /**
     * Calculate expected progress based on timeline
     */
    private function calculateExpectedProgress(PfeProject $project, $milestones): float
    {
        $totalDuration = $project->start_date->diffInDays($project->expected_end_date);
        $elapsed = $project->start_date->diffInDays(now());

        if ($totalDuration <= 0 || $elapsed <= 0) {
            return 0.0;
        }

        $timeProgress = min(($elapsed / $totalDuration), 1.0);

        return $timeProgress * 100;
    }

    /**
     * Check if all milestones are completed
     */
    private function areAllMilestonesCompleted(PfeProject $project): bool
    {
        return $project->milestones()->where('status', '!=', 'completed')->count() === 0;
    }

    /**
     * Get next sprint number for project
     */
    private function getNextSprintNumber(PfeProject $project): int
    {
        $lastSprint = $project->deliverables()->max('sprint_number') ?? 0;
        return $lastSprint + 1;
    }

    /**
     * Identify potential project risks
     */
    private function identifyProjectRisks(PfeProject $project, array $progress, array $analytics): array
    {
        $risks = [];

        // Timeline risk
        if (!$progress['on_track']) {
            $risks[] = [
                'type' => 'timeline',
                'level' => 'high',
                'description' => 'Project is behind schedule',
                'impact' => 'May affect final delivery date'
            ];
        }

        // Deliverable risk
        if ($analytics['deliverables']['overdue'] > 0) {
            $risks[] = [
                'type' => 'deliverables',
                'level' => 'medium',
                'description' => 'Overdue deliverables detected',
                'impact' => 'Quality and timeline may be affected'
            ];
        }

        // Milestone risk
        if ($analytics['milestones']['overdue'] > 1) {
            $risks[] = [
                'type' => 'milestones',
                'level' => 'high',
                'description' => 'Multiple milestones overdue',
                'impact' => 'Project structure at risk'
            ];
        }

        return $risks;
    }

    /**
     * Generate recommendations based on progress
     */
    private function generateRecommendations(PfeProject $project, array $progress, array $analytics): array
    {
        $recommendations = [];

        if (!$progress['on_track']) {
            $recommendations[] = 'Schedule additional supervision meetings to address delays';
            $recommendations[] = 'Review project scope and consider prioritizing core features';
        }

        if ($analytics['deliverables']['needs_revision'] > 2) {
            $recommendations[] = 'Focus on deliverable quality to reduce revision cycles';
        }

        if ($progress['overdue_milestones'] > 0) {
            $recommendations[] = 'Re-evaluate milestone deadlines and redistribute workload';
        }

        return $recommendations;
    }

    /**
     * Notify milestone completion
     */
    private function notifyMilestoneCompleted(ProjectMilestone $milestone): void
    {
        $teamMembers = $milestone->project->team->members()->pluck('user_id');

        foreach ($teamMembers as $userId) {
            PfeNotification::create([
                'user_id' => $userId,
                'type' => 'milestone_completed',
                'title' => 'Milestone Completed',
                'message' => "Milestone '{$milestone->title}' has been completed!",
                'data' => ['milestone_id' => $milestone->id, 'project_id' => $milestone->project_id]
            ]);
        }

        // Notify supervisor
        PfeNotification::create([
            'user_id' => $milestone->project->supervisor_id,
            'type' => 'milestone_completed',
            'title' => 'Milestone Completed',
            'message' => "Team '{$milestone->project->team->name}' completed milestone: {$milestone->title}",
            'data' => ['milestone_id' => $milestone->id, 'project_id' => $milestone->project_id]
        ]);
    }

    /**
     * Notify project ready for defense
     */
    private function notifyProjectReadyForDefense(PfeProject $project): void
    {
        // Notify admin PFE
        $adminPfeUsers = \App\Models\User::role('admin_pfe')->get();
        foreach ($adminPfeUsers as $admin) {
            PfeNotification::create([
                'user_id' => $admin->id,
                'type' => 'project_ready_defense',
                'title' => 'Project Ready for Defense',
                'message' => "Project '{$project->subject->title}' by team '{$project->team->name}' is ready for defense scheduling",
                'data' => ['project_id' => $project->id]
            ]);
        }
    }

    /**
     * Notify deliverable reviewed
     */
    private function notifyDeliverableReviewed(Deliverable $deliverable): void
    {
        $teamMembers = $deliverable->project->team->members()->pluck('user_id');

        foreach ($teamMembers as $userId) {
            PfeNotification::create([
                'user_id' => $userId,
                'type' => 'deliverable_reviewed',
                'title' => 'Deliverable Reviewed',
                'message' => "Your deliverable '{$deliverable->title}' has been reviewed",
                'data' => ['deliverable_id' => $deliverable->id, 'status' => $deliverable->status]
            ]);
        }
    }
}