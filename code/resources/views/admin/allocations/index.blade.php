@extends('layouts.pfe-app')

@section('page-title', __('app.allocation_management'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-tasks text-primary"></i> {{ __('app.allocation_management') }}
                        </h4>
                        <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#pageHelpModal">
                            <i class="bi bi-question-circle"></i>
                        </button>
                    </div>
                    <a href="{{ route('admin.allocations.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-sync"></i> {{ __('app.refresh') }}
                    </a>
                </div>
                <div class="card-body">
                    <!-- Filter Section -->
                    <form method="GET" class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="academic_year" class="form-label">{{ __('app.academic_year') }}</label>
                            <select class="form-select" name="academic_year" id="academic_year">
                                <option value="">{{ __('app.all_years') }}</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year }}" {{ request('academic_year') == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="level" class="form-label">{{ __('app.level') }}</label>
                            <select class="form-select" name="level" id="level">
                                <option value="">{{ __('app.all_levels') }}</option>
                                @foreach($levels as $level)
                                    <option value="{{ $level }}" {{ request('level') == $level ? 'selected' : '' }}>
                                        {{ $level }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-filter"></i> {{ __('app.filter') }}
                            </button>
                            <a href="{{ route('admin.allocations.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> {{ __('app.clear') }}
                            </a>
                        </div>
                    </form>

                    <!-- Deadlines List -->
                    <div class="row">
                        @forelse($deadlines as $deadline)
                            <div class="col-lg-6 col-xl-4 mb-4">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-header bg-light border-0">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <h6 class="card-title mb-0 text-primary">
                                                {{ $deadline->name }}
                                            </h6>
                                            <span class="badge
                                                @switch($deadline->status)
                                                    @case('active') bg-success @break
                                                    @case('draft') bg-secondary @break
                                                    @case('preferences_closed') bg-warning @break
                                                    @case('auto_allocation_completed') bg-info @break
                                                    @case('second_round_active') bg-primary @break
                                                    @default bg-dark
                                                @endswitch
                                            ">
                                                {{ ucfirst(str_replace('_', ' ', $deadline->status)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <small class="text-muted d-block">{{ __('app.academic_year_level') }}</small>
                                            <strong>{{ $deadline->academic_year }} - {{ $deadline->level }}</strong>
                                        </div>

                                        <div class="mb-3">
                                            <small class="text-muted d-block">{{ __('app.preferences_deadline') }}</small>
                                            <strong>{{ $deadline->preferences_deadline->format('d/m/Y H:i') }}</strong>
                                        </div>

                                        @if($deadline->auto_allocation_completed)
                                            <div class="mb-3">
                                                <small class="text-success">
                                                    <i class="fas fa-check-circle"></i> {{ __('app.auto_allocation_completed') }}
                                                </small>
                                            </div>
                                        @endif

                                        @if($deadline->second_round_needed)
                                            <div class="mb-3">
                                                <small class="text-warning">
                                                    <i class="fas fa-clock"></i> {{ __('app.second_round_needed') }}
                                                </small>
                                                @if($deadline->second_round_start && $deadline->second_round_deadline)
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ $deadline->second_round_start->format('d/m/Y') }} -
                                                        {{ $deadline->second_round_deadline->format('d/m/Y') }}
                                                    </small>
                                                @endif
                                            </div>
                                        @endif

                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                {{ __('app.created_by_user', ['name' => $deadline->creator->name ?? __('app.system')]) }}
                                            </small>
                                            <a href="{{ route('admin.allocations.show', $deadline) }}"
                                               class="btn btn-sm btn-primary">
                                                <i class="fas fa-cog"></i> {{ __('app.manage') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="text-center py-5">
                                    <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                                    <h5 class="text-muted">{{ __('app.no_allocation_deadlines_found') }}</h5>
                                    <p class="text-muted">{{ __('app.create_deadlines_message') }}</p>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    <x-admin-pagination :paginator="$deadlines" />
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto submit form on select change
    const selects = document.querySelectorAll('#academic_year, #level');
    selects.forEach(select => {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });
});
</script>
@endpush

<!-- Page Help Modal -->
<x-info-modal id="pageHelpModal" title="{{ __('app.allocation_management_help') }}" icon="bi-diagram-3">
    <h6>{{ __('app.what_is_this_page') }}</h6>
    <p>{{ __('app.allocation_management_page_description') }}</p>

    <h6>{{ __('app.how_to_use') }}</h6>
    <ul>
        <li><strong>{{ __('app.view_allocations') }}:</strong> {{ __('app.view_allocations_help') }}</li>
        <li><strong>{{ __('app.manage_deadline') }}:</strong> {{ __('app.manage_deadline_help') }}</li>
        <li><strong>{{ __('app.run_auto_allocation') }}:</strong> {{ __('app.run_auto_allocation_help') }}</li>
        <li><strong>{{ __('app.manual_assignment') }}:</strong> {{ __('app.manual_assignment_help') }}</li>
    </ul>

    <h6>{{ __('app.allocation_process') }}</h6>
    <ul>
        <li>{{ __('app.allocation_step_1') }}</li>
        <li>{{ __('app.allocation_step_2') }}</li>
        <li>{{ __('app.allocation_step_3') }}</li>
        <li>{{ __('app.allocation_step_4') }}</li>
    </ul>
</x-info-modal>

@endsection