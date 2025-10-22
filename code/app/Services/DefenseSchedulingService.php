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

        // Find available slot considering supervisor availability
        $slot = $this->findAvailableSlotForProject($project, $constraints);
        if (!$slot) {
            throw new \Exception('No available slots found for defense considering supervisor availability');
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
     * Find available time slot for a specific project considering supervisor availability.
     */
    public function findAvailableSlotForProject(Project $project, array $constraints = []): ?array
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
                // Check if supervisor is available at this time
                if (!$this->isSupervisorAvailable($project->supervisor_id, $date->format('Y-m-d'), $time)) {
                    continue;
                }

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
     * Check if supervisor is available at specific date and time.
     */
    private function isSupervisorAvailable(int $supervisorId, string $date, string $time): bool
    {
        // Check for conflicts with existing defenses where supervisor is assigned
        $conflicts = DefenseJury::where('teacher_id', $supervisorId)
            ->whereHas('defense', function ($q) use ($date, $time) {
                $q->where('defense_date', $date)
                  ->where('defense_time', $time);
            })->exists();

        return !$conflicts;
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

        // Assign supervisor as jury member (conflicts already handled in slot selection)
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

        // Find and assign examiner (exclude president if already assigned)
        $constraints['exclude_president_id'] = $president ? $president->id : null;
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

        $query = User::where('role', 'teacher')
            ->where('id', '!=', $defense->project->supervisor_id)
            ->whereDoesntHave('juryParticipations', function ($q) use ($defense) {
                $q->whereHas('defense', function ($defenseQuery) use ($defense) {
                    $defenseQuery->where('defense_date', $defense->defense_date)
                        ->where('defense_time', $defense->defense_time);
                });
            })
            ->whereDoesntHave('juryParticipations', function ($q) use ($defense) {
                $q->where('defense_id', $defense->id);
            });

        // Exclude president if already assigned
        if (!empty($constraints['exclude_president_id'])) {
            $query->where('id', '!=', $constraints['exclude_president_id']);
        }

        return $query->get()
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

        // Define improved keyword mappings for better matching
        $keywordMappings = [
            'ai' => ['intelligence artificielle', 'artificial intelligence', 'machine learning', 'deep learning', 'apprentissage automatique', 'apprentissage', 'tensorflow', 'pytorch', 'neural', 'réseaux de neurones'],
            'mobile' => ['application mobile', 'mobile app', 'android', 'ios', 'flutter', 'react native'],
            'web' => ['développement web', 'web development', 'site web', 'application web', 'html', 'css', 'javascript', 'react', 'vue', 'angular'],
            'iot' => ['internet des objets', 'internet of things', 'capteurs', 'sensors', 'arduino', 'raspberry'],
            'crypto' => ['cryptographie', 'cryptographic', 'sécurité', 'security', 'chiffrement', 'encryption'],
            'cloud' => ['cloud computing', 'aws', 'azure', 'google cloud', 'nuage', 'infrastructure'],
            'database' => ['base de données', 'sql', 'nosql', 'données', 'database', 'mysql', 'postgresql', 'mongodb'],
            'network' => ['réseau', 'networking', 'tcp', 'protocole', 'réseaux'],
            'image' => ['traitement d\'images', 'image processing', 'vision', 'opencv', 'computer vision', 'traitement'],
            'hmi' => ['interface homme-machine', 'human computer interaction', 'ui', 'ux', 'interface', 'utilisateur'],
            'data' => ['data science', 'analyse de données', 'big data', 'analytics', 'analyse prédictive', 'données', 'python', 'r'],
            'software' => ['génie logiciel', 'software engineering', 'développement', 'programming', 'programmation'],
            'hardware' => ['systèmes embarqués', 'embedded systems', 'hardware', 'électronique', 'microcontroleur'],
            'math' => ['mathématiques appliquées', 'applied mathematics', 'algorithmes', 'optimization', 'statistiques'],
            'ecommerce' => ['e-commerce', 'commerce électronique', 'boutique en ligne', 'vente en ligne', 'recommandation'],
            'recommendation' => ['recommandation', 'recommendation', 'système de recommandation', 'filtrage collaboratif']
        ];

        $score = 0.0;

        // Enhanced direct word matching with stemming-like approach
        $teacherWords = array_filter(explode(' ', $teacherSpeciality), fn($w) => strlen($w) > 2);
        $subjectWords = array_filter(explode(' ', $subjectKeywords), fn($w) => strlen($w) > 2);

        foreach ($teacherWords as $teacherWord) {
            foreach ($subjectWords as $subjectWord) {
                // Exact match
                if ($teacherWord === $subjectWord) {
                    $score += 5.0;
                }
                // Partial match (one word contains the other)
                elseif (strpos($subjectWord, $teacherWord) !== false || strpos($teacherWord, $subjectWord) !== false) {
                    $score += 3.0;
                }
                // Similar words (Levenshtein distance)
                elseif (strlen($teacherWord) > 4 && strlen($subjectWord) > 4) {
                    $distance = levenshtein($teacherWord, $subjectWord);
                    $maxLen = max(strlen($teacherWord), strlen($subjectWord));
                    $similarity = ($maxLen - $distance) / $maxLen;
                    if ($similarity > 0.7) {
                        $score += $similarity * 2.0;
                    }
                }
            }
        }

        // Enhanced keyword mapping matching
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
                $score += 4.0; // Category match bonus
            }
        }

        // Bonus for high-relevance specialities
        $highRelevanceBonus = [
            'intelligence artificielle' => ['apprentissage', 'machine learning', 'tensorflow', 'neural'],
            'data science' => ['données', 'analyse', 'python', 'statistiques'],
            'génie logiciel' => ['développement', 'programmation', 'application'],
            'base de données' => ['données', 'sql', 'database'],
        ];

        foreach ($highRelevanceBonus as $speciality => $relevantTerms) {
            if (strpos($teacherSpeciality, $speciality) !== false) {
                foreach ($relevantTerms as $term) {
                    if (strpos($subjectKeywords, $term) !== false) {
                        $score += 2.0;
                    }
                }
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
                       \Carbon\Carbon::parse($defense->defense_time)->format('H:i') . '_' .
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
                       \Carbon\Carbon::parse($participation->defense->defense_time)->format('H:i');
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