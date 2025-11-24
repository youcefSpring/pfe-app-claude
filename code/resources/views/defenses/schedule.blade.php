@extends('layouts.pfe-app')

@section('page-title', __('app.schedule_defense'))

@section('content')
<div class="container-fluid">
    {{-- <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title mb-2">{{ __('app.defense_scheduling') }}</h4>
                            <p class="card-text mb-0">{{ __('app.schedule_defenses_manually') }}</p>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-calendar-plus" style="font-size: 3rem; opacity: 0.7;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Action Buttons -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="btn-group w-100" role="group">
                        <button type="button" class="btn btn-outline-primary active" id="manual-tab">
                            <i class="bi bi-pencil-square me-2"></i>{{ __('app.manual_scheduling') }}
                        </button>
                        <button type="button" class="btn btn-outline-success" id="auto-tab">
                            <i class="bi bi-cpu me-2"></i>{{ __('app.auto_defense_plan') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Manual Scheduling Section -->
    <div id="manual-section">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-calendar-event me-2"></i>{{ __('app.schedule_new_defense') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('defenses.schedule') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="subject_id" class="form-label">{{ __('app.subject') }} <span class="text-danger">*</span></label>
                                        <select class="form-select @error('subject_id') is-invalid @enderror"
                                                id="subject_id" name="subject_id" required>
                                            <option value="">{{ __('app.select_subject') }}</option>
                                            @foreach($subjects as $subject)
                                                <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                                    {{ $subject->title }} - {{ $subject->teacher->name ?? __('app.no_teacher') }} ({{ ucfirst($subject->type) }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('subject_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="room_id" class="form-label">{{ __('app.room') }} <span class="text-danger">*</span></label>
                                        <select class="form-select @error('room_id') is-invalid @enderror"
                                                id="room_id" name="room_id" required>
                                            <option value="">{{ __('app.select_room') }}</option>
                                            @foreach($rooms as $room)
                                                <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                                    {{ $room->name }} ({{ __('app.capacity') }}: {{ $room->capacity ?? __('app.na') }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('room_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Team Selection Row -->
                            <div class="row" id="team-selection-row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="team_id" class="form-label">{{ __('app.team') }} <span class="text-danger">*</span></label>
                                        <select class="form-select @error('team_id') is-invalid @enderror"
                                                id="team_id" name="team_id" required>
                                            <option value="">{{ __('app.select_team') }}</option>
                                        </select>
                                        @error('team_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text" id="team-info"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="defense_date" class="form-label">{{ __('app.defense_date') }} <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('defense_date') is-invalid @enderror"
                                               id="defense_date" name="defense_date"
                                               value="{{ old('defense_date') }}"
                                               min="{{ now()->format('Y-m-d') }}" required>
                                        @error('defense_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            <i class="bi bi-info-circle"></i> {{ __('app.can_schedule_from_today') }}
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="defense_time" class="form-label">{{ __('app.defense_time') }} <span class="text-danger">*</span></label>
                                        <input type="time" class="form-control @error('defense_time') is-invalid @enderror"
                                               id="defense_time" name="defense_time" value="{{ old('defense_time') }}" required>
                                        @error('defense_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ __('app.jury_composition') }}</label>

                                <!-- Supervisor (Auto-filled) -->
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <div class="card border-success">
                                            <div class="card-body py-2">
                                                <div class="d-flex align-items-center">
                                                    <div class="badge bg-success me-2">{{ __('app.supervisor') }}</div>
                                                    <span id="supervisor-name" class="text-muted">{{ __('app.select_subject_to_see_supervisor') }}</span>
                                                    <input type="hidden" name="supervisor_id" id="supervisor_id">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- President -->
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label for="president_id" class="form-label">{{ __('app.president') }} <span class="text-danger">*</span></label>
                                        <select class="form-select @error('president_id') is-invalid @enderror"
                                                id="president_id" name="president_id" required>
                                            <option value="">{{ __('app.select_president') }}</option>
                                            @foreach($teachers as $teacher)
                                                <option value="{{ $teacher->id }}" {{ old('president_id') == $teacher->id ? 'selected' : '' }}>
                                                    {{ $teacher->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('president_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Examiner -->
                                <div class="row">
                                    <div class="col-12">
                                        <label for="examiner_id" class="form-label">{{ __('app.examiner') }} <span class="text-danger">*</span></label>
                                        <select class="form-select @error('examiner_id') is-invalid @enderror"
                                                id="examiner_id" name="examiner_id" required>
                                            <option value="">{{ __('app.select_examiner') }}</option>
                                            @foreach($teachers as $teacher)
                                                <option value="{{ $teacher->id }}" {{ old('examiner_id') == $teacher->id ? 'selected' : '' }}>
                                                    {{ $teacher->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('examiner_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="notes" class="form-label">{{ __('app.notes') }}</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3"
                                          placeholder="{{ __('app.additional_notes') }}">{{ old('notes') }}</textarea>
                            </div>

                            <div class="text-end">
                                <a href="{{ route('defenses.index') }}" class="btn btn-outline-secondary me-2">
                                    <i class="bi bi-arrow-left me-2"></i>{{ __('app.cancel') }}
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-calendar-check me-2"></i>{{ __('app.schedule_defense') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="bi bi-info-circle me-2"></i>{{ __('app.scheduling_guidelines') }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item px-0">
                                <strong>{{ __('app.jury_composition') }}:</strong>
                                <span class="text-muted">{{ __('app.supervisor_president_examiner') }}</span>
                            </div>
                            <div class="list-group-item px-0">
                                <strong>{{ __('app.project_requirement') }}:</strong>
                                <span class="text-muted">{{ __('app.subject_must_have_project') }}</span>
                            </div>
                            <div class="list-group-item px-0">
                                <strong>{{ __('app.defense_duration') }}:</strong>
                                <span class="text-muted">{{ __('app.ninety_minutes') }}</span>
                            </div>
                            <div class="list-group-item px-0">
                                <strong>{{ __('app.buffer_time') }}:</strong>
                                <span class="text-muted">{{ __('app.thirty_minutes_between_defenses') }}</span>
                            </div>
                            <div class="list-group-item px-0">
                                <strong>{{ __('app.working_hours') }}:</strong>
                                <span class="text-muted">{{ __('app.eight_am_to_six_pm') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="bi bi-clock me-2"></i>Quick Stats
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <h4 class="text-primary mb-1">{{ $subjects->count() }}</h4>
                                <small class="text-muted">Subjects Available</small>
                            </div>
                            <div class="col-6">
                                <h4 class="text-success mb-1">{{ $rooms->count() }}</h4>
                                <small class="text-muted">Rooms Available</small>
                            </div>
                        </div>
                        <hr>
                        <div class="text-center">
                            <h4 class="text-info mb-1">{{ $teachers->count() }}</h4>
                            <small class="text-muted">Teachers Available</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Automatic Planning Section -->
    <div id="auto-section" style="display: none;">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-cpu me-2"></i>Automatic Defense Planning
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('defenses.auto-schedule') }}" method="POST">
                            @csrf

                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                The system will automatically schedule all unscheduled defenses based on optimal planning algorithms.
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="start_date" class="form-label">Planning Start Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="start_date" name="start_date"
                                               value="{{ old('start_date', now()->format('Y-m-d')) }}"
                                               min="{{ now()->format('Y-m-d') }}" required>
                                        <small class="form-text text-muted">
                                            <i class="bi bi-info-circle"></i> Can start from today
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="end_date" class="form-label">Planning End Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="end_date" name="end_date"
                                               value="{{ old('end_date', now()->addMonth()->format('Y-m-d')) }}"
                                               min="{{ now()->format('Y-m-d') }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="daily_limit" class="form-label">Daily Defense Limit</label>
                                        <select class="form-select" id="daily_limit" name="daily_limit">
                                            <option value="4" {{ old('daily_limit', '4') == '4' ? 'selected' : '' }}>4 defenses per day</option>
                                            <option value="6" {{ old('daily_limit') == '6' ? 'selected' : '' }}>6 defenses per day</option>
                                            <option value="8" {{ old('daily_limit') == '8' ? 'selected' : '' }}>8 defenses per day</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="working_days" class="form-label">Working Days</label>
                                        <div class="form-check-group mt-2">
                                            @php
                                                $days = ['monday' => 'Monday', 'tuesday' => 'Tuesday', 'wednesday' => 'Wednesday',
                                                        'thursday' => 'Thursday', 'friday' => 'Friday', 'saturday' => 'Saturday'];
                                                $defaultDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
                                            @endphp
                                            @foreach($days as $value => $label)
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" name="working_days[]"
                                                           value="{{ $value }}" id="day_{{ $value }}"
                                                           {{ in_array($value, old('working_days', $defaultDays)) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="day_{{ $value }}">{{ $label }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Planning Options</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="optimize_jury_distribution"
                                           id="optimize_jury" checked>
                                    <label class="form-check-label" for="optimize_jury">
                                        Optimize jury member distribution
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="respect_teacher_preferences"
                                           id="teacher_preferences" checked>
                                    <label class="form-check-label" for="teacher_preferences">
                                        Respect teacher availability preferences
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="balance_room_usage"
                                           id="room_balance" checked>
                                    <label class="form-check-label" for="room_balance">
                                        Balance room usage across dates
                                    </label>
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="button" class="btn btn-outline-secondary me-2" onclick="showPreview()">
                                    <i class="bi bi-eye me-2"></i>Preview Plan
                                </button>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-play-circle me-2"></i>Generate Auto Plan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="bi bi-gear me-2"></i>Auto Planning Rules
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item px-0">
                                <strong>Jury Assignment:</strong>
                                <span class="text-muted">Random assignment ensuring no conflicts</span>
                            </div>
                            <div class="list-group-item px-0">
                                <strong>Room Allocation:</strong>
                                <span class="text-muted">Based on team size and availability</span>
                            </div>
                            <div class="list-group-item px-0">
                                <strong>Time Slots:</strong>
                                <span class="text-muted">90-minute slots with 30-min buffer</span>
                            </div>
                            <div class="list-group-item px-0">
                                <strong>Conflict Resolution:</strong>
                                <span class="text-muted">Automatic handling of scheduling conflicts</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const manualTab = document.getElementById('manual-tab');
    const autoTab = document.getElementById('auto-tab');
    const manualSection = document.getElementById('manual-section');
    const autoSection = document.getElementById('auto-section');

    manualTab.addEventListener('click', function() {
        manualTab.classList.add('active');
        autoTab.classList.remove('active');
        manualSection.style.display = 'block';
        autoSection.style.display = 'none';
    });

    autoTab.addEventListener('click', function() {
        autoTab.classList.add('active');
        manualTab.classList.remove('active');
        autoSection.style.display = 'block';
        manualSection.style.display = 'none';
    });

    // Subject selection and supervisor auto-population
    const subjectSelect = document.getElementById('subject_id');
    const supervisorName = document.getElementById('supervisor-name');
    const supervisorId = document.getElementById('supervisor_id');
    const presidentSelect = document.getElementById('president_id');
    const examinerSelect = document.getElementById('examiner_id');

    // Subject data for supervisor lookup and team management
    const subjectData = @json($subjectData);

    // Teams without defense data
    const teamsWithoutDefense = @json($teamsData);

    if (subjectSelect) {
        subjectSelect.addEventListener('change', function() {
            const selectedSubjectId = this.value;
            const subject = subjectData.find(s => s.id == selectedSubjectId);

            if (subject && subject.teacher_id) {
                supervisorName.textContent = subject.teacher_name;
                supervisorId.value = subject.teacher_id;

                // Handle team selection based on subject
                updateTeamSelection(subject);

                // Remove supervisor from president and examiner options if selected
                updateJuryOptions();
            } else {
                supervisorName.textContent = 'Select a subject to see supervisor';
                supervisorId.value = '';
                clearTeamSelection();
                updateJuryOptions();
            }
        });
    }

    function updateTeamSelection(subject) {
        const teamSelect = document.getElementById('team_id');
        const teamInfo = document.getElementById('team-info');

        // Clear existing options
        teamSelect.innerHTML = '<option value="">Choose team...</option>';
        teamInfo.innerHTML = '';

        if (subject.has_project && subject.assigned_team) {
            // Subject has assigned team - show readonly
            const option = document.createElement('option');
            option.value = subject.assigned_team.id;
            option.textContent = `${subject.assigned_team.name} (Assigned Team)`;
            option.selected = true;
            teamSelect.appendChild(option);
            teamSelect.disabled = true;

            teamInfo.innerHTML = `<small class="text-success">
                <i class="bi bi-check-circle"></i> Team assigned: ${subject.assigned_team.members.join(', ')}
            </small>`;
        } else {
            // Subject has no team - allow selection from available teams
            teamSelect.disabled = false;

            if (teamsWithoutDefense.length > 0) {
                teamsWithoutDefense.forEach(team => {
                    const option = document.createElement('option');
                    option.value = team.id;

                    if (team.has_project) {
                        option.textContent = `${team.name} (${team.members.length} members) - Has Project`;
                    } else {
                        option.textContent = `${team.name} (${team.members.length} members) - No Subject Chosen`;
                    }

                    teamSelect.appendChild(option);
                });

                const teamsWithoutProject = teamsWithoutDefense.filter(team => !team.has_project).length;
                const teamsWithProject = teamsWithoutDefense.filter(team => team.has_project).length;

                teamInfo.innerHTML = `<small class="text-info">
                    <i class="bi bi-info-circle"></i> Available teams: ${teamsWithProject} with projects, ${teamsWithoutProject} without subjects
                </small>`;
            } else {
                teamInfo.innerHTML = `<small class="text-warning">
                    <i class="bi bi-exclamation-triangle"></i> No teams available without defense
                </small>`;
            }
        }
    }

    function clearTeamSelection() {
        const teamSelect = document.getElementById('team_id');
        const teamInfo = document.getElementById('team-info');

        teamSelect.innerHTML = '<option value="">{{ __("app.select_team") }}</option>';
        teamSelect.disabled = true;
        teamInfo.innerHTML = '';
    }

    function updateJuryOptions() {
        const supervisorIdValue = supervisorId.value;

        // Update president options
        Array.from(presidentSelect.options).forEach(option => {
            if (option.value === supervisorIdValue) {
                option.disabled = true;
                option.text = option.text.replace(' (Supervisor)', '') + ' (Supervisor)';
            } else {
                option.disabled = false;
                option.text = option.text.replace(' (Supervisor)', '');
            }
        });

        // Update examiner options
        Array.from(examinerSelect.options).forEach(option => {
            if (option.value === supervisorIdValue) {
                option.disabled = true;
                option.text = option.text.replace(' (Supervisor)', '') + ' (Supervisor)';
            } else {
                option.disabled = false;
                option.text = option.text.replace(' (Supervisor)', '');
            }
        });
    }

    // Form validation
    const form = document.querySelector('form[action*="defenses.schedule"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            const presidentId = presidentSelect.value;
            const examinerId = examinerSelect.value;
            const supervisorIdValue = supervisorId.value;

            if (!supervisorIdValue) {
                e.preventDefault();
                alert('Please select a subject with a supervisor.');
                return false;
            }

            if (!presidentId) {
                e.preventDefault();
                alert('Please select a president for the jury.');
                return false;
            }

            if (!examinerId) {
                e.preventDefault();
                alert('Please select an examiner for the jury.');
                return false;
            }

            if (presidentId === examinerId) {
                e.preventDefault();
                alert('President and Examiner must be different people.');
                return false;
            }

            if (presidentId === supervisorIdValue || examinerId === supervisorIdValue) {
                e.preventDefault();
                alert('Supervisor cannot be the same as President or Examiner.');
                return false;
            }
        });
    }

    // Date validation
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');

    if (startDate && endDate) {
        startDate.addEventListener('change', function() {
            endDate.min = this.value;
            if (endDate.value && endDate.value < this.value) {
                endDate.value = this.value;
            }
        });
    }

    // Defense date validation - Allow from today
    const defenseDate = document.getElementById('defense_date');
    if (defenseDate) {
        const today = new Date();
        const minDateStr = today.toISOString().split('T')[0];

        // Set min attribute to today
        defenseDate.min = minDateStr;

        // Validate on change - only check not in past
        defenseDate.addEventListener('change', function() {
            const selectedDate = new Date(this.value);
            const todayDate = new Date();
            todayDate.setHours(0, 0, 0, 0);
            selectedDate.setHours(0, 0, 0, 0);

            if (selectedDate < todayDate) {
                alert('Defense date cannot be in the past. Please select today or a future date.');
                this.value = minDateStr;
            }
        });
    }

    // Auto-schedule date validation - Allow from today
    const autoStartDate = document.getElementById('start_date');
    if (autoStartDate) {
        const today = new Date();
        const minDateStr = today.toISOString().split('T')[0];

        autoStartDate.min = minDateStr;

        autoStartDate.addEventListener('change', function() {
            const selectedDate = new Date(this.value);
            const todayDate = new Date();
            todayDate.setHours(0, 0, 0, 0);
            selectedDate.setHours(0, 0, 0, 0);

            if (selectedDate < todayDate) {
                alert('Start date cannot be in the past. Please select today or a future date.');
                this.value = minDateStr;
            }
        });
    }
});

function showPreview() {
    // This would show a preview of the automatic planning
    alert('Preview functionality will be implemented to show the planned schedule before confirmation.');
}
</script>
@endpush
