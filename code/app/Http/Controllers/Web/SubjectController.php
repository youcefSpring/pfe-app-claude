<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class SubjectController extends Controller
{
    /**
     * Display a listing of subjects
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        $query = Subject::with(['teacher']);

        // Apply search filter
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Apply grade filter
        if ($request->filled('grade')) {
            $query->where('target_grade', $request->grade);
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter based on user role
        switch ($user->role) {
            case 'teacher':
                // Teachers see their own subjects
                $query->where('teacher_id', $user->id);
                break;
            case 'department_head':
                // Department heads see subjects from their department
                $query->whereHas('teacher', function($q) use ($user) {
                    $q->where('department', $user->department);
                });
                break;
            case 'student':
                // Students see validated subjects and their own external subjects
                $query->where(function($q) use ($user) {
                    $q->where('status', 'validated')
                      ->orWhere(function($subq) use ($user) {
                          $subq->where('is_external', true)
                               ->where('student_id', $user->id);
                      });
                });
                break;
            // Admin sees all subjects (no filter)
        }

        $subjects = $query->latest()->paginate(12)->appends($request->query());

        return view('subjects.index', compact('subjects'));
    }

    /**
     * Show the form for creating a new subject
     */
    public function create(): View
    {
        //$this->authorize('create', Subject::class);
        return view('subjects.create');
    }

    /**
     * Store a newly created subject
     */
    public function store(Request $request): RedirectResponse
    {
        //$this->authorize('create', Subject::class);

        $user = Auth::user();

        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'keywords' => 'required|string|max:500',
            'tools' => 'required|string|max:500',
            'plan' => 'required|string',
        ];

        // Add external subject validation for students
        if ($user->role === 'student') {
            $rules['is_external'] = 'boolean';
            $rules['company_name'] = 'required_if:is_external,true|string|max:255';
            $rules['dataset_resources_link'] = 'nullable|url|max:1000';
        }

        $validated = $request->validate($rules);

        if ($user->role === 'student') {
            // Student creating external subject
            $validated['student_id'] = $user->id;
            $validated['is_external'] = $request->boolean('is_external', true);
            $validated['teacher_id'] = null; // External subjects don't have teachers initially
        } else {
            // Teacher creating internal subject
            $validated['teacher_id'] = $user->id;
            $validated['is_external'] = false;
        }

        $validated['status'] = 'draft';

        $subject = Subject::create($validated);

        return redirect()->route('subjects.show', $subject)
            ->with('success', 'Subject created successfully!');
    }

    /**
     * Display the specified subject
     */
    public function show(Subject $subject): View
    {
        $subject->load(['teacher', 'student', 'validator', 'projects.team.members.user']);
        return view('subjects.show', compact('subject'));
    }

    /**
     * Show the form for editing the specified subject
     */
    public function edit(Subject $subject): View
    {
        //$this->authorize('update', $subject);
        return view('subjects.edit', compact('subject'));
    }

    /**
     * Update the specified subject
     */
    public function update(Request $request, Subject $subject): RedirectResponse
    {
        //$this->authorize('update', $subject);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'keywords' => 'required|string|max:500',
            'tools' => 'required|string|max:500',
            'plan' => 'required|string',
        ]);

        $subject->update($validated);

        return redirect()->route('subjects.show', $subject)
            ->with('success', 'Subject updated successfully!');
    }

    /**
     * Remove the specified subject
     */
    public function destroy(Subject $subject): RedirectResponse
    {
        //$this->authorize('delete', $subject);

        if ($subject->projects()->exists()) {
            return redirect()->back()
                ->with('error', 'Cannot delete subject with associated projects.');
        }

        $subject->delete();

        return redirect()->route('subjects.index')
            ->with('success', 'Subject deleted successfully!');
    }

    /**
     * Submit subject for validation
     */
    public function submitForValidation(Subject $subject): RedirectResponse
    {
        //$this->authorize('update', $subject);

        if ($subject->status !== 'draft') {
            return redirect()->back()
                ->with('error', 'Only draft subjects can be submitted for validation.');
        }

        $subject->update(['status' => 'pending_validation']);

        return redirect()->back()
            ->with('success', 'Subject submitted for validation!');
    }

    /**
     * Show available subjects for students
     */
    public function available(Request $request): View
    {
        $grade = $request->get('grade');

        $query = Subject::with(['teacher', 'projects'])
            ->where('status', 'validated')
            ->whereDoesntHave('projects', function($q) {
                $q->where('status', 'active');
            });

        if ($grade) {
            // Filter by grade if specified
            $query->where('target_grade', $grade);
        }

        $subjects = $query->latest()->paginate(12);

        return view('subjects.available', compact('subjects', 'grade'));
    }

    /**
     * Show pending validation subjects (department heads only)
     */
    public function pendingValidation(): View
    {
        //$this->authorize('validateSubjects', Subject::class);

        $user = Auth::user();

        $subjects = Subject::with(['teacher'])
            ->where('status', 'pending_validation')
            ->whereHas('teacher', function($q) use ($user) {
                $q->where('department', $user->department);
            })
            ->latest()
            ->paginate(15);

        return view('subjects.pending-validation', compact('subjects'));
    }

    /**
     * Validate a subject (department heads only)
     */
    public function validate(Request $request, Subject $subject): RedirectResponse
    {
        //$this->authorize('validateSubjects', Subject::class);

        $request->validate([
            'action' => 'required|in:approve,reject',
            'notes' => 'nullable|string|max:500'
        ]);

        $status = $request->action === 'approve' ? 'validated' : 'rejected';

        $subject->update([
            'status' => $status,
            'validation_notes' => $request->notes,
            'validated_by' => Auth::id(),
            'validated_at' => now()
        ]);

        $message = $request->action === 'approve'
            ? 'Subject approved successfully!'
            : 'Subject rejected.';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Batch validate subjects (department heads only)
     */
    public function batchValidate(Request $request): RedirectResponse
    {
        //$this->authorize('validateSubjects', Subject::class);

        $request->validate([
            'subject_ids' => 'required|array',
            'subject_ids.*' => 'exists:subjects,id',
            'action' => 'required|in:approve,reject'
        ]);

        $status = $request->action === 'approve' ? 'validated' : 'rejected';
        $user = Auth::user();

        $subjects = Subject::whereIn('id', $request->subject_ids)
            ->whereHas('teacher', function($q) use ($user) {
                $q->where('department', $user->department);
            })
            ->where('status', 'pending_validation')
            ->get();

        foreach ($subjects as $subject) {
            $subject->update([
                'status' => $status,
                'validated_by' => Auth::id(),
                'validated_at' => now()
            ]);
        }

        $count = $subjects->count();
        $action = $request->action === 'approve' ? 'approved' : 'rejected';

        return redirect()->back()
            ->with('success', "{$count} subjects {$action} successfully!");
    }
}
