<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SubjectController extends Controller
{
    /**
     * Display a listing of subjects
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        $query = Subject::with(['teacher'])
            ->withCount(['preferences as preferences_count']);

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
                })->where(function($q) use ($user) {
                    // Filter by speciality: show subjects for user's speciality OR for their team members' specialities
                    $userSpecialityIds = collect([$user->speciality_id])->filter();

                    // Get team members' speciality IDs if user is in a team
                    $teamMemberSpecialityIds = collect();
                    $activeTeam = $user->activeTeam();
                    if ($activeTeam) {
                        $teamMemberSpecialityIds = $activeTeam->members()
                            ->with('user')
                            ->get()
                            ->pluck('user.speciality_id')
                            ->filter()
                            ->unique();
                    }

                    $allSpecialityIds = $userSpecialityIds->merge($teamMemberSpecialityIds)->unique()->values();

                    if ($allSpecialityIds->isNotEmpty()) {
                        $q->whereHas('specialities', function($subq) use ($allSpecialityIds) {
                            $subq->whereIn('specialities.id', $allSpecialityIds);
                        });
                    } else {
                        // If no speciality, show all subjects (fallback)
                        $q->whereRaw('1 = 1');
                    }
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
        $specialities = \App\Models\Speciality::active()->get();
        return view('subjects.create', compact('specialities'));
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
            'tools' => 'nullable|string|max:500',
            'plan' => 'required|string',
            'specialities' => 'required|array|min:1',
            'specialities.*' => 'exists:specialities,id',
        ];

        // Add external subject validation for students
        if ($user->role === 'student') {
            $rules['is_external'] = 'boolean';
            $rules['company_name'] = 'required_if:is_external,true|string|max:255';
            $rules['dataset_resources_link'] = 'nullable|url|max:1000';
            $rules['external_supervisor_name'] = 'required_if:is_external,true|string|max:255';
            $rules['external_supervisor_email'] = 'required_if:is_external,true|email|max:255';
            $rules['external_supervisor_phone'] = 'nullable|string|max:20';
            $rules['external_supervisor_position'] = 'nullable|string|max:255';
        }

        $validated = $request->validate($rules);

        if ($user->role === 'student') {
            // Student creating external subject
            $validated['student_id'] = $user->id;
            $validated['is_external'] = $request->boolean('is_external', true);
            $validated['teacher_id'] = null; // External subjects don't have teachers initially

            // Handle external supervisor creation if this is an external subject
            if ($validated['is_external'] && $request->filled('external_supervisor_email')) {
                $externalSupervisor = $this->createOrFindExternalSupervisor($request);
                $validated['external_supervisor_id'] = $externalSupervisor->id;
            }

            // Remove supervisor fields from validated data as they're not in the subjects table
            unset($validated['external_supervisor_name'], $validated['external_supervisor_email'],
                  $validated['external_supervisor_phone'], $validated['external_supervisor_position']);
        } else {
            // Teacher creating internal subject
            $validated['teacher_id'] = $user->id;
            $validated['is_external'] = false;
        }

        $validated['status'] = 'draft';

        // Ensure tools has a default value if not provided
        if (empty($validated['tools'])) {
            $validated['tools'] = '';
        }

        // Remove specialities from validated data as it's handled separately
        $specialities = $validated['specialities'];
        unset($validated['specialities']);

        $subject = Subject::create($validated);

        // Attach specialities to the subject
        $subject->specialities()->attach($specialities);

        return redirect()->route('subjects.show', $subject)
            ->with('success', __('app.subject_created'));
    }

    /**
     * Display the specified subject
     */
    public function show(Subject $subject): View
    {
        $subject->load(['teacher', 'student', 'validator', 'externalSupervisor', 'projects.team.members.user']);

        // Get teams that have chosen this subject as a preference, ordered by priority
        $teamPreferences = \App\Models\SubjectPreference::where('subject_id', $subject->id)
            ->with([
                'team.members.user',
                'allocationDeadline'
            ])
            ->orderBy('preference_order')
            ->get()
            ->groupBy('team_id')
            ->map(function ($preferences) {
                return $preferences->first(); // Get the first (highest priority) preference for each team
            })
            ->sortBy('preference_order');

        return view('subjects.show', compact('subject', 'teamPreferences'));
    }

    /**
     * Display subject details for modal
     */
    public function modal(Subject $subject): View
    {
        $subject->load(['teacher', 'student', 'validator', 'externalSupervisor', 'projects.team.members.user']);
        return view('subjects.modal', compact('subject'));
    }

    /**
     * Display team requests for a subject
     */
    public function requests(Subject $subject): View
    {
        // Get students that have chosen this subject as a preference, ordered by priority
        $studentRequests = \App\Models\SubjectPreference::where('subject_id', $subject->id)
            ->with([
                'student.teamMember.team.members.user'
            ])
            ->orderBy('preference_order')
            ->get();

        // Group by team to show team-based requests
        $teamRequests = $studentRequests->groupBy(function($preference) {
            return $preference->student->teamMember ? $preference->student->teamMember->team_id : null;
        })->map(function($preferences, $teamId) {
            if($teamId === null) {
                // Students without teams
                return $preferences;
            }

            // Return the first preference for each team (assuming team members have same priorities)
            $firstPreference = $preferences->first();
            $team = $firstPreference->student->teamMember->team;

            // Add team data to the preference object
            $firstPreference->team = $team;
            $firstPreference->team_members_preferences = $preferences;

            return $firstPreference;
        })->filter()->sortBy('preference_order');

        return view('subjects.requests', compact('subject', 'teamRequests'));
    }

    /**
     * Show the form for editing the specified subject
     */
    public function edit(Subject $subject): View
    {
        //$this->authorize('update', $subject);
        $specialities = \App\Models\Speciality::active()->get();
        $subject->load('specialities');
        return view('subjects.edit', compact('subject', 'specialities'));
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
            'tools' => 'nullable|string|max:500',
            'plan' => 'required|string',
            'specialities' => 'required|array|min:1',
            'specialities.*' => 'exists:specialities,id',
        ]);

        // Ensure tools has a default value if not provided
        if (empty($validated['tools'])) {
            $validated['tools'] = '';
        }

        // Handle specialities separately
        $specialities = $validated['specialities'];
        unset($validated['specialities']);

        $subject->update($validated);

        // Sync specialities (add new ones, remove old ones)
        $subject->specialities()->sync($specialities);

        return redirect()->route('subjects.show', $subject)
            ->with('success', __('app.subject_updated'));
    }

    /**
     * Remove the specified subject
     */
    public function destroy(Subject $subject): RedirectResponse
    {
        //$this->authorize('delete', $subject);

        if ($subject->projects()->exists()) {
            return redirect()->back()
                ->with('error', __('app.cannot_delete_subject_with_projects'));
        }

        $subject->delete();

        return redirect()->route('subjects.index')
            ->with('success', __('app.subject_deleted'));
    }

    /**
     * Submit subject for validation
     */
    public function submitForValidation(Subject $subject): RedirectResponse
    {
        //$this->authorize('update', $subject);

        if ($subject->status !== 'draft') {
            return redirect()->back()
                ->with('error', __('app.only_draft_can_submit'));
        }

        $subject->update(['status' => 'pending_validation']);

        return redirect()->back()
            ->with('success', __('app.subject_submitted_validation'));
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
            ->with('success', __('app.subjects_bulk_action', ['count' => $count, 'action' => $action]));
    }

    /**
     * Create or find external supervisor user
     */
    private function createOrFindExternalSupervisor(Request $request): User
    {
        $email = $request->external_supervisor_email;

        // Check if user already exists
        $existingUser = User::where('email', $email)->first();

        if ($existingUser) {
            // Update user information if they exist
            $existingUser->update([
                'name' => $request->external_supervisor_name,
                'phone' => $request->external_supervisor_phone,
                'position' => $request->external_supervisor_position ?? 'External Supervisor',
                'role' => 'external_supervisor',
            ]);

            return $existingUser;
        }

        // Create new external supervisor user
        $user = User::create([
            'name' => $request->external_supervisor_name,
            'email' => $email,
            'phone' => $request->external_supervisor_phone,
            'position' => $request->external_supervisor_position ?? 'External Supervisor',
            'role' => 'external_supervisor',
            'password' => Hash::make(Str::random(16)), // Random password
            'email_verified_at' => now(), // Auto-verify external supervisors
        ]);

        return $user;
    }

    /**
     * Handle individual subject request from students without teams
     */
    public function requestIndividual(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Validate user is a student without a team
        if ($user->role !== 'student') {
            return redirect()->back()->with('error', __('app.only_students_can_request_subjects'));
        }

        if ($user->teamMember) {
            return redirect()->back()->with('error', __('app.team_members_cannot_request_individually'));
        }

        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'request_message' => 'required|string|max:1000',
            'work_preference' => 'required|in:individual,open_to_team',
        ]);

        $subject = Subject::findOrFail($request->subject_id);

        // Check if subject is available
        if ($subject->status !== 'validated') {
            return redirect()->back()->with('error', __('app.subject_not_available'));
        }

        if ($subject->projects()->exists()) {
            return redirect()->back()->with('error', __('app.subject_already_assigned'));
        }

        // Check deadline restrictions
        $currentDeadline = \App\Models\AllocationDeadline::active()->first();
        if (!$currentDeadline || !$currentDeadline->canStudentsChoose()) {
            return redirect()->back()->with('error', __('app.subject_request_period_ended'));
        }

        // Check if user already has a pending request for this subject
        $existingRequest = \App\Models\SubjectRequest::where('subject_id', $subject->id)
            ->where('requested_by', $user->id)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return redirect()->back()->with('error', __('app.subject_request_already_exists'));
        }

        // Create individual subject request
        \App\Models\SubjectRequest::create([
            'subject_id' => $subject->id,
            'requested_by' => $user->id,
            'team_id' => null, // Individual request
            'request_message' => $request->request_message,
            'work_preference' => $request->work_preference,
            'status' => 'pending',
            'requested_at' => now(),
        ]);

        return redirect()->back()->with('success', __('app.individual_subject_request_submitted'));
    }
}
