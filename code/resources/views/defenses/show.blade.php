@extends('layouts.pfe-app')

@section('title', 'Defense Details')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Defense Details</h1>
        <div class="btn-group">
            <a href="{{ route('defenses.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Defenses
            </a>
            @if(in_array(auth()->user()?->role, ['admin', 'department_head']))
                @if($defense->status === 'scheduled')
                    <a href="{{ route('defenses.edit', $defense) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                @endif
                @if($defense->status === 'completed')
                    <a href="{{ route('defenses.download-report-pdf', $defense) }}" class="btn btn-danger">
                        <i class="bi bi-file-pdf"></i> Download PDF
                    </a>
                @endif
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Defense Information -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Defense Information</h5>
                    <span class="badge bg-{{ $defense->status === 'completed' ? 'success' : ($defense->status === 'in_progress' ? 'warning' : ($defense->status === 'cancelled' ? 'danger' : 'primary')) }} fs-6">
                        {{ ucfirst(str_replace('_', ' ', $defense->status)) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Subject Information</h6>
                            <p><strong>Subject:</strong> {{ $defense->subject->title ?? 'N/A' }}</p>
                            <p><strong>Teacher:</strong> {{ $defense->subject->teacher->name ?? 'N/A' }}</p>
                            <p><strong>Type:</strong> {{ $defense->subject->is_external ? 'External' : 'Internal' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Defense Details</h6>
                            <p><strong>Date:</strong> {{ $defense->defense_date ? \Carbon\Carbon::parse($defense->defense_date)->format('M d, Y') : 'TBD' }}</p>
                            <p><strong>Time:</strong> {{ $defense->defense_time ? \Carbon\Carbon::parse($defense->defense_time)->format('g:i A') : 'TBD' }}</p>
                            <p><strong>Duration:</strong> {{ $defense->duration ?? 60 }} minutes</p>
                            <p><strong>Room:</strong> {{ $defense->room->name ?? 'TBD' }}
                                @if($defense->room && $defense->room->location)
                                    <small class="text-muted">({{ $defense->room->location }})</small>
                                @endif
                            </p>
                        </div>
                    </div>

                    @if($defense->notes)
                        <div class="mt-3">
                            <h6 class="text-muted">Notes</h6>
                            <p class="text-muted">{{ $defense->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Team Members -->
            @if($defense->project && $defense->project->team && $defense->project->team->members->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Team Members</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($defense->project->team->members as $member)
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                                            {{ substr($member->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-1">{{ $member->user->name }}</h6>
                                            <small class="text-muted">{{ $member->user->email }}</small>
                                            @if($member->is_leader)
                                                <span class="badge bg-success ms-2">Leader</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Jury Members -->
            @if($defense->juries->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Jury Members</h5>
                    </div>
                    <div class="card-body">
                        @foreach($defense->juries as $jury)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <h6 class="mb-1">{{ $jury->teacher->name }}</h6>
                                    <small class="text-muted">{{ $jury->role }}</small>
                                </div>
                                @if($defense->status === 'completed' && $jury->grade)
                                    <span class="badge bg-info">{{ $jury->grade }}/20</span>
                                @endif
                            </div>
                            @if(!$loop->last)
                                <hr class="my-2">
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Actions for Jury Members -->
            @if(auth()->user()?->role === 'teacher' && $defense->juries->contains('teacher_id', auth()->id()))
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Jury Actions</h5>
                    </div>
                    <div class="card-body">
                        @if($defense->status === 'scheduled' || $defense->status === 'in_progress')
                            <p class="text-muted">You are assigned as a jury member for this defense.</p>
                            @if($defense->status === 'scheduled')
                                <div class="alert alert-info">
                                    <small>Defense is scheduled. You can start evaluation when the defense begins.</small>
                                </div>
                            @endif
                        @elseif($defense->status === 'completed')
                            <div class="alert alert-success">
                                <small>Defense completed. Grades have been submitted.</small>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Actions for Team Members -->
            @if($isTeamMember)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Team Actions</h5>
                    </div>
                    <div class="card-body">
                        @if($defense->status === 'scheduled')
                            <div class="alert alert-info">
                                <strong>Your defense is scheduled!</strong><br>
                                <small>Make sure to be present at the scheduled time and location.</small>
                            </div>
                        @elseif($defense->status === 'completed')
                            <div class="alert alert-success">
                                <strong>Defense completed!</strong><br>
                                <small>Check with your supervisor for final results.</small>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 40px;
    height: 40px;
    font-size: 16px;
    font-weight: 600;
}
</style>
@endsection