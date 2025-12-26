@extends('layouts.pfe-app')

@section('title', __('app.teams'))

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-2">
            <h1 class="h3 mb-0">{{ __('app.teams') }}</h1>
            <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#pageHelpModal">
                <i class="bi bi-question-circle"></i>
            </button>
        </div>
        @if(auth()->user()?->role === 'student' && isset($canModifyTeams) && $canModifyTeams)
            <a href="{{ route('teams.create') }}" class="btn btn-primary">
                <i class="bi bi-plus"></i> {{ __('app.create_team') }}
            </a>
        @endif
    </div>

    <!-- Deadline Information -->
    @if(isset($currentDeadline) && $currentDeadline)
        <div class="alert {{ $canModifyTeams ? 'alert-info' : 'alert-warning' }} mb-4">
            <i class="bi bi-{{ $canModifyTeams ? 'info-circle' : 'exclamation-triangle' }}"></i>
            @if($canModifyTeams)
                <strong>{{ __('app.team_formation_active') }}</strong>
                <br>{{ __('app.deadline') }}: {{ $currentDeadline->preferences_deadline->format('d/m/Y \\à H:i') }}
                <br><small>{{ $currentDeadline->getRemainingTimeForPreferences() }}</small>
            @else
                <strong>{{ __('app.team_formation_ended') }}</strong>
                <br><small>{{ __('app.deadline_was') }}: {{ $currentDeadline->preferences_deadline->format('d/m/Y \\à H:i') }}</small>
            @endif
        </div>
    @endif

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('teams.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="status" class="form-label">{{ __('app.status') }}</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">{{ __('app.all_statuses') }}</option>
                        <option value="forming" {{ request('status') === 'forming' ? 'selected' : '' }}>{{ __('app.forming') }}</option>
                        <option value="complete" {{ request('status') === 'complete' ? 'selected' : '' }}>{{ __('app.complete') }}</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('app.active') }}</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="search" class="form-label">{{ __('app.search') }}</label>
                    <input type="text" class="form-control" id="search" name="search"
                           value="{{ request('search') }}" placeholder="{{ __('app.search') }} {{ __('app.teams') }}...">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-outline-primary">{{ __('app.filter') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Results Info -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="text-muted">
            {{ __('app.showing') }} {{ $teams->firstItem() ?? 0 }} {{ __('app.to') }} {{ $teams->lastItem() ?? 0 }}
            {{ __('app.of') }} {{ $teams->total() }} {{ __('app.results') }}
        </div>
        @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('teams.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-x-circle"></i> {{ __('app.clear_filters') }}
            </a>
        @endif
    </div>

    <!-- Teams Table -->
    <div class="card">
        <div class="card-body">
            @if($teams->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('app.team_name') }}</th>
                                <th>{{ __('app.members') }}</th>
                                <th>{{ __('app.leader_and_created') }}</th>
                                <th>{{ __('app.status') }}</th>
                                <th>{{ __('app.subject') }}</th>
                                <th>{{ __('app.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($teams as $team)
                                @php
                                    $userIsMember = $team->members->contains('student_id', auth()->id());
                                    $isLeader = $team->members->where('student_id', auth()->id())->where('role', 'leader')->count() > 0;
                                    $leader = $team->members->where('role', 'leader')->first();
                                @endphp
                                <tr>
                                    <td>
                                        <div>
                                            <strong>{{ $team->name }}</strong>
                                            @if($team->description)
                                                <div class="text-muted small">{{ Str::limit($team->description, 60) }}</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($team->members->take(3) as $member)
                                                <span class="badge bg-light text-dark small">
                                                    {{ $member->user?->name ?? '' }}
                                                </span>
                                            @endforeach
                                            @if($team->members->count() > 3)
                                                <span class="badge bg-secondary small">+{{ $team->members->count() - 3 }}</span>
                                            @endif
                                        </div>
                                        <small class="text-muted">{{ $team->members->count() }} {{ __('app.total') }}</small>
                                    </td>
                                    <td>
                                        @if($leader)
                                            <div class="text-nowrap">
                                                <i class="bi bi-star-fill text-warning"></i>
                                                {{ $leader->user->name }}
                                            </div>
                                        @else
                                            <div class="text-muted">{{ __('app.no_leader') }}</div>
                                        @endif
                                        <small class="text-muted">{{ $team->created_at->format('M d, Y') }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $team->status === 'active' ? 'success' : ($team->status === 'complete' ? 'primary' : 'warning') }}">
                                            {{ __('app.team_status_' . $team->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($team->project && $team->project->subject)
                                            <span class="text-nowrap" title="{{ $team->project->subject->title }}">
                                                <i class="bi bi-journal"></i>
                                                {{ Str::limit($team->project->subject->title, 25) }}
                                            </span>
                                            @if(auth()->user()?->role === 'teacher')
                                                <br><small class="text-success">{{ __('app.your_subject') }}</small>
                                            @endif
                                        @else
                                            <span class="text-muted">
                                                <i class="bi bi-question-circle"></i> {{ __('app.not_selected') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('teams.show', $team) }}"
                                               class="btn btn-outline-primary btn-sm"
                                               title="{{ __('app.view_details') }}">
                                                <i class="bi bi-eye"></i>
                                            </a>

                                            @if(auth()->user()?->role === 'student')
                                                @if(!$userIsMember && $team->status === 'forming')
                                                    <form method="POST" action="{{ route('teams.join', $team) }}" class="d-inline">
                                                        @csrf
                                                        <button type="submit"
                                                                class="btn btn-outline-success btn-sm"
                                                                title="{{ __('app.join_team') }}">
                                                            <i class="bi bi-person-plus"></i>
                                                        </button>
                                                    </form>
                                                @elseif($userIsMember && !$isLeader)
                                                    <form method="POST" action="{{ route('teams.leave', $team) }}" class="d-inline">
                                                        @csrf
                                                        <button type="submit"
                                                                class="btn btn-outline-danger btn-sm"
                                                                title="{{ __('app.leave_team') }}"
                                                                onclick="return confirmLeave('{{ __('app.confirm_leave_team') }}', null, this.form)">
                                                            <i class="bi bi-person-dash"></i>
                                                        </button>
                                                    </form>
                                                @elseif($isLeader)
                                                    <a href="{{ route('teams.edit', $team) }}"
                                                       class="btn btn-outline-warning btn-sm"
                                                       title="{{ __('app.edit_team') }}">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    @if($team->members->count() > 1)
                                                        <button type="button"
                                                                class="btn btn-outline-info btn-sm"
                                                                title="{{ __('app.transfer_leadership') }}"
                                                                data-bs-toggle="tooltip">
                                                            <i class="bi bi-arrow-repeat"></i>
                                                        </button>
                                                    @endif
                                                    <form method="POST" action="{{ route('teams.leave', $team) }}" class="d-inline">
                                                        @csrf
                                                        <button type="submit"
                                                                class="btn btn-outline-danger btn-sm"
                                                                title="{{ __('app.leave_team') }}"
                                                                onclick="return confirmLeave('{{ __('app.confirm_leave_team_leader') }}', null, this.form)">
                                                            <i class="bi bi-person-dash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-people text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3">{{ __('app.no_teams_found') }}</h4>
                    <p class="text-muted">
                        @if(request()->hasAny(['search', 'status']))
                            {{ __('app.no_teams_match_filters') }}
                        @elseif(auth()->user()?->role === 'teacher')
                            {{ __('app.no_teams_selected_subjects') }}
                        @else
                            {{ __('app.no_teams_available') }}
                        @endif
                    </p>
                    @if(auth()->user()?->role === 'student' && isset($canModifyTeams) && $canModifyTeams)
                        <a href="{{ route('teams.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus"></i> {{ __('app.create_first_team') }}
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Pagination -->
    @if($teams->hasPages())
        <div class="d-flex justify-content-center mt-4">
            <nav aria-label="{{ __('app.teams_pagination') }}">
                {{ $teams->appends(request()->query())->links('pagination::bootstrap-4') }}
            </nav>
        </div>
    @endif

    <!-- Page Help Modal -->
    <x-info-modal id="pageHelpModal" title="{{ __('app.teams_page_help') }}" icon="bi-people">
        <h6>{{ __('app.what_is_this_page') }}</h6>
        <p>{{ __('app.teams_page_description') }}</p>

        <h6>{{ __('app.how_to_use') }}</h6>
        <ul>
            <li><strong>{{ __('app.view_teams') }}:</strong> {{ __('app.view_teams_help') }}</li>
            @if(auth()->user()?->role === 'student')
                <li><strong>{{ __('app.create_team') }}:</strong> {{ __('app.create_team_help') }}</li>
                <li><strong>{{ __('app.join_team') }}:</strong> {{ __('app.join_team_help') }}</li>
                <li><strong>{{ __('app.manage_team') }}:</strong> {{ __('app.manage_team_help') }}</li>
            @endif
            @if(auth()->user()?->role === 'admin')
                <li><strong>{{ __('app.assign_subject') }}:</strong> {{ __('app.assign_subject_team_help') }}</li>
                <li><strong>{{ __('app.monitor_teams') }}:</strong> {{ __('app.monitor_teams_help') }}</li>
            @endif
        </ul>

        @if(auth()->user()?->role === 'student')
            <h6>{{ __('app.team_formation_tips') }}</h6>
            <ul>
                <li>{{ __('app.team_tip_1') }}</li>
                <li>{{ __('app.team_tip_2') }}</li>
                <li>{{ __('app.team_tip_3') }}</li>
            </ul>
        @endif
    </x-info-modal>
</div>
@endsection