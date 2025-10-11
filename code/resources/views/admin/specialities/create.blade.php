@extends('layouts.pfe-app')

@section('page-title', __('app.create_speciality'))

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">{{ __('app.create_speciality') }}</h4>
                    <a href="{{ route('admin.specialities.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> {{ __('app.back') }}
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.specialities.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="name" class="form-label">{{ __('app.name') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name') }}" required
                                           placeholder="{{ __('app.speciality_name_placeholder') }}">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="code" class="form-label">{{ __('app.code') }}</label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror"
                                           id="code" name="code" value="{{ old('code') }}"
                                           placeholder="{{ __('app.speciality_code_placeholder') }}" style="text-transform: uppercase;">
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">{{ __('app.optional') }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="level" class="form-label">{{ __('app.level') }} <span class="text-danger">*</span></label>
                                    <select class="form-select @error('level') is-invalid @enderror"
                                            id="level" name="level" required>
                                        <option value="">{{ __('app.select_level') }}</option>
                                        <option value="licence" {{ old('level') == 'licence' ? 'selected' : '' }}>{{ __('app.licence') }}</option>
                                        <option value="master" {{ old('level') == 'master' ? 'selected' : '' }}>{{ __('app.master') }}</option>
                                        <option value="ingenieur" {{ old('level') == 'ingenieur' ? 'selected' : '' }}>{{ __('app.ingenieur') }}</option>
                                    </select>
                                    @error('level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="academic_year" class="form-label">{{ __('app.academic_year') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('academic_year') is-invalid @enderror"
                                           id="academic_year" name="academic_year" value="{{ old('academic_year') }}" required
                                           placeholder="2024-2025">
                                    @error('academic_year')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">{{ __('app.enter_academic_year') }}</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="semester" class="form-label">{{ __('app.semester') }}</label>
                                    <select class="form-select @error('semester') is-invalid @enderror"
                                            id="semester" name="semester">
                                        <option value="">{{ __('app.select_semester') }}</option>
                                        <option value="1" {{ old('semester') == '1' ? 'selected' : '' }}>{{ __('app.semester_1') }}</option>
                                        <option value="2" {{ old('semester') == '2' ? 'selected' : '' }}>{{ __('app.semester_2') }}</option>
                                    </select>
                                    @error('semester')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">{{ __('app.optional') }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">{{ __('app.description') }}</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="4"
                                      placeholder="{{ __('app.speciality_description_placeholder') }}">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">{{ __('app.optional_but_recommended') }}</small>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    {{ __('app.active_speciality') }}
                                </label>
                                <small class="form-text text-muted d-block">{{ __('app.only_active_specialities_assigned') }}</small>
                            </div>
                        </div>

                        <hr>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-graduation-cap"></i> {{ __('app.create_speciality') }}
                            </button>
                            <a href="{{ route('admin.specialities.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> {{ __('app.cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Help Information -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle text-primary"></i> {{ __('app.speciality_information') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <h6 class="text-primary">{{ __('app.licence_programs') }}</h6>
                                <small class="text-muted">
                                    {{ __('app.licence_programs_description') }}
                                </small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <h6 class="text-success">{{ __('app.master_programs') }}</h6>
                                <small class="text-muted">
                                    {{ __('app.master_programs_description') }}
                                </small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <h6 class="text-warning">{{ __('app.doctorate_programs') }}</h6>
                                <small class="text-muted">
                                    {{ __('app.doctorate_programs_description') }}
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3">
                        <h6><i class="fas fa-lightbulb"></i> {{ __('app.best_practices') }}</h6>
                        <ul class="mb-0">
                            <li>{{ __('app.use_clear_descriptive_names') }}</li>
                            <li>{{ __('app.include_academic_year_organization') }}</li>
                            <li>{{ __('app.add_detailed_descriptions_help') }}</li>
                            <li>{{ __('app.use_speciality_codes_identification') }}</li>
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

    // Set current academic year if empty (use correct format YYYY-YYYY)
    const yearInput = document.getElementById('academic_year');
    if (!yearInput.value) {
        const currentDate = new Date();
        const currentYear = currentDate.getFullYear();
        const currentMonth = currentDate.getMonth() + 1; // JavaScript months are 0-indexed

        // Academic year starts in September (month 9)
        if (currentMonth >= 9) {
            yearInput.value = `${currentYear}-${currentYear + 1}`;
        } else {
            yearInput.value = `${currentYear - 1}-${currentYear}`;
        }
    }
});
</script>
@endpush
@endsection