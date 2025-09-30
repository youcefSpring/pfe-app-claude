<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Str;

class PfeHelper
{
    /**
     * Generate academic year string.
     */
    public static function getAcademicYear(?Carbon $date = null): string
    {
        $date = $date ?: now();
        $year = $date->year;
        $month = $date->month;

        // Academic year starts in September
        if ($month >= 9) {
            return $year . '-' . ($year + 1);
        } else {
            return ($year - 1) . '-' . $year;
        }
    }

    /**
     * Generate semester information.
     */
    public static function getCurrentSemester(?Carbon $date = null): array
    {
        $date = $date ?: now();
        $month = $date->month;

        if ($month >= 9 || $month <= 1) {
            return [
                'semester' => 1,
                'name' => 'First Semester',
                'academic_year' => self::getAcademicYear($date),
            ];
        } elseif ($month >= 2 && $month <= 6) {
            return [
                'semester' => 2,
                'name' => 'Second Semester',
                'academic_year' => self::getAcademicYear($date),
            ];
        } else {
            return [
                'semester' => 0,
                'name' => 'Summer Break',
                'academic_year' => self::getAcademicYear($date),
            ];
        }
    }

    /**
     * Generate unique team name suggestion.
     */
    public static function generateTeamNameSuggestion(array $memberNames): string
    {
        // Sort names alphabetically
        sort($memberNames);

        // Take first letter of each name
        $initials = implode('', array_map(function ($name) {
            return strtoupper(substr($name, 0, 1));
        }, $memberNames));

        // Add academic year
        $academicYear = str_replace('-', '', self::getAcademicYear());

        $baseName = "Team-{$initials}-{$academicYear}";

        // Ensure uniqueness
        $counter = 1;
        $finalName = $baseName;

        while (\App\Models\Team::where('name', $finalName)->exists()) {
            $finalName = $baseName . "-{$counter}";
            $counter++;
        }

        return $finalName;
    }

    /**
     * Generate matricule for new users.
     */
    public static function generateMatricule(string $role, string $department = null): string
    {
        $year = now()->format('y');
        $prefix = match ($role) {
            'student' => 'E',
            'teacher' => 'T',
            'department_head' => 'D',
            'admin' => 'A',
            default => 'U',
        };

        // Department code (first 2 letters)
        $deptCode = $department ? strtoupper(substr($department, 0, 2)) : 'XX';

        // Find next available number
        $lastUser = \App\Models\User::where('matricule', 'like', "{$prefix}{$year}{$deptCode}%")
            ->orderBy('matricule', 'desc')
            ->first();

        if ($lastUser) {
            $lastNumber = (int) substr($lastUser->matricule, -3);
            $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '001';
        }

        return "{$prefix}{$year}{$deptCode}{$nextNumber}";
    }

    /**
     * Calculate project timeline milestones.
     */
    public static function calculateProjectMilestones(Carbon $startDate, int $durationMonths = 6): array
    {
        $milestones = [
            [
                'name' => 'Project Start',
                'date' => $startDate,
                'percentage' => 0,
                'description' => 'Official project start date',
            ],
            [
                'name' => 'First Milestone',
                'date' => $startDate->copy()->addMonths(2),
                'percentage' => 30,
                'description' => 'Initial research and analysis phase',
            ],
            [
                'name' => 'Mid-term Review',
                'date' => $startDate->copy()->addMonths(3),
                'percentage' => 50,
                'description' => 'Mid-term presentation and review',
            ],
            [
                'name' => 'Implementation Phase',
                'date' => $startDate->copy()->addMonths(4),
                'percentage' => 75,
                'description' => 'Implementation and testing',
            ],
            [
                'name' => 'Final Submission',
                'date' => $startDate->copy()->addMonths($durationMonths - 1),
                'percentage' => 90,
                'description' => 'Final report submission deadline',
            ],
            [
                'name' => 'Defense',
                'date' => $startDate->copy()->addMonths($durationMonths),
                'percentage' => 100,
                'description' => 'Project defense presentation',
            ],
        ];

        return $milestones;
    }

    /**
     * Format academic period display.
     */
    public static function formatAcademicPeriod(string $grade): array
    {
        return match ($grade) {
            'master' => [
                'level' => 'Master',
                'duration' => '2 years',
                'team_size' => '1-2 students',
                'defense_duration' => '45 minutes',
            ],
            'phd' => [
                'level' => 'PhD',
                'duration' => '3-5 years',
                'team_size' => '1 student',
                'defense_duration' => '60 minutes',
            ],
            default => [
                'level' => 'License',
                'duration' => '1 year',
                'team_size' => '2-3 students',
                'defense_duration' => '30 minutes',
            ],
        };
    }

    /**
     * Generate file naming convention.
     */
    public static function generateFileName(string $type, \App\Models\Team $team, string $extension = 'pdf'): string
    {
        $academicYear = self::getAcademicYear();
        $teamName = Str::slug($team->name);

        $prefix = match ($type) {
            'final_report' => 'Rapport_Final',
            'presentation' => 'Presentation',
            'milestone' => 'Livrable',
            'defense_pv' => 'PV_Soutenance',
            default => 'Document',
        };

        return "{$prefix}_{$teamName}_{$academicYear}.{$extension}";
    }

    /**
     * Calculate statistics periods.
     */
    public static function getStatisticsPeriods(): array
    {
        $today = now();

        return [
            'current_month' => [
                'start' => $today->copy()->startOfMonth(),
                'end' => $today->copy()->endOfMonth(),
                'label' => 'This Month',
            ],
            'last_month' => [
                'start' => $today->copy()->subMonth()->startOfMonth(),
                'end' => $today->copy()->subMonth()->endOfMonth(),
                'label' => 'Last Month',
            ],
            'current_semester' => [
                'start' => self::getCurrentSemesterStart($today),
                'end' => self::getCurrentSemesterEnd($today),
                'label' => 'Current Semester',
            ],
            'academic_year' => [
                'start' => self::getAcademicYearStart($today),
                'end' => self::getAcademicYearEnd($today),
                'label' => 'Academic Year',
            ],
        ];
    }

    /**
     * Get defense time slots configuration.
     */
    public static function getDefenseTimeSlots(): array
    {
        return [
            '08:00' => '08:00 - 09:00',
            '09:00' => '09:00 - 10:00',
            '10:00' => '10:00 - 11:00',
            '11:00' => '11:00 - 12:00',
            '14:00' => '14:00 - 15:00',
            '15:00' => '15:00 - 16:00',
            '16:00' => '16:00 - 17:00',
            '17:00' => '17:00 - 18:00',
        ];
    }

    /**
     * Generate color coding for status.
     */
    public static function getStatusColor(string $status, string $context = 'general'): string
    {
        $colors = [
            'subject' => [
                'draft' => 'gray',
                'pending_validation' => 'yellow',
                'validated' => 'green',
                'rejected' => 'red',
                'needs_correction' => 'orange',
            ],
            'team' => [
                'forming' => 'blue',
                'complete' => 'green',
                'subject_selected' => 'purple',
                'assigned' => 'indigo',
                'active' => 'emerald',
                'completed' => 'gray',
            ],
            'project' => [
                'assigned' => 'blue',
                'in_progress' => 'yellow',
                'submitted' => 'purple',
                'defended' => 'green',
            ],
            'defense' => [
                'scheduled' => 'blue',
                'in_progress' => 'yellow',
                'completed' => 'green',
                'cancelled' => 'red',
            ],
            'general' => [
                'pending' => 'yellow',
                'approved' => 'green',
                'rejected' => 'red',
                'completed' => 'green',
                'cancelled' => 'red',
            ],
        ];

        return $colors[$context][$status] ?? $colors['general'][$status] ?? 'gray';
    }

    /**
     * Format duration for display.
     */
    public static function formatDuration(int $minutes): string
    {
        if ($minutes < 60) {
            return "{$minutes} minutes";
        }

        $hours = intval($minutes / 60);
        $remainingMinutes = $minutes % 60;

        if ($remainingMinutes === 0) {
            return $hours === 1 ? "1 hour" : "{$hours} hours";
        }

        return "{$hours}h {$remainingMinutes}m";
    }

    /**
     * Generate breadcrumb navigation.
     */
    public static function generateBreadcrumb(string $currentPage, array $context = []): array
    {
        $breadcrumbs = [
            ['name' => 'Dashboard', 'url' => '/dashboard'],
        ];

        switch ($currentPage) {
            case 'subjects':
                $breadcrumbs[] = ['name' => 'Subjects', 'url' => '/subjects'];
                break;

            case 'subject-create':
                $breadcrumbs[] = ['name' => 'Subjects', 'url' => '/subjects'];
                $breadcrumbs[] = ['name' => 'Create Subject', 'url' => null];
                break;

            case 'teams':
                $breadcrumbs[] = ['name' => 'Teams', 'url' => '/teams'];
                break;

            case 'projects':
                $breadcrumbs[] = ['name' => 'Projects', 'url' => '/projects'];
                break;

            case 'defenses':
                $breadcrumbs[] = ['name' => 'Defenses', 'url' => '/defenses'];
                break;

            case 'reports':
                $breadcrumbs[] = ['name' => 'Reports', 'url' => '/reports'];
                break;
        }

        // Add context-specific breadcrumbs
        if (isset($context['team'])) {
            $breadcrumbs[] = ['name' => $context['team']->name, 'url' => null];
        }

        if (isset($context['subject'])) {
            $breadcrumbs[] = ['name' => $context['subject']->title, 'url' => null];
        }

        return $breadcrumbs;
    }

    /**
     * Helper methods for date calculations.
     */
    private static function getCurrentSemesterStart(Carbon $date): Carbon
    {
        $month = $date->month;

        if ($month >= 9) {
            return $date->copy()->month(9)->startOfMonth();
        } else {
            return $date->copy()->subYear()->month(9)->startOfMonth();
        }
    }

    private static function getCurrentSemesterEnd(Carbon $date): Carbon
    {
        $month = $date->month;

        if ($month >= 9) {
            return $date->copy()->addYear()->month(1)->endOfMonth();
        } else {
            return $date->copy()->month(6)->endOfMonth();
        }
    }

    private static function getAcademicYearStart(Carbon $date): Carbon
    {
        if ($date->month >= 9) {
            return $date->copy()->month(9)->startOfMonth();
        } else {
            return $date->copy()->subYear()->month(9)->startOfMonth();
        }
    }

    private static function getAcademicYearEnd(Carbon $date): Carbon
    {
        if ($date->month >= 9) {
            return $date->copy()->addYear()->month(6)->endOfMonth();
        } else {
            return $date->copy()->month(6)->endOfMonth();
        }
    }
}