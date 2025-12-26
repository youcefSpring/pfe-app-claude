@extends('layouts.pfe-app')

@section('page-title', __('app.system_logs'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ __('app.system_logs') }}</h4>
                    <small class="text-muted">{{ __('app.monitor_system_activity') }}</small>
                </div>
                <div class="card-body">
                    <div class="text-center py-5">
                        <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">{{ __('app.system_logs') }}</h5>
                        <p class="text-muted">{{ __('app.log_viewing_functionality') }}</p>
                        <div class="mt-3">
                            <a href="{{ route('admin.maintenance') }}" class="btn btn-primary">
                                <i class="fas fa-tools"></i> {{ __('app.go_to_maintenance') }}
                            </a>
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> {{ __('app.back_to_dashboard') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection