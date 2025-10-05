@extends('layouts.pfe-app')

@section('title', __('app.defenses'))

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">{{ __('app.defenses') }}</h1>
        @if(in_array(auth()->user()?->role, ['admin', 'department_head']))
            <div class="btn-group">
                <a href="{{ route('defenses.schedule-form') }}" class="btn btn-primary">
                    <i class="bi bi-calendar-plus"></i> {{ __('app.schedule_defense') }}
                </a>
                <a href="{{ route('defenses.calendar') }}" class="btn btn-outline-primary">
                    <i class="bi bi-calendar"></i> {{ __('app.calendar_view') }}
                </a>
            </div>
        @endif
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('defenses.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="status" class="form-label">{{ __('app.status') }}</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">{{ __('app.all_statuses') }}</option>
                        <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>{{ __('app.scheduled') }}</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>{{ __('app.in_progress') }}</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>{{ __('app.completed') }}</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>{{ __('app.cancelled') }}</option>
                    </select>
                </div>
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
                <div class="col-md-3">
                    <label for="search" class="form-label">{{ __('app.search') }}</label>
                    <input type="text" class="form-control" id="search" name="search"
                           value="{{ request('search') }}" placeholder="{{ __('app.search_defenses') }}...">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-outline-primary">{{ __('app.filter') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Results Info -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="text-muted">
            {{ __('app.showing') }} {{ $defenses->firstItem() ?? 0 }} {{ __('app.to') }} {{ $defenses->lastItem() ?? 0 }}
            {{ __('app.of') }} {{ $defenses->total() }} {{ __('app.results') }}
        </div>
        @if(request()->hasAny(['search', 'status', 'date_from', 'date_to']))
            <a href="{{ route('defenses.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-x-circle"></i> {{ __('app.clear_filters') }}
            </a>
        @endif
    </div>

    <!-- Defenses List -->
    <div class="row">
        @forelse($defenses as $defense)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span class="badge bg-{{ $defense->status === 'completed' ? 'success' : ($defense->status === 'in_progress' ? 'warning' : ($defense->status === 'cancelled' ? 'danger' : 'primary')) }}">
                            {{ ucfirst(str_replace('_', ' ', $defense->status)) }}
                        </span>
                        <small class="text-muted">
                            {{ $defense->defense_date ? $defense->defense_date->format('M d, Y') : 'TBD' }}
                        </small>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">
                            {{ $defense->subject->title ?? 'Defense' }}
                        </h5>
                        <h6 class="text-muted">{{ $defense->subject->teacher->name ?? 'No Teacher' }}</h6>

                        <!-- Defense Details -->
                        <div class="text-muted small mb-2">
                            @if($defense->defense_date)
                                <div><i class="bi bi-calendar"></i> {{ $defense->defense_date->format('M d, Y \a\t g:i A') }}</div>
                            @endif
                            @if($defense->room)
                                <div><i class="bi bi-geo-alt"></i> {{ $defense->room->name }} ({{ $defense->room->location ?? 'Location TBD' }})</div>
                            @endif
                            <div><i class="bi bi-clock"></i> {{ $defense->duration ?? 60 }} minutes</div>
                        </div>

                        <!-- Subject Details -->
                        <div class="mb-2">
                            <h6 class="text-muted mb-1">{{ __('app.subject_type') }}:</h6>
                            <span class="badge bg-{{ $defense->subject->is_external ? 'secondary' : 'primary' }}">
                                {{ $defense->subject->is_external ? __('app.external') : __('app.internal') }}
                            </span>
                        </div>

                        <!-- Jury -->
                        @if($defense->juries->count() > 0)
                            <div class="mb-2">
                                <h6 class="text-muted mb-1">Jury:</h6>
                                @foreach($defense->juries->take(2) as $jury)
                                    <span class="badge bg-info text-dark me-1">{{ $jury->teacher->name }}</span>
                                @endforeach
                                @if($defense->juries->count() > 2)
                                    <span class="badge bg-secondary">+{{ $defense->juries->count() - 2 }} more</span>
                                @endif
                            </div>
                        @endif
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('defenses.show', $defense) }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-eye"></i> View Details
                            </a>
                            @if(in_array(auth()->user()?->role, ['admin', 'department_head']))
                                <div class="btn-group">
                                    @if($defense->status === 'scheduled')
                                        <a href="{{ route('defenses.edit', $defense) }}" class="btn btn-warning btn-sm">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="{{ route('defenses.cancel', $defense) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Are you sure you want to cancel this defense?')">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>
                                    @elseif($defense->status === 'completed')
                                        <a href="{{ route('defenses.generate-report', $defense) }}" class="btn btn-success btn-sm">
                                            <i class="bi bi-file-text"></i> Report
                                        </a>
                                        <a href="{{ route('defenses.download-report-pdf', $defense) }}" class="btn btn-danger btn-sm">
                                            <i class="bi bi-file-pdf"></i> PDF
                                        </a>
                                    @endif
                                </div>
                            @elseif(auth()->user()?->role === 'teacher' && $defense->juries->contains('teacher_id', auth()->id()))
                                @if($defense->status === 'scheduled' || $defense->status === 'in_progress')
                                    <a href="{{ route('defenses.show', $defense) }}" class="btn btn-success btn-sm">
                                        <i class="bi bi-clipboard"></i> Evaluate
                                    </a>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-shield-check text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3">No Defenses Found</h4>
                    <p class="text-muted">
                        @if(request()->hasAny(['search', 'status', 'date_from', 'date_to']))
                            No defenses match your current filters. Try adjusting your search criteria.
                        @else
                            There are no defenses scheduled at the moment.
                        @endif
                    </p>
                    @if(in_array(auth()->user()?->role, ['admin', 'department_head']))
                        <a href="{{ route('defenses.schedule-form') }}" class="btn btn-primary">
                            <i class="bi bi-calendar-plus"></i> Schedule First Defense
                        </a>
                    @endif
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($defenses->hasPages())
        <div class="d-flex justify-content-center mt-4">
            <nav aria-label="Defenses pagination">
                {{ $defenses->appends(request()->query())->links('pagination::bootstrap-4') }}
            </nav>
        </div>
    @endif
</div>
@endsection