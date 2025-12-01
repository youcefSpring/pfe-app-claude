@extends('layouts.pfe-app')

@section('title', 'Add Feedback')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">{{ __('Add Feedback to Team Response') }}</h1>
        <a href="{{ route('admin.external-documents.show', $response->external_document_id) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> {{ __('Back') }}
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
                    <h5 class="mb-0"><i class="bi bi-chat-text"></i> {{ __('Feedback Form') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.external-documents.responses.store-feedback', $response) }}" method="POST">
                        @csrf

                        <!-- Team Info -->
                        <div class="alert alert-info">
                            <strong>{{ __('Team:') }}</strong> {{ $response->team->name }}<br>
                            <strong>{{ __('Document:') }}</strong> {{ $response->externalDocument->name }}<br>
                            <strong>{{ __('Submitted:') }}</strong> {{ $response->created_at->format('Y-m-d H:i') }}
                        </div>

                        <!-- Feedback Textarea -->
                        <div class="mb-3">
                            <label for="admin_feedback" class="form-label">
                                {{ __('Your Feedback') }} <span class="text-danger">*</span>
                            </label>
                            <textarea name="admin_feedback" id="admin_feedback" rows="8"
                                      class="form-control @error('admin_feedback') is-invalid @enderror"
                                      required>{{ old('admin_feedback') }}</textarea>
                            @error('admin_feedback')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                {{ __('Minimum 10 characters. Be specific and constructive.') }}
                            </small>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> {{ __('Submit Feedback') }}
                            </button>
                            <a href="{{ route('admin.external-documents.show', $response->external_document_id) }}"
                               class="btn btn-secondary">
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Response Info & Download -->
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> {{ __('Response Details') }}</h5>
                </div>
                <div class="card-body">
                    <p><strong>{{ __('Team Name:') }}</strong><br>{{ $response->team->name }}</p>
                    <p><strong>{{ __('Team Members:') }}</strong></p>
                    <ul class="small">
                        @foreach($response->team->members as $member)
                            <li>{{ $member->student->name }}
                                @if($member->role === 'leader')
                                    <span class="badge bg-primary">{{ __('Leader') }}</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                    <hr>
                    <p><strong>{{ __('File:') }}</strong><br>
                        <span class="badge bg-secondary">{{ strtoupper($response->file_type) }}</span>
                        {{ $response->file_size_human }}
                    </p>
                    <a href="{{ route('admin.external-documents.responses.download', $response) }}"
                       class="btn btn-secondary w-100">
                        <i class="bi bi-download"></i> {{ __('Download Response') }}
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-lightbulb"></i> {{ __('Feedback Tips') }}</h5>
                </div>
                <div class="card-body">
                    <ul class="small">
                        <li>{{ __('Be specific about what needs improvement') }}</li>
                        <li>{{ __('Acknowledge good work') }}</li>
                        <li>{{ __('Provide actionable suggestions') }}</li>
                        <li>{{ __('Be professional and constructive') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
