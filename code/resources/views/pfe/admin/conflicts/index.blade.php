@extends('layouts.admin')

@section('title', __('Conflicts Management'))
@section('page-title', __('Conflicts Management'))

@section('breadcrumbs')
<span class="text-muted">{{ __('Home') }} / {{ __('Administration') }} / {{ __('Conflicts') }}</span>
@endsection

@section('content')
<div class="container-fluid p-4">
    <!-- Header Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">{{ __('Conflicts Management') }}</h1>
            <p class="text-muted">{{ __('Resolve conflicts and assignment issues') }}</p>
        </div>
        <div>
            <button class="btn btn-warning" onclick="autoResolveConflicts()">
                <i class="fas fa-magic me-2"></i>{{ __('Auto Resolve') }}
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                        <div>
                            <h4 class="mb-0">{{ $stats['active_conflicts'] ?? 5 }}</h4>
                            <small>{{ __('Active Conflicts') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-clock fa-2x me-3"></i>
                        <div>
                            <h4 class="mb-0">{{ $stats['pending_resolution'] ?? 3 }}</h4>
                            <small>{{ __('Pending Resolution') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle fa-2x me-3"></i>
                        <div>
                            <h4 class="mb-0">{{ $stats['resolved_conflicts'] ?? 12 }}</h4>
                            <small>{{ __('Resolved') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-users fa-2x me-3"></i>
                        <div>
                            <h4 class="mb-0">{{ $stats['affected_teams'] ?? 8 }}</h4>
                            <small>{{ __('Affected Teams') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Conflicts List -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('Active Conflicts') }}</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('Conflict Type') }}</th>
                            <th>{{ __('Teams/Users Involved') }}</th>
                            <th>{{ __('Subject/Resource') }}</th>
                            <th>{{ __('Priority') }}</th>
                            <th>{{ __('Created') }}</th>
                            <th class="text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($conflicts ?? [] as $conflict)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-{{ $conflict->icon ?? 'exclamation-triangle' }} text-warning me-2"></i>
                                    <span>{{ $conflict->type ?? 'Subject Assignment Conflict' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="small">
                                    {{ $conflict->teams ?? 'Team Alpha, Team Beta' }}
                                </div>
                            </td>
                            <td>
                                <span class="fw-medium">{{ $conflict->subject ?? 'AI-Based Student Management System' }}</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ ($conflict->priority ?? 'high') == 'high' ? 'danger' : (($conflict->priority ?? 'medium') == 'medium' ? 'warning' : 'info') }}">
                                    {{ __(ucfirst($conflict->priority ?? 'high')) }}
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $conflict->created_at ? $conflict->created_at->diffForHumans() : '2 hours ago' }}
                                </small>
                            </td>
                            <td class="text-end">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('pfe.admin.conflicts.show', $conflict->id ?? 1) }}"
                                       class="btn btn-sm btn-outline-info" title="{{ __('View Details') }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-success"
                                            title="{{ __('Resolve') }}" onclick="resolveConflict({{ $conflict->id ?? 1 }})">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <i class="fas fa-handshake text-success mb-3" style="font-size: 3rem;"></i>
                                <h5 class="text-success">{{ __('No Active Conflicts') }}</h5>
                                <p class="text-muted">{{ __('All assignments are running smoothly!') }}</p>
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
function autoResolveConflicts() {
    if (confirm('{{ __("Automatically resolve all conflicts using the system algorithm?") }}')) {
        fetch('{{ route("pfe.admin.conflicts.auto-resolve") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('{{ __("Error resolving conflicts") }}');
            }
        });
    }
}

function resolveConflict(conflictId) {
    if (confirm('{{ __("Mark this conflict as resolved?") }}')) {
        fetch(`/pfe/admin/conflicts/${conflictId}/resolve`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('{{ __("Error resolving conflict") }}');
            }
        });
    }
}
</script>
@endpush