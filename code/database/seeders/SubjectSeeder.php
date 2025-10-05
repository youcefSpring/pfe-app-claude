<?php

namespace Database\Seeders;

use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        // Get teachers by department
        $csTeachers = User::where('role', 'teacher')->where('department', 'Computer Science')->get();


        $subjects = [
            [
                'title' => 'AI-Powered E-commerce Recommendation System',
                'description' => 'Develop a machine learning system that provides personalized product recommendations for e-commerce platforms.',
                'keywords' => 'machine learning, recommendation systems, e-commerce, data analysis, Python, TensorFlow',
                'tools' => 'Python, TensorFlow/PyTorch, web frameworks, databases, APIs',
                'plan' => 'Phase 1: Data collection and analysis. Phase 2: Model development. Phase 3: Web application creation.',
                'status' => 'validated',
                'teacher_id' => $csTeachers->random()->id,
            ]
        ];

        foreach ($subjects as $subject) {
            Subject::create($subject);
        }

        $this->command->info('Created 9 subjects across all departments');
    }
}
