@extends('layouts.pfe-app')

@section('page-title', 'Allocation Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-tasks text-primary"></i> Allocation Management
                    </h4>
                    <a href="{{ route('admin.allocations.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-sync"></i> Refresh
                    </a>
                </div>
                <div class="card-body">
                    <!-- Filter Section -->
                    <form method="GET" class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="academic_year" class="form-label">Academic Year</label>
                            <select class="form-select" name="academic_year" id="academic_year">
                                <option value="">All Years</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year }}" {{ request('academic_year') == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="level" class="form-label">Level</label>
                            <select class="form-select" name="level" id="level">
                                <option value="">All Levels</option>
                                @foreach($levels as $level)
                                    <option value="{{ $level }}" {{ request('level') == $level ? 'selected' : '' }}>
                                        {{ $level }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <a href="{{ route('admin.allocations.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Clear
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
                                            <small class="text-muted d-block">Academic Year & Level</small>
                                            <strong>{{ $deadline->academic_year }} - {{ $deadline->level }}</strong>
                                        </div>

                                        <div class="mb-3">
                                            <small class="text-muted d-block">Preferences Deadline</small>
                                            <strong>{{ $deadline->preferences_deadline->format('d/m/Y H:i') }}</strong>
                                        </div>

                                        @if($deadline->auto_allocation_completed)
                                            <div class="mb-3">
                                                <small class="text-success">
                                                    <i class="fas fa-check-circle"></i> Auto-allocation completed
                                                </small>
                                            </div>
                                        @endif

                                        @if($deadline->second_round_needed)
                                            <div class="mb-3">
                                                <small class="text-warning">
                                                    <i class="fas fa-clock"></i> Second round needed
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
                                                Created by {{ $deadline->creator->name ?? 'System' }}
                                            </small>
                                            <a href="{{ route('admin.allocations.show', $deadline) }}"
                                               class="btn btn-sm btn-primary">
                                                <i class="fas fa-cog"></i> Manage
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="text-center py-5">
                                    <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                                    <h5 class="text-muted">No allocation deadlines found</h5>
                                    <p class="text-muted">Create allocation deadlines in the allocation management section.</p>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    @if($deadlines->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $deadlines->links() }}
                        </div>
                    @endif
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
@endsection