@extends('layouts.pfe-app')

@section('page-title', __('app.submission_details'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">{{ $submission->title }}</h4>
                    <div>
                        @if($submission->status === 'submitted')
                            <span class="badge bg-warning">{{ __('app.pending_review') }}</span>
                        @elseif($submission->status === 'approved')
                            <span class="badge bg-success">{{ __('app.approved') }}</span>
                        @elseif($submission->status === 'needs_revision')
                            <span class="badge bg-warning">{{ __('app.needs_revision') }}</span>
                        @elseif($submission->status === 'rejected')
                            <span class="badge bg-danger">{{ __('app.rejected') }}</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-4">
                                <h5>{{ __('app.description') }}</h5>
                                <div class="border p-3 bg-light rounded">
                                    {!! nl2br(e($submission->description)) !!}
                                </div>
                            </div>

                            @if($submission->notes)
                                <div class="mb-4">
                                    <h5>{{ __('app.notes') }}</h5>
                                    <div class="border p-3 bg-light rounded">
                                        {!! nl2br(e($submission->notes)) !!}
                                    </div>
                                </div>
                            @endif

                            @if($submission->feedback)
                                <div class="mb-4">
                                    <h5>{{ __('app.supervisor_feedback') }}</h5>
                                    <div class="alert alert-info">
                                        {!! nl2br(e($submission->feedback)) !!}
                                    </div>
                                </div>
                            @endif

                            <div class="mb-4">
                                <h5>{{ __('app.files') }}</h5>
                                @php
                                    $files = json_decode($submission->files, true) ?? [];
                                @endphp
                                @if(count($files) > 0)
                                    <div class="list-group">
                                        @foreach($files as $file)
                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <i class="fas fa-file me-2"></i>
                                                    <strong>{{ $file['original_name'] }}</strong>
                                                    <small class="text-muted d-block">
                                                        Size: {{ number_format($file['size'] / 1024, 2) }} KB
                                                        | Type: {{ $file['mime_type'] }}
                                                    </small>
                                                </div>
                                                <a href="{{ route('projects.download-submission', [$submission->project, $submission, $file['stored_name']]) }}"
                                                   class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-download"></i> {{ __('app.download') }}
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted">{{ __('app.no_files_uploaded') }}</p>
                                @endif
                            </div>

                            @if($canGrade && $submission->status === 'submitted')
                                <div class="mb-4">
                                    <h5>{{ __('app.grade_submission') }}</h5>
                                    <form action="{{ route('projects.grade-submission', [$submission->project, $submission]) }}" method="POST">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="grade" class="form-label">{{ __('app.grade_0_20') }}</label>
                                                    <input type="number" class="form-control" id="grade" name="grade"
                                                           min="0" max="20" step="0.5" value="{{ old('grade', $submission->grade) }}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="mb-3">
                                                    <label for="status" class="form-label">{{ __('app.status') }}</label>
                                                    <select class="form-select" id="status" name="status" required>
                                                        <option value="approved" {{ old('status') === 'approved' ? 'selected' : '' }}>{{ __('app.approved') }}</option>
                                                        <option value="needs_revision" {{ old('status') === 'needs_revision' ? 'selected' : '' }}>{{ __('app.needs_revision') }}</option>
                                                        <option value="rejected" {{ old('status') === 'rejected' ? 'selected' : '' }}>{{ __('app.rejected') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="feedback" class="form-label">{{ __('app.feedback') }}</label>
                                            <textarea class="form-control" id="feedback" name="feedback" rows="4"
                                                      placeholder="Provide feedback to the students...">{{ old('feedback', $submission->feedback) }}</textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Submit Grade
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>

                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">{{ __('app.submission_information') }}</h6>

                                    <div class="mb-3">
                                        <small class="text-muted">Type</small>
                                        <div>
                                            <span class="badge bg-info">{{ ucfirst($submission->submission_type) }}</span>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <small class="text-muted">{{ __('app.submitted_by') }}</small>
                                        <div>{{ $submission->submittedBy->name }}</div>
                                        <small class="text-muted">{{ $submission->submitted_at->format('M d, Y H:i') }}</small>
                                    </div>

                                    @if($submission->grade)
                                        <div class="mb-3">
                                            <small class="text-muted">{{ __('app.grade') }}</small>
                                            <div>
                                                <span class="badge bg-primary">{{ $submission->grade }}/20</span>
                                            </div>
                                        </div>
                                    @endif

                                    @if($submission->graded_at)
                                        <div class="mb-3">
                                            <small class="text-muted">{{ __('app.graded') }}</small>
                                            <div>{{ $submission->graded_at->format('M d, Y H:i') }}</div>
                                        </div>
                                    @endif

                                    <div class="mb-3">
                                        <small class="text-muted">{{ __('app.project') }}</small>
                                        <div>
                                            <a href="{{ route('projects.show', $submission->project) }}">
                                                {{ $submission->project->subject->title ?? 'View Project' }}
                                            </a>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <small class="text-muted">Team</small>
                                        <div>{{ $submission->project->team->name }}</div>
                                        <small class="text-muted">{{ $submission->project->team->members->count() }} members</small>
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