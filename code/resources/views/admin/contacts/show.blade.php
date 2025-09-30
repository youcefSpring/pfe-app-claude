@extends('layouts.admin')

@section('title', 'Contact Message - ' . $contact->subject)

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">
                    <i class="fas fa-envelope me-2"></i>Contact Message
                </h4>
                <a href="{{ route('admin.contacts.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Messages
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">{{ $contact->subject }}</h5>
                    <small class="text-muted">
                        Received on {{ $contact->created_at->format('F j, Y \a\t g:i A') }}
                        @if($contact->isRead())
                            • Read on {{ $contact->read_at->format('F j, Y \a\t g:i A') }}
                        @endif
                    </small>
                </div>
                <div class="card-body">
                    <div class="message-content">
                        {!! nl2br(e($contact->message)) !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0">Sender Information</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Name:</strong><br>
                        {{ $contact->name }}
                    </div>
                    <div class="mb-3">
                        <strong>Email:</strong><br>
                        <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a>
                    </div>
                    <div class="mb-3">
                        <strong>Status:</strong><br>
                        @if($contact->isRead())
                            <span class="badge bg-success">Read</span>
                        @else
                            <span class="badge bg-danger">Unread</span>
                        @endif
                    </div>
                    <div class="mb-3">
                        <strong>Received:</strong><br>
                        {{ $contact->created_at->format('M j, Y g:i A') }}
                    </div>

                    <div class="d-grid gap-2">
                        <a href="mailto:{{ $contact->email }}?subject=Re: {{ $contact->subject }}"
                           class="btn btn-primary">
                            <i class="fas fa-reply me-2"></i>Reply via Email
                        </a>
                        <form action="{{ route('admin.contacts.destroy', $contact) }}"
                              method="POST"
                              onsubmit="return confirm('Are you sure you want to delete this message?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash me-2"></i>Delete Message
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .message-content {
        background-color: #f8f9fa;
        padding: 1.5rem;
        border-radius: 0.375rem;
        border-left: 4px solid var(--secondary-color);
        line-height: 1.6;
    }
</style>
@endsection