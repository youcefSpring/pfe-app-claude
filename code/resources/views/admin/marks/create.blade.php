@extends('layouts.pfe-app')

@section('page-title', __('app.add_student_mark'))

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ __('app.add_student_mark') }}</h4>
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
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="mark" class="form-label">{{ __('app.mark') }} <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0"
                                           class="form-control @error('mark') is-invalid @enderror"
                                           id="mark" name="mark" value="{{ old('mark') }}"
                                           placeholder="{{ __('app.enter_mark') }}" required>
                                    @error('mark')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="max_mark" class="form-label">{{ __('app.max_mark') }} <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0.01"
                                           class="form-control @error('max_mark') is-invalid @enderror"
                                           id="max_mark" name="max_mark" value="{{ old('max_mark', '20') }}"
                                           placeholder="{{ __('app.enter_max_mark') }}" required>
                                    @error('max_mark')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="semester" class="form-label">{{ __('app.semester') }}</label>
                                    <select class="form-select @error('semester') is-invalid @enderror"
                                            id="semester" name="semester">
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
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="academic_year" class="form-label">{{ __('app.academic_year') }}</label>
                                    <input type="text" class="form-control @error('academic_year') is-invalid @enderror"
                                           id="academic_year" name="academic_year" value="{{ old('academic_year', '2024-2025') }}"
                                           placeholder="{{ __('app.enter_academic_year') }}">
                                    @error('academic_year')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">{{ __('app.notes') }}</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                      id="notes" name="notes" rows="3"
                                      placeholder="{{ __('app.enter_notes_optional') }}">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
    const markInput = document.getElementById('mark');
    const maxMarkInput = document.getElementById('max_mark');

    function calculatePercentage() {
        const mark = parseFloat(markInput.value) || 0;
        const maxMark = parseFloat(maxMarkInput.value) || 1;

        if (maxMark > 0) {
            const percentage = ((mark / maxMark) * 100).toFixed(2);
            const label = document.querySelector('label[for="mark"]');
            if (mark > 0) {
                label.innerHTML = `{{ __('app.mark') }} <span class="text-danger">*</span> <small class="text-info">(${percentage}%)</small>`;
            } else {
                label.innerHTML = `{{ __('app.mark') }} <span class="text-danger">*</span>`;
            }
        }
    }

    markInput.addEventListener('input', calculatePercentage);
    maxMarkInput.addEventListener('input', calculatePercentage);
});
</script>
@endpush