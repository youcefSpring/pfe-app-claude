<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\User;
use App\Models\Team;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SubjectController extends Controller
{
    /**
     * Display a listing of subjects
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        $query = Subject::with(['teacher', 'student', 'team.members', 'project.team', 'teams'])
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
                // Students see validated subjects and their team's external subjects
                $activeTeam = $user->activeTeam();

                $query->where(function($q) use ($activeTeam) {
                    $q->where('status', 'validated');

                    // If student is in a team, also show their team's external subjects
                    if ($activeTeam) {
                        $q->orWhere(function($subq) use ($activeTeam) {
                            $subq->where('is_external', true)
                                 ->where('team_id', $activeTeam->id);
                        });
                    }
                });

                // Apply speciality filter only if speciality relationships exist
                $hasSpecialityRelationships = \DB::table('subject_specialities')->exists();

                if ($hasSpecialityRelationships) {
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
                        $query->whereHas('specialities', function($subq) use ($allSpecialityIds) {
                            $subq->whereIn('specialities.id', $allSpecialityIds);
                        });
                    }
                    // If user has no speciality but relationships exist, don't show any subjects
                }
                // If no speciality relationships exist, show all validated subjects (no additional filter)
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

        // CHECK SETTINGS: Students can create subjects
        if ($user->role === 'student' && !\App\Services\SettingsService::canStudentsCreateSubjects()) {
            return redirect()->back()
                ->with('error', __('app.students_cannot_create_subjects'))
                ->withInput();
        }

        // CHECK SETTINGS: External projects allowed for students
        if ($user->role === 'student' && $request->boolean('is_external') && !\App\Services\SettingsService::areExternalProjectsAllowed()) {
            return redirect()->back()
                ->with('error', __('app.external_projects_not_allowed'))
                ->withInput();
        }

        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'keywords' => 'required|string|max:500',
            'tools' => 'nullable|string|max:500',
            'bibliography' => 'nullable|string|max:500',
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

            // Get student's team and associate external subject with the team
            $activeTeam = $user->activeTeam();
            if ($activeTeam) {
                $validated['team_id'] = $activeTeam->id;
            }

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

        // ✅ FIXED: Check if validation is required
        // If validation not required, auto-approve the subject
        if (\App\Services\SettingsService::requiresSubjectValidation()) {
            $validated['status'] = 'draft';
        } else {
            // Auto-approve if validation is disabled
            $validated['status'] = 'validated';
            $validated['validated_at'] = now();
            $validated['validated_by'] = $user->id;
        }

        // Ensure tools has a default value if not provided
        if (empty($validated['tools'])) {
            $validated['tools'] = '';
        }

        // Ensure bibliography has a default value if not provided
        if (empty($validated['bibliography'])) {
            $validated['bibliography'] = '';
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
        $teamPreferences = \App\Models\TeamSubjectPreference::where('subject_id', $subject->id)
            ->with([
                'team.members.user',
                'selectedBy'
            ])
            ->orderBy('preference_order')
            ->get()
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
        // Get teams that have chosen this subject as a preference, ordered by priority
        $teamRequests = \App\Models\TeamSubjectPreference::where('subject_id', $subject->id)
            ->with([
                'team.members.user',
                'selectedBy'
            ])
            ->orderBy('preference_order')
            ->get()
            ->sortBy('preference_order');

        return view('subjects.requests', compact('subject', 'teamRequests'));
    }

    /**
     * Show the form for editing the specified subject
     */
    public function edit(Subject $subject): View
    {
        $user = Auth::user();

        // Authorization check for external subjects
        if ($subject->is_external) {
            // Check if user can edit: only team leader or admin
            $canEdit = false;

            if ($user->role === 'admin') {
                $canEdit = true;
            } elseif ($subject->team_id) {
                // Check if user is the team leader of the team that owns this subject
                $userTeamMember = \App\Models\TeamMember::where('team_id', $subject->team_id)
                    ->where('student_id', $user->id)
                    ->first();

                if ($userTeamMember && $userTeamMember->role === 'leader') {
                    // Only team leaders can edit
                    $canEdit = true;
                }
            }

            if (!$canEdit) {
                abort(403, __('app.only_team_leader_can_edit_external_subject'));
            }
        } else {
            // For internal subjects, only the teacher who created it or admin can edit
            if ($user->role !== 'admin' && $subject->teacher_id !== $user->id) {
                // Department heads can also edit subjects from their department
                if ($user->role !== 'department_head' || $subject->teacher->department !== $user->department) {
                    abort(403, __('app.cannot_edit_subject_of_another_teacher'));
                }
            }
        }

        $specialities = \App\Models\Speciality::active()->get();
        $subject->load('specialities');
        return view('subjects.edit', compact('subject', 'specialities'));
    }

    /**
     * Update the specified subject
     */
    public function update(Request $request, Subject $subject): RedirectResponse
    {
        $user = Auth::user();

        // Authorization check for external subjects
        if ($subject->is_external) {
            // Check if user can edit: only team leader or admin
            $canEdit = false;

            if ($user->role === 'admin') {
                $canEdit = true;
            } elseif ($subject->team_id) {
                // Check if user is the team leader of the team that owns this subject
                $userTeamMember = \App\Models\TeamMember::where('team_id', $subject->team_id)
                    ->where('student_id', $user->id)
                    ->first();

                if ($userTeamMember && $userTeamMember->role === 'leader') {
                    // Only team leaders can edit
                    $canEdit = true;
                }
            }

            if (!$canEdit) {
                return redirect()->back()
                    ->with('error', __('app.only_team_leader_can_edit_external_subject'));
            }
        } else {
            // For internal subjects, only the teacher who created it or admin can edit
            if ($user->role !== 'admin' && $subject->teacher_id !== $user->id) {
                // Department heads can also edit subjects from their department
                if ($user->role !== 'department_head' || $subject->teacher->department !== $user->department) {
                    return redirect()->back()
                        ->with('error', __('app.cannot_edit_subject_of_another_teacher'));
                }
            }
        }

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
     * Remove the specified subject (soft delete)
     */
    public function destroy(Subject $subject): RedirectResponse
    {
        $user = Auth::user();

        // Authorization check for external subjects
        if ($subject->is_external) {
            // Check if user can delete: team leader or admin
            $canDelete = false;

            if ($user->role === 'admin') {
                $canDelete = true;
            } elseif ($subject->team_id) {
                // Check if user is team leader of the team that owns this subject
                $userTeamMember = \App\Models\TeamMember::where('team_id', $subject->team_id)
                    ->where('student_id', $user->id)
                    ->first();

                if ($userTeamMember && $userTeamMember->role === 'leader') {
                    // Only team leaders can delete
                    $canDelete = true;
                }
            }

            if (!$canDelete) {
                return redirect()->back()
                    ->with('error', __('app.cannot_delete_external_subject_of_another_team'));
            }
        } else {
            // For internal subjects, only the teacher who created it or admin can delete
            if ($user->role !== 'admin' && $subject->teacher_id !== $user->id) {
                // Department heads can also delete subjects from their department
                if ($user->role !== 'department_head' || $subject->teacher->department !== $user->department) {
                    return redirect()->back()
                        ->with('error', __('app.cannot_delete_subject_of_another_teacher'));
                }
            }
        }

        if ($subject->projects()->exists()) {
            return redirect()->back()
                ->with('error', __('app.cannot_delete_subject_with_projects'));
        }

        // Soft delete the subject
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

    /**
     * Assign a subject to a team (Admin only)
     */
    public function assignTeam(Request $request, Subject $subject): RedirectResponse
    {
        $validated = $request->validate([
            'team_id' => 'required|exists:teams,id',
        ]);

        $team = Team::findOrFail($validated['team_id']);

        // Check if team already has a project
        if ($team->project) {
            return redirect()->back()
                ->with('error', __('app.team_already_has_project'));
        }

        try {
            // Use transaction with locking to prevent race conditions
            DB::transaction(function () use ($team, $subject) {
                // Lock the teams table to check if subject is already assigned
                $assignedTeam = Team::where('subject_id', $subject->id)
                    ->where('id', '!=', $team->id)
                    ->lockForUpdate()
                    ->first();

                if ($assignedTeam) {
                    throw new \Exception(__('app.subject_already_assigned'));
                }

                // Assign subject to team
                $team->update([
                    'subject_id' => $subject->id,
                    'supervisor_id' => $subject->teacher_id,
                    'status' => 'active'
                ]);

                // Create project
                Project::create([
                    'title' => $subject->title,
                    'description' => $subject->description,
                    'subject_id' => $subject->id,
                    'team_id' => $team->id,
                    'supervisor_id' => $subject->teacher_id,
                    'status' => 'assigned',
                    'academic_year' => $team->academic_year,
                ]);
            });

            return redirect()->back()
                ->with('success', __('app.subject_assigned_successfully', ['team' => $team->name]));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Unassign a team from a subject (Admin only)
     */
    public function unassignTeam(Subject $subject): RedirectResponse
    {
        try {
            DB::transaction(function () use ($subject) {
                // Remove subject from all teams that have it
                Team::where('subject_id', $subject->id)->update([
                    'subject_id' => null,
                    'supervisor_id' => null,
                    'status' => 'forming'
                ]);

                // Delete associated projects
                if ($subject->project) {
                    $subject->project->delete();
                }

                // Delete all projects for this subject
                $subject->projects()->delete();
            });

            return redirect()->back()
                ->with('success', __('app.team_unassigned_successfully'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Display a list of all external subjects proposed by teams
     */
    public function externalList(Request $request): View
    {
        $user = Auth::user();

        // Build base query for external subjects
        $query = Subject::with(['team.members.user', 'student', 'externalSupervisor', 'specialities', 'projects.team'])
            ->where('is_external', true);

        // Apply role-based filtering
        switch ($user->role) {
            case 'student':
                // Students see only their team's external subjects
                $activeTeam = $user->activeTeam();

                if ($activeTeam) {
                    $query->where('team_id', $activeTeam->id);
                } else {
                    // If not in a team, don't show any external subjects
                    $query->whereRaw('1 = 0'); // No results
                }
                break;

            case 'teacher':
                // Teachers see external subjects they supervise
                $query->where(function($q) use ($user) {
                    $q->whereHas('projects', function($subq) use ($user) {
                        $subq->where('supervisor_id', $user->id);
                    });
                });
                break;

            case 'department_head':
                // Department heads see all external subjects
                // No additional filtering needed
                break;

            // Admin sees all external subjects (no filter)
        }

        // Apply search filter
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('company_name', 'like', '%' . $request->search . '%')
                  ->orWhereHas('student', function($subq) use ($request) {
                      $subq->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $externalSubjects = $query->latest()->paginate(15)->appends($request->query());

        return view('subjects.external-list', compact('externalSubjects'));
    }
}
