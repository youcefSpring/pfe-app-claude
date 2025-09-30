@extends('layouts.pfe-app')

@section('page-title', 'Dashboard')

@section('content')
<div class="row">
    <!-- Welcome Card -->
    <div class="col-12 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title mb-2">Welcome back, {{ auth()->user()->name }}!</h4>
                        <p class="card-text mb-0">
                            Academic Year: {{ now()->format('Y') }}/{{ now()->addYear()->format('Y') }}
                            @if(auth()->user()->department)
                                | Department: {{ auth()->user()->department }}
                            @endif
                            | Role: {{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}
                        </p>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-speedometer2" style="font-size: 3rem; opacity: 0.7;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Role-specific Dashboard Content -->
    @switch(auth()->user()->role)
        @case('student')
            @include('dashboard.partials.student-content')
            @break
        @case('teacher')
            @include('dashboard.partials.teacher-content')
            @break
        @case('department_head')
            @include('dashboard.partials.department-head-content')
            @break
        @case('admin')
            @include('dashboard.partials.admin-content')
            @break
        @default
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Your dashboard is being set up. Please contact the administrator if you need assistance.
                </div>
            </div>
    @endswitch
</div>

<!-- Quick Actions -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-lightning me-2"></i>Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @switch(auth()->user()->role)
                        @case('student')
                            <div class="col-md-6 col-lg-3">
                                <a href="{{ route('subjects.index') }}" class="btn btn-outline-primary w-100">
                                    <i class="bi bi-journal-text d-block mb-2" style="font-size: 1.5rem;"></i>
                                    Browse Subjects
                                </a>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <a href="{{ route('teams.index') }}" class="btn btn-outline-success w-100">
                                    <i class="bi bi-people d-block mb-2" style="font-size: 1.5rem;"></i>
                                    Manage Teams
                                </a>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <a href="{{ route('projects.index') }}" class="btn btn-outline-info w-100">
                                    <i class="bi bi-folder d-block mb-2" style="font-size: 1.5rem;"></i>
                                    My Projects
                                </a>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <a href="{{ route('defenses.index') }}" class="btn btn-outline-warning w-100">
                                    <i class="bi bi-shield-check d-block mb-2" style="font-size: 1.5rem;"></i>
                                    Defense Schedule
                                </a>
                            </div>
                            @break
                        @case('teacher')
                            <div class="col-md-6 col-lg-3">
                                <a href="{{ route('subjects.create') }}" class="btn btn-outline-primary w-100">
                                    <i class="bi bi-plus-circle d-block mb-2" style="font-size: 1.5rem;"></i>
                                    Add Subject
                                </a>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <a href="{{ route('projects.supervised') }}" class="btn btn-outline-success w-100">
                                    <i class="bi bi-eye d-block mb-2" style="font-size: 1.5rem;"></i>
                                    Supervised Projects
                                </a>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <a href="{{ route('defenses.jury-assignments') }}" class="btn btn-outline-info w-100">
                                    <i class="bi bi-people d-block mb-2" style="font-size: 1.5rem;"></i>
                                    Jury Assignments
                                </a>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <a href="{{ route('subjects.index') }}" class="btn btn-outline-warning w-100">
                                    <i class="bi bi-journal-text d-block mb-2" style="font-size: 1.5rem;"></i>
                                    My Subjects
                                </a>
                            </div>
                            @break
                        @case('department_head')
                            <div class="col-md-6 col-lg-3">
                                <a href="{{ route('subjects.pending-validation') }}" class="btn btn-outline-primary w-100">
                                    <i class="bi bi-check-circle d-block mb-2" style="font-size: 1.5rem;"></i>
                                    Validate Subjects
                                </a>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <a href="{{ route('defenses.schedule-form') }}" class="btn btn-outline-success w-100">
                                    <i class="bi bi-calendar-plus d-block mb-2" style="font-size: 1.5rem;"></i>
                                    Schedule Defense
                                </a>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <a href="{{ route('conflicts.index') }}" class="btn btn-outline-warning w-100">
                                    <i class="bi bi-exclamation-triangle d-block mb-2" style="font-size: 1.5rem;"></i>
                                    Resolve Conflicts
                                </a>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <a href="{{ route('admin.reports') }}" class="btn btn-outline-info w-100">
                                    <i class="bi bi-graph-up d-block mb-2" style="font-size: 1.5rem;"></i>
                                    Reports
                                </a>
                            </div>
                            @break
                        @case('admin')
                            <div class="col-md-6 col-lg-3">
                                <a href="{{ route('admin.users') }}" class="btn btn-outline-primary w-100">
                                    <i class="bi bi-people d-block mb-2" style="font-size: 1.5rem;"></i>
                                    Manage Users
                                </a>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <a href="{{ route('admin.students.upload') }}" class="btn btn-outline-success w-100">
                                    <i class="bi bi-upload d-block mb-2" style="font-size: 1.5rem;"></i>
                                    Upload Students
                                </a>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <a href="{{ route('admin.settings') }}" class="btn btn-outline-info w-100">
                                    <i class="bi bi-gear d-block mb-2" style="font-size: 1.5rem;"></i>
                                    System Settings
                                </a>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <a href="{{ route('admin.reports') }}" class="btn btn-outline-warning w-100">
                                    <i class="bi bi-graph-up d-block mb-2" style="font-size: 1.5rem;"></i>
                                    Analytics
                                </a>
                            </div>
                            @break
                    @endswitch
                </div>
            </div>
        </div>
    </div>
</div>

@if(isset($workflowStatus) && $workflowStatus)
<!-- Workflow Status -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-diagram-3 me-2"></i>Current Status
                </h5>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>Current Phase:</strong> {{ $workflowStatus['current_phase'] ?? 'Getting Started' }}</p>
                <p class="mb-2"><strong>Status:</strong> {{ $workflowStatus['status'] ?? 'In Progress' }}</p>
                @if(isset($workflowStatus['next_actions']) && count($workflowStatus['next_actions']) > 0)
                    <p class="mb-2"><strong>Next Actions:</strong></p>
                    <ul class="mb-0">
                        @foreach($workflowStatus['next_actions'] as $action)
                            <li>{{ $action }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
// Auto-refresh dashboard data every 5 minutes
setInterval(function() {
    // You can add AJAX calls here to refresh specific dashboard sections
    console.log('Dashboard auto-refresh');
}, 300000);
</script>
@endpush