@extends('layouts.pfe-app')

@section('page-title', __('app.previous_marks'))

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-chart-line me-2"></i>
                            {{ __('app.previous_semester_marks') }}
                        </h4>
                        <span class="badge bg-light text-dark">{{ __('app.step') }} 2/3</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="progress mb-4" style="height: 10px;">
                        <div class="progress-bar bg-primary" style="width: 66%"></div>
                    </div>

                    @if($requiredMarks > 0)
                        <div class="alert alert-info mb-4">
                            <h6 class="alert-heading">
                                <i class="fas fa-info-circle me-2"></i>
                                {{ __('app.marks_requirement_info') }}
                            </h6>
                            <p class="mb-0">
                                @if($user->student_level === 'licence_3')
                                    {{ __('app.licence_3_marks_explanation') }}
                                @else
                                    {{ __('app.master_marks_explanation') }}
                                @endif
                            </p>
                        </div>

                        <form action="{{ route('student.setup.store-marks') }}" method="POST">
                            @csrf

                            <div class="row justify-content-center">
                                <div class="col-md-8">
                                    <div class="card border-primary">
                                        <div class="card-header bg-primary text-white">
                                            <h6 class="mb-0">
                                                <i class="fas fa-chart-line me-2"></i>
                                                {{ __('app.semester_marks_entry') }}
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                @for($i = 1; $i <= $requiredMarks; $i++)
                                                    <div class="col-md-6 mb-3">
                                                        <div class="form-group">
                                                            <label for="semester_{{ $i }}_mark" class="form-label fw-bold">
                                                                @if($user->student_level === 'licence_3')
                                                                    {{ __('app.semester') }} S{{ $i }}
                                                                @else
                                                                    S{{ $i }} - {{ __('app.previous_year') }}
                                                                @endif
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <div class="input-group">
                                                                <input type="number"
                                                                       class="form-control @error('semester_'.$i.'_mark') is-invalid @enderror"
                                                                       id="semester_{{ $i }}_mark"
                                                                       name="semester_{{ $i }}_mark"
                                                                       value="{{ old('semester_'.$i.'_mark') }}"
                                                                       step="0.01"
                                                                       min="0"
                                                                       max="20"
                                                                       placeholder="0.00"
                                                                       required>
                                                                <span class="input-group-text">/20</span>
                                                                @error('semester_'.$i.'_mark')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                            <small class="text-muted">{{ __('app.enter_overall_semester_average') }}</small>
                                                        </div>
                                                    </div>
                                                @endfor
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-warning">
                                <h6 class="alert-heading">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    {{ __('app.important_note') }}
                                </h6>
                                <p class="mb-0">{{ __('app.marks_verification_note') }}</p>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('student.setup.personal-info') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    {{ __('app.back') }}
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    {{ __('app.continue') }}
                                    <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-success text-center">
                            <h5 class="alert-heading">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ __('app.no_marks_required') }}
                            </h5>
                            <p class="mb-3">{{ __('app.no_marks_required_explanation') }}</p>
                            <a href="{{ route('student.setup.complete') }}" class="btn btn-success">
                                {{ __('app.continue') }}
                                <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mark input validation
    const markInputs = document.querySelectorAll('input[type="number"][name*="_mark"]');

    markInputs.forEach(input => {
        input.addEventListener('input', function() {
            const value = parseFloat(this.value);
            if (value < 0) {
                this.value = 0;
            } else if (value > 20) {
                this.value = 20;
            }

            // Visual feedback for mark ranges
            this.classList.remove('border-success', 'border-warning', 'border-danger');
            if (value >= 16) {
                this.classList.add('border-success');
            } else if (value >= 10) {
                this.classList.add('border-warning');
            } else if (value > 0) {
                this.classList.add('border-danger');
            }
        });
    });
});
</script>
@endpush