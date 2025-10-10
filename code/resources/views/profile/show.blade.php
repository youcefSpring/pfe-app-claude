@extends('layouts.pfe-app')

@section('page-title', __('app.my_profile'))

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title mb-2">{{ __('app.my_profile') }}</h4>
                            <p class="card-text mb-0">{{ __('app.view_manage_account_info') }}</p>
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
                        <i class="bi bi-person me-2"></i>{{ __('app.personal_information') }}
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">{{ __('app.full_name') }}</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">{{ __('app.email_address') }}</label>
                                    <input type="email" class="form-control" id="email" value="{{ $user->email }}" disabled>
                                    <small class="form-text text-muted">{{ __('app.email_cannot_change') }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="role" class="form-label">{{ __('app.role') }}</label>
                                    <input type="text" class="form-control" id="role"
                                           value="{{ ucfirst(str_replace('_', ' ', $user->role)) }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="department" class="form-label">{{ __('app.department') }}</label>
                                    <input type="text" class="form-control" id="department"
                                           value="{{ $user->department ?? __('app.not_specified') }}" disabled>
                                </div>
                            </div>
                        </div>

                        @if($user->role === 'student')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="matricule" class="form-label">{{ __('app.matricule') }}</label>
                                        <input type="text" class="form-control" id="matricule"
                                               value="{{ $user->matricule ?? __('app.not_specified') }}" disabled>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="numero_inscription" class="form-label">{{ __('app.numero_inscription') }}</label>
                                        <input type="text" class="form-control" id="numero_inscription"
                                               value="{{ $user->numero_inscription ?? __('app.not_specified') }}" disabled>
                                    </div>
                                </div>
                            </div>

                            @if($user->speciality)
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="speciality" class="form-label">{{ __('app.speciality') }}</label>
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
                                    <label for="phone" class="form-label">{{ __('app.phone') }}</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                           id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="date_naissance" class="form-label">{{ __('app.date_of_birth') }}</label>
                                    <input type="date" class="form-control" id="date_naissance"
                                           value="{{ $user->date_naissance }}" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">{{ __('app.address') }}</label>
                            <textarea class="form-control @error('address') is-invalid @enderror"
                                      id="address" name="address" rows="3">{{ old('address', $user->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>{{ __('app.update_profile') }}
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
                        <i class="bi bi-shield-check me-2"></i>{{ __('app.account_status') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                            <i class="bi bi-check-circle text-success"></i>
                        </div>
                        <div>
                            <div class="fw-medium">{{ __('app.account_active') }}</div>
                            <small class="text-muted">{{ __('app.email_verified') }}</small>
                        </div>
                    </div>

                    <hr>

                    <div class="row text-center">
                        <div class="col-6">
                            <div class="fw-medium">{{ $user->created_at->format('M Y') }}</div>
                            <small class="text-muted">{{ __('app.joined') }}</small>
                        </div>
                        <div class="col-6">
                            <div class="fw-medium">{{ $user->updated_at->diffForHumans() }}</div>
                            <small class="text-muted">{{ __('app.last_updated') }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Change Password -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-key me-2"></i>{{ __('app.security') }}
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.change-password') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="current_password" class="form-label">{{ __('app.current_password') }}</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                   id="current_password" name="current_password" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="new_password" class="form-label">{{ __('app.new_password') }}</label>
                            <input type="password" class="form-control @error('new_password') is-invalid @enderror"
                                   id="new_password" name="new_password" required>
                            @error('new_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="new_password_confirmation" class="form-label">{{ __('app.confirm_new_password') }}</label>
                            <input type="password" class="form-control"
                                   id="new_password_confirmation" name="new_password_confirmation" required>
                        </div>

                        <button type="submit" class="btn btn-warning btn-sm w-100">
                            <i class="bi bi-key me-2"></i>{{ __('app.change_password') }}
                        </button>
                    </form>
                </div>
            </div>

            @if(isset($workflowStatus) && $workflowStatus)
                <!-- Workflow Status -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="bi bi-diagram-3 me-2"></i>{{ __('app.current_status') }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <strong>{{ __('app.phase') }}:</strong> {{ $workflowStatus['current_phase'] ?? __('app.getting_started') }}
                        </div>
                        <div class="mb-2">
                            <strong>{{ __('app.status') }}:</strong>
                            <span class="badge bg-primary">{{ $workflowStatus['status'] ?? __('app.in_progress') }}</span>
                        </div>
                        @if(isset($workflowStatus['next_actions']) && count($workflowStatus['next_actions']) > 0)
                            <div class="mb-2">
                                <strong>{{ __('app.next_actions') }}:</strong>
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