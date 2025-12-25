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
                'status' => 'draft',
                'teacher_id' => $csTeachers->random()->id,
            ],
            [
                'title' => 'Système de Recommandation E-commerce Propulsé par l\'IA',
                'description' => 'évelopper un système d\'apprentissage automatique qui fournit des recommandations de produits personnalisées pour les plateformes de commerce électronique.',
                'keywords' => 'apprentissage automatique, systèmes de recommandation, commerce électronique, analyse de données, Python, TensorFlow',
                'tools' => 'Python, TensorFlow/PyTorch, frameworks web, bases de données, APIs',
                'plan' => 'Phase 1: Collecte et analyse des données. Phase 2: Développement du modèle. Phase 3: Création de l\'application web.',
                'status' => 'draft',
                'teacher_id' => $csTeachers->random()->id,
            ],[
                'title' => 'Système de Recommandation E-commerce Propulsé par l\'IA',
                'description' => 'évelopper un système d\'apprentissage automatique qui fournit des recommandations de produits personnalisées pour les plateformes de commerce électronique.',
                'keywords' => 'apprentissage automatique, systèmes de recommandation, commerce électronique, analyse de données, Python, TensorFlow',
                'tools' => 'Python, TensorFlow/PyTorch, frameworks web, bases de données, APIs',
                'plan' => 'Phase 1: Collecte et analyse des données. Phase 2: Développement du modèle. Phase 3: Création de l\'application web.',
                'status' => 'validated',
                'teacher_id' => $csTeachers->random()->id,
            ]
        ];

        foreach ($subjects as $subject) {
            Subject::create($subject);
        }

        $this->command->info('Created 2 subjects for Computer Science department');
    }
}
