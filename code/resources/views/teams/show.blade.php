@extends('layouts.pfe-app')

@section('page-title', __('app.team_details'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">{{ $team->name }}</h4>
                    <div>
                        <span class="badge bg-{{ $team->status === 'active' ? 'success' : ($team->status === 'forming' ? 'warning' : 'secondary') }}">
                            {{ ucfirst($team->status) }}
                        </span>
                        @if($isLeader)
                            <a href="{{ route('teams.edit', $team) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> {{ __('app.edit_team') }}
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <!-- Team Members -->
                            <div class="mb-4">
                                <h5>{{ __('app.team_members') }} ({{ $team->members->count() }}/4)</h5>
                                <div class="row">
                                    @foreach($team->members as $member)
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-2 {{ $member->role === 'leader' ? 'border-primary' : 'border-light' }}">
                                                <div class="card-body p-3">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <h6 class="mb-1">{{ $member->user->name }}</h6>
                                                            <small class="text-muted">{{ $member->user->email }}</small>
                                                            <div>
                                                                <span class="badge bg-{{ $member->role === 'leader' ? 'primary' : 'secondary' }}">
                                                                    {{ ucfirst($member->role) }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        @if($isLeader && $member->role !== 'leader')
                                                            <div class="dropdown">
                                                                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                                                    <i class="fas fa-ellipsis-v"></i>
                                                                </button>
                                                                <ul class="dropdown-menu">
                                                                    <li>
                                                                        <button class="dropdown-item" onclick="transferLeadership({{ $member->user->id }}, '{{ $member->user->name }}')">
                                                                            Make Leader
                                                                        </button>
                                                                    </li>
                                                                    <li><hr class="dropdown-divider"></li>
                                                                    <li>
                                                                        <form action="{{ route('teams.remove-member', [$team, $member]) }}" method="POST" class="d-inline">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit" class="dropdown-item text-danger"
                                                                                    onclick="return showDeleteConfirmation({
                                                                                        itemName: '{{ $member->name }}',
                                                                                        message: '{{ __('app.confirm_remove_member') }}',
                                                                                        form: this.closest('form')
                                                                                    })">
                                                                                Remove from Team
                                                                            </button>
                                                                        </form>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <small class="text-muted">
                                                        Joined {{ $member->joined_at->format('M d, Y') }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                @if($isLeader && $team->members->count() < 4)
                                    <div class="mt-3">
                                        <h6>{{ __('app.invite_team_member') }}</h6>
                                        <p class="text-muted small">{{ __('app.invitation_will_be_sent_explain') }}</p>
                                        <form action="{{ route('teams.add-member', $team) }}" method="POST" class="row g-2" id="invite-student-form">
                                            @csrf
                                            <div class="col-md-8">
                                                <div class="position-relative">
                                                    <input type="email" class="form-control" name="student_email" id="student-search-input"
                                                           placeholder="{{ __('app.search_student_name_email') }}" required autocomplete="off">
                                                    <input type="hidden" name="student_id" id="selected-student-id">
                                                    <div id="student-dropdown" class="dropdown-menu w-100" style="display: none; max-height: 300px; overflow-y: auto;">
                                                        <!-- Student options will be populated here -->
                                                    </div>
                                                    <div id="search-loading" class="position-absolute" style="right: 10px; top: 50%; transform: translateY(-50%); display: none;">
                                                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                            <span class="visually-hidden">{{ __('app.loading') }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <small class="text-muted">{{ __('app.search_by_name_or_email') }}</small>
                                            </div>
                                            <div class="col-md-4">
                                                <button type="submit" class="btn btn-success" id="invite-btn">
                                                    <i class="fas fa-envelope"></i> {{ __('app.send_invitation') }}
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                            </div>

                            <!-- Project Information -->
                            @if($team->project)
                                <div class="mb-4">
                                    <h5>Project</h5>
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $team->project->subject->title }}</h6>
                                            <p class="card-text">{{ $team->project->subject->description }}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small class="text-muted">Supervisor:</small>
                                                    <div>{{ $team->project->supervisor->name ?? 'Not assigned' }}</div>
                                                </div>
                                                <a href="{{ route('projects.show', $team->project) }}" class="btn btn-primary btn-sm">
                                                    View Project
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="mb-4">
                                    <h5>{{ __('app.subject_selection') }}</h5>
                                    @if($team->status === 'forming')
                                        <div class="alert alert-info">
                                            {{ __('app.complete_team_formation_before_selecting_subject') }}
                                        </div>
                                    @elseif($isLeader)
                                        <div class="row">
                                            <div class="col-12">
                                                @php
                                                    $currentDeadline = App\Models\AllocationDeadline::active()->first();
                                                    $canSelectSubjects = $currentDeadline && $currentDeadline->canStudentsChoose() && $team->canSelectSubject();
                                                @endphp

                                                @if($canSelectSubjects)
                                                    <div class="alert alert-success mb-3">
                                                        <i class="fas fa-check-circle"></i>
                                                        <strong>Ready to Select!</strong> Your team can now choose a subject.
                                                        <small class="d-block">Deadline: {{ $currentDeadline->preferences_deadline->format('M d, Y H:i') }}</small>
                                                    </div>
                                                @else
                                                    @if(!$currentDeadline || !$currentDeadline->canStudentsChoose())
                                                        <div class="alert alert-warning mb-3">
                                                            <i class="fas fa-clock"></i>
                                                            Subject selection period is not active.
                                                        </div>
                                                    @elseif(!$team->canSelectSubject())
                                                        @php
                                                            // Get team leader's academic level to determine appropriate team size limits
                                                            $leader = $team->members->where('role', 'leader')->first();
                                                            $academicLevel = 'licence'; // default

                                                            if ($leader && $leader->student) {
                                                                $academicLevel = match($leader->student->student_level) {
                                                                    'licence_3' => 'licence',
                                                                    'master_1', 'master_2' => 'master',
                                                                    default => 'licence'
                                                                };
                                                            }

                                                            $minSize = config("team.sizes.{$academicLevel}.min", 1);
                                                            $maxSize = config("team.sizes.{$academicLevel}.max", 4);
                                                            $currentSize = $team->members->count();
                                                        @endphp
                                                        <div class="alert alert-warning mb-3">
                                                            <i class="fas fa-users"></i>
                                                            <strong>Cannot select subjects yet.</strong><br>
                                                            @if($currentSize < $minSize)
                                                                Your team needs {{ $minSize }}-{{ $maxSize }} members to select subjects.
                                                                (Current: {{ $currentSize }} members)
                                                            @elseif($currentSize > $maxSize)
                                                                Your team has too many members ({{ $currentSize }}/{{ $maxSize }} max).
                                                            @elseif($team->subject_id)
                                                                Your team already has a subject assigned.
                                                            @else
                                                                @php
                                                                    $debugInfo = $team->getSubjectSelectionDebugInfo();
                                                                @endphp
                                                                Team cannot select subjects. Debug info:<br>
                                                                <small>
                                                                    • Status: {{ $debugInfo['status'] }}<br>
                                                                    • Is Complete: {{ $debugInfo['isComplete'] ? 'Yes' : 'No' }}<br>
                                                                    • Has Valid Status: {{ $debugInfo['hasValidStatus'] ? 'Yes' : 'No' }}<br>
                                                                    • Has Subject: {{ $debugInfo['hasSubject'] ? 'Yes' : 'No' }}<br>
                                                                    • Member Count: {{ $debugInfo['memberCount'] }}<br>
                                                                    • Can Select: {{ $debugInfo['canSelect'] ? 'Yes' : 'No' }}
                                                                </small>
                                                            @endif
                                                        </div>
                                                    @endif
                                                @endif

                                                <div class="d-flex gap-2 mb-3 flex-wrap">
                                                    @if($canSelectSubjects)
                                                        <a href="{{ route('teams.select-subject-form', $team) }}" class="btn btn-success">
                                                            <i class="fas fa-book"></i> {{ __('app.select_subject') }}
                                                        </a>
                                                    @endif
                                                    <a href="{{ route('teams.subject-preferences', $team) }}" class="btn btn-primary">
                                                        <i class="fas fa-list-ol"></i> {{ __('app.manage_subject_preferences_max') }}
                                                    </a>
                                                    <a href="{{ route('teams.subject-requests', $team) }}" class="btn btn-outline-success">
                                                        <i class="fas fa-hand-paper"></i> {{ __('app.subject_requests') }}
                                                    </a>
                                                    @if($canSelectSubjects)
                                                        <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#requestSubjectModal">
                                                            <i class="fas fa-paper-plane"></i> {{ __('app.request_subject') }}
                                                        </button>
                                                    @endif
                                                    <a href="{{ route('teams.external-project-form', $team) }}" class="btn btn-outline-secondary">
                                                        <i class="fas fa-external-link-alt"></i> Submit External Project
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="alert alert-warning">
                                            Only the team leader can select a subject.
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">{{ __('app.team_information') }}</h6>

                                    <div class="mb-3">
                                        <small class="text-muted">Status</small>
                                        <div>
                                            <span class="badge bg-{{ $team->status === 'active' ? 'success' : ($team->status === 'forming' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($team->status) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <small class="text-muted">Created</small>
                                        <div>{{ $team->created_at->format('M d, Y') }}</div>
                                    </div>

                                    <div class="mb-3">
                                        <small class="text-muted">Members</small>
                                        <div>{{ $team->members->count() }}/4</div>
                                        <div class="progress mt-1" style="height: 6px;">
                                            <div class="progress-bar" style="width: {{ ($team->members->count() / 4) * 100 }}%"></div>
                                        </div>
                                    </div>

                                    @if($team->project)
                                        <div class="mb-3">
                                            <small class="text-muted">Project Status</small>
                                            <div>
                                                <span class="badge bg-info">{{ ucfirst($team->project->status) }}</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            @if(!$isMember && auth()->user()->role === 'student' && !auth()->user()->teamMember && $team->members->count() < 4)
                                <div class="mt-3">
                                    <div class="card border-success">
                                        <div class="card-body text-center">
                                            <h6 class="text-success">Join This Team</h6>
                                            <p class="small text-muted">This team has space for more members.</p>
                                            <form action="{{ route('teams.join', $team) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fas fa-user-plus"></i> Join Team
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($isMember && !$isLeader)
                                <div class="mt-3">
                                    <div class="card border-warning">
                                        <div class="card-body text-center">
                                            <h6 class="text-warning">Leave Team</h6>
                                            <p class="small text-muted">Remove yourself from this team.</p>
                                            <form action="{{ route('teams.leave', $team) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-warning btn-sm"
                                                        onclick="return showConfirmation({
                                                            title: '{{ __('app.confirm_leave') }}',
                                                            message: '{{ __('app.confirm_leave_team') }}',
                                                            confirmText: '{{ __('app.leave') }}',
                                                            confirmClass: 'btn-warning',
                                                            form: this.closest('form')
                                                        })">
                                                    <i class="fas fa-sign-out-alt"></i> Leave Team
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($isLeader && $team->members->count() === 1)
                                <div class="mt-3">
                                    <div class="card border-danger">
                                        <div class="card-body text-center">
                                            <h6 class="text-danger">Delete Team</h6>
                                            <p class="small text-muted">Permanently delete this team since you're the only member.</p>
                                            <form action="{{ route('teams.destroy', $team) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return showDeleteConfirmation({
                                                            itemName: '{{ $team->name }}',
                                                            message: '{{ __('app.confirm_delete_team') }}',
                                                            form: this.closest('form')
                                                        })">
                                                    <i class="fas fa-trash"></i> Delete Team
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Select Subject Modal -->
@if($isLeader && !$team->project && ($availableSubjects ?? collect())->count() > 0)
<div class="modal fade" id="selectSubjectModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select Subject</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    @foreach($availableSubjects as $subject)
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 subject-card" data-subject-id="{{ $subject->id }}">
                                <div class="card-body">
                                    <h6 class="card-title">{{ $subject->title }}</h6>
                                    <p class="card-text text-truncate-2">{{ Str::limit($subject->description, 100) }}</p>
                                    <div class="mb-2">
                                        <small class="text-muted">Teacher:</small>
                                        <div>{{ $subject->teacher->name }}</div>
                                    </div>
                                    <div class="d-flex flex-wrap">
                                        @foreach(array_slice(explode(',', $subject->keywords), 0, 3) as $keyword)
                                            <span class="badge bg-secondary me-1 mb-1">{{ trim($keyword) }}</span>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="button" class="btn btn-success btn-sm w-100"
                                            onclick="selectSubject({{ $subject->id }}, '{{ $subject->title }}')">
                                        Select This Subject
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Request Subject Modal -->
@if($isLeader && !$team->project)
<div class="modal fade" id="requestSubjectModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Request Subject for Team</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('teams.request-subject', $team) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="subject_id" class="form-label">Select Subject</label>
                        @if($availableSubjects && $availableSubjects->count() > 0)
                            <select name="subject_id" id="subject_id" class="form-select" required>
                                <option value="">Choose a subject...</option>
                                @foreach($availableSubjects as $subject)
                                    <option value="{{ $subject->id }}">
                                        {{ $subject->title }} - {{ $subject->teacher->name }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                No subjects are currently available for request. All subjects may already be assigned or there are no validated subjects.
                            </div>
                            <select name="subject_id" id="subject_id" class="form-select" disabled>
                                <option value="">No subjects available</option>
                            </select>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="request_message" class="form-label">Request Message (Optional)</label>
                        <textarea name="request_message" id="request_message" class="form-control" rows="4"
                                  placeholder="{{ __('app.why_team_want_subject_optional') }}"></textarea>
                        <small class="form-text text-muted">Explain why your team is interested in this subject and how it aligns with your goals.</small>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Note:</strong> Subject requests need admin approval. You'll be notified when your request is processed.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning" @if(!$availableSubjects || $availableSubjects->count() == 0) disabled @endif>
                        <i class="fas fa-paper-plane"></i> Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Transfer Leadership Modal -->
<div class="modal fade" id="transferLeadershipModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="transferLeadershipForm" method="POST" action="{{ route('teams.transfer-leadership', $team) }}">
                @csrf
                <input type="hidden" name="new_leader_id" id="newLeaderId">
                <div class="modal-header">
                    <h5 class="modal-title">Transfer Leadership</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to transfer team leadership to <strong id="newLeaderName"></strong>?</p>
                    <div class="alert alert-warning">
                        <strong>Note:</strong> You will no longer be the team leader and won't be able to manage team settings.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-exchange-alt"></i> Transfer Leadership
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.subject-card {
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
}

.subject-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.text-truncate-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
@endpush

@push('scripts')
<script>
function selectSubject(subjectId, subjectTitle) {
    showConfirmation({
        title: '{{ __('app.confirm_subject_selection') }}',
        message: `{{ __('app.confirm_select_subject_message') }}`.replace(':subject', subjectTitle),
        confirmText: '{{ __('app.select') }}',
        confirmClass: 'btn-primary',
        onConfirm: function() {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("teams.select-subject", $team) }}';

            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';

            const subjectInput = document.createElement('input');
            subjectInput.type = 'hidden';
            subjectInput.name = 'subject_id';
            subjectInput.value = subjectId;

            form.appendChild(csrfToken);
            form.appendChild(subjectInput);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function transferLeadership(userId, userName) {
    document.getElementById('newLeaderId').value = userId;
    document.getElementById('newLeaderName').textContent = userName;

    const modal = new bootstrap.Modal(document.getElementById('transferLeadershipModal'));
    modal.show();
}

// Student search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('student-search-input');
    const dropdown = document.getElementById('student-dropdown');
    const loadingSpinner = document.getElementById('search-loading');
    const selectedStudentId = document.getElementById('selected-student-id');
    const inviteForm = document.getElementById('invite-student-form');

    if (!searchInput) return;

    let searchTimeout;

    searchInput.addEventListener('input', function() {
        const query = this.value.trim();

        clearTimeout(searchTimeout);

        if (query.length < 2) {
            dropdown.style.display = 'none';
            return;
        }

        loadingSpinner.style.display = 'block';

        searchTimeout = setTimeout(() => {
            fetch(`{{ route('teams.search-students') }}?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(students => {
                    loadingSpinner.style.display = 'none';

                    if (students.length === 0) {
                        dropdown.innerHTML = '<div class="dropdown-item text-muted">{{ __('app.no_students_found') }}</div>';
                    } else {
                        dropdown.innerHTML = students.map(student => `
                            <div class="dropdown-item student-option"
                                 data-student-id="${student.id}"
                                 data-student-email="${student.email}"
                                 style="cursor: pointer;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>${student.name}</strong>
                                        <br>
                                        <small class="text-muted">${student.email}</small>
                                        ${student.student_id ? `<br><small class="text-info">{{ __('app.id') }}: ${student.student_id}</small>` : ''}
                                    </div>
                                    <i class="fas fa-plus text-success"></i>
                                </div>
                            </div>
                        `).join('');

                        // Add click handlers for student options
                        dropdown.querySelectorAll('.student-option').forEach(option => {
                            option.addEventListener('click', function() {
                                const studentId = this.dataset.studentId;
                                const studentEmail = this.dataset.studentEmail;
                                const studentName = this.querySelector('strong').textContent;

                                searchInput.value = studentEmail;
                                selectedStudentId.value = studentId;
                                dropdown.style.display = 'none';

                                // Show selected student info
                                searchInput.style.backgroundColor = '#e8f5e8';
                                searchInput.title = `{{ __('app.selected') }}: ${studentName}`;
                            });
                        });
                    }

                    dropdown.style.display = 'block';
                })
                .catch(error => {
                    console.error('Search error:', error);
                    loadingSpinner.style.display = 'none';
                    dropdown.innerHTML = '<div class="dropdown-item text-danger">{{ __('app.search_error') }}</div>';
                    dropdown.style.display = 'block';
                });
        }, 300);
    });

    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });

    // Clear selection when input changes manually
    searchInput.addEventListener('keydown', function() {
        selectedStudentId.value = '';
        searchInput.style.backgroundColor = '';
        searchInput.title = '';
    });

    // Form submission validation
    inviteForm.addEventListener('submit', function(e) {
        const email = searchInput.value.trim();

        if (!email) {
            e.preventDefault();
            alert('{{ __('app.please_select_student') }}');
            return;
        }

        // Validate email format
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            e.preventDefault();
            alert('{{ __('app.please_enter_valid_email') }}');
            return;
        }
    });
});
</script>
@endpush