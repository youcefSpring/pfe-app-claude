@extends('layouts.pfe-app')

@section('page-title', 'Create New Subject')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-plus-circle me-2"></i>Create New Subject
                </h5>
            </div>
            <div class="card-body">
                <form id="createSubjectForm" method="POST" action="{{ route('subjects.store') }}">
                    @csrf

                    <!-- Title -->
                    <div class="mb-3">
                        <label for="title" class="form-label required">Subject Title</label>
                        <input type="text"
                               class="form-control @error('title') is-invalid @enderror"
                               id="title"
                               name="title"
                               value="{{ old('title') }}"
                               required
                               placeholder="Enter a clear and descriptive title">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Choose a title that clearly describes the project topic and scope.
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label required">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description"
                                  name="description"
                                  rows="4"
                                  required
                                  placeholder="Provide a detailed description of the project...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Describe the project context, main goals, and expected outcomes.
                        </div>
                    </div>

                    <!-- Requirements -->
                    <div class="mb-3">
                        <label for="requirements" class="form-label">Requirements</label>
                        <textarea class="form-control @error('requirements') is-invalid @enderror"
                                  id="requirements"
                                  name="requirements"
                                  rows="3"
                                  placeholder="List technical requirements, prerequisites, skills needed...">{{ old('requirements') }}</textarea>
                        @error('requirements')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Specify technical skills, tools, or prior knowledge required for this project.
                        </div>
                    </div>

                    <!-- Objectives -->
                    <div class="mb-3">
                        <label for="objectives" class="form-label">Learning Objectives</label>
                        <textarea class="form-control @error('objectives') is-invalid @enderror"
                                  id="objectives"
                                  name="objectives"
                                  rows="3"
                                  placeholder="What will students learn from this project?">{{ old('objectives') }}</textarea>
                        @error('objectives')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Describe the educational goals and skills students will develop.
                        </div>
                    </div>

                    <!-- Expected Deliverables -->
                    <div class="mb-3">
                        <label for="expected_deliverables" class="form-label">Expected Deliverables</label>
                        <textarea class="form-control @error('expected_deliverables') is-invalid @enderror"
                                  id="expected_deliverables"
                                  name="expected_deliverables"
                                  rows="3"
                                  placeholder="List the expected deliverables (reports, software, prototypes...)">{{ old('expected_deliverables') }}</textarea>
                        @error('expected_deliverables')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Specify what students should deliver at the end of the project.
                        </div>
                    </div>

                    <div class="row">
                        <!-- Department -->
                        <div class="col-md-6 mb-3">
                            <label for="department" class="form-label required">Department</label>
                            <select class="form-select @error('department') is-invalid @enderror"
                                    id="department"
                                    name="department"
                                    required>
                                <option value="">Select Department</option>
                                <option value="Computer Science" {{ old('department') === 'Computer Science' ? 'selected' : '' }}>Computer Science</option>
                                <option value="Engineering" {{ old('department') === 'Engineering' ? 'selected' : '' }}>Engineering</option>
                                <option value="Mathematics" {{ old('department') === 'Mathematics' ? 'selected' : '' }}>Mathematics</option>
                                <option value="Physics" {{ old('department') === 'Physics' ? 'selected' : '' }}>Physics</option>
                            </select>
                            @error('department')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Academic Level -->
                        <div class="col-md-6 mb-3">
                            <label for="level" class="form-label required">Academic Level</label>
                            <select class="form-select @error('level') is-invalid @enderror"
                                    id="level"
                                    name="level"
                                    required>
                                <option value="">Select Level</option>
                                <option value="license" {{ old('level') === 'license' ? 'selected' : '' }}>License (Bachelor)</option>
                                <option value="master" {{ old('level') === 'master' ? 'selected' : '' }}>Master</option>
                            </select>
                            @error('level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <!-- Maximum Teams -->
                        <div class="col-md-6 mb-3">
                            <label for="max_teams" class="form-label">Maximum Teams</label>
                            <input type="number"
                                   class="form-control @error('max_teams') is-invalid @enderror"
                                   id="max_teams"
                                   name="max_teams"
                                   value="{{ old('max_teams', 1) }}"
                                   min="1"
                                   max="10"
                                   placeholder="1">
                            @error('max_teams')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                How many teams can work on this subject? (Leave 1 for exclusive subjects)
                            </div>
                        </div>

                        <!-- Tags/Keywords -->
                        <div class="col-md-6 mb-3">
                            <label for="tags" class="form-label">Tags/Keywords</label>
                            <input type="text"
                                   class="form-control @error('tags') is-invalid @enderror"
                                   id="tags"
                                   name="tags"
                                   value="{{ old('tags') }}"
                                   placeholder="AI, Machine Learning, Web Development...">
                            @error('tags')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Add relevant tags separated by commas to help students find this subject.
                            </div>
                        </div>
                    </div>

                    <!-- External Project Section -->
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_external" name="is_external" value="1" {{ old('is_external') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_external">
                                This is an external project (with industry partner)
                            </label>
                        </div>
                    </div>

                    <!-- External Project Details (hidden by default) -->
                    <div id="external-details" class="d-none">
                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <h6 class="card-title">External Project Information</h6>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="company_name" class="form-label">Company/Organization</label>
                                        <input type="text"
                                               class="form-control @error('company_name') is-invalid @enderror"
                                               id="company_name"
                                               name="company_name"
                                               value="{{ old('company_name') }}"
                                               placeholder="Company or organization name">
                                        @error('company_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="external_supervisor_name" class="form-label">External Supervisor</label>
                                        <input type="text"
                                               class="form-control @error('external_supervisor_name') is-invalid @enderror"
                                               id="external_supervisor_name"
                                               name="external_supervisor_name"
                                               value="{{ old('external_supervisor_name') }}"
                                               placeholder="Name of external supervisor">
                                        @error('external_supervisor_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="external_supervisor_email" class="form-label">Supervisor Email</label>
                                        <input type="email"
                                               class="form-control @error('external_supervisor_email') is-invalid @enderror"
                                               id="external_supervisor_email"
                                               name="external_supervisor_email"
                                               value="{{ old('external_supervisor_email') }}"
                                               placeholder="supervisor@company.com">
                                        @error('external_supervisor_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="external_supervisor_phone" class="form-label">Supervisor Phone</label>
                                        <input type="tel"
                                               class="form-control @error('external_supervisor_phone') is-invalid @enderror"
                                               id="external_supervisor_phone"
                                               name="external_supervisor_phone"
                                               value="{{ old('external_supervisor_phone') }}"
                                               placeholder="+213 XXX XXX XXX">
                                        @error('external_supervisor_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="project_context" class="form-label">Project Context</label>
                                    <textarea class="form-control @error('project_context') is-invalid @enderror"
                                              id="project_context"
                                              name="project_context"
                                              rows="3"
                                              placeholder="Describe the business context and real-world application...">{{ old('project_context') }}</textarea>
                                    @error('project_context')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('subjects.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Cancel
                                </a>
                                <div>
                                    <button type="submit" name="action" value="draft" class="btn btn-outline-primary me-2">
                                        <i class="bi bi-file-earmark me-2"></i>Save as Draft
                                    </button>
                                    <button type="submit" name="action" value="submit" class="btn btn-primary">
                                        <i class="bi bi-send me-2"></i>Submit for Validation
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.required::after {
    content: " *";
    color: red;
}

.form-check-input:checked {
    background-color: var(--bs-primary);
    border-color: var(--bs-primary);
}

.card.bg-light {
    border-left: 4px solid var(--bs-primary);
}

.form-text {
    font-size: 0.875rem;
    color: #6c757d;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const isExternalCheckbox = document.getElementById('is_external');
    const externalDetails = document.getElementById('external-details');

    // Toggle external project details
    isExternalCheckbox.addEventListener('change', function() {
        if (this.checked) {
            externalDetails.classList.remove('d-none');
            // Make external fields required
            document.getElementById('company_name').required = true;
            document.getElementById('external_supervisor_name').required = true;
            document.getElementById('external_supervisor_email').required = true;
        } else {
            externalDetails.classList.add('d-none');
            // Remove required attribute from external fields
            document.getElementById('company_name').required = false;
            document.getElementById('external_supervisor_name').required = false;
            document.getElementById('external_supervisor_email').required = false;
        }
    });

    // Auto-resize textareas
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    });

    // Form validation
    const form = document.getElementById('createSubjectForm');
    form.addEventListener('submit', function(e) {
        const submitButton = e.submitter;
        const action = submitButton.getAttribute('name') === 'action' ? submitButton.value : 'draft';

        // Add action to form data
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = action;
        form.appendChild(actionInput);

        // Show loading state
        submitButton.disabled = true;
        const originalText = submitButton.innerHTML;
        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

        // Re-enable button after 3 seconds (in case of validation errors)
        setTimeout(() => {
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        }, 3000);
    });

    // Character counter for title
    const titleInput = document.getElementById('title');
    const titleCounter = document.createElement('div');
    titleCounter.className = 'form-text text-end';
    titleInput.parentNode.appendChild(titleCounter);

    titleInput.addEventListener('input', function() {
        const length = this.value.length;
        const maxLength = 200;
        titleCounter.textContent = `${length}/${maxLength} characters`;

        if (length > maxLength * 0.9) {
            titleCounter.classList.add('text-warning');
        } else {
            titleCounter.classList.remove('text-warning');
        }
    });

    // Initialize character counter
    titleInput.dispatchEvent(new Event('input'));

    // Check if editing and show external details if needed
    if (isExternalCheckbox.checked) {
        isExternalCheckbox.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush