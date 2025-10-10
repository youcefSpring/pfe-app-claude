@extends('layouts.pfe-app')

@section('page-title', __('app.system_administration'))

@section('content')
{{-- <div class="row">
    <!-- Welcome Card -->
    <div class="col-12 mb-4">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title mb-2">{{ __('app.system_administration_dashboard') }}</h4>
                        <p class="card-text mb-0">
                            <strong>{{ auth()->user()->name }}</strong> - System Administrator
                            | Academic Year: {{ now()->format('Y') }}/{{ now()->addYear()->format('Y') }}
                        </p>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-shield-lock" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> --}}

<div class="row">
    <!-- System Overview Stats -->
    <div class="col-md-3 mb-4">
        <div class="card text-center h-100 border-primary">
            <div class="card-body">
                <i class="bi bi-people text-primary mb-3" style="font-size: 2.5rem;"></i>
                <h5 class="card-title">{{ __('app.total_users') }}</h5>
                <h3 class="text-primary" id="total-users">-</h3>
                <small class="text-muted">{{ __('app.all_system_users') }}</small>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card text-center h-100 border-success">
            <div class="card-body">
                <i class="bi bi-journal-text text-success mb-3" style="font-size: 2.5rem;"></i>
                <h5 class="card-title">{{ __('app.active_subjects') }}</h5>
                <h3 class="text-success" id="active-subjects">-</h3>
                <small class="text-muted">{{ __('app.available_assigned') }}</small>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card text-center h-100 border-warning">
            <div class="card-body">
                <i class="bi bi-folder text-warning mb-3" style="font-size: 2.5rem;"></i>
                <h5 class="card-title">{{ __('app.active_projects') }}</h5>
                <h3 class="text-warning" id="active-projects">-</h3>
                <small class="text-muted">{{ __('app.in_development') }}</small>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card text-center h-100 border-info">
            <div class="card-body">
                <i class="bi bi-shield-check text-info mb-3" style="font-size: 2.5rem;"></i>
                <h5 class="card-title">{{ __('app.completed_defenses') }}</h5>
                <h3 class="text-info" id="completed-defenses">-</h3>
                <small class="text-muted">{{ __('app.this_academic_year') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- System Health & Alerts -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-activity me-2"></i>{{ __('app.system_health_alerts') }}
                </h5>
                <div class="d-flex align-items-center">
                    <span class="badge bg-success me-2" id="system-status">{{ __('app.online') }}</span>
                    <button class="btn btn-sm btn-outline-primary" onclick="refreshSystemHealth()">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div id="system-alerts">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted mt-2">Checking system health...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Admin Actions -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-lightning me-2"></i>Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                        <i class="bi bi-person-plus me-2"></i>Add User
                    </a>
                    <a href="{{ route('admin.users.bulk-import') }}" class="btn btn-outline-primary">
                        <i class="bi bi-upload me-2"></i>Bulk Import
                    </a>
                    <a href="{{ route('admin.reports') }}" class="btn btn-outline-success">
                        <i class="bi bi-file-earmark-text me-2"></i>Generate Report
                    </a>
                    <a href="{{ route('admin.settings') }}" class="btn btn-outline-warning">
                        <i class="bi bi-gear me-2"></i>System Settings
                    </a>
                </div>

                <!-- System Info -->
                <div class="mt-4">
                    <h6 class="text-muted">System Information</h6>
                    <small class="text-muted d-block">Laravel {{ app()->version() }}</small>
                    <small class="text-muted d-block">PHP {{ phpversion() }}</small>
                    <small class="text-muted d-block">Uptime: <span id="system-uptime">Loading...</span></small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- User Management Overview -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-people me-2"></i>User Management
                </h5>
                <a href="{{ route('admin.users') }}" class="btn btn-sm btn-outline-primary">
                    Manage All Users
                </a>
            </div>
            <div class="card-body">
                <div id="user-breakdown">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted mt-2">Loading user statistics...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Log -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-clock-history me-2"></i>System Activity
                </h5>
                <a href="{{ route('admin.logs') }}" class="btn btn-sm btn-outline-primary">
                    View All Logs
                </a>
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

<!-- Department Analytics -->
<div class="row">
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-building me-2"></i>Department Overview
                </h5>
            </div>
            <div class="card-body">
                <div id="department-overview">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted mt-2">Loading department analytics...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Analytics Dashboard -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-graph-up me-2"></i>System Analytics & Reports
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Academic Progress -->
                    <div class="col-lg-3 mb-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="text-muted mb-3">Academic Progress</h6>
                                <div class="progress-ring mx-auto mb-3">
                                    <canvas width="80" height="80" id="academic-progress"></canvas>
                                </div>
                                <div id="academic-stats">
                                    <small class="text-muted">Loading...</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- System Usage -->
                    <div class="col-lg-3 mb-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="text-muted mb-3">System Usage</h6>
                                <div class="progress-ring mx-auto mb-3">
                                    <canvas width="80" height="80" id="usage-chart"></canvas>
                                </div>
                                <div id="usage-stats">
                                    <small class="text-muted">Loading...</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Performance Metrics -->
                    <div class="col-lg-3 mb-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="text-muted mb-3">Performance</h6>
                                <div class="progress-ring mx-auto mb-3">
                                    <canvas width="80" height="80" id="performance-chart"></canvas>
                                </div>
                                <div id="performance-stats">
                                    <small class="text-muted">Loading...</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Integrity -->
                    <div class="col-lg-3 mb-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="text-muted mb-3">Data Integrity</h6>
                                <div class="progress-ring mx-auto mb-3">
                                    <canvas width="80" height="80" id="integrity-chart"></canvas>
                                </div>
                                <div id="integrity-stats">
                                    <small class="text-muted">Loading...</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Management Tools -->
                <div class="row mt-4">
                    <div class="col-12">
                        <h6 class="text-muted mb-3">Management Tools</h6>
                        <div class="row">
                            <div class="col-md-2">
                                <a href="{{ route('admin.users') }}" class="btn btn-outline-primary w-100 mb-2">
                                    <i class="bi bi-people me-2"></i>Users
                                </a>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('admin.reports.subjects') }}" class="btn btn-outline-success w-100 mb-2">
                                    <i class="bi bi-journal-text me-2"></i>Subjects
                                </a>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('admin.reports.teams') }}" class="btn btn-outline-warning w-100 mb-2">
                                    <i class="bi bi-people me-2"></i>Teams
                                </a>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('admin.reports.projects') }}" class="btn btn-outline-info w-100 mb-2">
                                    <i class="bi bi-folder me-2"></i>Projects
                                </a>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('admin.reports.defenses') }}" class="btn btn-outline-secondary w-100 mb-2">
                                    <i class="bi bi-shield-check me-2"></i>Defenses
                                </a>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('admin.settings') }}" class="btn btn-outline-dark w-100 mb-2">
                                    <i class="bi bi-gear me-2"></i>Settings
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
    loadAdminDashboard();

    function loadAdminDashboard() {
        loadSystemStats();
        loadSystemHealth();
        loadUserBreakdown();
        loadRecentActivity();
        loadDepartmentOverview();
        loadSystemUptime();
    }

    async function loadSystemStats() {
        try {
            const response = await axios.get('/api/reports/dashboard-stats');
            const data = response.data.data;

            document.getElementById('total-users').textContent = data.total_users || 0;
            document.getElementById('active-subjects').textContent = data.active_subjects || 0;
            document.getElementById('active-projects').textContent = data.active_projects || 0;
            document.getElementById('completed-defenses').textContent = data.completed_defenses || 0;
        } catch (error) {
            console.log('Could not load system stats');
        }
    }

    async function loadSystemHealth() {
        try {
            const response = await axios.get('/api/health');
            const isHealthy = response.data.status === 'healthy';

            const statusBadge = document.getElementById('system-status');
            const alertsContainer = document.getElementById('system-alerts');

            if (isHealthy) {
                statusBadge.className = 'badge bg-success me-2';
                statusBadge.textContent = 'Healthy';

                alertsContainer.innerHTML = `
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle me-2"></i>
                        <strong>System Status:</strong> All systems are operating normally.
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="bi bi-database text-success mb-2" style="font-size: 2rem;"></i>
                                    <h6>Database</h6>
                                    <span class="badge bg-success">Connected</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="bi bi-cloud text-success mb-2" style="font-size: 2rem;"></i>
                                    <h6>API Services</h6>
                                    <span class="badge bg-success">Operational</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="bi bi-shield-check text-success mb-2" style="font-size: 2rem;"></i>
                                    <h6>Security</h6>
                                    <span class="badge bg-success">Secure</span>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                statusBadge.className = 'badge bg-danger me-2';
                statusBadge.textContent = 'Issues Detected';

                alertsContainer.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>System Alert:</strong> Some services may be experiencing issues.
                    </div>
                `;
            }
        } catch (error) {
            const statusBadge = document.getElementById('system-status');
            statusBadge.className = 'badge bg-warning me-2';
            statusBadge.textContent = 'Unknown';

            document.getElementById('system-alerts').innerHTML = `
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Unable to determine system health. Please check connectivity.
                </div>
            `;
        }
    }

    async function loadUserBreakdown() {
        try {
            const response = await axios.get('/api/users/statistics');
            const data = response.data.data;

            const container = document.getElementById('user-breakdown');
            container.innerHTML = `
                <div class="row">
                    <div class="col-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-25 rounded-circle p-2 me-3">
                                <i class="bi bi-mortarboard text-primary"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">${data.students || 0}</h6>
                                <small class="text-muted">Students</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-success bg-opacity-25 rounded-circle p-2 me-3">
                                <i class="bi bi-person-workspace text-success"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">${data.teachers || 0}</h6>
                                <small class="text-muted">Teachers</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-warning bg-opacity-25 rounded-circle p-2 me-3">
                                <i class="bi bi-building text-warning"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">${data.department_heads || 0}</h6>
                                <small class="text-muted">Dept. Heads</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-danger bg-opacity-25 rounded-circle p-2 me-3">
                                <i class="bi bi-shield-lock text-danger"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">${data.admins || 0}</h6>
                                <small class="text-muted">Admins</small>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        } catch (error) {
            document.getElementById('user-breakdown').innerHTML = `
                <div class="text-center py-3">
                    <p class="text-muted">Could not load user statistics</p>
                </div>
            `;
        }
    }

    async function loadRecentActivity() {
        try {
            // Mock recent activity - would be actual system logs in real implementation
            const container = document.getElementById('recent-activity');
            container.innerHTML = `
                <div class="d-flex align-items-start mb-3">
                    <div class="flex-shrink-0">
                        <div class="bg-success bg-opacity-10 rounded-circle p-2">
                            <i class="bi bi-person-plus text-success"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">New user registered</h6>
                        <p class="mb-1 text-muted small">Student account created for John Doe</p>
                        <small class="text-muted">2 hours ago</small>
                    </div>
                </div>
                <div class="d-flex align-items-start mb-3">
                    <div class="flex-shrink-0">
                        <div class="bg-warning bg-opacity-10 rounded-circle p-2">
                            <i class="bi bi-journal-text text-warning"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">Subject validated</h6>
                        <p class="mb-1 text-muted small">AI Research Project approved by Dept. Head</p>
                        <small class="text-muted">4 hours ago</small>
                    </div>
                </div>
                <div class="d-flex align-items-start mb-3">
                    <div class="flex-shrink-0">
                        <div class="bg-info bg-opacity-10 rounded-circle p-2">
                            <i class="bi bi-shield-check text-info"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">Defense scheduled</h6>
                        <p class="mb-1 text-muted small">Machine Learning project defense booked</p>
                        <small class="text-muted">6 hours ago</small>
                    </div>
                </div>
            `;
        } catch (error) {
            document.getElementById('recent-activity').innerHTML = `
                <div class="text-center py-3">
                    <p class="text-muted">Could not load recent activity</p>
                </div>
            `;
        }
    }

    async function loadDepartmentOverview() {
        try {
            const response = await axios.get('/api/reports/dashboard-stats');
            const data = response.data.data;

            const container = document.getElementById('department-overview');
            container.innerHTML = `
                <div class="row">
                    <div class="col-md-3">
                        <div class="card bg-light text-center">
                            <div class="card-body">
                                <h4 class="text-primary">${data.computer_science_teams || 0}</h4>
                                <h6>Computer Science</h6>
                                <small class="text-muted">Active teams</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light text-center">
                            <div class="card-body">
                                <h4 class="text-success">${data.engineering_teams || 0}</h4>
                                <h6>Engineering</h6>
                                <small class="text-muted">Active teams</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light text-center">
                            <div class="card-body">
                                <h4 class="text-warning">${data.mathematics_teams || 0}</h4>
                                <h6>Mathematics</h6>
                                <small class="text-muted">Active teams</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light text-center">
                            <div class="card-body">
                                <h4 class="text-info">${data.physics_teams || 0}</h4>
                                <h6>Physics</h6>
                                <small class="text-muted">Active teams</small>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        } catch (error) {
            document.getElementById('department-overview').innerHTML = `
                <div class="text-center py-3">
                    <p class="text-muted">Could not load department overview</p>
                </div>
            `;
        }
    }

    function loadSystemUptime() {
        // Mock uptime - would be actual system uptime in real implementation
        document.getElementById('system-uptime').textContent = '15 days, 4 hours';
    }

    window.refreshSystemHealth = function() {
        loadSystemHealth();
    };
});
</script>
@endpush
