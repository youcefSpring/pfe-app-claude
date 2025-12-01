@extends('layouts.pfe-app')

@section('title', 'Edit External Document')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">{{ __('Edit External Document') }}</h1>
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
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-pencil"></i> {{ __('Edit Document Information') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.external-documents.update', $externalDocument) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Document Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                {{ __('Document Name') }} <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" id="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $externalDocument->name) }}" required>
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
                                      class="form-control @error('description') is-invalid @enderror">{{ old('description', $externalDocument->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                                    <option value="{{ $year->id }}"
                                        {{ old('academic_year_id', $externalDocument->academic_year_id) == $year->id ? 'selected' : '' }}>
                                        {{ $year->year }}
                                    </option>
                                @endforeach
                            </select>
                            @error('academic_year_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- File Upload (Optional) -->
                        <div class="mb-3">
                            <label for="file" class="form-label">
                                {{ __('Replace Document File') }} <small class="text-muted">({{ __('Optional') }})</small>
                            </label>
                            <input type="file" name="file" id="file"
                                   class="form-control @error('file') is-invalid @enderror"
                                   accept=".pdf,.doc,.docx">
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                {{ __('Leave empty to keep current file') }}. {{ __('Current:') }}
                                <strong>{{ $externalDocument->file_original_name }}</strong>
                                ({{ $externalDocument->file_size_human }})
                            </small>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-check-circle"></i> {{ __('Update Document') }}
                            </button>
                            <a href="{{ route('admin.external-documents.index') }}" class="btn btn-secondary">
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Current Document Info -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-text"></i> {{ __('Current Document') }}</h5>
                </div>
                <div class="card-body">
                    <p><strong>{{ __('Name:') }}</strong><br>{{ $externalDocument->name }}</p>
                    <p><strong>{{ __('File:') }}</strong><br>
                        <span class="badge bg-secondary">{{ strtoupper($externalDocument->file_type) }}</span>
                        {{ $externalDocument->file_size_human }}
                    </p>
                    <p><strong>{{ __('Status:') }}</strong><br>
                        @if($externalDocument->is_active)
                            <span class="badge bg-success">{{ __('Active') }}</span>
                        @else
                            <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                        @endif
                    </p>
                    <p><strong>{{ __('Responses:') }}</strong><br>
                        <span class="badge bg-info">{{ $externalDocument->responses->count() }}</span>
                    </p>
                    <hr>
                    <a href="{{ route('admin.external-documents.download', $externalDocument) }}"
                       class="btn btn-secondary w-100 mb-2">
                        <i class="bi bi-download"></i> {{ __('Download Current File') }}
                    </a>
                    <a href="{{ route('admin.external-documents.show', $externalDocument) }}"
                       class="btn btn-info w-100">
                        <i class="bi bi-eye"></i> {{ __('View Details') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
