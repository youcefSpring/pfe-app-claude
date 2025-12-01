@extends('layouts.pfe-app')

@section('title', 'Document Details')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">{{ __('Document Details') }}</h1>
        <a href="{{ route('admin.external-documents.index') }}" class="btn btn-outline-secondary">
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

    <div class="row">
        <!-- Document Information -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-text"></i> {{ __('Document Information') }}</h5>
                </div>
                <div class="card-body">
                    <h4>{{ $document->name }}</h4>

                    @if($document->description)
                        <p class="text-muted">{{ $document->description }}</p>
                    @endif

                    <hr>

                    <div class="mb-2">
                        <strong>{{ __('Status:') }}</strong>
                        @if($document->is_active)
                            <span class="badge bg-success">{{ __('Active') }}</span>
                        @else
                            <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                        @endif
                    </div>

                    <div class="mb-2">
                        <strong>{{ __('File Type:') }}</strong>
                        <span class="badge bg-info">{{ strtoupper($document->file_type) }}</span>
                    </div>

                    <div class="mb-2">
                        <strong>{{ __('File Size:') }}</strong> {{ $document->file_size_human }}
                    </div>

                    <div class="mb-2">
                        <strong>{{ __('Uploaded By:') }}</strong> {{ $document->uploader->name }}
                    </div>

                    <div class="mb-2">
                        <strong>{{ __('Upload Date:') }}</strong> {{ $document->created_at->format('Y-m-d H:i') }}
                    </div>

                    @if($document->academicYear)
                        <div class="mb-2">
                            <strong>{{ __('Academic Year:') }}</strong> {{ $document->academicYear->year }}
                        </div>
                    @endif

                    <hr>

                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.external-documents.download', $document) }}"
                           class="btn btn-secondary">
                            <i class="bi bi-download"></i> {{ __('Download Document') }}
                        </a>
                        <a href="{{ route('admin.external-documents.edit', $document) }}"
                           class="btn btn-warning">
                            <i class="bi bi-pencil"></i> {{ __('Edit') }}
                        </a>
                        <form action="{{ route('admin.external-documents.toggle-active', $document) }}"
                              method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-{{ $document->is_active ? 'x-circle' : 'check-circle' }}"></i>
                                {{ $document->is_active ? __('Deactivate') : __('Activate') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Team Responses -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-file-earmark-arrow-up"></i>
                        {{ __('Team Responses') }}
                        <span class="badge bg-white text-info">{{ $document->responses->count() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    @if($document->responses->count() > 0)
                        @foreach($document->responses as $response)
                            <div class="card mb-3">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $response->team->name }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                {{ __('Submitted by:') }} {{ $response->uploader->name }}
                                                - {{ $response->created_at->format('Y-m-d H:i') }}
                                            </small>
                                        </div>
                                        <div>
                                            @if($response->hasFeedback())
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle"></i> {{ __('Feedback Given') }}
                                                </span>
                                            @else
                                                <span class="badge bg-warning">
                                                    <i class="bi bi-clock"></i> {{ __('Pending Feedback') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-2">
                                                <strong>{{ __('Team Members:') }}</strong>
                                            </p>
                                            <ul class="small">
                                                @foreach($response->team->members as $member)
                                                    <li>{{ $member->student->name }}
                                                        @if($member->role === 'leader')
                                                            <span class="badge bg-primary">{{ __('Leader') }}</span>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-2"><strong>{{ __('File Info:') }}</strong></p>
                                            <p class="small mb-0">
                                                <span class="badge bg-secondary">{{ strtoupper($response->file_type) }}</span>
                                                {{ $response->file_size_human }}
                                            </p>
                                        </div>
                                    </div>

                                    @if($response->hasFeedback())
                                        <hr>
                                        <div class="alert alert-light mb-0">
                                            <strong>{{ __('Admin Feedback:') }}</strong>
                                            <p class="mb-2 mt-2">{{ $response->admin_feedback }}</p>
                                            <small class="text-muted">
                                                {{ __('By:') }} {{ $response->feedbackProvider->name }}
                                                - {{ $response->feedback_at->format('Y-m-d H:i') }}
                                            </small>
                                        </div>
                                    @endif

                                    <div class="d-flex gap-2 mt-3">
                                        <a href="{{ route('admin.external-documents.responses.download', $response) }}"
                                           class="btn btn-sm btn-secondary">
                                            <i class="bi bi-download"></i> {{ __('Download') }}
                                        </a>
                                        @if(!$response->hasFeedback())
                                            <a href="{{ route('admin.external-documents.responses.feedback', $response) }}"
                                               class="btn btn-sm btn-primary">
                                                <i class="bi bi-chat-text"></i> {{ __('Add Feedback') }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="text-muted mt-3">{{ __('No team responses yet') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
