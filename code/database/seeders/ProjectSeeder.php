<?php

namespace Database\Seeders;

use App\Models\PfeProject;
use App\Models\Subject;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $validatedTeams = Team::where('status', 'validated')->get();
        $publishedSubjects = Subject::where('status', 'published')->get();

        // Project 1: WebTech Innovators get E-Learning Platform
        $webTechTeam = $validatedTeams->where('name', 'WebTech Innovators')->first();
        $elearningSubject = $publishedSubjects->where('title', 'LIKE', '%E-Learning%')->first();
        $supervisor = User::where('email', 'said.mansouri@pfe.edu')->first();

        if ($webTechTeam && $elearningSubject && $supervisor) {
            PfeProject::create([
                'subject_id' => $elearningSubject->id,
                'team_id' => $webTechTeam->id,
                'supervisor_id' => $supervisor->id,
                'status' => 'in_progress',
                'start_date' => now()->subDays(30),
                'expected_end_date' => now()->addMonths(4),
                'progress_percentage' => 35,
                'description' => 'Development of an AI-powered e-learning platform with personalized recommendations. The team is currently working on the user authentication system and course management modules.',
                'objectives' => 'Create a comprehensive e-learning platform, Implement AI recommendation engine, Develop responsive web interface, Integrate payment systems, Deploy and test the application',
                'methodology' => 'Agile development with 2-week sprints, Regular supervisor meetings, Continuous integration and testing, User feedback integration',
                'assigned_at' => now()->subDays(25),
            ]);
        }

        // Project 2: CodeCrafters get Plagiarism Detection System
        $codeCraftersTeam = $validatedTeams->where('name', 'CodeCrafters')->first();
        $plagiarismSubject = $publishedSubjects->where('title', 'LIKE', '%Plagiarism Detection%')->first();
        $aiSupervisor = User::where('email', 'amina.hadji@pfe.edu')->first();

        if ($codeCraftersTeam && $plagiarismSubject && $aiSupervisor) {
            PfeProject::create([
                'subject_id' => $plagiarismSubject->id,
                'team_id' => $codeCraftersTeam->id,
                'supervisor_id' => $aiSupervisor->id,
                'status' => 'in_progress',
                'start_date' => now()->subDays(25),
                'expected_end_date' => now()->addMonths(4),
                'progress_percentage' => 45,
                'description' => 'Advanced plagiarism detection system using NLP and machine learning. Currently implementing semantic analysis algorithms and building the document comparison engine.',
                'objectives' => 'Develop accurate plagiarism detection algorithms, Create web interface for document submission, Implement batch processing capabilities, Build comprehensive reporting system, Train and optimize ML models',
                'methodology' => 'Research-based development approach, Literature review and algorithm selection, Iterative model training and testing, Performance benchmarking against existing tools',
                'assigned_at' => now()->subDays(20),
            ]);
        }

        // Project 3: Digital Pioneers get IoT Smart Home (Blockchain was taken by another team in a different context)
        $digitalPioneersTeam = $validatedTeams->where('name', 'Digital Pioneers')->first();
        $iotSubject = $publishedSubjects->where('title', 'LIKE', '%Smart Home%')->first();
        $networkSupervisor = User::where('email', 'karim.bencherif@pfe.edu')->first();

        if ($digitalPioneersTeam && $iotSubject && $networkSupervisor) {
            PfeProject::create([
                'subject_id' => $iotSubject->id,
                'team_id' => $digitalPioneersTeam->id,
                'supervisor_id' => $networkSupervisor->id,
                'status' => 'in_progress',
                'start_date' => now()->subDays(20),
                'expected_end_date' => now()->addMonths(4),
                'progress_percentage' => 25,
                'description' => 'IoT-based smart home automation system with web and mobile interfaces. Team is currently setting up the hardware infrastructure and developing the central control hub.',
                'objectives' => 'Design and implement IoT sensor network, Develop web-based control dashboard, Create mobile application, Implement automated rules engine, Ensure security and reliability',
                'methodology' => 'Hardware-software co-design approach, Prototype-driven development, Regular testing with real sensors, Security-first implementation',
                'assigned_at' => now()->subDays(15),
            ]);
        }

        // Project 4: Circuit Masters get FPGA Image Processing
        $circuitMastersTeam = $validatedTeams->where('name', 'Circuit Masters')->first();
        $fpgaSubject = $publishedSubjects->where('title', 'LIKE', '%FPGA%')->first();
        $fpgaSupervisor = User::where('email', 'omar.chellali@pfe.edu')->first();

        if ($circuitMastersTeam && $fpgaSubject && $fpgaSupervisor) {
            PfeProject::create([
                'subject_id' => $fpgaSubject->id,
                'team_id' => $circuitMastersTeam->id,
                'supervisor_id' => $fpgaSupervisor->id,
                'status' => 'in_progress',
                'start_date' => now()->subDays(18),
                'expected_end_date' => now()->addMonths(4),
                'progress_percentage' => 40,
                'description' => 'Real-time image processing system using FPGA technology. Currently implementing edge detection and image filtering algorithms in VHDL.',
                'objectives' => 'Design FPGA-based image processing pipeline, Implement real-time video processing, Develop user interface for parameter control, Optimize performance for real-time operation, Create comprehensive testing framework',
                'methodology' => 'Hardware-centric development, VHDL/Verilog implementation, Simulation-based verification, Performance optimization, Real-time testing',
                'assigned_at' => now()->subDays(12),
            ]);
        }

        // Project 5: Green Engineers get Solar Water Pumping System
        $greenEngineersTeam = $validatedTeams->where('name', 'Green Engineers')->first();
        $solarSubject = $publishedSubjects->where('title', 'LIKE', '%Solar%')->first();
        $renewableSupervisor = User::where('email', 'samira.boudjemaa@pfe.edu')->first();

        if ($greenEngineersTeam && $solarSubject && $renewableSupervisor) {
            PfeProject::create([
                'subject_id' => $solarSubject->id,
                'team_id' => $greenEngineersTeam->id,
                'supervisor_id' => $renewableSupervisor->id,
                'status' => 'in_progress',
                'start_date' => now()->subDays(15),
                'expected_end_date' => now()->addMonths(4),
                'progress_percentage' => 30,
                'description' => 'Solar-powered water pumping system for rural applications. Currently working on system design calculations and component selection.',
                'objectives' => 'Design efficient solar pumping system, Select optimal components, Build and test prototype, Develop control system, Create installation and maintenance guides',
                'methodology' => 'Engineering design process, CAD modeling and simulation, Prototype construction and testing, Economic and environmental analysis',
                'assigned_at' => now()->subDays(10),
            ]);
        }

        // Completed Project Example (for historical data)
        // Let's create a completed project from last semester
        $completedTeam = Team::create([
            'name' => 'Legacy Developers',
            'description' => 'Previous semester team that completed their project successfully.',
            'leader_id' => User::where('role', 'student')->first()->id,
            'status' => 'completed',
            'max_members' => 2,
            'department' => 'informatique',
            'validated_at' => now()->subMonths(6),
            'created_at' => now()->subMonths(6),
        ]);

        $completedSubject = Subject::create([
            'title' => 'Student Information Management System',
            'description' => 'A comprehensive student information management system for university administration.',
            'keywords' => ['Web Development', 'Database', 'Management System', 'University'],
            'required_tools' => 'Laravel, MySQL, Bootstrap',
            'max_teams' => 1,
            'recommended_team_size' => 2,
            'difficulty_level' => 'intermediate',
            'department' => 'informatique',
            'supervisor_id' => $supervisor->id,
            'status' => 'archived',
            'created_at' => now()->subMonths(7),
        ]);

        PfeProject::create([
            'subject_id' => $completedSubject->id,
            'team_id' => $completedTeam->id,
            'supervisor_id' => $supervisor->id,
            'status' => 'completed',
            'start_date' => now()->subMonths(6),
            'expected_end_date' => now()->subMonths(2),
            'actual_end_date' => now()->subMonths(2),
            'progress_percentage' => 100,
            'description' => 'Successfully completed student information management system with all required features implemented.',
            'objectives' => 'Student registration, Grade management, Course enrollment, Reporting system',
            'methodology' => 'Traditional waterfall methodology with regular milestone reviews',
            'final_grade' => 16.5,
            'assigned_at' => now()->subMonths(6),
            'completed_at' => now()->subMonths(2),
        ]);
    }
}