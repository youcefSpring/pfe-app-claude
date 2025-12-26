@extends('layouts.pfe-app')

@section('page-title', __('app.complete_profile_setup'))

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">
            <!-- Progress Header -->
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <h3 class="h4 mb-1">
                                <i class="fas fa-user-graduate text-primary me-2"></i>
                                {{ __('app.complete_profile_setup') }}
                            </h3>
                            <p class="text-muted mb-0">{{ __('app.hello') }}, {{ $user->name }}!</p>
                        </div>
                        <div class="text-end">
                            <div class="badge bg-primary fs-6 mb-2">{{ __('app.step') }} <span id="current-step">1</span>/3</div>
                            <div class="progress" style="width: 150px; height: 8px;">
                                <div class="progress-bar bg-primary" id="progress-bar" style="width: 33%"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Info -->
                    <div class="alert alert-info py-2 px-3 mb-0">
                        <small class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            {{ __('app.setup_takes_5_minutes') }} • {{ __('app.birth_date_and_place') }}, {{ __('app.academic_level_info') }}, {{ __('app.previous_semester_marks') }}
                        </small>
                    </div>
                </div>
            </div>

            <!-- Multi-Step Form -->
            <form id="setup-form" method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- Step 1: Welcome & Personal Info -->
                <div class="setup-step" id="step-1">
                    <div class="card shadow mb-4">
                        <div class="card-header bg-primary text-white py-3">
                            <h5 class="mb-0">
                                <i class="fas fa-user me-2"></i>
                                {{ __('app.personal_information') }}
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="date_naissance" class="form-label fw-semibold">
                                        {{ __('app.birth_date') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="date"
                                           class="form-control form-control-lg @error('date_naissance') is-invalid @enderror"
                                           id="date_naissance"
                                           name="date_naissance"
                                           value="{{ old('date_naissance', $user->date_naissance) }}"
                                           required>
                                    @error('date_naissance')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror


<script>
  const input = document.getElementById("date_naissance");

  const today = new Date();
  const maxDate = today.toISOString().split("T")[0];

  const minDate = new Date();
  minDate.setFullYear(today.getFullYear() - 20);
  const minDateStr = minDate.toISOString().split("T")[0];

  input.min = minDateStr;
  input.max = maxDate;
</script>

                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="lieu_naissance" class="form-label fw-semibold">
                                        {{ __('app.birth_place') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           class="form-control form-control-lg @error('lieu_naissance') is-invalid @enderror"
                                           id="lieu_naissance"
                                           name="lieu_naissance"
                                           value="{{ old('lieu_naissance', $user->lieu_naissance) }}"
                                           placeholder="{{ __('app.enter_birth_place') }}"
                                           required>
                                    @error('lieu_naissance')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="student_level" class="form-label fw-semibold">
                                        {{ __('app.student_level') }} <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select form-select-lg @error('student_level') is-invalid @enderror"
                                            id="student_level"
                                            name="student_level"
                                            required>
                                        <option value="">{{ __('app.select_level') }}</option>
                                        <option value="licence_3" {{ old('student_level', $user->student_level) === 'licence_3' ? 'selected' : '' }}>
                                            {{ __('app.licence_3') }}
                                        </option>
                                        <option value="master_1" {{ old('student_level', $user->student_level) === 'master_1' ? 'selected' : '' }}>
                                            {{ __('app.master_1') }}
                                        </option>
                                        <option value="master_2" {{ old('student_level', $user->student_level) === 'master_2' ? 'selected' : '' }}>
                                            {{ __('app.master_2') }}
                                        </option>
                                    </select>
                                    @error('student_level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="birth_certificate" class="form-label fw-semibold">
                                    <i class="fas fa-file-upload me-2"></i>
                                    {{ __('app.birth_certificate') }} <span class="text-danger">*</span>
                                </label>
                                <input type="file"
                                       class="form-control form-control-lg @error('birth_certificate') is-invalid @enderror"
                                       id="birth_certificate"
                                       name="birth_certificate"
                                       accept=".pdf,.jpg,.jpeg,.png"
                                       required>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    {{ __('app.birth_certificate_requirements') }}
                                </div>
                                @error('birth_certificate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Level Requirements Info -->
                            <div class="row" id="level-info" style="display: none;">
                                <div class="col-12">
                                    <div class="alert alert-light border-info">
                                        <h6 class="alert-heading text-info">
                                            <i class="fas fa-graduation-cap me-2"></i>
                                            <span id="level-title"></span>
                                        </h6>
                                        <p class="mb-0 small" id="level-description"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Marks -->
                <div class="setup-step" id="step-2" style="display: none;">
                    <div class="card shadow mb-4">
                        <div class="card-header bg-primary text-white py-3">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-line me-2"></i>
                                {{ __('app.previous_semester_marks') }}
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div id="marks-content">
                                <!-- Marks will be loaded dynamically -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Complete -->
                <div class="setup-step" id="step-3" style="display: none;">
                    <div class="card shadow mb-4">
                        <div class="card-header bg-success text-white py-3">
                            <h5 class="mb-0">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ __('app.profile_setup_complete') }}
                            </h5>
                        </div>
                        <div class="card-body text-center p-5">
                            <div class="mb-4">
                                <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                                <h3 class="text-success mb-2">{{ __('app.congratulations') }}!</h3>
                                <p class="lead">{{ __('app.profile_setup_success_message') }}</p>
                            </div>

                            <div class="row text-start mb-4">
                                <div class="col-md-6">
                                    <div class="card border-warning mb-3">
                                        <div class="card-body">
                                            <h6 class="card-title text-warning">
                                                <i class="fas fa-certificate me-2"></i>
                                                {{ __('app.birth_certificate_status') }}
                                            </h6>
                                            <p class="mb-0">
                                                <span class="badge bg-warning">{{ __('app.pending_review') }}</span>
                                            </p>
                                            <small class="text-muted">{{ __('app.admin_will_review') }}</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-success mb-3">
                                        <div class="card-body">
                                            <h6 class="card-title text-success">
                                                <i class="fas fa-chart-line me-2"></i>
                                                {{ __('app.marks_submitted') }}
                                            </h6>
                                            <p class="mb-0">
                                                <span class="badge bg-success">{{ __('app.completed') }}</span>
                                            </p>
                                            <small class="text-muted" id="marks-count">{{ $user->getRequiredPreviousMarks() }} {{ __('app.marks_recorded') }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info text-start">
                                <h6 class="alert-heading">
                                    <i class="fas fa-lightbulb me-2"></i>
                                    {{ __('app.what_happens_next') }}
                                </h6>
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-1"><i class="fas fa-clock text-warning me-2"></i>{{ __('app.birth_certificate_review') }}</li>
                                    <li class="mb-1"><i class="fas fa-envelope text-primary me-2"></i>{{ __('app.notification_when_approved') }}</li>
                                    <li class="mb-1"><i class="fas fa-users text-success me-2"></i>{{ __('app.can_join_create_teams') }}</li>
                                    <li class="mb-1"><i class="fas fa-book text-info me-2"></i>{{ __('app.can_browse_subjects') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary" id="prev-btn" onclick="changeStep(-1)" style="display: none;">
                                <i class="fas fa-arrow-left me-2"></i>
                                {{ __('app.back') }}
                            </button>
                            <div></div>
                            <button type="button" class="btn btn-primary btn-lg" id="next-btn" onclick="changeStep(1)">
                                {{ __('app.continue') }}
                                <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                            <button type="submit" class="btn btn-success btn-lg" id="finish-btn" formaction="{{ route('student.setup.finish') }}" style="display: none;">
                                <i class="fas fa-home me-2"></i>
                                {{ __('app.go_to_dashboard') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('setup-form');
    const currentStepSpan = document.getElementById('current-step');
    const progressBar = document.getElementById('progress-bar');
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    const finishBtn = document.getElementById('finish-btn');
    
    let currentStep = 1;
    let formData = {};

    // Level requirements info
    const levelInfo = {
        'licence_3': {
            title: '{{ __("app.licence_3_students") }}',
            description: '{{ __("app.licence_3_requirements") }}'
        },
        'master_1': {
            title: '{{ __("app.master_students") }}',
            description: '{{ __("app.master_requirements") }}'
        },
        'master_2': {
            title: '{{ __("app.master_students") }}',
            description: '{{ __("app.master_requirements") }}'
        }
    };

    // Show level info when level is selected
    document.getElementById('student_level').addEventListener('change', function() {
        const level = this.value;
        const infoDiv = document.getElementById('level-info');
        const titleSpan = document.getElementById('level-title');
        const descSpan = document.getElementById('level-description');
        
        if (level && levelInfo[level]) {
            titleSpan.textContent = levelInfo[level].title;
            descSpan.textContent = levelInfo[level].description;
            infoDiv.style.display = 'block';
        } else {
            infoDiv.style.display = 'none';
        }
    });

    // File size validation
    document.getElementById('birth_certificate').addEventListener('change', function() {
        const file = this.files[0];
        if (file && file.size > 2048 * 1024) {
            alert('{{ __("app.file_too_large") }}');
            this.value = '';
        }
    });

    window.changeStep = function(direction) {
        if (direction === 1 && !validateCurrentStep()) {
            return;
        }

        // Hide current step
        document.getElementById(`step-${currentStep}`).style.display = 'none';
        
        // Update step
        currentStep += direction;
        
        // Show new step
        document.getElementById(`step-${currentStep}`).style.display = 'block';
        
        // Update UI
        updateUI();
        
        // Load step-specific content
        if (currentStep === 2) {
            loadMarksStep();
        } else if (currentStep === 3) {
            loadCompleteStep();
        }
    };

    function validateCurrentStep() {
        if (currentStep === 1) {
            const dateNaissance = document.getElementById('date_naissance').value;
            const lieuNaissance = document.getElementById('lieu_naissance').value;
            const studentLevel = document.getElementById('student_level').value;
            const birthCertificate = document.getElementById('birth_certificate').files.length;
            
            if (!dateNaissance || !lieuNaissance || !studentLevel || !birthCertificate) {
                alert('{{ __("app.fill_all_required_fields") }}');
                return false;
            }
        }
        return true;
    }

    function updateUI() {
        currentStepSpan.textContent = currentStep;
        progressBar.style.width = (currentStep * 33.33) + '%';
        
        // Update buttons
        prevBtn.style.display = currentStep > 1 ? 'block' : 'none';
        nextBtn.style.display = currentStep < 3 ? 'block' : 'none';
        finishBtn.style.display = currentStep === 3 ? 'block' : 'none';
        
        // Update progress bar color
        progressBar.className = 'progress-bar bg-' + (currentStep === 3 ? 'success' : 'primary');
    }

    function loadMarksStep() {
        const studentLevel = document.getElementById('student_level').value;
        const marksContent = document.getElementById('marks-content');
        
        // Get required marks based on level
        let requiredMarks = 2; // Default for Master
        if (studentLevel === 'licence_3') {
            requiredMarks = 4; // Licence 3 needs 4 marks (S1, S2, S3, S4)
        } else if (studentLevel === 'master_1' || studentLevel === 'master_2') {
            requiredMarks = 2; // Master needs 2 marks (previous year)
        }
        
        let marksHtml = `
            <div class="alert alert-info">
                <h6 class="alert-heading">
                    <i class="fas fa-info-circle me-2"></i>
                    ${studentLevel === 'licence_3' ? '{{ __("app.licence_3_marks_explanation") }}' : '{{ __("app.master_marks_explanation") }}'}
                </h6>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-chart-line me-2"></i>
                                {{ __('app.marks_entry') }}
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
        `;
        
        for (let i = 1; i <= requiredMarks; i++) {
            marksHtml += `
                <div class="col-md-6 mb-3">
                    <label for="semester_${i}_mark" class="form-label fw-bold">
                        {{ __('app.mark') }} ${i}
                        ${studentLevel !== 'licence_3' ? '- {{ __("app.previous_year") }}' : ''}
                        <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <input type="number"
                               class="form-control mark-input"
                               id="semester_${i}_mark"
                               name="semester_${i}_mark"
                               step="0.01"
                               min="0"
                               max="20"
                               placeholder="0.00"
                               required>
                        <span class="input-group-text">/20</span>
                    </div>
                    <small class="text-muted">{{ __('app.enter_overall_average') }}</small>
                </div>
            `;
        }
        
        marksHtml += `
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
        `;
        
        marksContent.innerHTML = marksHtml;
        
        // Add mark input validation
        document.querySelectorAll('.mark-input').forEach(input => {
            input.addEventListener('input', function() {
                const value = parseFloat(this.value);
                if (value < 0) {
                    this.value = 0;
                } else if (value > 20) {
                    this.value = 20;
                }
                
                // Visual feedback
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
    }

    function loadCompleteStep() {
        // Submit personal info and marks data
        submitData();
    }

    function submitData() {
        const formData = new FormData(form);
        
        // Submit personal info first
        fetch('{{ route("student.setup.store-personal-info") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        }).then(response => {
            if (response.ok) {
                // Submit marks
                return fetch('{{ route("student.setup.store-marks") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
            }
            throw new Error('Personal info submission failed');
        }).then(response => {
            if (response.ok) {
                // Mark profile as complete
                return fetch('{{ route("student.setup.complete") }}', {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
            }
            throw new Error('Marks submission failed');
        }).catch(error => {
            console.error('Submission error:', error);
            alert('{{ __("app.submission_error") }}');
        });
    }

    // Initialize
    updateUI();
});
</script>
@endpush