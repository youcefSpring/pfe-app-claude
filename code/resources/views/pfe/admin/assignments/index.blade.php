@extends('layouts.admin')

@section('title', __('Assignments Management'))
@section('page-title', __('Assignments Management'))

@section('breadcrumbs')
<span class="text-muted">{{ __('Home') }} / {{ __('Administration') }} / {{ __('Assignments') }}</span>
@endsection

@section('content')
<div class="container-fluid p-4">
    <!-- Header Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">{{ __('Assignments Management') }}</h1>
            <p class="text-muted">{{ __('Manage project assignments and team allocations') }}</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('pfe.admin.assignments.auto') }}" class="btn btn-primary">
                <i class="fas fa-magic me-2"></i>{{ __('Auto Assignment') }}
            </a>
            <a href="{{ route('pfe.admin.assignments.manual') }}" class="btn btn-outline-primary">
                <i class="fas fa-hand-paper me-2"></i>{{ __('Manual Assignment') }}
            </a>
        </div>
    </div>

    <!-- Assignment Methods -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-robot fa-3x text-primary mb-3"></i>
                    <h5>{{ __('Automatic Assignment') }}</h5>
                    <p class="text-muted">{{ __('Let the system automatically assign projects based on preferences and criteria') }}</p>
                    <a href="{{ route('pfe.admin.assignments.auto') }}" class="btn btn-primary">
                        {{ __('Configure Auto Assignment') }}
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-user-edit fa-3x text-success mb-3"></i>
                    <h5>{{ __('Manual Assignment') }}</h5>
                    <p class="text-muted">{{ __('Manually assign teams to projects with full control over the process') }}</p>
                    <a href="{{ route('pfe.admin.assignments.manual') }}" class="btn btn-success">
                        {{ __('Manual Assignment Tool') }}
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-chart-bar fa-3x text-info mb-3"></i>
                    <h5>{{ __('Assignment Results') }}</h5>
                    <p class="text-muted">{{ __('View and analyze the results of previous assignment processes') }}</p>
                    <a href="{{ route('pfe.admin.assignments.results') }}" class="btn btn-info">
                        {{ __('View Results') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Assignment Status -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Assignment Overview') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3 class="text-primary">{{ $stats['total_teams'] ?? 24 }}</h3>
                                <small class="text-muted">{{ __('Total Teams') }}</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3 class="text-success">{{ $stats['assigned_teams'] ?? 18 }}</h3>
                                <small class="text-muted">{{ __('Assigned') }}</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3 class="text-warning">{{ $stats['pending_teams'] ?? 6 }}</h3>
                                <small class="text-muted">{{ __('Pending') }}</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3 class="text-info">{{ $stats['available_projects'] ?? 12 }}</h3>
                                <small class="text-muted">{{ __('Available Projects') }}</small>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small">{{ __('Assignment Progress') }}</span>
                            <span class="small">{{ $stats['assignment_percentage'] ?? 75 }}%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-success" role="progressbar"
                                 style="width: {{ $stats['assignment_percentage'] ?? 75 }}%"></div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary btn-sm" onclick="balanceWorkload()">
                            <i class="fas fa-balance-scale me-1"></i>{{ __('Balance Workload') }}
                        </button>
                        <button class="btn btn-outline-warning btn-sm" onclick="regenerateAssignments()">
                            <i class="fas fa-redo me-1"></i>{{ __('Regenerate') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">{{ __('Quick Actions') }}</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary" onclick="exportAssignments()">
                            <i class="fas fa-download me-2"></i>{{ __('Export Assignments') }}
                        </button>
                        <button class="btn btn-outline-success" onclick="sendNotifications()">
                            <i class="fas fa-envelope me-2"></i>{{ __('Send Notifications') }}
                        </button>
                        <button class="btn btn-outline-info" onclick="generateReport()">
                            <i class="fas fa-chart-line me-2"></i>{{ __('Generate Report') }}
                        </button>
                        <button class="btn btn-outline-warning" onclick="viewConflicts()">
                            <i class="fas fa-exclamation-triangle me-2"></i>{{ __('View Conflicts') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Assignments -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('Recent Assignments') }}</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('Team') }}</th>
                            <th>{{ __('Project') }}</th>
                            <th>{{ __('Supervisor') }}</th>
                            <th>{{ __('Assignment Method') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th class="text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assignments ?? [] as $assignment)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-users text-primary me-2"></i>
                                    <span class="fw-medium">{{ $assignment->team_name ?? 'Team Alpha' }}</span>
                                </div>
                            </td>
                            <td>{{ $assignment->project_title ?? 'AI-Based Student Management System' }}</td>
                            <td>{{ $assignment->supervisor ?? 'Dr. Ahmed Mohamed' }}</td>
                            <td>
                                <span class="badge bg-{{ ($assignment->method ?? 'auto') == 'auto' ? 'primary' : 'success' }}">
                                    {{ $assignment->method == 'auto' ? __('Automatic') : __('Manual') }}
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $assignment->assigned_at ? $assignment->assigned_at->format('M d, Y') : 'Oct 15, 2024' }}
                                </small>
                            </td>
                            <td>
                                <span class="badge bg-{{ ($assignment->status ?? 'active') == 'active' ? 'success' : 'warning' }}">
                                    {{ __(ucfirst($assignment->status ?? 'active')) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-info"
                                            title="{{ __('View Details') }}" onclick="viewAssignment({{ $assignment->id ?? 1 }})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-warning"
                                            title="{{ __('Reassign') }}" onclick="reassignTeam({{ $assignment->id ?? 1 }})">
                                        <i class="fas fa-exchange-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-tasks text-muted mb-3" style="font-size: 3rem;"></i>
                                <p class="text-muted">{{ __('No assignments found') }}</p>
                                <a href="{{ route('pfe.admin.assignments.auto') }}" class="btn btn-primary">
                                    {{ __('Start Auto Assignment') }}
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function balanceWorkload() {
    if (confirm('{{ __("Balance teacher workloads across all assignments?") }}')) {
        fetch('{{ route("pfe.admin.assignments.balance-workload") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('{{ __("Workload balanced successfully") }}');
                location.reload();
            } else {
                alert('{{ __("Error balancing workload") }}');
            }
        });
    }
}

function regenerateAssignments() {
    if (confirm('{{ __("Regenerate all assignments? This will reset current assignments.") }}')) {
        window.location.href = '{{ route("pfe.admin.assignments.auto") }}';
    }
}

function exportAssignments() {
    window.open('/pfe/admin/assignments/export', '_blank');
}

function sendNotifications() {
    if (confirm('{{ __("Send assignment notifications to all teams and supervisors?") }}')) {
        fetch('/pfe/admin/assignments/notify', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('{{ __("Notifications sent successfully") }}');
            } else {
                alert('{{ __("Error sending notifications") }}');
            }
        });
    }
}

function generateReport() {
    window.open('/pfe/admin/assignments/report', '_blank');
}

function viewConflicts() {
    window.location.href = '{{ route("pfe.admin.conflicts.index") }}';
}

function viewAssignment(assignmentId) {
    // Open assignment details modal or page
    window.location.href = `/pfe/admin/assignments/${assignmentId}`;
}

function reassignTeam(assignmentId) {
    if (confirm('{{ __("Reassign this team to a different project?") }}')) {
        window.location.href = `/pfe/admin/assignments/${assignmentId}/reassign`;
    }
}
</script>
@endpush