@extends('layouts.pfe-app')

@section('page-title', 'Subject Requests Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Subject Requests Management</h4>
                    <p class="text-muted mb-0">Review and manage team subject requests</p>
                </div>
                <div class="card-body">
                    @if($subjectRequests->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Subject Requests</h5>
                            <p class="text-muted">There are currently no subject requests to review.</p>
                        </div>
                    @else
                        <!-- Filter tabs -->
                        <ul class="nav nav-tabs mb-4" id="requestTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
                                    <i class="fas fa-clock"></i> Pending
                                    <span class="badge bg-warning ms-1">{{ $subjectRequests->where('status', 'pending')->count() }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="approved-tab" data-bs-toggle="tab" data-bs-target="#approved" type="button" role="tab">
                                    <i class="fas fa-check"></i> Approved
                                    <span class="badge bg-success ms-1">{{ $subjectRequests->where('status', 'approved')->count() }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="rejected-tab" data-bs-toggle="tab" data-bs-target="#rejected" type="button" role="tab">
                                    <i class="fas fa-times"></i> Rejected
                                    <span class="badge bg-danger ms-1">{{ $subjectRequests->where('status', 'rejected')->count() }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab">
                                    <i class="fas fa-list"></i> All
                                    <span class="badge bg-primary ms-1">{{ $subjectRequests->count() }}</span>
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="requestTabsContent">
                            <!-- Pending Requests -->
                            <div class="tab-pane fade show active" id="pending" role="tabpanel">
                                @include('admin.subject-requests.partials.request-list', ['requests' => $subjectRequests->where('status', 'pending')])
                            </div>

                            <!-- Approved Requests -->
                            <div class="tab-pane fade" id="approved" role="tabpanel">
                                @include('admin.subject-requests.partials.request-list', ['requests' => $subjectRequests->where('status', 'approved')])
                            </div>

                            <!-- Rejected Requests -->
                            <div class="tab-pane fade" id="rejected" role="tabpanel">
                                @include('admin.subject-requests.partials.request-list', ['requests' => $subjectRequests->where('status', 'rejected')])
                            </div>

                            <!-- All Requests -->
                            <div class="tab-pane fade" id="all" role="tabpanel">
                                @include('admin.subject-requests.partials.request-list', ['requests' => $subjectRequests])
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $subjectRequests->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Response Modal -->
<div class="modal fade" id="responseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="responseModalTitle">Respond to Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="responseForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="admin_response" class="form-label">Response Message</label>
                        <textarea name="admin_response" id="admin_response" class="form-control" rows="4"
                                  placeholder="Enter your response message..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn" id="responseSubmitBtn">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openResponseModal(requestId, action, subjectTitle, teamName) {
    const modal = document.getElementById('responseModal');
    const form = document.getElementById('responseForm');
    const title = document.getElementById('responseModalTitle');
    const submitBtn = document.getElementById('responseSubmitBtn');
    const responseField = document.getElementById('admin_response');

    // Set form action
    form.action = `/admin/subject-requests/${requestId}/${action}`;

    // Set modal title and button
    if (action === 'approve') {
        title.textContent = `Approve Request: ${subjectTitle}`;
        submitBtn.textContent = 'Approve Request';
        submitBtn.className = 'btn btn-success';
        responseField.placeholder = 'Optional approval message...';
        responseField.required = false;
    } else {
        title.textContent = `Reject Request: ${subjectTitle}`;
        submitBtn.textContent = 'Reject Request';
        submitBtn.className = 'btn btn-danger';
        responseField.placeholder = 'Required rejection reason...';
        responseField.required = true;
    }

    // Clear previous response
    responseField.value = '';

    // Show modal
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
}
</script>
@endpush