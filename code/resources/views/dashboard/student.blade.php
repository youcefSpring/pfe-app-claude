@extends('layouts.pfe-app')

@section('page-title', __('app.student_dashboard'))

@section('content')
{{-- <div class="row">
    <!-- Welcome Card -->
    <div class="col-12 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title mb-2">Welcome back, {{ auth()->user()->name }}!</h4>
                        <p class="card-text mb-0">
                            Academic Year: {{ now()->format('Y') }}/{{ now()->addYear()->format('Y') }}
                            @if(auth()->user()->department)
                                | Department: {{ auth()->user()->department }}
                            @endif
                        </p>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-mortarboard" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> --}}

<div class="row">
    <!-- Quick Stats -->
    <div class="col-md-3 mb-4">
        <div class="card text-center h-100">
            <div class="card-body">
                <i class="bi bi-journal-text text-primary mb-3" style="font-size: 2.5rem;"></i>
                <h5 class="card-title">{{ __('app.available_subjects') }}</h5>
                <h3 class="text-primary" id="available-subjects">-</h3>
                <small class="text-muted">{{ __('app.ready_for_selection') }}</small>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card text-center h-100">
            <div class="card-body">
                <i class="bi bi-people text-success mb-3" style="font-size: 2.5rem;"></i>
                <h5 class="card-title">{{ __('app.my_team') }}</h5>
                <h3 class="text-success" id="team-status">-</h3>
                <small class="text-muted">{{ __('app.current_status') }}</small>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card text-center h-100">
            <div class="card-body">
                <i class="bi bi-folder text-warning mb-3" style="font-size: 2.5rem;"></i>
                <h5 class="card-title">{{ __('app.project_status') }}</h5>
                <h3 class="text-warning" id="project-status">-</h3>
                <small class="text-muted">{{ __('app.current_phase') }}</small>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card text-center h-100">
            <div class="card-body">
                <i class="bi bi-shield-check text-info mb-3" style="font-size: 2.5rem;"></i>
                <h5 class="card-title">{{ __('app.defense') }}</h5>
                <h3 class="text-info" id="defense-status">-</h3>
                <small class="text-muted">{{ __('app.upcoming') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Current Team Section -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-people me-2"></i>{{ __('app.my_team') }}
                </h5>
                <a href="{{ route('teams.index') }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="card-body" id="team-info">
                <div class="text-center py-4">
                    <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-2">{{ __('app.loading_team_information') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Project Section -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-folder me-2"></i>{{ __('app.current_project') }}
                </h5>
                <a href="{{ route('projects.index') }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="card-body" id="project-info">
                <div class="text-center py-4">
                    <i class="bi bi-folder text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-2">{{ __('app.loading_project_information') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Activity -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-clock-history me-2"></i>{{ __('app.recent_activity') }}
                </h5>
            </div>
            <div class="card-body">
                <div id="recent-activity">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">{{ __('app.loading') }}</span>
                        </div>
                        <p class="text-muted mt-2">{{ __('app.loading_recent_activity') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-lightning me-2"></i>{{ __('app.quick_actions') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('subjects.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-journal-text me-2"></i>{{ __('app.browse_subjects') }}
                    </a>
                    <a href="{{ route('teams.create') }}" class="btn btn-outline-success">
                        <i class="bi bi-people me-2"></i>{{ __('app.create_team') }}
                    </a>
                    <a href="{{ route('projects.index') }}" class="btn btn-outline-warning">
                        <i class="bi bi-folder me-2"></i>{{ __('app.view_projects') }}
                    </a>
                    <a href="{{ route('defenses.index') }}" class="btn btn-outline-info">
                        <i class="bi bi-shield-check me-2"></i>{{ __('app.defense_schedule') }}
                    </a>
                </div>

                <!-- Upcoming Deadlines -->
                <div class="mt-4">
                    <h6 class="text-muted">{{ __('app.upcoming_deadlines') }}</h6>
                    <div id="upcoming-deadlines">
                        <small class="text-muted">{{ __('app.loading_deadlines') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Student Marks Section -->
<div class="row">
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-bar-chart me-2"></i>{{ __('app.my_marks') }}
                </h5>
                <a href="{{ route('marks.index') }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-eye"></i> {{ __('app.view_all') }}
                </a>
            </div>
            <div class="card-body">
                @if($data['userMarks']->count() > 0)
                    <div class="row">
                        @foreach($data['userMarks']->take(6) as $mark)
                            <div class="col-md-4 mb-3">
                                <div class="card border-left-primary h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="mb-0">{{ $mark->subject }}</h6>
                                            <span class="badge bg-{{ $mark->getLetterGrade() === 'A' ? 'success' : ($mark->getLetterGrade() === 'B' ? 'primary' : ($mark->getLetterGrade() === 'C' ? 'warning' : 'danger')) }}">
                                                {{ $mark->getLetterGrade() }}
                                            </span>
                                        </div>

                                        <div class="mb-2">
                                            <div class="d-flex justify-content-between small text-muted mb-1">
                                                <span>{{ __('app.total_percentage') }}</span>
                                                <span>{{ number_format($mark->getTotalPercentage(), 1) }}%</span>
                                            </div>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-{{ $mark->getTotalPercentage() >= 70 ? 'success' : ($mark->getTotalPercentage() >= 50 ? 'warning' : 'danger') }}"
                                                     style="width: {{ $mark->getTotalPercentage() }}%"></div>
                                            </div>
                                        </div>

                                        <div class="row text-center small">
                                            @if($mark->theory_mark !== null)
                                                <div class="col">
                                                    <div class="text-muted">{{ __('app.theory') }}</div>
                                                    <strong>{{ $mark->theory_mark }}/20</strong>
                                                </div>
                                            @endif
                                            @if($mark->practice_mark !== null)
                                                <div class="col">
                                                    <div class="text-muted">{{ __('app.practice') }}</div>
                                                    <strong>{{ $mark->practice_mark }}/20</strong>
                                                </div>
                                            @endif
                                            @if($mark->project_mark !== null)
                                                <div class="col">
                                                    <div class="text-muted">{{ __('app.project') }}</div>
                                                    <strong>{{ $mark->project_mark }}/20</strong>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="mt-2">
                                            <small class="text-muted">
                                                {{ __('app.added_on') }} {{ $mark->created_at->format('d/m/Y') }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($data['userMarks']->count() > 6)
                        <div class="text-center mt-3">
                            <a href="{{ route('marks.index') }}" class="btn btn-outline-primary">
                                {{ __('app.view_all_marks') }} ({{ $data['userMarks']->count() }} {{ __('app.total') }})
                            </a>
                        </div>
                    @endif
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-bar-chart text-muted" style="font-size: 3rem;"></i>
                        <h6 class="mt-3 text-muted">{{ __('app.no_marks_yet') }}</h6>
                        <p class="text-muted">{{ __('app.marks_will_appear_here') }}</p>
                        <a href="{{ route('marks.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus me-2"></i>{{ __('app.add_marks') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Progress Timeline -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-diagram-3 me-2"></i>{{ __('app.pfe_progress_timeline') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Subject Selection -->
                    <div class="col-md-2 text-center">
                        <div class="progress-step" data-step="subject">
                            <div class="step-icon bg-secondary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                <i class="bi bi-journal-text"></i>
                            </div>
                            <h6 class="step-title">{{ __('app.subject_selection') }}</h6>
                            <small class="text-muted">{{ __('app.choose_your_topic') }}</small>
                        </div>
                    </div>

                    <!-- Team Formation -->
                    <div class="col-md-2 text-center">
                        <div class="progress-step" data-step="team">
                            <div class="step-icon bg-secondary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                <i class="bi bi-people"></i>
                            </div>
                            <h6 class="step-title">{{ __('app.team_formation') }}</h6>
                            <small class="text-muted">{{ __('app.build_your_team') }}</small>
                        </div>
                    </div>

                    <!-- Project Assignment -->
                    <div class="col-md-2 text-center">
                        <div class="progress-step" data-step="assignment">
                            <div class="step-icon bg-secondary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                <i class="bi bi-clipboard-check"></i>
                            </div>
                            <h6 class="step-title">{{ __('app.assignment') }}</h6>
                            <small class="text-muted">{{ __('app.get_supervisor') }}</small>
                        </div>
                    </div>

                    <!-- Development -->
                    <div class="col-md-2 text-center">
                        <div class="progress-step" data-step="development">
                            <div class="step-icon bg-secondary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                <i class="bi bi-code-slash"></i>
                            </div>
                            <h6 class="step-title">{{ __('app.development') }}</h6>
                            <small class="text-muted">{{ __('app.work_on_project') }}</small>
                        </div>
                    </div>

                    <!-- Submission -->
                    <div class="col-md-2 text-center">
                        <div class="progress-step" data-step="submission">
                            <div class="step-icon bg-secondary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                <i class="bi bi-file-earmark-arrow-up"></i>
                            </div>
                            <h6 class="step-title">{{ __('app.submission') }}</h6>
                            <small class="text-muted">{{ __('app.submit_deliverables') }}</small>
                        </div>
                    </div>

                    <!-- Defense -->
                    <div class="col-md-2 text-center">
                        <div class="progress-step" data-step="defense">
                            <div class="step-icon bg-secondary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <h6 class="step-title">{{ __('app.defense') }}</h6>
                            <small class="text-muted">{{ __('app.present_project') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadDashboardData();

    function loadDashboardData() {
        // Load dashboard statistics
        loadStats();
        loadTeamInfo();
        loadProjectInfo();
        loadRecentActivity();
        loadUpcomingDeadlines();
        updateProgressTimeline();
    }

    async function loadStats() {
        try {
            const response = await axios.get('/api/reports/dashboard-stats');
            const data = response.data.data;

            document.getElementById('available-subjects').textContent = data.available_subjects || 0;
            document.getElementById('team-status').textContent = data.team_status || 'None';
            document.getElementById('project-status').textContent = data.project_status || 'None';
            document.getElementById('defense-status').textContent = data.defense_status || 'None';
        } catch (error) {
            console.log('Could not load dashboard stats');
        }
    }

    async function loadTeamInfo() {
        try {
            const response = await axios.get('/api/teams');
            const teams = response.data.data;
            const myTeam = teams.find(team => team.members.some(member => member.user_id === {{ auth()->id() }}));

            const container = document.getElementById('team-info');

            if (myTeam) {
                container.innerHTML = `
                    <h6 class="mb-3">${myTeam.name}</h6>
                    <div class="mb-3">
                        <span class="badge status-${myTeam.status}">${myTeam.status.replace('_', ' ').toUpperCase()}</span>
                    </div>
                    <div class="mb-3">
                        <strong>Members:</strong>
                        <ul class="list-unstyled mt-2">
                            ${myTeam.members.map(member => `
                                <li class="d-flex align-items-center mb-1">
                                    <i class="bi bi-person-circle me-2"></i>
                                    ${member.user.name}
                                    ${member.role === 'leader' ? '<span class="badge bg-primary ms-2">Leader</span>' : ''}
                                </li>
                            `).join('')}
                        </ul>
                    </div>
                    ${myTeam.subject ? `
                        <div>
                            <strong>Subject:</strong>
                            <p class="mb-0 mt-1">${myTeam.subject.title}</p>
                        </div>
                    ` : ''}
                `;
            } else {
                container.innerHTML = `
                    <div class="text-center py-3">
                        <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2 mb-3">You're not part of any team yet</p>
                        <a href="${window.location.origin}/teams/create" class="btn btn-primary">
                            <i class="bi bi-plus me-2"></i>Create Team
                        </a>
                    </div>
                `;
            }
        } catch (error) {
            document.getElementById('team-info').innerHTML = `
                <div class="text-center py-3">
                    <p class="text-muted">Could not load team information</p>
                </div>
            `;
        }
    }

    async function loadProjectInfo() {
        try {
            const response = await axios.get('/api/projects');
            const projects = response.data.data;
            const myProject = projects.find(project => project.team.members.some(member => member.user_id === {{ auth()->id() }}));

            const container = document.getElementById('project-info');

            if (myProject) {
                container.innerHTML = `
                    <h6 class="mb-3">${myProject.title || 'Untitled Project'}</h6>
                    <div class="mb-3">
                        <span class="badge status-${myProject.status}">${myProject.status.replace('_', ' ').toUpperCase()}</span>
                    </div>
                    ${myProject.supervisor ? `
                        <div class="mb-3">
                            <strong>Supervisor:</strong>
                            <p class="mb-0">${myProject.supervisor.name}</p>
                        </div>
                    ` : ''}
                    ${myProject.description ? `
                        <div>
                            <strong>Description:</strong>
                            <p class="mb-0 text-muted">${myProject.description.substring(0, 100)}...</p>
                        </div>
                    ` : ''}
                `;
            } else {
                container.innerHTML = `
                    <div class="text-center py-3">
                        <i class="bi bi-folder text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2">No project assigned yet</p>
                    </div>
                `;
            }
        } catch (error) {
            document.getElementById('project-info').innerHTML = `
                <div class="text-center py-3">
                    <p class="text-muted">Could not load project information</p>
                </div>
            `;
        }
    }

    async function loadRecentActivity() {
        try {
            const response = await axios.get('/api/auth/notifications');
            const notifications = response.data.data.data;

            const container = document.getElementById('recent-activity');

            if (notifications.length > 0) {
                container.innerHTML = notifications.slice(0, 5).map(notification => `
                    <div class="d-flex align-items-start mb-3">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                                <i class="bi bi-bell text-primary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">${notification.data.title || 'Notification'}</h6>
                            <p class="mb-1 text-muted small">${notification.data.message || ''}</p>
                            <small class="text-muted">${formatDate(notification.created_at)}</small>
                        </div>
                    </div>
                `).join('');
            } else {
                container.innerHTML = `
                    <div class="text-center py-3">
                        <i class="bi bi-clock-history text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2">No recent activity</p>
                    </div>
                `;
            }
        } catch (error) {
            document.getElementById('recent-activity').innerHTML = `
                <div class="text-center py-3">
                    <p class="text-muted">Could not load recent activity</p>
                </div>
            `;
        }
    }

    function loadUpcomingDeadlines() {
        // Placeholder for upcoming deadlines
        document.getElementById('upcoming-deadlines').innerHTML = `
            <div class="alert alert-warning alert-sm">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <small>No upcoming deadlines</small>
            </div>
        `;
    }

    function updateProgressTimeline() {
        // This would be populated based on current student progress
        const steps = document.querySelectorAll('.progress-step');
        steps.forEach((step, index) => {
            if (index < 2) { // Example: first 2 steps completed
                step.querySelector('.step-icon').classList.remove('bg-secondary');
                step.querySelector('.step-icon').classList.add('bg-success');
            }
        });
    }

    function formatDate(dateString) {
        return new Date(dateString).toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
});
</script>
@endpush
