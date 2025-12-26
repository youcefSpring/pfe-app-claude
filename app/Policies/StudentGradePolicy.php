<?php

namespace App\Policies;

use App\Models\StudentGrade;
use App\Models\User;

class StudentGradePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isStudent() || $user->isAdmin() || $user->isDepartmentHead();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, StudentGrade $studentGrade): bool
    {
        return $user->id === $studentGrade->student_id
            || $user->isAdmin()
            || $user->isDepartmentHead();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isStudent();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, StudentGrade $studentGrade): bool
    {
        return $user->id === $studentGrade->student_id
            && in_array($studentGrade->status, ['draft', 'rejected']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, StudentGrade $studentGrade): bool
    {
        return $user->id === $studentGrade->student_id
            && in_array($studentGrade->status, ['draft', 'rejected']);
    }

    /**
     * Determine whether the user can verify grades.
     */
    public function verify(User $user): bool
    {
        return $user->isAdmin() || $user->isDepartmentHead();
    }
}