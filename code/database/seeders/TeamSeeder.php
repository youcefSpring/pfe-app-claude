<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = User::where('role', 'student')->get();

        // Team 1 - Computer Science (WebTech Innovators)
        $team1 = Team::create([
            'name' => 'WebTech Innovators',
            'leader_id' => $students->where('email', 'yassine.zerrouki@student.pfe.edu')->first()->id,
            'size' => 2,
            'status' => 'validated',
            'formation_completed_at' => now()->subDays(5),
        ]);

        // Add members to Team 1
        TeamMember::create([
            'team_id' => $team1->id,
            'user_id' => $students->where('email', 'yassine.zerrouki@student.pfe.edu')->first()->id,
            'role' => 'leader',
            'joined_at' => now()->subDays(15),
        ]);

        TeamMember::create([
            'team_id' => $team1->id,
            'user_id' => $students->where('email', 'meriem.belounis@student.pfe.edu')->first()->id,
            'role' => 'member',
            'joined_at' => now()->subDays(14),
        ]);

        // Team 2 - Computer Science (CodeCrafters)
        $team2 = Team::create([
            'name' => 'CodeCrafters',
            'leader_id' => $students->where('email', 'abderrahim.lakhdari@student.pfe.edu')->first()->id,
            'size' => 2,
            'status' => 'validated',
            'formation_completed_at' => now()->subDays(8),
        ]);

        TeamMember::create([
            'team_id' => $team2->id,
            'user_id' => $students->where('email', 'abderrahim.lakhdari@student.pfe.edu')->first()->id,
            'role' => 'leader',
            'joined_at' => now()->subDays(12),
        ]);

        TeamMember::create([
            'team_id' => $team2->id,
            'user_id' => $students->where('email', 'nesrine.boumediene@student.pfe.edu')->first()->id,
            'role' => 'member',
            'joined_at' => now()->subDays(12),
        ]);

        // Team 3 - Computer Science (Digital Pioneers)
        $team3 = Team::create([
            'name' => 'Digital Pioneers',
            'leader_id' => $students->where('email', 'khaled.meziane@student.pfe.edu')->first()->id,
            'size' => 3,
            'status' => 'validated',
            'formation_completed_at' => now()->subDays(3),
        ]);

        TeamMember::create([
            'team_id' => $team3->id,
            'user_id' => $students->where('email', 'khaled.meziane@student.pfe.edu')->first()->id,
            'role' => 'leader',
            'joined_at' => now()->subDays(10),
        ]);

        TeamMember::create([
            'team_id' => $team3->id,
            'user_id' => $students->where('email', 'soumia.ghazi@student.pfe.edu')->first()->id,
            'role' => 'member',
            'joined_at' => now()->subDays(9),
        ]);

        TeamMember::create([
            'team_id' => $team3->id,
            'user_id' => $students->where('email', 'bilal.hamdani@student.pfe.edu')->first()->id,
            'role' => 'member',
            'joined_at' => now()->subDays(8),
        ]);

        // Team 4 - Electronics (Circuit Masters)
        $team4 = Team::create([
            'name' => 'Circuit Masters',
            'leader_id' => $students->where('email', 'amine.benchikh@student.pfe.edu')->first()->id,
            'size' => 3,
            'status' => 'validated',
            'formation_completed_at' => now()->subDays(6),
        ]);

        TeamMember::create([
            'team_id' => $team4->id,
            'user_id' => $students->where('email', 'amine.benchikh@student.pfe.edu')->first()->id,
            'role' => 'leader',
            'joined_at' => now()->subDays(13),
        ]);

        TeamMember::create([
            'team_id' => $team4->id,
            'user_id' => $students->where('email', 'yasmine.charef@student.pfe.edu')->first()->id,
            'role' => 'member',
            'joined_at' => now()->subDays(12),
        ]);

        TeamMember::create([
            'team_id' => $team4->id,
            'user_id' => $students->where('email', 'reda.boutebba@student.pfe.edu')->first()->id,
            'role' => 'member',
            'joined_at' => now()->subDays(11),
        ]);

        // Team 5 - Electronics (IoT Solutions)
        $team5 = Team::create([
            'name' => 'IoT Solutions',
            'leader_id' => $students->where('email', 'farouk.benhabib@student.pfe.edu')->first()->id,
            'size' => 1,
            'status' => 'forming',
        ]);

        TeamMember::create([
            'team_id' => $team5->id,
            'user_id' => $students->where('email', 'farouk.benhabib@student.pfe.edu')->first()->id,
            'role' => 'leader',
            'joined_at' => now()->subDays(7),
        ]);

        // Team 6 - Mechanical Engineering (Green Engineers)
        $team6 = Team::create([
            'name' => 'Green Engineers',
            'leader_id' => $students->where('email', 'hamza.guerriche@student.pfe.edu')->first()->id,
            'size' => 2,
            'status' => 'validated',
            'formation_completed_at' => now()->subDays(4),
        ]);

        TeamMember::create([
            'team_id' => $team6->id,
            'user_id' => $students->where('email', 'hamza.guerriche@student.pfe.edu')->first()->id,
            'role' => 'leader',
            'joined_at' => now()->subDays(11),
        ]);

        TeamMember::create([
            'team_id' => $team6->id,
            'user_id' => $students->where('email', 'aicha.mammeri@student.pfe.edu')->first()->id,
            'role' => 'member',
            'joined_at' => now()->subDays(10),
        ]);

        // Individual student without team (looking to join)
        $individualTeam = Team::create([
            'name' => 'Solo Developer',
            'leader_id' => $students->where('email', 'rania.zouaoui@student.pfe.edu')->first()->id,
            'size' => 1,
            'status' => 'forming',
        ]);

        TeamMember::create([
            'team_id' => $individualTeam->id,
            'user_id' => $students->where('email', 'rania.zouaoui@student.pfe.edu')->first()->id,
            'role' => 'leader',
            'joined_at' => now()->subDays(5),
        ]);
    }
}