@extends('layouts.pfe-app')

@section('title', 'Edit Defense')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Edit Defense</h1>
        <a href="{{ route('defenses.show', $defense) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Defense
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Defense Information</h5>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('defenses.update', $defense) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="defense_date" class="form-label">Defense Date</label>
                                    <input type="date" class="form-control @error('defense_date') is-invalid @enderror"
                                           id="defense_date" name="defense_date"
                                           value="{{ old('defense_date', $defense->defense_date ? $defense->defense_date->format('Y-m-d') : '') }}" required>
                                    @error('defense_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="defense_time" class="form-label">Defense Time</label>
                                    <input type="time" class="form-control @error('defense_time') is-invalid @enderror"
                                           id="defense_time" name="defense_time"
                                           value="{{ old('defense_time', $defense->defense_date ? $defense->defense_date->format('H:i') : '') }}" required>
                                    @error('defense_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="room_id" class="form-label">Room</label>
                                    <select class="form-select @error('room_id') is-invalid @enderror" id="room_id" name="room_id" required>
                                        <option value="">Select Room</option>
                                        @foreach($rooms as $room)
                                            <option value="{{ $room->id }}"
                                                    {{ old('room_id', $defense->room_id) == $room->id ? 'selected' : '' }}>
                                                {{ $room->name }}
                                                @if($room->location)
                                                    - {{ $room->location }}
                                                @endif
                                                (Capacity: {{ $room->capacity ?? 'N/A' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('room_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="duration" class="form-label">Duration (minutes)</label>
                                    <input type="number" class="form-control @error('duration') is-invalid @enderror"
                                           id="duration" name="duration" min="30" max="180"
                                           value="{{ old('duration', $defense->duration ?? 90) }}" required>
                                    @error('duration')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="scheduled" {{ old('status', $defense->status) == 'scheduled' ? 'selected' : '' }}>
                                    Scheduled
                                </option>
                                <option value="in_progress" {{ old('status', $defense->status) == 'in_progress' ? 'selected' : '' }}>
                                    In Progress
                                </option>
                                <option value="completed" {{ old('status', $defense->status) == 'completed' ? 'selected' : '' }}>
                                    Completed
                                </option>
                                <option value="cancelled" {{ old('status', $defense->status) == 'cancelled' ? 'selected' : '' }}>
                                    Cancelled
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                      id="notes" name="notes" rows="3" maxlength="500"
                                      placeholder="Additional notes or instructions...">{{ old('notes', $defense->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Maximum 500 characters</div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('defenses.show', $defense) }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg"></i> Update Defense
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Project Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Project Information</h5>
                </div>
                <div class="card-body">
                    <h6>{{ $defense->project->subject->title ?? 'N/A' }}</h6>
                    <p class="text-muted mb-2">{{ $defense->project->team->name ?? 'N/A' }}</p>
                    <p class="small text-muted mb-0">
                        <strong>Type:</strong> {{ ucfirst($defense->project->subject->type ?? 'N/A') }}
                    </p>
                </div>
            </div>

            <!-- Current Jury Members -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Jury Members</h5>
                </div>
                <div class="card-body">
                    @if($defense->juries->count() > 0)
                        @foreach($defense->juries as $jury)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <h6 class="mb-0">{{ $jury->teacher->name }}</h6>
                                    <small class="text-muted">{{ ucfirst($jury->role) }}</small>
                                </div>
                                <span class="badge bg-info">{{ ucfirst($jury->role) }}</span>
                            </div>
                            @if(!$loop->last)
                                <hr class="my-2">
                            @endif
                        @endforeach
                    @else
                        <p class="text-muted">No jury members assigned.</p>
                    @endif
                </div>
            </div>

            <!-- Room Availability Info -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Room Availability</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <small>
                            <i class="bi bi-info-circle"></i>
                            The system will check room availability when you update the defense.
                        </small>
                    </div>

                    @if($rooms->count() > 0)
                        <h6 class="small text-muted mb-2">Available Rooms:</h6>
                        @foreach($rooms->take(3) as $room)
                            <div class="small mb-1">
                                <strong>{{ $room->name }}</strong>
                                @if($room->location)
                                    - {{ $room->location }}
                                @endif
                                <span class="text-muted">({{ $room->capacity ?? 'N/A' }} seats)</span>
                            </div>
                        @endforeach
                        @if($rooms->count() > 3)
                            <small class="text-muted">... and {{ $rooms->count() - 3 }} more rooms</small>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set minimum date to today
    const dateInput = document.getElementById('defense_date');
    const today = new Date().toISOString().split('T')[0];
    dateInput.min = today;

    // Character counter for notes
    const notesTextarea = document.getElementById('notes');
    const maxLength = 500;

    notesTextarea.addEventListener('input', function() {
        const remaining = maxLength - this.value.length;
        const formText = this.nextElementSibling;
        if (remaining < 50) {
            formText.textContent = `${remaining} characters remaining`;
            formText.className = remaining < 10 ? 'form-text text-warning' : 'form-text text-info';
        } else {
            formText.textContent = 'Maximum 500 characters';
            formText.className = 'form-text';
        }
    });
});
</script>
@endsection