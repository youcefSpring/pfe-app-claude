@extends('layouts.pfe-app')

@section('page-title', 'My Profile')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title mb-2">My Profile</h4>
                            <p class="card-text mb-0">View and manage your account information</p>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-person-circle" style="font-size: 3rem; opacity: 0.7;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Personal Information -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person me-2"></i>Personal Information
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" value="{{ $user->email }}" disabled>
                                    <small class="form-text text-muted">Email cannot be changed. Contact admin if needed.</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="role" class="form-label">Role</label>
                                    <input type="text" class="form-control" id="role"
                                           value="{{ ucfirst(str_replace('_', ' ', $user->role)) }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="department" class="form-label">Department</label>
                                    <input type="text" class="form-control" id="department"
                                           value="{{ $user->department ?? 'Not specified' }}" disabled>
                                </div>
                            </div>
                        </div>

                        @if($user->role === 'student')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="matricule" class="form-label">Matricule</label>
                                        <input type="text" class="form-control" id="matricule"
                                               value="{{ $user->matricule ?? 'Not specified' }}" disabled>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="numero_inscription" class="form-label">Numero Inscription</label>
                                        <input type="text" class="form-control" id="numero_inscription"
                                               value="{{ $user->numero_inscription ?? 'Not specified' }}" disabled>
                                    </div>
                                </div>
                            </div>

                            @if($user->speciality)
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="speciality" class="form-label">Speciality</label>
                                            <input type="text" class="form-control" id="speciality"
                                                   value="{{ $user->speciality->full_name }}" disabled>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                           id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="date_naissance" class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control" id="date_naissance"
                                           value="{{ $user->date_naissance }}" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror"
                                      id="address" name="address" rows="3">{{ old('address', $user->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Account Information & Quick Actions -->
        <div class="col-md-4">
            <!-- Account Status -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-shield-check me-2"></i>Account Status
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                            <i class="bi bi-check-circle text-success"></i>
                        </div>
                        <div>
                            <div class="fw-medium">Account Active</div>
                            <small class="text-muted">Email verified</small>
                        </div>
                    </div>

                    <hr>

                    <div class="row text-center">
                        <div class="col-6">
                            <div class="fw-medium">{{ $user->created_at->format('M Y') }}</div>
                            <small class="text-muted">Joined</small>
                        </div>
                        <div class="col-6">
                            <div class="fw-medium">{{ $user->updated_at->diffForHumans() }}</div>
                            <small class="text-muted">Last Updated</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Change Password -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-key me-2"></i>Security
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.change-password') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                   id="current_password" name="current_password" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control @error('new_password') is-invalid @enderror"
                                   id="new_password" name="new_password" required>
                            @error('new_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control"
                                   id="new_password_confirmation" name="new_password_confirmation" required>
                        </div>

                        <button type="submit" class="btn btn-warning btn-sm w-100">
                            <i class="bi bi-key me-2"></i>Change Password
                        </button>
                    </form>
                </div>
            </div>

            @if(isset($workflowStatus) && $workflowStatus)
                <!-- Workflow Status -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="bi bi-diagram-3 me-2"></i>Current Status
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <strong>Phase:</strong> {{ $workflowStatus['current_phase'] ?? 'Getting Started' }}
                        </div>
                        <div class="mb-2">
                            <strong>Status:</strong>
                            <span class="badge bg-primary">{{ $workflowStatus['status'] ?? 'In Progress' }}</span>
                        </div>
                        @if(isset($workflowStatus['next_actions']) && count($workflowStatus['next_actions']) > 0)
                            <div class="mb-2">
                                <strong>Next Actions:</strong>
                                <ul class="mb-0 mt-1">
                                    @foreach(array_slice($workflowStatus['next_actions'], 0, 3) as $action)
                                        <li><small>{{ $action }}</small></li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection