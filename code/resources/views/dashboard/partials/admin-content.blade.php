<!-- Admin Dashboard Content -->
<div class="col-md-3 mb-4">
    <div class="card border-primary">
        <div class="card-header bg-primary text-white">
            <h6 class="card-title mb-0">
                <i class="bi bi-people me-2"></i>{{ __('app.users') }}
            </h6>
        </div>
        <div class="card-body">
            @php
                $totalUsers = \App\Models\User::count();
                $activeUsers = \App\Models\User::where('email_verified_at', '!=', null)->count();
                $newUsersThisMonth = \App\Models\User::where('created_at', '>=', now()->startOfMonth())->count();
            @endphp
            <div class="row text-center">
                <div class="col-12 mb-2">
                    <h3 class="text-primary mb-1">{{ $totalUsers }}</h3>
                    <small class="text-muted">{{ __('app.total_users') }}</small>
                </div>
                <div class="col-6">
                    <h5 class="text-success mb-1">{{ $activeUsers }}</h5>
                    <small class="text-muted">{{ __('app.active') }}</small>
                </div>
                <div class="col-6">
                    <h5 class="text-info mb-1">{{ $newUsersThisMonth }}</h5>
                    <small class="text-muted">{{ __('app.new_this_month') }}</small>
                </div>
            </div>
            <a href="{{ route('admin.users') }}" class="btn btn-primary btn-sm w-100 mt-3">{{ __('app.manage_users') }}</a>
        </div>
    </div>
</div>

<div class="col-md-3 mb-4">
    <div class="card border-success">
        <div class="card-header bg-success text-white">
            <h6 class="card-title mb-0">
                <i class="bi bi-journal-text me-2"></i>{{ __('app.subjects') }}
            </h6>
        </div>
        <div class="card-body">
            @php
                $totalSubjects = \App\Models\Subject::count();
                $validatedSubjects = \App\Models\Subject::where('status', 'validated')->count();
                $pendingSubjects = \App\Models\Subject::where('status', 'pending_validation')->count();
            @endphp
            <div class="row text-center">
                <div class="col-12 mb-2">
                    <h3 class="text-success mb-1">{{ $totalSubjects }}</h3>
                    <small class="text-muted">{{ __('app.total_subjects') }}</small>
                </div>
                <div class="col-6">
                    <h5 class="text-success mb-1">{{ $validatedSubjects }}</h5>
                    <small class="text-muted">{{ __('app.validated') }}</small>
                </div>
                <div class="col-6">
                    <h5 class="text-warning mb-1">{{ $pendingSubjects }}</h5>
                    <small class="text-muted">{{ __('app.pending') }}</small>
                </div>
            </div>
            @if($pendingSubjects > 0)
                <a href="{{ route('admin.subjects.pending') }}" class="btn btn-warning btn-sm w-100 mt-2">
                    <i class="bi bi-clock"></i> {{ __('app.review_pending') }} ({{ $pendingSubjects }})
                </a>
            @endif
            <div class="btn-group w-100 mt-2">
                <a href="{{ route('admin.subjects.all') }}" class="btn btn-success btn-sm">
                    <i class="bi bi-list"></i> {{ __('app.all_subjects') }}
                </a>
                <a href="{{ route('subjects.index') }}" class="btn btn-outline-success btn-sm">
                    <i class="bi bi-eye"></i> {{ __('app.public_view') }}
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

<div class="col-md-3 mb-4">
    <div class="card border-warning">
        <div class="card-header bg-warning text-dark">
            <h6 class="card-title mb-0">
                <i class="bi bi-shield-check me-2"></i>Defenses
            </h6>
        </div>
        <div class="card-body">
            @php
                $totalDefenses = \App\Models\Defense::count();
                $scheduledDefenses = \App\Models\Defense::where('status', 'scheduled')->count();
                $completedDefenses = \App\Models\Defense::where('status', 'completed')->count();
            @endphp
            <div class="row text-center">
                <div class="col-12 mb-2">
                    <h3 class="text-warning mb-1">{{ $totalDefenses }}</h3>
                    <small class="text-muted">Total Defenses</small>
                </div>
                <div class="col-6">
                    <h5 class="text-primary mb-1">{{ $scheduledDefenses }}</h5>
                    <small class="text-muted">Scheduled</small>
                </div>
                <div class="col-6">
                    <h5 class="text-success mb-1">{{ $completedDefenses }}</h5>
                    <small class="text-muted">Completed</small>
                </div>
            </div>
            <a href="{{ route('defenses.index') }}" class="btn btn-warning btn-sm w-100 mt-3">View Defenses</a>
        </div>
    </div>
</div>

<div class="col-md-3 mb-4">
    <div class="card border-secondary">
        <div class="card-header bg-secondary text-white">
            <h6 class="card-title mb-0">
                <i class="bi bi-mortarboard me-2"></i>Specialities
            </h6>
        </div>
        <div class="card-body">
            @php
                $totalSpecialities = \App\Models\Speciality::count();
                $activeSpecialities = \App\Models\Speciality::where('is_active', true)->count();
                $studentsEnrolled = \App\Models\User::where('role', 'student')->whereNotNull('speciality_id')->count();
            @endphp
            <div class="row text-center">
                <div class="col-12 mb-2">
                    <h3 class="text-secondary mb-1">{{ $totalSpecialities }}</h3>
                    <small class="text-muted">Total Specialities</small>
                </div>
                <div class="col-6">
                    <h5 class="text-success mb-1">{{ $activeSpecialities }}</h5>
                    <small class="text-muted">Active</small>
                </div>
                <div class="col-6">
                    <h5 class="text-info mb-1">{{ $studentsEnrolled }}</h5>
                    <small class="text-muted">Students</small>
                </div>
            </div>
            <a href="{{ route('admin.specialities.index') }}" class="btn btn-secondary btn-sm w-100 mt-3">Manage Specialities</a>
        </div>
    </div>
</div>

<!-- Grade Verification Card -->
<div class="col-md-3 mb-4">
    <div class="card border-warning">
        <div class="card-header bg-warning text-white">
            <h6 class="card-title mb-0">
                <i class="bi bi-clipboard-check me-2"></i>Grade Verification
            </h6>
        </div>
        <div class="card-body">
            @php
                $pendingGrades = \App\Models\StudentGrade::where('status', 'pending_verification')->count();
                $verifiedGrades = \App\Models\StudentGrade::where('status', 'verified')->count();
                $totalGrades = \App\Models\StudentGrade::count();
            @endphp
            <div class="row text-center">
                <div class="col-12 mb-2">
                    <h3 class="text-warning mb-1">{{ $pendingGrades }}</h3>
                    <small class="text-muted">Pending Verification</small>
                </div>
                <div class="col-6">
                    <h5 class="text-success mb-1">{{ $verifiedGrades }}</h5>
                    <small class="text-muted">Verified</small>
                </div>
                <div class="col-6">
                    <h5 class="text-info mb-1">{{ $totalGrades }}</h5>
                    <small class="text-muted">Total</small>
                </div>
            </div>
            <a href="{{ route('grades.pending') }}" class="btn btn-warning btn-sm w-100 mt-3">Review Grades</a>
        </div>
    </div>
</div>

<!-- Allocation Management Card -->
<div class="col-md-3 mb-4">
    <div class="card border-info">
        <div class="card-header bg-info text-white">
            <h6 class="card-title mb-0">
                <i class="bi bi-calendar-event me-2"></i>Subject Allocation
            </h6>
        </div>
        <div class="card-body">
            @php
                $activeDeadline = \App\Models\AllocationDeadline::where('status', 'active')->first();
                $totalAllocations = \App\Models\SubjectAllocation::count();
                $confirmedAllocations = \App\Models\SubjectAllocation::where('status', 'confirmed')->count();
            @endphp
            <div class="row text-center">
                <div class="col-12 mb-2">
                    @if($activeDeadline)
                        <h3 class="text-info mb-1">{{ $activeDeadline->deadline->diffForHumans() }}</h3>
                        <small class="text-muted">Next Deadline</small>
                    @else
                        <h3 class="text-muted mb-1">-</h3>
                        <small class="text-muted">No Active Deadline</small>
                    @endif
                </div>
                <div class="col-6">
                    <h5 class="text-success mb-1">{{ $confirmedAllocations }}</h5>
                    <small class="text-muted">Confirmed</small>
                </div>
                <div class="col-6">
                    <h5 class="text-warning mb-1">{{ $totalAllocations - $confirmedAllocations }}</h5>
                    <small class="text-muted">Pending</small>
                </div>
            </div>
            <div class="d-grid gap-2 mt-3">
                <a href="{{ route('allocations.deadlines') }}" class="btn btn-info btn-sm">Manage Deadlines</a>
                <a href="{{ route('allocations.results') }}" class="btn btn-outline-info btn-sm">View Results</a>
            </div>
        </div>
    </div>
</div>

<!-- System Overview Chart -->
<div class="col-md-8 mb-4">
    <div class="card">
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
                    <small class="text-muted">Defenses This Month</small>
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