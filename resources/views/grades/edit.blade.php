@extends('layouts.pfe-app')
@section('title', 'Edit Grade')
@section('content')
<div class="container-fluid py-4">
    <h1 class="h3 mb-4">Edit Grade</h1>
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('grades.update', $grade) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="mb-3">
                            <label for="subject_name" class="form-label">Subject Name</label>
                            <input type="text" class="form-control" id="subject_name" name="subject_name" value="{{ $grade->subject_name }}" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="grade" class="form-label">Grade (0-20)</label>
                                    <input type="number" class="form-control" id="grade" name="grade" value="{{ $grade->grade }}" min="0" max="20" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="credits" class="form-label">Credits</label>
                                    <input type="number" class="form-control" id="credits" name="credits" value="{{ $grade->credits }}" min="1" max="10" required>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('grades.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Grade</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection