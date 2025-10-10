@extends('layouts.pfe-app')

@section('page-title', __('app.dashboard'))

@section('content')
<div class="row">
    <!-- Welcome Card -->
    {{-- <div class="col-12 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title mb-2">{{ __('app.welcome_back_user', ['name' => auth()->user()->name]) }}</h4>
                        <p class="card-text mb-0">
                            {{ __('app.academic_year') }}: {{ now()->format('Y') }}/{{ now()->addYear()->format('Y') }}
                            | {{ __('app.department') }}: {{ __('app.computer_science') }}
                            | {{ __('app.role') }}: {{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}
                        </p>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-speedometer2" style="font-size: 3rem; opacity: 0.7;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

<!-- Quick Actions -->
<div class="row mt-3">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-light py-2">
                <h6 class="card-title mb-0 fw-bold">
                    <i class="bi bi-lightning me-2 text-warning"></i>{{ __('app.quick_actions') }}
                </h6>
            </div>
            <div class="card-body py-3">
                <div class="row g-2">
                    @switch(auth()->user()->role)
                        @case('student')
                            @php
                                $userTeam = auth()->user()->teamMember?->team;
                                $hasTeam = auth()->user()->teamMember;
                            @endphp

                            <div class="col-md-6 col-lg-3">
                                <a href="{{ route('subjects.available') }}" class="btn btn-outline-primary w-100 py-2">
                                    <i class="bi bi-journal-text d-block mb-1" style="font-size: 1.2rem;"></i>
                                    <small>{{ __('app.available_subjects') }}</small>
                                </a>
                            </div>

                            @if($hasTeam)
                                <div class="col-md-6 col-lg-3">
                                    <a href="{{ route('teams.show', $userTeam) }}" class="btn btn-outline-success w-100 py-2">
                                        <i class="bi bi-people d-block mb-1" style="font-size: 1.2rem;"></i>
                                        <small>{{ __('app.my_team') }}</small>
                                    </a>
                                </div>
                            @else
                                <div class="col-md-6 col-lg-3">
                                    <a href="{{ route('teams.index') }}" class="btn btn-outline-success w-100 py-2">
                                        <i class="bi bi-people d-block mb-1" style="font-size: 1.2rem;"></i>
                                        <small>{{ __('app.join_team') }}</small>
                                    </a>
                                </div>
                            @endif

                            {{-- Projects temporarily hidden --}}
                            {{--
                            <div class="col-md-6 col-lg-3">
                                <a href="{{ route('projects.index') }}" class="btn btn-outline-info w-100">
                                    <i class="bi bi-folder d-block mb-2" style="font-size: 1.5rem;"></i>
                                    My Projects
                                </a>
                            </div>
                            --}}
                            <div class="col-md-6 col-lg-3">
                                <a href="{{ route('subjects.create') }}" class="btn btn-outline-info w-100 py-2">
                                    <i class="bi bi-plus-circle d-block mb-1" style="font-size: 1.2rem;"></i>
                                    <small>{{ __('app.propose_external_subject') }}</small>
                                </a>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <a href="{{ route('defenses.index') }}" class="btn btn-outline-warning w-100 py-2">
                                    <i class="bi bi-shield-check d-block mb-1" style="font-size: 1.2rem;"></i>
                                    <small>{{ __('app.defense_schedule') }}</small>
                                </a>
                            </div>
                            @break
                        @case('teacher')
                            <div class="col-md-6 col-lg-3">
                                <a href="{{ route('subjects.create') }}" class="btn btn-outline-primary w-100 py-2">
                                    <i class="bi bi-plus-circle d-block mb-1" style="font-size: 1.2rem;"></i>
                                    <small>{{ __('app.add_subject') }}</small>
                                </a>
                            </div>
                            {{-- Supervised Projects temporarily removed --}}
                            {{--
                            <div class="col-md-6 col-lg-3">
                                <a href="{{ route('projects.supervised') }}" class="btn btn-outline-success w-100">
                                    <i class="bi bi-eye d-block mb-2" style="font-size: 1.5rem;"></i>
                                    Supervised Projects
                                </a>
                            </div>
                            --}}
                            <div class="col-md-6 col-lg-3">
                                <a href="{{ route('defenses.jury-assignments') }}" class="btn btn-outline-info w-100 py-2">
                                    <i class="bi bi-people d-block mb-1" style="font-size: 1.2rem;"></i>
                                    <small>{{ __('app.jury_assignments') }}</small>
                                </a>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <a href="{{ route('subjects.index') }}" class="btn btn-outline-warning w-100 py-2">
                                    <i class="bi bi-journal-text d-block mb-1" style="font-size: 1.2rem;"></i>
                                    <small>{{ __('app.my_subjects') }}</small>
                                </a>
                            </div>
                            @break
                        @case('department_head')
                            <div class="col-md-6 col-lg-3">
                                <a href="{{ route('subjects.pending-validation') }}" class="btn btn-outline-primary w-100 py-2">
                                    <i class="bi bi-check-circle d-block mb-1" style="font-size: 1.2rem;"></i>
                                    <small>{{ __('app.validate_subjects') }}</small>
                                </a>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <a href="{{ route('defenses.schedule-form') }}" class="btn btn-outline-success w-100 py-2">
                                    <i class="bi bi-calendar-plus d-block mb-1" style="font-size: 1.2rem;"></i>
                                    <small>{{ __('app.schedule_defense') }}</small>
                                </a>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <a href="{{ route('conflicts.index') }}" class="btn btn-outline-warning w-100 py-2">
                                    <i class="bi bi-exclamation-triangle d-block mb-1" style="font-size: 1.2rem;"></i>
                                    <small>{{ __('app.resolve_conflicts') }}</small>
                                </a>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <a href="{{ route('admin.reports') }}" class="btn btn-outline-info w-100 py-2">
                                    <i class="bi bi-graph-up d-block mb-1" style="font-size: 1.2rem;"></i>
                                    <small>{{ __('app.reports') }}</small>
                                </a>
                            </div>
                            @break
                        @case('admin')
                            <div class="col-md-6 col-lg-3">
                                <a href="{{ route('admin.users') }}" class="btn btn-outline-primary w-100 py-2">
                                    <i class="bi bi-people d-block mb-1" style="font-size: 1.2rem;"></i>
                                    <small>{{ __('app.manage_users') }}</small>
                                </a>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <a href="{{ route('admin.specialities.index') }}" class="btn btn-outline-success w-100 py-2">
                                    <i class="bi bi-mortarboard d-block mb-1" style="font-size: 1.2rem;"></i>
                                    <small>{{ __('app.specialities') }}</small>
                                </a>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <a href="{{ route('admin.rooms') }}" class="btn btn-outline-info w-100 py-2">
                                    <i class="bi bi-building d-block mb-1" style="font-size: 1.2rem;"></i>
                                    <small>{{ __('app.manage_rooms') }}</small>
                                </a>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <a href="{{ route('admin.reports') }}" class="btn btn-outline-warning w-100 py-2">
                                    <i class="bi bi-graph-up d-block mb-1" style="font-size: 1.2rem;"></i>
                                    <small>{{ __('app.reports_analytics') }}</small>
                                </a>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <a href="{{ route('admin.academic-years.index') }}" class="btn btn-outline-dark w-100 py-2">
                                    <i class="bi bi-calendar-range d-block mb-1" style="font-size: 1.2rem;"></i>
                                    <small>{{ __('app.academic_years_management') }}</small>
                                </a>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <a href="{{ route('admin.settings') }}" class="btn btn-outline-secondary w-100 py-2">
                                    <i class="bi bi-gear d-block mb-1" style="font-size: 1.2rem;"></i>
                                    <small>{{ __('app.system_settings') }}</small>
                                </a>
                            </div>
                            @break
                    @endswitch
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Role-specific Dashboard Content -->
<div class="row">
    @switch(auth()->user()->role)
        @case('student')
            @include('dashboard.partials.student-content')
            @break
        @case('teacher')
            @include('dashboard.partials.teacher-content')
            @break
        @case('department_head')
            @include('dashboard.partials.department-head-content')
            @break
        @case('admin')
            @include('dashboard.partials.admin-content')
            @break
        @default
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    {{ __('app.dashboard_setup_message') }}
                </div>
            </div>
    @endswitch
</div>

@if(isset($workflowStatus) && $workflowStatus)
<!-- Workflow Status -->
<div class="row mt-3">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-light py-2">
                <h6 class="card-title mb-0 fw-bold">
                    <i class="bi bi-diagram-3 me-2 text-info"></i>{{ __('app.current_status') }}
                </h6>
            </div>
            <div class="card-body py-3">
                <div class="row">
                    <div class="col-md-4">
                        <small class="text-muted">{{ __('app.current_phase') }}</small>
                        <div class="fw-bold">{{ $workflowStatus['current_phase'] ?? __('app.getting_started') }}</div>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted">{{ __('app.status') }}</small>
                        <div class="fw-bold">{{ $workflowStatus['status'] ?? __('app.in_progress') }}</div>
                    </div>
                    @if(isset($workflowStatus['next_actions']) && count($workflowStatus['next_actions']) > 0)
                        <div class="col-md-4">
                            <small class="text-muted">{{ __('app.next_actions') }}</small>
                            <ul class="mb-0 small">
                                @foreach($workflowStatus['next_actions'] as $action)
                                    <li>{{ $action }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
// Auto-refresh dashboard data every 5 minutes
setInterval(function() {
    // You can add AJAX calls here to refresh specific dashboard sections
    console.log('Dashboard auto-refresh');
}, 300000);
</script>
@endpush
