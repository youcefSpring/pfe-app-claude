@extends('layouts.pfe-app')

@section('page-title', 'Subject Requests & Team Rankings - ' . $deadline->name)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="mb-1">Subject Requests & Rankings</h4>
                    <small class="text-muted">{{ $deadline->name }} - {{ $deadline->academic_year }}</small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.allocations.show', $deadline) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Allocation
                    </a>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#legendModal">
                        <i class="fas fa-info-circle"></i> Legend
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <h3 class="text-primary mb-0">{{ $subjectsWithTeams->where('is_allocated', false)->count() }}</h3>
                    <small class="text-muted">Available Subjects</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body text-center">
                    <h3 class="text-success mb-0">{{ $subjectsWithTeams->where('is_allocated', true)->count() }}</h3>
                    <small class="text-muted">Allocated Subjects</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <h3 class="text-warning mb-0">{{ $subjectsWithTeams->where('unallocated_requests', '>', 1)->count() }}</h3>
                    <small class="text-muted">Subjects with Conflicts</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body text-center">
                    <h3 class="text-info mb-0">{{ $subjectsWithTeams->sum('unallocated_requests') }}</h3>
                    <small class="text-muted">Total Pending Requests</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Subjects List -->
    <div class="row">
        <div class="col-12">
            @foreach($subjectsWithTeams as $item)
                <div class="card mb-3 subject-card {{ $item['is_allocated'] ? 'allocated' : '' }}">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h5 class="mb-1">
                                    {{ $item['subject']->title }}
                                    @if($item['is_allocated'])
                                        <span class="badge bg-success ms-2">
                                            <i class="fas fa-check"></i> Allocated
                                        </span>
                                    @elseif($item['unallocated_requests'] > 1)
                                        <span class="badge bg-warning">
                                            <i class="fas fa-exclamation-triangle"></i> {{ $item['unallocated_requests'] }} Teams Competing
                                        </span>
                                    @elseif($item['unallocated_requests'] == 1)
                                        <span class="badge bg-info">
                                            <i class="fas fa-user-check"></i> 1 Team Interested
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-inbox"></i> No Requests
                                        </span>
                                    @endif
                                </h5>
                                <div class="text-muted small">
                                    <i class="fas fa-user"></i> <strong>Teacher:</strong> {{ $item['subject']->teacher->name ?? 'Not assigned' }}
                                    @if($item['subject']->specialities->count() > 0)
                                        | <i class="fas fa-graduation-cap"></i> {{ $item['subject']->specialities->pluck('name')->join(', ') }}
                                    @endif
                                </div>
                            </div>
                            <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#subject-{{ $item['subject']->id }}" aria-expanded="false">
                                <i class="fas fa-chevron-down"></i> Details
                            </button>
                        </div>
                    </div>

                    <div class="collapse" id="subject-{{ $item['subject']->id }}">
                        <div class="card-body">
                            <!-- Subject Description -->
                            <div class="mb-3">
                                <strong>Description:</strong>
                                <p class="text-muted mb-0">{{ $item['subject']->description }}</p>
                            </div>

                            @if($item['is_allocated'])
                                <!-- Allocated Team Info -->
                                <div class="alert alert-success">
                                    <h6><i class="fas fa-check-circle"></i> Allocated to:</h6>
                                    <p class="mb-0">
                                        <strong>Team:</strong> {{ $item['allocation']->student->teamMember->team->name ?? 'N/A' }}
                                        <br>
                                        <strong>Student:</strong> {{ $item['allocation']->student->name }}
                                        <br>
                                        <strong>Average:</strong> {{ number_format($item['allocation']->student_average, 2) }}%
                                        <br>
                                        <strong>Preference:</strong> {{ $item['allocation']->getPreferenceLabel() }}
                                        <br>
                                        <strong>Method:</strong> <span class="badge bg-secondary">{{ $item['allocation']->allocation_method }}</span>
                                    </p>
                                </div>
                            @elseif($item['teams']->count() > 0)
                                <!-- Competing Teams Table -->
                                <h6 class="mb-3"><i class="fas fa-users"></i> Teams Requesting This Subject (Ranked by Best Student)</h6>
                                <div class="table-responsive">
                                    <table class="table table-hover table-sm">
                                        <thead>
                                            <tr>
                                                <th width="5%">Rank</th>
                                                <th width="20%">Team</th>
                                                <th width="25%">Best Student</th>
                                                <th width="10%">Average</th>
                                                <th width="12%">Preference</th>
                                                <th width="15%">Selected At</th>
                                                <th width="13%">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($item['teams'] as $index => $teamData)
                                                <tr class="{{ $index === 0 ? 'table-success' : '' }}">
                                                    <td>
                                                        @if($index === 0)
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-trophy"></i> #1
                                                            </span>
                                                        @else
                                                            <span class="badge bg-secondary">#{{ $index + 1 }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <strong>{{ $teamData['team']->name }}</strong>
                                                        <br>
                                                        <small class="text-muted">
                                                            {{ $teamData['team']->members->count() }} members
                                                        </small>
                                                    </td>
                                                    <td>
                                                        {{ $teamData['best_student']->name ?? 'N/A' }}
                                                        <br>
                                                        <small class="text-muted">{{ $teamData['best_student']->matricule ?? '' }}</small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-{{ $teamData['best_student_mark'] >= 80 ? 'success' : ($teamData['best_student_mark'] >= 70 ? 'warning' : 'secondary') }}">
                                                            {{ number_format($teamData['best_student_mark'], 2) }}%
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-{{ $teamData['preference_order'] <= 3 ? 'primary' : 'secondary' }}">
                                                            {{ $teamData['preference_order'] }}{{ $teamData['preference_order'] == 1 ? 'st' : ($teamData['preference_order'] == 2 ? 'nd' : ($teamData['preference_order'] == 3 ? 'rd' : 'th')) }} Choice
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <small>{{ $teamData['selected_at']->format('Y-m-d H:i') }}</small>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-primary"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#allocateModal"
                                                                data-team-id="{{ $teamData['team']->id }}"
                                                                data-team-name="{{ $teamData['team']->name }}"
                                                                data-subject-id="{{ $item['subject']->id }}"
                                                                data-subject-title="{{ $item['subject']->title }}"
                                                                data-default-supervisor="{{ $item['subject']->teacher_id }}">
                                                            <i class="fas fa-check"></i> Allocate
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-secondary">
                                    <i class="fas fa-inbox"></i> No teams have requested this subject yet.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Allocation Modal with Supervisor Selection -->
<div class="modal fade" id="allocateModal" tabindex="-1" aria-labelledby="allocateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.allocations.manual-allocate-supervisor') }}" method="POST">
                @csrf
                <input type="hidden" name="deadline_id" value="{{ $deadline->id }}">
                <input type="hidden" name="team_id" id="modal_team_id">
                <input type="hidden" name="subject_id" id="modal_subject_id">

                <div class="modal-header">
                    <h5 class="modal-title" id="allocateModalLabel">Manual Allocation with Supervisor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> You are allocating:
                        <br><strong>Team:</strong> <span id="modal_team_name"></span>
                        <br><strong>Subject:</strong> <span id="modal_subject_title"></span>
                    </div>

                    <div class="mb-3">
                        <label for="supervisor_id" class="form-label">
                            <i class="fas fa-user-tie"></i> Select Supervisor
                        </label>
                        <select class="form-select" name="supervisor_id" id="supervisor_id" required>
                            <option value="">Choose supervisor...</option>
                            @foreach(App\Models\User::where('role', 'teacher')->orderBy('name')->get() as $teacher)
                                <option value="{{ $teacher->id }}">
                                    {{ $teacher->name }} - {{ $teacher->speciality ?? 'N/A' }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">
                            The subject's default teacher will be pre-selected, but you can choose any teacher.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Confirm Allocation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Legend Modal -->
<div class="modal fade" id="legendModal" tabindex="-1" aria-labelledby="legendModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="legendModalLabel">Ranking & Badges Legend</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6><i class="fas fa-trophy"></i> Team Ranking</h6>
                <p>Teams are ranked by the <strong>BEST student average</strong> in each team, not the team average.</p>

                <h6 class="mt-3"><i class="fas fa-tag"></i> Badge Colors</h6>
                <ul>
                    <li><span class="badge bg-success">Green</span> - Top ranked team / High average (≥80%)</li>
                    <li><span class="badge bg-warning">Yellow</span> - Medium average (70-79%)</li>
                    <li><span class="badge bg-secondary">Gray</span> - Lower ranking / average (&lt;70%)</li>
                    <li><span class="badge bg-primary">Blue</span> - High preference (1st, 2nd, 3rd choice)</li>
                </ul>

                <h6 class="mt-3"><i class="fas fa-lightbulb"></i> How It Works</h6>
                <ol>
                    <li>When a subject is allocated, the team disappears from other subjects</li>
                    <li>The ranking is based on the best student's average in each team</li>
                    <li>You can manually choose any supervisor when allocating</li>
                    <li>Auto-allocation uses the same ranking system</li>
                </ol>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Got It</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.subject-card {
    transition: all 0.3s ease;
}
.subject-card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.subject-card.allocated {
    opacity: 0.7;
    background-color: #f8f9fa;
}
.table-success {
    background-color: #d4edda !important;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle allocation modal
    const allocateModal = document.getElementById('allocateModal');
    allocateModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;

        const teamId = button.getAttribute('data-team-id');
        const teamName = button.getAttribute('data-team-name');
        const subjectId = button.getAttribute('data-subject-id');
        const subjectTitle = button.getAttribute('data-subject-title');
        const defaultSupervisor = button.getAttribute('data-default-supervisor');

        document.getElementById('modal_team_id').value = teamId;
        document.getElementById('modal_team_name').textContent = teamName;
        document.getElementById('modal_subject_id').value = subjectId;
        document.getElementById('modal_subject_title').textContent = subjectTitle;

        // Pre-select default supervisor
        if (defaultSupervisor) {
            document.getElementById('supervisor_id').value = defaultSupervisor;
        }
    });
});
</script>
@endpush
@endsection
