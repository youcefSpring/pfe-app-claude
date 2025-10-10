@extends('layouts.pfe-app')

@section('page-title', __('app.academic_years_history'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">{{ __('app.academic_years_history') }}</h4>
                        <small class="text-muted">{{ __('app.view_completed_academic_years') }}</small>
                    </div>
                    <a href="{{ route('admin.academic-years.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> {{ __('app.back_to_management') }}
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>{{ __('app.year') }}</th>
                                    <th>{{ __('app.title') }}</th>
                                    <th>{{ __('app.date_range') }}</th>
                                    <th>{{ __('app.ended_date') }}</th>
                                    <th>{{ __('app.ended_by') }}</th>
                                    <th>{{ __('app.statistics') }}</th>
                                    <th>{{ __('app.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($completedYears as $year)
                                    <tr>
                                        <td>
                                            <strong>{{ $year->year }}</strong>
                                            <span class="badge bg-dark ms-2">{{ __('app.completed') }}</span>
                                        </td>
                                        <td>{{ $year->title ?: $year->year }}</td>
                                        <td>{{ $year->getFormattedDateRange() }}</td>
                                        <td>
                                            @if($year->ended_at)
                                                {{ $year->ended_at->format('d/m/Y') }}
                                                <br>
                                                <small class="text-muted">{{ $year->ended_at->format('H:i') }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($year->endedBy)
                                                {{ $year->endedBy->name }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($year->statistics)
                                                <div class="small">
                                                    <div><strong>{{ __('app.subjects') }}:</strong> {{ $year->statistics['total_subjects'] ?? 0 }}</div>
                                                    <div><strong>{{ __('app.teams') }}:</strong> {{ $year->statistics['total_teams'] ?? 0 }}</div>
                                                    <div><strong>{{ __('app.projects') }}:</strong> {{ $year->statistics['total_projects'] ?? 0 }}</div>
                                                    <div><strong>{{ __('app.defenses') }}:</strong> {{ $year->statistics['total_defenses'] ?? 0 }}</div>
                                                    <div><strong>{{ __('app.students') }}:</strong> {{ $year->statistics['total_students'] ?? 0 }}</div>
                                                </div>
                                            @else
                                                <span class="text-muted">{{ __('app.no_statistics') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.academic-years.show', $year) }}"
                                                   class="btn btn-sm btn-outline-info" title="{{ __('app.view_details') }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button class="btn btn-sm btn-outline-primary"
                                                        onclick="exportYearData({{ $year->id }})"
                                                        title="{{ __('app.export_data') }}">
                                                    <i class="fas fa-download"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">{{ __('app.no_completed_years_found') }}</p>
                                            <a href="{{ route('admin.academic-years.index') }}" class="btn btn-primary">
                                                {{ __('app.back_to_management') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($completedYears->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $completedYears->links() }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Statistics Summary Card -->
            @if($completedYears->count() > 0)
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('app.historical_summary') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <h4 class="mb-0">{{ $completedYears->count() }}</h4>
                                        <small>{{ __('app.completed_years') }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h4 class="mb-0">
                                            {{ $completedYears->sum(function($year) { return $year->statistics['total_subjects'] ?? 0; }) }}
                                        </h4>
                                        <small>{{ __('app.total_subjects_all_years') }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <h4 class="mb-0">
                                            {{ $completedYears->sum(function($year) { return $year->statistics['total_teams'] ?? 0; }) }}
                                        </h4>
                                        <small>{{ __('app.total_teams_all_years') }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <h4 class="mb-0">
                                            {{ $completedYears->sum(function($year) { return $year->statistics['total_students'] ?? 0; }) }}
                                        </h4>
                                        <small>{{ __('app.total_students_all_years') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function exportYearData(yearId) {
    // This would typically trigger a download of year data in CSV/Excel format
    // For now, we'll just show an alert
    alert('{{ __('app.export_feature_coming_soon') }}');
}
</script>
@endsection