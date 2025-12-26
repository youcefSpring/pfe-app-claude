<!-- Department Head Dashboard Content -->
<div class="col-md-3 mb-4">
    <div class="card">
        <div class="card-header">
            <h6 class="card-title mb-0">
                <i class="bi bi-check-circle me-2"></i>Subject Validation
            </h6>
        </div>
        <div class="card-body">
            @php
                $pendingSubjects = \App\Models\Subject::where('status', 'pending_validation')
                    ->whereHas('teacher', function($q) {
                        $q->where('department', auth()->user()->department);
                    })->count();
            @endphp
            <div class="text-center">
                <h3 class="text-warning mb-1">{{ $pendingSubjects }}</h3>
                <small class="text-muted">Pending Validation</small>
            </div>
            <a href="{{ route('subjects.pending-validation') }}" class="btn btn-warning btn-sm w-100 mt-3">
                Review Subjects
            </a>
        </div>
    </div>
</div>

<div class="col-md-3 mb-4">
    <div class="card">
        <div class="card-header">
            <h6 class="card-title mb-0">
                <i class="bi bi-people me-2"></i>Department Teams
            </h6>
        </div>
        <div class="card-body">
            @php
                $departmentTeams = \App\Models\Team::whereHas('members.user', function($q) {
                    $q->where('department', auth()->user()->department);
                })->count();
                $activeTeams = \App\Models\Team::where('status', 'active')
                    ->whereHas('members.user', function($q) {
                        $q->where('department', auth()->user()->department);
                    })->count();
            @endphp
            <div class="row text-center">
                <div class="col-6">
                    <h4 class="text-primary mb-1">{{ $departmentTeams }}</h4>
                    <small class="text-muted">Total</small>
                </div>
                <div class="col-6">
                    <h4 class="text-success mb-1">{{ $activeTeams }}</h4>
                    <small class="text-muted">Active</small>
                </div>
            </div>
            <a href="{{ route('teams.index') }}" class="btn btn-primary btn-sm w-100 mt-3">View Teams</a>
        </div>
    </div>
</div>

<div class="col-md-3 mb-4">
    <div class="card">
        <div class="card-header">
            <h6 class="card-title mb-0">
                <i class="bi bi-shield-check me-2"></i>Defense Schedule
            </h6>
        </div>
        <div class="card-body">
            @php
                $scheduledDefenses = \App\Models\Defense::where('status', 'scheduled')
                    ->whereHas('project.team.members.user', function($q) {
                        $q->where('department', auth()->user()->department);
                    })->count();
                $upcomingDefenses = \App\Models\Defense::where('defense_date', '>', now())
                    ->where('defense_date', '<=', now()->addWeek())
                    ->whereHas('project.team.members.user', function($q) {
                        $q->where('department', auth()->user()->department);
                    })->count();
            @endphp
            <div class="row text-center">
                <div class="col-6">
                    <h4 class="text-info mb-1">{{ $scheduledDefenses }}</h4>
                    <small class="text-muted">Scheduled</small>
                </div>
                <div class="col-6">
                    <h4 class="text-warning mb-1">{{ $upcomingDefenses }}</h4>
                    <small class="text-muted">This Week</small>
                </div>
            </div>
            <a href="{{ route('defenses.schedule-form') }}" class="btn btn-info btn-sm w-100 mt-3">Schedule Defense</a>
        </div>
    </div>
</div>

<div class="col-md-3 mb-4">
    <div class="card">
        <div class="card-header">
            <h6 class="card-title mb-0">
                <i class="bi bi-exclamation-triangle me-2"></i>Conflicts
            </h6>
        </div>
        <div class="card-body">
            @php
                $activeConflicts = \App\Models\SubjectConflict::where('status', 'pending')
                    ->whereHas('subject.teacher', function($q) {
                        $q->where('department', auth()->user()->department);
                    })->count();
            @endphp
            <div class="text-center">
                <h3 class="text-danger mb-1">{{ $activeConflicts }}</h3>
                <small class="text-muted">Active Conflicts</small>
            </div>
            @if($activeConflicts > 0)
                <a href="{{ route('conflicts.index') }}" class="btn btn-danger btn-sm w-100 mt-3">
                    Resolve Conflicts
                </a>
            @else
                <div class="btn btn-outline-success btn-sm w-100 mt-3 disabled">
                    No Conflicts
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Department Statistics -->
<div class="col-12 mb-4">
    <div class="card">
        <div class="card-header">
            <h6 class="card-title mb-0">
                <i class="bi bi-graph-up me-2"></i>Department Statistics
            </h6>
        </div>
        <div class="card-body">
            @php
                $departmentStudents = \App\Models\User::where('role', 'student')
                    ->where('department', auth()->user()->department)->count();
                $departmentTeachers = \App\Models\User::where('role', 'teacher')
                    ->where('department', auth()->user()->department)->count();
                $departmentSubjects = \App\Models\Subject::whereHas('teacher', function($q) {
                    $q->where('department', auth()->user()->department);
                })->count();
                $completedProjects = \App\Models\Project::where('status', 'completed')
                    ->whereHas('team.members.user', function($q) {
                        $q->where('department', auth()->user()->department);
                    })->count();
            @endphp
            <div class="row text-center">
                <div class="col-md-3">
                    <div class="p-3 border rounded">
                        <h4 class="text-primary mb-1">{{ $departmentStudents }}</h4>
                        <small class="text-muted">Students</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 border rounded">
                        <h4 class="text-success mb-1">{{ $departmentTeachers }}</h4>
                        <small class="text-muted">Teachers</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 border rounded">
                        <h4 class="text-info mb-1">{{ $departmentSubjects }}</h4>
                        <small class="text-muted">Subjects</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 border rounded">
                        <h4 class="text-warning mb-1">{{ $completedProjects }}</h4>
                        <small class="text-muted">Completed</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities -->
<div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <h6 class="card-title mb-0">
                <i class="bi bi-clock-history me-2"></i>Recent Validations
            </h6>
        </div>
        <div class="card-body">
            @php
                $recentValidations = \App\Models\Subject::where('status', 'validated')
                    ->whereHas('teacher', function($q) {
                        $q->where('department', auth()->user()->department);
                    })
                    ->latest('updated_at')
                    ->take(5)
                    ->get();
            @endphp
            <div class="list-group list-group-flush">
                @forelse($recentValidations as $subject)
                    <div class="list-group-item d-flex align-items-center">
                        <i class="bi bi-check-circle text-success me-3"></i>
                        <div>
                            <div class="fw-medium">{{ $subject->title }}</div>
                            <small class="text-muted">by {{ $subject->teacher->name }}</small>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted">
                        No recent validations
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Upcoming Deadlines -->
<div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <h6 class="card-title mb-0">
                <i class="bi bi-calendar-event me-2"></i>Upcoming Events
            </h6>
        </div>
        <div class="card-body">
            @php
                $upcomingEvents = \App\Models\Defense::where('defense_date', '>', now())
                    ->where('defense_date', '<=', now()->addDays(7))
                    ->whereHas('project.team.members.user', function($q) {
                        $q->where('department', auth()->user()->department);
                    })
                    ->orderBy('defense_date')
                    ->take(5)
                    ->get();
            @endphp
            <div class="list-group list-group-flush">
                @forelse($upcomingEvents as $defense)
                    <div class="list-group-item d-flex align-items-center">
                        <i class="bi bi-shield-check text-primary me-3"></i>
                        <div>
                            <div class="fw-medium">{{ $defense->project->team->name }} Defense</div>
                            <small class="text-muted">{{ $defense->defense_date->format('M d, Y H:i') }}</small>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted">
                        No upcoming events
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>