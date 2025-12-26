@extends('layouts.pfe-app')

@section('page-title', 'Projects Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Projects Report</h4>
                    <small class="text-muted">Overview of all projects in the system</small>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $stats['total'] }}</h3>
                                    <p class="mb-0">Total Projects</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $stats['active'] }}</h3>
                                    <p class="mb-0">Active Projects</p>
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
                                <h5>Project Statistics</h5>
                                <div>
                                    <a href="{{ route('projects.index') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye"></i> View All Projects
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
                                            <td><span class="badge bg-warning">Active</span></td>
                                            <td>{{ $stats['active'] }}</td>
                                            <td>{{ $stats['total'] > 0 ? round(($stats['active'] / $stats['total']) * 100, 2) : 0 }}%</td>
                                            <td>
                                                <a href="{{ route('projects.index') }}?status=active" class="btn btn-outline-warning btn-sm">
                                                    View Active
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-success">Completed</span></td>
                                            <td>{{ $stats['completed'] }}</td>
                                            <td>{{ $stats['total'] > 0 ? round(($stats['completed'] / $stats['total']) * 100, 2) : 0 }}%</td>
                                            <td>
                                                <a href="{{ route('projects.index') }}?status=completed" class="btn btn-outline-success btn-sm">
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