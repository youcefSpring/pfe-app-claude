@extends('layouts.pfe')

@section('title', 'Admin Dashboard - PFE Platform')
@section('contentheader', 'Admin Dashboard')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('pfe.dashboard') }}">Home</a></li>
<li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<!-- Statistics Cards -->
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $stats['total_subjects'] ?? 0 }}</h3>
                <p>Total Subjects</p>
            </div>
            <div class="icon">
                <i class="fas fa-book"></i>
            </div>
            <a href="{{ route('pfe.subjects.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $stats['total_teams'] ?? 0 }}</h3>
                <p>Active Teams</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
            <a href="{{ route('pfe.teams.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $stats['total_projects'] ?? 0 }}</h3>
                <p>Active Projects</p>
            </div>
            <div class="icon">
                <i class="fas fa-project-diagram"></i>
            </div>
            <a href="{{ route('pfe.projects.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
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
            <a href="{{ route('pfe.defenses.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<!-- Recent Subjects -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-book mr-2"></i>
            Recent Subjects
        </h3>
        <a class="btn btn-primary float-right" href="{{ route('pfe.subjects.create') }}">
            <i class="fas fa-plus"></i>
            Add New Subject
        </a>
    </div>
    <div class="card-body">
        @if(isset($recentSubjects) && count($recentSubjects) > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Supervisor</th>
                            <th>Status</th>
                            <th>Domain</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentSubjects as $subject)
                            <tr>
                                <td>{{ Str::limit($subject['title'], 50) }}</td>
                                <td>{{ $subject['supervisor'] ?? 'TBD' }}</td>
                                <td>
                                    @if($subject['status'] === 'published')
                                        <span class="badge badge-success">Published</span>
                                    @elseif($subject['status'] === 'approved')
                                        <span class="badge badge-primary">Approved</span>
                                    @elseif($subject['status'] === 'pending')
                                        <span class="badge badge-warning">Pending</span>
                                    @else
                                        <span class="badge badge-secondary">Draft</span>
                                    @endif
                                </td>
                                <td>{{ $subject['domain'] ?? 'General' }}</td>
                                <td>
                                    <small class="text-muted">{{ $subject['created_at'] ?? now()->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <a href="{{ route('pfe.subjects.show', $subject['id'] ?? 1) }}" title="View">
                                        <i class="fa fa-eye text-primary"></i>
                                    </a>
                                    <a href="{{ route('pfe.subjects.edit', $subject['id'] ?? 1) }}" title="Edit" class="ml-2">
                                        <i class="fa fa-edit text-success"></i>
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
                No subjects found. Start by creating your first subject.
            </div>
        @endif
    </div>
</div>

<!-- Active Teams -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-users mr-2"></i>
            Active Teams
        </h3>
        <a class="btn btn-success float-right" href="{{ route('pfe.teams.index') }}">
            <i class="fas fa-eye"></i>
            View All Teams
        </a>
    </div>
    <div class="card-body">
        @if(isset($activeTeams) && count($activeTeams) > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Team Name</th>
                            <th>Members</th>
                            <th>Status</th>
                            <th>Project</th>
                            <th>Leader</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activeTeams as $team)
                            <tr>
                                <td>{{ $team['name'] ?? 'Team ' . ($team['id'] ?? 1) }}</td>
                                <td>
                                    <span class="badge badge-info">{{ $team['member_count'] ?? 0 }}/3</span>
                                </td>
                                <td>
                                    @if($team['status'] === 'validated')
                                        <span class="badge badge-success">Validated</span>
                                    @elseif($team['status'] === 'formed')
                                        <span class="badge badge-primary">Formed</span>
                                    @else
                                        <span class="badge badge-warning">Forming</span>
                                    @endif
                                </td>
                                <td>{{ $team['project'] ?? 'Not Assigned' }}</td>
                                <td>{{ $team['leader'] ?? 'TBD' }}</td>
                                <td>
                                    <small class="text-muted">{{ $team['created_at'] ?? now()->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <a href="{{ route('pfe.teams.show', $team['id'] ?? 1) }}" title="View">
                                        <i class="fa fa-eye text-primary"></i>
                                    </a>
                                    <a href="{{ route('pfe.teams.edit', $team['id'] ?? 1) }}" title="Edit" class="ml-2">
                                        <i class="fa fa-edit text-success"></i>
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
                No active teams found.
            </div>
        @endif
    </div>
</div>

<!-- Pending Actions -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-tasks mr-2"></i>
            Pending Actions
        </h3>
        <a class="btn btn-warning float-right" href="{{ route('pfe.admin.dashboard') }}">
            <i class="fas fa-list"></i>
            View All Tasks
        </a>
    </div>
    <div class="card-body">
        @if(isset($pendingActions) && count($pendingActions) > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Task</th>
                            <th>Priority</th>
                            <th>Count</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingActions as $action)
                            <tr>
                                <td>{{ $action['task'] ?? 'Pending Task' }}</td>
                                <td>
                                    @if($action['priority'] === 'high')
                                        <span class="badge badge-danger">High</span>
                                    @elseif($action['priority'] === 'medium')
                                        <span class="badge badge-warning">Medium</span>
                                    @else
                                        <span class="badge badge-info">Low</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-primary">{{ $action['count'] ?? 0 }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-warning">Pending</span>
                                </td>
                                <td>
                                    <a href="{{ $action['url'] ?? '#' }}" title="View">
                                        <i class="fa fa-eye text-primary"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-success text-center my-4">
                <i class="fas fa-check-circle mr-2"></i>
                No pending actions. All tasks are up to date!
            </div>
        @endif
    </div>
</div>

@endsection