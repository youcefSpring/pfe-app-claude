@extends('layouts.pfe-app')

@section('page-title', 'Teams Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Teams Report</h4>
                    <small class="text-muted">Overview of all teams in the system</small>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $stats['total'] }}</h3>
                                    <p class="mb-0">Total Teams</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $stats['active'] }}</h3>
                                    <p class="mb-0">Active Teams</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $stats['forming'] }}</h3>
                                    <p class="mb-0">Teams Forming</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5>Team Statistics</h5>
                                <div>
                                    <a href="{{ route('teams.index') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye"></i> View All Teams
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
                                            <td><span class="badge bg-success">Active</span></td>
                                            <td>{{ $stats['active'] }}</td>
                                            <td>{{ $stats['total'] > 0 ? round(($stats['active'] / $stats['total']) * 100, 2) : 0 }}%</td>
                                            <td>
                                                <a href="{{ route('teams.index') }}?status=active" class="btn btn-outline-success btn-sm">
                                                    View Active
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-warning">Forming</span></td>
                                            <td>{{ $stats['forming'] }}</td>
                                            <td>{{ $stats['total'] > 0 ? round(($stats['forming'] / $stats['total']) * 100, 2) : 0 }}%</td>
                                            <td>
                                                <a href="{{ route('teams.index') }}?status=forming" class="btn btn-outline-warning btn-sm">
                                                    View Forming
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