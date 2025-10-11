@extends('layouts.pfe-app')

@section('page-title', __('app.edit_speciality'))

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">{{ __('app.edit_speciality') }}</h4>
                    <a href="{{ route('admin.specialities.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> {{ __('app.back_to_specialities') }}
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.specialities.update', $speciality) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="name" class="form-label">{{ __('app.speciality_name') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name', $speciality->name) }}" required
                                           placeholder="{{ __('app.speciality_name_placeholder') }}">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="code" class="form-label">{{ __('app.speciality_code') }}</label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror"
                                           id="code" name="code" value="{{ old('code', $speciality->code) }}"
                                           placeholder="{{ __('app.speciality_code_placeholder') }}" style="text-transform: uppercase;">
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">{{ __('app.speciality_code_help') }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="level" class="form-label">{{ __('app.academic_level') }} <span class="text-danger">*</span></label>
                                    <select class="form-select @error('level') is-invalid @enderror"
                                            id="level" name="level" required>
                                        <option value="">{{ __('app.select_level') }}</option>
                                        <option value="licence" {{ old('level', $speciality->level) == 'licence' ? 'selected' : '' }}>{{ __('app.licence') }}</option>
                                        <option value="master" {{ old('level', $speciality->level) == 'master' ? 'selected' : '' }}>{{ __('app.master') }}</option>
                                        <option value="ingenieur" {{ old('level', $speciality->level) == 'ingenieur' ? 'selected' : '' }}>{{ __('app.engineer') }}</option>
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
                                           id="academic_year" name="academic_year" value="{{ old('academic_year', $speciality->academic_year) }}" required
                                           placeholder="{{ __('app.academic_year_placeholder') }}">
                                    @error('academic_year')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">{{ __('app.academic_year_format') }}</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="semester" class="form-label">{{ __('app.semester') }}</label>
                                    <select class="form-select @error('semester') is-invalid @enderror"
                                            id="semester" name="semester">
                                        <option value="">{{ __('app.select_semester') }}</option>
                                        <option value="1" {{ old('semester', $speciality->semester) == '1' ? 'selected' : '' }}>{{ __('app.semester_1') }}</option>
                                        <option value="2" {{ old('semester', $speciality->semester) == '2' ? 'selected' : '' }}>{{ __('app.semester_2') }}</option>
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
                                      placeholder="{{ __('app.speciality_description_placeholder') }}">{{ old('description', $speciality->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">{{ __('app.description_help') }}</small>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                       {{ old('is_active', $speciality->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    {{ __('app.active_speciality') }}
                                </label>
                                <small class="form-text text-muted d-block">{{ __('app.active_speciality_help') }}</small>
                            </div>
                        </div>

                        <hr>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('app.update_speciality') }}
                            </button>
                            <a href="{{ route('admin.specialities.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> {{ __('app.cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Speciality Information -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('app.speciality_information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>{{ __('app.created') }}:</strong> {{ $speciality->created_at->format('M d, Y H:i') }}</p>
                            <p><strong>{{ __('app.last_updated') }}:</strong> {{ $speciality->updated_at->format('M d, Y H:i') }}</p>
                            <p><strong>{{ __('app.status') }}:</strong>
                                @if($speciality->is_active)
                                    <span class="badge bg-success">{{ __('app.active') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ __('app.inactive') }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>{{ __('app.students_enrolled') }}:</strong>
                                <span class="badge bg-info">{{ $speciality->students_count ?? 0 }}</span>
                            </p>
                            <p><strong>{{ __('app.full_name') }}:</strong> {{ $speciality->name }}</p>
                            @if($speciality->code)
                                <p><strong>{{ __('app.code') }}:</strong> <span class="badge bg-secondary">{{ $speciality->code }}</span></p>
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