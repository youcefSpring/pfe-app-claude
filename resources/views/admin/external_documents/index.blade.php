@extends('layouts.pfe-app')

@section('title', 'External Documents Management')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">{{ __('External Documents Management') }}</h1>
        <div class="btn-group">
            <a href="{{ route('admin.external-documents.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> {{ __('Upload Document') }}
            </a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-speedometer2"></i> {{ __('Dashboard') }}
            </a>
        </div>
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

    <!-- Documents Table -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-file-earmark-text"></i> {{ __('All Documents') }}</h5>
        </div>
        <div class="card-body">
            @if($documents->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Description') }}</th>
                                <th>{{ __('File') }}</th>
                                <th>{{ __('Uploaded By') }}</th>
                                <th>{{ __('Responses') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($documents as $document)
                                <tr>
                                    <td>
                                        <strong>{{ $document->name }}</strong>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ Str::limit($document->description, 50) }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ strtoupper($document->file_type) }}
                                        </span>
                                        <small class="text-muted">{{ $document->file_size_human }}</small>
                                    </td>
                                    <td>{{ $document->uploader->name }}</td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $document->responses->count() }} {{ __('responses') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($document->is_active)
                                            <span class="badge bg-success">{{ __('Active') }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $document->created_at->format('Y-m-d') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.external-documents.show', $document) }}"
                                               class="btn btn-info" title="{{ __('View') }}">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.external-documents.download', $document) }}"
                                               class="btn btn-secondary" title="{{ __('Download') }}">
                                                <i class="bi bi-download"></i>
                                            </a>
                                            <a href="{{ route('admin.external-documents.edit', $document) }}"
                                               class="btn btn-warning" title="{{ __('Edit') }}">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('admin.external-documents.toggle-active', $document) }}"
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-primary"
                                                        title="{{ $document->is_active ? __('Deactivate') : __('Activate') }}">
                                                    <i class="bi bi-{{ $document->is_active ? 'x-circle' : 'check-circle' }}"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.external-documents.destroy', $document) }}"
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" title="{{ __('Delete') }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $documents->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-folder-x" style="font-size: 3rem; color: #ccc;"></i>
                    <p class="text-muted mt-3">{{ __('No documents uploaded yet') }}</p>
                    <a href="{{ route('admin.external-documents.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> {{ __('Upload First Document') }}
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
