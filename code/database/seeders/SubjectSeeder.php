<?php

namespace Database\Seeders;

use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createComputerScienceSubjects();
        $this->createElectronicsSubjects();
        $this->createMechanicalSubjects();
    }

    private function createComputerScienceSubjects()
    {
        $teachers = User::where('department', 'informatique')->where('role', 'teacher')->get();

        if ($teachers->isEmpty()) {
            return; // Skip if no teachers found
        }

        $subjects = [
            [
                'title' => 'E-Learning Platform with AI-Powered Recommendations',
                'description' => 'Develop a comprehensive e-learning platform that uses artificial intelligence to provide personalized course recommendations to students. The system should include user management, course content delivery, progress tracking, and an AI recommendation engine based on learning patterns and preferences.

Features to implement:
- User authentication and role management (students, instructors, admins)
- Course creation and management tools
- Interactive video player with bookmarks and notes
- Discussion forums and real-time chat
- AI-powered course recommendation system
- Progress analytics and reporting
- Mobile-responsive design
- Integration with payment gateways

The AI component should analyze user behavior, completion rates, quiz scores, and time spent on different topics to suggest relevant courses and learning paths.',
                'keywords' => ['AI', 'Machine Learning', 'E-Learning', 'Web Development', 'Laravel', 'React', 'Python', 'Recommendation System'],
                'required_tools' => 'Laravel, React.js, Python, TensorFlow/Scikit-learn, MySQL, Redis, Docker',
                'max_teams' => 1,
                'recommended_team_size' => 3,
                'difficulty_level' => 'advanced',
                'department' => 'informatique',
                'status' => 'published',
                'prerequisites' => 'Web development experience, Basic knowledge of machine learning concepts, Database design',
                'expected_deliverables' => 'Functional web application, AI recommendation model, API documentation, User manual, Source code with documentation',
            ],
            [
                'title' => 'IoT-Based Smart Home Automation System',
                'description' => 'Design and implement a comprehensive smart home automation system that allows users to control and monitor various home devices through a web interface and mobile application. The system should integrate multiple IoT sensors and actuators.

Key components:
- Central control hub using Raspberry Pi or similar
- Web-based dashboard for device control
- Mobile application for remote access
- Sensor integration (temperature, humidity, motion, light)
- Device control (lights, fans, locks, cameras)
- Automated rules and scheduling
- Real-time notifications and alerts
- Data logging and analytics
- Voice control integration
- Security and encryption

The system should be scalable and allow easy addition of new devices and sensors.',
                'keywords' => ['IoT', 'Smart Home', 'Raspberry Pi', 'Web Development', 'Mobile App', 'Sensors', 'Automation', 'Node.js'],
                'required_tools' => 'Raspberry Pi, Arduino, Node.js, React, React Native/Flutter, MQTT, InfluxDB, Grafana',
                'max_teams' => 1,
                'recommended_team_size' => 3,
                'difficulty_level' => 'advanced',
                'department' => 'informatique',
                'status' => 'published',
                'prerequisites' => 'Programming experience, Basic electronics knowledge, Web/mobile development',
                'expected_deliverables' => 'Working smart home system, Web dashboard, Mobile app, Hardware setup guide, API documentation',
            ],
            [
                'title' => 'Blockchain-Based Digital Certificate Verification System',
                'description' => 'Create a secure digital certificate verification system using blockchain technology. The system will allow educational institutions to issue tamper-proof digital certificates and enable employers to verify their authenticity instantly.

System features:
- Certificate issuance platform for institutions
- Blockchain-based certificate storage
- QR code generation for easy verification
- Public verification portal
- Batch certificate processing
- Integration with existing student information systems
- Audit trail and analytics
- Multi-signature validation
- IPFS integration for document storage
- RESTful API for third-party integrations

The blockchain implementation should ensure immutability, transparency, and decentralization while maintaining privacy and security.',
                'keywords' => ['Blockchain', 'Digital Certificates', 'Ethereum', 'Smart Contracts', 'Web3', 'IPFS', 'Verification', 'Security'],
                'required_tools' => 'Solidity, Ethereum, Web3.js, Node.js, React, IPFS, Truffle/Hardhat, MetaMask',
                'max_teams' => 1,
                'recommended_team_size' => 2,
                'difficulty_level' => 'expert',
                'department' => 'informatique',
                'status' => 'approved',
                'prerequisites' => 'Cryptography basics, Web development, Understanding of blockchain concepts',
                'expected_deliverables' => 'Blockchain application, Smart contracts, Web interface, Mobile app, Verification portal, Technical documentation',
            ],
            [
                'title' => 'Real-Time Collaborative Code Editor with Video Chat',
                'description' => 'Develop a real-time collaborative code editor similar to Visual Studio Code Live Share, with integrated video chat and project management features. The platform should support multiple programming languages and real-time collaboration.

Core features:
- Real-time code editing with conflict resolution
- Syntax highlighting for multiple languages
- Integrated video/audio chat
- Project file management
- Version control integration (Git)
- User presence indicators
- Code execution environment
- Chat and commenting system
- Screen sharing capabilities
- Room-based collaboration
- Plugin system for extensions

The system should handle concurrent editing efficiently and provide a smooth user experience for distributed teams.',
                'keywords' => ['Real-time', 'Collaboration', 'Code Editor', 'WebRTC', 'Socket.io', 'Monaco Editor', 'WebSockets', 'Git'],
                'required_tools' => 'Node.js, Socket.io, Monaco Editor, WebRTC, React, Express, Docker, Git API',
                'max_teams' => 1,
                'recommended_team_size' => 3,
                'difficulty_level' => 'advanced',
                'department' => 'informatique',
                'status' => 'published',
                'prerequisites' => 'Web development, Real-time systems, WebSocket programming',
                'expected_deliverables' => 'Web application, Real-time collaboration engine, Video chat integration, User manual, API documentation',
            ],
            [
                'title' => 'AI-Powered Academic Plagiarism Detection System',
                'description' => 'Build an advanced plagiarism detection system using natural language processing and machine learning techniques. The system should detect various types of plagiarism including paraphrasing, translation, and code plagiarism.

System capabilities:
- Text similarity analysis using multiple algorithms
- Paraphrasing detection with semantic analysis
- Cross-language plagiarism detection
- Code plagiarism detection for programming assignments
- Batch processing of documents
- Detailed similarity reports with highlighting
- Integration with learning management systems
- Originality scoring and thresholds
- Citation and reference checking
- Performance analytics and reporting

The AI component should continuously learn and improve its detection accuracy.',
                'keywords' => ['AI', 'NLP', 'Plagiarism Detection', 'Machine Learning', 'Text Analysis', 'Academic Integrity', 'Python', 'Deep Learning'],
                'required_tools' => 'Python, NLTK/spaCy, TensorFlow/PyTorch, Elasticsearch, Flask/Django, React, PostgreSQL',
                'max_teams' => 1,
                'recommended_team_size' => 2,
                'difficulty_level' => 'advanced',
                'department' => 'informatique',
                'status' => 'approved',
                'prerequisites' => 'Python programming, Machine learning basics, NLP concepts, Web development',
                'expected_deliverables' => 'Detection system, ML models, Web interface, API, Performance evaluation report, User documentation',
            ],
            [
                'title' => 'Mobile Banking Application with Advanced Security',
                'description' => 'Develop a secure mobile banking application with biometric authentication, real-time fraud detection, and comprehensive financial management features.

Security features:
- Biometric authentication (fingerprint, face recognition)
- Two-factor authentication
- Real-time fraud detection using ML
- End-to-end encryption
- Secure PIN and pattern locks
- Session management and timeout
- Geolocation-based security

Banking features:
- Account balance and transaction history
- Money transfers and payments
- Bill payment and scheduling
- Budget tracking and financial insights
- Investment portfolio management
- Loan applications and tracking
- Customer support chat
- Offline transaction queuing

The application should meet banking security standards and provide an intuitive user experience.',
                'keywords' => ['Mobile Banking', 'Security', 'Biometrics', 'Fraud Detection', 'Fintech', 'React Native', 'Encryption', 'ML'],
                'required_tools' => 'React Native/Flutter, Node.js, PostgreSQL, Firebase, TensorFlow Lite, Biometric APIs',
                'max_teams' => 1,
                'recommended_team_size' => 3,
                'difficulty_level' => 'expert',
                'department' => 'informatique',
                'status' => 'submitted',
                'prerequisites' => 'Mobile development, Security concepts, Database design, API development',
                'expected_deliverables' => 'Mobile application, Backend API, Security implementation, Fraud detection model, User manual',
            ]
        ];

        foreach ($subjects as $index => $subjectData) {
            $supervisor = $teachers->get($index % $teachers->count());

            Subject::create([
                'title' => $subjectData['title'],
                'description' => $subjectData['description'],
                'keywords' => $subjectData['keywords'],
                'required_tools' => $subjectData['required_tools'],
                'max_teams' => $subjectData['max_teams'],
                'supervisor_id' => $supervisor->id,
                'status' => $subjectData['status'],
                'validated_at' => in_array($subjectData['status'], ['approved', 'published']) ? now()->subDays(rand(1, 15)) : null,
            ]);
        }
    }

    private function createElectronicsSubjects()
    {
        $teachers = User::where('department', 'informatique')->where('role', 'teacher')->get();

        if ($teachers->isEmpty()) {
            return; // Skip if no teachers found
        }

        $subjects = [
            [
                'title' => 'FPGA-Based Real-Time Image Processing System',
                'description' => 'Design and implement a real-time image processing system using FPGA technology for applications such as object detection, edge detection, and image filtering.

Technical specifications:
- FPGA-based hardware acceleration
- Real-time video stream processing
- Multiple image processing algorithms
- VGA/HDMI output for display
- Camera interface for input
- Hardware-software co-design
- Performance optimization
- Custom IP core development
- Memory management strategies
- User interface for parameter control

The system should achieve real-time processing rates suitable for video applications.',
                'keywords' => ['FPGA', 'Image Processing', 'VHDL', 'Verilog', 'Real-time', 'Hardware Design', 'Xilinx', 'Computer Vision'],
                'required_tools' => 'Xilinx Vivado, VHDL/Verilog, FPGA board, Camera module, MATLAB/Simulink',
                'max_teams' => 1,
                'recommended_team_size' => 2,
                'difficulty_level' => 'advanced',
                'department' => 'electronique',
                'status' => 'published',
            ],
            [
                'title' => 'Wireless Sensor Network for Environmental Monitoring',
                'description' => 'Develop a comprehensive wireless sensor network system for environmental monitoring including air quality, weather conditions, and pollution levels.

System components:
- Multiple sensor nodes with different sensors
- Wireless communication protocols (LoRaWAN, Zigbee)
- Data aggregation and processing
- Web-based monitoring dashboard
- Mobile application for alerts
- Solar-powered sensor nodes
- Data logging and storage
- Real-time alerts and notifications
- Network topology optimization
- Long-range communication capabilities

The system should be energy-efficient and capable of operating in outdoor conditions.',
                'keywords' => ['WSN', 'IoT', 'Environmental Monitoring', 'LoRaWAN', 'Arduino', 'Sensors', 'Energy Harvesting', 'Data Analytics'],
                'required_tools' => 'Arduino/ESP32, LoRa modules, Environmental sensors, Solar panels, Web technologies',
                'max_teams' => 1,
                'recommended_team_size' => 3,
                'difficulty_level' => 'intermediate',
                'department' => 'electronique',
                'status' => 'published',
            ]
        ];

        foreach ($subjects as $index => $subjectData) {
            $supervisor = $teachers->get($index % $teachers->count());

            Subject::create([
                'title' => $subjectData['title'],
                'description' => $subjectData['description'],
                'keywords' => $subjectData['keywords'],
                'required_tools' => $subjectData['required_tools'],
                'max_teams' => $subjectData['max_teams'],
                'supervisor_id' => $supervisor->id,
                'status' => $subjectData['status'],
                'validated_at' => $subjectData['status'] === 'published' ? now()->subDays(rand(1, 10)) : null,
            ]);
        }
    }

    private function createMechanicalSubjects()
    {
        $teachers = User::where('department', 'informatique')->where('role', 'teacher')->get();

        if ($teachers->isEmpty()) {
            return; // Skip if no teachers found
        }

        $subjects = [
            [
                'title' => 'Solar-Powered Water Pumping System Design',
                'description' => 'Design and prototype a solar-powered water pumping system for rural applications, focusing on efficiency, cost-effectiveness, and sustainability.

Project scope:
- Solar panel sizing and optimization
- Pump selection and performance analysis
- Control system design
- Water storage solutions
- Performance monitoring system
- Economic feasibility analysis
- Environmental impact assessment
- Prototype construction and testing
- Maintenance procedures development
- User training materials

The system should be suitable for deployment in remote areas with limited infrastructure.',
                'keywords' => ['Solar Energy', 'Water Pumping', 'Renewable Energy', 'Sustainable Design', 'Rural Technology', 'CAD', 'Efficiency'],
                'required_tools' => 'SolidWorks/AutoCAD, MATLAB/Simulink, Solar panels, Water pumps, Microcontrollers',
                'max_teams' => 1,
                'recommended_team_size' => 2,
                'difficulty_level' => 'intermediate',
                'department' => 'mecanique',
                'status' => 'approved',
            ]
        ];

        foreach ($subjects as $index => $subjectData) {
            $supervisor = $teachers->get($index % $teachers->count());

            Subject::create([
                'title' => $subjectData['title'],
                'description' => $subjectData['description'],
                'keywords' => $subjectData['keywords'],
                'required_tools' => $subjectData['required_tools'],
                'max_teams' => $subjectData['max_teams'],
                'supervisor_id' => $supervisor->id,
                'status' => $subjectData['status'],
                'validated_at' => now()->subDays(rand(1, 15)),
            ]);
        }
    }
}