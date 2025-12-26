@extends('layouts.pfe-app')

@section('page-title', 'My Projects')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <h5 class="mb-0">
                        <i class="bi bi-folder-fill me-2"></i>
                        @if(auth()->user()->role === 'student')
                            {{ __('app.my_projects') }}
                        @elseif(auth()->user()->role === 'teacher')
                            {{ __('app.supervised_projects') }}
                        @else
                            {{ __('app.all_projects') }}
                        @endif
                    </h5>
                    <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#pageHelpModal">
                        <i class="bi bi-question-circle"></i>
                    </button>
                </div>
                <div class="d-flex gap-2">
                    @if(auth()->user()->role === 'student')
                        @php
                            $userTeam = auth()->user()->teamMember?->team;
                            $hasProject = $userTeam && $userTeam->project;
                        @endphp
                        @if($userTeam && !$hasProject)
                            <a href="{{ route('teams.select-subject-form', $userTeam) }}" class="btn btn-primary">
                                <i class="bi bi-plus me-2"></i>Select Subject
                            </a>
                        @endif
                    @endif
                    @if(in_array(auth()->user()->role, ['admin', 'department_head']))
                        <a href="{{ route('projects.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus me-2"></i>Create Project
                        </a>
                    @endif
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-funnel me-2"></i>Filters
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="filterByStatus('all')">All Projects</a></li>
                            <li><a class="dropdown-item" href="#" onclick="filterByStatus('assigned')">Assigned</a></li>
                            <li><a class="dropdown-item" href="#" onclick="filterByStatus('active')">Active</a></li>
                            <li><a class="dropdown-item" href="#" onclick="filterByStatus('in_progress')">In Progress</a></li>
                            <li><a class="dropdown-item" href="#" onclick="filterByStatus('submitted')">Submitted</a></li>
                            <li><a class="dropdown-item" href="#" onclick="filterByStatus('completed')">Completed</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Quick Stats -->
                @if(auth()->user()->role !== 'student')
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h4 id="total-projects">{{ $projects->total() ?? 0 }}</h4>
                                <small>Total Projects</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h4 id="active-projects">-</h4>
                                <small>Active</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-dark">
                            <div class="card-body text-center">
                                <h4 id="progress-projects">-</h4>
                                <small>In Progress</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h4 id="completed-projects">-</h4>
                                <small>Completed</small>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Search and Filters -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <input type="text" class="form-control" id="searchInput" placeholder="Search projects by title or subject...">
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="statusFilter">
                            <option value="">All Statuses</option>
                            <option value="assigned">Assigned</option>
                            <option value="active">Active</option>
                            <option value="in_progress">In Progress</option>
                            <option value="submitted">Submitted</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    @if(auth()->user()->role !== 'student')
                    <div class="col-md-3">
                        <select class="form-select" id="typeFilter">
                            <option value="">All Types</option>
                            <option value="internal">Internal</option>
                            <option value="external">External</option>
                        </select>
                    </div>
                    @endif
                    <div class="col-md-2">
                        <button class="btn btn-outline-primary w-100" onclick="clearFilters()">
                            <i class="bi bi-x-circle me-2"></i>Clear
                        </button>
                    </div>
                </div>

                <!-- Projects List -->
                @if($projects->count() > 0)
                    <div class="row" id="projects-container">
                        @foreach($projects as $project)
                            <div class="col-lg-6 col-xl-4 mb-4">
                                <div class="card project-card h-100" data-project-id="{{ $project->id }}">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <h6 class="card-title mb-0">
                                                <a href="{{ route('projects.show', $project) }}" class="text-decoration-none">
                                                    {{ $project->subject ? $project->subject->title : 'External Project' }}
                                                </a>
                                            </h6>
                                            <span class="badge status-{{ $project->status }}">
                                                {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            @if($project->subject)
                                                <p class="text-muted mb-2">
                                                    {{ Str::limit($project->subject->description, 100) }}
                                                </p>
                                            @else
                                                <p class="text-muted mb-2">External project</p>
                                            @endif
                                        </div>

                                        <!-- Team Information -->
                                        @if($project->team)
                                        <div class="mb-3">
                                            <h6 class="text-primary mb-2">
                                                <i class="bi bi-people me-1"></i>Team: {{ $project->team->name }}
                                            </h6>
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($project->team->members as $member)
                                                    <span class="badge bg-light text-dark">
                                                        {{ $member->user->name }}
                                                        @if($member->role === 'leader')
                                                            <i class="bi bi-star-fill text-warning ms-1"></i>
                                                        @endif
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                        @endif

                                        <!-- Supervisor Information -->
                                        @if($project->supervisor)
                                        <div class="mb-3">
                                            <small class="text-muted">
                                                <i class="bi bi-person-badge me-1"></i>
                                                Supervisor: {{ $project->supervisor->name }}
                                            </small>
                                        </div>
                                        @endif

                                        <!-- Project Type and Dates -->
                                        <div class="row text-center">
                                            <div class="col-6">
                                                <small class="text-muted d-block">Type</small>
                                                <span class="badge bg-{{ $project->type === 'internal' ? 'primary' : 'success' }}">
                                                    {{ ucfirst($project->type) }}
                                                </span>
                                            </div>
                                            <div class="col-6">
                                                @if($project->started_at)
                                                    <small class="text-muted d-block">Started</small>
                                                    <small>{{ $project->started_at->format('M d, Y') }}</small>
                                                @else
                                                    <small class="text-muted d-block">Created</small>
                                                    <small>{{ $project->created_at->format('M d, Y') }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <a href="{{ route('projects.show', $project) }}" class="btn btn-primary btn-sm">
                                                <i class="bi bi-eye me-1"></i>View Details
                                            </a>

                                            <div class="btn-group">
                                                @if(auth()->user()->role === 'student' && $project->team && $project->team->members->contains('student_id', auth()->id()))
                                                    @if(in_array($project->status, ['active', 'in_progress']))
                                                        <a href="{{ route('projects.submit-form', $project) }}" class="btn btn-outline-success btn-sm" title="Submit Work">
                                                            <i class="bi bi-upload"></i>
                                                        </a>
                                                    @endif
                                                    <a href="{{ route('projects.timeline', $project) }}" class="btn btn-outline-info btn-sm" title="Timeline">
                                                        <i class="bi bi-clock-history"></i>
                                                    </a>
                                                @endif

                                                @if(auth()->user()->role === 'teacher' && $project->supervisor_id === auth()->id())
                                                    <a href="{{ route('projects.review-form', $project) }}" class="btn btn-outline-warning btn-sm" title="Review">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                @endif

                                                @if(in_array(auth()->user()->role, ['admin', 'department_head']))
                                                    <a href="{{ route('projects.edit', $project) }}" class="btn btn-outline-secondary btn-sm" title="Edit">
                                                        <i class="bi bi-gear"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if($projects->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $projects->appends(request()->query())->links() }}
                        </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="text-center py-5">
                        <i class="bi bi-folder text-muted" style="font-size: 4rem;"></i>
                        <h5 class="text-muted mt-3">
                            @if(auth()->user()->role === 'student')
                                No projects assigned yet
                            @else
                                No projects found
                            @endif
                        </h5>

                        @if(auth()->user()->role === 'student')
                            @php
                                $userTeam = auth()->user()->teamMember?->team;
                            @endphp
                            @if(!$userTeam)
                                <p class="text-muted mb-4">You need to join or create a team first to access projects.</p>
                                @if(!auth()->user()->teamMember)
                                    <a href="{{ route('teams.create') }}" class="btn btn-primary me-2">
                                        <i class="bi bi-plus me-1"></i>Create Team
                                    </a>
                                @endif
                                <a href="{{ route('teams.index') }}" class="btn btn-outline-primary">
                                    <i class="bi bi-eye me-1"></i>Browse Teams
                                </a>
                            @elseif(!$userTeam->project)
                                <p class="text-muted mb-4">Your team hasn't selected a subject yet.</p>
                                <a href="{{ route('teams.select-subject-form', $userTeam) }}" class="btn btn-primary">
                                    <i class="bi bi-journal-text me-1"></i>Select Subject
                                </a>
                            @else
                                <p class="text-muted">Your project will appear here once it's officially assigned.</p>
                            @endif
                        @else
                            <p class="text-muted mb-4">No projects have been created yet.</p>
                            @if(in_array(auth()->user()->role, ['admin', 'department_head']))
                                <a href="{{ route('projects.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus me-1"></i>Create First Project
                                </a>
                            @endif
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Project Details Modal -->
<div class="modal fade" id="projectModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Project Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="project-details">
                <!-- Details will be loaded here -->
            </div>
            <div class="modal-footer" id="project-actions">
                <!-- Actions will be loaded here -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentFilters = {};

    setupFilters();
    @if(auth()->user()->role !== 'student')
        loadProjectStats();
    @endif

    function setupFilters() {
        ['statusFilter', 'typeFilter', 'searchInput'].forEach(filterId => {
            const element = document.getElementById(filterId);
            if (element) {
                element.addEventListener('change', applyFilters);
                element.addEventListener('input', debounce(applyFilters, 300));
            }
        });
    }

    function applyFilters() {
        const status = document.getElementById('statusFilter')?.value || '';
        const type = document.getElementById('typeFilter')?.value || '';
        const search = document.getElementById('searchInput')?.value || '';

        const params = new URLSearchParams(window.location.search);

        if (status) params.set('status', status);
        else params.delete('status');

        if (type) params.set('type', type);
        else params.delete('type');

        if (search) params.set('search', search);
        else params.delete('search');

        window.location.search = params.toString();
    }

    function loadProjectStats() {
        axios.get('/api/projects/stats')
            .then(response => {
                const data = response.data;
                if (document.getElementById('active-projects')) {
                    document.getElementById('active-projects').textContent = data.active || 0;
                }
                if (document.getElementById('progress-projects')) {
                    document.getElementById('progress-projects').textContent = data.in_progress || 0;
                }
                if (document.getElementById('completed-projects')) {
                    document.getElementById('completed-projects').textContent = data.completed || 0;
                }
            })
            .catch(error => {
                console.log('Could not load project statistics');
            });
    }

    window.clearFilters = function() {
        if (document.getElementById('statusFilter')) document.getElementById('statusFilter').value = '';
        if (document.getElementById('typeFilter')) document.getElementById('typeFilter').value = '';
        if (document.getElementById('searchInput')) document.getElementById('searchInput').value = '';

        window.location.href = window.location.pathname;
    };

    window.filterByStatus = function(status) {
        if (document.getElementById('statusFilter')) {
            document.getElementById('statusFilter').value = status === 'all' ? '' : status;
            applyFilters();
        }
    };

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
});
</script>
@endpush

@push('styles')
<style>
.project-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: 1px solid #e5e7eb;
}

.project-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.status-assigned {
    background-color: #6c757d !important;
}

.status-active {
    background-color: #0d6efd !important;
}

.status-in_progress {
    background-color: #fd7e14 !important;
}

.status-submitted {
    background-color: #6f42c1 !important;
}

.status-completed {
    background-color: #198754 !important;
}

.status-defended {
    background-color: #20c997 !important;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.badge {
    font-size: 0.75rem;
}

.btn-group .btn {
    border-radius: 0.25rem;
    margin-left: 2px;
}

.card-footer {
    background-color: #f8f9fa;
    border-top: 1px solid #dee2e6;
}
</style>
@endpush

<!-- Page Help Modal -->
<x-info-modal id="pageHelpModal" title="{{ __('app.projects_page_help') }}" icon="bi-folder">
    <h6>{{ __('app.what_is_this_page') }}</h6>
    <p>{{ __('app.projects_page_description') }}</p>

    <h6>{{ __('app.how_to_use') }}</h6>
    <ul>
        @if(auth()->user()->role === 'student')
            <li><strong>{{ __('app.view_project') }}:</strong> {{ __('app.view_project_help') }}</li>
            <li><strong>{{ __('app.submit_project') }}:</strong> {{ __('app.submit_project_help') }}</li>
            <li><strong>{{ __('app.project_timeline') }}:</strong> {{ __('app.project_timeline_help') }}</li>
        @elseif(auth()->user()->role === 'teacher')
            <li><strong>{{ __('app.supervise_projects') }}:</strong> {{ __('app.supervise_projects_help') }}</li>
            <li><strong>{{ __('app.review_submissions') }}:</strong> {{ __('app.review_submissions_help') }}</li>
            <li><strong>{{ __('app.provide_feedback') }}:</strong> {{ __('app.provide_feedback_help') }}</li>
        @else
            <li><strong>{{ __('app.monitor_projects') }}:</strong> {{ __('app.monitor_projects_help') }}</li>
            <li><strong>{{ __('app.project_reports') }}:</strong> {{ __('app.project_reports_help') }}</li>
        @endif
    </ul>

    @if(auth()->user()->role === 'student')
        <h6>{{ __('app.project_tips') }}</h6>
        <ul>
            <li>{{ __('app.project_tip_1') }}</li>
            <li>{{ __('app.project_tip_2') }}</li>
            <li>{{ __('app.project_tip_3') }}</li>
        </ul>
    @endif
</x-info-modal>