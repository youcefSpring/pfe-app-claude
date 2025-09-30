@extends('layouts.app')

@section('title', 'Projects')

@section('content')
<!-- Header Section -->
<section class="py-5 bg-primary text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-5 fw-bold">Projects</h1>
                <p class="lead mb-0">Explore my development projects, research initiatives, and creative work.</p>
            </div>
            <div class="col-lg-4 text-center">
                <i class="bi bi-code-slash" style="font-size: 6rem; opacity: 0.7;"></i>
            </div>
        </div>
    </div>
</section>

<!-- Projects Content -->
<section class="py-5">
    <div class="container">
        <!-- Search and Filter -->
        <div class="row mb-4">
            <div class="col-md-6">
                <form method="GET" action="{{ route('projects.index') }}" class="d-flex gap-2">
                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Search projects by title, description, or technology..."
                           value="{{ request('search') }}">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-search"></i>
                    </button>
                    @if(request('search'))
                        <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </form>
            </div>
            <div class="col-md-6 text-md-end">
                <div class="btn-group" role="group">
                    <input type="radio" class="btn-check" name="filter" id="all" {{ !request('status') ? 'checked' : '' }}>
                    <label class="btn btn-outline-secondary" for="all">All</label>

                    <input type="radio" class="btn-check" name="filter" id="completed" {{ request('status') === 'completed' ? 'checked' : '' }}>
                    <label class="btn btn-outline-secondary" for="completed">Completed</label>

                    <input type="radio" class="btn-check" name="filter" id="active" {{ request('status') === 'active' ? 'checked' : '' }}>
                    <label class="btn btn-outline-secondary" for="active">Active</label>
                </div>
            </div>
        </div>

        @if(request('search'))
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                Showing results for "<strong>{{ request('search') }}</strong>" - {{ $projects->total() }} project(s) found
            </div>
        @endif

        <!-- Projects Grid -->
        @if($projects->count() > 0)
            <div class="row g-4">
                @foreach($projects as $project)
                    <div class="col-lg-6 col-xl-4">
                        <div class="card project-card h-100">
                            @if($project->images && count($project->images) > 0)
                                <div class="project-image-container">
                                    <img src="{{ asset('storage/' . $project->images[0]) }}"
                                         class="card-img-top project-image"
                                         alt="{{ $project->title }}"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
                                    <div class="project-placeholder" style="display: none;">
                                        <i class="bi bi-code-slash text-muted"></i>
                                    </div>
                                </div>
                            @else
                                <div class="project-placeholder">
                                    <i class="bi bi-code-slash text-muted"></i>
                                </div>
                            @endif

                            <div class="card-body d-flex flex-column">
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="card-title mb-0">
                                            <a href="{{ route('projects.show', $project->slug) }}" class="text-decoration-none">
                                                {{ $project->title }}
                                            </a>
                                        </h5>
                                        @if($project->status === 'completed')
                                            <span class="badge bg-success">Completed</span>
                                        @elseif($project->status === 'active')
                                            <span class="badge bg-primary">Active</span>
                                        @else
                                            <span class="badge bg-warning">In Progress</span>
                                        @endif
                                    </div>

                                    <p class="card-text text-muted">
                                        {{ Str::limit($project->description, 120) }}
                                    </p>
                                </div>

                                <!-- Technologies -->
                                @if($project->technologies_used)
                                    <div class="mb-3">
                                        @foreach(explode(',', $project->technologies_used) as $tech)
                                            <span class="badge bg-light text-dark border me-1 mb-1">{{ trim($tech) }}</span>
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Tags -->
                                @if($project->tags && $project->tags->count() > 0)
                                    <div class="mb-3">
                                        @foreach($project->tags as $tag)
                                            <span class="badge bg-secondary me-1">{{ $tag->name }}</span>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="mt-auto">
                                    @if($project->date_completed)
                                        <small class="text-muted d-block mb-2">
                                            <i class="bi bi-calendar-check me-1"></i>
                                            Completed {{ $project->date_completed->format('M Y') }}
                                        </small>
                                    @endif

                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="{{ route('projects.show', $project->slug) }}" class="btn btn-primary btn-sm">
                                            <i class="bi bi-eye me-1"></i>View Details
                                        </a>

                                        <div class="d-flex gap-2">
                                            @if($project->live_demo_url)
                                                <a href="{{ $project->live_demo_url }}" target="_blank" class="btn btn-outline-success btn-sm">
                                                    <i class="bi bi-box-arrow-up-right"></i>
                                                </a>
                                            @endif

                                            @if($project->source_code_url)
                                                <a href="{{ $project->source_code_url }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                                                    <i class="bi bi-github"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($projects->hasPages())
                <div class="d-flex justify-content-center mt-5">
                    {{ $projects->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-5">
                <i class="bi bi-code-slash text-muted mb-3" style="font-size: 4rem;"></i>

                @if(request('search'))
                    <h3 class="h4 text-muted mb-3">No projects found</h3>
                    <p class="text-muted mb-4">
                        No projects match your search criteria. Try adjusting your search terms.
                    </p>
                    <a href="{{ route('projects.index') }}" class="btn btn-primary">
                        <i class="bi bi-arrow-left me-1"></i>View All Projects
                    </a>
                @else
                    <h3 class="h4 text-muted mb-3">No projects available yet</h3>
                    <p class="text-muted">
                        Project portfolio will be available here once projects are added.
                    </p>
                @endif
            </div>
        @endif

        <!-- Project Categories Info -->
        @if($projects->count() > 0)
            <div class="row mt-5">
                <div class="col-12">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="bi bi-info-circle text-primary me-2"></i>
                                About My Projects
                            </h5>
                            <div class="row g-4">
                                <div class="col-md-3">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-globe text-success me-2 mt-1"></i>
                                        <div>
                                            <h6 class="mb-1">Web Applications</h6>
                                            <small class="text-muted">Full-stack web development projects</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-phone text-info me-2 mt-1"></i>
                                        <div>
                                            <h6 class="mb-1">Mobile Apps</h6>
                                            <small class="text-muted">Cross-platform mobile applications</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-cpu text-warning me-2 mt-1"></i>
                                        <div>
                                            <h6 class="mb-1">Research Tools</h6>
                                            <small class="text-muted">Academic and research software solutions</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-github text-secondary me-2 mt-1"></i>
                                        <div>
                                            <h6 class="mb-1">Open Source</h6>
                                            <small class="text-muted">Community contributions and libraries</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection

@section('styles')
<style>
    .project-card {
        border: 1px solid #e5e7eb;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
    }

    .project-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }

    .project-image-container {
        height: 200px;
        overflow: hidden;
        position: relative;
    }

    .project-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .project-card:hover .project-image {
        transform: scale(1.05);
    }

    .project-placeholder {
        height: 200px;
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
    }

    .badge {
        font-size: 0.75rem;
    }

    .btn-check:checked + .btn {
        background-color: #2563eb;
        border-color: #2563eb;
        color: white;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle filter buttons
        const filterButtons = document.querySelectorAll('input[name="filter"]');
        filterButtons.forEach(function(button) {
            button.addEventListener('change', function() {
                const url = new URL(window.location);
                if (this.id === 'all') {
                    url.searchParams.delete('status');
                } else {
                    url.searchParams.set('status', this.id);
                }
                window.location.href = url.toString();
            });
        });
    });
</script>
@endsection