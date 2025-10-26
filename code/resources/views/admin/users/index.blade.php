@extends('layouts.pfe-app')

@section('page-title', __('app.user_management'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="btn-group" role="group">
                            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                                <i class="bi bi-person-plus me-2"></i>{{ __('app.add_user') }}
                            </a>
                            <a href="{{ route('admin.users.bulk-import') }}" class="btn btn-success">
                                <i class="bi bi-upload me-2"></i>{{ __('app.bulk_import') }}
                            </a>
                        </div>
                    </div>

                    <!-- Search and Filter Section -->
                    <div class="card-body border-bottom">
                        <form method="GET" action="{{ route('admin.users') }}" id="filterForm">
                            <div class="row g-3">
                                <!-- Real-time Search -->
                                <div class="col-md-4">
                                    <label for="search" class="form-label">{{ __('app.search') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                                        <input type="text"
                                               class="form-control"
                                               id="search"
                                               name="search"
                                               value="{{ request('search') }}"
                                               placeholder="{{ __('app.search_users_placeholder') }}">
                                    </div>
                                </div>

                                <!-- Role Filter -->
                                <div class="col-md-3">
                                    <label for="role" class="form-label">{{ __('app.role') }}</label>
                                    <select class="form-select" id="role" name="role">
                                        <option value="all" {{ $selectedRole === 'all' ? 'selected' : '' }}>
                                            {{ __('app.all_roles') }} ({{ $roleCounts['all'] }})
                                        </option>
                                        <option value="student" {{ $selectedRole === 'student' ? 'selected' : '' }}>
                                            {{ __('app.students') }} ({{ $roleCounts['student'] }})
                                        </option>
                                        <option value="teacher" {{ $selectedRole === 'teacher' ? 'selected' : '' }}>
                                            {{ __('app.teachers') }} ({{ $roleCounts['teacher'] }})
                                        </option>
                                        <option value="department_head" {{ $selectedRole === 'department_head' ? 'selected' : '' }}>
                                            {{ __('app.department_heads') }} ({{ $roleCounts['department_head'] }})
                                        </option>
                                        <option value="admin" {{ $selectedRole === 'admin' ? 'selected' : '' }}>
                                            {{ __('app.admins') }} ({{ $roleCounts['admin'] }})
                                        </option>
                                    </select>
                                </div>

                                <!-- Speciality Filter -->
                                <div class="col-md-3">
                                    <label for="speciality_id" class="form-label">{{ __('app.speciality') }}</label>
                                    <select class="form-select" id="speciality_id" name="speciality_id">
                                        <option value="">{{ __('app.all_specialities') }}</option>
                                        @foreach($specialities as $speciality)
                                            <option value="{{ $speciality->id }}"
                                                    {{ request('speciality_id') == $speciality->id ? 'selected' : '' }}>
                                                {{ $speciality->name }} ({{ $speciality->level }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Clear Button -->
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-grid">
                                        <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">
                                            <i class="bi bi-arrow-clockwise me-1"></i>{{ __('app.clear') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="card-body">
                        @if($users->count() > 0)
                                                    <!-- Results Summary -->
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <div class="text-muted">
                                                            {{ __('app.showing_results', [
                                'from' => $users->firstItem() ?? 0,
                                'to' => $users->lastItem() ?? 0,
                                'total' => $users->total()
                            ]) }}
                                                        </div>
                                                        <div class="text-muted">
                                                            {{ __('app.per_page') }}: {{ $users->perPage() }}
                                                        </div>
                                                    </div>

                                                    <!-- Users Table -->
                                                    <div class="table-responsive">
                                                        <table class="table table-hover">
                                                            <thead>
                                                                <tr>
                                                                    <th>{{ __('app.name') }}</th>
                                                                    <th>{{ __('app.email') }}</th>
                                                                    <th>{{ __('app.role') }}</th>
                                                                    <th>{{ __('app.speciality') }}</th>
                                                                    <th>{{ __('app.created_at') }}</th>
                                                                    <th>{{ __('app.actions') }}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($users as $user)
                                                                    <tr>
                                                                        <td>
                                                                            <div class="d-flex align-items-center">
                                                                                <div class="avatar-circle bg-primary text-white me-3">
                                                                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                                                                </div>
                                                                                <div>
                                                                                    <h6 class="mb-1">{{ $user->name }}</h6>
                                                                                    @if($user->matricule)
                                                                                        <small class="text-muted">{{ $user->matricule }}</small>
                                                                                    @endif
                                                                                    @if($user->first_name && $user->last_name)
                                                                                        <br><small class="text-info">{{ $user->first_name }} {{ $user->last_name }}</small>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div>
                                                                                {{ $user->email }}
                                                                                @if($user->email_verified_at)
                                                                                    <i class="bi bi-check-circle-fill text-success ms-1" title="{{ __('app.verified') }}"></i>
                                                                                @else
                                                                                    <i class="bi bi-exclamation-circle-fill text-warning ms-1" title="{{ __('app.unverified') }}"></i>
                                                                                @endif
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            @if($user->role === 'student')
                                                                                <span class="badge bg-primary">{{ __('app.student') }}</span>
                                                                            @elseif($user->role === 'teacher')
                                                                                <span class="badge bg-success">{{ __('app.teacher') }}</span>
                                                                            @elseif($user->role === 'department_head')
                                                                                <span class="badge bg-warning">{{ __('app.department_head') }}</span>
                                                                            @elseif($user->role === 'admin')
                                                                                <span class="badge bg-danger">{{ __('app.admin') }}</span>
                                                                            @else
                                                                                <span class="badge bg-secondary">{{ ucfirst($user->role) }}</span>
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                        <small class="text-muted">
                                                                           {{ \App\Models\Speciality::where('id',$user->speciality_id)->first()->code ?? '-' }}
                                                                        </small>
                                                                        </td>
                                                                        <td>
                                                                            <small class="text-muted">
                                                                                {{ $user->created_at->format('d/m/Y') }}<br>
                                                                                {{ $user->created_at->format('H:i') }}
                                                                            </small>
                                                                        </td>
                                                                        <td>
                                                                            <div class="btn-group" role="group">
                                                                                <a href="{{ route('admin.users.details', $user) }}"
                                                                                   class="btn btn-sm btn-outline-info"
                                                                                   title="{{ __('app.see_details') }}">
                                                                                    <i class="bi bi-eye"></i>
                                                                                </a>
                                                                                <a href="{{ route('admin.users.edit', $user) }}"
                                                                                   class="btn btn-sm btn-outline-primary"
                                                                                   title="{{ __('app.edit') }}">
                                                                                    <i class="bi bi-pencil"></i>
                                                                                </a>
                                                                                @if($user->id !== auth()->id())
                                                                                    <form action="{{ route('admin.users.destroy', $user) }}"
                                                                                          method="POST"
                                                                                          class="d-inline"
                                                                                          onsubmit="return confirm('{{ __('app.confirm_delete_user') }}')">
                                                                                        @csrf
                                                                                        @method('DELETE')
                                                                                        <button type="submit"
                                                                                                class="btn btn-sm btn-outline-danger"
                                                                                                title="{{ __('app.delete') }}">
                                                                                            <i class="bi bi-trash"></i>
                                                                                        </button>
                                                                                    </form>
                                                                                @endif
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                    <!-- Pagination -->
                                                    <x-admin-pagination :paginator="$users" />
                        @else
                            <div class="text-center py-5">
                                <i class="bi bi-people display-1 text-muted"></i>
                                <h4 class="mt-3">{{ __('app.no_users_found') }}</h4>
                                @if(request()->hasAny(['search', 'role', 'speciality_id']))
                                    <p class="text-muted">{{ __('app.try_different_filters') }}</p>
                                    <a href="{{ route('admin.users') }}" class="btn btn-primary">
                                        {{ __('app.show_all_users') }}
                                    </a>
                                @else
                                    <p class="text-muted">{{ __('app.no_users_yet') }}</p>
                                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                                        <i class="bi bi-person-plus me-2"></i>{{ __('app.add_first_user') }}
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .avatar-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
        }

        .table tbody tr:hover {
            background-color: var(--bs-gray-50);
        }

        .btn-group .btn {
            border-radius: 0.375rem !important;
        }

        .btn-group .btn + .btn {
            margin-left: 0.25rem;
        }

        #search {
            transition: all 0.3s ease;
        }

        #search:focus {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .form-select:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .pagination .page-link {
            color: #0d6efd;
        }

        .pagination .page-item.active .page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
    </style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search');
    const roleSelect = document.getElementById('role');
    const specialitySelect = document.getElementById('speciality_id');
    const form = document.getElementById('filterForm');

    let searchTimeout;

    // Real-time search
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            form.submit();
        }, 500); // Wait 500ms after user stops typing
    });

    // Instant filtering on dropdown changes
    roleSelect.addEventListener('change', function() {
        form.submit();
    });

    specialitySelect.addEventListener('change', function() {
        form.submit();
    });

    // Show loading state during search
    form.addEventListener('submit', function() {
        const submitBtn = document.querySelector('#filterForm button[type="submit"]');
        if (submitBtn) {
            submitBtn.innerHTML = '<i class="bi bi-arrow-repeat spin me-1"></i>{{ __('app.searching') }}...';
            submitBtn.disabled = true;
        }
    });

    // Add spinning animation for loading
    const style = document.createElement('style');
    style.textContent = `
        .spin {
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    `;
    document.head.appendChild(style);
});
</script>
@endpush
