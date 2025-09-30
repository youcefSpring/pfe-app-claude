@extends('layouts.pfe-app')

@section('page-title', 'Schedule Defense')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title mb-2">Defense Scheduling</h4>
                            <p class="card-text mb-0">Schedule defenses manually or use automatic planning</p>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-calendar-plus" style="font-size: 3rem; opacity: 0.7;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="btn-group w-100" role="group">
                        <button type="button" class="btn btn-outline-primary active" id="manual-tab">
                            <i class="bi bi-pencil-square me-2"></i>Manual Scheduling
                        </button>
                        <button type="button" class="btn btn-outline-success" id="auto-tab">
                            <i class="bi bi-cpu me-2"></i>Auto Defense Plan
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
                            <i class="bi bi-calendar-event me-2"></i>Schedule New Defense
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('defenses.schedule') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="project_id" class="form-label">Project <span class="text-danger">*</span></label>
                                        <select class="form-select @error('project_id') is-invalid @enderror"
                                                id="project_id" name="project_id" required>
                                            <option value="">Select Project</option>
                                            @foreach($projects as $project)
                                                <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                                    {{ $project->subject->title }} - Team: {{ $project->team->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('project_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="room_id" class="form-label">Room <span class="text-danger">*</span></label>
                                        <select class="form-select @error('room_id') is-invalid @enderror"
                                                id="room_id" name="room_id" required>
                                            <option value="">Select Room</option>
                                            @foreach($rooms as $room)
                                                <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                                    {{ $room->name }} (Capacity: {{ $room->capacity ?? 'N/A' }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('room_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="defense_date" class="form-label">Defense Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('defense_date') is-invalid @enderror"
                                               id="defense_date" name="defense_date" value="{{ old('defense_date') }}"
                                               min="{{ now()->format('Y-m-d') }}" required>
                                        @error('defense_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="defense_time" class="form-label">Defense Time <span class="text-danger">*</span></label>
                                        <input type="time" class="form-control @error('defense_time') is-invalid @enderror"
                                               id="defense_time" name="defense_time" value="{{ old('defense_time') }}" required>
                                        @error('defense_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Jury Members <span class="text-danger">*</span></label>
                                <div class="row">
                                    @foreach($teachers as $teacher)
                                        <div class="col-md-4 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="jury_members[]"
                                                       value="{{ $teacher->id }}" id="teacher_{{ $teacher->id }}"
                                                       {{ in_array($teacher->id, old('jury_members', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="teacher_{{ $teacher->id }}">
                                                    {{ $teacher->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('jury_members')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3"
                                          placeholder="Additional notes for the defense">{{ old('notes') }}</textarea>
                            </div>

                            <div class="text-end">
                                <a href="{{ route('defenses.index') }}" class="btn btn-outline-secondary me-2">
                                    <i class="bi bi-arrow-left me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-calendar-check me-2"></i>Schedule Defense
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
                            <i class="bi bi-info-circle me-2"></i>Scheduling Guidelines
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item px-0">
                                <strong>Minimum Jury Size:</strong>
                                <span class="text-muted">3 teachers</span>
                            </div>
                            <div class="list-group-item px-0">
                                <strong>Defense Duration:</strong>
                                <span class="text-muted">90 minutes</span>
                            </div>
                            <div class="list-group-item px-0">
                                <strong>Buffer Time:</strong>
                                <span class="text-muted">30 minutes between defenses</span>
                            </div>
                            <div class="list-group-item px-0">
                                <strong>Working Hours:</strong>
                                <span class="text-muted">8:00 AM - 6:00 PM</span>
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
                                <h4 class="text-primary mb-1">{{ $projects->count() }}</h4>
                                <small class="text-muted">Projects Ready</small>
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
                                               value="{{ old('start_date', now()->addWeek()->format('Y-m-d')) }}"
                                               min="{{ now()->format('Y-m-d') }}" required>
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

    // Jury member validation
    const juryCheckboxes = document.querySelectorAll('input[name="jury_members[]"]');
    const form = document.querySelector('form[action*="defenses.schedule"]');

    if (form) {
        form.addEventListener('submit', function(e) {
            const checkedJury = Array.from(juryCheckboxes).filter(cb => cb.checked);
            if (checkedJury.length < 3) {
                e.preventDefault();
                alert('Please select at least 3 jury members for the defense.');
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
});

function showPreview() {
    // This would show a preview of the automatic planning
    alert('Preview functionality will be implemented to show the planned schedule before confirmation.');
}
</script>
@endpush