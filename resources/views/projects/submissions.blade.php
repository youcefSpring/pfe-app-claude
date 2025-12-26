@extends('layouts.pfe-app')

@section('title', 'Project Submissions')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Project Submissions</h1>
            <h6 class="text-muted">{{ $project->subject->title ?? 'Project' }}</h6>
        </div>
        <div class="btn-group">
            @if(auth()->user()->role === 'student')
                <a href="{{ route('projects.submit-form', $project) }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> New Submission
                </a>
            @endif
            <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-primary">
                <i class="bi bi-eye"></i> View Project
            </a>
        </div>
    </div>

    @if($project->submissions->count() > 0)
        <div class="row">
            @foreach($project->submissions->sortByDesc('created_at') as $submission)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Submission #{{ $loop->iteration }}</h6>
                            <span class="badge bg-{{ $submission->status === 'approved' ? 'success' : ($submission->status === 'rejected' ? 'danger' : 'warning') }}">
                                {{ ucfirst($submission->status) }}
                            </span>
                        </div>

                        <div class="card-body">
                            <h6 class="card-title">{{ $submission->title ?? 'Untitled Submission' }}</h6>

                            @if($submission->description)
                                <p class="text-muted small">{{ Str::limit($submission->description, 100) }}</p>
                            @endif

                            <!-- Submission Info -->
                            <div class="small text-muted mb-3">
                                <p class="mb-1">
                                    <i class="bi bi-person"></i>
                                    Submitted by: {{ $submission->student->name ?? 'Unknown' }}
                                </p>
                                <p class="mb-1">
                                    <i class="bi bi-calendar"></i>
                                    {{ $submission->created_at->format('M d, Y \\a\\t g:i A') }}
                                </p>
                                @if($submission->files->count() > 0)
                                    <p class="mb-1">
                                        <i class="bi bi-paperclip"></i>
                                        {{ $submission->files->count() }} file(s)
                                    </p>
                                @endif
                            </div>

                            <!-- Files List -->
                            @if($submission->files->count() > 0)
                                <div class="mb-3">
                                    <h6 class="small text-muted mb-2">Attached Files:</h6>
                                    @foreach($submission->files->take(3) as $file)
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <small class="text-truncate me-2">
                                                <i class="bi bi-file-earmark"></i>
                                                {{ $file->original_name }}
                                            </small>
                                            <a href="{{ route('projects.download-submission', [$project, $submission, $file->filename]) }}"
                                               class="btn btn-outline-primary btn-xs">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        </div>
                                    @endforeach
                                    @if($submission->files->count() > 3)
                                        <small class="text-muted">... and {{ $submission->files->count() - 3 }} more files</small>
                                    @endif
                                </div>
                            @endif

                            <!-- Grade/Feedback -->
                            @if($submission->grade)
                                <div class="alert alert-success py-2">
                                    <strong>Grade: {{ $submission->grade }}/20</strong>
                                    @if($submission->feedback)
                                        <br><small>{{ Str::limit($submission->feedback, 80) }}</small>
                                    @endif
                                </div>
                            @elseif($submission->feedback)
                                <div class="alert alert-info py-2">
                                    <small><strong>Feedback:</strong> {{ Str::limit($submission->feedback, 80) }}</small>
                                </div>
                            @endif
                        </div>

                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('submissions.show', $submission) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-eye"></i> View Details
                                </a>

                                @if(auth()->user()->role === 'teacher' && $submission->status === 'pending')
                                    <div class="btn-group">
                                        <button class="btn btn-success btn-sm" onclick="gradeSubmission({{ $submission->id }}, 'approved')">
                                            <i class="bi bi-check"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm" onclick="gradeSubmission({{ $submission->id }}, 'rejected')">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- No Submissions -->
        <div class="text-center py-5">
            <i class="bi bi-file-earmark-plus text-muted" style="font-size: 4rem;"></i>
            <h4 class="mt-3">No Submissions Yet</h4>
            <p class="text-muted">
                @if(auth()->user()->role === 'student')
                    You haven't submitted any work for this project yet.<br>
                    Start by uploading your progress or documentation.
                @else
                    Students haven't submitted any work for this project yet.
                @endif
            </p>
            @if(auth()->user()->role === 'student')
                <a href="{{ route('projects.submit-form', $project) }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Create First Submission
                </a>
            @endif
        </div>
    @endif

    <!-- Project Timeline -->
    @if($project->submissions->count() > 0)
        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Submission Timeline</h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            @foreach($project->submissions->sortBy('created_at') as $submission)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-{{ $submission->status === 'approved' ? 'success' : ($submission->status === 'rejected' ? 'danger' : 'warning') }}"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">{{ $submission->title ?? 'Submission #' . $loop->iteration }}</h6>
                                        <p class="text-muted small mb-1">
                                            Submitted by {{ $submission->student->name ?? 'Unknown' }}
                                            on {{ $submission->created_at->format('M d, Y \\a\\t g:i A') }}
                                        </p>
                                        @if($submission->grade)
                                            <span class="badge bg-success">Grade: {{ $submission->grade }}/20</span>
                                        @else
                                            <span class="badge bg-{{ $submission->status === 'approved' ? 'success' : ($submission->status === 'rejected' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($submission->status) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Grade Submission Modal -->
<div class="modal fade" id="gradeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Grade Submission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="gradeForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="grade" class="form-label">Grade (0-20)</label>
                        <input type="number" class="form-control" id="grade" name="grade" min="0" max="20" step="0.5">
                    </div>
                    <div class="mb-3">
                        <label for="feedback" class="form-label">Feedback</label>
                        <textarea class="form-control" id="feedback" name="feedback" rows="3"
                                  placeholder="Provide feedback on the submission..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Grade</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.btn-xs {
    padding: 0.125rem 0.25rem;
    font-size: 0.75rem;
}

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
    margin-bottom: 20px;
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
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid #dee2e6;
}
</style>

<script>
function gradeSubmission(submissionId, status) {
    const form = document.getElementById('gradeForm');
    form.action = `/projects/{{ $project->id }}/submissions/${submissionId}/grade`;

    const modal = new bootstrap.Modal(document.getElementById('gradeModal'));
    modal.show();
}

document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            if (alert.classList.contains('alert-dismissible')) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }
        });
    }, 5000);
});
</script>
@endsection