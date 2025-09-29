@extends('layouts.admin')

@section('title', __('System Settings'))
@section('page-title', __('System Settings'))

@section('breadcrumbs')
<span class="text-muted">{{ __('Home') }} / {{ __('Administration') }} / {{ __('Settings') }}</span>
@endsection

@section('content')
<div class="container-fluid p-4">
    <!-- Header -->
    <div class="mb-4">
        <h1 class="h3 mb-0">{{ __('System Settings') }}</h1>
        <p class="text-muted">{{ __('Configure global system settings and preferences') }}</p>
    </div>

    <div class="row g-4">
        <!-- Settings Navigation -->
        <div class="col-lg-3">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">{{ __('Settings Categories') }}</h6>
                </div>
                <div class="list-group list-group-flush">
                    <a href="#general" class="list-group-item list-group-item-action active" data-bs-toggle="tab">
                        <i class="fas fa-cog me-2"></i>{{ __('General') }}
                    </a>
                    <a href="#academic" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                        <i class="fas fa-graduation-cap me-2"></i>{{ __('Academic Calendar') }}
                    </a>
                    <a href="#notifications" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                        <i class="fas fa-bell me-2"></i>{{ __('Notifications') }}
                    </a>
                    <a href="#email" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                        <i class="fas fa-envelope me-2"></i>{{ __('Email Settings') }}
                    </a>
                    <a href="#security" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                        <i class="fas fa-shield-alt me-2"></i>{{ __('Security') }}
                    </a>
                    <a href="#backup" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                        <i class="fas fa-database me-2"></i>{{ __('Backup & Maintenance') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Settings Content -->
        <div class="col-lg-9">
            <form method="POST" action="{{ route('pfe.admin.update-settings') }}">
                @csrf
                @method('PUT')

                <div class="tab-content">
                    <!-- General Settings -->
                    <div class="tab-pane fade show active" id="general">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">{{ __('General Settings') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label for="app_name" class="form-label">{{ __('Application Name') }}</label>
                                        <input type="text" class="form-control @error('app_name') is-invalid @enderror"
                                               id="app_name" name="app_name" value="{{ old('app_name', $settings['app_name'] ?? 'PFE Management System') }}">
                                        @error('app_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="institution_name" class="form-label">{{ __('Institution Name') }}</label>
                                        <input type="text" class="form-control @error('institution_name') is-invalid @enderror"
                                               id="institution_name" name="institution_name" value="{{ old('institution_name', $settings['institution_name'] ?? 'University') }}">
                                        @error('institution_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label for="default_language" class="form-label">{{ __('Default Language') }}</label>
                                        <select class="form-select @error('default_language') is-invalid @enderror" id="default_language" name="default_language">
                                            <option value="en" {{ old('default_language', $settings['default_language'] ?? 'en') == 'en' ? 'selected' : '' }}>{{ __('English') }}</option>
                                            <option value="fr" {{ old('default_language', $settings['default_language'] ?? '') == 'fr' ? 'selected' : '' }}>{{ __('French') }}</option>
                                            <option value="ar" {{ old('default_language', $settings['default_language'] ?? '') == 'ar' ? 'selected' : '' }}>{{ __('Arabic') }}</option>
                                        </select>
                                        @error('default_language')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="timezone" class="form-label">{{ __('Timezone') }}</label>
                                        <select class="form-select @error('timezone') is-invalid @enderror" id="timezone" name="timezone">
                                            <option value="UTC" {{ old('timezone', $settings['timezone'] ?? 'UTC') == 'UTC' ? 'selected' : '' }}>UTC</option>
                                            <option value="Africa/Algiers" {{ old('timezone', $settings['timezone'] ?? '') == 'Africa/Algiers' ? 'selected' : '' }}>{{ __('Algeria Time') }}</option>
                                            <option value="Europe/Paris" {{ old('timezone', $settings['timezone'] ?? '') == 'Europe/Paris' ? 'selected' : '' }}>{{ __('Paris Time') }}</option>
                                        </select>
                                        @error('timezone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="max_team_size" class="form-label">{{ __('Maximum Team Size') }}</label>
                                        <input type="number" class="form-control @error('max_team_size') is-invalid @enderror"
                                               id="max_team_size" name="max_team_size" value="{{ old('max_team_size', $settings['max_team_size'] ?? 4) }}"
                                               min="1" max="10">
                                        @error('max_team_size')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="min_team_size" class="form-label">{{ __('Minimum Team Size') }}</label>
                                        <input type="number" class="form-control @error('min_team_size') is-invalid @enderror"
                                               id="min_team_size" name="min_team_size" value="{{ old('min_team_size', $settings['min_team_size'] ?? 1) }}"
                                               min="1" max="5">
                                        @error('min_team_size')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Academic Calendar -->
                    <div class="tab-pane fade" id="academic">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">{{ __('Academic Calendar Settings') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label for="academic_year_start" class="form-label">{{ __('Academic Year Start') }}</label>
                                        <input type="date" class="form-control @error('academic_year_start') is-invalid @enderror"
                                               id="academic_year_start" name="academic_year_start" value="{{ old('academic_year_start', $settings['academic_year_start'] ?? '2024-09-01') }}">
                                        @error('academic_year_start')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="academic_year_end" class="form-label">{{ __('Academic Year End') }}</label>
                                        <input type="date" class="form-control @error('academic_year_end') is-invalid @enderror"
                                               id="academic_year_end" name="academic_year_end" value="{{ old('academic_year_end', $settings['academic_year_end'] ?? '2025-07-31') }}">
                                        @error('academic_year_end')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label for="project_submission_deadline" class="form-label">{{ __('Project Submission Deadline') }}</label>
                                        <input type="date" class="form-control @error('project_submission_deadline') is-invalid @enderror"
                                               id="project_submission_deadline" name="project_submission_deadline" value="{{ old('project_submission_deadline', $settings['project_submission_deadline'] ?? '2025-05-15') }}">
                                        @error('project_submission_deadline')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="defense_period_start" class="form-label">{{ __('Defense Period Start') }}</label>
                                        <input type="date" class="form-control @error('defense_period_start') is-invalid @enderror"
                                               id="defense_period_start" name="defense_period_start" value="{{ old('defense_period_start', $settings['defense_period_start'] ?? '2025-06-01') }}">
                                        @error('defense_period_start')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="defense_period_end" class="form-label">{{ __('Defense Period End') }}</label>
                                        <input type="date" class="form-control @error('defense_period_end') is-invalid @enderror"
                                               id="defense_period_end" name="defense_period_end" value="{{ old('defense_period_end', $settings['defense_period_end'] ?? '2025-07-15') }}">
                                        @error('defense_period_end')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="defense_duration" class="form-label">{{ __('Defense Duration (minutes)') }}</label>
                                        <input type="number" class="form-control @error('defense_duration') is-invalid @enderror"
                                               id="defense_duration" name="defense_duration" value="{{ old('defense_duration', $settings['defense_duration'] ?? 45) }}"
                                               min="15" max="180" step="15">
                                        @error('defense_duration')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notifications Settings -->
                    <div class="tab-pane fade" id="notifications">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">{{ __('Notification Settings') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-4">
                                    <h6>{{ __('Email Notifications') }}</h6>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="email_project_updates" name="notifications[]" value="email_project_updates"
                                               {{ in_array('email_project_updates', old('notifications', $settings['notifications'] ?? [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="email_project_updates">
                                            {{ __('Project updates and submissions') }}
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="email_defense_reminders" name="notifications[]" value="email_defense_reminders"
                                               {{ in_array('email_defense_reminders', old('notifications', $settings['notifications'] ?? [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="email_defense_reminders">
                                            {{ __('Defense reminders and scheduling') }}
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="email_system_updates" name="notifications[]" value="email_system_updates"
                                               {{ in_array('email_system_updates', old('notifications', $settings['notifications'] ?? [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="email_system_updates">
                                            {{ __('System updates and maintenance') }}
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <h6>{{ __('In-App Notifications') }}</h6>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="app_real_time" name="notifications[]" value="app_real_time"
                                               {{ in_array('app_real_time', old('notifications', $settings['notifications'] ?? [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="app_real_time">
                                            {{ __('Real-time notifications') }}
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="app_daily_digest" name="notifications[]" value="app_daily_digest"
                                               {{ in_array('app_daily_digest', old('notifications', $settings['notifications'] ?? [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="app_daily_digest">
                                            {{ __('Daily digest summary') }}
                                        </label>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="notification_retention_days" class="form-label">{{ __('Notification Retention (Days)') }}</label>
                                        <input type="number" class="form-control @error('notification_retention_days') is-invalid @enderror"
                                               id="notification_retention_days" name="notification_retention_days" value="{{ old('notification_retention_days', $settings['notification_retention_days'] ?? 30) }}"
                                               min="7" max="365">
                                        @error('notification_retention_days')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Email Settings -->
                    <div class="tab-pane fade" id="email">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">{{ __('Email Configuration') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label for="smtp_host" class="form-label">{{ __('SMTP Host') }}</label>
                                        <input type="text" class="form-control @error('smtp_host') is-invalid @enderror"
                                               id="smtp_host" name="smtp_host" value="{{ old('smtp_host', $settings['smtp_host'] ?? '') }}">
                                        @error('smtp_host')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="smtp_port" class="form-label">{{ __('SMTP Port') }}</label>
                                        <input type="number" class="form-control @error('smtp_port') is-invalid @enderror"
                                               id="smtp_port" name="smtp_port" value="{{ old('smtp_port', $settings['smtp_port'] ?? '587') }}">
                                        @error('smtp_port')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label for="smtp_username" class="form-label">{{ __('SMTP Username') }}</label>
                                        <input type="text" class="form-control @error('smtp_username') is-invalid @enderror"
                                               id="smtp_username" name="smtp_username" value="{{ old('smtp_username', $settings['smtp_username'] ?? '') }}">
                                        @error('smtp_username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="smtp_encryption" class="form-label">{{ __('Encryption') }}</label>
                                        <select class="form-select @error('smtp_encryption') is-invalid @enderror" id="smtp_encryption" name="smtp_encryption">
                                            <option value="tls" {{ old('smtp_encryption', $settings['smtp_encryption'] ?? 'tls') == 'tls' ? 'selected' : '' }}>TLS</option>
                                            <option value="ssl" {{ old('smtp_encryption', $settings['smtp_encryption'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                            <option value="none" {{ old('smtp_encryption', $settings['smtp_encryption'] ?? '') == 'none' ? 'selected' : '' }}>{{ __('None') }}</option>
                                        </select>
                                        @error('smtp_encryption')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="mail_from_address" class="form-label">{{ __('From Email Address') }}</label>
                                        <input type="email" class="form-control @error('mail_from_address') is-invalid @enderror"
                                               id="mail_from_address" name="mail_from_address" value="{{ old('mail_from_address', $settings['mail_from_address'] ?? '') }}">
                                        @error('mail_from_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="mail_from_name" class="form-label">{{ __('From Name') }}</label>
                                        <input type="text" class="form-control @error('mail_from_name') is-invalid @enderror"
                                               id="mail_from_name" name="mail_from_name" value="{{ old('mail_from_name', $settings['mail_from_name'] ?? '') }}">
                                        @error('mail_from_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Security Settings -->
                    <div class="tab-pane fade" id="security">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">{{ __('Security Settings') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label for="session_timeout" class="form-label">{{ __('Session Timeout (minutes)') }}</label>
                                        <input type="number" class="form-control @error('session_timeout') is-invalid @enderror"
                                               id="session_timeout" name="session_timeout" value="{{ old('session_timeout', $settings['session_timeout'] ?? 120) }}"
                                               min="15" max="1440">
                                        @error('session_timeout')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="password_min_length" class="form-label">{{ __('Minimum Password Length') }}</label>
                                        <input type="number" class="form-control @error('password_min_length') is-invalid @enderror"
                                               id="password_min_length" name="password_min_length" value="{{ old('password_min_length', $settings['password_min_length'] ?? 8) }}"
                                               min="6" max="32">
                                        @error('password_min_length')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <h6>{{ __('Password Requirements') }}</h6>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="require_uppercase" name="password_requirements[]" value="uppercase"
                                               {{ in_array('uppercase', old('password_requirements', $settings['password_requirements'] ?? [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="require_uppercase">
                                            {{ __('Require uppercase letters') }}
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="require_numbers" name="password_requirements[]" value="numbers"
                                               {{ in_array('numbers', old('password_requirements', $settings['password_requirements'] ?? [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="require_numbers">
                                            {{ __('Require numbers') }}
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="require_symbols" name="password_requirements[]" value="symbols"
                                               {{ in_array('symbols', old('password_requirements', $settings['password_requirements'] ?? [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="require_symbols">
                                            {{ __('Require special symbols') }}
                                        </label>
                                    </div>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="enable_two_factor" name="enable_two_factor" value="1"
                                           {{ old('enable_two_factor', $settings['enable_two_factor'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="enable_two_factor">
                                        {{ __('Enable Two-Factor Authentication') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Backup & Maintenance -->
                    <div class="tab-pane fade" id="backup">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">{{ __('Backup & Maintenance') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label for="backup_frequency" class="form-label">{{ __('Backup Frequency') }}</label>
                                        <select class="form-select @error('backup_frequency') is-invalid @enderror" id="backup_frequency" name="backup_frequency">
                                            <option value="daily" {{ old('backup_frequency', $settings['backup_frequency'] ?? 'daily') == 'daily' ? 'selected' : '' }}>{{ __('Daily') }}</option>
                                            <option value="weekly" {{ old('backup_frequency', $settings['backup_frequency'] ?? '') == 'weekly' ? 'selected' : '' }}>{{ __('Weekly') }}</option>
                                            <option value="monthly" {{ old('backup_frequency', $settings['backup_frequency'] ?? '') == 'monthly' ? 'selected' : '' }}>{{ __('Monthly') }}</option>
                                        </select>
                                        @error('backup_frequency')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="backup_retention" class="form-label">{{ __('Backup Retention (days)') }}</label>
                                        <input type="number" class="form-control @error('backup_retention') is-invalid @enderror"
                                               id="backup_retention" name="backup_retention" value="{{ old('backup_retention', $settings['backup_retention'] ?? 30) }}"
                                               min="7" max="365">
                                        @error('backup_retention')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label for="maintenance_mode" class="form-label">{{ __('Maintenance Mode') }}</label>
                                        <select class="form-select @error('maintenance_mode') is-invalid @enderror" id="maintenance_mode" name="maintenance_mode">
                                            <option value="disabled" {{ old('maintenance_mode', $settings['maintenance_mode'] ?? 'disabled') == 'disabled' ? 'selected' : '' }}>{{ __('Disabled') }}</option>
                                            <option value="enabled" {{ old('maintenance_mode', $settings['maintenance_mode'] ?? '') == 'enabled' ? 'selected' : '' }}>{{ __('Enabled') }}</option>
                                        </select>
                                        @error('maintenance_mode')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="log_retention" class="form-label">{{ __('Log Retention (days)') }}</label>
                                        <input type="number" class="form-control @error('log_retention') is-invalid @enderror"
                                               id="log_retention" name="log_retention" value="{{ old('log_retention', $settings['log_retention'] ?? 30) }}"
                                               min="7" max="365">
                                        @error('log_retention')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="maintenance_message" class="form-label">{{ __('Maintenance Message') }}</label>
                                    <textarea class="form-control @error('maintenance_message') is-invalid @enderror"
                                              id="maintenance_message" name="maintenance_message" rows="3"
                                              placeholder="{{ __('Message to display during maintenance...') }}">{{ old('maintenance_message', $settings['maintenance_message'] ?? '') }}</textarea>
                                    @error('maintenance_message')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-outline-primary" onclick="performBackup()">
                                        <i class="fas fa-download me-2"></i>{{ __('Create Manual Backup') }}
                                    </button>
                                    <button type="button" class="btn btn-outline-warning" onclick="clearCache()">
                                        <i class="fas fa-broom me-2"></i>{{ __('Clear System Cache') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Save Button -->
                <div class="card mt-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                {{ __('Reset to Defaults') }}
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>{{ __('Save Settings') }}
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
function performBackup() {
    if (confirm('{{ __("Create a manual backup of the system?") }}')) {
        // Here you would trigger the backup process
        fetch('/pfe/admin/backup', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('{{ __("Backup created successfully") }}');
            } else {
                alert('{{ __("Error creating backup") }}');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('{{ __("Error creating backup") }}');
        });
    }
}

function clearCache() {
    if (confirm('{{ __("Clear system cache? This may temporarily slow down the application.") }}')) {
        fetch('/pfe/admin/cache/clear', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('{{ __("Cache cleared successfully") }}');
            } else {
                alert('{{ __("Error clearing cache") }}');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('{{ __("Error clearing cache") }}');
        });
    }
}

function resetForm() {
    if (confirm('{{ __("Reset all settings to default values?") }}')) {
        // Reset form to defaults
        document.querySelector('form').reset();
    }
}
</script>
@endpush