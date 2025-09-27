<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $teachers = User::where('role', 'teacher')->get();

        $courses = [
            [
                'title' => 'Advanced Web Development',
                'slug' => 'advanced-web-development',
                'description' => 'Comprehensive course covering modern web development technologies including Laravel, React, and cloud deployment.',
                'content' => 'This course covers advanced web development concepts...',
                'level' => 'master',
                'duration' => 60,
                'credits' => 6,
                'is_active' => true,
                'user_id' => $teachers->first()->id,
            ],
            [
                'title' => 'Machine Learning Fundamentals',
                'slug' => 'machine-learning-fundamentals',
                'description' => 'Introduction to machine learning algorithms and applications in real-world projects.',
                'content' => 'Machine learning fundamentals course content...',
                'level' => 'master',
                'duration' => 45,
                'credits' => 4,
                'is_active' => true,
                'user_id' => $teachers->skip(1)->first()->id,
            ],
        ];

        foreach ($courses as $course) {
            Course::create($course);
        }
    }
}