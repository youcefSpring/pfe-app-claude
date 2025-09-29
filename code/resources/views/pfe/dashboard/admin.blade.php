@extends('layouts.admin')

@section('title', 'Admin Dashboard - PFE Platform')
@section('page-title', 'Admin Dashboard')

@section('breadcrumb')
<li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content'>
<!-- Small boxes (Stat box) -->
<div class="row">
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $stats['total_subjects'] ?? 0 }}</h3>
                <p>Total Subjects</p>
            </div>
            <div class="icon">
                <i class="fas fa-book"></i>
            </div>
            <a href="{{ route('pfe.subjects.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $stats['total_teams'] ?? 0 }}</h3>
                <p>Active Teams</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
            <a href="{{ route('pfe.teams.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $stats['total_projects'] ?? 0 }}</h3>
                <p>Active Projects</p>
            </div>
            <div class="icon">
                <i class="fas fa-project-diagram"></i>
            </div>
            <a href="{{ route('pfe.projects.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $stats['upcoming_defenses'] ?? 0 }}</h3>
                <p>Upcoming Defenses</p>
            </div>
            <div class="icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <a href="{{ route('pfe.defenses.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
</div>
<!-- /.row -->

<!-- Main row -->
<div class="row">
    <!-- Left col -->
    <section class="col-lg-7 connectedSortable">
        <!-- Custom tabs (Charts with tabs)-->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie mr-1"></i>
                    PFE Progress Overview
                </h3>
                <div class="card-tools">
                    <ul class="nav nav-pills ml-auto">
                        <li class="nav-item">
                            <a class="nav-link active" href="#revenue-chart" data-toggle="tab">Area</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#sales-chart" data-toggle="tab">Donut</a>
                        </li>
                    </ul>
                </div>
            </div><!-- /.card-header -->
            <div class="card-body">
                <div class="tab-content p-0">
                    <!-- Morris chart - Sales -->
                    <div class="chart tab-pane active" id="revenue-chart" style="position: relative; height: 300px;">
                        <canvas id="progressChart" height="300" style="height: 300px;"></canvas>
                    </div>
                    <div class="chart tab-pane" id="sales-chart" style="position: relative; height: 300px;">
                        <canvas id="statusChart" height="300" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div><!-- /.card-body -->
        </div>
        <!-- /.card -->

        <!-- TO DO List -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ion ion-clipboard mr-1"></i>
                    Pending Actions
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <ul class="todo-list" data-widget="todo-list">
                    @if(isset($pendingActions))
                        @foreach($pendingActions as $action)
                        <li>
                            <span class="handle">
                                <i class="fas fa-ellipsis-v"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </span>
                            <span class="text">{{ $action['title'] }}</span>
                            <small class="badge badge-{{ $action['color'] ?? 'warning' }}">{{ $action['count'] ?? 0 }}</small>
                            <div class="tools">
                                <i class="fas fa-eye"></i>
                            </div>
                        </li>
                        @endforeach
                    @else
                        <li>
                            <span class="handle">
                                <i class="fas fa-ellipsis-v"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </span>
                            <span class="text">Subject Validations Pending</span>
                            <small class="badge badge-warning">{{ $stats['pending_validations'] ?? 0 }}</small>
                            <div class="tools">
                                <a href="{{ route('pfe.subjects.index') }}?status=pending"><i class="fas fa-eye"></i></a>
                            </div>
                        </li>
                        <li>
                            <span class="handle">
                                <i class="fas fa-ellipsis-v"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </span>
                            <span class="text">Teams Without Projects</span>
                            <small class="badge badge-danger">{{ $stats['teams_without_projects'] ?? 0 }}</small>
                            <div class="tools">
                                <i class="fas fa-eye"></i>
                            </div>
                        </li>
                        <li>
                            <span class="handle">
                                <i class="fas fa-ellipsis-v"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </span>
                            <span class="text">Overdue Deliverables</span>
                            <small class="badge badge-info">{{ $stats['overdue_deliverables'] ?? 0 }}</small>
                            <div class="tools">
                                <i class="fas fa-eye"></i>
                            </div>
                        </li>
                    @endif
                </ul>
            <!-- /.card-body -->
            <div class="card-footer clearfix">
                <a href="{{ route('pfe.admin.dashboard') }}" class="btn btn-primary float-right"><i class="fas fa-tasks"></i> View All Tasks</a>
            </div>
        </div>
        <!-- /.card -->
    </section>
    <!-- /.Left col -->

    <!-- Right col (fixed) -->
    <section class="col-lg-5 connectedSortable">
        <!-- Calendar -->
        <div class="card bg-gradient-success">
            <div class="card-header border-0">
                <h3 class="card-title">
                    <i class="far fa-calendar-alt"></i>
                    Defense Calendar
                </h3>
                <!-- tools card -->
                <div class="card-tools">
                    <button type="button" class="btn btn-success btn-sm" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
                <!-- /. tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body pt-0">
                <!-- The calendar -->
                <div id="calendar" style="width: 100%">
                    <div class="text-center p-4">
                        <i class="fas fa-calendar-alt fa-3x text-white mb-3"></i>
                        <p class="text-white">{{ $stats['upcoming_defenses'] ?? 0 }} upcoming defenses this month</p>
                        <a href="{{ route('pfe.defenses.index') }}" class="btn btn-light btn-sm">View Schedule</a>
                    </div>
                </div>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->

        <!-- solid sales graph -->
        <div class="card bg-gradient-info">
            <div class="card-header border-0">
                <h3 class="card-title">
                    <i class="fas fa-th mr-1"></i>
                    Subject Status Distribution
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-info btn-sm" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <canvas class="chart" id="line-chart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
            </div>
            <!-- /.card-body -->
            <div class="card-footer bg-transparent">
                <div class="row">
                    <div class="col-4 text-center">
                        <span class="text-white display-4 font-weight-bold">{{ $stats['subjects_approved'] ?? 0 }}</span>
                        <div class="text-white">Approved</div>
                    </div>
                    <!-- ./col -->
                    <div class="col-4 text-center">
                        <span class="text-white display-4 font-weight-bold">{{ $stats['subjects_pending'] ?? 0 }}</span>
                        <div class="text-white">Pending</div>
                    </div>
                    <!-- ./col -->
                    <div class="col-4 text-center">
                        <span class="text-white display-4 font-weight-bold">{{ $stats['subjects_published'] ?? 0 }}</span>
                        <div class="text-white">Published</div>
                    </div>
                    <!-- ./col -->
                </div>
                <!-- /.row -->
            </div>
        </div>
        <!-- /.card -->

        <!-- Map card -->
        <div class="card bg-gradient-primary">
            <div class="card-header border-0">
                <h3 class="card-title">
                    <i class="fas fa-users mr-1"></i>
                    Team Statistics
                </h3>
                <!-- card tools -->
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" data-card-widget="collapse" title="Collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
                <!-- /.card-tools -->
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="description-block border-right">
                            <span class="description-percentage text-warning"><i class="fas fa-check"></i></span>
                            <h5 class="description-header">{{ $stats['teams_complete'] ?? 0 }}</h5>
                            <span class="description-text">COMPLETE TEAMS</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-6">
                        <div class="description-block">
                            <span class="description-percentage text-success"><i class="fas fa-caret-up"></i> {{ $stats['team_growth'] ?? 20 }}%</span>
                            <h5 class="description-header">{{ $stats['teams_forming'] ?? 0 }}</h5>
                            <span class="description-text">FORMING</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                </div>
                <!-- /.row -->
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </section>
    <!-- right col -->
</div>
<!-- /.row (main row) -->

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Progress Chart
    var progressCtx = document.getElementById('progressChart').getContext('2d');
    var progressChart = new Chart(progressCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
            datasets: [{
                label: 'Projects Progress',
                data: [12, 19, 3, 5, 2, 3, 9],
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Status Chart
    var statusCtx = document.getElementById('statusChart').getContext('2d');
    var statusChart = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Draft', 'Submitted', 'Approved', 'Published'],
            datasets: [{
                data: [{{ $stats['subjects_draft'] ?? 0 }}, {{ $stats['subjects_submitted'] ?? 0 }}, {{ $stats['subjects_approved'] ?? 0 }}, {{ $stats['subjects_published'] ?? 0 }}],
                backgroundColor: ['#f56954', '#00a65a', '#f39c12', '#00c0ef']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Line Chart
    var lineCtx = document.getElementById('line-chart').getContext('2d');
    var lineChart = new Chart(lineCtx, {
        type: 'line',
        data: {
            labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
            datasets: [{
                label: 'Subject Submissions',
                backgroundColor: 'rgba(60,141,188,0.9)',
                borderColor: 'rgba(60,141,188,0.8)',
                pointRadius: false,
                pointColor: '#3b8bba',
                pointStrokeColor: 'rgba(60,141,188,1)',
                pointHighlightFill: '#fff',
                pointHighlightStroke: 'rgba(60,141,188,1)',
                data: [28, 48, 40, 19, 86, 27, 90]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
                display: false
            },
            scales: {
                x: {
                    display: false
                },
                y: {
                    display: false
                }
            }
        }
    });
});
</script>
@endpush