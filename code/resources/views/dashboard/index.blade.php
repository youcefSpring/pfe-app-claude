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
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-lightning me-2"></i>{{ __('app.quick_actions') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @switch(auth()->user()->role)
                        @case('student')
                            @php
                                $userTeam = auth()->user()->teamMember?->team;
                                $hasTeam = auth()->user()->teamMember;
                            @endphp

                            <div class="col-md-6 col-lg-3">
                                <a href="{{ route('subjects.available') }}" class="btn btn-outline-primary w-100">
                                    <i class="bi bi-journal-text d-block mb-2" style="font-size: 1.5rem;"></i>
                                    {{ __('app.available_subjects') }}
                                </a>
                            </div>

                            @if($hasTeam)
                                <div class="col-md-6 col-lg-3">
                                    <a href="{{ route('teams.show', $userTeam) }}" class="btn btn-outline-success w-100">
                                        <i class="bi bi-people d-block mb-2" style="font-size: 1.5rem;"></i>
                                        {{ __('app.my_team') }}
                                    </a>
                                </div>
                            @else
                                <div class="col-md-6 col-lg-3">
                                    <a href="{{ route('teams.index') }}" class="btn btn-outline-success w-100">
                                        <i class="bi bi-people d-block mb-2" style="font-size: 1.5rem;"></i>
                                        {{ __('app.join_team') }}
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
                                <a href="{{ route('subjects.create') }}" class="btn btn-outline-info w-100">
                                    <i class="bi bi-plus-circle d-block mb-2" style="font-size: 1.5rem;"></i>
                                    {{ __('app.propose_external_subject') }}
                                </a>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <a href="{{ route('defenses.index') }}" class="btn btn-outline-warning w-100">
                                    <i class="bi bi-shield-check d-block mb-2" style="font-size: 1.5rem;"></i>
                                    {{ __('app.defense_schedule') }}
                                </a>
                            </div>
                            @break
                        @case('teacher')
                            <div class="col-md-6 col-lg-3">
                                <a href="{{ route('subjects.create') }}" class="btn btn-outline-primary w-100">
                                    <i class="bi bi-plus-circle d-block mb-2" style="font-size: 1.5rem;"></i>
                                    {{ __('app.add_subject') }}
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
                                <a href="{{ route('defenses.jury-assignments') }}" class="btn btn-outline-info w-100">
                                    <i class="bi bi-people d-block mb-2" style="font-size: 1.5rem;"></i>
                                    {{ __('app.jury_assignments') }}
                                </a>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <a href="{{ route('subjects.index') }}" class="btn btn-outline-warning w-100">
                                    <i class="bi bi-journal-text d-block mb-2" style="font-size: 1.5rem;"></i>
                                    {{ __('app.my_subjects') }}
                                </a>
                            </div>
                            @break
                        @case('department_head')
                            <div class="col-md-6 col-lg-3">
                                <a href="{{ route('subjects.pending-validation') }}" class="btn btn-outline-primary w-100">
                                    <i class="bi bi-check-circle d-block mb-2" style="font-size: 1.5rem;"></i>
                                    {{ __('app.validate_subjects') }}
                                </a>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <a href="{{ route('defenses.schedule-form') }}" class="btn btn-outline-success w-100">
                                    <i class="bi bi-calendar-plus d-block mb-2" style="font-size: 1.5rem;"></i>
                                    {{ __('app.schedule_defense') }}
                                </a>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <a href="{{ route('conflicts.index') }}" class="btn btn-outline-warning w-100">
                                    <i class="bi bi-exclamation-triangle d-block mb-2" style="font-size: 1.5rem;"></i>
                                    {{ __('app.resolve_conflicts') }}
                                </a>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <a href="{{ route('admin.reports') }}" class="btn btn-outline-info w-100">
                                    <i class="bi bi-graph-up d-block mb-2" style="font-size: 1.5rem;"></i>
                                    {{ __('app.reports') }}
                                </a>
                            </div>
                            @break
                        @case('admin')
                            <div class="col-md-6 col-lg-3">
                                <a href="{{ route('admin.users') }}" class="btn btn-outline-primary w-100">
                                    <i class="bi bi-people d-block mb-2" style="font-size: 1.5rem;"></i>
                                    {{ __('app.manage_users') }}
                                </a>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <a href="{{ route('admin.specialities.index') }}" class="btn btn-outline-success w-100">
                                    <i class="bi bi-mortarboard d-block mb-2" style="font-size: 1.5rem;"></i>
                                    {{ __('app.specialities') }}
                                </a>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <a href="{{ route('admin.rooms') }}" class="btn btn-outline-info w-100">
                                    <i class="bi bi-building d-block mb-2" style="font-size: 1.5rem;"></i>
                                    {{ __('app.manage_rooms') }}
                                </a>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <a href="{{ route('admin.reports') }}" class="btn btn-outline-warning w-100">
                                    <i class="bi bi-graph-up d-block mb-2" style="font-size: 1.5rem;"></i>
                                    {{ __('app.reports_analytics') }}
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
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-diagram-3 me-2"></i>{{ __('app.current_status') }}
                </h5>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>{{ __('app.current_phase') }}:</strong> {{ $workflowStatus['current_phase'] ?? __('app.getting_started') }}</p>
                <p class="mb-2"><strong>{{ __('app.status') }}:</strong> {{ $workflowStatus['status'] ?? __('app.in_progress') }}</p>
                @if(isset($workflowStatus['next_actions']) && count($workflowStatus['next_actions']) > 0)
                    <p class="mb-2"><strong>{{ __('app.next_actions') }}:</strong></p>
                    <ul class="mb-0">
                        @foreach($workflowStatus['next_actions'] as $action)
                            <li>{{ $action }}</li>
                        @endforeach
                    </ul>
                @endif
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
