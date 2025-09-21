<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\PFE\CreateProjectRequest;
use App\Http\Requests\PFE\UpdateProjectRequest;
use App\Http\Requests\PFE\FileUploadRequest;
use App\Models\PfeProject;
use App\Models\Deliverable;
use App\Services\FileManagementService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function __construct(private FileManagementService $fileService)
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of projects
     */
    public function index(Request $request): View
    {
        $query = PfeProject::with([
            'subject:id,title',
            'team:id,name',
            'supervisor:id,first_name,last_name'
        ]);

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('department')) {
            $query->whereHas('supervisor', function ($q) use ($request) {
                $q->where('department', $request->department);
            });
        }

        // Role-based filtering
        $user = $request->user();
        if ($user->hasRole('student')) {
            $query->whereHas('team.members', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        } elseif ($user->hasRole('teacher') && !$user->hasRole(['admin_pfe', 'chef_master'])) {
            $query->where('supervisor_id', $user->id);
        }

        $projects = $query->paginate(15);

        return view('projects.index', [
            'projects' => $projects,
            'filters' => $request->only(['status', 'department'])
        ]);
    }

    /**
     * Show the form for creating a new project
     */
    public function create(): View
    {
        $this->authorize('create', PfeProject::class);

        $availableSubjects = \App\Models\Subject::where('status', 'published')
            ->whereDoesntHave('projects')
            ->with('supervisor:id,first_name,last_name')
            ->get();

        $availableTeams = \App\Models\Team::where('status', 'validated')
            ->whereDoesntHave('project')
            ->with('leader:id,first_name,last_name')
            ->get();

        return view('projects.create', [
            'availableSubjects' => $availableSubjects,
            'availableTeams' => $availableTeams
        ]);
    }

    /**
     * Store a newly created project
     */
    public function store(CreateProjectRequest $request): RedirectResponse
    {
        $project = PfeProject::create($request->validated());

        // Update team status
        $project->team->update(['status' => 'assigned']);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project assigned successfully');
    }

    /**
     * Display the specified project
     */
    public function show(PfeProject $project): View
    {
        $this->authorize('view', $project);

        $project->load([
            'subject:id,title,description,keywords,external_supervisor,external_company',
            'team.members.user:id,first_name,last_name,student_id,email',
            'supervisor:id,first_name,last_name,email,phone',
            'deliverables' => function($query) {
                $query->orderBy('submitted_at', 'desc');
            },
            'deliverables.submittedBy:id,first_name,last_name',
            'deliverables.reviewedBy:id,first_name,last_name',
            'defense:id,defense_date,start_time,status,final_grade'
        ]);

        return view('projects.show', [
            'project' => $project
        ]);
    }

    /**
     * Show the form for editing the specified project
     */
    public function edit(PfeProject $project): View
    {
        $this->authorize('update', $project);

        return view('projects.edit', [
            'project' => $project
        ]);
    }

    /**
     * Update the specified project
     */
    public function update(UpdateProjectRequest $request, PfeProject $project): RedirectResponse
    {
        $project->update($request->validated());

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project updated successfully');
    }

    /**
     * Show deliverable upload form
     */
    public function showUpload(PfeProject $project): View
    {
        $this->authorize('uploadDeliverable', $project);

        return view('projects.upload', [
            'project' => $project
        ]);
    }

    /**
     * Upload a deliverable
     */
    public function uploadDeliverable(FileUploadRequest $request, PfeProject $project): RedirectResponse
    {
        $this->authorize('uploadDeliverable', $project);

        try {
            $fileData = $this->fileService->uploadDeliverable(
                $request->file('file'),
                $project,
                $request->user()
            );

            Deliverable::create([
                'project_id' => $project->id,
                'title' => $request->title,
                'description' => $request->description,
                'file_path' => $fileData['file_path'],
                'file_size' => $fileData['file_size'],
                'file_type' => $fileData['file_type'],
                'is_final_report' => $request->boolean('is_final_report'),
                'submitted_by' => $request->user()->id,
                'status' => 'submitted'
            ]);

            return redirect()->route('projects.show', $project)
                ->with('success', 'Deliverable uploaded successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show deliverable review form
     */
    public function showReview(Deliverable $deliverable): View
    {
        $this->authorize('review', $deliverable);

        return view('projects.review', [
            'deliverable' => $deliverable->load(['project.subject', 'submittedBy:id,first_name,last_name'])
        ]);
    }

    /**
     * Review a deliverable
     */
    public function reviewDeliverable(Request $request, Deliverable $deliverable): RedirectResponse
    {
        $this->authorize('review', $deliverable);

        $request->validate([
            'status' => 'required|in:approved,rejected',
            'review_comments' => 'nullable|string|max:1000'
        ]);

        $deliverable->update([
            'status' => $request->status,
            'review_comments' => $request->review_comments,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now()
        ]);

        return redirect()->route('projects.show', $deliverable->project)
            ->with('success', 'Deliverable review completed');
    }

    /**
     * Download deliverable
     */
    public function downloadDeliverable(Deliverable $deliverable)
    {
        $this->authorize('download', $deliverable);

        $filePath = $this->fileService->downloadFile($deliverable->file_path, auth()->user());

        if (!$filePath) {
            abort(404, 'File not found or access denied');
        }

        return response()->download($filePath, $deliverable->title . '.' . $deliverable->file_type);
    }

    /**
     * Show user's project
     */
    public function myProject(Request $request): View
    {
        $user = $request->user();
        $project = null;

        if ($user->hasRole('student')) {
            $teamMembership = $user->teamMemberships()->with('team.project')->first();
            $project = $teamMembership?->team?->project;
        } elseif ($user->hasRole('teacher')) {
            $project = $user->supervisedPfeProjects()->with([
                'subject:id,title',
                'team:id,name'
            ])->first();
        }

        if ($project) {
            $project->load([
                'subject:id,title,description',
                'team.members.user:id,first_name,last_name,student_id',
                'supervisor:id,first_name,last_name,email',
                'deliverables' => function($query) {
                    $query->orderBy('submitted_at', 'desc');
                },
                'defense:id,defense_date,start_time,status'
            ]);
        }

        return view('projects.my-project', [
            'project' => $project,
            'userRole' => $user->getRoleNames()->first()
        ]);
    }

    /**
     * Progress tracking view
     */
    public function progress(PfeProject $project): View
    {
        $this->authorize('view', $project);

        $project->load([
            'deliverables' => function($query) {
                $query->orderBy('submitted_at', 'desc');
            }
        ]);

        // Calculate progress percentage
        $progress = $this->calculateProgress($project);

        return view('projects.progress', [
            'project' => $project,
            'progress' => $progress
        ]);
    }

    /**
     * Calculate project progress
     */
    private function calculateProgress(PfeProject $project): array
    {
        $milestones = [
            'assigned' => 10,
            'in_progress' => 30,
            'under_review' => 60,
            'ready_for_defense' => 80,
            'defended' => 95,
            'completed' => 100
        ];

        $baseProgress = $milestones[$project->status] ?? 0;

        // Add bonus for deliverables
        $deliverablesBonus = min($project->deliverables->count() * 5, 20);

        $totalProgress = min($baseProgress + $deliverablesBonus, 100);

        return [
            'percentage' => $totalProgress,
            'status' => $project->status,
            'deliverables_count' => $project->deliverables->count(),
            'has_final_report' => $project->deliverables->where('is_final_report', true)->isNotEmpty()
        ];
    }
}