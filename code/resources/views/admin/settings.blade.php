@extends('layouts.pfe-app')

@section('page-title', __('app.system_settings'))

@section('content')
<div class="container-fluid">
    <!-- Compact Page Header -->
    <div class="page-header-compact">
        <h1>
            <i class="bi bi-gear"></i>
            {{ __('app.system_settings') }}
        </h1>
    </div>

    <!-- Modern Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show alert-modern" role="alert">
            <div class="d-flex align-items-center">
                <div class="alert-icon">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="alert-content">
                    <strong>{{ __('app.success') }}!</strong>
                    <p class="mb-0">{{ session('success') }}</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show alert-modern" role="alert">
            <div class="d-flex align-items-center">
                <div class="alert-icon">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div class="alert-content">
                    <strong>{{ __('app.error') }}!</strong>
                    <p class="mb-0">{{ session('error') }}</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show alert-modern" role="alert">
            <div class="d-flex align-items-center">
                <div class="alert-icon">
                    <i class="bi bi-info-circle-fill"></i>
                </div>
                <div class="alert-content">
                    <strong>{{ __('app.info') }}!</strong>
                    <p class="mb-0">{{ session('info') }}</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    <!-- Actions Row -->
    <div class="d-flex justify-content-end mb-3">
        <div class="d-flex gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> {{ __('app.back_to_dashboard') }}
            </a>
            <button type="button" class="btn btn-warning" onclick="resetToDefaults()">
                <i class="bi bi-arrow-counterclockwise"></i> {{ __('app.reset_to_defaults') }}
            </button>
        </div>
    </div>

    <!-- Horizontal Tabs -->
    <div class="nav nav-pills mb-4 flex-nowrap overflow-auto pb-2 gap-2" id="settingsTab" role="tablist">
        <button class="nav-link active text-nowrap" id="university-tab" data-bs-toggle="tab" data-bs-target="#university" type="button" role="tab">
            <i class="bi bi-building me-2"></i> {{ __('app.university_information') }}
        </button>
        <button class="nav-link text-nowrap" id="system-tab" data-bs-toggle="tab" data-bs-target="#system" type="button" role="tab">
            <i class="bi bi-gear me-2"></i> {{ __('app.system_configuration') }}
        </button>
        <button class="nav-link text-nowrap" id="team-tab" data-bs-toggle="tab" data-bs-target="#team" type="button" role="tab">
            <i class="bi bi-people me-2"></i> {{ __('app.team_settings') }}
        </button>
        <button class="nav-link text-nowrap" id="subject-tab" data-bs-toggle="tab" data-bs-target="#subject" type="button" role="tab">
            <i class="bi bi-journal-text me-2"></i> {{ __('app.subject_settings') }}
        </button>
        <button class="nav-link text-nowrap" id="registration-tab" data-bs-toggle="tab" data-bs-target="#registration" type="button" role="tab">
            <i class="bi bi-person-plus me-2"></i> {{ __('app.registration_settings') }}
        </button>
        <button class="nav-link text-nowrap" id="defense-tab" data-bs-toggle="tab" data-bs-target="#defense" type="button" role="tab">
            <i class="bi bi-shield-check me-2"></i> {{ __('app.defense_settings') }}
        </button>
        <button class="nav-link text-nowrap" id="notification-tab" data-bs-toggle="tab" data-bs-target="#notification" type="button" role="tab">
            <i class="bi bi-bell me-2"></i> {{ __('app.notification_settings') }}
        </button>
        <button class="nav-link text-nowrap" id="allocation-tab" data-bs-toggle="tab" data-bs-target="#allocation" type="button" role="tab">
            <i class="bi bi-diagram-3 me-2"></i> {{ __('app.allocation_settings') }}
        </button>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-12">
            <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="tab-content" id="settingsTabContent">
                    <!-- University Information Tab -->
                    <div class="tab-pane fade show active" id="university" role="tabpanel">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="bi bi-building me-2"></i>
                                    {{ __('app.university_information') }}
                                </h5>
                                <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#universityHelpModal">
                                    <i class="bi bi-question-circle"></i> {{ __('app.help') }}
                                </button>
                            </div>
                            <div class="card-body">
                                <!-- Logo Upload Section -->
                                <div class="logo-upload-section mb-4">
                                    <label for="university_logo" class="form-label-compact d-flex align-items-center">
                                        <i class="bi bi-image me-2"></i>
                                        {{ __('app.university_logo') }}
                                    </label>
                                    <div class="row align-items-center">
                                        <div class="col-md-4">
                                            @if($currentLogo)
                                                <div class="logo-preview">
                                                    <img src="{{ $currentLogo }}" alt="{{ __('app.current_logo') }}"
                                                         class="img-thumbnail rounded shadow-sm" style="max-height: 120px; width: auto;">
                                                </div>
                                            @else
                                                <div class="logo-preview-placeholder">
                                                    <i class="bi bi-image" style="font-size: 3rem; color: #ccc;"></i>
                                                    <p class="text-muted small">{{ __('app.no_logo') }}</p>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-8">
                                            <input type="file" class="form-control-compact @error('university_logo') is-invalid @enderror"
                                                   id="university_logo" name="university_logo" accept="image/*">
                                            @error('university_logo')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text-compact text-muted d-block mt-1">
                                                <i class="bi bi-info-circle"></i>
                                                {{ __('app.max_2mb_png_jpg') }}
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <!-- Bilingual Names Section -->
                                <div class="row">
                                    <!-- French Names Column -->
                                    <div class="col-md-6">
                                        <div class="form-section-compact">
                                            <div class="form-section-header mb-3">
                                                <h6 class="mb-0 d-flex align-items-center">
                                                    <i class="bi bi-translate me-2"></i>
                                                    {{ __('app.french_names') }}
                                                </h6>
                                            </div>

                                            <div class="form-group-compact">
                                                <label for="university_name_fr" class="form-label-compact required">
                                                    {{ __('app.university_name') }}
                                                </label>
                                                <input type="text" class="form-control-compact" id="university_name_fr"
                                                       name="university_name_fr" value="{{ $universityInfo['name_fr'] }}" required>
                                            </div>

                                            <div class="form-group-compact">
                                                <label for="faculty_name_fr" class="form-label-compact required">
                                                    {{ __('app.faculty_name') }}
                                                </label>
                                                <input type="text" class="form-control-compact" id="faculty_name_fr"
                                                       name="faculty_name_fr" value="{{ $universityInfo['faculty_fr'] }}" required>
                                            </div>

                                            <div class="form-group-compact">
                                                <label for="department_name_fr" class="form-label-compact required">
                                                    {{ __('app.department_name') }}
                                                </label>
                                                <input type="text" class="form-control-compact" id="department_name_fr"
                                                       name="department_name_fr" value="{{ $universityInfo['department_fr'] }}" required>
                                            </div>

                                            <div class="form-group-compact">
                                                <label for="ministry_name_fr" class="form-label-compact">
                                                    {{ __('app.ministry_name') }}
                                                </label>
                                                <input type="text" class="form-control-compact" id="ministry_name_fr"
                                                       name="ministry_name_fr" value="{{ $universityInfo['ministry_fr'] }}">
                                            </div>

                                            <div class="form-group-compact">
                                                <label for="republic_name_fr" class="form-label-compact">
                                                    {{ __('app.republic_name') }}
                                                </label>
                                                <input type="text" class="form-control-compact" id="republic_name_fr"
                                                       name="republic_name_fr" value="{{ $universityInfo['republic_fr'] }}">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Arabic Names Column -->
                                    <div class="col-md-6">
                                        <div class="form-section-compact">
                                            <div class="form-section-header mb-3">
                                                <h6 class="mb-0 d-flex align-items-center">
                                                    <i class="bi bi-translate me-2"></i>
                                                    {{ __('app.arabic_names') }}
                                                </h6>
                                            </div>

                                            <div class="form-group-compact">
                                                <label for="university_name_ar" class="form-label-compact required">
                                                    {{ __('app.university_name') }}
                                                </label>
                                                <input type="text" class="form-control-compact" id="university_name_ar"
                                                       name="university_name_ar" value="{{ $universityInfo['name_ar'] }}" dir="rtl" required>
                                            </div>

                                            <div class="form-group-compact">
                                                <label for="faculty_name_ar" class="form-label-compact required">
                                                    {{ __('app.faculty_name') }}
                                                </label>
                                                <input type="text" class="form-control-compact" id="faculty_name_ar"
                                                       name="faculty_name_ar" value="{{ $universityInfo['faculty_ar'] }}" dir="rtl" required>
                                            </div>

                                            <div class="form-group-compact">
                                                <label for="department_name_ar" class="form-label-compact required">
                                                    {{ __('app.department_name') }}
                                                </label>
                                                <input type="text" class="form-control-compact" id="department_name_ar"
                                                       name="department_name_ar" value="{{ $universityInfo['department_ar'] }}" dir="rtl" required>
                                            </div>

                                            <div class="form-group-compact">
                                                <label for="ministry_name_ar" class="form-label-compact">
                                                    {{ __('app.ministry_name') }}
                                                </label>
                                                <input type="text" class="form-control-compact" id="ministry_name_ar"
                                                       name="ministry_name_ar" value="{{ $universityInfo['ministry_ar'] }}" dir="rtl">
                                            </div>

                                            <div class="form-group-compact">
                                                <label for="republic_name_ar" class="form-label-compact">
                                                    {{ __('app.republic_name') }}
                                                </label>
                                                <input type="text" class="form-control-compact" id="republic_name_ar"
                                                       name="republic_name_ar" value="{{ $universityInfo['republic_ar'] }}" dir="rtl">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- System Settings Tab -->
                    <div class="tab-pane fade" id="system" role="tabpanel">
                        <div class="card shadow-sm">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">
                                    <i class="bi bi-gear me-2"></i>
                                    {{ __('app.system_configuration') }}
                                </h5>
                                <p class="text-white-50 mb-0 mt-2 small">Configure system-wide settings and preferences</p>
                            </div>
                            <div class="card-body settings-grid">
                                <div class="row">
                                    @forelse($settingsGroups['system'] ?? [] as $setting)
                                        <div class="col-md-6">
                                            @include('admin.partials.setting-field', ['setting' => $setting])
                                        </div>
                                    @empty
                                        <div class="col-12">
                                            <div class="empty-state">
                                                <i class="bi bi-inbox"></i>
                                                <p>{{ __('app.no_settings_available') }}</p>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Team Settings Tab -->
                    <div class="tab-pane fade" id="team" role="tabpanel">
                        <div class="box box-success">
                            <div class="box-header with-border">
                                <h3 class="box-title">
                                    <i class="bi bi-people"></i>
                                    {{ __('app.team_settings') }}
                                </h5>
                                <p class="text-white-50 mb-0 mt-2 small">{{ __('app.configure_team_formation_rules') }}</p>
                            </div>
                            <div class="card-body settings-grid">
                                <div class="row">
                                    @forelse($settingsGroups['team'] ?? [] as $setting)
                                        <div class="col-md-6">
                                            @include('admin.partials.setting-field', ['setting' => $setting])
                                        </div>
                                    @empty
                                        <div class="col-12">
                                            <div class="empty-state">
                                                <i class="bi bi-inbox"></i>
                                                <p>{{ __('app.no_settings_available') }}</p>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Subject Settings Tab -->
                    <div class="tab-pane fade" id="subject" role="tabpanel">
                        <div class="box box-warning">
                            <div class="box-header with-border">
                                <h3 class="box-title">
                                    <i class="bi bi-journal-text"></i>
                                    {{ __('app.subject_settings') }}
                                </h5>
                                <p class="text-white-50 mb-0 mt-2 small">{{ __('app.configure_subject_management_rules') }}</p>
                            </div>
                            <div class="card-body settings-grid">
                                <div class="row">
                                    @forelse($settingsGroups['subject'] ?? [] as $setting)
                                        <div class="col-md-6">
                                            @include('admin.partials.setting-field', ['setting' => $setting])
                                        </div>
                                    @empty
                                        <div class="col-12">
                                            <div class="empty-state">
                                                <i class="bi bi-inbox"></i>
                                                <p>{{ __('app.no_settings_available') }}</p>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Registration Settings Tab -->
                    <div class="tab-pane fade" id="registration" role="tabpanel">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">
                                    <i class="bi bi-person-plus"></i>
                                    {{ __('app.registration_settings') }}
                                </h5>
                                <p class="text-white-50 mb-0 mt-2 small">{{ __('app.configure_student_registration_process') }}</p>
                            </div>
                            <div class="card-body settings-grid">
                                <div class="row">
                                    @forelse($settingsGroups['registration'] ?? [] as $setting)
                                        <div class="col-md-6">
                                            @include('admin.partials.setting-field', ['setting' => $setting])
                                        </div>
                                    @empty
                                        <div class="col-12">
                                            <div class="empty-state">
                                                <i class="bi bi-inbox"></i>
                                                <p>{{ __('app.no_settings_available') }}</p>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Defense Settings Tab -->
                    <div class="tab-pane fade" id="defense" role="tabpanel">
                        <div class="box box-danger">
                            <div class="box-header with-border">
                                <h3 class="box-title">
                                    <i class="bi bi-shield-check"></i>
                                    {{ __('app.defense_settings') }}
                                </h5>
                                <p class="text-white-50 mb-0 mt-2 small">{{ __('app.configure_defense_scheduling_rules') }}</p>
                            </div>
                            <div class="card-body settings-grid">
                                <div class="row">
                                    @forelse($settingsGroups['defense'] ?? [] as $setting)
                                        <div class="col-md-6">
                                            @include('admin.partials.setting-field', ['setting' => $setting])
                                        </div>
                                    @empty
                                        <div class="col-12">
                                            <div class="empty-state">
                                                <i class="bi bi-inbox"></i>
                                                <p>{{ __('app.no_settings_available') }}</p>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notification Settings Tab -->
                    <div class="tab-pane fade" id="notification" role="tabpanel">
                        <div class="card shadow-sm">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">
                                    <i class="bi bi-bell"></i>
                                    {{ __('app.notification_settings') }}
                                </h5>
                                <p class="text-white-50 mb-0 mt-2 small">{{ __('app.configure_notification_preferences') }}</p>
                            </div>
                            <div class="card-body settings-grid">
                                <div class="row">
                                    @forelse($settingsGroups['notification'] ?? [] as $setting)
                                        <div class="col-md-6">
                                            @include('admin.partials.setting-field', ['setting' => $setting])
                                        </div>
                                    @empty
                                        <div class="col-12">
                                            <div class="empty-state">
                                                <i class="bi bi-inbox"></i>
                                                <p>{{ __('app.no_settings_available') }}</p>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Allocation Settings Tab -->
                    <div class="tab-pane fade" id="allocation" role="tabpanel">
                        <div class="box box-success">
                            <div class="box-header with-border">
                                <h3 class="box-title">
                                    <i class="bi bi-diagram-3"></i>
                                    {{ __('app.allocation_settings') }}
                                </h5>
                                <p class="text-white-50 mb-0 mt-2 small">{{ __('app.configure_subject_allocation_process') }}</p>
                            </div>
                            <div class="card-body settings-grid">
                                <div class="row">
                                    @forelse($settingsGroups['allocation'] ?? [] as $setting)
                                        <div class="col-md-6">
                                            @include('admin.partials.setting-field', ['setting' => $setting])
                                        </div>
                                    @empty
                                        <div class="col-12">
                                            <div class="empty-state">
                                                <i class="bi bi-inbox"></i>
                                                <p>{{ __('app.no_settings_available') }}</p>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Save Button (Sticky) -->
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="bi bi-info-circle"></i>
                            {{ __('app.changes_saved_immediately') }}
                        </small>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i>
                            {{ __('app.save_all_settings') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Help Modal -->
<x-info-modal id="universityHelpModal" title="{{ __('app.university_information_help') }}" icon="bi-building">
    <h6>{{ __('app.university_logo') }}</h6>
    <p>{{ __('app.university_logo_help_text') }}</p>

    <h6>{{ __('app.multilingual_names') }}</h6>
    <p>{{ __('app.multilingual_names_help_text') }}</p>

    <h6>{{ __('app.required_fields') }}</h6>
    <ul>
        <li>{{ __('app.university_name_required') }}</li>
        <li>{{ __('app.faculty_name_required') }}</li>
        <li>{{ __('app.department_name_required') }}</li>
    </ul>
</x-info-modal>
@endsection

@push('styles')
<style>
/* Modern Alert Styling */
.alert-modern {
    border: none;
    border-radius: 8px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    padding: 1rem 1.25rem;
}

.alert-modern .alert-icon {
    font-size: 1.5rem;
    margin-right: 1rem;
    display: flex;
    align-items: center;
}

.alert-modern .alert-content {
    flex: 1;
}

.alert-modern .alert-content strong {
    display: block;
    font-size: 0.95rem;
    margin-bottom: 0.25rem;
}

.alert-modern .alert-content p {
    font-size: 0.875rem;
    opacity: 0.9;
}

.alert-success.alert-modern {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    color: #155724;
}

.alert-danger.alert-modern {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    color: #721c24;
}

.alert-info.alert-modern {
    background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
    color: #0c5460;
}

/* Modern Horizontal Tab Styling */
.nav-pills .nav-link {
    border-radius: 0;
    border-bottom: 3px solid transparent;
    padding: 0.75rem 1rem;
    color: #6c757d;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    font-weight: 500;
    font-size: 0.9rem;
    background: transparent;
    margin-bottom: 0;
}

.nav-pills .nav-link:hover {
    color: #2c3e50;
    background: rgba(0,0,0,0.02);
}

.nav-pills .nav-link.active {
    background: transparent;
    color: #388bfd;
    border-bottom-color: #388bfd;
    box-shadow: none;
}

.nav-pills .nav-link i {
    font-size: 1.1rem;
}

/* Tab Content Animation */
.tab-pane {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Sticky Save Button */
.card-footer {
    position: sticky;
    bottom: 0;
    background: white;
    z-index: 100;
    box-shadow: 0 -4px 16px rgba(0,0,0,0.1);
    border-top: 2px solid #e9ecef;
}

/* 2-Column Layout for Settings */
.row .col-md-6 .mb-3 {
    height: 100%;
}

/* Logo Upload Section */
.logo-upload-section {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.logo-preview {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 120px;
}

.logo-preview-placeholder {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    min-height: 120px;
    background: #ffffff;
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    padding: 1rem;
}

.logo-preview-placeholder i {
    color: #adb5bd;
    margin-bottom: 0.5rem;
}

.logo-preview-placeholder p {
    margin: 0;
    color: #6c757d;
    font-size: 0.875rem;
}

/* Form Section Headers */
.form-section-compact .form-section-header {
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 0.5rem;
    margin-bottom: 1rem;
}

.form-section-compact .form-section-header h6 {
    color: #495057;
    font-weight: 600;
    font-size: 0.95rem;
}

/* Settings Grid Layout */
.settings-grid {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: #adb5bd;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    display: block;
}

.empty-state p {
    font-size: 1rem;
    margin: 0;
}

/* Card Header Improvements */
.card-header {
    border-bottom: 2px solid rgba(255,255,255,0.2);
    padding: 1rem 1.25rem;
}

.card-header .text-white-50 {
    font-size: 0.875rem;
}

.card-header h5 i {
    opacity: 0.9;
}

/* Card Styling */
.card {
    border: 1px solid rgba(0,0,0,0.125);
    border-radius: 8px;
    margin-bottom: 1.5rem;
}

.card-body {
    padding: 1.5rem;
}

/* Form Control Compact Styling */
.form-control-compact {
    border-radius: 6px;
    border: 1px solid #ced4da;
    padding: 0.5rem 0.75rem;
    font-size: 0.9rem;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.form-control-compact:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.form-label-compact {
    font-weight: 500;
    font-size: 0.9rem;
    color: #495057;
    margin-bottom: 0.5rem;
}

.form-label-compact.required::after {
    content: " *";
    color: #dc3545;
}

.form-group-compact {
    margin-bottom: 1rem;
}

.form-text-compact {
    font-size: 0.825rem;
    color: #6c757d;
}
</style>
@endpush

@push('scripts')
<script>
function resetToDefaults() {
    // Modern confirmation modal
    if (confirm('{{ __('app.confirm_reset_to_defaults') }}')) {
        // Show loading state
        const resetBtn = document.querySelector('button[onclick="resetToDefaults()"]');
        resetBtn.disabled = true;
        resetBtn.innerHTML = '<i class="bi bi-hourglass-split spinner-border spinner-border-sm"></i> {{ __('app.loading') }}...';

        // Send reset request to server
        fetch('{{ route('admin.settings.reset') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message and reload
                const alert = document.createElement('div');
                alert.className = 'alert alert-success alert-dismissible fade show alert-modern';
                alert.innerHTML = `
                    <div class="d-flex align-items-center">
                        <div class="alert-icon">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <div class="alert-content">
                            <strong>{{ __('app.success') }}!</strong>
                            <p class="mb-0">${data.message}</p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                document.querySelector('.container-fluid').insertBefore(alert, document.querySelector('.row'));

                // Reload page after 2 seconds
                setTimeout(() => window.location.reload(), 2000);
            } else {
                throw new Error(data.message || '{{ __('app.error') }}');
            }
        })
        .catch(error => {
            // Show error message
            const alert = document.createElement('div');
            alert.className = 'alert alert-danger alert-dismissible fade show alert-modern';
            alert.innerHTML = `
                <div class="d-flex align-items-center">
                    <div class="alert-icon">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <div class="alert-content">
                        <strong>{{ __('app.error') }}!</strong>
                        <p class="mb-0">${error.message}</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            document.querySelector('.container-fluid').insertBefore(alert, document.querySelector('.row'));

            // Re-enable button
            resetBtn.disabled = false;
            resetBtn.innerHTML = '<i class="bi bi-arrow-counterclockwise"></i> {{ __('app.reset_to_defaults') }}';
        });
    }
}

// Auto-save indicator with modern styling
document.querySelector('form').addEventListener('submit', function(e) {
    const btn = e.submitter;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> {{ __('app.saving') }}...';
    btn.classList.add('btn-loading');
});

// Auto-dismiss alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert-modern');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});
</script>
@endpush
