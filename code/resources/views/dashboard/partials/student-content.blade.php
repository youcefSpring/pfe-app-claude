<!-- Student Dashboard Content -->
<div class="col-md-4 mb-4">
    <div class="card">
        <div class="card-header">
            <h6 class="card-title mb-0">
                <i class="bi bi-people me-2"></i>{{ __('app.my_team_status') }}
            </h6>
        </div>
        <div class="card-body">
            @if(auth()->user()->teamMember)
                <div class="d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                        <i class="bi bi-check-circle text-success"></i>
                    </div>
                    <div>
                        <h6 class="mb-1">{{ auth()->user()->teamMember->team->name }}</h6>
                        <small class="text-muted">{{ __('app.role') }}: {{ ucfirst(auth()->user()->teamMember->role) }}</small>
                    </div>
                </div>
            @else
                <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-3">
                        <i class="bi bi-exclamation-triangle text-warning"></i>
                    </div>
                    <div>
                        <h6 class="mb-1">{{ __('app.no_team') }}</h6>
                        <small class="text-muted">{{ __('app.join_or_create_team') }}</small>
                    </div>
                </div>
                <a href="{{ route('teams.create') }}" class="btn btn-primary btn-sm mt-2">{{ __('app.create_team') }}</a>
            @endif
        </div>
    </div>
</div>

<div class="col-md-4 mb-4">
    <div class="card">
        <div class="card-header">
            <h6 class="card-title mb-0">
                <i class="bi bi-journal-text me-2"></i>{{ __('app.subject_selection') }}
            </h6>
        </div>
        <div class="card-body">
            {{-- Project-related functionality temporarily hidden --}}
            {{--
            @if(auth()->user()->teamMember && auth()->user()->teamMember->team->project)
                <div class="d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                        <i class="bi bi-check-circle text-success"></i>
                    </div>
                    <div>
                        <h6 class="mb-1">Subject Selected</h6>
                        <small class="text-muted">{{ auth()->user()->teamMember->team->project->subject->title }}</small>
                    </div>
                </div>
            @else
            --}}
                <div class="d-flex align-items-center">
                    <div class="bg-info bg-opacity-10 rounded-circle p-2 me-3">
                        <i class="bi bi-search text-info"></i>
                    </div>
                    <div>
                        <h6 class="mb-1">Browse Subjects</h6>
                        <small class="text-muted">Explore available subjects</small>
                    </div>
                </div>
                <a href="{{ route('subjects.available') }}" class="btn btn-info btn-sm mt-2">Browse Available Subjects</a>
            {{--
            @endif
            --}}
        </div>
    </div>
</div>

<div class="col-md-4 mb-4">
    <div class="card">
        <div class="card-header">
            <h6 class="card-title mb-0">
                <i class="bi bi-shield-check me-2"></i>Defense Status
            </h6>
        </div>
        <div class="card-body">
            @if(auth()->user()->teamMember && auth()->user()->teamMember->team->defense)
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                        <i class="bi bi-calendar-check text-primary"></i>
                    </div>
                    <div>
                        <h6 class="mb-1">Defense Scheduled</h6>
                        <small class="text-muted">{{ auth()->user()->teamMember->team->defense->defense_date->format('M d, Y') }}</small>
                    </div>
                </div>
            @else
                <div class="d-flex align-items-center">
                    <div class="bg-secondary bg-opacity-10 rounded-circle p-2 me-3">
                        <i class="bi bi-clock text-secondary"></i>
                    </div>
                    <div>
                        <h6 class="mb-1">Pending</h6>
                        <small class="text-muted">Defense not scheduled yet</small>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Academic Performance -->
<div class="col-md-4 mb-4">
    <div class="card">
        <div class="card-header">
            <h6 class="card-title mb-0">
                <i class="bi bi-graph-up me-2"></i>Academic Performance
            </h6>
        </div>
        <div class="card-body">
            @php
                $verifiedGrades = auth()->user()->grades()->verified()->count();
                $totalGrades = auth()->user()->grades()->count();
                $pendingGrades = auth()->user()->grades()->pendingVerification()->count();
                $gradeController = app(\App\Http\Controllers\Web\GradeController::class);
                $currentAverage = $gradeController->calculateAverage(auth()->user());
            @endphp
            <div class="row text-center">
                <div class="col-4">
                    <h4 class="text-success mb-1">{{ $verifiedGrades }}</h4>
                    <small class="text-muted">Verified</small>
                </div>
                <div class="col-4">
                    <h4 class="text-warning mb-1">{{ $pendingGrades }}</h4>
                    <small class="text-muted">Pending</small>
                </div>
                <div class="col-4">
                    <h4 class="text-primary mb-1">{{ number_format($currentAverage, 2) }}</h4>
                    <small class="text-muted">Average</small>
                </div>
            </div>
            @if($totalGrades == 0)
                <div class="text-center mt-3">
                    <a href="{{ route('grades.create') }}" class="btn btn-primary btn-sm">Add Grades</a>
                </div>
            @else
                <div class="text-center mt-3">
                    <a href="{{ route('grades.index') }}" class="btn btn-outline-primary btn-sm">Manage Grades</a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Subject Preferences & Allocation -->
<div class="col-md-6 mb-4">
    <div class="card">
        <div class="card-header">
            <h6 class="card-title mb-0">
                <i class="bi bi-list-check me-2"></i>Subject Preferences
            </h6>
        </div>
        <div class="card-body">
            @php
                $preferences = auth()->user()->subjectPreferences()->count();
                $currentDeadline = \App\Models\AllocationDeadline::active()->first();
                $allocation = auth()->user()->subjectAllocation;
            @endphp

            @if($allocation)
                <div class="d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                        <i class="bi bi-check-circle text-success"></i>
                    </div>
                    <div>
                        <h6 class="mb-1">Subject Allocated</h6>
                        <small class="text-muted">{{ $allocation->subject->title }}</small>
                        <br>
                        <small class="text-info">{{ $allocation->getPreferenceLabel() }} - {{ ucfirst($allocation->status) }}</small>
                    </div>
                </div>
            @elseif($currentDeadline && $currentDeadline->preferences_deadline->isFuture())
                <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-3">
                        <i class="bi bi-clock text-warning"></i>
                    </div>
                    <div>
                        <h6 class="mb-1">Preferences Open</h6>
                        <small class="text-muted">{{ $preferences }} preferences selected</small>
                        <br>
                        <small class="text-warning">Deadline: {{ $currentDeadline->preferences_deadline->format('M d, Y H:i') }}</small>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('preferences.index') }}" class="btn btn-primary btn-sm me-2">Manage Preferences</a>
                    @if($preferences == 0)
                        <a href="{{ route('preferences.create') }}" class="btn btn-outline-primary btn-sm">Add Preferences</a>
                    @endif
                </div>
            @else
                <div class="d-flex align-items-center">
                    <div class="bg-secondary bg-opacity-10 rounded-circle p-2 me-3">
                        <i class="bi bi-hourglass text-secondary"></i>
                    </div>
                    <div>
                        <h6 class="mb-1">Waiting for Allocation</h6>
                        <small class="text-muted">{{ $preferences }} preferences submitted</small>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Allocation Status -->
<div class="col-md-6 mb-4">
    <div class="card">
        <div class="card-header">
            <h6 class="card-title mb-0">
                <i class="bi bi-calendar-event me-2"></i>Allocation Timeline
            </h6>
        </div>
        <div class="card-body">
            @if($currentDeadline)
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">{{ $currentDeadline->title }}</h6>
                            <small class="text-muted">{{ $currentDeadline->description }}</small>
                            <br>
                            @if($currentDeadline->preferences_deadline->isFuture())
                                <span class="badge bg-warning">{{ $currentDeadline->preferences_deadline->diffForHumans() }}</span>
                            @else
                                <span class="badge bg-info">Deadline passed</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <a href="{{ route('allocations.my-allocation') }}" class="btn btn-outline-info btn-sm">View My Allocation</a>
                </div>
            @else
                <div class="text-center">
                    <i class="bi bi-calendar-x text-muted" style="font-size: 2rem;"></i>
                    <p class="text-muted mt-2">No active allocation period</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="col-12 mb-4">
    <div class="card">
        <div class="card-header">
            <h6 class="card-title mb-0">
                <i class="bi bi-clock-history me-2"></i>Recent Activity
            </h6>
        </div>
        <div class="card-body">
            <div class="list-group list-group-flush">
                <div class="list-group-item d-flex align-items-center">
                    <i class="bi bi-person-plus text-success me-3"></i>
                    <div>
                        <div class="fw-medium">Joined the system</div>
                        <small class="text-muted">{{ auth()->user()->created_at->diffForHumans() }}</small>
                    </div>
                </div>
                @if(auth()->user()->teamMember)
                    <div class="list-group-item d-flex align-items-center">
                        <i class="bi bi-people text-primary me-3"></i>
                        <div>
                            <div class="fw-medium">Joined team "{{ auth()->user()->teamMember->team->name }}"</div>
                            <small class="text-muted">{{ auth()->user()->teamMember->joined_at->diffForHumans() }}</small>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>