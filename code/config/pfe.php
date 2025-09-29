<?php

return [
    /*
    |--------------------------------------------------------------------------
    | PFE System Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration settings for the PFE (Projet de Fin d'Études) platform
    |
    */

    'institution' => [
        'name' => env('INSTITUTION_NAME', 'Université de Technologie'),
        'faculty' => env('FACULTY_NAME', 'Faculté des Sciences et Techniques'),
        'department' => env('DEPARTMENT_NAME', 'Département d\'Informatique'),
        'address' => env('INSTITUTION_ADDRESS', 'Adresse de l\'Université'),
        'logo_path' => env('INSTITUTION_LOGO', 'images/university-logo.png'),
        'website' => env('INSTITUTION_WEBSITE', 'https://university.edu'),
    ],

    'academic' => [
        'current_year' => env('ACADEMIC_YEAR', '2024-2025'),
        'semester_start_month' => env('SEMESTER_START_MONTH', 9), // September
        'defense_period_start' => env('DEFENSE_PERIOD_START', '2025-05-01'),
        'defense_period_end' => env('DEFENSE_PERIOD_END', '2025-07-31'),
    ],

    'teams' => [
        'min_size' => env('TEAM_MIN_SIZE', 2),
        'max_size' => env('TEAM_MAX_SIZE', 4),
        'optimal_size' => env('TEAM_OPTIMAL_SIZE', 3),
        'allow_cross_department' => env('ALLOW_CROSS_DEPARTMENT_TEAMS', false),
        'formation_deadline' => env('TEAM_FORMATION_DEADLINE', '2024-12-31'),
    ],

    'subjects' => [
        'min_keywords' => env('SUBJECT_MIN_KEYWORDS', 3),
        'max_keywords' => env('SUBJECT_MAX_KEYWORDS', 10),
        'min_description_length' => env('SUBJECT_MIN_DESC_LENGTH', 100),
        'max_teams_per_subject' => env('SUBJECT_MAX_TEAMS', 5),
        'submission_deadline' => env('SUBJECT_SUBMISSION_DEADLINE', '2024-10-31'),
        'validation_period_days' => env('SUBJECT_VALIDATION_PERIOD', 14),
    ],

    'projects' => [
        'default_duration_months' => env('PROJECT_DEFAULT_DURATION', 6),
        'milestone_count' => env('PROJECT_MILESTONE_COUNT', 5),
        'deliverable_formats' => [
            'documents' => ['pdf', 'doc', 'docx'],
            'code' => ['zip', 'rar', '7z', 'tar.gz'],
            'presentations' => ['ppt', 'pptx', 'pdf'],
            'media' => ['mp4', 'avi', 'mov', 'mkv'],
        ],
        'max_file_size_mb' => env('MAX_FILE_SIZE_MB', 50),
    ],

    'defenses' => [
        'min_duration_minutes' => env('DEFENSE_MIN_DURATION', 30),
        'max_duration_minutes' => env('DEFENSE_MAX_DURATION', 120),
        'default_duration_minutes' => env('DEFENSE_DEFAULT_DURATION', 60),
        'preparation_time_minutes' => env('DEFENSE_PREPARATION_TIME', 15),
        'working_hours_start' => env('DEFENSE_HOURS_START', '08:00'),
        'working_hours_end' => env('DEFENSE_HOURS_END', '18:00'),
        'exclude_weekends' => env('DEFENSE_EXCLUDE_WEEKENDS', true),
        'min_notice_hours' => env('DEFENSE_MIN_NOTICE_HOURS', 48),
        'auto_schedule_enabled' => env('DEFENSE_AUTO_SCHEDULE', true),
    ],

    'grading' => [
        'scale_max' => env('GRADING_SCALE_MAX', 20),
        'pass_threshold' => env('GRADING_PASS_THRESHOLD', 10),
        'excellence_threshold' => env('GRADING_EXCELLENCE_THRESHOLD', 18),
        'components' => [
            'technical' => [
                'weight' => 0.4,
                'label' => 'Aspect Technique',
            ],
            'presentation' => [
                'weight' => 0.3,
                'label' => 'Présentation Orale',
            ],
            'report' => [
                'weight' => 0.3,
                'label' => 'Rapport Écrit',
            ],
        ],
        'mentions' => [
            18 => 'Excellent',
            16 => 'Très Bien',
            14 => 'Bien',
            12 => 'Assez Bien',
            10 => 'Passable',
            0 => 'Insuffisant',
        ],
    ],

    'notifications' => [
        'channels' => ['database', 'mail'],
        'types' => [
            'subject_created' => ['database'],
            'subject_submitted' => ['database', 'mail'],
            'subject_validated' => ['database', 'mail'],
            'subject_published' => ['database'],
            'team_invitation' => ['database', 'mail'],
            'team_validated' => ['database'],
            'project_assigned' => ['database', 'mail'],
            'defense_scheduled' => ['database', 'mail'],
            'defense_rescheduled' => ['database', 'mail'],
            'milestone_completed' => ['database'],
            'deliverable_reviewed' => ['database', 'mail'],
            'pv_generated' => ['database', 'mail'],
        ],
        'digest_enabled' => env('NOTIFICATION_DIGEST_ENABLED', true),
        'digest_frequency' => env('NOTIFICATION_DIGEST_FREQUENCY', 'daily'), // daily, weekly
    ],

    'conflict_resolution' => [
        'auto_resolve_enabled' => env('CONFLICT_AUTO_RESOLVE', false),
        'default_criteria' => env('CONFLICT_DEFAULT_CRITERIA', 'merit'), // merit, registration_order, random
        'resolution_deadline_days' => env('CONFLICT_RESOLUTION_DEADLINE', 7),
        'escalation_enabled' => env('CONFLICT_ESCALATION_ENABLED', true),
    ],

    'external_projects' => [
        'enabled' => env('EXTERNAL_PROJECTS_ENABLED', true),
        'auto_supervisor_assignment' => env('AUTO_SUPERVISOR_ASSIGNMENT', true),
        'supervisor_balance_enabled' => env('SUPERVISOR_BALANCE_ENABLED', true),
        'max_projects_per_supervisor' => env('MAX_PROJECTS_PER_SUPERVISOR', 5),
        'required_fields' => [
            'external_supervisor',
            'external_company',
            'company_contact_email',
        ],
    ],

    'security' => [
        'rate_limits' => [
            'file_upload' => env('RATE_LIMIT_FILE_UPLOAD', '30,1'), // 30 per minute
            'subject_submission' => env('RATE_LIMIT_SUBJECT_SUBMISSION', '10,1'), // 10 per minute
            'team_formation' => env('RATE_LIMIT_TEAM_FORMATION', '5,1'), // 5 per minute
        ],
        'allowed_file_types' => [
            'documents' => ['pdf', 'doc', 'docx', 'txt', 'rtf'],
            'code' => ['zip', 'rar', '7z', 'tar.gz', 'tar.bz2'],
            'presentations' => ['ppt', 'pptx', 'odp'],
            'spreadsheets' => ['xls', 'xlsx', 'ods', 'csv'],
            'images' => ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg'],
            'videos' => ['mp4', 'avi', 'mov', 'mkv', 'webm'],
        ],
        'quarantine_suspicious_files' => env('QUARANTINE_SUSPICIOUS_FILES', true),
        'scan_uploads' => env('SCAN_UPLOADS', false),
    ],

    'analytics' => [
        'enabled' => env('ANALYTICS_ENABLED', true),
        'retention_days' => env('ANALYTICS_RETENTION_DAYS', 365),
        'track_page_views' => env('TRACK_PAGE_VIEWS', true),
        'track_user_actions' => env('TRACK_USER_ACTIONS', true),
        'track_performance' => env('TRACK_PERFORMANCE', false),
    ],

    'backup' => [
        'enabled' => env('BACKUP_ENABLED', true),
        'frequency' => env('BACKUP_FREQUENCY', 'daily'), // daily, weekly
        'retention_days' => env('BACKUP_RETENTION_DAYS', 30),
        'include_files' => env('BACKUP_INCLUDE_FILES', true),
        'compress' => env('BACKUP_COMPRESS', true),
    ],

    'maintenance' => [
        'auto_cleanup_enabled' => env('AUTO_CLEANUP_ENABLED', true),
        'cleanup_old_logs_days' => env('CLEANUP_OLD_LOGS_DAYS', 90),
        'cleanup_temp_files_hours' => env('CLEANUP_TEMP_FILES_HOURS', 24),
        'optimize_database' => env('OPTIMIZE_DATABASE', true),
    ],

    'integrations' => [
        'ldap_enabled' => env('LDAP_ENABLED', false),
        'ldap_server' => env('LDAP_SERVER'),
        'ldap_base_dn' => env('LDAP_BASE_DN'),
        'sms_enabled' => env('SMS_ENABLED', false),
        'sms_provider' => env('SMS_PROVIDER', 'twilio'),
        'calendar_sync' => env('CALENDAR_SYNC_ENABLED', false),
        'calendar_provider' => env('CALENDAR_PROVIDER', 'google'),
    ],

    'features' => [
        'student_self_registration' => env('STUDENT_SELF_REGISTRATION', false),
        'teacher_subject_cloning' => env('TEACHER_SUBJECT_CLONING', true),
        'team_chat_enabled' => env('TEAM_CHAT_ENABLED', false),
        'project_templates' => env('PROJECT_TEMPLATES_ENABLED', true),
        'advanced_analytics' => env('ADVANCED_ANALYTICS_ENABLED', false),
        'mobile_app_support' => env('MOBILE_APP_SUPPORT', false),
        'api_access' => env('API_ACCESS_ENABLED', true),
        'webhook_notifications' => env('WEBHOOK_NOTIFICATIONS', false),
    ],
];