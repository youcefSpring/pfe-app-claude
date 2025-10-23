@extends('layouts.pfe-app')

@section('page-title', __('app.subject_details'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">{{ $subject->title }}</h4>
                    <div>
                        @if(auth()->user()?->id === $subject->teacher_id)
                            <a href="{{ route('subjects.edit', $subject) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> {{ __('app.edit') }}
                            </a>
                        @endif
                        @if($subject->status === 'draft' && auth()->user()->id === $subject->teacher_id)
                            <form action="{{ route('subjects.submit-validation', $subject) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="fas fa-paper-plane"></i> {{ __('app.submit_for_validation') }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-4">
                                <h5>{{ __('app.description') }}</h5>
                                <div class="border p-3 bg-light rounded">
                                    {!! nl2br(e($subject->description)) !!}
                                </div>
                            </div>

                            <div class="mb-4">
                                <h5>{{ __('app.project_plan') }}</h5>
                                <div class="border p-3 bg-light rounded">
                                    {!! nl2br(e($subject->plan)) !!}
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <h6>{{ __('app.keywords') }}</h6>
                                        <div class="d-flex flex-wrap">
                                            @foreach(explode(',', $subject->keywords) as $keyword)
                                                <span class="badge bg-secondary me-1 mb-1">{{ trim($keyword) }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <h6>Tools & Technologies</h6>
                                        <div class="d-flex flex-wrap">
                                            @foreach(explode(',', $subject->tools) as $tool)
                                                <span class="badge bg-info me-1 mb-1">{{ trim($tool) }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($subject->validation_notes)
                                <div class="mb-4">
                                    <h6>Validation Notes</h6>
                                    <div class="alert alert-info">
                                        {{ $subject->validation_notes }}
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Subject Information</h6>

                                    <div class="mb-3">
                                        <small class="text-muted">Status</small>
                                        <div>
                                            @if($subject->status === 'draft')
                                                <span class="badge bg-secondary">Draft</span>
                                            @elseif($subject->status === 'pending_validation')
                                                <span class="badge bg-warning">Pending Validation</span>
                                            @elseif($subject->status === 'validated')
                                                <span class="badge bg-success">Validated</span>
                                            @elseif($subject->status === 'rejected')
                                                <span class="badge bg-danger">Rejected</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <small class="text-muted">Type</small>
                                        <div>
                                            @if($subject->is_external)
                                                <span class="badge bg-info">External Subject</span>
                                            @else
                                                <span class="badge bg-primary">Internal Subject</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <small class="text-muted">Proposed by</small>
                                        @if($subject->is_external && $subject->student)
                                            <div>{{ $subject->student->name }}</div>
                                            <small class="text-muted">Student</small>
                                        @elseif($subject->teacher)
                                            <div>{{ $subject->teacher->name }}</div>
                                            <small class="text-muted">{{ $subject->teacher->department }}</small>
                                        @else
                                            <div class="text-muted">Not assigned</div>
                                        @endif
                                    </div>

                                    @if($subject->is_external && $subject->externalSupervisor)
                                        <div class="mb-3">
                                            <small class="text-muted">External Supervisor</small>
                                            <div>{{ $subject->externalSupervisor->name }}</div>
                                            <small class="text-muted">{{ $subject->externalSupervisor->email }}</small>
                                            @if($subject->externalSupervisor->position)
                                                <br><small class="text-muted">{{ $subject->externalSupervisor->position }}</small>
                                            @endif
                                            @if($subject->externalSupervisor->phone)
                                                <br><small class="text-muted">{{ $subject->externalSupervisor->phone }}</small>
                                            @endif
                                        </div>
                                    @endif

                                    @if($subject->validated_by)
                                        <div class="mb-3">
                                            <small class="text-muted">Validated by</small>
                                            <div>{{ $subject->validator?->name }}</div>
                                            <small class="text-muted">{{ $subject->validated_at?->format('M d, Y') }}</small>
                                        </div>
                                    @endif

                                    @if($subject->is_external)
                                        @if($subject->company_name)
                                            <div class="mb-3">
                                                <small class="text-muted">Company/Organization</small>
                                                <div>{{ $subject->company_name }}</div>
                                            </div>
                                        @endif

                                        @if($subject->dataset_resources_link)
                                            <div class="mb-3">
                                                <small class="text-muted">Resources Link</small>
                                                <div>
                                                    <a href="{{ $subject->dataset_resources_link }}" target="_blank" class="btn btn-outline-info btn-sm">
                                                        <i class="bi bi-link-45deg me-1"></i>View Resources
                                                    </a>
                                                </div>
                                            </div>
                                        @endif
                                    @endif

                                    <div class="mb-3">
                                        <small class="text-muted">Created</small>
                                        <div>{{ $subject->created_at->format('M d, Y') }}</div>
                                    </div>

                                    @if($subject->projects->count() > 0)
                                        <div class="mb-3">
                                            <small class="text-muted">Assigned Projects</small>
                                            @foreach($subject->projects as $project)
                                                <div class="border-start border-primary ps-2 mb-2">
                                                    <div class="fw-bold">{{ $project->team->name }}</div>
                                                    <small class="text-muted">
                                                        {{ $project->team->members->count() }} members
                                                    </small>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    @if($teamPreferences->count() > 0)
                                        <div class="mb-3">
                                            <small class="text-muted">Teams that chose this subject</small>
                                            <div class="small text-muted mb-2">Ordered by preference priority</div>
                                            @foreach($teamPreferences as $preference)
                                                <div class="border-start border-info ps-2 mb-2">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div>
                                                            <div class="fw-bold d-flex align-items-center">
                                                                <span class="badge bg-info me-2" style="font-size: 0.7rem;">
                                                                    {{ $preference->preference_order }}{{ $preference->preference_order == 1 ? 'st' : ($preference->preference_order == 2 ? 'nd' : ($preference->preference_order == 3 ? 'rd' : 'th')) }} choice
                                                                </span>
                                                                {{ $preference->team->name }}
                                                            </div>
                                                            <small class="text-muted">
                                                                {{ $preference->team->members->count() }} members:
                                                                @foreach($preference->team->members->take(3) as $member)
                                                                    {{ $member->user->name }}@if(!$loop->last), @endif
                                                                @endforeach
                                                                @if($preference->team->members->count() > 3)
                                                                    and {{ $preference->team->members->count() - 3 }} more
                                                                @endif
                                                            </small>
                                                        </div>
                                                        @if($preference->preference_order == 1)
                                                            <span class="badge bg-warning text-dark" title="First choice">
                                                                <i class="fas fa-star"></i>
                                                            </span>
                                                        @endif
                                                    </div>
                                                    @if($preference->allocationDeadline)
                                                        <small class="text-muted">
                                                            Deadline: {{ $preference->allocationDeadline->academic_year }} - {{ $preference->allocationDeadline->level }}
                                                        </small>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>

                            @if($subject->status === 'validated' && !$subject->projects->count() && auth()->user()->role === 'student')
                                <div class="mt-3">
                                    <div class="alert alert-success">
                                        <h6>Available for Selection</h6>
                                        <p class="mb-2">This subject is available for team selection.</p>
                                        @if(auth()->user()->teamMember?->team)
                                            @php
                                                $userTeam = auth()->user()->teamMember->team;
                                                $isLeader = auth()->user()->teamMember->role === 'leader';
                                                $hasActiveDeadline = App\Models\AllocationDeadline::active()->first()?->canStudentsChoose() ?? false;
                                                $teamCanSelect = $userTeam->canSelectSubject();
                                            @endphp

                                            <div class="d-flex gap-2 flex-wrap">
                                                <a href="{{ route('teams.show', $userTeam) }}" class="btn btn-success btn-sm">
                                                    <i class="fas fa-users"></i> View My Team
                                                </a>

                                                @if($isLeader && $hasActiveDeadline && $teamCanSelect)
                                                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#requestSubjectModal">
                                                        <i class="fas fa-paper-plane"></i> Request This Subject
                                                    </button>
                                                @endif
                                            </div>

                                            @if(!$hasActiveDeadline)
                                                <small class="text-muted d-block mt-2">
                                                    <i class="fas fa-clock"></i> Subject request period has ended
                                                </small>
                                            @elseif(!$isLeader)
                                                <small class="text-muted d-block mt-2">
                                                    <i class="fas fa-info-circle"></i> Only team leaders can request subjects
                                                </small>
                                            @elseif(!$teamCanSelect)
                                                <small class="text-muted d-block mt-2">
                                                    <i class="fas fa-users"></i> Your team must have {{ config('team.sizes.licence.min', 2) }}-{{ config('team.sizes.licence.max', 4) }} members to request subjects
                                                    @if($userTeam->members->count() < config('team.sizes.licence.min', 2))
                                                        ({{ $userTeam->members->count() }}/{{ config('team.sizes.licence.min', 2) }} members)
                                                    @elseif($userTeam->members->count() > config('team.sizes.licence.max', 4))
                                                        ({{ $userTeam->members->count() }} members - too many)
                                                    @endif
                                                </small>
                                            @endif
                                        @else
                                            @php
                                                $hasActiveDeadline = App\Models\AllocationDeadline::active()->first()?->canStudentsChoose() ?? false;
                                            @endphp

                                            <div class="d-flex gap-2 flex-wrap">
                                                <a href="{{ route('teams.create') }}" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-plus"></i> Create Team First
                                                </a>

                                                @if($hasActiveDeadline)
                                                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#requestSubjectIndividualModal">
                                                        <i class="fas fa-paper-plane"></i> Request Subject
                                                    </button>
                                                @endif
                                            </div>

                                            @if(!$hasActiveDeadline)
                                                <small class="text-muted d-block mt-2">
                                                    <i class="fas fa-clock"></i> Subject request period has ended
                                                </small>
                                            @else
                                                <small class="text-muted d-block mt-2">
                                                    <i class="fas fa-info-circle"></i> You can request this subject individually or create a team first
                                                </small>
                                            @endif
                                        @endif
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

<!-- Request Subject Modal -->
@auth
    @if(auth()->user()->role === 'student' && auth()->user()->teamMember?->team)
        @php
            $userTeam = auth()->user()->teamMember->team;
            $isLeader = auth()->user()->teamMember->role === 'leader';
        @endphp

        @if($isLeader)
        <div class="modal fade" id="requestSubjectModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Request Subject: {{ $subject->title }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('teams.request-subject', $userTeam) }}" method="POST">
                        @csrf
                        <input type="hidden" name="subject_id" value="{{ $subject->id }}">
                        <div class="modal-body">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle"></i> Subject Details</h6>
                                <p><strong>Title:</strong> {{ $subject->title }}</p>
                                <p><strong>Teacher:</strong> {{ $subject->teacher->name }}</p>
                                <p class="mb-0"><strong>Team:</strong> {{ $userTeam->name }}</p>
                            </div>

                            <div class="mb-3">
                                <label for="request_message" class="form-label">Request Message</label>
                                <textarea name="request_message" id="request_message" class="form-control" rows="4"
                                          placeholder="Explain why your team wants this subject and how it aligns with your goals..."></textarea>
                                <small class="form-text text-muted">
                                    Tell the administration why your team is interested in this specific subject.
                                </small>
                            </div>

                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Note:</strong> This request needs admin approval. You'll be notified when your request is processed.
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-paper-plane"></i> Submit Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    @endif
@endauth

<!-- Individual Subject Request Modal for users without teams -->
@auth
    @if(auth()->user()->role === 'student' && !auth()->user()->teamMember)
        <div class="modal fade" id="requestSubjectIndividualModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Request Subject: {{ $subject->title }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('subjects.request-individual') }}" method="POST">
                        @csrf
                        <input type="hidden" name="subject_id" value="{{ $subject->id }}">
                        <div class="modal-body">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle"></i> Subject Details</h6>
                                <p><strong>Title:</strong> {{ $subject->title }}</p>
                                <p><strong>Teacher:</strong> {{ $subject->teacher->name }}</p>
                                <p class="mb-0"><strong>Requesting as:</strong> Individual Student</p>
                            </div>

                            <div class="mb-3">
                                <label for="individual_request_message" class="form-label">Request Message</label>
                                <textarea name="request_message" id="individual_request_message" class="form-control" rows="4"
                                          placeholder="Explain why you want this subject and how it aligns with your goals..."
                                          required></textarea>
                                <small class="form-text text-muted">
                                    Tell the administration why you are interested in this specific subject and your qualifications.
                                </small>
                            </div>

                            <div class="mb-3">
                                <label for="work_preference" class="form-label">Work Preference</label>
                                <select name="work_preference" id="work_preference" class="form-select" required>
                                    <option value="">Select your preference...</option>
                                    <option value="individual">Work individually</option>
                                    <option value="open_to_team">Open to joining/forming a team later</option>
                                </select>
                                <small class="form-text text-muted">
                                    Let us know if you prefer to work alone or are open to team collaboration.
                                </small>
                            </div>

                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Note:</strong> This request needs admin approval. You'll be notified when your request is processed.
                                Individual requests may be matched with other students or you may be asked to form a team.
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-paper-plane"></i> Submit Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endauth

@endsection