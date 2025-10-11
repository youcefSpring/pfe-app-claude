@extends('layouts.pfe-app')

@section('page-title', 'Create New Subject')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
            </div>
            <div class="card-body">
                <form id="createSubjectForm" method="POST" action="{{ route('subjects.store') }}">
                    @csrf

                    <!-- Title -->
                    <div class="mb-3">
                        <label for="title" class="form-label required">{{ __('app.subject_title') }}</label>
                        <input type="text"
                               class="form-control @error('title') is-invalid @enderror"
                               id="title"
                               name="title"
                               value="{{ old('title') }}"
                               required
                               placeholder="{{ __('app.enter_clear_descriptive_title') }}">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            {{ __('app.choose_title_clearly_describes') }}
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label required">{{ __('app.description') }}</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description"
                                  name="description"
                                  rows="4"
                                  required
                                  placeholder="{{ __('app.provide_detailed_description') }}">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            {{ __('app.describe_project_context') }}
                        </div>
                    </div>

                    <!-- Keywords -->
                    <div class="mb-3">
                        <label for="keywords" class="form-label required">{{ __('app.keywords') }}</label>
                        <textarea class="form-control @error('keywords') is-invalid @enderror"
                                  id="keywords"
                                  name="keywords"
                                  rows="2"
                                  required
                                  placeholder="{{ __('app.enter_keywords_comma_separated') }}">{{ old('keywords') }}</textarea>
                        @error('keywords')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            {{ __('app.keywords_help_text') }}
                        </div>
                    </div>

                    <!-- Tools -->
                    <div class="mb-3">
                        <label for="tools" class="form-label required">{{ __('app.tools_technologies') }}</label>
                        <textarea class="form-control @error('tools') is-invalid @enderror"
                                  id="tools"
                                  name="tools"
                                  rows="2"
                                  required
                                  placeholder="{{ __('app.list_tools_technologies') }}">{{ old('tools') }}</textarea>
                        @error('tools')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            {{ __('app.tools_help_text') }}
                        </div>
                    </div>

                    <!-- Plan -->
                    <div class="mb-3">
                        <label for="plan" class="form-label required">{{ __('app.project_plan') }}</label>
                        <textarea class="form-control @error('plan') is-invalid @enderror"
                                  id="plan"
                                  name="plan"
                                  rows="4"
                                  required
                                  placeholder="{{ __('app.describe_project_phases') }}">{{ old('plan') }}</textarea>
                        @error('plan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            {{ __('app.project_plan_help_text') }}
                        </div>
                    </div>

                    @if(auth()->user()->role === 'teacher')
                    <!-- Teacher-specific fields -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="target_grade" class="form-label required">{{ __('app.target_grade') }}</label>
                                <select class="form-select @error('target_grade') is-invalid @enderror"
                                        id="target_grade"
                                        name="target_grade"
                                        required>
                                    <option value="">{{ __('app.select_target_grade') }}</option>
                                    <option value="L3" {{ old('target_grade') === 'L3' ? 'selected' : '' }}>{{ __('app.license_3') }} (L3)</option>
                                    <option value="M1" {{ old('target_grade') === 'M1' ? 'selected' : '' }}>{{ __('app.master_1') }} (M1)</option>
                                    <option value="M2" {{ old('target_grade') === 'M2' ? 'selected' : '' }}>{{ __('app.master_2') }} (M2)</option>
                                </select>
                                @error('target_grade')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    {{ __('app.target_grade_help_text') }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="co_supervisor_name" class="form-label">{{ __('app.co_supervisor') }} ({{ __('app.optional') }})</label>
                                <input type="text"
                                       class="form-control @error('co_supervisor_name') is-invalid @enderror"
                                       id="co_supervisor_name"
                                       name="co_supervisor_name"
                                       value="{{ old('co_supervisor_name') }}"
                                       placeholder="{{ __('app.enter_co_supervisor_name') }}">
                                @error('co_supervisor_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    {{ __('app.co_supervisor_help_text') }}
                                </div>
                            </div>
                        </div>
                        <!-- Max Teams Hidden Field - Fixed to 1 -->
                        <input type="hidden" name="max_teams" value="1">
                    </div>

                    <!-- Specialities Selection -->
                    <div class="mb-3">
                        <label for="specialities" class="form-label required">{{ __('app.target_specialities') }}</label>
                        <select class="form-select @error('specialities') is-invalid @enderror"
                                id="specialities"
                                name="specialities[]"
                                multiple
                                required>
                            @foreach($specialities as $speciality)
                                <option value="{{ $speciality->id }}"
                                    {{ in_array($speciality->id, old('specialities', [])) ? 'selected' : '' }}>
                                    {{ $speciality->name }} ({{ $speciality->level }})
                                </option>
                            @endforeach
                        </select>
                        @error('specialities')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            {{ __('app.select_specialities_can_work_on_subject') }}
                        </div>
                    </div>

                    <!-- Subject Type for Teachers -->
                    <div class="mb-3">
                        <label class="form-label required">{{ __('app.subject_type') }}</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" id="type_internal" value="internal" {{ old('type', 'internal') === 'internal' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="type_internal">
                                        <i class="bi bi-building me-2"></i>{{ __('app.internal_project') }}
                                    </label>
                                </div>
                                <small class="text-muted">{{ __('app.internal_project_description') }}</small>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" id="type_external" value="external" {{ old('type') === 'external' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="type_external">
                                        <i class="bi bi-briefcase me-2"></i>{{ __('app.external_project') }}
                                    </label>
                                </div>
                                <small class="text-muted">{{ __('app.external_project_description') }}</small>
                            </div>
                        </div>
                    </div>

                    <!-- Prerequisites -->
                    <div class="mb-3">
                        <label for="prerequisites" class="form-label">{{ __('app.prerequisites') }}</label>
                        <textarea class="form-control @error('prerequisites') is-invalid @enderror"
                                  id="prerequisites"
                                  name="prerequisites"
                                  rows="3"
                                  placeholder="{{ __('app.list_prerequisites') }}">{{ old('prerequisites') }}</textarea>
                        @error('prerequisites')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            {{ __('app.prerequisites_help_text') }}
                        </div>
                    </div>
                    @endif

                    @if(auth()->user()->role === 'student')
                    <!-- External Subject Section for Students -->
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_external" name="is_external" value="1" {{ old('is_external', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_external">
                                {{ __('app.this_is_external_subject') }}
                            </label>
                        </div>
                        <div class="form-text">
                            {{ __('app.external_subjects_description') }}
                        </div>
                    </div>

                    <!-- External Subject Details -->
                    <div id="external-details">
                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <h6 class="card-title">{{ __('app.external_subject_information') }}</h6>

                                <div class="mb-3">
                                    <label for="company_name" class="form-label">{{ __('app.company_organization_name') }}</label>
                                    <input type="text"
                                           class="form-control @error('company_name') is-invalid @enderror"
                                           id="company_name"
                                           name="company_name"
                                           value="{{ old('company_name') }}"
                                           placeholder="{{ __('app.enter_company_name') }}">
                                    @error('company_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        {{ __('app.company_name_help_text') }}
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="dataset_resources_link" class="form-label">{{ __('app.dataset_resources_link') }}</label>
                                    <input type="url"
                                           class="form-control @error('dataset_resources_link') is-invalid @enderror"
                                           id="dataset_resources_link"
                                           name="dataset_resources_link"
                                           value="{{ old('dataset_resources_link') }}"
                                           placeholder="{{ __('app.dataset_link_placeholder') }}">
                                    @error('dataset_resources_link')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        {{ __('app.dataset_link_help_text') }}
                                    </div>
                                </div>

                                <!-- External Supervisor Information -->
                                <div class="card bg-info bg-opacity-10 mt-3">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="bi bi-person-plus-fill me-2"></i>{{ __('app.external_supervisor_information') }}
                                        </h6>
                                        <p class="small text-muted mb-3">
                                            {{ __('app.external_supervisor_description') }}
                                        </p>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="external_supervisor_name" class="form-label required">{{ __('app.supervisor_name') }}</label>
                                                    <input type="text"
                                                           class="form-control @error('external_supervisor_name') is-invalid @enderror"
                                                           id="external_supervisor_name"
                                                           name="external_supervisor_name"
                                                           value="{{ old('external_supervisor_name') }}"
                                                           placeholder="{{ __('app.full_name_supervisor') }}">
                                                    @error('external_supervisor_name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="external_supervisor_email" class="form-label required">{{ __('app.supervisor_email') }}</label>
                                                    <input type="email"
                                                           class="form-control @error('external_supervisor_email') is-invalid @enderror"
                                                           id="external_supervisor_email"
                                                           name="external_supervisor_email"
                                                           value="{{ old('external_supervisor_email') }}"
                                                           placeholder="{{ __('app.supervisor_email_placeholder') }}">
                                                    @error('external_supervisor_email')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="external_supervisor_phone" class="form-label">{{ __('app.phone_number') }}</label>
                                                    <input type="tel"
                                                           class="form-control @error('external_supervisor_phone') is-invalid @enderror"
                                                           id="external_supervisor_phone"
                                                           name="external_supervisor_phone"
                                                           value="{{ old('external_supervisor_phone') }}"
                                                           placeholder="{{ __('app.phone_placeholder') }}">
                                                    @error('external_supervisor_phone')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="external_supervisor_position" class="form-label">{{ __('app.position_title') }}</label>
                                                    <input type="text"
                                                           class="form-control @error('external_supervisor_position') is-invalid @enderror"
                                                           id="external_supervisor_position"
                                                           name="external_supervisor_position"
                                                           value="{{ old('external_supervisor_position') }}"
                                                           placeholder="{{ __('app.position_placeholder') }}">
                                                    @error('external_supervisor_position')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Specialities Selection for Students -->
                    <div class="mb-3">
                        <label for="specialities_student" class="form-label required">{{ __('app.target_specialities') }}</label>
                        <select class="form-select @error('specialities') is-invalid @enderror"
                                id="specialities_student"
                                name="specialities[]"
                                multiple
                                required>
                            @foreach($specialities as $speciality)
                                <option value="{{ $speciality->id }}"
                                    {{ in_array($speciality->id, old('specialities', [])) ? 'selected' : '' }}>
                                    {{ $speciality->name }} ({{ $speciality->level }})
                                </option>
                            @endforeach
                        </select>
                        @error('specialities')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            {{ __('app.select_specialities_can_work_on_subject') }}
                        </div>
                    </div>
                    @endif

                    <!-- Actions -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('subjects.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>{{ __('app.cancel') }}
                                </a>
                                <div>
                                    @if(auth()->user()->role === 'teacher')
                                        <button type="submit" name="action" value="draft" class="btn btn-outline-primary me-2">
                                            <i class="bi bi-file-earmark me-2"></i>{{ __('app.save_as_draft') }}
                                        </button>
                                        <button type="submit" name="action" value="submit" class="btn btn-primary">
                                            <i class="bi bi-check-circle me-2"></i>{{ __('app.create_subject') }}
                                        </button>
                                    @else
                                        <button type="submit" name="action" value="draft" class="btn btn-outline-primary me-2">
                                            <i class="bi bi-file-earmark me-2"></i>{{ __('app.save_as_draft') }}
                                        </button>
                                        <button type="submit" name="action" value="submit" class="btn btn-primary">
                                            <i class="bi bi-send me-2"></i>{{ __('app.submit_for_validation') }}
                                        </button>
                                    @endif
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
        const supervisorFields = document.querySelectorAll('#external_supervisor_name, #external_supervisor_email');

        isExternalCheckbox.addEventListener('change', function() {
            if (this.checked) {
                externalDetails.classList.remove('d-none');
                // Make supervisor fields required when external is checked
                supervisorFields.forEach(field => {
                    field.setAttribute('required', 'required');
                });
            } else {
                externalDetails.classList.add('d-none');
                // Remove required attribute when external is unchecked
                supervisorFields.forEach(field => {
                    field.removeAttribute('required');
                });
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