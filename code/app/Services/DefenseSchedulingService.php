<?php

namespace App\Services;

use App\Models\Defense;
use App\Models\Project;
use App\Models\Room;
use App\Models\User;
use App\Models\DefenseJury;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class DefenseSchedulingService
{
    /**
     * Auto-schedule defenses for ready projects.
     */
    public function autoScheduleDefenses(array $constraints = []): array
    {
        $readyProjects = Project::readyForDefense()
            ->with(['team.members.student', 'supervisor'])
            ->get();

        if ($readyProjects->isEmpty()) {
            return ['message' => 'No projects ready for defense', 'scheduled' => []];
        }

        $scheduled = [];
        $failed = [];

        foreach ($readyProjects as $project) {
            try {
                $defense = $this->scheduleDefense($project, $constraints);
                $scheduled[] = $defense;
            } catch (\Exception $e) {
                $failed[] = [
                    'project' => $project,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return [
            'scheduled' => $scheduled,
            'failed' => $failed,
            'summary' => [
                'total_projects' => $readyProjects->count(),
                'successfully_scheduled' => count($scheduled),
                'failed_to_schedule' => count($failed),
            ]
        ];
    }

    /**
     * Schedule a single defense.
     */
    public function scheduleDefense(Project $project, array $constraints = []): Defense
    {
        // Validate project is ready
        if (!$project->isReadyForDefense()) {
            throw new \Exception('Project is not ready for defense');
        }

        // Check if already scheduled
        if ($project->defense) {
            throw new \Exception('Defense already scheduled for this project');
        }

        // Find available slot
        $slot = $this->findAvailableSlot($constraints);
        if (!$slot) {
            throw new \Exception('No available slots found for defense');
        }

        // Create defense
        $defense = Defense::create([
            'project_id' => $project->id,
            'room_id' => $slot['room']->id,
            'defense_date' => $slot['date'],
            'defense_time' => $slot['time'],
            'duration' => $constraints['duration'] ?? 60,
            'status' => 'scheduled',
        ]);

        // Assign jury
        $this->assignJury($defense, $constraints);

        return $defense;
    }

    /**
     * Find available time slot for defense.
     */
    public function findAvailableSlot(array $constraints = []): ?array
    {
        $startDate = Carbon::parse($constraints['start_date'] ?? 'next monday');
        $endDate = Carbon::parse($constraints['end_date'] ?? $startDate->copy()->addWeeks(4));
        $duration = $constraints['duration'] ?? 60;

        // Defense hours: 8:00 AM to 6:00 PM
        $dailySlots = $this->generateDailyTimeSlots('08:00', '18:00', $duration);

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            // Skip weekends unless specified
            if ($date->isWeekend() && !($constraints['include_weekends'] ?? false)) {
                continue;
            }

            foreach ($dailySlots as $time) {
                $room = $this->findAvailableRoom($date, $time, $duration);
                if ($room) {
                    return [
                        'date' => $date->format('Y-m-d'),
                        'time' => $time,
                        'room' => $room,
                    ];
                }
            }
        }

        return null;
    }

    /**
     * Find available room for specific date and time.
     */
    public function findAvailableRoom(Carbon $date, string $time, int $duration = 60): ?Room
    {
        $availableRooms = Room::available()->get();

        foreach ($availableRooms as $room) {
            if ($room->isAvailableAt($date->format('Y-m-d'), $time, $duration)) {
                return $room;
            }
        }

        return null;
    }

    /**
     * Assign jury to defense.
     */
    public function assignJury(Defense $defense, array $constraints = []): void
    {
        $project = $defense->project;

        // Assign supervisor as jury member
        DefenseJury::create([
            'defense_id' => $defense->id,
            'teacher_id' => $project->supervisor_id,
            'role' => 'supervisor',
        ]);

        // Find and assign president (department head or senior teacher)
        $president = $this->findJuryPresident($defense, $constraints);
        if ($president) {
            DefenseJury::create([
                'defense_id' => $defense->id,
                'teacher_id' => $president->id,
                'role' => 'president',
            ]);
        }

        // Find and assign examiner
        $examiner = $this->findJuryExaminer($defense, $constraints);
        if ($examiner) {
            DefenseJury::create([
                'defense_id' => $defense->id,
                'teacher_id' => $examiner->id,
                'role' => 'examiner',
            ]);
        }
    }

    /**
     * Find suitable jury president.
     */
    private function findJuryPresident(Defense $defense, array $constraints = []): ?User
    {
        // Prefer department heads
        $president = User::where('role', 'department_head')
            ->where('id', '!=', $defense->project->supervisor_id)
            ->whereDoesntHave('juryParticipations', function ($q) use ($defense) {
                $q->whereHas('defense', function ($defenseQuery) use ($defense) {
                    $defenseQuery->where('defense_date', $defense->defense_date)
                        ->where('defense_time', $defense->defense_time);
                });
            })
            ->first();

        // Fallback to senior teachers
        if (!$president) {
            $president = User::where('role', 'teacher')
                ->where('id', '!=', $defense->project->supervisor_id)
                ->whereDoesntHave('juryParticipations', function ($q) use ($defense) {
                    $q->whereHas('defense', function ($defenseQuery) use ($defense) {
                        $defenseQuery->where('defense_date', $defense->defense_date)
                            ->where('defense_time', $defense->defense_time);
                    });
                })
                ->first();
        }

        return $president;
    }

    /**
     * Find suitable jury examiner.
     */
    private function findJuryExaminer(Defense $defense, array $constraints = []): ?User
    {
        return User::where('role', 'teacher')
            ->where('id', '!=', $defense->project->supervisor_id)
            ->whereDoesntHave('juryParticipations', function ($q) use ($defense) {
                $q->whereHas('defense', function ($defenseQuery) use ($defense) {
                    $defenseQuery->where('defense_date', $defense->defense_date)
                        ->where('defense_time', $defense->defense_time);
                });
            })
            ->whereDoesntHave('juryParticipations', function ($q) use ($defense) {
                $q->where('defense_id', $defense->id);
            })
            ->first();
    }

    /**
     * Generate daily time slots.
     */
    private function generateDailyTimeSlots(string $startTime, string $endTime, int $duration): array
    {
        $slots = [];
        $start = Carbon::createFromFormat('H:i', $startTime);
        $end = Carbon::createFromFormat('H:i', $endTime);

        while ($start->lt($end)) {
            $slots[] = $start->format('H:i');
            $start->addMinutes($duration);
        }

        return $slots;
    }

    /**
     * Reschedule defense.
     */
    public function rescheduleDefense(Defense $defense, array $newSlot): bool
    {
        // Validate new slot availability
        $room = $this->findAvailableRoom(
            Carbon::parse($newSlot['date']),
            $newSlot['time'],
            $newSlot['duration'] ?? $defense->duration
        );

        if (!$room) {
            throw new \Exception('Requested slot is not available');
        }

        // Check jury availability
        if (!$this->isJuryAvailable($defense, $newSlot)) {
            throw new \Exception('Jury members are not available at requested time');
        }

        // Update defense
        $defense->update([
            'defense_date' => $newSlot['date'],
            'defense_time' => $newSlot['time'],
            'room_id' => $room->id,
            'duration' => $newSlot['duration'] ?? $defense->duration,
        ]);

        // TODO: Send notifications about rescheduling

        return true;
    }

    /**
     * Check if jury is available for new slot.
     */
    private function isJuryAvailable(Defense $defense, array $newSlot): bool
    {
        $juryIds = $defense->jury()->pluck('teacher_id');

        // Check conflicts with other defenses
        $conflicts = Defense::where('defense_date', $newSlot['date'])
            ->where('defense_time', $newSlot['time'])
            ->where('id', '!=', $defense->id)
            ->whereHas('jury', function ($q) use ($juryIds) {
                $q->whereIn('teacher_id', $juryIds);
            })
            ->exists();

        return !$conflicts;
    }

    /**
     * Get defense calendar for date range.
     */
    public function getDefenseCalendar(Carbon $startDate, Carbon $endDate): array
    {
        $defenses = Defense::whereBetween('defense_date', [$startDate, $endDate])
            ->with(['project.team.members.student', 'room', 'jury.teacher'])
            ->orderBy('defense_date')
            ->orderBy('defense_time')
            ->get();

        $calendar = [];

        foreach ($defenses as $defense) {
            $date = $defense->defense_date->format('Y-m-d');

            if (!isset($calendar[$date])) {
                $calendar[$date] = [];
            }

            $calendar[$date][] = [
                'defense' => $defense,
                'time_slot' => $defense->defense_time->format('H:i') . ' - ' .
                    $defense->defense_time->addMinutes($defense->duration)->format('H:i'),
                'team' => $defense->project->team->name,
                'room' => $defense->room->name,
                'jury_count' => $defense->jury->count(),
            ];
        }

        return $calendar;
    }

    /**
     * Get scheduling conflicts report.
     */
    public function getSchedulingConflicts(): array
    {
        // Room conflicts
        $roomConflicts = $this->findRoomConflicts();

        // Jury conflicts
        $juryConflicts = $this->findJuryConflicts();

        // Incomplete juries
        $incompleteJuries = $this->findIncompleteJuries();

        return [
            'room_conflicts' => $roomConflicts,
            'jury_conflicts' => $juryConflicts,
            'incomplete_juries' => $incompleteJuries,
            'total_conflicts' => count($roomConflicts) + count($juryConflicts) + count($incompleteJuries),
        ];
    }

    /**
     * Find room booking conflicts.
     */
    private function findRoomConflicts(): array
    {
        // Group defenses by date, time, and room
        $defenses = Defense::with(['room', 'project.team'])
            ->orderBy('defense_date')
            ->orderBy('defense_time')
            ->get()
            ->groupBy(function ($defense) {
                return $defense->defense_date->format('Y-m-d') . '_' .
                       $defense->defense_time->format('H:i') . '_' .
                       $defense->room_id;
            });

        $conflicts = [];
        foreach ($defenses as $group) {
            if ($group->count() > 1) {
                $conflicts[] = $group;
            }
        }

        return $conflicts;
    }

    /**
     * Find jury member conflicts.
     */
    private function findJuryConflicts(): array
    {
        // Find teachers assigned to multiple defenses at same time
        $conflicts = [];

        $juryMembers = DefenseJury::with(['defense', 'teacher'])
            ->get()
            ->groupBy('teacher_id');

        foreach ($juryMembers as $teacherId => $participations) {
            $timeSlots = $participations->groupBy(function ($participation) {
                return $participation->defense->defense_date->format('Y-m-d') . '_' .
                       $participation->defense->defense_time->format('H:i');
            });

            foreach ($timeSlots as $slot => $defenses) {
                if ($defenses->count() > 1) {
                    $conflicts[] = [
                        'teacher' => $defenses->first()->teacher,
                        'conflicting_defenses' => $defenses->pluck('defense'),
                        'time_slot' => $slot,
                    ];
                }
            }
        }

        return $conflicts;
    }

    /**
     * Find defenses with incomplete juries.
     */
    private function findIncompleteJuries(): array
    {
        return Defense::whereHas('jury', function ($q) {
            $q->select('defense_id')
              ->groupBy('defense_id')
              ->havingRaw('count(*) < 3'); // Minimum 3 jury members
        })->with(['project.team', 'jury.teacher'])->get()->toArray();
    }
}