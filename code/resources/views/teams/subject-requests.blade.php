@extends('layouts.pfe-app')

@section('page-title', 'Subject Requests')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Subject Requests for {{ $team->name }}</h4>
                    <div>
                        <a href="{{ route('teams.show', $team) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Team
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($team->subjectRequests->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Subject Requests Yet</h5>
                            <p class="text-muted">Your team hasn't submitted any subject requests.</p>
                            <a href="{{ route('teams.show', $team) }}" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Submit Your First Request
                            </a>
                        </div>
                    @else
                        @php
                            $user = Auth::user();
                            $member = $team->members->where('student_id', $user->id)->first();
                            $isLeader = $member && $member->role === 'leader';
                            $sortedRequests = $team->subjectRequests->sortBy('priority_order');
                        @endphp

                        @if($isLeader && $sortedRequests->count() > 1)
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Tip:</strong> Drag and drop the requests below to change their priority order. Higher priority requests are considered first during allocation.
                            </div>
                        @endif
                        <!-- Sortable Request List -->
                        <div id="sortable-requests" class="@if($isLeader) sortable-enabled @endif">
                            @foreach($sortedRequests as $request)
                                <div class="request-item mb-3" data-request-id="{{ $request->id }}">
                                    <div class="card border-2
                                        @if($request->isApproved()) border-success
                                        @elseif($request->isRejected()) border-danger
                                        @else border-warning
                                        @endif">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                @if($isLeader)
                                                    <i class="fas fa-grip-vertical text-muted me-2 drag-handle" style="cursor: move;" title="Drag to reorder"></i>
                                                @endif
                                                <span class="badge bg-primary me-2">#{{ $request->priority_order }}</span>
                                                <h6 class="mb-0">{{ $request->subject->title }}</h6>
                                            </div>
                                            <span class="badge {{ $request->getStatusBadgeClass() }}">
                                                {{ ucfirst($request->status) }}
                                            </span>
                                        </div>
                                        <div class="card-body">
                                            <p class="text-muted mb-2">
                                                <i class="fas fa-user"></i> {{ $request->subject->teacher->name }}
                                            </p>
                                            <p class="text-sm text-muted mb-2">
                                                <i class="fas fa-clock"></i>
                                                Requested {{ $request->requested_at->diffForHumans() }}
                                            </p>

                                            @if($request->request_message)
                                                <div class="mb-3">
                                                    <small class="text-muted">Your Message:</small>
                                                    <p class="text-sm">{{ $request->request_message }}</p>
                                                </div>
                                            @endif

                                            @if($request->admin_response)
                                                <div class="alert alert-{{ $request->isApproved() ? 'success' : 'danger' }} alert-sm">
                                                    <small class="fw-bold">Admin Response:</small><br>
                                                    <small>{{ $request->admin_response }}</small>
                                                </div>
                                            @endif

                                            @if($request->responded_at)
                                                <small class="text-muted">
                                                    <i class="fas fa-check"></i>
                                                    Responded {{ $request->responded_at->diffForHumans() }}
                                                    by {{ $request->respondedBy->name ?? 'Admin' }}
                                                </small>
                                            @endif
                                        </div>
                                        <div class="card-footer">
                                            @if($request->isPending())
                                                @php
                                                    $user = Auth::user();
                                                    $member = $team->members->where('student_id', $user->id)->first();
                                                    $isLeader = $member && $member->role === 'leader';
                                                @endphp
                                                @if($isLeader)
                                                    <form action="{{ route('teams.cancel-subject-request', [$team, $request]) }}"
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                                onclick="return confirm('Are you sure you want to cancel this request?')">
                                                            <i class="fas fa-times"></i> Cancel Request
                                                        </button>
                                                    </form>
                                                @endif
                                                <span class="badge bg-warning text-dark">Awaiting Admin Review</span>
                                            @elseif($request->isApproved())
                                                <span class="badge bg-success">✓ Approved</span>
                                            @else
                                                <span class="badge bg-danger">✗ Rejected</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($isLeader && $sortedRequests->count() > 1)
                            <div class="text-center mt-3">
                                <button id="save-order" class="btn btn-success" style="display: none;">
                                    <i class="fas fa-save"></i> Save New Order
                                </button>
                            </div>
                        @endif
                    @endif

                    <!-- Stats -->
                    @if($team->subjectRequests->isNotEmpty())
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Request Statistics</h6>
                                        <div class="row text-center">
                                            <div class="col-md-3">
                                                <div class="text-warning">
                                                    <h4>{{ $team->subjectRequests->where('status', 'pending')->count() }}</h4>
                                                    <small>Pending</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-success">
                                                    <h4>{{ $team->subjectRequests->where('status', 'approved')->count() }}</h4>
                                                    <small>Approved</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-danger">
                                                    <h4>{{ $team->subjectRequests->where('status', 'rejected')->count() }}</h4>
                                                    <small>Rejected</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-primary">
                                                    <h4>{{ $team->subjectRequests->count() }}</h4>
                                                    <small>Total</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.sortable-enabled .request-item {
    transition: all 0.3s ease;
}

.sortable-enabled .request-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.sortable-enabled .request-item.sortable-ghost {
    opacity: 0.4;
}

.sortable-enabled .request-item.sortable-chosen {
    transform: rotate(5deg);
}

.drag-handle:hover {
    color: #0d6efd !important;
}

.request-item.moving {
    z-index: 1000;
    transform: rotate(5deg);
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sortableContainer = document.getElementById('sortable-requests');
    const saveButton = document.getElementById('save-order');
    let hasChanges = false;

    if (sortableContainer && sortableContainer.classList.contains('sortable-enabled')) {
        const sortable = Sortable.create(sortableContainer, {
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            handle: '.drag-handle',
            onStart: function(evt) {
                evt.item.classList.add('moving');
            },
            onEnd: function(evt) {
                evt.item.classList.remove('moving');
                hasChanges = true;
                saveButton.style.display = 'block';
                updatePriorityNumbers();
            }
        });

        // Save order functionality
        if (saveButton) {
            saveButton.addEventListener('click', function() {
                const requestItems = sortableContainer.querySelectorAll('.request-item');
                const requestIds = Array.from(requestItems).map(item => item.dataset.requestId);

                // Create form to submit new order
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('teams.update-subject-request-order', $team) }}';
                form.style.display = 'none';

                // Add CSRF token
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);

                // Add method override
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'PUT';
                form.appendChild(methodField);

                // Add request IDs
                requestIds.forEach(function(id) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'request_ids[]';
                    input.value = id;
                    form.appendChild(input);
                });

                document.body.appendChild(form);
                form.submit();
            });
        }

        function updatePriorityNumbers() {
            const requestItems = sortableContainer.querySelectorAll('.request-item');
            requestItems.forEach(function(item, index) {
                const badge = item.querySelector('.badge.bg-primary');
                if (badge) {
                    badge.textContent = '#' + (index + 1);
                }
            });
        }
    }
});
</script>
@endpush