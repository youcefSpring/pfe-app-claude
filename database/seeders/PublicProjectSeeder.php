<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class PublicProjectSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        $projects = [
            [
                'title' => 'PFE Management Platform',
                'slug' => 'pfe-management-platform',
                'description' => 'Comprehensive platform for managing final year projects in universities.',
                'content' => 'This platform provides a complete solution for managing PFE projects...',
                'technologies' => ['Laravel', 'React', 'MySQL', 'Docker'],
                'status' => 'completed',
                'featured' => true,
                'user_id' => $user->id,
            ],
        ];

        foreach ($projects as $project) {
            Project::create($project);
        }
    }
}