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
        // Get projects that are ready for defense (with validated subjects, no existing defenses)
        $readyProjects = Project::with(['subject', 'team.members.user', 'supervisor'])
            ->whereHas('subject', function($q) {
                $q->where('status', 'validated');
            })
            ->whereDoesntHave('defense')
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
            'subject_id' => $project->subject_id,
            'defense_date' => $slot['date'],
            'defense_time' => $slot['time'],
            'room_id' => $slot['room']->id,
            'duration' => $constraints['duration'] ?? 90,
            'status' => 'scheduled',
            'scheduled_by' => auth()->id() ?? 1,
            'scheduled_at' => now()
        ]);

        // Assign jury with tag-based matching
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
        $availableRooms = Room::where('is_available', true)->get();

        foreach ($availableRooms as $room) {
            if ($this->isRoomAvailableAt($room, $date->format('Y-m-d'), $time, $duration)) {
                return $room;
            }
        }

        return null;
    }

    /**
     * Check if room is available at specific date and time.
     */
    private function isRoomAvailableAt(Room $room, string $date, string $time, int $duration): bool
    {
        $startTime = Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $time);
        $endTime = $startTime->copy()->addMinutes($duration);

        // Check for conflicts with existing defenses
        $conflicts = Defense::where('room_id', $room->id)
            ->where('defense_date', $date)
            ->where(function($query) use ($time, $duration) {
                $startTime = Carbon::createFromFormat('H:i', $time);
                $endTime = $startTime->copy()->addMinutes($duration);

                $query->where(function($q) use ($startTime, $endTime) {
                    // Defense starts during our slot
                    $q->whereBetween('defense_time', [$startTime->format('H:i:s'), $endTime->format('H:i:s')])
                    // OR defense ends during our slot
                    ->orWhere(function($subQ) use ($startTime, $endTime) {
                        $subQ->whereRaw('ADDTIME(defense_time, MAKETIME(?, 0, 0)) BETWEEN ? AND ?',
                            [90, $startTime->format('H:i:s'), $endTime->format('H:i:s')]);
                    })
                    // OR our slot is completely inside their defense
                    ->orWhere(function($subQ) use ($startTime, $endTime) {
                        $subQ->where('defense_time', '<=', $startTime->format('H:i:s'))
                             ->whereRaw('ADDTIME(defense_time, MAKETIME(?, 0, 0)) >= ?',
                                [90, $endTime->format('H:i:s')]);
                    });
                });
            })
            ->exists();

        return !$conflicts;
    }

    /**
     * Assign jury to defense.
     */
    public function assignJury(Defense $defense, array $constraints = []): void
    {
        $project = $defense->project;

        // Check if supervisor is available at this time
        $supervisorConflict = DefenseJury::where('teacher_id', $project->supervisor_id)
            ->whereHas('defense', function ($q) use ($defense) {
                $q->where('defense_date', $defense->defense_date)
                  ->where('defense_time', $defense->defense_time)
                  ->where('id', '!=', $defense->id); // Exclude current defense
            })->exists();

        if ($supervisorConflict) {
            throw new \Exception('Supervisor is not available at the selected time - already assigned to another defense.');
        }

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
     * Find suitable jury president with tag-based matching.
     */
    private function findJuryPresident(Defense $defense, array $constraints = []): ?User
    {
        $subjectKeywords = $defense->subject ? strtolower($defense->subject->keywords) : '';

        // Prefer department heads with matching speciality
        $president = User::where('role', 'department_head')
            ->where('id', '!=', $defense->project->supervisor_id)
            ->whereDoesntHave('juryParticipations', function ($q) use ($defense) {
                $q->whereHas('defense', function ($defenseQuery) use ($defense) {
                    $defenseQuery->where('defense_date', $defense->defense_date)
                        ->where('defense_time', $defense->defense_time);
                });
            })
            ->get()
            ->sortByDesc(function($teacher) use ($subjectKeywords) {
                return $this->calculateSpecialityMatch($teacher->speciality, $subjectKeywords);
            })
            ->first();

        // Fallback to teachers with matching speciality
        if (!$president) {
            $president = User::where('role', 'teacher')
                ->where('id', '!=', $defense->project->supervisor_id)
                ->whereDoesntHave('juryParticipations', function ($q) use ($defense) {
                    $q->whereHas('defense', function ($defenseQuery) use ($defense) {
                        $defenseQuery->where('defense_date', $defense->defense_date)
                            ->where('defense_time', $defense->defense_time);
                    });
                })
                ->get()
                ->sortByDesc(function($teacher) use ($subjectKeywords) {
                    return $this->calculateSpecialityMatch($teacher->speciality, $subjectKeywords);
                })
                ->first();
        }

        return $president;
    }

    /**
     * Find suitable jury examiner with tag-based matching.
     */
    private function findJuryExaminer(Defense $defense, array $constraints = []): ?User
    {
        $subjectKeywords = $defense->subject ? strtolower($defense->subject->keywords) : '';

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
            ->get()
            ->sortByDesc(function($teacher) use ($subjectKeywords) {
                return $this->calculateSpecialityMatch($teacher->speciality, $subjectKeywords);
            })
            ->first();
    }

    /**
     * Calculate how well a teacher's speciality matches subject keywords.
     */
    private function calculateSpecialityMatch(?string $teacherSpeciality, string $subjectKeywords): float
    {
        if (empty($teacherSpeciality) || empty($subjectKeywords)) {
            return 0.0;
        }

        $teacherSpeciality = strtolower($teacherSpeciality);
        $subjectKeywords = strtolower($subjectKeywords);

        // Define keyword mappings for better matching
        $keywordMappings = [
            'ai' => ['intelligence artificielle', 'artificial intelligence', 'machine learning', 'deep learning', 'research', 'reconnaissance'],
            'mobile' => ['application mobile', 'mobile app', 'android', 'ios', 'development', 'développement'],
            'web' => ['développement web', 'web development', 'site web', 'application web', 'development', 'génie logiciel'],
            'iot' => ['internet des objets', 'internet of things', 'capteurs', 'sensors', 'hardware', 'systèmes embarqués'],
            'crypto' => ['cryptographie', 'cryptographic', 'sécurité', 'security', 'mathématiques appliquées', 'algorithmes'],
            'cloud' => ['cloud computing', 'aws', 'azure', 'nuage', 'infrastructure'],
            'database' => ['base de données', 'sql', 'nosql', 'données', 'database'],
            'network' => ['réseau', 'networking', 'tcp', 'protocole', 'réseaux et sécurité'],
            'image' => ['traitement d\'images', 'image processing', 'vision', 'opencv', 'research', 'traitement'],
            'hmi' => ['interface homme-machine', 'human computer interaction', 'ui', 'ux', 'accessibility'],
            'data' => ['data science', 'analyse de données', 'big data', 'analytics', 'analyse prédictive'],
            'software' => ['génie logiciel', 'software engineering', 'development', 'développement'],
            'hardware' => ['systèmes embarqués', 'embedded systems', 'hardware', 'électronique'],
            'math' => ['mathématiques appliquées', 'applied mathematics', 'algorithmes', 'optimization']
        ];

        $score = 0.0;

        // Direct word matching
        $teacherWords = explode(' ', $teacherSpeciality);
        $subjectWords = explode(' ', $subjectKeywords);

        foreach ($teacherWords as $teacherWord) {
            if (strlen($teacherWord) > 3) { // Skip very short words
                foreach ($subjectWords as $subjectWord) {
                    if (strlen($subjectWord) > 3 && strpos($subjectWord, $teacherWord) !== false) {
                        $score += 2.0; // Direct match
                    }
                }
            }
        }

        // Keyword mapping matching
        foreach ($keywordMappings as $category => $keywords) {
            $teacherMatches = false;
            $subjectMatches = false;

            foreach ($keywords as $keyword) {
                if (strpos($teacherSpeciality, $keyword) !== false) {
                    $teacherMatches = true;
                }
                if (strpos($subjectKeywords, $keyword) !== false) {
                    $subjectMatches = true;
                }
            }

            if ($teacherMatches && $subjectMatches) {
                $score += 3.0; // Category match
            }
        }

        return $score;
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

            // Calculate end time properly
            $startTime = Carbon::createFromFormat('H:i:s', $defense->defense_time);
            $endTime = $startTime->copy()->addMinutes($defense->duration);

            $calendar[$date][] = [
                'defense' => $defense,
                'time_slot' => $startTime->format('H:i') . ' - ' . $endTime->format('H:i'),
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