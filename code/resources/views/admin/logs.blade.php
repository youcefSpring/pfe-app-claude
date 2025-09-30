@extends('layouts.pfe-app')

@section('page-title', 'System Logs')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">System Logs</h4>
                    <small class="text-muted">Monitor system activity and errors</small>
                </div>
                <div class="card-body">
                    <div class="text-center py-5">
                        <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">System Logs</h5>
                        <p class="text-muted">Log viewing functionality will be implemented here.</p>
                        <div class="mt-3">
                            <a href="{{ route('admin.maintenance') }}" class="btn btn-primary">
                                <i class="fas fa-tools"></i> Go to Maintenance
                            </a>
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection