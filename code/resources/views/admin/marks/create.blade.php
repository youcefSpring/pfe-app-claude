@extends('layouts.pfe-app')

@section('page-title', __('app.add_student_mark'))

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <small class="text-muted">{{ __('app.add_mark_description') }}</small>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.marks.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
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
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="subject_name" class="form-label">{{ __('app.subject_name') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('subject_name') is-invalid @enderror"
                                           id="subject_name" name="subject_name" value="{{ old('subject_name') }}"
                                           placeholder="{{ __('app.enter_subject_name') }}" required>
                                    @error('subject_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="semester" class="form-label">{{ __('app.semester') }}</label>
                                    <select class="form-select @error('semester') is-invalid @enderror" id="semester" name="semester">
                                        <option value="">{{ __('app.select_semester') }}</option>
                                        <option value="S1" {{ old('semester') == 'S1' ? 'selected' : '' }}>S1</option>
                                        <option value="S2" {{ old('semester') == 'S2' ? 'selected' : '' }}>S2</option>
                                        <option value="S3" {{ old('semester') == 'S3' ? 'selected' : '' }}>S3</option>
                                        <option value="S4" {{ old('semester') == 'S4' ? 'selected' : '' }}>S4</option>
                                        <option value="S5" {{ old('semester') == 'S5' ? 'selected' : '' }}>S5</option>
                                        <option value="S6" {{ old('semester') == 'S6' ? 'selected' : '' }}>S6</option>
                                    </select>
                                    @error('semester')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="academic_year" class="form-label">{{ __('app.academic_year') }} <span class="text-danger">*</span></label>
                                    <select class="form-select @error('academic_year') is-invalid @enderror" id="academic_year" name="academic_year" required>
                                        <option value="">{{ __('app.select_academic_year') }}</option>
                                        @php
                                            $currentYear = date('Y');
                                            $currentAcademicYear = $currentYear . '-' . ($currentYear + 1);
                                        @endphp
                                        @for($year = $currentYear - 2; $year <= $currentYear + 1; $year++)
                                            @php $academicYear = $year . '-' . ($year + 1); @endphp
                                            <option value="{{ $academicYear }}" {{ old('academic_year', $currentAcademicYear) == $academicYear ? 'selected' : '' }}>
                                                {{ $academicYear }}
                                            </option>
                                        @endfor
                                    </select>
                                    @error('academic_year')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="max_mark" class="form-label">{{ __('app.max_mark') }} <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="1" max="100"
                                           class="form-control @error('max_mark') is-invalid @enderror"
                                           id="max_mark" name="max_mark" value="{{ old('max_mark', 20) }}" required>
                                    @error('max_mark')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="notes" class="form-label">{{ __('app.notes') }}</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror"
                                              id="notes" name="notes" rows="2"
                                              placeholder="{{ __('app.additional_notes_optional') }}">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Simple Marks Section -->
                        <div class="row">
                            <div class="col-12">
                                <h5 class="mb-3">{{ __('app.marks') }}</h5>
                                <p class="text-muted small">{{ __('app.marks_simple_description') }}</p>
                            </div>
                        </div>

                        <!-- 5 Simple Mark Inputs in One Row -->
                        <div class="row mb-3">
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="mark_1" class="form-label">{{ __('app.mark') }} 1 <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0"
                                           class="form-control @error('mark_1') is-invalid @enderror mark-input"
                                           id="mark_1" name="mark_1" value="{{ old('mark_1') }}"
                                           placeholder="0.00" required>
                                    @error('mark_1')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="mark_2" class="form-label">{{ __('app.mark') }} 2 <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0"
                                           class="form-control @error('mark_2') is-invalid @enderror mark-input"
                                           id="mark_2" name="mark_2" value="{{ old('mark_2') }}"
                                           placeholder="0.00" required>
                                    @error('mark_2')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="mark_3" class="form-label">{{ __('app.mark') }} 3</label>
                                    <input type="number" step="0.01" min="0"
                                           class="form-control @error('mark_3') is-invalid @enderror mark-input"
                                           id="mark_3" name="mark_3" value="{{ old('mark_3') }}"
                                           placeholder="0.00">
                                    @error('mark_3')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="mark_4" class="form-label">{{ __('app.mark') }} 4</label>
                                    <input type="number" step="0.01" min="0"
                                           class="form-control @error('mark_4') is-invalid @enderror mark-input"
                                           id="mark_4" name="mark_4" value="{{ old('mark_4') }}"
                                           placeholder="0.00">
                                    @error('mark_4')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="mark_5" class="form-label">{{ __('app.mark') }} 5</label>
                                    <input type="number" step="0.01" min="0"
                                           class="form-control @error('mark_5') is-invalid @enderror mark-input"
                                           id="mark_5" name="mark_5" value="{{ old('mark_5') }}"
                                           placeholder="0.00">
                                    @error('mark_5')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ __('app.average') }}</label>
                                    <input type="text" class="form-control fw-bold text-center" id="average_display" readonly>
                                </div>
                            </div>
                        </div>


                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.marks') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> {{ __('app.back_to_marks') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('app.add_mark') }}
                            </button>
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
    const maxMarkInput = document.getElementById('max_mark');
    const markInputs = document.querySelectorAll('.mark-input');

    // Update max attribute of mark inputs when max_mark changes
    function updateMaxValues() {
        const maxValue = parseFloat(maxMarkInput.value) || 20;
        markInputs.forEach(input => {
            input.setAttribute('max', maxValue);
        });
        calculateAverage();
    }

    // Calculate simple average of the 5 marks
    function calculateAverage() {
        let total = 0;
        let count = 0;
        const maxMark = parseFloat(maxMarkInput.value) || 20;

        for (let i = 1; i <= 5; i++) {
            const markInput = document.getElementById(`mark_${i}`);
            if (markInput) {
                const mark = parseFloat(markInput.value) || 0;
                if (mark > 0) {
                    total += mark;
                    count++;
                }
            }
        }

        const averageDisplay = document.getElementById('average_display');
        if (count > 0) {
            const average = (total / count).toFixed(2);
            const percentage = ((average / maxMark) * 100).toFixed(1);
            averageDisplay.value = `${average}/${maxMark} (${percentage}%)`;

            // Color coding based on percentage
            if (percentage >= 80) {
                averageDisplay.className = 'form-control fw-bold text-center text-success';
            } else if (percentage >= 60) {
                averageDisplay.className = 'form-control fw-bold text-center text-warning';
            } else if (percentage >= 50) {
                averageDisplay.className = 'form-control fw-bold text-center text-primary';
            } else {
                averageDisplay.className = 'form-control fw-bold text-center text-danger';
            }
        } else {
            averageDisplay.value = '';
            averageDisplay.className = 'form-control fw-bold text-center';
        }
    }

    // Add event listeners to max_mark input
    maxMarkInput.addEventListener('input', updateMaxValues);

    // Add event listeners to all mark inputs
    markInputs.forEach(input => {
        input.addEventListener('input', calculateAverage);
    });

    // Initial setup
    updateMaxValues();
});
</script>
@endpush