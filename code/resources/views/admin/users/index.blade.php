@extends('layouts.pfe-app')

@section('page-title', 'User Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">User Management</h4>
                    <div>
                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add User
                        </a>
                        <a href="{{ route('admin.users.bulk-import') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-upload"></i> Bulk Import
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($users->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Department</th>
                                        <th>Speciality</th>
                                        <th>Last Login</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                        <tr>
                                            <td>
                                                <div>
                                                    <h6 class="mb-1">{{ $user->name }}</h6>
                                                    @if($user->matricule)
                                                        <small class="text-muted">{{ $user->matricule }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                @if($user->role === 'student')
                                                    <span class="badge bg-primary">Student</span>
                                                @elseif($user->role === 'teacher')
                                                    <span class="badge bg-success">Teacher</span>
                                                @elseif($user->role === 'department_head')
                                                    <span class="badge bg-warning">Dept. Head</span>
                                                @elseif($user->role === 'admin')
                                                    <span class="badge bg-danger">Admin</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($user->role) }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $user->department ?? '-' }}</td>
                                            <td>
                                                @if($user->speciality)
                                                    {{ $user->speciality->name }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if($user->last_login_at)
                                                    <div>
                                                        <div>{{ $user->last_login_at->format('M d, Y') }}</div>
                                                        <small class="text-muted">{{ $user->last_login_at->diffForHumans() }}</small>
                                                    </div>
                                                @else
                                                    <span class="text-muted">Never</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.users.edit', $user) }}"
                                                       class="btn btn-outline-primary btn-sm" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @if($user->id !== auth()->id())
                                                        <button type="button" class="btn btn-outline-danger btn-sm"
                                                                onclick="deleteUser({{ $user->id }}, '{{ $user->name }}')" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{ $users->links() }}
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Users Found</h5>
                            <p class="text-muted">Start by adding users to the system.</p>
                            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add First User
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete User Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h6>⚠️ This action cannot be undone!</h6>
                    <p class="mb-0">Deleting this user will remove all their data and cannot be reversed.</p>
                </div>
                <p>Are you sure you want to delete user <strong id="deleteUserName"></strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteUserForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Delete User
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function deleteUser(userId, userName) {
    document.getElementById('deleteUserName').textContent = userName;
    document.getElementById('deleteUserForm').action = `/admin/users/${userId}`;

    const modal = new bootstrap.Modal(document.getElementById('deleteUserModal'));
    modal.show();
}
</script>
@endpush