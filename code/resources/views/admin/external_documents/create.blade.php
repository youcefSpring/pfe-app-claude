@extends('layouts.pfe-app')

@section('title', 'Upload External Document')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">{{ __('Upload External Document') }}</h1>
        <a href="{{ route('admin.external-documents.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> {{ __('Back to List') }}
        </a>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>{{ __('Validation Errors:') }}</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-cloud-upload"></i> {{ __('Document Information') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.external-documents.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Document Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                {{ __('Document Name') }} <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" id="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">
                                {{ __('Description') }}
                            </label>
                            <textarea name="description" id="description" rows="4"
                                      class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">{{ __('Optional description for teams') }}</small>
                        </div>

                        <!-- Academic Year -->
                        <div class="mb-3">
                            <label for="academic_year_id" class="form-label">
                                {{ __('Academic Year') }}
                            </label>
                            <select name="academic_year_id" id="academic_year_id"
                                    class="form-select @error('academic_year_id') is-invalid @enderror">
                                <option value="">{{ __('Current Year') }}</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>
                                        {{ $year->year }}
                                    </option>
                                @endforeach
                            </select>
                            @error('academic_year_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- File Upload -->
                        <div class="mb-3">
                            <label for="file" class="form-label">
                                {{ __('Document File') }} <span class="text-danger">*</span>
                            </label>
                            <input type="file" name="file" id="file"
                                   class="form-control @error('file') is-invalid @enderror"
                                   accept=".pdf,.doc,.docx" required>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                {{ __('Allowed formats: PDF, DOC, DOCX. Max size: 10MB') }}
                            </small>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-cloud-upload"></i> {{ __('Upload Document') }}
                            </button>
                            <a href="{{ route('admin.external-documents.index') }}" class="btn btn-secondary">
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Help Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> {{ __('Help') }}</h5>
                </div>
                <div class="card-body">
                    <h6>{{ __('Document Guidelines') }}</h6>
                    <ul class="small">
                        <li>{{ __('Use clear and descriptive document names') }}</li>
                        <li>{{ __('Add a description to help teams understand the document') }}</li>
                        <li>{{ __('Only PDF, DOC, and DOCX formats are allowed') }}</li>
                        <li>{{ __('Maximum file size is 10MB') }}</li>
                        <li>{{ __('Teams can submit one response per document') }}</li>
                    </ul>

                    <hr>

                    <h6>{{ __('Important Notes') }}</h6>
                    <ul class="small">
                        <li>{{ __('Documents are visible to all teams immediately') }}</li>
                        <li>{{ __('You can deactivate documents at any time') }}</li>
                        <li>{{ __('Teams can only respond during the response period') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
