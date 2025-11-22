<?php

namespace App\Policies;

use App\Models\Defense;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DefensePolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any defenses.
     */
    public function viewAny(User $user): bool
    {
        // Teachers, department heads, and admins can view defenses
        return in_array($user->role, ['teacher', 'department_head', 'admin']);
    }

    /**
     * Determine if the user can view the defense.
     */
    public function view(User $user, Defense $defense): bool
    {
        // Admin and department heads can view all
        if (in_array($user->role, ['admin', 'department_head'])) {
            return true;
        }

        // Teachers can view if they are jury members or supervisor
        if ($user->role === 'teacher') {
            return $defense->juries()->where('teacher_id', $user->id)->exists() ||
                   $defense->subject->teacher_id === $user->id;
        }

        // Students can view their own defense
        if ($user->role === 'student' && $defense->project && $defense->project->team) {
            return $defense->project->team->members()->where('student_id', $user->id)->exists();
        }

        return false;
    }

    /**
     * Determine if the user can schedule defenses.
     */
    public function schedule(User $user): bool
    {
        return in_array($user->role, ['admin', 'department_head']);
    }

    /**
     * Determine if the user can update the defense.
     */
    public function update(User $user, Defense $defense): bool
    {
        return in_array($user->role, ['admin', 'department_head']);
    }

    /**
     * Determine if the user can delete the defense.
     */
    public function delete(User $user, Defense $defense): bool
    {
        // Only admins can delete defenses
        if ($user->role !== 'admin') {
            return false;
        }

        // Cannot delete completed defenses
        return $defense->status !== 'completed';
    }

    /**
     * Determine if the user can grade the defense.
     */
    public function grade(User $user, Defense $defense): bool
    {
        // Only jury members can grade
        return $defense->juries()->where('teacher_id', $user->id)->exists();
    }

    /**
     * Determine if the user can view the defense report.
     */
    public function viewReport(User $user, Defense $defense): bool
    {
        // Admin and department heads can view all reports
        if (in_array($user->role, ['admin', 'department_head'])) {
            return true;
        }

        // Teachers can view if they are involved
        if ($user->role === 'teacher') {
            return $defense->juries()->where('teacher_id', $user->id)->exists() ||
                   $defense->subject->teacher_id === $user->id;
        }

        // Students can view their own defense report
        if ($user->role === 'student' && $defense->project && $defense->project->team) {
            return $defense->project->team->members()->where('student_id', $user->id)->exists();
        }

        return false;
    }
}
