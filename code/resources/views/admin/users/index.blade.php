@extends('layouts.pfe-app')

@section('page-title', __('app.user_management'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">{{ __('app.user_management') }}</h4>
                        <div>
                            <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> {{ __('app.add_user') }}
                            </a>
                            <a href="{{ route('admin.users.bulk-import') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-upload"></i> {{ __('app.bulk_import') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($users->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>{{ __('app.name') }}</th>
                                            <th>{{ __('app.email') }}</th>
                                            <th>{{ __('app.role') }}</th>
                                            {{-- <th>{{ __('app.department') }}</th> --}}
                                            <th>{{ __('app.speciality') }}</th>
                                            {{-- <th>{{ __('app.section_group') }}</th> --}}
                                            <th>{{ __('app.last_login') }}</th>
                                            <th>{{ __('app.actions') }}</th>
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
                                                        <span class="badge bg-primary">{{ __('app.student') }}</span>
                                                    @elseif($user->role === 'teacher')
                                                        <span class="badge bg-success">{{ __('app.teacher') }}</span>
                                                    @elseif($user->role === 'department_head')
                                                        <span class="badge bg-warning">{{ __('app.dept_head') }}</span>
                                                    @elseif($user->role === 'admin')
                                                        <span class="badge bg-danger">{{ __('app.admin') }}</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ ucfirst($user->role) }}</span>
                                                    @endif
                                                </td>
                                                {{-- <td>{{ $user->department ?? '-' }}</td> --}}
                                                <td>
                                                    @if($user->speciality)
                                                        {{ $user->speciality?->name ?? '-' }}
                                                    @else
                                                        {{ $user->speciality }}
                                                    @endif
                                                </td>
                                                {{-- <td>
                                                    <div>
                                                        @if($user->section || $user->groupe)
                                                            <small class="text-muted">
                                                                @if($user->section){{ __('app.section') }}: {{ $user->section }}@endif
                                                                @if($user->section && $user->groupe) | @endif
                                                                @if($user->groupe){{ __('app.group') }}: {{ $user->groupe }}@endif
                                                            </small>
                                                        @else
                                                            -
                                                        @endif
                                                    </div>
                                                </td> --}}
                                                <td>
                                                    @if($user->last_login_at)
                                                        <div>
                                                            <div>{{ $user->last_login_at->format('M d, Y') }}</div>
                                                            <small class="text-muted">{{ $user->last_login_at->diffForHumans() }}</small>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">{{ __('app.never') }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('admin.users.edit', $user) }}"
                                                           class="btn btn-outline-primary btn-sm" title="{{ __('app.edit') }}">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        @if($user->id !== auth()->id())
                                                            <button type="button" class="btn btn-outline-danger btn-sm"
                                                                    onclick="deleteUser({{ $user->id }}, '{{ $user->name }}')" title="{{ __('app.delete') }}">
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

                            <div class="d-flex justify-content-center mt-4">
                                <nav aria-label="Users pagination">
                                    {{ $users->links('pagination::bootstrap-4') }}
                                </nav>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">{{ __('app.no_users_found') }}</h5>
                                <p class="text-muted">{{ __('app.start_by_adding_users') }}</p>
                                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> {{ __('app.add_first_user') }}
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
                    <h5 class="modal-title">{{ __('app.delete_user') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <h6>⚠️ {{ __('app.action_cannot_be_undone') }}</h6>
                        <p class="mb-0">{{ __('app.deleting_user_warning') }}</p>
                    </div>
                    <p>{{ __('app.confirm_delete_user') }} <strong id="deleteUserName"></strong>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('app.cancel') }}</button>
                    <form id="deleteUserForm" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> {{ __('app.delete_user') }}
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
