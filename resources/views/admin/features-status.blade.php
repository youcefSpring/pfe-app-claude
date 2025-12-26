@extends('layouts.pfe-app')

@section('title', __('app.features_status_dashboard'))

@section('content')
<div class="container-fluid">
    <div class="page-header mb-4">
        <h1 class="page-title">
            <i class="bi bi-toggles"></i>
            {{ __('app.features_status_dashboard') }}
        </h1>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                {{ __('app.features_status_description') ?? 'Ce tableau de bord affiche l\'état de toutes les fonctionnalités du système. Les fonctionnalités désactivées empêchent les utilisateurs d\'accéder aux routes associées.' }}
            </div>
        </div>
    </div>

    <div class="row">
        @foreach($features as $feature)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card feature-card h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="feature-icon bg-{{ $feature['color'] }} bg-opacity-10 p-3 rounded">
                            <i class="{{ $feature['icon'] }} text-{{ $feature['color'] }}" style="font-size: 2rem;"></i>
                        </div>
                        <div>
                            @if($feature['enabled'])
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>{{ __('app.feature_enabled') }}
                                </span>
                            @else
                                <span class="badge bg-danger">
                                    <i class="bi bi-x-circle me-1"></i>{{ __('app.feature_disabled_short') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <h5 class="card-title">{{ $feature['name'] }}</h5>
                    <p class="card-text text-muted small">
                        <strong>{{ __('app.setting_key') ?? 'Clé' }}:</strong> <code>{{ $feature['key'] }}</code>
                    </p>

                    <div class="mt-3">
                        <a href="{{ route('admin.settings') }}" class="btn btn-sm btn-outline-{{ $feature['color'] }}">
                            <i class="bi bi-gear me-1"></i>{{ __('app.configure') ?? 'Configurer' }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-shield-lock me-2"></i>{{ __('app.middleware_protection') ?? 'Protection par Middleware' }}
                    </h5>
                </div>
                <div class="card-body">
                    <p>{{ __('app.middleware_description') ?? 'Les middlewares suivants protègent les routes de l\'application:' }}</p>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <code>check.team.formation</code> - {{ __('app.team_formation_disabled') }}
                        </li>
                        <li class="list-group-item">
                            <code>check.student.subject</code> - {{ __('app.student_subject_creation_disabled') }}
                        </li>
                        <li class="list-group-item">
                            <code>check.preferences</code> - {{ __('app.preferences_disabled') }}
                        </li>
                        <li class="list-group-item">
                            <code>check.registration</code> - {{ __('app.registration_closed') }}
                        </li>
                        <li class="list-group-item">
                            <code>check.external.projects</code> - {{ __('app.external_projects_disabled') }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12 text-center">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>{{ __('app.back_to_dashboard') }}
            </a>
            <a href="{{ route('admin.settings') }}" class="btn btn-primary">
                <i class="bi bi-gear me-2"></i>{{ __('app.system_settings') }}
            </a>
        </div>
    </div>
</div>

<style>
.feature-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: none;
}

.feature-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
}

.feature-icon {
    display: inline-block;
}

.page-header {
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 1rem;
}

.page-title {
    font-weight: 600;
    color: #333;
}
</style>
@endsection
