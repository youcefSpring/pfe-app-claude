@extends('layouts.pfe-app')

@section('page-title', __('app.edit_team'))

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">{{ __('app.edit_team') }}</h4>
                    <span class="badge bg-{{ $team->status === 'active' ? 'success' : ($team->status === 'forming' ? 'warning' : 'secondary') }}">
                        {{ ucfirst($team->status) }}
                    </span>
                </div>
                <div class="card-body">
                    <form action="{{ route('teams.update', $team) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="name" class="form-label">Team Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name', $team->name) }}"
                                   placeholder="{{ __('app.enter_team_name') }}">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Team name must be unique across all teams.
                            </small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Current Team Status</h6>
                                        <div class="mb-2">
                                            <small class="text-muted">Status:</small>
                                            <span class="badge bg-{{ $team->status === 'active' ? 'success' : ($team->status === 'forming' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($team->status) }}
                                            </span>
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-muted">Members:</small> {{ $team->members->count() }}/4
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-muted">Created:</small> {{ $team->created_at->format('M d, Y') }}
                                        </div>
                                        @if($team->project)
                                            <div class="mb-2">
                                                <small class="text-muted">Project:</small> {{ $team->project->subject->title }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Team Members</h6>
                                        @foreach($team->members as $member)
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <div>
                                                    <div class="fw-bold">{{ $member->user->name }}</div>
                                                    <small class="text-muted">
                                                        {{ $member->role === 'leader' ? 'Team Leader' : 'Member' }}
                                                    </small>
                                                </div>
                                                @if($member->role === 'leader')
                                                    <span class="badge bg-primary">Leader</span>
                                                @else
                                                    <span class="badge bg-secondary">Member</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($team->project)
                            <div class="alert alert-info mt-3">
                                <h6><i class="fas fa-info-circle"></i> Project Assigned</h6>
                                <p class="mb-2">This team has been assigned to work on: <strong>{{ $team->project->subject->title }}</strong></p>
                                <p class="mb-0">Some team settings may be restricted to maintain project continuity.</p>
                            </div>
                        @endif

                        <div class="mt-4">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('teams.show', $team) }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to Team
                                </a>
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Team
                                    </button>
                                    @if(!$team->project && $team->members->count() === 1)
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteTeamModal">
                                            <i class="fas fa-trash"></i> Delete Team
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Team Modal -->
@if(!$team->project && $team->members->count() === 1)
<div class="modal fade" id="deleteTeamModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Team</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h6>⚠️ This action cannot be undone!</h6>
                    <p class="mb-0">
                        Deleting the team will remove all members and cannot be reversed.
                        All team members will need to join or create new teams.
                    </p>
                </div>
                <p>Are you sure you want to delete <strong>{{ $team->name }}</strong>?</p>

                <div class="mb-3">
                    <label for="confirmTeamName" class="form-label">
                        Type the team name to confirm deletion:
                    </label>
                    <input type="text" class="form-control" id="confirmTeamName"
                           placeholder="{{ $team->name }}" onkeyup="checkTeamName()">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('teams.destroy', $team) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" id="confirmDeleteBtn" disabled>
                        <i class="fas fa-trash"></i> Delete Team
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
function checkTeamName() {
    const input = document.getElementById('confirmTeamName');
    const deleteBtn = document.getElementById('confirmDeleteBtn');
    const teamName = '{{ $team->name }}';

    if (input.value === teamName) {
        deleteBtn.disabled = false;
        deleteBtn.classList.remove('btn-secondary');
        deleteBtn.classList.add('btn-danger');
    } else {
        deleteBtn.disabled = true;
        deleteBtn.classList.remove('btn-danger');
        deleteBtn.classList.add('btn-secondary');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Reset confirmation when modal is closed
    const modal = document.getElementById('deleteTeamModal');
    if (modal) {
        modal.addEventListener('hidden.bs.modal', function () {
            document.getElementById('confirmTeamName').value = '';
            checkTeamName();
        });
    }
});
</script>
@endpush