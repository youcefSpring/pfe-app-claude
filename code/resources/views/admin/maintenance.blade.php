@extends('layouts.pfe-app')

@section('page-title', 'System Maintenance')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">System Maintenance</h4>
                    <small class="text-muted">System maintenance and backup operations</small>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Database Maintenance</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <h6>Database Optimization</h6>
                                        <p class="text-muted">Optimize database tables and clean up unused data.</p>
                                        <button type="button" class="btn btn-info btn-sm">
                                            <i class="fas fa-database"></i> Optimize Database
                                        </button>
                                    </div>

                                    <div class="mb-3">
                                        <h6>Clear Cache</h6>
                                        <p class="text-muted">Clear application cache, routes, and configurations.</p>
                                        <button type="button" class="btn btn-warning btn-sm">
                                            <i class="fas fa-broom"></i> Clear Cache
                                        </button>
                                    </div>

                                    <div class="mb-3">
                                        <h6>Run Migrations</h6>
                                        <p class="text-muted">Apply pending database migrations.</p>
                                        <button type="button" class="btn btn-success btn-sm">
                                            <i class="fas fa-sync"></i> Run Migrations
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Backup Operations</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <h6>Create Backup</h6>
                                        <p class="text-muted">Create a full system backup including database and files.</p>
                                        <form action="{{ route('admin.backup') }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <i class="fas fa-download"></i> Create Backup
                                            </button>
                                        </form>
                                    </div>

                                    <div class="mb-3">
                                        <h6>Recent Backups</h6>
                                        <p class="text-muted">View and manage recent system backups.</p>
                                        <div class="list-group">
                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1">backup-2024-09-30.sql</h6>
                                                    <small class="text-muted">Created: Sept 30, 2024 10:30 AM</small>
                                                </div>
                                                <div>
                                                    <span class="badge bg-success">25 MB</span>
                                                    <button class="btn btn-outline-primary btn-sm ms-2">
                                                        <i class="fas fa-download"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">File Management</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <h6>Storage Usage</h6>
                                        <p class="text-muted">Monitor disk space and file storage usage.</p>
                                        <div class="progress mb-2">
                                            <div class="progress-bar" role="progressbar" style="width: 45%" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100">45%</div>
                                        </div>
                                        <small class="text-muted">4.5 GB of 10 GB used</small>
                                    </div>

                                    <div class="mb-3">
                                        <h6>Clean Temporary Files</h6>
                                        <p class="text-muted">Remove temporary files and uploads.</p>
                                        <button type="button" class="btn btn-warning btn-sm">
                                            <i class="fas fa-trash"></i> Clean Files
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">System Status</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <h6>Service Status</h6>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span>Database</span>
                                            <span class="badge bg-success">Online</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span>Cache</span>
                                            <span class="badge bg-success">Active</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span>Queue</span>
                                            <span class="badge bg-warning">Idle</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span>Email</span>
                                            <span class="badge bg-success">Working</span>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <h6>System Information</h6>
                                        <small class="text-muted">
                                            PHP Version: {{ PHP_VERSION }}<br>
                                            Laravel Version: {{ app()->version() }}<br>
                                            Server: {{ $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 text-center">
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