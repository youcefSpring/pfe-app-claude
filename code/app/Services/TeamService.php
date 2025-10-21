<?php

namespace App\Services;

use App\Models\Team;
use App\Models\User;
use App\Models\Subject;
use App\Models\TeamMember;
use Illuminate\Database\Eloquent\Collection;

class TeamService
{
    /**
     * Create a new team.
     */
    public function createTeam(array $data, User $creator): Team
    {
        // Validate that user is a student
        if (!$creator->isStudent()) {
            throw new \Exception('Only students can create teams');
        }

        // Check if student is already in a team
        if ($this->studentHasActiveTeam($creator)) {
            throw new \Exception('Student is already in an active team');
        }

        // Validate team name uniqueness
        if (Team::where('name', $data['name'])->exists()) {
            throw new \Exception('Team name must be unique');
        }

        // Create team
        $team = Team::create([
            'name' => $data['name'],
            'status' => 'forming',
        ]);

        // Add creator as team leader
        $team->addMember($creator, 'leader');

        return $team;
    }

    /**
     * Add member to team.
     */
    public function addMemberToTeam(Team $team, User $student, string $role = 'member'): bool
    {
        // Validate student role
        if (!$student->isStudent()) {
            throw new \Exception('Only students can join teams');
        }

        // Check team status
        if (!in_array($team->status, ['forming', 'complete'])) {
            throw new \Exception('Cannot add members to this team at current status');
        }

        // Check team size limits
        $teamSizes = $this->getTeamSizeConfig($student->grade);
        if ($team->members()->count() >= $teamSizes['max']) {
            throw new \Exception("Team cannot exceed {$teamSizes['max']} members");
        }

        // Check if student is already in a team
        if ($this->studentHasActiveTeam($student)) {
            throw new \Exception('Student is already in an active team');
        }

        // Validate same level (license/master)
        $existingGrades = $team->members()
            ->join('users', 'team_members.student_id', '=', 'users.id')
            ->pluck('grade')
            ->unique();

        if ($existingGrades->isNotEmpty() && !$existingGrades->contains($student->grade)) {
            throw new \Exception('All team members must be from the same academic level');
        }

        return $team->addMember($student, $role);
    }

    /**
     * Remove member from team.
     */
    public function removeMemberFromTeam(Team $team, User $student): bool
    {
        // Check if team has already selected a subject
        if ($team->subject_id) {
            throw new \Exception('Cannot remove members after subject selection');
        }

        // Remove member
        $result = $team->removeMember($student);

        // If team becomes empty, delete it
        if ($team->members()->count() === 0) {
            $team->delete();
        }

        return $result;
    }

    /**
     * Select subject for team.
     */
    public function selectSubject(Team $team, Subject $subject): bool
    {
        // Validate team can select subject
        if (!$team->canSelectSubject()) {
            throw new \Exception('Team cannot select a subject at this time');
        }

        // Validate subject availability
        if (!$subject->canBeSelected()) {
            throw new \Exception('Subject is not available for selection');
        }

        // Select subject
        $result = $team->selectSubject($subject);

        // Check for conflicts
        $conflict = $subject->createConflictIfNeeded($team);

        if ($conflict) {
            // TODO: Send notification about conflict
        }

        return $result;
    }

    /**
     * Get teams for a specific student.
     */
    public function getStudentTeams(User $student): Collection
    {
        if (!$student->isStudent()) {
            throw new \Exception('User is not a student');
        }

        return Team::whereHas('members', function ($q) use ($student) {
            $q->where('student_id', $student->id);
        })->with('members.student', 'subject')->get();
    }

    /**
     * Get available students for team invitation.
     */
    public function getAvailableStudents(Team $team): Collection
    {
        // Get grade of existing team members
        $grades = $team->members()
            ->join('users', 'team_members.student_id', '=', 'users.id')
            ->pluck('grade')
            ->unique();

        $query = User::where('role', 'student')
            ->whereDoesntHave('teamMemberships', function ($q) {
                $q->whereHas('team', function ($teamQuery) {
                    $teamQuery->whereIn('status', ['forming', 'complete', 'subject_selected', 'assigned', 'active']);
                });
            });

        // Filter by same grade if team has members
        if ($grades->isNotEmpty()) {
            $query->whereIn('grade', $grades->toArray());
        }

        return $query->orderBy('name')->get();
    }

    /**
     * Get team statistics for dashboard.
     */
    public function getTeamStatistics(): array
    {
        return [
            'total' => Team::count(),
            'forming' => Team::forming()->count(),
            'complete' => Team::complete()->count(),
            'with_subject' => Team::withSubject()->count(),
            'assigned' => Team::where('status', 'assigned')->count(),
        ];
    }

    /**
     * Get teams by status.
     */
    public function getTeamsByStatus(string $status): Collection
    {
        return Team::where('status', $status)
            ->with('members.student', 'subject', 'supervisor')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Assign supervisor to team.
     */
    public function assignSupervisor(Team $team, User $supervisor): bool
    {
        if (!$supervisor->isTeacher() && !$supervisor->isExternalSupervisor()) {
            throw new \Exception('Supervisor must be a teacher or external supervisor');
        }

        // Check supervisor workload
        if ($supervisor->isTeacher() && !$supervisor->canSuperviseMoreProjects()) {
            throw new \Exception('Supervisor has reached maximum project capacity');
        }

        $team->update(['supervisor_id' => $supervisor->id]);

        return true;
    }

    /**
     * Check if student has an active team.
     */
    public function studentHasActiveTeam(User $student): bool
    {
        return TeamMember::whereHas('team', function ($q) {
            $q->whereIn('status', ['forming', 'complete', 'subject_selected', 'assigned', 'active']);
        })->where('student_id', $student->id)->exists();
    }

    /**
     * Get team size configuration based on academic level.
     */
    public function getTeamSizeConfig(string $grade): array
    {
        return match ($grade) {
            'master' => ['min' => 1, 'max' => 4], // Updated to match config
            'phd' => ['min' => 1, 'max' => 1],
            default => ['min' => 1, 'max' => 4], // licence - updated to allow single member teams
        };
    }

    /**
     * Validate team completeness.
     */
    public function validateTeamCompleteness(Team $team): array
    {
        $issues = [];
        $memberCount = $team->members()->count();

        // Get first member's grade to determine size requirements
        $firstMember = $team->members()->with('student')->first();
        if (!$firstMember) {
            $issues[] = 'Team has no members';
            return $issues;
        }

        $sizeConfig = $this->getTeamSizeConfig($firstMember->student->grade);

        // Check size
        if ($memberCount < $sizeConfig['min']) {
            $issues[] = "Team needs at least {$sizeConfig['min']} members";
        }

        if ($memberCount > $sizeConfig['max']) {
            $issues[] = "Team cannot exceed {$sizeConfig['max']} members";
        }

        // Check leadership
        if (!$team->members()->where('role', 'leader')->exists()) {
            $issues[] = 'Team needs a leader';
        }

        // Check same academic level
        $grades = $team->members()
            ->join('users', 'team_members.student_id', '=', 'users.id')
            ->pluck('grade')
            ->unique();

        if ($grades->count() > 1) {
            $issues[] = 'All team members must be from the same academic level';
        }

        return $issues;
    }
}