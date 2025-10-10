@extends('layouts.pfe-app')

@section('page-title', __('app.edit_student_mark'))

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <small class="text-muted">{{ __('app.edit_mark_description') }}</small>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.marks.update', $mark) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="user_id" class="form-label">{{ __('app.student') }} <span class="text-danger">*</span></label>
                                    <select class="form-select @error('user_id') is-invalid @enderror"
                                            id="user_id" name="user_id" required>
                                        <option value="">{{ __('app.select_student') }}</option>
                                        @foreach($students as $student)
                                            <option value="{{ $student->id }}" {{ (old('user_id', $mark->user_id) == $student->id) ? 'selected' : '' }}>
                                                {{ $student->name }} @if($student->matricule)({{ $student->matricule }})@endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
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
                                    <input type="number" step="0.01" min="0" max="20"
                                           class="form-control @error('mark_1') is-invalid @enderror"
                                           id="mark_1" name="mark_1" value="{{ old('mark_1', $mark->mark_1) }}"
                                           placeholder="0.00" required>
                                    @error('mark_1')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="mark_2" class="form-label">{{ __('app.mark') }} 2 <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0" max="20"
                                           class="form-control @error('mark_2') is-invalid @enderror"
                                           id="mark_2" name="mark_2" value="{{ old('mark_2', $mark->mark_2) }}"
                                           placeholder="0.00" required>
                                    @error('mark_2')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="mark_3" class="form-label">{{ __('app.mark') }} 3</label>
                                    <input type="number" step="0.01" min="0" max="20"
                                           class="form-control @error('mark_3') is-invalid @enderror"
                                           id="mark_3" name="mark_3" value="{{ old('mark_3', $mark->mark_3) }}"
                                           placeholder="0.00">
                                    @error('mark_3')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="mark_4" class="form-label">{{ __('app.mark') }} 4</label>
                                    <input type="number" step="0.01" min="0" max="20"
                                           class="form-control @error('mark_4') is-invalid @enderror"
                                           id="mark_4" name="mark_4" value="{{ old('mark_4', $mark->mark_4) }}"
                                           placeholder="0.00">
                                    @error('mark_4')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="mark_5" class="form-label">{{ __('app.mark') }} 5</label>
                                    <input type="number" step="0.01" min="0" max="20"
                                           class="form-control @error('mark_5') is-invalid @enderror"
                                           id="mark_5" name="mark_5" value="{{ old('mark_5', $mark->mark_5) }}"
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
                                <i class="fas fa-save"></i> {{ __('app.update_mark') }}
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
    // Calculate simple average of the 5 marks (only for entered marks)
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
            averageDisplay.value = average;

            // Color coding
            if (average >= 16) {
                averageDisplay.className = 'form-control fw-bold text-center text-success';
            } else if (average >= 12) {
                averageDisplay.className = 'form-control fw-bold text-center text-warning';
            } else if (average >= 10) {
                averageDisplay.className = 'form-control fw-bold text-center text-primary';
            } else {
                averageDisplay.className = 'form-control fw-bold text-center text-danger';
            }
        } else {
            averageDisplay.value = '';
            averageDisplay.className = 'form-control fw-bold text-center';
        }
    }

    // Add event listeners to all mark inputs
    for (let i = 1; i <= 5; i++) {
        const markInput = document.getElementById(`mark_${i}`);
        if (markInput) {
            markInput.addEventListener('input', calculateAverage);
        }
    }

    // Initial calculation
    calculateAverage();
});
</script>
@endpush