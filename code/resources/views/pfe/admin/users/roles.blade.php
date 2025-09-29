@extends('layouts.admin')

@section('title', __('Manage User Roles'))
@section('page-title', __('Manage User Roles'))

@section('breadcrumbs')
<span class="text-muted">{{ __('Home') }} / {{ __('Administration') }} / <a href="{{ route('pfe.admin.users.index') }}">{{ __('Users') }}</a> / {{ __('Roles') }}</span>
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
                    <h1 class="h3 mb-0">{{ __('Manage User Roles') }}</h1>
                    <p class="text-muted mb-0">{{ __('Assign roles and permissions to user') }}</p>
                </div>
            </div>

            <!-- User Information Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <img src="{{ $user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name ?? 'Sample User') }}"
                             alt="{{ $user->name ?? 'Sample User' }}" class="rounded-circle me-3" style="width: 60px; height: 60px;">
                        <div>
                            <h5 class="mb-1">{{ $user->name ?? 'Sample User' }}</h5>
                            <p class="text-muted mb-1">{{ $user->email ?? 'user@example.com' }}</p>
                            <span class="badge bg-{{ ($user->role ?? 'student') == 'admin_pfe' ? 'danger' : (($user->role ?? 'student') == 'teacher' ? 'primary' : 'success') }}">
                                {{ __(ucfirst($user->role ?? 'student')) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Role Management Form -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Role Assignment') }}</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('pfe.admin.users.update-roles', $id) }}">
                        @csrf
                        @method('PUT')

                        <!-- Primary Role Selection -->
                        <div class="mb-4">
                            <label class="form-label">{{ __('Primary Role') }} <span class="text-danger">*</span></label>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="card role-card {{ ($user->role ?? 'student') == 'student' ? 'border-success' : '' }}"
                                         onclick="selectRole('student')">
                                        <div class="card-body text-center">
                                            <input type="radio" name="primary_role" value="student" id="role_student"
                                                   {{ ($user->role ?? 'student') == 'student' ? 'checked' : '' }} class="d-none">
                                            <i class="fas fa-graduation-cap fa-3x text-success mb-3"></i>
                                            <h5>{{ __('Student') }}</h5>
                                            <p class="text-muted small mb-0">{{ __('Access to student features and project management') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card role-card {{ ($user->role ?? '') == 'teacher' ? 'border-primary' : '' }}"
                                         onclick="selectRole('teacher')">
                                        <div class="card-body text-center">
                                            <input type="radio" name="primary_role" value="teacher" id="role_teacher"
                                                   {{ ($user->role ?? '') == 'teacher' ? 'checked' : '' }} class="d-none">
                                            <i class="fas fa-chalkboard-teacher fa-3x text-primary mb-3"></i>
                                            <h5>{{ __('Teacher') }}</h5>
                                            <p class="text-muted small mb-0">{{ __('Supervise projects and manage students') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card role-card {{ ($user->role ?? '') == 'admin_pfe' ? 'border-danger' : '' }}"
                                         onclick="selectRole('admin_pfe')">
                                        <div class="card-body text-center">
                                            <input type="radio" name="primary_role" value="admin_pfe" id="role_admin_pfe"
                                                   {{ ($user->role ?? '') == 'admin_pfe' ? 'checked' : '' }} class="d-none">
                                            <i class="fas fa-user-shield fa-3x text-danger mb-3"></i>
                                            <h5>{{ __('PFE Administrator') }}</h5>
                                            <p class="text-muted small mb-0">{{ __('Full administrative access to PFE system') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card role-card {{ ($user->role ?? '') == 'chef_master' ? 'border-warning' : '' }}"
                                         onclick="selectRole('chef_master')">
                                        <div class="card-body text-center">
                                            <input type="radio" name="primary_role" value="chef_master" id="role_chef_master"
                                                   {{ ($user->role ?? '') == 'chef_master' ? 'checked' : '' }} class="d-none">
                                            <i class="fas fa-crown fa-3x text-warning mb-3"></i>
                                            <h5>{{ __('Chef Master') }}</h5>
                                            <p class="text-muted small mb-0">{{ __('Senior administrator with special privileges') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Role Permissions -->
                        <div class="mb-4">
                            <label class="form-label">{{ __('Role Permissions') }}</label>
                            <div id="permissions-display" class="p-3 bg-light rounded">
                                <!-- Permissions will be populated by JavaScript -->
                            </div>
                        </div>

                        <!-- Additional Settings (for admin roles) -->
                        <div id="admin-settings" class="mb-4 d-none">
                            <label class="form-label">{{ __('Administrative Settings') }}</label>
                            <div class="card">
                                <div class="card-body">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="can_manage_users" name="permissions[]" value="manage_users">
                                        <label class="form-check-label" for="can_manage_users">
                                            {{ __('Can manage users') }}
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="can_manage_projects" name="permissions[]" value="manage_projects">
                                        <label class="form-check-label" for="can_manage_projects">
                                            {{ __('Can manage projects') }}
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="can_view_reports" name="permissions[]" value="view_reports">
                                        <label class="form-check-label" for="can_view_reports">
                                            {{ __('Can view system reports') }}
                                        </label>
                                    </div>
                                    <div class="form-check mb-0">
                                        <input class="form-check-input" type="checkbox" id="can_manage_settings" name="permissions[]" value="manage_settings">
                                        <label class="form-check-label" for="can_manage_settings">
                                            {{ __('Can manage system settings') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Teacher Settings -->
                        <div id="teacher-settings" class="mb-4 d-none">
                            <label class="form-label">{{ __('Teaching Settings') }}</label>
                            <div class="card">
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="max_projects" class="form-label">{{ __('Maximum Projects to Supervise') }}</label>
                                            <input type="number" class="form-control" id="max_projects" name="max_projects"
                                                   value="{{ $user->max_projects ?? 5 }}" min="1" max="20">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="department" class="form-label">{{ __('Department') }}</label>
                                            <select class="form-select" id="department" name="department">
                                                <option value="">{{ __('Select department') }}</option>
                                                <option value="Computer Science">{{ __('Computer Science') }}</option>
                                                <option value="Mathematics">{{ __('Mathematics') }}</option>
                                                <option value="Physics">{{ __('Physics') }}</option>
                                                <option value="Biology">{{ __('Biology') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('pfe.admin.users.show', $id) }}" class="btn btn-secondary">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>{{ __('Update Roles') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.role-card {
    cursor: pointer;
    transition: all 0.3s ease;
}

.role-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.role-card.border-success,
.role-card.border-primary,
.role-card.border-danger,
.role-card.border-warning {
    border-width: 3px !important;
}
</style>
@endpush

@push('scripts')
<script>
const rolePermissions = {
    student: [
        '{{ __("View available projects") }}',
        '{{ __("Submit project proposals") }}',
        '{{ __("Join teams") }}',
        '{{ __("Upload deliverables") }}',
        '{{ __("View project progress") }}'
    ],
    teacher: [
        '{{ __("Create and manage projects") }}',
        '{{ __("Supervise student teams") }}',
        '{{ __("Review deliverables") }}',
        '{{ __("Participate in defenses") }}',
        '{{ __("View student progress") }}'
    ],
    admin_pfe: [
        '{{ __("Full system administration") }}',
        '{{ __("Manage all users") }}',
        '{{ __("Configure system settings") }}',
        '{{ __("Generate reports") }}',
        '{{ __("Manage academic calendar") }}',
        '{{ __("Handle conflicts and assignments") }}'
    ],
    chef_master: [
        '{{ __("All administrative privileges") }}',
        '{{ __("System-wide oversight") }}',
        '{{ __("Final decision authority") }}',
        '{{ __("Cross-departmental management") }}',
        '{{ __("Strategic planning access") }}'
    ]
};

function selectRole(role) {
    // Remove active states
    document.querySelectorAll('.role-card').forEach(card => {
        card.classList.remove('border-success', 'border-primary', 'border-danger', 'border-warning');
    });

    // Add active state to selected card
    const selectedCard = document.querySelector(`[onclick="selectRole('${role}')"]`);
    const borderClass = {
        student: 'border-success',
        teacher: 'border-primary',
        admin_pfe: 'border-danger',
        chef_master: 'border-warning'
    };
    selectedCard.classList.add(borderClass[role]);

    // Check the radio button
    document.getElementById(`role_${role}`).checked = true;

    // Update permissions display
    updatePermissionsDisplay(role);

    // Show/hide role-specific settings
    toggleRoleSettings(role);
}

function updatePermissionsDisplay(role) {
    const permissionsDiv = document.getElementById('permissions-display');
    const permissions = rolePermissions[role] || [];

    if (permissions.length > 0) {
        const permissionsList = permissions.map(permission =>
            `<div class="d-flex align-items-center mb-1">
                <i class="fas fa-check text-success me-2"></i>
                <span>${permission}</span>
            </div>`
        ).join('');

        permissionsDiv.innerHTML = `
            <h6 class="mb-2">${'{{ __("This role includes:") }}'}</h6>
            ${permissionsList}
        `;
    } else {
        permissionsDiv.innerHTML = '<p class="text-muted mb-0">${{ __("Select a role to see permissions") }}</p>';
    }
}

function toggleRoleSettings(role) {
    const adminSettings = document.getElementById('admin-settings');
    const teacherSettings = document.getElementById('teacher-settings');

    // Hide all settings
    adminSettings.classList.add('d-none');
    teacherSettings.classList.add('d-none');

    // Show relevant settings
    if (role === 'admin_pfe' || role === 'chef_master') {
        adminSettings.classList.remove('d-none');
    } else if (role === 'teacher') {
        teacherSettings.classList.remove('d-none');
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const checkedRole = document.querySelector('input[name="primary_role"]:checked');
    if (checkedRole) {
        updatePermissionsDisplay(checkedRole.value);
        toggleRoleSettings(checkedRole.value);
    }
});
</script>
@endpush