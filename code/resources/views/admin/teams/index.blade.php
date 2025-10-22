@extends('layouts.pfe-app')

@section('title', __('app.team_management'))

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">{{ __('app.team_management') }}</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('teams.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left me-1"></i>{{ __('app.back_to_teams') }}
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" action="{{ route('admin.teams') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="search" class="form-label">{{ __('app.search') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                    <input type="text" name="search" id="search" class="form-control"
                                           placeholder="{{ __('app.search_teams') }}"
                                           value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label for="status" class="form-label">{{ __('app.status') }}</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">{{ __('app.all_statuses') }}</option>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status }}"
                                                @if(request('status') == $status) selected @endif>
                                            {{ __('app.' . $status) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="academic_year" class="form-label">{{ __('app.academic_year') }}</label>
                                <select name="academic_year" id="academic_year" class="form-select">
                                    <option value="">{{ __('app.all_years') }}</option>
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year }}"
                                                @if(request('academic_year') == $year) selected @endif>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-search me-1"></i>{{ __('app.search') }}
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <a href="{{ route('admin.teams') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-clockwise me-1"></i>{{ __('app.clear') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Teams Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('app.team_name') }}</th>
                                    <th>{{ __('app.members') }}</th>
                                    <th>{{ __('app.assigned_subject') }}</th>
                                    <th>{{ __('app.supervisor') }}</th>
                                    <th>{{ __('app.status') }}</th>
                                    <th>{{ __('app.academic_year') }}</th>
                                    <th>{{ __('app.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($teams as $team)
                                    <tr>
                                        <td>
                                            <strong>{{ $team->name }}</strong>
                                        </td>
                                        <td>
                                            @foreach($team->members as $member)
                                                <span class="badge bg-info text-white mb-1 me-1">
                                                    {{ $member->user->name }}
                                                    <small>({{ $member->user->matricule }})</small>
                                                </span>
                                            @endforeach
                                        </td>
                                        <td>
                                            @if($team->subject)
                                                <div class="subject-info">
                                                    <strong>{{ $team->subject->title }}</strong>
                                                    <small class="text-muted d-block">
                                                        {{ Str::limit($team->subject->description, 60) }}
                                                    </small>
                                                </div>
                                            @else
                                                <span class="text-muted">{{ __('app.no_subject_assigned') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($team->supervisor)
                                                {{ $team->supervisor->name }}
                                            @else
                                                <span class="text-muted">{{ __('app.no_supervisor') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $team->status == 'active' ? 'success' : ($team->status == 'forming' ? 'warning' : 'secondary') }}">
                                                {{ __('app.' . $team->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $team->academic_year }}</td>
                                        <td>
                                            @if(auth()->user()?->role === 'admin')
                                                <div class="btn-group" role="group">
                                                    @if(!$team->subject)
                                                        <button type="button" class="btn btn-sm btn-success assign-subject-btn"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#assignSubjectModal"
                                                                data-team-id="{{ $team->id }}"
                                                                data-team-name="{{ $team->name }}">
                                                            <i class="bi bi-plus-circle me-1"></i>{{ __('app.assign_subject') }}
                                                        </button>
                                                    @else
                                                        <form method="POST"
                                                              action="{{ route('admin.teams.remove-subject', $team) }}"
                                                              style="display: inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-warning"
                                                                    onclick="return confirm('{{ __('app.confirm_remove_subject') }}')">
                                                                <i class="bi bi-x-circle me-1"></i>{{ __('app.remove_subject') }}
                                                            </button>
                                                        </form>
                                                    @endif
                                                    <a href="{{ route('teams.show', $team) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye me-1"></i>{{ __('app.view') }}
                                                    </a>
                                                </div>
                                            @else
                                                <span class="text-muted">{{ __('app.no_actions_available') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            {{ __('app.no_teams_found') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $teams->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assign Subject Modal -->
@if(auth()->user()?->role === 'admin')
<div class="modal fade" id="assignSubjectModal" tabindex="-1" aria-labelledby="assignSubjectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignSubjectModalLabel">{{ __('app.assign_subject_to_team') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.teams.assign-subject') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="team_id" id="modal-team-id">

                    <div class="mb-3">
                        <label for="modal-team-name" class="form-label">{{ __('app.team') }}</label>
                        <input type="text" id="modal-team-name" class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="subject_id" class="form-label">{{ __('app.select_subject') }}</label>
                        <select name="subject_id" id="subject_id" class="form-select" required>
                            <option value="">{{ __('app.choose_subject') }}</option>
                            <!-- Subjects will be loaded dynamically via AJAX -->
                        </select>
                        <div class="form-text">{{ __('app.only_available_subjects_shown') }}</div>
                    </div>

                    <div id="subject-details" class="mt-3" style="display: none;">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">{{ __('app.subject_details') }}</h6>
                                <div id="subject-info"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('app.cancel') }}</button>
                    <button type="submit" class="btn btn-success">{{ __('app.assign_subject') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle assign subject modal
    const assignSubjectModal = document.getElementById('assignSubjectModal');
    if (assignSubjectModal) {
        assignSubjectModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const teamId = button.getAttribute('data-team-id');
            const teamName = button.getAttribute('data-team-name');

            // Set team info
            document.getElementById('modal-team-id').value = teamId;
            document.getElementById('modal-team-name').value = teamName;

            // Load available subjects
            loadAvailableSubjects();
        });
    }

    // Load available subjects via AJAX
    function loadAvailableSubjects() {
        const subjectSelect = document.getElementById('subject_id');
        const loadingOption = '<option value="">{{ __('app.loading') }}...</option>';
        subjectSelect.innerHTML = loadingOption;

        fetch('{{ route("admin.available-subjects") }}')
            .then(response => response.json())
            .then(data => {
                let options = '<option value="">{{ __('app.choose_subject') }}</option>';

                if (data.subjects && data.subjects.length > 0) {
                    data.subjects.forEach(subject => {
                        options += `<option value="${subject.id}"
                                           data-description="${subject.description}"
                                           data-teacher="${subject.teacher_name}"
                                           data-level="${subject.level || ''}">
                                        ${subject.title} - ${subject.teacher_name}
                                    </option>`;
                    });
                } else {
                    options += '<option value="" disabled>{{ __('app.no_available_subjects') }}</option>';

                    // Disable all assign subject buttons if no subjects available
                    const assignButtons = document.querySelectorAll('.assign-subject-btn');
                    assignButtons.forEach(btn => {
                        btn.disabled = true;
                        btn.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i>{{ __('app.no_subjects_available') }}';
                        btn.classList.remove('btn-success');
                        btn.classList.add('btn-secondary');
                    });
                }

                subjectSelect.innerHTML = options;
            })
            .catch(error => {
                console.error('Error loading subjects:', error);
                subjectSelect.innerHTML = '<option value="" disabled>{{ __('app.error_loading_subjects') }}</option>';
            });
    }

    // Show subject details when a subject is selected
    const subjectSelect = document.getElementById('subject_id');
    if (subjectSelect) {
        subjectSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const subjectDetails = document.getElementById('subject-details');

            if (selectedOption.value) {
                const description = selectedOption.getAttribute('data-description') || '{{ __('app.no_description') }}';
                const teacher = selectedOption.getAttribute('data-teacher') || '{{ __('app.no_teacher') }}';
                const level = selectedOption.getAttribute('data-level') || '{{ __('app.not_specified') }}';

                document.getElementById('subject-info').innerHTML = `
                    <p><strong>{{ __('app.description') }}:</strong> ${description}</p>
                    <p><strong>{{ __('app.teacher') }}:</strong> ${teacher}</p>
                    <p><strong>{{ __('app.level') }}:</strong> ${level}</p>
                `;
                subjectDetails.style.display = 'block';
            } else {
                subjectDetails.style.display = 'none';
            }
        });
    }
});
</script>
@endpush