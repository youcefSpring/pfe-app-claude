@extends('layouts.pfe-app')

@section('title', __('app.create_speciality'))

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ __('app.create_speciality') }}</h5>
                        <a href="{{ route('specialities.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> {{ __('app.back') }}
                        </a>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('specialities.store') }}" method="POST">
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
                                               placeholder="{{ __('app.speciality_code_placeholder') }}">
                                        @error('code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="level" class="form-label">{{ __('app.level') }} <span class="text-danger">*</span></label>
                                        <select class="form-select @error('level') is-invalid @enderror" id="level" name="level" required>
                                            <option value="">{{ __('app.select_level') }}</option>
                                            <option value="licence" {{ old('level') === 'licence' ? 'selected' : '' }}>{{ __('app.licence') }}</option>
                                            <option value="master" {{ old('level') === 'master' ? 'selected' : '' }}>{{ __('app.master') }}</option>
                                            <option value="ingenieur" {{ old('level') === 'ingenieur' ? 'selected' : '' }}>{{ __('app.ingenieur') }}</option>
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
                                               id="academic_year" name="academic_year"
                                               value="{{ old('academic_year', App\Models\Speciality::getCurrentAcademicYear()) }}"
                                               required placeholder="2024/2025">
                                        @error('academic_year')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="semester" class="form-label">{{ __('app.semester') }}</label>
                                        <input type="text" class="form-control @error('semester') is-invalid @enderror"
                                               id="semester" name="semester" value="{{ old('semester') }}"
                                               placeholder="{{ __('app.semester_placeholder') }}">
                                        @error('semester')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
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
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                           value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        {{ __('app.active') }}
                                    </label>
                                </div>
                                <small class="form-text text-muted">{{ __('app.active_speciality_help') }}</small>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('specialities.index') }}" class="btn btn-secondary">
                                    {{ __('app.cancel') }}
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> {{ __('app.create') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Auto-generate code from name
    document.getElementById('name').addEventListener('input', function() {
        const name = this.value;
        const codeField = document.getElementById('code');

        if (!codeField.value || codeField.dataset.autoGenerated === 'true') {
            // Generate code from first letters of words
            const words = name.trim().split(/\s+/);
            let code = words.map(word => word.charAt(0).toUpperCase()).join('');

            // Limit to 6 characters
            if (code.length > 6) {
                code = code.substring(0, 6);
            }

            codeField.value = code;
            codeField.dataset.autoGenerated = 'true';
        }
    });

    // Remove auto-generation flag when user manually edits code
    document.getElementById('code').addEventListener('input', function() {
        this.dataset.autoGenerated = 'false';
    });
</script>
@endsection