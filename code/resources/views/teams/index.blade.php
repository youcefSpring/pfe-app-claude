@extends('layouts.pfe-app')

@section('title', 'Teams')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Teams</h1>
        @if(auth()->user()?->role === 'student')
            <a href="{{ route('teams.create') }}" class="btn btn-primary">
                <i class="bi bi-plus"></i> Create Team
            </a>
        @endif
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('teams.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="forming" {{ request('status') === 'forming' ? 'selected' : '' }}>Forming</option>
                        <option value="complete" {{ request('status') === 'complete' ? 'selected' : '' }}>Complete</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search"
                           value="{{ request('search') }}" placeholder="Search teams...">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-outline-primary">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Results Info -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="text-muted">
            Showing {{ $teams->firstItem() ?? 0 }} to {{ $teams->lastItem() ?? 0 }}
            of {{ $teams->total() }} results
        </div>
        @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('teams.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-x-circle"></i> Clear Filters
            </a>
        @endif
    </div>

    <!-- Teams Table -->
    <div class="card">
        <div class="card-body">
            @if($teams->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Team Name</th>
                                <th>Members</th>
                                <th>Leader</th>
                                <th>Status</th>
                                <th>Subject</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($teams as $team)
                                @php
                                    $userIsMember = $team->members->contains('user_id', auth()->id());
                                    $isLeader = $team->members->where('user_id', auth()->id())->where('role', 'leader')->count() > 0;
                                    $leader = $team->members->where('role', 'leader')->first();
                                @endphp
                                <tr>
                                    <td>
                                        <div>
                                            <strong>{{ $team->name }}</strong>
                                            @if($team->description)
                                                <div class="text-muted small">{{ Str::limit($team->description, 60) }}</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($team->members->take(3) as $member)
                                                <span class="badge bg-light text-dark small">
                                                    {{ $member->user->name }}
                                                </span>
                                            @endforeach
                                            @if($team->members->count() > 3)
                                                <span class="badge bg-secondary small">+{{ $team->members->count() - 3 }}</span>
                                            @endif
                                        </div>
                                        <small class="text-muted">{{ $team->members->count() }} total</small>
                                    </td>
                                    <td>
                                        @if($leader)
                                            <span class="text-nowrap">
                                                <i class="bi bi-star-fill text-warning"></i>
                                                {{ $leader->user->name }}
                                            </span>
                                        @else
                                            <span class="text-muted">No leader</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $team->status === 'active' ? 'success' : ($team->status === 'complete' ? 'primary' : 'warning') }}">
                                            {{ ucfirst($team->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($team->project && $team->project->subject)
                                            <span class="text-nowrap" title="{{ $team->project->subject->title }}">
                                                <i class="bi bi-journal"></i>
                                                {{ Str::limit($team->project->subject->title, 25) }}
                                            </span>
                                        @else
                                            <span class="text-muted">
                                                <i class="bi bi-question-circle"></i> Not selected
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-nowrap">
                                        {{ $team->created_at->format('M d, Y') }}
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('teams.show', $team) }}"
                                               class="btn btn-outline-primary btn-sm"
                                               title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>

                                            @if(auth()->user()?->role === 'student')
                                                @if(!$userIsMember && $team->status === 'forming')
                                                    <form method="POST" action="{{ route('teams.join', $team) }}" class="d-inline">
                                                        @csrf
                                                        <button type="submit"
                                                                class="btn btn-outline-success btn-sm"
                                                                title="Join Team">
                                                            <i class="bi bi-person-plus"></i>
                                                        </button>
                                                    </form>
                                                @elseif($userIsMember && !$isLeader)
                                                    <form method="POST" action="{{ route('teams.leave', $team) }}" class="d-inline">
                                                        @csrf
                                                        <button type="submit"
                                                                class="btn btn-outline-danger btn-sm"
                                                                title="Leave Team"
                                                                onclick="return confirm('Are you sure you want to leave this team?')">
                                                            <i class="bi bi-person-dash"></i>
                                                        </button>
                                                    </form>
                                                @elseif($isLeader)
                                                    <a href="{{ route('teams.edit', $team) }}"
                                                       class="btn btn-outline-warning btn-sm"
                                                       title="Edit Team">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form method="POST" action="{{ route('teams.destroy', $team) }}" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="btn btn-outline-danger btn-sm"
                                                                title="Delete Team"
                                                                onclick="return confirm('Are you sure you want to delete this team?')">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-people text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3">No Teams Found</h4>
                    <p class="text-muted">
                        @if(request()->hasAny(['search', 'status']))
                            No teams match your current filters. Try adjusting your search criteria.
                        @else
                            There are no teams available at the moment.
                        @endif
                    </p>
                    @if(auth()->user()?->role === 'student')
                        <a href="{{ route('teams.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus"></i> Create First Team
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Pagination -->
    @if($teams->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $teams->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection