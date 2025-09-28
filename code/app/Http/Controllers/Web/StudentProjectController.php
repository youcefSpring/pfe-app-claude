<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PfeProject;
use App\Models\Deliverable;
use App\Models\TeamMember;
use App\Models\ProjectMilestone;
use App\Services\NotificationService;
use App\Services\FileManagementService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;

class StudentProjectController extends Controller
{
    public function __construct(
        private NotificationService $notificationService,
        private FileManagementService $fileService
    ) {
        $this->middleware('auth');
        $this->middleware('role:student');
    }

    /**
     * Project overview dashboard
     */
    public function index(): View
    {
        $student = auth()->user();
        $project = $this->getCurrentProject($student);

        if (!$project) {
            return view('pfe.student.project.no-project', [
                'message' => 'You have not been assigned a project yet. Please wait for assignment or check with your supervisor.'
            ]);
        }

        $project->load([
            'team.members.user',
            'subject.supervisor',
            'deliverables' => function($query) {
                $query->orderBy('due_date', 'desc');
            },
            'milestones' => function($query) {
                $query->orderBy('due_date', 'asc');
            }
        ]);

        $projectStats = $this->getProjectStats($project);
        $upcomingTasks = $this->getUpcomingTasks($project);
        $recentActivities = $this->getRecentActivities($project);
        $progressAnalysis = $this->analyzeProjectProgress($project);

        return view('pfe.student.project.index', [
            'project' => $project,
            'stats' => $projectStats,
            'upcoming_tasks' => $upcomingTasks,
            'recent_activities' => $recentActivities,
            'progress_analysis' => $progressAnalysis
        ]);
    }

    /**
     * Show project details
     */
    public function show(PfeProject $project): View
    {
        $this->authorize('view', $project);

        $project->load([
            'team.members.user',
            'subject.supervisor',
            'deliverables.reviews',
            'milestones',
            'communications' => function($query) {
                $query->orderBy('created_at', 'desc')->take(10);
            }
        ]);

        $timelineEvents = $this->getProjectTimeline($project);
        $resourcesUsed = $this->getProjectResources($project);
        $teamContributions = $this->getTeamContributions($project);

        return view('pfe.student.project.show', [
            'project' => $project,
            'timeline_events' => $timelineEvents,
            'resources_used' => $resourcesUsed,
            'team_contributions' => $teamContributions
        ]);
    }

    /**
     * Deliverables management
     */
    public function deliverables(PfeProject $project): View
    {
        $this->authorize('view', $project);

        $deliverables = $project->deliverables()
            ->with(['reviews.reviewer'])
            ->orderBy('due_date', 'desc')
            ->get();

        $deliverableStats = $this->getDeliverableStats($project);
        $submissionGuidelines = $this->getSubmissionGuidelines();

        return view('pfe.student.project.deliverables', [
            'project' => $project,
            'deliverables' => $deliverables,
            'stats' => $deliverableStats,
            'guidelines' => $submissionGuidelines
        ]);
    }

    /**
     * Upload deliverable
     */
    public function uploadDeliverable(Request $request, PfeProject $project): RedirectResponse
    {
        $this->authorize('manage', $project);

        $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'required|string|max:1000',
            'type' => 'required|in:report,code,presentation,documentation,other',
            'file' => 'required|file|max:50240', // 50MB max
            'due_date' => 'nullable|date|after:today',
            'milestone_id' => 'nullable|exists:project_milestones,id'
        ]);

        // Check file type
        $allowedTypes = ['pdf', 'doc', 'docx', 'zip', 'rar', 'ppt', 'pptx'];
        $fileExtension = $request->file('file')->getClientOriginalExtension();

        if (!in_array(strtolower($fileExtension), $allowedTypes)) {
            return back()->withErrors(['file' => 'File type not allowed. Allowed types: ' . implode(', ', $allowedTypes)]);
        }

        // Store file
        $filePath = $request->file('file')->store(
            "projects/{$project->id}/deliverables",
            'local'
        );

        // Create deliverable record
        $deliverable = Deliverable::create([
            'project_id' => $project->id,
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'file_path' => $filePath,
            'original_filename' => $request->file('file')->getClientOriginalName(),
            'file_size' => $request->file('file')->getSize(),
            'file_type' => $fileExtension,
            'due_date' => $request->due_date,
            'milestone_id' => $request->milestone_id,
            'submitted_by' => auth()->id(),
            'submitted_at' => now(),
            'status' => 'submitted'
        ]);

        // Update milestone progress if linked
        if ($request->milestone_id) {
            $milestone = ProjectMilestone::find($request->milestone_id);
            if ($milestone && $milestone->status === 'not_started') {
                $milestone->update(['status' => 'in_progress']);
            }
        }

        // Notify supervisor
        $this->notificationService->notify(
            $project->supervisor,
            'deliverable_submitted',
            'New deliverable submitted',
            "Team {$project->team->name} submitted: {$request->title}",
            ['deliverable_id' => $deliverable->id, 'project_id' => $project->id]
        );

        // Notify team members
        foreach ($project->team->members as $member) {
            if ($member->user_id !== auth()->id()) {
                $this->notificationService->notify(
                    $member->user,
                    'team_deliverable_submitted',
                    'Team deliverable submitted',
                    "Deliverable '{$request->title}' was submitted by " . auth()->user()->first_name,
                    ['deliverable_id' => $deliverable->id, 'project_id' => $project->id]
                );
            }
        }

        return redirect()->route('pfe.student.project.deliverables', $project)
            ->with('success', 'Deliverable uploaded successfully!');
    }

    /**
     * Download deliverable
     */
    public function downloadDeliverable(Deliverable $deliverable): Response
    {
        $this->authorize('download', $deliverable);

        if (!Storage::disk('local')->exists($deliverable->file_path)) {
            abort(404, 'File not found');
        }

        return Storage::disk('local')->download(
            $deliverable->file_path,
            $deliverable->original_filename
        );
    }

    /**
     * Project milestones view
     */
    public function milestones(PfeProject $project): View
    {
        $this->authorize('view', $project);

        $milestones = $project->milestones()
            ->orderBy('due_date', 'asc')
            ->get();

        $milestoneStats = $this->getMilestoneStats($project);
        $progressChart = $this->generateProgressChart($milestones);

        return view('pfe.student.project.milestones', [
            'project' => $project,
            'milestones' => $milestones,
            'stats' => $milestoneStats,
            'progress_chart' => $progressChart
        ]);
    }

    /**
     * Update milestone progress
     */
    public function updateMilestoneProgress(Request $request, ProjectMilestone $milestone): RedirectResponse
    {
        $this->authorize('update', $milestone->project);

        $request->validate([
            'progress_percentage' => 'required|integer|min:0|max:100',
            'progress_notes' => 'nullable|string|max:1000',
            'challenges' => 'nullable|string|max:1000',
            'next_steps' => 'nullable|string|max:1000'
        ]);

        $milestone->update([
            'progress_percentage' => $request->progress_percentage,
            'progress_notes' => $request->progress_notes,
            'challenges' => $request->challenges,
            'next_steps' => $request->next_steps,
            'last_updated_by' => auth()->id(),
            'last_updated_at' => now(),
            'status' => $request->progress_percentage == 100 ? 'completed' :
                       ($request->progress_percentage > 0 ? 'in_progress' : 'not_started')
        ]);

        if ($request->progress_percentage == 100) {
            $milestone->update(['completed_at' => now()]);
        }

        // Notify supervisor
        $this->notificationService->notify(
            $milestone->project->supervisor,
            'milestone_updated',
            'Milestone progress updated',
            "Milestone '{$milestone->title}' is now {$request->progress_percentage}% complete",
            ['milestone_id' => $milestone->id, 'project_id' => $milestone->project_id]
        );

        return back()->with('success', 'Milestone progress updated successfully!');
    }

    /**
     * Communication with supervisor
     */
    public function communication(PfeProject $project): View
    {
        $this->authorize('view', $project);

        $communications = $project->communications()
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $communicationStats = $this->getCommunicationStats($project);

        return view('pfe.student.project.communication', [
            'project' => $project,
            'communications' => $communications,
            'stats' => $communicationStats
        ]);
    }

    /**
     * Send message to supervisor
     */
    public function sendMessage(Request $request, PfeProject $project): RedirectResponse
    {
        $this->authorize('view', $project);

        $request->validate([
            'message' => 'required|string|max:2000',
            'subject' => 'required|string|max:200',
            'priority' => 'required|in:low,normal,high',
            'attachment' => 'nullable|file|max:10240' // 10MB
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store(
                "projects/{$project->id}/communications",
                'local'
            );
        }

        $communication = $project->communications()->create([
            'sender_id' => auth()->id(),
            'recipient_id' => $project->supervisor_id,
            'subject' => $request->subject,
            'message' => $request->message,
            'priority' => $request->priority,
            'attachment_path' => $attachmentPath,
            'attachment_name' => $request->file('attachment')?->getClientOriginalName(),
            'sent_at' => now()
        ]);

        // Notify supervisor
        $this->notificationService->notify(
            $project->supervisor,
            'student_message',
            'New message from student',
            "Message from team {$project->team->name}: {$request->subject}",
            ['communication_id' => $communication->id, 'project_id' => $project->id]
        );

        return back()->with('success', 'Message sent to supervisor successfully!');
    }

    /**
     * Progress tracking
     */
    public function progress(PfeProject $project): View
    {
        $this->authorize('view', $project);

        $progressData = $this->getDetailedProgressData($project);
        $performanceMetrics = $this->getPerformanceMetrics($project);
        $riskAssessment = $this->assessProjectRisk($project);

        return view('pfe.student.project.progress', [
            'project' => $project,
            'progress_data' => $progressData,
            'performance_metrics' => $performanceMetrics,
            'risk_assessment' => $riskAssessment
        ]);
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
     * Get project statistics
     */
    private function getProjectStats(PfeProject $project): array
    {
        $totalDeliverables = $project->deliverables()->count();
        $submittedDeliverables = $project->deliverables()->where('status', '!=', 'draft')->count();
        $approvedDeliverables = $project->deliverables()->where('status', 'approved')->count();

        $totalMilestones = $project->milestones()->count();
        $completedMilestones = $project->milestones()->where('status', 'completed')->count();

        $daysElapsed = $project->start_date ? now()->diffInDays($project->start_date) : 0;
        $totalDuration = $project->expected_end_date ?
            $project->start_date->diffInDays($project->expected_end_date) : 180;
        $timeProgress = min(100, ($daysElapsed / $totalDuration) * 100);

        return [
            'deliverables' => [
                'total' => $totalDeliverables,
                'submitted' => $submittedDeliverables,
                'approved' => $approvedDeliverables,
                'submission_rate' => $totalDeliverables > 0 ? ($submittedDeliverables / $totalDeliverables) * 100 : 0,
                'approval_rate' => $submittedDeliverables > 0 ? ($approvedDeliverables / $submittedDeliverables) * 100 : 0
            ],
            'milestones' => [
                'total' => $totalMilestones,
                'completed' => $completedMilestones,
                'completion_rate' => $totalMilestones > 0 ? ($completedMilestones / $totalMilestones) * 100 : 0
            ],
            'timeline' => [
                'days_elapsed' => $daysElapsed,
                'total_duration' => $totalDuration,
                'time_progress' => $timeProgress,
                'days_remaining' => max(0, $totalDuration - $daysElapsed)
            ]
        ];
    }

    /**
     * Get upcoming tasks
     */
    private function getUpcomingTasks(PfeProject $project): array
    {
        $tasks = [];

        // Upcoming deliverable deadlines
        $upcomingDeliverables = $project->deliverables()
            ->where('due_date', '>=', now())
            ->where('status', 'draft')
            ->orderBy('due_date')
            ->take(3)
            ->get();

        foreach ($upcomingDeliverables as $deliverable) {
            $tasks[] = [
                'type' => 'deliverable',
                'title' => "Submit: {$deliverable->title}",
                'due_date' => $deliverable->due_date,
                'priority' => $deliverable->due_date->diffInDays(now()) <= 7 ? 'high' : 'normal',
                'url' => route('pfe.student.project.deliverables', $project)
            ];
        }

        // Upcoming milestone deadlines
        $upcomingMilestones = $project->milestones()
            ->where('due_date', '>=', now())
            ->where('status', '!=', 'completed')
            ->orderBy('due_date')
            ->take(3)
            ->get();

        foreach ($upcomingMilestones as $milestone) {
            $tasks[] = [
                'type' => 'milestone',
                'title' => "Complete: {$milestone->title}",
                'due_date' => $milestone->due_date,
                'priority' => $milestone->due_date->diffInDays(now()) <= 7 ? 'high' : 'normal',
                'url' => route('pfe.student.project.milestones', $project)
            ];
        }

        // Sort by due date
        usort($tasks, function($a, $b) {
            return $a['due_date'] <=> $b['due_date'];
        });

        return array_slice($tasks, 0, 5);
    }

    /**
     * Get recent activities
     */
    private function getRecentActivities(PfeProject $project): array
    {
        $activities = [];

        // Recent deliverable submissions
        $recentDeliverables = $project->deliverables()
            ->where('submitted_at', '>=', now()->subWeeks(2))
            ->orderBy('submitted_at', 'desc')
            ->get();

        foreach ($recentDeliverables as $deliverable) {
            $activities[] = [
                'type' => 'deliverable_submitted',
                'title' => 'Deliverable submitted',
                'description' => $deliverable->title,
                'timestamp' => $deliverable->submitted_at,
                'user' => $deliverable->submittedBy->first_name ?? 'Team'
            ];
        }

        // Recent milestone updates
        $recentMilestones = $project->milestones()
            ->where('last_updated_at', '>=', now()->subWeeks(2))
            ->orderBy('last_updated_at', 'desc')
            ->get();

        foreach ($recentMilestones as $milestone) {
            $activities[] = [
                'type' => 'milestone_updated',
                'title' => 'Milestone updated',
                'description' => "{$milestone->title} - {$milestone->progress_percentage}%",
                'timestamp' => $milestone->last_updated_at,
                'user' => $milestone->lastUpdatedBy->first_name ?? 'Team'
            ];
        }

        // Sort by timestamp
        usort($activities, function($a, $b) {
            return $b['timestamp'] <=> $a['timestamp'];
        });

        return array_slice($activities, 0, 10);
    }

    // Additional helper methods...
    private function analyzeProjectProgress($project): array
    {
        // Implementation for progress analysis
        return [
            'overall_health' => 'good',
            'risk_factors' => [],
            'recommendations' => []
        ];
    }

    private function getProjectTimeline($project): array { return []; }
    private function getProjectResources($project): array { return []; }
    private function getTeamContributions($project): array { return []; }
    private function getDeliverableStats($project): array { return []; }
    private function getSubmissionGuidelines(): array { return []; }
    private function getMilestoneStats($project): array { return []; }
    private function generateProgressChart($milestones): array { return []; }
    private function getCommunicationStats($project): array { return []; }
    private function getDetailedProgressData($project): array { return []; }
    private function getPerformanceMetrics($project): array { return []; }
    private function assessProjectRisk($project): array { return []; }
}