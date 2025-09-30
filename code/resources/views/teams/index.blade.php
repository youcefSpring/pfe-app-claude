@extends('layouts.pfe-app')

@section('page-title', 'Teams Management')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-people me-2"></i>All Teams
                </h5>
                <div class="d-flex gap-2">
                    @if(auth()->user()?->role === 'student')
                        <a href="{{ route('teams.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus me-2"></i>Create Team
                        </a>
                    @endif
                    @if(in_array(auth()->user()?->role, ['department_head', 'admin']))
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-funnel me-2"></i>Filters
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="filterByStatus('all')">All Teams</a></li>
                                <li><a class="dropdown-item" href="#" onclick="filterByStatus('forming')">Forming</a></li>
                                <li><a class="dropdown-item" href="#" onclick="filterByStatus('complete')">Complete</a></li>
                                <li><a class="dropdown-item" href="#" onclick="filterByStatus('assigned')">Assigned</a></li>
                                <li><a class="dropdown-item" href="#" onclick="filterByStatus('active')">Active</a></li>
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <!-- Quick Stats -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h4 id="total-teams">-</h4>
                                <small>Total Teams</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-dark">
                            <div class="card-body text-center">
                                <h4 id="forming-teams">-</h4>
                                <small>Forming</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h4 id="complete-teams">-</h4>
                                <small>Complete</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h4 id="active-teams">-</h4>
                                <small>Active</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search and Filters -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <input type="text" class="form-control" id="searchInput" placeholder="Search teams by name...">
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="statusFilter">
                            <option value="">All Statuses</option>
                            <option value="forming">Forming</option>
                            <option value="complete">Complete</option>
                            <option value="assigned">Assigned</option>
                            <option value="active">Active</option>
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
                    <div class="col-md-2">
                        <button class="btn btn-outline-primary w-100" onclick="clearFilters()">
                            <i class="bi bi-x-circle me-2"></i>Clear
                        </button>
                    </div>
                </div>

                <!-- Teams List -->
                <div id="teams-container">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted mt-2">Loading teams...</p>
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

<!-- Team Details Modal -->
<div class="modal fade" id="teamModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Team Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="team-details">
                <!-- Details will be loaded here -->
            </div>
            <div class="modal-footer" id="team-actions">
                <!-- Actions will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Invite Member Modal -->
<div class="modal fade" id="inviteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Invite Team Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="inviteForm">
                    <input type="hidden" id="invite-team-id">
                    <div class="mb-3">
                        <label for="student-email" class="form-label">Student Email</label>
                        <input type="email" class="form-control" id="student-email" required
                               placeholder="student@university.edu">
                        <div class="form-text">Enter the email address of the student you want to invite.</div>
                    </div>
                    <div class="mb-3">
                        <label for="invite-message" class="form-label">Invitation Message (Optional)</label>
                        <textarea class="form-control" id="invite-message" rows="3"
                                  placeholder="Add a personal message to your invitation..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="sendInvitation()">Send Invitation</button>
            </div>
        </div>
    </div>
</div>

<!-- Subject Selection Modal -->
<div class="modal fade" id="subjectModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select Subject</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" class="form-control" id="subject-search" placeholder="Search available subjects...">
                </div>
                <div id="available-subjects">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted mt-2">Loading available subjects...</p>
                    </div>
                </div>
                <input type="hidden" id="subject-team-id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentPage = 1;
    let currentFilters = {};

    loadTeams();
    loadTeamStats();
    setupFilters();

    function loadTeams(page = 1) {
        currentPage = page;
        const params = new URLSearchParams({
            page: page,
            ...currentFilters
        });

        axios.get(`/api/teams?${params}`)
            .then(response => {
                renderTeams(response.data.data);
                renderPagination(response.data);
            })
            .catch(error => {
                document.getElementById('teams-container').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-circle me-2"></i>
                        Error loading teams. Please try again.
                    </div>
                `;
            });
    }

    function loadTeamStats() {
        axios.get('/api/reports/dashboard-stats')
            .then(response => {
                const data = response.data.data;
                document.getElementById('total-teams').textContent = data.total_teams || 0;
                document.getElementById('forming-teams').textContent = data.forming_teams || 0;
                document.getElementById('complete-teams').textContent = data.complete_teams || 0;
                document.getElementById('active-teams').textContent = data.active_teams || 0;
            })
            .catch(error => {
                console.log('Could not load team statistics');
            });
    }

    function renderTeams(teams) {
        const container = document.getElementById('teams-container');

        if (teams.length === 0) {
            container.innerHTML = `
                <div class="text-center py-5">
                    <i class="bi bi-people text-muted" style="font-size: 4rem;"></i>
                    <h5 class="text-muted mt-3">No teams found</h5>
                    <p class="text-muted">Try adjusting your filters or create a new team.</p>
                </div>
            `;
            return;
        }

        container.innerHTML = teams.map(team => `
            <div class="card mb-3 team-card" data-team-id="${team.id}">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="card-title mb-2">
                                        <a href="#" onclick="viewTeam(${team.id})" class="text-decoration-none">
                                            ${team.name}
                                        </a>
                                        ${isMyTeam(team) ? '<span class="badge bg-primary ms-2">My Team</span>' : ''}
                                    </h6>
                                    <div class="d-flex align-items-center gap-3 mb-2">
                                        <small class="text-muted">
                                            <i class="bi bi-person me-1"></i>
                                            ${team.members.length} member${team.members.length !== 1 ? 's' : ''}
                                        </small>
                                        <small class="text-muted">
                                            <i class="bi bi-mortarboard me-1"></i>
                                            ${team.academic_level ? team.academic_level.charAt(0).toUpperCase() + team.academic_level.slice(1) : 'Not specified'}
                                        </small>
                                        ${team.subject ? `
                                            <small class="text-muted">
                                                <i class="bi bi-journal-text me-1"></i>
                                                ${team.subject.title}
                                            </small>
                                        ` : ''}
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        ${team.members.map(member => `
                                            <span class="badge bg-light text-dark">
                                                ${member.user.name}
                                                ${member.role === 'leader' ? '<i class="bi bi-star-fill text-warning ms-1"></i>' : ''}
                                            </span>
                                        `).join('')}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="mb-2">
                                <span class="badge status-${team.status} mb-2">
                                    ${team.status.replace('_', ' ').toUpperCase()}
                                </span>
                            </div>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-outline-primary" onclick="viewTeam(${team.id})" title="View Details">
                                    <i class="bi bi-eye"></i>
                                </button>
                                ${canManageTeam(team) ? `
                                    <button class="btn btn-sm btn-outline-success" onclick="inviteMember(${team.id})" title="Invite Member">
                                        <i class="bi bi-person-plus"></i>
                                    </button>
                                ` : ''}
                                ${canSelectSubject(team) ? `
                                    <button class="btn btn-sm btn-outline-warning" onclick="selectSubject(${team.id})" title="Select Subject">
                                        <i class="bi bi-journal-text"></i>
                                    </button>
                                ` : ''}
                                ${canEditTeam(team) ? `
                                    <a href="/teams/${team.id}/edit" class="btn btn-sm btn-outline-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                ` : ''}
                                ${canDeleteTeam(team) ? `
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteTeam(${team.id})" title="Delete">
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
                    <a class="page-link" href="#" onclick="loadTeams(${paginationData.current_page - 1})">Previous</a>
                </li>
            `;
        }

        // Page numbers
        for (let i = 1; i <= paginationData.last_page; i++) {
            if (i === paginationData.current_page) {
                paginationHtml += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
            } else if (i === 1 || i === paginationData.last_page || Math.abs(i - paginationData.current_page) <= 2) {
                paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="loadTeams(${i})">${i}</a></li>`;
            } else if (i === paginationData.current_page - 3 || i === paginationData.current_page + 3) {
                paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }

        // Next button
        if (paginationData.current_page < paginationData.last_page) {
            paginationHtml += `
                <li class="page-item">
                    <a class="page-link" href="#" onclick="loadTeams(${paginationData.current_page + 1})">Next</a>
                </li>
            `;
        }

        pagination.innerHTML = paginationHtml;
    }

    function setupFilters() {
        ['statusFilter', 'departmentFilter', 'searchInput'].forEach(filterId => {
            const element = document.getElementById(filterId);
            if (element) {
                element.addEventListener('change', applyFilters);
                element.addEventListener('input', applyFilters);
            }
        });
    }

    function applyFilters() {
        const status = document.getElementById('statusFilter').value;
        const department = document.getElementById('departmentFilter').value;
        const search = document.getElementById('searchInput').value;

        currentFilters = {};
        if (status) currentFilters.status = status;
        if (department) currentFilters.department = department;
        if (search) currentFilters.search = search;

        loadTeams(1);
    }

    window.clearFilters = function() {
        document.getElementById('statusFilter').value = '';
        document.getElementById('departmentFilter').value = '';
        document.getElementById('searchInput').value = '';
        currentFilters = {};
        loadTeams(1);
    };

    window.filterByStatus = function(status) {
        document.getElementById('statusFilter').value = status === 'all' ? '' : status;
        applyFilters();
    };

    window.viewTeam = async function(teamId) {
        try {
            const response = await axios.get(`/api/teams/${teamId}`);
            const team = response.data.data;

            document.getElementById('team-details').innerHTML = `
                <div class="row">
                    <div class="col-md-8">
                        <h6 class="text-primary">${team.name}</h6>
                        <p class="text-muted mb-3">${team.description || 'No description available'}</p>

                        <div class="mb-3">
                            <strong>Academic Level:</strong>
                            <span class="badge bg-secondary ms-2">${team.academic_level ? team.academic_level.charAt(0).toUpperCase() + team.academic_level.slice(1) : 'Not specified'}</span>
                        </div>

                        <div class="mb-3">
                            <strong>Team Members:</strong>
                            <div class="mt-2">
                                ${team.members.map(member => `
                                    <div class="d-flex align-items-center mb-2 p-2 bg-light rounded">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0">${member.user.name}</h6>
                                            <small class="text-muted">${member.user.email}</small>
                                        </div>
                                        <div class="flex-shrink-0">
                                            ${member.role === 'leader' ?
                                                '<span class="badge bg-warning">Leader</span>' :
                                                '<span class="badge bg-secondary">Member</span>'
                                            }
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>

                        ${team.subject ? `
                            <div class="mb-3">
                                <strong>Selected Subject:</strong>
                                <div class="mt-2 p-3 bg-light rounded">
                                    <h6 class="mb-1">${team.subject.title}</h6>
                                    <p class="mb-0 text-muted">${team.subject.description ? team.subject.description.substring(0, 100) + '...' : ''}</p>
                                </div>
                            </div>
                        ` : ''}
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Team Information</h6>
                                <p class="mb-2"><strong>Status:</strong> <span class="badge status-${team.status}">${team.status.replace('_', ' ').toUpperCase()}</span></p>
                                <p class="mb-2"><strong>Created:</strong> ${formatDate(team.created_at)}</p>
                                <p class="mb-0"><strong>Members:</strong> ${team.members.length}/${team.max_members || 4}</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Set up action buttons
            const actionsContainer = document.getElementById('team-actions');
            let actionsHtml = '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>';

            if (canManageTeam(team)) {
                actionsHtml += ` <button class="btn btn-success" onclick="inviteMember(${team.id})">Invite Member</button>`;
            }

            if (canSelectSubject(team)) {
                actionsHtml += ` <button class="btn btn-warning" onclick="selectSubject(${team.id})">Select Subject</button>`;
            }

            if (canEditTeam(team)) {
                actionsHtml += ` <a href="/teams/${team.id}/edit" class="btn btn-primary">Edit Team</a>`;
            }

            actionsContainer.innerHTML = actionsHtml;

            const modal = new bootstrap.Modal(document.getElementById('teamModal'));
            modal.show();
        } catch (error) {
            alert('Error loading team details');
        }
    };

    window.inviteMember = function(teamId) {
        document.getElementById('invite-team-id').value = teamId;
        document.getElementById('inviteForm').reset();

        const modal = new bootstrap.Modal(document.getElementById('inviteModal'));
        modal.show();
    };

    window.sendInvitation = async function() {
        const teamId = document.getElementById('invite-team-id').value;
        const email = document.getElementById('student-email').value;
        const message = document.getElementById('invite-message').value;

        if (!email) {
            alert('Please enter a student email');
            return;
        }

        try {
            await axios.post(`/api/teams/${teamId}/members`, {
                email: email,
                message: message
            });

            bootstrap.Modal.getInstance(document.getElementById('inviteModal')).hide();
            loadTeams(currentPage);
            alert('Invitation sent successfully');
        } catch (error) {
            alert('Error sending invitation: ' + (error.response?.data?.message || 'Unknown error'));
        }
    };

    window.selectSubject = async function(teamId) {
        document.getElementById('subject-team-id').value = teamId;

        try {
            const response = await axios.get('/api/subjects/available');
            const subjects = response.data.data;

            const container = document.getElementById('available-subjects');

            if (subjects.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-4">
                        <i class="bi bi-journal-text text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2">No available subjects at this time</p>
                    </div>
                `;
            } else {
                container.innerHTML = subjects.map(subject => `
                    <div class="card mb-2 subject-option" onclick="confirmSubjectSelection(${teamId}, ${subject.id})">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="card-title mb-1">${subject.title}</h6>
                                    <p class="card-text text-muted mb-2">${subject.description ? subject.description.substring(0, 100) + '...' : ''}</p>
                                    <small class="text-muted">
                                        <i class="bi bi-person me-1"></i>${subject.teacher.name} |
                                        <i class="bi bi-building me-1"></i>${subject.department} |
                                        <i class="bi bi-mortarboard me-1"></i>${subject.level}
                                    </small>
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="badge bg-primary">Available</span>
                                </div>
                            </div>
                        </div>
                    </div>
                `).join('');
            }

            const modal = new bootstrap.Modal(document.getElementById('subjectModal'));
            modal.show();
        } catch (error) {
            alert('Error loading available subjects');
        }
    };

    window.confirmSubjectSelection = async function(teamId, subjectId) {
        if (!confirm('Are you sure you want to select this subject? This action cannot be undone.')) {
            return;
        }

        try {
            await axios.post(`/api/teams/${teamId}/select-subject`, {
                subject_id: subjectId
            });

            bootstrap.Modal.getInstance(document.getElementById('subjectModal')).hide();
            loadTeams(currentPage);
            alert('Subject selected successfully');
        } catch (error) {
            alert('Error selecting subject: ' + (error.response?.data?.message || 'Unknown error'));
        }
    };

    window.deleteTeam = async function(teamId) {
        if (!confirm('Are you sure you want to delete this team? This action cannot be undone.')) {
            return;
        }

        try {
            await axios.delete(`/api/teams/${teamId}`);
            loadTeams(currentPage);
            alert('Team deleted successfully');
        } catch (error) {
            alert('Error deleting team');
        }
    };

    function isMyTeam(team) {
        const userId = {{ auth()->id() }};
        return team.members.some(member => member.user_id === userId);
    }

    function canManageTeam(team) {
        const userRole = '{{ auth()->user()?->role }}';
        const userId = {{ auth()->id() }};

        if (userRole === 'admin') return true;

        // Team leader can manage the team
        const userMember = team.members.find(member => member.user_id === userId);
        return userMember && userMember.role === 'leader' && team.status === 'forming';
    }

    function canSelectSubject(team) {
        const userRole = '{{ auth()->user()?->role }}';
        const userId = {{ auth()->id() }};

        if (userRole === 'admin') return true;

        // Team can select subject if it's complete and no subject is assigned
        const userMember = team.members.find(member => member.user_id === userId);
        return userMember && team.status === 'complete' && !team.subject;
    }

    function canEditTeam(team) {
        const userRole = '{{ auth()->user()?->role }}';
        const userId = {{ auth()->id() }};

        if (userRole === 'admin') return true;

        // Team leader can edit if team is forming or complete
        const userMember = team.members.find(member => member.user_id === userId);
        return userMember && userMember.role === 'leader' && ['forming', 'complete'].includes(team.status);
    }

    function canDeleteTeam(team) {
        const userRole = '{{ auth()->user()?->role }}';
        const userId = {{ auth()->id() }};

        if (userRole === 'admin') return true;

        // Team leader can delete if team is forming
        const userMember = team.members.find(member => member.user_id === userId);
        return userMember && userMember.role === 'leader' && team.status === 'forming';
    }

    function formatDate(dateString) {
        return new Date(dateString).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    }
});
</script>
@endpush

@push('styles')
<style>
.team-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.subject-option {
    cursor: pointer;
    transition: all 0.3s ease;
}

.subject-option:hover {
    background-color: #f8f9fa;
    transform: translateY(-1px);
}

.badge {
    font-size: 0.75rem;
}

.card.bg-light {
    border-left: 4px solid var(--bs-primary);
}
</style>
@endpush