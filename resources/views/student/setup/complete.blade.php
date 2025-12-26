@extends('layouts.pfe-app')

@section('page-title', __('app.setup_complete'))

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ __('app.profile_setup_complete') }}
                        </h4>
                        <span class="badge bg-light text-dark">{{ __('app.step') }} 3/3</span>
                    </div>
                </div>
                <div class="card-body text-center py-5">
                    <div class="progress mb-4" style="height: 10px;">
                        <div class="progress-bar bg-success" style="width: 100%"></div>
                    </div>

                    <div class="mb-4">
                        <i class="fas fa-check-circle fa-5x text-success mb-3"></i>
                        <h2 class="text-success">{{ __('app.congratulations') }}!</h2>
                        <p class="lead">{{ __('app.profile_setup_success_message') }}</p>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-info-circle text-info me-2"></i>
                                        {{ __('app.what_happens_next') }}
                                    </h5>
                                    <div class="row text-start">
                                        <div class="col-md-6">
                                            <ul class="list-unstyled">
                                                <li class="mb-2">
                                                    <i class="fas fa-clock text-warning me-2"></i>
                                                    {{ __('app.birth_certificate_review') }}
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-envelope text-primary me-2"></i>
                                                    {{ __('app.notification_when_approved') }}
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <ul class="list-unstyled">
                                                <li class="mb-2">
                                                    <i class="fas fa-users text-success me-2"></i>
                                                    {{ __('app.can_join_create_teams') }}
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-book text-info me-2"></i>
                                                    {{ __('app.can_browse_subjects') }}
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card border-warning">
                                <div class="card-body">
                                    <h6 class="card-title text-warning">
                                        <i class="fas fa-certificate me-2"></i>
                                        {{ __('app.birth_certificate_status') }}
                                    </h6>
                                    <p class="card-text">
                                        <span class="badge bg-warning">{{ __('app.pending_review') }}</span>
                                    </p>
                                    <small class="text-muted">{{ __('app.admin_will_review') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-success">
                                <div class="card-body">
                                    <h6 class="card-title text-success">
                                        <i class="fas fa-chart-line me-2"></i>
                                        {{ __('app.marks_submitted') }}
                                    </h6>
                                    <p class="card-text">
                                        <span class="badge bg-success">{{ __('app.completed') }}</span>
                                    </p>
                                    <small class="text-muted">{{ $user->getRequiredPreviousMarks() }} {{ __('app.marks_recorded') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="fas fa-lightbulb me-2"></i>
                            {{ __('app.getting_started_tips') }}
                        </h6>
                        <ul class="list-unstyled mb-0 text-start">
                            <li class="mb-1">• {{ __('app.tip_explore_dashboard') }}</li>
                            <li class="mb-1">• {{ __('app.tip_browse_subjects') }}</li>
                            <li class="mb-1">• {{ __('app.tip_find_teammates') }}</li>
                            <li class="mb-1">• {{ __('app.tip_check_notifications') }}</li>
                        </ul>
                    </div>

                    <form action="{{ route('student.setup.finish') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-home me-2"></i>
                            {{ __('app.go_to_dashboard') }}
                        </button>
                    </form>

                    <p class="text-muted small mt-3">
                        {{ __('app.can_update_profile_later') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection