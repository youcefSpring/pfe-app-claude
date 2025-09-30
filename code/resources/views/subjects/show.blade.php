@extends('layouts.pfe-app')

@section('page-title', 'Subject Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">{{ $subject->title }}</h4>
                    <div>
                        @can('update', $subject)
                            <a href="{{ route('subjects.edit', $subject) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        @endcan
                        @if($subject->status === 'draft' && auth()->user()->id === $subject->teacher_id)
                            <form action="{{ route('subjects.submit-validation', $subject) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="fas fa-paper-plane"></i> Submit for Validation
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-4">
                                <h5>Description</h5>
                                <div class="border p-3 bg-light rounded">
                                    {!! nl2br(e($subject->description)) !!}
                                </div>
                            </div>

                            <div class="mb-4">
                                <h5>Project Plan</h5>
                                <div class="border p-3 bg-light rounded">
                                    {!! nl2br(e($subject->plan)) !!}
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <h6>Keywords</h6>
                                        <div class="d-flex flex-wrap">
                                            @foreach(explode(',', $subject->keywords) as $keyword)
                                                <span class="badge bg-secondary me-1 mb-1">{{ trim($keyword) }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <h6>Tools & Technologies</h6>
                                        <div class="d-flex flex-wrap">
                                            @foreach(explode(',', $subject->tools) as $tool)
                                                <span class="badge bg-info me-1 mb-1">{{ trim($tool) }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($subject->validation_notes)
                                <div class="mb-4">
                                    <h6>Validation Notes</h6>
                                    <div class="alert alert-info">
                                        {{ $subject->validation_notes }}
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Subject Information</h6>

                                    <div class="mb-3">
                                        <small class="text-muted">Status</small>
                                        <div>
                                            @if($subject->status === 'draft')
                                                <span class="badge bg-secondary">Draft</span>
                                            @elseif($subject->status === 'pending_validation')
                                                <span class="badge bg-warning">Pending Validation</span>
                                            @elseif($subject->status === 'validated')
                                                <span class="badge bg-success">Validated</span>
                                            @elseif($subject->status === 'rejected')
                                                <span class="badge bg-danger">Rejected</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <small class="text-muted">Type</small>
                                        <div>
                                            @if($subject->is_external)
                                                <span class="badge bg-info">External Subject</span>
                                            @else
                                                <span class="badge bg-primary">Internal Subject</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <small class="text-muted">Proposed by</small>
                                        @if($subject->is_external && $subject->student)
                                            <div>{{ $subject->student->name }}</div>
                                            <small class="text-muted">Student</small>
                                        @elseif($subject->teacher)
                                            <div>{{ $subject->teacher->name }}</div>
                                            <small class="text-muted">{{ $subject->teacher->department }}</small>
                                        @else
                                            <div class="text-muted">Not assigned</div>
                                        @endif
                                    </div>

                                    @if($subject->validated_by)
                                        <div class="mb-3">
                                            <small class="text-muted">Validated by</small>
                                            <div>{{ $subject->validator?->name }}</div>
                                            <small class="text-muted">{{ $subject->validated_at?->format('M d, Y') }}</small>
                                        </div>
                                    @endif

                                    @if($subject->is_external)
                                        @if($subject->company_name)
                                            <div class="mb-3">
                                                <small class="text-muted">Company/Organization</small>
                                                <div>{{ $subject->company_name }}</div>
                                            </div>
                                        @endif

                                        @if($subject->dataset_resources_link)
                                            <div class="mb-3">
                                                <small class="text-muted">Resources Link</small>
                                                <div>
                                                    <a href="{{ $subject->dataset_resources_link }}" target="_blank" class="btn btn-outline-info btn-sm">
                                                        <i class="bi bi-link-45deg me-1"></i>View Resources
                                                    </a>
                                                </div>
                                            </div>
                                        @endif
                                    @endif

                                    <div class="mb-3">
                                        <small class="text-muted">Created</small>
                                        <div>{{ $subject->created_at->format('M d, Y') }}</div>
                                    </div>

                                    @if($subject->projects->count() > 0)
                                        <div class="mb-3">
                                            <small class="text-muted">Assigned Projects</small>
                                            @foreach($subject->projects as $project)
                                                <div class="border-start border-primary ps-2 mb-2">
                                                    <div class="fw-bold">{{ $project->team->name }}</div>
                                                    <small class="text-muted">
                                                        {{ $project->team->members->count() }} members
                                                    </small>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>

                            @if($subject->status === 'validated' && !$subject->projects->count() && auth()->user()->role === 'student')
                                <div class="mt-3">
                                    <div class="alert alert-success">
                                        <h6>Available for Selection</h6>
                                        <p class="mb-2">This subject is available for team selection.</p>
                                        @if(auth()->user()->teamMember?->team)
                                            <a href="{{ route('teams.show', auth()->user()->teamMember->team) }}" class="btn btn-success btn-sm">
                                                View My Team
                                            </a>
                                        @else
                                            <a href="{{ route('teams.create') }}" class="btn btn-primary btn-sm">
                                                Create Team First
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection