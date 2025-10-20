@if($requests->isEmpty())
    <div class="text-center py-4">
        <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
        <p class="text-muted">No requests in this category.</p>
    </div>
@else
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Team</th>
                    <th>Subject</th>
                    <th>Teacher</th>
                    <th>Requested By</th>
                    <th>Requested At</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($requests as $request)
                    <tr>
                        <td>
                            <strong>{{ $request->team->name }}</strong>
                            <br>
                            <small class="text-muted">
                                {{ $request->team->members->count() }} member(s)
                            </small>
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
                            {{ $request->requested_at->format('M d, Y') }}
                            <br>
                            <small class="text-muted">{{ $request->requested_at->diffForHumans() }}</small>
                        </td>
                        <td>
                            <span class="badge {{ $request->getStatusBadgeClass() }}">
                                {{ ucfirst($request->status) }}
                            </span>
                            @if($request->responded_at)
                                <br>
                                <small class="text-muted">
                                    {{ $request->responded_at->diffForHumans() }}
                                </small>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group-vertical btn-group-sm" role="group">
                                <!-- View Details -->
                                <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="collapse"
                                        data-bs-target="#details-{{ $request->id }}">
                                    <i class="fas fa-eye"></i> Details
                                </button>

                                @if($request->isPending())
                                    <!-- Approve Button -->
                                    <button type="button" class="btn btn-success btn-sm"
                                            onclick="openResponseModal({{ $request->id }}, 'approve', '{{ addslashes($request->subject->title) }}', '{{ addslashes($request->team->name) }}')">
                                        <i class="fas fa-check"></i> Approve
                                    </button>

                                    <!-- Reject Button -->
                                    <button type="button" class="btn btn-danger btn-sm"
                                            onclick="openResponseModal({{ $request->id }}, 'reject', '{{ addslashes($request->subject->title) }}', '{{ addslashes($request->team->name) }}')">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                @endif
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
                                            <h6>Request Details</h6>
                                            @if($request->request_message)
                                                <p><strong>Message:</strong></p>
                                                <p class="text-muted">{{ $request->request_message }}</p>
                                            @else
                                                <p class="text-muted">No message provided.</p>
                                            @endif

                                            <p><strong>Team Members:</strong></p>
                                            <ul class="list-unstyled">
                                                @foreach($request->team->members as $member)
                                                    <li>
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
                                            <h6>Subject Information</h6>
                                            <p><strong>Title:</strong> {{ $request->subject->title }}</p>
                                            <p><strong>Teacher:</strong> {{ $request->subject->teacher->name }}</p>
                                            <p><strong>Description:</strong></p>
                                            <p class="text-muted">{{ Str::limit($request->subject->description, 200) }}</p>

                                            @if($request->admin_response)
                                                <h6 class="mt-3">Admin Response</h6>
                                                <div class="alert alert-{{ $request->isApproved() ? 'success' : 'danger' }} alert-sm">
                                                    {{ $request->admin_response }}
                                                </div>
                                                <small class="text-muted">
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
@endif