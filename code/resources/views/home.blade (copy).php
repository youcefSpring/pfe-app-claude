@extends('layouts.app')

@section('title', 'Home')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-3">Welcome to My Academic Portfolio</h1>
                <p class="lead mb-4">
                    Exploring the intersection of teaching, research, and technology.
                    Sharing knowledge through education, publications, and innovative projects.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('about') }}" class="btn btn-light btn-lg">
                        <i class="bi bi-person me-2"></i>Learn More About Me
                    </a>
                    <a href="{{ route('contact.show') }}" class="btn btn-outline-light btn-lg">
                        <i class="bi bi-envelope me-2"></i>Get In Touch
                    </a>
                </div>
            </div>
            <div class="col-lg-4 text-center">
                <div class="position-relative">
                    <i class="bi bi-mortarboard" style="font-size: 8rem; opacity: 0.1;"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quick Stats -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-3 col-sm-6">
                <div class="stats-card text-center h-100">
                    <i class="bi bi-book text-primary mb-3" style="font-size: 2.5rem;"></i>
                    <h3 class="h4 mb-2">{{ $stats['courses'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">Courses Taught</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stats-card text-center h-100">
                    <i class="bi bi-code-slash text-primary mb-3" style="font-size: 2.5rem;"></i>
                    <h3 class="h4 mb-2">{{ $stats['projects'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">Projects Completed</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stats-card text-center h-100">
                    <i class="bi bi-journal-text text-primary mb-3" style="font-size: 2.5rem;"></i>
                    <h3 class="h4 mb-2">{{ $stats['publications'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">Publications</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stats-card text-center h-100">
                    <i class="bi bi-pencil-square text-primary mb-3" style="font-size: 2.5rem;"></i>
                    <h3 class="h4 mb-2">{{ $stats['blog_posts'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">Blog Articles</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Content -->
<section class="py-5 bg-white">
    <div class="container">
        <h2 class="text-center mb-5">Featured Content</h2>

        <div class="row g-4">
            <!-- Recent Courses -->
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-book me-2"></i>Recent Courses
                        </h5>
                    </div>
                    <div class="card-body">
                        @forelse($recentCourses ?? [] as $course)
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h6 class="mb-1">
                                        <a href="{{ route('courses.show', $course->slug) }}" class="text-decoration-none">
                                            {{ $course->title }}
                                        </a>
                                    </h6>
                                    <small class="text-muted">{{ $course->start_date->format('M Y') }}</small>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted">No courses available yet.</p>
                        @endforelse

                        <div class="text-center mt-3">
                            <a href="{{ route('courses.index') }}" class="btn btn-outline-primary btn-sm">
                                View All Courses <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Projects -->
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-code-slash me-2"></i>Recent Projects
                        </h5>
                    </div>
                    <div class="card-body">
                        @forelse($recentProjects ?? [] as $project)
                            <div class="mb-3">
                                <h6 class="mb-1">
                                    <a href="{{ route('projects.show', $project->slug) }}" class="text-decoration-none">
                                        {{ $project->title }}
                                    </a>
                                </h6>
                                <p class="text-muted small mb-1">{{ Str::limit($project->description, 80) }}</p>
                                <small class="text-muted">{{ $project->date_completed?->format('M Y') ?? 'In Progress' }}</small>
                            </div>
                        @empty
                            <p class="text-muted">No projects available yet.</p>
                        @endforelse

                        <div class="text-center mt-3">
                            <a href="{{ route('projects.index') }}" class="btn btn-outline-success btn-sm">
                                View All Projects <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Blog Posts -->
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header bg-warning text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-pencil-square me-2"></i>Recent Posts
                        </h5>
                    </div>
                    <div class="card-body">
                        @forelse($recentPosts ?? [] as $post)
                            <div class="mb-3">
                                <h6 class="mb-1">
                                    <a href="{{ route('blog.show', $post->slug) }}" class="text-decoration-none">
                                        {{ $post->title }}
                                    </a>
                                </h6>
                                <p class="text-muted small mb-1">{{ Str::limit(strip_tags($post->content), 80) }}</p>
                                <small class="text-muted">{{ $post->created_at->format('M j, Y') }}</small>
                            </div>
                        @empty
                            <p class="text-muted">No blog posts available yet.</p>
                        @endforelse

                        <div class="text-center mt-3">
                            <a href="{{ route('blog.index') }}" class="btn btn-outline-warning btn-sm">
                                Read All Posts <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <h2 class="mb-3">Ready to Connect?</h2>
                <p class="lead text-muted mb-4">
                    Whether you're interested in collaboration, have questions about my work,
                    or just want to connect, I'd love to hear from you.
                </p>
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <a href="{{ route('contact.show') }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-envelope me-2"></i>Send a Message
                    </a>
                    <a href="{{ route('download-cv') }}" class="btn btn-outline-primary btn-lg">
                        <i class="bi bi-download me-2"></i>Download CV
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('styles')
<style>
    .stats-card {
        background: white;
        border-radius: 0.75rem;
        padding: 2rem 1.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .card {
        transition: transform 0.3s ease;
    }

    .card:hover {
        transform: translateY(-2px);
    }

    .hero-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        position: relative;
        overflow: hidden;
    }

    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><radialGradient id="a"><stop offset="0" stop-color="%23ffffff" stop-opacity="0.1"/><stop offset="1" stop-color="%23ffffff" stop-opacity="0"/></radialGradient></defs><circle cx="500" cy="500" r="400" fill="url(%23a)"/></svg>');
        opacity: 0.1;
    }

    .hero-section .container {
        position: relative;
        z-index: 1;
    }
</style>
@endsection