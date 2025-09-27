<?php

namespace Database\Seeders;

use App\Models\Subject;
use App\Models\Team;
use App\Models\TeamSubjectPreference;
use Illuminate\Database\Seeder;

class TeamSubjectPreferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teams = Team::where('status', 'validated')->get();
        $publishedSubjects = Subject::where('status', 'published')->get();

        // WebTech Innovators preferences (CS Team 1)
        $webTechTeam = $teams->where('name', 'WebTech Innovators')->first();
        if ($webTechTeam && $publishedSubjects->count() > 0) {
            $csSubjects = $publishedSubjects->where('department', 'informatique');

            // First preference: E-Learning Platform (perfect match for their AI interest)
            $elearningSubject = $csSubjects->where('title', 'LIKE', '%E-Learning%')->first();
            if ($elearningSubject) {
                TeamSubjectPreference::create([
                    'team_id' => $webTechTeam->id,
                    'subject_id' => $elearningSubject->id,
                    'preference_order' => 1,
                    'motivation' => 'Our team has strong experience in web development and machine learning. This project perfectly aligns with our skills in Laravel, React, and AI technologies.',
                    'created_at' => now()->subDays(4),
                ]);
            }

            // Second preference: Collaborative Code Editor
            $codeEditorSubject = $csSubjects->where('title', 'LIKE', '%Collaborative Code Editor%')->first();
            if ($codeEditorSubject) {
                TeamSubjectPreference::create([
                    'team_id' => $webTechTeam->id,
                    'subject_id' => $codeEditorSubject->id,
                    'preference_order' => 2,
                    'motivation' => 'We are passionate about real-time web technologies and have experience with WebSockets and collaborative tools.',
                    'created_at' => now()->subDays(4),
                ]);
            }

            // Third preference: IoT Smart Home
            $iotSubject = $csSubjects->where('title', 'LIKE', '%Smart Home%')->first();
            if ($iotSubject) {
                TeamSubjectPreference::create([
                    'team_id' => $webTechTeam->id,
                    'subject_id' => $iotSubject->id,
                    'preference_order' => 3,
                    'motivation' => 'Interested in expanding our skills to IoT development and integrating web technologies with hardware.',
                    'created_at' => now()->subDays(4),
                ]);
            }
        }

        // CodeCrafters preferences (CS Team 2)
        $codeCraftersTeam = $teams->where('name', 'CodeCrafters')->first();
        if ($codeCraftersTeam && $publishedSubjects->count() > 0) {
            $csSubjects = $publishedSubjects->where('department', 'informatique');

            // First preference: Plagiarism Detection System
            $plagiarismSubject = $csSubjects->where('title', 'LIKE', '%Plagiarism Detection%')->first();
            if ($plagiarismSubject) {
                TeamSubjectPreference::create([
                    'team_id' => $codeCraftersTeam->id,
                    'subject_id' => $plagiarismSubject->id,
                    'preference_order' => 1,
                    'motivation' => 'We have strong background in Python and machine learning. Academic integrity is important to us and we want to contribute to educational technology.',
                    'created_at' => now()->subDays(3),
                ]);
            }

            // Second preference: Collaborative Code Editor (competing with Team 1)
            $codeEditorSubject = $csSubjects->where('title', 'LIKE', '%Collaborative Code Editor%')->first();
            if ($codeEditorSubject) {
                TeamSubjectPreference::create([
                    'team_id' => $codeCraftersTeam->id,
                    'subject_id' => $codeEditorSubject->id,
                    'preference_order' => 2,
                    'motivation' => 'As developers ourselves, we understand the needs of collaborative coding environments and have ideas for innovative features.',
                    'created_at' => now()->subDays(3),
                ]);
            }
        }

        // Digital Pioneers preferences (CS Team 3)
        $digitalPioneersTeam = $teams->where('name', 'Digital Pioneers')->first();
        if ($digitalPioneersTeam && $publishedSubjects->count() > 0) {
            $csSubjects = $publishedSubjects->where('department', 'informatique');

            // First preference: Blockchain Certificate System
            $blockchainSubject = $csSubjects->where('title', 'LIKE', '%Blockchain%')->first();
            if ($blockchainSubject) {
                TeamSubjectPreference::create([
                    'team_id' => $digitalPioneersTeam->id,
                    'subject_id' => $blockchainSubject->id,
                    'preference_order' => 1,
                    'motivation' => 'Blockchain technology represents the future of digital verification. Our team has been studying cryptocurrency and smart contracts, making this project ideal for our expertise.',
                    'created_at' => now()->subDays(2),
                ]);
            }

            // Second preference: IoT Smart Home
            $iotSubject = $csSubjects->where('title', 'LIKE', '%Smart Home%')->first();
            if ($iotSubject) {
                TeamSubjectPreference::create([
                    'team_id' => $digitalPioneersTeam->id,
                    'subject_id' => $iotSubject->id,
                    'preference_order' => 2,
                    'motivation' => 'IoT is a rapidly growing field that combines hardware and software. We want to explore distributed systems and real-time data processing.',
                    'created_at' => now()->subDays(2),
                ]);
            }
        }

        // Circuit Masters preferences (Electronics Team)
        $circuitMastersTeam = $teams->where('name', 'Circuit Masters')->first();
        if ($circuitMastersTeam && $publishedSubjects->count() > 0) {
            $elecSubjects = $publishedSubjects->where('department', 'electronique');

            // First preference: FPGA Image Processing
            $fpgaSubject = $elecSubjects->where('title', 'LIKE', '%FPGA%')->first();
            if ($fpgaSubject) {
                TeamSubjectPreference::create([
                    'team_id' => $circuitMastersTeam->id,
                    'subject_id' => $fpgaSubject->id,
                    'preference_order' => 1,
                    'motivation' => 'Our team has extensive experience with FPGA development and VHDL programming. Real-time image processing combines our hardware skills with cutting-edge applications.',
                    'created_at' => now()->subDays(5),
                ]);
            }

            // Second preference: Wireless Sensor Network
            $wsnSubject = $elecSubjects->where('title', 'LIKE', '%Wireless Sensor Network%')->first();
            if ($wsnSubject) {
                TeamSubjectPreference::create([
                    'team_id' => $circuitMastersTeam->id,
                    'subject_id' => $wsnSubject->id,
                    'preference_order' => 2,
                    'motivation' => 'Environmental monitoring is crucial for our future. We have experience with microcontrollers and wireless communication protocols.',
                    'created_at' => now()->subDays(5),
                ]);
            }
        }

        // Green Engineers preferences (Mechanical Team)
        $greenEngineersTeam = $teams->where('name', 'Green Engineers')->first();
        if ($greenEngineersTeam && $publishedSubjects->count() > 0) {
            $mechSubjects = $publishedSubjects->where('department', 'mecanique');

            // First preference: Solar Water Pumping System
            $solarSubject = $mechSubjects->where('title', 'LIKE', '%Solar%')->first();
            if ($solarSubject) {
                TeamSubjectPreference::create([
                    'team_id' => $greenEngineersTeam->id,
                    'subject_id' => $solarSubject->id,
                    'preference_order' => 1,
                    'motivation' => 'Sustainable technology is our passion. This project addresses real-world problems in rural communities while promoting renewable energy adoption.',
                    'created_at' => now()->subDays(3),
                ]);
            }
        }

        // Create some additional preferences to simulate conflicts
        // Team CodeCrafters also wants the E-Learning platform (creates conflict with WebTech Innovators)
        $elearningSubject = $publishedSubjects->where('title', 'LIKE', '%E-Learning%')->first();
        if ($codeCraftersTeam && $elearningSubject) {
            TeamSubjectPreference::create([
                'team_id' => $codeCraftersTeam->id,
                'subject_id' => $elearningSubject->id,
                'preference_order' => 3,
                'motivation' => 'As our third choice, we believe our software engineering approach could bring a different perspective to e-learning platform development.',
                'created_at' => now()->subDays(2),
            ]);
        }
    }
}