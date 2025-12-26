@extends('layouts.pfe-app')
@section('title', 'Generate Reports')
@section('content')
<div class="container-fluid py-4">
    <h1 class="h3 mb-4">Generate System Reports</h1>
    <div class="row">
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-book text-primary" style="font-size: 2rem;"></i>
                    <h5 class="mt-3">Subjects Report</h5>
                    <p class="text-muted">Generate report on all subjects and their status</p>
                    <a href="{{ route('admin.reports.subjects') }}" class="btn btn-primary">Generate</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-people text-success" style="font-size: 2rem;"></i>
                    <h5 class="mt-3">Teams Report</h5>
                    <p class="text-muted">Generate report on team formations and progress</p>
                    <a href="{{ route('admin.reports.teams') }}" class="btn btn-success">Generate</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-folder text-info" style="font-size: 2rem;"></i>
                    <h5 class="mt-3">Projects Report</h5>
                    <p class="text-muted">Generate report on project status and completion</p>
                    <a href="{{ route('admin.reports.projects') }}" class="btn btn-info">Generate</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-shield-check text-warning" style="font-size: 2rem;"></i>
                    <h5 class="mt-3">Defenses Report</h5>
                    <p class="text-muted">Generate report on defense schedules and results</p>
                    <a href="{{ route('admin.reports.defenses') }}" class="btn btn-warning">Generate</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection