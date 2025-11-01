@extends('layouts.pfe-app')

@section('page-title', 'Edit Subject')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">{{ __('app.edit_subject') }}</h4>
                        <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#pageHelpModal">
                            <i class="bi bi-question-circle"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('subjects.update', $subject) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                                           id="title" name="title" value="{{ old('title', $subject->title) }}"
                                           placeholder="Enter subject title">
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                              id="description" name="description" rows="5"
                                              placeholder="Provide a detailed description of the subject">{{ old('description', $subject->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="keywords" class="form-label">Keywords <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('keywords') is-invalid @enderror"
                                                   id="keywords" name="keywords"
                                                   value="{{ old('keywords', $subject->keywords) }}"
                                                   placeholder="e.g., web development, react, nodejs">
                                            <small class="form-text text-muted">Separate keywords with commas</small>
                                            @error('keywords')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="tools" class="form-label">Tools & Technologies <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('tools') is-invalid @enderror"
                                                   id="tools" name="tools"
                                                   value="{{ old('tools', $subject->tools) }}"
                                                   placeholder="e.g., Laravel, MySQL, Bootstrap">
                                            <small class="form-text text-muted">Separate tools with commas</small>
                                            @error('tools')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="plan" class="form-label">Project Plan <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('plan') is-invalid @enderror"
                                              id="plan" name="plan" rows="8"
                                              placeholder="Describe the project plan, milestones, and deliverables">{{ old('plan', $subject->plan) }}</textarea>
                                    @error('plan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Co-supervisor -->
                                <div class="mb-3">
                                    <label for="co_supervisor_name" class="form-label">{{ __('app.co_supervisor') }} ({{ __('app.optional') }})</label>
                                    <input type="text"
                                           class="form-control @error('co_supervisor_name') is-invalid @enderror"
                                           id="co_supervisor_name"
                                           name="co_supervisor_name"
                                           value="{{ old('co_supervisor_name', $subject->co_supervisor_name ?? '') }}"
                                           placeholder="{{ __('app.enter_co_supervisor_name') }}">
                                    @error('co_supervisor_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        {{ __('app.co_supervisor_help_text') }}
                                    </div>
                                </div>

                                <!-- Specialities Selection -->
                                <div class="mb-3">
                                    <label for="specialities" class="form-label">{{ __('app.target_specialities') }} <span class="text-danger">*</span></label>
                                    <select class="form-select @error('specialities') is-invalid @enderror"
                                            id="specialities"
                                            name="specialities[]"
                                            multiple
                                            required>
                                        @foreach($specialities as $speciality)
                                            <option value="{{ $speciality->id }}"
                                                {{ (in_array($speciality->id, old('specialities', $subject->specialities->pluck('id')->toArray()))) ? 'selected' : '' }}>
                                                {{ $speciality->name }} ({{ $speciality->level }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('specialities')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">{{ __('app.select_specialities_can_work_on_subject') }}</small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Subject Status</h6>

                                        <div class="mb-3">
                                            <small class="text-muted">Current Status</small>
                                            <div>
                                                @if($subject->status === 'draft')
                                                    <span class="badge bg-secondary">Draft</span>
                                                @elseif($subject->status === 'pending_validation')
                                                    <span class="badge bg-warning">Pending Validation</span>
                                                @elseif($subject->status === 'validated')
                                                    <span class="badge bg-success">Validated</span>
                                                @elseif($subject->status === 'rejected')
                                                    <span class="badge bg-danger">Rejected</span>
                                                @endif
                                            </div>
                                        </div>

                                        @if($subject->status === 'validated')
                                            <div class="alert alert-info">
                                                <small>
                                                    <strong>Note:</strong> This subject has been validated.
                                                    Changes may require re-validation.
                                                </small>
                                            </div>
                                        @endif

                                        @if($subject->status === 'rejected' && $subject->validation_notes)
                                            <div class="alert alert-warning">
                                                <small>
                                                    <strong>Rejection Notes:</strong><br>
                                                    {{ $subject->validation_notes }}
                                                </small>
                                            </div>
                                        @endif

                                        <div class="mb-3">
                                            <small class="text-muted">Created</small>
                                            <div>{{ $subject->created_at->format('M d, Y') }}</div>
                                        </div>

                                        @if($subject->validated_at)
                                            <div class="mb-3">
                                                <small class="text-muted">Validated</small>
                                                <div>{{ $subject->validated_at->format('M d, Y') }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <h6>Guidelines</h6>
                                    <div class="small text-muted">
                                        <ul class="ps-3">
                                            <li>Provide clear and detailed descriptions</li>
                                            <li>Include relevant keywords for searchability</li>
                                            <li>Specify required tools and technologies</li>
                                            <li>Outline project milestones and deliverables</li>
                                            <li>Ensure the scope is appropriate for the academic level</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('subjects.show', $subject) }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Back
                                    </a>
                                    <div>
                                        @if($subject->status === 'draft')
                                            <button type="submit" name="action" value="save" class="btn btn-primary">
                                                <i class="fas fa-save"></i> Save Draft
                                            </button>
                                            <button type="submit" name="action" value="submit" class="btn btn-success">
                                                <i class="fas fa-paper-plane"></i> Save & Submit for Validation
                                            </button>
                                        @else
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> Update Subject
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
});
</script>
@endpush

<!-- Help Modal -->
<x-info-modal id="pageHelpModal" title="{{ __('app.edit_subject_help') }}" icon="bi-journal-text">
    <h6>{{ __('app.what_is_this_page') }}</h6>
    <p>{{ __('app.edit_subject_page_description') }}</p>

    <h6>{{ __('app.how_to_use') }}</h6>
    <ul>
        <li><strong>{{ __('app.edit_details') }}:</strong> {{ __('app.edit_subject_details_help') }}</li>
        <li><strong>{{ __('app.change_status') }}:</strong> {{ __('app.change_subject_status_help') }}</li>
        <li><strong>{{ __('app.save_changes') }}:</strong> {{ __('app.save_subject_changes_help') }}</li>
    </ul>

    <h6>{{ __('app.important_notes') }}</h6>
    <ul>
        <li>{{ __('app.edit_subject_note_1') }}</li>
        <li>{{ __('app.edit_subject_note_2') }}</li>
        <li>{{ __('app.edit_subject_note_3') }}</li>
    </ul>
</x-info-modal>