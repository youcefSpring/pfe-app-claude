<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'System Administrator',
            'email' => 'admin@university.edu',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'department' => 'Administration',
            'email_verified_at' => now(),
        ]);

        // Create Department Heads
        $departments = ['Computer Science', 'Engineering', 'Mathematics', 'Physics'];
        foreach ($departments as $dept) {
            User::create([
                'name' => 'Head of ' . $dept,
                'email' => strtolower(str_replace(' ', '.', $dept)) . '.head@university.edu',
                'password' => Hash::make('password'),
                'role' => 'department_head',
                'department' => $dept,
                'email_verified_at' => now(),
            ]);
        }

        // Create Teachers for Computer Science
        $csTeachers = [
            ['name' => 'Dr. Sarah Johnson', 'email' => 'sarah.johnson@university.edu'],
            ['name' => 'Prof. Michael Chen', 'email' => 'michael.chen@university.edu'],
            ['name' => 'Dr. Emily Rodriguez', 'email' => 'emily.rodriguez@university.edu'],
            ['name' => 'Prof. David Kim', 'email' => 'david.kim@university.edu'],
            ['name' => 'Dr. Lisa Thompson', 'email' => 'lisa.thompson@university.edu'],
            ['name' => 'Prof. Ahmed Hassan', 'email' => 'ahmed.hassan@university.edu'],
        ];

        foreach ($csTeachers as $teacher) {
            User::create([
                'name' => $teacher['name'],
                'email' => $teacher['email'],
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'department' => 'Computer Science',
                'email_verified_at' => now(),
            ]);
        }

        // Create Teachers for Engineering
        $engTeachers = [
            ['name' => 'Dr. Robert Wilson', 'email' => 'robert.wilson@university.edu'],
            ['name' => 'Prof. Maria Garcia', 'email' => 'maria.garcia@university.edu'],
            ['name' => 'Dr. James Lee', 'email' => 'james.lee@university.edu'],
        ];

        foreach ($engTeachers as $teacher) {
            User::create([
                'name' => $teacher['name'],
                'email' => $teacher['email'],
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'department' => 'Engineering',
                'email_verified_at' => now(),
            ]);
        }

        // Create Mathematics Teachers
        $mathTeachers = [
            ['name' => 'Dr. Sophie Martin', 'email' => 'sophie.martin@university.edu'],
            ['name' => 'Prof. Thomas Brown', 'email' => 'thomas.brown@university.edu'],
        ];

        foreach ($mathTeachers as $teacher) {
            User::create([
                'name' => $teacher['name'],
                'email' => $teacher['email'],
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'department' => 'Mathematics',
                'email_verified_at' => now(),
            ]);
        }

        // Create Physics Teachers
        $physicsTeachers = [
            ['name' => 'Dr. Anna Petrov', 'email' => 'anna.petrov@university.edu'],
            ['name' => 'Prof. Carlos Santos', 'email' => 'carlos.santos@university.edu'],
        ];

        foreach ($physicsTeachers as $teacher) {
            User::create([
                'name' => $teacher['name'],
                'email' => $teacher['email'],
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'department' => 'Physics',
                'email_verified_at' => now(),
            ]);
        }

        // Create Computer Science Students (License level)
        $csLicenseStudents = [
            ['name' => 'Alice Dubois', 'email' => 'alice.dubois@student.university.edu'],
            ['name' => 'Bob Martin', 'email' => 'bob.martin@student.university.edu'],
            ['name' => 'Charlie Leblanc', 'email' => 'charlie.leblanc@student.university.edu'],
            ['name' => 'Diana Moreau', 'email' => 'diana.moreau@student.university.edu'],
            ['name' => 'Ethan Rousseau', 'email' => 'ethan.rousseau@student.university.edu'],
            ['name' => 'Fiona Laurent', 'email' => 'fiona.laurent@student.university.edu'],
            ['name' => 'Gabriel Simon', 'email' => 'gabriel.simon@student.university.edu'],
            ['name' => 'Hannah Michel', 'email' => 'hannah.michel@student.university.edu'],
            ['name' => 'Ivan Petit', 'email' => 'ivan.petit@student.university.edu'],
            ['name' => 'Julia Girard', 'email' => 'julia.girard@student.university.edu'],
            ['name' => 'Kevin Roux', 'email' => 'kevin.roux@student.university.edu'],
            ['name' => 'Lila Fournier', 'email' => 'lila.fournier@student.university.edu'],
            ['name' => 'Marc Morel', 'email' => 'marc.morel@student.university.edu'],
            ['name' => 'Nina Andre', 'email' => 'nina.andre@student.university.edu'],
            ['name' => 'Oscar Leroy', 'email' => 'oscar.leroy@student.university.edu'],
            ['name' => 'Paula Clement', 'email' => 'paula.clement@student.university.edu'],
        ];

        foreach ($csLicenseStudents as $student) {
            User::create([
                'name' => $student['name'],
                'email' => $student['email'],
                'password' => Hash::make('password'),
                'role' => 'student',
                'department' => 'Computer Science',
                'email_verified_at' => now(),
            ]);
        }

        // Create Computer Science Students (Master level)
        $csMasterStudents = [
            ['name' => 'Quentin Bernard', 'email' => 'quentin.bernard@student.university.edu'],
            ['name' => 'Rachel Durand', 'email' => 'rachel.durand@student.university.edu'],
            ['name' => 'Samuel Lefebvre', 'email' => 'samuel.lefebvre@student.university.edu'],
            ['name' => 'Tara Mercier', 'email' => 'tara.mercier@student.university.edu'],
            ['name' => 'Ulysse Blanc', 'email' => 'ulysse.blanc@student.university.edu'],
            ['name' => 'Valerie Guerin', 'email' => 'valerie.guerin@student.university.edu'],
            ['name' => 'William Muller', 'email' => 'william.muller@student.university.edu'],
            ['name' => 'Xenia Robin', 'email' => 'xenia.robin@student.university.edu'],
        ];

        foreach ($csMasterStudents as $student) {
            User::create([
                'name' => $student['name'],
                'email' => $student['email'],
                'password' => Hash::make('password'),
                'role' => 'student',
                'department' => 'Computer Science',
                'grade' => 'master',
                'email_verified_at' => now(),
            ]);
        }

        // Create Engineering Students
        $engStudents = [
            ['name' => 'Yann Garnier', 'email' => 'yann.garnier@student.university.edu'],
            ['name' => 'Zoe Chevalier', 'email' => 'zoe.chevalier@student.university.edu'],
            ['name' => 'Adam Francois', 'email' => 'adam.francois@student.university.edu'],
            ['name' => 'Bella Faure', 'email' => 'bella.faure@student.university.edu'],
            ['name' => 'Cedric Giraud', 'email' => 'cedric.giraud@student.university.edu'],
            ['name' => 'Delphine Henry', 'email' => 'delphine.henry@student.university.edu'],
        ];

        foreach ($engStudents as $student) {
            User::create([
                'name' => $student['name'],
                'email' => $student['email'],
                'password' => Hash::make('password'),
                'role' => 'student',
                'department' => 'Engineering',
                'email_verified_at' => now(),
            ]);
        }

        // Create External Supervisors
        $externalSupervisors = [
            ['name' => 'Dr. Jean Dupont', 'email' => 'jean.dupont@techcorp.com'],
            ['name' => 'Marie Lecomte', 'email' => 'marie.lecomte@innovate.fr'],
            ['name' => 'Pierre Dubois', 'email' => 'pierre.dubois@startup.ai'],
        ];

        foreach ($externalSupervisors as $supervisor) {
            User::create([
                'name' => $supervisor['name'],
                'email' => $supervisor['email'],
                'password' => Hash::make('password'),
                'role' => 'external_supervisor',
                'department' => 'External',
                'email_verified_at' => now(),
            ]);
        }

        $this->command->info('Created users: 1 admin, 4 department heads, 13 teachers, 30 students, 3 external supervisors');
    }
}
