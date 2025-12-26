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
                                        <option value="student" selected>{{ __('app.student') }}</option>
                                        <option value="teacher">{{ __('app.teacher') }}</option>
                                        <option value="department_head">{{ __('app.department_head') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="speciality_id" class="form-label">{{ __('app.default_speciality') }} <span class="text-danger">*</span></label>
                                    <select class="form-select" id="speciality_id" name="speciality_id" required>
                                        <option value="">{{ __('app.select_speciality') }}</option>
                                        @foreach(\App\Models\Speciality::active()->orderBy('name')->get() as $speciality)
                                            <option value="{{ $speciality->id }}">{{ $speciality->name }} ({{ $speciality->level }})</option>
                                        @endforeach
                                    </select>
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