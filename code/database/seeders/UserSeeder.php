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
        $teachers = [
            ['name' => 'Dr. Benabderrezak Youcef', 'email' => 'benabderrezak.youcef@university.edu'],
            ['name' => 'Dr. Kherfi Rachid', 'email' => 'ahmed.hassan@university.edu']
        ];

        foreach ($teachers as $teacher) {
            User::create([
                'name' => $teacher['name'],
                'email' => $teacher['email'],
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'department' => 'Computer Science',
                'email_verified_at' => now(),
            ]);
        }

        // Create Computer Science Students (License level)
        $students = [
            ['name' => 'Medjdene Imade ddine', 'email' => 'medjdene.imade.ddine@student.university.edu'],
            ['name' => 'ayoub dubois', 'email' => 'ayoub@student.university.edu'],
            ['name' => 'omar dubois', 'email' => 'omar@student.university.edu'],
            ['name' => 'anfel dubois', 'email' => 'anfel@student.university.edu'],
            ['name' => 'Ahlem dubois', 'email' => 'ahlem@student.university.edu'],
            ['name' => 'saida', 'email' => 'saida@student.university.edu'],
            ['name' => 'yasmine', 'email' => 'alice.dubois@student.university.edu']
        ];

        foreach ($students as $student) {
            User::create([
                'name' => $student['name'],
                'email' => $student['email'],
                'password' => Hash::make('password'),
                'role' => 'student',
                'department' => 'Computer Science',
                'email_verified_at' => now(),
            ]);

        }
        // Create External Supervisors
        $externalSupervisors =[
            ['name' => 'Mr Ayoub Souhaib', 'email' => 'ayoub.souhaib@techcorp.com']]
        ;

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

        $this->command->info('Created users: 1 admin, 4 department heads, 2 teachers, 7 students, 1 external supervisor');
    }
}
