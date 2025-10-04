@extends('layouts.pfe-app')

@section('title', 'Edit Project')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Edit Project</h1>
        <div class="btn-group">
            <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-primary">
                <i class="bi bi-eye"></i> View Project
            </a>
            <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Projects
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Project Information</h5>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('projects.update', $project) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="title" class="form-label">Project Title</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror"
                                   id="title" name="title" value="{{ old('title', $project->title) }}"
                                   placeholder="Enter project title">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="4"
                                      placeholder="Describe the project objectives, scope, and methodology...">{{ old('description', $project->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="supervisor_id" class="form-label">Supervisor <span class="text-danger">*</span></label>
                            <select class="form-select @error('supervisor_id') is-invalid @enderror" id="supervisor_id" name="supervisor_id" required>
                                <option value="">Select Supervisor</option>
                                @foreach($supervisors as $supervisor)
                                    <option value="{{ $supervisor->id }}"
                                            {{ old('supervisor_id', $project->supervisor_id) == $supervisor->id ? 'selected' : '' }}>
                                        {{ $supervisor->name }}
                                        @if($supervisor->email)
                                            ({{ $supervisor->email }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('supervisor_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">Start Date</label>
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                           id="start_date" name="start_date"
                                           value="{{ old('start_date', $project->start_date ? $project->start_date->format('Y-m-d') : '') }}">
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">Expected End Date</label>
                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                           id="end_date" name="end_date"
                                           value="{{ old('end_date', $project->end_date ? $project->end_date->format('Y-m-d') : '') }}">
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                <option value="pending" {{ old('status', $project->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="active" {{ old('status', $project->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="in_progress" {{ old('status', $project->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ old('status', $project->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ old('status', $project->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="requirements" class="form-label">Requirements & Guidelines</label>
                            <textarea class="form-control @error('requirements') is-invalid @enderror"
                                      id="requirements" name="requirements" rows="3"
                                      placeholder="List specific requirements, deliverables, and guidelines...">{{ old('requirements', $project->requirements) }}</textarea>
                            @error('requirements')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Additional Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                      id="notes" name="notes" rows="2"
                                      placeholder="Any additional notes or comments...">{{ old('notes', $project->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg"></i> Update Project
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Current Project Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Current Project Info</h5>
                </div>
                <div class="card-body">
                    <h6>{{ $project->subject->title ?? 'N/A' }}</h6>
                    <p class="text-muted mb-2">{{ $project->team->name ?? 'No team assigned' }}</p>

                    <div class="small">
                        <p class="mb-1"><strong>Subject Type:</strong> {{ ucfirst($project->subject->type ?? 'N/A') }}</p>
                        <p class="mb-1"><strong>Current Status:</strong>
                            <span class="badge bg-{{ $project->status === 'active' ? 'success' : ($project->status === 'in_progress' ? 'warning' : 'secondary') }}">
                                {{ ucfirst($project->status) }}
                            </span>
                        </p>
                        <p class="mb-1"><strong>Created:</strong> {{ $project->created_at->format('M d, Y') }}</p>
                        @if($project->updated_at != $project->created_at)
                            <p class="mb-0"><strong>Last Updated:</strong> {{ $project->updated_at->format('M d, Y') }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Team Information -->
            @if($project->team && $project->team->members->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Team Members</h5>
                    </div>
                    <div class="card-body">
                        @foreach($project->team->members as $member)
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar-sm bg-{{ $member->is_leader ? 'primary' : 'secondary' }} text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                    {{ substr($member->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <h6 class="mb-0 small">{{ $member->user->name }}</h6>
                                    @if($member->is_leader)
                                        <small class="text-primary">Team Leader</small>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Warning -->
            <div class="card border-warning">
                <div class="card-body">
                    <div class="alert alert-warning mb-0">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Note:</strong> Changing the supervisor or status may affect project workflow and notifications.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 12px;
    font-weight: 600;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set minimum dates
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');

    startDateInput.addEventListener('change', function() {
        endDateInput.min = this.value;
    });

    // Initialize end date minimum
    if (startDateInput.value) {
        endDateInput.min = startDateInput.value;
    }
});
</script>
@endsection