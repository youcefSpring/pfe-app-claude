<?php

namespace Database\Seeders;

use App\Models\Defense;
use App\Models\PfeProject;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;

class DefenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = PfeProject::where('status', 'in_progress')->get();
        $rooms = Room::where('is_available', true)->get();
        $teachers = User::where('role', 'teacher')->get();
        $chefMasters = User::where('role', 'chef_master')->get();

        // Create some scheduled defenses for projects that are advanced enough
        $advancedProjects = $projects->where('progress_percentage', '>', 30);

        foreach ($advancedProjects->take(3) as $index => $project) {
            $defenseDate = now()->addDays(rand(7, 45));
            $startTime = ['09:00:00', '10:30:00', '14:00:00', '15:30:00'][rand(0, 3)];

            // Select jury members
            $supervisor = $project->supervisor;
            $availableTeachers = $teachers->where('id', '!=', $supervisor->id);
            $president = $chefMasters->random();
            $examiner = $availableTeachers->random();

            Defense::create([
                'project_id' => $project->id,
                'room_id' => $rooms->random()->id,
                'defense_date' => $defenseDate,
                'start_time' => $startTime,
                'duration' => 60, // 60 minutes
                'status' => 'scheduled',
                'jury_president_id' => $president->id,
                'jury_examiner_id' => $examiner->id,
                'jury_supervisor_id' => $supervisor->id,
                'defense_type' => 'final',
                'instructions' => 'Présentation de 20 minutes suivie de 40 minutes de questions et délibération.',
                'scheduled_at' => now()->subDays(rand(1, 10)),
            ]);
        }

        // Create a completed defense with grades
        $completedProject = PfeProject::where('status', 'completed')->first();
        if ($completedProject) {
            $president = $chefMasters->random();
            $examiner = $teachers->where('id', '!=', $completedProject->supervisor_id)->random();

            Defense::create([
                'project_id' => $completedProject->id,
                'room_id' => $rooms->where('name', 'Amphithéâtre A')->first()->id,
                'defense_date' => now()->subMonths(2),
                'start_time' => '10:00:00',
                'duration' => 60,
                'status' => 'completed',
                'jury_president_id' => $president->id,
                'jury_examiner_id' => $examiner->id,
                'jury_supervisor_id' => $completedProject->supervisor_id,
                'defense_type' => 'final',
                'final_grade' => 16.5,
                'grade_president' => 17.0,
                'grade_examiner' => 16.0,
                'grade_supervisor' => 17.0,
                'observations' => 'Excellent travail technique avec une présentation claire et professionnelle. Le projet répond parfaitement aux objectifs fixés. Quelques améliorations mineures suggérées pour la documentation.',
                'deliberation_notes' => 'Jury unanime sur la qualité du travail. Félicitations pour l\'innovation et la rigueur technique.',
                'defense_minutes' => 'Soutenance excellente avec démonstration convaincante du système développé.',
                'scheduled_at' => now()->subMonths(3),
                'completed_at' => now()->subMonths(2),
            ]);
        }

        // Create a defense that needs rescheduling
        if ($projects->count() > 3) {
            $projectToReschedule = $projects->skip(3)->first();
            $president = $chefMasters->random();
            $examiner = $teachers->where('id', '!=', $projectToReschedule->supervisor_id)->random();

            Defense::create([
                'project_id' => $projectToReschedule->id,
                'room_id' => $rooms->random()->id,
                'defense_date' => now()->addDays(5),
                'start_time' => '14:00:00',
                'duration' => 60,
                'status' => 'needs_rescheduling',
                'jury_president_id' => $president->id,
                'jury_examiner_id' => $examiner->id,
                'jury_supervisor_id' => $projectToReschedule->supervisor_id,
                'defense_type' => 'final',
                'rescheduling_reason' => 'Conflit d\'horaire avec le président du jury',
                'scheduled_at' => now()->subDays(3),
            ]);
        }
    }
}