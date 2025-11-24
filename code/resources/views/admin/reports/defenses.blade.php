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
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $stats['total'] }}</h3>
                                    <p class="mb-0">Total Defenses</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-dark">
                                <div class="card-body text-center">
                                    <h3>{{ $stats['scheduled'] }}</h3>
                                    <p class="mb-0">Scheduled</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $stats['completed'] }}</h3>
                                    <p class="mb-0">Completed</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h3>{{ round($avgJurySize, 1) }}</h3>
                                    <p class="mb-0">Avg Jury Size</p>
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

                            <!-- Status Breakdown Table -->
                            <div class="table-responsive mb-4">
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
                                            <td><span class="badge bg-warning text-dark">Scheduled</span></td>
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
                                        @if($stats['in_progress'] > 0)
                                        <tr>
                                            <td><span class="badge bg-info">In Progress</span></td>
                                            <td>{{ $stats['in_progress'] }}</td>
                                            <td>{{ $stats['total'] > 0 ? round(($stats['in_progress'] / $stats['total']) * 100, 2) : 0 }}%</td>
                                            <td>
                                                <a href="{{ route('defenses.index') }}?status=in_progress" class="btn btn-outline-info btn-sm">
                                                    View In Progress
                                                </a>
                                            </td>
                                        </tr>
                                        @endif
                                        @if($stats['cancelled'] > 0)
                                        <tr>
                                            <td><span class="badge bg-danger">Cancelled</span></td>
                                            <td>{{ $stats['cancelled'] }}</td>
                                            <td>{{ $stats['total'] > 0 ? round(($stats['cancelled'] / $stats['total']) * 100, 2) : 0 }}%</td>
                                            <td>
                                                <a href="{{ route('defenses.index') }}?status=cancelled" class="btn btn-outline-danger btn-sm">
                                                    View Cancelled
                                                </a>
                                            </td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                            <!-- Most Used Rooms -->
                            @if($roomUsage->count() > 0)
                            <div class="mb-4">
                                <h5 class="mb-3">Most Used Rooms</h5>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Room</th>
                                                <th>Capacity</th>
                                                <th>Times Used</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($roomUsage as $usage)
                                            <tr>
                                                <td><strong>{{ $usage['room']->name }}</strong></td>
                                                <td>{{ $usage['room']->capacity ?? 'N/A' }}</td>
                                                <td><span class="badge bg-primary">{{ $usage['count'] }}</span></td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @endif

                            <!-- Defenses by Month -->
                            @if($defensesByMonth->count() > 0)
                            <div class="mb-4">
                                <h5 class="mb-3">Defenses by Month</h5>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Month</th>
                                                <th>Number of Defenses</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($defensesByMonth as $month => $monthDefenses)
                                            <tr>
                                                <td><strong>{{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}</strong></td>
                                                <td><span class="badge bg-secondary">{{ $monthDefenses->count() }}</span></td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @endif

                            <!-- Recent Defenses List -->
                            <div class="mb-4">
                                <h5 class="mb-3">Recent Defenses</h5>
                                @if($defenses->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover table-sm">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Time</th>
                                                <th>Subject</th>
                                                <th>Team</th>
                                                <th>Room</th>
                                                <th>Jury</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($defenses->take(10) as $defense)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($defense->defense_date)->format('d/m/Y') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($defense->defense_time)->format('H:i') }}</td>
                                                <td>
                                                    <small><strong>{{ $defense->subject->title ?? 'N/A' }}</strong></small>
                                                </td>
                                                <td>
                                                    <small>{{ $defense->project->team->name ?? 'N/A' }}</small>
                                                </td>
                                                <td>{{ $defense->room->name ?? 'N/A' }}</td>
                                                <td><span class="badge bg-info">{{ $defense->juries->count() }} members</span></td>
                                                <td>
                                                    @if($defense->status == 'scheduled')
                                                        <span class="badge bg-warning text-dark">Scheduled</span>
                                                    @elseif($defense->status == 'completed')
                                                        <span class="badge bg-success">Completed</span>
                                                    @elseif($defense->status == 'in_progress')
                                                        <span class="badge bg-info">In Progress</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ ucfirst($defense->status) }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> No defenses found in the system.
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection