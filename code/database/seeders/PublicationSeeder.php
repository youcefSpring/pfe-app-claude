<?php

namespace Database\Seeders;

use App\Models\Publication;
use App\Models\User;
use Illuminate\Database\Seeder;

class PublicationSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        Publication::create([
            'title' => 'Digital Transformation in Higher Education',
            'slug' => 'digital-transformation-higher-education',
            'abstract' => 'This paper explores the impact of digital transformation on higher education institutions.',
            'content' => 'Full content of the publication...',
            'publication_date' => now()->subMonths(6),
            'journal' => 'International Journal of Educational Technology',
            'doi' => '10.1000/xyz123',
            'status' => 'published',
            'user_id' => $user->id,
        ]);
    }
}