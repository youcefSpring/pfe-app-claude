<!-- Student Dashboard Content -->
<div class="col-md-4 mb-4">
    <div class="card">
        <div class="card-header">
            <h6 class="card-title mb-0">
                <i class="bi bi-people me-2"></i>My Team Status
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
                        <small class="text-muted">Role: {{ ucfirst(auth()->user()->teamMember->role) }}</small>
                    </div>
                </div>
            @else
                <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-3">
                        <i class="bi bi-exclamation-triangle text-warning"></i>
                    </div>
                    <div>
                        <h6 class="mb-1">No Team</h6>
                        <small class="text-muted">Join or create a team</small>
                    </div>
                </div>
                <a href="{{ route('teams.create') }}" class="btn btn-primary btn-sm mt-2">Create Team</a>
            @endif
        </div>
    </div>
</div>

<div class="col-md-4 mb-4">
    <div class="card">
        <div class="card-header">
            <h6 class="card-title mb-0">
                <i class="bi bi-journal-text me-2"></i>Subject Selection
            </h6>
        </div>
        <div class="card-body">
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
                <div class="d-flex align-items-center">
                    <div class="bg-info bg-opacity-10 rounded-circle p-2 me-3">
                        <i class="bi bi-search text-info"></i>
                    </div>
                    <div>
                        <h6 class="mb-1">Select Subject</h6>
                        <small class="text-muted">Browse available subjects</small>
                    </div>
                </div>
                <a href="{{ route('subjects.available') }}" class="btn btn-info btn-sm mt-2">Browse Subjects</a>
            @endif
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