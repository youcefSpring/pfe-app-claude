@extends('layouts.pfe-app')

@section('page-title', __('app.welcome_to_setup'))

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-user-graduate me-2"></i>
                        {{ __('app.welcome_to_profile_setup') }}
                    </h4>
                </div>
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-clipboard-list fa-4x text-primary mb-3"></i>
                        <h2>{{ __('app.hello') }}, {{ $user->name }}!</h2>
                        <p class="lead text-muted">{{ __('app.complete_profile_setup_description') }}</p>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <h5 class="alert-heading">
                                    <i class="fas fa-info-circle me-2"></i>
                                    {{ __('app.setup_will_collect') }}
                                </h5>
                                <ul class="list-unstyled mb-0">
                                    <li><i class="fas fa-check text-success me-2"></i>{{ __('app.birth_date_and_place') }}</li>
                                    <li><i class="fas fa-check text-success me-2"></i>{{ __('app.birth_certificate_upload') }}</li>
                                    <li><i class="fas fa-check text-success me-2"></i>{{ __('app.academic_level_info') }}</li>
                                    <li><i class="fas fa-check text-success me-2"></i>{{ __('app.previous_semester_marks') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card border-warning">
                                <div class="card-body">
                                    <h6 class="card-title text-warning">
                                        <i class="fas fa-graduation-cap me-2"></i>
                                        {{ __('app.licence_3_students') }}
                                    </h6>
                                    <p class="card-text small">{{ __('app.licence_3_requirements') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-info">
                                <div class="card-body">
                                    <h6 class="card-title text-info">
                                        <i class="fas fa-university me-2"></i>
                                        {{ __('app.master_students') }}
                                    </h6>
                                    <p class="card-text small">{{ __('app.master_requirements') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="{{ route('student.setup.personal-info') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-arrow-right me-2"></i>
                            {{ __('app.start_setup') }}
                        </a>
                    </div>

                    <p class="text-muted small mt-3">
                        <i class="fas fa-clock me-1"></i>
                        {{ __('app.setup_takes_5_minutes') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection