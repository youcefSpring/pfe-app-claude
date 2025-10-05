@extends('layouts.pfe-app')

@section('page-title', __('app.add_student_marks_bulk'))

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ __('app.add_student_marks_bulk') }}</h4>
                    <small class="text-muted">{{ __('app.add_multiple_marks_description') }}</small>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.marks.bulk-store') }}" method="POST" id="bulk-marks-form">
                        @csrf

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="user_id" class="form-label">{{ __('app.student') }} <span class="text-danger">*</span></label>
                                    <select class="form-select @error('user_id') is-invalid @enderror"
                                            id="user_id" name="user_id" required>
                                        <option value="">{{ __('app.select_student') }}</option>
                                        @foreach($students as $student)
                                            <option value="{{ $student->id }}" {{ old('user_id') == $student->id ? 'selected' : '' }}>
                                                {{ $student->name }} @if($student->matricule)({{ $student->matricule }})@endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="semester" class="form-label">{{ __('app.semester') }} <span class="text-danger">*</span></label>
                                    <select class="form-select @error('semester') is-invalid @enderror"
                                            id="semester" name="semester" required>
                                        <option value="">{{ __('app.select_semester') }}</option>
                                        <option value="S1" {{ old('semester') === 'S1' ? 'selected' : '' }}>S1</option>
                                        <option value="S2" {{ old('semester') === 'S2' ? 'selected' : '' }}>S2</option>
                                        <option value="S3" {{ old('semester') === 'S3' ? 'selected' : '' }}>S3</option>
                                        <option value="S4" {{ old('semester') === 'S4' ? 'selected' : '' }}>S4</option>
                                        <option value="S5" {{ old('semester') === 'S5' ? 'selected' : '' }}>S5</option>
                                        <option value="S6" {{ old('semester') === 'S6' ? 'selected' : '' }}>S6</option>
                                    </select>
                                    @error('semester')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="academic_year" class="form-label">{{ __('app.academic_year') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('academic_year') is-invalid @enderror"
                                           id="academic_year" name="academic_year" value="{{ old('academic_year', '2024-2025') }}"
                                           placeholder="{{ __('app.enter_academic_year') }}" required>
                                    @error('academic_year')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>{{ __('app.subjects_marks') }}</h5>
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="add-subject">
                                        <i class="bi bi-plus-circle"></i> {{ __('app.add_subject') }}
                                    </button>
                                </div>

                                <div id="subjects-container">
                                    <!-- Dynamic subject entries will be added here -->
                                </div>

                                @error('marks')
                                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="notes" class="form-label">{{ __('app.notes') }}</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror"
                                              id="notes" name="notes" rows="3"
                                              placeholder="{{ __('app.enter_notes_optional') }}">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.marks') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> {{ __('app.back_to_marks') }}
                            </a>
                            <button type="submit" class="btn btn-primary" id="submit-btn">
                                <i class="bi bi-save"></i> {{ __('app.save_all_marks') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Subject Row Template -->
<template id="subject-template">
    <div class="row subject-row mb-3 p-3 border rounded">
        <div class="col-md-4">
            <div class="mb-3">
                <label class="form-label">{{ __('app.subject_name') }} <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="marks[INDEX][subject_name]"
                       placeholder="{{ __('app.enter_subject_name') }}" required>
            </div>
        </div>
        <div class="col-md-2">
            <div class="mb-3">
                <label class="form-label">{{ __('app.mark') }} <span class="text-danger">*</span></label>
                <input type="number" step="0.01" min="0" class="form-control mark-input"
                       name="marks[INDEX][mark]" placeholder="0.00" required>
            </div>
        </div>
        <div class="col-md-2">
            <div class="mb-3">
                <label class="form-label">{{ __('app.max_mark') }} <span class="text-danger">*</span></label>
                <input type="number" step="0.01" min="0.01" class="form-control max-mark-input"
                       name="marks[INDEX][max_mark]" value="20" required>
            </div>
        </div>
        <div class="col-md-3">
            <div class="mb-3">
                <label class="form-label">{{ __('app.percentage') }}</label>
                <div class="input-group">
                    <input type="text" class="form-control percentage-display" readonly>
                    <span class="input-group-text">%</span>
                </div>
            </div>
        </div>
        <div class="col-md-1">
            <div class="mb-3">
                <label class="form-label">&nbsp;</label>
                <button type="button" class="btn btn-danger btn-sm remove-subject d-block">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    </div>
</template>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let subjectIndex = 0;
    const subjectsContainer = document.getElementById('subjects-container');
    const addSubjectBtn = document.getElementById('add-subject');
    const template = document.getElementById('subject-template');

    // Add initial subject row
    addSubjectRow();

    // Add subject button click
    addSubjectBtn.addEventListener('click', function() {
        addSubjectRow();
    });

    function addSubjectRow() {
        const templateContent = template.content.cloneNode(true);
        const row = templateContent.querySelector('.subject-row');

        // Replace INDEX with actual index
        row.innerHTML = row.innerHTML.replace(/INDEX/g, subjectIndex);

        // Add event listeners
        const markInput = row.querySelector('.mark-input');
        const maxMarkInput = row.querySelector('.max-mark-input');
        const percentageDisplay = row.querySelector('.percentage-display');
        const removeBtn = row.querySelector('.remove-subject');

        // Calculate percentage on input
        function calculatePercentage() {
            const mark = parseFloat(markInput.value) || 0;
            const maxMark = parseFloat(maxMarkInput.value) || 1;

            if (maxMark > 0) {
                const percentage = ((mark / maxMark) * 100).toFixed(2);
                percentageDisplay.value = percentage;

                // Color coding
                if (percentage >= 80) {
                    percentageDisplay.className = 'form-control percentage-display text-success fw-bold';
                } else if (percentage >= 60) {
                    percentageDisplay.className = 'form-control percentage-display text-warning fw-bold';
                } else {
                    percentageDisplay.className = 'form-control percentage-display text-danger fw-bold';
                }
            }
        }

        markInput.addEventListener('input', calculatePercentage);
        maxMarkInput.addEventListener('input', calculatePercentage);

        // Remove button
        removeBtn.addEventListener('click', function() {
            if (subjectsContainer.children.length > 1) {
                row.remove();
            } else {
                alert('{{ __('app.at_least_one_subject_required') }}');
            }
        });

        subjectsContainer.appendChild(row);
        subjectIndex++;

        // Calculate initial percentage if values exist
        calculatePercentage();
    }

    // Form validation
    document.getElementById('bulk-marks-form').addEventListener('submit', function(e) {
        const subjects = document.querySelectorAll('.subject-row');
        let hasErrors = false;

        subjects.forEach(function(subject) {
            const subjectName = subject.querySelector('input[name*="[subject_name]"]').value.trim();
            const mark = subject.querySelector('input[name*="[mark]"]').value;
            const maxMark = subject.querySelector('input[name*="[max_mark]"]').value;

            if (!subjectName || !mark || !maxMark) {
                hasErrors = true;
            }

            if (parseFloat(mark) > parseFloat(maxMark)) {
                hasErrors = true;
                alert('{{ __('app.mark_cannot_exceed_max_mark') }}');
            }
        });

        if (hasErrors) {
            e.preventDefault();
            alert('{{ __('app.please_fill_all_required_fields') }}');
        }
    });
});
</script>
@endpush