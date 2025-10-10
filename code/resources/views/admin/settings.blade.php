@extends('layouts.pfe-app')

@section('page-title', __('app.system_settings'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ __('app.system_settings') }}</h4>
                    <small class="text-muted">{{ __('app.configure_system_settings') }}</small>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Navigation Tabs -->
                    <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="university-tab" data-bs-toggle="tab" data-bs-target="#university" type="button" role="tab">
                                <i class="fas fa-university me-2"></i>{{ __('app.university_information') }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                                <i class="fas fa-cog me-2"></i>{{ __('app.general_settings') }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="team-tab" data-bs-toggle="tab" data-bs-target="#team" type="button" role="tab">
                                <i class="fas fa-users me-2"></i>{{ __('app.team_settings') }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="defense-tab" data-bs-toggle="tab" data-bs-target="#defense" type="button" role="tab">
                                <i class="fas fa-gavel me-2"></i>{{ __('app.defense_settings') }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="files-tab" data-bs-toggle="tab" data-bs-target="#files" type="button" role="tab">
                                <i class="fas fa-file-upload me-2"></i>{{ __('app.file_upload_settings') }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="email-tab" data-bs-toggle="tab" data-bs-target="#email" type="button" role="tab">
                                <i class="fas fa-envelope me-2"></i>{{ __('app.email_settings') }}
                            </button>
                        </li>
                    </ul>

                    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Tab Content -->
                        <div class="tab-content mt-3" id="settingsTabContent">
                            <!-- University Information Tab -->
                            <div class="tab-pane fade show active" id="university" role="tabpanel">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">{{ __('app.university_information') }}</h5>
                                        <small class="text-muted">{{ __('app.configure_university_details') }}</small>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="university_logo" class="form-label">{{ __('app.university_logo') }}</label>
                                                    @if($currentLogo)
                                                        <div class="mb-2">
                                                            <img src="{{ $currentLogo }}" alt="{{ __('app.current_logo') }}" class="img-thumbnail" style="max-height: 100px;">
                                                        </div>
                                                    @endif
                                                    <input type="file" class="form-control @error('university_logo') is-invalid @enderror"
                                                           id="university_logo" name="university_logo" accept="image/*">
                                                    @error('university_logo')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <small class="form-text text-muted">{{ __('app.max_size_formats') }}</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="university_name_fr" class="form-label">{{ __('app.university_name_french') }}</label>
                                                    <input type="text" class="form-control" id="university_name_fr" name="university_name_fr"
                                                           value="{{ $universityInfo['name_fr'] }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="faculty_name_fr" class="form-label">{{ __('app.faculty_name_french') }}</label>
                                                    <input type="text" class="form-control" id="faculty_name_fr" name="faculty_name_fr"
                                                           value="{{ $universityInfo['faculty_fr'] }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="department_name_fr" class="form-label">{{ __('app.department_name_french') }}</label>
                                                    <input type="text" class="form-control" id="department_name_fr" name="department_name_fr"
                                                           value="{{ $universityInfo['department_fr'] }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="ministry_name_fr" class="form-label">{{ __('app.ministry_name_french') }}</label>
                                                    <input type="text" class="form-control" id="ministry_name_fr" name="ministry_name_fr"
                                                           value="{{ $universityInfo['ministry_fr'] }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="republic_name_fr" class="form-label">{{ __('app.republic_name_french') }}</label>
                                                    <input type="text" class="form-control" id="republic_name_fr" name="republic_name_fr"
                                                           value="{{ $universityInfo['republic_fr'] }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="university_name_ar" class="form-label">{{ __('app.university_name_arabic') }}</label>
                                                    <input type="text" class="form-control" id="university_name_ar" name="university_name_ar"
                                                           value="{{ $universityInfo['name_ar'] }}" dir="rtl">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="faculty_name_ar" class="form-label">{{ __('app.faculty_name_arabic') }}</label>
                                                    <input type="text" class="form-control" id="faculty_name_ar" name="faculty_name_ar"
                                                           value="{{ $universityInfo['faculty_ar'] }}" dir="rtl">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="department_name_ar" class="form-label">{{ __('app.department_name_arabic') }}</label>
                                                    <input type="text" class="form-control" id="department_name_ar" name="department_name_ar"
                                                           value="{{ $universityInfo['department_ar'] }}" dir="rtl">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="ministry_name_ar" class="form-label">{{ __('app.ministry_name_arabic') }}</label>
                                                    <input type="text" class="form-control" id="ministry_name_ar" name="ministry_name_ar"
                                                           value="{{ $universityInfo['ministry_ar'] }}" dir="rtl">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="republic_name_ar" class="form-label">{{ __('app.republic_name_arabic') }}</label>
                                                    <input type="text" class="form-control" id="republic_name_ar" name="republic_name_ar"
                                                           value="{{ $universityInfo['republic_ar'] }}" dir="rtl">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- General Settings Tab -->
                            <div class="tab-pane fade" id="general" role="tabpanel">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">{{ __('app.general_settings') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="site_name" class="form-label">{{ __('app.site_name') }}</label>
                                            <input type="text" class="form-control" id="site_name" name="site_name"
                                                   value="PFE Management System" placeholder="Site Name">
                                        </div>

                                        <div class="mb-3">
                                            <label for="site_description" class="form-label">{{ __('app.site_description') }}</label>
                                            <textarea class="form-control" id="site_description" name="site_description" rows="3"
                                                      placeholder="{{ __('app.site_description') }}">Final Year Project Management System for University</textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label for="academic_year" class="form-label">{{ __('app.current_academic_year') }}</label>
                                            <input type="text" class="form-control" id="academic_year" name="academic_year"
                                                   value="2024-2025" placeholder="2024-2025">
                                        </div>

                                        <div class="mb-3">
                                            <label for="semester" class="form-label">{{ __('app.current_semester') }}</label>
                                            <select class="form-select" id="semester" name="semester">
                                                <option value="1">{{ __('app.semester_1') }}</option>
                                                <option value="2" selected>{{ __('app.semester_2') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Team Settings Tab -->
                            <div class="tab-pane fade" id="team" role="tabpanel">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">{{ __('app.team_settings') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="min_team_size" class="form-label">{{ __('app.minimum_team_size') }}</label>
                                            <input type="number" class="form-control" id="min_team_size" name="min_team_size"
                                                   value="2" min="1" max="10">
                                        </div>

                                        <div class="mb-3">
                                            <label for="max_team_size" class="form-label">{{ __('app.maximum_team_size') }}</label>
                                            <input type="number" class="form-control" id="max_team_size" name="max_team_size"
                                                   value="4" min="1" max="10">
                                        </div>

                                        <div class="mb-3">
                                            <label for="team_formation_deadline" class="form-label">{{ __('app.team_formation_deadline') }}</label>
                                            <input type="date" class="form-control" id="team_formation_deadline" name="team_formation_deadline"
                                                   value="{{ date('Y-m-d', strtotime('+30 days')) }}">
                                        </div>

                                        <div class="mb-3">
                                            <label for="subject_selection_deadline" class="form-label">{{ __('app.subject_selection_deadline') }}</label>
                                            <input type="date" class="form-control" id="subject_selection_deadline" name="subject_selection_deadline"
                                                   value="{{ date('Y-m-d', strtotime('+45 days')) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Defense Settings Tab -->
                            <div class="tab-pane fade" id="defense" role="tabpanel">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">{{ __('app.defense_settings') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="defense_duration" class="form-label">{{ __('app.default_defense_duration') }}</label>
                                            <input type="number" class="form-control" id="defense_duration" name="defense_duration"
                                                   value="60" min="30" max="180">
                                        </div>

                                        <div class="mb-3">
                                            <label for="defense_start_time" class="form-label">{{ __('app.defense_start_time') }}</label>
                                            <input type="time" class="form-control" id="defense_start_time" name="defense_start_time"
                                                   value="08:00">
                                        </div>

                                        <div class="mb-3">
                                            <label for="defense_end_time" class="form-label">{{ __('app.defense_end_time') }}</label>
                                            <input type="time" class="form-control" id="defense_end_time" name="defense_end_time"
                                                   value="17:00">
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="weekend_defenses" name="weekend_defenses">
                                                <label class="form-check-label" for="weekend_defenses">
                                                    {{ __('app.allow_weekend_defenses') }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- File Upload Settings Tab -->
                            <div class="tab-pane fade" id="files" role="tabpanel">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">{{ __('app.file_upload_settings') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="max_file_size" class="form-label">{{ __('app.maximum_file_size') }}</label>
                                            <input type="number" class="form-control" id="max_file_size" name="max_file_size"
                                                   value="20" min="1" max="100">
                                        </div>

                                        <div class="mb-3">
                                            <label for="allowed_file_types" class="form-label">{{ __('app.allowed_file_types') }}</label>
                                            <input type="text" class="form-control" id="allowed_file_types" name="allowed_file_types"
                                                   value="pdf,doc,docx,ppt,pptx,txt,zip,rar" placeholder="pdf,doc,docx">
                                            <small class="form-text text-muted">{{ __('app.separate_extensions_commas') }}</small>
                                        </div>

                                        <div class="mb-3">
                                            <label for="max_files_per_submission" class="form-label">{{ __('app.max_files_per_submission') }}</label>
                                            <input type="number" class="form-control" id="max_files_per_submission" name="max_files_per_submission"
                                                   value="5" min="1" max="20">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Email Settings Tab -->
                            <div class="tab-pane fade" id="email" role="tabpanel">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">{{ __('app.email_settings') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="email_notifications" name="email_notifications" checked>
                                                        <label class="form-check-label" for="email_notifications">
                                                            {{ __('app.enable_email_notifications') }}
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="defense_reminders" name="defense_reminders" checked>
                                                        <label class="form-check-label" for="defense_reminders">
                                                            {{ __('app.send_defense_reminders') }}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="deadline_reminders" name="deadline_reminders" checked>
                                                        <label class="form-check-label" for="deadline_reminders">
                                                            {{ __('app.send_deadline_reminders') }}
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="grade_notifications" name="grade_notifications" checked>
                                                        <label class="form-check-label" for="grade_notifications">
                                                            {{ __('app.send_grade_notifications') }}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('app.save_settings') }}
                            </button>
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> {{ __('app.back_to_dashboard') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection