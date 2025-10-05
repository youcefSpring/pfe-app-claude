@extends('layouts.pfe-app')

@section('title', 'All Subjects Management')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">All Subjects Management</h1>
        <div class="btn-group">
            <a href="{{ route('admin.subjects.pending') }}" class="btn btn-warning">
                <i class="bi bi-clock"></i> Pending ({{ $stats['pending'] }})
            </a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <h3 class="text-primary">{{ $stats['total'] }}</h3>
                    <p class="text-muted mb-0">Total Subjects</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <h3 class="text-warning">{{ $stats['pending'] }}</h3>
                    <p class="text-muted mb-0">Pending Approval</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body text-center">
                    <h3 class="text-success">{{ $stats['validated'] }}</h3>
                    <p class="text-muted mb-0">Validated</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-danger">
                <div class="card-body text-center">
                    <h3 class="text-danger">{{ $stats['rejected'] }}</h3>
                    <p class="text-muted mb-0">Rejected</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.subjects.all') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="pending_validation" {{ request('status') === 'pending_validation' ? 'selected' : '' }}>Pending</option>
                            <option value="validated" {{ request('status') === 'validated' ? 'selected' : '' }}>Validated</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="type" class="form-select">
                            <option value="">All Types</option>
                            <option value="internal" {{ request('type') === 'internal' ? 'selected' : '' }}>Internal</option>
                            <option value="external" {{ request('type') === 'external' ? 'selected' : '' }}>External</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Search subjects..."
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Subjects List -->
    @if($subjects->count() > 0)
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Subjects ({{ $subjects->total() }} results)</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Subject Details</th>
                                <th>Teacher</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subjects as $subject)
                                <tr>
                                    <td>
                                        <div>
                                            <h6 class="mb-1">{{ $subject->title }}</h6>
                                            <p class="text-muted small mb-1">{{ Str::limit($subject->description, 80) }}</p>
                                            @if($subject->keywords)
                                                <div class="mt-1">
                                                    @foreach(array_slice(explode(',', $subject->keywords), 0, 3) as $keyword)
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
                                                <small class="text-muted">{{ $subject->teacher->department ?? '' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $subject->type === 'internal' ? 'primary' : 'info' }}">
                                            {{ ucfirst($subject->type) }}
                                        </span>
                                        @if($subject->is_external && $subject->company_name)
                                            <br><small class="text-muted">{{ $subject->company_name }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'pending_validation' => 'warning',
                                                'validated' => 'success',
                                                'rejected' => 'danger'
                                            ];
                                            $statusColor = $statusColors[$subject->status] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $statusColor }}">
                                            {{ ucfirst(str_replace('_', ' ', $subject->status)) }}
                                        </span>
                                        @if($subject->validated_at)
                                            <br><small class="text-muted">{{ $subject->validated_at->format('M d, Y') }}</small>
                                        @endif
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
                                                <i class="bi bi-eye"></i>
                                            </button>

                                            @if($subject->status === 'pending_validation')
                                                <button type="button" class="btn btn-success btn-sm"
                                                        onclick="approveSubject({{ $subject->id }})">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#rejectModal{{ $subject->id }}">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                                <!-- Subject Details Modal -->
                                <div class="modal fade" id="subjectModal{{ $subject->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{ $subject->title }}</h5>
                                                <span class="badge bg-{{ $statusColor }} ms-2">{{ ucfirst(str_replace('_', ' ', $subject->status)) }}</span>
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

                                                        @if($subject->validation_feedback)
                                                            <h6>Validation Feedback</h6>
                                                            <div class="alert alert-{{ $subject->status === 'validated' ? 'success' : 'warning' }}">
                                                                {{ $subject->validation_feedback }}
                                                            </div>
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
                                                            @if($subject->company_name)
                                                                <strong>External Company:</strong> {{ $subject->company_name }}<br>
                                                            @endif
                                                            <strong>Created:</strong> {{ $subject->created_at->format('M d, Y g:i A') }}
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
                                                @if($subject->status === 'pending_validation')
                                                    <button type="button" class="btn btn-success" onclick="approveSubject({{ $subject->id }})">
                                                        <i class="bi bi-check-lg"></i> Approve
                                                    </button>
                                                    <button type="button" class="btn btn-danger"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#rejectModal{{ $subject->id }}"
                                                            data-bs-dismiss="modal">
                                                        <i class="bi bi-x-lg"></i> Reject
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Reject Modal (only for pending subjects) -->
                                @if($subject->status === 'pending_validation')
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
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        @if($subjects->hasPages())
            <div class="d-flex justify-content-center mt-4">
                <nav aria-label="Subjects pagination">
                    {{ $subjects->appends(request()->query())->links('pagination::bootstrap-4') }}
                </nav>
            </div>
        @endif
    @else
        <!-- No Subjects -->
        <div class="text-center py-5">
            <i class="bi bi-journal-x text-muted" style="font-size: 4rem;"></i>
            <h4 class="mt-3">No Subjects Found</h4>
            <p class="text-muted">No subjects match your current filters.</p>
            <a href="{{ route('admin.subjects.all') }}" class="btn btn-outline-primary">
                <i class="bi bi-arrow-clockwise"></i> Clear Filters
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