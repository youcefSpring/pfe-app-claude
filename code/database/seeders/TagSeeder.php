<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            ['name' => 'Web Development', 'slug' => 'web-development'],
            ['name' => 'Machine Learning', 'slug' => 'machine-learning'],
            ['name' => 'IoT', 'slug' => 'iot'],
            ['name' => 'Mobile Development', 'slug' => 'mobile-development'],
            ['name' => 'Blockchain', 'slug' => 'blockchain'],
            ['name' => 'AI', 'slug' => 'ai'],
            ['name' => 'Data Science', 'slug' => 'data-science'],
            ['name' => 'Cybersecurity', 'slug' => 'cybersecurity'],
        ];

        foreach ($tags as $tag) {
            Tag::create($tag);
        }
    }
}