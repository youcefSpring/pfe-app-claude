@extends('layouts.pfe-app')

@section('page-title', 'Edit Room')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Edit Room: {{ $room->name }}</h4>
                    <a href="{{ route('admin.rooms') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Rooms
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.rooms.update', $room) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Room Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name', $room->name) }}"
                                           placeholder="e.g., Amphitheater A, Lab 101" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="location" class="form-label">Location</label>
                                    <input type="text" class="form-control @error('location') is-invalid @enderror"
                                           id="location" name="location" value="{{ old('location', $room->location) }}"
                                           placeholder="e.g., Building A, Floor 2">
                                    @error('location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="capacity" class="form-label">Capacity <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('capacity') is-invalid @enderror"
                                           id="capacity" name="capacity" value="{{ old('capacity', $room->capacity) }}"
                                           min="1" max="500" placeholder="Number of seats" required>
                                    @error('capacity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Maximum number of people the room can accommodate</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="is_available" class="form-label">Availability Status</label>
                                    <select class="form-select @error('is_available') is-invalid @enderror"
                                            id="is_available" name="is_available">
                                        <option value="1" {{ old('is_available', $room->is_available ? '1' : '0') == '1' ? 'selected' : '' }}>Available</option>
                                        <option value="0" {{ old('is_available', $room->is_available ? '1' : '0') == '0' ? 'selected' : '' }}>Unavailable</option>
                                    </select>
                                    @error('is_available')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="equipment" class="form-label">Available Equipment & Materials</label>
                            <textarea class="form-control @error('equipment') is-invalid @enderror"
                                      id="equipment" name="equipment" rows="4"
                                      placeholder="Describe the equipment available in this room (projector, whiteboard, computers, lab equipment, etc.)">{{ old('equipment', $room->equipment) }}</textarea>
                            @error('equipment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">List all equipment and materials available for defenses and presentations</small>
                        </div>

                        <hr>

                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Note:</strong>
                            @if($room->defenses()->count() > 0)
                                This room has {{ $room->defenses()->count() }} scheduled defense(s). Changes to availability may affect scheduled defenses.
                            @else
                                This room has no scheduled defenses. You can safely modify all settings.
                            @endif
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Update Room
                            </button>
                            <a href="{{ route('admin.rooms') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Room Usage Information -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Room Usage Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Created:</strong> {{ $room->created_at->format('M d, Y H:i') }}</p>
                            <p><strong>Last Updated:</strong> {{ $room->updated_at->format('M d, Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Scheduled Defenses:</strong> {{ $room->defenses()->count() }}</p>
                            <p><strong>Current Status:</strong>
                                @if($room->is_available)
                                    <span class="badge bg-success">Available</span>
                                @else
                                    <span class="badge bg-danger">Unavailable</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    @if($room->defenses()->count() > 0)
                        <hr>
                        <h6 class="text-primary">Recent Defense Schedules</h6>
                        <div class="list-group list-group-flush">
                            @foreach($room->defenses()->with(['project.subject', 'project.team'])->latest()->take(5)->get() as $defense)
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-medium">{{ $defense->project->subject->title }}</div>
                                            <small class="text-muted">Team: {{ $defense->project->team->name }}</small>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-medium">{{ $defense->defense_date->format('M d, Y') }}</div>
                                            <small class="text-muted">{{ $defense->defense_date->format('H:i') }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection