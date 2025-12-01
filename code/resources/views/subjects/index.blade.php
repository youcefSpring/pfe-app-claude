@extends('layouts.pfe-app')

@section('title', __('app.subjects'))

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center gap-2">
                <h1 class="h3 mb-0">{{ __('app.subjects') }}</h1>
                <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#pageHelpModal">
                    <i class="bi bi-question-circle"></i>
                </button>
            </div>
            @if(auth()->user()?->role === 'teacher')
                <a href="{{ route('subjects.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus"></i> {{ __('app.create') }} {{ __('app.subject') }}
                </a>
            @endif
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">{{ __('app.filters') }}</h6>
                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#filtersCollapse" aria-expanded="false" aria-controls="filtersCollapse">
                    <i class="bi bi-funnel me-1"></i> {{ __('app.show_filters') }}
                </button>
            </div>
            <div class="collapse" id="filtersCollapse">
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
        </div>

        <!-- Results Info -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="text-muted">
                {{ __('app.showing') }} {{ $subjects->firstItem() ?? 0 }} {{ __('app.to') }} {{ $subjects->lastItem() ?? 0 }}
                {{ __('app.of') }} {{ $subjects->total() }} {{ __('app.results') }}
            </div>
            @if(request()->hasAny(['search', 'grade', 'status']))
                <a href="{{ route('subjects.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x-circle"></i> {{ __('app.clear_filters') }}
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
                                    <th>{{ __('app.teams') }}</th>
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
                                                {{ __('app.' . $subject->status) }}
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
                                            <div class="d-flex gap-1 align-items-center">
                                                @if($subject->preferences_count > 0)
                                                    <button type="button"
                                                            class="btn btn-success btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#requestsModal"
                                                            data-subject-id="{{ $subject->id }}"
                                                            title="{{ __('app.view_team_requests') }}">
                                                        <i class="bi bi-people"></i> {{ $subject->preferences_count }}
                                                    </button>
                                                @endif

                                                @php
                                                    // Check for assigned team - either via direct relationship or project
                                                    $assignedTeam = null;
                                                    if ($subject->teams && $subject->teams->count() > 0) {
                                                        $assignedTeam = $subject->teams->first();
                                                    } elseif ($subject->project && $subject->project->team) {
                                                        $assignedTeam = $subject->project->team;
                                                    }
                                                @endphp

                                                @if($assignedTeam)
                                                    <a href="{{ route('teams.show', $assignedTeam) }}"
                                                       class="btn btn-primary btn-sm"
                                                       title="{{ __('app.view_assigned_team') }}">
                                                        <i class="bi bi-check-circle"></i> {{ $assignedTeam->name }}
                                                    </a>
                                                    @if(in_array(auth()->user()?->role, ['admin', 'department_head']))
                                                        <form method="POST"
                                                              action="{{ route('admin.subjects.unassign-team', $subject) }}"
                                                              style="display: inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                    class="btn btn-sm btn-outline-warning"
                                                                    title="{{ __('app.unassign_team') }}"
                                                                    onclick="return confirm('{{ __('app.confirm_unassign_team', ['team' => $assignedTeam->name]) }}')">
                                                                <i class="bi bi-x-circle"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                @elseif($subject->preferences_count === 0)
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-nowrap">
                                            {{ $subject->created_at->format('d/m/Y') }}
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
                        <h4 class="mt-3">{{ __('app.no_subjects_found') }}</h4>
                        <p class="text-muted">
                            @if(request()->hasAny(['search', 'grade', 'status']))
                                {{ __('app.no_subjects_match_filters') }}
                            @else
                                {{ __('app.no_subjects_available_moment') }}
                            @endif
                        </p>
                        @if(auth()->user()?->role === 'teacher')
                            <a href="{{ route('subjects.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus"></i> {{ __('app.create_first_subject') }}
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Pagination -->
        @if($subjects->hasPages())
            <div class="d-flex justify-content-center mt-4">
                <nav aria-label="{{ __('app.subjects_pagination') }}">
                    {{ $subjects->appends(request()->query())->links('pagination::bootstrap-4') }}
                </nav>
            </div>
        @endif
    </div>

    <!-- Subject Details Modal -->
    <div class="modal fade" id="subjectModal" tabindex="-1" aria-labelledby="subjectModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="subjectModalLabel">{{ __('app.subject_details') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="subjectModalContent">
                        <div class="text-center py-4">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">{{ __('app.loading') }}</span>
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
                    <h5 class="modal-title" id="requestsModalLabel">{{ __('app.team_requests') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="requestsModalContent">
                        <div class="text-center py-4">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">{{ __('app.loading') }}</span>
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

    <!-- Page Help Modal -->
    <x-info-modal id="pageHelpModal" title="{{ __('app.subjects_page_help') }}" icon="bi-journal-text">
        <h6>{{ __('app.what_is_this_page') }}</h6>
        <p>{{ __('app.subjects_page_description') }}</p>

        <h6>{{ __('app.how_to_use') }}</h6>
        <ul>
            <li><strong>{{ __('app.browse_subjects') }}:</strong> {{ __('app.browse_subjects_help') }}</li>
            <li><strong>{{ __('app.filter') }}:</strong> {{ __('app.filter_subjects_help') }}</li>
            <li><strong>{{ __('app.view_details') }}:</strong> {{ __('app.view_subject_details_help') }}</li>
            @if(auth()->user()?->role === 'teacher')
                <li><strong>{{ __('app.create_subject') }}:</strong> {{ __('app.create_subject_help') }}</li>
                <li><strong>{{ __('app.edit_subject') }}:</strong> {{ __('app.edit_subject_help') }}</li>
            @endif
            @if(auth()->user()?->role === 'student')
                <li><strong>{{ __('app.request_subject') }}:</strong> {{ __('app.request_subject_help') }}</li>
            @endif
        </ul>

        @if(auth()->user()?->role === 'teacher')
            <h6>{{ __('app.subject_status') }}</h6>
            <ul>
                <li><strong>{{ __('app.draft') }}:</strong> {{ __('app.draft_status_help') }}</li>
                <li><strong>{{ __('app.pending') }}:</strong> {{ __('app.pending_status_help') }}</li>
                <li><strong>{{ __('app.validated') }}:</strong> {{ __('app.validated_status_help') }}</li>
            </ul>
        @endif
    </x-info-modal>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter toggle button text update
    const filtersCollapse = document.getElementById('filtersCollapse');
    const filterToggleBtn = document.querySelector('[data-bs-target="#filtersCollapse"]');

    if (filtersCollapse && filterToggleBtn) {
        filtersCollapse.addEventListener('show.bs.collapse', function() {
            filterToggleBtn.innerHTML = '<i class="bi bi-funnel me-1"></i> {{ __("app.hide_filters") }}';
        });

        filtersCollapse.addEventListener('hide.bs.collapse', function() {
            filterToggleBtn.innerHTML = '<i class="bi bi-funnel me-1"></i> {{ __("app.show_filters") }}';
        });
    }

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
                    <span class="visually-hidden">{{ __('app.loading') }}</span>
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
                        {{ __('app.error_loading_subject_details') }}
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
                    <span class="visually-hidden">{{ __('app.loading') }}</span>
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
                        {{ __('app.error_loading_team_requests') }}
                    </div>
                `;
            });
    });
});
</script>
@endpush
