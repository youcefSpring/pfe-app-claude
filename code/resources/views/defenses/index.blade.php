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
                                        {{ __('app.defense_status_scheduled') }} ({{ $statusCounts['scheduled'] }})
                                    </option>
                                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>
                                        {{ __('app.defense_status_in_progress') }} ({{ $statusCounts['in_progress'] ?? 0 }})
                                    </option>
                                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>
                                        {{ __('app.defense_status_completed') }} ({{ $statusCounts['completed'] }})
                                    </option>
                                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>
                                        {{ __('app.defense_status_cancelled') }} ({{ $statusCounts['cancelled'] ?? 0 }})
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
                        <!-- Results Summary and View Toggle -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="text-muted">
                                {{ __('app.showing_results', [
                                    'from' => $defenses->firstItem() ?? 0,
                                    'to' => $defenses->lastItem() ?? 0,
                                    'total' => $defenses->total()
                                ]) }}
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <!-- View Toggle -->
                                <div class="btn-group" role="group" aria-label="View toggle">
                                    <button type="button" class="btn btn-outline-secondary btn-sm active" id="cardViewBtn">
                                        <i class="bi bi-grid-3x3-gap me-1"></i>{{ __('app.cards') }}
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="tableViewBtn">
                                        <i class="bi bi-table me-1"></i>{{ __('app.table') }}
                                    </button>
                                </div>
                                <div class="text-muted">
                                    {{ __('app.per_page') }}: {{ $defenses->perPage() }}
                                </div>
                            </div>
                        </div>

                        <!-- Defenses Cards -->
                        <div class="row g-3" id="cardView">
                            @foreach($defenses as $defense)
                                <div class="col-lg-6 col-xl-4">
                                    <div class="card defense-card h-100 border-0 shadow-sm">
                                        <!-- Status Header -->
                                        <div class="card-header border-0 pb-0">
                                            <div class="d-flex justify-content-between align-items-start">
                                                @if($defense->status === 'scheduled')
                                                    <span class="badge bg-warning text-dark fs-6">
                                                        <i class="bi bi-calendar-event me-1"></i>{{ __('app.defense_status_scheduled') }}
                                                    </span>
                                                @elseif($defense->status === 'in_progress')
                                                    <span class="badge bg-primary fs-6">
                                                        <i class="bi bi-play-circle me-1"></i>{{ __('app.defense_status_in_progress') }}
                                                    </span>
                                                @elseif($defense->status === 'completed')
                                                    <span class="badge bg-success fs-6">
                                                        <i class="bi bi-check-circle me-1"></i>{{ __('app.defense_status_completed') }}
                                                    </span>
                                                @elseif($defense->status === 'cancelled')
                                                    <span class="badge bg-danger fs-6">
                                                        <i class="bi bi-x-circle me-1"></i>{{ __('app.defense_status_cancelled') }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary fs-6">{{ ucfirst($defense->status) }}</span>
                                                @endif

                                                <!-- Grade Display -->
                                                @if($defense->final_grade)
                                                    <div class="text-end">
                                                        <div class="grade-display">
                                                            <span class="fw-bold text-primary fs-5">{{ number_format($defense->final_grade, 1) }}</span>
                                                            <small class="text-muted">/20</small>
                                                        </div>
                                                        @php
                                                            $grade = $defense->final_grade;
                                                            if ($grade >= 18) $mention = __('app.excellent');
                                                            elseif ($grade >= 16) $mention = __('app.very_good');
                                                            elseif ($grade >= 14) $mention = __('app.good');
                                                            elseif ($grade >= 12) $mention = __('app.fairly_good');
                                                            elseif ($grade >= 10) $mention = __('app.passable');
                                                            else $mention = __('app.failed');
                                                        @endphp
                                                        <small class="text-muted d-block">{{ $mention }}</small>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="card-body">
                                            <!-- Subject Title -->
                                            <h5 class="card-title mb-2 text-truncate" title="{{ $defense->subject?->title ?? __('app.no_subject') }}">
                                                {{ $defense->subject?->title ?? __('app.no_subject') }}
                                            </h5>

                                            <!-- Date & Time -->
                                            @if($defense->defense_date)
                                                <div class="d-flex align-items-center mb-2 text-primary">
                                                    <i class="bi bi-calendar3 me-2"></i>
                                                    <strong>{{ $defense->defense_date ? \Carbon\Carbon::parse($defense->defense_date)->format('d/m/Y') : 'TBD' }}</strong>
                                                    @if($defense->defense_time)
                                                        <span class="mx-2">•</span>
                                                        <strong>{{ $defense->defense_time ? \Carbon\Carbon::parse($defense->defense_time)->format('H:i') : '' }}</strong>
                                                    @endif
                                                    @if($defense->duration)
                                                        <span class="badge bg-info ms-2">{{ $defense->duration }}min</span>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="d-flex align-items-center mb-2 text-muted">
                                                    <i class="bi bi-calendar-x me-2"></i>
                                                    <span>{{ __('app.not_scheduled') }}</span>
                                                </div>
                                            @endif

                                            <!-- Room -->
                                            @if($defense->room)
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="bi bi-geo-alt me-2 text-muted"></i>
                                                    <span class="fw-semibold">{{ $defense->room->name }}</span>
                                                    @if($defense->room->capacity)
                                                        <small class="text-muted ms-1">({{ $defense->room->capacity }} places)</small>
                                                    @endif
                                                </div>
                                            @endif

                                            <!-- Supervisor -->
                                            @if($defense->subject?->teacher)
                                                <div class="d-flex align-items-center mb-3">
                                                    <i class="bi bi-person-badge me-2 text-muted"></i>
                                                    <small class="text-muted">{{ __('app.supervisor') }}: </small>
                                                    <span class="ms-1">{{ $defense->subject->teacher->name }}</span>
                                                </div>
                                            @endif

                                            <!-- Team Members -->
                                            @if($defense->project && $defense->project->team && $defense->project->team->members)
                                                <div class="mb-3">
                                                    <small class="text-muted fw-semibold">{{ __('app.team_members') }}:</small>
                                                    <div class="d-flex flex-wrap gap-2 mt-1">
                                                        @foreach($defense->project->team->members as $member)
                                                            <div class="student-tag">
                                                                <div class="avatar-sm me-2">
                                                                    {{ strtoupper(substr($member->user->name, 0, 1)) }}
                                                                </div>
                                                                <div class="student-info">
                                                                    <div class="student-name">{{ $member->user->name }}</div>
                                                                    @if($member->user->matricule)
                                                                        <div class="student-matricule">{{ $member->user->matricule }}</div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Jury (Simplified) -->
                                            @if($defense->juries && $defense->juries->count() > 0)
                                                <div class="mb-3">
                                                    <small class="text-muted fw-semibold">{{ __('app.jury') }}:</small>
                                                    <div class="jury-list mt-1">
                                                        @foreach($defense->juries as $jury)
                                                            <div class="jury-member">
                                                                <span class="jury-name">{{ $jury->teacher->name }}</span>
                                                                @if($jury->role === 'president')
                                                                    <span class="badge bg-warning text-dark badge-sm">{{ __('app.president') }}</span>
                                                                @elseif($jury->role === 'examiner')
                                                                    <span class="badge bg-info badge-sm">{{ __('app.examiner') }}</span>
                                                                @elseif($jury->role === 'supervisor')
                                                                    <span class="badge bg-success badge-sm">{{ __('app.supervisor') }}</span>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Actions Footer -->
                                        <div class="card-footer border-0 bg-light">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <a href="{{ route('defenses.show', $defense) }}"
                                                   class="btn btn-outline-primary btn-sm">
                                                    <i class="bi bi-eye me-1"></i>{{ __('app.view') }}
                                                </a>

                                                <div class="btn-group">
                                                    @if(in_array(auth()->user()?->role, ['admin', 'department_head']))
                                                        <a href="{{ route('defenses.edit', $defense) }}"
                                                           class="btn btn-outline-warning btn-sm"
                                                           title="{{ __('app.edit') }}">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>

                                                        <a href="{{ route('defenses.report', $defense) }}"
                                                           class="btn btn-outline-primary btn-sm"
                                                           title="{{ __('app.view_report') }}"
                                                           target="_blank">
                                                            <i class="bi bi-file-text"></i>
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
                                                                    class="btn btn-outline-danger btn-sm"
                                                                    title="{{ __('app.delete') }}">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Table View (Hidden by default) -->
                        <div class="table-responsive d-none" id="tableView">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Sujet</th>
                                        <th>Étudiants</th>
                                        <th>Date/Heure</th>
                                        <th>Salle</th>
                                        <th>Statut</th>
                                        <th>Note</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($defenses as $defense)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold text-truncate" style="max-width: 200px;" title="{{ $defense->subject?->title ?? __('app.no_subject') }}">
                                                    {{ $defense->subject?->title ?? __('app.no_subject') }}
                                                </div>
                                                @if($defense->subject?->teacher)
                                                    <small class="text-muted">{{ $defense->subject->teacher->name }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($defense->project && $defense->project->team && $defense->project->team->members)
                                                    @foreach($defense->project->team->members as $member)
                                                        <div class="d-flex align-items-center mb-1">
                                                            <div class="avatar-xs me-2">{{ strtoupper(substr($member->user->name, 0, 1)) }}</div>
                                                            <small>{{ $member->user->name }}</small>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">--</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($defense->defense_date)
                                                    <div class="fw-semibold">{{ $defense->defense_date ? \Carbon\Carbon::parse($defense->defense_date)->format('d/m/Y') : 'TBD' }}</div>
                                                    @if($defense->defense_time)
                                                        <small class="text-muted">{{ $defense->defense_time ? \Carbon\Carbon::parse($defense->defense_time)->format('H:i') : '' }}</small>
                                                    @endif
                                                @else
                                                    <span class="text-muted">Non programmée</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($defense->room)
                                                    <span class="badge bg-secondary">{{ $defense->room->name }}</span>
                                                @else
                                                    <span class="text-muted">--</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($defense->status === 'scheduled')
                                                    <span class="badge bg-warning text-dark">Programmé</span>
                                                @elseif($defense->status === 'in_progress')
                                                    <span class="badge bg-primary">En cours</span>
                                                @elseif($defense->status === 'completed')
                                                    <span class="badge bg-success">Terminé</span>
                                                @elseif($defense->status === 'cancelled')
                                                    <span class="badge bg-danger">Annulé</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($defense->status) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($defense->final_grade)
                                                    <span class="fw-bold text-primary">{{ number_format($defense->final_grade, 1) }}/20</span>
                                                @else
                                                    <span class="text-muted">--</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('defenses.show', $defense) }}" class="btn btn-sm btn-outline-primary" title="Voir">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    @if(in_array(auth()->user()?->role, ['admin', 'department_head']))
                                                        <a href="{{ route('defenses.edit', $defense) }}" class="btn btn-sm btn-outline-warning" title="Modifier">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <a href="{{ route('defenses.report', $defense) }}" class="btn btn-sm btn-outline-primary" title="{{ __('app.view_report') }}" target="_blank">
                                                            <i class="bi bi-file-text"></i>
                                                        </a>
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
    /* Search and Filter Styles */
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

    /* Defense Cards */
    .defense-card {
        transition: all 0.3s ease;
        border-radius: 12px;
        overflow: hidden;
    }

    .defense-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
    }

    .defense-card .card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 1rem 1.25rem 0.5rem 1.25rem;
    }

    .defense-card .card-footer {
        background: #f8f9fa;
        padding: 0.75rem 1.25rem;
    }

    /* Status Badges */
    .badge.fs-6 {
        font-size: 0.85rem !important;
        padding: 0.5rem 0.75rem;
        border-radius: 8px;
    }

    .badge-sm {
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
    }

    /* Grade Display */
    .grade-display {
        background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
        padding: 0.5rem;
        border-radius: 8px;
        text-align: center;
        min-width: 60px;
    }

    /* Student Tags */
    .student-tag {
        display: flex;
        align-items: center;
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 0.5rem;
        margin-bottom: 0.5rem;
        transition: all 0.2s ease;
    }

    .student-tag:hover {
        background: #e9ecef;
        transform: scale(1.02);
    }

    .avatar-sm {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 0.8rem;
        flex-shrink: 0;
    }

    .student-info {
        min-width: 0;
        flex: 1;
    }

    .student-name {
        font-weight: 600;
        font-size: 0.85rem;
        color: #495057;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .student-matricule {
        font-size: 0.75rem;
        color: #6c757d;
        margin-top: 2px;
    }

    /* Jury List */
    .jury-list {
        max-height: 120px;
        overflow-y: auto;
    }

    .jury-member {
        display: flex;
        justify-content: between;
        align-items: center;
        padding: 0.25rem 0;
        border-bottom: 1px solid #f1f3f4;
        gap: 0.5rem;
    }

    .jury-member:last-child {
        border-bottom: none;
    }

    .jury-name {
        font-size: 0.85rem;
        font-weight: 500;
        color: #495057;
        flex: 1;
        min-width: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Icons */
    .bi {
        font-size: 1rem;
    }

    /* Card Title */
    .card-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #212529;
        line-height: 1.3;
    }

    /* Buttons */
    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.8rem;
        border-radius: 6px;
    }

    .btn-group .btn {
        border-radius: 6px !important;
        margin-left: 0.25rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .defense-card .card-header {
            padding: 0.75rem 1rem 0.25rem 1rem;
        }

        .defense-card .card-body {
            padding: 0.75rem 1rem;
        }

        .defense-card .card-footer {
            padding: 0.5rem 1rem;
        }

        .student-tag {
            padding: 0.375rem;
        }

        .avatar-sm {
            width: 28px;
            height: 28px;
            font-size: 0.7rem;
        }
    }

    /* Smooth animations */
    * {
        transition: all 0.2s ease;
    }

    /* Date time styling */
    .text-primary {
        color: #0d6efd !important;
    }

    /* Card shadow improvements */
    .shadow-sm {
        box-shadow: 0 2px 10px rgba(0,0,0,0.08) !important;
    }

    /* Table View Styles */
    .avatar-xs {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 0.7rem;
        flex-shrink: 0;
    }

    .table {
        margin-bottom: 0;
    }

    .table thead th {
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        background-color: #f8f9fa;
        color: #495057;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .table td {
        vertical-align: middle;
        border-top: 1px solid #f1f3f4;
    }

    /* View Toggle Buttons */
    .btn-group .btn.active {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: white;
    }

    .btn-group .btn:not(.active):hover {
        background-color: #e9ecef;
        border-color: #adb5bd;
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

    // View toggle elements
    const cardViewBtn = document.getElementById('cardViewBtn');
    const tableViewBtn = document.getElementById('tableViewBtn');
    const cardView = document.getElementById('cardView');
    const tableView = document.getElementById('tableView');

    let searchTimeout;

    // View Toggle Functionality
    if (cardViewBtn && tableViewBtn) {
        // Load saved preference
        const savedView = localStorage.getItem('defensesView') || 'cards';
        if (savedView === 'table') {
            showTableView();
        } else {
            showCardView();
        }

        cardViewBtn.addEventListener('click', function() {
            showCardView();
            localStorage.setItem('defensesView', 'cards');
        });

        tableViewBtn.addEventListener('click', function() {
            showTableView();
            localStorage.setItem('defensesView', 'table');
        });
    }

    function showCardView() {
        cardView.classList.remove('d-none');
        tableView.classList.add('d-none');
        cardViewBtn.classList.add('active');
        tableViewBtn.classList.remove('active');
    }

    function showTableView() {
        cardView.classList.add('d-none');
        tableView.classList.remove('d-none');
        tableViewBtn.classList.add('active');
        cardViewBtn.classList.remove('active');
    }

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