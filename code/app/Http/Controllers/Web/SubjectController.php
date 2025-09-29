<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\PFE\CreateSubjectRequest;
use App\Http\Requests\PFE\UpdateSubjectRequest;
use App\Http\Requests\PFE\ValidateSubjectRequest;
use App\Models\Subject;
use App\Services\SubjectService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function __construct(private SubjectService $subjectService)
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of subjects
     */
    public function index(Request $request): View
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

        // Role-based filtering
        $user = $request->user();
        if ($user->hasRole('teacher') && !$user->hasRole(['admin_pfe', 'chef_master'])) {
            $query->where('supervisor_id', $user->id);
        }

        $subjects = $query->paginate(15);

        return view('subjects.index', [
            'subjects' => $subjects,
            'filters' => $request->only(['status', 'department', 'search'])
        ]);
    }

    /**
     * Show the form for creating a new subject
     */
    public function create(): View
    {
        $this->authorize('create', Subject::class);

        return view('subjects.create');
    }

    /**
     * Store a newly created subject
     */
    public function store(CreateSubjectRequest $request): RedirectResponse
    {
        $subject = $this->subjectService->createSubject(
            $request->validated(),
            $request->user()
        );

        return redirect()->route('subjects.show', $subject)
            ->with('success', 'Subject created successfully');
    }

    /**
     * Display the specified subject
     */
    public function show(Subject $subject): View
    {
        $this->authorize('view', $subject);

        $subject->load([
            'supervisor:id,first_name,last_name,email,department',
            'teamPreferences.team:id,name',
            'projects.team:id,name'
        ]);

        return view('subjects.show', [
            'subject' => $subject
        ]);
    }

    /**
     * Show the form for editing the specified subject
     */
    public function edit(Subject $subject): View
    {
        $this->authorize('update', $subject);

        return view('subjects.edit', [
            'subject' => $subject
        ]);
    }

    /**
     * Update the specified subject
     */
    public function update(UpdateSubjectRequest $request, Subject $subject): RedirectResponse
    {
        $subject->update($request->validated());

        return redirect()->route('subjects.show', $subject)
            ->with('success', 'Subject updated successfully');
    }

    /**
     * Remove the specified subject
     */
    public function destroy(Subject $subject): RedirectResponse
    {
        $this->authorize('delete', $subject);

        if ($subject->status !== 'draft') {
            return back()->with('error', 'Only draft subjects can be deleted');
        }

        $subject->delete();

        return redirect()->route('subjects.index')
            ->with('success', 'Subject deleted successfully');
    }

    /**
     * Submit subject for validation
     */
    public function submit(Subject $subject): RedirectResponse
    {
        $this->authorize('update', $subject);

        $this->subjectService->submitSubject($subject);

        return back()->with('success', 'Subject submitted for validation');
    }

    /**
     * Show validation form
     */
    public function showValidation(Subject $subject): View
    {
        $this->authorize('validate', $subject);

        return view('subjects.validate', [
            'subject' => $subject
        ]);
    }

    /**
     * Validate a subject
     */
    public function validateSubject(ValidateSubjectRequest $request, Subject $subject): RedirectResponse
    {
        $this->subjectService->validateSubject(
            $subject,
            $request->user(),
            $request->action,
            $request->validation_notes
        );

        $messages = [
            'approved' => 'Subject approved successfully',
            'rejected' => 'Subject rejected',
            'needs_correction' => 'Subject returned for corrections'
        ];

        return redirect()->route('subjects.show', $subject)
            ->with('success', $messages[$request->action]);
    }

    /**
     * Publish subject for team selection
     */
    public function publish(Subject $subject): RedirectResponse
    {
        $this->authorize('publish', $subject);

        $this->subjectService->publishSubject($subject);

        return back()->with('success', 'Subject published for team selection');
    }

    /**
     * Show available subjects for selection
     */
    public function available(): View
    {
        $this->authorize('viewAvailable', Subject::class);

        $subjects = $this->subjectService->getAvailableSubjects();

        return view('subjects.available', [
            'subjects' => $subjects
        ]);
    }
}