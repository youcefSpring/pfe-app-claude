@extends('layouts.pfe-app')

@section('title', __('app.defense_details'))

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">{{ __('app.defense_details') }}</h1>
        <div class="btn-group">
            <a href="{{ route('defenses.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> {{ __('app.back_to_defenses') }}
            </a>
            @if(in_array(auth()->user()?->role, ['admin', 'department_head']))
                @if($defense->status === 'scheduled')
                    <a href="{{ route('defenses.edit', $defense) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> {{ __('app.edit') }}
                    </a>
                @endif
                @if($defense->status === 'completed')
                    <div class="btn-group">
                        <a href="{{ route('defenses.report', $defense) }}" class="btn btn-primary" target="_blank">
                            <i class="bi bi-file-text"></i> {{ __('app.view_report') }}
                        </a>
                        <button type="button" class="btn btn-danger dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">
                            <span class="visually-hidden">{{ __('app.toggle_dropdown') }}</span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><h6 class="dropdown-header">{{ __('app.individual_reports') }}</h6></li>
                            @if($defense->project && $defense->project->team)
                                @foreach($defense->project->team->members as $member)
                                    <li>
                                        <a class="dropdown-item" href="{{ route('defenses.download-student-report-pdf', [$defense, $member->user]) }}">
                                            <i class="bi bi-person-fill"></i> {{ $member->user->name }}
                                        </a>
                                    </li>
                                @endforeach
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-primary fw-bold" href="{{ route('defenses.download-batch-reports-pdf', $defense) }}">
                                        <i class="bi bi-archive-fill"></i> {{ __('app.download_all_zip') }}
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                @endif
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Defense Information -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('app.defense_information') }}</h5>
                    <span class="badge bg-{{ $defense->status === 'completed' ? 'success' : ($defense->status === 'in_progress' ? 'warning' : ($defense->status === 'cancelled' ? 'danger' : 'primary')) }} fs-6">
                        {{ __('app.defense_status_' . $defense->status) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">{{ __('app.subject_information') }}</h6>
                            <p><strong>{{ __('app.subject') }}:</strong> {{ $defense->subject->title ?? __('app.not_available') }}</p>
                            <p><strong>{{ __('app.teacher') }}:</strong> {{ $defense->subject->teacher->name ?? __('app.not_available') }}</p>
                            <p><strong>{{ __('app.type') }}:</strong> {{ $defense->subject->is_external ? __('app.external') : __('app.internal') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">{{ __('app.defense_details') }}</h6>
                            <p><strong>{{ __('app.date') }}:</strong> {{ $defense->defense_date ? \Carbon\Carbon::parse($defense->defense_date)->format('M d, Y') : __('app.to_be_determined') }}</p>
                            <p><strong>{{ __('app.time') }}:</strong> {{ $defense->defense_time ? \Carbon\Carbon::parse($defense->defense_time)->format('g:i A') : __('app.to_be_determined') }}</p>
                            <p><strong>{{ __('app.duration') }}:</strong> {{ $defense->duration ?? 60 }} {{ __('app.minutes') }}</p>
                            <p><strong>{{ __('app.room') }}:</strong> {{ $defense->room->name ?? __('app.to_be_determined') }}
                                @if($defense->room && $defense->room->location)
                                    <small class="text-muted">({{ $defense->room->location }})</small>
                                @endif
                            </p>
                        </div>
                    </div>

                    @if($defense->notes)
                        <div class="mt-3">
                            <h6 class="text-muted">{{ __('app.notes') }}</h6>
                            <p class="text-muted">{{ $defense->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Team Members -->
            @if($defense->project && $defense->project->team && $defense->project->team->members->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('app.team_members') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($defense->project->team->members as $member)
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                                                {{ substr($member->user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <h6 class="mb-1">{{ $member->user->name }}</h6>
                                                <small class="text-muted">{{ $member->user->email }}</small>
                                                @if($member->is_leader)
                                                    <span class="badge bg-success ms-2">{{ __('app.leader') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        @if($defense->status === 'completed' && in_array(auth()->user()?->role, ['admin', 'department_head']))
                                            <a href="{{ route('defenses.download-student-report-pdf', [$defense, $member->user]) }}"
                                               class="btn btn-sm btn-outline-danger"
                                               title="{{ __('app.download_student_report', ['name' => $member->user->name]) }}">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($defense->status === 'completed' && in_array(auth()->user()?->role, ['admin', 'department_head']))
                            <div class="border-top pt-3 mt-3">
                                <div class="d-flex justify-content-center">
                                    <a href="{{ route('defenses.download-batch-reports-pdf', $defense) }}"
                                       class="btn btn-primary">
                                        <i class="bi bi-archive-fill"></i> {{ __('app.download_all_reports_zip') }}
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Jury Members -->
            @if($defense->juries->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('app.jury_members') }}</h5>
                    </div>
                    <div class="card-body">
                        @foreach($defense->juries as $jury)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <h6 class="mb-1">{{ $jury->teacher->name }}</h6>
                                    <small class="text-muted">{{ $jury->role }}</small>
                                </div>
                                @if($defense->status === 'completed' && $jury->grade)
                                    <span class="badge bg-info">{{ $jury->grade }}/20</span>
                                @endif
                            </div>
                            @if(!$loop->last)
                                <hr class="my-2">
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Actions for Jury Members -->
            @if(auth()->user()?->role === 'teacher' && $defense->juries->contains('teacher_id', auth()->id()))
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('app.jury_actions') }}</h5>
                    </div>
                    <div class="card-body">
                        @if($defense->status === 'scheduled' || $defense->status === 'in_progress')
                            <p class="text-muted">{{ __('app.jury_member_assigned') }}</p>
                            @if($defense->status === 'scheduled')
                                <div class="alert alert-info">
                                    <small>{{ __('app.defense_scheduled_evaluation') }}</small>
                                </div>
                            @endif
                            @if($defense->status === 'in_progress')
                                @php
                                    $isPresident = $defense->juries->where('teacher_id', auth()->id())->where('role', 'president')->count() > 0;
                                @endphp
                                @if($isPresident)
                                    <div class="card border-primary mt-3">
                                        <div class="card-header bg-primary text-white">
                                            <h6 class="mb-0">{{ __('app.pv_de_soutenance') }}</h6>
                                        </div>
                                        <div class="card-body">
                                            <form action="{{ route('defenses.add-pv-notes', $defense) }}" method="POST">
                                                @csrf
                                                <div class="mb-3">
                                                    <label for="pv_notes" class="form-label">{{ __('app.defense_proceedings_notes') }}</label>
                                                    <textarea class="form-control" id="pv_notes" name="pv_notes" rows="4"
                                                              placeholder="{{ __('app.enter_defense_proceedings') }}">{{ old('pv_notes', $defense->pv_notes) }}</textarea>
                                                </div>
                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    <i class="bi bi-save"></i> {{ __('app.save_proceedings') }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        @elseif($defense->status === 'completed')
                            <div class="alert alert-success">
                                <small>{{ __('app.defense_completed_grades') }}</small>
                            </div>
                            @if($defense->pv_notes)
                                <div class="card border-success mt-3">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0">{{ __('app.pv_de_soutenance') }}</h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-0">{{ $defense->pv_notes }}</p>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            @endif

            <!-- Actions for Team Members -->
            @if($isTeamMember)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('app.team_actions') }}</h5>
                    </div>
                    <div class="card-body">
                        @if($defense->status === 'scheduled')
                            <div class="alert alert-info">
                                <strong>{{ __('app.defense_scheduled_notice') }}</strong><br>
                                <small>{{ __('app.be_present_notice') }}</small>
                            </div>
                        @elseif($defense->status === 'completed')
                            <div class="alert alert-success">
                                <strong>{{ __('app.defense_completed_notice') }}</strong><br>
                                <small>{{ __('app.check_supervisor_results') }}</small>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Defense Grades Form for Admin/President -->
            @if(in_array(auth()->user()?->role, ['admin', 'department_head']) && $defense->status === 'completed')
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('app.defense_grades') }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('defenses.update-grades', $defense) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="manuscript_grade" class="form-label">{{ __('app.manuscript_grade') }} (6/8)</label>
                                <input type="number" class="form-control @error('manuscript_grade') is-invalid @enderror"
                                       id="manuscript_grade" name="manuscript_grade"
                                       value="{{ old('manuscript_grade', $defense->manuscript_grade) }}"
                                       step="0.01" min="0" max="8">
                                @error('manuscript_grade')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="oral_grade" class="form-label">{{ __('app.oral_grade') }} (4/6)</label>
                                <input type="number" class="form-control @error('oral_grade') is-invalid @enderror"
                                       id="oral_grade" name="oral_grade"
                                       value="{{ old('oral_grade', $defense->oral_grade) }}"
                                       step="0.01" min="0" max="6">
                                @error('oral_grade')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="questions_grade" class="form-label">{{ __('app.questions_grade') }} (5/6)</label>
                                <input type="number" class="form-control @error('questions_grade') is-invalid @enderror"
                                       id="questions_grade" name="questions_grade"
                                       value="{{ old('questions_grade', $defense->questions_grade) }}"
                                       step="0.01" min="0" max="6">
                                @error('questions_grade')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="realization_grade" class="form-label">{{ __('app.realization_grade') }} (5/-)</label>
                                <input type="number" class="form-control @error('realization_grade') is-invalid @enderror"
                                       id="realization_grade" name="realization_grade"
                                       value="{{ old('realization_grade', $defense->realization_grade) }}"
                                       step="0.01" min="0" max="20">
                                @error('realization_grade')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="final_grade" class="form-label">{{ __('app.final_grade') }} (20)</label>
                                <input type="number" class="form-control @error('final_grade') is-invalid @enderror"
                                       id="final_grade" name="final_grade"
                                       value="{{ old('final_grade', $defense->final_grade) }}"
                                       step="0.01" min="0" max="20">
                                @error('final_grade')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> {{ __('app.save_grades') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 40px;
    height: 40px;
    font-size: 16px;
    font-weight: 600;
}
</style>
@endsection