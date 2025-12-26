@extends('layouts.pfe-app')

@section('page-title', __('app.academic_year_details'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">{{ $academicYear->title ?: $academicYear->year }}</h4>
                        <small class="text-muted">{{ __('app.academic_year_details') }}</small>
                    </div>
                    <div>
                        @if($academicYear->canBeEdited())
                            <a href="{{ route('admin.academic-years.edit', $academicYear) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> {{ __('app.edit') }}
                            </a>
                        @endif
                        <a href="{{ route('admin.academic-years.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> {{ __('app.back') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">{{ __('app.basic_information') }}</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>{{ __('app.year') }}:</strong></td>
                                            <td>{{ $academicYear->year }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('app.title') }}:</strong></td>
                                            <td>{{ $academicYear->title ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('app.status') }}:</strong></td>
                                            <td>
                                                @if($academicYear->status === 'draft')
                                                    <span class="badge bg-secondary">{{ __('app.draft') }}</span>
                                                @elseif($academicYear->status === 'active')
                                                    <span class="badge bg-success">{{ __('app.active') }}</span>
                                                @elseif($academicYear->status === 'completed')
                                                    <span class="badge bg-dark">{{ __('app.completed') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('app.current_year') }}:</strong></td>
                                            <td>
                                                @if($academicYear->is_current)
                                                    <span class="badge bg-primary">{{ __('app.yes') }}</span>
                                                @else
                                                    <span class="badge bg-light text-dark">{{ __('app.no') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('app.date_range') }}:</strong></td>
                                            <td>{{ $academicYear->getFormattedDateRange() }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('app.duration') }}:</strong></td>
                                            <td>{{ $academicYear->getDurationInDays() }} {{ __('app.days') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('app.progress') }}:</strong></td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar
                                                        @if($academicYear->getProgressPercentage() < 30) bg-danger
                                                        @elseif($academicYear->getProgressPercentage() < 70) bg-warning
                                                        @else bg-success
                                                        @endif"
                                                        role="progressbar"
                                                        style="width: {{ $academicYear->getProgressPercentage() }}%">
                                                        {{ number_format($academicYear->getProgressPercentage(), 1) }}%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @if($academicYear->description)
                                            <tr>
                                                <td><strong>{{ __('app.description') }}:</strong></td>
                                                <td>{{ $academicYear->description }}</td>
                                            </tr>
                                        @endif
                                        @if($academicYear->ended_at)
                                            <tr>
                                                <td><strong>{{ __('app.ended_at') }}:</strong></td>
                                                <td>{{ $academicYear->ended_at->format('d/m/Y H:i') }}</td>
                                            </tr>
                                            @if($academicYear->endedBy)
                                                <tr>
                                                    <td><strong>{{ __('app.ended_by') }}:</strong></td>
                                                    <td>{{ $academicYear->endedBy->name }}</td>
                                                </tr>
                                            @endif
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Statistics -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">{{ __('app.statistics') }}</h5>
                                </div>
                                <div class="card-body">
                                    @if($statistics)
                                        <div class="row">
                                            <div class="col-6 mb-3">
                                                <div class="card bg-primary text-white">
                                                    <div class="card-body text-center">
                                                        <h3 class="mb-0">{{ $statistics['total_subjects'] ?? 0 }}</h3>
                                                        <small>{{ __('app.total_subjects') }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <div class="card bg-success text-white">
                                                    <div class="card-body text-center">
                                                        <h3 class="mb-0">{{ $statistics['total_teams'] ?? 0 }}</h3>
                                                        <small>{{ __('app.total_teams') }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <div class="card bg-info text-white">
                                                    <div class="card-body text-center">
                                                        <h3 class="mb-0">{{ $statistics['total_projects'] ?? 0 }}</h3>
                                                        <small>{{ __('app.total_projects') }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <div class="card bg-warning text-white">
                                                    <div class="card-body text-center">
                                                        <h3 class="mb-0">{{ $statistics['total_defenses'] ?? 0 }}</h3>
                                                        <small>{{ __('app.total_defenses') }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <div class="card bg-secondary text-white">
                                                    <div class="card-body text-center">
                                                        <h3 class="mb-0">{{ $statistics['total_students'] ?? 0 }}</h3>
                                                        <small>{{ __('app.total_students') }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <div class="card bg-dark text-white">
                                                    <div class="card-body text-center">
                                                        <h3 class="mb-0">{{ $statistics['total_teachers'] ?? 0 }}</h3>
                                                        <small>{{ __('app.total_teachers') }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        @if(isset($statistics['average_defense_grade']) && $statistics['average_defense_grade'])
                                            <div class="mt-3">
                                                <strong>{{ __('app.average_defense_grade') }}:</strong>
                                                {{ number_format($statistics['average_defense_grade'], 2) }}/20
                                            </div>
                                        @endif

                                        @if(isset($statistics['calculated_at']))
                                            <div class="mt-2">
                                                <small class="text-muted">
                                                    {{ __('app.last_calculated') }}: {{ \Carbon\Carbon::parse($statistics['calculated_at'])->format('d/m/Y H:i') }}
                                                </small>
                                            </div>
                                        @endif
                                    @else
                                        <p class="text-muted">{{ __('app.no_statistics_available') }}</p>
                                        @if($academicYear->isCompleted())
                                            <button class="btn btn-outline-primary" onclick="calculateStatistics()">
                                                <i class="fas fa-calculator"></i> {{ __('app.calculate_statistics') }}
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">{{ __('app.actions') }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="btn-group" role="group">
                                        @if($academicYear->status === 'draft')
                                            <form action="{{ route('admin.academic-years.activate', $academicYear) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success"
                                                        onclick="return confirm('{{ __('app.confirm_activate_year') }}')">
                                                    <i class="fas fa-play"></i> {{ __('app.activate_year') }}
                                                </button>
                                            </form>
                                        @endif

                                        @if($academicYear->canBeEnded())
                                            <form action="{{ route('admin.academic-years.end', $academicYear) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-warning"
                                                        onclick="return confirm('{{ __('app.confirm_end_year') }}')">
                                                    <i class="fas fa-stop"></i> {{ __('app.end_year') }}
                                                </button>
                                            </form>
                                        @endif

                                        @if(!$academicYear->isActive() && !$academicYear->isCurrent())
                                            <form action="{{ route('admin.academic-years.destroy', $academicYear) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger"
                                                        onclick="return confirm('{{ __('app.confirm_delete_year') }}')">
                                                    <i class="fas fa-trash"></i> {{ __('app.delete') }}
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function calculateStatistics() {
    fetch('{{ route('admin.academic-years.statistics', $academicYear) }}')
        .then(response => response.json())
        .then(data => {
            location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('{{ __('app.error_calculating_statistics') }}');
        });
}
</script>
@endsection