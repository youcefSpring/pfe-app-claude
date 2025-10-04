@extends('layouts.pfe-app')
@section('title', 'Add Subject Preference')
@section('content')
<div class="container-fluid py-4">
    <h1 class="h3 mb-4">Add Subject Preference</h1>
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('preferences.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="subject_id" class="form-label">Select Subject <span class="text-danger">*</span></label>
                            <select class="form-select @error('subject_id') is-invalid @enderror" id="subject_id" name="subject_id" required>
                                <option value="">Choose a subject...</option>
                                @foreach($availableSubjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->title }} - {{ $subject->teacher->name ?? 'No teacher' }} ({{ ucfirst($subject->type) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('subject_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                            <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                                <option value="">Select priority...</option>
                                @for($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}" {{ old('priority') == $i ? 'selected' : '' }}>
                                        Priority {{ $i }} {{ $i == 1 ? '(Highest)' : ($i == 5 ? '(Lowest)' : '') }}
                                    </option>
                                @endfor
                            </select>
                            @error('priority')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('preferences.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Add Preference</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h5>Guidelines</h5></div>
                <div class="card-body">
                    <ul class="small">
                        <li>Select subjects in order of preference</li>
                        <li>Priority 1 is your most desired subject</li>
                        <li>You can select up to 5 preferences</li>
                        <li>Preferences are used for subject allocation</li>
                    </ul>
                    @if($currentDeadline)
                        <div class="alert alert-warning mt-3">
                            <small>Deadline: {{ $currentDeadline->format('M d, Y') }}</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection