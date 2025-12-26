@extends('layouts.pfe-app')
@section('title', __('app.my_subject_allocation'))
@section('content')
<div class="container-fluid py-4">
    <h1 class="h3 mb-4">{{ __('app.my_subject_allocation') }}</h1>
    @if($allocation ?? false)
        <div class="card">
            <div class="card-body">
                <div class="alert alert-success">
                    <h5><i class="bi bi-check-circle"></i> {{ __('app.subject_allocated') }}</h5>
                    <p class="mb-0">{{ __('app.you_have_been_allocated') }}</p>
                </div>
                <h4>{{ $allocation->subject->title }}</h4>
                <p class="text-muted">{{ $allocation->subject->description }}</p>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <strong>{{ __('app.supervisor') }}:</strong> {{ $allocation->subject->teacher->name ?? 'N/A' }}<br>
                        <strong>{{ __('app.type') }}:</strong> {{ ucfirst($allocation->subject->type) }}<br>
                        <strong>{{ __('app.allocated') }}:</strong> {{ $allocation->created_at->format('M d, Y') }}
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('subjects.show', $allocation->subject) }}" class="btn btn-primary">{{ __('app.view_subject_details') }}</a>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">{{ __('app.back_to_dashboard') }}</a>
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