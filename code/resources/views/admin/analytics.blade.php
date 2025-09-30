@extends('layouts.pfe-app')

@section('page-title', 'Analytics')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">System Analytics</h4>
                    <small class="text-muted">Advanced analytics and insights</small>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-chart-line"></i> Analytics Dashboard</h6>
                                <p class="mb-0">Advanced analytics functionality will be implemented here. This includes charts, graphs, and detailed insights about system usage and performance.</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Usage Statistics</h5>
                                </div>
                                <div class="card-body">
                                    <div class="text-center py-4">
                                        <i class="fas fa-chart-pie fa-3x text-primary mb-3"></i>
                                        <h6>User Activity Charts</h6>
                                        <p class="text-muted">Visual representation of user engagement and system usage patterns.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Performance Metrics</h5>
                                </div>
                                <div class="card-body">
                                    <div class="text-center py-4">
                                        <i class="fas fa-tachometer-alt fa-3x text-success mb-3"></i>
                                        <h6>System Performance</h6>
                                        <p class="text-muted">Monitor system performance, response times, and resource utilization.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Academic Progress</h5>
                                </div>
                                <div class="card-body">
                                    <div class="text-center py-4">
                                        <i class="fas fa-graduation-cap fa-3x text-warning mb-3"></i>
                                        <h6>Progress Tracking</h6>
                                        <p class="text-muted">Track student progress, project completion rates, and academic milestones.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Trends Analysis</h5>
                                </div>
                                <div class="card-body">
                                    <div class="text-center py-4">
                                        <i class="fas fa-trending-up fa-3x text-info mb-3"></i>
                                        <h6>Trend Analysis</h6>
                                        <p class="text-muted">Identify trends in subject selection, team formation, and project success rates.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 text-center">
                        <a href="{{ route('admin.reports') }}" class="btn btn-primary">
                            <i class="fas fa-file-alt"></i> View Reports
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
@endsection