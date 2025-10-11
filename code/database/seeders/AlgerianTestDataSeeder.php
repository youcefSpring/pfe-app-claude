<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Subject;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\Project;
use App\Models\Room;
use Illuminate\Support\Facades\Hash;

class AlgerianTestDataSeeder extends Seeder
{
    public function run()
    {
        // Check if test data already exists
        if (User::where('email', 'ahmed.benali@univ.dz')->exists()) {
            $this->command->info('✓ Algerian test data already exists. Skipping...');
            return;
        }
        // Create 10 Algerian Teachers
        $teachers = [
            [
                'name' => 'Dr. Ahmed Benali',
                'email' => 'ahmed.benali@univ.dz',
                'specialty' => 'Intelligence Artificielle',
                'department' => 'Informatique'
            ],
            [
                'name' => 'Dr. Fatima Boudjadar',
                'email' => 'fatima.boudjadar@univ.dz',
                'specialty' => 'Réseaux et Sécurité',
                'department' => 'Informatique'
            ],
            [
                'name' => 'Dr. Mohamed Cherif',
                'email' => 'mohamed.cherif@univ.dz',
                'specialty' => 'Génie Logiciel',
                'department' => 'Informatique'
            ],
            [
                'name' => 'Dr. Amina Kaddour',
                'email' => 'amina.kaddour@univ.dz',
                'specialty' => 'Base de Données',
                'department' => 'Informatique'
            ],
            [
                'name' => 'Dr. Youcef Mammeri',
                'email' => 'youcef.mammeri@univ.dz',
                'specialty' => 'Systèmes Embarqués',
                'department' => 'Électronique'
            ],
            [
                'name' => 'Dr. Samira Brahimi',
                'email' => 'samira.brahimi@univ.dz',
                'specialty' => 'Traitement d\'Images',
                'department' => 'Informatique'
            ],
            [
                'name' => 'Dr. Karim Belhadj',
                'email' => 'karim.belhadj@univ.dz',
                'specialty' => 'Mathématiques Appliquées',
                'department' => 'Mathématiques'
            ],
            [
                'name' => 'Dr. Nabila Chettab',
                'email' => 'nabila.chettab@univ.dz',
                'specialty' => 'Interface Homme-Machine',
                'department' => 'Informatique'
            ],
            [
                'name' => 'Dr. Omar Zenati',
                'email' => 'omar.zenati@univ.dz',
                'specialty' => 'Cloud Computing',
                'department' => 'Informatique'
            ],
            [
                'name' => 'Dr. Leila Mokrani',
                'email' => 'leila.mokrani@univ.dz',
                'specialty' => 'Data Science',
                'department' => 'Informatique'
            ]
        ];

        $createdTeachers = [];
        foreach ($teachers as $teacherData) {
            $teacher = User::create([
                'name' => $teacherData['name'],
                'email' => $teacherData['email'],
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'role' => 'teacher',
                'department' => $teacherData['department'],
                'speciality' => $teacherData['specialty'],
                'phone' => '+213' . rand(500000000, 799999999)
            ]);
            $createdTeachers[] = $teacher;
        }

        // Create 10 Algerian Students with different levels
        $students = [
            [
                'name' => 'Amine Boubekeur',
                'email' => 'amine.boubekeur@etudiant.univ.dz',
                'matricule' => 'L3INF001',
                'level' => 'L3',
                'specialty' => 'Informatique',
                'average' => 14.50
            ],
            [
                'name' => 'Yasmine Djamel',
                'email' => 'yasmine.djamel@etudiant.univ.dz',
                'matricule' => 'M1INF001',
                'level' => 'M1',
                'specialty' => 'Intelligence Artificielle',
                'average' => 16.25
            ],
            [
                'name' => 'Bilal Hamidi',
                'email' => 'bilal.hamidi@etudiant.univ.dz',
                'matricule' => 'M2INF001',
                'level' => 'M2',
                'specialty' => 'Génie Logiciel',
                'average' => 15.75
            ],
            [
                'name' => 'Meriem Saidani',
                'email' => 'meriem.saidani@etudiant.univ.dz',
                'matricule' => 'L3INF002',
                'level' => 'L3',
                'specialty' => 'Informatique',
                'average' => 13.80
            ],
            [
                'name' => 'Riad Boumediene',
                'email' => 'riad.boumediene@etudiant.univ.dz',
                'matricule' => 'M1ELN001',
                'level' => 'M1',
                'specialty' => 'Électronique',
                'average' => 15.20
            ],
            [
                'name' => 'Hanane Berkane',
                'email' => 'hanane.berkane@etudiant.univ.dz',
                'matricule' => 'M2INF002',
                'level' => 'M2',
                'specialty' => 'Réseaux et Sécurité',
                'average' => 17.10
            ],
            [
                'name' => 'Sofiane Tlemcani',
                'email' => 'sofiane.tlemcani@etudiant.univ.dz',
                'matricule' => 'L3MAT001',
                'level' => 'L3',
                'specialty' => 'Mathématiques Appliquées',
                'average' => 14.95
            ],
            [
                'name' => 'Aicha Benzerga',
                'email' => 'aicha.benzerga@etudiant.univ.dz',
                'matricule' => 'M1INF002',
                'level' => 'M1',
                'specialty' => 'Data Science',
                'average' => 16.80
            ],
            [
                'name' => 'Farid Ouali',
                'email' => 'farid.ouali@etudiant.univ.dz',
                'matricule' => 'M2ELN001',
                'level' => 'M2',
                'specialty' => 'Systèmes Embarqués',
                'average' => 15.45
            ],
            [
                'name' => 'Nadia Chaouche',
                'email' => 'nadia.chaouche@etudiant.univ.dz',
                'matricule' => 'L3INF003',
                'level' => 'L3',
                'specialty' => 'Informatique',
                'average' => 13.25
            ]
        ];

        $createdStudents = [];
        foreach ($students as $studentData) {
            $student = User::create([
                'name' => $studentData['name'],
                'email' => $studentData['email'],
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'matricule' => $studentData['matricule'],
                'role' => 'student',
                'department' => 'Informatique',
                'speciality' => $studentData['specialty'],
                'phone' => '+213' . rand(500000000, 799999999)
            ]);

            $createdStudents[] = $student;
        }

        // Create diverse subjects for different specialties
        $subjects = [
            [
                'title' => 'Système de Reconnaissance Faciale avec IA',
                'description' => 'Développement d\'un système de reconnaissance faciale utilisant les réseaux de neurones convolutifs',
                'type' => 'research',
                'tools' => 'Python, TensorFlow, OpenCV',
                'teacher_id' => $createdTeachers[0]->id, // Dr. Ahmed Benali (IA)
                'level' => 'M2',
                'academic_year' => '2024-2025'
            ],
            [
                'title' => 'Plateforme E-learning Sécurisée',
                'description' => 'Conception et développement d\'une plateforme d\'apprentissage en ligne avec authentification multi-facteurs',
                'type' => 'development',
                'tools' => 'Laravel, Vue.js, MySQL',
                'teacher_id' => $createdTeachers[1]->id, // Dr. Fatima Boudjadar (Réseaux)
                'level' => 'M1',
                'academic_year' => '2024-2025'
            ],
            [
                'title' => 'Application Mobile de Gestion Universitaire',
                'description' => 'Développement d\'une application mobile pour la gestion des notes et emplois du temps universitaires',
                'type' => 'development',
                'tools' => 'Flutter, Firebase',
                'teacher_id' => $createdTeachers[2]->id, // Dr. Mohamed Cherif (Génie Logiciel)
                'level' => 'L3',
                'academic_year' => '2024-2025'
            ],
            [
                'title' => 'Système de Gestion Hospitalière',
                'description' => 'Base de données distribuée pour la gestion des dossiers médicaux et rendez-vous',
                'type' => 'database',
                'tools' => 'PostgreSQL, Redis',
                'teacher_id' => $createdTeachers[3]->id, // Dr. Amina Kaddour (BDD)
                'level' => 'M2',
                'academic_year' => '2024-2025'
            ],
            [
                'title' => 'Système IoT pour Agriculture Intelligente',
                'description' => 'Conception d\'un système IoT pour surveiller et optimiser l\'irrigation agricole',
                'type' => 'hardware',
                'tools' => 'Arduino, MQTT, Node-RED',
                'teacher_id' => $createdTeachers[4]->id, // Dr. Youcef Mammeri (Embarqué)
                'level' => 'M1',
                'academic_year' => '2024-2025'
            ],
            [
                'title' => 'Détection de Fraude par Vision Artificielle',
                'description' => 'Système de détection automatique de fraude dans les examens utilisant la vision par ordinateur',
                'type' => 'research',
                'tools' => 'Python, Keras, OpenCV',
                'teacher_id' => $createdTeachers[5]->id, // Dr. Samira Brahimi (Traitement Images)
                'level' => 'M2',
                'academic_year' => '2024-2025'
            ],
            [
                'title' => 'Optimisation des Algorithmes Cryptographiques',
                'description' => 'Étude et optimisation des algorithmes de chiffrement pour les applications mobiles',
                'type' => 'research',
                'tools' => 'C++, OpenSSL',
                'teacher_id' => $createdTeachers[6]->id, // Dr. Karim Belhadj (Math)
                'level' => 'L3',
                'academic_year' => '2024-2025'
            ],
            [
                'title' => 'Interface Vocale pour Personnes Malvoyantes',
                'description' => 'Développement d\'une interface utilisateur vocale accessible pour applications web',
                'type' => 'accessibility',
                'tools' => 'JavaScript, Web Speech API',
                'teacher_id' => $createdTeachers[7]->id, // Dr. Nabila Chettab (IHM)
                'level' => 'M1',
                'academic_year' => '2024-2025'
            ],
            [
                'title' => 'Migration vers le Cloud AWS',
                'description' => 'Étude de cas : migration d\'une infrastructure on-premise vers Amazon Web Services',
                'type' => 'infrastructure',
                'tools' => 'AWS EC2, S3, RDS',
                'teacher_id' => $createdTeachers[8]->id, // Dr. Omar Zenati (Cloud)
                'level' => 'M2',
                'academic_year' => '2024-2025'
            ],
            [
                'title' => 'Analyse Prédictive des Ventes E-commerce',
                'description' => 'Modèle de machine learning pour prédire les tendances de vente en ligne',
                'type' => 'analytics',
                'tools' => 'Python, Scikit-learn, Pandas',
                'teacher_id' => $createdTeachers[9]->id, // Dr. Leila Mokrani (Data Science)
                'level' => 'M1',
                'academic_year' => '2024-2025'
            ]
        ];

        $createdSubjects = [];
        foreach ($subjects as $subjectData) {
            $subject = Subject::create([
                'title' => $subjectData['title'],
                'description' => $subjectData['description'],
                'keywords' => $subjectData['type'],
                'tools' => $subjectData['tools'],
                'plan' => 'Plan détaillé du projet sera défini avec les étudiants lors de la première réunion.',
                'teacher_id' => $subjectData['teacher_id'],
                'status' => 'validated',
                'is_external' => false,
                'validated_by' => 1, // Assuming admin user exists
                'validated_at' => now(),
                'academic_year' => $subjectData['academic_year']
            ]);
            $createdSubjects[] = $subject;
        }

        // Create teams and assign subjects to create projects for defense testing
        $teamNames = [
            'Équipe Alpha',
            'Équipe Beta',
            'Équipe Gamma',
            'Équipe Delta',
            'Équipe Epsilon'
        ];

        for ($i = 0; $i < 5; $i++) {
            // Create team with 2 students each
            $team = Team::create([
                'name' => $teamNames[$i],
                'academic_year' => '2024-2025',
                'status' => 'complete'
            ]);

            // Add team members
            TeamMember::create([
                'team_id' => $team->id,
                'student_id' => $createdStudents[$i * 2]->id,
                'role' => 'leader',
                'joined_at' => now()
            ]);

            TeamMember::create([
                'team_id' => $team->id,
                'student_id' => $createdStudents[$i * 2 + 1]->id,
                'role' => 'member',
                'joined_at' => now()
            ]);

            // Create project with assigned subject
            Project::create([
                'team_id' => $team->id,
                'subject_id' => $createdSubjects[$i * 2]->id,
                'supervisor_id' => $createdSubjects[$i * 2]->teacher_id,
                'type' => 'internal',
                'status' => 'in_progress',
                'academic_year' => '2024-2025',
                'started_at' => now()->subMonths(3),
                'created_at' => now()->subMonths(3)
            ]);
        }

        // Create some defense rooms
        $rooms = [
            ['name' => 'Amphithéâtre A', 'capacity' => 100, 'location' => 'Bâtiment Central, RDC'],
            ['name' => 'Salle de Conférence B', 'capacity' => 50, 'location' => 'Bâtiment Informatique, 1er étage'],
            ['name' => 'Laboratoire C', 'capacity' => 30, 'location' => 'Bâtiment Recherche, 2ème étage'],
            ['name' => 'Salle Polyvalente D', 'capacity' => 80, 'location' => 'Bâtiment Administration, RDC'],
            ['name' => 'Salle de Réunion E', 'capacity' => 25, 'location' => 'Bâtiment Direction, 3ème étage']
        ];

        foreach ($rooms as $roomData) {
            Room::create([
                'name' => $roomData['name'],
                'capacity' => $roomData['capacity'],
                'location' => $roomData['location'],
                'equipment' => 'Projecteur, Tableau blanc, Système audio, Wifi',
                'is_available' => true
            ]);
        }

        $this->command->info('✓ Created 10 Algerian teachers with different specialties');
        $this->command->info('✓ Created 10 Algerian students with different levels (L3, M1, M2)');
        $this->command->info('✓ Created 10 diverse subjects across different domains');
        $this->command->info('✓ Created 5 complete teams with projects ready for defense');
        $this->command->info('✓ Created 5 defense rooms with different capacities');
        $this->command->info('🎯 Test data is ready for defense scheduling and conflict testing!');
    }
}
