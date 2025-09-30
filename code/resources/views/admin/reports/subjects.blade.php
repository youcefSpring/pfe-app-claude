@extends('layouts.pfe-app')

@section('page-title', 'Subjects Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Subjects Report</h4>
                    <small class="text-muted">Overview of all subjects in the system</small>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $stats['total'] }}</h3>
                                    <p class="mb-0">Total Subjects</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $stats['validated'] }}</h3>
                                    <p class="mb-0">Validated</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $stats['pending'] }}</h3>
                                    <p class="mb-0">Pending Validation</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $stats['rejected'] }}</h3>
                                    <p class="mb-0">Rejected</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5>Subject Statistics</h5>
                                <div>
                                    <a href="{{ route('subjects.index') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye"></i> View All Subjects
                                    </a>
                                    <a href="{{ route('admin.reports') }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-arrow-left"></i> Back to Reports
                                    </a>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Status</th>
                                            <th>Count</th>
                                            <th>Percentage</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><span class="badge bg-success">Validated</span></td>
                                            <td>{{ $stats['validated'] }}</td>
                                            <td>{{ $stats['total'] > 0 ? round(($stats['validated'] / $stats['total']) * 100, 2) : 0 }}%</td>
                                            <td>
                                                <a href="{{ route('subjects.index') }}?status=validated" class="btn btn-outline-primary btn-sm">
                                                    View Validated
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-warning">Pending</span></td>
                                            <td>{{ $stats['pending'] }}</td>
                                            <td>{{ $stats['total'] > 0 ? round(($stats['pending'] / $stats['total']) * 100, 2) : 0 }}%</td>
                                            <td>
                                                <a href="{{ route('subjects.pending-validation') }}" class="btn btn-outline-warning btn-sm">
                                                    View Pending
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-danger">Rejected</span></td>
                                            <td>{{ $stats['rejected'] }}</td>
                                            <td>{{ $stats['total'] > 0 ? round(($stats['rejected'] / $stats['total']) * 100, 2) : 0 }}%</td>
                                            <td>
                                                <a href="{{ route('subjects.index') }}?status=rejected" class="btn btn-outline-danger btn-sm">
                                                    View Rejected
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection