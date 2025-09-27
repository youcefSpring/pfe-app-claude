<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Database\Seeder;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        BlogPost::create([
            'title' => 'Getting Started with Your PFE Project',
            'slug' => 'getting-started-pfe-project',
            'excerpt' => 'Essential tips for students beginning their final year project journey.',
            'content' => 'Starting your PFE project can be overwhelming. Here are some essential tips...',
            'status' => 'published',
            'featured' => true,
            'published_at' => now()->subDays(7),
            'user_id' => $user->id,
        ]);
    }
}