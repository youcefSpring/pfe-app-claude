<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    /**
     * Display a listing of projects
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        $query = Project::with(['team.members.user', 'subject.teacher', 'supervisor']);

        // Filter based on user role
        switch ($user->role) {
            case 'student':
                // Students see only their team's project
                $query->whereHas('team.members', function($subQ) use ($user) {
                    $subQ->where('student_id', $user->id);
                });
                break;
            case 'teacher':
                // Teachers see projects they supervise
                $query->where('supervisor_id', $user->id);
                break;
            case 'department_head':
                // Department heads see projects from their department
                $query->whereHas('team.members.user', function($q) use ($user) {
                    $q->where('department', $user->department);
                });
                break;
            // Admin sees all projects (no filter)
        }

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->whereHas('subject', function($subQ) use ($search) {
                    $subQ->where('title', 'like', "%{$search}%")
                         ->orWhere('description', 'like', "%{$search}%");
                })->orWhereHas('team', function($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%");
                });
            });
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        // Apply type filter
        if ($request->filled('type')) {
            $query->where('type', $request->get('type'));
        }

        $projects = $query->latest()->paginate(12);

        return view('projects.index', compact('projects'));
    }

    /**
     * Display the specified project
     */
    public function show(Project $project): View
    {
        $project->load([
            'team.members.user',
            'subject.teacher',
            'supervisor',
            'submissions' => function($query) {
                $query->latest();
            }
        ]);

        $user = Auth::user();
        $isTeamMember = $project->team->members->contains('student_id', $user->id);
        $isSupervisor = $project->supervisor_id === $user->id;

        // Get related projects (same supervisor or same subject type)
        $relatedProjects = Project::where('id', '!=', $project->id)
            ->where(function($query) use ($project) {
                $query->where('supervisor_id', $project->supervisor_id)
                      ->orWhereHas('subject', function($q) use ($project) {
                          if ($project->subject) {
                              $q->where('type', $project->subject->type);
                          }
                      });
            })
            ->with(['team.members.user', 'subject.teacher', 'supervisor'])
            ->limit(6)
            ->get();

        return view('projects.show', compact('project', 'isTeamMember', 'isSupervisor', 'relatedProjects'));
    }

    /**
     * Show supervised projects (for teachers)
     */
    public function supervised(): View
    {
        $user = Auth::user();

        if ($user->role !== 'teacher') {
            abort(403, 'Access denied.');
        }

        $projects = Project::with(['team.members.user', 'subject'])
            ->where('supervisor_id', $user->id)
            ->latest()
            ->paginate(12);

        return view('projects.supervised', compact('projects'));
    }

    /**
     * Show project submissions
     */
    public function submissions(Project $project): View
    {
        $this->authorize('view', $project);

        $project->load([
            'team.members.user',
            'submissions' => function($query) {
                $query->with('submittedBy')->latest();
            }
        ]);

        return view('projects.submissions', compact('project'));
    }

    /**
     * Show submission form
     */
    public function submitForm(Project $project): View
    {
        $this->authorize('submit', $project);

        return view('projects.submit', compact('project'));
    }

    /**
     * Store a project submission
     */
    public function submit(Request $request, Project $project): RedirectResponse
    {
        $this->authorize('submit', $project);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'submission_type' => 'required|in:progress,deliverable,final,revision,other',
            'files.*' => 'required|file|max:20480', // 20MB max per file
            'notes' => 'nullable|string|max:500'
        ]);

        $files = [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('submissions/' . $project->id, $filename, 'local');
                $files[] = [
                    'original_name' => $file->getClientOriginalName(),
                    'stored_name' => $filename,
                    'path' => $path,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType()
                ];
            }
        }

        $submission = $project->submissions()->create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'type' => $validated['submission_type'],
            'file_path' => json_encode($files),
            'submission_date' => now(),
            'status' => 'submitted'
        ]);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Submission uploaded successfully!');
    }

    /**
     * Show project timeline
     */
    public function timeline(Project $project): View
    {
        $this->authorize('view', $project);

        $project->load([
            'team.members.user',
            'submissions' => function($query) {
                $query->with('submittedBy')->latest();
            },
            'defense'
        ]);

        return view('projects.timeline', compact('project'));
    }

    /**
     * Show review form (for supervisors)
     */
    public function reviewForm(Project $project): View
    {
        $this->authorize('review', $project);

        $project->load([
            'team.members.user',
            'submissions' => function($query) {
                $query->with('submittedBy')->latest();
            }
        ]);

        return view('projects.review', compact('project'));
    }

    /**
     * Submit project review (for supervisors)
     */
    public function submitReview(Request $request, Project $project): RedirectResponse
    {
        $this->authorize('review', $project);

        $validated = $request->validate([
            'overall_grade' => 'required|numeric|min:0|max:20',
            'technical_grade' => 'required|numeric|min:0|max:20',
            'presentation_grade' => 'required|numeric|min:0|max:20',
            'report_grade' => 'required|numeric|min:0|max:20',
            'comments' => 'required|string',
            'recommendations' => 'nullable|string',
            'status' => 'required|in:in_progress,completed,needs_revision'
        ]);

        $project->update([
            'overall_grade' => $validated['overall_grade'],
            'technical_grade' => $validated['technical_grade'],
            'presentation_grade' => $validated['presentation_grade'],
            'report_grade' => $validated['report_grade'],
            'supervisor_comments' => $validated['comments'],
            'supervisor_recommendations' => $validated['recommendations'],
            'status' => $validated['status'],
            'reviewed_at' => now()
        ]);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project review submitted successfully!');
    }

    /**
     * Grade a submission (for supervisors)
     */
    public function gradeSubmission(Request $request, Project $project, Submission $submission): RedirectResponse
    {
        $this->authorize('review', $project);

        $validated = $request->validate([
            'grade' => 'required|numeric|min:0|max:20',
            'feedback' => 'required|string',
            'status' => 'required|in:approved,needs_revision,rejected'
        ]);

        $submission->update([
            'grade' => $validated['grade'],
            'feedback' => $validated['feedback'],
            'status' => $validated['status'],
            'graded_by' => Auth::id(),
            'graded_at' => now()
        ]);

        return redirect()->back()
            ->with('success', 'Submission graded successfully!');
    }

    /**
     * Assign supervisor to project (for admins/department heads)
     */
    public function assignSupervisor(Request $request, Project $project): RedirectResponse
    {
        $this->authorize('assignSupervisor', $project);

        $validated = $request->validate([
            'supervisor_id' => 'required|exists:users,id'
        ]);

        $supervisor = User::find($validated['supervisor_id']);

        if ($supervisor->role !== 'teacher') {
            return redirect()->back()
                ->with('error', 'Selected user is not a teacher.');
        }

        $project->update([
            'supervisor_id' => $supervisor->id,
            'supervisor_assigned_at' => now()
        ]);

        return redirect()->back()
            ->with('success', 'Supervisor assigned successfully!');
    }

    /**
     * Download submission file
     */
    public function downloadSubmission(Project $project, Submission $submission, string $filename): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $this->authorize('view', $project);

        $files = json_decode($submission->files, true);
        $file = collect($files)->firstWhere('stored_name', $filename);

        if (!$file) {
            abort(404, 'File not found.');
        }

        $path = Storage::disk('local')->path($file['path']);

        if (!file_exists($path)) {
            abort(404, 'File not found on disk.');
        }

        return response()->download($path, $file['original_name']);
    }

    /**
     * Create project (for admins/department heads)
     */
    public function create(): View
    {
        $this->authorize('create', Project::class);

        $teams = \App\Models\Team::with('members.user')
            ->whereDoesntHave('project')
            ->get();

        $subjects = \App\Models\Subject::where('status', 'validated')
            ->whereDoesntHave('projects')
            ->get();

        $supervisors = User::where('role', 'teacher')->get();

        return view('projects.create', compact('teams', 'subjects', 'supervisors'));
    }

    /**
     * Store a new project (for admins/department heads)
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Project::class);

        $validated = $request->validate([
            'team_id' => 'required|exists:teams,id',
            'subject_id' => 'required|exists:subjects,id',
            'supervisor_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'expected_end_date' => 'nullable|date|after:start_date',
            'description' => 'nullable|string'
        ]);

        $team = \App\Models\Team::find($validated['team_id']);
        $subject = \App\Models\Subject::find($validated['subject_id']);
        $supervisor = User::find($validated['supervisor_id']);

        if ($team->project) {
            return redirect()->back()
                ->with('error', 'Team already has a project assigned.');
        }

        if ($subject->projects()->exists()) {
            return redirect()->back()
                ->with('error', 'Subject is already assigned to another project.');
        }

        if ($supervisor->role !== 'teacher') {
            return redirect()->back()
                ->with('error', 'Supervisor must be a teacher.');
        }

        $currentYear = \App\Models\AcademicYear::getCurrentYear();
        $project = Project::create($validated + [
            'status' => 'assigned',
            'created_by' => Auth::id(),
            'academic_year' => $currentYear ? $currentYear->year : date('Y') . '-' . (date('Y') + 1),
        ]);

        $team->update(['status' => 'assigned']);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project created successfully!');
    }

    /**
     * Edit project (for admins/department heads)
     */
    public function edit(Project $project): View
    {
        $this->authorize('update', $project);

        $supervisors = User::where('role', 'teacher')->get();

        return view('projects.edit', compact('project', 'supervisors'));
    }

    /**
     * Update project (for admins/department heads)
     */
    public function update(Request $request, Project $project): RedirectResponse
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'supervisor_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'expected_end_date' => 'nullable|date|after:start_date',
            'description' => 'nullable|string',
            'status' => 'required|in:active,completed,suspended,cancelled'
        ]);

        $supervisor = User::find($validated['supervisor_id']);

        if ($supervisor->role !== 'teacher') {
            return redirect()->back()
                ->with('error', 'Supervisor must be a teacher.');
        }

        $project->update($validated);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project updated successfully!');
    }

    /**
     * Show all submissions for teachers
     */
    public function allSubmissions(): View
    {
        $user = Auth::user();

        if ($user->role !== 'teacher') {
            abort(403, 'Access denied.');
        }

        $submissions = Submission::whereHas('project', function($q) use ($user) {
            $q->where('supervisor_id', $user->id);
        })->with(['project.team.members.user', 'submittedBy'])
        ->latest()
        ->paginate(15);

        return view('submissions.index', compact('submissions'));
    }

    /**
     * Show individual submission
     */
    public function showSubmission(Submission $submission): View
    {
        $submission->load(['project.team.members.user', 'submittedBy']);

        $user = Auth::user();
        $canGrade = $submission->project->supervisor_id === $user->id;

        return view('submissions.show', compact('submission', 'canGrade'));
    }
}
