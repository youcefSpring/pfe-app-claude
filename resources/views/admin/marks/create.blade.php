@extends('layouts.pfe-app')

@section('page-title', __('app.add_student_mark'))

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('app.add_student_mark') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.marks.store') }}" method="POST">
                        @csrf

                        <!-- Student Selection -->
                        <div class="mb-4">
                            <label for="user_id" class="form-label">{{ __('app.select_student') }} <span class="text-danger">*</span></label>
                            <select class="form-select select2-student @error('user_id') is-invalid @enderror"
                                    id="user_id" name="user_id" required>
                                <option value="">{{ __('app.search_select_student') }}</option>
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

                        <!-- 5 Marks Section -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">{{ __('app.enter_5_marks') }}</label>
                            <div class="row g-2">
                                <div class="col">
                                    <label for="mark_1" class="form-label small">{{ __('app.mark') }} 1 <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0" max="20"
                                           class="form-control @error('mark_1') is-invalid @enderror mark-input"
                                           id="mark_1" name="mark_1" value="{{ old('mark_1') }}"
                                           placeholder="0-20" required>
                                    @error('mark_1')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col">
                                    <label for="mark_2" class="form-label small">{{ __('app.mark') }} 2 <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0" max="20"
                                           class="form-control @error('mark_2') is-invalid @enderror mark-input"
                                           id="mark_2" name="mark_2" value="{{ old('mark_2') }}"
                                           placeholder="0-20" required>
                                    @error('mark_2')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col">
                                    <label for="mark_3" class="form-label small">{{ __('app.mark') }} 3</label>
                                    <input type="number" step="0.01" min="0" max="20"
                                           class="form-control @error('mark_3') is-invalid @enderror mark-input"
                                           id="mark_3" name="mark_3" value="{{ old('mark_3') }}"
                                           placeholder="0-20">
                                    @error('mark_3')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col">
                                    <label for="mark_4" class="form-label small">{{ __('app.mark') }} 4</label>
                                    <input type="number" step="0.01" min="0" max="20"
                                           class="form-control @error('mark_4') is-invalid @enderror mark-input"
                                           id="mark_4" name="mark_4" value="{{ old('mark_4') }}"
                                           placeholder="0-20">
                                    @error('mark_4')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col">
                                    <label for="mark_5" class="form-label small">{{ __('app.mark') }} 5</label>
                                    <input type="number" step="0.01" min="0" max="20"
                                           class="form-control @error('mark_5') is-invalid @enderror mark-input"
                                           id="mark_5" name="mark_5" value="{{ old('mark_5') }}"
                                           placeholder="0-20">
                                    @error('mark_5')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Average Display -->
                        <div class="mb-4">
                            <div class="alert alert-info d-flex justify-content-between align-items-center">
                                <span>{{ __('app.calculated_average') }}:</span>
                                <h4 class="mb-0" id="average_display">--</h4>
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

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
.select2-container .select2-selection--single {
    height: 38px;
    display: flex;
    align-items: center;
}
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Select2 for student select with search
    $('.select2-student').select2({
        theme: 'bootstrap-5',
        placeholder: '{{ __('app.search_select_student') }}',
        allowClear: true,
        width: '100%'
    });

    const markInputs = document.querySelectorAll('.mark-input');

    // Calculate simple average of the 5 marks (out of 20)
    function calculateAverage() {
        let total = 0;
        let count = 0;

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
            averageDisplay.textContent = average + ' / 20';

            // Color coding based on average
            const alertBox = averageDisplay.closest('.alert');
            alertBox.classList.remove('alert-info', 'alert-success', 'alert-warning', 'alert-danger');

            if (average >= 16) {
                alertBox.classList.add('alert-success');
            } else if (average >= 12) {
                alertBox.classList.add('alert-info');
            } else if (average >= 10) {
                alertBox.classList.add('alert-warning');
            } else {
                alertBox.classList.add('alert-danger');
            }
        } else {
            averageDisplay.textContent = '--';
            const alertBox = averageDisplay.closest('.alert');
            alertBox.classList.remove('alert-success', 'alert-warning', 'alert-danger');
            alertBox.classList.add('alert-info');
        }
    }

    // Add event listeners to all mark inputs
    markInputs.forEach(input => {
        input.addEventListener('input', calculateAverage);
    });

    // Initial calculation
    calculateAverage();
});
</script>
@endpush