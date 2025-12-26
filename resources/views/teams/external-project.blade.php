@extends('layouts.pfe-app')

@section('page-title', 'Submit External Project')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Submit External Project Proposal</h4>
                    <small class="text-muted">Team: {{ $team->name }}</small>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> External Project Guidelines</h6>
                        <ul class="mb-0">
                            <li>External projects must be approved by the department head</li>
                            <li>Projects should align with academic standards and learning objectives</li>
                            <li>A company supervisor must be assigned to guide the project</li>
                            <li>Regular progress reports will be required</li>
                            <li>The project scope should be appropriate for the academic level</li>
                        </ul>
                    </div>

                    <form action="{{ route('teams.submit-external-project', $team) }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Project Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                                           id="title" name="title" value="{{ old('title') }}"
                                           placeholder="Enter the project title">
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Project Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                              id="description" name="description" rows="5"
                                              placeholder="Provide a detailed description of the project, its goals, and expected outcomes">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="objectives" class="form-label">Project Objectives <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('objectives') is-invalid @enderror"
                                              id="objectives" name="objectives" rows="4"
                                              placeholder="List the main objectives and learning goals of this project">{{ old('objectives') }}</textarea>
                                    @error('objectives')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="technologies" class="form-label">Technologies & Tools <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('technologies') is-invalid @enderror"
                                                   id="technologies" name="technologies" value="{{ old('technologies') }}"
                                                   placeholder="e.g., React, Node.js, MySQL, Docker">
                                            @error('technologies')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">Separate technologies with commas</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="project_duration" class="form-label">Project Duration (months) <span class="text-danger">*</span></label>
                                            <select class="form-select @error('project_duration') is-invalid @enderror"
                                                    id="project_duration" name="project_duration">
                                                <option value="">Select duration</option>
                                                @for($i = 1; $i <= 12; $i++)
                                                    <option value="{{ $i }}" {{ old('project_duration') == $i ? 'selected' : '' }}>
                                                        {{ $i }} month{{ $i > 1 ? 's' : '' }}
                                                    </option>
                                                @endfor
                                            </select>
                                            @error('project_duration')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Company Information</h6>

                                        <div class="mb-3">
                                            <label for="company_name" class="form-label">Company Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('company_name') is-invalid @enderror"
                                                   id="company_name" name="company_name" value="{{ old('company_name') }}"
                                                   placeholder="Company/Organization name">
                                            @error('company_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="supervisor_name" class="form-label">Supervisor Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('supervisor_name') is-invalid @enderror"
                                                   id="supervisor_name" name="supervisor_name" value="{{ old('supervisor_name') }}"
                                                   placeholder="Company supervisor name">
                                            @error('supervisor_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="supervisor_email" class="form-label">Supervisor Email <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control @error('supervisor_email') is-invalid @enderror"
                                                   id="supervisor_email" name="supervisor_email" value="{{ old('supervisor_email') }}"
                                                   placeholder="supervisor@company.com">
                                            @error('supervisor_email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="supervisor_phone" class="form-label">Supervisor Phone</label>
                                            <input type="tel" class="form-control @error('supervisor_phone') is-invalid @enderror"
                                                   id="supervisor_phone" name="supervisor_phone" value="{{ old('supervisor_phone') }}"
                                                   placeholder="+1234567890">
                                            @error('supervisor_phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="card bg-light mt-3">
                                    <div class="card-body">
                                        <h6 class="card-title">Team Information</h6>
                                        <div class="mb-2">
                                            <small class="text-muted">Team:</small> {{ $team->name }}
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-muted">Members:</small> {{ $team->members->count() }}
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-muted">Leader:</small>
                                            {{ $team->members->where('role', 'leader')->first()->user->name }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="alert alert-warning">
                                    <h6><i class="fas fa-exclamation-triangle"></i> Important Notice</h6>
                                    <p class="mb-2">
                                        By submitting this external project proposal, you acknowledge that:
                                    </p>
                                    <ul class="mb-0">
                                        <li>All information provided is accurate and complete</li>
                                        <li>The company supervisor agrees to guide and evaluate the project</li>
                                        <li>The project meets academic standards and requirements</li>
                                        <li>Regular progress reports will be submitted as required</li>
                                        <li>The project is subject to approval by the department head</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('teams.show', $team) }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Back to Team
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane"></i> Submit Project Proposal
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-resize textareas
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });
    });

    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('is-invalid');
            } else {
                field.classList.remove('is-invalid');
            }
        });

        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields.');
        }
    });
});
</script>
@endpush