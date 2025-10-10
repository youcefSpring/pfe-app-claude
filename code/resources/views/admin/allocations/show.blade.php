@extends('layouts.pfe-app')

@section('page-title', __('app.allocation_details') . ' - ' . $deadline->name)

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-1">{{ $deadline->name }}</h4>
                            <small class="text-muted">{{ $deadline->academic_year }} - {{ $deadline->level }}</small>
                        </div>
                        <div class="d-flex gap-2">
                            <span class="badge bg-{{ $deadline->status == 'active' ? 'success' : ($deadline->status == 'auto_allocation_completed' ? 'info' : 'secondary') }} fs-6">
                                {{ ucfirst(str_replace('_', ' ', $deadline->status)) }}
                            </span>
                            <a href="{{ route('admin.allocations.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> {{ __('app.back') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="stat-card text-center">
                                <div class="stat-number text-primary">{{ $stats['total_teams'] }}</div>
                                <div class="stat-label">{{ __('app.total_teams') }}</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card text-center">
                                <div class="stat-number text-info">{{ $stats['teams_with_preferences'] }}</div>
                                <div class="stat-label">{{ __('app.teams_with_preferences') }}</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card text-center">
                                <div class="stat-number text-success">{{ $stats['allocated_teams'] }}</div>
                                <div class="stat-label">{{ __('app.allocated_teams') }}</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card text-center">
                                <div class="stat-number text-warning">{{ $stats['available_subjects'] }}</div>
                                <div class="stat-label">{{ __('app.available_subjects') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        @if($deadline->canPerformAutoAllocation())
                            <form action="{{ route('admin.allocations.auto-allocation', $deadline) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-primary" onclick="return confirm('{{ __('app.confirm_auto_allocation') }}')">
                                    <i class="fas fa-magic"></i> {{ __('app.perform_auto_allocation') }}
                                </button>
                            </form>
                        @else
                            <button class="btn btn-secondary" disabled>
                                <i class="fas fa-magic"></i> {{ __('app.auto_allocation_completed') }}
                            </button>
                        @endif

                        @if(!$deadline->second_round_needed && $unallocatedTeams->count() > 0)
                            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#secondRoundModal">
                                <i class="fas fa-redo"></i> {{ __('app.initialize_second_round') }}
                            </button>
                        @endif

                        @if($deadline->isSecondRoundActive())
                            <span class="badge bg-primary fs-6 align-self-center">
                                <i class="fas fa-clock"></i> {{ __('app.second_round_active') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Unallocated Teams -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-users text-warning"></i>
                        {{ __('app.unallocated_teams') }} ({{ $unallocatedTeams->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    @forelse($unallocatedTeams as $team)
                        <div class="border rounded p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="mb-0">{{ $team->name }}</h6>
                                <button type="button" class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#manualAssignModal"
                                        data-team-id="{{ $team->id }}"
                                        data-team-name="{{ $team->name }}">
                                    <i class="fas fa-hand-point-right"></i> {{ __('app.assign') }}
                                </button>
                            </div>

                            <div class="mb-2">
                                <small class="text-muted">{{ __('app.members') }}:</small>
                                <div class="small">
                                    @foreach($team->members as $member)
                                        <span class="badge bg-light text-dark me-1">
                                            {{ $member->user->name }}
                                            @if($member->is_leader)
                                                <i class="fas fa-crown text-warning" title="{{ __('app.team_leader') }}"></i>
                                            @endif
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            @if($team->preferences->count() > 0)
                                <div>
                                    <small class="text-muted">{{ __('app.preferences') }}:</small>
                                    <div class="small">
                                        @foreach($team->preferences->take(3) as $preference)
                                            <div class="text-truncate">
                                                {{ $preference->preference_order }}. {{ $preference->subject->title }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <small class="text-danger">{{ __('app.no_preferences_submitted') }}</small>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h6 class="text-success">{{ __('app.all_teams_allocated') }}</h6>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Available Subjects -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-book text-success"></i>
                        {{ __('app.available_subjects') }} ({{ $availableSubjects->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    @forelse($availableSubjects as $subject)
                        <div class="border rounded p-3 mb-3">
                            <h6 class="mb-1">{{ $subject->title }}</h6>
                            <div class="mb-2">
                                <small class="text-muted">{{ __('app.supervisor') }}:</small>
                                <span class="fw-semibold">{{ $subject->teacher->name ?? __('app.not_assigned') }}</span>
                            </div>
                            @if($subject->description)
                                <div class="small text-muted mb-2">
                                    {{ Str::limit($subject->description, 100) }}
                                </div>
                            @endif
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-{{ $subject->is_external ? 'info' : 'primary' }}">
                                    {{ $subject->is_external ? __('app.external') : __('app.internal') }}
                                </span>
                                <button type="button" class="btn btn-sm btn-outline-success"
                                        data-bs-toggle="modal"
                                        data-bs-target="#manualAssignModal"
                                        data-subject-id="{{ $subject->id }}"
                                        data-subject-title="{{ $subject->title }}">
                                    <i class="fas fa-plus"></i> Assign
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h6 class="text-success">{{ __('app.all_subjects_allocated') }}</h6>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Manual Assignment Modal -->
<div class="modal fade" id="manualAssignModal" tabindex="-1" aria-labelledby="manualAssignModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.allocations.manual-assignment') }}" method="POST">
                @csrf
                <input type="hidden" name="deadline_id" value="{{ $deadline->id }}">
                <div class="modal-header">
                    <h5 class="modal-title" id="manualAssignModalLabel">{{ __('app.manual_assignment') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('app.close') }}"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="team_id" class="form-label">{{ __('app.select_team') }}</label>
                        <select class="form-select" name="team_id" id="team_id" required>
                            <option value="">{{ __('app.choose_team') }}</option>
                            @foreach($unallocatedTeams as $team)
                                <option value="{{ $team->id }}">{{ $team->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="subject_id" class="form-label">{{ __('app.select_subject') }}</label>
                        <select class="form-select" name="subject_id" id="subject_id" required>
                            <option value="">{{ __('app.choose_subject') }}</option>
                            @foreach($availableSubjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('app.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('app.assign_subject') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Second Round Modal -->
<div class="modal fade" id="secondRoundModal" tabindex="-1" aria-labelledby="secondRoundModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.allocations.second-round', $deadline) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="secondRoundModalLabel">{{ __('app.initialize_second_round') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('app.close') }}"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        {{ __('app.second_round_info') }}
                    </div>
                    <div class="mb-3">
                        <label for="second_round_start" class="form-label">{{ __('app.second_round_start_date') }}</label>
                        <input type="datetime-local" class="form-control" name="second_round_start" id="second_round_start" required>
                    </div>
                    <div class="mb-3">
                        <label for="second_round_deadline" class="form-label">{{ __('app.second_round_deadline') }}</label>
                        <input type="datetime-local" class="form-control" name="second_round_deadline" id="second_round_deadline" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('app.cancel') }}</button>
                    <button type="submit" class="btn btn-warning">{{ __('app.initialize_second_round') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
.stat-card {
    padding: 1rem;
    border-radius: 8px;
    background: #f8f9fa;
}
.stat-number {
    font-size: 2rem;
    font-weight: bold;
}
.stat-label {
    font-size: 0.875rem;
    color: #6c757d;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle manual assignment modal
    const manualAssignModal = document.getElementById('manualAssignModal');
    const teamSelect = document.getElementById('team_id');
    const subjectSelect = document.getElementById('subject_id');

    manualAssignModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const teamId = button.getAttribute('data-team-id');
        const subjectId = button.getAttribute('data-subject-id');

        if (teamId) {
            teamSelect.value = teamId;
        }
        if (subjectId) {
            subjectSelect.value = subjectId;
        }
    });

    // Set default dates for second round
    const now = new Date();
    const tomorrow = new Date(now);
    tomorrow.setDate(tomorrow.getDate() + 1);
    const weekLater = new Date(now);
    weekLater.setDate(weekLater.getDate() + 8);

    document.getElementById('second_round_start').value = tomorrow.toISOString().slice(0, 16);
    document.getElementById('second_round_deadline').value = weekLater.toISOString().slice(0, 16);
});
</script>
@endpush
@endsection