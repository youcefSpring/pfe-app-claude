@extends('layouts.app')

@section('title', $project->title ?? 'Project Details')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('home') }}" class="text-white text-decoration-none">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('projects.index') }}" class="text-white text-decoration-none">Projects</a>
                        </li>
                        <li class="breadcrumb-item active text-white-50" aria-current="page">
                            {{ Str::limit($project->title ?? 'Project', 50) }}
                        </li>
                    </ol>
                </nav>

                <!-- Project Meta -->
                <div class="d-flex flex-wrap gap-2 mb-3">
                    @if($project->status === 'active')
                        <span class="badge bg-success">Active</span>
                    @elseif($project->status === 'completed')
                        <span class="badge bg-primary">Completed</span>
                    @elseif($project->status === 'planning')
                        <span class="badge bg-warning text-dark">Planning</span>
                    @else
                        <span class="badge bg-secondary">On Hold</span>
                    @endif

                    @if($project->type)
                        <span class="badge bg-light text-dark">{{ ucfirst($project->type) }}</span>
                    @endif

                    @if($project->funding_source)
                        <span class="badge bg-info">{{ $project->funding_source }}</span>
                    @endif
                </div>

                <!-- Project Title -->
                <h1 class="display-5 fw-bold mb-4">{{ $project->title ?? 'Project Title' }}</h1>

                <!-- Project Summary -->
                @if($project->summary ?? false)
                    <p class="lead mb-4">{{ $project->summary }}</p>
                @endif

                <!-- Quick Info -->
                <div class="row text-center">
                    @if($project->start_date)
                    <div class="col-md-3 col-6 mb-3">
                        <div class="text-white-50">
                            <i class="bi bi-calendar-event" style="font-size: 1.5rem;"></i>
                            <div class="mt-2">
                                <strong class="text-white d-block">{{ $project->start_date->format('M Y') }}</strong>
                                <small>Started</small>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($project->duration)
                    <div class="col-md-3 col-6 mb-3">
                        <div class="text-white-50">
                            <i class="bi bi-clock" style="font-size: 1.5rem;"></i>
                            <div class="mt-2">
                                <strong class="text-white d-block">{{ $project->duration }}</strong>
                                <small>Duration</small>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($project->funding_amount)
                    <div class="col-md-3 col-6 mb-3">
                        <div class="text-white-50">
                            <i class="bi bi-currency-dollar" style="font-size: 1.5rem;"></i>
                            <div class="mt-2">
                                <strong class="text-white d-block">${{ number_format($project->funding_amount) }}</strong>
                                <small>Funding</small>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($project->collaborators)
                    <div class="col-md-3 col-6 mb-3">
                        <div class="text-white-50">
                            <i class="bi bi-people" style="font-size: 1.5rem;"></i>
                            <div class="mt-2">
                                <strong class="text-white d-block">{{ count(explode(',', $project->collaborators)) }}</strong>
                                <small>Collaborators</small>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Project Content -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Project Image -->
                @if($project->featured_image ?? false)
                <div class="mb-5">
                    <img src="{{ Storage::url($project->featured_image) }}"
                         class="img-fluid rounded shadow-sm" alt="{{ $project->title }}"
                         style="width: 100%; height: 300px; object-fit: cover;">
                </div>
                @endif

                <!-- Project Overview -->
                <div class="card shadow-sm border-0 mb-5">
                    <div class="card-header bg-info text-white">
                        <h4 class="mb-0"><i class="bi bi-info-circle me-2"></i>Project Overview</h4>
                    </div>
                    <div class="card-body p-4">
                        @if($project->description ?? false)
                            <div class="project-description">
                                {!! nl2br(e($project->description)) !!}
                            </div>
                        @else
                            <p>This research project addresses important questions in the field through innovative methodologies and collaborative approaches. The work aims to advance understanding and provide practical solutions to real-world challenges.</p>

                            <p>Our team employs cutting-edge techniques and interdisciplinary perspectives to explore complex problems and generate meaningful insights that contribute to both academic knowledge and practical applications.</p>
                        @endif
                    </div>
                </div>

                <!-- Objectives -->
                @if($project->objectives ?? false)
                <div class="card shadow-sm border-0 mb-5">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0"><i class="bi bi-bullseye me-2"></i>Research Objectives</h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="project-objectives">
                            {!! $project->objectives !!}
                        </div>
                    </div>
                </div>
                @endif

                <!-- Methodology -->
                @if($project->methodology ?? false)
                <div class="card shadow-sm border-0 mb-5">
                    <div class="card-header bg-warning text-white">
                        <h4 class="mb-0"><i class="bi bi-gear me-2"></i>Methodology</h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="project-methodology">
                            {!! $project->methodology !!}
                        </div>
                    </div>
                </div>
                @else
                <!-- Sample Methodology -->
                <div class="card shadow-sm border-0 mb-5">
                    <div class="card-header bg-warning text-white">
                        <h4 class="mb-0"><i class="bi bi-gear me-2"></i>Research Approach</h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <span class="badge bg-warning me-3">1</span>
                                    <div>
                                        <h6>Literature Review</h6>
                                        <small class="text-muted">Comprehensive analysis of existing research</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <span class="badge bg-warning me-3">2</span>
                                    <div>
                                        <h6>Data Collection</h6>
                                        <small class="text-muted">Gathering and organizing research data</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <span class="badge bg-warning me-3">3</span>
                                    <div>
                                        <h6>Analysis & Modeling</h6>
                                        <small class="text-muted">Statistical analysis and predictive modeling</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <span class="badge bg-warning me-3">4</span>
                                    <div>
                                        <h6>Validation & Testing</h6>
                                        <small class="text-muted">Verification of results and conclusions</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Results/Findings -->
                @if($project->results ?? false)
                <div class="card shadow-sm border-0 mb-5">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="bi bi-graph-up me-2"></i>Results & Findings</h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="project-results">
                            {!! $project->results !!}
                        </div>
                    </div>
                </div>
                @endif

                <!-- Publications -->
                @if($project->publications ?? false)
                <div class="card shadow-sm border-0 mb-5">
                    <div class="card-header bg-secondary text-white">
                        <h4 class="mb-0"><i class="bi bi-journal-text me-2"></i>Related Publications</h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="project-publications">
                            {!! $project->publications !!}
                        </div>
                    </div>
                </div>
                @endif

                <!-- Project Gallery -->
                @if($project->gallery ?? false)
                <div class="card shadow-sm border-0 mb-5">
                    <div class="card-header bg-dark text-white">
                        <h4 class="mb-0"><i class="bi bi-images me-2"></i>Project Gallery</h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            @foreach(json_decode($project->gallery, true) ?? [] as $image)
                                <div class="col-md-4 mb-3">
                                    <img src="{{ Storage::url($image['path']) }}"
                                         class="img-fluid rounded shadow-sm"
                                         alt="{{ $image['caption'] ?? 'Project Image' }}"
                                         style="height: 200px; width: 100%; object-fit: cover;">
                                    @if($image['caption'] ?? false)
                                        <small class="text-muted d-block mt-2">{{ $image['caption'] }}</small>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Project Information -->
                <div class="card shadow-sm border-0 mb-4 sticky-top" style="top: 2rem;">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Project Details</h5>
                    </div>
                    <div class="card-body">
                        @if($project->start_date)
                        <div class="mb-3">
                            <strong>Start Date:</strong><br>
                            <span class="text-muted">{{ $project->start_date->format('F j, Y') }}</span>
                        </div>
                        @endif

                        @if($project->end_date)
                        <div class="mb-3">
                            <strong>End Date:</strong><br>
                            <span class="text-muted">{{ $project->end_date->format('F j, Y') }}</span>
                        </div>
                        @endif

                        @if($project->duration)
                        <div class="mb-3">
                            <strong>Duration:</strong><br>
                            <span class="text-muted">{{ $project->duration }}</span>
                        </div>
                        @endif

                        @if($project->status)
                        <div class="mb-3">
                            <strong>Status:</strong><br>
                            <span class="text-muted">{{ ucfirst($project->status) }}</span>
                        </div>
                        @endif

                        @if($project->funding_source)
                        <div class="mb-3">
                            <strong>Funding Source:</strong><br>
                            <span class="text-muted">{{ $project->funding_source }}</span>
                        </div>
                        @endif

                        @if($project->funding_amount)
                        <div class="mb-3">
                            <strong>Funding Amount:</strong><br>
                            <span class="text-muted">${{ number_format($project->funding_amount) }}</span>
                        </div>
                        @endif

                        @if($project->location)
                        <div class="mb-3">
                            <strong>Location:</strong><br>
                            <span class="text-muted">{{ $project->location }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Team Members -->
                @if($project->collaborators ?? false)
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-people me-2"></i>Research Team</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            @if($teacher->avatar ?? false)
                                <img src="{{ Storage::url($teacher->avatar) }}"
                                     alt="{{ $teacher->name ?? 'Principal Investigator' }}"
                                     class="rounded-circle mb-2"
                                     style="width: 60px; height: 60px; object-fit: cover;">
                            @endif
                            <h6 class="mb-1">{{ $teacher->name ?? 'Dr. [Your Name]' }}</h6>
                            <small class="text-muted">Principal Investigator</small>
                        </div>

                        <h6 class="mt-4 mb-3">Collaborators:</h6>
                        @foreach(explode(',', $project->collaborators) as $collaborator)
                            <div class="mb-2">
                                <i class="bi bi-person text-success me-2"></i>{{ trim($collaborator) }}
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Technologies -->
                @if($project->technologies ?? false)
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0"><i class="bi bi-gear me-2"></i>Technologies & Tools</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            @foreach(explode(',', $project->technologies) as $tech)
                                <span class="badge bg-light text-dark">{{ trim($tech) }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Keywords -->
                @if($project->keywords ?? false)
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="bi bi-tags me-2"></i>Keywords</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            @foreach(explode(',', $project->keywords) as $keyword)
                                <span class="badge bg-light text-dark">#{{ trim($keyword) }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Project Links -->
                @if($project->website_url || $project->repository_url || $project->demo_url)
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-link me-2"></i>Project Links</h5>
                    </div>
                    <div class="card-body">
                        @if($project->website_url)
                            <div class="mb-2">
                                <a href="{{ $project->website_url }}" target="_blank" rel="noopener"
                                   class="text-decoration-none">
                                    <i class="bi bi-globe text-primary me-2"></i>Project Website
                                </a>
                            </div>
                        @endif

                        @if($project->repository_url)
                            <div class="mb-2">
                                <a href="{{ $project->repository_url }}" target="_blank" rel="noopener"
                                   class="text-decoration-none">
                                    <i class="bi bi-github text-primary me-2"></i>Code Repository
                                </a>
                            </div>
                        @endif

                        @if($project->demo_url)
                            <div class="mb-2">
                                <a href="{{ $project->demo_url }}" target="_blank" rel="noopener"
                                   class="text-decoration-none">
                                    <i class="bi bi-play-circle text-primary me-2"></i>Live Demo
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Related Projects -->
                @if(isset($relatedProjects) && $relatedProjects->count() > 0)
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="bi bi-collection me-2"></i>Related Projects</h5>
                    </div>
                    <div class="card-body p-0">
                        @foreach($relatedProjects->take(3) as $relatedProject)
                            <div class="p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <h6 class="mb-2">
                                    <a href="{{ route('projects.show', $relatedProject->slug) }}"
                                       class="text-decoration-none">
                                        {{ $relatedProject->title }}
                                    </a>
                                </h6>
                                <small class="text-muted">
                                    {{ ucfirst($relatedProject->status) }} • {{ $relatedProject->start_date?->format('Y') }}
                                </small>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Navigation -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="row">
            @if(isset($previousProject))
                <div class="col-md-6 mb-3">
                    <div class="card h-100 border-0">
                        <div class="card-body">
                            <small class="text-muted">Previous Project</small>
                            <h6 class="mt-2">
                                <a href="{{ route('projects.show', $previousProject->slug) }}"
                                   class="text-decoration-none">
                                    <i class="bi bi-arrow-left me-1"></i>{{ $previousProject->title }}
                                </a>
                            </h6>
                        </div>
                    </div>
                </div>
            @endif

            @if(isset($nextProject))
                <div class="col-md-6 mb-3">
                    <div class="card h-100 border-0">
                        <div class="card-body text-end">
                            <small class="text-muted">Next Project</small>
                            <h6 class="mt-2">
                                <a href="{{ route('projects.show', $nextProject->slug) }}"
                                   class="text-decoration-none">
                                    {{ $nextProject->title }}<i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </h6>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="text-center mt-3">
            <a href="{{ route('projects.index') }}" class="btn btn-info text-white">
                <i class="bi bi-grid me-2"></i>View All Projects
            </a>
        </div>
    </div>
</section>
@endsection