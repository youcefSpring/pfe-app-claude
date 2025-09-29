@extends('layouts.pfe')

@section('title', 'Teacher Dashboard - PFE Platform')
@section('contentheader', 'Teacher Dashboard')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('pfe.teacher.dashboard') }}">Home</a></li>
<li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<!-- Small boxes (Stat box) -->
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $stats['my_subjects'] ?? 0 }}</h3>
                <p>My Subjects</p>
            </div>
            <div class="icon">
                <i class="fas fa-book"></i>
            </div>
            <a href="{{ route('pfe.teacher.subjects.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $stats['supervised_projects'] ?? 0 }}</h3>
                <p>Supervised Projects</p>
            </div>
            <div class="icon">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <a href="{{ route('pfe.teacher.supervision.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $stats['pending_reviews'] ?? 0 }}</h3>
                <p>Pending Reviews</p>
            </div>
            <div class="icon">
                <i class="fas fa-file-alt"></i>
            </div>
            <a href="{{ route('pfe.teacher.deliverables.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $stats['upcoming_defenses'] ?? 0 }}</h3>
                <p>Upcoming Defenses</p>
            </div>
            <div class="icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <a href="{{ route('pfe.teacher.defenses.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<!-- Main row -->
<div class="row">
    <!-- Left col -->
    <section class="col-lg-8 connectedSortable">

        <!-- My Subjects -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-book mr-1"></i>
                    My Recent Subjects
                </h3>
                <div class="card-tools">
                    <a href="{{ route('pfe.teacher.subjects.index') }}" class="btn btn-tool">
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Subject Title</th>
                            <th>Status</th>
                            <th>Interest</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentSubjects ?? [] as $subject)
                        <tr>
                            <td>{{ Str::limit($subject['title'], 40) }}</td>
                            <td>
                                <span class="badge badge-{{ $subject['status'] === 'published' ? 'success' : ($subject['status'] === 'approved' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($subject['status']) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $subject['interested_teams'] ?? 0 }} teams</span>
                            </td>
                            <td>
                                <a href="{{ route('pfe.teacher.subjects.show', $subject['id']) }}" class="btn btn-xs btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">No subjects created yet</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Supervised Projects Status -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-project-diagram mr-1"></i>
                    Supervised Projects Overview
                </h3>
                <div class="card-tools">
                    <a href="{{ route('pfe.teacher.supervision.index') }}" class="btn btn-tool">
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    @forelse($supervisedProjects ?? [] as $project)
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">{{ Str::limit($project['title'], 30) }}</h5>
                                <p class="card-text">
                                    <strong>Team:</strong> {{ $project['team_name'] }}<br>
                                    <strong>Progress:</strong>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-primary" style="width: {{ $project['progress'] ?? 0 }}%"></div>
                                    </div>
                                </p>
                                <a href="{{ route('pfe.teacher.supervision.project', $project['id']) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center">
                        <p class="text-muted">No supervised projects yet</p>
                        <a href="{{ route('pfe.teacher.subjects.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create Your First Subject
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-clock mr-1"></i>
                    Recent Activities
                </h3>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @forelse($recentActivities ?? [] as $activity)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $activity['title'] }}</strong><br>
                            <small class="text-muted">{{ $activity['description'] }}</small>
                        </div>
                        <small class="text-muted">{{ $activity['date'] }}</small>
                    </li>
                    @empty
                    <li class="list-group-item">No recent activities</li>
                    @endforelse
                </ul>
            </div>
        </div>

    </section>

    <!-- Right col -->
    <section class="col-lg-4">

        <!-- Pending Reviews -->
        <div class="card bg-gradient-warning">
            <div class="card-header border-0">
                <h3 class="card-title">
                    <i class="fas fa-file-alt"></i>
                    Pending Reviews
                </h3>
                <div class="card-tools">
                    <a href="{{ route('pfe.teacher.deliverables.index') }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="text-center p-2">
                    <h2 class="text-white">{{ $stats['pending_reviews'] ?? 0 }}</h2>
                    <p class="text-white">Deliverables awaiting review</p>
                    @if(($stats['pending_reviews'] ?? 0) > 0)
                        <a href="{{ route('pfe.teacher.deliverables.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-eye"></i> Review Now
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Defense Schedule -->
        <div class="card bg-gradient-info">
            <div class="card-header border-0">
                <h3 class="card-title">
                    <i class="fas fa-graduation-cap"></i>
                    Defense Schedule
                </h3>
                <div class="card-tools">
                    <a href="{{ route('pfe.teacher.defenses.calendar') }}" class="btn btn-info btn-sm">
                        <i class="fas fa-calendar"></i>
                    </a>
                </div>
            </div>
            <div class="card-body">
                @forelse($upcomingDefenses ?? [] as $defense)
                    <div class="d-flex justify-content-between border-bottom mb-2 pb-2">
                        <div>
                            <strong>{{ Str::limit($defense['project_title'], 25) }}</strong><br>
                            <small>{{ $defense['team_name'] }}</small>
                        </div>
                        <div class="text-right">
                            <small>{{ $defense['date'] }}</small><br>
                            <small>{{ $defense['time'] }}</small>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-white p-2">
                        <p>No upcoming defenses</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie mr-1"></i>
                    Quick Stats
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="description-block border-right">
                            <span class="description-percentage text-success">
                                <i class="fas fa-check"></i>
                            </span>
                            <h5 class="description-header">{{ $stats['completed_reviews'] ?? 0 }}</h5>
                            <span class="description-text">COMPLETED REVIEWS</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="description-block">
                            <span class="description-percentage text-warning">
                                <i class="fas fa-hourglass-half"></i>
                            </span>
                            <h5 class="description-header">{{ $stats['avg_response_time'] ?? 0 }}</h5>
                            <span class="description-text">AVG RESPONSE (DAYS)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-bolt mr-1"></i>
                    Quick Actions
                </h3>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('pfe.teacher.subjects.create') }}" class="btn btn-primary btn-sm mb-2">
                        <i class="fas fa-plus"></i> Create New Subject
                    </a>
                    <a href="{{ route('pfe.teacher.deliverables.index') }}" class="btn btn-warning btn-sm mb-2">
                        <i class="fas fa-file-alt"></i> Review Deliverables
                    </a>
                    <a href="{{ route('pfe.teacher.supervision.reports') }}" class="btn btn-info btn-sm mb-2">
                        <i class="fas fa-chart-bar"></i> Progress Reports
                    </a>
                    <a href="{{ route('pfe.teacher.defenses.calendar') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-calendar"></i> View Defense Calendar
                    </a>
                </div>
            </div>
        </div>

    </section>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize AdminLTE components
    // Add any teacher-specific JavaScript here
});
</script>
@endpush