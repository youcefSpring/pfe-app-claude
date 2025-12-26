@extends('layouts.pfe-app')
@section('title', 'Subject Preferences')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">My Subject Preferences</h1>
        @if($canModifyPreferences)
            <a href="{{ route('preferences.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add Preference
            </a>
        @endif
    </div>

    @if($currentDeadline)
        <div class="alert {{ $currentDeadline->canStudentsChoose() ? 'alert-info' : 'alert-warning' }}">
            <i class="bi bi-{{ $currentDeadline->canStudentsChoose() ? 'info-circle' : 'exclamation-triangle' }}"></i>
            @if($currentDeadline->canStudentsChoose())
                Preference submission deadline: {{ $currentDeadline->preferences_deadline->format('M d, Y \\a\\t g:i A') }}
                <br><small>{{ $currentDeadline->getRemainingTimeForPreferences() }}</small>
            @else
                <strong>Preference submission period has ended.</strong>
                <br><small>Deadline was: {{ $currentDeadline->preferences_deadline->format('M d, Y \\a\\t g:i A') }}</small>
            @endif
        </div>
    @endif

    @if($preferences->count() > 0)
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Priority</th>
                                <th>Subject</th>
                                <th>Teacher</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($preferences as $preference)
                                <tr>
                                    <td><span class="badge bg-primary">#{{ $preference->priority }}</span></td>
                                    <td>{{ $preference->subject->title }}</td>
                                    <td>{{ $preference->subject->teacher->name ?? 'N/A' }}</td>
                                    <td>{{ ucfirst($preference->subject->type) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $preference->subject->status === 'approved' ? 'success' : 'warning' }}">
                                            {{ ucfirst($preference->subject->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($canModifyPreferences)
                                            <form method="POST" action="{{ route('preferences.destroy') }}" class="d-inline">
                                                @csrf @method('DELETE')
                                                <input type="hidden" name="subject_id" value="{{ $preference->subject_id }}">
                                                <button type="submit" class="btn btn-outline-danger btn-sm"
                                                        onclick="return confirm('Remove this preference?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <small class="text-muted">
                                                <i class="bi bi-lock"></i> {{ __('app.read_only') }}
                                            </small>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-heart text-muted" style="font-size: 4rem;"></i>
            <h4 class="mt-3">No Preferences Set</h4>
            <p class="text-muted">You haven't selected any subject preferences yet.</p>
            @if($canModifyPreferences)
                <a href="{{ route('preferences.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Add First Preference
                </a>
            @endif
        </div>
    @endif
</div>
@endsection