<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PFE\CreateProjectRequest;
use App\Http\Requests\PFE\UpdateProjectRequest;
use App\Http\Requests\PFE\FileUploadRequest;
use App\Models\PfeProject;
use App\Models\Deliverable;
use App\Services\ProjectAssignmentService;
use App\Services\FileManagementService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProjectController extends Controller
{
    public function __construct(
        private ProjectAssignmentService $assignmentService,
        private FileManagementService $fileService
    ) {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of projects
     */
    public function index(Request $request): JsonResponse
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

        if ($request->has('supervisor_id')) {
            $query->where('supervisor_id', $request->supervisor_id);
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
        } elseif ($user->hasRole('teacher')) {
            $query->where('supervisor_id', $user->id);
        }

        $projects = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'data' => $projects->items(),
            'meta' => [
                'current_page' => $projects->currentPage(),
                'total' => $projects->total(),
                'per_page' => $projects->perPage(),
                'last_page' => $projects->lastPage()
            ]
        ]);
    }

    /**
     * Store a newly created project
     */
    public function store(CreateProjectRequest $request): JsonResponse
    {
        $project = PfeProject::create($request->validated());

        // Update team status
        $project->team->update(['status' => 'assigned']);

        return response()->json([
            'project' => $project->load([
                'subject:id,title',
                'team:id,name',
                'supervisor:id,first_name,last_name'
            ]),
            'message' => 'Project assigned successfully'
        ], 201);
    }

    /**
     * Display the specified project
     */
    public function show(PfeProject $project): JsonResponse
    {
        $this->authorize('view', $project);

        $project->load([
            'subject:id,title,description,keywords',
            'team.members.user:id,first_name,last_name,student_id',
            'supervisor:id,first_name,last_name,email',
            'deliverables:id,title,file_type,is_final_report,status,submitted_at',
            'defense:id,defense_date,start_time,status'
        ]);

        return response()->json([
            'project' => $project,
            'team' => $project->team,
            'supervisor' => $project->supervisor,
            'deliverables' => $project->deliverables,
            'defense' => $project->defense
        ]);
    }

    /**
     * Update the specified project
     */
    public function update(UpdateProjectRequest $request, PfeProject $project): JsonResponse
    {
        $project->update($request->validated());

        return response()->json([
            'project' => $project->load([
                'subject:id,title',
                'team:id,name',
                'supervisor:id,first_name,last_name'
            ]),
            'message' => 'Project updated successfully'
        ]);
    }

    /**
     * Upload a deliverable for the project
     */
    public function uploadDeliverable(FileUploadRequest $request, PfeProject $project): JsonResponse
    {
        $this->authorize('uploadDeliverable', $project);

        $fileData = $this->fileService->uploadDeliverable(
            $request->file('file'),
            $project,
            $request->user()
        );

        $deliverable = Deliverable::create([
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

        return response()->json([
            'deliverable' => $deliverable,
            'message' => 'Deliverable uploaded successfully'
        ], 201);
    }

    /**
     * Get project deliverables
     */
    public function deliverables(PfeProject $project): JsonResponse
    {
        $this->authorize('view', $project);

        $deliverables = $project->deliverables()
            ->with(['submittedBy:id,first_name,last_name', 'reviewedBy:id,first_name,last_name'])
            ->orderBy('submitted_at', 'desc')
            ->get();

        return response()->json([
            'deliverables' => $deliverables
        ]);
    }

    /**
     * Review a deliverable
     */
    public function reviewDeliverable(Request $request, Deliverable $deliverable): JsonResponse
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

        return response()->json([
            'deliverable' => $deliverable->load(['submittedBy:id,first_name,last_name', 'reviewedBy:id,first_name,last_name']),
            'message' => 'Review completed successfully'
        ]);
    }

    /**
     * Submit external project proposal
     */
    public function submitExternal(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'required|string|min:100',
            'external_company' => 'required|string|max:100',
            'external_supervisor' => 'required|string|max:100',
            'keywords' => 'array|min:3'
        ]);

        // Check if user has a team
        $user = $request->user();
        $teamMembership = $user->teamMemberships()->first();

        if (!$teamMembership || $teamMembership->team->status !== 'validated') {
            return response()->json([
                'error' => 'Invalid Team',
                'message' => 'You must be in a validated team to submit an external project'
            ], 422);
        }

        $project = $this->assignmentService->assignExternalProject(
            $teamMembership->team,
            $request->all()
        );

        return response()->json([
            'project' => $project->load([
                'subject:id,title',
                'team:id,name',
                'supervisor:id,first_name,last_name'
            ]),
            'message' => 'External project submitted successfully'
        ], 201);
    }

    /**
     * Get user's project
     */
    public function myProject(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->hasRole('student')) {
            $teamMembership = $user->teamMemberships()->with('team.project')->first();
            $project = $teamMembership?->team?->project;
        } elseif ($user->hasRole('teacher')) {
            $project = $user->supervisedPfeProjects()->with([
                'subject:id,title',
                'team:id,name'
            ])->first();
        } else {
            return response()->json([
                'project' => null,
                'message' => 'No project found'
            ]);
        }

        if (!$project) {
            return response()->json([
                'project' => null,
                'message' => 'No project assigned'
            ]);
        }

        return response()->json([
            'project' => $project->load([
                'subject:id,title,description',
                'team.members.user:id,first_name,last_name',
                'supervisor:id,first_name,last_name'
            ])
        ]);
    }
}