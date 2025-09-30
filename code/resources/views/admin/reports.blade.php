@extends('layouts.pfe-app')

@section('page-title', 'Reports')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">System Reports</h4>
                    <small class="text-muted">View detailed reports and statistics</small>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $stats['total_users'] }}</h3>
                                    <p class="mb-0">Total Users</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $stats['total_students'] }}</h3>
                                    <p class="mb-0">Students</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $stats['total_teachers'] }}</h3>
                                    <p class="mb-0">Teachers</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $stats['total_specialities'] }}</h3>
                                    <p class="mb-0">Specialities</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Detailed Reports</h5>
                                </div>
                                <div class="card-body">
                                    <div class="list-group">
                                        <a href="{{ route('admin.reports.subjects') }}" class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">Subjects Report</h6>
                                                <i class="fas fa-book text-primary"></i>
                                            </div>
                                            <p class="mb-1">View detailed statistics about subjects, validation status, and teacher assignments.</p>
                                        </a>
                                        <a href="{{ route('admin.reports.teams') }}" class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">Teams Report</h6>
                                                <i class="fas fa-users text-success"></i>
                                            </div>
                                            <p class="mb-1">Analyze team formation, status distribution, and member statistics.</p>
                                        </a>
                                        <a href="{{ route('admin.reports.projects') }}" class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">Projects Report</h6>
                                                <i class="fas fa-project-diagram text-info"></i>
                                            </div>
                                            <p class="mb-1">Monitor project progress, supervisor assignments, and completion rates.</p>
                                        </a>
                                        <a href="{{ route('admin.reports.defenses') }}" class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">Defenses Report</h6>
                                                <i class="fas fa-graduation-cap text-warning"></i>
                                            </div>
                                            <p class="mb-1">Track defense schedules, jury assignments, and evaluation results.</p>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Quick Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('admin.analytics') }}" class="btn btn-primary">
                                            <i class="fas fa-chart-bar"></i> View Analytics
                                        </a>
                                        <a href="{{ route('admin.users') }}" class="btn btn-success">
                                            <i class="fas fa-users"></i> Manage Users
                                        </a>
                                        <a href="{{ route('admin.settings') }}" class="btn btn-info">
                                            <i class="fas fa-cog"></i> System Settings
                                        </a>
                                        <a href="{{ route('admin.maintenance') }}" class="btn btn-warning">
                                            <i class="fas fa-tools"></i> Maintenance
                                        </a>
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