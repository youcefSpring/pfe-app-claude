<?php

namespace App\Services;

use App\Models\Subject;
use App\Models\User;
use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;

class SubjectService
{
    /**
     * Create a new subject with validation.
     */
    public function createSubject(array $data, User $user): Subject
    {
        // Validate that user can create subjects
        if (!$user->isTeacher() && !$user->isStudent()) {
            throw new \Exception('Only teachers and students can create subjects');
        }

        // Validate required fields
        $this->validateSubjectData($data);

        $subjectData = [
            'title' => $data['title'],
            'description' => $data['description'],
            'keywords' => $data['keywords'],
            'tools' => $data['tools'],
            'plan' => $data['plan'],
            'status' => 'draft',
        ];

        // Handle external subjects for students
        if ($user->isStudent()) {
            $subjectData['is_external'] = $data['is_external'] ?? true;
            $subjectData['student_id'] = $user->id;
            $subjectData['teacher_id'] = null;

            if ($data['is_external'] ?? true) {
                $subjectData['company_name'] = $data['company_name'] ?? null;
                $subjectData['dataset_resources_link'] = $data['dataset_resources_link'] ?? null;
            }
        } else {
            // Teachers create internal subjects
            $subjectData['teacher_id'] = $user->id;
            $subjectData['is_external'] = false;
        }

        return Subject::create($subjectData);
    }

    /**
     * Submit subject for validation.
     */
    public function submitForValidation(Subject $subject): bool
    {
        if ($subject->status !== 'draft') {
            throw new \Exception('Only draft subjects can be submitted for validation');
        }

        $subject->update(['status' => 'pending_validation']);

        // TODO: Send notification to department head

        return true;
    }

    /**
     * Validate a subject (approve/reject/request corrections).
     */
    public function validateSubject(Subject $subject, User $validator, string $action, string $feedback = null): bool
    {
        // Check validator permissions
        if (!$validator->isDepartmentHead() && !$validator->isAdmin()) {
            throw new \Exception('Only department heads and admins can validate subjects');
        }

        if ($subject->status !== 'pending_validation') {
            throw new \Exception('Subject is not in pending validation status');
        }

        switch ($action) {
            case 'approve':
                return $subject->validate($validator, $feedback);

            case 'reject':
                return $subject->reject($validator, $feedback);

            case 'request_corrections':
                return $subject->requestCorrections($validator, $feedback);

            default:
                throw new \Exception('Invalid validation action');
        }
    }

    /**
     * Get subjects for validation by department.
     */
    public function getSubjectsForValidation(string $department = null): Collection
    {
        $query = Subject::pendingValidation()
            ->with('teacher');

        if ($department) {
            $query->whereHas('teacher', function ($q) use ($department) {
                $q->where('department', $department);
            });
        }

        return $query->orderBy('created_at', 'asc')->get();
    }

    /**
     * Get available subjects for team selection.
     */
    public function getAvailableSubjects(): Collection
    {
        return Subject::available()
            ->with('teacher')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Search subjects by keywords.
     */
    public function searchSubjects(string $query, array $filters = []): Collection
    {
        $subjects = Subject::validated()
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('keywords', 'like', "%{$query}%")
                  ->orWhere('tools', 'like', "%{$query}%");
            });

        // Apply filters
        if (isset($filters['teacher_id'])) {
            $subjects->where('teacher_id', $filters['teacher_id']);
        }

        if (isset($filters['department'])) {
            $subjects->whereHas('teacher', function ($q) use ($filters) {
                $q->where('department', $filters['department']);
            });
        }

        return $subjects->with('teacher')->get();
    }

    /**
     * Check if subject can be selected by team.
     */
    public function canBeSelectedByTeam(Subject $subject, Team $team): bool
    {
        // Subject must be validated
        if (!$subject->canBeSelected()) {
            return false;
        }

        // Team must be complete
        if (!$team->canSelectSubject()) {
            return false;
        }

        // Check if team already has a subject
        if ($team->subject_id) {
            return false;
        }

        return true;
    }

    /**
     * Get subjects by teacher.
     */
    public function getSubjectsByTeacher(User $teacher): Collection
    {
        if (!$teacher->isTeacher()) {
            throw new \Exception('User is not a teacher');
        }

        return Subject::byTeacher($teacher->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get subject statistics for dashboard.
     */
    public function getSubjectStatistics(User $user = null): array
    {
        $stats = [
            'total' => Subject::count(),
            'pending_validation' => Subject::pendingValidation()->count(),
            'validated' => Subject::validated()->count(),
            'assigned' => Subject::validated()->whereHas('teams', function ($q) {
                $q->where('status', 'assigned');
            })->count(),
        ];

        if ($user && $user->isTeacher()) {
            $stats['my_subjects'] = Subject::byTeacher($user->id)->count();
            $stats['my_pending'] = Subject::byTeacher($user->id)->pendingValidation()->count();
        }

        return $stats;
    }

    /**
     * Get subjects for a specific user based on their role.
     */
    public function getSubjectsForUser(User $user, array $filters = [], int $perPage = 15)
    {
        $query = Subject::with(['teacher', 'student']);

        // Filter based on user role
        switch ($user->role) {
            case 'teacher':
                $query->where('teacher_id', $user->id);
                break;
            case 'department_head':
                $query->whereHas('teacher', function($q) use ($user) {
                    $q->where('department', $user->department);
                });
                break;
            case 'student':
                // Students see validated subjects and their own external subjects
                $query->where(function($q) use ($user) {
                    $q->where('status', 'validated')
                      ->orWhere(function($subq) use ($user) {
                          $subq->where('is_external', true)
                               ->where('student_id', $user->id);
                      });
                });
                break;
            // Admin sees all subjects (no filter)
        }

        // Apply additional filters
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('keywords', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['department'])) {
            $query->whereHas('teacher', function($q) use ($filters) {
                $q->where('department', $filters['department']);
            });
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Validate subject data.
     */
    private function validateSubjectData(array $data): void
    {
        $required = ['title', 'description', 'keywords', 'tools', 'plan'];

        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new \Exception("Field {$field} is required");
            }
        }

        // Validate minimum lengths
        if (strlen($data['description']) < 50) {
            throw new \Exception('Description must be at least 50 characters');
        }

        if (strlen($data['plan']) < 100) {
            throw new \Exception('Plan must be at least 100 characters');
        }

        // Check title uniqueness
        if (Subject::where('title', $data['title'])->exists()) {
            throw new \Exception('Subject title must be unique');
        }
    }
}