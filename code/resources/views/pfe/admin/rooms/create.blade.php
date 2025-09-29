@extends('layouts.admin')

@section('title', __('Add New Room'))
@section('page-title', __('Add New Room'))

@section('breadcrumbs')
<span class="text-muted">{{ __('Home') }} / {{ __('Administration') }} / <a href="{{ route('pfe.admin.rooms.index') }}">{{ __('Rooms') }}</a> / {{ __('Add New') }}</span>
@endsection

@section('content')
<div class="container-fluid p-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('pfe.admin.rooms.index') }}" class="btn btn-outline-secondary me-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="h3 mb-0">{{ __('Add New Room') }}</h1>
                    <p class="text-muted mb-0">{{ __('Create a new room for defenses and meetings') }}</p>
                </div>
            </div>

            <!-- Room Creation Form -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Room Information') }}</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('pfe.admin.rooms.store') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Basic Information -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="name" class="form-label">{{ __('Room Name') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}" required
                                       placeholder="{{ __('e.g., Room A101, Conference Hall 1') }}">
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="code" class="form-label">{{ __('Room Code') }}</label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror"
                                       id="code" name="code" value="{{ old('code') }}"
                                       placeholder="{{ __('e.g., A101, CH1') }}">
                                @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-8">
                                <label for="location" class="form-label">{{ __('Location') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('location') is-invalid @enderror"
                                       id="location" name="location" value="{{ old('location') }}" required
                                       placeholder="{{ __('e.g., Building A, Floor 1, Wing East') }}">
                                @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="floor" class="form-label">{{ __('Floor') }}</label>
                                <select class="form-select @error('floor') is-invalid @enderror" id="floor" name="floor">
                                    <option value="">{{ __('Select floor') }}</option>
                                    <option value="Ground" {{ old('floor') == 'Ground' ? 'selected' : '' }}>{{ __('Ground Floor') }}</option>
                                    <option value="1" {{ old('floor') == '1' ? 'selected' : '' }}>{{ __('1st Floor') }}</option>
                                    <option value="2" {{ old('floor') == '2' ? 'selected' : '' }}>{{ __('2nd Floor') }}</option>
                                    <option value="3" {{ old('floor') == '3' ? 'selected' : '' }}>{{ __('3rd Floor') }}</option>
                                    <option value="4" {{ old('floor') == '4' ? 'selected' : '' }}>{{ __('4th Floor') }}</option>
                                </select>
                                @error('floor')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Room Specifications -->
                        <hr class="my-4">
                        <h6 class="mb-3">{{ __('Room Specifications') }}</h6>

                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label for="capacity" class="form-label">{{ __('Capacity') }} <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('capacity') is-invalid @enderror"
                                       id="capacity" name="capacity" value="{{ old('capacity') }}" required
                                       min="1" max="200" placeholder="{{ __('Number of people') }}">
                                @error('capacity')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="type" class="form-label">{{ __('Room Type') }} <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="">{{ __('Select type') }}</option>
                                    <option value="classroom" {{ old('type') == 'classroom' ? 'selected' : '' }}>{{ __('Classroom') }}</option>
                                    <option value="conference" {{ old('type') == 'conference' ? 'selected' : '' }}>{{ __('Conference Room') }}</option>
                                    <option value="amphitheater" {{ old('type') == 'amphitheater' ? 'selected' : '' }}>{{ __('Amphitheater') }}</option>
                                    <option value="laboratory" {{ old('type') == 'laboratory' ? 'selected' : '' }}>{{ __('Laboratory') }}</option>
                                    <option value="auditorium" {{ old('type') == 'auditorium' ? 'selected' : '' }}>{{ __('Auditorium') }}</option>
                                </select>
                                @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="status" class="form-label">{{ __('Status') }}</label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                    <option value="available" {{ old('status', 'available') == 'available' ? 'selected' : '' }}>{{ __('Available') }}</option>
                                    <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>{{ __('Under Maintenance') }}</option>
                                    <option value="renovation" {{ old('status') == 'renovation' ? 'selected' : '' }}>{{ __('Under Renovation') }}</option>
                                </select>
                                @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Features and Equipment -->
                        <div class="mb-4">
                            <label class="form-label">{{ __('Available Features & Equipment') }}</label>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="projector" name="features[]" value="projector"
                                               {{ in_array('projector', old('features', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="projector">
                                            <i class="fas fa-video me-2"></i>{{ __('Projector') }}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="whiteboard" name="features[]" value="whiteboard"
                                               {{ in_array('whiteboard', old('features', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="whiteboard">
                                            <i class="fas fa-chalkboard me-2"></i>{{ __('Whiteboard') }}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="microphone" name="features[]" value="microphone"
                                               {{ in_array('microphone', old('features', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="microphone">
                                            <i class="fas fa-microphone me-2"></i>{{ __('Microphone') }}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="air_conditioning" name="features[]" value="air_conditioning"
                                               {{ in_array('air_conditioning', old('features', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="air_conditioning">
                                            <i class="fas fa-snowflake me-2"></i>{{ __('Air Conditioning') }}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="computer" name="features[]" value="computer"
                                               {{ in_array('computer', old('features', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="computer">
                                            <i class="fas fa-desktop me-2"></i>{{ __('Computer') }}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="wifi" name="features[]" value="wifi"
                                               {{ in_array('wifi', old('features', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="wifi">
                                            <i class="fas fa-wifi me-2"></i>{{ __('WiFi') }}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="screen" name="features[]" value="screen"
                                               {{ in_array('screen', old('features', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="screen">
                                            <i class="fas fa-tv me-2"></i>{{ __('Large Screen') }}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="camera" name="features[]" value="camera"
                                               {{ in_array('camera', old('features', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="camera">
                                            <i class="fas fa-camera me-2"></i>{{ __('Camera') }}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="recording" name="features[]" value="recording"
                                               {{ in_array('recording', old('features', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="recording">
                                            <i class="fas fa-record-vinyl me-2"></i>{{ __('Recording Equipment') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <hr class="my-4">
                        <h6 class="mb-3">{{ __('Additional Information') }}</h6>

                        <div class="mb-4">
                            <label for="description" class="form-label">{{ __('Description') }}</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3"
                                      placeholder="{{ __('Additional details about the room...') }}">{{ old('description') }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="contact_person" class="form-label">{{ __('Contact Person') }}</label>
                                <input type="text" class="form-control @error('contact_person') is-invalid @enderror"
                                       id="contact_person" name="contact_person" value="{{ old('contact_person') }}"
                                       placeholder="{{ __('Room manager or contact person') }}">
                                @error('contact_person')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="contact_phone" class="form-label">{{ __('Contact Phone') }}</label>
                                <input type="tel" class="form-control @error('contact_phone') is-invalid @enderror"
                                       id="contact_phone" name="contact_phone" value="{{ old('contact_phone') }}"
                                       placeholder="{{ __('Phone number for room inquiries') }}">
                                @error('contact_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Booking Settings -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="booking_advance_days" class="form-label">{{ __('Advance Booking (Days)') }}</label>
                                <input type="number" class="form-control @error('booking_advance_days') is-invalid @enderror"
                                       id="booking_advance_days" name="booking_advance_days" value="{{ old('booking_advance_days', 30) }}"
                                       min="1" max="365" placeholder="{{ __('Maximum days in advance') }}">
                                @error('booking_advance_days')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="min_booking_duration" class="form-label">{{ __('Minimum Booking Duration (Hours)') }}</label>
                                <input type="number" class="form-control @error('min_booking_duration') is-invalid @enderror"
                                       id="min_booking_duration" name="min_booking_duration" value="{{ old('min_booking_duration', 1) }}"
                                       min="0.5" max="24" step="0.5" placeholder="{{ __('Minimum hours per booking') }}">
                                @error('min_booking_duration')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('pfe.admin.rooms.index') }}" class="btn btn-secondary">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>{{ __('Create Room') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate room code based on name
    const nameInput = document.getElementById('name');
    const codeInput = document.getElementById('code');

    nameInput.addEventListener('input', function() {
        if (!codeInput.value) {
            // Simple code generation logic
            let code = this.value.replace(/[^a-zA-Z0-9]/g, '').substring(0, 10).toUpperCase();
            codeInput.value = code;
        }
    });

    // Update capacity recommendations based on room type
    const typeSelect = document.getElementById('type');
    const capacityInput = document.getElementById('capacity');

    typeSelect.addEventListener('change', function() {
        const recommendations = {
            'classroom': 30,
            'conference': 15,
            'amphitheater': 100,
            'laboratory': 25,
            'auditorium': 200
        };

        if (!capacityInput.value && recommendations[this.value]) {
            capacityInput.value = recommendations[this.value];
        }
    });
});
</script>
@endpush