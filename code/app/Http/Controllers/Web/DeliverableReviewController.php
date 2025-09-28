<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Deliverable;
use App\Models\PfeProject;
use App\Services\NotificationService;
use App\Services\FileManagementService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;

class DeliverableReviewController extends Controller
{
    public function __construct(
        private NotificationService $notificationService,
        private FileManagementService $fileService
    ) {
        $this->middleware('auth');
        $this->middleware('role:teacher');
    }

    /**
     * List all deliverables pending review
     */
    public function index(Request $request): View
    {
        $teacher = auth()->user();

        $query = Deliverable::whereHas('project', function($q) use ($teacher) {
            $q->where('supervisor_id', $teacher->id);
        })->with(['project.team', 'project.subject']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'submitted'); // Default to pending reviews
        }

        // Filter by project
        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Search by title or description
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $deliverables = $query->orderBy('submitted_at', 'desc')->paginate(15);

        $supervisedProjects = PfeProject::where('supervisor_id', $teacher->id)
            ->pluck('id', 'subject.title');

        $stats = $this->getReviewStats($teacher);

        return view('pfe.teacher.deliverables.index', [
            'deliverables' => $deliverables,
            'supervised_projects' => $supervisedProjects,
            'stats' => $stats,
            'filters' => $request->only(['status', 'project_id', 'search'])
        ]);
    }

    /**
     * Show deliverable review interface
     */
    public function show(Deliverable $deliverable): View
    {
        $this->authorize('review', $deliverable);

        $deliverable->load([
            'project.team.members.user',
            'project.subject',
            'reviews' => function($query) {
                $query->orderBy('created_at', 'desc');
            }
        ]);

        // Mark as viewed by supervisor
        if (!$deliverable->viewed_by_supervisor_at) {
            $deliverable->update([
                'viewed_by_supervisor_at' => now(),
                'viewed_by_supervisor_id' => auth()->id()
            ]);
        }

        $fileAnalysis = $this->analyzeSubmittedFiles($deliverable);
        $plagiarismCheck = $this->checkPlagiarism($deliverable);
        $previousDeliverables = $this->getPreviousDeliverables($deliverable);

        return view('pfe.teacher.deliverables.review', [
            'deliverable' => $deliverable,
            'file_analysis' => $fileAnalysis,
            'plagiarism_check' => $plagiarismCheck,
            'previous_deliverables' => $previousDeliverables
        ]);
    }

    /**
     * Submit deliverable review
     */
    public function submitReview(Request $request, Deliverable $deliverable): RedirectResponse
    {
        $this->authorize('review', $deliverable);

        $request->validate([
            'status' => 'required|in:approved,rejected,needs_revision',
            'grade' => 'nullable|numeric|min:0|max:20',
            'feedback' => 'required|string|max:2000',
            'strengths' => 'nullable|string|max:1000',
            'weaknesses' => 'nullable|string|max:1000',
            'improvement_suggestions' => 'nullable|string|max:1000',
            'revision_deadline' => 'nullable|date|after:today',
            'rubric_scores' => 'nullable|array',
            'rubric_scores.*' => 'integer|min:0|max:5'
        ]);

        // Calculate overall grade if rubric scores provided
        $overallGrade = $request->grade;
        if ($request->rubric_scores) {
            $overallGrade = $this->calculateGradeFromRubric($request->rubric_scores);
        }

        // Create review record
        $review = $deliverable->reviews()->create([
            'reviewer_id' => auth()->id(),
            'status' => $request->status,
            'grade' => $overallGrade,
            'feedback' => $request->feedback,
            'strengths' => $request->strengths,
            'weaknesses' => $request->weaknesses,
            'improvement_suggestions' => $request->improvement_suggestions,
            'rubric_scores' => $request->rubric_scores,
            'reviewed_at' => now()
        ]);

        // Update deliverable status
        $deliverable->update([
            'status' => $request->status,
            'grade' => $overallGrade,
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
            'revision_deadline' => $request->revision_deadline
        ]);

        // Handle status-specific actions
        switch ($request->status) {
            case 'approved':
                $this->handleApproval($deliverable, $review);
                break;
            case 'rejected':
                $this->handleRejection($deliverable, $review);
                break;
            case 'needs_revision':
                $this->handleRevisionRequest($deliverable, $review, $request->revision_deadline);
                break;
        }

        $statusMessages = [
            'approved' => 'Deliverable approved successfully',
            'rejected' => 'Deliverable rejected with feedback',
            'needs_revision' => 'Revision requested with feedback'
        ];

        return redirect()->route('pfe.teacher.deliverables.index')
            ->with('success', $statusMessages[$request->status]);
    }

    /**
     * Download deliverable file
     */
    public function download(Deliverable $deliverable): Response
    {
        $this->authorize('review', $deliverable);

        $filePath = $deliverable->file_path;
        if (!Storage::disk('local')->exists($filePath)) {
            abort(404, 'File not found');
        }

        return Storage::disk('local')->download(
            $filePath,
            $deliverable->original_filename ?? basename($filePath)
        );
    }

    /**
     * Bulk review operations
     */
    public function bulkReview(Request $request): RedirectResponse
    {
        $request->validate([
            'deliverable_ids' => 'required|array',
            'deliverable_ids.*' => 'exists:deliverables,id',
            'action' => 'required|in:approve_all,request_revision_all,mark_priority',
            'bulk_feedback' => 'nullable|string|max:1000'
        ]);

        $teacher = auth()->user();
        $deliverables = Deliverable::whereIn('id', $request->deliverable_ids)
            ->whereHas('project', function($q) use ($teacher) {
                $q->where('supervisor_id', $teacher->id);
            })->get();

        $processedCount = 0;

        foreach ($deliverables as $deliverable) {
            switch ($request->action) {
                case 'approve_all':
                    if ($deliverable->status === 'submitted') {
                        $this->quickApprove($deliverable, $request->bulk_feedback);
                        $processedCount++;
                    }
                    break;

                case 'request_revision_all':
                    if ($deliverable->status === 'submitted') {
                        $this->quickRevisionRequest($deliverable, $request->bulk_feedback);
                        $processedCount++;
                    }
                    break;

                case 'mark_priority':
                    $deliverable->update(['is_priority' => true]);
                    $processedCount++;
                    break;
            }
        }

        return back()->with('success', "Processed {$processedCount} deliverables successfully");
    }

    /**
     * Generate deliverable analytics
     */
    public function analytics(Request $request): View
    {
        $teacher = auth()->user();

        $period = $request->get('period', 'current_semester');
        $dates = $this->getPeriodDates($period);

        $analytics = [
            'total_reviewed' => $this->getTotalReviewed($teacher, $dates),
            'average_grade' => $this->getAverageGrade($teacher, $dates),
            'approval_rate' => $this->getApprovalRate($teacher, $dates),
            'revision_rate' => $this->getRevisionRate($teacher, $dates),
            'review_time_stats' => $this->getReviewTimeStats($teacher, $dates),
            'grade_distribution' => $this->getGradeDistribution($teacher, $dates),
            'feedback_trends' => $this->getFeedbackTrends($teacher, $dates)
        ];

        return view('pfe.teacher.deliverables.analytics', [
            'analytics' => $analytics,
            'period' => $period
        ]);
    }

    /**
     * Export review report
     */
    public function exportReport(Request $request)
    {
        $teacher = auth()->user();
        $format = $request->get('format', 'pdf');

        $deliverables = Deliverable::whereHas('project', function($q) use ($teacher) {
            $q->where('supervisor_id', $teacher->id);
        })
        ->where('status', '!=', 'draft')
        ->with(['project.team', 'project.subject', 'reviews'])
        ->orderBy('reviewed_at', 'desc')
        ->get();

        if ($format === 'excel') {
            return $this->exportToExcel($deliverables, $teacher);
        }

        return $this->exportToPdf($deliverables, $teacher);
    }

    /**
     * Analyze submitted files
     */
    private function analyzeSubmittedFiles(Deliverable $deliverable): array
    {
        $filePath = $deliverable->file_path;
        if (!$filePath || !Storage::disk('local')->exists($filePath)) {
            return ['error' => 'File not found'];
        }

        $fileSize = Storage::disk('local')->size($filePath);
        $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
        $mimeType = Storage::disk('local')->mimeType($filePath);

        return [
            'size' => $this->formatFileSize($fileSize),
            'extension' => strtoupper($fileExtension),
            'mime_type' => $mimeType,
            'is_readable' => $this->isFileReadable($filePath),
            'page_count' => $this->getPageCount($filePath),
            'word_count' => $this->getWordCount($filePath)
        ];
    }

    /**
     * Check for plagiarism (simplified implementation)
     */
    private function checkPlagiarism(Deliverable $deliverable): array
    {
        // This would integrate with a plagiarism detection service
        // For now, return mock data
        return [
            'similarity_score' => rand(5, 25),
            'sources_found' => rand(0, 3),
            'status' => 'completed',
            'last_checked' => now()
        ];
    }

    /**
     * Get previous deliverables from same project
     */
    private function getPreviousDeliverables(Deliverable $deliverable)
    {
        return Deliverable::where('project_id', $deliverable->project_id)
            ->where('id', '!=', $deliverable->id)
            ->where('status', '!=', 'draft')
            ->orderBy('submitted_at', 'desc')
            ->take(5)
            ->get();
    }

    /**
     * Calculate grade from rubric scores
     */
    private function calculateGradeFromRubric(array $rubricScores): float
    {
        if (empty($rubricScores)) {
            return 0;
        }

        $totalScore = array_sum($rubricScores);
        $maxScore = count($rubricScores) * 5; // Assuming 5-point scale

        return ($totalScore / $maxScore) * 20; // Convert to 20-point scale
    }

    /**
     * Handle deliverable approval
     */
    private function handleApproval(Deliverable $deliverable, $review): void
    {
        // Notify team members
        foreach ($deliverable->project->team->members as $member) {
            $this->notificationService->notify(
                $member->user,
                'deliverable_approved',
                "Deliverable approved: {$deliverable->title}",
                "Your deliverable has been approved with grade: {$deliverable->grade}/20",
                ['deliverable_id' => $deliverable->id, 'review_id' => $review->id]
            );
        }

        // Check if project can advance to next phase
        $this->checkProjectProgress($deliverable->project);
    }

    /**
     * Handle deliverable rejection
     */
    private function handleRejection(Deliverable $deliverable, $review): void
    {
        foreach ($deliverable->project->team->members as $member) {
            $this->notificationService->notify(
                $member->user,
                'deliverable_rejected',
                "Deliverable rejected: {$deliverable->title}",
                "Your deliverable has been rejected. Please review the feedback.",
                ['deliverable_id' => $deliverable->id, 'review_id' => $review->id]
            );
        }
    }

    /**
     * Handle revision request
     */
    private function handleRevisionRequest(Deliverable $deliverable, $review, $deadline): void
    {
        foreach ($deliverable->project->team->members as $member) {
            $this->notificationService->notify(
                $member->user,
                'deliverable_revision_requested',
                "Revision requested: {$deliverable->title}",
                "Revision deadline: " . ($deadline ? \Carbon\Carbon::parse($deadline)->format('M d, Y') : 'TBD'),
                ['deliverable_id' => $deliverable->id, 'review_id' => $review->id]
            );
        }
    }

    /**
     * Quick approve deliverable
     */
    private function quickApprove(Deliverable $deliverable, ?string $feedback): void
    {
        $review = $deliverable->reviews()->create([
            'reviewer_id' => auth()->id(),
            'status' => 'approved',
            'grade' => 15, // Default grade for bulk approval
            'feedback' => $feedback ?: 'Bulk approved',
            'reviewed_at' => now()
        ]);

        $deliverable->update([
            'status' => 'approved',
            'grade' => 15,
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id()
        ]);

        $this->handleApproval($deliverable, $review);
    }

    /**
     * Quick revision request
     */
    private function quickRevisionRequest(Deliverable $deliverable, ?string $feedback): void
    {
        $review = $deliverable->reviews()->create([
            'reviewer_id' => auth()->id(),
            'status' => 'needs_revision',
            'feedback' => $feedback ?: 'Needs revision - see detailed feedback',
            'reviewed_at' => now()
        ]);

        $deliverable->update([
            'status' => 'needs_revision',
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
            'revision_deadline' => now()->addWeek()
        ]);

        $this->handleRevisionRequest($deliverable, $review, now()->addWeek());
    }

    /**
     * Get review statistics
     */
    private function getReviewStats($teacher): array
    {
        $totalPending = Deliverable::whereHas('project', function($q) use ($teacher) {
            $q->where('supervisor_id', $teacher->id);
        })->where('status', 'submitted')->count();

        $totalReviewed = Deliverable::whereHas('project', function($q) use ($teacher) {
            $q->where('supervisor_id', $teacher->id);
        })->where('reviewed_by', $teacher->id)->count();

        $avgReviewTime = Deliverable::whereHas('project', function($q) use ($teacher) {
            $q->where('supervisor_id', $teacher->id);
        })
        ->where('reviewed_by', $teacher->id)
        ->whereNotNull('reviewed_at')
        ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, submitted_at, reviewed_at)) as avg_hours')
        ->value('avg_hours');

        return [
            'pending_reviews' => $totalPending,
            'total_reviewed' => $totalReviewed,
            'avg_review_time_hours' => round($avgReviewTime ?? 0, 1),
            'review_efficiency' => $this->calculateReviewEfficiency($teacher)
        ];
    }

    /**
     * Calculate review efficiency
     */
    private function calculateReviewEfficiency($teacher): string
    {
        $avgTime = $this->getReviewStats($teacher)['avg_review_time_hours'];

        if ($avgTime <= 24) return 'Excellent';
        if ($avgTime <= 48) return 'Good';
        if ($avgTime <= 72) return 'Fair';
        return 'Needs Improvement';
    }

    /**
     * Check project progress after deliverable approval
     */
    private function checkProjectProgress(PfeProject $project): void
    {
        $totalDeliverables = $project->deliverables()->count();
        $approvedDeliverables = $project->deliverables()->where('status', 'approved')->count();

        if ($totalDeliverables > 0 && $approvedDeliverables / $totalDeliverables >= 0.8) {
            if ($project->status !== 'ready_for_defense') {
                $project->update(['status' => 'ready_for_defense']);

                // Notify team
                foreach ($project->team->members as $member) {
                    $this->notificationService->notify(
                        $member->user,
                        'project_ready_for_defense',
                        'Project ready for defense',
                        'Your project is now ready for defense scheduling',
                        ['project_id' => $project->id]
                    );
                }
            }
        }
    }

    // Additional helper methods for file analysis, statistics, etc.
    private function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    private function isFileReadable(string $filePath): bool
    {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        return in_array(strtolower($extension), ['pdf', 'doc', 'docx', 'txt']);
    }

    private function getPageCount(string $filePath): ?int
    {
        // Implementation for PDF page counting
        return null; // Placeholder
    }

    private function getWordCount(string $filePath): ?int
    {
        // Implementation for word counting
        return null; // Placeholder
    }

    private function getPeriodDates(string $period): array
    {
        // Same implementation as in TeacherSupervisionController
        switch ($period) {
            case 'current_month':
                return ['start' => now()->startOfMonth(), 'end' => now()->endOfMonth()];
            case 'current_semester':
            default:
                return ['start' => now()->startOfYear(), 'end' => now()->endOfYear()];
        }
    }

    // Analytics helper methods (simplified implementations)
    private function getTotalReviewed($teacher, $dates): int { return 0; }
    private function getAverageGrade($teacher, $dates): float { return 0.0; }
    private function getApprovalRate($teacher, $dates): float { return 0.0; }
    private function getRevisionRate($teacher, $dates): float { return 0.0; }
    private function getReviewTimeStats($teacher, $dates): array { return []; }
    private function getGradeDistribution($teacher, $dates): array { return []; }
    private function getFeedbackTrends($teacher, $dates): array { return []; }

    private function exportToExcel($deliverables, $teacher) { /* Implementation */ }
    private function exportToPdf($deliverables, $teacher) { /* Implementation */ }
}