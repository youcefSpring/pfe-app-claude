@extends('layouts.pfe-app')

@section('page-title', __('app.create_subject'))

@section('content')
<div class="container-fluid">
    <!-- Compact Page Header -->
    <div class="page-header-compact">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="mb-0">
                <i class="bi bi-journal-plus"></i>
                {{ __('app.create_new_subject') }}
            </h1>
            <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#pageHelpModal">
                <i class="bi bi-question-circle"></i>
            </button>
        </div>
    </div>

    <!-- Compact Info Alert -->
    <div class="alert-compact alert-info-compact border-left-info">
        <i class="bi bi-info-circle"></i>
        <strong>{{ __('app.quick_guide') }}:</strong>
        {{ __('app.fill_required_fields_marked') }}
        <button type="button" class="btn btn-xs btn-info-modal float-end" data-bs-toggle="modal" data-bs-target="#guideModal">
            <i class="bi bi-question-circle"></i> {{ __('app.full_guide') }}
        </button>
    </div>

    <form id="createSubjectForm" method="POST" action="{{ route('subjects.store') }}">
        @csrf

        <!-- Basic Information Box -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="bi bi-info-circle"></i>
                    {{ __('app.basic_information') }}
                </h3>
                <button type="button" class="btn btn-xs btn-info float-end" data-bs-toggle="modal" data-bs-target="#basicInfoModal">
                    <i class="bi bi-question-circle"></i> {{ __('app.help') }}
                </button>
            </div>
            <div class="box-body">
                <!-- Title -->
                <div class="form-group-compact">
                    <label for="title" class="form-label-compact required">
                        <i class="bi bi-card-heading"></i>
                        {{ __('app.subject_title') }}
                    </label>
                    <input type="text"
                           class="form-control-compact @error('title') is-invalid @enderror"
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
                <div class="form-group-compact">
                    <label for="description" class="form-label-compact required">
                        <i class="bi bi-file-text"></i>
                        {{ __('app.description') }}
                    </label>
                    <textarea class="form-control-compact @error('description') is-invalid @enderror"
                              id="description"
                              name="description"
                              rows="4"
                              required
                              placeholder="{{ __('app.provide_detailed_description') }}">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror>
                </div>

                <!-- Keywords & Tools - Two Columns -->
                <div class="form-row-2col">
                    <div class="form-group-compact">
                        <label for="keywords" class="form-label-compact required">
                            <i class="bi bi-tags"></i>
                            {{ __('app.keywords') }}
                        </label>
                        <textarea class="form-control-compact @error('keywords') is-invalid @enderror"
                                  id="keywords"
                                  name="keywords"
                                  rows="2"
                                  required
                                  placeholder="AI, Machine Learning, Python...">{{ old('keywords') }}</textarea>
                        @error('keywords')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group-compact">
                        <label for="tools" class="form-label-compact required">
                            <i class="bi bi-gear"></i>
                            {{ __('app.tools_technologies') }}
                        </label>
                        <textarea class="form-control-compact @error('tools') is-invalid @enderror"
                                  id="tools"
                                  name="tools"
                                  rows="2"
                                  required
                                  placeholder="React, Laravel, MySQL...">{{ old('tools') }}</textarea>
                        @error('tools')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Project Plan -->
                <div class="form-group-compact">
                    <label for="plan" class="form-label-compact required">
                        <i class="bi bi-diagram-3"></i>
                        {{ __('app.project_plan') }}
                    </label>
                    <textarea class="form-control-compact @error('plan') is-invalid @enderror"
                              id="plan"
                              name="plan"
                              rows="4"
                              required
                              placeholder="{{ __('app.describe_project_phases') }}">{{ old('plan') }}</textarea>
                    @error('plan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        @if(auth()->user()->role === 'teacher')
        <!-- Teacher-specific Configuration -->
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="bi bi-sliders"></i>
                    {{ __('app.configuration') }}
                </h3>
                <button type="button" class="btn btn-xs btn-info float-end" data-bs-toggle="modal" data-bs-target="#configModal">
                    <i class="bi bi-question-circle"></i> {{ __('app.help') }}
                </button>
            </div>
            <div class="box-body">
                <div class="form-row-2col">
                    <!-- Target Grade -->
                    <div class="form-group-compact">
                        <label for="target_grade" class="form-label-compact required">
                            <i class="bi bi-mortarboard"></i>
                            {{ __('app.target_grade') }}
                        </label>
                        <select class="form-select-compact @error('target_grade') is-invalid @enderror"
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
                    <div class="form-group-compact">
                        <label for="co_supervisor_name" class="form-label-compact">
                            <i class="bi bi-person-plus"></i>
                            {{ __('app.co_supervisor') }}
                        </label>
                        <input type="text"
                               class="form-control-compact @error('co_supervisor_name') is-invalid @enderror"
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
                <div class="form-group-compact">
                    <label for="specialities" class="form-label-compact required">
                        <i class="bi bi-diagram-2"></i>
                        {{ __('app.target_specialities') }}
                    </label>
                    <select class="form-select-compact @error('specialities') is-invalid @enderror"
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
                    <span class="form-text-compact text-muted">
                        <i class="bi bi-info-circle"></i>
                        {{ __('app.hold_ctrl_to_select_multiple') }}
                    </span>
                    @error('specialities')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Subject Type -->
                <div class="form-group-compact">
                    <label class="form-label-compact required">
                        <i class="bi bi-tag"></i>
                        {{ __('app.subject_type') }}
                    </label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="type" id="type_internal"
                                   value="internal" {{ old('type', 'internal') === 'internal' ? 'checked' : '' }} required>
                            <label class="form-check-label" for="type_internal">
                                <i class="bi bi-building"></i> {{ __('app.internal') }}
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="type" id="type_external"
                                   value="external" {{ old('type') === 'external' ? 'checked' : '' }}>
                            <label class="form-check-label" for="type_external">
                                <i class="bi bi-briefcase"></i> {{ __('app.external') }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if(auth()->user()->role === 'student')
        <!-- External Subject Information (Students) -->
        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="bi bi-building"></i>
                    {{ __('app.external_subject_information') }}
                </h3>
                <button type="button" class="btn btn-xs btn-warning float-end" data-bs-toggle="modal" data-bs-target="#externalInfoModal">
                    <i class="bi bi-question-circle"></i> {{ __('app.help') }}
                </button>
            </div>
            <div class="box-body">
                <!-- Company Name & Dataset Link -->
                <div class="form-row-2col">
                    <div class="form-group-compact">
                        <label for="company_name" class="form-label-compact">
                            <i class="bi bi-building"></i>
                            {{ __('app.company_name') }}
                        </label>
                        <input type="text"
                               class="form-control-compact @error('company_name') is-invalid @enderror"
                               id="company_name"
                               name="company_name"
                               value="{{ old('company_name') }}"
                               placeholder="ABC Company">
                        @error('company_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group-compact">
                        <label for="dataset_resources_link" class="form-label-compact">
                            <i class="bi bi-link-45deg"></i>
                            {{ __('app.resources_link') }}
                        </label>
                        <input type="url"
                               class="form-control-compact @error('dataset_resources_link') is-invalid @enderror"
                               id="dataset_resources_link"
                               name="dataset_resources_link"
                               value="{{ old('dataset_resources_link') }}"
                               placeholder="https://...">
                        @error('dataset_resources_link')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- External Supervisor -->
                <div class="form-section-compact">
                    <div class="form-section-header">
                        <h6 class="mb-0">
                            <i class="bi bi-person-badge"></i>
                            {{ __('app.external_supervisor') }}
                        </h6>
                    </div>

                    <div class="form-row-2col">
                        <div class="form-group-compact">
                            <label for="external_supervisor_name" class="form-label-compact required">
                                {{ __('app.full_name') }}
                            </label>
                            <input type="text"
                                   class="form-control-compact @error('external_supervisor_name') is-invalid @enderror"
                                   id="external_supervisor_name"
                                   name="external_supervisor_name"
                                   value="{{ old('external_supervisor_name') }}"
                                   placeholder="Dr. John Doe">
                            @error('external_supervisor_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-compact">
                            <label for="external_supervisor_email" class="form-label-compact required">
                                {{ __('app.email') }}
                            </label>
                            <input type="email"
                                   class="form-control-compact @error('external_supervisor_email') is-invalid @enderror"
                                   id="external_supervisor_email"
                                   name="external_supervisor_email"
                                   value="{{ old('external_supervisor_email') }}"
                                   placeholder="john@company.com">
                            @error('external_supervisor_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-compact">
                            <label for="external_supervisor_phone" class="form-label-compact">
                                {{ __('app.phone') }}
                            </label>
                            <input type="tel"
                                   class="form-control-compact @error('external_supervisor_phone') is-invalid @enderror"
                                   id="external_supervisor_phone"
                                   name="external_supervisor_phone"
                                   value="{{ old('external_supervisor_phone') }}"
                                   placeholder="+213 555 1234">
                            @error('external_supervisor_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-compact">
                            <label for="external_supervisor_position" class="form-label-compact">
                                {{ __('app.position') }}
                            </label>
                            <input type="text"
                                   class="form-control-compact @error('external_supervisor_position') is-invalid @enderror"
                                   id="external_supervisor_position"
                                   name="external_supervisor_position"
                                   value="{{ old('external_supervisor_position') }}"
                                   placeholder="Senior Developer">
                            @error('external_supervisor_position')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Specialities for Students -->
                <div class="form-group-compact">
                    <label for="specialities_student" class="form-label-compact required">
                        <i class="bi bi-diagram-2"></i>
                        {{ __('app.target_specialities') }}
                    </label>
                    <select class="form-select-compact @error('specialities') is-invalid @enderror"
                            id="specialities_student"
                            name="specialities[]"
                            multiple
                            required
                            size="3">
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
                </div>
            </div>
        </div>
        @endif

        <!-- Form Actions -->
        <div class="box-footer">
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('subjects.index') }}" class="btn btn-default">
                    <i class="bi bi-arrow-left"></i>
                    {{ __('app.cancel') }}
                </a>
                <div class="d-flex gap-2">
                    <button type="submit" name="action" value="draft" class="btn btn-default">
                        <i class="bi bi-file-earmark"></i>
                        {{ __('app.save_draft') }}
                    </button>
                    @if(auth()->user()->role === 'teacher')
                        <button type="submit" name="action" value="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i>
                            {{ __('app.submit_validation') }}
                        </button>
                    @else
                        <button type="submit" name="action" value="submit" class="btn btn-success">
                            <i class="bi bi-send-check"></i>
                            {{ __('app.submit_validation') }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </form>
</div>

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
