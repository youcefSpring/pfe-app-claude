@extends('layouts.pfe-app')

@section('page-title', 'Create User')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Create New User</h4>
                    <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Users
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.users.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                           id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                    <select class="form-select @error('role') is-invalid @enderror"
                                            id="role" name="role" required>
                                        <option value="">Select Role</option>
                                        <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student</option>
                                        <option value="teacher" {{ old('role') == 'teacher' ? 'selected' : '' }}>Teacher</option>
                                        <option value="department_head" {{ old('role') == 'department_head' ? 'selected' : '' }}>Department Head</option>
                                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                    </select>
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="matricule" class="form-label">Matricule</label>
                                    <input type="text" class="form-control @error('matricule') is-invalid @enderror"
                                           id="matricule" name="matricule" value="{{ old('matricule') }}">
                                    @error('matricule')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Optional for teachers and admins</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="department" class="form-label">Department</label>
                                    <input type="text" class="form-control @error('department') is-invalid @enderror"
                                           id="department" name="department" value="{{ old('department', 'Computer Science') }}" readonly>
                                    @error('department')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Currently fixed to Computer Science department</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="speciality_id" class="form-label">Speciality</label>
                                    <select class="form-select @error('speciality_id') is-invalid @enderror"
                                            id="speciality_id" name="speciality_id">
                                        <option value="">Select Speciality</option>
                                        @foreach($specialities as $speciality)
                                            <option value="{{ $speciality->id }}"
                                                {{ old('speciality_id') == $speciality->id ? 'selected' : '' }}>
                                                {{ $speciality->name }} ({{ ucfirst($speciality->level) }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('speciality_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Required for students</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <h6 class="text-primary">Password Settings</h6>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                           id="password" name="password" required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Minimum 8 characters</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control"
                                           id="password_confirmation" name="password_confirmation" required>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="send_welcome_email" name="send_welcome_email" checked>
                                <label class="form-check-label" for="send_welcome_email">
                                    Send welcome email with login credentials
                                </label>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-user-plus"></i> Create User
                            </button>
                            <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Role Descriptions -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Role Descriptions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h6 class="text-primary">Student</h6>
                                <small class="text-muted">Can join teams, select subjects, submit projects, and view defenses.</small>
                            </div>
                            <div class="mb-3">
                                <h6 class="text-success">Teacher</h6>
                                <small class="text-muted">Can create subjects, supervise projects, grade submissions, and participate in juries.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h6 class="text-warning">Department Head</h6>
                                <small class="text-muted">Can validate subjects, manage conflicts, and oversee department activities.</small>
                            </div>
                            <div class="mb-3">
                                <h6 class="text-danger">Admin</h6>
                                <small class="text-muted">Full system access including user management, reports, and system settings.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    const specialitySelect = document.getElementById('speciality_id');
    const matriculeInput = document.getElementById('matricule');

    roleSelect.addEventListener('change', function() {
        const isStudent = this.value === 'student';

        // Make speciality required for students
        if (isStudent) {
            specialitySelect.setAttribute('required', 'required');
            specialitySelect.parentElement.querySelector('label').innerHTML =
                'Speciality <span class="text-danger">*</span>';
        } else {
            specialitySelect.removeAttribute('required');
            specialitySelect.parentElement.querySelector('label').innerHTML = 'Speciality';
        }
    });
});
</script>
@endpush
@endsection