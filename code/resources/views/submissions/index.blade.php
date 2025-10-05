@extends('layouts.pfe-app')

@section('page-title', __('app.all_submissions'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ __('app.all_submissions') }}</h4>
                    <small class="text-muted">{{ __('app.submissions_from_supervised_projects') }}</small>
                </div>
                <div class="card-body">
                    @if($submissions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('app.project') }}</th>
                                        <th>Team</th>
                                        <th>{{ __('app.submission') }}</th>
                                        <th>Type</th>
                                        <th>{{ __('app.submitted') }}</th>
                                        <th>{{ __('app.status') }}</th>
                                        <th>Grade</th>
                                        <th>{{ __('app.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($submissions as $submission)
                                        <tr>
                                            <td>
                                                <div>
                                                    <h6 class="mb-1">{{ $submission->project->subject->title ?? 'Project' }}</h6>
                                                    <small class="text-muted">ID: {{ $submission->project->id }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <div class="fw-bold">{{ $submission->project->team->name }}</div>
                                                    <small class="text-muted">{{ $submission->project->team->members->count() }} members</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <h6 class="mb-1">{{ $submission->title }}</h6>
                                                    <small class="text-muted">{{ Str::limit($submission->description, 50) }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ ucfirst($submission->submission_type) }}</span>
                                            </td>
                                            <td>
                                                <div>
                                                    <div>{{ $submission->submitted_at->format('M d, Y') }}</div>
                                                    <small class="text-muted">{{ $submission->submitted_at->diffForHumans() }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                @if($submission->status === 'submitted')
                                                    <span class="badge bg-warning">{{ __('app.pending_review') }}</span>
                                                @elseif($submission->status === 'approved')
                                                    <span class="badge bg-success">{{ __('app.approved') }}</span>
                                                @elseif($submission->status === 'needs_revision')
                                                    <span class="badge bg-warning">{{ __('app.needs_revision') }}</span>
                                                @elseif($submission->status === 'rejected')
                                                    <span class="badge bg-danger">{{ __('app.rejected') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($submission->grade)
                                                    <span class="badge bg-primary">{{ $submission->grade }}/20</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('submissions.show', $submission) }}"
                                                       class="btn btn-outline-primary btn-sm" title="{{ __('app.view_details') }}">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('projects.show', $submission->project) }}"
                                                       class="btn btn-outline-info btn-sm" title="{{ __('app.view_project') }}">
                                                        <i class="fas fa-project-diagram"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            <nav aria-label="Submissions pagination">
                                {{ $submissions->links('pagination::bootstrap-4') }}
                            </nav>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-file-upload fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">{{ __('app.no_submissions_yet') }}</h5>
                            <p class="text-muted">{{ __('app.no_submissions_from_projects') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection