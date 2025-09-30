<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use App\Http\Requests\ValidateSubjectRequest;
use App\Models\Subject;
use App\Services\SubjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    protected SubjectService $subjectService;

    public function __construct(SubjectService $subjectService)
    {
        $this->subjectService = $subjectService;
    }

    /**
     * Display a listing of subjects.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $filters = $request->only(['status', 'grade', 'department', 'search']);
        $perPage = $request->get('per_page', 15);

        $subjects = $this->subjectService->getSubjectsForUser($user, $filters, $perPage);

        return response()->json([
            'success' => true,
            'data' => $subjects,
        ]);
    }

    /**
     * Store a newly created subject.
     */
    public function store(CreateSubjectRequest $request): JsonResponse
    {
        try {
            $subject = $this->subjectService->createSubject(
                $request->validated(),
                $request->user()
            );

            return response()->json([
                'success' => true,
                'message' => 'Subject created successfully',
                'data' => $subject->load('teacher'),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create subject',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified subject.
     */
    public function show(Subject $subject): JsonResponse
    {
        $subject->load(['teacher', 'validator', 'teams.members.student']);

        return response()->json([
            'success' => true,
            'data' => $subject,
        ]);
    }

    /**
     * Update the specified subject.
     */
    public function update(UpdateSubjectRequest $request, Subject $subject): JsonResponse
    {
        try {
            $updatedSubject = $this->subjectService->updateSubject(
                $subject,
                $request->validated()
            );

            return response()->json([
                'success' => true,
                'message' => 'Subject updated successfully',
                'data' => $updatedSubject->load('teacher'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update subject',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified subject.
     */
    public function destroy(Subject $subject): JsonResponse
    {
        try {
            $this->subjectService->deleteSubject($subject);

            return response()->json([
                'success' => true,
                'message' => 'Subject deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete subject',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Submit subject for validation.
     */
    public function submitForValidation(Subject $subject): JsonResponse
    {
        try {
            $this->subjectService->submitForValidation($subject);

            return response()->json([
                'success' => true,
                'message' => 'Subject submitted for validation',
                'data' => $subject->fresh(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit subject',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Validate a subject (approve/reject).
     */
    public function validate(ValidateSubjectRequest $request, Subject $subject): JsonResponse
    {
        try {
            $validatedSubject = $this->subjectService->validateSubject(
                $subject,
                $request->user(),
                $request->input('action'),
                $request->input('feedback')
            );

            return response()->json([
                'success' => true,
                'message' => 'Subject validation completed',
                'data' => $validatedSubject->load(['teacher', 'validator']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to validate subject',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get subjects pending validation for department head.
     */
    public function pendingValidation(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'department_head') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }

        $subjects = $this->subjectService->getSubjectsForValidation($user->department);

        return response()->json([
            'success' => true,
            'data' => $subjects,
        ]);
    }

    /**
     * Get available subjects for team selection.
     */
    public function available(Request $request): JsonResponse
    {
        $grade = $request->get('grade');
        $subjects = $this->subjectService->getAvailableSubjects($grade);

        return response()->json([
            'success' => true,
            'data' => $subjects,
        ]);
    }
}
