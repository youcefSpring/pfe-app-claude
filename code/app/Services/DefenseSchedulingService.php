<?php

namespace App\Services;

use App\Models\Defense;
use App\Models\PfeProject;
use App\Models\Room;
use App\Models\User;
use App\Models\PfeNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class DefenseSchedulingService
{
    private const MIN_DEFENSE_DURATION = 30;
    private const MAX_DEFENSE_DURATION = 120;
    private const PREPARATION_TIME = 15; // minutes between defenses
    private const WORKING_HOURS_START = '08:00';
    private const WORKING_HOURS_END = '18:00';

    public function scheduleDefenses(array $projectIds, array $options = []): array
    {
        $projects = PfeProject::whereIn('id', $projectIds)
            ->where('status', 'ready_for_defense')
            ->with(['team.members.user', 'supervisor'])
            ->get();

        return DB::transaction(function () use ($projects, $options) {
            $scheduled = [];
            $conflicts = [];

            // Get available resources
            $rooms = Room::where('is_available', true)->get();
            $juryPool = $this->getAvailableJuryMembers();

            // Sort projects by priority (supervisor workload, team formation date, etc.)
            $sortedProjects = $this->prioritizeProjects($projects);

            foreach ($sortedProjects as $project) {
                try {
                    $defense = $this->scheduleProjectDefense($project, $rooms, $juryPool, $options);
                    $scheduled[] = $defense;
                } catch (ValidationException $e) {
                    $conflicts[] = [
                        'project_id' => $project->id,
                        'project_title' => $project->subject->title,
                        'team_name' => $project->team->name,
                        'error' => $e->getMessage()
                    ];
                }
            }

            return [
                'scheduled' => $scheduled,
                'conflicts' => $conflicts,
                'success_rate' => count($scheduled) / max(count($projects), 1) * 100
            ];
        });
    }

    public function rescheduleDefense(Defense $defense, array $newSchedule): Defense
    {
        $this->validateReschedule($defense, $newSchedule);

        return DB::transaction(function () use ($defense, $newSchedule) {
            $oldSchedule = [
                'date' => $defense->defense_date,
                'time' => $defense->start_time,
                'room' => $defense->room->name
            ];

            $defense->update([
                'defense_date' => $newSchedule['date'],
                'start_time' => $newSchedule['start_time'],
                'end_time' => $newSchedule['end_time'],
                'room_id' => $newSchedule['room_id'],
                'status' => 'rescheduled'
            ]);

            $this->notifyReschedule($defense, $oldSchedule);

            return $defense;
        });
    }

    public function generateJuryComposition(PfeProject $project, array $constraints = []): array
    {
        $supervisor = $project->supervisor;
        $department = $supervisor->department;

        // Get available jury members
        $availableJury = User::role(['teacher', 'chef_master'])
            ->where('is_active', true)
            ->withCount('presidedDefenses', 'examinedDefenses')
            ->get();

        // Select jury president (senior teacher, not supervisor)
        $president = $availableJury
            ->where('id', '!=', $supervisor->id)
            ->where('department', $department)
            ->sortBy('presided_defenses_count')
            ->first();

        // Select examiner (different department or domain expertise)
        $examiner = $availableJury
            ->where('id', '!=', $supervisor->id)
            ->where('id', '!=', $president->id)
            ->sortBy('examined_defenses_count')
            ->first();

        if (!$president || !$examiner) {
            throw ValidationException::withMessages([
                'jury' => 'Unable to form complete jury for this defense'
            ]);
        }

        return [
            'jury_president_id' => $president->id,
            'jury_examiner_id' => $examiner->id,
            'jury_supervisor_id' => $supervisor->id
        ];
    }

    public function checkAvailability(string $date, string $startTime, string $endTime, array $juryIds, int $roomId): array
    {
        $conflicts = [];

        // Check room availability
        $roomConflict = Defense::where('room_id', $roomId)
            ->where('defense_date', $date)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<=', $startTime)
                          ->where('end_time', '>=', $endTime);
                    });
            })
            ->exists();

        if ($roomConflict) {
            $conflicts[] = 'Room is not available at this time';
        }

        // Check jury availability
        foreach ($juryIds as $juryId) {
            $juryConflict = Defense::where('defense_date', $date)
                ->where(function ($query) use ($juryId) {
                    $query->where('jury_president_id', $juryId)
                        ->orWhere('jury_examiner_id', $juryId)
                        ->orWhere('jury_supervisor_id', $juryId);
                })
                ->where(function ($query) use ($startTime, $endTime) {
                    $query->whereBetween('start_time', [$startTime, $endTime])
                        ->orWhereBetween('end_time', [$startTime, $endTime]);
                })
                ->exists();

            if ($juryConflict) {
                $user = User::find($juryId);
                $conflicts[] = "Jury member {$user->first_name} {$user->last_name} is not available";
            }
        }

        return $conflicts;
    }

    private function scheduleProjectDefense(PfeProject $project, Collection $rooms, Collection $juryPool, array $options): Defense
    {
        // Generate jury composition
        $juryComposition = $this->generateJuryComposition($project);

        // Find available time slot
        $timeSlot = $this->findAvailableTimeSlot($rooms, array_values($juryComposition), $options);

        if (!$timeSlot) {
            throw ValidationException::withMessages([
                'schedule' => 'No available time slot found for this defense'
            ]);
        }

        // Create defense record
        $defense = Defense::create(array_merge([
            'project_id' => $project->id,
            'room_id' => $timeSlot['room_id'],
            'defense_date' => $timeSlot['date'],
            'start_time' => $timeSlot['start_time'],
            'end_time' => $timeSlot['end_time'],
            'duration' => $options['duration'] ?? 60,
            'status' => 'scheduled'
        ], $juryComposition));

        $this->notifyDefenseScheduled($defense);

        return $defense;
    }

    private function findAvailableTimeSlot(Collection $rooms, array $juryIds, array $options): ?array
    {
        $startDate = Carbon::parse($options['start_date'] ?? now()->addWeek());
        $endDate = Carbon::parse($options['end_date'] ?? now()->addMonth());
        $duration = $options['duration'] ?? 60;

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            // Skip weekends
            if ($date->isWeekend()) {
                continue;
            }

            foreach ($rooms as $room) {
                $timeSlots = $this->generateTimeSlots($date, $duration);

                foreach ($timeSlots as $slot) {
                    $conflicts = $this->checkAvailability(
                        $date->format('Y-m-d'),
                        $slot['start'],
                        $slot['end'],
                        $juryIds,
                        $room->id
                    );

                    if (empty($conflicts)) {
                        return [
                            'room_id' => $room->id,
                            'date' => $date->format('Y-m-d'),
                            'start_time' => $slot['start'],
                            'end_time' => $slot['end']
                        ];
                    }
                }
            }
        }

        return null;
    }

    private function generateTimeSlots(Carbon $date, int $duration): array
    {
        $slots = [];
        $startTime = Carbon::parse(self::WORKING_HOURS_START);
        $endTime = Carbon::parse(self::WORKING_HOURS_END);

        while ($startTime->addMinutes($duration + self::PREPARATION_TIME)->lte($endTime)) {
            $slotEnd = $startTime->copy()->addMinutes($duration);

            $slots[] = [
                'start' => $startTime->format('H:i'),
                'end' => $slotEnd->format('H:i')
            ];

            $startTime->addMinutes($duration + self::PREPARATION_TIME);
        }

        return $slots;
    }

    private function getAvailableJuryMembers(): Collection
    {
        return User::role(['teacher', 'chef_master'])
            ->where('is_active', true)
            ->withCount(['presidedDefenses', 'examinedDefenses', 'supervisedDefenses'])
            ->get();
    }

    private function prioritizeProjects(Collection $projects): Collection
    {
        return $projects->sortBy(function ($project) {
            $score = 0;

            // Priority 1: Supervisor workload (less loaded = higher priority)
            $supervisorLoad = $project->supervisor->supervisedDefenses()->count();
            $score += (10 - $supervisorLoad) * 10;

            // Priority 2: Team formation date (earlier = higher priority)
            if ($project->team->formation_completed_at) {
                $daysAgo = now()->diffInDays($project->team->formation_completed_at);
                $score += $daysAgo;
            }

            // Priority 3: Project deadline proximity
            $score += now()->diffInDays($project->expected_end_date, false) * 2;

            return -$score; // Negative for descending sort
        });
    }

    private function validateReschedule(Defense $defense, array $newSchedule): void
    {
        if ($defense->status === 'completed') {
            throw ValidationException::withMessages([
                'status' => 'Completed defenses cannot be rescheduled'
            ]);
        }

        $newDate = Carbon::parse($newSchedule['date']);
        if ($newDate->isPast()) {
            throw ValidationException::withMessages([
                'date' => 'Cannot reschedule to a past date'
            ]);
        }

        // Check minimum notice period (e.g., 48 hours)
        if ($newDate->diffInHours(now()) < 48) {
            throw ValidationException::withMessages([
                'notice' => 'Defenses must be rescheduled with at least 48 hours notice'
            ]);
        }
    }

    private function notifyDefenseScheduled(Defense $defense): void
    {
        $recipients = [
            // Team members
            ...$defense->project->team->members()->pluck('user_id'),
            // Jury members
            $defense->jury_president_id,
            $defense->jury_examiner_id,
            $defense->jury_supervisor_id
        ];

        foreach (array_unique($recipients) as $userId) {
            PfeNotification::create([
                'user_id' => $userId,
                'type' => 'defense_scheduled',
                'title' => 'Defense Scheduled',
                'message' => "Defense for '{$defense->project->subject->title}' scheduled on {$defense->defense_date} at {$defense->start_time}",
                'data' => [
                    'defense_id' => $defense->id,
                    'project_id' => $defense->project_id
                ]
            ]);
        }
    }

    private function notifyReschedule(Defense $defense, array $oldSchedule): void
    {
        $recipients = [
            ...$defense->project->team->members()->pluck('user_id'),
            $defense->jury_president_id,
            $defense->jury_examiner_id,
            $defense->jury_supervisor_id
        ];

        foreach (array_unique($recipients) as $userId) {
            PfeNotification::create([
                'user_id' => $userId,
                'type' => 'defense_rescheduled',
                'title' => 'Defense Rescheduled',
                'message' => "Defense for '{$defense->project->subject->title}' rescheduled from {$oldSchedule['date']} to {$defense->defense_date}",
                'data' => [
                    'defense_id' => $defense->id,
                    'old_schedule' => $oldSchedule
                ]
            ]);
        }
    }
}