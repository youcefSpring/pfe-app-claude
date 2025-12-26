@extends('layouts.pfe-app')

@section('page-title', __('app.my_invitations'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-inbox"></i> {{ __('app.pending_team_invitations') }}
                    </h4>
                </div>
                <div class="card-body">
                    @if($invitations->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">{{ __('app.no_pending_invitations') }}</h5>
                            <p class="text-muted">{{ __('app.when_invited_see_here') }}</p>
                        </div>
                    @else
                        <div class="row">
                            @foreach($invitations as $invitation)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100 border-primary">
                                        <div class="card-header bg-light">
                                            <h6 class="card-title mb-0">
                                                <i class="fas fa-users"></i> {{ $invitation->team->name }}
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <p class="card-text">
                                                <strong>{{ __('app.invited_by') }}:</strong><br>
                                                {{ $invitation->invitedBy->name }}
                                            </p>
                                            <p class="card-text">
                                                <strong>{{ __('app.current_members') }}:</strong>
                                                {{ $invitation->team->members()->count() }}/4
                                            </p>
                                            <p class="card-text">
                                                <strong>{{ __('app.expires_on') }}:</strong><br>
                                                <span class="text-danger">{{ $invitation->expires_at->format('d/m/Y H:i') }}</span>
                                            </p>

                                            <div class="mb-3">
                                                <h6>{{ __('app.team_members') }}:</h6>
                                                @foreach($invitation->team->members as $member)
                                                    <small class="d-block">
                                                        {{ $member->user->name }}
                                                        @if($member->role === 'leader')
                                                            <span class="badge bg-primary badge-sm">{{ __('app.leader') }}</span>
                                                        @endif
                                                    </small>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <div class="row">
                                                <div class="col-6">
                                                    <form action="{{ route('teams.accept-invitation', $invitation->token) }}" method="POST" class="d-grid">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm">
                                                            <i class="fas fa-check"></i> {{ __('app.accept') }}
                                                        </button>
                                                    </form>
                                                </div>
                                                <div class="col-6">
                                                    <form action="{{ route('teams.decline-invitation', $invitation->token) }}" method="POST" class="d-grid">
                                                        @csrf
                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                            <i class="fas fa-times"></i> {{ __('app.decline') }}
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="mt-2 d-grid">
                                                <a href="{{ route('teams.show-invitation', $invitation->token) }}" class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-eye"></i> {{ __('app.view_invitation') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection