@extends('layouts.app')

@section('title', __('app.subject_allocations'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">{{ __('app.subject_allocations') }}</h1>
                    <p class="text-muted">{{ __('app.manage_student_subject_allocations') }}</p>
                </div>
                <div>
                    <a href="{{ route('allocations.deadlines') }}" class="btn btn-outline-primary me-2">
                        <i class="bi bi-calendar-event me-1"></i>{{ __('app.manage_deadlines') }}
                    </a>
                    <a href="{{ route('allocations.results') }}" class="btn btn-primary">
                        <i class="bi bi-graph-up me-1"></i>{{ __('app.view_results') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <h3 class="text-primary mb-1">{{ $allocations->total() }}</h3>
                    <small class="text-muted">{{ __('app.total_allocations') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card border-success">
                <div class="card-body text-center">
                    <h3 class="text-success mb-1">{{ $deadlines->where('status', 'active')->count() }}</h3>
                    <small class="text-muted">{{ __('app.active_deadlines') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card border-info">
                <div class="card-body text-center">
                    <h3 class="text-info mb-1">{{ $deadlines->where('status', 'completed')->count() }}</h3>
                    <small class="text-muted">{{ __('app.completed_periods') }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-list-check me-2"></i>{{ __('app.recent_allocations') }}
                    </h5>
                </div>
                <div class="card-body">
                    @if($allocations->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('app.student') }}</th>
                                        <th>{{ __('app.subject') }}</th>
                                        <th>{{ __('app.preference') }}</th>
                                        <th>{{ __('app.average') }}</th>
                                        <th>{{ __('app.status') }}</th>
                                        <th>{{ __('app.deadline') }}</th>
                                        <th>{{ __('app.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($allocations as $allocation)
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong>{{ $allocation->student->name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $allocation->student->matricule }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $allocation->subject->title }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ Str::limit($allocation->subject->description, 50) }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                @if($allocation->student_preference_order)
                                                    <span class="badge bg-info">{{ $allocation->getPreferenceLabel() }}</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ __('app.not_preferred') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <strong class="text-primary">{{ number_format($allocation->student_average, 2) }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge {{ $allocation->status === 'confirmed' ? 'bg-success' : ($allocation->status === 'rejected' ? 'bg-danger' : 'bg-warning') }}">
                                                    {{ ucfirst($allocation->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $allocation->allocationDeadline->title }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-outline-secondary btn-sm" title="{{ __('app.view_details') }}">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            <nav aria-label="{{ __('app.allocations_pagination') }}">
                                {{ $allocations->links('pagination::bootstrap-4') }}
                            </nav>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox display-1 text-muted"></i>
                            <h4 class="text-muted mt-3">{{ __('app.no_allocations_found') }}</h4>
                            <p class="text-muted">{{ __('app.no_allocations_created') }}</p>
                            <a href="{{ route('allocations.deadlines') }}" class="btn btn-primary">
                                <i class="bi bi-calendar-event me-1"></i>{{ __('app.create_allocation_deadline') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection