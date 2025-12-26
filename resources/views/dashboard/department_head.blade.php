@extends('layouts.pfe-app')

@section('page-title', 'Department Head Dashboard')

@section('content')
<div class="row">
    <!-- Welcome Card -->
    <div class="col-12 mb-4">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title mb-2">Department Management Dashboard</h4>
                        <p class="card-text mb-0">
                            <strong>{{ auth()->user()->name }}</strong> - Head of {{ auth()->user()->department ?? 'Department' }}
                            | Academic Year: {{ now()->format('Y') }}/{{ now()->addYear()->format('Y') }}
                        </p>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-building" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Critical Stats - Requiring Attention -->
    <div class="col-md-3 mb-4">
        <div class="card text-center h-100 border-warning">
            <div class="card-body">
                <i class="bi bi-exclamation-triangle text-warning mb-3" style="font-size: 2.5rem;"></i>
                <h5 class="card-title">Pending Validations</h5>
                <h3 class="text-warning" id="pending-validations">-</h3>
                <small class="text-muted">Subjects awaiting approval</small>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card text-center h-100 border-danger">
            <div class="card-body">
                <i class="bi bi-exclamation-circle text-danger mb-3" style="font-size: 2.5rem;"></i>
                <h5 class="card-title">Active Conflicts</h5>
                <h3 class="text-danger" id="active-conflicts">-</h3>
                <small class="text-muted">Require resolution</small>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card text-center h-100 border-primary">
            <div class="card-body">
                <i class="bi bi-people text-primary mb-3" style="font-size: 2.5rem;"></i>
                <h5 class="card-title">Active Teams</h5>
                <h3 class="text-primary" id="active-teams">-</h3>
                <small class="text-muted">Department teams</small>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card text-center h-100 border-success">
            <div class="card-body">
                <i class="bi bi-shield-check text-success mb-3" style="font-size: 2.5rem;"></i>
                <h5 class="card-title">Scheduled Defenses</h5>
                <h3 class="text-success" id="scheduled-defenses">-</h3>
                <small class="text-muted">This month</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Critical Actions Required -->
    <div class="col-lg-8 mb-4">
        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>Immediate Actions Required
                </h5>
            </div>
            <div class="card-body">
                <div id="critical-actions">
                    <div class="text-center py-4">
                        <div class="spinner-border text-danger" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted mt-2">Loading critical actions...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Department Overview -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-pie-chart me-2"></i>Department Overview
                </h5>
            </div>
            <div class="card-body">
                <div id="department-stats">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted mt-2">Loading overview...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Subjects Pending Validation -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-journal-text me-2"></i>Subjects Pending Validation
                </h5>
                <a href="{{ route('subjects.pending-validation') }}" class="btn btn-sm btn-warning">
                    <i class="bi bi-eye me-1"></i>Review All
                </a>
            </div>
            <div class="card-body">
                <div id="pending-subjects">
                    <div class="text-center py-4">
                        <div class="spinner-border text-warning" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted mt-2">Loading pending subjects...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Conflicts Requiring Resolution -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>Active Conflicts
                </h5>
                <a href="{{ route('conflicts.index') }}" class="btn btn-sm btn-danger">
                    <i class="bi bi-tools me-1"></i>Resolve All
                </a>
            </div>
            <div class="card-body">
                <div id="active-conflicts-list">
                    <div class="text-center py-4">
                        <div class="spinner-border text-danger" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted mt-2">Loading conflicts...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Department Teams Status -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-people me-2"></i>Department Teams
                </h5>
                <a href="{{ route('teams.index') }}" class="btn btn-sm btn-outline-primary">
                    View All
                </a>
            </div>
            <div class="card-body">
                <div id="department-teams">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted mt-2">Loading teams...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Defenses -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-calendar-event me-2"></i>Upcoming Defenses
                </h5>
                <a href="{{ route('defenses.calendar') }}" class="btn btn-sm btn-outline-success">
                    View Calendar
                </a>
            </div>
            <div class="card-body">
                <div id="upcoming-defenses">
                    <div class="text-center py-4">
                        <div class="spinner-border text-success" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted mt-2">Loading defenses...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Department Analytics -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-graph-up me-2"></i>Department Analytics & Reports
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Progress Distribution -->
                    <div class="col-lg-3 mb-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="text-muted">Subject Status</h6>
                                <div id="subject-progress-chart" class="mt-3">
                                    <div class="progress-ring mx-auto">
                                        <canvas width="60" height="60"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Team Formation Progress -->
                    <div class="col-lg-3 mb-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="text-muted">Team Formation</h6>
                                <div id="team-progress-chart" class="mt-3">
                                    <div class="progress-ring mx-auto">
                                        <canvas width="60" height="60"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Project Status -->
                    <div class="col-lg-3 mb-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="text-muted">Project Status</h6>
                                <div id="project-progress-chart" class="mt-3">
                                    <div class="progress-ring mx-auto">
                                        <canvas width="60" height="60"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Defense Progress -->
                    <div class="col-lg-3 mb-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="text-muted">Defense Progress</h6>
                                <div id="defense-progress-chart" class="mt-3">
                                    <div class="progress-ring mx-auto">
                                        <canvas width="60" height="60"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Reports -->
                <div class="row mt-4">
                    <div class="col-12">
                        <h6 class="text-muted mb-3">Quick Reports</h6>
                        <div class="row">
                            <div class="col-md-3">
                                <a href="{{ route('admin.reports.subjects') }}" class="btn btn-outline-primary w-100 mb-2">
                                    <i class="bi bi-file-text me-2"></i>Subject Report
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('admin.reports.teams') }}" class="btn btn-outline-success w-100 mb-2">
                                    <i class="bi bi-people me-2"></i>Team Report
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('admin.reports.projects') }}" class="btn btn-outline-warning w-100 mb-2">
                                    <i class="bi bi-folder me-2"></i>Project Report
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('admin.reports.defenses') }}" class="btn btn-outline-info w-100 mb-2">
                                    <i class="bi bi-shield-check me-2"></i>Defense Report
                                </a>
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
        loadCriticalStats();
        loadCriticalActions();
        loadDepartmentOverview();
        loadPendingSubjects();
        loadActiveConflicts();
        loadDepartmentTeams();
        loadUpcomingDefenses();
    }

    async function loadCriticalStats() {
        try {
            const response = await axios.get('/api/workflow/status');
            const data = response.data.data;

            document.getElementById('pending-validations').textContent = data.pending_validations || 0;
            document.getElementById('active-conflicts').textContent = data.pending_conflicts || 0;
            document.getElementById('active-teams').textContent = data.active_teams || 0;
            document.getElementById('scheduled-defenses').textContent = data.scheduled_defenses || 0;
        } catch (error) {
            console.log('Could not load critical stats');
        }
    }

    async function loadCriticalActions() {
        try {
            const [conflictsRes, subjectsRes] = await Promise.all([
                axios.get('/api/conflicts'),
                axios.get('/api/subjects/pending-validation')
            ]);

            const conflicts = conflictsRes.data.data;
            const pendingSubjects = subjectsRes.data.data;

            const container = document.getElementById('critical-actions');
            const actions = [];

            if (pendingSubjects.length > 0) {
                actions.push({
                    type: 'validation',
                    icon: 'journal-text',
                    title: `${pendingSubjects.length} Subject(s) Pending Validation`,
                    description: 'Subjects are waiting for your approval before teams can select them',
                    action: 'Review & Validate',
                    url: '/subjects/pending-validation',
                    priority: 'warning'
                });
            }

            if (conflicts.length > 0) {
                actions.push({
                    type: 'conflict',
                    icon: 'exclamation-triangle',
                    title: `${conflicts.length} Active Conflict(s)`,
                    description: 'Schedule conflicts require immediate resolution to prevent delays',
                    action: 'Resolve Conflicts',
                    url: '/conflicts',
                    priority: 'danger'
                });
            }

            if (actions.length > 0) {
                container.innerHTML = actions.map(action => `
                    <div class="alert alert-${action.priority} d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <i class="bi bi-${action.icon}" style="font-size: 1.5rem;"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="alert-heading mb-1">${action.title}</h6>
                            <p class="mb-2">${action.description}</p>
                            <a href="${action.url}" class="btn btn-${action.priority} btn-sm">
                                <i class="bi bi-arrow-right me-1"></i>${action.action}
                            </a>
                        </div>
                    </div>
                `).join('');
            } else {
                container.innerHTML = `
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle me-2"></i>
                        <strong>Great!</strong> No critical actions required at this time.
                    </div>
                `;
            }
        } catch (error) {
            document.getElementById('critical-actions').innerHTML = `
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Could not load critical actions. Please refresh to try again.
                </div>
            `;
        }
    }

    async function loadDepartmentOverview() {
        try {
            const response = await axios.get('/api/reports/dashboard-stats');
            const data = response.data.data;

            const container = document.getElementById('department-stats');
            container.innerHTML = `
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <h4 class="text-primary">${data.total_students || 0}</h4>
                        <small class="text-muted">Students</small>
                    </div>
                    <div class="col-6 mb-3">
                        <h4 class="text-success">${data.total_teachers || 0}</h4>
                        <small class="text-muted">Teachers</small>
                    </div>
                    <div class="col-6 mb-3">
                        <h4 class="text-warning">${data.total_subjects || 0}</h4>
                        <small class="text-muted">Subjects</small>
                    </div>
                    <div class="col-6 mb-3">
                        <h4 class="text-info">${data.total_projects || 0}</h4>
                        <small class="text-muted">Projects</small>
                    </div>
                </div>
            `;
        } catch (error) {
            document.getElementById('department-stats').innerHTML = `
                <div class="text-center py-3">
                    <p class="text-muted">Could not load department overview</p>
                </div>
            `;
        }
    }

    async function loadPendingSubjects() {
        try {
            const response = await axios.get('/api/subjects/pending-validation');
            const subjects = response.data.data;

            const container = document.getElementById('pending-subjects');

            if (subjects.length > 0) {
                container.innerHTML = subjects.slice(0, 3).map(subject => `
                    <div class="d-flex align-items-center justify-content-between mb-3 p-3 bg-warning bg-opacity-10 rounded">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">${subject.title}</h6>
                            <p class="mb-1 text-muted small">By: ${subject.teacher.name}</p>
                            <span class="badge bg-warning">Pending Validation</span>
                        </div>
                        <div class="flex-shrink-0">
                            <a href="/subjects/${subject.id}/validate" class="btn btn-sm btn-warning">
                                <i class="bi bi-check2"></i>
                            </a>
                        </div>
                    </div>
                `).join('');
            } else {
                container.innerHTML = `
                    <div class="text-center py-3">
                        <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2">All subjects are validated</p>
                    </div>
                `;
            }
        } catch (error) {
            document.getElementById('pending-subjects').innerHTML = `
                <div class="text-center py-3">
                    <p class="text-muted">Could not load pending subjects</p>
                </div>
            `;
        }
    }

    async function loadActiveConflicts() {
        try {
            const response = await axios.get('/api/conflicts');
            const conflicts = response.data.data;

            const container = document.getElementById('active-conflicts-list');

            if (conflicts.length > 0) {
                container.innerHTML = conflicts.slice(0, 3).map(conflict => `
                    <div class="d-flex align-items-center justify-content-between mb-3 p-3 bg-danger bg-opacity-10 rounded">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">${conflict.type.replace('_', ' ').toUpperCase()} Conflict</h6>
                            <p class="mb-1 text-muted small">${conflict.description}</p>
                            <span class="badge bg-danger">Active</span>
                        </div>
                        <div class="flex-shrink-0">
                            <a href="/conflicts/${conflict.id}/resolve" class="btn btn-sm btn-danger">
                                <i class="bi bi-tools"></i>
                            </a>
                        </div>
                    </div>
                `).join('');
            } else {
                container.innerHTML = `
                    <div class="text-center py-3">
                        <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2">No active conflicts</p>
                    </div>
                `;
            }
        } catch (error) {
            document.getElementById('active-conflicts-list').innerHTML = `
                <div class="text-center py-3">
                    <p class="text-muted">Could not load conflicts</p>
                </div>
            `;
        }
    }

    async function loadDepartmentTeams() {
        try {
            const response = await axios.get('/api/teams');
            const teams = response.data.data;

            const container = document.getElementById('department-teams');

            if (teams.length > 0) {
                container.innerHTML = teams.slice(0, 4).map(team => `
                    <div class="d-flex align-items-center justify-content-between mb-2 p-2 bg-light rounded">
                        <div>
                            <h6 class="mb-0">${team.name}</h6>
                            <small class="text-muted">${team.members.length} members</small>
                        </div>
                        <span class="badge status-${team.status}">${team.status.replace('_', ' ').toUpperCase()}</span>
                    </div>
                `).join('');
            } else {
                container.innerHTML = `
                    <div class="text-center py-3">
                        <i class="bi bi-people text-muted" style="font-size: 2rem;"></i>
                        <p class="text-muted mt-2">No teams formed yet</p>
                    </div>
                `;
            }
        } catch (error) {
            document.getElementById('department-teams').innerHTML = `
                <div class="text-center py-3">
                    <p class="text-muted">Could not load teams</p>
                </div>
            `;
        }
    }

    async function loadUpcomingDefenses() {
        try {
            const response = await axios.get('/api/defenses');
            const defenses = response.data.data;
            const upcomingDefenses = defenses.filter(defense =>
                new Date(defense.defense_date) > new Date() && defense.status === 'scheduled'
            );

            const container = document.getElementById('upcoming-defenses');

            if (upcomingDefenses.length > 0) {
                container.innerHTML = upcomingDefenses.slice(0, 3).map(defense => `
                    <div class="d-flex align-items-center justify-content-between mb-3 p-3 bg-success bg-opacity-10 rounded">
                        <div>
                            <h6 class="mb-1">${defense.project.title || 'Project Defense'}</h6>
                            <p class="mb-1 text-muted small">
                                <i class="bi bi-calendar me-1"></i>${formatDateTime(defense.defense_date)}
                            </p>
                            <p class="mb-0 text-muted small">
                                <i class="bi bi-geo-alt me-1"></i>${defense.room.name}
                            </p>
                        </div>
                        <a href="/defenses/${defense.id}" class="btn btn-sm btn-outline-success">
                            <i class="bi bi-eye"></i>
                        </a>
                    </div>
                `).join('');
            } else {
                container.innerHTML = `
                    <div class="text-center py-3">
                        <i class="bi bi-calendar-x text-muted" style="font-size: 2rem;"></i>
                        <p class="text-muted mt-2">No upcoming defenses</p>
                    </div>
                `;
            }
        } catch (error) {
            document.getElementById('upcoming-defenses').innerHTML = `
                <div class="text-center py-3">
                    <p class="text-muted">Could not load defenses</p>
                </div>
            `;
        }
    }

    function formatDateTime(dateString) {
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