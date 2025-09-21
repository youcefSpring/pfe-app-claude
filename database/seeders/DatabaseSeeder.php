<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Core system setup
            RoleAndPermissionSeeder::class,
            UserSeeder::class,

            // Academic setup
            RoomSeeder::class,
            SubjectSeeder::class,

            // Team and project setup
            TeamSeeder::class,
            TeamSubjectPreferenceSeeder::class,
            ProjectSeeder::class,

            // Deliverables and progress
            DeliverableSeeder::class,

            // Defense system
            DefenseSeeder::class,

            // Notifications and activities
            NotificationSeeder::class,
            ActivitySeeder::class,

            // Sample content for public site
            CourseSeeder::class,
            PublicProjectSeeder::class,
            PublicationSeeder::class,
            BlogPostSeeder::class,
            TagSeeder::class,
        ]);
    }
}