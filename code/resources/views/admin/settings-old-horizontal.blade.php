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
                            <button class="nav-link" id="system-tab" data-bs-toggle="tab" data-bs-target="#system" type="button" role="tab">
                                <i class="fas fa-cog me-2"></i>{{ __('app.system_settings') }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="team-tab" data-bs-toggle="tab" data-bs-target="#team" type="button" role="tab">
                                <i class="fas fa-users me-2"></i>{{ __('app.team_settings') }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="subject-tab" data-bs-toggle="tab" data-bs-target="#subject" type="button" role="tab">
                                <i class="fas fa-book me-2"></i>{{ __('app.subject_settings') }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="registration-tab" data-bs-toggle="tab" data-bs-target="#registration" type="button" role="tab">
                                <i class="fas fa-user-plus me-2"></i>{{ __('app.registration_settings') }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="defense-tab" data-bs-toggle="tab" data-bs-target="#defense" type="button" role="tab">
                                <i class="fas fa-gavel me-2"></i>{{ __('app.defense_settings') }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="notification-tab" data-bs-toggle="tab" data-bs-target="#notification" type="button" role="tab">
                                <i class="fas fa-bell me-2"></i>{{ __('app.notification_settings') }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="allocation-tab" data-bs-toggle="tab" data-bs-target="#allocation" type="button" role="tab">
                                <i class="fas fa-random me-2"></i>{{ __('app.allocation_settings') }}
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

                            <!-- System Settings Tab -->
                            <div class="tab-pane fade" id="system" role="tabpanel">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">{{ __('app.system_settings') }}</h5>
                                        <small class="text-muted">{{ __('app.configure_system_behavior') }}</small>
                                    </div>
                                    <div class="card-body">
                                        @if(isset($settingsGroups['system']))
                                            @foreach($settingsGroups['system'] as $setting)
                                                @include('admin.partials.setting-field', ['setting' => $setting])
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Team Settings Tab -->
                            <div class="tab-pane fade" id="team" role="tabpanel">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">{{ __('app.team_settings') }}</h5>
                                        <small class="text-muted">{{ __('app.configure_team_formation') }}</small>
                                    </div>
                                    <div class="card-body">
                                        @if(isset($settingsGroups['team']))
                                            @foreach($settingsGroups['team'] as $setting)
                                                @include('admin.partials.setting-field', ['setting' => $setting])
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Subject Settings Tab -->
                            <div class="tab-pane fade" id="subject" role="tabpanel">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">{{ __('app.subject_settings') }}</h5>
                                        <small class="text-muted">{{ __('app.configure_subject_management') }}</small>
                                    </div>
                                    <div class="card-body">
                                        @if(isset($settingsGroups['subject']))
                                            @foreach($settingsGroups['subject'] as $setting)
                                                @include('admin.partials.setting-field', ['setting' => $setting])
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Registration Settings Tab -->
                            <div class="tab-pane fade" id="registration" role="tabpanel">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">{{ __('app.registration_settings') }}</h5>
                                        <small class="text-muted">{{ __('app.configure_student_registration') }}</small>
                                    </div>
                                    <div class="card-body">
                                        @if(isset($settingsGroups['registration']))
                                            @foreach($settingsGroups['registration'] as $setting)
                                                @include('admin.partials.setting-field', ['setting' => $setting])
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Defense Settings Tab -->
                            <div class="tab-pane fade" id="defense" role="tabpanel">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">{{ __('app.defense_settings') }}</h5>
                                        <small class="text-muted">{{ __('app.configure_defense_scheduling') }}</small>
                                    </div>
                                    <div class="card-body">
                                        @if(isset($settingsGroups['defense']))
                                            @foreach($settingsGroups['defense'] as $setting)
                                                @include('admin.partials.setting-field', ['setting' => $setting])
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Notification Settings Tab -->
                            <div class="tab-pane fade" id="notification" role="tabpanel">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">{{ __('app.notification_settings') }}</h5>
                                        <small class="text-muted">{{ __('app.configure_notifications') }}</small>
                                    </div>
                                    <div class="card-body">
                                        @if(isset($settingsGroups['notification']))
                                            @foreach($settingsGroups['notification'] as $setting)
                                                @include('admin.partials.setting-field', ['setting' => $setting])
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Allocation Settings Tab -->
                            <div class="tab-pane fade" id="allocation" role="tabpanel">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">{{ __('app.allocation_settings') }}</h5>
                                        <small class="text-muted">{{ __('app.configure_subject_allocation') }}</small>
                                    </div>
                                    <div class="card-body">
                                        @if(isset($settingsGroups['allocation']))
                                            @foreach($settingsGroups['allocation'] as $setting)
                                                @include('admin.partials.setting-field', ['setting' => $setting])
                                            @endforeach
                                        @endif
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