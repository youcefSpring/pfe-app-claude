<?php

namespace App\Services;

use App\Models\User;
use App\Models\Team;
use App\Models\Subject;

class ValidationHelper
{
    /**
     * Validate business rules for team formation.
     */
    public static function validateTeamFormation(Team $team): array
    {
        $errors = [];

        // Check team size
        $memberCount = $team->members()->count();
        $sizeConfig = self::getTeamSizeConfig($team);

        if ($memberCount < $sizeConfig['min']) {
            $errors[] = "Team needs at least {$sizeConfig['min']} members";
        }

        if ($memberCount > $sizeConfig['max']) {
            $errors[] = "Team cannot exceed {$sizeConfig['max']} members";
        }

        // Check academic level consistency
        $grades = $team->members()
            ->join('users', 'team_members.student_id', '=', 'users.id')
            ->pluck('grade')
            ->unique();

        if ($grades->count() > 1) {
            $errors[] = 'All team members must be from the same academic level';
        }

        // Check leadership
        $leaderCount = $team->members()->where('role', 'leader')->count();
        if ($leaderCount === 0) {
            $errors[] = 'Team must have a leader';
        } elseif ($leaderCount > 1) {
            $errors[] = 'Team can have only one leader';
        }

        return $errors;
    }

    /**
     * Validate subject data against business rules.
     */
    public static function validateSubjectData(array $data): array
    {
        $errors = [];

        // Required fields
        $required = ['title', 'description', 'keywords', 'tools', 'plan'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $errors[] = "Field '{$field}' is required";
            }
        }

        // Length validations
        if (isset($data['description']) && strlen($data['description']) < 50) {
            $errors[] = 'Description must be at least 50 characters long';
        }

        if (isset($data['plan']) && strlen($data['plan']) < 100) {
            $errors[] = 'Project plan must be at least 100 characters long';
        }

        if (isset($data['title']) && strlen($data['title']) > 255) {
            $errors[] = 'Title cannot exceed 255 characters';
        }

        // Keywords validation
        if (isset($data['keywords'])) {
            $keywords = explode(',', $data['keywords']);
            if (count($keywords) < 3) {
                $errors[] = 'At least 3 keywords are required';
            }
            if (count($keywords) > 10) {
                $errors[] = 'Maximum 10 keywords allowed';
            }
        }

        // Title uniqueness (if creating new)
        if (isset($data['title']) && !isset($data['id'])) {
            if (Subject::where('title', $data['title'])->exists()) {
                $errors[] = 'Subject title must be unique';
            }
        }

        return $errors;
    }

    /**
     * Validate supervisor assignment rules.
     */
    public static function validateSupervisorAssignment(User $supervisor, Team $team): array
    {
        $errors = [];

        // Check if user can supervise
        if (!$supervisor->isTeacher() && !$supervisor->isExternalSupervisor()) {
            $errors[] = 'Only teachers and external supervisors can supervise projects';
        }

        // Check workload limits
        if ($supervisor->isTeacher()) {
            $currentWorkload = $supervisor->getCurrentWorkload();
            if ($currentWorkload >= 5) {
                $errors[] = 'Supervisor has reached maximum capacity (5 projects)';
            }
        }

        // Check department matching for internal projects
        if ($team->subject && $team->subject->teacher->department !== $supervisor->department) {
            // This is a warning, not an error
            $errors[] = 'Warning: Supervisor is from different department than subject creator';
        }

        return $errors;
    }

    /**
     * Validate defense scheduling constraints.
     */
    public static function validateDefenseScheduling($date, $time, $roomId, $juryIds = []): array
    {
        $errors = [];

        // Check if date is in the past
        if (now()->gt($date . ' ' . $time)) {
            $errors[] = 'Defense cannot be scheduled in the past';
        }

        // Check if date is too far in future (max 6 months)
        if (now()->addMonths(6)->lt($date)) {
            $errors[] = 'Defense cannot be scheduled more than 6 months in advance';
        }

        // Check business hours (8 AM to 6 PM)
        $hour = (int) explode(':', $time)[0];
        if ($hour < 8 || $hour >= 18) {
            $errors[] = 'Defenses must be scheduled between 8:00 AM and 6:00 PM';
        }

        // Check weekends
        $dayOfWeek = date('N', strtotime($date));
        if ($dayOfWeek >= 6) {
            $errors[] = 'Defenses cannot be scheduled on weekends';
        }

        // Check room availability
        $room = \App\Models\Room::find($roomId);
        if ($room && !$room->isAvailableAt($date, $time)) {
            $errors[] = 'Room is not available at the requested time';
        }

        // Check jury availability
        if (!empty($juryIds)) {
            $conflicts = \App\Models\Defense::where('defense_date', $date)
                ->where('defense_time', $time)
                ->whereHas('jury', function ($q) use ($juryIds) {
                    $q->whereIn('teacher_id', $juryIds);
                })
                ->exists();

            if ($conflicts) {
                $errors[] = 'One or more jury members are not available at the requested time';
            }
        }

        return $errors;
    }

    /**
     * Validate project submission rules.
     */
    public static function validateProjectSubmission(\App\Models\Project $project): array
    {
        $errors = [];

        // Check project status
        if ($project->status !== 'in_progress') {
            $errors[] = 'Project must be in progress to submit for defense';
        }

        // Check final report approval
        $finalReport = $project->submissions()
            ->where('type', 'final_report')
            ->where('status', 'approved')
            ->first();

        if (!$finalReport) {
            $errors[] = 'Final report must be approved before project submission';
        }

        // Check minimum project duration (e.g., 3 months)
        if ($project->started_at && $project->started_at->gt(now()->subMonths(3))) {
            $errors[] = 'Project must run for at least 3 months before submission';
        }

        // Check all required submissions
        $requiredSubmissions = ['milestone', 'final_report'];
        foreach ($requiredSubmissions as $type) {
            $submission = $project->submissions()->where('type', $type)->first();
            if (!$submission) {
                $errors[] = "Missing required submission: {$type}";
            } elseif ($submission->status !== 'approved') {
                $errors[] = "Submission '{$type}' must be approved";
            }
        }

        return $errors;
    }

    /**
     * Validate grade entry rules.
     */
    public static function validateGradeEntry(float $grade, string $context = 'defense'): array
    {
        $errors = [];

        // Grade range validation
        if ($grade < 0 || $grade > 20) {
            $errors[] = 'Grade must be between 0 and 20';
        }

        // Precision validation (max 2 decimal places)
        if (round($grade, 2) !== $grade) {
            $errors[] = 'Grade can have maximum 2 decimal places';
        }

        // Context-specific validations
        switch ($context) {
            case 'defense':
                if ($grade < 8) {
                    $errors[] = 'Warning: Grade below 8 indicates failure';
                }
                break;

            case 'submission':
                if ($grade < 10) {
                    $errors[] = 'Submission grade should be at least 10 for approval';
                }
                break;
        }

        return $errors;
    }

    /**
     * Get team size configuration based on academic level.
     */
    private static function getTeamSizeConfig(Team $team): array
    {
        // Get first member's grade to determine requirements
        $firstMember = $team->members()->with('student')->first();

        if (!$firstMember) {
            return ['min' => 2, 'max' => 3]; // Default
        }

        return match ($firstMember->student->grade) {
            'master' => ['min' => 1, 'max' => 2],
            'phd' => ['min' => 1, 'max' => 1],
            default => ['min' => 2, 'max' => 3], // licence
        };
    }

    /**
     * Validate file upload constraints.
     */
    public static function validateFileUpload(array $file, string $type = 'document'): array
    {
        $errors = [];

        // File size limits (in MB)
        $sizeLimits = [
            'document' => 10,
            'report' => 50,
            'presentation' => 100,
        ];

        $maxSize = $sizeLimits[$type] ?? 10;
        $fileSizeMB = $file['size'] / (1024 * 1024);

        if ($fileSizeMB > $maxSize) {
            $errors[] = "File size cannot exceed {$maxSize}MB for {$type} uploads";
        }

        // File type validation
        $allowedTypes = [
            'document' => ['pdf', 'doc', 'docx'],
            'report' => ['pdf'],
            'presentation' => ['pdf', 'ppt', 'pptx'],
        ];

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = $allowedTypes[$type] ?? ['pdf'];

        if (!in_array($extension, $allowed)) {
            $errors[] = "Only " . implode(', ', $allowed) . " files are allowed for {$type}";
        }

        // File name validation
        if (strlen($file['name']) > 255) {
            $errors[] = 'File name cannot exceed 255 characters';
        }

        // Check for dangerous characters in filename
        if (preg_match('/[<>:"|?*]/', $file['name'])) {
            $errors[] = 'File name contains invalid characters';
        }

        return $errors;
    }

    /**
     * Validate email notification settings.
     */
    public static function validateNotificationRules(array $recipients, string $type): array
    {
        $errors = [];

        // Check recipient count limits
        $maxRecipients = [
            'conflict_resolution' => 10,
            'defense_scheduled' => 20,
            'subject_validation' => 5,
            'project_submission' => 10,
        ];

        $max = $maxRecipients[$type] ?? 10;
        if (count($recipients) > $max) {
            $errors[] = "Maximum {$max} recipients allowed for {$type} notifications";
        }

        // Validate email addresses
        foreach ($recipients as $email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Invalid email address: {$email}";
            }
        }

        return $errors;
    }
}