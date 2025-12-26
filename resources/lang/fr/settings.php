<?php

return [
    // Team Settings
    'team_formation_enabled_label' => 'Activer la Formation d\'Équipes',
    'team_formation_enabled_desc' => 'Permettre aux étudiants de créer et rejoindre des équipes',

    'team_min_size_licence_label' => 'Taille Min Équipe Licence',
    'team_min_size_licence_desc' => 'Nombre minimum de membres pour les équipes Licence',

    'team_max_size_licence_label' => 'Taille Max Équipe Licence',
    'team_max_size_licence_desc' => 'Nombre maximum de membres pour les équipes Licence',

    'team_min_size_master_label' => 'Taille Min Équipe Master',
    'team_min_size_master_desc' => 'Nombre minimum de membres pour les équipes Master',

    'team_max_size_master_label' => 'Taille Max Équipe Master',
    'team_max_size_master_desc' => 'Nombre maximum de membres pour les équipes Master',

    // Subject Preference Settings
    'preferences_enabled_label' => 'Activer les Préférences',
    'preferences_enabled_desc' => 'Permettre aux équipes de sélectionner leurs sujets préférés',

    'max_subject_preferences_label' => 'Préférences Maximum',
    'max_subject_preferences_desc' => 'Nombre maximum de sujets qu\'une équipe peut sélectionner',

    'min_subject_preferences_label' => 'Préférences Minimum',
    'min_subject_preferences_desc' => 'Nombre minimum de préférences requises',

    // Subject Settings
    'students_can_create_subjects_label' => 'Étudiants Peuvent Proposer des Sujets',
    'students_can_create_subjects_desc' => 'Permettre aux étudiants de soumettre leurs propres sujets',

    'subject_validation_required_label' => 'Validation de Sujets Requise',
    'subject_validation_required_desc' => 'Les sujets doivent être validés par le chef de département',

    'external_projects_allowed_label' => 'Autoriser les Projets Externes',
    'external_projects_allowed_desc' => 'Les étudiants peuvent soumettre des projets d\'entreprises externes',

    // Registration Settings
    'registration_open_label' => 'Inscription Ouverte',
    'registration_open_desc' => 'Statut d\'inscription à l\'échelle du système',

    'require_profile_completion_label' => 'Exiger Profil Complet',
    'require_profile_completion_desc' => 'Les étudiants doivent compléter leur profil avant d\'accéder aux fonctionnalités',

    'require_birth_certificate_label' => 'Exiger Acte de Naissance',
    'require_birth_certificate_desc' => 'Les étudiants doivent télécharger leur acte de naissance',

    'require_previous_marks_label' => 'Exiger Notes Précédentes',
    'require_previous_marks_desc' => 'Les étudiants doivent saisir les notes des semestres précédents',

    // File Upload Settings
    'max_file_upload_size_label' => 'Taille Max Téléchargement',
    'max_file_upload_size_desc' => 'Taille maximale de fichier en Ko (1024 Ko = 1 Mo)',

    'allowed_file_extensions_label' => 'Extensions Autorisées',
    'allowed_file_extensions_desc' => 'Liste des types de fichiers autorisés séparés par des virgules',

    // Defense Settings
    'defense_duration_minutes_label' => 'Durée de Soutenance',
    'defense_duration_minutes_desc' => 'Durée par défaut des sessions de soutenance (minutes)',

    'defense_notice_min_days_label' => 'Préavis Minimum',
    'defense_notice_min_days_desc' => 'Nombre minimum de jours de préavis pour programmer une soutenance',

    'auto_scheduling_enabled_label' => 'Planification Auto Activée',
    'auto_scheduling_enabled_desc' => 'Activer le système de planification automatique des soutenances',

    // Notification Settings
    'email_notifications_enabled_label' => 'Notifications Email',
    'email_notifications_enabled_desc' => 'Activer ou désactiver toutes les notifications par email',

    'notification_team_invite_enabled_label' => 'Notifications d\'Invitation',
    'notification_team_invite_enabled_desc' => 'Envoyer un email lors d\'une invitation à une équipe',

    'notification_subject_assigned_enabled_label' => 'Notifications d\'Attribution',
    'notification_subject_assigned_enabled_desc' => 'Envoyer un email lors de l\'attribution d\'un sujet',

    'notification_defense_scheduled_enabled_label' => 'Notifications de Programmation',
    'notification_defense_scheduled_enabled_desc' => 'Envoyer un email lors de la programmation d\'une soutenance',

    'notification_grade_published_enabled_label' => 'Notifications de Notes',
    'notification_grade_published_enabled_desc' => 'Envoyer un email lors de la publication des notes',

    // Allocation Settings
    'auto_allocation_enabled_label' => 'Auto-Attribution Activée',
    'auto_allocation_enabled_desc' => 'Activer le système d\'attribution automatique des sujets',

    'allocation_algorithm_label' => 'Algorithme d\'Attribution',
    'allocation_algorithm_desc' => 'Algorithme utilisé pour l\'attribution (priority_based, random, first_come)',

    'allow_second_round_allocation_label' => 'Autoriser Second Tour',
    'allow_second_round_allocation_desc' => 'Activer le second tour d\'attribution pour les équipes non assignées',

    // System Settings
    'maintenance_mode_label' => 'Mode Maintenance',
    'maintenance_mode_desc' => 'Mettre le système en mode maintenance (seuls les admins peuvent accéder)',

    'maintenance_message_label' => 'Message de Maintenance',
    'maintenance_message_desc' => 'Message affiché aux utilisateurs pendant la maintenance',

    'default_language_label' => 'Langue Par Défaut',
    'default_language_desc' => 'Langue par défaut du système (ar, fr, en)',

    'available_languages_label' => 'Langues Disponibles',
    'available_languages_desc' => 'Liste des langues activées séparées par des virgules',
];
