@extends('layouts.pfe-app')

@section('page-title', __('app.create_subject'))

@section('content')
<div class="container-fluid py-3">
    <!-- Modern Page Header -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1 fw-bold text-primary">
                        <i class="bi bi-journal-plus me-2"></i>
                        {{ __('app.create_new_subject') }}
                    </h1>
                    <p class="text-muted mb-0 small">{{ __('app.fill_required_fields_marked') }}</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#guideModal">
                        <i class="bi bi-book me-1"></i> {{ __('app.full_guide') }}
                    </button>
                    <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#pageHelpModal">
                        <i class="bi bi-question-circle"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <form id="createSubjectForm" method="POST" action="{{ route('subjects.store') }}">
        @csrf

        <!-- Basic Information Section -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        {{ __('app.basic_information') }}
                    </h5>
                    <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#basicInfoModal">
                        <i class="bi bi-question-circle me-1"></i> {{ __('app.help') }}
                    </button>
                </div>
            </div>
            <div class="card-body p-4">
                <!-- Title -->
                <div class="mb-4">
                    <label for="title" class="form-label fw-semibold">
                        <i class="bi bi-card-heading text-primary me-2"></i>
                        {{ __('app.subject_title') }}
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           class="form-control form-control-lg @error('title') is-invalid @enderror"
                           id="title"
                           name="title"
                           value="{{ old('title') }}"
                           required
                           maxlength="200"
                           placeholder="{{ __('app.enter_clear_descriptive_title') }}">
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <label for="description" class="form-label fw-semibold">
                        <i class="bi bi-file-text text-primary me-2"></i>
                        {{ __('app.description') }}
                        <span class="text-danger">*</span>
                    </label>
                    <textarea class="form-control @error('description') is-invalid @enderror"
                              id="description"
                              name="description"
                              rows="5"
                              required
                              placeholder="{{ __('app.provide_detailed_description') }}">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Keywords & Tools - Two Columns -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="keywords" class="form-label fw-semibold">
                            <i class="bi bi-tags text-primary me-2"></i>
                            {{ __('app.keywords') }}
                            <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control @error('keywords') is-invalid @enderror"
                                  id="keywords"
                                  name="keywords"
                                  rows="3"
                                  required
                                  placeholder="{{ __('app.keywords_placeholder') }}">{{ old('keywords') }}</textarea>
                        <small class="form-text text-muted">{{ __('app.separate_with_commas') }}</small>
                        @error('keywords')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="tools" class="form-label fw-semibold">
                            <i class="bi bi-gear text-primary me-2"></i>
                            {{ __('app.tools_technologies') }}
                            <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control @error('tools') is-invalid @enderror"
                                  id="tools"
                                  name="tools"
                                  rows="3"
                                  required
                                  placeholder="{{ __('app.tools_placeholder') }}">{{ old('tools') }}</textarea>
                        <small class="form-text text-muted">{{ __('app.separate_with_commas') }}</small>
                        @error('tools')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Project Plan -->
                <div class="mb-0">
                    <label for="plan" class="form-label fw-semibold">
                        <i class="bi bi-diagram-3 text-primary me-2"></i>
                        {{ __('app.project_plan') }}
                        <span class="text-danger">*</span>
                    </label>
                    <textarea class="form-control @error('plan') is-invalid @enderror"
                              id="plan"
                              name="plan"
                              rows="5"
                              required
                              placeholder="{{ __('app.describe_project_phases') }}">{{ old('plan') }}</textarea>
                    @error('plan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <!-- Project bibliograhy -->
                <div class="mb-0">
                    <label for="plan" class="form-label fw-semibold">
                        <i class="bi bi-diagram-3 text-primary me-2"></i>
                        {{ __('app.bibliograhy') }}
                        <span class="text-danger">*</span>
                    </label>
                    <textarea class="form-control @error('bibliograhy') is-invalid @enderror"
                              id="bibliograhy"
                              name="bibliograhy"
                              rows="5"
                              required
                              placeholder="{{ __('app.describe_bibliograhy') }}">{{ old('bibliograhy') }}</textarea>
                    @error('plan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        @if(auth()->user()->role === 'teacher')
        <!-- Teacher-specific Configuration -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-sliders text-info me-2"></i>
                        {{ __('app.configuration') }}
                    </h5>
                    <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#configModal">
                        <i class="bi bi-question-circle me-1"></i> {{ __('app.help') }}
                    </button>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row g-3 mb-4">
                    <!-- Target Grade -->
                    <div class="col-md-6">
                        <label for="target_grade" class="form-label fw-semibold">
                            <i class="bi bi-mortarboard text-info me-2"></i>
                            {{ __('app.target_grade') }}
                            <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('target_grade') is-invalid @enderror"
                                id="target_grade"
                                name="target_grade"
                                required>
                            <option value="">{{ __('app.select') }}...</option>
                            <option value="L3" {{ old('target_grade') === 'L3' ? 'selected' : '' }}>{{ __('app.license_3') }} (L3)</option>
                            <option value="M1" {{ old('target_grade') === 'M1' ? 'selected' : '' }}>{{ __('app.master_1') }} (M1)</option>
                            <option value="M2" {{ old('target_grade') === 'M2' ? 'selected' : '' }}>{{ __('app.master_2') }} (M2)</option>
                        </select>
                        @error('target_grade')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Co-Supervisor -->
                    <div class="col-md-6">
                        <label for="co_supervisor_name" class="form-label fw-semibold">
                            <i class="bi bi-person-plus text-info me-2"></i>
                            {{ __('app.co_supervisor') }}
                            <small class="text-muted">({{ __('app.optional') }})</small>
                        </label>
                        <input type="text"
                               class="form-control @error('co_supervisor_name') is-invalid @enderror"
                               id="co_supervisor_name"
                               name="co_supervisor_name"
                               value="{{ old('co_supervisor_name') }}"
                               placeholder="{{ __('app.optional') }}">
                        @error('co_supervisor_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Specialities -->
                <div class="mb-4">
                    <label for="specialities" class="form-label fw-semibold">
                        <i class="bi bi-diagram-2 text-info me-2"></i>
                        {{ __('app.target_specialities') }}
                        <span class="text-danger">*</span>
                    </label>
                    <select class="form-select @error('specialities') is-invalid @enderror"
                            id="specialities"
                            name="specialities[]"
                            multiple
                            required
                            size="4">
                        @foreach($specialities as $speciality)
                            <option value="{{ $speciality->id }}"
                                {{ in_array($speciality->id, old('specialities', [])) ? 'selected' : '' }}>
                                {{ $speciality->name }} ({{ $speciality->level }})
                            </option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        {{ __('app.hold_ctrl_to_select_multiple') }}
                    </small>
                    @error('specialities')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Subject Type -->
                <div class="mb-0">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-tag text-info me-2"></i>
                        {{ __('app.subject_type') }}
                        <span class="text-danger">*</span>
                    </label>
                    <div class="d-flex gap-4">
                        <div class="form-check form-check-lg">
                            <input class="form-check-input" type="radio" name="type" id="type_internal"
                                   value="internal" {{ old('type', 'internal') === 'internal' ? 'checked' : '' }} required>
                            <label class="form-check-label fw-normal" for="type_internal">
                                <i class="bi bi-building me-1"></i> {{ __('app.internal') }}
                            </label>
                        </div>
                        <div class="form-check form-check-lg">
                            <input class="form-check-input" type="radio" name="type" id="type_external"
                                   value="external" {{ old('type') === 'external' ? 'checked' : '' }}>
                            <label class="form-check-label fw-normal" for="type_external">
                                <i class="bi bi-briefcase me-1"></i> {{ __('app.external') }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if(auth()->user()->role === 'student')
        <!-- External Subject Information (Students) -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-building text-success me-2"></i>
                        {{ __('app.external_subject_information') }}
                    </h5>
                    <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#externalInfoModal">
                        <i class="bi bi-question-circle me-1"></i> {{ __('app.help') }}
                    </button>
                </div>
            </div>
            <div class="card-body p-4">
                <!-- Company Name & Dataset Link -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="company_name" class="form-label fw-semibold">
                            <i class="bi bi-building text-success me-2"></i>
                            {{ __('app.company_name') }}
                            <small class="text-muted">({{ __('app.optional') }})</small>
                        </label>
                        <input type="text"
                               class="form-control @error('company_name') is-invalid @enderror"
                               id="company_name"
                               name="company_name"
                               value="{{ old('company_name') }}"
                               placeholder="{{ __('app.company_name_placeholder') }}">
                        @error('company_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="dataset_resources_link" class="form-label fw-semibold">
                            <i class="bi bi-link-45deg text-success me-2"></i>
                            {{ __('app.resources_link') }}
                            <small class="text-muted">({{ __('app.optional') }})</small>
                        </label>
                        <input type="url"
                               class="form-control @error('dataset_resources_link') is-invalid @enderror"
                               id="dataset_resources_link"
                               name="dataset_resources_link"
                               value="{{ old('dataset_resources_link') }}"
                               placeholder="{{ __('app.url_placeholder') }}">
                        @error('dataset_resources_link')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- External Supervisor -->
                <div class="border-start border-success border-4 ps-3 mb-4">
                    <h6 class="mb-3 fw-bold text-dark">
                        <i class="bi bi-person-badge text-success me-2"></i>
                        {{ __('app.external_supervisor') }}
                    </h6>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="external_supervisor_name" class="form-label fw-semibold">
                                {{ __('app.full_name') }}
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="form-control @error('external_supervisor_name') is-invalid @enderror"
                                   id="external_supervisor_name"
                                   name="external_supervisor_name"
                                   value="{{ old('external_supervisor_name') }}"
                                   placeholder="{{ __('app.supervisor_name_placeholder') }}">
                            @error('external_supervisor_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="external_supervisor_email" class="form-label fw-semibold">
                                {{ __('app.email') }}
                                <span class="text-danger">*</span>
                            </label>
                            <input type="email"
                                   class="form-control @error('external_supervisor_email') is-invalid @enderror"
                                   id="external_supervisor_email"
                                   name="external_supervisor_email"
                                   value="{{ old('external_supervisor_email') }}"
                                   placeholder="{{ __('app.email_placeholder') }}">
                            @error('external_supervisor_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="external_supervisor_phone" class="form-label fw-semibold">
                                {{ __('app.phone') }}
                                <small class="text-muted">({{ __('app.optional') }})</small>
                            </label>
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

                        <div class="col-md-6">
                            <label for="external_supervisor_position" class="form-label fw-semibold">
                                {{ __('app.position') }}
                                <small class="text-muted">({{ __('app.optional') }})</small>
                            </label>
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

                <!-- Specialities for Students -->
                <div class="mb-0">
                    <label for="specialities_student" class="form-label fw-semibold">
                        <i class="bi bi-diagram-2 text-success me-2"></i>
                        {{ __('app.target_specialities') }}
                        <span class="text-danger">*</span>
                    </label>
                    <select class="form-select @error('specialities') is-invalid @enderror"
                            id="specialities_student"
                            name="specialities[]"
                            multiple
                            required
                            size="4">
                        @foreach($specialities as $speciality)
                            <option value="{{ $speciality->id }}"
                                {{ in_array($speciality->id, old('specialities', [])) ? 'selected' : '' }}>
                                {{ $speciality->name }} ({{ $speciality->level }})
                            </option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        {{ __('app.hold_ctrl_to_select_multiple') }}
                    </small>
                    @error('specialities')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
        @endif

        <!-- Form Actions -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <a href="{{ route('subjects.index') }}" class="btn btn-outline-secondary btn-lg">
                        <i class="bi bi-arrow-left me-2"></i>
                        {{ __('app.cancel') }}
                    </a>
                    <div class="d-flex gap-3">
                        <button type="submit" name="action" value="draft" class="btn btn-outline-primary btn-lg">
                            <i class="bi bi-file-earmark me-2"></i>
                            {{ __('app.save_draft') }}
                        </button>
                        @if(auth()->user()->role === 'teacher')
                            <button type="submit" name="action" value="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-circle me-2"></i>
                                {{ __('app.submit_validation') }}
                            </button>
                        @else
                            <button type="submit" name="action" value="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-send-check me-2"></i>
                                {{ __('app.submit_validation') }}
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('styles')
<style>
    /* Enhanced form styling */
    .form-control-lg {
        font-size: 1.05rem;
        padding: 0.75rem 1rem;
    }

    .form-label {
        margin-bottom: 0.5rem;
    }

    .form-select[multiple] {
        border-radius: 0.375rem;
    }

    .form-select[multiple] option {
        padding: 0.5rem;
        margin: 0.2rem 0;
    }

    .form-select[multiple] option:hover {
        background-color: #f0f0f0;
    }

    .card {
        transition: box-shadow 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
    }

    .btn-lg {
        padding: 0.75rem 1.5rem;
        font-size: 1rem;
        font-weight: 500;
    }

    textarea.form-control {
        resize: vertical;
    }

    .border-start.border-4 {
        padding-left: 1.5rem !important;
    }

    /* Form check enhancement */
    .form-check-lg .form-check-input {
        width: 1.5rem;
        height: 1.5rem;
        margin-top: 0.15rem;
    }

    .form-check-lg .form-check-label {
        font-size: 1rem;
        padding-left: 0.5rem;
    }
</style>
@endpush

<!-- Info Modals -->
<x-info-modal id="guideModal" title="{{ __('app.complete_guide') }}" icon="bi-book">
    <h6>{{ __('app.subject_creation_guide') }}</h6>
    <p>{{ __('app.guide_description_text') }}</p>

    <h6>{{ __('app.required_fields') }}</h6>
    <ul>
        <li><strong>{{ __('app.title') }}:</strong> {{ __('app.title_guide') }}</li>
        <li><strong>{{ __('app.description') }}:</strong> {{ __('app.description_guide') }}</li>
        <li><strong>{{ __('app.keywords') }}:</strong> {{ __('app.keywords_guide') }}</li>
        <li><strong>{{ __('app.tools') }}:</strong> {{ __('app.tools_guide') }}</li>
        <li><strong>{{ __('app.plan') }}:</strong> {{ __('app.plan_guide') }}</li>
    </ul>
</x-info-modal>

<x-info-modal id="basicInfoModal" title="{{ __('app.basic_information_help') }}" icon="bi-info-circle">
    <h6>{{ __('app.subject_title') }}</h6>
    <p>{{ __('app.title_help_detailed') }}</p>

    <h6>{{ __('app.description') }}</h6>
    <p>{{ __('app.description_help_detailed') }}</p>

    <h6>{{ __('app.keywords') }}</h6>
    <p>{{ __('app.keywords_help_detailed') }}</p>
</x-info-modal>

@if(auth()->user()->role === 'teacher')
<x-info-modal id="configModal" title="{{ __('app.configuration_help') }}" icon="bi-sliders">
    <h6>{{ __('app.target_grade') }}</h6>
    <p>{{ __('app.target_grade_help_detailed') }}</p>

    <h6>{{ __('app.specialities') }}</h6>
    <p>{{ __('app.specialities_help_detailed') }}</p>

    <h6>{{ __('app.subject_type') }}</h6>
    <p>{{ __('app.subject_type_help_detailed') }}</p>
</x-info-modal>
@endif

@if(auth()->user()->role === 'student')
<x-info-modal id="externalInfoModal" title="{{ __('app.external_subject_help') }}" icon="bi-building">
    <h6>{{ __('app.what_is_external_subject') }}</h6>
    <p>{{ __('app.external_subject_explanation') }}</p>

    <h6>{{ __('app.external_supervisor_role') }}</h6>
    <p>{{ __('app.external_supervisor_explanation') }}</p>
</x-info-modal>
@endif

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('createSubjectForm');

    // Form submission with loading state
    form.addEventListener('submit', function(e) {
        const submitButton = e.submitter;
        submitButton.disabled = true;
        submitButton.classList.add('btn-loading-compact');

        // Re-enable after 5 seconds in case of validation errors
        setTimeout(() => {
            submitButton.disabled = false;
            submitButton.classList.remove('btn-loading-compact');
        }, 5000);
    });

    // Auto-resize textareas
    document.querySelectorAll('textarea').forEach(textarea => {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    });
});
</script>
@endpush

<!-- Page Help Modal -->
<x-info-modal id="pageHelpModal" title="{{ __('app.create_subject_help') }}" icon="bi-journal-plus">
    <h6>{{ __('app.what_is_this_page') }}</h6>
    <p>{{ __('app.create_subject_page_description') }}</p>

    <h6>{{ __('app.how_to_use') }}</h6>
    <ul>
        <li><strong>{{ __('app.fill_basic_info') }}:</strong> {{ __('app.fill_basic_info_help') }}</li>
        <li><strong>{{ __('app.select_target_audience') }}:</strong> {{ __('app.select_target_audience_help') }}</li>
        <li><strong>{{ __('app.add_keywords_tools') }}:</strong> {{ __('app.add_keywords_tools_help') }}</li>
        <li><strong>{{ __('app.define_project_plan') }}:</strong> {{ __('app.define_project_plan_help') }}</li>
        <li><strong>{{ __('app.save_or_submit') }}:</strong> {{ __('app.save_or_submit_help') }}</li>
    </ul>

    <h6>{{ __('app.subject_types') }}</h6>
    <ul>
        <li><strong>{{ __('app.internal_subject') }}:</strong> {{ __('app.internal_subject_description') }}</li>
        <li><strong>{{ __('app.external_subject') }}:</strong> {{ __('app.external_subject_description') }}</li>
    </ul>

    <h6>{{ __('app.important_notes') }}</h6>
    <ul>
        <li>{{ __('app.create_subject_note_1') }}</li>
        <li>{{ __('app.create_subject_note_2') }}</li>
        <li>{{ __('app.create_subject_note_3') }}</li>
    </ul>
</x-info-modal>
