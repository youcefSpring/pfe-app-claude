<?php

namespace App\Services;

use App\Models\AllocationDeadline;
use App\Models\Subject;
use App\Models\Team;
use App\Models\SubjectAllocation;
use App\Models\User;
use App\Models\TeamMember;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoAllocationService
{
    /**
     * Perform auto-allocation with conflict resolution
     */
    public function performAutoAllocation(AllocationDeadline $deadline): array
    {
        if (!$deadline->canPerformAutoAllocation()) {
            throw new \Exception('Auto-allocation cannot be performed at this time.');
        }

        DB::beginTransaction();

        try {
            $result = [
                'allocated_teams' => [],
                'conflicts_resolved' => [],
                'teams_without_subjects' => [],
                'second_round_needed' => false,
                'statistics' => []
            ];

            // Get all teams with preferences for this deadline
            $teamsWithPreferences = $this->getTeamsWithPreferences($deadline);

            // Get all available subjects
            $availableSubjects = $this->getAvailableSubjects($deadline);

            // Group teams by their first choice
            $preferenceGroups = $this->groupTeamsByPreferences($teamsWithPreferences);

            // Resolve conflicts and allocate subjects
            foreach ($preferenceGroups as $subjectId => $teams) {
                $subject = $availableSubjects->firstWhere('id', $subjectId);

                if (!$subject) {
                    continue;
                }

                if ($teams->count() === 1) {
                    // No conflict - direct allocation
                    $this->allocateSubjectToTeam($teams->first(), $subject, $deadline, 'direct');
                    $result['allocated_teams'][] = [
                        'team' => $teams->first(),
                        'subject' => $subject,
                        'method' => 'direct'
                    ];
                } else {
                    // Conflict - resolve based on best student grade
                    $winningTeam = $this->resolveConflict($teams);
                    $losingTeams = $teams->except($winningTeam->id);

                    $this->allocateSubjectToTeam($winningTeam, $subject, $deadline, 'conflict_resolved');

                    $result['allocated_teams'][] = [
                        'team' => $winningTeam,
                        'subject' => $subject,
                        'method' => 'conflict_resolved'
                    ];

                    $result['conflicts_resolved'][] = [
                        'subject' => $subject,
                        'winning_team' => $winningTeam,
                        'losing_teams' => $losingTeams->toArray(),
                        'reason' => 'best_student_grade'
                    ];

                    // Add losing teams to unallocated list
                    foreach ($losingTeams as $team) {
                        $result['teams_without_subjects'][] = $team;
                    }
                }
            }

            // Handle teams that didn't get their first choice - try other preferences
            $unallocatedTeams = $this->handleRemainingPreferences($teamsWithPreferences, $availableSubjects, $deadline, $result);

            // Add remaining unallocated teams
            foreach ($unallocatedTeams as $team) {
                $result['teams_without_subjects'][] = $team;
            }

            // If there are teams without subjects, mark second round as needed
            if (count($result['teams_without_subjects']) > 0) {
                $result['second_round_needed'] = true;

                // Initialize second round (7 days period)
                $secondRoundStart = now()->addDays(1);
                $secondRoundEnd = now()->addDays(8);
                $deadline->initializeSecondRound($secondRoundStart, $secondRoundEnd);
            }

            // Mark auto-allocation as completed
            $deadline->markAutoAllocationCompleted();

            // Generate statistics
            $result['statistics'] = $this->generateStatistics($result);

            DB::commit();

            Log::info('Auto-allocation completed', [
                'deadline_id' => $deadline->id,
                'allocated_count' => count($result['allocated_teams']),
                'conflicts_count' => count($result['conflicts_resolved']),
                'unallocated_count' => count($result['teams_without_subjects'])
            ]);

            return $result;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Auto-allocation failed', [
                'deadline_id' => $deadline->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Get teams with preferences for the deadline
     */
    private function getTeamsWithPreferences(AllocationDeadline $deadline): Collection
    {
        return Team::with(['members.user.grades', 'preferences.subject'])
            ->whereHas('preferences', function ($query) use ($deadline) {
                $query->where('allocation_deadline_id', $deadline->id);
            })
            ->where('level', $deadline->level)
            ->where('academic_year', $deadline->academic_year)
            ->get();
    }

    /**
     * Get available subjects for allocation
     */
    private function getAvailableSubjects(AllocationDeadline $deadline): Collection
    {
        return Subject::where('level', $deadline->level)
            ->where('academic_year', $deadline->academic_year)
            ->where('is_validated', true)
            ->whereDoesntHave('allocations', function ($query) use ($deadline) {
                $query->where('allocation_deadline_id', $deadline->id)
                      ->where('status', 'confirmed');
            })
            ->get();
    }

    /**
     * Group teams by their first preference
     */
    private function groupTeamsByPreferences(Collection $teams): Collection
    {
        return $teams->groupBy(function ($team) {
            $firstPreference = $team->preferences
                ->where('preference_order', 1)
                ->first();

            return $firstPreference ? $firstPreference->subject_id : null;
        })->filter(function ($group, $key) {
            return $key !== null;
        });
    }

    /**
     * Resolve conflict between teams wanting the same subject
     */
    private function resolveConflict(Collection $teams): Team
    {
        return $teams->sortByDesc(function ($team) {
            return $this->getBestStudentGrade($team);
        })->first();
    }

    /**
     * Get the best student grade in a team
     */
    private function getBestStudentGrade(Team $team): float
    {
        $bestGrade = 0;

        foreach ($team->members as $member) {
            $studentGrade = $member->user->grades()
                ->where('academic_year', $team->academic_year)
                ->avg('grade') ?? 0;

            if ($studentGrade > $bestGrade) {
                $bestGrade = $studentGrade;
            }
        }

        return $bestGrade;
    }

    /**
     * Allocate subject to team
     */
    private function allocateSubjectToTeam(Team $team, Subject $subject, AllocationDeadline $deadline, string $method): void
    {
        // Get the team leader or first member
        $teamLeader = $team->members()->where('is_leader', true)->first()
            ?? $team->members()->first();

        if (!$teamLeader) {
            throw new \Exception("Team {$team->name} has no members");
        }

        $preference = $team->preferences()
            ->where('subject_id', $subject->id)
            ->first();

        SubjectAllocation::create([
            'allocation_deadline_id' => $deadline->id,
            'student_id' => $teamLeader->user_id,
            'subject_id' => $subject->id,
            'student_preference_order' => $preference ? $preference->preference_order : 99,
            'student_average' => $this->getBestStudentGrade($team),
            'allocation_rank' => 1,
            'allocation_method' => $method,
            'status' => 'confirmed',
            'confirmed_by' => 1, // System allocation
            'confirmed_at' => now(),
        ]);

        // Create project for this allocation
        $this->createProjectForAllocation($team, $subject, $deadline);
    }

    /**
     * Create project for allocated team
     */
    private function createProjectForAllocation(Team $team, Subject $subject, AllocationDeadline $deadline): void
    {
        \App\Models\Project::create([
            'team_id' => $team->id,
            'subject_id' => $subject->id,
            'supervisor_id' => $subject->teacher_id,
            'title' => $subject->title,
            'description' => $subject->description,
            'type' => $subject->is_external ? 'external' : 'internal',
            'status' => 'assigned',
            'academic_year' => $deadline->academic_year,
            'level' => $deadline->level,
            'start_date' => now(),
            'end_date' => now()->addMonths(6), // Default 6 months
        ]);
    }

    /**
     * Handle remaining preferences for unallocated teams
     */
    private function handleRemainingPreferences(Collection $teams, Collection $availableSubjects, AllocationDeadline $deadline, array &$result): Collection
    {
        $allocated = collect($result['allocated_teams'])->pluck('team.id');
        $unallocatedTeams = $teams->whereNotIn('id', $allocated);

        foreach ($unallocatedTeams as $team) {
            $preferences = $team->preferences()
                ->where('allocation_deadline_id', $deadline->id)
                ->orderBy('preference_order')
                ->get();

            foreach ($preferences as $preference) {
                $subject = $availableSubjects->firstWhere('id', $preference->subject_id);

                if ($subject && !$this->isSubjectAllocated($subject, $deadline)) {
                    $this->allocateSubjectToTeam($team, $subject, $deadline, 'secondary_preference');

                    $result['allocated_teams'][] = [
                        'team' => $team,
                        'subject' => $subject,
                        'method' => 'secondary_preference'
                    ];

                    $unallocatedTeams = $unallocatedTeams->except($team->id);
                    break;
                }
            }
        }

        return $unallocatedTeams;
    }

    /**
     * Check if subject is already allocated
     */
    private function isSubjectAllocated(Subject $subject, AllocationDeadline $deadline): bool
    {
        return SubjectAllocation::where('subject_id', $subject->id)
            ->where('allocation_deadline_id', $deadline->id)
            ->where('status', 'confirmed')
            ->exists();
    }

    /**
     * Generate allocation statistics
     */
    private function generateStatistics(array $result): array
    {
        return [
            'total_allocated' => count($result['allocated_teams']),
            'direct_allocations' => count(array_filter($result['allocated_teams'], fn($a) => $a['method'] === 'direct')),
            'conflict_resolutions' => count($result['conflicts_resolved']),
            'secondary_preferences' => count(array_filter($result['allocated_teams'], fn($a) => $a['method'] === 'secondary_preference')),
            'teams_without_subjects' => count($result['teams_without_subjects']),
            'second_round_needed' => $result['second_round_needed']
        ];
    }

    /**
     * Check if defense scheduling is allowed for a team
     */
    public function canTeamScheduleDefense(Team $team): bool
    {
        $deadline = AllocationDeadline::where('academic_year', $team->academic_year)
            ->where('level', $team->level)
            ->where('status', '!=', 'draft')
            ->first();

        if (!$deadline) {
            return false; // No deadline configured
        }

        return $deadline->canScheduleDefenses();
    }
}