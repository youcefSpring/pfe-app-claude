@extends('layouts.pfe-app')

@section('page-title', __('app.allocation_deadlines'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">{{ __('app.allocation_deadlines') }}</h4>
                        <small class="text-muted">{{ __('app.manage_subject_allocation_deadlines') }}</small>
                    </div>
                    <div>
                        <a href="{{ route('allocations.index') }}" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-arrow-left me-1"></i>{{ __('app.back_to_allocations') }}
                        </a>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createDeadlineModal">
                            <i class="fas fa-plus me-1"></i>{{ __('app.create_deadline') }}
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($deadlines->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                            <tr>
                                <th>{{ __('app.title') }}</th>
                                <th>{{ __('app.academic_year') }}</th>
                                <th>{{ __('app.semester') }}</th>
                                <th>{{ __('app.deadline') }}</th>
                                <th>{{ __('app.status') }}</th>
                                <th>{{ __('app.created_by') }}</th>
                                <th>{{ __('app.actions') }}</th>
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
                                            <strong>{{ $deadline->preferences_deadline->format('M d, Y') }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $deadline->preferences_deadline->format('H:i') }}</small>
                                            <br>
                                            @if($deadline->preferences_deadline->isFuture())
                                                <small class="text-info">{{ $deadline->preferences_deadline->diffForHumans() }}</small>
                                            @else
                                                <small class="text-muted">{{ $deadline->preferences_deadline->diffForHumans() }}</small>
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
                                        <div class="d-flex gap-1 justify-content-center">
                                            <button type="button" class="btn btn-sm btn-outline-warning rounded-pill" onclick="editDeadline({{ $deadline->id }})" title="{{ __('app.edit') }}" data-bs-toggle="tooltip">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            @if($deadline->preferences_deadline->isPast() && $deadline->status === 'active')
                                                <form action="{{ route('allocations.run-allocation') }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success rounded-pill" onclick="return confirm('{{ __('app.run_allocation_algorithm') }}')" title="{{ __('app.run_allocation') }}" data-bs-toggle="tooltip">
                                                        <i class="fas fa-play"></i>
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

                        @if($deadlines->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $deadlines->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted mt-3">{{ __('app.no_deadlines_found') }}</h4>
                            <p class="text-muted">{{ __('app.no_deadlines_created') }}</p>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createDeadlineModal">
                                <i class="fas fa-plus me-1"></i>{{ __('app.create_first_deadline') }}
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Deadline Modal -->
<div class="modal fade" id="createDeadlineModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('app.create_allocation_deadline') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('allocations.deadlines.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">{{ __('app.title') }} <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" class="form-control" required
                               placeholder="e.g., Subject Selection Deadline 2024-2025">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">{{ __('app.description') }}</label>
                        <textarea name="description" id="description" class="form-control" rows="2"
                                  placeholder="{{ __('app.optional_description') }}"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="academic_year" class="form-label">{{ __('app.academic_year') }} <span class="text-danger">*</span></label>
                                <input type="text" name="academic_year" id="academic_year" class="form-control" required
                                       placeholder="e.g., 2024-2025" value="{{ date('Y') }}-{{ date('Y') + 1 }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="semester" class="form-label">{{ __('app.semester') }} <span class="text-danger">*</span></label>
                                <select name="semester" id="semester" class="form-select" required>
                                    <option value="">{{ __('app.select_semester') }}</option>
                                    <option value="S1">{{ __('app.semester_1') }}</option>
                                    <option value="S2">{{ __('app.semester_2') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="deadline" class="form-label">{{ __('app.deadline') }} <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="deadline" id="deadline" class="form-control" required
                               min="{{ now()->format('Y-m-d\TH:i') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('app.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('app.create_deadline') }}</button>
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
                <h5 class="modal-title">{{ __('app.edit_allocation_deadline') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editDeadlineForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_title" class="form-label">{{ __('app.title') }} <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="edit_title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">{{ __('app.description') }}</label>
                        <textarea name="description" id="edit_description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_deadline" class="form-label">{{ __('app.deadline') }} <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="deadline" id="edit_deadline" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_status" class="form-label">{{ __('app.status') }} <span class="text-danger">*</span></label>
                        <select name="status" id="edit_status" class="form-select" required>
                            <option value="active">{{ __('app.active') }}</option>
                            <option value="inactive">{{ __('app.inactive') }}</option>
                            <option value="completed">{{ __('app.completed') }}</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('app.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('app.update_deadline') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.btn-group .btn,
.d-flex .btn {
    transition: all 0.2s ease-in-out;
}

.btn-sm.rounded-pill {
    padding: 0.25rem 0.6rem;
    font-size: 0.8rem;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});

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