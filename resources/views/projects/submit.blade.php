@extends('layouts.pfe-app')

@section('title', 'Submit Project Work')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Submit Project Work</h1>
            <h6 class="text-muted">{{ $project->subject->title ?? 'Project' }}</h6>
        </div>
        <a href="{{ route('projects.submissions', $project) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Submissions
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">New Submission</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('projects.submit', $project) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="title" class="form-label">Submission Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror"
                                   id="title" name="title" value="{{ old('title') }}"
                                   placeholder="e.g., Week 1 Progress, Final Report, Demo Video..." required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="4" required
                                      placeholder="Describe what you're submitting, what you've accomplished, and any notes for your supervisor...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="files" class="form-label">Files <span class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('files') is-invalid @enderror"
                                   id="files" name="files[]" multiple required
                                   accept=".pdf,.doc,.docx,.ppt,.pptx,.txt,.zip,.rar,.jpg,.jpeg,.png,.mp4,.avi">
                            @error('files')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Allowed formats: PDF, DOC, DOCX, PPT, PPTX, TXT, ZIP, RAR, JPG, PNG, MP4, AVI<br>
                                Maximum file size: 20MB per file
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="submission_type" class="form-label">Submission Type</label>
                            <select class="form-select @error('submission_type') is-invalid @enderror" id="submission_type" name="submission_type">
                                <option value="progress" {{ old('submission_type', 'progress') == 'progress' ? 'selected' : '' }}>Progress Report</option>
                                <option value="deliverable" {{ old('submission_type') == 'deliverable' ? 'selected' : '' }}>Deliverable</option>
                                <option value="final" {{ old('submission_type') == 'final' ? 'selected' : '' }}>Final Submission</option>
                                <option value="revision" {{ old('submission_type') == 'revision' ? 'selected' : '' }}>Revision</option>
                                <option value="other" {{ old('submission_type') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('submission_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Additional Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                      id="notes" name="notes" rows="2"
                                      placeholder="Any additional information, issues encountered, or requests for feedback...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('projects.submissions', $project) }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-upload"></i> Submit Work
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Project Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Project Information</h5>
                </div>
                <div class="card-body">
                    <h6>{{ $project->subject->title }}</h6>
                    <p class="text-muted mb-2">{{ $project->team->name }}</p>

                    @if($project->supervisor)
                        <p class="small mb-1"><strong>Supervisor:</strong> {{ $project->supervisor->name }}</p>
                    @endif

                    @if($project->end_date)
                        <p class="small mb-1"><strong>Project Deadline:</strong> {{ $project->end_date->format('M d, Y') }}</p>
                    @endif

                    <p class="small mb-0"><strong>Status:</strong>
                        <span class="badge bg-{{ $project->status === 'active' ? 'success' : 'secondary' }}">
                            {{ ucfirst($project->status) }}
                        </span>
                    </p>
                </div>
            </div>

            <!-- Previous Submissions -->
            @if($project->submissions->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Recent Submissions</h5>
                    </div>
                    <div class="card-body">
                        @foreach($project->submissions->sortByDesc('created_at')->take(3) as $submission)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <h6 class="mb-0 small">{{ $submission->title }}</h6>
                                    <small class="text-muted">{{ $submission->created_at->diffForHumans() }}</small>
                                </div>
                                <span class="badge bg-{{ $submission->status === 'approved' ? 'success' : ($submission->status === 'rejected' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($submission->status) }}
                                </span>
                            </div>
                            @if(!$loop->last)<hr class="my-2">@endif
                        @endforeach

                        @if($project->submissions->count() > 3)
                            <div class="text-center mt-2">
                                <a href="{{ route('projects.submissions', $project) }}" class="btn btn-outline-primary btn-sm">
                                    View All Submissions
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Guidelines -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Submission Guidelines</h5>
                </div>
                <div class="card-body">
                    <ul class="small mb-0">
                        <li>Use clear, descriptive titles for your submissions</li>
                        <li>Include comprehensive descriptions of your work</li>
                        <li>Organize files in a logical structure</li>
                        <li>Test all files before submission</li>
                        <li>Submit regularly to track progress</li>
                        <li>Include source code, documentation, and demo materials</li>
                    </ul>

                    @if($project->requirements)
                        <hr>
                        <h6 class="small text-muted">Project Requirements:</h6>
                        <p class="small text-muted">{{ Str::limit($project->requirements, 200) }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('files');
    const maxFileSize = 20 * 1024 * 1024; // 20MB in bytes

    fileInput.addEventListener('change', function() {
        const files = Array.from(this.files);
        let totalSize = 0;
        let hasOversizedFile = false;

        files.forEach(file => {
            totalSize += file.size;
            if (file.size > maxFileSize) {
                hasOversizedFile = true;
            }
        });

        if (hasOversizedFile) {
            showAlert('{{ __('app.file_size_error') }}', '{{ __('app.files_exceed_limit') }}', 'warning');
            this.value = '';
            return;
        }

        if (totalSize > 100 * 1024 * 1024) { // 100MB total limit
            showAlert('{{ __('app.file_size_error') }}', '{{ __('app.total_size_exceeds_limit') }}', 'warning');
            this.value = '';
            return;
        }

        // Update file info display
        updateFileInfo(files);
    });

    function updateFileInfo(files) {
        const fileText = document.querySelector('.form-text');
        if (files.length > 0) {
            const totalSizeMB = files.reduce((sum, file) => sum + file.size, 0) / (1024 * 1024);
            fileText.innerHTML = `
                Selected ${files.length} file(s) (${totalSizeMB.toFixed(1)}MB total)<br>
                Allowed formats: PDF, DOC, DOCX, PPT, PPTX, TXT, ZIP, RAR, JPG, PNG, MP4, AVI<br>
                Maximum file size: 20MB per file
            `;
        }
    }
});
</script>
@endsection