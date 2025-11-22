<!-- Admin Dashboard Content -->
<!-- Statistics Cards Row -->
<div class="col-12 mb-4">
    <div class="row g-4">
        <!-- Users Card -->
        <div class="col-xl-3 col-lg-6 col-md-6">
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

        <!-- Subjects Card -->
        <div class="col-xl-3 col-lg-6 col-md-6">
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

        <!-- Defenses Card -->
        <div class="col-xl-3 col-lg-6 col-md-6">
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

        <!-- Specialities Card -->
        <div class="col-xl-3 col-lg-6 col-md-6">
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
    </div>
</div>

<!-- Management Cards Row -->


<!-- Analytics and System Overview Row -->


<!-- Activity and Analytics Row -->
