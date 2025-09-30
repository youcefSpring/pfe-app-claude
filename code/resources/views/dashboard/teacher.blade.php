@extends('layouts.pfe-app')

@section('page-title', 'Teacher Dashboard')

@section('content')
<div class="row">
    <!-- Welcome Card -->
    <div class="col-12 mb-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title mb-2">Welcome back, Prof. {{ auth()->user()->name }}!</h4>
                        <p class="card-text mb-0">
                            Academic Year: {{ now()->format('Y') }}/{{ now()->addYear()->format('Y') }}
                            @if(auth()->user()->department)
                                | Department: {{ auth()->user()->department }}
                            @endif
                        </p>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-person-workspace" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Quick Stats -->
    <div class="col-md-3 mb-4">
        <div class="card text-center h-100">
            <div class="card-body">
                <i class="bi bi-folder-check text-primary mb-3" style="font-size: 2.5rem;"></i>
                <h5 class="card-title">Supervised Projects</h5>
                <h3 class="text-primary" id="supervised-projects">-</h3>
                <small class="text-muted">Active projects</small>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card text-center h-100">
            <div class="card-body">
                <i class="bi bi-journal-text text-warning mb-3" style="font-size: 2.5rem;"></i>
                <h5 class="card-title">My Subjects</h5>
                <h3 class="text-warning" id="my-subjects">-</h3>
                <small class="text-muted">Proposed subjects</small>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card text-center h-100">
            <div class="card-body">
                <i class="bi bi-shield-check text-success mb-3" style="font-size: 2.5rem;"></i>
                <h5 class="card-title">Defenses</h5>
                <h3 class="text-success" id="defense-count">-</h3>
                <small class="text-muted">As jury member</small>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card text-center h-100">
            <div class="card-body">
                <i class="bi bi-file-earmark-text text-info mb-3" style="font-size: 2.5rem;"></i>
                <h5 class="card-title">Submissions</h5>
                <h3 class="text-info" id="pending-submissions">-</h3>
                <small class="text-muted">Pending review</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Supervised Projects -->
    <div class="col-lg-8 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-folder-check me-2"></i>Supervised Projects
                </h5>
                <a href="{{ route('projects.index') }}" class="btn btn-sm btn-outline-primary">
                    View All
                </a>
            </div>
            <div class="card-body">
                <div id="supervised-projects-list">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted mt-2">Loading supervised projects...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-lightning me-2"></i>Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('subjects.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus me-2"></i>Add New Subject
                    </a>
                    <a href="{{ route('projects.index') }}" class="btn btn-outline-success">
                        <i class="bi bi-folder me-2"></i>Review Projects
                    </a>
                    <a href="{{ route('defenses.index') }}" class="btn btn-outline-warning">
                        <i class="bi bi-shield-check me-2"></i>Defense Schedule
                    </a>
                    <a href="{{ route('submissions.index') }}" class="btn btn-outline-info">
                        <i class="bi bi-file-earmark-text me-2"></i>Review Submissions
                    </a>
                </div>

                <!-- Upcoming Events -->
                <div class="mt-4">
                    <h6 class="text-muted">Upcoming Events</h6>
                    <div id="upcoming-events">
                        <small class="text-muted">Loading events...</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- My Subjects -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-journal-text me-2"></i>My Subjects
                </h5>
                <a href="{{ route('subjects.index') }}" class="btn btn-sm btn-outline-primary">
                    Manage All
                </a>
            </div>
            <div class="card-body">
                <div id="my-subjects-list">
                    <div class="text-center py-4">
                        <div class="spinner-border text-warning" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted mt-2">Loading your subjects...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-clock-history me-2"></i>Recent Activity
                </h5>
            </div>
            <div class="card-body">
                <div id="recent-activity">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted mt-2">Loading recent activity...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pending Reviews -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-clipboard-check me-2"></i>Items Requiring Your Attention
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Pending Submissions -->
                    <div class="col-lg-4 mb-3">
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="bg-warning bg-opacity-25 rounded-circle p-3 me-3">
                                        <i class="bi bi-file-earmark-text text-warning"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Pending Submissions</h6>
                                        <p class="mb-0 text-muted" id="pending-submissions-count">Loading...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Upcoming Defenses -->
                    <div class="col-lg-4 mb-3">
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="bg-info bg-opacity-25 rounded-circle p-3 me-3">
                                        <i class="bi bi-shield-check text-info"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Upcoming Defenses</h6>
                                        <p class="mb-0 text-muted" id="upcoming-defenses-count">Loading...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Project Updates -->
                    <div class="col-lg-4 mb-3">
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="bg-success bg-opacity-25 rounded-circle p-3 me-3">
                                        <i class="bi bi-folder-check text-success"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Project Updates</h6>
                                        <p class="mb-0 text-muted" id="project-updates-count">Loading...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadDashboardData();

    function loadDashboardData() {
        loadStats();
        loadSupervisedProjects();
        loadMySubjects();
        loadRecentActivity();
        loadUpcomingEvents();
        loadPendingReviews();
    }

    async function loadStats() {
        try {
            const response = await axios.get('/api/reports/dashboard-stats');
            const data = response.data.data;

            document.getElementById('supervised-projects').textContent = data.supervised_projects || 0;
            document.getElementById('my-subjects').textContent = data.my_subjects || 0;
            document.getElementById('defense-count').textContent = data.defense_participations || 0;
            document.getElementById('pending-submissions').textContent = data.pending_submissions || 0;
        } catch (error) {
            console.log('Could not load dashboard stats');
        }
    }

    async function loadSupervisedProjects() {
        try {
            const response = await axios.get('/api/projects/supervised');
            const projects = response.data.data;

            const container = document.getElementById('supervised-projects-list');

            if (projects.length > 0) {
                container.innerHTML = projects.slice(0, 4).map(project => `
                    <div class="d-flex align-items-center mb-3 p-3 bg-light rounded">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-25 rounded-circle p-2">
                                <i class="bi bi-folder text-primary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">${project.title || 'Untitled Project'}</h6>
                            <p class="mb-1 text-muted small">Team: ${project.team.name}</p>
                            <span class="badge status-${project.status}">${project.status.replace('_', ' ').toUpperCase()}</span>
                        </div>
                        <div class="flex-shrink-0">
                            <a href="/projects/${project.id}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                `).join('');
            } else {
                container.innerHTML = `
                    <div class="text-center py-3">
                        <i class="bi bi-folder text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2">No projects under supervision</p>
                    </div>
                `;
            }
        } catch (error) {
            document.getElementById('supervised-projects-list').innerHTML = `
                <div class="text-center py-3">
                    <p class="text-muted">Could not load supervised projects</p>
                </div>
            `;
        }
    }

    async function loadMySubjects() {
        try {
            const response = await axios.get('/api/subjects');
            const subjects = response.data.data;
            const mySubjects = subjects.filter(subject => subject.proposed_by === {{ auth()->id() }});

            const container = document.getElementById('my-subjects-list');

            if (mySubjects.length > 0) {
                container.innerHTML = mySubjects.slice(0, 3).map(subject => `
                    <div class="d-flex align-items-center justify-content-between mb-3 p-3 bg-light rounded">
                        <div>
                            <h6 class="mb-1">${subject.title}</h6>
                            <span class="badge status-${subject.status}">${subject.status.replace('_', ' ').toUpperCase()}</span>
                        </div>
                        <a href="/subjects/${subject.id}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i>
                        </a>
                    </div>
                `).join('');
            } else {
                container.innerHTML = `
                    <div class="text-center py-3">
                        <i class="bi bi-journal-text text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2 mb-3">No subjects proposed yet</p>
                        <a href="/subjects/create" class="btn btn-primary">
                            <i class="bi bi-plus me-2"></i>Add Subject
                        </a>
                    </div>
                `;
            }
        } catch (error) {
            document.getElementById('my-subjects-list').innerHTML = `
                <div class="text-center py-3">
                    <p class="text-muted">Could not load your subjects</p>
                </div>
            `;
        }
    }

    async function loadRecentActivity() {
        try {
            const response = await axios.get('/api/auth/notifications');
            const notifications = response.data.data.data;

            const container = document.getElementById('recent-activity');

            if (notifications.length > 0) {
                container.innerHTML = notifications.slice(0, 4).map(notification => `
                    <div class="d-flex align-items-start mb-3">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                                <i class="bi bi-bell text-primary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">${notification.data.title || 'Notification'}</h6>
                            <p class="mb-1 text-muted small">${notification.data.message || ''}</p>
                            <small class="text-muted">${formatDate(notification.created_at)}</small>
                        </div>
                    </div>
                `).join('');
            } else {
                container.innerHTML = `
                    <div class="text-center py-3">
                        <i class="bi bi-clock-history text-muted" style="font-size: 2rem;"></i>
                        <p class="text-muted mt-2">No recent activity</p>
                    </div>
                `;
            }
        } catch (error) {
            document.getElementById('recent-activity').innerHTML = `
                <div class="text-center py-3">
                    <p class="text-muted">Could not load recent activity</p>
                </div>
            `;
        }
    }

    function loadUpcomingEvents() {
        // Placeholder for upcoming events
        document.getElementById('upcoming-events').innerHTML = `
            <div class="alert alert-info alert-sm">
                <i class="bi bi-calendar-event me-2"></i>
                <small>No upcoming events</small>
            </div>
        `;
    }

    function loadPendingReviews() {
        // Placeholder counters for pending reviews
        document.getElementById('pending-submissions-count').textContent = '0 items awaiting review';
        document.getElementById('upcoming-defenses-count').textContent = '0 defenses this week';
        document.getElementById('project-updates-count').textContent = '0 updates to review';
    }

    function formatDate(dateString) {
        return new Date(dateString).toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
});
</script>
@endpush