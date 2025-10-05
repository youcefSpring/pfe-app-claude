<?php

namespace App\Services;

use App\Models\Subject;
use App\Models\Team;
use App\Models\SubjectConflict;
use Illuminate\Support\Collection;

class SubjectAllocationService
{
    /**
     * Resolve subject conflicts based on team priority scores.
     * Teams with higher average marks get priority.
     */
    public function resolveSubjectConflicts(): array
    {
        $results = [
            'allocated' => [],
            'rejected' => [],
            'conflicts_resolved' => 0
        ];

        // Get all pending conflicts
        $conflicts = SubjectConflict::where('status', 'pending')
            ->with(['subject', 'teams.members.user'])
            ->get();

        foreach ($conflicts as $conflict) {
            $allocationResult = $this->allocateSubjectToTeam($conflict);

            $results['allocated'][] = $allocationResult['winner'];
            $results['rejected'] = array_merge($results['rejected'], $allocationResult['losers']);
            $results['conflicts_resolved']++;
        }

        return $results;
    }

    /**
     * Allocate subject to the team with highest priority score.
     */
    private function allocateSubjectToTeam(SubjectConflict $conflict): array
    {
        $teams = $conflict->teams()->with(['members.user'])->get();

        // Calculate priority scores for all competing teams
        $teamScores = $teams->map(function ($team) {
            return [
                'team' => $team,
                'priority_score' => $this->calculateTeamPriorityScore($team),
                'average_marks' => $team->average_marks
            ];
        });

        // Sort by priority score (highest first)
        $rankedTeams = $teamScores->sortByDesc('priority_score');

        $winner = $rankedTeams->first();
        $losers = $rankedTeams->slice(1);

        // Assign subject to winning team
        $this->assignSubjectToTeam($winner['team'], $conflict->subject);

        // Reject other teams
        foreach ($losers as $loser) {
            $this->rejectTeamSubjectSelection($loser['team'], $conflict->subject);
        }

        // Mark conflict as resolved
        $conflict->update([
            'status' => 'resolved',
            'winning_team_id' => $winner['team']->id,
            'resolved_at' => now()
        ]);

        return [
            'winner' => [
                'team' => $winner['team'],
                'subject' => $conflict->subject,
                'priority_score' => $winner['priority_score'],
                'average_marks' => $winner['average_marks']
            ],
            'losers' => $losers->map(function ($loser) use ($conflict) {
                return [
                    'team' => $loser['team'],
                    'subject' => $conflict->subject,
                    'priority_score' => $loser['priority_score'],
                    'average_marks' => $loser['average_marks']
                ];
            })->toArray()
        ];
    }

    /**
     * Calculate team priority score based on various factors.
     */
    private function calculateTeamPriorityScore(Team $team): float
    {
        $score = 0;

        // Primary factor: Team average marks (weight: 70%)
        $averageMarks = $team->average_marks;
        $score += $averageMarks * 0.7;

        // Secondary factor: Team completion status (weight: 20%)
        $completionBonus = $this->getCompletionBonus($team);
        $score += $completionBonus * 0.2;

        // Tertiary factor: Selection timing (weight: 10%)
        $timingBonus = $this->getTimingBonus($team);
        $score += $timingBonus * 0.1;

        return round($score, 2);
    }

    /**
     * Get completion bonus points for team status.
     */
    private function getCompletionBonus(Team $team): float
    {
        return match ($team->status) {
            'complete' => 10.0,
            'active' => 8.0,
            'subject_selected' => 6.0,
            'forming' => 2.0,
            default => 0.0
        };
    }

    /**
     * Get timing bonus based on when team was formed or selected subject.
     */
    private function getTimingBonus(Team $team): float
    {
        $conflictEntry = $team->conflicts()->first();

        if (!$conflictEntry || !$conflictEntry->pivot->selection_date) {
            return 0.0;
        }

        // Earlier selection gets higher bonus (max 5 points)
        $selectionDate = $conflictEntry->pivot->selection_date;
        $hoursAgo = now()->diffInHours($selectionDate);

        // Give bonus points for early selection (diminishing returns)
        return max(0, 5 - ($hoursAgo / 24));
    }

    /**
     * Assign subject to winning team.
     */
    private function assignSubjectToTeam(Team $team, Subject $subject): void
    {
        $team->update([
            'subject_id' => $subject->id,
            'status' => 'assigned'
        ]);

        $subject->update([
            'status' => 'assigned',
            'assigned_team_id' => $team->id
        ]);
    }

    /**
     * Reject team's subject selection.
     */
    private function rejectTeamSubjectSelection(Team $team, Subject $subject): void
    {
        $team->update([
            'subject_id' => null,
            'status' => 'complete' // Reset to complete status
        ]);

        // Remove team from conflict
        $team->conflicts()->detach($subject->conflicts()->where('status', 'pending')->first()?->id);
    }

    /**
     * Get all teams that need subject allocation.
     */
    public function getTeamsNeedingAllocation(): Collection
    {
        return Team::where('status', 'subject_selected')
            ->whereNotNull('subject_id')
            ->with(['subject', 'members.user'])
            ->get();
    }

    /**
     * Run automatic allocation process.
     */
    public function runAutomaticAllocation(): array
    {
        $results = [
            'total_conflicts' => 0,
            'resolved_conflicts' => 0,
            'allocated_teams' => [],
            'failed_allocations' => []
        ];

        try {
            // First, identify and create any new conflicts
            $this->identifyNewConflicts();

            // Then resolve existing conflicts
            $allocationResults = $this->resolveSubjectConflicts();

            $results['resolved_conflicts'] = $allocationResults['conflicts_resolved'];
            $results['allocated_teams'] = $allocationResults['allocated'];
            $results['failed_allocations'] = $allocationResults['rejected'];

        } catch (\Exception $e) {
            $results['error'] = $e->getMessage();
        }

        return $results;
    }

    /**
     * Identify new conflicts where multiple teams selected same subject.
     */
    private function identifyNewConflicts(): void
    {
        $subjectSelections = Team::where('status', 'subject_selected')
            ->whereNotNull('subject_id')
            ->selectRaw('subject_id, COUNT(*) as team_count')
            ->groupBy('subject_id')
            ->having('team_count', '>', 1)
            ->get();

        foreach ($subjectSelections as $selection) {
            $subject = Subject::find($selection->subject_id);

            // Check if conflict already exists
            $existingConflict = SubjectConflict::where('subject_id', $subject->id)
                ->where('status', 'pending')
                ->first();

            if (!$existingConflict) {
                // Create new conflict
                $conflict = SubjectConflict::create([
                    'subject_id' => $subject->id,
                    'status' => 'pending',
                    'detected_at' => now()
                ]);

                // Attach all teams that selected this subject
                $conflictingTeams = Team::where('subject_id', $subject->id)
                    ->where('status', 'subject_selected')
                    ->get();

                foreach ($conflictingTeams as $team) {
                    $conflict->teams()->attach($team->id, [
                        'priority_score' => $this->calculateTeamPriorityScore($team),
                        'selection_date' => $team->updated_at
                    ]);
                }
            }
        }
    }
}