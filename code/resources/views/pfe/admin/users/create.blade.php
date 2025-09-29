@extends('layouts.admin')

@section('title', __('Add New User'))
@section('page-title', __('Add New User'))

@section('breadcrumbs')
<span class="text-muted">{{ __('Home') }} / {{ __('Administration') }} / <a href="{{ route('pfe.admin.users.index') }}">{{ __('Users') }}</a> / {{ __('Add New') }}</span>
@endsection

@section('content')
<div class="container-fluid p-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('pfe.admin.users.index') }}" class="btn btn-outline-secondary me-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="h3 mb-0">{{ __('Add New User') }}</h1>
                    <p class="text-muted mb-0">{{ __('Create a new user account in the system') }}</p>
                </div>
            </div>

            <!-- User Creation Form -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('User Information') }}</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('pfe.admin.users.store') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Personal Information -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">{{ __('First Name') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                       id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                                @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="form-label">{{ __('Last Name') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                       id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                                @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="email" class="form-label">{{ __('Email Address') }} <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">{{ __('Phone Number') }}</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                       id="phone" name="phone" value="{{ old('phone') }}">
                                @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Account Information -->
                        <hr class="my-4">
                        <h6 class="mb-3">{{ __('Account Information') }}</h6>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="role" class="form-label">{{ __('User Role') }} <span class="text-danger">*</span></label>
                                <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                    <option value="">{{ __('Select a role') }}</option>
                                    <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>{{ __('Student') }}</option>
                                    <option value="teacher" {{ old('role') == 'teacher' ? 'selected' : '' }}>{{ __('Teacher') }}</option>
                                    <option value="admin_pfe" {{ old('role') == 'admin_pfe' ? 'selected' : '' }}>{{ __('PFE Administrator') }}</option>
                                    <option value="chef_master" {{ old('role') == 'chef_master' ? 'selected' : '' }}>{{ __('Chef Master') }}</option>
                                </select>
                                @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label">{{ __('Account Status') }}</label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                </select>
                                @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="password" class="form-label">{{ __('Password') }} <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       id="password" name="password" required>
                                <div class="form-text">{{ __('Minimum 8 characters') }}</div>
                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }} <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            </div>
                        </div>

                        <!-- Student-Specific Fields -->
                        <div id="student-fields" class="d-none">
                            <hr class="my-4">
                            <h6 class="mb-3">{{ __('Student Information') }}</h6>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="student_id" class="form-label">{{ __('Student ID') }}</label>
                                    <input type="text" class="form-control @error('student_id') is-invalid @enderror"
                                           id="student_id" name="student_id" value="{{ old('student_id') }}">
                                    @error('student_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="level" class="form-label">{{ __('Academic Level') }}</label>
                                    <select class="form-select @error('level') is-invalid @enderror" id="level" name="level">
                                        <option value="">{{ __('Select level') }}</option>
                                        <option value="L3" {{ old('level') == 'L3' ? 'selected' : '' }}>{{ __('License 3') }}</option>
                                        <option value="M1" {{ old('level') == 'M1' ? 'selected' : '' }}>{{ __('Master 1') }}</option>
                                        <option value="M2" {{ old('level') == 'M2' ? 'selected' : '' }}>{{ __('Master 2') }}</option>
                                    </select>
                                    @error('level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="specialty" class="form-label">{{ __('Specialty') }}</label>
                                    <input type="text" class="form-control @error('specialty') is-invalid @enderror"
                                           id="specialty" name="specialty" value="{{ old('specialty') }}">
                                    @error('specialty')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="academic_year" class="form-label">{{ __('Academic Year') }}</label>
                                    <input type="text" class="form-control @error('academic_year') is-invalid @enderror"
                                           id="academic_year" name="academic_year" value="{{ old('academic_year', '2024-2025') }}">
                                    @error('academic_year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Teacher-Specific Fields -->
                        <div id="teacher-fields" class="d-none">
                            <hr class="my-4">
                            <h6 class="mb-3">{{ __('Teacher Information') }}</h6>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="department" class="form-label">{{ __('Department') }}</label>
                                    <input type="text" class="form-control @error('department') is-invalid @enderror"
                                           id="department" name="department" value="{{ old('department') }}">
                                    @error('department')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="title" class="form-label">{{ __('Academic Title') }}</label>
                                    <select class="form-select @error('title') is-invalid @enderror" id="title" name="title">
                                        <option value="">{{ __('Select title') }}</option>
                                        <option value="Dr." {{ old('title') == 'Dr.' ? 'selected' : '' }}>{{ __('Doctor') }}</option>
                                        <option value="Prof." {{ old('title') == 'Prof.' ? 'selected' : '' }}>{{ __('Professor') }}</option>
                                        <option value="Mr." {{ old('title') == 'Mr.' ? 'selected' : '' }}>{{ __('Mister') }}</option>
                                        <option value="Ms." {{ old('title') == 'Ms.' ? 'selected' : '' }}>{{ __('Miss') }}</option>
                                    </select>
                                    @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('pfe.admin.users.index') }}" class="btn btn-secondary">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>{{ __('Create User') }}
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
    const roleSelect = document.getElementById('role');
    const studentFields = document.getElementById('student-fields');
    const teacherFields = document.getElementById('teacher-fields');

    function toggleFields() {
        const selectedRole = roleSelect.value;

        // Hide all role-specific fields
        studentFields.classList.add('d-none');
        teacherFields.classList.add('d-none');

        // Show relevant fields based on role
        if (selectedRole === 'student') {
            studentFields.classList.remove('d-none');
        } else if (selectedRole === 'teacher') {
            teacherFields.classList.remove('d-none');
        }
    }

    roleSelect.addEventListener('change', toggleFields);

    // Initialize on page load
    toggleFields();
});
</script>
@endpush