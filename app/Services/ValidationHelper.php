<?php

namespace App\Services;

use App\Models\User;
use App\Models\Team;
use App\Models\Subject;
use App\Models\Defense;
use Carbon\Carbon;

class ValidationHelper
{
    public static function validateSubjectRules(array $data): array
    {
        $errors = [];

        // Title validation
        if (empty($data['title']) || strlen($data['title']) > 200) {
            $errors['title'] = 'Title is required and must not exceed 200 characters';
        }

        // Description validation
        if (empty($data['description']) || strlen($data['description']) < 100) {
            $errors['description'] = 'Description is required and must be at least 100 characters';
        }

        // Keywords validation
        if (!isset($data['keywords']) || !is_array($data['keywords']) || count($data['keywords']) < 3) {
            $errors['keywords'] = 'At least 3 keywords are required';
        }

        // Max teams validation
        if (!isset($data['max_teams']) || $data['max_teams'] < 1 || $data['max_teams'] > 3) {
            $errors['max_teams'] = 'Max teams must be between 1 and 3';
        }

        return $errors;
    }

    public static function validateTeamRules(array $data): array
    {
        $errors = [];

        // Team name validation
        if (empty($data['name']) || strlen($data['name']) > 100) {
            $errors['name'] = 'Team name is required and must not exceed 100 characters';
        }

        // Team name uniqueness
        if (isset($data['name']) && Team::where('name', $data['name'])->exists()) {
            $errors['name'] = 'Team name must be unique';
        }

        // Team size validation
        if (!isset($data['members']) || !is_array($data['members'])) {
            $errors['members'] = 'Team members array is required';
        } else {
            $totalSize = count($data['members']) + 1; // +1 for leader
            if ($totalSize < 2 || $totalSize > 4) {
                $errors['size'] = 'Team size must be between 2 and 4 members';
            }
        }

        return $errors;
    }

    public static function validateDefenseRules(array $data): array
    {
        $errors = [];

        // Defense date validation
        if (empty($data['defense_date'])) {
            $errors['defense_date'] = 'Defense date is required';
        } else {
            $defenseDate = Carbon::parse($data['defense_date']);
            if ($defenseDate->isPast()) {
                $errors['defense_date'] = 'Defense date cannot be in the past';
            }
            if ($defenseDate->isWeekend()) {
                $errors['defense_date'] = 'Defense cannot be scheduled on weekends';
            }
        }

        // Duration validation
        if (!isset($data['duration']) || $data['duration'] < 30 || $data['duration'] > 120) {
            $errors['duration'] = 'Defense duration must be between 30 and 120 minutes';
        }

        // Working hours validation
        if (!empty($data['start_time'])) {
            $startTime = Carbon::parse($data['start_time']);
            if ($startTime->hour < 8 || $startTime->hour >= 18) {
                $errors['start_time'] = 'Defense must be scheduled during working hours (8:00-18:00)';
            }
        }

        return $errors;
    }

    public static function validateUserConstraints(User $user, string $action, array $context = []): array
    {
        $errors = [];

        switch ($action) {
            case 'create_subject':
                if (!$user->hasRole('teacher')) {
                    $errors['permission'] = 'Only teachers can create subjects';
                }
                break;

            case 'validate_subject':
                if (!$user->hasRole('chef_master')) {
                    $errors['permission'] = 'Only department heads can validate subjects';
                }
                break;

            case 'join_team':
                if (!$user->hasRole('student')) {
                    $errors['permission'] = 'Only students can join teams';
                }
                if ($user->teamMemberships()->exists()) {
                    $errors['team'] = 'User is already a member of another team';
                }
                break;

            case 'supervise_project':
                if (!$user->hasRole('teacher')) {
                    $errors['permission'] = 'Only teachers can supervise projects';
                }
                $currentProjects = $user->supervisedPfeProjects()->count();
                $maxProjects = config('pfe.max_projects_per_supervisor', 8);
                if ($currentProjects >= $maxProjects) {
                    $errors['workload'] = "Cannot supervise more than {$maxProjects} projects";
                }
                break;

            case 'schedule_defense':
                if (!$user->hasRole(['admin_pfe', 'chef_master'])) {
                    $errors['permission'] = 'Only admin or department head can schedule defenses';
                }
                break;
        }

        return $errors;
    }

    public static function validateBusinessConstraints(string $entity, array $data): array
    {
        $errors = [];

        switch ($entity) {
            case 'subject_assignment':
                // Check if subject is already assigned
                if (isset($data['subject_id'])) {
                    $subject = Subject::find($data['subject_id']);
                    if ($subject && $subject->projects()->exists()) {
                        $errors['subject'] = 'Subject is already assigned to another team';
                    }
                }
                break;

            case 'team_assignment':
                // Check if team is already assigned
                if (isset($data['team_id'])) {
                    $team = Team::find($data['team_id']);
                    if ($team && $team->project()->exists()) {
                        $errors['team'] = 'Team is already assigned to a project';
                    }
                }
                break;

            case 'defense_scheduling':
                // Check jury availability
                if (isset($data['jury_ids']) && isset($data['defense_date']) && isset($data['start_time'])) {
                    $conflicts = self::checkJuryAvailability(
                        $data['jury_ids'],
                        $data['defense_date'],
                        $data['start_time'],
                        $data['end_time']
                    );
                    if (!empty($conflicts)) {
                        $errors['jury'] = 'Jury members have scheduling conflicts: ' . implode(', ', $conflicts);
                    }
                }
                break;
        }

        return $errors;
    }

    public static function validatePhoneNumber(string $phone): bool
    {
        // Algerian phone number format: +213xxxxxxxxx
        return preg_match('/^\+213[0-9]{9}$/', $phone);
    }

    public static function validateStudentId(string $studentId): bool
    {
        // Example format: YYDDDDxxxx (Year + Department + Sequential)
        return preg_match('/^[0-9]{2}[0-9]{4}[0-9]{4}$/', $studentId);
    }

    public static function validateFileUpload(array $file, array $allowedTypes = [], int $maxSize = 10485760): array
    {
        $errors = [];

        // Check file size (default 10MB)
        if ($file['size'] > $maxSize) {
            $errors['size'] = 'File size exceeds maximum allowed size of ' . ($maxSize / 1024 / 1024) . 'MB';
        }

        // Check file type
        if (!empty($allowedTypes)) {
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($extension, $allowedTypes)) {
                $errors['type'] = 'File type not allowed. Allowed types: ' . implode(', ', $allowedTypes);
            }
        }

        // Check for malicious files
        $dangerousExtensions = ['php', 'exe', 'bat', 'sh', 'cmd'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (in_array($extension, $dangerousExtensions)) {
            $errors['security'] = 'File type is not allowed for security reasons';
        }

        return $errors;
    }

    private static function checkJuryAvailability(array $juryIds, string $date, string $startTime, string $endTime): array
    {
        $conflicts = [];

        foreach ($juryIds as $juryId) {
            $conflict = Defense::where('defense_date', $date)
                ->where(function ($query) use ($juryId) {
                    $query->where('jury_president_id', $juryId)
                        ->orWhere('jury_examiner_id', $juryId)
                        ->orWhere('jury_supervisor_id', $juryId);
                })
                ->where(function ($query) use ($startTime, $endTime) {
                    $query->whereBetween('start_time', [$startTime, $endTime])
                        ->orWhereBetween('end_time', [$startTime, $endTime])
                        ->orWhere(function ($q) use ($startTime, $endTime) {
                            $q->where('start_time', '<=', $startTime)
                              ->where('end_time', '>=', $endTime);
                        });
                })
                ->with('project.subject')
                ->first();

            if ($conflict) {
                $user = User::find($juryId);
                $conflicts[] = "{$user->first_name} {$user->last_name} (busy with {$conflict->project->subject->title})";
            }
        }

        return $conflicts;
    }
}