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

                    <!-- Keywords -->
                    <div class="mb-3">
                        <label for="keywords" class="form-label required">Keywords</label>
                        <textarea class="form-control @error('keywords') is-invalid @enderror"
                                  id="keywords"
                                  name="keywords"
                                  rows="2"
                                  required
                                  placeholder="Enter relevant keywords separated by commas...">{{ old('keywords') }}</textarea>
                        @error('keywords')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Add keywords that describe the technologies, concepts, or fields involved.
                        </div>
                    </div>

                    <!-- Tools -->
                    <div class="mb-3">
                        <label for="tools" class="form-label required">Tools & Technologies</label>
                        <textarea class="form-control @error('tools') is-invalid @enderror"
                                  id="tools"
                                  name="tools"
                                  rows="2"
                                  required
                                  placeholder="List the tools, technologies, and frameworks to be used...">{{ old('tools') }}</textarea>
                        @error('tools')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Specify programming languages, frameworks, libraries, or software tools required.
                        </div>
                    </div>

                    <!-- Plan -->
                    <div class="mb-3">
                        <label for="plan" class="form-label required">Project Plan</label>
                        <textarea class="form-control @error('plan') is-invalid @enderror"
                                  id="plan"
                                  name="plan"
                                  rows="4"
                                  required
                                  placeholder="Describe the project phases, milestones, and expected timeline...">{{ old('plan') }}</textarea>
                        @error('plan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Outline the project plan including major phases and deliverables.
                        </div>
                    </div>

                    @if(auth()->user()->role === 'student')
                    <!-- External Subject Section for Students -->
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_external" name="is_external" value="1" {{ old('is_external', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_external">
                                This is an external subject (proposed by student)
                            </label>
                        </div>
                        <div class="form-text">
                            External subjects are proposed by students and require validation from faculty.
                        </div>
                    </div>

                    <!-- External Subject Details -->
                    <div id="external-details">
                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <h6 class="card-title">External Subject Information</h6>

                                <div class="mb-3">
                                    <label for="company_name" class="form-label">Company/Organization Name</label>
                                    <input type="text"
                                           class="form-control @error('company_name') is-invalid @enderror"
                                           id="company_name"
                                           name="company_name"
                                           value="{{ old('company_name') }}"
                                           placeholder="Enter company or organization name (if applicable)">
                                    @error('company_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        If this subject involves a company or external organization, enter their name.
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="dataset_resources_link" class="form-label">Dataset/Resources Link</label>
                                    <input type="url"
                                           class="form-control @error('dataset_resources_link') is-invalid @enderror"
                                           id="dataset_resources_link"
                                           name="dataset_resources_link"
                                           value="{{ old('dataset_resources_link') }}"
                                           placeholder="https://example.com/dataset (optional)">
                                    @error('dataset_resources_link')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Optional: Link to datasets, resources, or documentation relevant to the project.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

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

    // Toggle external subject details (for students only)
    if (isExternalCheckbox) {
        isExternalCheckbox.addEventListener('change', function() {
            if (this.checked) {
                externalDetails.classList.remove('d-none');
            } else {
                externalDetails.classList.add('d-none');
            }
        });
    }

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
    if (isExternalCheckbox && isExternalCheckbox.checked) {
        isExternalCheckbox.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush