<?php

namespace Database\Seeders;

use App\Models\Deliverable;
use App\Models\PfeProject;
use App\Models\User;
use Illuminate\Database\Seeder;

class DeliverableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = PfeProject::where('status', 'in_progress')->get();

        foreach ($projects as $project) {
            $teamMembers = $project->team->members;
            $submitter = $teamMembers->random();

            // Create project proposal deliverable
            Deliverable::create([
                'project_id' => $project->id,
                'submitted_by' => $submitter->id,
                'title' => 'Proposition de Projet PFE',
                'description' => 'Document de proposition initiale du projet incluant les objectifs, la méthodologie et le planning prévisionnel.',
                'file_path' => 'deliverables/2024/' . $project->id . '/proposal.pdf',
                'file_size' => rand(500000, 2000000), // 500KB to 2MB
                'file_type' => 'pdf',
                'status' => 'approved',
                'submitted_at' => $project->start_date->addDays(3),
                'reviewed_by' => $project->supervisor_id,
                'reviewed_at' => $project->start_date->addDays(5),
                'review_comments' => 'Proposition bien structurée avec des objectifs clairs. Approuvé pour démarrer le développement.',
                'is_final_report' => false,
            ]);

            // Create progress report
            if ($project->progress_percentage > 25) {
                Deliverable::create([
                    'project_id' => $project->id,
                    'submitted_by' => $submitter->id,
                    'title' => 'Rapport d\'Avancement - Milestone 1',
                    'description' => 'Premier rapport d\'avancement détaillant les réalisations, les difficultés rencontrées et le planning révisé.',
                        'file_path' => 'deliverables/2024/' . $project->id . '/progress_1.pdf',
                    'file_size' => rand(800000, 1500000),
                    'file_type' => 'pdf',
                    'status' => 'approved',
                    'submitted_at' => $project->start_date->addDays(21),
                    'reviewed_by' => $project->supervisor_id,
                    'reviewed_at' => $project->start_date->addDays(23),
                    'review_comments' => 'Bon progrès réalisé. Continuez sur cette lancée.',
                    'is_final_report' => false,
                ]);
            }

            // Create technical documentation
            if ($project->progress_percentage > 35) {
                Deliverable::create([
                    'project_id' => $project->id,
                    'submitted_by' => $teamMembers->random()->id,
                    'title' => 'Documentation Technique',
                    'description' => 'Documentation technique complète incluant l\'architecture système, les diagrammes UML et le guide d\'installation.',
                        'file_path' => 'deliverables/2024/' . $project->id . '/technical_doc.pdf',
                    'file_size' => rand(1200000, 3000000),
                    'file_type' => 'pdf',
                    'status' => 'pending',
                    'submitted_at' => now()->subDays(3),
                    'is_final_report' => false,
                ]);
            }

            // Create source code package
            if ($project->progress_percentage > 40) {
                Deliverable::create([
                    'project_id' => $project->id,
                    'submitted_by' => $teamMembers->random()->id,
                    'title' => 'Code Source du Projet',
                    'description' => 'Archive complète du code source avec documentation technique et scripts de déploiement.',
                        'file_path' => 'deliverables/2024/' . $project->id . '/source_code.zip',
                    'file_size' => rand(5000000, 50000000), // 5MB to 50MB
                    'file_type' => 'zip',
                    'status' => 'approved',
                    'submitted_at' => now()->subDays(7),
                    'reviewed_by' => $project->supervisor_id,
                    'reviewed_at' => now()->subDays(5),
                    'review_comments' => 'Code bien structuré et documenté. Respecte les bonnes pratiques de développement.',
                    'is_final_report' => false,
                ]);
            }
        }

        // Create final report for completed project
        $completedProject = PfeProject::where('status', 'completed')->first();
        if ($completedProject) {
            $teamLeader = $completedProject->team->members->where('role', 'leader')->first();

            Deliverable::create([
                'project_id' => $completedProject->id,
                'submitted_by' => $teamLeader->user_id,
                'title' => 'Rapport Final de PFE',
                'description' => 'Rapport final complet du projet de fin d\'études incluant tous les aspects techniques, méthodologiques et les résultats obtenus.',
                'file_path' => 'deliverables/2024/' . $completedProject->id . '/final_report.pdf',
                'file_size' => rand(3000000, 8000000), // 3MB to 8MB
                'file_type' => 'pdf',
                'status' => 'approved',
                'submitted_at' => $completedProject->actual_end_date->subDays(7),
                'reviewed_by' => $completedProject->supervisor_id,
                'reviewed_at' => $completedProject->actual_end_date->subDays(3),
                'review_comments' => 'Excellent rapport final. Travail de qualité professionnelle avec une analyse approfondie des résultats.',
                'is_final_report' => true,
            ]);

            // Create presentation slides
            Deliverable::create([
                'project_id' => $completedProject->id,
                'submitted_by' => $teamLeader->user_id,
                'title' => 'Présentation de Soutenance',
                'description' => 'Support de présentation PowerPoint utilisé lors de la soutenance finale.',
                'file_path' => 'deliverables/2024/' . $completedProject->id . '/presentation.pptx',
                'file_size' => rand(2000000, 15000000), // 2MB to 15MB
                'file_type' => 'pptx',
                'status' => 'approved',
                'submitted_at' => $completedProject->actual_end_date->subDays(3),
                'reviewed_by' => $completedProject->supervisor_id,
                'reviewed_at' => $completedProject->actual_end_date->subDays(1),
                'review_comments' => 'Présentation claire et professionnelle.',
                'is_final_report' => false,
            ]);
        }
    }
}