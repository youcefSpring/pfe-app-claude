@extends('layouts.pfe-app')

@section('title', 'My Defense')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">My Defense</h1>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    @if($defense)
        <div class="row">
            <!-- Defense Status Card -->
            <div class="col-12 mb-4">
                <div class="card border-{{ $defense->status === 'completed' ? 'success' : ($defense->status === 'in_progress' ? 'warning' : ($defense->status === 'cancelled' ? 'danger' : 'primary')) }}">
                    <div class="card-body text-center">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <i class="bi bi-shield-check display-4 text-{{ $defense->status === 'completed' ? 'success' : ($defense->status === 'in_progress' ? 'warning' : ($defense->status === 'cancelled' ? 'danger' : 'primary')) }}"></i>
                            </div>
                            <div class="col-md-6">
                                <h4 class="mb-2">Defense Status</h4>
                                <h2 class="text-{{ $defense->status === 'completed' ? 'success' : ($defense->status === 'in_progress' ? 'warning' : ($defense->status === 'cancelled' ? 'danger' : 'primary')) }}">
                                    {{ ucfirst(str_replace('_', ' ', $defense->status)) }}
                                </h2>
                                @if($defense->defense_date)
                                    <p class="text-muted mb-0">
                                        {{ $defense->defense_date->format('F d, Y \\a\\t g:i A') }}
                                    </p>
                                @endif
                            </div>
                            <div class="col-md-3">
                                @if($defense->status === 'scheduled')
                                    @php
                                        $daysUntil = now()->diffInDays($defense->defense_date, false);
                                        $hoursUntil = now()->diffInHours($defense->defense_date, false);
                                    @endphp
                                    @if($daysUntil > 0)
                                        <h5 class="text-primary mb-1">{{ $daysUntil }} Days</h5>
                                        <small class="text-muted">Until Defense</small>
                                    @elseif($hoursUntil > 0)
                                        <h5 class="text-warning mb-1">{{ $hoursUntil }} Hours</h5>
                                        <small class="text-muted">Until Defense</small>
                                    @else
                                        <h5 class="text-danger mb-1">Today!</h5>
                                        <small class="text-muted">Defense Day</small>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Defense Details -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Defense Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-muted">Project Details</h6>
                                <p><strong>Subject:</strong> {{ $defense->project->subject->title ?? 'N/A' }}</p>
                                <p><strong>Type:</strong> {{ ucfirst($defense->project->subject->type ?? 'N/A') }}</p>
                                <p><strong>Supervisor:</strong> {{ $defense->project->subject->teacher->name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">Schedule & Location</h6>
                                <p><strong>Date:</strong> {{ $defense->defense_date ? $defense->defense_date->format('M d, Y') : 'TBD' }}</p>
                                <p><strong>Time:</strong> {{ $defense->defense_date ? $defense->defense_date->format('g:i A') : 'TBD' }}</p>
                                <p><strong>Duration:</strong> {{ $defense->duration ?? 60 }} minutes</p>
                                <p><strong>Room:</strong> {{ $defense->room->name ?? 'TBD' }}
                                    @if($defense->room && $defense->room->location)
                                        <br><small class="text-muted">{{ $defense->room->location }}</small>
                                    @endif
                                </p>
                            </div>
                        </div>

                        @if($defense->notes)
                            <div class="mt-3">
                                <h6 class="text-muted">Instructions</h6>
                                <div class="alert alert-info">
                                    <small>{{ $defense->notes }}</small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Team Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Team Members</h5>
                    </div>
                    <div class="card-body">
                        @if($defense->project->team->members->count() > 0)
                            <div class="row">
                                @foreach($defense->project->team->members as $member)
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-lg bg-{{ $member->is_leader ? 'primary' : 'secondary' }} text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                                                {{ substr($member->user->name, 0, 2) }}
                                            </div>
                                            <div>
                                                <h6 class="mb-1">{{ $member->user->name }}</h6>
                                                <small class="text-muted">{{ $member->user->email }}</small>
                                                @if($member->is_leader)
                                                    <br><span class="badge bg-primary">Team Leader</span>
                                                @endif
                                                @if($member->user->id === auth()->id())
                                                    <br><span class="badge bg-success">You</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Preparation Checklist -->
                @if($defense->status === 'scheduled')
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Defense Preparation Checklist</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="preparation1">
                                <label class="form-check-label" for="preparation1">
                                    <strong>Presentation slides ready</strong><br>
                                    <small class="text-muted">Prepare a clear and concise presentation covering your project</small>
                                </label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="preparation2">
                                <label class="form-check-label" for="preparation2">
                                    <strong>Demo prepared</strong><br>
                                    <small class="text-muted">Test your application/system and prepare for live demonstration</small>
                                </label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="preparation3">
                                <label class="form-check-label" for="preparation3">
                                    <strong>Documentation complete</strong><br>
                                    <small class="text-muted">Ensure all required documentation is submitted and organized</small>
                                </label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="preparation4">
                                <label class="form-check-label" for="preparation4">
                                    <strong>Questions & answers preparation</strong><br>
                                    <small class="text-muted">Anticipate potential questions and prepare clear answers</small>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="preparation5">
                                <label class="form-check-label" for="preparation5">
                                    <strong>Backup plans ready</strong><br>
                                    <small class="text-muted">Prepare backup materials in case of technical difficulties</small>
                                </label>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Jury Panel -->
                @if($defense->juries->count() > 0)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Jury Panel</h5>
                        </div>
                        <div class="card-body">
                            @foreach($defense->juries as $jury)
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avatar-sm bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                                        {{ substr($jury->teacher->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $jury->teacher->name }}</h6>
                                        <small class="text-muted">{{ ucfirst($jury->role) }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Important Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Important Information</h5>
                    </div>
                    <div class="card-body">
                        @if($defense->status === 'scheduled')
                            <div class="alert alert-primary">
                                <i class="bi bi-info-circle"></i>
                                <strong>Reminder:</strong> Please arrive 15 minutes before your scheduled time.
                            </div>
                        @elseif($defense->status === 'in_progress')
                            <div class="alert alert-warning">
                                <i class="bi bi-clock"></i>
                                <strong>In Progress:</strong> Your defense is currently underway.
                            </div>
                        @elseif($defense->status === 'completed')
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle"></i>
                                <strong>Completed:</strong> Your defense has been successfully completed.
                            </div>
                        @elseif($defense->status === 'cancelled')
                            <div class="alert alert-danger">
                                <i class="bi bi-x-circle"></i>
                                <strong>Cancelled:</strong> Your defense has been cancelled. Please contact your supervisor.
                            </div>
                        @endif

                        <h6 class="mt-3">Contact Information</h6>
                        <p class="small text-muted mb-2">
                            <strong>Supervisor:</strong><br>
                            {{ $defense->project->subject->teacher->name ?? 'N/A' }}<br>
                            <a href="mailto:{{ $defense->project->subject->teacher->email ?? '' }}">{{ $defense->project->subject->teacher->email ?? 'N/A' }}</a>
                        </p>

                        @if($defense->status === 'scheduled')
                            <h6 class="mt-3">What to Bring</h6>
                            <ul class="small text-muted">
                                <li>Student ID card</li>
                                <li>Presentation materials</li>
                                <li>Laptop/device for demo</li>
                                <li>Backup storage device</li>
                                <li>Any required documents</li>
                            </ul>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions -->
                @if($defense->status === 'completed' && $defense->report)
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Results</h5>
                        </div>
                        <div class="card-body text-center">
                            <h3 class="text-success mb-2">{{ $defense->report->overall_grade ?? 'N/A' }}/20</h3>
                            <p class="text-muted">Overall Grade</p>
                            <a href="{{ route('defenses.show', $defense) }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-eye"></i> View Full Details
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @else
        <!-- No Defense Scheduled -->
        <div class="text-center py-5">
            <i class="bi bi-calendar-x text-muted" style="font-size: 4rem;"></i>
            <h4 class="mt-3">No Defense Scheduled</h4>
            <p class="text-muted">
                You don't have a defense scheduled yet. Make sure your team has completed all requirements:<br>
                ✓ Team formation<br>
                ✓ Subject selection<br>
                ✓ Project submission<br>
            </p>
            <a href="{{ route('teams.index') }}" class="btn btn-primary">
                <i class="bi bi-people"></i> View My Team
            </a>
        </div>
    @endif
</div>

<style>
.avatar-sm {
    width: 40px;
    height: 40px;
    font-size: 14px;
    font-weight: 600;
}

.avatar-lg {
    width: 50px;
    height: 50px;
    font-size: 16px;
    font-weight: 600;
}

.form-check-label strong {
    display: block;
    margin-bottom: 2px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Save checklist state in localStorage
    const checkboxes = document.querySelectorAll('.form-check-input');
    checkboxes.forEach(function(checkbox) {
        // Load saved state
        const saved = localStorage.getItem(checkbox.id);
        if (saved === 'true') {
            checkbox.checked = true;
        }

        // Save state on change
        checkbox.addEventListener('change', function() {
            localStorage.setItem(this.id, this.checked);
        });
    });
});
</script>
@endsection