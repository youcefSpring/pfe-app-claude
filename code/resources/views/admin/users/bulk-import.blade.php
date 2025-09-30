@extends('layouts.pfe-app')

@section('page-title', 'Bulk Import Users')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Bulk Import Users</h4>
                    <small class="text-muted">Import multiple users from Excel file</small>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> Import Instructions</h6>
                        <ul class="mb-0">
                            <li>Excel file should contain columns: Name, Email, Role, Department</li>
                            <li>Supported roles: student, teacher, department_head</li>
                            <li>Maximum file size: 10MB</li>
                            <li>Supported formats: .xlsx, .xls</li>
                        </ul>
                    </div>

                    <form action="#" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="users_file" class="form-label">Excel File</label>
                            <input type="file" class="form-control" id="users_file" name="users_file"
                                   accept=".xlsx,.xls" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="default_role" class="form-label">Default Role</label>
                                    <select class="form-select" id="default_role" name="default_role">
                                        <option value="student">Student</option>
                                        <option value="teacher">Teacher</option>
                                        <option value="department_head">Department Head</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="default_department" class="form-label">Default Department</label>
                                    <input type="text" class="form-control" id="default_department"
                                           name="default_department" placeholder="e.g., Computer Science">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.users') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Users
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload"></i> Import Users
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection