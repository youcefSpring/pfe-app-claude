<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Subject;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\TeamSubjectPreference;
use App\Models\AllocationDeadline;
use App\Models\StudentMark;
use App\Models\Speciality;
use App\Models\AcademicYear;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AllocationTestDataSeeder extends Seeder
{
    private $currentYear = '2024-2025';
    private $teachers = [];
    private $students = [];
    private $subjects = [];
    private $teams = [];

    // Algerian first names
    private $maleFirstNames = [
        'Mohamed', 'Ahmed', 'Youcef', 'Karim', 'Amine', 'Bilal', 'Sofiane', 'Farid',
        'Hamza', 'Raouf', 'Walid', 'Nadir', 'Salim', 'Tarek', 'Hichem', 'Fares',
        'Nabil', 'Adel', 'Redouane', 'Zakaria', 'Mehdi', 'Aymen', 'Abdallah', 'Omar',
        'Rachid', 'Samir', 'Ilyes', 'Anis', 'Mustapha', 'Imad'
    ];

    private $femaleFirstNames = [
        'Amina', 'Fatima', 'Samira', 'Leila', 'Nabila', 'Rania', 'Nesrine', 'Yasmine',
        'Meriem', 'Khadija', 'Souad', 'Nacera', 'Karima', 'Zohra', 'Hanane', 'Lamia',
        'Sabrina', 'Nadia', 'Malika', 'Sihem', 'Wafa', 'Imane', 'Lynda', 'Sarah',
        'Katia', 'Djamila', 'Dalila', 'Assia', 'Hafida', 'Houria'
    ];

    private $lastNames = [
        'Benali', 'Boudjadar', 'Cherif', 'Kaddour', 'Mammeri', 'Brahimi', 'Belhadj',
        'Chettab', 'Zenati', 'Mokrani', 'Hamdi', 'Touati', 'Bensaid', 'Meziane',
        'Amrani', 'Boukhari', 'Madani', 'Benahmed', 'Djellab', 'Ferhat', 'Ghozali',
        'Hassani', 'Idrissi', 'Kadri', 'Larbi', 'Moussa', 'Nadir', 'Ouahab',
        'Rahmani', 'Slimani', 'Taleb', 'Yahiaoui', 'Ziani', 'Bencheikh', 'Djelloul'
    ];

    private $subjectTitles = [
        'Intelligence Artificielle et Machine Learning',
        'Développement Web Full-Stack avec React et Laravel',
        'Sécurité Informatique et Cryptographie',
        'Architecture Microservices et Cloud Computing',
        'Traitement d\'Images et Vision par Ordinateur',
        'Big Data et Analyse de Données Massives',
        'Internet des Objets (IoT) et Systèmes Embarqués',
        'Blockchain et Applications Décentralisées',
        'Réseaux Neuronaux Profonds (Deep Learning)',
        'Application Mobile Cross-Platform avec Flutter',
        'Cybersécurité et Pentesting',
        'Data Science et Analyse Prédictive',
        'Réalité Virtuelle et Réalité Augmentée',
        'DevOps et Intégration Continue (CI/CD)',
        'Systèmes Distribués et Parallélisme',
        'Compilation et Optimisation de Code',
        'Intelligence Artificielle pour le Traitement du Langage Naturel',
        'Développement de Jeux Vidéo avec Unity',
        'Cloud Native Applications avec Kubernetes',
        'Biométrie et Reconnaissance Faciale',
        'Système de Recommandation Intelligent',
        'Informatique Quantique et Algorithmes',
        'Robotique et Automatisation',
        'Edge Computing et Fog Computing',
        'Gestion Intelligente de l\'Énergie avec IoT',
        'Plateforme E-learning Intelligente',
        'Application de Santé Mobile (M-Health)',
        'Système de Surveillance Intelligente',
        'Application de Commerce Électronique',
        'Gestion de Projet Agile avec Scrum',
        'Analyse de Sentiment sur les Réseaux Sociaux',
        'Système de Gestion de Bibliothèque Numérique',
        'Application de Transport Intelligent',
        'Chatbot Intelligent avec NLP',
        'Système de Détection de Fraude',
        'Application de Gestion Hospitalière',
        'Plateforme de Crowdfunding',
        'Système de Gestion de Stock Intelligent',
        'Application de Réservation en Ligne',
        'Réseau Social Éducatif'
    ];

    public function run(): void
    {
        $this->command->info('🚀 Starting Allocation Test Data Seeder...');

        DB::beginTransaction();
        try {
            // Create academic year
            $this->createAcademicYear();

            // Create specialities
            $this->createSpecialities();

            // Create 60 teachers
            $this->command->info('👨‍🏫 Creating 60 teachers...');
            $this->createTeachers(60);

            // Create 100 students
            $this->command->info('👨‍🎓 Creating 100 students...');
            $this->createStudents(100);

            // Create 40 subjects
            $this->command->info('📚 Creating 40 subjects...');
            $this->createSubjects(40);

            // Create ~25 teams (4 students per team on average)
            $this->command->info('👥 Creating teams...');
            $this->createTeams();

            // Create allocation deadline
            $this->command->info('📅 Creating allocation deadline...');
            $deadline = $this->createAllocationDeadline();

            // Create team preferences (each team chooses 5-10 subjects)
            $this->command->info('⭐ Creating team preferences...');
            $this->createTeamPreferences();

            DB::commit();

            $this->command->info('✅ Allocation test data created successfully!');
            $this->command->info('📊 Summary:');
            $this->command->info("   - Teachers: " . count($this->teachers));
            $this->command->info("   - Students: " . count($this->students));
            $this->command->info("   - Subjects: " . count($this->subjects));
            $this->command->info("   - Teams: " . count($this->teams));
            $this->command->info("   - Allocation Deadline: {$deadline->name}");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Error: ' . $e->getMessage());
            $this->command->error($e->getTraceAsString());
        }
    }

    private function createAcademicYear()
    {
        if (!AcademicYear::where('year', $this->currentYear)->exists()) {
            AcademicYear::create([
                'year' => $this->currentYear,
                'start_date' => Carbon::parse('2024-09-15'),
                'end_date' => Carbon::parse('2025-06-30'),
                'is_current' => true,
            ]);
            $this->command->info("   ✓ Academic year {$this->currentYear} created");
        }
    }

    private function createSpecialities()
    {
        $specialities = [
            ['name' => 'Génie Logiciel', 'code' => 'GL', 'level' => 'master', 'academic_year' => $this->currentYear],
            ['name' => 'Intelligence Artificielle', 'code' => 'IA', 'level' => 'master', 'academic_year' => $this->currentYear],
            ['name' => 'Réseaux et Sécurité', 'code' => 'RS', 'level' => 'master', 'academic_year' => $this->currentYear],
            ['name' => 'Science des Données', 'code' => 'DS', 'level' => 'master', 'academic_year' => $this->currentYear],
            ['name' => 'Informatique', 'code' => 'INF', 'level' => 'licence', 'academic_year' => $this->currentYear],
        ];

        foreach ($specialities as $spec) {
            if (!Speciality::where('code', $spec['code'])->where('academic_year', $this->currentYear)->exists()) {
                Speciality::create($spec);
            }
        }
    }

    private function createTeachers(int $count)
    {
        $specialities = ['Intelligence Artificielle', 'Génie Logiciel', 'Réseaux et Sécurité',
                        'Base de Données', 'Cloud Computing', 'Cybersécurité', 'Data Science',
                        'Systèmes Embarqués', 'Interface Homme-Machine', 'Traitement d\'Images'];

        $grades = ['Professeur', 'MCA', 'MCB', 'MAA', 'MAB'];

        for ($i = 1; $i <= $count; $i++) {
            $gender = rand(0, 1) ? 'male' : 'female';
            $firstName = $gender === 'male'
                ? $this->maleFirstNames[array_rand($this->maleFirstNames)]
                : $this->femaleFirstNames[array_rand($this->femaleFirstNames)];
            $lastName = $this->lastNames[array_rand($this->lastNames)];

            $email = strtolower(str_replace(' ', '.', $firstName)) . '.' .
                     strtolower($lastName) . $i . '@univ.dz';

            if (User::where('email', $email)->exists()) {
                continue;
            }

            $teacher = User::create([
                'name' => "Dr. {$firstName} {$lastName}",
                'email' => $email,
                'password' => Hash::make('password123'),
                'role' => 'teacher',
                'speciality' => $specialities[array_rand($specialities)],
                'department' => 'Informatique',
                'grade' => $grades[array_rand($grades)],
            ]);

            $this->teachers[] = $teacher;
        }
    }

    private function createStudents(int $count)
    {
        $levels = ['master_1', 'master_2', 'licence_3'];
        $specialities = Speciality::all();

        for ($i = 1; $i <= $count; $i++) {
            $gender = rand(0, 1) ? 'male' : 'female';
            $firstName = $gender === 'male'
                ? $this->maleFirstNames[array_rand($this->maleFirstNames)]
                : $this->femaleFirstNames[array_rand($this->femaleFirstNames)];
            $lastName = $this->lastNames[array_rand($this->lastNames)];

            $matricule = 'STU' . str_pad($i, 5, '0', STR_PAD_LEFT);
            $email = strtolower($firstName) . '.' . strtolower($lastName) . $i . '@student.univ.dz';

            if (User::where('email', $email)->exists()) {
                continue;
            }

            $level = $levels[array_rand($levels)];

            $student = User::create([
                'name' => "{$firstName} {$lastName}",
                'email' => $email,
                'password' => Hash::make('password123'),
                'role' => 'student',
                'matricule' => $matricule,
                'student_level' => $level,
                'speciality_id' => $specialities->random()->id,
            ]);

            // Create realistic marks for the student (60-95 range)
            $this->createStudentMarks($student);

            $this->students[] = $student;
        }
    }

    private function createStudentMarks(User $student)
    {
        // Create base average for consistency (some students are better than others)
        $baseAverage = rand(60, 95);

        $subjects = [
            'Algorithmique Avancée',
            'Base de Données',
            'Programmation Web',
            'Réseaux Informatiques',
            'Systèmes d\'Exploitation',
            'Génie Logiciel',
            'Architecture des Ordinateurs',
            'Mathématiques pour l\'Informatique'
        ];

        foreach ($subjects as $subjectName) {
            // Vary marks slightly around base average (±10 points)
            $variation = rand(-10, 10);
            $finalMark = max(0, min(100, $baseAverage + $variation));

            StudentMark::create([
                'user_id' => $student->id,
                'subject_name' => $subjectName,
                'mark' => $finalMark,
                'max_mark' => 100,
                'semester' => rand(1, 2),
                'academic_year' => $this->currentYear,
                'created_by' => 1, // System/Admin
            ]);
        }
    }

    private function createSubjects(int $count)
    {
        $usedTitles = [];

        for ($i = 0; $i < $count; $i++) {
            // Get unique title
            do {
                $title = $this->subjectTitles[array_rand($this->subjectTitles)];
            } while (in_array($title, $usedTitles) && count($usedTitles) < count($this->subjectTitles));

            $usedTitles[] = $title;

            $teacher = $this->teachers[array_rand($this->teachers)];
            $specialities = Speciality::where('level', 'master')->get();

            $subject = Subject::create([
                'title' => $title,
                'description' => "Description détaillée du projet: {$title}. Ce projet vise à développer une solution innovante et pratique dans le domaine.",
                'keywords' => 'Programming, Development, Innovation',
                'tools' => 'Laravel, React, MySQL',
                'plan' => 'Analyse, Conception, Développement, Tests',
                'teacher_id' => $teacher->id,
                'academic_year' => $this->currentYear,
                'status' => 'validated', // Already validated for allocation
                'is_external' => rand(0, 10) > 7, // 30% external
                'validated_at' => now()->subDays(rand(1, 30)),
            ]);

            // Attach to specialities
            $subject->specialities()->attach($specialities->random(rand(1, 2))->pluck('id'));

            $this->subjects[] = $subject;
        }
    }

    private function createTeams()
    {
        // Group students by level for realistic team formation
        $studentsByLevel = collect($this->students)->groupBy('student_level');

        foreach ($studentsByLevel as $level => $students) {
            $studentsList = $students->values()->all();
            $teamNumber = 1;

            // Create teams of 3-4 students
            $i = 0;
            while ($i < count($studentsList)) {
                $teamSize = min(rand(3, 4), count($studentsList) - $i);

                if ($teamSize < 3) {
                    break; // Stop if remaining students are less than minimum team size
                }

                $teamStudents = array_slice($studentsList, $i, $teamSize);

                // Determine academic level based on student_level
                $academicLevel = match($level) {
                    'master_1' => 'M1',
                    'master_2' => 'M2',
                    'licence_3' => 'L3',
                    default => 'M1'
                };

                $team = Team::create([
                    'name' => "Team {$academicLevel}-{$teamNumber}",
                    'status' => 'complete',
                    'academic_year' => $this->currentYear,
                    'level' => $academicLevel,
                ]);

                // Add team members
                foreach ($teamStudents as $index => $student) {
                    TeamMember::create([
                        'team_id' => $team->id,
                        'student_id' => $student->id,
                        'role' => $index === 0 ? 'leader' : 'member', // First student is leader
                        'joined_at' => now()->subDays(rand(10, 30)),
                    ]);
                }

                $this->teams[] = $team;
                $teamNumber++;
                $i += $teamSize;
            }
        }

        $this->command->info("   ✓ Created " . count($this->teams) . " teams");
    }

    private function createAllocationDeadline()
    {
        $deadline = AllocationDeadline::create([
            'name' => 'Allocation Période 1 - ' . $this->currentYear,
            'academic_year' => $this->currentYear,
            'level' => 'M1', // For Master 1 teams
            'preferences_start' => now()->subDays(15),
            'preferences_deadline' => now()->addDays(15),
            'grades_verification_deadline' => now()->addDays(10),
            'allocation_date' => now()->addDays(20),
            'status' => 'active',
            'description' => 'Première période d\'allocation pour l\'année académique ' . $this->currentYear,
            'created_by' => User::where('role', 'admin')->first()->id ?? 1,
            'auto_allocation_completed' => false,
            'second_round_needed' => false,
        ]);

        return $deadline;
    }

    private function createTeamPreferences()
    {
        $preferenceCount = 0;

        foreach ($this->teams as $team) {
            // Each team selects 5-10 subjects as preferences
            $numPreferences = rand(5, 10);
            $selectedSubjects = collect($this->subjects)
                ->random(min($numPreferences, count($this->subjects)))
                ->values();

            foreach ($selectedSubjects as $index => $subject) {
                // Add some time variation to selection dates for realistic conflict resolution
                $selectedAt = now()->subDays(rand(1, 14))->subHours(rand(0, 23));

                $leader = $team->members()->where('role', 'leader')->first();

                TeamSubjectPreference::create([
                    'team_id' => $team->id,
                    'subject_id' => $subject->id,
                    'preference_order' => $index + 1,
                    'selected_at' => $selectedAt,
                    'selected_by' => $leader ? $leader->student_id : $team->members()->first()->student_id,
                ]);

                $preferenceCount++;
            }
        }

        $this->command->info("   ✓ Created {$preferenceCount} team preferences");
    }
}
