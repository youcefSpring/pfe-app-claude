<?php

namespace Database\Seeders;

use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        // Get teachers by department
        $csTeachers = User::where('role', 'teacher')->where('department', 'Computer Science')->get();
        $engTeachers = User::where('role', 'teacher')->where('department', 'Engineering')->get();
        $mathTeachers = User::where('role', 'teacher')->where('department', 'Mathematics')->get();
        $physicsTeachers = User::where('role', 'teacher')->where('department', 'Physics')->get();

        $subjects = [
            [
                'title' => 'AI-Powered E-commerce Recommendation System',
                'description' => 'Develop a machine learning system that provides personalized product recommendations for e-commerce platforms.',
                'keywords' => 'machine learning, recommendation systems, e-commerce, data analysis, Python, TensorFlow',
                'tools' => 'Python, TensorFlow/PyTorch, web frameworks, databases, APIs',
                'plan' => 'Phase 1: Data collection and analysis. Phase 2: Model development. Phase 3: Web application creation.',
                'status' => 'validated',
                'teacher_id' => $csTeachers->random()->id,
            ],
            [
                'title' => 'Blockchain-Based Supply Chain Management',
                'description' => 'Create a blockchain application to track products through the supply chain.',
                'keywords' => 'blockchain, supply chain, smart contracts, Solidity, transparency',
                'tools' => 'Solidity, Web3.js, Node.js, React, Ethereum',
                'plan' => 'Phase 1: Blockchain design. Phase 2: Smart contract development. Phase 3: Web interface.',
                'status' => 'validated',
                'teacher_id' => $csTeachers->random()->id,
            ],
            [
                'title' => 'Mobile Health Monitoring Application',
                'description' => 'Develop a mobile application that connects to wearable devices to monitor health metrics.',
                'keywords' => 'mobile development, health tech, IoT, wearables, APIs',
                'tools' => 'React Native, Node.js, MongoDB, health APIs',
                'plan' => 'Phase 1: API integration. Phase 2: Mobile app development. Phase 3: Testing.',
                'status' => 'validated',
                'teacher_id' => $csTeachers->random()->id,
            ],
            [
                'title' => 'Real-time Chat Application with Video Calling',
                'description' => 'Build a comprehensive chat application with real-time messaging and video calling.',
                'keywords' => 'real-time communication, WebRTC, socket.io, chat, video calling',
                'tools' => 'Node.js, Socket.io, WebRTC, React, MongoDB',
                'plan' => 'Phase 1: Real-time messaging. Phase 2: Video calling integration. Phase 3: File sharing.',
                'status' => 'validated',
                'teacher_id' => $csTeachers->random()->id,
            ],
            [
                'title' => 'Cybersecurity Incident Response Platform',
                'description' => 'Develop a platform for managing cybersecurity incidents and threat detection.',
                'keywords' => 'cybersecurity, incident response, SIEM, threat detection, security',
                'tools' => 'Python, Django, PostgreSQL, security tools, APIs',
                'plan' => 'Phase 1: Threat detection. Phase 2: Incident management. Phase 3: Automated response.',
                'status' => 'pending_validation',
                'teacher_id' => $csTeachers->random()->id,
            ],
            [
                'title' => 'Autonomous Drone Navigation System',
                'description' => 'Design and implement an autonomous navigation system for drones.',
                'keywords' => 'drone, autonomous systems, computer vision, GPS, embedded systems',
                'tools' => 'C++, Python, OpenCV, ArduPilot, sensors',
                'plan' => 'Phase 1: Computer vision. Phase 2: Navigation algorithms. Phase 3: Hardware integration.',
                'status' => 'validated',
                'teacher_id' => $engTeachers->random()->id,
            ],
            [
                'title' => 'Smart Home Automation System',
                'description' => 'Develop an IoT-based home automation system.',
                'keywords' => 'IoT, home automation, sensors, wireless communication, mobile app',
                'tools' => 'Arduino, Raspberry Pi, Node.js, React Native, MQTT',
                'plan' => 'Phase 1: Sensor integration. Phase 2: Communication protocols. Phase 3: Mobile interface.',
                'status' => 'validated',
                'teacher_id' => $engTeachers->random()->id,
            ],
            [
                'title' => 'Mathematical Modeling of Epidemic Spread',
                'description' => 'Develop mathematical models to predict and analyze epidemic spread.',
                'keywords' => 'mathematical modeling, epidemiology, differential equations, statistics',
                'tools' => 'Python, R, MATLAB, statistical libraries',
                'plan' => 'Phase 1: Model development. Phase 2: Parameter estimation. Phase 3: Validation.',
                'status' => 'validated',
                'teacher_id' => $mathTeachers->random()->id,
            ],
            [
                'title' => 'Quantum Computing Simulation Platform',
                'description' => 'Develop a software platform to simulate quantum computing algorithms.',
                'keywords' => 'quantum computing, simulation, quantum algorithms, visualization',
                'tools' => 'Python, Qiskit, Jupyter, quantum simulators',
                'plan' => 'Phase 1: Quantum algorithms. Phase 2: Simulation engine. Phase 3: Visualization.',
                'status' => 'validated',
                'teacher_id' => $physicsTeachers->random()->id,
            ],
        ];

        foreach ($subjects as $subject) {
            Subject::create($subject);
        }

        $this->command->info('Created 9 subjects across all departments');
    }
}
