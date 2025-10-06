<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Defense;
use App\Models\DefenseJury;
use App\Models\Project;
use App\Models\Room;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        $query = Defense::with(['subject.teacher', 'room', 'juries.teacher']);

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
        }

        $defenses = $query->get();

        return view('defenses.calendar', compact('defenses'));
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
        //$this->authorize('schedule', Defense::class);

        $subjects = Subject::with(['teacher'])
            ->where('status', 'validated')
            ->get();

        $rooms = Room::orderBy('name')->get();

        $teachers = User::whereIn('role', ['teacher','department_head'])->orderBy('name')->get();

        return view('defenses.schedule', compact('subjects', 'rooms', 'teachers'));
    }

    /**
     * Schedule a defense (for admins/department heads)
     */
    public function schedule(Request $request): RedirectResponse
    {
        //$this->authorize('schedule', Defense::class);

        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'defense_date' => 'required|date|after:now',
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

        // Check jury availability
        $juryMembers = [$validated['supervisor_id'], $validated['president_id'], $validated['examiner_id']];
        $conflictingJury = DefenseJury::whereIn('teacher_id', $juryMembers)
            ->whereHas('defense', function($q) use ($validated) {
                $q->where('defense_date', $validated['defense_date'])
                  ->where('defense_time', $validated['defense_time']);
            })->first();

        if ($conflictingJury) {
            return redirect()->back()
                ->with('error', 'One or more jury members are not available at the selected time.');
        }

        DB::beginTransaction();
        try {
            // Create defense
            $defense = Defense::create([
                'subject_id' => $validated['subject_id'],
                'defense_date' => $validated['defense_date'],
                'defense_time' => $validated['defense_time'],
                'room_id' => $validated['room_id'],
                'duration' => 90, // Default 90 minutes
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
            'start_date' => 'required|date|after:now',
            'end_date' => 'required|date|after:start_date',
            'daily_limit' => 'required|integer|min:1|max:10',
            'working_days' => 'required|array|min:1',
            'working_days.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'optimize_jury_distribution' => 'boolean',
            'respect_teacher_preferences' => 'boolean',
            'balance_room_usage' => 'boolean'
        ]);

        // Get unscheduled projects
        $projects = Project::with(['team.members.user', 'subject'])
            ->where('status', 'active')
            ->whereDoesntHave('defense')
            ->get();

        if ($projects->isEmpty()) {
            return redirect()->back()
                ->with('info', 'No unscheduled projects found.');
        }

        $rooms = Room::orderBy('name')->get();
        $teachers = User::where('role', 'teacher')->orderBy('name')->get();

        if ($rooms->isEmpty() || $teachers->count() < 3) {
            return redirect()->back()
                ->with('error', 'Insufficient resources: Need at least 1 room and 3 teachers for scheduling.');
        }

        $scheduled = 0;
        $errors = [];
        $currentDate = new \DateTime($validated['start_date']);
        $endDate = new \DateTime($validated['end_date']);
        $timeSlots = ['08:00', '10:00', '12:00', '14:00', '16:00']; // 2-hour intervals
        $dayNames = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];

        DB::beginTransaction();
        try {
            foreach ($projects as $project) {
                $projectScheduled = false;
                $attempts = 0;
                $maxAttempts = 50; // Prevent infinite loops

                while (!$projectScheduled && $attempts < $maxAttempts && $currentDate <= $endDate) {
                    $dayName = strtolower($dayNames[$currentDate->format('w')]);

                    // Check if this day is a working day
                    if (!in_array($dayName, $validated['working_days'])) {
                        $currentDate->modify('+1 day');
                        $attempts++;
                        continue;
                    }

                    // Check daily defense limit
                    $defensesToday = Defense::whereDate('defense_date', $currentDate->format('Y-m-d'))->count();
                    if ($defensesToday >= $validated['daily_limit']) {
                        $currentDate->modify('+1 day');
                        $attempts++;
                        continue;
                    }

                    // Try each time slot for this day
                    foreach ($timeSlots as $timeSlot) {
                        $defenseDateTime = $currentDate->format('Y-m-d') . ' ' . $timeSlot;

                        // Find available room
                        $availableRoom = null;
                        foreach ($rooms as $room) {
                            $conflictingDefense = Defense::where('room_id', $room->id)
                                ->where('defense_date', $defenseDateTime)
                                ->first();

                            if (!$conflictingDefense) {
                                $availableRoom = $room;
                                break;
                            }
                        }

                        if (!$availableRoom) {
                            continue; // No room available at this time slot
                        }

                        // Find available jury members (need at least 3)
                        $availableTeachers = [];
                        foreach ($teachers as $teacher) {
                            $isAvailable = !DefenseJury::whereHas('defense', function($q) use ($defenseDateTime) {
                                $q->where('defense_date', $defenseDateTime);
                            })->where('teacher_id', $teacher->id)->exists();

                            if ($isAvailable) {
                                $availableTeachers[] = $teacher;
                            }

                            if (count($availableTeachers) >= 3) {
                                break; // We have enough jury members
                            }
                        }

                        if (count($availableTeachers) < 3) {
                            continue; // Not enough jury members available
                        }

                        // Schedule the defense
                        $defense = Defense::create([
                            'project_id' => $project->id,
                            'defense_date' => $defenseDateTime,
                            'room_id' => $availableRoom->id,
                            'duration' => 90,
                            'status' => 'scheduled',
                            'notes' => 'Auto-scheduled by system',
                            'scheduled_by' => Auth::id(),
                            'scheduled_at' => now()
                        ]);

                        // Assign jury members
                        $roles = ['president', 'examiner', 'supervisor'];
                        for ($i = 0; $i < 3; $i++) {
                            DefenseJury::create([
                                'defense_id' => $defense->id,
                                'teacher_id' => $availableTeachers[$i]->id,
                                'role' => $roles[$i]
                            ]);
                        }

                        $scheduled++;
                        $projectScheduled = true;
                        break; // Move to next project
                    }

                    if (!$projectScheduled) {
                        $currentDate->modify('+1 day');
                        $attempts++;
                    }
                }

                if (!$projectScheduled) {
                    $errors[] = "Could not schedule defense for project: {$project->subject->title}";
                }

                // Reset date for next project but advance slightly for distribution
                $currentDate = new \DateTime($validated['start_date']);
                if ($scheduled > 0) {
                    $currentDate->modify('+' . ($scheduled % 7) . ' days'); // Distribute across days
                }
            }

            DB::commit();

            $message = "Auto-scheduling completed! Scheduled {$scheduled} defenses.";
            if (!empty($errors)) {
                $message .= " " . count($errors) . " projects could not be scheduled.";
            }

            return redirect()->route('defenses.schedule-form')
                ->with('success', $message)
                ->with('scheduling_errors', $errors);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Auto-scheduling failed: ' . $e->getMessage());
        }
    }

    /**
     * Edit defense (for admins/department heads)
     */
    public function edit(Defense $defense): View
    {
        //$this->authorize('update', $defense);

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
        //$this->authorize('update', $defense);

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
        //$this->authorize('delete', $defense);

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
        //$this->authorize('update', $defense);

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
        //$this->authorize('grade', $defense);

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
     * Generate defense report (HTML view)
     */
    public function generateReport(Defense $defense): View
    {
        //$this->authorize('viewReport', $defense);

        $defense->load([
            'subject.teacher',
            'room',
            'juries.teacher',
            'report'
        ]);

        // Also load project relationships if they exist
        if ($defense->project_id) {
            $defense->load([
                'project.team.members.user',
                'project.team.speciality'
            ]);
        }

        // Load additional relationships for comprehensive data
        $defense->load([
            'project.team.members.user' => function($query) {
                $query->select('id', 'first_name', 'last_name', 'name', 'date_naissance', 'lieu_naissance');
            }
        ]);

        return view('defenses.report', compact('defense'));
    }

    /**
     * Generate defense report as PDF
     */
    public function downloadReportPdf(Defense $defense)
    {
        //$this->authorize('viewReport', $defense);

        $defense->load([
            'subject.teacher',
            'room',
            'juries.teacher',
            'report'
        ]);

        // Also load project relationships if they exist
        if ($defense->project_id) {
            $defense->load([
                'project.team.members.user',
                'project.team.speciality'
            ]);
        }

        // Load additional relationships for comprehensive data
        $defense->load([
            'project.team.members.user' => function($query) {
                $query->select('id', 'first_name', 'last_name', 'name', 'date_naissance', 'lieu_naissance');
            }
        ]);

        $pdf = \PDF::loadView('defenses.report', compact('defense'));

        // Configure PDF options
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'Arial',
            'isRemoteEnabled' => true,
        ]);

        $filename = 'PV_Soutenance_' . str_replace(' ', '_', $defense->project->subject->title ?? 'Defense') . '_' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
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
}
