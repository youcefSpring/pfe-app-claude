@extends('layouts.pfe-app')

@section('page-title', __('app.add_marks_all_students'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ __('app.add_marks_all_students') }}</h4>
                    <small class="text-muted">{{ __('app.add_marks_all_students_description') }}</small>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.marks.bulk-all-store') }}" method="POST" id="bulk-all-marks-form">
                        @csrf

                        <!-- Simplified Header - Only Filter -->
                        <div class="row mb-4 p-3 bg-light rounded">
                            <div class="col-12">
                                <h5 class="mb-3">{{ __('app.marks') }}</h5>
                                <p class="text-muted small">{{ __('app.add_marks_all_students_simple_description') }}</p>
                            </div>
                        </div>

                        <!-- Students Table -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 5%;">
                                            <input type="checkbox" id="select-all" class="form-check-input">
                                        </th>
                                        <th style="width: 20%;">{{ __('app.student') }}</th>
                                        <th style="width: 10%;">{{ __('app.matricule') }}</th>
                                        <th style="width: 8%;">{{ __('app.mark') }} 1</th>
                                        <th style="width: 8%;">{{ __('app.mark') }} 2</th>
                                        <th style="width: 8%;">{{ __('app.mark') }} 3</th>
                                        <th style="width: 8%;">{{ __('app.mark') }} 4</th>
                                        <th style="width: 8%;">{{ __('app.mark') }} 5</th>
                                        <th style="width: 10%;">{{ __('app.average') }}</th>
                                        <th style="width: 15%;">{{ __('app.speciality') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($students as $index => $student)
                                        <tr class="student-row">
                                            <td>
                                                <input type="checkbox" name="selected_students[]" value="{{ $student->id }}"
                                                       class="form-check-input student-checkbox" checked>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                                        <i class="fas fa-user text-primary"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1">{{ $student->name }}</h6>
                                                        <small class="text-muted">{{ $student->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $student->matricule ?? '-' }}</span>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" min="0" max="20"
                                                       class="form-control form-control-sm mark-input"
                                                       name="mark_1[{{ $student->id }}]"
                                                       placeholder="0.00"
                                                       data-student-id="{{ $student->id }}"
                                                       data-mark-num="1" required>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" min="0" max="20"
                                                       class="form-control form-control-sm mark-input"
                                                       name="mark_2[{{ $student->id }}]"
                                                       placeholder="0.00"
                                                       data-student-id="{{ $student->id }}"
                                                       data-mark-num="2" required>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" min="0" max="20"
                                                       class="form-control form-control-sm mark-input"
                                                       name="mark_3[{{ $student->id }}]"
                                                       placeholder="0.00"
                                                       data-student-id="{{ $student->id }}"
                                                       data-mark-num="3">
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" min="0" max="20"
                                                       class="form-control form-control-sm mark-input"
                                                       name="mark_4[{{ $student->id }}]"
                                                       placeholder="0.00"
                                                       data-student-id="{{ $student->id }}"
                                                       data-mark-num="4">
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" min="0" max="20"
                                                       class="form-control form-control-sm mark-input"
                                                       name="mark_5[{{ $student->id }}]"
                                                       placeholder="0.00"
                                                       data-student-id="{{ $student->id }}"
                                                       data-mark-num="5">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm fw-bold text-center average-display"
                                                       data-student-id="{{ $student->id }}" readonly>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $student->speciality ?? '-' }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Quick Actions -->
                        <div class="row mt-4 p-3 bg-light rounded">
                            <div class="col-md-8">
                                <div class="d-flex gap-2 flex-wrap">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="fillAllMarks(1, 10)">
                                            {{ __('app.mark') }} 1: 10
                                        </button>
                                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="fillAllMarks(1, 15)">
                                            {{ __('app.mark') }} 1: 15
                                        </button>
                                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="fillAllMarks(1, 20)">
                                            {{ __('app.mark') }} 1: 20
                                        </button>
                                    </div>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-outline-success btn-sm" onclick="fillAllMarks(2, 10)">
                                            {{ __('app.mark') }} 2: 10
                                        </button>
                                        <button type="button" class="btn btn-outline-success btn-sm" onclick="fillAllMarks(2, 15)">
                                            {{ __('app.mark') }} 2: 15
                                        </button>
                                        <button type="button" class="btn btn-outline-success btn-sm" onclick="fillAllMarks(2, 20)">
                                            {{ __('app.mark') }} 2: 20
                                        </button>
                                    </div>
                                    <button type="button" class="btn btn-outline-warning btn-sm" onclick="clearAllMarks()">
                                        <i class="fas fa-eraser"></i> {{ __('app.clear_all') }}
                                    </button>
                                    <button type="button" class="btn btn-outline-info btn-sm" onclick="fillRandomMarks()">
                                        <i class="fas fa-random"></i> {{ __('app.random_marks') }}
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <small class="text-muted">
                                    <span id="selected-count">{{ $students->count() }}</span> {{ __('app.students_selected') }}
                                </small>
                            </div>
                        </div>

                        <!-- Submit Actions -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('admin.marks') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-2"></i>{{ __('app.back_to_marks') }}
                            </a>
                            <div>
                                <button type="button" class="btn btn-outline-info me-2" onclick="previewMarks()">
                                    <i class="bi bi-eye me-2"></i>{{ __('app.preview') }}
                                </button>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle me-2"></i>{{ __('app.save_all_marks') }}
                                    <span class="badge bg-light text-dark ms-2" id="save-count">{{ $students->count() }}</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('app.marks_preview') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="preview-content"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('app.close') }}</button>
                <button type="button" class="btn btn-success" onclick="submitForm()">{{ __('app.confirm_save') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all');
    const studentCheckboxes = document.querySelectorAll('.student-checkbox');

    // Select/Deselect All
    selectAllCheckbox.addEventListener('change', function() {
        studentCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelectedCount();
    });

    // Individual checkbox change
    studentCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });

    // Calculate average for each student when marks change
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('mark-input')) {
            const studentId = e.target.dataset.studentId;
            calculateAverage(studentId);
        }
    });

    function calculateAverage(studentId) {
        let total = 0;
        let count = 0;

        for (let i = 1; i <= 5; i++) {
            const markInput = document.querySelector(`input[name="mark_${i}[${studentId}]"]`);
            if (markInput) {
                const mark = parseFloat(markInput.value) || 0;
                if (mark > 0) {
                    total += mark;
                    count++;
                }
            }
        }

        const averageDisplay = document.querySelector(`.average-display[data-student-id="${studentId}"]`);
        if (count > 0) {
            const average = (total / count).toFixed(2);
            averageDisplay.value = average;

            // Color coding
            if (average >= 16) {
                averageDisplay.className = 'form-control form-control-sm fw-bold text-center text-success';
            } else if (average >= 12) {
                averageDisplay.className = 'form-control form-control-sm fw-bold text-center text-warning';
            } else if (average >= 10) {
                averageDisplay.className = 'form-control form-control-sm fw-bold text-center text-primary';
            } else {
                averageDisplay.className = 'form-control form-control-sm fw-bold text-center text-danger';
            }
        } else {
            averageDisplay.value = '';
            averageDisplay.className = 'form-control form-control-sm fw-bold text-center';
        }
    }

    function updateSelectedCount() {
        const checkedCount = document.querySelectorAll('.student-checkbox:checked').length;
        document.getElementById('selected-count').textContent = checkedCount;
        document.getElementById('save-count').textContent = checkedCount;
    }

    // Global functions for buttons
    window.fillAllMarks = function(markNum, value) {
        document.querySelectorAll('.student-checkbox:checked').forEach(checkbox => {
            const studentId = checkbox.value;
            const markInput = document.querySelector(`input[name="mark_${markNum}[${studentId}]"]`);
            markInput.value = value;
            calculateAverage(studentId);
        });
    };

    window.clearAllMarks = function() {
        document.querySelectorAll('.mark-input').forEach(input => {
            input.value = '';
            const studentId = input.dataset.studentId;
            calculateAverage(studentId);
        });
    };

    window.fillRandomMarks = function() {
        document.querySelectorAll('.student-checkbox:checked').forEach(checkbox => {
            const studentId = checkbox.value;

            // Fill required marks (1 and 2) and randomly fill others
            for (let i = 1; i <= 5; i++) {
                const markInput = document.querySelector(`input[name="mark_${i}[${studentId}]"]`);
                if (i <= 2 || Math.random() > 0.5) { // Always fill 1&2, randomly fill 3,4,5
                    const randomMark = (Math.random() * 16 + 4).toFixed(2); // Random between 4-20
                    markInput.value = randomMark;
                }
            }
            calculateAverage(studentId);
        });
    };

    window.previewMarks = function() {
        const selectedStudents = document.querySelectorAll('.student-checkbox:checked');
        let previewHtml = '<div class="table-responsive"><table class="table table-sm"><thead><tr><th>Student</th><th>Mark 1</th><th>Mark 2</th><th>Mark 3</th><th>Mark 4</th><th>Mark 5</th><th>Average</th></tr></thead><tbody>';

        selectedStudents.forEach(checkbox => {
            const studentId = checkbox.value;
            const row = checkbox.closest('tr');
            const studentName = row.querySelector('h6').textContent;

            let hasMarks = false;
            let markValues = [];
            for (let i = 1; i <= 5; i++) {
                const markInput = row.querySelector(`input[name="mark_${i}[${studentId}]"]`);
                const value = markInput.value || '-';
                markValues.push(value);
                if (markInput.value) hasMarks = true;
            }

            const average = row.querySelector(`.average-display[data-student-id="${studentId}"]`).value || '-';

            if (hasMarks) {
                previewHtml += `<tr><td>${studentName}</td><td>${markValues[0]}</td><td>${markValues[1]}</td><td>${markValues[2]}</td><td>${markValues[3]}</td><td>${markValues[4]}</td><td><strong>${average}</strong></td></tr>`;
            }
        });

        previewHtml += '</tbody></table></div>';
        document.getElementById('preview-content').innerHTML = previewHtml;
        new bootstrap.Modal(document.getElementById('previewModal')).show();
    };

    window.submitForm = function() {
        document.getElementById('bulk-all-marks-form').submit();
    };
});
</script>
@endpush