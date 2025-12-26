<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TeamPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any teams.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view teams
    }

    /**
     * Determine if the user can view the team.
     */
    public function view(User $user, Team $team): bool
    {
        return true; // All authenticated users can view team details
    }

    /**
     * Determine if the user can create teams.
     */
    public function create(User $user): bool
    {
        // Only students can create teams
        if ($user->role !== 'student') {
            return false;
        }

        // Check if student is already in a team
        return !$user->teamMember()->exists();
    }

    /**
     * Determine if the user can update the team.
     */
    public function update(User $user, Team $team): bool
    {
        // Admin can update any team
        if ($user->role === 'admin') {
            return true;
        }

        // Only team leader can update
        if ($user->role === 'student') {
            $member = $team->members()->where('student_id', $user->id)->first();
            return $member && $member->role === 'leader';
        }

        return false;
    }

    /**
     * Determine if the user can delete the team.
     */
    public function delete(User $user, Team $team): bool
    {
        // Admin can delete any team
        if ($user->role === 'admin') {
            return true;
        }

        // Only team leader can delete if team is eligible
        if ($user->role === 'student') {
            $member = $team->members()->where('student_id', $user->id)->first();
            return $member && $member->role === 'leader' && $team->canBeDeleted();
        }

        return false;
    }

    /**
     * Determine if the user can add members to the team.
     */
    public function addMember(User $user, Team $team): bool
    {
        // Admin can add members
        if ($user->role === 'admin') {
            return true;
        }

        // Only team leader can add members
        if ($user->role === 'student') {
            $member = $team->members()->where('student_id', $user->id)->first();
            return $member && $member->role === 'leader';
        }

        return false;
    }

    /**
     * Determine if the user can remove a member from the team.
     */
    public function removeMember(User $user, Team $team, TeamMember $member): bool
    {
        // Admin can remove any member
        if ($user->role === 'admin') {
            return true;
        }

        // Team leader can remove members (except themselves if there are other members)
        if ($user->role === 'student') {
            $userMember = $team->members()->where('student_id', $user->id)->first();
            
            if (!$userMember || $userMember->role !== 'leader') {
                return false;
            }

            // Cannot remove leader if there are other members
            if ($member->role === 'leader' && $team->members()->count() > 1) {
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * Determine if the user can select a subject for the team.
     */
    public function selectSubject(User $user, Team $team): bool
    {
        // Must be a team member
        if ($user->role !== 'student') {
            return false;
        }

        if (!$team->members()->where('student_id', $user->id)->exists()) {
            return false;
        }

        // Team must be able to select subject
        return $team->canSelectSubject();
    }

    /**
     * Determine if the user can transfer leadership.
     */
    public function transferLeadership(User $user, Team $team): bool
    {
        // Admin can transfer leadership
        if ($user->role === 'admin') {
            return true;
        }

        // Only current leader can transfer
        if ($user->role === 'student') {
            $member = $team->members()->where('student_id', $user->id)->first();
            return $member && $member->role === 'leader';
        }

        return false;
    }
}
