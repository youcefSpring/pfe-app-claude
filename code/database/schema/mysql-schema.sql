/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `allocation_deadlines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `allocation_deadlines` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `academic_year` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `preferences_start` datetime NOT NULL,
  `preferences_deadline` datetime NOT NULL,
  `grades_verification_deadline` datetime NOT NULL,
  `allocation_date` datetime NOT NULL,
  `status` enum('draft','active','preferences_closed','grades_pending','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `allocation_deadlines_created_by_foreign` (`created_by`),
  KEY `allocation_deadlines_academic_year_level_index` (`academic_year`,`level`),
  KEY `allocation_deadlines_status_index` (`status`),
  CONSTRAINT `allocation_deadlines_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `idx_expiration` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `conflict_teams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `conflict_teams` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `conflict_id` bigint unsigned NOT NULL,
  `team_id` bigint unsigned NOT NULL,
  `priority_score` int NOT NULL DEFAULT '0',
  `selection_date` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `conflict_teams_conflict_id_team_id_unique` (`conflict_id`,`team_id`),
  KEY `conflict_teams_team_id_foreign` (`team_id`),
  CONSTRAINT `conflict_teams_conflict_id_foreign` FOREIGN KEY (`conflict_id`) REFERENCES `subject_conflicts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `conflict_teams_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `defense_juries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `defense_juries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `defense_id` bigint unsigned NOT NULL,
  `teacher_id` bigint unsigned NOT NULL,
  `role` enum('president','examiner','supervisor') COLLATE utf8mb4_unicode_ci NOT NULL,
  `individual_grade` decimal(4,2) DEFAULT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `defense_juries_defense_id_teacher_id_unique` (`defense_id`,`teacher_id`),
  KEY `defense_juries_teacher_id_foreign` (`teacher_id`),
  CONSTRAINT `defense_juries_defense_id_foreign` FOREIGN KEY (`defense_id`) REFERENCES `defenses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `defense_juries_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `defense_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `defense_reports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `defense_id` bigint unsigned NOT NULL,
  `file_path` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `generated_at` timestamp NOT NULL,
  `generated_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `defense_reports_defense_id_unique` (`defense_id`),
  KEY `defense_reports_generated_by_foreign` (`generated_by`),
  CONSTRAINT `defense_reports_defense_id_foreign` FOREIGN KEY (`defense_id`) REFERENCES `defenses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `defense_reports_generated_by_foreign` FOREIGN KEY (`generated_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `defenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `defenses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned DEFAULT NULL,
  `subject_id` bigint unsigned DEFAULT NULL,
  `room_id` bigint unsigned NOT NULL,
  `defense_date` date NOT NULL,
  `defense_time` time NOT NULL,
  `duration` int NOT NULL DEFAULT '60',
  `status` enum('scheduled','in_progress','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'scheduled',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `scheduled_by` bigint unsigned DEFAULT NULL,
  `scheduled_at` timestamp NULL DEFAULT NULL,
  `final_grade` decimal(4,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `defenses_project_id_foreign` (`project_id`),
  KEY `defenses_room_id_foreign` (`room_id`),
  KEY `defenses_defense_date_defense_time_index` (`defense_date`,`defense_time`),
  KEY `defenses_status_index` (`status`),
  KEY `defenses_subject_id_foreign` (`subject_id`),
  KEY `defenses_scheduled_by_foreign` (`scheduled_by`),
  CONSTRAINT `defenses_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `defenses_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `defenses_scheduled_by_foreign` FOREIGN KEY (`scheduled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `defenses_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `external_projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `external_projects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_person` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `project_description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `technologies` text COLLATE utf8mb4_unicode_ci,
  `team_id` bigint unsigned NOT NULL,
  `assigned_supervisor_id` bigint unsigned DEFAULT NULL,
  `status` enum('submitted','under_review','assigned','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'submitted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `external_projects_team_id_foreign` (`team_id`),
  KEY `external_projects_assigned_supervisor_id_foreign` (`assigned_supervisor_id`),
  KEY `external_projects_status_index` (`status`),
  CONSTRAINT `external_projects_assigned_supervisor_id_foreign` FOREIGN KEY (`assigned_supervisor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `external_projects_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint unsigned NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`),
  KEY `idx_token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `projects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `team_id` bigint unsigned NOT NULL,
  `subject_id` bigint unsigned DEFAULT NULL,
  `external_project_id` bigint unsigned DEFAULT NULL,
  `supervisor_id` bigint unsigned NOT NULL,
  `co_supervisor_id` bigint unsigned DEFAULT NULL,
  `type` enum('internal','external') COLLATE utf8mb4_unicode_ci NOT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` enum('assigned','active','in_progress','submitted','completed','defended') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'assigned',
  PRIMARY KEY (`id`),
  KEY `projects_team_id_foreign` (`team_id`),
  KEY `projects_subject_id_foreign` (`subject_id`),
  KEY `projects_external_project_id_foreign` (`external_project_id`),
  KEY `projects_co_supervisor_id_foreign` (`co_supervisor_id`),
  KEY `projects_type_index` (`type`),
  KEY `projects_supervisor_id_index` (`supervisor_id`),
  CONSTRAINT `projects_co_supervisor_id_foreign` FOREIGN KEY (`co_supervisor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `projects_external_project_id_foreign` FOREIGN KEY (`external_project_id`) REFERENCES `external_projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `projects_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `projects_supervisor_id_foreign` FOREIGN KEY (`supervisor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `projects_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rooms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `capacity` int NOT NULL,
  `equipment` text COLLATE utf8mb4_unicode_ci,
  `is_available` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rooms_name_unique` (`name`),
  KEY `rooms_is_available_index` (`is_available`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`),
  KEY `settings_key_index` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `specialities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `specialities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `level` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'license',
  `academic_year` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `semester` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `specialities_name_level_academic_year_unique` (`name`,`level`,`academic_year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `student_alerts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_alerts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `admin_response` text COLLATE utf8mb4_unicode_ci,
  `responded_at` timestamp NULL DEFAULT NULL,
  `responded_by` bigint unsigned DEFAULT NULL,
  `status` enum('pending','responded') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `student_alerts_user_id_foreign` (`user_id`),
  KEY `student_alerts_responded_by_foreign` (`responded_by`),
  CONSTRAINT `student_alerts_responded_by_foreign` FOREIGN KEY (`responded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `student_alerts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `student_grades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_grades` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint unsigned NOT NULL,
  `subject_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `semester` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `academic_year` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `grade` decimal(5,2) NOT NULL,
  `coefficient` decimal(3,1) NOT NULL DEFAULT '1.0',
  `status` enum('draft','submitted','verified','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `verification_notes` text COLLATE utf8mb4_unicode_ci,
  `verified_by` bigint unsigned DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `student_grades_verified_by_foreign` (`verified_by`),
  KEY `student_grades_student_id_status_index` (`student_id`,`status`),
  KEY `student_grades_student_id_academic_year_index` (`student_id`,`academic_year`),
  CONSTRAINT `student_grades_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_grades_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `student_marks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_marks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `subject_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mark` decimal(5,2) NOT NULL,
  `mark_1` decimal(5,2) DEFAULT NULL,
  `mark_2` decimal(5,2) DEFAULT NULL,
  `mark_3` decimal(5,2) DEFAULT NULL,
  `mark_4` decimal(5,2) DEFAULT NULL,
  `mark_5` decimal(5,2) DEFAULT NULL,
  `max_mark_1` decimal(5,2) NOT NULL DEFAULT '20.00',
  `max_mark_2` decimal(5,2) NOT NULL DEFAULT '20.00',
  `max_mark_3` decimal(5,2) NOT NULL DEFAULT '20.00',
  `max_mark_4` decimal(5,2) NOT NULL DEFAULT '20.00',
  `max_mark_5` decimal(5,2) NOT NULL DEFAULT '20.00',
  `weight_1` decimal(5,2) NOT NULL DEFAULT '20.00',
  `weight_2` decimal(5,2) NOT NULL DEFAULT '20.00',
  `weight_3` decimal(5,2) NOT NULL DEFAULT '20.00',
  `weight_4` decimal(5,2) NOT NULL DEFAULT '20.00',
  `weight_5` decimal(5,2) NOT NULL DEFAULT '20.00',
  `max_mark` decimal(5,2) NOT NULL DEFAULT '20.00',
  `semester` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `academic_year` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_student_subject_semester` (`user_id`,`subject_name`,`semester`,`academic_year`),
  KEY `student_marks_created_by_foreign` (`created_by`),
  CONSTRAINT `student_marks_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_marks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `subject_allocations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subject_allocations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `allocation_deadline_id` bigint unsigned NOT NULL,
  `student_id` bigint unsigned NOT NULL,
  `subject_id` bigint unsigned NOT NULL,
  `student_preference_order` int NOT NULL,
  `student_average` decimal(5,2) NOT NULL,
  `allocation_rank` int NOT NULL,
  `allocation_method` enum('preference','automatic','manual') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'automatic',
  `allocation_notes` text COLLATE utf8mb4_unicode_ci,
  `status` enum('tentative','confirmed','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tentative',
  `confirmed_by` bigint unsigned DEFAULT NULL,
  `confirmed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subject_allocations_allocation_deadline_id_student_id_unique` (`allocation_deadline_id`,`student_id`),
  KEY `subject_allocations_subject_id_foreign` (`subject_id`),
  KEY `subject_allocations_confirmed_by_foreign` (`confirmed_by`),
  KEY `subject_allocations_allocation_deadline_id_subject_id_index` (`allocation_deadline_id`,`subject_id`),
  KEY `subject_allocations_student_id_status_index` (`student_id`,`status`),
  CONSTRAINT `subject_allocations_allocation_deadline_id_foreign` FOREIGN KEY (`allocation_deadline_id`) REFERENCES `allocation_deadlines` (`id`) ON DELETE CASCADE,
  CONSTRAINT `subject_allocations_confirmed_by_foreign` FOREIGN KEY (`confirmed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `subject_allocations_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `subject_allocations_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `subject_conflicts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subject_conflicts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `subject_id` bigint unsigned NOT NULL,
  `status` enum('pending','resolved') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `resolved_by` bigint unsigned DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `resolution_notes` text COLLATE utf8mb4_unicode_ci,
  `winning_team_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subject_conflicts_subject_id_foreign` (`subject_id`),
  KEY `subject_conflicts_resolved_by_foreign` (`resolved_by`),
  KEY `subject_conflicts_winning_team_id_foreign` (`winning_team_id`),
  KEY `subject_conflicts_status_index` (`status`),
  CONSTRAINT `subject_conflicts_resolved_by_foreign` FOREIGN KEY (`resolved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `subject_conflicts_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `subject_conflicts_winning_team_id_foreign` FOREIGN KEY (`winning_team_id`) REFERENCES `teams` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `subject_preferences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subject_preferences` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint unsigned NOT NULL,
  `subject_id` bigint unsigned NOT NULL,
  `preference_order` int NOT NULL,
  `submitted_at` timestamp NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subject_preferences_student_id_subject_id_unique` (`student_id`,`subject_id`),
  UNIQUE KEY `subject_preferences_student_id_preference_order_unique` (`student_id`,`preference_order`),
  KEY `subject_preferences_subject_id_foreign` (`subject_id`),
  KEY `subject_preferences_student_id_preference_order_index` (`student_id`,`preference_order`),
  CONSTRAINT `subject_preferences_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `subject_preferences_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subjects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `keywords` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `tools` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `plan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `teacher_id` bigint unsigned NOT NULL,
  `status` enum('draft','pending_validation','validated','rejected','needs_correction') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `is_external` tinyint(1) NOT NULL DEFAULT '0',
  `company_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dataset_resources_link` text COLLATE utf8mb4_unicode_ci,
  `validation_feedback` text COLLATE utf8mb4_unicode_ci,
  `validated_at` timestamp NULL DEFAULT NULL,
  `validated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `target_grade` enum('license','master','doctorate') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'master',
  `student_id` bigint unsigned DEFAULT NULL,
  `external_supervisor_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subjects_title_unique` (`title`),
  KEY `subjects_validated_by_foreign` (`validated_by`),
  KEY `subjects_status_index` (`status`),
  KEY `subjects_teacher_id_index` (`teacher_id`),
  KEY `subjects_is_external_index` (`is_external`),
  KEY `subjects_student_id_index` (`student_id`),
  KEY `subjects_external_supervisor_id_index` (`external_supervisor_id`),
  CONSTRAINT `subjects_external_supervisor_id_foreign` FOREIGN KEY (`external_supervisor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `subjects_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `subjects_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `subjects_validated_by_foreign` FOREIGN KEY (`validated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `submissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `submissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `type` enum('milestone','final_report') COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `file_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `submission_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('submitted','reviewed','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'submitted',
  `feedback` text COLLATE utf8mb4_unicode_ci,
  `reviewed_by` bigint unsigned DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `submissions_reviewed_by_foreign` (`reviewed_by`),
  KEY `submissions_project_id_type_index` (`project_id`,`type`),
  CONSTRAINT `submissions_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `submissions_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `team_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `team_members` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `team_id` bigint unsigned NOT NULL,
  `student_id` bigint unsigned NOT NULL,
  `role` enum('leader','member') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'member',
  `joined_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `team_members_student_id_unique` (`student_id`),
  KEY `team_members_team_id_index` (`team_id`),
  CONSTRAINT `team_members_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `team_members_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `teams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `teams` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('forming','complete','subject_selected','assigned','active','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'forming',
  `subject_id` bigint unsigned DEFAULT NULL,
  `supervisor_id` bigint unsigned DEFAULT NULL,
  `external_supervisor` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `teams_name_unique` (`name`),
  KEY `teams_supervisor_id_foreign` (`supervisor_id`),
  KEY `teams_status_index` (`status`),
  KEY `teams_subject_id_index` (`subject_id`),
  CONSTRAINT `teams_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `teams_supervisor_id_foreign` FOREIGN KEY (`supervisor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `matricule` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `speciality` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grade` enum('master','phd','professor') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'master',
  `role` enum('student','teacher','department_head','admin','external_supervisor') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'student',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `academic_year` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_completed` tinyint(1) NOT NULL DEFAULT '0',
  `locale` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `speciality_id` bigint unsigned DEFAULT NULL,
  `numero_inscription` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `annee_bac` year DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `section` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `groupe` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_matricule_unique` (`matricule`),
  UNIQUE KEY `users_numero_inscription_unique` (`numero_inscription`),
  KEY `idx_matricule` (`matricule`),
  KEY `idx_email` (`email`),
  KEY `idx_department` (`department`),
  KEY `users_speciality_id_foreign` (`speciality_id`),
  KEY `users_numero_inscription_index` (`numero_inscription`),
  KEY `users_section_index` (`section`),
  KEY `users_groupe_index` (`groupe`),
  CONSTRAINT `users_speciality_id_foreign` FOREIGN KEY (`speciality_id`) REFERENCES `specialities` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'2014_10_12_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'2014_10_12_100000_create_password_reset_tokens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'2019_08_19_000000_create_failed_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2024_01_01_000008_create_cache_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2024_01_01_000009_create_sessions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2025_09_30_075438_create_subjects_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2025_09_30_075440_create_teams_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2025_09_30_075441_create_team_members_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2025_09_30_075444_create_external_projects_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2025_09_30_075445_create_projects_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'2025_09_30_075446_create_rooms_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2025_09_30_075447_create_defenses_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2025_09_30_075448_create_submissions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2025_09_30_075449_create_subject_conflicts_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2025_09_30_075450_create_defense_juries_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2025_09_30_075451_create_defense_reports_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2025_09_30_075455_create_conflict_teams_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2025_09_30_094524_create_specialities_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2025_09_30_094555_add_speciality_fields_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2025_09_30_121237_add_target_grade_to_subjects_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2025_09_30_121241_update_projects_status_enum',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22,'2025_09_30_121307_create_notifications_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23,'2025_09_30_124911_add_external_fields_to_subjects_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (24,'2025_09_30_130219_add_location_to_rooms_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25,'2025_09_30_131016_create_subject_preferences_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26,'2025_09_30_131033_create_student_grades_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27,'2025_09_30_131050_create_allocation_deadlines_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28,'2025_09_30_131109_create_subject_allocations_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (29,'2025_09_30_133257_create_personal_access_tokens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30,'2025_09_30_135106_add_locale_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31,'2025_10_02_182437_add_student_columns_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32,'2025_10_04_184113_create_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (33,'2025_10_05_130512_add_subject_id_to_defenses_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (34,'2025_10_05_131026_create_student_marks_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (35,'2025_10_05_141122_add_multiple_marks_to_student_marks_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (36,'2025_10_05_143509_create_student_alerts_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (37,'2025_10_05_160059_add_external_supervisor_to_subjects_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (38,'2025_10_05_160224_add_position_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (39,'2025_10_05_193858_add_scheduled_fields_to_defenses_table',1);
