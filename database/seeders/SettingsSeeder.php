<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // ========================================
            // TEAM SETTINGS
            // ========================================
            [
                'key' => 'team_formation_enabled',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Enable or disable team formation'
            ],
            [
                'key' => 'team_min_size_licence',
                'value' => '2',
                'type' => 'integer',
                'description' => 'Minimum team size for Licence students'
            ],
            [
                'key' => 'team_max_size_licence',
                'value' => '3',
                'type' => 'integer',
                'description' => 'Maximum team size for Licence students'
            ],
            [
                'key' => 'team_min_size_master',
                'value' => '1',
                'type' => 'integer',
                'description' => 'Minimum team size for Master students'
            ],
            [
                'key' => 'team_max_size_master',
                'value' => '2',
                'type' => 'integer',
                'description' => 'Maximum team size for Master students'
            ],

            // ========================================
            // SUBJECT PREFERENCE SETTINGS
            // ========================================
            [
                'key' => 'preferences_enabled',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Enable or disable subject preferences'
            ],
            [
                'key' => 'max_subject_preferences',
                'value' => '10',
                'type' => 'integer',
                'description' => 'Maximum number of subject preferences a team can select'
            ],
            [
                'key' => 'min_subject_preferences',
                'value' => '3',
                'type' => 'integer',
                'description' => 'Minimum number of subject preferences required'
            ],

            // ========================================
            // SUBJECT SETTINGS
            // ========================================
            [
                'key' => 'students_can_create_subjects',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Allow students to propose their own subjects'
            ],
            [
                'key' => 'subject_validation_required',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Require department head validation for subjects'
            ],
            [
                'key' => 'external_projects_allowed',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Allow students to submit external company projects'
            ],

            // ========================================
            // REGISTRATION SETTINGS
            // ========================================
            [
                'key' => 'registration_open',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'System-wide registration status (open/closed)'
            ],
            [
                'key' => 'require_profile_completion',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Require students to complete their profile before accessing features'
            ],
            [
                'key' => 'require_birth_certificate',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Require students to upload birth certificate'
            ],
            [
                'key' => 'require_previous_marks',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Require students to enter previous semester marks'
            ],

            // ========================================
            // FILE UPLOAD SETTINGS
            // ========================================
            [
                'key' => 'max_file_upload_size',
                'value' => '10240',
                'type' => 'integer',
                'description' => 'Maximum file upload size in KB (default: 10MB)'
            ],
            [
                'key' => 'allowed_file_extensions',
                'value' => 'pdf,doc,docx,zip,rar,ppt,pptx',
                'type' => 'string',
                'description' => 'Allowed file extensions for uploads (comma-separated)'
            ],

            // ========================================
            // DEFENSE SETTINGS
            // ========================================
            [
                'key' => 'defense_duration_minutes',
                'value' => '30',
                'type' => 'integer',
                'description' => 'Default defense duration in minutes'
            ],
            [
                'key' => 'defense_notice_min_days',
                'value' => '7',
                'type' => 'integer',
                'description' => 'Minimum days notice required for defense scheduling'
            ],
            [
                'key' => 'auto_scheduling_enabled',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Enable automatic defense scheduling'
            ],

            // ========================================
            // NOTIFICATION SETTINGS
            // ========================================
            [
                'key' => 'email_notifications_enabled',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Enable or disable all email notifications'
            ],
            [
                'key' => 'notification_team_invite_enabled',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Send email when student is invited to team'
            ],
            [
                'key' => 'notification_subject_assigned_enabled',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Send email when subject is assigned to team'
            ],
            [
                'key' => 'notification_defense_scheduled_enabled',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Send email when defense is scheduled'
            ],
            [
                'key' => 'notification_grade_published_enabled',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Send email when grades are published'
            ],

            // ========================================
            // ALLOCATION SETTINGS
            // ========================================
            [
                'key' => 'auto_allocation_enabled',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Enable automatic subject allocation'
            ],
            [
                'key' => 'allocation_algorithm',
                'value' => 'priority_based',
                'type' => 'string',
                'description' => 'Allocation algorithm: priority_based, random, first_come'
            ],
            [
                'key' => 'allow_second_round_allocation',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Allow second round allocation for unassigned teams'
            ],

            // ========================================
            // SYSTEM SETTINGS
            // ========================================
            [
                'key' => 'maintenance_mode',
                'value' => '0',
                'type' => 'boolean',
                'description' => 'Put system in maintenance mode (only admins can access)'
            ],
            [
                'key' => 'maintenance_message',
                'value' => 'System is currently under maintenance. Please try again later.',
                'type' => 'string',
                'description' => 'Message displayed during maintenance mode'
            ],
            [
                'key' => 'default_language',
                'value' => 'fr',
                'type' => 'string',
                'description' => 'Default system language (ar, fr, en)'
            ],
            [
                'key' => 'available_languages',
                'value' => 'ar,fr,en',
                'type' => 'string',
                'description' => 'Available languages (comma-separated)'
            ],

            // ========================================
            // UNIVERSITY INFORMATION
            // ========================================
            [
                'key' => 'university_name_ar',
                'value' => 'جامعة أحمد بوڤرة - بومرداس',
                'type' => 'string',
                'description' => 'University name in Arabic'
            ],
            [
                'key' => 'university_name_fr',
                'value' => "Université M'Hamed BOUGARA - Boumerdes",
                'type' => 'string',
                'description' => 'University name in French'
            ],
            [
                'key' => 'faculty_name_ar',
                'value' => 'كلية العلوم',
                'type' => 'string',
                'description' => 'Faculty name in Arabic'
            ],
            [
                'key' => 'faculty_name_fr',
                'value' => 'Faculté des Sciences',
                'type' => 'string',
                'description' => 'Faculty name in French'
            ],
            [
                'key' => 'department_name_ar',
                'value' => 'قسم الاعلام الآلي',
                'type' => 'string',
                'description' => 'Department name in Arabic'
            ],
            [
                'key' => 'department_name_fr',
                'value' => 'Département : Informatique',
                'type' => 'string',
                'description' => 'Department name in French'
            ],
            [
                'key' => 'ministry_name_ar',
                'value' => 'وزارة التعليم العالي و البحث العلمي',
                'type' => 'string',
                'description' => 'Ministry name in Arabic'
            ],
            [
                'key' => 'ministry_name_fr',
                'value' => "Ministère de l'Enseignement Supérieur et de la Recherche Scientifique",
                'type' => 'string',
                'description' => 'Ministry name in French'
            ],
            [
                'key' => 'republic_name_ar',
                'value' => 'الجمهورية الجزائرية الديمقراطية الشعبية',
                'type' => 'string',
                'description' => 'Republic name in Arabic'
            ],
            [
                'key' => 'republic_name_fr',
                'value' => 'RÉPUBLIQUE ALGÉRIENNE DÉMOCRATIQUE ET POPULAIRE',
                'type' => 'string',
                'description' => 'Republic name in French'
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('Settings seeded successfully!');
    }
}
