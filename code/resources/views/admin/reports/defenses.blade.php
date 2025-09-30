@extends('layouts.pfe-app')

@section('page-title', 'Defenses Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Defenses Report</h4>
                    <small class="text-muted">Overview of all defenses in the system</small>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $stats['total'] }}</h3>
                                    <p class="mb-0">Total Defenses</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $stats['scheduled'] }}</h3>
                                    <p class="mb-0">Scheduled</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $stats['completed'] }}</h3>
                                    <p class="mb-0">Completed</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5>Defense Statistics</h5>
                                <div>
                                    <a href="{{ route('defenses.index') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye"></i> View All Defenses
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
                                            <td><span class="badge bg-warning">Scheduled</span></td>
                                            <td>{{ $stats['scheduled'] }}</td>
                                            <td>{{ $stats['total'] > 0 ? round(($stats['scheduled'] / $stats['total']) * 100, 2) : 0 }}%</td>
                                            <td>
                                                <a href="{{ route('defenses.index') }}?status=scheduled" class="btn btn-outline-warning btn-sm">
                                                    View Scheduled
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-success">Completed</span></td>
                                            <td>{{ $stats['completed'] }}</td>
                                            <td>{{ $stats['total'] > 0 ? round(($stats['completed'] / $stats['total']) * 100, 2) : 0 }}%</td>
                                            <td>
                                                <a href="{{ route('defenses.index') }}?status=completed" class="btn btn-outline-success btn-sm">
                                                    View Completed
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