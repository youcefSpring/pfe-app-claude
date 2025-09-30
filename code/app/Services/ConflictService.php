<?php

namespace App\Services;

use App\Models\Subject;
use App\Models\Team;
use App\Models\User;
use App\Models\SubjectConflict;
use App\Models\ConflictTeam;
use Illuminate\Database\Eloquent\Collection;

class ConflictService
{
    /**
     * Detect and create conflicts for subject selection.
     */
    public function detectConflict(Subject $subject, Team $team): ?SubjectConflict
    {
        // Check if other teams have selected this subject
        $otherTeams = Team::where('subject_id', $subject->id)
            ->where('id', '!=', $team->id)
            ->where('status', 'subject_selected')
            ->get();

        if ($otherTeams->isEmpty()) {
            return null; // No conflict
        }

        // Create or get existing conflict
        $conflict = SubjectConflict::firstOrCreate([
            'subject_id' => $subject->id,
            'status' => 'pending',
        ]);

        // Add all teams to conflict (including the new one)
        $allTeams = $otherTeams->push($team);

        foreach ($allTeams as $conflictTeam) {
            ConflictTeam::firstOrCreate([
                'conflict_id' => $conflict->id,
                'team_id' => $conflictTeam->id,
            ], [
                'priority_score' => $this->calculatePriorityScore($conflictTeam),
                'selection_date' => $conflictTeam->updated_at,
            ]);
        }

        return $conflict;
    }

    /**
     * Get all pending conflicts.
     */
    public function getPendingConflicts(): Collection
    {
        return SubjectConflict::pending()
            ->with(['subject', 'teams.members.student'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Get conflicts for a specific department.
     */
    public function getConflictsByDepartment(string $department): Collection
    {
        return SubjectConflict::pending()
            ->whereHas('subject.teacher', function ($q) use ($department) {
                $q->where('department', $department);
            })
            ->with(['subject', 'teams.members.student'])
            ->get();
    }

    /**
     * Resolve conflict manually.
     */
    public function resolveConflict(
        SubjectConflict $conflict,
        Team $winningTeam,
        User $resolver,
        string $notes = null
    ): bool {
        // Validate resolver permissions
        if (!$resolver->isDepartmentHead() && !$resolver->isAdmin()) {
            throw new \Exception('Only department heads and admins can resolve conflicts');
        }

        // Validate conflict status
        if ($conflict->status !== 'pending') {
            throw new \Exception('Conflict is not in pending status');
        }

        // Validate winning team is part of conflict
        if (!$conflict->teams()->where('team_id', $winningTeam->id)->exists()) {
            throw new \Exception('Winning team is not part of this conflict');
        }

        // Resolve conflict
        $result = $conflict->resolve($resolver, $winningTeam, $notes);

        // Update losing teams status
        $losingTeams = $conflict->teams()->where('team_id', '!=', $winningTeam->id)->get();
        foreach ($losingTeams as $losingTeam) {
            $losingTeam->update([
                'subject_id' => null,
                'status' => 'complete', // Reset to complete so they can select another subject
            ]);
        }

        // TODO: Send notifications to all teams about resolution

        return $result;
    }

    /**
     * Auto-resolve conflict based on priority scores.
     */
    public function autoResolveConflict(SubjectConflict $conflict): bool
    {
        // Get team with highest priority score
        $winningTeamData = $conflict->teams()
            ->orderBy('conflict_teams.priority_score', 'desc')
            ->first();

        if (!$winningTeamData) {
            throw new \Exception('No teams found in conflict');
        }

        // Create system user for auto-resolution
        $systemUser = User::where('role', 'admin')->first();
        if (!$systemUser) {
            throw new \Exception('No admin user found for auto-resolution');
        }

        return $this->resolveConflict(
            $conflict,
            $winningTeamData,
            $systemUser,
            'Auto-resolved based on priority scoring system'
        );
    }

    /**
     * Calculate priority score for a team.
     */
    public function calculatePriorityScore(Team $team): int
    {
        $score = 0;

        // 1. Selection timestamp (first come, first served) - up to 1000 points
        $hoursFromStart = now()->diffInHours($team->updated_at);
        $score += max(0, 1000 - $hoursFromStart);

        // 2. Academic merit (average grades if available) - up to 500 points
        $academicScore = $this->getTeamAcademicScore($team);
        $score += ($academicScore * 500) / 20; // Assuming 20/20 scale

        // 3. Team completion speed - up to 300 points
        $teamAge = $team->created_at->diffInDays($team->updated_at);
        $score += max(0, 300 - ($teamAge * 10)); // Faster formation gets higher score

        // 4. Random factor to break ties - up to 100 points
        $score += rand(1, 100);

        return (int) $score;
    }

    /**
     * Get team academic score (placeholder implementation).
     */
    private function getTeamAcademicScore(Team $team): float
    {
        // TODO: Implement based on actual academic scoring system
        // For now, return a placeholder based on team size and grade
        $members = $team->members()->with('student')->get();

        if ($members->isEmpty()) {
            return 0.0;
        }

        // Higher grade gets slight bonus
        $gradeBonus = match ($members->first()->student->grade) {
            'phd' => 2.0,
            'master' => 1.0,
            default => 0.0,
        };

        // Base score between 10-18 plus grade bonus
        return rand(10, 18) + $gradeBonus;
    }

    /**
     * Get conflict statistics.
     */
    public function getConflictStatistics(): array
    {
        return [
            'pending' => SubjectConflict::pending()->count(),
            'resolved_today' => SubjectConflict::where('status', 'resolved')
                ->whereDate('resolved_at', today())
                ->count(),
            'total_resolved' => SubjectConflict::where('status', 'resolved')->count(),
            'average_resolution_time' => $this->getAverageResolutionTime(),
        ];
    }

    /**
     * Get average resolution time in hours.
     */
    private function getAverageResolutionTime(): float
    {
        $resolvedConflicts = SubjectConflict::where('status', 'resolved')
            ->whereNotNull('resolved_at')
            ->get();

        if ($resolvedConflicts->isEmpty()) {
            return 0;
        }

        $totalHours = $resolvedConflicts->sum(function ($conflict) {
            return $conflict->created_at->diffInHours($conflict->resolved_at);
        });

        return round($totalHours / $resolvedConflicts->count(), 2);
    }

    /**
     * Get conflict resolution suggestions.
     */
    public function getResolutionSuggestions(SubjectConflict $conflict): array
    {
        $teams = $conflict->teams()->with(['members.student'])->get();
        $suggestions = [];

        foreach ($teams as $team) {
            $score = $this->calculatePriorityScore($team);
            $academicScore = $this->getTeamAcademicScore($team);

            $suggestions[] = [
                'team' => $team,
                'priority_score' => $score,
                'academic_score' => $academicScore,
                'selection_date' => $team->pivot->selection_date,
                'members_count' => $team->members()->count(),
                'reasons' => $this->generateResolutionReasons($team, $score, $academicScore),
            ];
        }

        // Sort by priority score descending
        usort($suggestions, function ($a, $b) {
            return $b['priority_score'] <=> $a['priority_score'];
        });

        return $suggestions;
    }

    /**
     * Generate reasons for conflict resolution suggestion.
     */
    private function generateResolutionReasons(Team $team, int $priorityScore, float $academicScore): array
    {
        $reasons = [];

        if ($priorityScore > 800) {
            $reasons[] = 'Early selection (first come, first served)';
        }

        if ($academicScore > 16) {
            $reasons[] = 'High academic performance';
        }

        if ($team->members()->count() >= 3) {
            $reasons[] = 'Complete team with multiple members';
        }

        $teamAge = $team->created_at->diffInDays($team->updated_at);
        if ($teamAge <= 1) {
            $reasons[] = 'Quick team formation';
        }

        if (empty($reasons)) {
            $reasons[] = 'Standard evaluation criteria';
        }

        return $reasons;
    }

    /**
     * Preview conflict resolution impact.
     */
    public function previewResolution(SubjectConflict $conflict, Team $winningTeam): array
    {
        $impact = [
            'winning_team' => $winningTeam->name,
            'subject_assigned' => $conflict->subject->title,
            'losing_teams' => [],
            'next_actions' => [],
        ];

        // Get losing teams
        $losingTeams = $conflict->teams()->where('team_id', '!=', $winningTeam->id)->get();
        foreach ($losingTeams as $losingTeam) {
            $impact['losing_teams'][] = [
                'name' => $losingTeam->name,
                'members_count' => $losingTeam->members()->count(),
                'status_change' => 'Reset to complete (can select another subject)',
            ];
        }

        // Suggest next actions
        $impact['next_actions'] = [
            'Send notification to winning team',
            'Send notification to losing teams with alternative subjects',
            'Update project assignment if needed',
            'Monitor for new subject selections from losing teams',
        ];

        return $impact;
    }
}