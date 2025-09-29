@extends('layouts.admin')

@section('title', __('Edit User'))
@section('page-title', __('Edit User'))

@section('breadcrumbs')
<span class="text-muted">{{ __('Home') }} / {{ __('Administration') }} / <a href="{{ route('pfe.admin.users.index') }}">{{ __('Users') }}</a> / {{ __('Edit') }}</span>
@endsection

@section('content')
<div class="container-fluid p-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('pfe.admin.users.show', $id) }}" class="btn btn-outline-secondary me-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="h3 mb-0">{{ __('Edit User') }}</h1>
                    <p class="text-muted mb-0">{{ __('Update user information and settings') }}</p>
                </div>
            </div>

            <!-- Edit User Form -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('User Information') }}</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('pfe.admin.users.update', $id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Personal Information -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">{{ __('First Name') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                       id="first_name" name="first_name" value="{{ old('first_name', $user->first_name ?? 'Sample') }}" required>
                                @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="form-label">{{ __('Last Name') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                       id="last_name" name="last_name" value="{{ old('last_name', $user->last_name ?? 'User') }}" required>
                                @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="email" class="form-label">{{ __('Email Address') }} <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email', $user->email ?? 'user@example.com') }}" required>
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">{{ __('Phone Number') }}</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                       id="phone" name="phone" value="{{ old('phone', $user->phone ?? '') }}">
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
                                    <option value="student" {{ old('role', $user->role ?? 'student') == 'student' ? 'selected' : '' }}>{{ __('Student') }}</option>
                                    <option value="teacher" {{ old('role', $user->role ?? '') == 'teacher' ? 'selected' : '' }}>{{ __('Teacher') }}</option>
                                    <option value="admin_pfe" {{ old('role', $user->role ?? '') == 'admin_pfe' ? 'selected' : '' }}>{{ __('PFE Administrator') }}</option>
                                    <option value="chef_master" {{ old('role', $user->role ?? '') == 'chef_master' ? 'selected' : '' }}>{{ __('Chef Master') }}</option>
                                </select>
                                @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label">{{ __('Account Status') }}</label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                    <option value="active" {{ old('status', $user->status ?? 'active') == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                    <option value="inactive" {{ old('status', $user->status ?? '') == 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                </select>
                                @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Password Change -->
                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="change_password" name="change_password">
                                    <label class="form-check-label" for="change_password">
                                        {{ __('Change Password') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div id="password-fields" class="d-none">
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="password" class="form-label">{{ __('New Password') }}</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                           id="password" name="password">
                                    <div class="form-text">{{ __('Minimum 8 characters') }}</div>
                                    @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="password_confirmation" class="form-label">{{ __('Confirm New Password') }}</label>
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                                </div>
                            </div>
                        </div>

                        <!-- Role-Specific Fields -->
                        <div id="student-fields" class="{{ old('role', $user->role ?? 'student') == 'student' ? '' : 'd-none' }}">
                            <hr class="my-4">
                            <h6 class="mb-3">{{ __('Student Information') }}</h6>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="student_id" class="form-label">{{ __('Student ID') }}</label>
                                    <input type="text" class="form-control @error('student_id') is-invalid @enderror"
                                           id="student_id" name="student_id" value="{{ old('student_id', $user->student_id ?? '') }}">
                                    @error('student_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="level" class="form-label">{{ __('Academic Level') }}</label>
                                    <select class="form-select @error('level') is-invalid @enderror" id="level" name="level">
                                        <option value="">{{ __('Select level') }}</option>
                                        <option value="L3" {{ old('level', $user->level ?? '') == 'L3' ? 'selected' : '' }}>{{ __('License 3') }}</option>
                                        <option value="M1" {{ old('level', $user->level ?? '') == 'M1' ? 'selected' : '' }}>{{ __('Master 1') }}</option>
                                        <option value="M2" {{ old('level', $user->level ?? '') == 'M2' ? 'selected' : '' }}>{{ __('Master 2') }}</option>
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
                                           id="specialty" name="specialty" value="{{ old('specialty', $user->specialty ?? '') }}">
                                    @error('specialty')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="academic_year" class="form-label">{{ __('Academic Year') }}</label>
                                    <input type="text" class="form-control @error('academic_year') is-invalid @enderror"
                                           id="academic_year" name="academic_year" value="{{ old('academic_year', $user->academic_year ?? '2024-2025') }}">
                                    @error('academic_year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Teacher-Specific Fields -->
                        <div id="teacher-fields" class="{{ old('role', $user->role ?? '') == 'teacher' ? '' : 'd-none' }}">
                            <hr class="my-4">
                            <h6 class="mb-3">{{ __('Teacher Information') }}</h6>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="department" class="form-label">{{ __('Department') }}</label>
                                    <input type="text" class="form-control @error('department') is-invalid @enderror"
                                           id="department" name="department" value="{{ old('department', $user->department ?? '') }}">
                                    @error('department')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="title" class="form-label">{{ __('Academic Title') }}</label>
                                    <select class="form-select @error('title') is-invalid @enderror" id="title" name="title">
                                        <option value="">{{ __('Select title') }}</option>
                                        <option value="Dr." {{ old('title', $user->title ?? '') == 'Dr.' ? 'selected' : '' }}>{{ __('Doctor') }}</option>
                                        <option value="Prof." {{ old('title', $user->title ?? '') == 'Prof.' ? 'selected' : '' }}>{{ __('Professor') }}</option>
                                        <option value="Mr." {{ old('title', $user->title ?? '') == 'Mr.' ? 'selected' : '' }}>{{ __('Mister') }}</option>
                                        <option value="Ms." {{ old('title', $user->title ?? '') == 'Ms.' ? 'selected' : '' }}>{{ __('Miss') }}</option>
                                    </select>
                                    @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('pfe.admin.users.show', $id) }}" class="btn btn-secondary">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>{{ __('Update User') }}
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
    const changePasswordCheckbox = document.getElementById('change_password');
    const passwordFields = document.getElementById('password-fields');

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

    function togglePasswordFields() {
        if (changePasswordCheckbox.checked) {
            passwordFields.classList.remove('d-none');
            document.getElementById('password').required = true;
            document.getElementById('password_confirmation').required = true;
        } else {
            passwordFields.classList.add('d-none');
            document.getElementById('password').required = false;
            document.getElementById('password_confirmation').required = false;
        }
    }

    roleSelect.addEventListener('change', toggleFields);
    changePasswordCheckbox.addEventListener('change', togglePasswordFields);

    // Initialize on page load
    toggleFields();
    togglePasswordFields();
});
</script>
@endpush