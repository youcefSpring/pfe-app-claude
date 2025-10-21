@extends('layouts.pfe-app')

@section('page-title', __('app.my_team'))

@section('content')
<div class="container-fluid">
    @if($team)
        <!-- Team Information -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">{{ __('app.my_team') }}: {{ $team->name }}</h4>
                        <small class="text-muted">{{ __('app.team_details_and_members') }}</small>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>{{ __('app.team_information') }}</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>{{ __('app.team_name') }}:</strong></td>
                                        <td>{{ $team->name }}</td>
                                    </tr>
                                    @if($team->description)
                                    <tr>
                                        <td><strong>{{ __('app.description') }}:</strong></td>
                                        <td>{{ $team->description }}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td><strong>{{ __('app.created_at') }}:</strong></td>
                                        <td>{{ $team->created_at->format('d/m/Y') }}</td>
                                    </tr>
                                    @if($team->subject)
                                    <tr>
                                        <td><strong>{{ __('app.subject') }}:</strong></td>
                                        <td>{{ $team->subject->title }}</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6>{{ __('app.team_members') }}</h6>
                                @if($team->members->count() > 0)
                                    <div class="list-group">
                                        @foreach($team->members as $member)
                                            <div class="list-group-item">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h6 class="mb-1">{{ $member->user->name }}</h6>
                                                        <small>{{ $member->user->email }}</small>
                                                        @if($member->user->matricule)
                                                            <br><small class="text-muted">{{ __('app.matricule') }}: {{ $member->user->matricule }}</small>
                                                        @endif
                                                    </div>
                                                    @if($member->role === 'leader')
                                                        <span class="badge bg-primary">{{ __('app.leader') }}</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ __('app.member') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted">{{ __('app.no_members_yet') }}</p>
                                @endif
                            </div>
                        </div>

                        @if($team->project)
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h6>{{ __('app.project_information') }}</h6>
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6>{{ $team->project->title }}</h6>
                                            @if($team->project->description)
                                                <p class="mb-2">{{ $team->project->description }}</p>
                                            @endif
                                            @if($team->project->supervisor)
                                                <small class="text-muted">{{ __('app.supervisor') }}: {{ $team->project->supervisor->name }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="mt-4">
                            @php
                                $user = auth()->user();
                                $isLeader = $team->members->where('student_id', $user->id)->where('role', 'leader')->first();
                                $currentDeadline = \App\Models\AllocationDeadline::active()->first();
                                $canManagePreferences = $isLeader &&
                                                      $currentDeadline &&
                                                      $currentDeadline->canStudentsChoose() &&
                                                      $team->canManagePreferences();
                                $hasProject = $team->project()->exists();
                            @endphp

                            @if($canManagePreferences && !$hasProject)
                                <a href="{{ route('teams.subject-preferences', $team) }}" class="btn btn-success me-2">
                                    <i class="fas fa-list"></i> {{ __('app.manage_preferences') }}
                                </a>
                            @endif

                            @if($isLeader)
                                <a href="{{ route('teams.edit', $team) }}" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> {{ __('app.edit_team') }}
                                </a>
                            @endif

                            @if($hasProject && $isLeader)
                                <a href="{{ route('projects.show', $team->project) }}" class="btn btn-info ms-2">
                                    <i class="fas fa-project-diagram"></i> {{ __('app.view_project') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- No Team - Show Options -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">{{ __('app.my_team') }}</h4>
                        <small class="text-muted">{{ __('app.you_are_not_in_team') }}</small>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <i class="fas fa-plus fa-3x mb-3"></i>
                                        <h5>{{ __('app.create_new_team') }}</h5>
                                        <p>{{ __('app.create_team_description') }}</p>
                                        <a href="{{ route('teams.create') }}" class="btn btn-light">
                                            <i class="fas fa-plus"></i> {{ __('app.create_team') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <i class="fas fa-users fa-3x mb-3"></i>
                                        <h5>{{ __('app.join_existing_team') }}</h5>
                                        <p>{{ __('app.join_team_description') }}</p>
                                        <a href="{{ route('teams.index') }}" class="btn btn-light">
                                            <i class="fas fa-search"></i> {{ __('app.browse_teams') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if(isset($availableTeams) && $availableTeams->count() > 0)
                            <div class="mt-4">
                                <h6>{{ __('app.available_teams_to_join') }}</h6>
                                <div class="row">
                                    @foreach($availableTeams as $availableTeam)
                                        <div class="col-md-6 mb-3">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h6 class="card-title">{{ $availableTeam->name }}</h6>
                                                    @if($availableTeam->description)
                                                        <p class="card-text small">{{ Str::limit($availableTeam->description, 100) }}</p>
                                                    @endif
                                                    <div class="mb-2">
                                                        <small class="text-muted">
                                                            {{ __('app.members') }}: {{ $availableTeam->members->count() }}/2
                                                        </small>
                                                    </div>
                                                    <a href="{{ route('teams.show', $availableTeam) }}" class="btn btn-sm btn-outline-primary">
                                                        {{ __('app.view_team') }}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection