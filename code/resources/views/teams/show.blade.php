@extends('layouts.pfe-app')

@section('page-title', 'Team Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">{{ $team->name }}</h4>
                    <div>
                        <span class="badge bg-{{ $team->status === 'active' ? 'success' : ($team->status === 'forming' ? 'warning' : 'secondary') }}">
                            {{ ucfirst($team->status) }}
                        </span>
                        @if($isLeader)
                            <a href="{{ route('teams.edit', $team) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Edit Team
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <!-- Team Members -->
                            <div class="mb-4">
                                <h5>Team Members ({{ $team->members->count() }}/4)</h5>
                                <div class="row">
                                    @foreach($team->members as $member)
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-2 {{ $member->role === 'leader' ? 'border-primary' : 'border-light' }}">
                                                <div class="card-body p-3">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <h6 class="mb-1">{{ $member->user->name }}</h6>
                                                            <small class="text-muted">{{ $member->user->email }}</small>
                                                            <div>
                                                                <span class="badge bg-{{ $member->role === 'leader' ? 'primary' : 'secondary' }}">
                                                                    {{ ucfirst($member->role) }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        @if($isLeader && $member->role !== 'leader')
                                                            <div class="dropdown">
                                                                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                                                    <i class="fas fa-ellipsis-v"></i>
                                                                </button>
                                                                <ul class="dropdown-menu">
                                                                    <li>
                                                                        <button class="dropdown-item" onclick="transferLeadership({{ $member->user->id }}, '{{ $member->user->name }}')">
                                                                            Make Leader
                                                                        </button>
                                                                    </li>
                                                                    <li><hr class="dropdown-divider"></li>
                                                                    <li>
                                                                        <form action="{{ route('teams.remove-member', [$team, $member]) }}" method="POST" class="d-inline">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit" class="dropdown-item text-danger"
                                                                                    onclick="return confirm('Remove this member from the team?')">
                                                                                Remove from Team
                                                                            </button>
                                                                        </form>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <small class="text-muted">
                                                        Joined {{ $member->joined_at->format('M d, Y') }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                @if($isLeader && $team->members->count() < 4)
                                    <div class="mt-3">
                                        <h6>Add Team Member</h6>
                                        <form action="{{ route('teams.add-member', $team) }}" method="POST" class="row g-2">
                                            @csrf
                                            <div class="col-md-8">
                                                <input type="email" class="form-control" name="student_email"
                                                       placeholder="Enter student email address" required>
                                            </div>
                                            <div class="col-md-4">
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fas fa-plus"></i> Add Member
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                            </div>

                            <!-- Project Information -->
                            @if($team->project)
                                <div class="mb-4">
                                    <h5>Project</h5>
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $team->project->subject->title }}</h6>
                                            <p class="card-text">{{ $team->project->subject->description }}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small class="text-muted">Supervisor:</small>
                                                    <div>{{ $team->project->supervisor->name ?? 'Not assigned' }}</div>
                                                </div>
                                                <a href="{{ route('projects.show', $team->project) }}" class="btn btn-primary btn-sm">
                                                    View Project
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="mb-4">
                                    <h5>Subject Selection</h5>
                                    @if($team->status === 'forming')
                                        <div class="alert alert-info">
                                            Complete team formation before selecting a subject.
                                        </div>
                                    @elseif($isLeader)
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="d-flex gap-2 mb-3">
                                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#selectSubjectModal">
                                                        <i class="fas fa-book"></i> Select from Available Subjects
                                                    </button>
                                                    <a href="{{ route('teams.external-project-form', $team) }}" class="btn btn-outline-primary">
                                                        <i class="fas fa-external-link-alt"></i> Submit External Project
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="alert alert-warning">
                                            Only the team leader can select a subject.
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Team Information</h6>

                                    <div class="mb-3">
                                        <small class="text-muted">Status</small>
                                        <div>
                                            <span class="badge bg-{{ $team->status === 'active' ? 'success' : ($team->status === 'forming' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($team->status) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <small class="text-muted">Created</small>
                                        <div>{{ $team->created_at->format('M d, Y') }}</div>
                                    </div>

                                    <div class="mb-3">
                                        <small class="text-muted">Members</small>
                                        <div>{{ $team->members->count() }}/4</div>
                                        <div class="progress mt-1" style="height: 6px;">
                                            <div class="progress-bar" style="width: {{ ($team->members->count() / 4) * 100 }}%"></div>
                                        </div>
                                    </div>

                                    @if($team->project)
                                        <div class="mb-3">
                                            <small class="text-muted">Project Status</small>
                                            <div>
                                                <span class="badge bg-info">{{ ucfirst($team->project->status) }}</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            @if(!$isMember && auth()->user()->role === 'student' && !auth()->user()->teamMember && $team->members->count() < 4)
                                <div class="mt-3">
                                    <div class="card border-success">
                                        <div class="card-body text-center">
                                            <h6 class="text-success">Join This Team</h6>
                                            <p class="small text-muted">This team has space for more members.</p>
                                            <form action="{{ route('teams.join', $team) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fas fa-user-plus"></i> Join Team
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($isMember && !$isLeader)
                                <div class="mt-3">
                                    <div class="card border-warning">
                                        <div class="card-body text-center">
                                            <h6 class="text-warning">Leave Team</h6>
                                            <p class="small text-muted">Remove yourself from this team.</p>
                                            <form action="{{ route('teams.leave', $team) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-warning btn-sm"
                                                        onclick="return confirm('Are you sure you want to leave this team?')">
                                                    <i class="fas fa-sign-out-alt"></i> Leave Team
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Select Subject Modal -->
@if($isLeader && !$team->project && $availableSubjects->count() > 0)
<div class="modal fade" id="selectSubjectModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select Subject</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    @foreach($availableSubjects as $subject)
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 subject-card" data-subject-id="{{ $subject->id }}">
                                <div class="card-body">
                                    <h6 class="card-title">{{ $subject->title }}</h6>
                                    <p class="card-text text-truncate-2">{{ Str::limit($subject->description, 100) }}</p>
                                    <div class="mb-2">
                                        <small class="text-muted">Teacher:</small>
                                        <div>{{ $subject->teacher->name }}</div>
                                    </div>
                                    <div class="d-flex flex-wrap">
                                        @foreach(array_slice(explode(',', $subject->keywords), 0, 3) as $keyword)
                                            <span class="badge bg-secondary me-1 mb-1">{{ trim($keyword) }}</span>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="button" class="btn btn-success btn-sm w-100"
                                            onclick="selectSubject({{ $subject->id }}, '{{ $subject->title }}')">
                                        Select This Subject
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Transfer Leadership Modal -->
<div class="modal fade" id="transferLeadershipModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="transferLeadershipForm" method="POST" action="{{ route('teams.transfer-leadership', $team) }}">
                @csrf
                <input type="hidden" name="new_leader_id" id="newLeaderId">
                <div class="modal-header">
                    <h5 class="modal-title">Transfer Leadership</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to transfer team leadership to <strong id="newLeaderName"></strong>?</p>
                    <div class="alert alert-warning">
                        <strong>Note:</strong> You will no longer be the team leader and won't be able to manage team settings.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-exchange-alt"></i> Transfer Leadership
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.subject-card {
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
}

.subject-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.text-truncate-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
@endpush

@push('scripts')
<script>
function selectSubject(subjectId, subjectTitle) {
    if (confirm(`Are you sure you want to select "${subjectTitle}" for your team?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("teams.select-subject", $team) }}';

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';

        const subjectInput = document.createElement('input');
        subjectInput.type = 'hidden';
        subjectInput.name = 'subject_id';
        subjectInput.value = subjectId;

        form.appendChild(csrfToken);
        form.appendChild(subjectInput);
        document.body.appendChild(form);
        form.submit();
    }
}

function transferLeadership(userId, userName) {
    document.getElementById('newLeaderId').value = userId;
    document.getElementById('newLeaderName').textContent = userName;

    const modal = new bootstrap.Modal(document.getElementById('transferLeadershipModal'));
    modal.show();
}
</script>
@endpush