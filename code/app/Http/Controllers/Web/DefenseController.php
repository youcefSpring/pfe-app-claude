<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Defense;
use App\Models\DefenseJury;
use App\Models\Project;
use App\Models\Room;
use App\Models\Subject;
use App\Models\Team;
use App\Models\User;
use App\Models\AllocationDeadline;
use App\Services\ReportService;
use App\Services\AutoAllocationService;
use App\Services\DefenseSchedulingService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use ZipArchive;
use PDF;

class DefenseController extends Controller
{
    /**
     * Display a listing of defenses
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        $query = Defense::with(['subject.teacher', 'room', 'juries.teacher', 'project.team.members.user']);

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('subject', function($subq) use ($search) {
                    $subq->where('title', 'like', '%' . $search . '%');
                })
                ->orWhereHas('room', function($roomq) use ($search) {
                    $roomq->where('name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('project.team.members.user', function($userq) use ($search) {
                    $userq->where('name', 'like', '%' . $search . '%')
                          ->orWhere('first_name', 'like', '%' . $search . '%')
                          ->orWhere('last_name', 'like', '%' . $search . '%')
                          ->orWhere('matricule', 'like', '%' . $search . '%');
                })
                ->orWhereHas('juries.teacher', function($teacherq) use ($search) {
                    $teacherq->where('name', 'like', '%' . $search . '%');
                });
            });
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Apply date filter
        if ($request->filled('date_from')) {
            $query->where('defense_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('defense_date', '<=', $request->date_to);
        }

        // Filter based on user role
        switch ($user->role) {
            case 'student':
                // Students see defenses from their department
                $query->whereHas('subject.teacher', function($q) use ($user) {
                    $q->where('department', $user->department);
                });
                break;
            case 'teacher':
                // Teachers see defenses where they are jury members
                $query->whereHas('juries', function($q) use ($user) {
                    $q->where('teacher_id', $user->id);
                });
                break;
            case 'department_head':
                // Department heads see defenses from their department
                $query->whereHas('subject.teacher', function($q) use ($user) {
                    $q->where('department', $user->department);
                });
                break;
            // Admin sees all defenses (no filter)
        }

        $defenses = $query->orderBy('defense_date', 'desc')
                          ->orderBy('defense_time', 'desc')
                          ->paginate(20)
                          ->appends($request->query());

        // Get status counts for filters
        $statusCounts = [
            'all' => Defense::count(),
            'scheduled' => Defense::where('status', 'scheduled')->count(),
            'completed' => Defense::where('status', 'completed')->count(),
            'cancelled' => Defense::where('status', 'cancelled')->count(),
        ];

        return view('defenses.index', compact('defenses', 'statusCounts'));
    }

    /**
     * Display the specified defense
     */
    public function show(Defense $defense)
    {
        $defense->load([
            'subject.teacher',
            'room',
            'juries.teacher',
            'report'
        ]);

        // Also load project relationship if it exists
        if ($defense->project_id) {
            $defense->load(['project.team.members.user']);
        }

        $user = Auth::user();

        // Check if user is a team member (only if project exists)
        $isTeamMember = false;
        if ($defense->project && $defense->project->team) {
            $isTeamMember = $defense->project->team->members->contains('student_id', $user->id);
        }

        $isJuryMember = $defense->juries->contains('teacher_id', $user->id);

        return view('defenses.show', compact('defense', 'isTeamMember', 'isJuryMember'));
    }

    /**
     * Show defense calendar
     */
    public function calendar(): View
    {
        $user = Auth::user();

        $query = Defense::with(['subject.teacher', 'room', 'juries.teacher', 'project.team']);

        // Only show current and future defenses
        $query->where('defense_date', '>=', now()->toDateString());

        // Filter based on user role
        switch ($user->role) {
            case 'student':
                $query->whereHas('subject.teacher', function($q) use ($user) {
                    $q->where('department', $user->department);
                });
                break;
            case 'teacher':
                $query->whereHas('juries', function($q) use ($user) {
                    $q->where('teacher_id', $user->id);
                });
                break;
            case 'department_head':
                $query->whereHas('subject.teacher', function($q) use ($user) {
                    $q->where('department', $user->department);
                });
                break;
            case 'admin':
                // Admin can see all defenses
                break;
        }

        $defensesCollection = $query->orderBy('defense_date')->orderBy('defense_time')->get();

        // Pass both collections and formatted arrays
        $defenses = $defensesCollection; // For Blade template

        // Format defenses for JavaScript
        $defensesJson = $defensesCollection->map(function($defense) {
            return [
                'id' => $defense->id,
                'title' => $defense->subject->title ?? 'Defense',
                'date' => $defense->defense_date ? $defense->defense_date->format('Y-m-d') : null,
                'time' => $defense->defense_time ? \Carbon\Carbon::parse($defense->defense_time)->format('H:i') : null,
                'status' => $defense->status,
                'room' => $defense->room->name ?? 'TBD',
            ];
        });

        return view('defenses.calendar', compact('defenses', 'defensesJson'));
    }

    /**
     * Show my defense (for students)
     */
    public function myDefense(): View
    {
        $user = Auth::user();

        if ($user->role !== 'student') {
            abort(403, 'Access denied.');
        }

        $defense = Defense::whereHas('project.team.members', function($q) use ($user) {
            $q->where('student_id', $user->id);
        })->with([
            'project.team.members.user',
            'project.subject.teacher',
            'room',
            'juries.teacher',
            'report'
        ])->first();

        return view('defenses.my-defense', compact('defense'));
    }

    /**
     * Show jury assignments (for teachers)
     */
    public function juryAssignments(): View
    {
        $user = Auth::user();

        if ($user->role !== 'teacher') {
            abort(403, 'Access denied.');
        }

        $assignments = DefenseJury::with([
            'defense.project.team.members.user',
            'defense.project.subject',
            'defense.room'
        ])->where('teacher_id', $user->id)
          ->orderBy('created_at', 'desc')
          ->paginate(12);

        return view('defenses.jury-assignments', compact('assignments'));
    }

    /**
     * Show schedule form (for admins/department heads)
     */
    public function scheduleForm(): View
    {
        $this->authorize('schedule', Defense::class);

        $subjects = Subject::with(['teacher', 'projects.team.members.user'])
            ->where('status', 'validated')
            ->get();

        $rooms = Room::orderBy('name')->get();

        $teachers = User::whereIn('role', ['teacher','department_head'])->orderBy('name')->get();

        // Get teams that don't have a defense scheduled for current academic year
        // This includes teams with projects but no defense AND teams without projects (no subject chosen)
        $currentAcademicYear = '2024-2025'; // This should be dynamic based on your system

        $teamsWithoutDefense = Team::with(['members.user', 'project.subject'])
            ->whereHas('members') // Teams must have at least one member
            ->where(function($outerQuery) use ($currentAcademicYear) {
                // First condition: teams where academic_year is null (no academic year set)
                $outerQuery->whereHas('members.user', function($query) {
                    $query->whereNull('academic_year');
                })
                // OR teams with the current academic year
                ->orWhereHas('members.user', function($query) use ($currentAcademicYear) {
                    $query->where('academic_year', $currentAcademicYear);
                });
            })
            ->where(function($query) {
                $query->whereDoesntHave('project') // Teams without any project (no subject chosen)
                      ->orWhereHas('project', function($subQuery) { // OR teams with project but no defense
                          $subQuery->whereDoesntHave('defense');
                      });
            })
            ->get();

        // Prepare subject data for JavaScript
        $subjectData = $subjects->map(function($subject) {
            $assignedTeam = null;
            if ($subject->projects->count() > 0) {
                $project = $subject->projects->first();
                $assignedTeam = [
                    'id' => $project->team->id,
                    'name' => $project->team->name,
                    'members' => $project->team->members->map(function($member) {
                        return $member->user->name;
                    })->toArray()
                ];
            }

            return [
                'id' => $subject->id,
                'teacher_id' => $subject->teacher_id,
                'teacher_name' => $subject->teacher->name ?? 'No Teacher',
                'has_project' => $subject->projects->count() > 0,
                'assigned_team' => $assignedTeam
            ];
        });

        // Prepare teams data for JavaScript
        $teamsData = $teamsWithoutDefense->map(function($team) {
            $hasProject = $team->project !== null;
            $projectTitle = $hasProject ? ($team->project->subject ? $team->project->subject->title : 'Project without subject') : 'No Subject Chosen';

            return [
                'id' => $team->id,
                'name' => $team->name,
                'members' => $team->members->map(function($member) {
                    return $member->user->name;
                })->toArray(),
                'project_title' => $projectTitle,
                'has_project' => $hasProject,
                'subject_id' => $hasProject && $team->project->subject ? $team->project->subject->id : null,
                'status' => $hasProject ? 'Has Project' : 'No Subject Chosen'
            ];
        });

        return view('defenses.schedule', compact('subjects', 'rooms', 'teachers', 'teamsWithoutDefense', 'subjectData', 'teamsData'));
    }

    /**
     * Schedule a defense (for admins/department heads)
     */
    public function schedule(Request $request): RedirectResponse
    {
        $this->authorize('schedule', Defense::class);

        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'team_id' => 'required|exists:teams,id',
            'defense_date' => 'required|date|after_or_equal:today',
            'defense_time' => 'required|date_format:H:i',
            'room_id' => 'required|exists:rooms,id',
            'supervisor_id' => 'required|exists:users,id',
            'president_id' => 'required|exists:users,id|different:supervisor_id',
            'examiner_id' => 'required|exists:users,id|different:supervisor_id,president_id',
            'notes' => 'nullable|string|max:500'
        ]);

        // Check if subject exists and is validated
        $subject = Subject::find($validated['subject_id']);

        if (!$subject) {
            return redirect()->back()
                ->with('error', 'Subject not found.');
        }

        if ($subject->status !== 'validated') {
            return redirect()->back()
                ->with('error', 'Only validated subjects can have defenses scheduled.');
        }

        // Check if deadline for subject choice has passed (NEW VALIDATION)
        $deadline = AllocationDeadline::where('academic_year', $subject->academic_year)
            ->where('level', $subject->level)
            ->where('status', '!=', 'draft')
            ->first();

        if ($deadline && !$deadline->canScheduleDefenses()) {
            return redirect()->back()
                ->with('error', 'La soutenance ne peut pas être programmée car la date limite de choix des sujets n\'est pas encore dépassée. Veuillez attendre après le ' . $deadline->defense_scheduling_allowed_after?->format('d/m/Y H:i') ?? $deadline->preferences_deadline->format('d/m/Y H:i') . '.');
        }

        // Check if subject already has a defense scheduled
        $existingDefense = Defense::where('subject_id', $validated['subject_id'])->first();

        if ($existingDefense) {
            return redirect()->back()
                ->with('error', 'Subject already has a defense scheduled.');
        }

        // Check room availability
        $conflictingDefense = Defense::where('room_id', $validated['room_id'])
            ->where('defense_date', $validated['defense_date'])
            ->where('defense_time', $validated['defense_time'])
            ->first();

        if ($conflictingDefense) {
            return redirect()->back()
                ->with('error', 'Room is not available at the selected time.');
        }

        // Check if team already has a defense scheduled
        $team = Team::find($validated['team_id']);
        $existingTeamDefense = Defense::whereHas('project.team', function($q) use ($team) {
            $q->where('id', $team->id);
        })->first();

        if ($existingTeamDefense) {
            return redirect()->back()
                ->with('error', "Team {$team->name} already has a defense scheduled.");
        }

        // Check jury availability
        $juryMembers = [$validated['supervisor_id'], $validated['president_id'], $validated['examiner_id']];
        $conflictingJury = DefenseJury::whereIn('teacher_id', $juryMembers)
            ->whereHas('defense', function($q) use ($validated) {
                $q->where('defense_date', $validated['defense_date'])
                  ->where('defense_time', $validated['defense_time']);
            })->with('teacher', 'defense.subject')->first();

        if ($conflictingJury) {
            $teacherName = $conflictingJury->teacher->name ?? 'Unknown Teacher';
            $conflictSubject = $conflictingJury->defense->subject->title ?? 'Unknown Subject';
            return redirect()->back()
                ->with('error', "Teacher {$teacherName} is already assigned to another defense at this time (Subject: {$conflictSubject}). Please choose a different time or jury member.");
        }

        // Check if team already has a defense scheduled
        $team = Team::find($validated['team_id']);
        $existingTeamDefense = Defense::whereHas('project.team', function($q) use ($team) {
            $q->where('id', $team->id);
        })->first();

        if ($existingTeamDefense) {
            return redirect()->back()
                ->with('error', "Team {$team->name} already has a defense scheduled.");
        }

        // ✅ IMPROVED: Ensure project exists or create it properly
        // First, check if a project already exists for this team and subject
        $project = Project::where('team_id', $validated['team_id'])
            ->where('subject_id', $validated['subject_id'])
            ->first();

        // If no project exists, create one
        if (!$project) {
            $project = Project::create([
                'team_id' => $validated['team_id'],
                'subject_id' => $validated['subject_id'],
                'supervisor_id' => $subject->teacher_id,
                'academic_year' => $subject->academic_year ?? '2024-2025',
                'type' => $subject->is_external ? 'external' : 'internal',
                'status' => 'assigned',
                'started_at' => now(),
            ]);
        }

        // Validate project is ready for defense
        if (!in_array($project->status, ['submitted', 'assigned', 'in_progress'])) {
            return redirect()->back()
                ->with('error', 'Project must be in a valid status (assigned, in_progress, or submitted) to schedule a defense.');
        }

        DB::beginTransaction();
        try {
            // Create defense
            // ✅ FIXED: Use SettingsService instead of hardcoded duration
            $defaultDuration = \App\Services\SettingsService::getDefenseDuration();

            $defense = Defense::create([
                'project_id' => $project->id,
                'subject_id' => $validated['subject_id'],
                'defense_date' => $validated['defense_date'],
                'defense_time' => $validated['defense_time'],
                'room_id' => $validated['room_id'],
                'duration' => $defaultDuration,
                'status' => 'scheduled',
                'notes' => $validated['notes'],
                'scheduled_by' => Auth::id(),
                'scheduled_at' => now()
            ]);

            // Create jury assignments with specific roles
            DefenseJury::create([
                'defense_id' => $defense->id,
                'teacher_id' => $validated['supervisor_id'],
                'role' => 'supervisor'
            ]);

            DefenseJury::create([
                'defense_id' => $defense->id,
                'teacher_id' => $validated['president_id'],
                'role' => 'president'
            ]);

            DefenseJury::create([
                'defense_id' => $defense->id,
                'teacher_id' => $validated['examiner_id'],
                'role' => 'examiner'
            ]);

            DB::commit();

            return redirect()->route('defenses.schedule-form')
                ->with('success', 'Defense scheduled successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to schedule defense: ' . $e->getMessage());
        }
    }

    /**
     * Auto schedule defenses (for admins/department heads)
     */
    public function autoSchedule(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'daily_limit' => 'required|integer|min:1|max:10',
            'working_days' => 'required|array|min:1',
            'working_days.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'optimize_jury_distribution' => 'boolean',
            'respect_teacher_preferences' => 'boolean',
            'balance_room_usage' => 'boolean'
        ]);

        try {
            $defenseSchedulingService = new DefenseSchedulingService();

            // ✅ FIXED: Use SettingsService instead of hardcoded duration
            $defaultDuration = \App\Services\SettingsService::getDefenseDuration();

            $constraints = [
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'duration' => $defaultDuration,
                'include_weekends' => in_array('saturday', $validated['working_days']) || in_array('sunday', $validated['working_days'])
            ];

            $result = $defenseSchedulingService->autoScheduleDefenses($constraints);

            if (empty($result['scheduled'])) {
                return redirect()->back()
                    ->with('warning', 'No defenses could be scheduled. ' . ($result['message'] ?? 'Please check available resources and constraints.'));
            }

            $scheduled = count($result['scheduled']);
            $failed = count($result['failed'] ?? []);

            $message = "Auto-scheduling completed! Scheduled {$scheduled} defenses.";
            if ($failed > 0) {
                $message .= " {$failed} defenses could not be scheduled due to conflicts or resource constraints.";
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to auto-schedule defenses: ' . $e->getMessage());
        }
    }

    /**
     * Edit defense (for admins/department heads)
     */
    public function edit(Defense $defense): View
    {
        $this->authorize('update', $defense);

        $rooms = Room::orderBy('name')->get();
        $teachers = User::where('role', 'teacher')->orderBy('name')->get();

        $defense->load(['juries.teacher']);

        return view('defenses.edit', compact('defense', 'rooms', 'teachers'));
    }

    /**
     * Update defense (for admins/department heads)
     */
    public function update(Request $request, Defense $defense): RedirectResponse
    {
        $this->authorize('update', $defense);

        $validated = $request->validate([
            'defense_date' => 'required|date',
            'defense_time' => 'required|date_format:H:i',
            'room_id' => 'required|exists:rooms,id',
            'duration' => 'required|integer|min:30|max:180',
            'notes' => 'nullable|string|max:500',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
            'supervisor_id' => 'required|exists:users,id',
            'president_id' => 'required|exists:users,id|different:supervisor_id',
            'examiner_id' => 'required|exists:users,id|different:supervisor_id,president_id',
        ]);

        // Check room availability (excluding current defense)
        $conflictingDefense = Defense::where('room_id', $validated['room_id'])
            ->where('defense_date', $validated['defense_date'])
            ->where('defense_time', $validated['defense_time'])
            ->where('id', '!=', $defense->id)
            ->first();

        if ($conflictingDefense) {
            return redirect()->back()
                ->with('error', 'Room is not available at the selected time.');
        }

        // Check jury availability (excluding current defense)
        $juryMembers = [$validated['supervisor_id'], $validated['president_id'], $validated['examiner_id']];
        $conflictingJury = DefenseJury::whereIn('teacher_id', $juryMembers)
            ->whereHas('defense', function($q) use ($validated, $defense) {
                $q->where('defense_date', $validated['defense_date'])
                  ->where('defense_time', $validated['defense_time'])
                  ->where('id', '!=', $defense->id);
            })->first();

        if ($conflictingJury) {
            return redirect()->back()
                ->with('error', 'One or more jury members are not available at the selected time.');
        }

        DB::beginTransaction();
        try {
            // Update defense
            $defense->update([
                'defense_date' => $validated['defense_date'],
                'defense_time' => $validated['defense_time'],
                'room_id' => $validated['room_id'],
                'duration' => $validated['duration'],
                'notes' => $validated['notes'],
                'status' => $validated['status']
            ]);

            // Update jury members - delete old and create new
            $defense->juries()->delete();

            // Create new jury assignments
            DefenseJury::create([
                'defense_id' => $defense->id,
                'teacher_id' => $validated['supervisor_id'],
                'role' => 'supervisor'
            ]);

            DefenseJury::create([
                'defense_id' => $defense->id,
                'teacher_id' => $validated['president_id'],
                'role' => 'president'
            ]);

            DefenseJury::create([
                'defense_id' => $defense->id,
                'teacher_id' => $validated['examiner_id'],
                'role' => 'examiner'
            ]);

            DB::commit();

            return redirect()->route('defenses.show', $defense)
                ->with('success', __('app.defense_updated'));

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to update defense: ' . $e->getMessage());
        }
    }

    /**
     * Cancel defense (for admins/department heads)
     */
    public function cancel(Defense $defense): RedirectResponse
    {
        $this->authorize('delete', $defense);

        if ($defense->status === 'completed') {
            return redirect()->back()
                ->with('error', 'Cannot cancel a completed defense.');
        }

        $defense->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancelled_by' => Auth::id()
        ]);

        return redirect()->route('defenses.index')
            ->with('success', 'Defense cancelled successfully!');
    }

    /**
     * Complete defense (for admins/department heads)
     */
    public function complete(Defense $defense): RedirectResponse
    {
        $this->authorize('update', $defense);

        if ($defense->status !== 'in_progress' && $defense->status !== 'scheduled') {
            return redirect()->back()
                ->with('error', 'Cannot complete defense with current status.');
        }

        $defense->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);

        return redirect()->route('defenses.show', $defense)
            ->with('success', 'Defense marked as completed!');
    }

    /**
     * Submit grade for defense (for jury members)
     */
    public function submitGrade(Request $request, Defense $defense): RedirectResponse
    {
        $this->authorize('grade', $defense);

        $validated = $request->validate([
            'presentation_grade' => 'required|numeric|min:0|max:20',
            'technical_grade' => 'required|numeric|min:0|max:20',
            'defense_grade' => 'required|numeric|min:0|max:20',
            'overall_grade' => 'required|numeric|min:0|max:20',
            'comments' => 'required|string',
            'recommendations' => 'nullable|string'
        ]);

        $user = Auth::user();
        $juryMember = $defense->juries->where('teacher_id', $user->id)->first();

        if (!$juryMember) {
            return redirect()->back()
                ->with('error', 'You are not a jury member for this defense.');
        }

        // Update or create defense report
        $defense->report()->updateOrCreate(
            ['defense_id' => $defense->id],
            [
                'presentation_grade' => $validated['presentation_grade'],
                'technical_grade' => $validated['technical_grade'],
                'defense_grade' => $validated['defense_grade'],
                'overall_grade' => $validated['overall_grade'],
                'comments' => $validated['comments'],
                'recommendations' => $validated['recommendations'],
                'graded_by' => $user->id,
                'graded_at' => now()
            ]
        );

        return redirect()->back()
            ->with('success', 'Grade submitted successfully!');
    }


    /**
     * Generate defense report as PDF
     */
    public function downloadReportPdf(Defense $defense)
    {
        $this->authorize('viewReport', $defense);

        $user = Auth::user();

        // Load necessary relationships early
        $defense->load(['project.team.members.user.speciality', 'juries.teacher']);

        // Check if user can view this defense report
        if ($user->role === 'student') {
            // Students can only view their own defense reports
            $teamMember = $defense->project->team->members()->where('student_id', $user->id)->first();
            if (!$teamMember) {
                abort(403, 'Unauthorized');
            }
        } elseif ($user->role === 'teacher') {
            // Teachers can view reports for defenses they're involved in
            $isInvolved = $defense->juries()->where('teacher_id', $user->id)->exists() ||
                         $defense->subject->teacher_id === $user->id;
            if (!$isInvolved) {
                abort(403, 'Unauthorized');
            }
        }
        // Admin and department_head can view all reports

        // Validate that all required relationships exist
        if (!$defense->project) {
            abort(404, 'Defense project not found');
        }

        if (!$defense->project->team) {
            abort(404, 'Defense team not found');
        }

        // Get all team members for generating individual pages
        $teamMembers = $defense->project->team->members()->with('user.speciality')->get();
        if ($teamMembers->isEmpty()) {
            abort(404, 'No team members found');
        }

        // Get jury members
        $juries = $defense->juries()->with('teacher')->get();

        // Get current academic year
        $currentDate = now();
        $academicYear = $currentDate->month >= 9
            ? $currentDate->year . '/' . ($currentDate->year + 1)
            : ($currentDate->year - 1) . '/' . $currentDate->year;

        // Generate PDF with one page per student
        $pdf = \PDF::loadView('defenses.report', compact(
            'defense',
            'teamMembers',
            'juries',
            'academicYear'
        ) + ['isPdf' => true]);

        // Configure PDF options
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'dpi' => 150,
            'defaultFont' => 'Times-Roman',
            'isRemoteEnabled' => true,
        ]);

        // Generate filename using team name or first student name
        $teamName = $defense->project->team->name ?? 'Team';
        $filename = 'PV_Soutenance_' . str_replace(' ', '_', $teamName) . '_' .
                   ($defense->defense_date ? $defense->defense_date->format('Y-m-d') : date('Y-m-d')) . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Download individual student defense report as PDF
     */
    public function downloadStudentReportPdf(Defense $defense, User $student, ReportService $reportService)
    {
        // Validate that the student belongs to the defense team
        if (!$defense->project || !$defense->project->team ||
            !$defense->project->team->members->pluck('student_id')->contains($student->id)) {
            abort(404, 'Student not found in this defense team.');
        }

        try {
            $pdfContent = $reportService->generateStudentDefenseReport($defense, $student);
            $filename = 'PV_Soutenance_' . str_replace(' ', '_', $student->name) . '_' . ($defense->defense_date ? \Carbon\Carbon::parse($defense->defense_date)->format('Y-m-d') : date('Y-m-d')) . '.pdf';

            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to generate report: ' . $e->getMessage());
        }
    }

    /**
     * Download batch reports for all students in a team as ZIP
     */
    public function downloadBatchStudentReports(Defense $defense, ReportService $reportService)
    {
        if (!$defense->project || !$defense->project->team || $defense->project->team->members->isEmpty()) {
            return redirect()->back()
                ->with('error', 'No team members found for this defense.');
        }

        try {
            $reports = $reportService->generateBatchStudentReports($defense);

            if (empty($reports)) {
                return redirect()->back()
                    ->with('error', 'No reports to generate.');
            }

            // Create temporary ZIP file
            $tempFile = tempnam(sys_get_temp_dir(), 'defense_reports_');
            $zip = new ZipArchive();

            if ($zip->open($tempFile, ZipArchive::CREATE) !== TRUE) {
                return redirect()->back()
                    ->with('error', 'Failed to create ZIP file.');
            }

            // Add each report to the ZIP
            foreach ($reports as $report) {
                $zip->addFromString($report['filename'], $report['content']);
            }

            $zip->close();

            // Generate ZIP filename
            $teamName = str_replace(' ', '_', $defense->project->team->name ?? 'Team');
            $date = $defense->defense_date ? \Carbon\Carbon::parse($defense->defense_date)->format('Y-m-d') : date('Y-m-d');
            $zipFilename = "PV_Soutenance_{$teamName}_{$date}_All_Students.zip";

            return response()->download($tempFile, $zipFilename)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to generate batch reports: ' . $e->getMessage());
        }
    }

    /**
     * Delete a defense (for admins only)
     */
    public function destroy(Defense $defense): RedirectResponse
    {
        try {
            // Only admins can delete defenses
            if (auth()->user()->role !== 'admin') {
                return redirect()->back()
                    ->with('error', __('app.unauthorized_action'));
            }

            // Store defense info for the success message
            $defenseInfo = $defense->subject?->title ?? __('app.defense');

            // Delete the defense
            $defense->delete();

            return redirect()->route('defenses.index')
                ->with('success', __('app.defense_deleted_successfully', ['defense' => $defenseInfo]));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('app.defense_delete_failed'));
        }
    }

    /**
     * Add PV de soutenance notes (for defense president)
     */
    public function addPvNotes(Request $request, Defense $defense): RedirectResponse
    {
        // Check if user is the defense president
        $isPresident = $defense->juries->where('teacher_id', auth()->id())->where('role', 'president')->count() > 0;

        if (!$isPresident) {
            return redirect()->back()
                ->with('error', __('app.only_president_can_add_pv_notes'));
        }

        // Validate that defense is in progress
        if ($defense->status !== 'in_progress') {
            return redirect()->back()
                ->with('error', __('app.can_only_add_pv_notes_during_defense'));
        }

        $request->validate([
            'pv_notes' => 'required|string|max:2000',
        ]);

        $defense->update([
            'pv_notes' => $request->pv_notes,
        ]);

        return redirect()->back()
            ->with('success', __('app.pv_notes_saved_successfully'));
    }

    /**
     * Generate defense report (Procès-Verbal) for all team members
     */
    public function generateReport(Defense $defense): View
    {
        $user = Auth::user();

        // Load necessary relationships early
        $defense->load(['project.team.members.user.speciality', 'juries.teacher']);

        // Check if user can view this defense report
        if ($user->role === 'student') {
            // Students can only view their own defense reports
            $teamMember = $defense->project->team->members()->where('student_id', $user->id)->first();
            if (!$teamMember) {
                abort(403, 'Unauthorized');
            }
        } elseif ($user->role === 'teacher') {
            // Teachers can view reports for defenses they're involved in
            $isInvolved = $defense->juries()->where('teacher_id', $user->id)->exists() ||
                         $defense->subject->teacher_id === $user->id;
            if (!$isInvolved) {
                abort(403, 'Unauthorized');
            }
        }
        // Admin and department_head can view all reports

        // Validate that all required relationships exist
        if (!$defense->project) {
            abort(404, 'Defense project not found');
        }

        if (!$defense->project->team) {
            abort(404, 'Defense team not found');
        }

        // Get ALL team members for generating individual pages
        $teamMembers = $defense->project->team->members()->with('user.speciality')->get();
        if ($teamMembers->isEmpty()) {
            abort(404, 'No team members found');
        }

        // Get jury members
        $juries = $defense->juries()->with('teacher')->get();

        // Get current academic year
        $currentDate = now();
        $academicYear = $currentDate->month >= 9
            ? $currentDate->year . '/' . ($currentDate->year + 1)
            : ($currentDate->year - 1) . '/' . $currentDate->year;

        return view('defenses.report', compact(
            'defense',
            'teamMembers',
            'juries',
            'academicYear'
        ));
    }

    /**
     * Download defense report as PDF
     */
    public function downloadReport(Defense $defense)
    {
        $user = Auth::user();

        // Load necessary relationships early
        $defense->load(['project.team.members.user.speciality', 'juries.teacher']);

        // Check if user can view this defense report
        if ($user->role === 'student') {
            // Students can only view their own defense reports
            $teamMember = $defense->project->team->members()->where('student_id', $user->id)->first();
            if (!$teamMember) {
                abort(403, 'Unauthorized');
            }
        } elseif ($user->role === 'teacher') {
            // Teachers can view reports for defenses they're involved in
            $isInvolved = $defense->juries()->where('teacher_id', $user->id)->exists() ||
                         $defense->subject->teacher_id === $user->id;
            if (!$isInvolved) {
                abort(403, 'Unauthorized');
            }
        }
        // Admin and department_head can view all reports

        // Validate that all required relationships exist
        if (!$defense->project) {
            abort(404, 'Defense project not found');
        }

        if (!$defense->project->team) {
            abort(404, 'Defense team not found');
        }

        // Get the first team member for student data
        $teamMember = $defense->project->team->members()->first();
        if (!$teamMember) {
            abort(404, 'Team member not found');
        }

        if (!$teamMember->user) {
            abort(404, 'User data not found');
        }

        $userData = $teamMember->user;

        // Get jury members
        $juries = $defense->juries()->with('teacher')->get();

        // Get current academic year
        $currentDate = now();
        $academicYear = $currentDate->month >= 9
            ? $currentDate->year . '/' . ($currentDate->year + 1)
            : ($currentDate->year - 1) . '/' . $currentDate->year;

        // Generate PDF
        $pdf = \PDF::loadView('defenses.report', compact(
            'defense',
            'userData',
            'juries',
            'academicYear'
        ) + ['isPdf' => true]);

        // Configure PDF options
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'dpi' => 150,
            'defaultFont' => 'Times-Roman',
            'isRemoteEnabled' => true,
        ]);

        // Generate filename
        $filename = 'PV_Soutenance_' . str_replace(' ', '_', $userData->name) . '_' .
                   ($defense->defense_date ? $defense->defense_date->format('Y-m-d') : date('Y-m-d')) . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Show form to edit student data for defense report
     */
    public function editStudentData(Defense $defense): View
    {
        // Check authorization
        if (!in_array(auth()->user()->role, ['admin', 'department_head'])) {
            abort(403, 'Unauthorized');
        }

        // Load necessary relationships including speciality
        $defense->load([
            'project.team.members.user.speciality',
            'juries.teacher',
            'subject.teacher'
        ]);

        // Validate that all required relationships exist
        if (!$defense->project || !$defense->project->team) {
            abort(404, 'Defense team not found');
        }

        // Get all team members with speciality relationship
        $teamMembers = $defense->project->team->members()->with('user.speciality')->get();
        if ($teamMembers->isEmpty()) {
            abort(404, 'No team members found');
        }

        return view('defenses.report-editable', compact('defense', 'teamMembers'));
    }

    /**
     * Update student data for defense report
     */
    public function updateStudentData(Request $request, Defense $defense): RedirectResponse
    {
        // Check authorization
        if (!in_array(auth()->user()->role, ['admin', 'department_head'])) {
            abort(403, 'Unauthorized');
        }

        // Validate the request
        $validated = $request->validate([
            'students' => 'required|array|min:1',
            'students.*.user_id' => 'required|exists:users,id',
            'students.*.name' => 'required|string|max:255',
            'students.*.email' => 'required|email|max:255',
            'students.*.speciality_id' => 'nullable|exists:specialities,id',
            'students.*.date_naissance' => 'required|date|before:today',
            'students.*.lieu_naissance' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            // Update each student's data
            foreach ($validated['students'] as $studentData) {
                $user = User::find($studentData['user_id']);
                if ($user) {
                    // Check email uniqueness (excluding current user)
                    $emailExists = User::where('email', $studentData['email'])
                        ->where('id', '!=', $user->id)
                        ->exists();
                    
                    if ($emailExists) {
                        throw new \Exception("Email {$studentData['email']} is already in use by another user.");
                    }

                    $user->update([
                        'name' => $studentData['name'],
                        'email' => $studentData['email'],
                        'speciality_id' => $studentData['speciality_id'] ?? null,
                        'date_naissance' => $studentData['date_naissance'],
                        'lieu_naissance' => $studentData['lieu_naissance'],
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('defenses.report', $defense)
                ->with('success', __('app.student_data_updated_successfully'));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update student data: ' . $e->getMessage());
        }
    }

    /**
     * Update defense grades (for admin and department heads only)
     */
    public function updateGrades(Request $request, Defense $defense)
    {
        // Check authorization
        if (!in_array(auth()->user()->role, ['admin', 'department_head'])) {
            abort(403, 'Unauthorized');
        }

        // Validate the request
        $validated = $request->validate([
            'manuscript_grade' => 'nullable|numeric|min:0|max:8',
            'oral_grade' => 'nullable|numeric|min:0|max:6',
            'questions_grade' => 'nullable|numeric|min:0|max:6',
            'realization_grade' => 'nullable|numeric|min:0|max:20',
            'final_grade' => 'nullable|numeric|min:0|max:20',
        ]);

        // Update the defense with the new grades
        $defense->update($validated);

        return redirect()->route('defenses.show', $defense)
            ->with('success', __('app.grades_updated_successfully'));
    }
}
