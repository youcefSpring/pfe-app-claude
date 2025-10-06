@extends('layouts.pfe-app')

@section('title', __('app.defenses'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">{{ __('app.defenses') }}</h4>
                    @if(in_array(auth()->user()?->role, ['admin', 'department_head']))
                        <div class="btn-group" role="group">
                            <a href="{{ route('defenses.schedule-form') }}" class="btn btn-primary">
                                <i class="bi bi-calendar-plus me-2"></i>{{ __('app.schedule_defense') }}
                            </a>
                            <a href="{{ route('defenses.calendar') }}" class="btn btn-outline-primary">
                                <i class="bi bi-calendar me-2"></i>{{ __('app.calendar_view') }}
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Search and Filter Section -->
                <div class="card-body border-bottom">
                    <form method="GET" action="{{ route('defenses.index') }}" id="filterForm">
                        <div class="row g-3">
                            <!-- Real-time Search -->
                            <div class="col-md-4">
                                <label for="search" class="form-label">{{ __('app.search') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                    <input type="text"
                                           class="form-control"
                                           id="search"
                                           name="search"
                                           value="{{ request('search') }}"
                                           placeholder="{{ __('app.search_defenses_placeholder') }}">
                                </div>
                            </div>

                            <!-- Status Filter -->
                            <div class="col-md-2">
                                <label for="status" class="form-label">{{ __('app.status') }}</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">{{ __('app.all_statuses') }} ({{ $statusCounts['all'] }})</option>
                                    <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>
                                        {{ __('app.scheduled') }} ({{ $statusCounts['scheduled'] }})
                                    </option>
                                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>
                                        {{ __('app.completed') }} ({{ $statusCounts['completed'] }})
                                    </option>
                                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>
                                        {{ __('app.cancelled') }} ({{ $statusCounts['cancelled'] ?? 0 }})
                                    </option>
                                </select>
                            </div>

                            <!-- Date Range Filters -->
                            <div class="col-md-2">
                                <label for="date_from" class="form-label">{{ __('app.from_date') }}</label>
                                <input type="date" class="form-control" id="date_from" name="date_from"
                                       value="{{ request('date_from') }}">
                            </div>

                            <div class="col-md-2">
                                <label for="date_to" class="form-label">{{ __('app.to_date') }}</label>
                                <input type="date" class="form-control" id="date_to" name="date_to"
                                       value="{{ request('date_to') }}">
                            </div>

                            <!-- Clear Button -->
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <a href="{{ route('defenses.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-clockwise me-1"></i>{{ __('app.clear') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body">
                    @if($defenses->count() > 0)
                        <!-- Results Summary -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="text-muted">
                                {{ __('app.showing_results', [
                                    'from' => $defenses->firstItem() ?? 0,
                                    'to' => $defenses->lastItem() ?? 0,
                                    'total' => $defenses->total()
                                ]) }}
                            </div>
                            <div class="text-muted">
                                {{ __('app.per_page') }}: {{ $defenses->perPage() }}
                            </div>
                        </div>

                        <!-- Defenses Table -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('app.subject') }}</th>
                                        <th>{{ __('app.student_team') }}</th>
                                        <th>{{ __('app.date_time') }}</th>
                                        <th>{{ __('app.room') }}</th>
                                        <th>{{ __('app.jury') }}</th>
                                        <th>{{ __('app.status') }}</th>
                                        <th>{{ __('app.grade') }}</th>
                                        <th>{{ __('app.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($defenses as $defense)
                                        <tr>
                                            <td>
                                                <div>
                                                    <h6 class="mb-1">{{ $defense->subject?->title ?? __('app.no_subject') }}</h6>
                                                    @if($defense->subject?->teacher)
                                                        <small class="text-muted">{{ __('app.supervisor') }}: {{ $defense->subject->teacher->name }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if($defense->project && $defense->project->team && $defense->project->team->members)
                                                    @foreach($defense->project->team->members as $member)
                                                        <div class="d-flex align-items-center mb-1">
                                                            <div class="avatar-circle bg-primary text-white me-2">
                                                                {{ strtoupper(substr($member->user->name, 0, 2)) }}
                                                            </div>
                                                            <div>
                                                                <small class="fw-bold">{{ $member->user->name }}</small>
                                                                @if($member->user->matricule)
                                                                    <br><small class="text-muted">{{ $member->user->matricule }}</small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">{{ __('app.no_team_assigned') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($defense->defense_date)
                                                    <div class="text-center">
                                                        <div class="fw-bold">{{ $defense->defense_date->format('d/m/Y') }}</div>
                                                        @if($defense->defense_time)
                                                            <small class="text-muted">{{ $defense->defense_time->format('H:i') }}</small>
                                                        @endif
                                                        @if($defense->duration)
                                                            <br><small class="badge bg-info">{{ $defense->duration }}min</small>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-muted">{{ __('app.not_scheduled') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($defense->room)
                                                    <span class="badge bg-secondary">{{ $defense->room->name }}</span>
                                                    @if($defense->room->capacity)
                                                        <br><small class="text-muted">{{ $defense->room->capacity }} {{ __('app.seats') }}</small>
                                                    @endif
                                                @else
                                                    <span class="text-muted">{{ __('app.no_room') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($defense->juries && $defense->juries->count() > 0)
                                                    @foreach($defense->juries as $jury)
                                                        <div class="mb-1">
                                                            <small class="fw-bold">{{ $jury->teacher->name }}</small>
                                                            <br>
                                                            @if($jury->role === 'president')
                                                                <span class="badge bg-warning">{{ __('app.president') }}</span>
                                                            @elseif($jury->role === 'examiner')
                                                                <span class="badge bg-info">{{ __('app.examiner') }}</span>
                                                            @elseif($jury->role === 'supervisor')
                                                                <span class="badge bg-success">{{ __('app.supervisor') }}</span>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">{{ __('app.no_jury') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($defense->status === 'scheduled')
                                                    <span class="badge bg-warning">{{ __('app.scheduled') }}</span>
                                                @elseif($defense->status === 'in_progress')
                                                    <span class="badge bg-primary">{{ __('app.in_progress') }}</span>
                                                @elseif($defense->status === 'completed')
                                                    <span class="badge bg-success">{{ __('app.completed') }}</span>
                                                @elseif($defense->status === 'cancelled')
                                                    <span class="badge bg-danger">{{ __('app.cancelled') }}</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($defense->status) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($defense->final_grade)
                                                    <div class="text-center">
                                                        <span class="fw-bold text-primary">{{ number_format($defense->final_grade, 2) }}/20</span>
                                                        @php
                                                            $grade = $defense->final_grade;
                                                            if ($grade >= 18) $mention = __('app.excellent');
                                                            elseif ($grade >= 16) $mention = __('app.very_good');
                                                            elseif ($grade >= 14) $mention = __('app.good');
                                                            elseif ($grade >= 12) $mention = __('app.fairly_good');
                                                            elseif ($grade >= 10) $mention = __('app.passable');
                                                            else $mention = __('app.failed');
                                                        @endphp
                                                        <br><small class="text-muted">{{ $mention }}</small>
                                                    </div>
                                                @else
                                                    <span class="text-muted">{{ __('app.not_graded') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('defenses.show', $defense) }}"
                                                       class="btn btn-sm btn-outline-primary"
                                                       title="{{ __('app.view') }}">
                                                        <i class="bi bi-eye"></i>
                                                    </a>

                                                    @if(in_array(auth()->user()?->role, ['admin', 'department_head']))
                                                        <a href="{{ route('defenses.edit', $defense) }}"
                                                           class="btn btn-sm btn-outline-warning"
                                                           title="{{ __('app.edit') }}">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>

                                                        <!-- PDF Download Button -->
                                                        <a href="{{ route('defenses.download-report-pdf', $defense) }}"
                                                           class="btn btn-sm btn-outline-danger"
                                                           title="{{ __('app.download_pdf_report') }}"
                                                           target="_blank">
                                                            <i class="bi bi-file-pdf"></i>
                                                        </a>
                                                    @endif

                                                    @if(auth()->user()?->role === 'admin')
                                                        <form action="{{ route('defenses.destroy', $defense) }}"
                                                              method="POST"
                                                              class="d-inline"
                                                              onsubmit="return confirm('{{ __('app.confirm_delete_defense') }}')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                    class="btn btn-sm btn-outline-danger"
                                                                    title="{{ __('app.delete') }}">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $defenses->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-calendar-x display-1 text-muted"></i>
                            <h4 class="mt-3">{{ __('app.no_defenses_found') }}</h4>
                            @if(request()->hasAny(['search', 'status', 'date_from', 'date_to']))
                                <p class="text-muted">{{ __('app.try_different_filters') }}</p>
                                <a href="{{ route('defenses.index') }}" class="btn btn-primary">
                                    {{ __('app.show_all_defenses') }}
                                </a>
                            @else
                                <p class="text-muted">{{ __('app.no_defenses_yet') }}</p>
                                @if(in_array(auth()->user()?->role, ['admin', 'department_head']))
                                    <a href="{{ route('defenses.schedule-form') }}" class="btn btn-primary">
                                        <i class="bi bi-calendar-plus me-2"></i>{{ __('app.schedule_first_defense') }}
                                    </a>
                                @endif
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .avatar-circle {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 12px;
    }

    .table tbody tr:hover {
        background-color: var(--bs-gray-50);
    }

    .btn-group .btn {
        border-radius: 0.375rem !important;
    }

    .btn-group .btn + .btn {
        margin-left: 0.25rem;
    }

    #search {
        transition: all 0.3s ease;
    }

    #search:focus {
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    .form-select:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    .pagination .page-link {
        color: #0d6efd;
    }

    .pagination .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    .badge {
        font-size: 0.75em;
    }

    .table td {
        vertical-align: middle;
    }

    .table th {
        border-top: none;
        font-weight: 600;
        background-color: var(--bs-gray-50);
    }
</style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search');
    const statusSelect = document.getElementById('status');
    const dateFromInput = document.getElementById('date_from');
    const dateToInput = document.getElementById('date_to');
    const form = document.getElementById('filterForm');

    let searchTimeout;

    // Real-time search
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            form.submit();
        }, 500); // Wait 500ms after user stops typing
    });

    // Instant filtering on dropdown changes
    statusSelect.addEventListener('change', function() {
        form.submit();
    });

    // Date filter changes
    dateFromInput.addEventListener('change', function() {
        form.submit();
    });

    dateToInput.addEventListener('change', function() {
        form.submit();
    });

    // Show loading state during search
    form.addEventListener('submit', function() {
        const submitBtn = document.querySelector('#filterForm button[type="submit"]');
        if (submitBtn) {
            submitBtn.innerHTML = '<i class="bi bi-arrow-repeat spin me-1"></i>{{ __('app.searching') }}...';
            submitBtn.disabled = true;
        }
    });

    // Add spinning animation for loading
    const style = document.createElement('style');
    style.textContent = `
        .spin {
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    `;
    document.head.appendChild(style);
});
</script>
@endpush