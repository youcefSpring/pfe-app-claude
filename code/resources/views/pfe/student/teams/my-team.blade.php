@extends('layouts.admin')

@section('title', 'My Team - PFE Platform')
@section('page-title', 'My Team')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('pfe.student.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">My Team</li>
@endsection

@section('content')

@if($team)
<!-- Team Information -->
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-users mr-2"></i>
                    {{ $team->name }}
                </h3>
                <div class="card-tools">
                    <span class="badge badge-{{ $team->status === 'complete' ? 'success' : 'warning' }}">
                        {{ ucfirst($team->status) }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Description:</strong>
                        <p>{{ $team->description ?? 'No description provided' }}</p>

                        <strong>Created:</strong>
                        <p>{{ $team->created_at->format('M j, Y') }}</p>

                        <strong>Team Leader:</strong>
                        <p>{{ $teamLeader->first_name ?? 'Not assigned' }} {{ $teamLeader->last_name ?? '' }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Project:</strong>
                        <p>
                            @if($team->project)
                                <a href="{{ route('pfe.student.projects.show', $team->project->id) }}">
                                    {{ $team->project->title ?? $team->project->subject->title }}
                                </a>
                            @else
                                <span class="text-muted">Not assigned yet</span>
                            @endif
                        </p>

                        <strong>Subject:</strong>
                        <p>
                            @if($team->project)
                                {{ $team->project->subject->title }}
                            @else
                                <span class="text-muted">To be assigned</span>
                            @endif
                        </p>

                        <strong>Supervisor:</strong>
                        <p>
                            @if($team->project?->supervisor)
                                {{ $team->project->supervisor->first_name }} {{ $team->project->supervisor->last_name }}
                            @else
                                <span class="text-muted">To be assigned</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie mr-2"></i>
                    Team Progress
                </h3>
            </div>
            <div class="card-body text-center">
                <div class="progress-group">
                    <span class="float-right"><b>{{ $teamProgress ?? 0 }}%</b></span>
                    <span>Overall Progress</span>
                    <div class="progress progress-lg">
                        <div class="progress-bar bg-primary" style="width: {{ $teamProgress ?? 0 }}%"></div>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="row">
                        <div class="col-6">
                            <div class="description-block border-right">
                                <span class="description-percentage text-success">
                                    <i class="fas fa-check"></i>
                                </span>
                                <h5 class="description-header">{{ $completedTasks ?? 0 }}</h5>
                                <span class="description-text">COMPLETED</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="description-block">
                                <span class="description-percentage text-warning">
                                    <i class="fas fa-clock"></i>
                                </span>
                                <h5 class="description-header">{{ $pendingTasks ?? 0 }}</h5>
                                <span class="description-text">PENDING</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Team Members -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user-friends mr-2"></i>
                    Team Members ({{ $team->members->count() }}/{{ $maxMembers ?? 3 }})
                </h3>
                @if($team->members->count() < ($maxMembers ?? 3) && $canInvite ?? true)
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#inviteMemberModal">
                        <i class="fas fa-plus"></i> Invite Member
                    </button>
                </div>
                @endif
            </div>
            <div class="card-body p-0">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Member</th>
                            <th>Role</th>
                            <th>Email</th>
                            <th>Joined</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($team->members as $member)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $member->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($member->user->first_name . ' ' . $member->user->last_name) }}"
                                         alt="Avatar" class="img-circle img-size-32 mr-2">
                                    <div>
                                        <strong>{{ $member->user->first_name }} {{ $member->user->last_name }}</strong>
                                        @if($member->role === 'leader')
                                            <span class="badge badge-primary ml-1">Leader</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ ucfirst($member->role) }}</td>
                            <td>{{ $member->user->email }}</td>
                            <td>{{ $member->created_at->format('M j, Y') }}</td>
                            <td>
                                <span class="badge badge-{{ $member->status === 'active' ? 'success' : 'warning' }}">
                                    {{ ucfirst($member->status) }}
                                </span>
                            </td>
                            <td>
                                @if($member->user->id !== auth()->id() && ($isLeader ?? false))
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeMember({{ $member->user->id }})">
                                            <i class="fas fa-user-minus"></i>
                                        </button>
                                    </div>
                                @elseif($member->user->id === auth()->id())
                                    <button type="button" class="btn btn-sm btn-outline-warning" onclick="leaveTeam()">
                                        <i class="fas fa-sign-out-alt"></i> Leave
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No members found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@if($team->project)
<!-- Recent Activities -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-clock mr-2"></i>
                    Recent Team Activities
                </h3>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @forelse($recentActivities ?? [] as $activity)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas {{ $activity['icon'] ?? 'fa-circle' }} text-{{ $activity['color'] ?? 'primary' }} mr-2"></i>
                            <strong>{{ $activity['member'] ?? 'Team' }}</strong>
                            {{ $activity['action'] }}
                            @if(isset($activity['item']))
                                <span class="text-muted">{{ $activity['item'] }}</span>
                            @endif
                        </div>
                        <small class="text-muted">{{ $activity['date'] ?? '' }}</small>
                    </li>
                    @empty
                    <li class="list-group-item">No recent activities</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endif

@else
<!-- No Team State -->
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-users fa-4x text-muted mb-4"></i>
                <h4>You're not part of any team yet</h4>
                <p class="text-muted mb-4">
                    To start your PFE journey, you need to either create a new team or join an existing one.
                    Teams are required to work on projects and participate in the PFE program.
                </p>

                <div class="btn-group" role="group">
                    <a href="{{ route('pfe.student.teams.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create New Team
                    </a>
                    <a href="{{ route('pfe.student.teams.browse') }}" class="btn btn-outline-primary">
                        <i class="fas fa-search"></i> Browse Teams
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Invite Member Modal -->
@if($team && ($canInvite ?? true))
<div class="modal fade" id="inviteMemberModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Invite Team Member</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('pfe.student.teams.invite', $team->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="student_email">Student Email</label>
                        <input type="email" class="form-control" id="student_email" name="email" required>
                        <small class="form-text text-muted">Enter the email of the student you want to invite</small>
                    </div>
                    <div class="form-group">
                        <label for="invitation_message">Message (optional)</label>
                        <textarea class="form-control" id="invitation_message" name="message" rows="3"
                                  placeholder="Add a personal message to your invitation..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Send Invitation</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
function removeMember(userId) {
    if (confirm('Are you sure you want to remove this member from the team?')) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('pfe.student.teams.remove-member', ['team' => $team->id ?? 0, 'user' => '']) }}/${userId}`;

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

function leaveTeam() {
    if (confirm('Are you sure you want to leave this team? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("pfe.student.teams.leave") }}';

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';

        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush