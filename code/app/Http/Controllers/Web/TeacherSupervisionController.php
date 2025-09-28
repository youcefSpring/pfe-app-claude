<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PfeProject;
use App\Models\Deliverable;
use App\Models\Subject;
use App\Models\Team;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class TeacherSupervisionController extends Controller
{
    public function __construct(private NotificationService $notificationService)
    {
        $this->middleware('auth');
        $this->middleware('role:teacher');
    }

    /**
     * Teacher supervision dashboard
     */
    public function index(): View
    {
        $teacher = auth()->user();

        $supervisedProjects = PfeProject::where('supervisor_id', $teacher->id)
            ->with(['team.members.user', 'subject', 'deliverables'])
            ->orderBy('created_at', 'desc')
            ->get();

        $pendingDeliverables = Deliverable::whereHas('project', function($query) use ($teacher) {
            $query->where('supervisor_id', $teacher->id);
        })
        ->where('status', 'submitted')
        ->with(['project.team', 'project.subject'])
        ->orderBy('submitted_at', 'desc')
        ->get();

        $mySubjects = Subject::where('supervisor_id', $teacher->id)
            ->withCount(['teamPreferences', 'projects'])
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = $this->getSupervisionStats($teacher);

        return view('pfe.teacher.supervision.index', [
            'supervised_projects' => $supervisedProjects,
            'pending_deliverables' => $pendingDeliverables,
            'my_subjects' => $mySubjects,
            'stats' => $stats
        ]);
    }

    /**
     * Show detailed project supervision view
     */
    public function showProject(PfeProject $project): View
    {
        $this->authorize('supervise', $project);

        $project->load([
            'team.members.user',
            'subject',
            'deliverables' => function($query) {
                $query->orderBy('submitted_at', 'desc');
            },
            'defense'
        ]);

        $projectProgress = $this->calculateProjectProgress($project);
        $teamPerformance = $this->analyzeTeamPerformance($project);
        $upcomingMilestones = $this->getUpcomingMilestones($project);

        return view('pfe.teacher.supervision.project', [
            'project' => $project,
            'progress' => $projectProgress,
            'team_performance' => $teamPerformance,
            'upcoming_milestones' => $upcomingMilestones
        ]);
    }

    /**
     * Project communication hub
     */
    public function showCommunication(PfeProject $project): View
    {
        $this->authorize('supervise', $project);

        $messages = $project->communications()
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $meetings = $project->meetings()
            ->orderBy('scheduled_at', 'desc')
            ->get();

        return view('pfe.teacher.supervision.communication', [
            'project' => $project,
            'messages' => $messages,
            'meetings' => $meetings
        ]);
    }

    /**
     * Send message to team
     */
    public function sendMessage(Request $request, PfeProject $project): RedirectResponse
    {
        $this->authorize('supervise', $project);

        $request->validate([
            'message' => 'required|string|max:2000',
            'priority' => 'required|in:low,normal,high,urgent',
            'attachment' => 'nullable|file|max:10240' // 10MB max
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store(
                "communications/project_{$project->id}",
                'local'
            );
        }

        $communication = $project->communications()->create([
            'sender_id' => auth()->id(),
            'message' => $request->message,
            'priority' => $request->priority,
            'attachment_path' => $attachmentPath,
            'attachment_name' => $request->file('attachment')?->getClientOriginalName()
        ]);

        // Notify team members
        foreach ($project->team->members as $member) {
            $this->notificationService->notify(
                $member->user,
                'supervisor_message',
                "New message from supervisor",
                $request->message,
                ['communication_id' => $communication->id, 'project_id' => $project->id]
            );
        }

        return back()->with('success', 'Message sent to team successfully');
    }

    /**
     * Schedule meeting with team
     */
    public function scheduleMeeting(Request $request, PfeProject $project): RedirectResponse
    {
        $this->authorize('supervise', $project);

        $request->validate([
            'title' => 'required|string|max:200',
            'date' => 'required|date|after:now',
            'duration' => 'required|integer|min:15|max:180', // 15 mins to 3 hours
            'location' => 'nullable|string|max:200',
            'agenda' => 'nullable|string|max:1000',
            'is_mandatory' => 'boolean'
        ]);

        $meeting = $project->meetings()->create([
            'title' => $request->title,
            'scheduled_at' => $request->date,
            'duration_minutes' => $request->duration,
            'location' => $request->location,
            'agenda' => $request->agenda,
            'is_mandatory' => $request->is_mandatory ?? false,
            'organizer_id' => auth()->id(),
            'status' => 'scheduled'
        ]);

        // Notify team members
        foreach ($project->team->members as $member) {
            $this->notificationService->notify(
                $member->user,
                'meeting_scheduled',
                "Meeting scheduled: {$request->title}",
                "Meeting scheduled for " . Carbon::parse($request->date)->format('M d, Y \a\t H:i'),
                ['meeting_id' => $meeting->id, 'project_id' => $project->id]
            );
        }

        return back()->with('success', 'Meeting scheduled successfully');
    }

    /**
     * Provide project feedback
     */
    public function provideFeedback(Request $request, PfeProject $project): RedirectResponse
    {
        $this->authorize('supervise', $project);

        $request->validate([
            'feedback_type' => 'required|in:progress,milestone,general,warning',
            'feedback' => 'required|string|max:2000',
            'rating' => 'nullable|integer|min:1|max:5',
            'recommendations' => 'nullable|string|max:1000',
            'next_steps' => 'nullable|string|max:1000'
        ]);

        $projectFeedback = $project->feedbacks()->create([
            'supervisor_id' => auth()->id(),
            'type' => $request->feedback_type,
            'feedback' => $request->feedback,
            'rating' => $request->rating,
            'recommendations' => $request->recommendations,
            'next_steps' => $request->next_steps,
            'given_at' => now()
        ]);

        // Update project status if needed
        if ($request->feedback_type === 'warning' && $request->rating <= 2) {
            $project->update(['status' => 'at_risk']);
        }

        // Notify team members
        foreach ($project->team->members as $member) {
            $this->notificationService->notify(
                $member->user,
                'supervisor_feedback',
                "New feedback from supervisor",
                "Feedback type: " . ucfirst($request->feedback_type),
                ['feedback_id' => $projectFeedback->id, 'project_id' => $project->id]
            );
        }

        return back()->with('success', 'Feedback provided successfully');
    }

    /**
     * Update project milestone
     */
    public function updateMilestone(Request $request, PfeProject $project): RedirectResponse
    {
        $this->authorize('supervise', $project);

        $request->validate([
            'milestone_id' => 'required|exists:project_milestones,id',
            'status' => 'required|in:not_started,in_progress,completed,overdue',
            'supervisor_notes' => 'nullable|string|max:1000'
        ]);

        $milestone = $project->milestones()->findOrFail($request->milestone_id);

        $milestone->update([
            'status' => $request->status,
            'supervisor_notes' => $request->supervisor_notes,
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id()
        ]);

        if ($request->status === 'completed') {
            $milestone->update(['completed_at' => now()]);
        }

        return back()->with('success', 'Milestone updated successfully');
    }

    /**
     * Generate supervision report
     */
    public function generateSupervisionReport(Request $request): View
    {
        $teacher = auth()->user();

        $request->validate([
            'period' => 'required|in:current_month,last_month,semester,academic_year',
            'include_projects' => 'boolean',
            'include_subjects' => 'boolean',
            'include_defenses' => 'boolean'
        ]);

        $period = $this->getPeriodDates($request->period);

        $data = [
            'teacher' => $teacher,
            'period' => $request->period,
            'start_date' => $period['start'],
            'end_date' => $period['end']
        ];

        if ($request->include_projects) {
            $data['projects'] = PfeProject::where('supervisor_id', $teacher->id)
                ->whereBetween('created_at', [$period['start'], $period['end']])
                ->with(['team', 'subject', 'deliverables'])
                ->get();
        }

        if ($request->include_subjects) {
            $data['subjects'] = Subject::where('supervisor_id', $teacher->id)
                ->whereBetween('created_at', [$period['start'], $period['end']])
                ->withCount(['teamPreferences', 'projects'])
                ->get();
        }

        if ($request->include_defenses) {
            $data['defenses'] = auth()->user()->juryDefenses()
                ->whereBetween('defense_date', [$period['start'], $period['end']])
                ->with(['project.team', 'project.subject'])
                ->get();
        }

        return view('pfe.teacher.supervision.report', $data);
    }

    /**
     * Calculate project progress statistics
     */
    private function calculateProjectProgress(PfeProject $project): array
    {
        $totalMilestones = $project->milestones()->count();
        $completedMilestones = $project->milestones()->where('status', 'completed')->count();
        $overdueMilestones = $project->milestones()
            ->where('due_date', '<', now())
            ->where('status', '!=', 'completed')
            ->count();

        $totalDeliverables = $project->deliverables()->count();
        $approvedDeliverables = $project->deliverables()->where('status', 'approved')->count();
        $pendingDeliverables = $project->deliverables()->where('status', 'submitted')->count();

        return [
            'milestones' => [
                'total' => $totalMilestones,
                'completed' => $completedMilestones,
                'overdue' => $overdueMilestones,
                'completion_rate' => $totalMilestones > 0 ? ($completedMilestones / $totalMilestones) * 100 : 0
            ],
            'deliverables' => [
                'total' => $totalDeliverables,
                'approved' => $approvedDeliverables,
                'pending' => $pendingDeliverables,
                'approval_rate' => $totalDeliverables > 0 ? ($approvedDeliverables / $totalDeliverables) * 100 : 0
            ],
            'overall_progress' => $this->calculateOverallProgress($project)
        ];
    }

    /**
     * Analyze team performance
     */
    private function analyzeTeamPerformance(PfeProject $project): array
    {
        $team = $project->team;

        $communicationFrequency = $project->communications()
            ->where('created_at', '>=', now()->subMonth())
            ->count();

        $deliverableTimeliness = $project->deliverables()
            ->where('submitted_at', '<=', \DB::raw('due_date'))
            ->count();

        $totalDeliverables = $project->deliverables()->count();

        $meetingAttendance = $project->meetings()
            ->where('status', 'completed')
            ->avg('attendance_rate') ?? 0;

        return [
            'communication_score' => min(100, $communicationFrequency * 10), // Max 100
            'timeliness_score' => $totalDeliverables > 0 ? ($deliverableTimeliness / $totalDeliverables) * 100 : 0,
            'attendance_score' => $meetingAttendance,
            'overall_performance' => $this->calculateOverallPerformance($project)
        ];
    }

    /**
     * Get upcoming milestones
     */
    private function getUpcomingMilestones(PfeProject $project): array
    {
        return $project->milestones()
            ->where('due_date', '>=', now())
            ->where('status', '!=', 'completed')
            ->orderBy('due_date')
            ->take(5)
            ->get()
            ->toArray();
    }

    /**
     * Get supervision statistics
     */
    private function getSupervisionStats($teacher): array
    {
        $totalProjects = PfeProject::where('supervisor_id', $teacher->id)->count();
        $activeProjects = PfeProject::where('supervisor_id', $teacher->id)
            ->whereIn('status', ['assigned', 'in_progress'])->count();
        $completedProjects = PfeProject::where('supervisor_id', $teacher->id)
            ->where('status', 'completed')->count();

        $pendingReviews = Deliverable::whereHas('project', function($query) use ($teacher) {
            $query->where('supervisor_id', $teacher->id);
        })->where('status', 'submitted')->count();

        $mySubjects = Subject::where('supervisor_id', $teacher->id)->count();
        $approvedSubjects = Subject::where('supervisor_id', $teacher->id)
            ->where('status', 'approved')->count();

        return [
            'total_projects' => $totalProjects,
            'active_projects' => $activeProjects,
            'completed_projects' => $completedProjects,
            'pending_reviews' => $pendingReviews,
            'my_subjects' => $mySubjects,
            'approved_subjects' => $approvedSubjects,
            'supervision_load' => $this->calculateSupervisionLoad($teacher)
        ];
    }

    /**
     * Calculate overall project progress
     */
    private function calculateOverallProgress(PfeProject $project): float
    {
        $milestoneWeight = 0.6;
        $deliverableWeight = 0.4;

        $milestoneProgress = $project->milestones()->count() > 0 ?
            ($project->milestones()->where('status', 'completed')->count() / $project->milestones()->count()) * 100 : 0;

        $deliverableProgress = $project->deliverables()->count() > 0 ?
            ($project->deliverables()->where('status', 'approved')->count() / $project->deliverables()->count()) * 100 : 0;

        return ($milestoneProgress * $milestoneWeight) + ($deliverableProgress * $deliverableWeight);
    }

    /**
     * Calculate overall team performance
     */
    private function calculateOverallPerformance(PfeProject $project): float
    {
        // Implementation for performance calculation
        return 75.0; // Placeholder
    }

    /**
     * Calculate supervision load
     */
    private function calculateSupervisionLoad($teacher): string
    {
        $activeProjects = PfeProject::where('supervisor_id', $teacher->id)
            ->whereIn('status', ['assigned', 'in_progress'])->count();

        if ($activeProjects <= 3) return 'Light';
        if ($activeProjects <= 6) return 'Normal';
        if ($activeProjects <= 9) return 'Heavy';
        return 'Overloaded';
    }

    /**
     * Get period dates based on selection
     */
    private function getPeriodDates(string $period): array
    {
        switch ($period) {
            case 'current_month':
                return [
                    'start' => now()->startOfMonth(),
                    'end' => now()->endOfMonth()
                ];
            case 'last_month':
                return [
                    'start' => now()->subMonth()->startOfMonth(),
                    'end' => now()->subMonth()->endOfMonth()
                ];
            case 'semester':
                return [
                    'start' => now()->month >= 9 ? now()->startOfYear()->addMonths(8) : now()->startOfYear()->addMonths(1),
                    'end' => now()->month >= 9 ? now()->startOfYear()->addYear()->addMonths(1) : now()->startOfYear()->addMonths(7)
                ];
            case 'academic_year':
            default:
                return [
                    'start' => now()->month >= 9 ? now()->startOfYear()->addMonths(8) : now()->subYear()->startOfYear()->addMonths(8),
                    'end' => now()->month >= 9 ? now()->addYear()->startOfYear()->addMonths(7) : now()->startOfYear()->addMonths(7)
                ];
        }
    }
}