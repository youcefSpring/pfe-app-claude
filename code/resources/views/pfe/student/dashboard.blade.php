@extends('layouts.admin')

@section('title', 'Student Dashboard - PFE Platform')
@section('page-title', 'Student Dashboard')

@section('breadcrumb')
<li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<!-- Info boxes -->
<div class="row">
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-users"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Team Status</span>
                <span class="info-box-number">
                    {{ $teamStatus ?? 'Not in Team' }}
                </span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-project-diagram"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Project Status</span>
                <span class="info-box-number">{{ $projectStatus ?? 'Not Assigned' }}</span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-file-alt"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Deliverables</span>
                <span class="info-box-number">
                    {{ $stats['completed_deliverables'] ?? 0 }}/{{ $stats['total_deliverables'] ?? 0 }}
                </span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-graduation-cap"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Defense Date</span>
                <span class="info-box-number">{{ $defenseDate ?? 'Not Scheduled' }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Main row -->
<div class="row">
    <!-- Left col -->
    <section class="col-lg-8 connectedSortable">

        <!-- Team Information -->
        @if($team ?? null)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-users mr-1"></i>
                    My Team
                </h3>
                <div class="card-tools">
                    <a href="{{ route('pfe.student.teams.my-team') }}" class="btn btn-tool">
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Team Name:</strong> {{ $team->name }}<br>
                        <strong>Members:</strong> {{ $team->members->count() }}/{{ $team->max_members ?? 3 }}<br>
                        <strong>Status:</strong>
                        <span class="badge badge-{{ $team->status === 'complete' ? 'success' : 'warning' }}">
                            {{ ucfirst($team->status) }}
                        </span>
                    </div>
                    <div class="col-md-6">
                        <strong>Project:</strong> {{ $team->project->title ?? 'Not Assigned' }}<br>
                        <strong>Supervisor:</strong> {{ $team->project->supervisor->full_name ?? 'TBD' }}
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="card bg-warning">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    No Team Yet
                </h3>
            </div>
            <div class="card-body">
                <p>You are not currently part of any team. You need to either create a team or join an existing one.</p>
                <div class="btn-group" role="group">
                    <a href="{{ route('pfe.student.teams.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Team
                    </a>
                    <a href="{{ route('pfe.student.teams.browse') }}" class="btn btn-secondary">
                        <i class="fas fa-search"></i> Browse Teams
                    </a>
                </div>
            </div>
        </div>
        @endif

        <!-- Project Progress -->
        @if($project ?? null)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-tasks mr-1"></i>
                    Project Progress
                </h3>
                <div class="card-tools">
                    <a href="{{ route('pfe.student.projects.my-project') }}" class="btn btn-tool">
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="progress-group">
                    <span class="float-right"><b>{{ $projectProgress ?? 0 }}%</b></span>
                    <span>Overall Progress</span>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-primary" style="width: {{ $projectProgress ?? 0 }}%"></div>
                    </div>
                </div>

                @if($milestones ?? [])
                    @foreach($milestones as $milestone)
                    <div class="progress-group">
                        <span class="float-right"><b>{{ $milestone['completed'] ? '100' : '0' }}%</b></span>
                        <span>{{ $milestone['title'] }}</span>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-{{ $milestone['completed'] ? 'success' : 'secondary' }}"
                                 style="width: {{ $milestone['completed'] ? '100' : '0' }}%"></div>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
        @endif

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

        <!-- Calendar -->
        <div class="card bg-gradient-success">
            <div class="card-header border-0">
                <h3 class="card-title">
                    <i class="far fa-calendar-alt"></i>
                    Important Dates
                </h3>
            </div>
            <div class="card-body pt-0">
                <div class="text-center p-4">
                    @if($defenseDate ?? null)
                        <i class="fas fa-graduation-cap fa-3x text-white mb-3"></i>
                        <h4 class="text-white">Defense Scheduled</h4>
                        <p class="text-white">{{ $defenseDate }}</p>
                    @else
                        <i class="fas fa-calendar-alt fa-3x text-white mb-3"></i>
                        <h4 class="text-white">Defense Not Scheduled</h4>
                        <p class="text-white">Complete your project to schedule defense</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- To-Do List -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list mr-1"></i>
                    My Tasks
                </h3>
            </div>
            <div class="card-body">
                <ul class="todo-list" data-widget="todo-list">
                    @forelse($todos ?? [] as $todo)
                    <li class="{{ $todo['completed'] ? 'done' : '' }}">
                        <span class="text">{{ $todo['task'] }}</span>
                        <small class="badge badge-{{ $todo['priority'] === 'high' ? 'danger' : ($todo['priority'] === 'medium' ? 'warning' : 'info') }}">
                            {{ $todo['priority'] }}
                        </small>
                        <div class="tools">
                            @if(!$todo['completed'])
                                <i class="fas fa-check"></i>
                            @endif
                        </div>
                    </li>
                    @empty
                    <li>No pending tasks</li>
                    @endforelse
                </ul>
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
                    @if(!($team ?? null))
                        <a href="{{ route('pfe.student.teams.create') }}" class="btn btn-primary btn-sm mb-2">
                            <i class="fas fa-plus"></i> Create Team
                        </a>
                        <a href="{{ route('pfe.student.teams.browse') }}" class="btn btn-outline-primary btn-sm mb-2">
                            <i class="fas fa-search"></i> Join Team
                        </a>
                    @endif

                    @if($team ?? null && !($project ?? null))
                        <a href="{{ route('pfe.student.subjects.browse') }}" class="btn btn-success btn-sm mb-2">
                            <i class="fas fa-book"></i> Browse Subjects
                        </a>
                        <a href="{{ route('pfe.student.subjects.preferences') }}" class="btn btn-outline-success btn-sm mb-2">
                            <i class="fas fa-heart"></i> Set Preferences
                        </a>
                    @endif

                    @if($project ?? null)
                        <a href="{{ route('pfe.student.projects.deliverables', ['project' => $project->id]) }}" class="btn btn-warning btn-sm mb-2">
                            <i class="fas fa-upload"></i> Upload Deliverable
                        </a>
                        <a href="{{ route('pfe.student.projects.communication', ['project' => $project->id]) }}" class="btn btn-outline-warning btn-sm mb-2">
                            <i class="fas fa-comments"></i> Contact Supervisor
                        </a>
                    @endif
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
    $('.todo-list').todoList();
});
</script>
@endpush