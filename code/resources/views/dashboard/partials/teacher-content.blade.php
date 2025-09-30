<!-- Teacher Dashboard Content -->
<div class="col-md-4 mb-4">
    <div class="card">
        <div class="card-header">
            <h6 class="card-title mb-0">
                <i class="bi bi-journal-text me-2"></i>My Subjects
            </h6>
        </div>
        <div class="card-body">
            @php
                $subjectCount = auth()->user()->subjects()->count();
                $validatedCount = auth()->user()->subjects()->where('status', 'validated')->count();
            @endphp
            <div class="row text-center">
                <div class="col-6">
                    <h4 class="text-primary mb-1">{{ $subjectCount }}</h4>
                    <small class="text-muted">Total Subjects</small>
                </div>
                <div class="col-6">
                    <h4 class="text-success mb-1">{{ $validatedCount }}</h4>
                    <small class="text-muted">Validated</small>
                </div>
            </div>
            <a href="{{ route('subjects.create') }}" class="btn btn-primary btn-sm w-100 mt-3">Add New Subject</a>
        </div>
    </div>
</div>

<div class="col-md-4 mb-4">
    <div class="card">
        <div class="card-header">
            <h6 class="card-title mb-0">
                <i class="bi bi-eye me-2"></i>Supervised Projects
            </h6>
        </div>
        <div class="card-body">
            @php
                $supervisedCount = auth()->user()->supervisedProjects()->count();
                $activeCount = auth()->user()->supervisedProjects()->where('status', 'active')->count();
            @endphp
            <div class="row text-center">
                <div class="col-6">
                    <h4 class="text-info mb-1">{{ $supervisedCount }}</h4>
                    <small class="text-muted">Total Projects</small>
                </div>
                <div class="col-6">
                    <h4 class="text-warning mb-1">{{ $activeCount }}</h4>
                    <small class="text-muted">Active</small>
                </div>
            </div>
            <a href="{{ route('projects.supervised') }}" class="btn btn-info btn-sm w-100 mt-3">View Projects</a>
        </div>
    </div>
</div>

<div class="col-md-4 mb-4">
    <div class="card">
        <div class="card-header">
            <h6 class="card-title mb-0">
                <i class="bi bi-people me-2"></i>Jury Assignments
            </h6>
        </div>
        <div class="card-body">
            @php
                $juryCount = auth()->user()->juryAssignments()->count();
                $upcomingCount = auth()->user()->juryAssignments()
                    ->whereHas('defense', function($q) {
                        $q->where('defense_date', '>', now());
                    })->count();
            @endphp
            <div class="row text-center">
                <div class="col-6">
                    <h4 class="text-secondary mb-1">{{ $juryCount }}</h4>
                    <small class="text-muted">Total Assignments</small>
                </div>
                <div class="col-6">
                    <h4 class="text-primary mb-1">{{ $upcomingCount }}</h4>
                    <small class="text-muted">Upcoming</small>
                </div>
            </div>
            <a href="{{ route('defenses.jury-assignments') }}" class="btn btn-secondary btn-sm w-100 mt-3">View Assignments</a>
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
                @forelse(auth()->user()->subjects()->latest()->take(3)->get() as $subject)
                    <div class="list-group-item d-flex align-items-center">
                        <i class="bi bi-journal-text text-primary me-3"></i>
                        <div>
                            <div class="fw-medium">Created subject: "{{ $subject->title }}"</div>
                            <small class="text-muted">{{ $subject->created_at->diffForHumans() }}</small>
                        </div>
                        <span class="badge bg-{{ $subject->status === 'validated' ? 'success' : ($subject->status === 'pending_validation' ? 'warning' : 'secondary') }} ms-auto">
                            {{ ucfirst($subject->status) }}
                        </span>
                    </div>
                @empty
                    <div class="list-group-item text-center text-muted">
                        <i class="bi bi-journal-plus d-block mb-2" style="font-size: 2rem;"></i>
                        No subjects created yet. <a href="{{ route('subjects.create') }}">Create your first subject</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Pending Actions -->
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h6 class="card-title mb-0">
                <i class="bi bi-exclamation-triangle me-2"></i>Pending Actions
            </h6>
        </div>
        <div class="card-body">
            @php
                $pendingSubmissions = auth()->user()->supervisedProjects()
                    ->whereHas('submissions', function($q) {
                        $q->where('status', 'submitted');
                    })->count();
                $pendingDefenses = auth()->user()->juryAssignments()
                    ->whereHas('defense', function($q) {
                        $q->where('defense_date', '>', now())
                          ->where('defense_date', '<=', now()->addWeek());
                    })->count();
            @endphp

            @if($pendingSubmissions > 0 || $pendingDefenses > 0)
                <div class="row">
                    @if($pendingSubmissions > 0)
                        <div class="col-md-6">
                            <div class="alert alert-warning">
                                <i class="bi bi-file-earmark-text me-2"></i>
                                <strong>{{ $pendingSubmissions }}</strong> submission(s) need review
                            </div>
                        </div>
                    @endif
                    @if($pendingDefenses > 0)
                        <div class="col-md-6">
                            <div class="alert alert-info">
                                <i class="bi bi-calendar-check me-2"></i>
                                <strong>{{ $pendingDefenses }}</strong> defense(s) this week
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <div class="text-center text-muted">
                    <i class="bi bi-check-circle d-block mb-2" style="font-size: 2rem;"></i>
                    All caught up! No pending actions.
                </div>
            @endif
        </div>
    </div>
</div>