@extends('layouts.pfe-app')

@section('title', 'Document Details')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">{{ __('Document Details') }}</h1>
        <a href="{{ route('external-documents.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> {{ __('Back to List') }}
        </a>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

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
        <!-- Document Information -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-text"></i> {{ $externalDocument->name }}</h5>
                </div>
                <div class="card-body">
                    @if($externalDocument->description)
                        <h6>{{ __('Description') }}</h6>
                        <p class="text-muted">{{ $externalDocument->description }}</p>
                        <hr>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>{{ __('File Type:') }}</strong>
                                <span class="badge bg-secondary">{{ strtoupper($externalDocument->file_type) }}</span>
                            </p>
                            <p><strong>{{ __('File Size:') }}</strong> {{ $externalDocument->file_size_human }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>{{ __('Uploaded:') }}</strong> {{ $externalDocument->created_at->format('Y-m-d') }}</p>
                            @if($externalDocument->academicYear)
                                <p><strong>{{ __('Academic Year:') }}</strong> {{ $externalDocument->academicYear->year }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="d-grid">
                        <a href="{{ route('external-documents.download', $externalDocument) }}"
                           class="btn btn-lg btn-secondary">
                            <i class="bi bi-download"></i> {{ __('Download Document') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Response Status -->
            @if($response)
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-check-circle"></i> {{ __('Your Team Has Responded') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <p><strong>{{ __('Submitted:') }}</strong> {{ $response->created_at->format('Y-m-d H:i') }}</p>
                        <p><strong>{{ __('Submitted By:') }}</strong> {{ $response->uploader->name }}</p>
                        <p><strong>{{ __('File:') }}</strong>
                            <span class="badge bg-secondary">{{ strtoupper($response->file_type) }}</span>
                            {{ $response->file_size_human }}
                        </p>

                        @if($response->hasFeedback())
                            <div class="alert alert-info">
                                <h6><i class="bi bi-chat-text"></i> {{ __('Admin Feedback') }}</h6>
                                <p class="mb-0">{{ $response->admin_feedback }}</p>
                                <hr>
                                <small class="text-muted">
                                    {{ __('By:') }} {{ $response->feedbackProvider->name }} -
                                    {{ $response->feedback_at->format('Y-m-d H:i') }}
                                </small>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="bi bi-hourglass-split"></i> {{ __('Waiting for admin feedback') }}
                            </div>
                        @endif

                        <a href="{{ route('external-documents.view-response', $externalDocument) }}"
                           class="btn btn-success">
                            <i class="bi bi-eye"></i> {{ __('View Full Response') }}
                        </a>
                    </div>
                </div>
            @else
                <!-- Response Form -->
                <div class="card border-primary">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-upload"></i> {{ __('Submit Your Response') }}</h5>
                    </div>
                    <div class="card-body">
                        @if($deadline && $deadline->canSubmitResponses())
                            <form action="{{ route('external-documents.respond', $externalDocument) }}"
                                  method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i>
                                    {{ __('Your team can submit only ONE response to this document') }}
                                </div>

                                <div class="mb-3">
                                    <label for="file" class="form-label">
                                        {{ __('Response Document') }} <span class="text-danger">*</span>
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

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="bi bi-upload"></i> {{ __('Submit Response') }}
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="alert alert-warning mb-0">
                                <i class="bi bi-exclamation-triangle"></i>
                                {{ __('Response submission period is currently closed') }}
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Team Info -->
            <div class="card mb-3">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="bi bi-people-fill"></i> {{ __('Your Team') }}</h6>
                </div>
                <div class="card-body">
                    <h6>{{ $team->name }}</h6>
                    <p class="mb-2"><strong>{{ __('Members:') }}</strong></p>
                    <ul class="small">
                        @foreach($team->members as $member)
                            <li>{{ $member->student->name }}
                                @if($member->role === 'leader')
                                    <span class="badge bg-primary">{{ __('Leader') }}</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- Deadline Info -->
            @if($deadline)
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0"><i class="bi bi-clock"></i> {{ __('Deadlines') }}</h6>
                    </div>
                    <div class="card-body">
                        @if($deadline->response_start)
                            <p class="small mb-2">
                                <strong>{{ __('Start:') }}</strong><br>
                                {{ $deadline->response_start->format('Y-m-d H:i') }}
                            </p>
                        @endif
                        @if($deadline->response_deadline)
                            <p class="small mb-0">
                                <strong>{{ __('Deadline:') }}</strong><br>
                                {{ $deadline->response_deadline->format('Y-m-d H:i') }}
                            </p>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
