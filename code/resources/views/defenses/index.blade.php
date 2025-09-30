@extends('layouts.pfe-app')

@section('page-title', 'Defense Management')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-shield-check me-2"></i>Defense Schedule
                </h5>
                <div class="d-flex gap-2">
                    @if(in_array(auth()->user()?->role, ['department_head', 'admin']))
                        <a href="{{ route('defenses.schedule') }}" class="btn btn-primary">
                            <i class="bi bi-calendar-plus me-2"></i>Schedule Defense
                        </a>
                    @endif
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-calendar-event me-2"></i>View
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="switchView('list')">List View</a></li>
                            <li><a class="dropdown-item" href="#" onclick="switchView('calendar')">Calendar View</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('defenses.calendar') }}">Full Calendar</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Quick Stats -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h4 id="total-defenses">-</h4>
                                <small>Total Defenses</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-dark">
                            <div class="card-body text-center">
                                <h4 id="scheduled-defenses">-</h4>
                                <small>Scheduled</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h4 id="today-defenses">-</h4>
                                <small>Today</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h4 id="completed-defenses">-</h4>
                                <small>Completed</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <select class="form-select" id="statusFilter">
                            <option value="">All Statuses</option>
                            <option value="scheduled">Scheduled</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="dateFilter">
                            <option value="">All Dates</option>
                            <option value="today">Today</option>
                            <option value="tomorrow">Tomorrow</option>
                            <option value="this_week">This Week</option>
                            <option value="next_week">Next Week</option>
                            <option value="this_month">This Month</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="roomFilter">
                            <option value="">All Rooms</option>
                            <!-- Rooms will be populated by JavaScript -->
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control" id="searchInput" placeholder="Search by project or team...">
                    </div>
                </div>

                <!-- View Toggle -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="btn-group" role="group">
                        <input type="radio" class="btn-check" name="view" id="list-view" checked onclick="switchView('list')">
                        <label class="btn btn-outline-primary" for="list-view">
                            <i class="bi bi-list-ul me-2"></i>List
                        </label>
                        <input type="radio" class="btn-check" name="view" id="calendar-view" onclick="switchView('calendar')">
                        <label class="btn btn-outline-primary" for="calendar-view">
                            <i class="bi bi-calendar3 me-2"></i>Calendar
                        </label>
                    </div>
                    <div>
                        <button class="btn btn-outline-secondary btn-sm" onclick="clearFilters()">
                            <i class="bi bi-x-circle me-2"></i>Clear Filters
                        </button>
                    </div>
                </div>

                <!-- List View -->
                <div id="list-view-container">
                    <div id="defenses-container">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted mt-2">Loading defenses...</p>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center" id="pagination">
                            <!-- Pagination will be populated by JavaScript -->
                        </ul>
                    </nav>
                </div>

                <!-- Calendar View -->
                <div id="calendar-view-container" class="d-none">
                    <div class="row">
                        <div class="col-md-8">
                            <div id="mini-calendar" class="border rounded p-3">
                                <!-- Mini calendar will be generated here -->
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Upcoming This Week</h6>
                                </div>
                                <div class="card-body" id="upcoming-this-week">
                                    <!-- Upcoming defenses will be listed here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Defense Details Modal -->
<div class="modal fade" id="defenseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Defense Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="defense-details">
                <!-- Details will be loaded here -->
            </div>
            <div class="modal-footer" id="defense-actions">
                <!-- Actions will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Reschedule Modal -->
<div class="modal fade" id="rescheduleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reschedule Defense</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="rescheduleForm">
                    <input type="hidden" id="reschedule-defense-id">
                    <div class="mb-3">
                        <label for="new-date" class="form-label">New Date</label>
                        <input type="date" class="form-control" id="new-date" required>
                    </div>
                    <div class="mb-3">
                        <label for="new-time" class="form-label">New Time</label>
                        <input type="time" class="form-control" id="new-time" required>
                    </div>
                    <div class="mb-3">
                        <label for="new-room" class="form-label">Room</label>
                        <select class="form-select" id="new-room" required>
                            <option value="">Select Room</option>
                            <!-- Rooms will be populated -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="reschedule-reason" class="form-label">Reason for Rescheduling</label>
                        <textarea class="form-control" id="reschedule-reason" rows="3" required
                                  placeholder="Please provide a reason for rescheduling..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" onclick="submitReschedule()">Reschedule Defense</button>
            </div>
        </div>
    </div>
</div>

<!-- Grading Modal -->
<div class="modal fade" id="gradingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Grade Defense</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="gradingForm">
                    <input type="hidden" id="grading-defense-id">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="final-grade" class="form-label">Final Grade</label>
                            <input type="number" class="form-control" id="final-grade" min="0" max="20" step="0.5" required>
                        </div>
                        <div class="col-md-6">
                            <label for="grade-scale" class="form-label">Grade Scale</label>
                            <select class="form-select" id="grade-scale">
                                <option value="20">Out of 20</option>
                                <option value="100">Out of 100</option>
                            </select>
                        </div>
                    </div>

                    <!-- Jury Individual Grades -->
                    <div class="mb-3">
                        <h6>Individual Jury Evaluations</h6>
                        <div id="jury-grades">
                            <!-- Jury grades will be populated here -->
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="defense-comments" class="form-label">Comments & Feedback</label>
                        <textarea class="form-control" id="defense-comments" rows="4"
                                  placeholder="Provide detailed feedback on the defense performance..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="recommendations" class="form-label">Recommendations</label>
                        <textarea class="form-control" id="recommendations" rows="3"
                                  placeholder="Any recommendations for future work or improvements..."></textarea>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="generate-report">
                            <label class="form-check-label" for="generate-report">
                                Generate defense report (PV de soutenance)
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="submitGrade()">Submit Grade</button>
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
    let currentView = 'list';

    loadDefenses();
    loadDefenseStats();
    loadRooms();
    setupFilters();

    function loadDefenses(page = 1) {
        currentPage = page;
        const params = new URLSearchParams({
            page: page,
            ...currentFilters
        });

        axios.get(`/api/defenses?${params}`)
            .then(response => {
                if (currentView === 'list') {
                    renderDefensesList(response.data.data);
                    renderPagination(response.data);
                } else {
                    renderCalendarView(response.data.data);
                }
            })
            .catch(error => {
                document.getElementById('defenses-container').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-circle me-2"></i>
                        Error loading defenses. Please try again.
                    </div>
                `;
            });
    }

    function loadDefenseStats() {
        axios.get('/api/reports/dashboard-stats')
            .then(response => {
                const data = response.data.data;
                document.getElementById('total-defenses').textContent = data.total_defenses || 0;
                document.getElementById('scheduled-defenses').textContent = data.scheduled_defenses || 0;
                document.getElementById('today-defenses').textContent = data.today_defenses || 0;
                document.getElementById('completed-defenses').textContent = data.completed_defenses || 0;
            })
            .catch(error => {
                console.log('Could not load defense statistics');
            });
    }

    function loadRooms() {
        // Mock rooms data - would come from API in real implementation
        const rooms = ['Amphitheater A', 'Room 101', 'Room 102', 'Conference Room', 'Lab 1', 'Lab 2'];
        const roomFilter = document.getElementById('roomFilter');
        const newRoomSelect = document.getElementById('new-room');

        rooms.forEach(room => {
            const option1 = new Option(room, room);
            const option2 = new Option(room, room);
            roomFilter.appendChild(option1);
            if (newRoomSelect) newRoomSelect.appendChild(option2);
        });
    }

    function renderDefensesList(defenses) {
        const container = document.getElementById('defenses-container');

        if (defenses.length === 0) {
            container.innerHTML = `
                <div class="text-center py-5">
                    <i class="bi bi-shield-check text-muted" style="font-size: 4rem;"></i>
                    <h5 class="text-muted mt-3">No defenses found</h5>
                    <p class="text-muted">Try adjusting your filters or schedule a new defense.</p>
                </div>
            `;
            return;
        }

        container.innerHTML = defenses.map(defense => `
            <div class="card mb-3 defense-card" data-defense-id="${defense.id}">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="card-title mb-2">
                                        <a href="#" onclick="viewDefense(${defense.id})" class="text-decoration-none">
                                            ${defense.project.title || 'Project Defense'}
                                        </a>
                                    </h6>
                                    <div class="d-flex align-items-center gap-3 mb-2">
                                        <small class="text-muted">
                                            <i class="bi bi-people me-1"></i>
                                            ${defense.project.team.name}
                                        </small>
                                        <small class="text-muted">
                                            <i class="bi bi-calendar me-1"></i>
                                            ${formatDateTime(defense.defense_date)}
                                        </small>
                                        <small class="text-muted">
                                            <i class="bi bi-geo-alt me-1"></i>
                                            ${defense.room ? defense.room.name : 'TBD'}
                                        </small>
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            ${defense.duration || 60} min
                                        </small>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <strong class="text-muted">Jury:</strong>
                                        ${defense.jury_members ? defense.jury_members.map(jury => `
                                            <span class="badge bg-light text-dark">
                                                ${jury.teacher.name}
                                                ${jury.role === 'president' ? '<i class="bi bi-star-fill text-warning ms-1"></i>' : ''}
                                            </span>
                                        `).join('') : '<span class="text-muted">Not assigned</span>'}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="mb-2">
                                <span class="badge status-${defense.status} mb-2">
                                    ${defense.status.replace('_', ' ').toUpperCase()}
                                </span>
                                ${isUpcoming(defense.defense_date) ? '<span class="badge bg-info ms-1">Upcoming</span>' : ''}
                                ${isToday(defense.defense_date) ? '<span class="badge bg-warning text-dark ms-1">Today</span>' : ''}
                            </div>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-outline-primary" onclick="viewDefense(${defense.id})" title="View Details">
                                    <i class="bi bi-eye"></i>
                                </button>
                                ${canReschedule(defense) ? `
                                    <button class="btn btn-sm btn-outline-warning" onclick="rescheduleDefense(${defense.id})" title="Reschedule">
                                        <i class="bi bi-calendar-x"></i>
                                    </button>
                                ` : ''}
                                ${canGrade(defense) ? `
                                    <button class="btn btn-sm btn-outline-success" onclick="gradeDefense(${defense.id})" title="Grade">
                                        <i class="bi bi-award"></i>
                                    </button>
                                ` : ''}
                                ${defense.status === 'completed' && defense.final_grade ? `
                                    <a href="/defenses/${defense.id}/report" class="btn btn-sm btn-outline-info" title="Download Report">
                                        <i class="bi bi-download"></i>
                                    </a>
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
                    <a class="page-link" href="#" onclick="loadDefenses(${paginationData.current_page - 1})">Previous</a>
                </li>
            `;
        }

        // Page numbers
        for (let i = 1; i <= paginationData.last_page; i++) {
            if (i === paginationData.current_page) {
                paginationHtml += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
            } else if (i === 1 || i === paginationData.last_page || Math.abs(i - paginationData.current_page) <= 2) {
                paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="loadDefenses(${i})">${i}</a></li>`;
            } else if (i === paginationData.current_page - 3 || i === paginationData.current_page + 3) {
                paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }

        // Next button
        if (paginationData.current_page < paginationData.last_page) {
            paginationHtml += `
                <li class="page-item">
                    <a class="page-link" href="#" onclick="loadDefenses(${paginationData.current_page + 1})">Next</a>
                </li>
            `;
        }

        pagination.innerHTML = paginationHtml;
    }

    function renderCalendarView(defenses) {
        // Simple calendar view implementation
        const container = document.getElementById('mini-calendar');
        const upcomingContainer = document.getElementById('upcoming-this-week');

        // Generate a simple monthly calendar
        const now = new Date();
        const year = now.getFullYear();
        const month = now.getMonth();

        container.innerHTML = `
            <h6 class="text-center mb-3">${new Date(year, month).toLocaleDateString('en-US', {month: 'long', year: 'numeric'})}</h6>
            <div class="calendar-grid">
                <!-- Calendar implementation would go here -->
                <p class="text-center text-muted">Calendar view is under development</p>
                <p class="text-center">
                    <a href="/defenses/calendar" class="btn btn-primary">View Full Calendar</a>
                </p>
            </div>
        `;

        // Show upcoming defenses this week
        const thisWeekDefenses = defenses.filter(defense => {
            const defenseDate = new Date(defense.defense_date);
            const weekStart = new Date(now);
            weekStart.setDate(now.getDate() - now.getDay());
            const weekEnd = new Date(weekStart);
            weekEnd.setDate(weekStart.getDate() + 6);
            return defenseDate >= weekStart && defenseDate <= weekEnd;
        });

        if (thisWeekDefenses.length > 0) {
            upcomingContainer.innerHTML = thisWeekDefenses.map(defense => `
                <div class="d-flex align-items-center mb-2 p-2 bg-light rounded">
                    <div class="flex-grow-1">
                        <h6 class="mb-0">${defense.project.title || 'Defense'}</h6>
                        <small class="text-muted">${formatDateTime(defense.defense_date)}</small>
                    </div>
                    <span class="badge status-${defense.status}">${defense.status}</span>
                </div>
            `).join('');
        } else {
            upcomingContainer.innerHTML = `
                <p class="text-muted text-center">No defenses scheduled this week</p>
            `;
        }
    }

    function setupFilters() {
        ['statusFilter', 'dateFilter', 'roomFilter', 'searchInput'].forEach(filterId => {
            const element = document.getElementById(filterId);
            if (element) {
                element.addEventListener('change', applyFilters);
                element.addEventListener('input', applyFilters);
            }
        });
    }

    function applyFilters() {
        const status = document.getElementById('statusFilter').value;
        const date = document.getElementById('dateFilter').value;
        const room = document.getElementById('roomFilter').value;
        const search = document.getElementById('searchInput').value;

        currentFilters = {};
        if (status) currentFilters.status = status;
        if (date) currentFilters.date = date;
        if (room) currentFilters.room = room;
        if (search) currentFilters.search = search;

        loadDefenses(1);
    }

    window.clearFilters = function() {
        document.getElementById('statusFilter').value = '';
        document.getElementById('dateFilter').value = '';
        document.getElementById('roomFilter').value = '';
        document.getElementById('searchInput').value = '';
        currentFilters = {};
        loadDefenses(1);
    };

    window.switchView = function(view) {
        currentView = view;

        if (view === 'list') {
            document.getElementById('list-view-container').classList.remove('d-none');
            document.getElementById('calendar-view-container').classList.add('d-none');
            document.getElementById('list-view').checked = true;
            loadDefenses(currentPage);
        } else {
            document.getElementById('list-view-container').classList.add('d-none');
            document.getElementById('calendar-view-container').classList.remove('d-none');
            document.getElementById('calendar-view').checked = true;
            loadDefenses(1);
        }
    };

    window.viewDefense = async function(defenseId) {
        try {
            const response = await axios.get(`/api/defenses/${defenseId}`);
            const defense = response.data.data;

            document.getElementById('defense-details').innerHTML = `
                <div class="row">
                    <div class="col-md-8">
                        <h6 class="text-primary">${defense.project.title || 'Project Defense'}</h6>
                        <div class="mb-3">
                            <strong>Team:</strong> ${defense.project.team.name}
                            <br><strong>Project Description:</strong>
                            <p class="text-muted mt-1">${defense.project.description || 'No description available'}</p>
                        </div>

                        <div class="mb-3">
                            <strong>Defense Schedule:</strong>
                            <div class="mt-2 p-3 bg-light rounded">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <i class="bi bi-calendar me-2"></i>${formatDateTime(defense.defense_date)}
                                    </div>
                                    <div class="col-sm-6">
                                        <i class="bi bi-geo-alt me-2"></i>${defense.room ? defense.room.name : 'Room TBD'}
                                    </div>
                                    <div class="col-sm-6">
                                        <i class="bi bi-clock me-2"></i>${defense.duration || 60} minutes
                                    </div>
                                    <div class="col-sm-6">
                                        <i class="bi bi-shield-check me-2"></i>${defense.status.replace('_', ' ').toUpperCase()}
                                    </div>
                                </div>
                            </div>
                        </div>

                        ${defense.jury_members && defense.jury_members.length > 0 ? `
                            <div class="mb-3">
                                <strong>Jury Members:</strong>
                                <div class="mt-2">
                                    ${defense.jury_members.map(jury => `
                                        <div class="d-flex align-items-center mb-2 p-2 bg-light rounded">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">${jury.teacher.name}</h6>
                                                <small class="text-muted">${jury.teacher.email}</small>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <span class="badge ${jury.role === 'president' ? 'bg-warning' : 'bg-secondary'}">
                                                    ${jury.role.charAt(0).toUpperCase() + jury.role.slice(1)}
                                                </span>
                                            </div>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                        ` : ''}

                        ${defense.final_grade ? `
                            <div class="mb-3">
                                <strong>Final Grade:</strong>
                                <span class="badge bg-success fs-6 ms-2">${defense.final_grade}/20</span>
                            </div>
                        ` : ''}

                        ${defense.comments ? `
                            <div class="mb-3">
                                <strong>Comments:</strong>
                                <p class="text-muted mt-1">${defense.comments}</p>
                            </div>
                        ` : ''}
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Defense Information</h6>
                                <p class="mb-2"><strong>Status:</strong> <span class="badge status-${defense.status}">${defense.status.replace('_', ' ').toUpperCase()}</span></p>
                                <p class="mb-2"><strong>Scheduled:</strong> ${formatDate(defense.created_at)}</p>
                                ${defense.final_grade ? `<p class="mb-2"><strong>Grade:</strong> ${defense.final_grade}/20</p>` : ''}
                                ${defense.project.supervisor ? `<p class="mb-0"><strong>Supervisor:</strong> ${defense.project.supervisor.name}</p>` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Set up action buttons
            const actionsContainer = document.getElementById('defense-actions');
            let actionsHtml = '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>';

            if (canReschedule(defense)) {
                actionsHtml += ` <button class="btn btn-warning" onclick="rescheduleDefense(${defense.id})">Reschedule</button>`;
            }

            if (canGrade(defense)) {
                actionsHtml += ` <button class="btn btn-success" onclick="gradeDefense(${defense.id})">Grade Defense</button>`;
            }

            if (defense.status === 'completed' && defense.final_grade) {
                actionsHtml += ` <a href="/defenses/${defense.id}/report" class="btn btn-info">Download Report</a>`;
            }

            actionsContainer.innerHTML = actionsHtml;

            const modal = new bootstrap.Modal(document.getElementById('defenseModal'));
            modal.show();
        } catch (error) {
            alert('Error loading defense details');
        }
    };

    window.rescheduleDefense = function(defenseId) {
        document.getElementById('reschedule-defense-id').value = defenseId;
        document.getElementById('rescheduleForm').reset();

        const modal = new bootstrap.Modal(document.getElementById('rescheduleModal'));
        modal.show();
    };

    window.submitReschedule = async function() {
        const defenseId = document.getElementById('reschedule-defense-id').value;
        const newDate = document.getElementById('new-date').value;
        const newTime = document.getElementById('new-time').value;
        const newRoom = document.getElementById('new-room').value;
        const reason = document.getElementById('reschedule-reason').value;

        if (!newDate || !newTime || !newRoom || !reason) {
            alert('Please fill in all required fields');
            return;
        }

        try {
            await axios.post(`/api/defenses/${defenseId}/reschedule`, {
                defense_date: `${newDate} ${newTime}`,
                room_id: newRoom,
                reason: reason
            });

            bootstrap.Modal.getInstance(document.getElementById('rescheduleModal')).hide();
            loadDefenses(currentPage);
            alert('Defense rescheduled successfully');
        } catch (error) {
            alert('Error rescheduling defense: ' + (error.response?.data?.message || 'Unknown error'));
        }
    };

    window.gradeDefense = async function(defenseId) {
        document.getElementById('grading-defense-id').value = defenseId;
        document.getElementById('gradingForm').reset();

        // Load jury members for individual grading
        try {
            const response = await axios.get(`/api/defenses/${defenseId}`);
            const defense = response.data.data;

            if (defense.jury_members && defense.jury_members.length > 0) {
                const juryGradesContainer = document.getElementById('jury-grades');
                juryGradesContainer.innerHTML = defense.jury_members.map(jury => `
                    <div class="row mb-2">
                        <div class="col-md-8">
                            <label class="form-label">${jury.teacher.name} (${jury.role})</label>
                        </div>
                        <div class="col-md-4">
                            <input type="number" class="form-control" name="jury_grade_${jury.id}" min="0" max="20" step="0.5" placeholder="Grade">
                        </div>
                    </div>
                `).join('');
            }

            const modal = new bootstrap.Modal(document.getElementById('gradingModal'));
            modal.show();
        } catch (error) {
            alert('Error loading defense details for grading');
        }
    };

    window.submitGrade = async function() {
        const defenseId = document.getElementById('grading-defense-id').value;
        const finalGrade = document.getElementById('final-grade').value;
        const comments = document.getElementById('defense-comments').value;
        const recommendations = document.getElementById('recommendations').value;
        const generateReport = document.getElementById('generate-report').checked;

        if (!finalGrade) {
            alert('Please enter a final grade');
            return;
        }

        try {
            await axios.post(`/api/defenses/${defenseId}/grade`, {
                final_grade: finalGrade,
                comments: comments,
                recommendations: recommendations,
                generate_report: generateReport
            });

            bootstrap.Modal.getInstance(document.getElementById('gradingModal')).hide();
            loadDefenses(currentPage);
            alert('Defense graded successfully');
        } catch (error) {
            alert('Error grading defense: ' + (error.response?.data?.message || 'Unknown error'));
        }
    };

    function canReschedule(defense) {
        const userRole = '{{ auth()->user()?->role }}';
        const defenseDate = new Date(defense.defense_date);
        const now = new Date();

        return ['department_head', 'admin'].includes(userRole) &&
               defense.status === 'scheduled' &&
               defenseDate > now;
    }

    function canGrade(defense) {
        const userRole = '{{ auth()->user()?->role }}';
        const userId = {{ auth()->id() }};

        if (['department_head', 'admin'].includes(userRole)) return true;

        // Jury members can grade
        return defense.jury_members && defense.jury_members.some(jury =>
            jury.teacher_id === userId && defense.status === 'completed'
        );
    }

    function isUpcoming(dateString) {
        const defenseDate = new Date(dateString);
        const now = new Date();
        const threeDaysFromNow = new Date(now.getTime() + (3 * 24 * 60 * 60 * 1000));
        return defenseDate > now && defenseDate <= threeDaysFromNow;
    }

    function isToday(dateString) {
        const defenseDate = new Date(dateString);
        const today = new Date();
        return defenseDate.toDateString() === today.toDateString();
    }

    function formatDateTime(dateString) {
        return new Date(dateString).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
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
.defense-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.calendar-grid {
    min-height: 300px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.badge {
    font-size: 0.75rem;
}

.card.bg-light {
    border-left: 4px solid var(--bs-primary);
}

.btn-group .btn {
    border-radius: 0.375rem !important;
    margin-right: 2px;
}
</style>
@endpush