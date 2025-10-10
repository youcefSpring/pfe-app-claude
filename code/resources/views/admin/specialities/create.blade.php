@extends('layouts.pfe-app')

@section('page-title', 'Create Speciality')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Create New Speciality</h4>
                    <a href="{{ route('admin.specialities.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Specialities
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.specialities.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Speciality Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name') }}" required
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
                                           id="code" name="code" value="{{ old('code') }}"
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
                                        <option value="licence" {{ old('level') == 'licence' ? 'selected' : '' }}>Licence</option>
                                        <option value="master" {{ old('level') == 'master' ? 'selected' : '' }}>Master</option>
                                        <option value="ingenieur" {{ old('level') == 'ingenieur' ? 'selected' : '' }}>Ingénieur</option>
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
                                           id="academic_year" name="academic_year" value="{{ old('academic_year', '2024/2025') }}" required
                                           placeholder="2024/2025" readonly>
                                    @error('academic_year')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Format: YYYY/YYYY (automatically set)</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="semester" class="form-label">Semester</label>
                                    <select class="form-select @error('semester') is-invalid @enderror"
                                            id="semester" name="semester">
                                        <option value="">Select Semester</option>
                                        <option value="1" {{ old('semester') == '1' ? 'selected' : '' }}>Semester 1</option>
                                        <option value="2" {{ old('semester') == '2' ? 'selected' : '' }}>Semester 2</option>
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
                                      placeholder="Brief description of the speciality, its focus areas, and career prospects...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Optional but recommended for better organization</small>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active Speciality
                                </label>
                                <small class="form-text text-muted d-block">Only active specialities can be assigned to students</small>
                            </div>
                        </div>

                        <hr>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-graduation-cap"></i> Create Speciality
                            </button>
                            <a href="{{ route('admin.specialities.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Help Information -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle text-primary"></i> Speciality Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <h6 class="text-primary">License Programs</h6>
                                <small class="text-muted">
                                    Undergraduate programs typically lasting 3 years.
                                    Students in license programs work on practical projects.
                                </small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <h6 class="text-success">Master Programs</h6>
                                <small class="text-muted">
                                    Graduate programs typically lasting 2 years.
                                    Master students work on advanced research projects.
                                </small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <h6 class="text-warning">Doctorate Programs</h6>
                                <small class="text-muted">
                                    PhD programs lasting 3-5 years.
                                    Doctoral students conduct original research.
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3">
                        <h6><i class="fas fa-lightbulb"></i> Best Practices</h6>
                        <ul class="mb-0">
                            <li>Use clear, descriptive names for specialities</li>
                            <li>Include the academic year for better organization</li>
                            <li>Add detailed descriptions to help students choose</li>
                            <li>Use speciality codes for quick identification</li>
                        </ul>
                    </div>
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

    // Set current academic year if empty (use correct format YYYY/YYYY)
    const yearInput = document.getElementById('academic_year');
    if (!yearInput.value) {
        const currentDate = new Date();
        const currentYear = currentDate.getFullYear();
        const currentMonth = currentDate.getMonth() + 1; // JavaScript months are 0-indexed

        // Academic year starts in September (month 9)
        if (currentMonth >= 9) {
            yearInput.value = `${currentYear}/${currentYear + 1}`;
        } else {
            yearInput.value = `${currentYear - 1}/${currentYear}`;
        }
    }
});
</script>
@endpush
@endsection