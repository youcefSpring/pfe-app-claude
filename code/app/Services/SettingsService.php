<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    /**
     * Cache duration in seconds (1 hour)
     */
    protected const CACHE_DURATION = 3600;

    /**
     * Get a setting value with caching
     */
    public static function get(string $key, $default = null)
    {
        return Cache::remember("setting_{$key}", self::CACHE_DURATION, function () use ($key, $default) {
            return Setting::get($key, $default);
        });
    }

    /**
     * Set a setting value and clear cache
     */
    public static function set(string $key, $value, string $type = 'string', ?string $description = null): void
    {
        Setting::set($key, $value, $type, $description);
        Cache::forget("setting_{$key}");
    }

    /**
     * Clear all settings cache
     */
    public static function clearCache(): void
    {
        Cache::flush();
    }

    // ============================================================================
    // TEAM SETTINGS
    // ============================================================================

    /**
     * Check if team formation is enabled
     */
    public static function isTeamFormationEnabled(): bool
    {
        return (bool) self::get('team_formation_enabled', true);
    }

    /**
     * Get minimum team size for a level
     */
    public static function getMinTeamSize(string $level = 'licence_3'): int
    {
        $key = match($level) {
            'licence_3' => 'team_min_size_licence',
            'master_1', 'master_2' => 'team_min_size_master',
            default => 'team_min_size_licence'
        };

        return (int) self::get($key, $level === 'licence_3' ? 2 : 1);
    }

    /**
     * Get maximum team size for a level
     */
    public static function getMaxTeamSize(string $level = 'licence_3'): int
    {
        $key = match($level) {
            'licence_3' => 'team_max_size_licence',
            'master_1', 'master_2' => 'team_max_size_master',
            default => 'team_max_size_licence'
        };

        return (int) self::get($key, $level === 'licence_3' ? 3 : 2);
    }

    // ============================================================================
    // SUBJECT PREFERENCE SETTINGS
    // ============================================================================

    /**
     * Check if subject preferences are enabled
     */
    public static function arePreferencesEnabled(): bool
    {
        return (bool) self::get('preferences_enabled', true);
    }

    /**
     * Get maximum number of subject preferences allowed
     */
    public static function getMaxPreferences(): int
    {
        return (int) self::get('max_subject_preferences', 10);
    }

    /**
     * Get minimum number of subject preferences required
     */
    public static function getMinPreferences(): int
    {
        return (int) self::get('min_subject_preferences', 3);
    }

    // ============================================================================
    // SUBJECT SETTINGS
    // ============================================================================

    /**
     * Check if subject creation by students is enabled
     */
    public static function canStudentsCreateSubjects(): bool
    {
        return (bool) self::get('students_can_create_subjects', true);
    }

    /**
     * Check if subject validation is required
     */
    public static function requiresSubjectValidation(): bool
    {
        return (bool) self::get('subject_validation_required', true);
    }

    /**
     * Check if external projects are allowed
     */
    public static function areExternalProjectsAllowed(): bool
    {
        return (bool) self::get('external_projects_allowed', true);
    }

    // ============================================================================
    // REGISTRATION SETTINGS
    // ============================================================================

    /**
     * Check if student registration is open
     */
    public static function isRegistrationOpen(): bool
    {
        return (bool) self::get('registration_open', true);
    }

    /**
     * Check if profile completion is required
     */
    public static function requiresProfileCompletion(): bool
    {
        return (bool) self::get('require_profile_completion', true);
    }

    /**
     * Check if birth certificate is required
     */
    public static function requiresBirthCertificate(): bool
    {
        return (bool) self::get('require_birth_certificate', true);
    }

    /**
     * Check if previous marks are required
     */
    public static function requiresPreviousMarks(): bool
    {
        return (bool) self::get('require_previous_marks', true);
    }

    // ============================================================================
    // FILE UPLOAD SETTINGS
    // ============================================================================

    /**
     * Get maximum file upload size in KB
     */
    public static function getMaxFileSize(): int
    {
        return (int) self::get('max_file_upload_size', 10240); // 10MB default
    }

    /**
     * Get allowed file extensions
     */
    public static function getAllowedFileExtensions(): array
    {
        $extensions = self::get('allowed_file_extensions', 'pdf,doc,docx,zip,rar');
        return explode(',', $extensions);
    }

    // ============================================================================
    // DEFENSE SETTINGS
    // ============================================================================

    /**
     * Get defense duration in minutes
     */
    public static function getDefenseDuration(): int
    {
        return (int) self::get('defense_duration_minutes', 30);
    }

    /**
     * Get minimum days notice for defense scheduling
     */
    public static function getDefenseNoticeMinDays(): int
    {
        return (int) self::get('defense_notice_min_days', 7);
    }

    /**
     * Check if auto-scheduling is enabled
     */
    public static function isAutoSchedulingEnabled(): bool
    {
        return (bool) self::get('auto_scheduling_enabled', true);
    }

    // ============================================================================
    // NOTIFICATION SETTINGS
    // ============================================================================

    /**
     * Check if email notifications are enabled
     */
    public static function areEmailNotificationsEnabled(): bool
    {
        return (bool) self::get('email_notifications_enabled', true);
    }

    /**
     * Check if notifications are enabled for a specific type
     */
    public static function isNotificationEnabled(string $type): bool
    {
        return (bool) self::get("notification_{$type}_enabled", true);
    }

    // ============================================================================
    // SYSTEM SETTINGS
    // ============================================================================

    /**
     * Check if system is in maintenance mode
     */
    public static function isMaintenanceMode(): bool
    {
        return (bool) self::get('maintenance_mode', false);
    }

    /**
     * Get maintenance mode message
     */
    public static function getMaintenanceMessage(): string
    {
        return self::get('maintenance_message', 'System is currently under maintenance. Please try again later.');
    }

    /**
     * Get default language
     */
    public static function getDefaultLanguage(): string
    {
        return self::get('default_language', 'fr');
    }

    /**
     * Get all available languages
     */
    public static function getAvailableLanguages(): array
    {
        $languages = self::get('available_languages', 'ar,fr,en');
        return explode(',', $languages);
    }

    // ============================================================================
    // ALLOCATION SETTINGS
    // ============================================================================

    /**
     * Check if automatic allocation is enabled
     */
    public static function isAutoAllocationEnabled(): bool
    {
        return (bool) self::get('auto_allocation_enabled', true);
    }

    /**
     * Get allocation algorithm type
     */
    public static function getAllocationAlgorithm(): string
    {
        return self::get('allocation_algorithm', 'priority_based'); // priority_based, random, first_come
    }

    /**
     * Check if second round allocation is allowed
     */
    public static function allowSecondRoundAllocation(): bool
    {
        return (bool) self::get('allow_second_round_allocation', true);
    }

    // ============================================================================
    // UNIVERSITY INFO
    // ============================================================================

    /**
     * Get university information
     */
    public static function getUniversityInfo(): array
    {
        return Setting::getUniversityInfo();
    }

    /**
     * Get university logo
     */
    public static function getUniversityLogo(): ?string
    {
        return Setting::getUniversityLogo();
    }

    // ============================================================================
    // HELPER METHODS
    // ============================================================================

    /**
     * Get all settings grouped by category and sorted by type (boolean, integer, string)
     */
    public static function getAllSettings(): array
    {
        $allSettings = Setting::orderBy('key')->get();

        // Define type order priority: boolean (1), integer (2), text/string (3)
        $typeOrder = [
            'boolean' => 1,
            'integer' => 2,
            'text' => 3,
            'string' => 4,
        ];

        // Helper function to sort settings by type
        $sortByType = function($settings) use ($typeOrder) {
            return $settings->sortBy(function($setting) use ($typeOrder) {
                return $typeOrder[$setting->type] ?? 99;
            })->values();
        };

        return [
            'team' => $sortByType($allSettings->filter(fn($s) => str_starts_with($s->key, 'team_'))),
            'subject' => $sortByType($allSettings->filter(fn($s) => str_starts_with($s->key, 'subject_') || str_starts_with($s->key, 'preferences_') || str_starts_with($s->key, 'max_subject_') || str_starts_with($s->key, 'min_subject_') || str_starts_with($s->key, 'external_'))),
            'registration' => $sortByType($allSettings->filter(fn($s) => str_starts_with($s->key, 'registration_') || str_starts_with($s->key, 'require_'))),
            'file' => $sortByType($allSettings->filter(fn($s) => str_starts_with($s->key, 'max_file_') || str_starts_with($s->key, 'allowed_file_'))),
            'defense' => $sortByType($allSettings->filter(fn($s) => str_starts_with($s->key, 'defense_') || str_starts_with($s->key, 'auto_scheduling_'))),
            'notification' => $sortByType($allSettings->filter(fn($s) => str_starts_with($s->key, 'email_') || str_starts_with($s->key, 'notification_'))),
            'allocation' => $sortByType($allSettings->filter(fn($s) => str_starts_with($s->key, 'auto_allocation_') || str_starts_with($s->key, 'allocation_') || str_starts_with($s->key, 'allow_second_'))),
            'system' => $sortByType($allSettings->filter(fn($s) => str_starts_with($s->key, 'maintenance_') || str_starts_with($s->key, 'default_') || str_starts_with($s->key, 'available_'))),
            'university' => $sortByType($allSettings->filter(fn($s) => str_starts_with($s->key, 'university_') || str_starts_with($s->key, 'faculty_') || str_starts_with($s->key, 'department_') || str_starts_with($s->key, 'ministry_') || str_starts_with($s->key, 'republic_'))),
        ];
    }

    /**
     * Validate team size against settings
     */
    public static function validateTeamSize(int $memberCount, string $level): bool
    {
        return $memberCount >= self::getMinTeamSize($level) && $memberCount <= self::getMaxTeamSize($level);
    }

    /**
     * Validate preference count against settings
     */
    public static function validatePreferenceCount(int $count): bool
    {
        return $count >= self::getMinPreferences() && $count <= self::getMaxPreferences();
    }
}
