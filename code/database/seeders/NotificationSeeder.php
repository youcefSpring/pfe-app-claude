<?php

namespace Database\Seeders;

use App\Models\PfeNotification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = User::where('role', 'student')->get();
        $teachers = User::where('role', 'teacher')->get();
        $admins = User::whereIn('role', ['admin_pfe', 'chef_master'])->get();

        // Notifications for students
        foreach ($students->take(5) as $student) {
            // Subject published notification
            PfeNotification::create([
                'user_id' => $student->id,
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => $student->id,
                'type' => 'subject_published',
                'title' => 'Nouveaux sujets disponibles',
                'message' => 'De nouveaux sujets de PFE ont été publiés et sont maintenant disponibles pour sélection.',
                'data' => [
                    'action_url' => '/pfe/subjects/available',
                    'icon' => 'book',
                    'color' => 'blue'
                ],
                'read_at' => null,
                'created_at' => now()->subHours(2),
            ]);

            // Team validation notification
            PfeNotification::create([
                'user_id' => $student->id,
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => $student->id,
                'type' => 'team_validated',
                'title' => 'Équipe validée',
                'message' => 'Félicitations ! Votre équipe a été validée par l\'administration.',
                'data' => [
                    'action_url' => '/pfe/teams/my-team',
                    'icon' => 'check-circle',
                    'color' => 'green'
                ],
                'read_at' => rand(0, 1) == 1 ? now()->subHours(rand(1, 12)) : null,
                'created_at' => now()->subDays(3),
            ]);

            // Defense scheduled notification
            if (rand(0, 1)) {
                PfeNotification::create([
                    'user_id' => $student->id,
                    'notifiable_type' => 'App\\Models\\User',
                    'notifiable_id' => $student->id,
                    'type' => 'defense_scheduled',
                    'title' => 'Soutenance programmée',
                    'message' => 'Votre soutenance de PFE a été programmée. Consultez les détails.',
                    'data' => [
                        'action_url' => '/pfe/defenses',
                        'icon' => 'calendar',
                        'color' => 'purple',
                        'defense_date' => now()->addDays(15)->format('Y-m-d'),
                        'defense_time' => '10:30'
                    ],
                    'read_at' => null,
                    'created_at' => now()->subDays(1),
                ]);
            }
        }

        // Notifications for teachers
        foreach ($teachers->take(3) as $teacher) {
            // Subject validation required
            PfeNotification::create([
                'user_id' => $teacher->id,
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => $teacher->id,
                'type' => 'deliverable_submitted',
                'title' => 'Nouveau livrable à évaluer',
                'message' => 'Un nouveau livrable a été soumis par votre équipe encadrée et nécessite votre évaluation.',
                'data' => [
                    'action_url' => '/pfe/projects',
                    'icon' => 'file-text',
                    'color' => 'orange'
                ],
                'read_at' => null,
                'created_at' => now()->subHours(6),
            ]);

            // Defense jury assignment
            PfeNotification::create([
                'user_id' => $teacher->id,
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => $teacher->id,
                'type' => 'jury_assignment',
                'title' => 'Affectation jury de soutenance',
                'message' => 'Vous avez été désigné(e) comme membre du jury pour une soutenance de PFE.',
                'data' => [
                    'action_url' => '/pfe/defenses',
                    'icon' => 'users',
                    'color' => 'indigo'
                ],
                'read_at' => rand(0, 1) == 1 ? now()->subHours(rand(1, 12)) : null,
                'created_at' => now()->subDays(2),
            ]);
        }

        // Notifications for admins
        foreach ($admins as $admin) {
            // System notification
            PfeNotification::create([
                'user_id' => $admin->id,
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => $admin->id,
                'type' => 'subject_submitted',
                'title' => 'Sujet soumis pour validation',
                'message' => 'Un nouveau sujet de PFE a été soumis et attend votre validation.',
                'data' => [
                    'action_url' => '/pfe/subjects',
                    'icon' => 'clipboard-check',
                    'color' => 'yellow'
                ],
                'read_at' => null,
                'created_at' => now()->subHours(12),
            ]);

            // Conflict resolution needed
            PfeNotification::create([
                'user_id' => $admin->id,
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => $admin->id,
                'type' => 'conflict_resolution',
                'title' => 'Conflit d\'attribution détecté',
                'message' => 'Un conflit d\'attribution de sujet nécessite votre intervention pour résolution.',
                'data' => [
                    'action_url' => '/pfe/admin/conflicts',
                    'icon' => 'exclamation-triangle',
                    'color' => 'red'
                ],
                'read_at' => null,
                'created_at' => now()->subDays(1),
            ]);

            // System maintenance
            PfeNotification::create([
                'user_id' => $admin->id,
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => $admin->id,
                'type' => 'system_maintenance',
                'title' => 'Maintenance système programmée',
                'message' => 'Une maintenance système est programmée ce weekend. Préparez les communications nécessaires.',
                'data' => [
                    'action_url' => '/pfe/admin/settings',
                    'icon' => 'cog',
                    'color' => 'gray'
                ],
                'read_at' => rand(0, 1) == 1 ? now()->subHours(rand(1, 12)) : null,
                'created_at' => now()->subDays(5),
            ]);
        }

        // Global announcements
        $allUsers = User::all();
        foreach ($allUsers->random(10) as $user) {
            PfeNotification::create([
                'user_id' => $user->id,
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => $user->id,
                'type' => 'announcement',
                'title' => 'Nouvelle année académique 2024-2025',
                'message' => 'Bienvenue dans la nouvelle année académique ! Consultez le calendrier des échéances importantes.',
                'data' => [
                    'action_url' => '/pfe/dashboard',
                    'icon' => 'megaphone',
                    'color' => 'blue',
                    'priority' => 'high'
                ],
                'read_at' => rand(0, 1) == 1 ? now()->subHours(rand(1, 12)) : null,
                'created_at' => now()->subWeeks(2),
            ]);
        }
    }
}