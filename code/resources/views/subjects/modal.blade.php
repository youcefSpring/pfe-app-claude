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
                    <h6>{{ __('app.tools_technologies') }}</h6>
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
                <h6>{{ __('app.validation_notes') }}</h6>
                <div class="alert alert-info">
                    {{ $subject->validation_notes }}
                </div>
            </div>
        @endif
    </div>

    <div class="col-md-4">
        <div class="card bg-light">
            <div class="card-body">
                <h6 class="card-title">{{ __('app.subject_information') }}</h6>

                <div class="mb-3">
                    <small class="text-muted">{{ __('app.status') }}</small>
                    <div>
                        @if($subject->status === 'draft')
                            <span class="badge bg-secondary">{{ __('app.draft') }}</span>
                        @elseif($subject->status === 'pending_validation')
                            <span class="badge bg-warning">{{ __('app.pending_validation') }}</span>
                        @elseif($subject->status === 'validated')
                            <span class="badge bg-success">{{ __('app.validated') }}</span>
                        @elseif($subject->status === 'rejected')
                            <span class="badge bg-danger">{{ __('app.rejected') }}</span>
                        @endif
                    </div>
                </div>

                <div class="mb-3">
                    <small class="text-muted">{{ __('app.type') }}</small>
                    <div>
                        @if($subject->is_external)
                            <span class="badge bg-info">{{ __('app.external_subject') }}</span>
                        @else
                            <span class="badge bg-primary">{{ __('app.internal_subject') }}</span>
                        @endif
                    </div>
                </div>

                <div class="mb-3">
                    <small class="text-muted">{{ __('app.proposed_by') }}</small>
                    @if($subject->is_external && $subject->student)
                        <div>{{ $subject->student->name }}</div>
                        <small class="text-muted">{{ __('app.student') }}</small>
                    @elseif($subject->teacher)
                        <div>{{ $subject->teacher->name }}</div>
                        <small class="text-muted">{{ $subject->teacher->department }}</small>
                    @else
                        <div class="text-muted">{{ __('app.not_assigned') }}</div>
                    @endif
                </div>

                @if($subject->is_external && $subject->externalSupervisor)
                    <div class="mb-3">
                        <small class="text-muted">{{ __('app.external_supervisor') }}</small>
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
                        <small class="text-muted">{{ __('app.validated_by') }}</small>
                        <div>{{ $subject->validator?->name }}</div>
                        <small class="text-muted">{{ $subject->validated_at?->format('M d, Y') }}</small>
                    </div>
                @endif

                @if($subject->is_external)
                    @if($subject->company_name)
                        <div class="mb-3">
                            <small class="text-muted">{{ __('app.company_organization') }}</small>
                            <div>{{ $subject->company_name }}</div>
                        </div>
                    @endif

                    @if($subject->dataset_resources_link)
                        <div class="mb-3">
                            <small class="text-muted">{{ __('app.resources_link') }}</small>
                            <div>
                                <a href="{{ $subject->dataset_resources_link }}" target="_blank" class="btn btn-outline-info btn-sm">
                                    <i class="bi bi-link-45deg me-1"></i>{{ __('app.view_resources') }}
                                </a>
                            </div>
                        </div>
                    @endif
                @endif

                <div class="mb-3">
                    <small class="text-muted">{{ __('app.created') }}</small>
                    <div>{{ $subject->created_at->format('M d, Y') }}</div>
                </div>

                @if($subject->projects->count() > 0)
                    <div class="mb-3">
                        <small class="text-muted">{{ __('app.assigned_projects') }}</small>
                        @foreach($subject->projects as $project)
                            <div class="border-start border-primary ps-2 mb-2">
                                <div class="fw-bold">{{ $project->team->name }}</div>
                                <small class="text-muted">
                                    {{ $project->team->members->count() }} {{ __('app.members') }}
                                </small>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        @if($subject->status === 'validated' && !$subject->projects->count() && auth()->user()->role === 'student')
            <div class="mt-3">
                <div class="alert alert-success border-0 shadow-sm">
                    <h6 class="fw-bold mb-2">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        {{ __('app.available_for_selection') }}
                    </h6>
                    <p class="mb-3 small">{{ __('app.subject_available_for_team_selection') }}</p>
                    @if(auth()->user()->teamMember?->team)
                        @php
                            $userTeam = auth()->user()->teamMember->team;
                            $isInPreferences = $userTeam->subjectPreferences->contains('subject_id', $subject->id);
                            $isLeader = auth()->user()->teamMember->role === 'leader';
                            $canAddMore = $userTeam->subjectPreferences->count() < 10;
                        @endphp

                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('teams.my-team') }}" class="btn btn-success">
                                <i class="bi bi-people-fill me-2"></i>
                                {{ __('app.view_my_team') }}
                            </a>

                            @if($isLeader)
                                @if($isInPreferences)
                                    <button class="btn btn-secondary" disabled>
                                        <i class="bi bi-check-circle-fill me-2"></i> {{ __('In Preferences') }}
                                    </button>
                                @elseif($canAddMore)
                                    <form action="{{ route('teams.add-subject-preference', $userTeam) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="subject_id" value="{{ $subject->id }}">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-star-fill me-2"></i> {{ __('Add to Favorites') }}
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-warning" disabled title="{{ __('Maximum 10 preferences reached') }}">
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ __('Max Reached') }}
                                    </button>
                                @endif
                                <a href="{{ route('teams.subject-preferences', $userTeam) }}" class="btn btn-outline-primary">
                                    <i class="bi bi-list-ol me-2"></i> {{ __('Manage Preferences') }}
                                </a>
                            @endif
                        </div>
                    @else
                        <a href="{{ route('teams.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>
                            {{ __('app.create_team_first') }}
                        </a>
                    @endif
                </div>
            </div>
        @endif

        @if(auth()->user()?->id === $subject->teacher_id)
        <div class="mt-3">
            <div class="d-grid gap-2">
                <a href="{{ route('subjects.edit', $subject) }}" class="btn btn-primary">
                    <i class="bi bi-pencil me-2"></i> {{ __('app.edit') }}
                </a>
                @if($subject->status === 'draft' && auth()->user()->id === $subject->teacher_id)
                    <form action="{{ route('subjects.submit-validation', $subject) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-check-circle me-2"></i> {{ __('app.submit_for_validation') }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

<style>
    /* Enhanced button styling for subject modal */
    .alert-success {
        background-color: #d1e7dd;
        border: 1px solid #badbcc;
    }

    .alert-success .btn {
        font-weight: 500;
        padding: 0.5rem 1rem;
        transition: all 0.2s ease;
    }

    .alert-success .btn:hover:not(:disabled) {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .alert-success .btn-success {
        background-color: #198754;
        border-color: #198754;
    }

    .alert-success .btn-success:hover {
        background-color: #157347;
        border-color: #146c43;
    }

    .alert-success .btn-primary {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    .alert-success .btn-primary:hover {
        background-color: #0b5ed7;
        border-color: #0a58ca;
    }

    .alert-success .btn-outline-primary {
        color: #0d6efd;
        border-color: #0d6efd;
        background-color: transparent;
    }

    .alert-success .btn-outline-primary:hover {
        color: #fff;
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    .alert-success .btn-secondary:disabled {
        opacity: 0.7;
    }

    .alert-success .btn-warning:disabled {
        opacity: 0.8;
    }
</style>