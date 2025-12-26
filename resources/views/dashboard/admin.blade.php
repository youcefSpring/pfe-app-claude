@extends('layouts.pfe-app')

@section('page-title', __('app.system_administration'))

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="text-center mb-5">
        <h2 class="mb-2">Welcome, <strong>{{ auth()->user()->name }}</strong></h2>
        <p class="text-muted">Academic Year {{ now()->format('Y') }}/{{ now()->addYear()->format('Y') }}</p>
    </div>

    <!-- Quick Actions Only -->
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">
            <div class="quick-actions-grid">
                <a href="{{ route('admin.users') }}" class="action-card">
                    <div class="action-icon bg-primary">
                        <i class="bi bi-people"></i>
                    </div>
                    <h5>Manage Users</h5>
                    <p>Add, edit, and manage system users</p>
                </a>

                <a href="{{ route('admin.specialities.index') }}" class="action-card">
                    <div class="action-icon bg-success">
                        <i class="bi bi-diagram-3"></i>
                    </div>
                    <h5>Specialities</h5>
                    <p>Manage academic specialities</p>
                </a>

                <a href="{{ route('admin.academic-years.index') }}" class="action-card">
                    <div class="action-icon bg-warning">
                        <i class="bi bi-calendar-range"></i>
                    </div>
                    <h5>Academic Years</h5>
                    <p>Manage academic year settings</p>
                </a>

                <a href="{{ route('admin.settings') }}" class="action-card">
                    <div class="action-icon bg-info">
                        <i class="bi bi-gear"></i>
                    </div>
                    <h5>System Settings</h5>
                    <p>Configure application settings</p>
                </a>

                <a href="{{ route('admin.reports') }}" class="action-card">
                    <div class="action-icon bg-purple">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <h5>Reports</h5>
                    <p>View analytics and reports</p>
                </a>

                <a href="{{ route('admin.users.bulk-import') }}" class="action-card">
                    <div class="action-icon bg-cyan">
                        <i class="bi bi-upload"></i>
                    </div>
                    <h5>Bulk Import</h5>
                    <p>Import users from CSV/Excel</p>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
/* Simple Dashboard Styles */
.quick-actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
    padding: 2rem 0;
}

.action-card {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    text-decoration: none;
    color: inherit;
    border: 2px solid #e5e7eb;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.action-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    border-color: transparent;
    color: inherit;
}

.action-icon {
    width: 80px;
    height: 80px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    color: white;
    margin-bottom: 1.5rem;
    box-shadow: 0 8px 16px rgba(0,0,0,0.15);
}

.action-card h5 {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: #1a202c;
}

.action-card p {
    font-size: 0.9375rem;
    color: #6b7280;
    margin: 0;
    line-height: 1.5;
}

/* Color Classes */
.bg-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.bg-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.bg-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
.bg-info { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
.bg-purple { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }
.bg-cyan { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }

/* Responsive */
@media (max-width: 768px) {
    .quick-actions-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .action-card {
        padding: 1.5rem;
    }
    
    .action-icon {
        width: 64px;
        height: 64px;
        font-size: 2rem;
    }
}
</style>
@endsection
