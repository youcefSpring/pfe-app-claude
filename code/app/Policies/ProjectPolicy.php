<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any projects.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view projects list
    }

    /**
     * Determine if the user can view the project.
     */
    public function view(User $user, Project $project): bool
    {
        // Admin can view all
        if ($user->role === 'admin') {
            return true;
        }

        // Department heads can view projects from their department
        if ($user->role === 'department_head') {
            return $project->team->members()
                ->whereHas('user', function($q) use ($user) {
                    $q->where('department', $user->department);
                })->exists();
        }

        // Teachers can view projects they supervise
        if ($user->role === 'teacher') {
            return $project->supervisor_id === $user->id;
        }

        // Students can view their team's project
        if ($user->role === 'student') {
            return $project->team->members()->where('student_id', $user->id)->exists();
        }

        return false;
    }

    /**
     * Determine if the user can create projects.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'department_head']);
    }

    /**
     * Determine if the user can update the project.
     */
    public function update(User $user, Project $project): bool
    {
        return in_array($user->role, ['admin', 'department_head']);
    }

    /**
     * Determine if the user can delete the project.
     */
    public function delete(User $user, Project $project): bool
    {
        // Only admins can delete projects
        if ($user->role !== 'admin') {
            return false;
        }

        // Cannot delete projects with defenses
        return !$project->defense()->exists();
    }

    /**
     * Determine if the user can submit to the project.
     */
    public function submit(User $user, Project $project): bool
    {
        // Only team members can submit
        if ($user->role !== 'student') {
            return false;
        }

        return $project->team->members()->where('student_id', $user->id)->exists();
    }

    /**
     * Determine if the user can review the project.
     */
    public function review(User $user, Project $project): bool
    {
        // Only the supervisor can review
        return $user->role === 'teacher' && $project->supervisor_id === $user->id;
    }

    /**
     * Determine if the user can assign a supervisor.
     */
    public function assignSupervisor(User $user, Project $project): bool
    {
        return in_array($user->role, ['admin', 'department_head']);
    }
}
