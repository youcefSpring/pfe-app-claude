<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\Subject;
use App\Models\AcademicYear;
use App\Models\AllocationDeadline;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TeamLeadershipTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating test data for team leadership and subject selection...');

        // 1. Create test students with different academic levels
        $this->createTestStudents();

        // 2. Create test teachers
        $this->createTestTeachers();

        // 3. Create validated subjects for selection
        $this->createValidatedSubjects();

        // 4. Create teams with different scenarios
        $this->createTestTeams();

        // 5. Set up allocation deadline for testing
        $this->setupAllocationDeadline();

        $this->command->info('✅ Test data created successfully!');
        $this->command->info('');
        $this->command->info('🔐 Test Login Credentials:');
        $this->command->info('Team Leader (Licence): leader@test.com / password');
        $this->command->info('Team Leader (Master): master@test.com / password');
        $this->command->info('Single Student: single@test.com / password');
        $this->command->info('Team Member: member@test.com / password');
        $this->command->info('');
        $this->command->info('📋 Test Scenarios Available:');
        $this->command->info('1. Team leader can select subjects (TestTeam-Leader)');
        $this->command->info('2. Single student can request subjects individually');
        $this->command->info('3. Master team with proper size limits (TestTeam-Master)');
        $this->command->info('4. Multiple validated subjects to choose from');
    }

    private function createTestStudents(): void
    {
        $students = [
            [
                'name' => 'Ahmed Test Leader',
                'email' => 'leader@test.com',
                'password' => Hash::make('password'),
                'role' => 'student',
                'matricule' => 'TL001',
                'department' => 'Computer Science',
                'speciality' => 'Software Engineering',
                'numero_inscription' => '2024TL001',
                'student_level' => 'licence_3',
                'annee_bac' => 2021,
                'date_naissance' => '2000-05-15',
                'lieu_naissance' => 'Algiers',
                'section' => 'A',
                'groupe' => '1',
                'profile_completed' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Fatima Test Master',
                'email' => 'master@test.com',
                'password' => Hash::make('password'),
                'role' => 'student',
                'matricule' => 'TM001',
                'department' => 'Computer Science',
                'speciality' => 'Software Engineering',
                'numero_inscription' => '2024TM001',
                'student_level' => 'master_1',
                'annee_bac' => 2020,
                'date_naissance' => '1999-03-20',
                'lieu_naissance' => 'Oran',
                'section' => 'M',
                'groupe' => '1',
                'profile_completed' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Omar Single Student',
                'email' => 'single@test.com',
                'password' => Hash::make('password'),
                'role' => 'student',
                'matricule' => 'TS001',
                'department' => 'Computer Science',
                'speciality' => 'Software Engineering',
                'numero_inscription' => '2024TS001',
                'student_level' => 'licence_3',
                'annee_bac' => 2021,
                'date_naissance' => '2000-08-10',
                'lieu_naissance' => 'Constantine',
                'section' => 'A',
                'groupe' => '2',
                'profile_completed' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Amina Team Member',
                'email' => 'member@test.com',
                'password' => Hash::make('password'),
                'role' => 'student',
                'matricule' => 'TM002',
                'department' => 'Computer Science',
                'speciality' => 'Software Engineering',
                'numero_inscription' => '2024TM002',
                'student_level' => 'licence_3',
                'annee_bac' => 2021,
                'date_naissance' => '2000-11-25',
                'lieu_naissance' => 'Annaba',
                'section' => 'A',
                'groupe' => '1',
                'profile_completed' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Youssef Team Member 2',
                'email' => 'member2@test.com',
                'password' => Hash::make('password'),
                'role' => 'student',
                'matricule' => 'TM003',
                'department' => 'Computer Science',
                'speciality' => 'Software Engineering',
                'numero_inscription' => '2024TM003',
                'student_level' => 'master_1',
                'annee_bac' => 2020,
                'date_naissance' => '1999-07-12',
                'lieu_naissance' => 'Setif',
                'section' => 'M',
                'groupe' => '1',
                'profile_completed' => true,
                'email_verified_at' => now(),
            ],
        ];

        foreach ($students as $studentData) {
            User::updateOrCreate(
                ['email' => $studentData['email']],
                $studentData
            );
        }

        $this->command->info('✓ Created test students');
    }

    private function createTestTeachers(): void
    {
        $teachers = [
            [
                'name' => 'Dr. Samir Benali',
                'email' => 'teacher1@test.com',
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'matricule' => 'TCH-001',
                'department' => 'Computer Science',
                'speciality' => 'Artificial Intelligence',
                'grade' => 'professor',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Dr. Leila Cherif',
                'email' => 'teacher2@test.com',
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'matricule' => 'TCH-002',
                'department' => 'Computer Science',
                'speciality' => 'Software Engineering',
                'grade' => 'phd',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Dr. Karim Mokrani',
                'email' => 'teacher3@test.com',
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'matricule' => 'TCH-003',
                'department' => 'Computer Science',
                'speciality' => 'Data Science',
                'grade' => 'master',
                'email_verified_at' => now(),
            ],
        ];

        foreach ($teachers as $teacherData) {
            User::updateOrCreate(
                ['email' => $teacherData['email']],
                $teacherData
            );
        }

        $this->command->info('✓ Created test teachers');
    }

    private function createValidatedSubjects(): void
    {
        $teachers = User::where('role', 'teacher')->get();

        $subjects = [
            [
                'title' => 'AI-Powered Student Management System',
                'description' => 'Develop an intelligent system to manage student records, grades, and academic progress using machine learning algorithms for predictive analytics and automated insights.',
                'keywords' => 'artificial intelligence, machine learning, student management, web development, data analytics',
                'tools' => 'Python, Django/Flask, TensorFlow, PostgreSQL, React, Docker',
                'plan' => 'Phase 1: Requirements analysis and system design (2 weeks). Phase 2: Database design and backend development (4 weeks). Phase 3: ML model development for predictive features (3 weeks). Phase 4: Frontend development and integration (3 weeks). Phase 5: Testing and deployment (2 weeks).',
                'status' => 'validated',
                'teacher_id' => $teachers->first()->id,
                'target_grade' => 'license',
                'academic_year' => AcademicYear::getCurrentYearString(),
            ],
            [
                'title' => 'Blockchain-Based Document Verification System',
                'description' => 'Create a secure document verification system using blockchain technology to ensure authenticity and prevent forgery of academic certificates and official documents.',
                'keywords' => 'blockchain, document verification, security, cryptography, smart contracts',
                'tools' => 'Solidity, Web3.js, Node.js, React, IPFS, Ganache',
                'plan' => 'Phase 1: Blockchain fundamentals and smart contract design (3 weeks). Phase 2: Smart contract development and testing (4 weeks). Phase 3: Web interface development (3 weeks). Phase 4: IPFS integration for document storage (2 weeks). Phase 5: Security testing and deployment (2 weeks).',
                'status' => 'validated',
                'teacher_id' => $teachers->skip(1)->first()->id,
                'target_grade' => 'master',
                'academic_year' => AcademicYear::getCurrentYearString(),
            ],
            [
                'title' => 'IoT-Based Smart Campus System',
                'description' => 'Design and implement an Internet of Things solution for smart campus management including energy monitoring, security systems, and environmental controls.',
                'keywords' => 'IoT, smart campus, sensors, energy management, automation, real-time monitoring',
                'tools' => 'Arduino, Raspberry Pi, MQTT, InfluxDB, Grafana, React, Node.js',
                'plan' => 'Phase 1: IoT system architecture and sensor selection (2 weeks). Phase 2: Hardware setup and sensor deployment (3 weeks). Phase 3: Data collection and communication protocols (3 weeks). Phase 4: Dashboard development for monitoring (3 weeks). Phase 5: Testing and optimization (3 weeks).',
                'status' => 'validated',
                'teacher_id' => $teachers->skip(2)->first()->id,
                'target_grade' => 'license',
                'academic_year' => AcademicYear::getCurrentYearString(),
            ],
            [
                'title' => 'Mobile Health Monitoring Application',
                'description' => 'Develop a comprehensive mobile application for personal health monitoring with features for tracking vital signs, medication reminders, and doctor consultations.',
                'keywords' => 'mobile development, health monitoring, healthcare, Android, iOS, API integration',
                'tools' => 'React Native, Firebase, Node.js, Express, MongoDB, Chart.js',
                'plan' => 'Phase 1: Market research and feature specification (2 weeks). Phase 2: UI/UX design and prototyping (2 weeks). Phase 3: Backend API development (4 weeks). Phase 4: Mobile app development (4 weeks). Phase 5: Testing and app store deployment (2 weeks).',
                'status' => 'validated',
                'teacher_id' => $teachers->first()->id,
                'target_grade' => 'license',
                'academic_year' => AcademicYear::getCurrentYearString(),
            ],
            [
                'title' => 'E-Learning Platform with Adaptive Learning',
                'description' => 'Build an intelligent e-learning platform that adapts to student learning patterns and provides personalized content recommendations and learning paths.',
                'keywords' => 'e-learning, adaptive learning, education technology, personalization, analytics',
                'tools' => 'Vue.js, Laravel, MySQL, Python, Scikit-learn, Redis, Docker',
                'plan' => 'Phase 1: Learning analytics research and algorithm design (3 weeks). Phase 2: Backend development with Laravel (4 weeks). Phase 3: Frontend development with Vue.js (3 weeks). Phase 4: ML algorithm integration (3 weeks). Phase 5: User testing and refinement (1 week).',
                'status' => 'validated',
                'teacher_id' => $teachers->skip(1)->first()->id,
                'target_grade' => 'master',
                'academic_year' => AcademicYear::getCurrentYearString(),
            ],
        ];

        foreach ($subjects as $subjectData) {
            Subject::updateOrCreate(
                [
                    'title' => $subjectData['title'],
                    'teacher_id' => $subjectData['teacher_id']
                ],
                $subjectData
            );
        }

        $this->command->info('✓ Created validated subjects for selection');
    }

    private function createTestTeams(): void
    {
        // Get test students
        $leader = User::where('email', 'leader@test.com')->first();
        $masterLeader = User::where('email', 'master@test.com')->first();
        $member = User::where('email', 'member@test.com')->first();
        $member2 = User::where('email', 'member2@test.com')->first();

        // Create team with licence leader (can have 1-4 members)
        $licenceTeam = Team::updateOrCreate(
            ['name' => 'TestTeam-Leader'],
            [
                'name' => 'TestTeam-Leader',
                'status' => 'complete',
                'academic_year' => AcademicYear::getCurrentYearString(),
            ]
        );

        // Add leader and one member
        TeamMember::updateOrCreate(
            ['team_id' => $licenceTeam->id, 'student_id' => $leader->id],
            [
                'team_id' => $licenceTeam->id,
                'student_id' => $leader->id,
                'role' => 'leader',
                'joined_at' => now(),
            ]
        );

        TeamMember::updateOrCreate(
            ['team_id' => $licenceTeam->id, 'student_id' => $member->id],
            [
                'team_id' => $licenceTeam->id,
                'student_id' => $member->id,
                'role' => 'member',
                'joined_at' => now(),
            ]
        );

        // Create team with master leader (can have 1-4 members)
        $masterTeam = Team::updateOrCreate(
            ['name' => 'TestTeam-Master'],
            [
                'name' => 'TestTeam-Master',
                'status' => 'complete',
                'academic_year' => AcademicYear::getCurrentYearString(),
            ]
        );

        // Add master leader and one member
        TeamMember::updateOrCreate(
            ['team_id' => $masterTeam->id, 'student_id' => $masterLeader->id],
            [
                'team_id' => $masterTeam->id,
                'student_id' => $masterLeader->id,
                'role' => 'leader',
                'joined_at' => now(),
            ]
        );

        TeamMember::updateOrCreate(
            ['team_id' => $masterTeam->id, 'student_id' => $member2->id],
            [
                'team_id' => $masterTeam->id,
                'student_id' => $member2->id,
                'role' => 'member',
                'joined_at' => now(),
            ]
        );

        $this->command->info('✓ Created test teams with leaders');
    }

    private function setupAllocationDeadline(): void
    {
        // Ensure there's an active allocation deadline for testing
        AllocationDeadline::updateOrCreate(
            ['academic_year' => AcademicYear::getCurrentYearString()],
            [
                'name' => 'Test Subject Selection Period',
                'academic_year' => AcademicYear::getCurrentYearString(),
                'level' => 'L3',
                'preferences_start' => now()->subDays(7),
                'preferences_deadline' => now()->addDays(30),
                'grades_verification_deadline' => now()->addDays(35),
                'allocation_date' => now()->addDays(40),
                'status' => 'active',
                'description' => 'Test period for subject selection and team formation',
                'created_by' => User::where('role', 'admin')->first()?->id ?? 1,
            ]
        );

        $this->command->info('✓ Set up allocation deadline for testing');
    }
}