@extends('layouts.pfe-app')

@section('title', __('app.subjects'))

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">{{ __('app.subjects') }}</h1>
            @if(auth()->user()?->role === 'teacher')
                <a href="{{ route('subjects.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus"></i> {{ __('app.create') }} {{ __('app.subject') }}
                </a>
            @endif
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('subjects.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label for="grade" class="form-label">{{ __('app.grade') }}</label>
                        <select class="form-select" id="grade" name="grade">
                            {{-- <option value="">All Grades</option> --}}
                            <option value="master" {{ request('grade') === 'master' ? 'selected' : '' }}>Master 2</option>

                            {{-- <option value="license" {{ request('grade') === 'license' ? 'selected' : '' }}>L3</option> --}}
                            {{-- <option value="m1" {{ request('grade') === 'master 1' ? 'selected' : '' }}>Master 1</option> --}}
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">{{ __('app.status') }}</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">{{ __('app.all_statuses') }}</option>
                            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>{{ __('app.draft') }}</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('app.pending') }}</option>
                            <option value="validated" {{ request('status') === 'validated' ? 'selected' : '' }}>{{ __('app.validated') }}</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="search" class="form-label">{{ __('app.search') }}</label>
                        <input type="text" class="form-control" id="search" name="search"
                               value="{{ request('search') }}" placeholder="{{ __('app.search') }} {{ __('app.subjects') }}...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-outline-primary">{{ __('app.filter') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Results Info -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="text-muted">
                Showing {{ $subjects->firstItem() ?? 0 }} to {{ $subjects->lastItem() ?? 0 }}
                of {{ $subjects->total() }} results
            </div>
            @if(request()->hasAny(['search', 'grade', 'status']))
                <a href="{{ route('subjects.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x-circle"></i> Clear Filters
                </a>
            @endif
        </div>

        <!-- Subjects Table -->
        <div class="card">
            <div class="card-body">
                @if($subjects->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('app.title') }}</th>
                                    <th>{{ __('app.teacher') }}</th>
                                    <th>{{ __('app.grade') }}</th>
                                    <th>{{ __('app.status') }}</th>
                                    <th>{{ __('app.type') }}</th>
                                    <th>Teams</th>
                                    <th>{{ __('app.created') }}</th>
                                    <th>{{ __('app.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subjects as $subject)
                                    <tr>
                                        <td>
                                            <div>
                                                <strong>{{ $subject->title }}</strong>
                                                <div class="text-muted small">{{ Str::limit($subject->description, 80) }}</div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-nowrap">{{ $subject->teacher->name ?? __('app.tbd') }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ strtoupper($subject->target_grade ?? __('app.na')) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $subject->status === 'validated' ? 'success' : ($subject->status === 'pending' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($subject->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($subject->is_external)
                                                <span class="badge bg-secondary">
                                                    <i class="bi bi-building"></i> {{ __('app.external') }}
                                                </span>
                                            @else
                                                <span class="badge bg-primary">
                                                    <i class="bi bi-house"></i> {{ __('app.internal') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($subject->preferences_count > 0)
                                                <span class="badge bg-success" title="{{ $subject->preferences_count }} teams interested">
                                                    <i class="bi bi-people"></i> {{ $subject->preferences_count }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-nowrap">
                                            {{ $subject->created_at->format('M d, Y') }}
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button"
                                                        class="btn btn-outline-primary btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#subjectModal"
                                                        data-subject-id="{{ $subject->id }}"
                                                        title="{{ __('app.view_details') }}">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                @if($subject->preferences_count > 0)
                                                    <button type="button"
                                                            class="btn btn-outline-info btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#requestsModal"
                                                            data-subject-id="{{ $subject->id }}"
                                                            title="See Requests ({{ $subject->preferences_count }})">
                                                        <i class="bi bi-list-ul"></i>
                                                    </button>
                                                @endif
                                                @if(auth()->user()?->id === $subject->teacher_id)
                                                    <a href="{{ route('subjects.edit', $subject) }}"
                                                       class="btn btn-outline-warning btn-sm"
                                                       title="{{ __('app.edit') }}">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form method="POST" action="{{ route('subjects.destroy', $subject) }}" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="btn btn-outline-danger btn-sm"
                                                                title="{{ __('app.delete') }}"
                                                                onclick="return showDeleteConfirmation({
                                                                    itemName: '{{ $subject->title }}',
                                                                    message: '{{ __('app.confirm_delete_subject') }}',
                                                                    form: this.closest('form')
                                                                })">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-journal-x text-muted" style="font-size: 4rem;"></i>
                        <h4 class="mt-3">No Subjects Found</h4>
                        <p class="text-muted">
                            @if(request()->hasAny(['search', 'grade', 'status']))
                                No subjects match your current filters. Try adjusting your search criteria.
                            @else
                                There are no subjects available at the moment.
                            @endif
                        </p>
                        @if(auth()->user()?->role === 'teacher')
                            <a href="{{ route('subjects.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus"></i> Create First Subject
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Pagination -->
        @if($subjects->hasPages())
            <div class="d-flex justify-content-center mt-4">
                <nav aria-label="Subjects pagination">
                    {{ $subjects->appends(request()->query())->links('pagination::bootstrap-4') }}
                </nav>
            </div>
        @endif
    </div>

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

    <!-- Team Requests Modal -->
    <div class="modal fade" id="requestsModal" tabindex="-1" aria-labelledby="requestsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="requestsModalLabel">Team Requests</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="requestsModalContent">
                        <div class="text-center py-4">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const subjectModal = document.getElementById('subjectModal');
    const requestsModal = document.getElementById('requestsModal');

    // Subject details modal
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

    // Team requests modal
    requestsModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const subjectId = button.getAttribute('data-subject-id');
        const modalContent = document.getElementById('requestsModalContent');

        // Show loading spinner
        modalContent.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `;

        // Fetch team requests
        fetch(`/subjects/${subjectId}/requests`)
            .then(response => response.text())
            .then(html => {
                modalContent.innerHTML = html;
            })
            .catch(error => {
                modalContent.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i>
                        Error loading team requests. Please try again.
                    </div>
                `;
            });
    });
});
</script>
@endpush
