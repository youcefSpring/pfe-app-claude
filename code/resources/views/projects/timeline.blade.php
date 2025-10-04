@extends('layouts.pfe-app')

@section('title', 'Project Timeline')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Project Timeline</h1>
            <h6 class="text-muted">{{ $project->subject->title ?? 'Project' }}</h6>
        </div>
        <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Project
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Project Progress Timeline</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <!-- Project Creation -->
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Project Created</h6>
                                <p class="text-muted small mb-1">
                                    Project was initiated on {{ $project->created_at->format('M d, Y \\a\\t g:i A') }}
                                </p>
                                <span class="badge bg-primary">Created</span>
                            </div>
                        </div>

                        <!-- Submissions Timeline -->
                        @foreach($project->submissions->sortBy('created_at') as $submission)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-{{ $submission->status === 'approved' ? 'success' : ($submission->status === 'rejected' ? 'danger' : 'warning') }}"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">{{ $submission->title }}</h6>
                                    <p class="text-muted small mb-2">
                                        Submitted by {{ $submission->student->name ?? 'Student' }} on
                                        {{ $submission->created_at->format('M d, Y \\a\\t g:i A') }}
                                    </p>
                                    @if($submission->description)
                                        <p class="small mb-2">{{ Str::limit($submission->description, 150) }}</p>
                                    @endif
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-{{ $submission->status === 'approved' ? 'success' : ($submission->status === 'rejected' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($submission->status) }}
                                        </span>
                                        @if($submission->grade)
                                            <span class="badge bg-info">Grade: {{ $submission->grade }}/20</span>
                                        @endif
                                        @if($submission->files->count() > 0)
                                            <small class="text-muted">{{ $submission->files->count() }} file(s)</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <!-- Reviews Timeline -->
                        @if($project->reviews && $project->reviews->count() > 0)
                            @foreach($project->reviews->sortBy('created_at') as $review)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-info"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Supervisor Review</h6>
                                        <p class="text-muted small mb-2">
                                            Reviewed by {{ $review->reviewer->name ?? 'Supervisor' }} on
                                            {{ $review->created_at->format('M d, Y \\a\\t g:i A') }}
                                        </p>
                                        @if($review->comments)
                                            <p class="small mb-2">{{ Str::limit($review->comments, 150) }}</p>
                                        @endif
                                        @if($review->rating)
                                            <span class="badge bg-info">Rating: {{ $review->rating }}/5</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        <!-- Defense Scheduled -->
                        @if($project->defense)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-{{ $project->defense->status === 'completed' ? 'success' : 'primary' }}"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Defense {{ $project->defense->status === 'completed' ? 'Completed' : 'Scheduled' }}</h6>
                                    @if($project->defense->defense_date)
                                        <p class="text-muted small mb-2">
                                            @if($project->defense->status === 'completed')
                                                Defense was held on {{ $project->defense->defense_date->format('M d, Y \\a\\t g:i A') }}
                                            @else
                                                Defense scheduled for {{ $project->defense->defense_date->format('M d, Y \\a\\t g:i A') }}
                                            @endif
                                        </p>
                                    @endif
                                    @if($project->defense->room)
                                        <p class="small mb-2">Room: {{ $project->defense->room->name }}</p>
                                    @endif
                                    <span class="badge bg-{{ $project->defense->status === 'completed' ? 'success' : 'primary' }}">
                                        {{ ucfirst(str_replace('_', ' ', $project->defense->status)) }}
                                    </span>
                                </div>
                            </div>
                        @endif

                        <!-- Project Completion -->
                        @if($project->status === 'completed')
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Project Completed</h6>
                                    <p class="text-muted small mb-1">
                                        Project was marked as completed on {{ $project->updated_at->format('M d, Y \\a\\t g:i A') }}
                                    </p>
                                    <span class="badge bg-success">Completed</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Project Statistics -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Project Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <h4 class="text-primary">{{ $project->submissions->count() }}</h4>
                            <small class="text-muted">Submissions</small>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success">{{ $project->submissions->where('status', 'approved')->count() }}</h4>
                            <small class="text-muted">Approved</small>
                        </div>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-6">
                            <h4 class="text-info">{{ $project->reviews->count() ?? 0 }}</h4>
                            <small class="text-muted">Reviews</small>
                        </div>
                        <div class="col-6">
                            <h4 class="text-{{ $project->defense ? 'success' : 'muted' }}">
                                @if($project->defense)
                                    <i class="bi bi-shield-check"></i>
                                @else
                                    <i class="bi bi-shield-x"></i>
                                @endif
                            </h4>
                            <small class="text-muted">Defense</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Project Progress -->
            @if($project->start_date && $project->end_date)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Project Progress</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $totalDays = $project->start_date->diffInDays($project->end_date);
                            $elapsedDays = $project->start_date->diffInDays(now());
                            $progressPercentage = $totalDays > 0 ? min(100, max(0, ($elapsedDays / $totalDays) * 100)) : 0;
                        @endphp

                        <div class="mb-3">
                            <div class="d-flex justify-content-between small text-muted mb-1">
                                <span>Time Progress</span>
                                <span>{{ round($progressPercentage) }}%</span>
                            </div>
                            <div class="progress mb-2" style="height: 8px;">
                                <div class="progress-bar bg-{{ $progressPercentage > 80 ? 'danger' : ($progressPercentage > 60 ? 'warning' : 'success') }}"
                                     style="width: {{ $progressPercentage }}%"></div>
                            </div>
                        </div>

                        <div class="small text-muted">
                            <p class="mb-1"><strong>Started:</strong> {{ $project->start_date->format('M d, Y') }}</p>
                            <p class="mb-1"><strong>Due:</strong> {{ $project->end_date->format('M d, Y') }}</p>
                            @if($project->end_date->isFuture())
                                <p class="mb-0"><strong>Remaining:</strong> {{ now()->diffInDays($project->end_date) }} days</p>
                            @elseif($project->status !== 'completed')
                                <p class="mb-0 text-danger"><strong>Overdue by:</strong> {{ $project->end_date->diffInDays(now()) }} days</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body d-grid gap-2">
                    @if(auth()->user()->role === 'student')
                        <a href="{{ route('projects.submit-form', $project) }}" class="btn btn-primary">
                            <i class="bi bi-upload"></i> New Submission
                        </a>
                    @endif
                    <a href="{{ route('projects.submissions', $project) }}" class="btn btn-outline-primary">
                        <i class="bi bi-file-earmark-text"></i> View Submissions
                    </a>
                    @if(auth()->user()->role === 'teacher')
                        <a href="{{ route('projects.review-form', $project) }}" class="btn btn-outline-success">
                            <i class="bi bi-clipboard-check"></i> Review Project
                        </a>
                    @endif
                    @if($project->defense)
                        <a href="{{ route('defenses.show', $project->defense) }}" class="btn btn-outline-info">
                            <i class="bi bi-shield-check"></i> View Defense
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
}

.timeline-content {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    border-left: 3px solid #dee2e6;
}
</style>
@endsection