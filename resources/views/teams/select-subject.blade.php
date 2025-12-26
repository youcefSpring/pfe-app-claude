@extends('layouts.pfe-app')

@section('page-title', 'Select Subject - ' . $team->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-book"></i> Select Subject for {{ $team->name }}
                    </h4>
                    <a href="{{ route('teams.show', $team) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Team
                    </a>
                </div>
                <div class="card-body">
                    <!-- Deadline Info -->
                    <div class="alert alert-info mb-4">
                        <h6><i class="fas fa-clock"></i> Selection Period</h6>
                        <p class="mb-1">Subject selection deadline: {{ $currentDeadline->preferences_deadline->format('F d, Y H:i') }}</p>
                        <p class="mb-0">Time remaining: {{ $currentDeadline->getRemainingTimeForPreferences() }}</p>
                    </div>

                    <!-- Team Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6>Team Information</h6>
                                    <p class="mb-1"><strong>Team:</strong> {{ $team->name }}</p>
                                    <p class="mb-1"><strong>Members:</strong> {{ $team->members->count() }}</p>
                                    <p class="mb-0"><strong>Status:</strong>
                                        <span class="badge bg-{{ $team->status === 'forming' ? 'warning' : 'success' }}">
                                            {{ ucfirst($team->status) }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6>Selection Rules</h6>
                                    <ul class="mb-0 small">
                                        <li>Only team leaders can select subjects</li>
                                        <li>Team must have {{ config('team.sizes.licence.min', 2) }}-{{ config('team.sizes.licence.max', 4) }} members</li>
                                        <li>Subject must be validated and available</li>
                                        <li>Selection is final once confirmed</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Available Subjects -->
                    @if($availableSubjects->count() > 0)
                        <h5 class="mb-3">Available Subjects ({{ $availableSubjects->total() }})</h5>

                        <div class="row">
                            @foreach($availableSubjects as $subject)
                                <div class="col-lg-6 col-xl-4 mb-4">
                                    <div class="card h-100 border-2 border-success">
                                        <div class="card-header bg-success text-white">
                                            <h6 class="card-title mb-0">{{ $subject->title }}</h6>
                                            <small>by {{ $subject->teacher->name ?? 'N/A' }}</small>
                                        </div>
                                        <div class="card-body d-flex flex-column">
                                            <div class="mb-3 flex-grow-1">
                                                <p class="card-text">{{ Str::limit($subject->description, 150) }}</p>

                                                @if($subject->keywords)
                                                    <div class="mb-2">
                                                        <small class="text-muted">Keywords:</small><br>
                                                        @foreach(explode(',', $subject->keywords) as $keyword)
                                                            <span class="badge bg-light text-dark me-1">{{ trim($keyword) }}</span>
                                                        @endforeach
                                                    </div>
                                                @endif

                                                @if($subject->tools)
                                                    <div class="mb-2">
                                                        <small class="text-muted">Tools:</small><br>
                                                        @foreach(explode(',', $subject->tools) as $tool)
                                                            <span class="badge bg-info text-white me-1">{{ trim($tool) }}</span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="mt-auto">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <span class="badge bg-success">Validated</span>
                                                        @if($subject->is_external)
                                                            <span class="badge bg-warning">External</span>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <a href="{{ route('subjects.show', $subject) }}"
                                                           class="btn btn-outline-primary btn-sm me-2" target="_blank">
                                                            <i class="fas fa-eye"></i> View
                                                        </a>
                                                        <button type="button" class="btn btn-success btn-sm"
                                                                onclick="confirmSelection({{ $subject->id }}, '{{ $subject->title }}')">
                                                            <i class="fas fa-check"></i> Select
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if($availableSubjects->hasPages())
                            <div class="d-flex justify-content-center">
                                {{ $availableSubjects->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-exclamation-circle fa-3x text-warning mb-3"></i>
                            <h5>No Available Subjects</h5>
                            <p class="text-muted">There are currently no validated subjects available for selection.</p>
                            <a href="{{ route('subjects.index') }}" class="btn btn-primary">
                                <i class="fas fa-search"></i> Browse All Subjects
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Selection Confirmation Modal -->
<div class="modal fade" id="selectionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Subject Selection</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Important:</strong> This action cannot be undone. Once you select a subject, your team will be committed to working on this project.
                </div>
                <p>Are you sure you want to select <strong id="selectedSubjectTitle"></strong> for your team?</p>
                <p class="text-muted small">This will:</p>
                <ul class="text-muted small">
                    <li>Create a project for your team</li>
                    <li>Set your team status to "active"</li>
                    <li>Make the subject unavailable to other teams</li>
                    <li>Assign the subject's teacher as your supervisor</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="selectionForm" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="subject_id" id="selectedSubjectId">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Confirm Selection
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmSelection(subjectId, subjectTitle) {
    document.getElementById('selectedSubjectId').value = subjectId;
    document.getElementById('selectedSubjectTitle').textContent = subjectTitle;
    document.getElementById('selectionForm').action = '{{ route('teams.select-subject', $team) }}';

    const modal = new bootstrap.Modal(document.getElementById('selectionModal'));
    modal.show();
}
</script>
@endpush