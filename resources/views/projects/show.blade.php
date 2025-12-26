@extends('layouts.pfe-app')

@section('page-title', 'Project Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">{{ $project->title }}</h4>
                    <div>
                        <span class="badge bg-{{ $project->status === 'completed' ? 'success' : ($project->status === 'active' ? 'primary' : 'warning') }}">
                            {{ ucfirst($project->status) }}
                        </span>
                        @if($isSupervisor)
                            <a href="{{ route('projects.review-form', $project) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-eye"></i> Review Project
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <!-- Project Description -->
                            @if($project->description)
                            <div class="mb-4">
                                <h5>Project Description</h5>
                                <p class="text-muted">{{ $project->description }}</p>
                            </div>
                            @endif

                            <!-- Team Members -->
                            @if($project->team && $project->team->members)
                            <div class="mb-4">
                                <h5>Team Members ({{ $project->team->members->count() }})</h5>
                                <div class="row">
                                    @foreach($project->team->members as $member)
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-2 {{ $member->role === 'leader' ? 'border-primary' : 'border-light' }}">
                                                <div class="card-body p-3">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <h6 class="mb-1">{{ $member->user->name }}</h6>
                                                            <small class="text-muted">{{ $member->user->email }}</small>
                                                            <div>
                                                                <span class="badge bg-{{ $member->role === 'leader' ? 'primary' : 'secondary' }}">
                                                                    {{ ucfirst($member->role) }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <small class="text-muted">
                                                        Joined {{ $member->joined_at ? $member->joined_at->format('M d, Y') : 'N/A' }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            <!-- Subject Information -->
                            @if($project->subject)
                            <div class="mb-4">
                                <h5>Subject</h5>
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">{{ $project->subject->title }}</h6>
                                        @if($project->subject->description)
                                            <p class="card-text">{{ $project->subject->description }}</p>
                                        @endif
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                @if($project->subject->teacher)
                                                    <small class="text-muted">Teacher:</small>
                                                    <div>{{ $project->subject->teacher->name }}</div>
                                                @endif
                                            </div>
                                            <a href="{{ route('subjects.show', $project->subject) }}" class="btn btn-primary btn-sm">
                                                View Subject
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Project Content -->
                            @if($project->content)
                            <div class="mb-4">
                                <h5>Project Overview</h5>
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="project-content">
                                            {!! nl2br(e($project->content)) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Submissions -->
                            @if($project->submissions && $project->submissions->count() > 0)
                            <div class="mb-4">
                                <h5>Project Submissions ({{ $project->submissions->count() }})</h5>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Title</th>
                                                <th>Submitted By</th>
                                                <th>Date</th>
                                                <th>Grade</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($project->submissions->take(5) as $submission)
                                            <tr>
                                                <td>
                                                    <strong>{{ $submission->title ?? 'Submission #' . $submission->id }}</strong>
                                                    @if($submission->description)
                                                        <br><small class="text-muted">{{ Str::limit($submission->description, 50) }}</small>
                                                    @endif
                                                </td>
                                                <td>{{ $submission->submittedBy ? $submission->submittedBy->name : 'N/A' }}</td>
                                                <td>{{ $submission->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    @if($submission->grade)
                                                        <span class="badge bg-success">{{ $submission->grade }}/20</span>
                                                    @else
                                                        <span class="badge bg-warning">Pending</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($isSupervisor)
                                                        <a href="{{ route('submissions.show', $submission) }}" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                @if($isTeamMember)
                                <div class="d-flex gap-2 mt-3">
                                    <a href="{{ route('projects.submissions', $project) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-list"></i> View All Submissions
                                    </a>
                                    <a href="{{ route('projects.submit-form', $project) }}" class="btn btn-success btn-sm">
                                        <i class="fas fa-plus"></i> New Submission
                                    </a>
                                </div>
                                @endif
                            </div>
                            @endif

                            <!-- Related Projects -->
                            @if(isset($relatedProjects) && $relatedProjects->count() > 0)
                            <div class="mb-4">
                                <h5>Related Projects</h5>
                                <div class="row">
                                    @foreach($relatedProjects->take(3) as $relatedProject)
                                    <div class="col-md-4 mb-3">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <h6 class="card-title">
                                                    <a href="{{ route('projects.show', $relatedProject) }}" class="text-decoration-none">
                                                        {{ $relatedProject->title }}
                                                    </a>
                                                </h6>
                                                @if($relatedProject->description)
                                                    <p class="card-text text-muted small">{{ Str::limit($relatedProject->description, 80) }}</p>
                                                @endif
                                                <div class="d-flex gap-1">
                                                    <span class="badge bg-{{ $relatedProject->status === 'completed' ? 'success' : 'primary' }}">
                                                        {{ ucfirst($relatedProject->status) }}
                                                    </span>
                                                    @if($relatedProject->supervisor)
                                                        <span class="badge bg-secondary">{{ Str::limit($relatedProject->supervisor->name, 15) }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="col-md-4">
                            <!-- Project Actions -->
                            @if($isTeamMember || $isSupervisor)
                            <div class="card bg-light mb-4">
                                <div class="card-body">
                                    <h6 class="card-title">Actions</h6>
                                    <div class="d-grid gap-2">
                                        @if($isTeamMember)
                                            <a href="{{ route('projects.submit-form', $project) }}" class="btn btn-success btn-sm">
                                                <i class="fas fa-plus"></i> Submit Work
                                            </a>
                                            <a href="{{ route('projects.timeline', $project) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-clock"></i> Timeline
                                            </a>
                                            <a href="{{ route('projects.submissions', $project) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-list"></i> View Submissions
                                            </a>
                                        @endif

                                        @if($isSupervisor)
                                            <a href="{{ route('projects.review-form', $project) }}" class="btn btn-warning btn-sm">
                                                <i class="fas fa-eye"></i> Review Project
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Supervisor Info -->
                            @if($project->supervisor)
                            <div class="card bg-light mb-4">
                                <div class="card-body">
                                    <h6 class="card-title">Supervisor</h6>
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-user-tie me-2 text-primary"></i>
                                        <div>
                                            <strong>{{ $project->supervisor->name }}</strong>
                                            @if($project->supervisor->email)
                                                <br><small class="text-muted">{{ $project->supervisor->email }}</small>
                                            @endif
                                        </div>
                                    </div>
                                    @if($project->supervisor->department)
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-building me-2 text-secondary"></i>
                                            <small>{{ $project->supervisor->department }}</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @endif

                            <!-- Quick Stats -->
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Project Statistics</h6>
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="border-end">
                                                <h4 class="text-primary mb-0">{{ $project->submissions ? $project->submissions->count() : 0 }}</h4>
                                                <small class="text-muted">Submissions</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <h4 class="text-success mb-0">{{ $project->team && $project->team->members ? $project->team->members->count() : 0 }}</h4>
                                            <small class="text-muted">Members</small>
                                        </div>
                                    </div>

                                    <hr class="my-3">

                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <small class="text-muted">Status:</small>
                                        <span class="badge bg-{{ $project->status === 'completed' ? 'success' : ($project->status === 'active' ? 'primary' : 'warning') }}">
                                            {{ ucfirst($project->status) }}
                                        </span>
                                    </div>

                                    @if($project->created_at)
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <small class="text-muted">Created:</small>
                                        <small>{{ $project->created_at->format('M d, Y') }}</small>
                                    </div>
                                    @endif

                                    @if($project->updated_at)
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">Last Updated:</small>
                                        <small>{{ $project->updated_at->format('M d, Y') }}</small>
                                    </div>
                                    @endif
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