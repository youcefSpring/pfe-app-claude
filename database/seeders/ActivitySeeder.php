<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        $activities = [
            [
                'description' => 'Nouveau sujet "E-Learning Platform with AI" créé',
                'icon' => 'plus',
                'user_id' => User::where('email', 'said.mansouri@pfe.edu')->first()?->id,
                'created_at' => now()->subHours(2),
            ],
            [
                'description' => 'Équipe "WebTech Innovators" validée',
                'icon' => 'check-circle',
                'user_id' => User::where('role', 'chef_master')->first()?->id,
                'created_at' => now()->subHours(5),
            ],
            [
                'description' => 'Livrable "Documentation Technique" soumis',
                'icon' => 'file-text',
                'user_id' => User::where('email', 'yassine.zerrouki@student.pfe.edu')->first()?->id,
                'created_at' => now()->subDays(1),
            ],
            [
                'description' => 'Soutenance programmée pour l\'équipe "CodeCrafters"',
                'icon' => 'calendar',
                'user_id' => User::where('role', 'admin_pfe')->first()?->id,
                'created_at' => now()->subDays(2),
            ],
            [
                'description' => 'Conflit d\'attribution résolu pour le sujet "Blockchain Certificate"',
                'icon' => 'shield-check',
                'user_id' => User::where('role', 'chef_master')->first()?->id,
                'created_at' => now()->subDays(3),
            ],
        ];

        foreach ($activities as $activity) {
            Activity::create($activity);
        }
    }
}