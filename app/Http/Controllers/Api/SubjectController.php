<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PFE\CreateSubjectRequest;
use App\Http\Requests\PFE\UpdateSubjectRequest;
use App\Http\Requests\PFE\ValidateSubjectRequest;
use App\Models\Subject;
use App\Services\SubjectService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SubjectController extends Controller
{
    public function __construct(private SubjectService $subjectService)
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of subjects
     */
    public function index(Request $request): JsonResponse
    {
        $query = Subject::with(['supervisor:id,first_name,last_name,department']);

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('department')) {
            $query->whereHas('supervisor', function ($q) use ($request) {
                $q->where('department', $request->department);
            });
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $subjects = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'data' => $subjects->items(),
            'meta' => [
                'current_page' => $subjects->currentPage(),
                'total' => $subjects->total(),
                'per_page' => $subjects->perPage(),
                'last_page' => $subjects->lastPage()
            ]
        ]);
    }

    /**
     * Store a newly created subject
     */
    public function store(CreateSubjectRequest $request): JsonResponse
    {
        $subject = $this->subjectService->createSubject(
            $request->validated(),
            $request->user()
        );

        return response()->json([
            'subject' => $subject->load('supervisor:id,first_name,last_name'),
            'message' => 'Subject created successfully'
        ], 201);
    }

    /**
     * Display the specified subject
     */
    public function show(Subject $subject): JsonResponse
    {
        $subject->load([
            'supervisor:id,first_name,last_name,department',
            'teamPreferences.team:id,name'
        ]);

        return response()->json([
            'subject' => $subject,
            'preferences_count' => $subject->teamPreferences->count()
        ]);
    }

    /**
     * Update the specified subject
     */
    public function update(UpdateSubjectRequest $request, Subject $subject): JsonResponse
    {
        $subject->update($request->validated());

        return response()->json([
            'subject' => $subject->load('supervisor:id,first_name,last_name'),
            'message' => 'Subject updated successfully'
        ]);
    }

    /**
     * Remove the specified subject
     */
    public function destroy(Subject $subject): JsonResponse
    {
        // Only allow deletion of draft subjects
        if ($subject->status !== 'draft') {
            return response()->json([
                'error' => 'Cannot delete',
                'message' => 'Only draft subjects can be deleted'
            ], 422);
        }

        // Check if user owns the subject
        if ($subject->supervisor_id !== auth()->id() && !auth()->user()->hasRole(['admin_pfe', 'chef_master'])) {
            return response()->json([
                'error' => 'Forbidden',
                'message' => 'You can only delete your own subjects'
            ], 403);
        }

        $subject->delete();

        return response()->json([
            'message' => 'Subject deleted successfully'
        ]);
    }

    /**
     * Validate a subject (approve/reject/request correction)
     */
    public function validate(ValidateSubjectRequest $request, Subject $subject): JsonResponse
    {
        $validatedSubject = $this->subjectService->validateSubject(
            $subject,
            $request->user(),
            $request->action,
            $request->validation_notes
        );

        return response()->json([
            'subject' => $validatedSubject->load('supervisor:id,first_name,last_name'),
            'message' => 'Subject validation completed'
        ]);
    }

    /**
     * Publish a subject for team selection
     */
    public function publish(Subject $subject): JsonResponse
    {
        $this->authorize('publish', $subject);

        $publishedSubject = $this->subjectService->publishSubject($subject);

        return response()->json([
            'subject' => $publishedSubject,
            'message' => 'Subject published for selection'
        ]);
    }

    /**
     * Submit a subject for validation
     */
    public function submit(Subject $subject): JsonResponse
    {
        $this->authorize('update', $subject);

        $submittedSubject = $this->subjectService->submitSubject($subject);

        return response()->json([
            'subject' => $submittedSubject,
            'message' => 'Subject submitted for validation'
        ]);
    }

    /**
     * Get available subjects for team selection
     */
    public function available(): JsonResponse
    {
        $subjects = $this->subjectService->getAvailableSubjects();

        return response()->json([
            'subjects' => $subjects
        ]);
    }
}