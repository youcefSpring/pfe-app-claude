<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Experience;
use App\Models\Education;
use App\Models\Skill;
use App\Models\Project;
use App\Models\Publication;
use App\Models\BlogPost;
use Illuminate\Support\Facades\Hash;

class ProfileDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or update the main user profile
        $user = User::updateOrCreate(
            ['email' => 'y.brnabderrezak@univ-boumerdes.dz'],
            [
            'name' => 'Benabderrezak Youcef',
            'email' => 'y.brnabderrezak@univ-boumerdes.dzm',
            'password' => Hash::make('Youcef1997@@@'),
            'role' => 'admin',
            'status' => 'active',
            'title' => 'PhD Researcher & Senior PHP Laravel Developer',
            'bio' => 'I am a dedicated professional with over +4.5 years of experience as a PHP Laravel developer and a researcher in cybersecurity. As a developer, I specialize in building scalable web applications, integrating APIs, and crafting responsive, user-friendly interfaces using Laravel, Vue.js. Currently pursuing a PhD in cybersecurity, my research focuses on enhancing the security of drone systems through secure elements.',
            'department' => 'Computer Sciences Department',
            'phone' => '+213 697 898 885',
            'specializations' => 'Web Development, Cybersecurity, Drone Security, Cryptographic Techniques',
            'linkedin' => 'https://linkedin.com/in/youcef-benabderrezak-9974191a7/',
            'github' => 'https://github.com/youcefSpring',
            'google_scholar' => 'https://scholar.google.com/citations?user=youcef benaberrezak',
            'researchgate' => 'https://www.researchgate.net/profile/Youcef-Benabderrezak',
            ]
        );

        // Clear existing data for this user
        $user->experiences()->delete();
        $user->education()->delete();
        $user->projects()->delete();
        $user->publications()->delete();
        $user->blogPosts()->delete();

        // Create work experiences
        $experiences = [
            [
                'position' => 'PHP Laravel Developer',
                'company' => 'Charikatec - Itihad Group',
                'description' => 'The work involves developing a dynamic and interactive dashboard using PHP Laravel and Vue.js with Vuetify and Chart.js. This includes backend API development, implementing authentication and authorization, managing data visualization with Vuetify components and Chart.js charts, optimizing user interfaces for responsiveness, and ensuring seamless integration between frontend and backend.',
                'start_date' => '2024-08-01',
                'end_date' => null,
                'is_current' => true,
                'location' => 'Algeria',
                'employment_type' => 'full-time',
            ],
            [
                'position' => 'PHP Laravel Developer',
                'company' => 'Handelp Agency',
                'description' => 'The work involves developing an influencer marketing platform named Vaguy for Handelp Agency using PHP Laravel. Responsibilities include creating robust backend systems, implementing features for influencer and brand collaboration, managing data securely, integrating payment solutions, and delivering a user-friendly, scalable platform tailored to the agency\'s needs.',
                'start_date' => '2024-02-01',
                'end_date' => '2024-07-31',
                'is_current' => false,
                'location' => 'Remote',
                'employment_type' => 'contract',
            ],
            [
                'position' => 'PHP Laravel Developer',
                'company' => 'Wegoo',
                'description' => 'The work involves integrating delivery companies\' APIs into the Wegoo SaaS e-commerce system using PHP Laravel.',
                'start_date' => '2023-06-01',
                'end_date' => '2023-07-31',
                'is_current' => false,
                'location' => 'Remote',
                'employment_type' => 'contract',
            ],
            [
                'position' => 'Full-stack Web Developer',
                'company' => 'Coup de pouce permis',
                'description' => 'The work involves developing a landing page with an integrated dashboard for Coup de Pouce Permis Paris using PHP Laravel.',
                'start_date' => '2021-03-01',
                'end_date' => '2021-04-30',
                'is_current' => false,
                'location' => 'Paris, France',
                'employment_type' => 'freelance',
            ],
        ];

        foreach ($experiences as $experience) {
            Experience::create(array_merge($experience, ['user_id' => $user->id]));
        }

        // Create education
        $educations = [
            [
                'degree' => 'PhD in Cybersecurity',
                'field_of_study' => 'Computer Sciences',
                'institution' => 'University of Boumerdes',
                'description' => 'As a PhD student and researcher in Cybersecurity, my focus is on securing drones using secure elements. My research explores advanced cryptographic techniques, secure communication protocols, and hardware-based security solutions to enhance the integrity, confidentiality, and resilience of drone systems against cyber threats.',
                'start_date' => '2022-04-01',
                'end_date' => null,
                'is_current' => true,
                'location' => 'Boumerdes, Algeria',
            ],
            [
                'degree' => 'Master\'s Degree',
                'field_of_study' => 'Information Technology',
                'institution' => 'University of Boumerdes',
                'description' => 'I hold a Master\'s degree in Information Technology, with a strong foundation in software development, data management, and network security. My studies focused on advanced technologies, system architecture, and problem-solving techniques to design and implement efficient IT solutions.',
                'start_date' => '2018-09-01',
                'end_date' => '2020-12-31',
                'is_current' => false,
                'location' => 'Boumerdes, Algeria',
            ],
        ];

        foreach ($educations as $education) {
            Education::create(array_merge($education, ['user_id' => $user->id]));
        }

        // Create skills
        $skills = [
            ['name' => 'PHP', 'proficiency_level' => 5, 'category' => 'programming', 'years_experience' => 4, 'is_featured' => true],
            ['name' => 'Laravel', 'proficiency_level' => 5, 'category' => 'framework', 'years_experience' => 4, 'is_featured' => true],
            ['name' => 'Vue.js', 'proficiency_level' => 4, 'category' => 'framework', 'years_experience' => 3, 'is_featured' => true],
            ['name' => 'JavaScript', 'proficiency_level' => 4, 'category' => 'programming', 'years_experience' => 4, 'is_featured' => true],
            ['name' => 'jQuery', 'proficiency_level' => 4, 'category' => 'framework', 'years_experience' => 3, 'is_featured' => false],
            ['name' => 'HTML', 'proficiency_level' => 5, 'category' => 'programming', 'years_experience' => 4, 'is_featured' => false],
            ['name' => 'CSS', 'proficiency_level' => 4, 'category' => 'programming', 'years_experience' => 4, 'is_featured' => false],
            ['name' => 'Ajax', 'proficiency_level' => 4, 'category' => 'programming', 'years_experience' => 3, 'is_featured' => false],
            ['name' => 'Git', 'proficiency_level' => 4, 'category' => 'tool', 'years_experience' => 4, 'is_featured' => true],
            ['name' => 'GitHub', 'proficiency_level' => 4, 'category' => 'tool', 'years_experience' => 4, 'is_featured' => false],
            ['name' => 'Cybersecurity', 'proficiency_level' => 5, 'category' => 'soft_skill', 'years_experience' => 3, 'is_featured' => true],
            ['name' => 'Cryptography', 'proficiency_level' => 4, 'category' => 'soft_skill', 'years_experience' => 3, 'is_featured' => true],
            ['name' => 'Drone Security', 'proficiency_level' => 5, 'category' => 'soft_skill', 'years_experience' => 3, 'is_featured' => true],
        ];

        // Clear existing skills (with foreign key constraints)
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Skill::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        foreach ($skills as $skill) {
            Skill::create($skill);
        }

        // Create sample projects
        $projects = [
            [
                'title' => 'Vaguy - Influencer Marketing Platform',
                'description' => 'A comprehensive influencer marketing platform built with Laravel that connects brands with influencers. Features include user authentication, campaign management, payment integration, analytics dashboard, and real-time messaging system.',
                'technologies_used' => 'Laravel, Vue.js, MySQL, Payment APIs',
                'live_demo_url' => 'https://vaguy.example.com',
                'source_code_url' => 'https://github.com/yourprofile/vaguy',
                'status' => 'featured',
                'date_completed' => '2024-07-31',
                'user_id' => $user->id,
            ],
            [
                'title' => 'Wegoo E-commerce Integration',
                'description' => 'API integration project for Wegoo SaaS e-commerce platform, connecting multiple delivery company APIs to provide seamless shipping solutions for online stores.',
                'technologies_used' => 'Laravel, REST APIs, JSON, Third-party Integrations',
                'live_demo_url' => null,
                'source_code_url' => null,
                'status' => 'featured',
                'date_completed' => '2023-07-31',
                'user_id' => $user->id,
            ],
            [
                'title' => 'Drone Security Research Platform',
                'description' => 'Research platform for testing and implementing secure communication protocols for drone systems. Includes cryptographic implementations, secure element integration, and vulnerability assessment tools.',
                'technologies_used' => 'Python, C++, Cryptographic Libraries, Hardware Security',
                'live_demo_url' => null,
                'source_code_url' => 'https://github.com/yourprofile/drone-security',
                'status' => 'active',
                'date_completed' => null,
                'user_id' => $user->id,
            ],
        ];

        foreach ($projects as $project) {
            Project::create($project);
        }

        // Create sample publications
        $publications = [
            [
                'title' => 'Enhanced Cryptographic Techniques for Drone Security Systems',
                'abstract' => 'This paper presents novel cryptographic approaches to secure drone communication channels against cyber threats. We propose a hybrid encryption method that combines hardware-based secure elements with advanced cryptographic protocols.',
                'year' => 2024,
                'journal' => 'International Journal of Cybersecurity, Vol. 15, Issue 3, pp. 45-62',
                'authors' => 'Your Name, Co-Author Name',
                'external_link' => 'https://doi.org/10.1000/ijcs.2024.15.3.45',
                'user_id' => $user->id,
            ],
            [
                'title' => 'Secure Element Integration in Unmanned Aerial Systems',
                'abstract' => 'An investigation into the implementation of secure elements in drone systems to enhance security posture and protect against hardware-based attacks.',
                'year' => 2024,
                'journal' => 'IEEE Transactions on Aerospace Security, Vol. 8, Issue 2, pp. 112-128',
                'authors' => 'Your Name, Research Team',
                'external_link' => 'https://doi.org/10.1109/tas.2024.8.2.112',
                'user_id' => $user->id,
            ],
        ];

        foreach ($publications as $publication) {
            Publication::create($publication);
        }

        // Create sample blog posts
        $blogPosts = [
            [
                'title' => 'Building Scalable Laravel Applications: Best Practices',
                'content' => 'In this post, I share my experience building scalable Laravel applications over the past 4 years. We\'ll cover architecture patterns, database optimization, caching strategies, and deployment best practices. Laravel has proven to be an excellent framework for building enterprise-level applications when proper patterns are followed.',
                'status' => 'published',
                'published_at' => '2024-09-15',
                'user_id' => $user->id,
            ],
            [
                'title' => 'The Future of Drone Security: Challenges and Solutions',
                'content' => 'As drones become more prevalent in various industries, ensuring their security becomes paramount. This article explores current challenges in drone security and presents innovative solutions. From hardware-based security elements to advanced cryptographic protocols, the future of drone security relies on multi-layered approaches.',
                'status' => 'published',
                'published_at' => '2024-08-28',
                'user_id' => $user->id,
            ],
        ];

        foreach ($blogPosts as $blogPost) {
            BlogPost::create($blogPost);
        }
    }
}
