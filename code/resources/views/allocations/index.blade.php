@extends('layouts.app')

@section('title', 'Subject Allocations')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Subject Allocations</h1>
                    <p class="text-muted">Manage student subject allocations</p>
                </div>
                <div>
                    <a href="{{ route('allocations.deadlines') }}" class="btn btn-outline-primary me-2">
                        <i class="bi bi-calendar-event me-1"></i>Manage Deadlines
                    </a>
                    <a href="{{ route('allocations.results') }}" class="btn btn-primary">
                        <i class="bi bi-graph-up me-1"></i>View Results
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <h3 class="text-primary mb-1">{{ $allocations->total() }}</h3>
                    <small class="text-muted">Total Allocations</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card border-success">
                <div class="card-body text-center">
                    <h3 class="text-success mb-1">{{ $deadlines->where('status', 'active')->count() }}</h3>
                    <small class="text-muted">Active Deadlines</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card border-info">
                <div class="card-body text-center">
                    <h3 class="text-info mb-1">{{ $deadlines->where('status', 'completed')->count() }}</h3>
                    <small class="text-muted">Completed Periods</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-list-check me-2"></i>Recent Allocations
                    </h5>
                </div>
                <div class="card-body">
                    @if($allocations->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Student</th>
                                        <th>Subject</th>
                                        <th>Preference</th>
                                        <th>Average</th>
                                        <th>Status</th>
                                        <th>Deadline</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($allocations as $allocation)
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong>{{ $allocation->student->name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $allocation->student->matricule }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $allocation->subject->title }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ Str::limit($allocation->subject->description, 50) }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                @if($allocation->student_preference_order)
                                                    <span class="badge bg-info">{{ $allocation->getPreferenceLabel() }}</span>
                                                @else
                                                    <span class="badge bg-secondary">Not Preferred</span>
                                                @endif
                                            </td>
                                            <td>
                                                <strong class="text-primary">{{ number_format($allocation->student_average, 2) }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge {{ $allocation->status === 'confirmed' ? 'bg-success' : ($allocation->status === 'rejected' ? 'bg-danger' : 'bg-warning') }}">
                                                    {{ ucfirst($allocation->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $allocation->allocationDeadline->title }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-outline-secondary btn-sm" title="View Details">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            {{ $allocations->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox display-1 text-muted"></i>
                            <h4 class="text-muted mt-3">No Allocations Found</h4>
                            <p class="text-muted">No subject allocations have been created yet.</p>
                            <a href="{{ route('allocations.deadlines') }}" class="btn btn-primary">
                                <i class="bi bi-calendar-event me-1"></i>Create Allocation Deadline
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection