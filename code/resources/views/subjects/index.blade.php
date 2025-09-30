@extends('layouts.pfe-app')

@section('page-title', 'Subjects Management')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-journal-text me-2"></i>All Subjects
                </h5>
                <div class="d-flex gap-2">
                    @if(in_array(auth()->user()?->role, ['teacher', 'admin']))
                        <a href="{{ route('subjects.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus me-2"></i>Add New Subject
                        </a>
                    @endif
                    @if(auth()->user()?->role === 'department_head')
                        <a href="{{ route('subjects.pending-validation') }}" class="btn btn-warning">
                            <i class="bi bi-clock me-2"></i>Pending Validation
                            <span class="badge bg-light text-dark ms-1" id="pending-count">0</span>
                        </a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <select class="form-select" id="statusFilter">
                            <option value="">All Statuses</option>
                            <option value="draft">Draft</option>
                            <option value="pending">Pending Validation</option>
                            <option value="validated">Validated</option>
                            <option value="rejected">Rejected</option>
                            <option value="assigned">Assigned</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="departmentFilter">
                            <option value="">All Departments</option>
                            <option value="Computer Science">Computer Science</option>
                            <option value="Engineering">Engineering</option>
                            <option value="Mathematics">Mathematics</option>
                            <option value="Physics">Physics</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="levelFilter">
                            <option value="">All Levels</option>
                            <option value="license">License</option>
                            <option value="master">Master</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control" id="searchInput" placeholder="Search subjects...">
                    </div>
                </div>

                <!-- Subjects List -->
                <div id="subjects-container">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted mt-2">Loading subjects...</p>
                    </div>
                </div>

                <!-- Pagination -->
                <nav class="mt-4">
                    <ul class="pagination justify-content-center" id="pagination">
                        <!-- Pagination will be populated by JavaScript -->
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Subject Details Modal -->
<div class="modal fade" id="subjectModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Subject Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="subject-details">
                <!-- Details will be loaded here -->
            </div>
            <div class="modal-footer" id="subject-actions">
                <!-- Actions will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Validation Modal (Department Heads) -->
@if(auth()->user()?->role === 'department_head')
<div class="modal fade" id="validationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Validate Subject</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="validationForm">
                    <input type="hidden" id="subject-id">
                    <div class="mb-3">
                        <label class="form-label">Validation Decision</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="decision" value="validate" id="validate">
                            <label class="form-check-label text-success" for="validate">
                                <i class="bi bi-check-circle me-2"></i>Validate Subject
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="decision" value="reject" id="reject">
                            <label class="form-check-label text-danger" for="reject">
                                <i class="bi bi-x-circle me-2"></i>Reject Subject
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="feedback" class="form-label">Feedback/Comments</label>
                        <textarea class="form-control" id="feedback" rows="3" placeholder="Provide feedback for the teacher..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitValidation()">Submit Decision</button>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentPage = 1;
    let currentFilters = {};

    loadSubjects();
    setupFilters();

    function loadSubjects(page = 1) {
        currentPage = page;
        const params = new URLSearchParams({
            page: page,
            ...currentFilters
        });

        axios.get(`/api/subjects?${params}`)
            .then(response => {
                renderSubjects(response.data.data);
                renderPagination(response.data);
            })
            .catch(error => {
                document.getElementById('subjects-container').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-circle me-2"></i>
                        Error loading subjects. Please try again.
                    </div>
                `;
            });
    }

    function renderSubjects(subjects) {
        const container = document.getElementById('subjects-container');

        if (subjects.length === 0) {
            container.innerHTML = `
                <div class="text-center py-5">
                    <i class="bi bi-journal-text text-muted" style="font-size: 4rem;"></i>
                    <h5 class="text-muted mt-3">No subjects found</h5>
                    <p class="text-muted">Try adjusting your filters or add a new subject.</p>
                </div>
            `;
            return;
        }

        container.innerHTML = subjects.map(subject => `
            <div class="card mb-3 subject-card" data-subject-id="${subject.id}">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="card-title mb-2">
                                        <a href="#" onclick="viewSubject(${subject.id})" class="text-decoration-none">
                                            ${subject.title}
                                        </a>
                                    </h6>
                                    <p class="card-text text-muted mb-2">${subject.description ? subject.description.substring(0, 150) + '...' : 'No description available'}</p>
                                    <div class="d-flex align-items-center gap-3">
                                        <small class="text-muted">
                                            <i class="bi bi-person me-1"></i>
                                            ${subject.teacher ? subject.teacher.name : 'No teacher assigned'}
                                        </small>
                                        <small class="text-muted">
                                            <i class="bi bi-building me-1"></i>
                                            ${subject.department || 'No department'}
                                        </small>
                                        <small class="text-muted">
                                            <i class="bi bi-mortarboard me-1"></i>
                                            ${subject.level ? subject.level.charAt(0).toUpperCase() + subject.level.slice(1) : 'Not specified'}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="mb-2">
                                <span class="badge status-${subject.status} mb-2">
                                    ${subject.status.replace('_', ' ').toUpperCase()}
                                </span>
                            </div>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-outline-primary" onclick="viewSubject(${subject.id})" title="View Details">
                                    <i class="bi bi-eye"></i>
                                </button>
                                ${canEditSubject(subject) ? `
                                    <a href="/subjects/${subject.id}/edit" class="btn btn-sm btn-outline-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                ` : ''}
                                ${canValidateSubject(subject) ? `
                                    <button class="btn btn-sm btn-outline-success" onclick="validateSubject(${subject.id})" title="Validate">
                                        <i class="bi bi-check-circle"></i>
                                    </button>
                                ` : ''}
                                ${canDeleteSubject(subject) ? `
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteSubject(${subject.id})" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
    }

    function renderPagination(paginationData) {
        const pagination = document.getElementById('pagination');

        if (paginationData.last_page <= 1) {
            pagination.innerHTML = '';
            return;
        }

        let paginationHtml = '';

        // Previous button
        if (paginationData.current_page > 1) {
            paginationHtml += `
                <li class="page-item">
                    <a class="page-link" href="#" onclick="loadSubjects(${paginationData.current_page - 1})">Previous</a>
                </li>
            `;
        }

        // Page numbers
        for (let i = 1; i <= paginationData.last_page; i++) {
            if (i === paginationData.current_page) {
                paginationHtml += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
            } else if (i === 1 || i === paginationData.last_page || Math.abs(i - paginationData.current_page) <= 2) {
                paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="loadSubjects(${i})">${i}</a></li>`;
            } else if (i === paginationData.current_page - 3 || i === paginationData.current_page + 3) {
                paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }

        // Next button
        if (paginationData.current_page < paginationData.last_page) {
            paginationHtml += `
                <li class="page-item">
                    <a class="page-link" href="#" onclick="loadSubjects(${paginationData.current_page + 1})">Next</a>
                </li>
            `;
        }

        pagination.innerHTML = paginationHtml;
    }

    function setupFilters() {
        ['statusFilter', 'departmentFilter', 'levelFilter', 'searchInput'].forEach(filterId => {
            document.getElementById(filterId).addEventListener('change', applyFilters);
            document.getElementById(filterId).addEventListener('input', applyFilters);
        });
    }

    function applyFilters() {
        const status = document.getElementById('statusFilter').value;
        const department = document.getElementById('departmentFilter').value;
        const level = document.getElementById('levelFilter').value;
        const search = document.getElementById('searchInput').value;

        currentFilters = {};
        if (status) currentFilters.status = status;
        if (department) currentFilters.department = department;
        if (level) currentFilters.level = level;
        if (search) currentFilters.search = search;

        loadSubjects(1);
    }

    window.viewSubject = async function(subjectId) {
        try {
            const response = await axios.get(`/api/subjects/${subjectId}`);
            const subject = response.data.data;

            document.getElementById('subject-details').innerHTML = `
                <div class="row">
                    <div class="col-md-8">
                        <h6 class="text-primary">${subject.title}</h6>
                        <p class="text-muted mb-3">${subject.description || 'No description available'}</p>

                        <div class="mb-3">
                            <strong>Requirements:</strong>
                            <p class="mb-0">${subject.requirements || 'No specific requirements'}</p>
                        </div>

                        ${subject.objectives ? `
                            <div class="mb-3">
                                <strong>Objectives:</strong>
                                <p class="mb-0">${subject.objectives}</p>
                            </div>
                        ` : ''}

                        ${subject.expected_deliverables ? `
                            <div class="mb-3">
                                <strong>Expected Deliverables:</strong>
                                <p class="mb-0">${subject.expected_deliverables}</p>
                            </div>
                        ` : ''}
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Details</h6>
                                <p class="mb-2"><strong>Status:</strong> <span class="badge status-${subject.status}">${subject.status.replace('_', ' ').toUpperCase()}</span></p>
                                <p class="mb-2"><strong>Department:</strong> ${subject.department || 'Not specified'}</p>
                                <p class="mb-2"><strong>Level:</strong> ${subject.level ? subject.level.charAt(0).toUpperCase() + subject.level.slice(1) : 'Not specified'}</p>
                                <p class="mb-2"><strong>Max Teams:</strong> ${subject.max_teams || 'Unlimited'}</p>
                                <p class="mb-2"><strong>Proposed by:</strong> ${subject.teacher ? subject.teacher.name : 'Unknown'}</p>
                                ${subject.team ? `<p class="mb-0"><strong>Assigned to:</strong> ${subject.team.name}</p>` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Set up action buttons
            const actionsContainer = document.getElementById('subject-actions');
            let actionsHtml = '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>';

            if (canEditSubject(subject)) {
                actionsHtml += ` <a href="/subjects/${subject.id}/edit" class="btn btn-warning">Edit Subject</a>`;
            }

            if (canValidateSubject(subject)) {
                actionsHtml += ` <button class="btn btn-success" onclick="validateSubject(${subject.id})">Validate</button>`;
            }

            actionsContainer.innerHTML = actionsHtml;

            const modal = new bootstrap.Modal(document.getElementById('subjectModal'));
            modal.show();
        } catch (error) {
            alert('Error loading subject details');
        }
    };

    @if(auth()->user()?->role === 'department_head')
    window.validateSubject = function(subjectId) {
        document.getElementById('subject-id').value = subjectId;
        document.getElementById('validationForm').reset();

        const modal = new bootstrap.Modal(document.getElementById('validationModal'));
        modal.show();
    };

    window.submitValidation = async function() {
        const subjectId = document.getElementById('subject-id').value;
        const decision = document.querySelector('input[name="decision"]:checked');
        const feedback = document.getElementById('feedback').value;

        if (!decision) {
            alert('Please select a validation decision');
            return;
        }

        try {
            const endpoint = decision.value === 'validate' ? 'validate' : 'reject';
            await axios.post(`/api/subjects/${subjectId}/${endpoint}`, {
                feedback: feedback
            });

            bootstrap.Modal.getInstance(document.getElementById('validationModal')).hide();
            loadSubjects(currentPage);

            alert(`Subject ${decision.value === 'validate' ? 'validated' : 'rejected'} successfully`);
        } catch (error) {
            alert('Error processing validation');
        }
    };
    @endif

    window.deleteSubject = async function(subjectId) {
        if (!confirm('Are you sure you want to delete this subject? This action cannot be undone.')) {
            return;
        }

        try {
            await axios.delete(`/api/subjects/${subjectId}`);
            loadSubjects(currentPage);
            alert('Subject deleted successfully');
        } catch (error) {
            alert('Error deleting subject');
        }
    };

    function canEditSubject(subject) {
        const userRole = '{{ auth()->user()?->role }}';
        const userId = {{ auth()->id() }};

        return userRole === 'admin' ||
               (userRole === 'teacher' && subject.proposed_by === userId && subject.status === 'draft');
    }

    function canValidateSubject(subject) {
        return '{{ auth()->user()?->role }}' === 'department_head' && subject.status === 'pending';
    }

    function canDeleteSubject(subject) {
        const userRole = '{{ auth()->user()?->role }}';
        const userId = {{ auth()->id() }};

        return userRole === 'admin' ||
               (userRole === 'teacher' && subject.proposed_by === userId && ['draft', 'rejected'].includes(subject.status));
    }

    // Load pending validation count for department heads
    @if(auth()->user()?->role === 'department_head')
    axios.get('/api/subjects/pending-validation')
        .then(response => {
            document.getElementById('pending-count').textContent = response.data.data.length;
        })
        .catch(() => {
            document.getElementById('pending-count').textContent = '0';
        });
    @endif
});
</script>
@endpush