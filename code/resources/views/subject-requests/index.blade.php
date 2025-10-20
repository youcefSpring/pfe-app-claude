@extends('layouts.pfe-app')

@section('page-title', 'Subject Requests')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-paper-plane"></i> Subject Requests
                    </h4>
                    <p class="text-muted mb-0">
                        @auth
                            @if(auth()->user()->role === 'student')
                                Your team's subject requests ordered by submission date
                            @elseif(auth()->user()->role === 'teacher')
                                Subject requests for your supervised subjects
                            @else
                                All subject requests in the system
                            @endif
                        @endauth
                    </p>
                </div>
                <div class="card-body">
                    @if($subjectRequests->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Subject Requests</h5>
                            <p class="text-muted">
                                @auth
                                    @if(auth()->user()->role === 'student')
                                        Your team hasn't submitted any subject requests yet.
                                    @elseif(auth()->user()->role === 'teacher')
                                        No teams have requested your subjects yet.
                                    @else
                                        No subject requests have been submitted yet.
                                    @endif
                                @endauth
                            </p>
                            @auth
                                @if(auth()->user()->role === 'student')
                                    <a href="{{ route('teams.my-team') }}" class="btn btn-primary">
                                        <i class="fas fa-users"></i> Go to My Team
                                    </a>
                                @endif
                            @endauth
                        </div>
                    @else
                        <!-- Stats Summary -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <h4>{{ $subjectRequests->where('status', 'pending')->count() }}</h4>
                                        <small>Pending</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h4>{{ $subjectRequests->where('status', 'approved')->count() }}</h4>
                                        <small>Approved</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-danger text-white">
                                    <div class="card-body text-center">
                                        <h4>{{ $subjectRequests->where('status', 'rejected')->count() }}</h4>
                                        <small>Rejected</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <h4>{{ $subjectRequests->count() }}</h4>
                                        <small>Total</small>
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
                                            <i class="fas fa-calendar"></i> Request Date
                                            <small class="text-muted d-block">Oldest First</small>
                                        </th>
                                        <th><i class="fas fa-users"></i> Team</th>
                                        <th><i class="fas fa-book"></i> Subject</th>
                                        <th><i class="fas fa-user-tie"></i> Teacher</th>
                                        <th><i class="fas fa-user"></i> Requested By</th>
                                        <th><i class="fas fa-info-circle"></i> Status</th>
                                        <th><i class="fas fa-cog"></i> Actions</th>
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
                                                            {{ $request->team->members->count() }} member(s)
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <strong>{{ $request->subject->title }}</strong>
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
                                                <small class="text-muted">Team Leader</small>
                                            </td>
                                            <td>
                                                <span class="badge {{ $request->getStatusBadgeClass() }}">
                                                    @if($request->isPending())
                                                        <i class="fas fa-clock"></i> Pending
                                                    @elseif($request->isApproved())
                                                        <i class="fas fa-check"></i> Approved
                                                    @else
                                                        <i class="fas fa-times"></i> Rejected
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
                                                        <i class="fas fa-eye"></i> Details
                                                    </button>

                                                    @auth
                                                        @if(auth()->user()->role === 'admin' && $request->isPending())
                                                            <!-- Admin Actions -->
                                                            <a href="{{ route('admin.subject-requests') }}"
                                                               class="btn btn-primary btn-sm">
                                                                <i class="fas fa-cog"></i> Manage
                                                            </a>
                                                        @elseif(auth()->user()->role === 'student' && $request->isPending())
                                                            <!-- Team Leader can cancel -->
                                                            @php
                                                                $member = $request->team->members->where('student_id', auth()->id())->first();
                                                                $isLeader = $member && $member->role === 'leader';
                                                            @endphp
                                                            @if($isLeader)
                                                                <form action="{{ route('teams.cancel-subject-request', [$request->team, $request]) }}"
                                                                      method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-outline-danger btn-sm"
                                                                            onclick="return confirm('Cancel this request?')">
                                                                        <i class="fas fa-times"></i> Cancel
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
                                                                <h6><i class="fas fa-info-circle"></i> Request Details</h6>
                                                                @if($request->request_message)
                                                                    <p><strong>Team Message:</strong></p>
                                                                    <div class="alert alert-info">
                                                                        {{ $request->request_message }}
                                                                    </div>
                                                                @else
                                                                    <p class="text-muted">No message provided by the team.</p>
                                                                @endif

                                                                <p><strong>Team Members:</strong></p>
                                                                <ul class="list-unstyled">
                                                                    @foreach($request->team->members as $member)
                                                                        <li class="mb-1">
                                                                            <i class="fas fa-user"></i>
                                                                            {{ $member->user->name }}
                                                                            @if($member->role === 'leader')
                                                                                <span class="badge bg-primary">Leader</span>
                                                                            @endif
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <h6><i class="fas fa-book"></i> Subject Information</h6>
                                                                <p><strong>Title:</strong> {{ $request->subject->title }}</p>
                                                                <p><strong>Teacher:</strong> {{ $request->subject->teacher->name }}</p>
                                                                @if($request->subject->description)
                                                                    <p><strong>Description:</strong></p>
                                                                    <p class="text-muted">{{ Str::limit($request->subject->description, 200) }}</p>
                                                                @endif

                                                                @if($request->admin_response)
                                                                    <h6 class="mt-3"><i class="fas fa-reply"></i> Admin Response</h6>
                                                                    <div class="alert alert-{{ $request->isApproved() ? 'success' : 'danger' }}">
                                                                        {{ $request->admin_response }}
                                                                    </div>
                                                                    <small class="text-muted">
                                                                        <i class="fas fa-user-shield"></i>
                                                                        Responded by {{ $request->respondedBy->name ?? 'Admin' }}
                                                                        on {{ $request->responded_at->format('M d, Y \a\t H:i') }}
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