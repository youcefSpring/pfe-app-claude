<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin
        $superAdmin = User::create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'name' => 'Super Admin',
            'email' => 'superadmin@pfe.edu',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'status' => 'active',
            'is_active' => true,
            //'department' => 'administration',
            // //'bio' => 'System super administrator with full access to all platform features.',
            'email_verified_at' => now(),
        ]);
        // $superAdmin->assignRole('super_admin');

        // Create Admin PFE
        $adminPfe = User::create([
            'first_name' => 'Ahmed',
            'last_name' => 'Benali',
            'name' => 'Ahmed Benali',
            'email' => 'admin@pfe.edu',
            'password' => Hash::make('password'),
            'role' => 'admin_pfe',
            'status' => 'active',
            'is_active' => true,
            //'department' => 'administration',
            'phone' => '+213 555 123 456',
            //'bio' => 'PFE platform administrator responsible for system management and coordination.',
            'email_verified_at' => now(),
        ]);
        // $adminPfe->assignRole('admin_pfe');

        // Create Chef Master - Computer Science
        $chefMasterCS = User::create([
            'first_name' => 'Dr. Fatima',
            'last_name' => 'Zohra',
            'name' => 'Dr. Fatima Zohra',
            'email' => 'chef.master.cs@pfe.edu',
            'password' => Hash::make('password'),
            'role' => 'chef_master',
            'status' => 'active',
            'is_active' => true,
            //'department' => 'informatique',
            'phone' => '+213 555 234 567',
            //'bio' => 'Head of Computer Science department, responsible for validating CS projects.',
            'email_verified_at' => now(),
        ]);
        // $chefMasterCS->assignRole('chef_master');

        // Create Chef Master - Electronics
        $chefMasterElec = User::create([
            'first_name' => 'Dr. Mohamed',
            'last_name' => 'Khelifi',
            'name' => 'Dr. Mohamed Khelifi',
            'email' => 'chef.master.elec@pfe.edu',
            'password' => Hash::make('password'),
            'role' => 'chef_master',
            'status' => 'active',
            'is_active' => true,
            //'department' => 'electronique',
            'phone' => '+213 555 345 678',
            //'bio' => 'Head of Electronics department, specializing in embedded systems and IoT.',
            'email_verified_at' => now(),
        ]);
        // $chefMasterElec->assignRole('chef_master');

        // Create Teachers
        $this->createTeachers();

        // Create Students
        $this->createStudents();
    }

    private function createTeachers()
    {
        $teachers = [
            // Computer Science Teachers
            [
                'first_name' => 'Dr. Said',
                'last_name' => 'Mansouri',
                'name' => 'Dr. Said Mansouri',
                'email' => 'said.mansouri@pfe.edu',
                //'department' => 'informatique',
                //'bio' => 'Professor of Software Engineering and Web Development. Expert in Laravel, React, and modern web technologies.',
                'specialization' => 'Web Development, Software Engineering'
            ],
            [
                'first_name' => 'Dr. Amina',
                'last_name' => 'Hadji',
                'name' => 'Dr. Amina Hadji',
                'email' => 'amina.hadji@pfe.edu',
                //'department' => 'informatique',
                //'bio' => 'Associate Professor specializing in Artificial Intelligence and Machine Learning.',
                'specialization' => 'AI/ML, Data Science'
            ],
            [
                'first_name' => 'Pr. Karim',
                'last_name' => 'Bencherif',
                'name' => 'Pr. Karim Bencherif',
                'email' => 'karim.bencherif@pfe.edu',
                //'department' => 'informatique',
                //'bio' => 'Professor of Computer Networks and Cybersecurity.',
                'specialization' => 'Networks, Cybersecurity'
            ],
            [
                'first_name' => 'Dr. Leila',
                'last_name' => 'Bensalem',
                'name' => 'Dr. Leila Bensalem',
                'email' => 'leila.bensalem@pfe.edu',
                //'department' => 'informatique',
                //'bio' => 'Assistant Professor in Database Systems and Big Data.',
                'specialization' => 'Databases, Big Data'
            ],

            // Electronics Teachers
            [
                'first_name' => 'Dr. Omar',
                'last_name' => 'Chellali',
                'name' => 'Dr. Omar Chellali',
                'email' => 'omar.chellali@pfe.edu',
                //'department' => 'electronique',
                //'bio' => 'Professor of Digital Electronics and FPGA Design.',
                'specialization' => 'Digital Electronics, FPGA'
            ],
            [
                'first_name' => 'Dr. Nadia',
                'last_name' => 'Brahimi',
                'name' => 'Dr. Nadia Brahimi',
                'email' => 'nadia.brahimi@pfe.edu',
                //'department' => 'electronique',
                //'bio' => 'Associate Professor specializing in Embedded Systems and IoT.',
                'specialization' => 'Embedded Systems, IoT'
            ],

            // Mechanical Engineering Teachers
            [
                'first_name' => 'Dr. Youcef',
                'last_name' => 'Mimouni',
                'name' => 'Dr. Youcef Mimouni',
                'email' => 'youcef.mimouni@pfe.edu',
                //'department' => 'mecanique',
                //'bio' => 'Professor of Mechanical Design and CAD/CAM systems.',
                'specialization' => 'Mechanical Design, CAD/CAM'
            ],
            [
                'first_name' => 'Dr. Samira',
                'last_name' => 'Boudjemaa',
                'name' => 'Dr. Samira Boudjemaa',
                'email' => 'samira.boudjemaa@pfe.edu',
                //'department' => 'mecanique',
                //'bio' => 'Associate Professor in Renewable Energy and Sustainable Engineering.',
                'specialization' => 'Renewable Energy, Sustainability'
            ],
        ];

        foreach ($teachers as $teacherData) {
            $teacher = User::create([
                'first_name' => $teacherData['first_name'],
                'last_name' => $teacherData['last_name'],
                'name' => $teacherData['name'],
                'email' => $teacherData['email'],
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'status' => 'active',
                'is_active' => true,
                //'department' => $teacherData[//'department'],
                'phone' => '+213 555 ' . rand(100, 999) . ' ' . rand(100, 999),
                //'bio' => $teacherData[//'bio'],
                'email_verified_at' => now(),
            ]);
            // $teacher->assignRole('teacher');
        }
    }

    private function createStudents()
    {
        $students = [
            // Computer Science Students - Team 1
            [
                'first_name' => 'Yassine',
                'last_name' => 'Zerrouki',
                'email' => 'yassine.zerrouki@student.pfe.edu',
                'student_id' => 'CS2024001',
                //'department' => 'informatique',
            ],
            [
                'first_name' => 'Meriem',
                'last_name' => 'Belounis',
                'email' => 'meriem.belounis@student.pfe.edu',
                'student_id' => 'CS2024002',
                //'department' => 'informatique',
            ],

            // Computer Science Students - Team 2
            [
                'first_name' => 'Abderrahim',
                'last_name' => 'Lakhdari',
                'email' => 'abderrahim.lakhdari@student.pfe.edu',
                'student_id' => 'CS2024003',
                //'department' => 'informatique',
            ],
            [
                'first_name' => 'Nesrine',
                'last_name' => 'Boumediene',
                'email' => 'nesrine.boumediene@student.pfe.edu',
                'student_id' => 'CS2024004',
                //'department' => 'informatique',
            ],

            // Computer Science Students - Team 3
            [
                'first_name' => 'Khaled',
                'last_name' => 'Meziane',
                'email' => 'khaled.meziane@student.pfe.edu',
                'student_id' => 'CS2024005',
                //'department' => 'informatique',
            ],
            [
                'first_name' => 'Soumia',
                'last_name' => 'Ghazi',
                'email' => 'soumia.ghazi@student.pfe.edu',
                'student_id' => 'CS2024006',
                //'department' => 'informatique',
            ],
            [
                'first_name' => 'Bilal',
                'last_name' => 'Hamdani',
                'email' => 'bilal.hamdani@student.pfe.edu',
                'student_id' => 'CS2024007',
                //'department' => 'informatique',
            ],

            // Electronics Students
            [
                'first_name' => 'Amine',
                'last_name' => 'Benchikh',
                'email' => 'amine.benchikh@student.pfe.edu',
                'student_id' => 'EL2024001',
                //'department' => 'electronique',
            ],
            [
                'first_name' => 'Yasmine',
                'last_name' => 'Charef',
                'email' => 'yasmine.charef@student.pfe.edu',
                'student_id' => 'EL2024002',
                //'department' => 'electronique',
            ],
            [
                'first_name' => 'Reda',
                'last_name' => 'Boutebba',
                'email' => 'reda.boutebba@student.pfe.edu',
                'student_id' => 'EL2024003',
                //'department' => 'electronique',
            ],

            // Mechanical Engineering Students
            [
                'first_name' => 'Hamza',
                'last_name' => 'Guerriche',
                'email' => 'hamza.guerriche@student.pfe.edu',
                'student_id' => 'ME2024001',
                //'department' => 'mecanique',
            ],
            [
                'first_name' => 'Aicha',
                'last_name' => 'Mammeri',
                'email' => 'aicha.mammeri@student.pfe.edu',
                'student_id' => 'ME2024002',
                //'department' => 'mecanique',
            ],

            // Additional individual students
            [
                'first_name' => 'Rania',
                'last_name' => 'Zouaoui',
                'email' => 'rania.zouaoui@student.pfe.edu',
                'student_id' => 'CS2024008',
                //'department' => 'informatique',
            ],
            [
                'first_name' => 'Farouk',
                'last_name' => 'Benhabib',
                'email' => 'farouk.benhabib@student.pfe.edu',
                'student_id' => 'EL2024004',
                //'department' => 'electronique',
            ],
        ];

        foreach ($students as $studentData) {
            $student = User::create([
                'first_name' => $studentData['first_name'],
                'last_name' => $studentData['last_name'],
                'name' => $studentData['first_name'] . ' ' . $studentData['last_name'],
                'email' => $studentData['email'],
                'password' => Hash::make('password'),
                'role' => 'student',
                'status' => 'active',
                'is_active' => true,
                'student_id' => $studentData['student_id'],
                //'department' => $studentData[//'department'],
                'phone' => '+213 555 ' . rand(100, 999) . ' ' . rand(100, 999),
                //'bio' => 'Final year student in ' . ucfirst($studentData[//'department']) . ' department.',
                'email_verified_at' => now(),
            ]);
            // $student->assignRole('student');
        }
    }
}
