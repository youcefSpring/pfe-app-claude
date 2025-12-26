<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $teacher->name ?? 'Professional Portfolio' }} - {{ $teacher->title ?? 'Developer & Researcher' }}</title>

    <!-- Meta Description -->
    <meta name="description" content="{{ Str::limit($teacher->bio ?? 'Professional portfolio showcasing development and research work', 160) }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Compiled CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Space Grotesk', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900 antialiased">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm fixed top-0 left-0 right-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex-shrink-0">
                    <h1 class="text-xl font-bold text-slate-900">{{ $teacher->name ?? 'Portfolio' }}</h1>
                </div>

                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a href="#about" class="text-slate-600 hover:text-slate-900 px-3 py-2 rounded-md text-sm font-medium">About</a>
                        <a href="#experience" class="text-slate-600 hover:text-slate-900 px-3 py-2 rounded-md text-sm font-medium">Experience</a>
                        <a href="#skills" class="text-slate-600 hover:text-slate-900 px-3 py-2 rounded-md text-sm font-medium">Skills</a>
                        <a href="#projects" class="text-slate-600 hover:text-slate-900 px-3 py-2 rounded-md text-sm font-medium">Projects</a>
                        <a href="#publications" class="text-slate-600 hover:text-slate-900 px-3 py-2 rounded-md text-sm font-medium">Publications</a>
                        <a href="#blog" class="text-slate-600 hover:text-slate-900 px-3 py-2 rounded-md text-sm font-medium">Blog</a>
                        @auth
                            <a href="{{ route('admin.dashboard') }}" class="bg-purple-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-purple-700">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="bg-purple-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-purple-700">Login</a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-24 pb-12 bg-gradient-to-br from-purple-50 to-blue-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl md:text-6xl font-bold text-slate-900 mb-6">
                    {{ $teacher->name ?? 'Professional Developer' }}
                </h1>
                <h2 class="text-xl md:text-2xl text-slate-600 mb-8">
                    {{ $teacher->title ?? 'Full-Stack Developer & Researcher' }}
                </h2>
                @if($teacher && $teacher->bio)
                    <p class="text-lg text-slate-700 max-w-3xl mx-auto mb-8">
                        {{ $teacher->bio }}
                    </p>
                @endif

                <!-- Social Links -->
                @if($teacher)
                    <div class="flex justify-center space-x-4 mb-8">
                        @if($teacher->linkedin)
                            <a href="{{ $teacher->linkedin }}" target="_blank" class="text-slate-600 hover:text-blue-600 text-2xl">
                                <i class="fab fa-linkedin"></i>
                            </a>
                        @endif
                        @if($teacher->github)
                            <a href="{{ $teacher->github }}" target="_blank" class="text-slate-600 hover:text-slate-900 text-2xl">
                                <i class="fab fa-github"></i>
                            </a>
                        @endif
                        @if($teacher->google_scholar)
                            <a href="{{ $teacher->google_scholar }}" target="_blank" class="text-slate-600 hover:text-red-600 text-2xl">
                                <i class="fas fa-graduation-cap"></i>
                            </a>
                        @endif
                        @if($teacher->researchgate)
                            <a href="{{ $teacher->researchgate }}" target="_blank" class="text-slate-600 hover:text-green-600 text-2xl">
                                <i class="fab fa-researchgate"></i>
                            </a>
                        @endif
                    </div>
                @endif

                <div class="flex justify-center space-x-4">
                    <a href="#projects" class="bg-purple-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-purple-700 transition-colors">
                        View My Work
                    </a>
                    <a href="#contact" class="border-2 border-purple-600 text-purple-600 px-8 py-3 rounded-lg font-semibold hover:bg-purple-600 hover:text-white transition-colors">
                        Get In Touch
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4">About Me</h2>
                <p class="text-lg text-slate-600 max-w-3xl mx-auto">
                    Combining technical expertise with research excellence
                </p>
            </div>

            @if($teacher && $teacher->education && $teacher->education->count() > 0)
                <div class="grid md:grid-cols-2 gap-8">
                    <!-- Education -->
                    <div>
                        <h3 class="text-2xl font-semibold text-slate-900 mb-6">Education</h3>
                        <div class="space-y-6">
                            @foreach($teacher->education as $education)
                                <div class="border-l-4 border-purple-600 pl-6">
                                    <h4 class="text-lg font-semibold text-slate-900">{{ $education->degree }}</h4>
                                    <p class="text-purple-600 font-medium">{{ $education->field_of_study }}</p>
                                    <p class="text-slate-600">{{ $education->institution }}</p>
                                    <p class="text-sm text-slate-500">
                                        {{ $education->start_date->format('M Y') }} -
                                        {{ $education->is_current ? 'Present' : $education->end_date->format('M Y') }}
                                    </p>
                                    @if($education->description)
                                        <p class="text-slate-700 mt-2">{{ $education->description }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div>
                        <h3 class="text-2xl font-semibold text-slate-900 mb-6">Quick Stats</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-slate-50 rounded-lg p-6 text-center">
                                <div class="text-3xl font-bold text-purple-600">4+</div>
                                <div class="text-slate-600">Years Experience</div>
                            </div>
                            <div class="bg-slate-50 rounded-lg p-6 text-center">
                                <div class="text-3xl font-bold text-purple-600">{{ $featuredProjects->count() }}</div>
                                <div class="text-slate-600">Featured Projects</div>
                            </div>
                            <div class="bg-slate-50 rounded-lg p-6 text-center">
                                <div class="text-3xl font-bold text-purple-600">{{ $latestPublications->count() }}</div>
                                <div class="text-slate-600">Publications</div>
                            </div>
                            <div class="bg-slate-50 rounded-lg p-6 text-center">
                                <div class="text-3xl font-bold text-purple-600">{{ $featuredSkills->count() }}</div>
                                <div class="text-slate-600">Core Skills</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <!-- Experience Section -->
    @if($teacher && $teacher->experiences && $teacher->experiences->count() > 0)
    <section id="experience" class="py-16 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4">Professional Experience</h2>
                <p class="text-lg text-slate-600">My journey in software development and research</p>
            </div>

            <div class="space-y-8">
                @foreach($teacher->experiences as $experience)
                    <div class="bg-white rounded-lg shadow-md p-8">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
                            <div>
                                <h3 class="text-xl font-semibold text-slate-900">{{ $experience->position }}</h3>
                                <p class="text-purple-600 font-medium">{{ $experience->company }}</p>
                                @if($experience->location)
                                    <p class="text-slate-600">{{ $experience->location }}</p>
                                @endif
                            </div>
                            <div class="text-slate-500 mt-2 md:mt-0">
                                {{ $experience->start_date->format('M Y') }} -
                                {{ $experience->is_current ? 'Present' : $experience->end_date->format('M Y') }}
                                @if($experience->employment_type)
                                    <span class="ml-2 px-2 py-1 bg-slate-100 rounded text-xs">{{ ucfirst($experience->employment_type) }}</span>
                                @endif
                            </div>
                        </div>
                        <p class="text-slate-700">{{ $experience->description }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Skills Section -->
    @if($featuredSkills && $featuredSkills->count() > 0)
    <section id="skills" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4">Skills & Technologies</h2>
                <p class="text-lg text-slate-600">Technologies I work with</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach($featuredSkills as $skill)
                    <div class="bg-slate-50 rounded-lg p-6 text-center hover:shadow-md transition-shadow">
                        <h3 class="font-semibold text-slate-900 mb-2">{{ $skill->name }}</h3>
                        <div class="text-sm text-slate-600 mb-3">{{ $skill->proficiency_label }}</div>
                        <div class="w-full bg-slate-200 rounded-full h-2">
                            <div class="bg-purple-600 h-2 rounded-full" style="width: {{ ($skill->proficiency_level / 5) * 100 }}%"></div>
                        </div>
                        @if($skill->years_experience)
                            <div class="text-xs text-slate-500 mt-2">{{ $skill->years_experience }} years</div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Projects Section -->
    @if($featuredProjects && $featuredProjects->count() > 0)
    <section id="projects" class="py-16 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4">Featured Projects</h2>
                <p class="text-lg text-slate-600">Some of my recent work</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($featuredProjects as $project)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-slate-900 mb-3">{{ $project->title }}</h3>
                            <p class="text-slate-700 mb-4">{{ Str::limit($project->description, 120) }}</p>

                            @if($project->technologies_used)
                                <div class="flex flex-wrap gap-2 mb-4">
                                    @foreach(explode(',', $project->technologies_used) as $tech)
                                        <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs">{{ trim($tech) }}</span>
                                    @endforeach
                                </div>
                            @endif

                            <div class="flex space-x-4">
                                @if($project->live_demo_url)
                                    <a href="{{ $project->live_demo_url }}" target="_blank"
                                       class="text-purple-600 hover:text-purple-800 text-sm font-medium">
                                        <i class="fas fa-external-link-alt mr-1"></i>Live Demo
                                    </a>
                                @endif
                                @if($project->source_code_url)
                                    <a href="{{ $project->source_code_url }}" target="_blank"
                                       class="text-slate-600 hover:text-slate-800 text-sm font-medium">
                                        <i class="fab fa-github mr-1"></i>Source Code
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Publications Section -->
    @if($latestPublications && $latestPublications->count() > 0)
    <section id="publications" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4">Recent Publications</h2>
                <p class="text-lg text-slate-600">My research contributions</p>
            </div>

            <div class="space-y-6">
                @foreach($latestPublications as $publication)
                    <div class="bg-slate-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-slate-900 mb-2">{{ $publication->title }}</h3>
                        <p class="text-purple-600 font-medium mb-2">{{ $publication->authors }} ({{ $publication->year }})</p>
                        <p class="text-slate-600 mb-3">{{ $publication->journal }}</p>
                        <p class="text-slate-700 mb-4">{{ $publication->abstract }}</p>
                        @if($publication->external_link)
                            <a href="{{ $publication->external_link }}" target="_blank"
                               class="text-purple-600 hover:text-purple-800 text-sm font-medium">
                                <i class="fas fa-external-link-alt mr-1"></i>Read Publication
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Blog Section -->
    @if($latestPosts && $latestPosts->count() > 0)
    <section id="blog" class="py-16 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4">Latest Blog Posts</h2>
                <p class="text-lg text-slate-600">Thoughts and insights</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($latestPosts as $post)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-slate-900 mb-3">{{ $post->title }}</h3>
                            <p class="text-slate-700 mb-4">{{ $post->excerpt }}</p>
                            <div class="flex justify-between items-center text-sm text-slate-500">
                                <span>{{ $post->published_at->format('M j, Y') }}</span>
                                <span>{{ $post->reading_time }} min read</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Contact Section -->
    <section id="contact" class="py-16 bg-purple-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Let's Work Together</h2>
            <p class="text-xl mb-8 text-purple-100">
                Interested in collaboration or have a project in mind?
            </p>
            @if($teacher && $teacher->phone)
                <div class="mb-6">
                    <p class="text-purple-100">
                        <i class="fas fa-phone mr-2"></i>{{ $teacher->phone }}
                    </p>
                </div>
            @endif
            <div class="flex justify-center space-x-4">
                @if($teacher && $teacher->linkedin)
                    <a href="{{ $teacher->linkedin }}" target="_blank"
                       class="bg-white text-purple-600 px-6 py-3 rounded-lg font-semibold hover:bg-purple-50 transition-colors">
                        Connect on LinkedIn
                    </a>
                @endif
                @if($teacher && $teacher->github)
                    <a href="{{ $teacher->github }}" target="_blank"
                       class="border-2 border-white text-white px-6 py-3 rounded-lg font-semibold hover:bg-white hover:text-purple-600 transition-colors">
                        View GitHub
                    </a>
                @endif
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-900 text-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p>&copy; {{ date('Y') }} {{ $teacher->name ?? 'Portfolio' }}. All rights reserved.</p>
            @if($teacher && $teacher->specializations)
                <p class="text-slate-400 mt-2">{{ $teacher->specializations }}</p>
            @endif
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <button id="scrollTop" class="fixed bottom-8 right-8 bg-purple-600 text-white p-3 rounded-full shadow-lg hover:bg-purple-700 transition-colors hidden">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Scripts -->
    <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Scroll to top button
        const scrollTopBtn = document.getElementById('scrollTop');
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                scrollTopBtn.classList.remove('hidden');
            } else {
                scrollTopBtn.classList.add('hidden');
            }
        });

        scrollTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Navbar background on scroll
        window.addEventListener('scroll', () => {
            const nav = document.querySelector('nav');
            if (window.pageYOffset > 50) {
                nav.classList.add('bg-white/90', 'backdrop-blur-sm');
            } else {
                nav.classList.remove('bg-white/90', 'backdrop-blur-sm');
            }
        });
    </script>
</body>
</html>