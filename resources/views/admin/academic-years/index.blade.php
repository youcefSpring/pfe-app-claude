@extends('layouts.pfe-app')

@section('page-title', __('app.academic_years_management'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">{{ __('app.academic_years_management') }}</h4>
                        <small class="text-muted">{{ __('app.manage_academic_years_description') }}</small>
                    </div>
                    <a href="{{ route('admin.academic-years.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> {{ __('app.create_academic_year') }}
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($currentYear)
                        <div class="alert alert-info mb-4">
                            <i class="fas fa-info-circle me-2"></i>
                            {{ __('app.current_academic_year') }}: <strong>{{ $currentYear->year }}</strong>
                            ({{ $currentYear->getFormattedDateRange() }})
                            - {{ __('app.progress') }}: {{ number_format($currentYear->getProgressPercentage(), 1) }}%
                        </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <a href="{{ route('admin.academic-years.history') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-history"></i> {{ __('app.view_history') }}
                            </a>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>{{ __('app.year') }}</th>
                                    <th>{{ __('app.title') }}</th>
                                    <th>{{ __('app.date_range') }}</th>
                                    <th>{{ __('app.status') }}</th>
                                    <th>{{ __('app.progress') }}</th>
                                    <th>{{ __('app.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($academicYears as $year)
                                    <tr>
                                        <td>
                                            <strong>{{ $year->year }}</strong>
                                            @if($year->is_current)
                                                <span class="badge bg-primary ms-2">{{ __('app.current') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $year->title ?: $year->year }}</td>
                                        <td>{{ $year->getFormattedDateRange() }}</td>
                                        <td>
                                            @if($year->status === 'draft')
                                                <span class="badge bg-secondary">{{ __('app.draft') }}</span>
                                            @elseif($year->status === 'active')
                                                <span class="badge bg-success">{{ __('app.active') }}</span>
                                            @elseif($year->status === 'completed')
                                                <span class="badge bg-dark">{{ __('app.completed') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar
                                                    @if($year->getProgressPercentage() < 30) bg-danger
                                                    @elseif($year->getProgressPercentage() < 70) bg-warning
                                                    @else bg-success
                                                    @endif"
                                                    role="progressbar"
                                                    style="width: {{ $year->getProgressPercentage() }}%"
                                                    aria-valuenow="{{ $year->getProgressPercentage() }}"
                                                    aria-valuemin="0"
                                                    aria-valuemax="100">
                                                    {{ number_format($year->getProgressPercentage(), 1) }}%
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1 justify-content-center">
                                                <!-- View Button -->
                                                <a href="{{ route('admin.academic-years.show', $year) }}"
                                                   class="btn btn-sm btn-outline-info rounded-pill"
                                                   title="{{ __('app.view') }}" data-bs-toggle="tooltip">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                <!-- Edit Button -->
                                                @if($year->canBeEdited())
                                                    <a href="{{ route('admin.academic-years.edit', $year) }}"
                                                       class="btn btn-sm btn-outline-warning rounded-pill"
                                                       title="{{ __('app.edit') }}" data-bs-toggle="tooltip">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif

                                                <!-- Status Action Buttons -->
                                                @if($year->status === 'draft')
                                                    <form action="{{ route('admin.academic-years.activate', $year) }}"
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success rounded-pill"
                                                                title="{{ __('app.activate') }}" data-bs-toggle="tooltip"
                                                                onclick="return confirm('{{ __('app.confirm_activate_year') }}')">
                                                            <i class="fas fa-play"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                @if($year->canBeEnded())
                                                    <form action="{{ route('admin.academic-years.end', $year) }}"
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-warning rounded-pill"
                                                                title="{{ __('app.end_year') }}" data-bs-toggle="tooltip"
                                                                onclick="return confirm('{{ __('app.confirm_end_year') }}')">
                                                            <i class="fas fa-stop"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                <!-- Delete Button -->
                                                @if(!$year->isActive() && !$year->isCurrent())
                                                    <form action="{{ route('admin.academic-years.destroy', $year) }}"
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill"
                                                                title="{{ __('app.delete') }}" data-bs-toggle="tooltip"
                                                                onclick="return confirm('{{ __('app.confirm_delete_year') }}')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">{{ __('app.no_academic_years_found') }}</p>
                                            <a href="{{ route('admin.academic-years.create') }}" class="btn btn-primary">
                                                {{ __('app.create_first_academic_year') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <x-admin-pagination :paginator="$academicYears" />
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.btn-group .btn,
.d-flex .btn {
    transition: all 0.2s ease-in-out;
}

.btn-sm.rounded-pill {
    padding: 0.25rem 0.6rem;
    font-size: 0.8rem;
}

.progress {
    background-color: #e9ecef;
    border-radius: 0.375rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});
</script>
@endsection