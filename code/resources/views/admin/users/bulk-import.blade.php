@extends('layouts.pfe-app')

@section('page-title', __('app.bulk_import_users'))

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ __('app.bulk_import_users') }}</h4>
                    <small class="text-muted">{{ __('app.import_multiple_users') }}</small>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> {{ __('app.import_instructions') }}</h6>
                        <ul class="mb-0">
                            <li>{{ __('app.excel_file_format') }}</li>
                            <li><strong>{{ __('app.column_c') }}:</strong> {{ __('app.matricule_student_id') }}</li>
                            <li><strong>{{ __('app.column_d') }}:</strong> {{ __('app.nom_last_name') }}</li>
                            <li><strong>{{ __('app.column_e') }}:</strong> {{ __('app.prenom_first_name') }}</li>
                            <li><strong>{{ __('app.column_f') }}:</strong> {{ __('app.section_optional') }}</li>
                            <li><strong>{{ __('app.column_g') }}:</strong> {{ __('app.groupe_optional') }}</li>
                            <li><strong>{{ __('app.name_format') }}:</strong> {{ __('app.name_format_description') }}</li>
                            <li>{{ __('app.email_auto_generated') }}: <code>matricule@gmail.com</code></li>
                            <li>{{ __('app.password_set_to_matricule') }}</li>
                            <li>{{ __('app.default_speciality_master') }}</li>
                            <li>{{ __('app.file_size_formats') }}</li>
                        </ul>
                    </div>

                    <form action="{{ route('admin.users.bulk-import.process') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="users_file" class="form-label">{{ __('app.excel_file') }}</label>
                            <input type="file" class="form-control" id="users_file" name="users_file"
                                   accept=".xlsx,.xls" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="default_role" class="form-label">{{ __('app.default_role') }}</label>
                                    <select class="form-select" id="default_role" name="default_role">
                                        <option value="student">{{ __('app.student') }}</option>
                                        <option value="teacher">{{ __('app.teacher') }}</option>
                                        <option value="department_head">{{ __('app.department_head') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="default_department" class="form-label">{{ __('app.default_department') }}</label>
                                    <input type="text" class="form-control" id="default_department"
                                           name="default_department" placeholder="e.g., Computer Science">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.users') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> {{ __('app.back_to_users') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload"></i> {{ __('app.import_users') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection