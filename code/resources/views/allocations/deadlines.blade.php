@extends('layouts.app')

@section('title', 'Allocation Deadlines')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Allocation Deadlines</h1>
                    <p class="text-muted">Manage subject allocation deadlines and periods</p>
                </div>
                <div>
                    <a href="{{ route('allocations.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="bi bi-arrow-left me-1"></i>Back to Allocations
                    </a>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createDeadlineModal">
                        <i class="bi bi-plus me-1"></i>Create Deadline
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Deadlines List -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="bi bi-calendar-event me-2"></i>Allocation Deadlines
            </h5>
        </div>
        <div class="card-body">
            @if($deadlines->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Title</th>
                                <th>Academic Year</th>
                                <th>Semester</th>
                                <th>Deadline</th>
                                <th>Status</th>
                                <th>Created By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($deadlines as $deadline)
                                <tr>
                                    <td>
                                        <div>
                                            <strong>{{ $deadline->title }}</strong>
                                            @if($deadline->description)
                                                <br>
                                                <small class="text-muted">{{ Str::limit($deadline->description, 60) }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ $deadline->academic_year }}</td>
                                    <td>{{ $deadline->semester }}</td>
                                    <td>
                                        <div>
                                            <strong>{{ $deadline->deadline->format('M d, Y') }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $deadline->deadline->format('H:i') }}</small>
                                            <br>
                                            @if($deadline->deadline->isFuture())
                                                <small class="text-info">{{ $deadline->deadline->diffForHumans() }}</small>
                                            @else
                                                <small class="text-muted">{{ $deadline->deadline->diffForHumans() }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $deadline->status === 'active' ? 'bg-success' : ($deadline->status === 'completed' ? 'bg-info' : 'bg-secondary') }}">
                                            {{ ucfirst($deadline->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $deadline->creator->name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $deadline->created_at->format('M d, Y') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="editDeadline({{ $deadline->id }})" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            @if($deadline->deadline->isPast() && $deadline->status === 'active')
                                                <form action="{{ route('allocations.run-allocation') }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Run allocation algorithm for this deadline?')" title="Run Allocation">
                                                        <i class="bi bi-play-circle"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $deadlines->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-calendar-x display-1 text-muted"></i>
                    <h4 class="text-muted mt-3">No Deadlines Found</h4>
                    <p class="text-muted">No allocation deadlines have been created yet.</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createDeadlineModal">
                        <i class="bi bi-plus me-1"></i>Create First Deadline
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Create Deadline Modal -->
<div class="modal fade" id="createDeadlineModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Allocation Deadline</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('allocations.deadlines.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" class="form-control" required
                               placeholder="e.g., Subject Selection Deadline 2024-2025">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" class="form-control" rows="2"
                                  placeholder="Optional description of this deadline period"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="academic_year" class="form-label">Academic Year <span class="text-danger">*</span></label>
                                <input type="text" name="academic_year" id="academic_year" class="form-control" required
                                       placeholder="e.g., 2024-2025" value="{{ date('Y') }}-{{ date('Y') + 1 }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="semester" class="form-label">Semester <span class="text-danger">*</span></label>
                                <select name="semester" id="semester" class="form-select" required>
                                    <option value="">Select Semester</option>
                                    <option value="S1">Semester 1</option>
                                    <option value="S2">Semester 2</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="deadline" class="form-label">Deadline <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="deadline" id="deadline" class="form-control" required
                               min="{{ now()->format('Y-m-d\TH:i') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Deadline</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Deadline Modal -->
<div class="modal fade" id="editDeadlineModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Allocation Deadline</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editDeadlineForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="edit_title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea name="description" id="edit_description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_deadline" class="form-label">Deadline <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="deadline" id="edit_deadline" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" id="edit_status" class="form-select" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Deadline</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function editDeadline(deadlineId) {
    // In a real implementation, you would fetch the deadline data via AJAX
    // For now, we'll show the modal with empty fields
    const form = document.getElementById('editDeadlineForm');
    form.action = `/allocations/deadlines/${deadlineId}`;

    // Clear form fields
    document.getElementById('edit_title').value = '';
    document.getElementById('edit_description').value = '';
    document.getElementById('edit_deadline').value = '';
    document.getElementById('edit_status').value = 'active';

    const modal = new bootstrap.Modal(document.getElementById('editDeadlineModal'));
    modal.show();
}
</script>
@endpush