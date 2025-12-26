@extends('layouts.pfe-app')

@section('page-title', __('app.reports'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ __('app.reports') }}</h4>
                    <small class="text-muted">{{ __('app.view_detailed_reports_statistics') }}</small>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $stats['total_users'] }}</h3>
                                    <p class="mb-0">{{ __('app.total_users') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $stats['total_students'] }}</h3>
                                    <p class="mb-0">{{ __('app.students') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $stats['total_teachers'] }}</h3>
                                    <p class="mb-0">{{ __('app.teachers') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $stats['total_specialities'] }}</h3>
                                    <p class="mb-0">{{ __('app.specialities') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">{{ __('app.detailed_reports') }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="list-group">
                                        <a href="{{ route('admin.reports.subjects') }}" class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">{{ __('app.subjects_report') }}</h6>
                                                <i class="fas fa-book text-primary"></i>
                                            </div>
                                            <p class="mb-1">{{ __('app.subjects_report_description') }}</p>
                                        </a>
                                        <a href="{{ route('admin.reports.teams') }}" class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">{{ __('app.teams_report') }}</h6>
                                                <i class="fas fa-users text-success"></i>
                                            </div>
                                            <p class="mb-1">{{ __('app.teams_report_description') }}</p>
                                        </a>
                                        <a href="{{ route('admin.reports.projects') }}" class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">{{ __('app.projects_report') }}</h6>
                                                <i class="fas fa-project-diagram text-info"></i>
                                            </div>
                                            <p class="mb-1">{{ __('app.projects_report_description') }}</p>
                                        </a>
                                        <a href="{{ route('admin.reports.defenses') }}" class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">{{ __('app.defenses_report') }}</h6>
                                                <i class="fas fa-graduation-cap text-warning"></i>
                                            </div>
                                            <p class="mb-1">{{ __('app.defenses_report_description') }}</p>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">{{ __('app.quick_actions') }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('admin.analytics') }}" class="btn btn-primary">
                                            <i class="fas fa-chart-bar"></i> {{ __('app.view_analytics') }}
                                        </a>
                                        <a href="{{ route('admin.users') }}" class="btn btn-success">
                                            <i class="fas fa-users"></i> {{ __('app.manage_users') }}
                                        </a>
                                        <a href="{{ route('admin.settings') }}" class="btn btn-info">
                                            <i class="fas fa-cog"></i> {{ __('app.system_settings') }}
                                        </a>
                                        <a href="{{ route('admin.maintenance') }}" class="btn btn-warning">
                                            <i class="fas fa-tools"></i> {{ __('app.maintenance') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection