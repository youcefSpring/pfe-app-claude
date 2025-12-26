@if($requests->isEmpty())
    <div class="text-center py-4">
        <i class="bi bi-inbox fs-2 text-muted mb-2"></i>
        <p class="text-muted">{{ __('app.no_requests_in_category') }}</p>
    </div>
@else
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>{{ __('app.team') }}</th>
                    <th>{{ __('app.subject') }}</th>
                    <th>{{ __('app.teacher') }}</th>
                    <th>{{ __('app.requested_by') }}</th>
                    <th>{{ __('app.requested_at') }}</th>
                    <th>{{ __('app.status') }}</th>
                    <th>{{ __('app.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($requests as $request)
                    <tr>
                        <td>
                            <strong>{{ $request->team->name }}</strong>
                            <br>
                            <small class="text-muted">
                                {{ $request->team->members->count() }} {{ __('app.members_lowercase') }}
                            </small>
                        </td>
                        <td>
                            <strong>{{ $request->subject->title }}</strong>
                            @if($request->request_message)
                                <br>
                                <small class="text-muted">
                                    <i class="bi bi-chat-text"></i>
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
                            {{ $request->requested_at->format('M d, Y') }}
                            <br>
                            <small class="text-muted">{{ $request->requested_at->diffForHumans() }}</small>
                        </td>
                        <td>
                            <span class="badge {{ $request->getStatusBadgeClass() }}">
                                {{ __('app.' . $request->status) }}
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
                                    <i class="bi bi-eye"></i> {{ __('app.details') }}
                                </button>

                                @if($request->isPending())
                                    <!-- Approve Button -->
                                    <button type="button" class="btn btn-success btn-sm"
                                            onclick="openResponseModal({{ $request->id }}, 'approve', '{{ addslashes($request->subject->title) }}', '{{ addslashes($request->team->name) }}')">
                                        <i class="bi bi-check-circle"></i> {{ __('app.approve') }}
                                    </button>

                                    <!-- Reject Button -->
                                    <button type="button" class="btn btn-danger btn-sm"
                                            onclick="openResponseModal({{ $request->id }}, 'reject', '{{ addslashes($request->subject->title) }}', '{{ addslashes($request->team->name) }}')">
                                        <i class="bi bi-x-circle"></i> {{ __('app.reject') }}
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
                                            <h6>{{ __('app.request_details') }}</h6>
                                            @if($request->request_message)
                                                <p><strong>{{ __('app.message') }}:</strong></p>
                                                <p class="text-muted">{{ $request->request_message }}</p>
                                            @else
                                                <p class="text-muted">{{ __('app.no_message_provided') }}</p>
                                            @endif

                                            <p><strong>{{ __('app.team_members') }}:</strong></p>
                                            <ul class="list-unstyled">
                                                @foreach($request->team->members as $member)
                                                    <li>
                                                        <i class="bi bi-person"></i>
                                                        {{ $member->user->name }}
                                                        @if($member->role === 'leader')
                                                            <span class="badge bg-primary">{{ __('app.leader') }}</span>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>{{ __('app.subject_information') }}</h6>
                                            <p><strong>{{ __('app.title') }}:</strong> {{ $request->subject->title }}</p>
                                            <p><strong>{{ __('app.teacher') }}:</strong> {{ $request->subject->teacher->name }}</p>
                                            <p><strong>{{ __('app.description') }}:</strong></p>
                                            <p class="text-muted">{{ Str::limit($request->subject->description, 200) }}</p>

                                            @if($request->admin_response)
                                                <h6 class="mt-3">{{ __('app.admin_response') }}</h6>
                                                <div class="alert alert-{{ $request->isApproved() ? 'success' : 'danger' }} alert-sm">
                                                    {{ $request->admin_response }}
                                                </div>
                                                <small class="text-muted">
                                                    {{ __('app.responded_by') }} {{ $request->respondedBy->name ?? 'Admin' }}
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
@endif