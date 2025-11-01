<?php

return [
    // Team Settings
    'team_formation_enabled_label' => 'Enable Team Formation',
    'team_formation_enabled_desc' => 'Allow students to create and join teams',

    'team_min_size_licence_label' => 'Licence Min Team Size',
    'team_min_size_licence_desc' => 'Minimum number of members for Licence teams',

    'team_max_size_licence_label' => 'Licence Max Team Size',
    'team_max_size_licence_desc' => 'Maximum number of members for Licence teams',

    'team_min_size_master_label' => 'Master Min Team Size',
    'team_min_size_master_desc' => 'Minimum number of members for Master teams',

    'team_max_size_master_label' => 'Master Max Team Size',
    'team_max_size_master_desc' => 'Maximum number of members for Master teams',

    // Subject Preference Settings
    'preferences_enabled_label' => 'Enable Subject Preferences',
    'preferences_enabled_desc' => 'Allow teams to select their preferred subjects',

    'max_subject_preferences_label' => 'Maximum Preferences',
    'max_subject_preferences_desc' => 'Maximum number of subjects a team can select',

    'min_subject_preferences_label' => 'Minimum Preferences',
    'min_subject_preferences_desc' => 'Minimum number of preferences required',

    // Subject Settings
    'students_can_create_subjects_label' => 'Students Can Propose Subjects',
    'students_can_create_subjects_desc' => 'Allow students to submit their own project subjects',

    'subject_validation_required_label' => 'Subject Validation Required',
    'subject_validation_required_desc' => 'Subjects must be validated by department head',

    'external_projects_allowed_label' => 'Allow External Projects',
    'external_projects_allowed_desc' => 'Students can submit projects from external companies',

    // Registration Settings
    'registration_open_label' => 'Registration Open',
    'registration_open_desc' => 'System-wide registration status',

    'require_profile_completion_label' => 'Require Profile Completion',
    'require_profile_completion_desc' => 'Students must complete profile setup before accessing features',

    'require_birth_certificate_label' => 'Require Birth Certificate',
    'require_birth_certificate_desc' => 'Students must upload their birth certificate',

    'require_previous_marks_label' => 'Require Previous Marks',
    'require_previous_marks_desc' => 'Students must enter marks from previous semesters',

    // File Upload Settings
    'max_file_upload_size_label' => 'Max File Upload Size',
    'max_file_upload_size_desc' => 'Maximum file size in KB (1024 KB = 1 MB)',

    'allowed_file_extensions_label' => 'Allowed File Extensions',
    'allowed_file_extensions_desc' => 'Comma-separated list of allowed file types',

    // Defense Settings
    'defense_duration_minutes_label' => 'Defense Duration',
    'defense_duration_minutes_desc' => 'Default duration for defense sessions (minutes)',

    'defense_notice_min_days_label' => 'Minimum Notice Period',
    'defense_notice_min_days_desc' => 'Minimum days of advance notice for defense scheduling',

    'auto_scheduling_enabled_label' => 'Auto-Scheduling Enabled',
    'auto_scheduling_enabled_desc' => 'Enable automatic defense scheduling system',

    // Notification Settings
    'email_notifications_enabled_label' => 'Email Notifications',
    'email_notifications_enabled_desc' => 'Enable or disable all email notifications',

    'notification_team_invite_enabled_label' => 'Team Invite Notifications',
    'notification_team_invite_enabled_desc' => 'Send email when student receives team invitation',

    'notification_subject_assigned_enabled_label' => 'Subject Assignment Notifications',
    'notification_subject_assigned_enabled_desc' => 'Send email when subject is assigned to team',

    'notification_defense_scheduled_enabled_label' => 'Defense Schedule Notifications',
    'notification_defense_scheduled_enabled_desc' => 'Send email when defense is scheduled',

    'notification_grade_published_enabled_label' => 'Grade Published Notifications',
    'notification_grade_published_enabled_desc' => 'Send email when grades are published',

    // Allocation Settings
    'auto_allocation_enabled_label' => 'Auto-Allocation Enabled',
    'auto_allocation_enabled_desc' => 'Enable automatic subject allocation system',

    'allocation_algorithm_label' => 'Allocation Algorithm',
    'allocation_algorithm_desc' => 'Algorithm used for subject allocation (priority_based, random, first_come)',

    'allow_second_round_allocation_label' => 'Allow Second Round',
    'allow_second_round_allocation_desc' => 'Enable second round allocation for unassigned teams',

    // System Settings
    'maintenance_mode_label' => 'Maintenance Mode',
    'maintenance_mode_desc' => 'Put system in maintenance mode (only admins can access)',

    'maintenance_message_label' => 'Maintenance Message',
    'maintenance_message_desc' => 'Message displayed to users during maintenance',

    'default_language_label' => 'Default Language',
    'default_language_desc' => 'Default system language (ar, fr, en)',

    'available_languages_label' => 'Available Languages',
    'available_languages_desc' => 'Comma-separated list of enabled languages',
];
