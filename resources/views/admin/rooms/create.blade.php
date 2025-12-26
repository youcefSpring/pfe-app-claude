@extends('layouts.pfe-app')

@section('page-title', 'Add New Room')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Add New Room</h4>
                    <a href="{{ route('admin.rooms') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Rooms
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.rooms.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Room Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name') }}"
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
                                           id="location" name="location" value="{{ old('location') }}"
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
                                           id="capacity" name="capacity" value="{{ old('capacity') }}"
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
                                        <option value="1" {{ old('is_available', '1') == '1' ? 'selected' : '' }}>Available</option>
                                        <option value="0" {{ old('is_available') == '0' ? 'selected' : '' }}>Unavailable</option>
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
                                      placeholder="Describe the equipment available in this room (projector, whiteboard, computers, lab equipment, etc.)">{{ old('equipment') }}</textarea>
                            @error('equipment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">List all equipment and materials available for defenses and presentations</small>
                        </div>

                        <hr>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Room Guidelines:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Ensure the room can accommodate the defense jury and audience</li>
                                <li>Consider equipment needs for presentations (projector, screen, etc.)</li>
                                <li>Verify accessibility and comfort for extended defense sessions</li>
                                <li>Include any special features or limitations in the equipment field</li>
                            </ul>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Create Room
                            </button>
                            <a href="{{ route('admin.rooms') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection