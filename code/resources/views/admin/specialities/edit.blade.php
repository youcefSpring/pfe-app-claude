@extends('layouts.pfe-app')

@section('page-title', 'Edit Speciality')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Edit Speciality</h4>
                    <a href="{{ route('admin.specialities.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Specialities
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.specialities.update', $speciality) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Speciality Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name', $speciality->name) }}" required
                                           placeholder="e.g., Computer Science, Electrical Engineering">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="code" class="form-label">Speciality Code</label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror"
                                           id="code" name="code" value="{{ old('code', $speciality->code) }}"
                                           placeholder="e.g., CS, EE" style="text-transform: uppercase;">
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Optional short code (2-5 characters)</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="level" class="form-label">Academic Level <span class="text-danger">*</span></label>
                                    <select class="form-select @error('level') is-invalid @enderror"
                                            id="level" name="level" required>
                                        <option value="">Select Level</option>
                                        <option value="L2 ING" {{ old('level', $speciality->level) == 'L2 ING' ? 'selected' : '' }}>L2 ING (2nd Year Engineering)</option>
                                        <option value="L3 LMD" {{ old('level', $speciality->level) == 'L3 LMD' ? 'selected' : '' }}>L3 LMD (3rd Year License LMD)</option>
                                        <option value="L4 ING" {{ old('level', $speciality->level) == 'L4 ING' ? 'selected' : '' }}>L4 ING (4th Year Engineering)</option>
                                        <option value="L5 ING" {{ old('level', $speciality->level) == 'L5 ING' ? 'selected' : '' }}>L5 ING (5th Year Engineering)</option>
                                        <option value="M2 LMD" {{ old('level', $speciality->level) == 'M2 LMD' ? 'selected' : '' }}>M2 LMD (2nd Year Master LMD)</option>
                                    </select>
                                    @error('level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="academic_year" class="form-label">Academic Year <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('academic_year') is-invalid @enderror"
                                           id="academic_year" name="academic_year" value="{{ old('academic_year', $speciality->academic_year) }}" required
                                           placeholder="2024-2025">
                                    @error('academic_year')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="semester" class="form-label">Semester</label>
                                    <select class="form-select @error('semester') is-invalid @enderror"
                                            id="semester" name="semester">
                                        <option value="">Select Semester</option>
                                        <option value="1" {{ old('semester', $speciality->semester) == '1' ? 'selected' : '' }}>Semester 1</option>
                                        <option value="2" {{ old('semester', $speciality->semester) == '2' ? 'selected' : '' }}>Semester 2</option>
                                    </select>
                                    @error('semester')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Optional</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="4"
                                      placeholder="Brief description of the speciality, its focus areas, and career prospects...">{{ old('description', $speciality->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Optional but recommended for better organization</small>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                       {{ old('is_active', $speciality->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active Speciality
                                </label>
                                <small class="form-text text-muted d-block">Only active specialities can be assigned to students</small>
                            </div>
                        </div>

                        <hr>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Speciality
                            </button>
                            <a href="{{ route('admin.specialities.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Speciality Information -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Speciality Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Created:</strong> {{ $speciality->created_at->format('M d, Y H:i') }}</p>
                            <p><strong>Last Updated:</strong> {{ $speciality->updated_at->format('M d, Y H:i') }}</p>
                            <p><strong>Status:</strong>
                                @if($speciality->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Students Enrolled:</strong>
                                <span class="badge bg-info">{{ $speciality->students_count ?? 0 }}</span>
                            </p>
                            <p><strong>Full Name:</strong> {{ $speciality->name }}</p>
                            @if($speciality->code)
                                <p><strong>Code:</strong> <span class="badge bg-secondary">{{ $speciality->code }}</span></p>
                            @endif
                        </div>
                    </div>

                    @if($speciality->students_count > 0)
                        <hr>
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Important Note</h6>
                            <p class="mb-0">
                                This speciality has <strong>{{ $speciality->students_count }}</strong> enrolled students.
                                Changing the level or major details may affect student records and reporting.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-uppercase the code field
    const codeInput = document.getElementById('code');
    codeInput.addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });
});
</script>
@endpush
@endsection