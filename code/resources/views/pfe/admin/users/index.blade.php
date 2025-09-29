@extends('layouts.admin')

@section('title', __('Users Management'))
@section('page-title', __('Users Management'))

@section('breadcrumbs')
<span class="text-muted">{{ __('Home') }} / {{ __('Administration') }} / {{ __('Users') }}</span>
@endsection

@section('content')
<div class="container-fluid p-4">
    <!-- Header Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">{{ __('Users Management') }}</h1>
            <p class="text-muted">{{ __('Manage system users, roles and permissions') }}</p>
        </div>
        <div>
            <a href="{{ route('pfe.admin.users.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>{{ __('Add User') }}
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-users fa-2x me-3"></i>
                        <div>
                            <h4 class="mb-0">{{ $stats['total_users'] ?? 156 }}</h4>
                            <small>{{ __('Total Users') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-graduation-cap fa-2x me-3"></i>
                        <div>
                            <h4 class="mb-0">{{ $stats['students'] ?? 120 }}</h4>
                            <small>{{ __('Students') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-chalkboard-teacher fa-2x me-3"></i>
                        <div>
                            <h4 class="mb-0">{{ $stats['teachers'] ?? 28 }}</h4>
                            <small>{{ __('Teachers') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-user-shield fa-2x me-3"></i>
                        <div>
                            <h4 class="mb-0">{{ $stats['admins'] ?? 8 }}</h4>
                            <small>{{ __('Administrators') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('pfe.admin.users.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="search" class="form-label">{{ __('Search') }}</label>
                        <input type="text" class="form-control" id="search" name="search"
                               value="{{ request('search') }}" placeholder="{{ __('Search by name or email...') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="role" class="form-label">{{ __('Role') }}</label>
                        <select class="form-select" id="role" name="role">
                            <option value="">{{ __('All Roles') }}</option>
                            <option value="student" {{ request('role') == 'student' ? 'selected' : '' }}>{{ __('Student') }}</option>
                            <option value="teacher" {{ request('role') == 'teacher' ? 'selected' : '' }}>{{ __('Teacher') }}</option>
                            <option value="admin_pfe" {{ request('role') == 'admin_pfe' ? 'selected' : '' }}>{{ __('PFE Admin') }}</option>
                            <option value="chef_master" {{ request('role') == 'chef_master' ? 'selected' : '' }}>{{ __('Chef Master') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">{{ __('Status') }}</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">{{ __('All Status') }}</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-search me-1"></i>{{ __('Filter') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('Users List') }}</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('User') }}</th>
                            <th>{{ __('Role') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Last Login') }}</th>
                            <th>{{ __('Created') }}</th>
                            <th class="text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users ?? [] as $user)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name ?? 'User') }}"
                                         alt="{{ $user->name ?? 'User' }}" class="rounded-circle me-3" style="width: 40px; height: 40px;">
                                    <div>
                                        <h6 class="mb-0">{{ $user->name ?? 'Sample User' }}</h6>
                                        <small class="text-muted">{{ $user->email ?? 'user@example.com' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $user->role == 'admin_pfe' ? 'danger' : ($user->role == 'teacher' ? 'primary' : 'success') }}">
                                    {{ __(ucfirst($user->role ?? 'student')) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ ($user->status ?? 'active') == 'active' ? 'success' : 'secondary' }}">
                                    {{ __(ucfirst($user->status ?? 'active')) }}
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : __('Never') }}
                                </small>
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $user->created_at ? $user->created_at->format('M d, Y') : 'Jan 1, 2024' }}
                                </small>
                            </td>
                            <td class="text-end">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('pfe.admin.users.show', $user->id ?? 1) }}"
                                       class="btn btn-sm btn-outline-info" title="{{ __('View') }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('pfe.admin.users.edit', $user->id ?? 1) }}"
                                       class="btn btn-sm btn-outline-primary" title="{{ __('Edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('pfe.admin.users.roles', $user->id ?? 1) }}"
                                       class="btn btn-sm btn-outline-warning" title="{{ __('Roles') }}">
                                        <i class="fas fa-user-tag"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                            title="{{ __('Delete') }}" onclick="confirmDelete({{ $user->id ?? 1 }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <i class="fas fa-users text-muted mb-3" style="font-size: 3rem;"></i>
                                <p class="text-muted">{{ __('No users found') }}</p>
                                <a href="{{ route('pfe.admin.users.create') }}" class="btn btn-primary">
                                    {{ __('Add First User') }}
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if(isset($users) && method_exists($users, 'links'))
        <div class="card-footer">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(userId) {
    if (confirm('{{ __("Are you sure you want to delete this user?") }}')) {
        // Create and submit a delete form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/pfe/admin/users/${userId}`;

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';

        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';

        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush