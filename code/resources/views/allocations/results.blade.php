@extends('layouts.app')

@section('title', 'Allocation Results')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Subject Allocation Results</h1>
                    <p class="text-muted">Review and manage subject allocation outcomes</p>
                </div>
                <div>
                    <a href="{{ route('allocations.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="bi bi-arrow-left me-1"></i>Back to Allocations
                    </a>
                    <a href="{{ route('allocations.deadlines') }}" class="btn btn-primary">
                        <i class="bi bi-calendar-event me-1"></i>Manage Deadlines
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <h3 class="text-primary mb-1">{{ $stats['total_allocations'] }}</h3>
                    <small class="text-muted">Total Allocations</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body text-center">
                    <h3 class="text-success mb-1">{{ $stats['confirmed_allocations'] }}</h3>
                    <small class="text-muted">Confirmed</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <h3 class="text-warning mb-1">{{ $stats['total_allocations'] - $stats['confirmed_allocations'] }}</h3>
                    <small class="text-muted">Pending</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body text-center">
                    <h3 class="text-info mb-1">{{ $stats['first_choice_allocations'] }}</h3>
                    <small class="text-muted">First Choice</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Allocations Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="bi bi-list-check me-2"></i>Allocation Results
            </h5>
        </div>
        <div class="card-body">
            @if($allocations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Rank</th>
                                <th>Student</th>
                                <th>Subject</th>
                                <th>Preference</th>
                                <th>Average</th>
                                <th>Method</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allocations as $allocation)
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary">#{{ $allocation->allocation_rank }}</span>
                                    </td>
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
                                            <small class="text-muted">{{ Str::limit($allocation->subject->description, 60) }}</small>
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
                                        <span class="badge {{ $allocation->allocation_method === 'automatic_by_merit' ? 'bg-success' : 'bg-warning' }}">
                                            {{ ucfirst(str_replace('_', ' ', $allocation->allocation_method)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $allocation->status === 'confirmed' ? 'bg-success' : ($allocation->status === 'rejected' ? 'bg-danger' : 'bg-warning') }}">
                                            {{ ucfirst($allocation->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($allocation->status === 'tentative')
                                            <div class="btn-group" role="group">
                                                <form action="{{ route('allocations.confirm', $allocation) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm" title="Confirm Allocation">
                                                        <i class="bi bi-check-circle"></i>
                                                    </button>
                                                </form>
                                                <button type="button" class="btn btn-danger btn-sm" onclick="rejectAllocation({{ $allocation->id }})" title="Reject Allocation">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            </div>
                                        @else
                                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="viewAllocationDetails({{ $allocation->id }})" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    <nav aria-label="Allocation results pagination">
                        {{ $allocations->links('pagination::bootstrap-4') }}
                    </nav>
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

<!-- Rejection Modal -->
<div class="modal fade" id="rejectionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Allocation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectionForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="3" required
                                  placeholder="Please provide a reason for rejecting this allocation..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Allocation</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Allocation Details Modal -->
<div class="modal fade" id="allocationDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Allocation Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="allocationDetailsContent">
                <!-- Details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function rejectAllocation(allocationId) {
    const form = document.getElementById('rejectionForm');
    form.action = `/allocations/${allocationId}/reject`;

    const modal = new bootstrap.Modal(document.getElementById('rejectionModal'));
    modal.show();
}

function viewAllocationDetails(allocationId) {
    // In a real implementation, you would fetch the allocation details via AJAX
    // For now, we'll show a placeholder
    document.getElementById('allocationDetailsContent').innerHTML = `
        <div class="text-center">
            <i class="bi bi-info-circle display-1 text-info"></i>
            <h4 class="mt-3">Allocation Details</h4>
            <p class="text-muted">Detailed allocation information would be displayed here.</p>
        </div>
    `;

    const modal = new bootstrap.Modal(document.getElementById('allocationDetailsModal'));
    modal.show();
}
</script>
@endpush