@extends('layouts.pfe-app')

@section('title', 'Pending Subjects Approval')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Pending Subjects Approval</h1>
        <div class="btn-group">
            <a href="{{ route('admin.subjects.all') }}" class="btn btn-outline-primary">
                <i class="bi bi-list"></i> All Subjects
            </a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </div>
    </div>

    <!-- Summary -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <h3 class="text-warning">{{ $pendingSubjects->total() }}</h3>
                    <p class="text-muted mb-0">Pending Approval</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Subjects -->
    @if($pendingSubjects->count() > 0)
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Subjects Awaiting Approval</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Subject Details</th>
                                <th>Teacher</th>
                                <th>Type</th>
                                <th>Submitted</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingSubjects as $subject)
                                <tr>
                                    <td>
                                        <div>
                                            <h6 class="mb-1">{{ $subject->title }}</h6>
                                            <p class="text-muted small mb-1">{{ Str::limit($subject->description, 100) }}</p>
                                            @if($subject->keywords)
                                                <div class="mt-1">
                                                    @foreach(explode(',', $subject->keywords) as $keyword)
                                                        <span class="badge bg-light text-dark me-1">{{ trim($keyword) }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                {{ substr($subject->teacher->name ?? 'N', 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-medium">{{ $subject->teacher->name ?? 'No Teacher' }}</div>
                                                <small class="text-muted">{{ $subject->teacher->email ?? '' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $subject->type === 'internal' ? 'primary' : 'info' }}">
                                            {{ ucfirst($subject->type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="fw-medium">{{ $subject->created_at->format('M d, Y') }}</div>
                                            <small class="text-muted">{{ $subject->created_at->diffForHumans() }}</small>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-outline-primary btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#subjectModal{{ $subject->id }}">
                                                <i class="bi bi-eye"></i> Review
                                            </button>
                                            <button type="button" class="btn btn-success btn-sm"
                                                    onclick="approveSubject({{ $subject->id }})">
                                                <i class="bi bi-check-lg"></i> Approve
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#rejectModal{{ $subject->id }}">
                                                <i class="bi bi-x-lg"></i> Reject
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Subject Details Modal -->
                                <div class="modal fade" id="subjectModal{{ $subject->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{ $subject->title }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h6>Description</h6>
                                                        <p class="text-muted">{{ $subject->description }}</p>

                                                        @if($subject->keywords)
                                                            <h6>Keywords</h6>
                                                            <p class="text-muted">{{ $subject->keywords }}</p>
                                                        @endif

                                                        @if($subject->tools)
                                                            <h6>Tools & Technologies</h6>
                                                            <p class="text-muted">{{ $subject->tools }}</p>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h6>Teacher Information</h6>
                                                        <p class="text-muted">
                                                            <strong>Name:</strong> {{ $subject->teacher->name ?? 'N/A' }}<br>
                                                            <strong>Email:</strong> {{ $subject->teacher->email ?? 'N/A' }}<br>
                                                            <strong>Department:</strong> {{ $subject->teacher->department ?? 'N/A' }}
                                                        </p>

                                                        <h6>Subject Details</h6>
                                                        <p class="text-muted">
                                                            <strong>Type:</strong> {{ ucfirst($subject->type) }}<br>
                                                            <strong>Target Grade:</strong> {{ $subject->target_grade ?? 'Not specified' }}<br>
                                                            <strong>External Company:</strong> {{ $subject->company_name ?? 'N/A' }}
                                                        </p>

                                                        @if($subject->plan)
                                                            <h6>Project Plan</h6>
                                                            <p class="text-muted">{{ $subject->plan }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="button" class="btn btn-success" onclick="approveSubject({{ $subject->id }})">
                                                    <i class="bi bi-check-lg"></i> Approve
                                                </button>
                                                <button type="button" class="btn btn-danger"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#rejectModal{{ $subject->id }}"
                                                        data-bs-dismiss="modal">
                                                    <i class="bi bi-x-lg"></i> Reject
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Reject Modal -->
                                <div class="modal fade" id="rejectModal{{ $subject->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('admin.subjects.reject', $subject) }}" method="POST">
                                                @csrf
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title">Reject Subject</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Are you sure you want to reject "<strong>{{ $subject->title }}</strong>"?</p>
                                                    <div class="mb-3">
                                                        <label for="feedback{{ $subject->id }}" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                                                        <textarea class="form-control" id="feedback{{ $subject->id }}" name="feedback"
                                                                  rows="3" required placeholder="Please provide a reason for rejection..."></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="bi bi-x-lg"></i> Reject Subject
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        @if($pendingSubjects->hasPages())
            <div class="d-flex justify-content-center mt-4">
                <nav aria-label="Pending subjects pagination">
                    {{ $pendingSubjects->links('pagination::bootstrap-4') }}
                </nav>
            </div>
        @endif
    @else
        <!-- No Pending Subjects -->
        <div class="text-center py-5">
            <i class="bi bi-clipboard-check text-muted" style="font-size: 4rem;"></i>
            <h4 class="mt-3">No Pending Subjects</h4>
            <p class="text-muted">All subjects have been reviewed and approved/rejected.</p>
            <a href="{{ route('admin.subjects.all') }}" class="btn btn-outline-primary">
                <i class="bi bi-list"></i> View All Subjects
            </a>
        </div>
    @endif
</div>

<script>
function approveSubject(subjectId) {
    if (confirm('Are you sure you want to approve this subject?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/subjects/${subjectId}/approve`;

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';

        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}

// Auto-hide alerts
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
});
</script>

<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 14px;
}

.table th {
    border-top: none;
    font-weight: 600;
}

.btn-group .btn {
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}
</style>
@endsection