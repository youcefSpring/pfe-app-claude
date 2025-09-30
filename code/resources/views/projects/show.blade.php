@extends('layouts.app')

@section('title', $project->title)

@section('content')
<!-- Project Header -->
<section class="py-5 bg-primary text-white">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb text-white-50">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white-50">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('projects.index') }}" class="text-white-50">Projects</a></li>
                <li class="breadcrumb-item active text-white">{{ $project->title }}</li>
            </ol>
        </nav>

        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-5 fw-bold mb-3">{{ $project->title }}</h1>
                <p class="lead mb-3">{{ $project->description }}</p>

                <div class="d-flex flex-wrap gap-2 mb-4">
                    @if($project->status === 'completed')
                        <span class="badge bg-success fs-6">Completed</span>
                    @elseif($project->status === 'active')
                        <span class="badge bg-light text-dark fs-6">Active Development</span>
                    @else
                        <span class="badge bg-warning fs-6">In Progress</span>
                    @endif

                    @if($project->date_completed)
                        <span class="badge bg-info fs-6">{{ $project->date_completed->format('M Y') }}</span>
                    @endif
                </div>

                <div class="d-flex flex-wrap gap-3">
                    @if($project->live_demo_url)
                        <a href="{{ $project->live_demo_url }}" target="_blank" class="btn btn-light btn-lg">
                            <i class="bi bi-box-arrow-up-right me-2"></i>View Live Demo
                        </a>
                    @endif

                    @if($project->source_code_url)
                        <a href="{{ $project->source_code_url }}" target="_blank" class="btn btn-outline-light btn-lg">
                            <i class="bi bi-github me-2"></i>Source Code
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Project Details -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Project Images -->
                @if($project->images && count($project->images) > 0)
                    <div class="card mb-4">
                        <div class="card-body p-0">
                            @if(count($project->images) === 1)
                                <img src="{{ asset('storage/' . $project->images[0]) }}"
                                     class="img-fluid rounded"
                                     alt="{{ $project->title }}"
                                     style="width: 100%; max-height: 400px; object-fit: cover;">
                            @else
                                <div id="projectCarousel" class="carousel slide" data-bs-ride="carousel">
                                    <div class="carousel-indicators">
                                        @foreach($project->images as $index => $image)
                                            <button type="button"
                                                    data-bs-target="#projectCarousel"
                                                    data-bs-slide-to="{{ $index }}"
                                                    class="{{ $index === 0 ? 'active' : '' }}"></button>
                                        @endforeach
                                    </div>
                                    <div class="carousel-inner">
                                        @foreach($project->images as $index => $image)
                                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                                <img src="{{ asset('storage/' . $image) }}"
                                                     class="d-block w-100"
                                                     alt="{{ $project->title }}"
                                                     style="height: 400px; object-fit: cover;">
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="carousel-control-prev" type="button" data-bs-target="#projectCarousel" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon"></span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#projectCarousel" data-bs-slide="next">
                                        <span class="carousel-control-next-icon"></span>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Project Overview -->
                @if($project->content)
                    <div class="card mb-4">
                        <div class="card-body p-4">
                            <h2 class="h4 mb-3">
                                <i class="bi bi-file-text text-primary me-2"></i>
                                Project Overview
                            </h2>
                            <div class="project-content">
                                {!! nl2br(e($project->content)) !!}
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Features -->
                @if($project->features)
                    <div class="card mb-4">
                        <div class="card-body p-4">
                            <h2 class="h4 mb-3">
                                <i class="bi bi-stars text-primary me-2"></i>
                                Key Features
                            </h2>
                            <div class="features-content">
                                {!! nl2br(e($project->features)) !!}
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Challenges -->
                @if($project->challenges)
                    <div class="card mb-4">
                        <div class="card-body p-4">
                            <h2 class="h4 mb-3">
                                <i class="bi bi-puzzle text-primary me-2"></i>
                                Challenges & Solutions
                            </h2>
                            <div class="challenges-content">
                                {!! nl2br(e($project->challenges)) !!}
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Lessons Learned -->
                @if($project->lessons_learned)
                    <div class="card mb-4">
                        <div class="card-body p-4">
                            <h2 class="h4 mb-3">
                                <i class="bi bi-lightbulb text-primary me-2"></i>
                                Lessons Learned
                            </h2>
                            <div class="lessons-content">
                                {!! nl2br(e($project->lessons_learned)) !!}
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Contact -->
                <div class="card mb-4">
                    <div class="card-body p-4">
                        <h2 class="h4 mb-3">
                            <i class="bi bi-chat-dots text-primary me-2"></i>
                            Interested in This Project?
                        </h2>
                        <p class="text-muted mb-3">
                            Have questions about this project or want to collaborate on something similar?
                            I'd love to hear from you.
                        </p>
                        <a href="{{ route('contact.show') }}" class="btn btn-primary">
                            <i class="bi bi-envelope me-2"></i>Get In Touch
                        </a>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Project Info -->
                <div class="card mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-3">
                            <i class="bi bi-info-circle text-primary me-2"></i>
                            Project Information
                        </h5>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Status:</span>
                            @if($project->status === 'completed')
                                <span class="badge bg-success">Completed</span>
                            @elseif($project->status === 'active')
                                <span class="badge bg-primary">Active</span>
                            @else
                                <span class="badge bg-warning">In Progress</span>
                            @endif
                        </div>

                        @if($project->date_started)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Started:</span>
                                <span class="fw-medium">{{ $project->date_started->format('M j, Y') }}</span>
                            </div>
                        @endif

                        @if($project->date_completed)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Completed:</span>
                                <span class="fw-medium">{{ $project->date_completed->format('M j, Y') }}</span>
                            </div>
                        @endif

                        @if($project->duration_months)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Duration:</span>
                                <span class="fw-medium">{{ $project->duration_months }} month(s)</span>
                            </div>
                        @endif

                        @if($project->live_demo_url || $project->source_code_url)
                            <hr class="my-3">
                            <div class="d-grid gap-2">
                                @if($project->live_demo_url)
                                    <a href="{{ $project->live_demo_url }}" target="_blank" class="btn btn-primary">
                                        <i class="bi bi-box-arrow-up-right me-1"></i>
                                        Live Demo
                                    </a>
                                @endif

                                @if($project->source_code_url)
                                    <a href="{{ $project->source_code_url }}" target="_blank" class="btn btn-outline-secondary">
                                        <i class="bi bi-github me-1"></i>
                                        Source Code
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Technologies -->
                @if($project->technologies_used)
                    <div class="card mb-4">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-3">
                                <i class="bi bi-tools text-primary me-2"></i>
                                Technologies Used
                            </h5>

                            <div class="d-flex flex-wrap gap-2">
                                @foreach(explode(',', $project->technologies_used) as $tech)
                                    <span class="badge bg-light text-dark border">{{ trim($tech) }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Tags -->
                @if($project->tags && $project->tags->count() > 0)
                    <div class="card mb-4">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-3">
                                <i class="bi bi-tags text-primary me-2"></i>
                                Tags
                            </h5>

                            <div class="d-flex flex-wrap gap-2">
                                @foreach($project->tags as $tag)
                                    <span class="badge bg-secondary">{{ $tag->name }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Developer -->
                <div class="card mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-3">
                            <i class="bi bi-person-badge text-primary me-2"></i>
                            Developer
                        </h5>

                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-person-circle text-muted me-3" style="font-size: 2rem;"></i>
                            <div>
                                <h6 class="mb-0">{{ $project->user->name ?? 'Developer' }}</h6>
                                <small class="text-muted">Project Lead</small>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <a href="{{ route('contact.show') }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-envelope me-1"></i>Contact
                            </a>
                            <a href="{{ route('about') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-person me-1"></i>About Me
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Related Projects -->
                @if($relatedProjects && $relatedProjects->count() > 0)
                    <div class="card">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-3">
                                <i class="bi bi-collection text-primary me-2"></i>
                                Related Projects
                            </h5>

                            @foreach($relatedProjects as $relatedProject)
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">
                                            <a href="{{ route('projects.show', $relatedProject->slug) }}"
                                               class="text-decoration-none">
                                                {{ $relatedProject->title }}
                                            </a>
                                        </h6>
                                        <small class="text-muted">
                                            {{ $relatedProject->date_completed?->format('M Y') ?? 'In Progress' }}
                                        </small>
                                    </div>
                                    @if($relatedProject->status === 'completed')
                                        <span class="badge bg-success ms-2">Done</span>
                                    @endif
                                </div>
                            @endforeach

                            <div class="text-center mt-3">
                                <a href="{{ route('projects.index') }}" class="btn btn-outline-primary btn-sm">
                                    View All Projects <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection

@section('styles')
<style>
    .project-content,
    .features-content,
    .challenges-content,
    .lessons-content {
        line-height: 1.7;
        font-size: 1.05rem;
    }

    .breadcrumb-item + .breadcrumb-item::before {
        color: rgba(255, 255, 255, 0.5);
    }

    .breadcrumb-item a {
        text-decoration: none;
    }

    .breadcrumb-item a:hover {
        text-decoration: underline;
    }

    .card {
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .carousel-control-prev,
    .carousel-control-next {
        width: 5%;
    }

    .carousel-indicators button {
        width: 12px;
        height: 12px;
        border-radius: 50%;
    }
</style>
@endsection