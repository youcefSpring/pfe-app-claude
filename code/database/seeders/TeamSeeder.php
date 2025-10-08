<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use App\Models\Subject;
use App\Models\Project;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    public function run(): void
    {
        // Get students
        $csStudents = User::where('role', 'student')
            ->where('department', 'Computer Science')
            ->get();

        $engStudents = User::where('role', 'student')
            ->where('department', 'Engineering')
            ->get();

        // Create a few simple teams
        $teams = [
            [
                'name' => 'CodeCrafters',
                'status' => 'forming',
            ]
        ];

        foreach ($teams as $teamData) {
            $team = Team::create($teamData);

            // Add some team members
            $students = $csStudents->take(1);
            foreach ($students as $index => $student) {
                TeamMember::create([
                    'team_id' => $team->id,
                    'student_id' => $student->id,
                    'role' => $index === 0 ? 'leader' : 'member',
                    'joined_at' => now(),
                ]);
            }

            // Skip used students for next team
            $csStudents = $csStudents->skip(1);
        }

        $this->command->info('Created 3 teams with members');
    }
}
