@extends('layouts.pfe-app')

@section('title', __('app.external_subjects'))

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center gap-2">
                <h1 class="h3 mb-0">
                    <i class="bi bi-building"></i> {{ __('app.external_subjects') }}
                </h1>
                <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#pageHelpModal">
                    <i class="bi bi-question-circle"></i>
                </button>
            </div>
            @if(auth()->user()?->role === 'student' && \App\Services\SettingsService::canStudentsCreateSubjects())
                <a href="{{ route('subjects.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus"></i> {{ __('app.propose_external_subject') }}
                </a>
            @endif
        </div>

        <!-- Info Alert -->
        <div class="alert alert-info mb-4">
            <i class="bi bi-info-circle me-2"></i>
            <strong>{{ __('app.about_external_subjects') }}:</strong>
            {{ __('app.external_subjects_description') }}
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
                    <form method="GET" action="{{ route('subjects.external-list') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="status" class="form-label">{{ __('app.status') }}</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">{{ __('app.all_statuses') }}</option>
                                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>{{ __('app.draft') }}</option>
                                <option value="pending_validation" {{ request('status') === 'pending_validation' ? 'selected' : '' }}>{{ __('app.pending_validation') }}</option>
                                <option value="validated" {{ request('status') === 'validated' ? 'selected' : '' }}>{{ __('app.validated') }}</option>
                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>{{ __('app.rejected') }}</option>
                            </select>
                        </div>
                        <div class="col-md-7">
                            <label for="search" class="form-label">{{ __('app.search') }}</label>
                            <input type="text" class="form-control" id="search" name="search"
                                   value="{{ request('search') }}"
                                   placeholder="{{ __('app.search_by_title_company_student') }}...">
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
                {{ __('app.showing') }} {{ $externalSubjects->firstItem() ?? 0 }} {{ __('app.to') }} {{ $externalSubjects->lastItem() ?? 0 }}
                {{ __('app.of') }} {{ $externalSubjects->total() }} {{ __('app.results') }}
            </div>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('subjects.external-list') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x-circle"></i> {{ __('app.clear_filters') }}
                </a>
            @endif
        </div>

        <!-- External Subjects Table -->
        <div class="card">
            <div class="card-body">
                @if($externalSubjects->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('app.title') }}</th>
                                    <th>{{ __('app.team') }}</th>
                                    <th>{{ __('app.company') }}</th>
                                    <th>{{ __('app.external_supervisor') }}</th>
                                    <th>{{ __('app.status') }}</th>
                                    <th>{{ __('app.created') }}</th>
                                    <th>{{ __('app.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($externalSubjects as $subject)
                                    <tr>
                                        <td>
                                            <div>
                                                <strong>{{ $subject->title }}</strong>
                                                <div class="text-muted small">{{ Str::limit($subject->description, 80) }}</div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($subject->team)
                                                <a href="{{ route('teams.show', $subject->team) }}" class="text-decoration-none">
                                                    <i class="bi bi-people"></i> {{ $subject->team->name }}
                                                </a>
                                                <div class="text-muted small">
                                                    {{ $subject->team->members->count() }} {{ __('app.members') }}
                                                </div>
                                            @else
                                                <span class="text-muted">{{ __('app.no_team') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-nowrap">
                                                {{ $subject->company_name ?? __('app.na') }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-nowrap">
                                                {{ $subject->externalSupervisor->name ?? __('app.not_assigned') }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{
                                                $subject->status === 'validated' ? 'success' :
                                                ($subject->status === 'pending_validation' ? 'warning' :
                                                ($subject->status === 'rejected' ? 'danger' : 'secondary'))
                                            }}">
                                                {{ __('app.' . $subject->status) }}
                                            </span>
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
                                                @php
                                                    $canEdit = false;
                                                    $canDelete = false;
                                                    $user = auth()->user();

                                                    if ($user) {
                                                        // Admins can always edit and delete
                                                        if ($user->role === 'admin') {
                                                            $canEdit = true;
                                                            $canDelete = true;
                                                        } elseif ($subject->team) {
                                                            // Check if user is the team leader
                                                            $userTeamMember = $subject->team->members->where('student_id', $user->id)->first();

                                                            if ($userTeamMember && $userTeamMember->role === 'leader') {
                                                                // Only team leaders can edit and delete
                                                                $canEdit = true;
                                                                $canDelete = true;
                                                            }
                                                        }
                                                    }
                                                @endphp

                                                @if($canEdit)
                                                    <a href="{{ route('subjects.edit', $subject) }}"
                                                       class="btn btn-outline-warning btn-sm"
                                                       title="{{ __('app.edit') }}">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                @endif

                                                @if($canDelete)
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
                        <i class="bi bi-building text-muted" style="font-size: 4rem;"></i>
                        <h4 class="mt-3">{{ __('app.no_external_subjects_found') }}</h4>
                        <p class="text-muted">
                            @if(request()->hasAny(['search', 'status']))
                                {{ __('app.no_external_subjects_match_filters') }}
                            @else
                                {{ __('app.no_external_subjects_available') }}
                            @endif
                        </p>
                        @if(auth()->user()?->role === 'student' && \App\Services\SettingsService::canStudentsCreateSubjects())
                            <a href="{{ route('subjects.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus"></i> {{ __('app.propose_first_external_subject') }}
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Pagination -->
        @if($externalSubjects->hasPages())
            <div class="d-flex justify-content-center mt-4">
                <nav aria-label="{{ __('app.external_subjects_pagination') }}">
                    {{ $externalSubjects->appends(request()->query())->links('pagination::bootstrap-4') }}
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

    <!-- Page Help Modal -->
    <x-info-modal id="pageHelpModal" title="{{ __('app.external_subjects_help') }}" icon="bi-building">
        <h6>{{ __('app.what_are_external_subjects') }}</h6>
        <p>{{ __('app.external_subjects_help_description') }}</p>

        <h6>{{ __('app.how_to_propose') }}</h6>
        <ul>
            <li><strong>{{ __('app.create_proposal') }}:</strong> {{ __('app.create_external_subject_help') }}</li>
            <li><strong>{{ __('app.company_details') }}:</strong> {{ __('app.provide_company_info_help') }}</li>
            <li><strong>{{ __('app.supervisor_info') }}:</strong> {{ __('app.provide_supervisor_info_help') }}</li>
        </ul>

        @if(auth()->user()?->role !== 'student')
            <h6>{{ __('app.management') }}</h6>
            <ul>
                <li><strong>{{ __('app.view_all') }}:</strong> {{ __('app.view_all_external_subjects_help') }}</li>
                <li><strong>{{ __('app.validate') }}:</strong> {{ __('app.validate_external_subjects_help') }}</li>
                <li><strong>{{ __('app.monitor') }}:</strong> {{ __('app.monitor_external_progress_help') }}</li>
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
});
</script>
@endpush
