@extends('layouts.pfe-app')

@section('page-title', __('app.personal_information'))

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-user me-2"></i>
                            {{ __('app.personal_information') }}
                        </h4>
                        <span class="badge bg-light text-dark">{{ __('app.step') }} 1/3</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="progress mb-4" style="height: 10px;">
                        <div class="progress-bar bg-primary" style="width: 33%"></div>
                    </div>

                    <form action="{{ route('student.setup.store-personal-info') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="date_naissance" class="form-label">
                                    {{ __('app.birth_date') }} <span class="text-danger">*</span>
                                </label>
                                <input type="date"
                                       class="form-control @error('date_naissance') is-invalid @enderror"
                                       id="date_naissance"
                                       name="date_naissance"
                                       value="{{ old('date_naissance', $user->date_naissance) }}"
                                       required>
                                @error('date_naissance')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="lieu_naissance" class="form-label">
                                    {{ __('app.birth_place') }} <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control @error('lieu_naissance') is-invalid @enderror"
                                       id="lieu_naissance"
                                       name="lieu_naissance"
                                       value="{{ old('lieu_naissance', $user->lieu_naissance) }}"
                                       placeholder="{{ __('app.enter_birth_place') }}"
                                       required>
                                @error('lieu_naissance')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="student_level" class="form-label">
                                    {{ __('app.student_level') }} <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('student_level') is-invalid @enderror"
                                        id="student_level"
                                        name="student_level"
                                        required>
                                    <option value="">{{ __('app.select_level') }}</option>
                                    <option value="licence_3" {{ old('student_level', $user->student_level) === 'licence_3' ? 'selected' : '' }}>
                                        {{ __('app.licence_3') }}
                                    </option>
                                    <option value="master_1" {{ old('student_level', $user->student_level) === 'master_1' ? 'selected' : '' }}>
                                        {{ __('app.master_1') }}
                                    </option>
                                    <option value="master_2" {{ old('student_level', $user->student_level) === 'master_2' ? 'selected' : '' }}>
                                        {{ __('app.master_2') }}
                                    </option>
                                </select>
                                @error('student_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="birth_certificate" class="form-label">
                                {{ __('app.birth_certificate') }} <span class="text-danger">*</span>
                            </label>
                            <input type="file"
                                   class="form-control @error('birth_certificate') is-invalid @enderror"
                                   id="birth_certificate"
                                   name="birth_certificate"
                                   accept=".pdf,.jpg,.jpeg,.png"
                                   required>
                            <div class="form-text">
                                {{ __('app.birth_certificate_requirements') }}
                            </div>
                            @error('birth_certificate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <h6 class="alert-heading">
                                <i class="fas fa-info-circle me-2"></i>
                                {{ __('app.why_we_need_this') }}
                            </h6>
                            <p class="mb-0">{{ __('app.birth_certificate_explanation') }}</p>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('student.setup.welcome') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>
                                {{ __('app.back') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                {{ __('app.continue') }}
                                <i class="fas fa-arrow-right ms-2"></i>
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
    const fileInput = document.getElementById('birth_certificate');
    const studentLevel = document.getElementById('student_level');

    // File size validation
    fileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file && file.size > 2048 * 1024) { // 2MB in bytes
            alert('{{ __("app.file_too_large") }}');
            this.value = '';
        }
    });
});
</script>
@endpush