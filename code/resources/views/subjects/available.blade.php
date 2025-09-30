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
                        <div class="row">
                            @foreach($subjects as $subject)
                                <div class="col-lg-6 col-xl-4 mb-4">
                                    <div class="card h-100 border-2">
                                        <div class="card-body d-flex flex-column">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="card-title text-primary">{{ $subject->title }}</h6>
                                                <span class="badge bg-success">Available</span>
                                            </div>

                                            <div class="mb-2">
                                                <small class="text-muted">Proposed by:</small>
                                                <div class="fw-bold">{{ $subject->teacher->name }}</div>
                                                <small class="text-muted">{{ $subject->teacher->department }}</small>
                                            </div>

                                            <div class="mb-3 flex-grow-1">
                                                <p class="card-text text-truncate-3">
                                                    {{ Str::limit($subject->description, 150) }}
                                                </p>
                                            </div>

                                            <div class="mb-3">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <small class="text-muted">Keywords:</small>
                                                        <div class="d-flex flex-wrap">
                                                            @foreach(array_slice(explode(',', $subject->keywords), 0, 3) as $keyword)
                                                                <span class="badge bg-secondary me-1 mb-1">{{ trim($keyword) }}</span>
                                                            @endforeach
                                                            @if(count(explode(',', $subject->keywords)) > 3)
                                                                <span class="badge bg-light text-dark me-1 mb-1">+{{ count(explode(',', $subject->keywords)) - 3 }} more</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <small class="text-muted">Tools:</small>
                                                <div class="d-flex flex-wrap">
                                                    @foreach(array_slice(explode(',', $subject->tools), 0, 3) as $tool)
                                                        <span class="badge bg-info me-1 mb-1">{{ trim($tool) }}</span>
                                                    @endforeach
                                                    @if(count(explode(',', $subject->tools)) > 3)
                                                        <span class="badge bg-light text-dark me-1 mb-1">+{{ count(explode(',', $subject->tools)) - 3 }} more</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="mt-auto">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted">
                                                        Added {{ $subject->created_at->diffForHumans() }}
                                                    </small>
                                                    <div>
                                                        <a href="{{ route('subjects.show', $subject) }}" class="btn btn-outline-primary btn-sm">
                                                            <i class="fas fa-eye"></i> View
                                                        </a>
                                                        @if(auth()->user()->role === 'student' && auth()->user()->teamMember?->team && !auth()->user()->teamMember->team->project)
                                                            <button type="button" class="btn btn-success btn-sm"
                                                                    onclick="selectSubject({{ $subject->id }}, '{{ $subject->title }}')">
                                                                <i class="fas fa-check"></i> Select
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{ $subjects->links() }}
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
</script>
@endpush