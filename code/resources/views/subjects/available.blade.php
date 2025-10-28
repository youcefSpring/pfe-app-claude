@extends('layouts.pfe-app')

@section('page-title', 'Available Subjects')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Available Subjects</h4>
                    <div class="d-flex gap-2">
                        <form method="GET" action="{{ route('subjects.available') }}" class="d-flex gap-2">
                            <select name="grade" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">All Grades</option>
                                <option value="master" {{ request('grade') === 'master' ? 'selected' : '' }}>Master</option>
                                <option value="phd" {{ request('grade') === 'phd' ? 'selected' : '' }}>PhD</option>
                            </select>
                        </form>
                        @if(auth()->user()->role === 'student' && !auth()->user()->teamMember)
                            <a href="{{ route('teams.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-users"></i> Create Team
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if($subjects->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('app.title') }}</th>
                                        <th>{{ __('app.proposed_by') }}</th>
                                        <th>{{ __('app.description') }}</th>
                                        <th>{{ __('app.keywords') }}</th>
                                        <th>{{ __('app.tools') }}</th>
                                        <th>{{ __('app.created_at') }}</th>
                                        <th>{{ __('app.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($subjects as $subject)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <strong class="text-primary">{{ $subject->title }}</strong>
                                                        <br><span class="badge bg-success">{{ __('app.available') }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $subject->teacher->name }}</strong>
                                                    <br><small class="text-muted">{{ $subject->teacher->department }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span title="{{ $subject->description }}">
                                                    {{ Str::limit($subject->description, 80) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-wrap">
                                                    @foreach(array_slice(explode(',', $subject->keywords), 0, 2) as $keyword)
                                                        <span class="badge bg-secondary me-1 mb-1">{{ trim($keyword) }}</span>
                                                    @endforeach
                                                    @if(count(explode(',', $subject->keywords)) > 2)
                                                        <span class="badge bg-light text-dark">+{{ count(explode(',', $subject->keywords)) - 2 }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-wrap">
                                                    @foreach(array_slice(explode(',', $subject->tools), 0, 2) as $tool)
                                                        <span class="badge bg-info me-1 mb-1">{{ trim($tool) }}</span>
                                                    @endforeach
                                                    @if(count(explode(',', $subject->tools)) > 2)
                                                        <span class="badge bg-light text-dark">+{{ count(explode(',', $subject->tools)) - 2 }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="text-nowrap">
                                                {{ $subject->created_at->format('M d, Y') }}
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button"
                                                            class="btn btn-outline-primary"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#subjectModal"
                                                            data-subject-id="{{ $subject->id }}"
                                                            title="{{ __('app.view_details') }}">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    @if(auth()->user()->role === 'student' && auth()->user()->teamMember?->team && !auth()->user()->teamMember?->team?->project)
                                                        <button type="button" class="btn btn-outline-success"
                                                                onclick="selectSubject({{ $subject->id }}, '{{ $subject->title }}')"
                                                                title="{{ __('app.select_subject') }}">
                                                            <i class="bi bi-check"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $subjects->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Available Subjects</h5>
                            <p class="text-muted">
                                @if(request('grade'))
                                    No subjects are available for the selected grade level.
                                @else
                                    No subjects are currently available for selection.
                                @endif
                            </p>
                            @if(request('grade'))
                                <a href="{{ route('subjects.available') }}" class="btn btn-primary">
                                    View All Subjects
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Subject Selection Modal -->
@if(auth()->user()->role === 'student' && auth()->user()->teamMember?->team)
    <div class="modal fade" id="selectSubjectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Subject Selection</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to select "<span id="subjectTitle"></span>" for your team?</p>
                    <div class="alert alert-info">
                        <strong>Note:</strong> Once selected, this subject will be assigned to your team and cannot be changed without admin approval.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="selectSubjectForm" method="POST" class="d-inline">
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
@endif

<!-- Subject Details Modal -->
<div class="modal fade" id="subjectModal" tabindex="-1" aria-labelledby="subjectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="subjectModalLabel">{{ __('app.subject_details') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="subjectModalContent">
                    <div class="text-center py-4">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('app.close') }}</button>
            </div>
        </div>
    </div>
</div>

@if(auth()->user()->role === 'student' && !auth()->user()->teamMember)
    <div class="alert alert-warning mt-3">
        <h6><i class="fas fa-info-circle"></i> Join or Create a Team First</h6>
        <p class="mb-2">You need to be part of a team to select a subject.</p>
        <a href="{{ route('teams.create') }}" class="btn btn-primary btn-sm">Create Team</a>
        <a href="{{ route('teams.index') }}" class="btn btn-outline-primary btn-sm">Browse Teams</a>
    </div>
@endif
@endsection

@push('styles')
<style>
.text-truncate-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}

.card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
</style>
@endpush

@push('scripts')
<script>
function selectSubject(subjectId, subjectTitle) {
    document.getElementById('selectedSubjectId').value = subjectId;
    document.getElementById('subjectTitle').textContent = subjectTitle;
    document.getElementById('selectSubjectForm').action = "{{ route('teams.select-subject', auth()->user()->teamMember?->team ?? 0) }}";

    const modal = new bootstrap.Modal(document.getElementById('selectSubjectModal'));
    modal.show();
}

document.addEventListener('DOMContentLoaded', function() {
    const subjectModal = document.getElementById('subjectModal');

    subjectModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const subjectId = button.getAttribute('data-subject-id');
        const modalContent = document.getElementById('subjectModalContent');

        // Show loading spinner
        modalContent.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `;

        // Fetch subject details
        fetch(`/subjects/${subjectId}/modal`)
            .then(response => response.text())
            .then(html => {
                modalContent.innerHTML = html;
            })
            .catch(error => {
                modalContent.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i>
                        Error loading subject details. Please try again.
                    </div>
                `;
            });
    });
});
</script>
@endpush