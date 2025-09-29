@extends('layouts.pfe')

@section('title', 'Student Dashboard - PFE Platform')
@section('contentheader', 'Student Dashboard')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('pfe.student.dashboard') }}">Home</a></li>
<li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<!-- Statistics Cards -->
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ $teamStatus ?? 'No Team' }}</h3>
                <p>Team Status</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
            <a href="{{ route('pfe.student.teams.my-team') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $projectStatus ?? 'No Project' }}</h3>
                <p>Project Status</p>
            </div>
            <div class="icon">
                <i class="fas fa-project-diagram"></i>
            </div>
            <a href="{{ route('pfe.student.projects.my-project') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $stats['completed_deliverables'] ?? 0 }}/{{ $stats['total_deliverables'] ?? 0 }}</h3>
                <p>Deliverables</p>
            </div>
            <div class="icon">
                <i class="fas fa-file-alt"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $defenseDate ? 'Scheduled' : 'TBD' }}</h3>
                <p>Defense Date</p>
            </div>
            <div class="icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <a href="{{ route('pfe.student.defense.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<!-- My Team Status -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-users mr-2"></i>
            My Team Status
        </h3>
        <a class="btn btn-primary float-right" href="{{ route('pfe.student.teams.my-team') }}">
            <i class="fas fa-eye"></i>
            View Team Details
        </a>
    </div>
    <div class="card-body">
        @if($team ?? null)
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Team Name</th>
                            <th>Members</th>
                            <th>Status</th>
                            <th>Project</th>
                            <th>Progress</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $team->name ?? 'My Team' }}</td>
                            <td><span class="badge badge-info">{{ $team->members->count() ?? 0 }}/3</span></td>
                            <td>
                                <span class="badge badge-{{ $team->status === 'validated' ? 'success' : 'warning' }}">
                                    {{ ucfirst($team->status ?? 'forming') }}
                                </span>
                            </td>
                            <td>{{ $team->project->title ?? 'Not Assigned' }}</td>
                            <td>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-primary" style="width: {{ $projectProgress ?? 0 }}%"></div>
                                </div>
                                <small>{{ $projectProgress ?? 0 }}% Complete</small>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-warning text-center my-4">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                You are not part of any team yet. Create or join a team to get started.
                <div class="mt-3">
                    <a href="{{ route('pfe.student.teams.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Team
                    </a>
                    <a href="{{ route('pfe.student.teams.browse') }}" class="btn btn-outline-primary">
                        <i class="fas fa-search"></i> Browse Teams
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- My Tasks -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-tasks mr-2"></i>
            My Tasks
        </h3>
        <a class="btn btn-warning float-right" href="#">
            <i class="fas fa-plus"></i>
            Add Task
        </a>
    </div>
    <div class="card-body">
        @if(isset($todos) && count($todos) > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Task</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($todos as $todo)
                            <tr>
                                <td>{{ $todo['task'] }}</td>
                                <td>
                                    @if($todo['priority'] === 'high')
                                        <span class="badge badge-danger">High</span>
                                    @elseif($todo['priority'] === 'medium')
                                        <span class="badge badge-warning">Medium</span>
                                    @else
                                        <span class="badge badge-info">Low</span>
                                    @endif
                                </td>
                                <td>
                                    @if($todo['completed'])
                                        <span class="badge badge-success">Completed</span>
                                    @else
                                        <span class="badge badge-warning">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!$todo['completed'])
                                        <a href="#" title="Mark Complete">
                                            <i class="fa fa-check text-success"></i>
                                        </a>
                                    @endif
                                    <a href="#" title="Edit" class="ml-2">
                                        <i class="fa fa-edit text-primary"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info text-center my-4">
                <i class="fas fa-info-circle mr-2"></i>
                No tasks found. All caught up!
            </div>
        @endif
    </div>
</div>

<!-- Recent Activities -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-clock mr-2"></i>
            Recent Activities
        </h3>
        <a class="btn btn-info float-right" href="#">
            <i class="fas fa-history"></i>
            View All
        </a>
    </div>
    <div class="card-body">
        @if(isset($recentActivities) && count($recentActivities) > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Activity</th>
                            <th>Description</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentActivities as $activity)
                            <tr>
                                <td>{{ $activity['title'] ?? 'Activity' }}</td>
                                <td>{{ $activity['description'] ?? 'No description' }}</td>
                                <td>
                                    <small class="text-muted">{{ $activity['date'] ?? 'Recently' }}</small>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info text-center my-4">
                <i class="fas fa-info-circle mr-2"></i>
                No recent activities to display.
            </div>
        @endif
    </div>
</div>

@endsection