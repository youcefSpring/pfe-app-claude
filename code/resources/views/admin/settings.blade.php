@extends('layouts.pfe-app')

@section('page-title', 'System Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">System Settings</h4>
                    <small class="text-muted">Configure system-wide settings</small>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- University Information -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">University Information</h5>
                                        <small class="text-muted">Configure university details for official documents</small>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="university_logo" class="form-label">University Logo</label>
                                                    @if($currentLogo)
                                                        <div class="mb-2">
                                                            <img src="{{ $currentLogo }}" alt="Current Logo" class="img-thumbnail" style="max-height: 100px;">
                                                        </div>
                                                    @endif
                                                    <input type="file" class="form-control @error('university_logo') is-invalid @enderror"
                                                           id="university_logo" name="university_logo" accept="image/*">
                                                    @error('university_logo')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <small class="form-text text-muted">Max size: 2MB. Formats: JPG, PNG, GIF</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="university_name_fr" class="form-label">University Name (French)</label>
                                                    <input type="text" class="form-control" id="university_name_fr" name="university_name_fr"
                                                           value="{{ $universityInfo['name_fr'] }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="faculty_name_fr" class="form-label">Faculty Name (French)</label>
                                                    <input type="text" class="form-control" id="faculty_name_fr" name="faculty_name_fr"
                                                           value="{{ $universityInfo['faculty_fr'] }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="department_name_fr" class="form-label">Department Name (French)</label>
                                                    <input type="text" class="form-control" id="department_name_fr" name="department_name_fr"
                                                           value="{{ $universityInfo['department_fr'] }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="ministry_name_fr" class="form-label">Ministry Name (French)</label>
                                                    <input type="text" class="form-control" id="ministry_name_fr" name="ministry_name_fr"
                                                           value="{{ $universityInfo['ministry_fr'] }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="republic_name_fr" class="form-label">Republic Name (French)</label>
                                                    <input type="text" class="form-control" id="republic_name_fr" name="republic_name_fr"
                                                           value="{{ $universityInfo['republic_fr'] }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="university_name_ar" class="form-label">University Name (Arabic)</label>
                                                    <input type="text" class="form-control" id="university_name_ar" name="university_name_ar"
                                                           value="{{ $universityInfo['name_ar'] }}" dir="rtl">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="faculty_name_ar" class="form-label">Faculty Name (Arabic)</label>
                                                    <input type="text" class="form-control" id="faculty_name_ar" name="faculty_name_ar"
                                                           value="{{ $universityInfo['faculty_ar'] }}" dir="rtl">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="department_name_ar" class="form-label">Department Name (Arabic)</label>
                                                    <input type="text" class="form-control" id="department_name_ar" name="department_name_ar"
                                                           value="{{ $universityInfo['department_ar'] }}" dir="rtl">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="ministry_name_ar" class="form-label">Ministry Name (Arabic)</label>
                                                    <input type="text" class="form-control" id="ministry_name_ar" name="ministry_name_ar"
                                                           value="{{ $universityInfo['ministry_ar'] }}" dir="rtl">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="republic_name_ar" class="form-label">Republic Name (Arabic)</label>
                                                    <input type="text" class="form-control" id="republic_name_ar" name="republic_name_ar"
                                                           value="{{ $universityInfo['republic_ar'] }}" dir="rtl">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">General Settings</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="site_name" class="form-label">Site Name</label>
                                            <input type="text" class="form-control" id="site_name" name="site_name"
                                                   value="PFE Management System" placeholder="Site Name">
                                        </div>

                                        <div class="mb-3">
                                            <label for="site_description" class="form-label">Site Description</label>
                                            <textarea class="form-control" id="site_description" name="site_description" rows="3"
                                                      placeholder="Site description">Final Year Project Management System for University</textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label for="academic_year" class="form-label">Current Academic Year</label>
                                            <input type="text" class="form-control" id="academic_year" name="academic_year"
                                                   value="2024-2025" placeholder="2024-2025">
                                        </div>

                                        <div class="mb-3">
                                            <label for="semester" class="form-label">Current Semester</label>
                                            <select class="form-select" id="semester" name="semester">
                                                <option value="1">Semester 1</option>
                                                <option value="2" selected>Semester 2</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Team Settings</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="min_team_size" class="form-label">Minimum Team Size</label>
                                            <input type="number" class="form-control" id="min_team_size" name="min_team_size"
                                                   value="2" min="1" max="10">
                                        </div>

                                        <div class="mb-3">
                                            <label for="max_team_size" class="form-label">Maximum Team Size</label>
                                            <input type="number" class="form-control" id="max_team_size" name="max_team_size"
                                                   value="4" min="1" max="10">
                                        </div>

                                        <div class="mb-3">
                                            <label for="team_formation_deadline" class="form-label">Team Formation Deadline</label>
                                            <input type="date" class="form-control" id="team_formation_deadline" name="team_formation_deadline"
                                                   value="{{ date('Y-m-d', strtotime('+30 days')) }}">
                                        </div>

                                        <div class="mb-3">
                                            <label for="subject_selection_deadline" class="form-label">Subject Selection Deadline</label>
                                            <input type="date" class="form-control" id="subject_selection_deadline" name="subject_selection_deadline"
                                                   value="{{ date('Y-m-d', strtotime('+45 days')) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Defense Settings</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="defense_duration" class="form-label">Default Defense Duration (minutes)</label>
                                            <input type="number" class="form-control" id="defense_duration" name="defense_duration"
                                                   value="60" min="30" max="180">
                                        </div>

                                        <div class="mb-3">
                                            <label for="defense_start_time" class="form-label">Defense Start Time</label>
                                            <input type="time" class="form-control" id="defense_start_time" name="defense_start_time"
                                                   value="08:00">
                                        </div>

                                        <div class="mb-3">
                                            <label for="defense_end_time" class="form-label">Defense End Time</label>
                                            <input type="time" class="form-control" id="defense_end_time" name="defense_end_time"
                                                   value="17:00">
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="weekend_defenses" name="weekend_defenses">
                                                <label class="form-check-label" for="weekend_defenses">
                                                    Allow Weekend Defenses
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">File Upload Settings</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="max_file_size" class="form-label">Maximum File Size (MB)</label>
                                            <input type="number" class="form-control" id="max_file_size" name="max_file_size"
                                                   value="20" min="1" max="100">
                                        </div>

                                        <div class="mb-3">
                                            <label for="allowed_file_types" class="form-label">Allowed File Types</label>
                                            <input type="text" class="form-control" id="allowed_file_types" name="allowed_file_types"
                                                   value="pdf,doc,docx,ppt,pptx,txt,zip,rar" placeholder="pdf,doc,docx">
                                            <small class="form-text text-muted">Separate file extensions with commas</small>
                                        </div>

                                        <div class="mb-3">
                                            <label for="max_files_per_submission" class="form-label">Max Files per Submission</label>
                                            <input type="number" class="form-control" id="max_files_per_submission" name="max_files_per_submission"
                                                   value="5" min="1" max="20">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Email Settings</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="email_notifications" name="email_notifications" checked>
                                                        <label class="form-check-label" for="email_notifications">
                                                            Enable Email Notifications
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="defense_reminders" name="defense_reminders" checked>
                                                        <label class="form-check-label" for="defense_reminders">
                                                            Send Defense Reminders
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="deadline_reminders" name="deadline_reminders" checked>
                                                        <label class="form-check-label" for="deadline_reminders">
                                                            Send Deadline Reminders
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="grade_notifications" name="grade_notifications" checked>
                                                        <label class="form-check-label" for="grade_notifications">
                                                            Send Grade Notifications
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
                                <i class="fas fa-save"></i> Save Settings
                            </button>
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Dashboard
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection