<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test users for different roles
        $testUsers = [
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'matricule' => 'ADM001',
                'department' => 'Administration',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Teacher User',
                'email' => 'teacher@example.com',
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'matricule' => 'TCH001',
                'department' => 'Computer Science',
                'speciality' => 'Software Engineering',
                'grade' => 'professor',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Student User',
                'email' => 'student@example.com',
                'password' => Hash::make('password'),
                'role' => 'student',
                'matricule' => 'STU001',
                'department' => 'Computer Science',
                'speciality' => 'Software Engineering',
                'numero_inscription' => '2024001',
                'annee_bac' => 2022,
                'date_naissance' => '2000-01-01',
                'section' => 'A',
                'groupe' => '1',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Department Head',
                'email' => 'head@example.com',
                'password' => Hash::make('password'),
                'role' => 'department_head',
                'matricule' => 'DPT001',
                'department' => 'Computer Science',
                'speciality' => 'Software Engineering',
                'grade' => 'professor',
                'email_verified_at' => now(),
            ],
        ];

        foreach ($testUsers as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        $this->command->info('Test users created successfully!');
        $this->command->info('Admin: admin@example.com / password');
        $this->command->info('Teacher: teacher@example.com / password');
        $this->command->info('Student: student@example.com / password');
        $this->command->info('Department Head: head@example.com / password');
    }
}
