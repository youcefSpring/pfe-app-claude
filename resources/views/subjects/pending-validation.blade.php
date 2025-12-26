@extends('layouts.pfe-app')

@section('page-title', 'Pending Validation')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Subjects Pending Validation</h4>
                    <div>
                        @if($subjects->count() > 0)
                            <button type="button" class="btn btn-success btn-sm" onclick="selectAllSubjects()">
                                <i class="fas fa-check-double"></i> Select All
                            </button>
                            <button type="button" class="btn btn-primary btn-sm" onclick="batchApprove()" disabled id="batchApproveBtn">
                                <i class="fas fa-check"></i> Batch Approve
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="batchReject()" disabled id="batchRejectBtn">
                                <i class="fas fa-times"></i> Batch Reject
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if($subjects->count() > 0)
                        <form id="batchValidationForm" method="POST" action="{{ route('subjects.batch-validate') }}">
                            @csrf
                            <input type="hidden" name="action" id="batchAction">

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th width="50">
                                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                            </th>
                                            <th>Subject</th>
                                            <th>Teacher</th>
                                            <th>Keywords</th>
                                            <th>Submitted</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($subjects as $subject)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="subject_ids[]" value="{{ $subject->id }}"
                                                           class="subject-checkbox" onchange="updateBatchButtons()">
                                                </td>
                                                <td>
                                                    <div>
                                                        <h6 class="mb-1">{{ $subject->title }}</h6>
                                                        <small class="text-muted">
                                                            {{ Str::limit($subject->description, 80) }}
                                                        </small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <div class="fw-bold">{{ $subject->teacher->name }}</div>
                                                        <small class="text-muted">{{ $subject->teacher->department }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-wrap">
                                                        @foreach(array_slice(explode(',', $subject->keywords), 0, 2) as $keyword)
                                                            <span class="badge bg-secondary me-1 mb-1">{{ trim($keyword) }}</span>
                                                        @endforeach
                                                        @if(count(explode(',', $subject->keywords)) > 2)
                                                            <span class="badge bg-light text-dark">+{{ count(explode(',', $subject->keywords)) - 2 }}</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ $subject->created_at->format('M d, Y') }}<br>
                                                        {{ $subject->created_at->diffForHumans() }}
                                                    </small>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('subjects.show', $subject) }}"
                                                           class="btn btn-outline-primary btn-sm" title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-success btn-sm"
                                                                onclick="validateSubject({{ $subject->id }}, 'approve')" title="Approve">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                                onclick="showRejectModal({{ $subject->id }}, '{{ $subject->title }}')" title="Reject">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </form>

                        {{ $subjects->links() }}
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-clipboard-check fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Pending Validations</h5>
                            <p class="text-muted">All subjects in your department have been reviewed.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Subject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="rejectForm" method="POST">
                @csrf
                <input type="hidden" name="action" value="reject">
                <div class="modal-header">
                    <h5 class="modal-title">Reject Subject</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to reject "<span id="rejectSubjectTitle"></span>"?</p>

                    <div class="mb-3">
                        <label for="rejectNotes" class="form-label">Rejection Notes</label>
                        <textarea class="form-control" name="notes" id="rejectNotes" rows="3"
                                  placeholder="Provide feedback on why this subject is being rejected..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times"></i> Reject Subject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Batch Action Confirmation Modal -->
<div class="modal fade" id="batchActionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="batchActionTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="batchActionMessage"></p>
                <div id="selectedSubjectsList"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn" id="confirmBatchAction" onclick="confirmBatchAction()">
                    Confirm
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentBatchAction = '';

function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.subject-checkbox');

    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });

    updateBatchButtons();
}

function selectAllSubjects() {
    const selectAll = document.getElementById('selectAll');
    selectAll.checked = true;
    toggleSelectAll();
}

function updateBatchButtons() {
    const checkedBoxes = document.querySelectorAll('.subject-checkbox:checked');
    const batchApproveBtn = document.getElementById('batchApproveBtn');
    const batchRejectBtn = document.getElementById('batchRejectBtn');

    const hasSelected = checkedBoxes.length > 0;
    batchApproveBtn.disabled = !hasSelected;
    batchRejectBtn.disabled = !hasSelected;
}

function validateSubject(subjectId, action) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/subjects/${subjectId}/validate`;

    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';

    const actionInput = document.createElement('input');
    actionInput.type = 'hidden';
    actionInput.name = 'action';
    actionInput.value = action;

    form.appendChild(csrfToken);
    form.appendChild(actionInput);
    document.body.appendChild(form);
    form.submit();
}

function showRejectModal(subjectId, subjectTitle) {
    document.getElementById('rejectSubjectTitle').textContent = subjectTitle;
    document.getElementById('rejectForm').action = `/subjects/${subjectId}/validate`;

    const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
    modal.show();
}

function batchApprove() {
    currentBatchAction = 'approve';
    showBatchActionModal('Batch Approve Subjects', 'approve', 'Are you sure you want to approve the selected subjects?');
}

function batchReject() {
    currentBatchAction = 'reject';
    showBatchActionModal('Batch Reject Subjects', 'reject', 'Are you sure you want to reject the selected subjects?');
}

function showBatchActionModal(title, action, message) {
    const checkedBoxes = document.querySelectorAll('.subject-checkbox:checked');

    if (checkedBoxes.length === 0) {
        alert('Please select at least one subject.');
        return;
    }

    document.getElementById('batchActionTitle').textContent = title;
    document.getElementById('batchActionMessage').textContent = message;

    const confirmBtn = document.getElementById('confirmBatchAction');
    confirmBtn.className = `btn ${action === 'approve' ? 'btn-success' : 'btn-danger'}`;
    confirmBtn.innerHTML = action === 'approve' ? '<i class="fas fa-check"></i> Approve' : '<i class="fas fa-times"></i> Reject';

    // Show selected subjects
    const subjectsList = document.getElementById('selectedSubjectsList');
    const subjects = Array.from(checkedBoxes).map(cb => {
        const row = cb.closest('tr');
        const title = row.querySelector('h6').textContent;
        return `<li>${title}</li>`;
    }).join('');

    subjectsList.innerHTML = `<div class="mt-3"><strong>Selected subjects (${checkedBoxes.length}):</strong><ul class="mt-2">${subjects}</ul></div>`;

    const modal = new bootstrap.Modal(document.getElementById('batchActionModal'));
    modal.show();
}

function confirmBatchAction() {
    document.getElementById('batchAction').value = currentBatchAction;
    document.getElementById('batchValidationForm').submit();
}
</script>
@endpush