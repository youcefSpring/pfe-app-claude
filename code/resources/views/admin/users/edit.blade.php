@extends('layouts.pfe-app')

@section('page-title', 'Edit User')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Edit User</h4>
                    <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Users
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                           id="email" name="email" value="{{ old('email', $user->email) }}" required>
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
                                        <option value="student" {{ old('role', $user->role) == 'student' ? 'selected' : '' }}>Student</option>
                                        <option value="teacher" {{ old('role', $user->role) == 'teacher' ? 'selected' : '' }}>Teacher</option>
                                        <option value="department_head" {{ old('role', $user->role) == 'department_head' ? 'selected' : '' }}>Department Head</option>
                                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
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
                                           id="matricule" name="matricule" value="{{ old('matricule', $user->matricule) }}">
                                    @error('matricule')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="department" class="form-label">Department</label>
                                    <input type="text" class="form-control @error('department') is-invalid @enderror"
                                           id="department" name="department" value="{{ old('department', $user->department ?: 'Computer Science') }}" readonly>
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
                                                {{ old('speciality_id', $user->speciality_id) == $speciality->id ? 'selected' : '' }}>
                                                {{ $speciality->name }} ({{ ucfirst($speciality->level) }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('speciality_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Additional User Information -->
                        <div class="mb-3">
                            <h6 class="text-primary">Additional Information</h6>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="first_name" class="form-label">First Name</label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                           id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}">
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="last_name" class="form-label">Last Name</label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                           id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}">
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="date_naissance" class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control @error('date_naissance') is-invalid @enderror"
                                           id="date_naissance" name="date_naissance" value="{{ old('date_naissance', $user->date_naissance ? $user->date_naissance->format('Y-m-d') : '') }}">
                                    @error('date_naissance')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="lieu_naissance" class="form-label">Place of Birth</label>
                                    <input type="text" class="form-control @error('lieu_naissance') is-invalid @enderror"
                                           id="lieu_naissance" name="lieu_naissance" value="{{ old('lieu_naissance', $user->lieu_naissance) }}">
                                    @error('lieu_naissance')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="grade" class="form-label">Grade</label>
                                    <input type="text" class="form-control @error('grade') is-invalid @enderror"
                                           id="grade" name="grade" value="{{ old('grade', $user->grade) }}">
                                    @error('grade')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Academic grade for teachers (e.g., Professeur, Maître de Conférences)</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="position" class="form-label">Position</label>
                                    <input type="text" class="form-control @error('position') is-invalid @enderror"
                                           id="position" name="position" value="{{ old('position', $user->position) }}">
                                    @error('position')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Position or title</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <h6 class="text-primary">Change Password (Optional)</h6>
                            <small class="text-muted">Leave blank to keep current password</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">New Password</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                           id="password" name="password">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control"
                                           id="password_confirmation" name="password_confirmation">
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="send_notification" name="send_notification" checked>
                                <label class="form-check-label" for="send_notification">
                                    Send email notification to user about account update
                                </label>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update User
                            </button>
                            <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- User Information Card -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">User Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Created:</strong> {{ $user->created_at->format('M d, Y H:i') }}</p>
                            <p><strong>Last Updated:</strong> {{ $user->updated_at->format('M d, Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Last Login:</strong>
                                @if($user->last_login_at)
                                    {{ $user->last_login_at->format('M d, Y H:i') }}
                                @else
                                    <span class="text-muted">Never</span>
                                @endif
                            </p>
                            <p><strong>Status:</strong>
                                <span class="badge bg-success">Active</span>
                            </p>
                        </div>
                    </div>

                    @if($user->role === 'student' && $user->teamMember)
                        <hr>
                        <h6 class="text-primary">Team Information</h6>
                        <p><strong>Team:</strong> {{ $user->teamMember->team->name }}</p>
                        <p><strong>Role in Team:</strong> {{ ucfirst($user->teamMember->role) }}</p>
                        <p><strong>Joined Team:</strong> {{ $user->teamMember->joined_at?->format('M d, Y') ?? 'N/A' }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection