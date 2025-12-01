@extends('layouts.pfe-app')

@section('title', 'Your Response')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">{{ __('Your Team Response') }}</h1>
        <div class="btn-group">
            <a href="{{ route('external-documents.show', $externalDocument) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> {{ __('Back to Document') }}
            </a>
            <a href="{{ route('external-documents.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-list"></i> {{ __('All Documents') }}
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Response Details -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-file-earmark-check"></i>
                        {{ __('Response to:') }} {{ $externalDocument->name }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>{{ __('Submitted By:') }}</strong><br>{{ $response->uploader->name }}</p>
                            <p><strong>{{ __('Submission Date:') }}</strong><br>
                                {{ $response->created_at->format('Y-m-d H:i') }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>{{ __('File Type:') }}</strong><br>
                                <span class="badge bg-secondary">{{ strtoupper($response->file_type) }}</span>
                            </p>
                            <p><strong>{{ __('File Size:') }}</strong><br>{{ $response->file_size_human }}</p>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        {{ __('File Name:') }} <strong>{{ $response->file_original_name }}</strong>
                    </div>

                    <div class="d-grid">
                        <a href="{{ route('admin.external-documents.responses.download', $response) }}"
                           class="btn btn-lg btn-secondary">
                            <i class="bi bi-download"></i> {{ __('Download Your Response') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Admin Feedback -->
            @if($response->hasFeedback())
                <div class="card border-primary">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-chat-text"></i> {{ __('Admin Feedback') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-light">
                            <p class="mb-0" style="white-space: pre-wrap;">{{ $response->admin_feedback }}</p>
                        </div>

                        <div class="border-top pt-3 mt-3">
                            <small class="text-muted">
                                <i class="bi bi-person"></i> {{ __('Feedback provided by:') }}
                                <strong>{{ $response->feedbackProvider->name }}</strong>
                            </small>
                            <br>
                            <small class="text-muted">
                                <i class="bi bi-calendar"></i> {{ __('Date:') }}
                                {{ $response->feedback_at->format('Y-m-d H:i') }}
                            </small>
                        </div>
                    </div>
                </div>
            @else
                <div class="card border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="bi bi-hourglass-split"></i> {{ __('Feedback Status') }}</h5>
                    </div>
                    <div class="card-body text-center py-5">
                        <i class="bi bi-hourglass-split" style="font-size: 3rem; color: #ffc107;"></i>
                        <p class="mt-3 mb-0">{{ __('Your response is being reviewed by the administrator') }}</p>
                        <small class="text-muted">{{ __('You will be notified when feedback is available') }}</small>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Original Document Info -->
            <div class="card mb-3">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="bi bi-file-earmark-text"></i> {{ __('Original Document') }}</h6>
                </div>
                <div class="card-body">
                    <h6>{{ $externalDocument->name }}</h6>
                    @if($externalDocument->description)
                        <p class="small text-muted">{{ Str::limit($externalDocument->description, 100) }}</p>
                    @endif

                    <p class="small mb-2">
                        <span class="badge bg-secondary">{{ strtoupper($externalDocument->file_type) }}</span>
                        {{ $externalDocument->file_size_human }}
                    </p>

                    <a href="{{ route('external-documents.download', $externalDocument) }}"
                       class="btn btn-sm btn-secondary w-100 mb-2">
                        <i class="bi bi-download"></i> {{ __('Download Original') }}
                    </a>
                    <a href="{{ route('external-documents.show', $externalDocument) }}"
                       class="btn btn-sm btn-info w-100">
                        <i class="bi bi-eye"></i> {{ __('View Document Page') }}
                    </a>
                </div>
            </div>

            <!-- Team Info -->
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0"><i class="bi bi-people-fill"></i> {{ __('Team Info') }}</h6>
                </div>
                <div class="card-body">
                    <h6>{{ $team->name }}</h6>
                    <p class="mb-2 small"><strong>{{ __('Members:') }}</strong></p>
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
        </div>
    </div>
</div>
@endsection
