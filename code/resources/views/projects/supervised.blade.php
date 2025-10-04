@extends('layouts.pfe-app')

@section('title', 'Supervised Projects')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">My Supervised Projects</h1>
        <div class="btn-group">
            <a href="{{ route('projects.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-list"></i> All Projects
            </a>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-house"></i> Dashboard
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <h3 class="text-primary">{{ $projects->where('status', 'active')->count() }}</h3>
                    <p class="text-muted mb-0">Active Projects</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <h3 class="text-warning">{{ $projects->where('status', 'in_progress')->count() }}</h3>
                    <p class="text-muted mb-0">In Progress</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body text-center">
                    <h3 class="text-success">{{ $projects->where('status', 'completed')->count() }}</h3>
                    <p class="text-muted mb-0">Completed</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body text-center">
                    <h3 class="text-info">{{ $projects->count() }}</h3>
                    <p class="text-muted mb-0">Total Supervised</p>
                </div>
            </div>
        </div>
    </div>

    @if($projects->count() > 0)
        <!-- Projects List -->
        <div class="row">
            @foreach($projects as $project)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 border-start border-{{ $project->status === 'completed' ? 'success' : ($project->status === 'in_progress' ? 'warning' : ($project->status === 'cancelled' ? 'danger' : 'primary')) }} border-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span class="badge bg-{{ $project->status === 'completed' ? 'success' : ($project->status === 'in_progress' ? 'warning' : ($project->status === 'cancelled' ? 'danger' : 'primary')) }}">
                                {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                            </span>
                            <small class="text-muted">
                                {{ $project->created_at->diffForHumans() }}
                            </small>
                        </div>

                        <div class="card-body">
                            <h5 class="card-title">{{ $project->subject->title ?? 'Project' }}</h5>
                            <h6 class="text-muted mb-3">{{ $project->team->name ?? 'No team assigned' }}</h6>

                            <!-- Project Progress -->
                            @if($project->start_date && $project->end_date)
                                @php
                                    $totalDays = $project->start_date->diffInDays($project->end_date);
                                    $elapsedDays = $project->start_date->diffInDays(now());
                                    $progressPercentage = $totalDays > 0 ? min(100, max(0, ($elapsedDays / $totalDays) * 100)) : 0;
                                @endphp
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between small text-muted mb-1">
                                        <span>Progress</span>
                                        <span>{{ round($progressPercentage) }}%</span>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-{{ $progressPercentage > 80 ? 'danger' : ($progressPercentage > 60 ? 'warning' : 'success') }}"
                                             style="width: {{ $progressPercentage }}%"></div>
                                    </div>
                                </div>
                            @endif

                            <!-- Timeline -->
                            @if($project->start_date || $project->end_date)
                                <div class="mb-3 small text-muted">
                                    @if($project->start_date)
                                        <p class="mb-1">
                                            <i class="bi bi-play-circle"></i>
                                            Started: {{ $project->start_date->format('M d, Y') }}
                                        </p>
                                    @endif
                                    @if($project->end_date)
                                        <p class="mb-1">
                                            <i class="bi bi-flag"></i>
                                            Due: {{ $project->end_date->format('M d, Y') }}
                                            @if($project->end_date->isPast() && $project->status !== 'completed')
                                                <span class="badge bg-danger ms-1">Overdue</span>
                                            @elseif($project->end_date->diffInDays(now()) <= 7 && $project->status !== 'completed')
                                                <span class="badge bg-warning ms-1">Due Soon</span>
                                            @endif
                                        </p>
                                    @endif
                                </div>
                            @endif

                            <!-- Team Members -->
                            @if($project->team && $project->team->members->count() > 0)
                                <div class="mb-3">
                                    <h6 class="small text-muted mb-2">Team Members:</h6>
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($project->team->members->take(3) as $member)
                                            <div class="avatar-xs bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                                 title="{{ $member->user->name }}">
                                                {{ substr($member->user->name, 0, 1) }}
                                            </div>
                                        @endforeach
                                        @if($project->team->members->count() > 3)
                                            <div class="avatar-xs bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center"
                                                 title="+{{ $project->team->members->count() - 3 }} more">
                                                +{{ $project->team->members->count() - 3 }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Quick Stats -->
                            <div class="row text-center small text-muted">
                                <div class="col-4">
                                    <div class="border-end">
                                        <strong class="d-block">{{ $project->submissions->count() ?? 0 }}</strong>
                                        <span>Submissions</span>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="border-end">
                                        <strong class="d-block">{{ $project->reviews->count() ?? 0 }}</strong>
                                        <span>Reviews</span>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <strong class="d-block">
                                        @if($project->defense)
                                            <i class="bi bi-shield-check text-success"></i>
                                        @else
                                            <i class="bi bi-shield-x text-muted"></i>
                                        @endif
                                    </strong>
                                    <span>Defense</span>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="btn-group">
                                    <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                    <a href="{{ route('projects.review-form', $project) }}" class="btn btn-outline-success btn-sm">
                                        <i class="bi bi-clipboard-check"></i> Review
                                    </a>
                                </div>

                                @if($project->status === 'active' || $project->status === 'in_progress')
                                    @if($project->end_date && $project->end_date->isPast())
                                        <span class="badge bg-danger">Overdue</span>
                                    @elseif($project->end_date && $project->end_date->diffInDays(now()) <= 3)
                                        <span class="badge bg-warning">{{ $project->end_date->diffInDays(now()) }} days left</span>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Action Buttons -->
        <div class="row mt-4">
            <div class="col-12 text-center">
                <div class="btn-group">
                    <a href="{{ route('projects.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-list"></i> View All Projects
                    </a>
                    <a href="{{ route('submissions.index') }}" class="btn btn-outline-success">
                        <i class="bi bi-file-earmark-text"></i> Review Submissions
                    </a>
                </div>
            </div>
        </div>
    @else
        <!-- No Projects -->
        <div class="text-center py-5">
            <i class="bi bi-clipboard-x text-muted" style="font-size: 4rem;"></i>
            <h4 class="mt-3">No Supervised Projects</h4>
            <p class="text-muted">
                You haven't been assigned to supervise any projects yet.<br>
                Projects are typically assigned by department heads or admins.
            </p>
            <a href="{{ route('projects.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-list"></i> View All Projects
            </a>
        </div>
    @endif
</div>

<style>
.avatar-xs {
    width: 24px;
    height: 24px;
    font-size: 10px;
    font-weight: 600;
}

.border-3 {
    border-width: 3px !important;
}

.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}
</style>
@endsection