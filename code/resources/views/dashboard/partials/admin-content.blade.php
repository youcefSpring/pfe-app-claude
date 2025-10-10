<!-- Admin Dashboard Content -->
<div class="col-lg-3 col-md-6 mb-4">
    <div class="card h-100 border-0 shadow-sm dashboard-card" id="users-card">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="flex-shrink-0">
                    <div class="icon-circle bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div class="text-end">
                    @php
                        $totalUsers = \App\Models\User::count();
                        $activeUsers = \App\Models\User::where('email_verified_at', '!=', null)->count();
                        $newUsersThisMonth = \App\Models\User::where('created_at', '>=', now()->startOfMonth())->count();
                    @endphp
                    <h2 class="fw-bold text-dark mb-0">{{ $totalUsers }}</h2>
                    <p class="text-muted mb-0 small">{{ __('app.total_users') }}</p>
                </div>
            </div>
            <div class="row g-2 mb-3">
                <div class="col-6">
                    <div class="text-center p-2 bg-success bg-opacity-10 rounded">
                        <div class="fw-bold text-success">{{ $activeUsers }}</div>
                        <small class="text-muted">{{ __('app.active') }}</small>
                    </div>
                </div>
                <div class="col-6">
                    <div class="text-center p-2 bg-info bg-opacity-10 rounded">
                        <div class="fw-bold text-info">{{ $newUsersThisMonth }}</div>
                        <small class="text-muted">{{ __('app.new_this_month') }}</small>
                    </div>
                </div>
            </div>
            <a href="{{ route('admin.users') }}" class="btn btn-primary btn-sm w-100">
                <i class="fas fa-arrow-right me-1"></i>{{ __('app.manage_users') }}
            </a>
        </div>
    </div>
</div>

<div class="col-lg-3 col-md-6 mb-4">
    <div class="card h-100 border-0 shadow-sm dashboard-card" id="subjects-card">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="flex-shrink-0">
                    <div class="icon-circle bg-success bg-opacity-10 text-success">
                        <i class="fas fa-book"></i>
                    </div>
                </div>
                <div class="text-end">
                    @php
                        $totalSubjects = \App\Models\Subject::count();
                        $validatedSubjects = \App\Models\Subject::where('status', 'validated')->count();
                        $pendingSubjects = \App\Models\Subject::where('status', 'pending_validation')->count();
                    @endphp
                    <h2 class="fw-bold text-dark mb-0">{{ $totalSubjects }}</h2>
                    <p class="text-muted mb-0 small">{{ __('app.total_subjects') }}</p>
                </div>
            </div>
            <div class="row g-2 mb-3">
                <div class="col-6">
                    <div class="text-center p-2 bg-success bg-opacity-10 rounded">
                        <div class="fw-bold text-success">{{ $validatedSubjects }}</div>
                        <small class="text-muted">{{ __('app.validated') }}</small>
                    </div>
                </div>
                <div class="col-6">
                    <div class="text-center p-2 bg-warning bg-opacity-10 rounded">
                        <div class="fw-bold text-warning">{{ $pendingSubjects }}</div>
                        <small class="text-muted">{{ __('app.pending') }}</small>
                    </div>
                </div>
            </div>
            @if($pendingSubjects > 0)
                <a href="{{ route('admin.subjects.pending') }}" class="btn btn-warning btn-sm w-100 mb-2">
                    <i class="fas fa-clock me-1"></i>{{ __('app.review_pending') }} ({{ $pendingSubjects }})
                </a>
            @endif
            <div class="d-grid gap-1">
                <a href="{{ route('admin.subjects.all') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-list me-1"></i>{{ __('app.all_subjects') }}
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Projects section hidden as requested --}}
{{--
<div class="col-md-3 mb-4">
    <div class="card border-info">
        <div class="card-header bg-info text-white">
            <h6 class="card-title mb-0">
                <i class="bi bi-folder me-2"></i>Projects
            </h6>
        </div>
        <div class="card-body">
            @php
                $totalProjects = \App\Models\Project::count();
                $activeProjects = \App\Models\Project::where('status', 'active')->count();
                $completedProjects = \App\Models\Project::where('status', 'completed')->count();
            @endphp
            <div class="row text-center">
                <div class="col-12 mb-2">
                    <h3 class="text-info mb-1">{{ $totalProjects }}</h3>
                    <small class="text-muted">Total Projects</small>
                </div>
                <div class="col-6">
                    <h5 class="text-warning mb-1">{{ $activeProjects }}</h5>
                    <small class="text-muted">Active</small>
                </div>
                <div class="col-6">
                    <h5 class="text-success mb-1">{{ $completedProjects }}</h5>
                    <small class="text-muted">Completed</small>
                </div>
            </div>
            <a href="{{ route('projects.index') }}" class="btn btn-info btn-sm w-100 mt-3">View Projects</a>
        </div>
    </div>
</div>
--}}

<div class="col-lg-3 col-md-6 mb-4">
    <div class="card h-100 border-0 shadow-sm dashboard-card" id="defenses-card">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="flex-shrink-0">
                    <div class="icon-circle bg-warning bg-opacity-10 text-warning">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                </div>
                <div class="text-end">
                    @php
                        $totalDefenses = \App\Models\Defense::count();
                        $scheduledDefenses = \App\Models\Defense::where('status', 'scheduled')->count();
                        $completedDefenses = \App\Models\Defense::where('status', 'completed')->count();
                    @endphp
                    <h2 class="fw-bold text-dark mb-0">{{ $totalDefenses }}</h2>
                    <p class="text-muted mb-0 small">{{ __('app.total_defenses') }}</p>
                </div>
            </div>
            <div class="row g-2 mb-3">
                <div class="col-6">
                    <div class="text-center p-2 bg-primary bg-opacity-10 rounded">
                        <div class="fw-bold text-primary">{{ $scheduledDefenses }}</div>
                        <small class="text-muted">{{ __('app.scheduled') }}</small>
                    </div>
                </div>
                <div class="col-6">
                    <div class="text-center p-2 bg-success bg-opacity-10 rounded">
                        <div class="fw-bold text-success">{{ $completedDefenses }}</div>
                        <small class="text-muted">{{ __('app.completed') }}</small>
                    </div>
                </div>
            </div>
            <a href="{{ route('defenses.index') }}" class="btn btn-warning btn-sm w-100">
                <i class="fas fa-arrow-right me-1"></i>{{ __('app.view_defenses') }}
            </a>
        </div>
    </div>
</div>

<div class="col-lg-3 col-md-6 mb-4">
    <div class="card h-100 border-0 shadow-sm dashboard-card" id="specialities-card">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="flex-shrink-0">
                    <div class="icon-circle bg-secondary bg-opacity-10 text-secondary">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                </div>
                <div class="text-end">
                    @php
                        $totalSpecialities = \App\Models\Speciality::count();
                        $activeSpecialities = \App\Models\Speciality::where('is_active', true)->count();
                        $studentsEnrolled = \App\Models\User::where('role', 'student')->whereNotNull('speciality_id')->count();
                    @endphp
                    <h2 class="fw-bold text-dark mb-0">{{ $totalSpecialities }}</h2>
                    <p class="text-muted mb-0 small">{{ __('app.total_specialities') }}</p>
                </div>
            </div>
            <div class="row g-2 mb-3">
                <div class="col-6">
                    <div class="text-center p-2 bg-success bg-opacity-10 rounded">
                        <div class="fw-bold text-success">{{ $activeSpecialities }}</div>
                        <small class="text-muted">{{ __('app.active') }}</small>
                    </div>
                </div>
                <div class="col-6">
                    <div class="text-center p-2 bg-info bg-opacity-10 rounded">
                        <div class="fw-bold text-info">{{ $studentsEnrolled }}</div>
                        <small class="text-muted">{{ __('app.students') }}</small>
                    </div>
                </div>
            </div>
            <a href="{{ route('admin.specialities.index') }}" class="btn btn-secondary btn-sm w-100">
                <i class="fas fa-arrow-right me-1"></i>{{ __('app.manage_specialities') }}
            </a>
        </div>
    </div>
</div>

<!-- Grade Verification Card -->
<div class="col-lg-3 col-md-6 mb-4">
    <div class="card h-100 border-0 shadow-sm dashboard-card" id="grades-card">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="flex-shrink-0">
                    <div class="icon-circle bg-warning bg-opacity-10 text-warning">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                </div>
                <div class="text-end">
                    @php
                        $pendingGrades = \App\Models\StudentGrade::where('status', 'pending_verification')->count();
                        $verifiedGrades = \App\Models\StudentGrade::where('status', 'verified')->count();
                        $totalGrades = \App\Models\StudentGrade::count();
                    @endphp
                    <h2 class="fw-bold text-dark mb-0">{{ $pendingGrades }}</h2>
                    <p class="text-muted mb-0 small">{{ __('app.pending_verification') }}</p>
                </div>
            </div>
            <div class="row g-2 mb-3">
                <div class="col-6">
                    <div class="text-center p-2 bg-success bg-opacity-10 rounded">
                        <div class="fw-bold text-success">{{ $verifiedGrades }}</div>
                        <small class="text-muted">{{ __('app.verified') }}</small>
                    </div>
                </div>
                <div class="col-6">
                    <div class="text-center p-2 bg-info bg-opacity-10 rounded">
                        <div class="fw-bold text-info">{{ $totalGrades }}</div>
                        <small class="text-muted">{{ __('app.total') }}</small>
                    </div>
                </div>
            </div>
            <a href="{{ route('grades.pending') }}" class="btn btn-warning btn-sm w-100">
                <i class="fas fa-clock me-1"></i>{{ __('app.review_grades') }}
            </a>
        </div>
    </div>
</div>

<!-- Allocation Management Card -->
<div class="col-lg-3 col-md-6 mb-4">
    <div class="card h-100 border-0 shadow-sm dashboard-card" id="allocations-card">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="flex-shrink-0">
                    <div class="icon-circle bg-info bg-opacity-10 text-info">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
                <div class="text-end">
                    @php
                        $activeDeadline = \App\Models\AllocationDeadline::where('status', 'active')->first();
                        $totalAllocations = \App\Models\SubjectAllocation::count();
                        $confirmedAllocations = \App\Models\SubjectAllocation::where('status', 'confirmed')->count();
                    @endphp
                    @if($activeDeadline)
                        <h2 class="fw-bold text-dark mb-0">{{ $activeDeadline->deadline->diffInDays() }}</h2>
                        <p class="text-muted mb-0 small">{{ __('app.days_until_deadline') }}</p>
                    @else
                        <h2 class="fw-bold text-muted mb-0">-</h2>
                        <p class="text-muted mb-0 small">{{ __('app.no_active_deadline') }}</p>
                    @endif
                </div>
            </div>
            <div class="row g-2 mb-3">
                <div class="col-6">
                    <div class="text-center p-2 bg-success bg-opacity-10 rounded">
                        <div class="fw-bold text-success">{{ $confirmedAllocations }}</div>
                        <small class="text-muted">{{ __('app.confirmed') }}</small>
                    </div>
                </div>
                <div class="col-6">
                    <div class="text-center p-2 bg-warning bg-opacity-10 rounded">
                        <div class="fw-bold text-warning">{{ $totalAllocations - $confirmedAllocations }}</div>
                        <small class="text-muted">{{ __('app.pending') }}</small>
                    </div>
                </div>
            </div>
            <div class="d-grid gap-1">
                <a href="{{ route('allocations.deadlines') }}" class="btn btn-info btn-sm">
                    <i class="fas fa-calendar-plus me-1"></i>{{ __('app.manage_deadlines') }}
                </a>
                <a href="{{ route('allocations.results') }}" class="btn btn-outline-info btn-sm">
                    <i class="fas fa-list me-1"></i>{{ __('app.view_results') }}
                </a>
            </div>
        </div>
    </div>
</div>

<!-- System Overview Chart -->
<div class="col-md-8 mb-4">
    <div class="card" id="system-overview-card">
        <div class="card-header">
            <h6 class="card-title mb-0">
                <i class="bi bi-graph-up me-2"></i>System Overview
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-muted">By Department</h6>
                    @php
                        $departments = \App\Models\User::select('department', \DB::raw('count(*) as count'))
                            ->where('role', 'student')
                            ->whereNotNull('department')
                            ->groupBy('department')
                            ->get();
                    @endphp
                    @foreach($departments as $dept)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>{{ $dept->department }}</span>
                            <div class="progress flex-grow-1 mx-3" style="height: 20px;">
                                <div class="progress-bar" role="progressbar"
                                     style="width: {{ ($dept->count / $totalUsers) * 100 }}%">
                                    {{ $dept->count }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted">By Role</h6>
                    @php
                        $roles = \App\Models\User::select('role', \DB::raw('count(*) as count'))
                            ->groupBy('role')
                            ->get();
                    @endphp
                    @foreach($roles as $role)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>{{ ucfirst(str_replace('_', ' ', $role->role)) }}</span>
                            <div class="progress flex-grow-1 mx-3" style="height: 20px;">
                                <div class="progress-bar bg-{{ $role->role === 'admin' ? 'danger' : ($role->role === 'teacher' ? 'success' : 'primary') }}"
                                     role="progressbar"
                                     style="width: {{ ($role->count / $totalUsers) * 100 }}%">
                                    {{ $role->count }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- System Health -->
<div class="col-md-4 mb-4">
    <div class="card">
        <div class="card-header">
            <h6 class="card-title mb-0">
                <i class="bi bi-heart-pulse me-2"></i>System Health
            </h6>
        </div>
        <div class="card-body">
            <div class="list-group list-group-flush">
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span>Database</span>
                    <span class="badge bg-success">Online</span>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span>File Storage</span>
                    <span class="badge bg-success">Healthy</span>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span>Email Service</span>
                    <span class="badge bg-success">Active</span>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span>Backups</span>
                    <span class="badge bg-warning">Manual</span>
                </div>
            </div>
            <a href="{{ route('admin.maintenance') }}" class="btn btn-outline-secondary btn-sm w-100 mt-3">
                System Maintenance
            </a>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <h6 class="card-title mb-0">
                <i class="bi bi-clock-history me-2"></i>Recent System Activity
            </h6>
        </div>
        <div class="card-body">
            <div class="list-group list-group-flush">
                @php
                    $recentUsers = \App\Models\User::latest()->take(3)->get();
                    $recentSubjects = \App\Models\Subject::latest()->take(2)->get();
                @endphp

                @foreach($recentUsers as $user)
                    <div class="list-group-item d-flex align-items-center">
                        <i class="bi bi-person-plus text-primary me-3"></i>
                        <div>
                            <div class="fw-medium">New user: {{ $user->name }}</div>
                            <small class="text-muted">{{ $user->role }} - {{ $user->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                @endforeach

                @foreach($recentSubjects as $subject)
                    <div class="list-group-item d-flex align-items-center">
                        <i class="bi bi-journal-plus text-success me-3"></i>
                        <div>
                            <div class="fw-medium">New subject: {{ Str::limit($subject->title, 30) }}</div>
                            <small class="text-muted">by {{ $subject->teacher->name }} - {{ $subject->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <h6 class="card-title mb-0">
                <i class="bi bi-speedometer me-2"></i>Quick Statistics
            </h6>
        </div>
        <div class="card-body">
            @php
                $todayLogins = \App\Models\User::where('last_login_at', '>=', now()->startOfDay())->count();
                // $thisWeekProjects = \App\Models\Project::where('created_at', '>=', now()->startOfWeek())->count(); // Hidden projects
                $thisMonthDefenses = \App\Models\Defense::where('defense_date', '>=', now()->startOfMonth())->count();
                $thisWeekSubjects = \App\Models\Subject::where('created_at', '>=', now()->startOfWeek())->count();
            @endphp

            <div class="row text-center">
                <div class="col-4">
                    <div class="border-end">
                        <h4 class="text-primary mb-1">{{ $todayLogins }}</h4>
                        <small class="text-muted">Today's Logins</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="border-end">
                        <h4 class="text-success mb-1">{{ $thisWeekSubjects }}</h4>
                        <small class="text-muted">Subjects This Week</small>
                    </div>
                </div>
                <div class="col-4">
                    <h4 class="text-warning mb-1">{{ $thisMonthDefenses }}</h4>
                    <small class="text-muted">{{ __('app.defenses_this_month') }}</small>
                </div>
            </div>

            <hr>

            <div class="d-grid gap-2">
                <a href="{{ route('admin.reports') }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-graph-up me-2"></i>View Detailed Reports
                </a>
                <a href="{{ route('admin.analytics') }}" class="btn btn-outline-info btn-sm">
                    <i class="bi bi-pie-chart me-2"></i>Analytics Dashboard
                </a>
            </div>
        </div>
    </div>
</div>