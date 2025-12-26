@extends('layouts.pfe-app')

@section('title', 'External Documents')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">{{ __('External Documents') }}</h1>
        <a href="{{ route('dashboard.student') }}" class="btn btn-outline-secondary">
            <i class="bi bi-speedometer2"></i> {{ __('Dashboard') }}
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

    <!-- Team Info Card -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5><i class="bi bi-people-fill text-primary"></i> {{ $team->name }}</h5>
                    <p class="mb-0 text-muted">
                        <strong>{{ __('Members:') }}</strong>
                        @foreach($team->members as $member)
                            {{ $member->student->name }}@if(!$loop->last), @endif
                        @endforeach
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    @if($deadline && $deadline->canSubmitResponses())
                        <span class="badge bg-success p-2">
                            <i class="bi bi-check-circle"></i> {{ __('Response Period Open') }}
                        </span>
                    @else
                        <span class="badge bg-secondary p-2">
                            <i class="bi bi-x-circle"></i> {{ __('Response Period Closed') }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Documents List -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-file-earmark-text"></i> {{ __('Available Documents') }}</h5>
        </div>
        <div class="card-body">
            @if($documents->count() > 0)
                <div class="row">
                    @foreach($documents as $document)
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 @if($document->has_responded) border-success @endif">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <h6 class="mb-0">{{ $document->name }}</h6>
                                        @if($document->has_responded)
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle"></i> {{ __('Responded') }}
                                            </span>
                                        @else
                                            <span class="badge bg-warning">
                                                <i class="bi bi-hourglass-split"></i> {{ __('Pending') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-body">
                                    @if($document->description)
                                        <p class="text-muted small">{{ Str::limit($document->description, 100) }}</p>
                                    @endif

                                    <div class="mb-2">
                                        <span class="badge bg-secondary">{{ strtoupper($document->file_type) }}</span>
                                        <small class="text-muted">{{ $document->file_size_human }}</small>
                                    </div>

                                    <div class="mb-2">
                                        <small class="text-muted">
                                            <i class="bi bi-calendar"></i> {{ $document->created_at->format('Y-m-d') }}
                                        </small>
                                    </div>

                                    @if($document->has_responded && $document->team_response->hasFeedback())
                                        <div class="alert alert-info small mb-0 mt-2">
                                            <i class="bi bi-chat-text"></i> {{ __('Admin feedback available') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="card-footer bg-transparent">
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('external-documents.show', $document) }}"
                                           class="btn btn-sm btn-primary flex-grow-1">
                                            <i class="bi bi-eye"></i> {{ __('View Details') }}
                                        </a>
                                        <a href="{{ route('external-documents.download', $document) }}"
                                           class="btn btn-sm btn-secondary">
                                            <i class="bi bi-download"></i>
                                        </a>
                                        @if($document->has_responded)
                                            <a href="{{ route('external-documents.view-response', $document) }}"
                                               class="btn btn-sm btn-success">
                                                <i class="bi bi-file-check"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-folder-x" style="font-size: 3rem; color: #ccc;"></i>
                    <p class="text-muted mt-3">{{ __('No documents available yet') }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Deadline Info -->
    @if($deadline)
        <div class="card mt-4">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="bi bi-info-circle"></i> {{ __('Important Deadlines') }}</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @if($deadline->response_start)
                        <div class="col-md-6">
                            <p class="mb-1"><strong>{{ __('Response Period Starts:') }}</strong></p>
                            <p class="text-muted">{{ $deadline->response_start->format('Y-m-d H:i') }}</p>
                        </div>
                    @endif
                    @if($deadline->response_deadline)
                        <div class="col-md-6">
                            <p class="mb-1"><strong>{{ __('Response Deadline:') }}</strong></p>
                            <p class="text-muted">{{ $deadline->response_deadline->format('Y-m-d H:i') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
