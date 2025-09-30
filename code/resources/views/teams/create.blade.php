@extends('layouts.pfe-app')

@section('page-title', 'Create Team')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Create New Team</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('teams.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="name" class="form-label">Team Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name') }}"
                                   placeholder="Enter a unique team name">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Choose a unique name that represents your team. This will be visible to other students and teachers.
                            </small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Team Guidelines</h6>
                                        <ul class="small mb-0">
                                            <li>Team names must be unique</li>
                                            <li>You will automatically become the team leader</li>
                                            <li>Teams can have 2-4 members maximum</li>
                                            <li>You can invite other students after creation</li>
                                            <li>All team members must be from the same department</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Next Steps</h6>
                                        <ul class="small mb-0">
                                            <li>Add team members by email</li>
                                            <li>Complete team formation (2+ members)</li>
                                            <li>Select a subject from available options</li>
                                            <li>Or submit an external project proposal</li>
                                            <li>Start working on your project</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('teams.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to Teams
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-users"></i> Create Team
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @if(auth()->user()->teamMember)
                <div class="alert alert-warning mt-3">
                    <h6><i class="fas fa-exclamation-triangle"></i> Already in a Team</h6>
                    <p class="mb-2">You are already a member of <strong>{{ auth()->user()->teamMember->team->name }}</strong>.</p>
                    <a href="{{ route('teams.show', auth()->user()->teamMember->team) }}" class="btn btn-primary btn-sm">
                        View My Team
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if user is already in a team
    @if(auth()->user()->teamMember)
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            alert('You are already a member of a team. Please leave your current team first.');
        });
    @endif
});
</script>
@endpush