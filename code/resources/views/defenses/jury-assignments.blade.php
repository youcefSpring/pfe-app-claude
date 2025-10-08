@extends('layouts.pfe-app')

@section('title', 'Jury Assignments')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">My Jury Assignments</h1>
        <div class="btn-group">
            <a href="{{ route('defenses.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-list"></i> All Defenses
            </a>
            <a href="{{ route('defenses.calendar') }}" class="btn btn-outline-secondary">
                <i class="bi bi-calendar"></i> Calendar View
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <h3 class="text-primary">{{ $assignments->where('defense.status', 'scheduled')->count() }}</h3>
                    <p class="text-muted mb-0">Upcoming</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <h3 class="text-warning">{{ $assignments->where('defense.status', 'in_progress')->count() }}</h3>
                    <p class="text-muted mb-0">In Progress</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body text-center">
                    <h3 class="text-success">{{ $assignments->where('defense.status', 'completed')->count() }}</h3>
                    <p class="text-muted mb-0">Completed</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body text-center">
                    <h3 class="text-info">{{ $assignments->total() }}</h3>
                    <p class="text-muted mb-0">Total Assignments</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Assignments List -->
    @if($assignments->count() > 0)
        <div class="row">
            @foreach($assignments as $assignment)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 border-start border-{{ $assignment->defense->status === 'completed' ? 'success' : ($assignment->defense->status === 'in_progress' ? 'warning' : ($assignment->defense->status === 'cancelled' ? 'danger' : 'primary')) }} border-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span class="badge bg-{{ $assignment->defense->status === 'completed' ? 'success' : ($assignment->defense->status === 'in_progress' ? 'warning' : ($assignment->defense->status === 'cancelled' ? 'danger' : 'primary')) }}">
                                {{ ucfirst(str_replace('_', ' ', $assignment->defense->status)) }}
                            </span>
                            <span class="badge bg-secondary">{{ ucfirst($assignment->role) }}</span>
                        </div>

                        <div class="card-body">
                            <h5 class="card-title">{{ $assignment->defense->project->subject->title ?? 'Defense' }}</h5>
                            <h6 class="text-muted mb-3">{{ $assignment->defense->project->team->name ?? 'Team' }}</h6>

                            <!-- Defense Details -->
                            <div class="mb-3">
                                @if($assignment->defense->defense_date)
                                    <p class="small mb-1">
                                        <i class="bi bi-calendar me-1"></i>
                                        {{ $assignment->defense->defense_date ? \Carbon\Carbon::parse($assignment->defense->defense_date)->format('M d, Y \\a\\t g:i A') : 'TBD' }}
                                    </p>
                                @else
                                    <p class="small mb-1 text-muted">
                                        <i class="bi bi-calendar me-1"></i>
                                        Date TBD
                                    </p>
                                @endif

                                @if($assignment->defense->room)
                                    <p class="small mb-1">
                                        <i class="bi bi-geo-alt me-1"></i>
                                        {{ $assignment->defense->room->name }}
                                        @if($assignment->defense->room->location)
                                            <small class="text-muted">({{ $assignment->defense->room->location }})</small>
                                        @endif
                                    </p>
                                @endif

                                <p class="small mb-1">
                                    <i class="bi bi-clock me-1"></i>
                                    {{ $assignment->defense->duration ?? 60 }} minutes
                                </p>
                            </div>

                            <!-- Team Members -->
                            @if($assignment->defense->project->team->members->count() > 0)
                                <div class="mb-3">
                                    <h6 class="text-muted small mb-2">Team Members:</h6>
                                    @foreach($assignment->defense->project->team->members->take(2) as $member)
                                        <span class="badge bg-light text-dark me-1 mb-1">{{ $member->user->name }}</span>
                                    @endforeach
                                    @if($assignment->defense->project->team->members->count() > 2)
                                        <span class="badge bg-secondary mb-1">+{{ $assignment->defense->project->team->members->count() - 2 }}</span>
                                    @endif
                                </div>
                            @endif

                            <!-- Role-specific Information -->
                            @if($assignment->role === 'president')
                                <div class="alert alert-info py-2">
                                    <small><strong>As President:</strong> You will lead the defense session and coordinate with other jury members.</small>
                                </div>
                            @elseif($assignment->role === 'examiner')
                                <div class="alert alert-secondary py-2">
                                    <small><strong>As Examiner:</strong> You will evaluate the technical aspects and ask detailed questions.</small>
                                </div>
                            @elseif($assignment->role === 'supervisor')
                                <div class="alert alert-success py-2">
                                    <small><strong>As Supervisor:</strong> You have guided this project and will provide final assessment.</small>
                                </div>
                            @endif
                        </div>

                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('defenses.show', $assignment->defense) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-eye"></i> View Details
                                </a>

                                @if($assignment->defense->status === 'scheduled')
                                    @if($assignment->defense->defense_date && $assignment->defense->defense_date->isToday())
                                        <span class="badge bg-warning">Today!</span>
                                    @elseif($assignment->defense->defense_date && $assignment->defense->defense_date->isTomorrow())
                                        <span class="badge bg-info">Tomorrow</span>
                                    @elseif($assignment->defense->defense_date)
                                        @php
                                            $daysUntil = now()->diffInDays($assignment->defense->defense_date, false);
                                        @endphp
                                        @if($daysUntil > 0)
                                            <small class="text-muted">{{ $daysUntil }} days</small>
                                        @else
                                            <small class="text-danger">Overdue</small>
                                        @endif
                                    @endif
                                @elseif($assignment->defense->status === 'completed')
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle"></i> Done
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($assignments->hasPages())
            <div class="d-flex justify-content-center mt-4">
                <nav aria-label="Jury assignments pagination">
                    {{ $assignments->links('pagination::bootstrap-4') }}
                </nav>
            </div>
        @endif
    @else
        <!-- No Assignments -->
        <div class="text-center py-5">
            <i class="bi bi-clipboard-x text-muted" style="font-size: 4rem;"></i>
            <h4 class="mt-3">No Jury Assignments</h4>
            <p class="text-muted">
                You haven't been assigned to any defense juries yet.<br>
                Assignments are typically made by the department head or admin.
            </p>
            <a href="{{ route('defenses.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-shield-check"></i> View All Defenses
            </a>
        </div>
    @endif
</div>

<!-- Upcoming Defense Modal -->
@if($assignments->where('defense.status', 'scheduled')->where('defense.defense_date', '<=', now()->addDay())->count() > 0)
    <div class="modal fade" id="upcomingDefenseModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle"></i> Upcoming Defense Reminder
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>You have defense(s) scheduled for today or tomorrow:</p>
                    @foreach($assignments->where('defense.status', 'scheduled')->where('defense.defense_date', '<=', now()->addDay()) as $upcoming)
                        <div class="alert alert-warning">
                            <strong>{{ $upcoming->defense->project->subject->title ?? 'Defense' }}</strong><br>
                            <small>
                                {{ $upcoming->defense->defense_date ? \Carbon\Carbon::parse($upcoming->defense->defense_date)->format('M d, Y \\a\\t g:i A') : 'TBD' }} -
                                {{ $upcoming->defense->room->name ?? 'Room TBD' }}<br>
                                Role: {{ ucfirst($upcoming->role) }}
                            </small>
                        </div>
                    @endforeach
                    <p class="text-muted">Please make sure you're prepared and available.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Understood</button>
                    <a href="{{ route('defenses.calendar') }}" class="btn btn-primary">View Calendar</a>
                </div>
            </div>
        </div>
    </div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show upcoming defense modal if there are defenses today/tomorrow
    const upcomingModal = document.getElementById('upcomingDefenseModal');
    if (upcomingModal) {
        const modal = new bootstrap.Modal(upcomingModal);

        // Only show if not dismissed today
        const dismissedToday = localStorage.getItem('defenseReminderDismissed');
        const today = new Date().toDateString();

        if (dismissedToday !== today) {
            setTimeout(() => modal.show(), 1000);
        }

        // Mark as dismissed when modal is closed
        upcomingModal.addEventListener('hidden.bs.modal', function() {
            localStorage.setItem('defenseReminderDismissed', today);
        });
    }
});
</script>

<style>
.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}

.border-3 {
    border-width: 3px !important;
}

.alert {
    font-size: 0.875rem;
}
</style>
@endsection