<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Defense;
use App\Models\DefenseJury;
use App\Models\Project;
use App\Models\Room;
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
    public function index(): View
    {
        $user = Auth::user();

        $query = Defense::with(['project.team.members.user', 'project.subject', 'room', 'juries.teacher']);

        // Filter based on user role
        switch ($user->role) {
            case 'student':
                // Students see defenses from their department
                $query->whereHas('project.team.members.user', function($q) use ($user) {
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
                $query->whereHas('project.team.members.user', function($q) use ($user) {
                    $q->where('department', $user->department);
                });
                break;
            // Admin sees all defenses (no filter)
        }

        $defenses = $query->orderBy('defense_date')->paginate(12);

        return view('defenses.index', compact('defenses'));
    }

    /**
     * Display the specified defense
     */
    public function show(Defense $defense): View
    {
        $defense->load([
            'project.team.members.user',
            'project.subject.teacher',
            'room',
            'juries.teacher',
            'report'
        ]);

        $user = Auth::user();
        $isTeamMember = $defense->project->team->members->contains('student_id', $user->id);
        $isJuryMember = $defense->juries->contains('teacher_id', $user->id);

        return view('defenses.show', compact('defense', 'isTeamMember', 'isJuryMember'));
    }

    /**
     * Show defense calendar
     */
    public function calendar(): View
    {
        $user = Auth::user();

        $query = Defense::with(['project.team', 'room']);

        // Filter based on user role
        switch ($user->role) {
            case 'student':
                $query->whereHas('project.team.members.user', function($q) use ($user) {
                    $q->where('department', $user->department);
                });
                break;
            case 'teacher':
                $query->whereHas('juries', function($q) use ($user) {
                    $q->where('teacher_id', $user->id);
                });
                break;
            case 'department_head':
                $query->whereHas('project.team.members.user', function($q) use ($user) {
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

        $projects = Project::with(['team.members.user', 'subject'])
            ->where('status', 'active')
            ->whereDoesntHave('defense')
            ->get();

        $rooms = Room::orderBy('name')->get();

        $teachers = User::where('role', 'teacher')->orderBy('name')->get();

        return view('defenses.schedule', compact('projects', 'rooms', 'teachers'));
    }

    /**
     * Schedule a defense (for admins/department heads)
     */
    public function schedule(Request $request): RedirectResponse
    {
        //$this->authorize('schedule', Defense::class);

        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'defense_date' => 'required|date|after:now',
            'defense_time' => 'required|date_format:H:i',
            'room_id' => 'required|exists:rooms,id',
            'jury_members' => 'required|array|min:3',
            'jury_members.*' => 'exists:users,id',
            'notes' => 'nullable|string|max:500'
        ]);

        $project = Project::find($validated['project_id']);

        if ($project->defense) {
            return redirect()->back()
                ->with('error', 'Project already has a defense scheduled.');
        }

        // Combine date and time
        $defenseDateTime = $validated['defense_date'] . ' ' . $validated['defense_time'];

        // Check room availability
        $conflictingDefense = Defense::where('room_id', $validated['room_id'])
            ->where('defense_date', $defenseDateTime)
            ->first();

        if ($conflictingDefense) {
            return redirect()->back()
                ->with('error', 'Room is not available at the selected time.');
        }

        // Check jury availability
        $conflictingJury = DefenseJury::whereIn('teacher_id', $validated['jury_members'])
            ->whereHas('defense', function($q) use ($defenseDateTime) {
                $q->where('defense_date', $defenseDateTime);
            })->first();

        if ($conflictingJury) {
            return redirect()->back()
                ->with('error', 'One or more jury members are not available at the selected time.');
        }

        DB::beginTransaction();
        try {
            // Create defense
            $defense = Defense::create([
                'project_id' => $project->id,
                'defense_date' => $defenseDateTime,
                'room_id' => $validated['room_id'],
                'duration' => 90, // Default 90 minutes
                'status' => 'scheduled',
                'notes' => $validated['notes'],
                'scheduled_by' => Auth::id(),
                'scheduled_at' => now()
            ]);

            // Create jury assignments with roles
            $roles = ['president', 'examiner', 'supervisor'];
            foreach ($validated['jury_members'] as $index => $teacherId) {
                DefenseJury::create([
                    'defense_id' => $defense->id,
                    'teacher_id' => $teacherId,
                    'role' => $roles[$index] ?? 'member'
                ]);
            }

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
            'status' => 'required|in:scheduled,in_progress,completed,cancelled'
        ]);

        // Combine date and time
        $defenseDateTime = $validated['defense_date'] . ' ' . $validated['defense_time'];

        // Check room availability (excluding current defense)
        $conflictingDefense = Defense::where('room_id', $validated['room_id'])
            ->where('defense_date', $defenseDateTime)
            ->where('id', '!=', $defense->id)
            ->first();

        if ($conflictingDefense) {
            return redirect()->back()
                ->with('error', 'Room is not available at the selected time.');
        }

        $defense->update([
            'defense_date' => $defenseDateTime,
            'room_id' => $validated['room_id'],
            'duration' => $validated['duration'],
            'notes' => $validated['notes'],
            'status' => $validated['status']
        ]);

        return redirect()->route('defenses.show', $defense)
            ->with('success', 'Defense updated successfully!');
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
     * Generate defense report
     */
    public function generateReport(Defense $defense): View
    {
        //$this->authorize('viewReport', $defense);

        $defense->load([
            'project.team.members.user',
            'project.subject.teacher',
            'room',
            'juries.teacher',
            'report'
        ]);

        return view('defenses.report', compact('defense'));
    }
}
