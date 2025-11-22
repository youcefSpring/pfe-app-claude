@extends('layouts.pfe-app')

@section('page-title', __('app.system_administration'))

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">{{ __('app.dashboard') }}</h2>
            <p class="text-muted mb-0">Welcome back, <strong>{{ auth()->user()->name }}</strong></p>
        </div>
        <div class="text-end">
            <small class="text-muted d-block">Academic Year</small>
            <strong>{{ now()->format('Y') }}/{{ now()->addYear()->format('Y') }}</strong>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="quick-actions-modern">
                <a href="{{ route('admin.users') }}" class="quick-action-card">
                    <div class="quick-action-icon bg-primary-gradient">
                        <i class="bi bi-people"></i>
                    </div>
                    <span>Manage Users</span>
                </a>
                <a href="{{ route('admin.specialities.index') }}" class="quick-action-card">
                    <div class="quick-action-icon bg-success-gradient">
                        <i class="bi bi-diagram-3"></i>
                    </div>
                    <span>Specialities</span>
                </a>
                <a href="{{ route('admin.settings') }}" class="quick-action-card">
                    <div class="quick-action-icon bg-warning-gradient">
                        <i class="bi bi-gear"></i>
                    </div>
                    <span>System Settings</span>
                </a>
                <a href="{{ route('admin.reports') }}" class="quick-action-card">
                    <div class="quick-action-icon bg-info-gradient">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <span>Reports & Analytics</span>
                </a>
                <a href="{{ route('admin.users.create') }}" class="quick-action-card">
                    <div class="quick-action-icon bg-purple-gradient">
                        <i class="bi bi-person-plus"></i>
                    </div>
                    <span>Add User</span>
                </a>
                <a href="{{ route('admin.users.bulk-import') }}" class="quick-action-card">
                    <div class="quick-action-icon bg-cyan-gradient">
                        <i class="bi bi-upload"></i>
                    </div>
                    <span>Bulk Import</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-card-primary">
                <div class="stat-card-body">
                    <div class="stat-icon">
                        <i class="bi bi-people"></i>
                    </div>
                    <div class="stat-content">
                        <h3 id="total-users" class="stat-number">-</h3>
                        <p class="stat-label">Total Users</p>
                        <div class="stat-meta">
                            <span id="active-users-count" class="badge bg-success-soft">35 Active</span>
                            <span id="new-users-count" class="badge bg-info-soft">5 New</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-card-success">
                <div class="stat-card-body">
                    <div class="stat-icon">
                        <i class="bi bi-journal-text"></i>
                    </div>
                    <div class="stat-content">
                        <h3 id="active-subjects" class="stat-number">-</h3>
                        <p class="stat-label">Total Subjects</p>
                        <div class="stat-meta">
                            <span id="validated-subjects" class="badge bg-success-soft">12 Validated</span>
                            <span id="pending-subjects" class="badge bg-warning-soft">8 Pending</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-card-warning">
                <div class="stat-card-body">
                    <div class="stat-icon">
                        <i class="bi bi-folder"></i>
                    </div>
                    <div class="stat-content">
                        <h3 id="active-projects" class="stat-number">-</h3>
                        <p class="stat-label">Total Defenses</p>
                        <div class="stat-meta">
                            <span id="scheduled-defenses" class="badge bg-info-soft">0 Scheduled</span>
                            <span id="completed-defenses-badge" class="badge bg-success-soft">0 Done</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-card-info">
                <div class="stat-card-body">
                    <div class="stat-icon">
                        <i class="bi bi-mortarboard"></i>
                    </div>
                    <div class="stat-content">
                        <h3 id="total-specialities" class="stat-number">8</h3>
                        <p class="stat-label">Total Specialities</p>
                        <div class="stat-meta">
                            <span id="active-specialities" class="badge bg-success-soft">8 Active</span>
                            <span id="students-count" class="badge bg-info-soft">0 Students</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="row g-3">
        <!-- System Health -->
        <div class="col-lg-8">
            <div class="modern-card">
                <div class="modern-card-header">
                    <h5 class="modern-card-title">
                        <i class="bi bi-activity me-2"></i>System Health
                    </h5>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-success" id="system-status">Online</span>
                        <button class="btn btn-sm btn-light" onclick="refreshSystemHealth()">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </div>
                </div>
                <div class="modern-card-body">
                    <div id="system-alerts">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted mt-2 mb-0">Checking system health...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Statistics -->
        <div class="col-lg-4">
            <div class="modern-card">
                <div class="modern-card-header">
                    <h5 class="modern-card-title">
                        <i class="bi bi-graph-up me-2"></i>Quick Statistics
                    </h5>
                </div>
                <div class="modern-card-body">
                    <div class="quick-stat-item">
                        <div class="quick-stat-icon bg-primary-soft">
                            <i class="bi bi-calendar-check text-primary"></i>
                        </div>
                        <div class="quick-stat-content">
                            <h6 class="mb-0" id="today-logins">0</h6>
                            <small class="text-muted">Today's Logins</small>
                        </div>
                    </div>
                    <div class="quick-stat-item">
                        <div class="quick-stat-icon bg-success-soft">
                            <i class="bi bi-journal-check text-success"></i>
                        </div>
                        <div class="quick-stat-content">
                            <h6 class="mb-0" id="subjects-this-week">12</h6>
                            <small class="text-muted">Subjects This Week</small>
                        </div>
                    </div>
                    <div class="quick-stat-item">
                        <div class="quick-stat-icon bg-warning-soft">
                            <i class="bi bi-shield-check text-warning"></i>
                        </div>
                        <div class="quick-stat-content">
                            <h6 class="mb-0" id="defenses-this-month">0</h6>
                            <small class="text-muted">Defenses This Month</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Breakdown -->
        <div class="col-lg-6">
            <div class="modern-card">
                <div class="modern-card-header">
                    <h5 class="modern-card-title">
                        <i class="bi bi-people me-2"></i>User Management
                    </h5>
                    <a href="{{ route('admin.users') }}" class="btn btn-sm btn-primary">
                        Manage All
                    </a>
                </div>
                <div class="modern-card-body">
                    <div id="user-breakdown">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted mt-2 mb-0">Loading user statistics...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="col-lg-6">
            <div class="modern-card">
                <div class="modern-card-header">
                    <h5 class="modern-card-title">
                        <i class="bi bi-clock-history me-2"></i>Recent Activity
                    </h5>
                    <a href="{{ route('admin.logs') }}" class="btn btn-sm btn-outline-primary">
                        View All
                    </a>
                </div>
                <div class="modern-card-body">
                    <div id="recent-activity">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted mt-2 mb-0">Loading recent activity...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Modern Dashboard Styles */
.quick-actions-modern {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 1rem;
    padding: 1.5rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 16px;
    box-shadow: 0 8px 24px rgba(102, 126, 234, 0.2);
}

.quick-action-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
    padding: 1.25rem 0.75rem;
    background: rgba(255, 255, 255, 0.95);
    border-radius: 12px;
    text-decoration: none;
    color: #1a202c;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.quick-action-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.1);
    border-color: rgba(102, 126, 234, 0.3);
    color: #1a202c;
}

.quick-action-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.quick-action-card span {
    font-size: 0.875rem;
    font-weight: 600;
    text-align: center;
    line-height: 1.3;
}

/* Gradient Backgrounds */
.bg-primary-gradient { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.bg-success-gradient { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.bg-warning-gradient { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
.bg-info-gradient { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
.bg-purple-gradient { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }
.bg-cyan-gradient { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }

/* Stat Cards */
.stat-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid #e5e7eb;
    height: 100%;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.08);
}

.stat-card-body {
    padding: 1.5rem;
    display: flex;
    align-items: flex-start;
    gap: 1.25rem;
}

.stat-icon {
    width: 64px;
    height: 64px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    flex-shrink: 0;
}

.stat-card-primary .stat-icon {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.stat-card-success .stat-icon {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
}

.stat-card-warning .stat-icon {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
}

.stat-card-info .stat-icon {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
}

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    margin: 0 0 0.25rem 0;
    color: #1a202c;
}

.stat-label {
    font-size: 0.875rem;
    color: #6b7280;
    margin: 0 0 0.75rem 0;
    font-weight: 500;
}

.stat-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

/* Badge Soft Variants */
.bg-success-soft {
    background-color: #d1fae5;
    color: #065f46;
}

.bg-info-soft {
    background-color: #dbeafe;
    color: #1e40af;
}

.bg-warning-soft {
    background-color: #fef3c7;
    color: #92400e;
}

.bg-primary-soft {
    background-color: #e0e7ff;
    color: #3730a3;
}

/* Modern Cards */
.modern-card {
    background: white;
    border-radius: 16px;
    border: 1px solid #e5e7eb;
    overflow: hidden;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.modern-card-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f9fafb;
}

.modern-card-title {
    margin: 0;
    font-size: 1rem;
    font-weight: 600;
    color: #1a202c;
    display: flex;
    align-items: center;
}

.modern-card-body {
    padding: 1.5rem;
    flex: 1;
}

/* Quick Stat Items */
.quick-stat-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border-radius: 12px;
    margin-bottom: 0.75rem;
    background: #f9fafb;
    transition: all 0.2s ease;
}

.quick-stat-item:hover {
    background: #f3f4f6;
    transform: translateX(4px);
}

.quick-stat-item:last-child {
    margin-bottom: 0;
}

.quick-stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.quick-stat-content h6 {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1a202c;
}

.quick-stat-content small {
    font-size: 0.8125rem;
}

/* Responsive */
@media (max-width: 768px) {
    .quick-actions-modern {
        grid-template-columns: repeat(3, 1fr);
    }
    
    .stat-card-body {
        flex-direction: column;
        text-align: center;
    }
    
    .stat-icon {
        margin: 0 auto;
    }
}

@media (max-width: 576px) {
    .quick-actions-modern {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>
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
    }

    async function loadSystemStats() {
        try {
            const response = await axios.get('/api/reports/dashboard-stats');
            const data = response.data.data;

            document.getElementById('total-users').textContent = data.total_users || 0;
            document.getElementById('active-subjects').textContent = data.active_subjects || 0;
            document.getElementById('active-projects').textContent = data.active_projects || 0;
            document.getElementById('total-specialities').textContent = data.total_specialities || 8;
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
                statusBadge.className = 'badge bg-success';
                statusBadge.textContent = 'Healthy';

                alertsContainer.innerHTML = `
                    <div class="alert alert-success border-0 mb-3">
                        <i class="bi bi-check-circle me-2"></i>
                        <strong>System Status:</strong> All systems are operating normally.
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="text-center p-3 bg-light rounded-3">
                                <i class="bi bi-database text-success mb-2" style="font-size: 2rem;"></i>
                                <h6 class="mb-1">Database</h6>
                                <span class="badge bg-success">Connected</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 bg-light rounded-3">
                                <i class="bi bi-cloud text-success mb-2" style="font-size: 2rem;"></i>
                                <h6 class="mb-1">API Services</h6>
                                <span class="badge bg-success">Operational</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 bg-light rounded-3">
                                <i class="bi bi-shield-check text-success mb-2" style="font-size: 2rem;"></i>
                                <h6 class="mb-1">Security</h6>
                                <span class="badge bg-success">Secure</span>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                statusBadge.className = 'badge bg-danger';
                statusBadge.textContent = 'Issues Detected';

                alertsContainer.innerHTML = `
                    <div class="alert alert-warning border-0">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>System Alert:</strong> Some services may be experiencing issues.
                    </div>
                `;
            }
        } catch (error) {
            const statusBadge = document.getElementById('system-status');
            statusBadge.className = 'badge bg-warning';
            statusBadge.textContent = 'Unknown';

            document.getElementById('system-alerts').innerHTML = `
                <div class="alert alert-warning border-0">
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
                <div class="row g-3">
                    <div class="col-6">
                        <div class="quick-stat-item">
                            <div class="quick-stat-icon bg-primary-soft">
                                <i class="bi bi-mortarboard text-primary"></i>
                            </div>
                            <div class="quick-stat-content">
                                <h6>${data.students || 0}</h6>
                                <small class="text-muted">Students</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="quick-stat-item">
                            <div class="quick-stat-icon bg-success-soft">
                                <i class="bi bi-person-workspace text-success"></i>
                            </div>
                            <div class="quick-stat-content">
                                <h6>${data.teachers || 0}</h6>
                                <small class="text-muted">Teachers</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="quick-stat-item">
                            <div class="quick-stat-icon bg-warning-soft">
                                <i class="bi bi-building text-warning"></i>
                            </div>
                            <div class="quick-stat-content">
                                <h6>${data.department_heads || 0}</h6>
                                <small class="text-muted">Dept. Heads</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="quick-stat-item">
                            <div class="quick-stat-icon bg-info-soft">
                                <i class="bi bi-shield-lock text-primary"></i>
                            </div>
                            <div class="quick-stat-content">
                                <h6>${data.admins || 0}</h6>
                                <small class="text-muted">Admins</small>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        } catch (error) {
            document.getElementById('user-breakdown').innerHTML = `
                <div class="text-center py-3">
                    <p class="text-muted mb-0">Could not load user statistics</p>
                </div>
            `;
        }
    }

    async function loadRecentActivity() {
        const container = document.getElementById('recent-activity');
        container.innerHTML = `
            <div class="activity-item">
                <div class="activity-icon bg-success-soft">
                    <i class="bi bi-person-plus text-success"></i>
                </div>
                <div class="activity-content">
                    <h6 class="mb-1">New user registered</h6>
                    <p class="mb-1 text-muted small">Student account created</p>
                    <small class="text-muted">2 hours ago</small>
                </div>
            </div>
            <div class="activity-item">
                <div class="activity-icon bg-warning-soft">
                    <i class="bi bi-journal-text text-warning"></i>
                </div>
                <div class="activity-content">
                    <h6 class="mb-1">Subject validated</h6>
                    <p class="mb-1 text-muted small">AI Research Project approved</p>
                    <small class="text-muted">4 hours ago</small>
                </div>
            </div>
            <div class="activity-item">
                <div class="activity-icon bg-info-soft">
                    <i class="bi bi-shield-check text-info"></i>
                </div>
                <div class="activity-content">
                    <h6 class="mb-1">Defense scheduled</h6>
                    <p class="mb-1 text-muted small">ML project defense booked</p>
                    <small class="text-muted">6 hours ago</small>
                </div>
            </div>
        `;
    }

    window.refreshSystemHealth = function() {
        loadSystemHealth();
    };
});
</script>

<style>
.activity-item {
    display: flex;
    gap: 1rem;
    padding: 1rem;
    border-radius: 12px;
    margin-bottom: 0.75rem;
    background: #f9fafb;
    transition: all 0.2s ease;
}

.activity-item:hover {
    background: #f3f4f6;
    transform: translateX(4px);
}

.activity-item:last-child {
    margin-bottom: 0;
}

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.125rem;
    flex-shrink: 0;
}

.activity-content h6 {
    font-size: 0.9375rem;
    font-weight: 600;
    color: #1a202c;
}

.activity-content p {
    font-size: 0.8125rem;
}
</style>
@endpush
