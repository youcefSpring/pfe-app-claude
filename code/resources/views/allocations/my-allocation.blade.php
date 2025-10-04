@extends('layouts.pfe-app')
@section('title', 'My Subject Allocation')
@section('content')
<div class="container-fluid py-4">
    <h1 class="h3 mb-4">My Subject Allocation</h1>
    @if($allocation ?? false)
        <div class="card">
            <div class="card-body">
                <div class="alert alert-success">
                    <h5><i class="bi bi-check-circle"></i> Subject Allocated</h5>
                    <p class="mb-0">You have been allocated the following subject:</p>
                </div>
                <h4>{{ $allocation->subject->title }}</h4>
                <p class="text-muted">{{ $allocation->subject->description }}</p>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <strong>Supervisor:</strong> {{ $allocation->subject->teacher->name ?? 'N/A' }}<br>
                        <strong>Type:</strong> {{ ucfirst($allocation->subject->type) }}<br>
                        <strong>Allocated:</strong> {{ $allocation->created_at->format('M d, Y') }}
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('subjects.show', $allocation->subject) }}" class="btn btn-primary">View Subject Details</a>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Back to Dashboard</a>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-hourglass text-muted" style="font-size: 4rem;"></i>
            <h4 class="mt-3">No Allocation Yet</h4>
            <p class="text-muted">Your subject allocation is still pending. Please check back later.</p>
            <a href="{{ route('preferences.index') }}" class="btn btn-outline-primary">View My Preferences</a>
        </div>
    @endif
</div>
@endsection