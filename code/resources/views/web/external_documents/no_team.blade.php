@extends('layouts.pfe-app')

@section('title', 'External Documents')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">{{ __('External Documents') }}</h1>
        <a href="{{ route('dashboard.student') }}" class="btn btn-outline-secondary">
            <i class="bi bi-speedometer2"></i> {{ __('Dashboard') }}
        </a>
    </div>

    <div class="card border-warning">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="bi bi-exclamation-triangle"></i> {{ __('Team Required') }}</h5>
        </div>
        <div class="card-body text-center py-5">
            <i class="bi bi-people" style="font-size: 5rem; color: #ffc107;"></i>
            <h4 class="mt-4">{{ __('You Need to Join a Team') }}</h4>
            <p class="text-muted">
                {{ __('To access external documents and submit responses, you must be part of a team.') }}
            </p>

            <div class="mt-4">
                <a href="{{ route('teams.index') }}" class="btn btn-primary btn-lg">
                    <i class="bi bi-people-fill"></i> {{ __('Go to Teams') }}
                </a>
            </div>

            <hr class="my-4">

            <div class="row justify-content-center">
                <div class="col-md-8">
                    <h6>{{ __('What you can do:') }}</h6>
                    <ul class="list-unstyled text-start mt-3">
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success"></i>
                            {{ __('Create a new team and invite members') }}
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success"></i>
                            {{ __('Join an existing team using an invitation code') }}
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success"></i>
                            {{ __('Wait for a team invitation from other students') }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
