@extends('layouts.pfe-app')

@section('page-title', __('app.team_invitation'))

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-envelope"></i> {{ __('app.team_invitation') }}
                    </h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5>{{ __('app.you_have_been_invited_to_join') }} <strong>{{ $invitation->team->name }}</strong></h5>
                        <p class="mb-2">
                            <strong>{{ __('app.invited_by') }}:</strong> {{ $invitation->invitedBy->name }} ({{ $invitation->invitedBy->email }})
                        </p>
                        <p class="mb-0">
                            <strong>{{ __('app.invitation_expires_on') }}:</strong>
                            <span class="text-danger">{{ $invitation->expires_at->format('d/m/Y H:i') }}</span>
                        </p>
                    </div>

                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('app.team_details') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>{{ __('app.team_name') }}:</strong> {{ $invitation->team->name }}</p>
                                    <p><strong>{{ __('app.status') }}:</strong>
                                        <span class="badge bg-{{ $invitation->team->status === 'forming' ? 'warning' : 'success' }}">
                                            {{ __('app.' . $invitation->team->status) }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>{{ __('app.current_members') }}:</strong> {{ $invitation->team->members()->count() }}/4</p>
                                </div>
                            </div>

                            <h6 class="mt-3">{{ __('app.current_members') }}:</h6>
                            <div class="list-group">
                                @foreach($invitation->team->members as $member)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $member->user->name }}</strong>
                                            @if($member->role === 'leader')
                                                <span class="badge bg-primary ms-2">{{ __('app.leader') }}</span>
                                            @endif
                                            <br>
                                            <small class="text-muted">{{ $member->user->email }}</small>
                                        </div>
                                        <small class="text-muted">
                                            {{ __('app.joined') }}: {{ $member->joined_at->format('d/m/Y') }}
                                        </small>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <form action="{{ route('teams.accept-invitation', $invitation->token) }}" method="POST" class="d-grid">
                                @csrf
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-check"></i> {{ __('app.accept_invitation') }}
                                </button>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <form action="{{ route('teams.decline-invitation', $invitation->token) }}" method="POST" class="d-grid">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-lg">
                                    <i class="fas fa-times"></i> {{ __('app.decline_invitation') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection