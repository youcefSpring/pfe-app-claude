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

    <div class="row">
        <!-- Vertical Navigation Tabs -->
        <div class="col-md-3">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="bi bi-list-ul"></i>
                        {{ __('app.settings_categories') }}
                    </h3>
                </div>
                <div class="box-body p-0">
                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <button class="nav-link active" id="v-pills-university-tab" data-bs-toggle="pill"
                                data-bs-target="#v-pills-university" type="button" role="tab">
                            <i class="bi bi-building me-2"></i>
                            {{ __('app.university_information') }}
                        </button>
                        <button class="nav-link" id="v-pills-system-tab" data-bs-toggle="pill"
                                data-bs-target="#v-pills-system" type="button" role="tab">
                            <i class="bi bi-gear me-2"></i>
                            {{ __('app.system_configuration') }}
                        </button>
                        <button class="nav-link" id="v-pills-team-tab" data-bs-toggle="pill"
                                data-bs-target="#v-pills-team" type="button" role="tab">
                            <i class="bi bi-people me-2"></i>
                            {{ __('app.team_settings') }}
                        </button>
                        <button class="nav-link" id="v-pills-subject-tab" data-bs-toggle="pill"
                                data-bs-target="#v-pills-subject" type="button" role="tab">
                            <i class="bi bi-journal-text me-2"></i>
                            {{ __('app.subject_settings') }}
                        </button>
                        <button class="nav-link" id="v-pills-registration-tab" data-bs-toggle="pill"
                                data-bs-target="#v-pills-registration" type="button" role="tab">
                            <i class="bi bi-person-plus me-2"></i>
                            {{ __('app.registration_settings') }}
                        </button>
                        <button class="nav-link" id="v-pills-defense-tab" data-bs-toggle="pill"
                                data-bs-target="#v-pills-defense" type="button" role="tab">
                            <i class="bi bi-shield-check me-2"></i>
                            {{ __('app.defense_settings') }}
                        </button>
                        <button class="nav-link" id="v-pills-notification-tab" data-bs-toggle="pill"
                                data-bs-target="#v-pills-notification" type="button" role="tab">
                            <i class="bi bi-bell me-2"></i>
                            {{ __('app.notification_settings') }}
                        </button>
                        <button class="nav-link" id="v-pills-allocation-tab" data-bs-toggle="pill"
                                data-bs-target="#v-pills-allocation" type="button" role="tab">
                            <i class="bi bi-diagram-3 me-2"></i>
                            {{ __('app.allocation_settings') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="bi bi-lightning"></i>
                        {{ __('app.quick_actions') }}
                    </h3>
                </div>
                <div class="box-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-default">
                            <i class="bi bi-arrow-left"></i>
                            {{ __('app.back_to_dashboard') }}
                        </a>
                        <button type="button" class="btn btn-sm btn-warning" onclick="resetToDefaults()">
                            <i class="bi bi-arrow-counterclockwise"></i>
                            {{ __('app.reset_to_defaults') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="col-md-9">
            <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="tab-content" id="v-pills-tabContent">
                    <!-- University Information Tab -->
                    <div class="tab-pane fade show active" id="v-pills-university" role="tabpanel">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">
                                    <i class="bi bi-building"></i>
                                    {{ __('app.university_information') }}
                                </h3>
                                <button type="button" class="btn btn-xs btn-info float-end" data-bs-toggle="modal" data-bs-target="#universityHelpModal">
                                    <i class="bi bi-question-circle"></i> {{ __('app.help') }}
                                </button>
                            </div>
                            <div class="box-body">
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
                    <div class="tab-pane fade" id="v-pills-system" role="tabpanel">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">
                                    <i class="bi bi-gear"></i>
                                    {{ __('app.system_configuration') }}
                                </h3>
                            </div>
                            <div class="box-body">
                                @if(isset($settingsGroups['system']))
                                    @foreach($settingsGroups['system'] as $setting)
                                        @include('admin.partials.setting-field', ['setting' => $setting])
                                    @endforeach
                                @else
                                    <p class="text-muted">{{ __('app.no_settings_available') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Team Settings Tab -->
                    <div class="tab-pane fade" id="v-pills-team" role="tabpanel">
                        <div class="box box-success">
                            <div class="box-header with-border">
                                <h3 class="box-title">
                                    <i class="bi bi-people"></i>
                                    {{ __('app.team_settings') }}
                                </h3>
                            </div>
                            <div class="box-body">
                                <p class="text-muted mb-3">{{ __('app.configure_team_formation_rules') }}</p>
                                @if(isset($settingsGroups['team']))
                                    <div class="row">
                                        @foreach($settingsGroups['team'] as $index => $setting)
                                            <div class="col-md-6">
                                                @include('admin.partials.setting-field', ['setting' => $setting])
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted">{{ __('app.no_settings_available') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Subject Settings Tab -->
                    <div class="tab-pane fade" id="v-pills-subject" role="tabpanel">
                        <div class="box box-warning">
                            <div class="box-header with-border">
                                <h3 class="box-title">
                                    <i class="bi bi-journal-text"></i>
                                    {{ __('app.subject_settings') }}
                                </h3>
                            </div>
                            <div class="box-body">
                                <p class="text-muted mb-3">{{ __('app.configure_subject_management_rules') }}</p>
                                @if(isset($settingsGroups['subject']))
                                    <div class="row">
                                        @foreach($settingsGroups['subject'] as $index => $setting)
                                            <div class="col-md-6">
                                                @include('admin.partials.setting-field', ['setting' => $setting])
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted">{{ __('app.no_settings_available') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Registration Settings Tab -->
                    <div class="tab-pane fade" id="v-pills-registration" role="tabpanel">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">
                                    <i class="bi bi-person-plus"></i>
                                    {{ __('app.registration_settings') }}
                                </h3>
                            </div>
                            <div class="box-body">
                                <p class="text-muted mb-3">{{ __('app.configure_student_registration_process') }}</p>
                                @if(isset($settingsGroups['registration']))
                                    @foreach($settingsGroups['registration'] as $setting)
                                        @include('admin.partials.setting-field', ['setting' => $setting])
                                    @endforeach
                                @else
                                    <p class="text-muted">{{ __('app.no_settings_available') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Defense Settings Tab -->
                    <div class="tab-pane fade" id="v-pills-defense" role="tabpanel">
                        <div class="box box-danger">
                            <div class="box-header with-border">
                                <h3 class="box-title">
                                    <i class="bi bi-shield-check"></i>
                                    {{ __('app.defense_settings') }}
                                </h3>
                            </div>
                            <div class="box-body">
                                <p class="text-muted mb-3">{{ __('app.configure_defense_scheduling_rules') }}</p>
                                @if(isset($settingsGroups['defense']))
                                    @foreach($settingsGroups['defense'] as $setting)
                                        @include('admin.partials.setting-field', ['setting' => $setting])
                                    @endforeach
                                @else
                                    <p class="text-muted">{{ __('app.no_settings_available') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Notification Settings Tab -->
                    <div class="tab-pane fade" id="v-pills-notification" role="tabpanel">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">
                                    <i class="bi bi-bell"></i>
                                    {{ __('app.notification_settings') }}
                                </h3>
                            </div>
                            <div class="box-body">
                                <p class="text-muted mb-3">{{ __('app.configure_notification_preferences') }}</p>
                                @if(isset($settingsGroups['notification']))
                                    @foreach($settingsGroups['notification'] as $setting)
                                        @include('admin.partials.setting-field', ['setting' => $setting])
                                    @endforeach
                                @else
                                    <p class="text-muted">{{ __('app.no_settings_available') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Allocation Settings Tab -->
                    <div class="tab-pane fade" id="v-pills-allocation" role="tabpanel">
                        <div class="box box-success">
                            <div class="box-header with-border">
                                <h3 class="box-title">
                                    <i class="bi bi-diagram-3"></i>
                                    {{ __('app.allocation_settings') }}
                                </h3>
                            </div>
                            <div class="box-body">
                                <p class="text-muted mb-3">{{ __('app.configure_subject_allocation_process') }}</p>
                                @if(isset($settingsGroups['allocation']))
                                    @foreach($settingsGroups['allocation'] as $setting)
                                        @include('admin.partials.setting-field', ['setting' => $setting])
                                    @endforeach
                                @else
                                    <p class="text-muted">{{ __('app.no_settings_available') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Save Button (Sticky) -->
                <div class="box-footer">
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

/* Modern Vertical Tab Styling */
.nav-pills .nav-link {
    text-align: left;
    border-radius: 0;
    border-left: 4px solid transparent;
    padding: 0.85rem 1.25rem;
    color: #495057;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    align-items: center;
    font-weight: 500;
    font-size: 0.9rem;
    margin-bottom: 2px;
}

.nav-pills .nav-link:hover {
    background: linear-gradient(90deg, #f8f9fa 0%, #e9ecef 100%);
    border-left-color: #667eea;
    transform: translateX(4px);
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.15);
}

.nav-pills .nav-link.active {
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-left-color: #5a67d8;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.nav-pills .nav-link i {
    width: 24px;
    font-size: 1.1rem;
}

/* Sticky Save Button */
.box-footer {
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
