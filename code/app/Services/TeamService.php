<?php

namespace App\Services;

use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use App\Models\PfeNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TeamService
{
    public function createTeam(array $data, User $leader): Team
    {
        $this->validateTeamCreation($data, $leader);

        return DB::transaction(function () use ($data, $leader) {
            $team = Team::create([
                'name' => $data['name'],
                'leader_id' => $leader->id,
                'size' => count($data['members']) + 1, // +1 for leader
                'status' => 'forming'
            ]);

            // Add leader as team member
            TeamMember::create([
                'team_id' => $team->id,
                'user_id' => $leader->id,
                'role' => 'leader'
            ]);

            // Add other members
            foreach ($data['members'] as $memberId) {
                TeamMember::create([
                    'team_id' => $team->id,
                    'user_id' => $memberId,
                    'role' => 'member'
                ]);

                $this->notifyTeamInvitation($team, $memberId);
            }

            // Check if team is complete
            if ($team->size >= 2) {
                $team->update([
                    'status' => 'complete',
                    'formation_completed_at' => now()
                ]);
            }

            return $team;
        });
    }

    public function addMember(Team $team, int $userId): Team
    {
        $this->validateMemberAddition($team, $userId);

        return DB::transaction(function () use ($team, $userId) {
            TeamMember::create([
                'team_id' => $team->id,
                'user_id' => $userId,
                'role' => 'member'
            ]);

            $team->increment('size');

            // Check if team becomes complete
            if ($team->size >= 2 && $team->status === 'forming') {
                $team->update([
                    'status' => 'complete',
                    'formation_completed_at' => now()
                ]);
            }

            $this->notifyTeamInvitation($team, $userId);

            return $team->fresh();
        });
    }

    public function removeMember(Team $team, int $userId): Team
    {
        $this->validateMemberRemoval($team, $userId);

        return DB::transaction(function () use ($team, $userId) {
            TeamMember::where('team_id', $team->id)
                ->where('user_id', $userId)
                ->delete();

            $team->decrement('size');

            // Check if team becomes incomplete
            if ($team->size < 2 && $team->status === 'complete') {
                $team->update([
                    'status' => 'forming',
                    'formation_completed_at' => null
                ]);
            }

            return $team->fresh();
        });
    }

    public function validateTeam(Team $team): Team
    {
        if ($team->status !== 'complete') {
            throw ValidationException::withMessages([
                'status' => 'Only complete teams can be validated'
            ]);
        }

        $this->checkTeamConstraints($team);

        $team->update(['status' => 'validated']);

        $this->notifyTeamValidated($team);

        return $team;
    }

    public function assignTeamToProject(Team $team, int $projectId): Team
    {
        if ($team->status !== 'validated') {
            throw ValidationException::withMessages([
                'status' => 'Only validated teams can be assigned to projects'
            ]);
        }

        $team->update(['status' => 'assigned']);

        $this->notifyTeamAssigned($team, $projectId);

        return $team;
    }

    public function getAvailableTeams(): \Illuminate\Database\Eloquent\Collection
    {
        return Team::where('status', 'validated')
            ->whereDoesntHave('project')
            ->with(['leader', 'members.user'])
            ->get();
    }

    private function validateTeamCreation(array $data, User $leader): void
    {
        // Check if leader is already in a team
        if (TeamMember::where('user_id', $leader->id)->exists()) {
            throw ValidationException::withMessages([
                'leader' => 'User is already a member of another team'
            ]);
        }

        // Check team size
        $totalSize = count($data['members']) + 1; // +1 for leader
        if ($totalSize < 2 || $totalSize > 4) {
            throw ValidationException::withMessages([
                'size' => 'Team size must be between 2 and 4 members'
            ]);
        }

        // Check if any member is already in a team
        $existingMembers = TeamMember::whereIn('user_id', $data['members'])->pluck('user_id');
        if ($existingMembers->isNotEmpty()) {
            throw ValidationException::withMessages([
                'members' => 'Some users are already members of other teams'
            ]);
        }

        // Check if team name is unique
        if (Team::where('name', $data['name'])->exists()) {
            throw ValidationException::withMessages([
                'name' => 'Team name must be unique'
            ]);
        }
    }

    private function validateMemberAddition(Team $team, int $userId): void
    {
        if ($team->size >= 4) {
            throw ValidationException::withMessages([
                'size' => 'Team cannot have more than 4 members'
            ]);
        }

        if (TeamMember::where('user_id', $userId)->exists()) {
            throw ValidationException::withMessages([
                'member' => 'User is already a member of a team'
            ]);
        }

        if ($team->status === 'assigned') {
            throw ValidationException::withMessages([
                'status' => 'Cannot modify assigned teams'
            ]);
        }
    }

    private function validateMemberRemoval(Team $team, int $userId): void
    {
        if ($team->size <= 2) {
            throw ValidationException::withMessages([
                'size' => 'Team must have at least 2 members'
            ]);
        }

        if ($team->leader_id === $userId) {
            throw ValidationException::withMessages([
                'leader' => 'Cannot remove team leader'
            ]);
        }

        if ($team->status === 'assigned') {
            throw ValidationException::withMessages([
                'status' => 'Cannot modify assigned teams'
            ]);
        }
    }

    private function checkTeamConstraints(Team $team): void
    {
        // Check all members are students
        $members = $team->members()->with('user')->get();
        foreach ($members as $member) {
            if (!$member->user->hasRole('student')) {
                throw ValidationException::withMessages([
                    'members' => 'All team members must be students'
                ]);
            }
        }

        // Check same department constraint (if applicable)
        $departments = $members->pluck('user.department')->unique();
        if ($departments->count() > 1) {
            throw ValidationException::withMessages([
                'department' => 'All team members must be from the same department'
            ]);
        }
    }

    private function notifyTeamInvitation(Team $team, int $userId): void
    {
        PfeNotification::create([
            'user_id' => $userId,
            'type' => 'team_invitation',
            'title' => 'Team Invitation',
            'message' => "You have been invited to join team '{$team->name}'",
            'data' => ['team_id' => $team->id]
        ]);
    }

    private function notifyTeamValidated(Team $team): void
    {
        $members = $team->members()->pluck('user_id');

        foreach ($members as $userId) {
            PfeNotification::create([
                'user_id' => $userId,
                'type' => 'team_validated',
                'title' => 'Team Validated',
                'message' => "Your team '{$team->name}' has been validated",
                'data' => ['team_id' => $team->id]
            ]);
        }
    }

    private function notifyTeamAssigned(Team $team, int $projectId): void
    {
        $members = $team->members()->pluck('user_id');

        foreach ($members as $userId) {
            PfeNotification::create([
                'user_id' => $userId,
                'type' => 'team_assigned',
                'title' => 'Project Assigned',
                'message' => "Your team '{$team->name}' has been assigned to a project",
                'data' => ['team_id' => $team->id, 'project_id' => $projectId]
            ]);
        }
    }
}