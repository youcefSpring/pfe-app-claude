<?php

namespace App\Services;

use App\Models\Team;
use App\Models\Subject;
use App\Models\Project;
use App\Models\TeamSubjectPreference;
use App\Models\AcademicYear;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubjectAllocationService
{
    /**
     * Allocate subjects to teams based on preferences and conflict resolution rules
     */
    public function allocateSubjects(string $academicYear): array
    {
        DB::beginTransaction();

        try {
            $results = [
                'allocated' => [],
                'conflicts' => [],
                'unallocated' => []
            ];

            // Get all teams with preferences for this academic year
            $teamsWithPreferences = Team::where('academic_year', $academicYear)
                ->whereHas('subjectPreferences')
                ->with([
                    'subjectPreferences' => function($query) {
                        $query->orderBy('preference_order', 'asc');
                    },
                    'subjectPreferences.subject',
                    'members.user'
                ])
                ->get();

            // Get all available subjects
            $availableSubjects = Subject::where('status', 'validated')
                ->where('academic_year', $academicYear)
                ->whereDoesntHave('projects')
                ->get()
                ->keyBy('id');

            // Process each preference level (1st choice, 2nd choice, etc.)
            for ($preferenceLevel = 1; $preferenceLevel <= TeamSubjectPreference::MAX_PREFERENCES; $preferenceLevel++) {
                $this->processPreferenceLevel($teamsWithPreferences, $availableSubjects, $preferenceLevel, $results);
            }

            // Mark unallocated teams
            foreach ($teamsWithPreferences as $team) {
                if (!isset($results['allocated'][$team->id])) {
                    $results['unallocated'][] = [
                        'team' => $team,
                        'reason' => 'All preferred subjects were allocated to other teams'
                    ];
                }
            }

            DB::commit();

            return $results;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Subject allocation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Process a specific preference level (1st, 2nd, 3rd choice, etc.)
     */
    private function processPreferenceLevel(Collection $teams, Collection &$availableSubjects, int $preferenceLevel, array &$results): void
    {
        // Get all teams that want subjects at this preference level
        $preferencesAtLevel = [];

        foreach ($teams as $team) {
            // Skip already allocated teams
            if (isset($results['allocated'][$team->id])) {
                continue;
            }

            $preference = $team->subjectPreferences
                ->where('preference_order', $preferenceLevel)
                ->first();

            if ($preference && isset($availableSubjects[$preference->subject_id])) {
                $preferencesAtLevel[] = [
                    'team' => $team,
                    'preference' => $preference,
                    'subject_id' => $preference->subject_id
                ];
            }
        }

        // Group by subject to handle conflicts
        $subjectGroups = collect($preferencesAtLevel)->groupBy('subject_id');

        foreach ($subjectGroups as $subjectId => $teamPreferences) {
            $subject = $availableSubjects[$subjectId];

            if ($teamPreferences->count() === 1) {
                // No conflict - allocate directly
                $teamPreference = $teamPreferences->first();
                $this->allocateSubjectToTeam($teamPreference['team'], $subject, $results);
                unset($availableSubjects[$subjectId]);

            } else {
                // Conflict - resolve based on selection date and best student average
                $winner = $this->resolveConflict($teamPreferences);
                $this->allocateSubjectToTeam($winner['team'], $subject, $results);
                unset($availableSubjects[$subjectId]);

                // Record conflict for losers
                foreach ($teamPreferences as $teamPreference) {
                    if ($teamPreference['team']->id !== $winner['team']->id) {
                        $results['conflicts'][] = [
                            'team' => $teamPreference['team'],
                            'subject' => $subject,
                            'preference_level' => $preferenceLevel,
                            'winner' => $winner['team'],
                            'reason' => $this->getConflictReason($teamPreference, $winner)
                        ];
                    }
                }
            }
        }
    }

    /**
     * Resolve conflict between teams wanting the same subject
     */
    private function resolveConflict(Collection $teamPreferences): array
    {
        return $teamPreferences
            ->sortBy([
                // Primary: Selection date (earlier is better)
                fn($pref) => $pref['preference']->selected_at,
                // Secondary: Best student average (higher is better) - negative for desc order
                fn($pref) => -$pref['team']->average_marks,
                // Tertiary: Team ID for consistency
                fn($pref) => $pref['team']->id
            ])
            ->first();
    }

    /**
     * Get reason why a team lost in conflict resolution
     */
    private function getConflictReason(array $loser, array $winner): string
    {
        $loserDate = $loser['preference']->selected_at;
        $winnerDate = $winner['preference']->selected_at;

        if ($loserDate > $winnerDate) {
            return 'Selected later than winning team';
        } elseif ($loserDate == $winnerDate) {
            $loserAverage = $loser['team']->average_marks;
            $winnerAverage = $winner['team']->average_marks;

            if ($loserAverage < $winnerAverage) {
                return 'Lower team average marks';
            } else {
                return 'Tie-breaker (team ID)';
            }
        }

        return 'Unknown reason';
    }

    /**
     * Allocate a subject to a team by creating a project
     */
    private function allocateSubjectToTeam(Team $team, Subject $subject, array &$results): void
    {
        $currentYear = AcademicYear::getCurrentYear();

        $project = Project::create([
            'team_id' => $team->id,
            'subject_id' => $subject->id,
            'supervisor_id' => $subject->teacher_id,
            'type' => 'internal',
            'status' => 'assigned',
            'academic_year' => $currentYear ? $currentYear->year : $team->academic_year,
        ]);

        // Update team status
        $team->update(['status' => 'assigned']);

        // Mark the preference as allocated
        $team->subjectPreferences()
            ->where('subject_id', $subject->id)
            ->update(['is_allocated' => true]);

        $results['allocated'][$team->id] = [
            'team' => $team,
            'subject' => $subject,
            'project' => $project,
            'preference_level' => $team->subjectPreferences
                ->where('subject_id', $subject->id)
                ->first()
                ->preference_order
        ];
    }

    /**
     * Get allocation summary statistics
     */
    public function getAllocationSummary(array $results): array
    {
        return [
            'total_teams' => count($results['allocated']) + count($results['unallocated']),
            'allocated_teams' => count($results['allocated']),
            'unallocated_teams' => count($results['unallocated']),
            'conflicts_resolved' => count($results['conflicts']),
            'allocation_rate' => count($results['allocated']) > 0
                ? round((count($results['allocated']) / (count($results['allocated']) + count($results['unallocated']))) * 100, 2)
                : 0
        ];
    }

    /**
     * Get detailed allocation report
     */
    public function generateAllocationReport(array $results): string
    {
        $summary = $this->getAllocationSummary($results);

        $report = "=== SUBJECT ALLOCATION REPORT ===\n\n";
        $report .= "Summary:\n";
        $report .= "- Total teams: {$summary['total_teams']}\n";
        $report .= "- Allocated: {$summary['allocated_teams']}\n";
        $report .= "- Unallocated: {$summary['unallocated_teams']}\n";
        $report .= "- Conflicts resolved: {$summary['conflicts_resolved']}\n";
        $report .= "- Allocation rate: {$summary['allocation_rate']}%\n\n";

        $report .= "=== ALLOCATED TEAMS ===\n";
        foreach ($results['allocated'] as $allocation) {
            $report .= "- {$allocation['team']->name} -> {$allocation['subject']->title} (Choice #{$allocation['preference_level']})\n";
        }

        $report .= "\n=== CONFLICTS RESOLVED ===\n";
        foreach ($results['conflicts'] as $conflict) {
            $report .= "- {$conflict['team']->name} lost {$conflict['subject']->title} to {$conflict['winner']->name} ({$conflict['reason']})\n";
        }

        $report .= "\n=== UNALLOCATED TEAMS ===\n";
        foreach ($results['unallocated'] as $unallocated) {
            $report .= "- {$unallocated['team']->name}: {$unallocated['reason']}\n";
        }

        return $report;
    }
}