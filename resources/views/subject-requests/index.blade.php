@extends('layouts.pfe-app')

@section('page-title', __('app.subject_requests'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-paper-plane"></i> {{ __('app.subject_requests') }}
                    </h4>
                    <p class="text-muted mb-0">
                        @auth
                            @if(auth()->user()->role === 'student')
                                {{ __('app.your_team_subject_requests_ordered') }}
                            @elseif(auth()->user()->role === 'teacher')
                                {{ __('app.subject_requests_for_supervised') }}
                            @else
                                {{ __('app.all_subject_requests_system') }}
                            @endif
                        @endauth
                    </p>
                </div>
                <div class="card-body">
                    @if($subjectRequests->isEmpty())
                        @if(isset($teamPreferences) && $teamPreferences->isNotEmpty())

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('app.preference_order') }}</th>
                                            <th>{{ __('app.subject') }}</th>
                                            <th>{{ __('app.teacher') }}</th>
                                            <th>{{ __('app.status') }}</th>
                                            <th>{{ __('app.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($teamPreferences as $preference)
                                            <tr>
                                                <td>
                                                    @php
                                                        $badgeClass = 'bg-secondary';
                                                        if ($preference->preference_order <= 3) {
                                                            $badgeClass = 'bg-success'; // Green for top 3
                                                        } elseif ($preference->preference_order >= 8) {
                                                            $badgeClass = 'bg-warning'; // Orange for bottom 3
                                                        } else {
                                                            $badgeClass = 'bg-primary'; // Blue for middle
                                                        }
                                                    @endphp
                                                    <span class="badge {{ $badgeClass }} me-2">{{ $preference->preference_order }}</span>
                                                </td>
                                                <td>
                                                    <a href="#" class="text-decoration-none" onclick="showSubjectModal({{ $preference->subject->id }})">
                                                        <strong>{{ $preference->subject->title }}</strong>
                                                    </a>
                                                    @if($preference->subject->description)
                                                        <br>
                                                        <small class="text-muted">{{ Str::limit($preference->subject->description, 80) }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($preference->subject->teacher)
                                                        <span class="badge bg-light text-dark">
                                                            {{ $preference->subject->teacher->name }}
                                                        </span>
                                                    @else
                                                        <small class="text-muted">{{ __('app.no_teacher_assigned') }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">{{ __('app.preference') }}</span>
                                                </td>
                                                <td>
                                                    <button type="button"
                                                            class="btn btn-outline-primary btn-sm"
                                                            onclick="showSubjectModal({{ $preference->subject->id }})"
                                                            title="{{ __('app.view_details') }}">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">{{ __('app.no_subject_requests') }}</h5>
                                <p class="text-muted">
                                    @auth
                                        @if(auth()->user()->role === 'student')
                                            {{ __('app.team_no_requests_yet') }}
                                        @elseif(auth()->user()->role === 'teacher')
                                            {{ __('app.no_teams_requested_subjects') }}
                                        @else
                                            {{ __('app.no_requests_submitted_yet') }}
                                        @endif
                                    @endauth
                                </p>
                                @auth
                                    @if(auth()->user()->role === 'student')
                                        <a href="{{ route('teams.my-team') }}" class="btn btn-primary">
                                            <i class="fas fa-users"></i> {{ __('app.go_to_my_team') }}
                                        </a>
                                    @endif
                                @endauth
                            </div>
                        @endif
                    @else
                        <!-- Stats Summary -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <h4>{{ $subjectRequests->where('status', 'pending')->count() }}</h4>
                                        <small>{{ __('app.pending') }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h4>{{ $subjectRequests->where('status', 'approved')->count() }}</h4>
                                        <small>{{ __('app.approved') }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-danger text-white">
                                    <div class="card-body text-center">
                                        <h4>{{ $subjectRequests->where('status', 'rejected')->count() }}</h4>
                                        <small>{{ __('app.rejected') }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <h4>{{ $subjectRequests->count() }}</h4>
                                        <small>{{ __('app.total') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Requests List -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>
                                            <i class="fas fa-calendar"></i> {{ __('app.request_date') }}
                                            <small class="text-muted d-block">{{ __('app.oldest_first') }}</small>
                                        </th>
                                        <th><i class="fas fa-users"></i> {{ __('app.team') }}</th>
                                        <th><i class="fas fa-list-ol"></i> {{ __('app.team_preferences') }}</th>
                                        <th><i class="fas fa-book"></i> {{ __('app.subject') }}</th>
                                        <th><i class="fas fa-user-tie"></i> {{ __('app.teacher') }}</th>
                                        <th><i class="fas fa-user"></i> {{ __('app.requested_by') }}</th>
                                        <th><i class="fas fa-info-circle"></i> {{ __('app.status') }}</th>
                                        <th><i class="fas fa-cog"></i> {{ __('app.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($subjectRequests as $request)
                                        <tr class="
                                            @if($request->isPending()) table-warning
                                            @elseif($request->isApproved()) table-success
                                            @elseif($request->isRejected()) table-danger
                                            @endif
                                        ">
                                            <td>
                                                <strong>{{ $request->requested_at->format('M d, Y') }}</strong>
                                                <br>
                                                <small class="text-muted">
                                                    {{ $request->requested_at->format('H:i') }}
                                                    ({{ $request->requested_at->diffForHumans() }})
                                                </small>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="badge bg-primary me-2">#{{ $request->priority_order }}</span>
                                                    <div>
                                                        <strong>{{ $request->team->name }}</strong>
                                                        <br>
                                                        <small class="text-muted">
                                                            {{ $request->team->members->count() }} {{ __('app.members_lowercase') }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $teamPreferences = $request->team->subjectPreferences()
                                                        ->with('subject')
                                                        ->orderBy('preference_order')
                                                        ->take(5)
                                                        ->get();
                                                @endphp
                                                @if($teamPreferences->count() > 0)
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @foreach($teamPreferences as $pref)
                                                            @php
                                                                $badgeClass = 'bg-secondary';
                                                                if ($pref->is_allocated) {
                                                                    $badgeClass = 'bg-success';
                                                                } elseif ($pref->preference_order <= 3) {
                                                                    $badgeClass = 'bg-success'; // Green for top 3
                                                                } elseif ($pref->preference_order >= 8) {
                                                                    $badgeClass = 'bg-warning'; // Orange for bottom 3
                                                                } else {
                                                                    $badgeClass = 'bg-primary'; // Blue for middle
                                                                }
                                                            @endphp
                                                            <span class="badge {{ $badgeClass }} small"
                                                                  title="{{ $pref->subject->title }}"
                                                                  style="font-size: 0.7rem;">
                                                                {{ $pref->preference_order }}
                                                            </span>
                                                        @endforeach
                                                        @if($teamPreferences->count() >= 5 && $request->team->subjectPreferences()->count() > 5)
                                                            <span class="badge bg-light text-dark small" style="font-size: 0.7rem;">+{{ $request->team->subjectPreferences()->count() - 5 }}</span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <small class="text-muted">{{ __('app.no_preferences_set') }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="#" class="text-decoration-none" onclick="showSubjectModal({{ $request->subject->id }})">
                                                    <strong>{{ $request->subject->title }}</strong>
                                                </a>
                                                @if($request->request_message)
                                                    <br>
                                                    <small class="text-muted">
                                                        <i class="fas fa-comment"></i>
                                                        {{ Str::limit($request->request_message, 50) }}
                                                    </small>
                                                @endif
                                            </td>
                                            <td>{{ $request->subject->teacher->name }}</td>
                                            <td>
                                                {{ $request->requestedBy->name }}
                                                <br>
                                                <small class="text-muted">{{ __('app.team_leader') }}</small>
                                            </td>
                                            <td>
                                                <span class="badge {{ $request->getStatusBadgeClass() }}">
                                                    @if($request->isPending())
                                                        <i class="fas fa-clock"></i> {{ __('app.pending') }}
                                                    @elseif($request->isApproved())
                                                        <i class="fas fa-check"></i> {{ __('app.approved') }}
                                                    @else
                                                        <i class="fas fa-times"></i> {{ __('app.rejected') }}
                                                    @endif
                                                </span>
                                                @if($request->responded_at)
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ $request->responded_at->diffForHumans() }}
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group-vertical btn-group-sm">
                                                    <!-- View Details -->
                                                    <button type="button" class="btn btn-outline-info btn-sm"
                                                            data-bs-toggle="collapse"
                                                            data-bs-target="#details-{{ $request->id }}">
                                                        <i class="fas fa-eye"></i> {{ __('app.details') }}
                                                    </button>

                                                    @auth
                                                        @if(auth()->user()->role === 'admin' && $request->isPending())
                                                            <!-- Admin Actions -->
                                                            <a href="{{ route('admin.subject-requests') }}"
                                                               class="btn btn-primary btn-sm">
                                                                <i class="fas fa-cog"></i> {{ __('app.manage') }}
                                                            </a>
                                                        @elseif(auth()->user()->role === 'student' && $request->isPending())
                                                            <!-- Team members can cancel -->
                                                            @php
                                                                $member = $request->team->members->where('student_id', auth()->id())->first();
                                                                $isMember = $member !== null;
                                                            @endphp
                                                            @if($isMember)
                                                                <form action="{{ route('teams.cancel-subject-request', [$request->team, $request]) }}"
                                                                      method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-outline-danger btn-sm"
                                                                            onclick="return confirm('{{ __('app.confirm_cancel_request') }}')">
                                                                        <i class="fas fa-times"></i> {{ __('app.cancel') }}
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        @endif
                                                    @endauth
                                                </div>
                                            </td>
                                        </tr>
                                        <!-- Collapsible Details Row -->
                                        <tr>
                                            <td colspan="7" class="p-0">
                                                <div class="collapse" id="details-{{ $request->id }}">
                                                    <div class="card card-body border-0 bg-light">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <h6><i class="fas fa-info-circle"></i> {{ __('app.request_details') }}</h6>
                                                                @if($request->request_message)
                                                                    <p><strong>{{ __('app.team_message') }}:</strong></p>
                                                                    <div class="alert alert-info">
                                                                        {{ $request->request_message }}
                                                                    </div>
                                                                @else
                                                                    <p class="text-muted">{{ __('app.no_message_provided') }}</p>
                                                                @endif

                                                                <p><strong>{{ __('app.team_members') }}:</strong></p>
                                                                <ul class="list-unstyled">
                                                                    @foreach($request->team->members as $member)
                                                                        <li class="mb-1">
                                                                            <i class="fas fa-user"></i>
                                                                            {{ $member->user->name }}
                                                                            @if($member->role === 'leader')
                                                                                <span class="badge bg-primary">{{ __('app.leader') }}</span>
                                                                            @endif
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <h6><i class="fas fa-book"></i> {{ __('app.subject_information') }}</h6>
                                                                <p><strong>{{ __('app.title') }}:</strong> {{ $request->subject->title }}</p>
                                                                <p><strong>{{ __('app.teacher') }}:</strong> {{ $request->subject->teacher->name }}</p>
                                                                @if($request->subject->description)
                                                                    <p><strong>{{ __('app.description') }}:</strong></p>
                                                                    <p class="text-muted">{{ Str::limit($request->subject->description, 200) }}</p>
                                                                @endif

                                                                @if($request->admin_response)
                                                                    <h6 class="mt-3"><i class="fas fa-reply"></i> {{ __('app.admin_response') }}</h6>
                                                                    <div class="alert alert-{{ $request->isApproved() ? 'success' : 'danger' }}">
                                                                        {{ $request->admin_response }}
                                                                    </div>
                                                                    <small class="text-muted">
                                                                        <i class="fas fa-user-shield"></i>
                                                                        {{ __('app.responded_by') }} {{ $request->respondedBy->name ?? __('app.admin') }}
                                                                        {{ __('app.on') }} {{ $request->responded_at->format('M d, Y \a\t H:i') }}
                                                                    </small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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

<!-- Subject Details Modal -->
<div class="modal fade" id="subjectModal" tabindex="-1" aria-labelledby="subjectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="subjectModalLabel">{{ __('app.subject_details') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="subjectModalContent">
                    <div class="text-center py-4">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.table-warning {
    --bs-table-bg: #fff3cd;
}
.table-success {
    --bs-table-bg: #d1e7dd;
}
.table-danger {
    --bs-table-bg: #f8d7da;
}
</style>
@endpush

@push('scripts')
<script>
function showSubjectModal(subjectId) {
    const modal = new bootstrap.Modal(document.getElementById('subjectModal'));
    const modalContent = document.getElementById('subjectModalContent');

    // Show loading spinner
    modalContent.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;

    // Show the modal
    modal.show();

    // Fetch subject details
    fetch(`/subjects/${subjectId}/modal`)
        .then(response => response.text())
        .then(html => {
            modalContent.innerHTML = html;
        })
        .catch(error => {
            modalContent.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Error loading subject details. Please try again.
                </div>
            `;
        });
}
</script>
@endpush