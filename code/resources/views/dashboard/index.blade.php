@extends('layouts.pfe-app')

@section('page-title', __('app.dashboard'))

@section('content')
<div class="container-fluid">
    <!-- Welcome Header -->
    <div class="text-center mb-4">
        <h3 class="mb-2">{{ __('app.welcome') }}, <strong>{{ auth()->user()->name }}</strong></h3>
        <p class="text-muted">{{ __('app.academic_year') }} {{ now()->format('Y') }}/{{ now()->addYear()->format('Y') }}</p>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-lightning me-2 text-warning"></i>{{ __('app.quick_actions') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @switch(auth()->user()->role)
                            @case('admin')
                                <div class="col-md-4 col-lg-3">
                                    <a href="{{ route('admin.users') }}" class="btn btn-primary text-white w-100 py-3">
                                        <i class="bi bi-people d-block mb-2" style="font-size: 2rem;"></i>
                                        <small>{{ __('app.manage_users') }}</small>
                                    </a>
                                </div>
                                <div class="col-md-4 col-lg-3">
                                    <a href="{{ route('admin.specialities.index') }}" class="btn btn-success text-white w-100 py-3">
                                        <i class="bi bi-mortarboard d-block mb-2" style="font-size: 2rem;"></i>
                                        <small>{{ __('app.specialities') }}</small>
                                    </a>
                                </div>
                                <div class="col-md-4 col-lg-3">
                                    <a href="{{ route('admin.academic-years.index') }}" class="btn btn-info text-white w-100 py-3">
                                        <i class="bi bi-calendar-range d-block mb-2" style="font-size: 2rem;"></i>
                                        <small>{{ __('app.academic_years_management') }}</small>
                                    </a>
                                </div>
                                <div class="col-md-4 col-lg-3">
                                    <a href="{{ route('admin.reports') }}" class="btn btn-warning text-white w-100 py-3">
                                        <i class="bi bi-graph-up d-block mb-2" style="font-size: 2rem;"></i>
                                        <small>{{ __('app.reports_analytics') }}</small>
                                    </a>
                                </div>
                                <div class="col-md-4 col-lg-3">
                                    <a href="{{ route('admin.settings') }}" class="btn btn-secondary text-white w-100 py-3">
                                        <i class="bi bi-gear d-block mb-2" style="font-size: 2rem;"></i>
                                        <small>{{ __('app.system_settings') }}</small>
                                    </a>
                                </div>
                                <div class="col-md-4 col-lg-3">
                                    <a href="{{ route('admin.users.bulk-import') }}" class="btn btn-dark text-white w-100 py-3">
                                        <i class="bi bi-upload d-block mb-2" style="font-size: 2rem;"></i>
                                        <small>Bulk Import</small>
                                    </a>
                                </div>
                                @break

                            @case('student')
                                @php
                                    $hasTeam = auth()->user()->teamMember;
                                    $userTeam = $hasTeam?->team;
                                @endphp
                                <div class="col-md-6 col-lg-3">
                                    <a href="{{ route('subjects.available') }}" class="btn btn-outline-primary w-100 py-3">
                                        <i class="bi bi-journal-text d-block mb-2" style="font-size: 2rem;"></i>
                                        <small>{{ __('app.available_subjects') }}</small>
                                    </a>
                                </div>
                                @if($hasTeam)
                                    <div class="col-md-6 col-lg-3">
                                        <a href="{{ route('teams.show', $userTeam) }}" class="btn btn-outline-success w-100 py-3">
                                            <i class="bi bi-people d-block mb-2" style="font-size: 2rem;"></i>
                                            <small>{{ __('app.my_team') }}</small>
                                        </a>
                                    </div>
                                @else
                                    <div class="col-md-6 col-lg-3">
                                        <a href="{{ route('teams.index') }}" class="btn btn-outline-success w-100 py-3">
                                            <i class="bi bi-people d-block mb-2" style="font-size: 2rem;"></i>
                                            <small>{{ __('app.join_team') }}</small>
                                        </a>
                                    </div>
                                @endif
                                <div class="col-md-6 col-lg-3">
                                    <a href="{{ route('subjects.create') }}" class="btn btn-outline-info w-100 py-3">
                                        <i class="bi bi-plus-circle d-block mb-2" style="font-size: 2rem;"></i>
                                        <small>{{ __('app.propose_external_subject') }}</small>
                                    </a>
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <a href="{{ route('defenses.index') }}" class="btn btn-outline-warning w-100 py-3">
                                        <i class="bi bi-shield-check d-block mb-2" style="font-size: 2rem;"></i>
                                        <small>{{ __('app.defense_schedule') }}</small>
                                    </a>
                                </div>
                                @break

                            @case('teacher')
                                <div class="col-md-6 col-lg-3">
                                    <a href="{{ route('subjects.create') }}" class="btn btn-outline-primary w-100 py-3">
                                        <i class="bi bi-plus-circle d-block mb-2" style="font-size: 2rem;"></i>
                                        <small>{{ __('app.add_subject') }}</small>
                                    </a>
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <a href="{{ route('defenses.jury-assignments') }}" class="btn btn-outline-info w-100 py-3">
                                        <i class="bi bi-people d-block mb-2" style="font-size: 2rem;"></i>
                                        <small>{{ __('app.jury_assignments') }}</small>
                                    </a>
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <a href="{{ route('subjects.index') }}" class="btn btn-outline-warning w-100 py-3">
                                        <i class="bi bi-journal-text d-block mb-2" style="font-size: 2rem;"></i>
                                        <small>{{ __('app.my_subjects') }}</small>
                                    </a>
                                </div>
                                @break

                            @case('department_head')
                                <div class="col-md-6 col-lg-3">
                                    <a href="{{ route('subjects.pending-validation') }}" class="btn btn-outline-primary w-100 py-3">
                                        <i class="bi bi-check-circle d-block mb-2" style="font-size: 2rem;"></i>
                                        <small>{{ __('app.validate_subjects') }}</small>
                                    </a>
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <a href="{{ route('defenses.schedule-form') }}" class="btn btn-outline-success w-100 py-3">
                                        <i class="bi bi-calendar-plus d-block mb-2" style="font-size: 2rem;"></i>
                                        <small>{{ __('app.schedule_defense') }}</small>
                                    </a>
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <a href="{{ route('conflicts.index') }}" class="btn btn-outline-warning w-100 py-3">
                                        <i class="bi bi-exclamation-triangle d-block mb-2" style="font-size: 2rem;"></i>
                                        <small>{{ __('app.resolve_conflicts') }}</small>
                                    </a>
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <a href="{{ route('admin.reports') }}" class="btn btn-outline-info w-100 py-3">
                                        <i class="bi bi-graph-up d-block mb-2" style="font-size: 2rem;"></i>
                                        <small>{{ __('app.reports') }}</small>
                                    </a>
                                </div>
                                @break
                        @endswitch
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(auth()->user()->role === 'admin')
    <!-- New Subjects (Admin Only) -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-journal-plus me-2 text-success"></i>{{ __('app.new_subjects') }}
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $newSubjects = \App\Models\Subject::where('created_at', '>=', now()->subDays(30))
                            ->with(['teacher', 'speciality'])
                            ->orderBy('created_at', 'desc')
                            ->limit(10)
                            ->get();
                    @endphp

                    @if($newSubjects->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('app.title') }}</th>
                                        <th>{{ __('app.teacher') }}</th>
                                        <th>{{ __('app.speciality') }}</th>
                                        <th>{{ __('app.created') }}</th>
                                        <th>{{ __('app.status') }}</th>
                                        <th>{{ __('app.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($newSubjects as $subject)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                                        <i class="bi bi-journal-text text-primary"></i>
                                                    </div>
                                                    <strong>{{ Str::limit($subject->title, 40) }}</strong>
                                                </div>
                                            </td>
                                            <td>{{ $subject->teacher->name }}</td>
                                            <td>{{ $subject->speciality->name ?? 'N/A' }}</td>
                                            <td>
                                                <small class="text-muted">{{ $subject->created_at->diffForHumans() }}</small>
                                            </td>
                                            <td>
                                                @if($subject->status === 'validated')
                                                    <span class="badge bg-success">{{ __('app.validated') }}</span>
                                                @elseif($subject->status === 'pending_validation')
                                                    <span class="badge bg-warning">{{ __('app.pending') }}</span>
                                                @elseif($subject->status === 'rejected')
                                                    <span class="badge bg-danger">{{ __('app.rejected') }}</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($subject->status) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('subjects.show', $subject) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                            <p class="mt-2">{{ __('app.no_new_subjects') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.table-hover tbody tr:hover {
    background-color: #f8f9fa;
}
</style>
@endsection
