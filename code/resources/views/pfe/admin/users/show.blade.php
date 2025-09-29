@extends('layouts.admin')

@section('title', __('User Details'))
@section('page-title', __('User Details'))

@section('breadcrumbs')
<span class="text-muted">{{ __('Home') }} / {{ __('Administration') }} / <a href="{{ route('pfe.admin.users.index') }}">{{ __('Users') }}</a> / {{ __('Details') }}</span>
@endsection

@section('content')
<div class="container-fluid p-4">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center">
            <a href="{{ route('pfe.admin.users.index') }}" class="btn btn-outline-secondary me-3">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="h3 mb-0">{{ $user->name ?? 'Sample User' }}</h1>
                <p class="text-muted mb-0">{{ __('User profile and information') }}</p>
            </div>
        </div>
        <div class="btn-group">
            <a href="{{ route('pfe.admin.users.edit', $id) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>{{ __('Edit User') }}
            </a>
            <a href="{{ route('pfe.admin.users.roles', $id) }}" class="btn btn-outline-warning">
                <i class="fas fa-user-tag me-2"></i>{{ __('Manage Roles') }}
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- User Profile Card -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    <img src="{{ $user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name ?? 'Sample User') }}"
                         alt="{{ $user->name ?? 'Sample User' }}" class="rounded-circle mb-3" style="width: 120px; height: 120px;">

                    <h4 class="mb-1">{{ $user->name ?? 'Sample User' }}</h4>
                    <p class="text-muted mb-3">{{ $user->email ?? 'user@example.com' }}</p>

                    <div class="mb-3">
                        <span class="badge bg-{{ ($user->role ?? 'student') == 'admin_pfe' ? 'danger' : (($user->role ?? 'student') == 'teacher' ? 'primary' : 'success') }} px-3 py-2">
                            {{ __(ucfirst($user->role ?? 'student')) }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <span class="badge bg-{{ ($user->status ?? 'active') == 'active' ? 'success' : 'secondary' }}">
                            <i class="fas fa-circle me-1" style="font-size: 0.7rem;"></i>
                            {{ __(ucfirst($user->status ?? 'active')) }}
                        </span>
                    </div>

                    @if(($user->status ?? 'active') == 'active')
                    <button class="btn btn-outline-danger btn-sm" onclick="toggleUserStatus()">
                        <i class="fas fa-user-slash me-1"></i>{{ __('Deactivate') }}
                    </button>
                    @else
                    <button class="btn btn-outline-success btn-sm" onclick="toggleUserStatus()">
                        <i class="fas fa-user-check me-1"></i>{{ __('Activate') }}
                    </button>
                    @endif
                </div>
            </div>

            <!-- Contact Information -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">{{ __('Contact Information') }}</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted small">{{ __('Email') }}</label>
                        <p class="mb-0">{{ $user->email ?? 'user@example.com' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small">{{ __('Phone') }}</label>
                        <p class="mb-0">{{ $user->phone ?? __('Not provided') }}</p>
                    </div>
                    <div class="mb-0">
                        <label class="form-label text-muted small">{{ __('Registration Date') }}</label>
                        <p class="mb-0">{{ $user->created_at ? $user->created_at->format('F d, Y') : 'January 15, 2024' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Account Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Account Information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small">{{ __('User ID') }}</label>
                            <p class="mb-0">#{{ $user->id ?? '1001' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">{{ __('Username') }}</label>
                            <p class="mb-0">{{ $user->username ?? strtolower(str_replace(' ', '.', $user->name ?? 'sample.user')) }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">{{ __('Last Login') }}</label>
                            <p class="mb-0">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : __('Never logged in') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">{{ __('Email Verified') }}</label>
                            <p class="mb-0">
                                @if($user->email_verified_at ?? true)
                                <span class="badge bg-success">{{ __('Verified') }}</span>
                                @else
                                <span class="badge bg-warning">{{ __('Pending') }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Role-Specific Information -->
            @if(($user->role ?? 'student') == 'student')
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Student Information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small">{{ __('Student ID') }}</label>
                            <p class="mb-0">{{ $user->student_id ?? '202400001' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">{{ __('Academic Level') }}</label>
                            <p class="mb-0">{{ $user->level ?? 'M2' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">{{ __('Specialty') }}</label>
                            <p class="mb-0">{{ $user->specialty ?? 'Computer Science' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">{{ __('Academic Year') }}</label>
                            <p class="mb-0">{{ $user->academic_year ?? '2024-2025' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student's Team Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Team Information') }}</h5>
                </div>
                <div class="card-body">
                    @if($user->team ?? null)
                    <div class="d-flex align-items-center">
                        <i class="fas fa-users fa-2x text-primary me-3"></i>
                        <div>
                            <h6 class="mb-1">{{ $user->team->name ?? 'Team Alpha' }}</h6>
                            <p class="text-muted mb-0">{{ $user->team->members_count ?? 3 }} {{ __('members') }}</p>
                        </div>
                        <a href="#" class="btn btn-sm btn-outline-primary ms-auto">{{ __('View Team') }}</a>
                    </div>
                    @else
                    <div class="text-center py-3">
                        <i class="fas fa-user-friends text-muted mb-2" style="font-size: 2rem;"></i>
                        <p class="text-muted mb-0">{{ __('Not assigned to any team') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            @elseif(($user->role ?? 'student') == 'teacher')
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Teacher Information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small">{{ __('Department') }}</label>
                            <p class="mb-0">{{ $user->department ?? 'Computer Science' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">{{ __('Academic Title') }}</label>
                            <p class="mb-0">{{ $user->title ?? 'Dr.' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">{{ __('Supervised Projects') }}</label>
                            <p class="mb-0">{{ $user->supervised_projects_count ?? 5 }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">{{ __('Available Slots') }}</label>
                            <p class="mb-0">{{ $user->available_slots ?? 3 }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Activity Timeline -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Recent Activity') }}</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @forelse($activities ?? [] as $activity)
                        <div class="d-flex mb-3">
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                                 style="width: 40px; height: 40px; min-width: 40px;">
                                <i class="fas fa-{{ $activity->icon ?? 'user' }} text-white"></i>
                            </div>
                            <div class="flex-fill">
                                <p class="mb-1">{{ $activity->description ?? 'User logged in' }}</p>
                                <small class="text-muted">{{ $activity->created_at ? $activity->created_at->diffForHumans() : '2 hours ago' }}</small>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-3">
                            <i class="fas fa-history text-muted mb-2" style="font-size: 2rem;"></i>
                            <p class="text-muted mb-0">{{ __('No recent activity') }}</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleUserStatus() {
    const currentStatus = '{{ $user->status ?? "active" }}';
    const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
    const action = newStatus === 'active' ? 'activate' : 'deactivate';

    if (confirm(`{{ __("Are you sure you want to") }} ${action} {{ __("this user?") }}`)) {
        // Here you would make an AJAX call to toggle status
        fetch(`/pfe/admin/users/{{ $id }}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ status: newStatus })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('{{ __("Error updating user status") }}');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('{{ __("Error updating user status") }}');
        });
    }
}
</script>
@endpush