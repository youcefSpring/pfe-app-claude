@extends('layouts.pfe-app')
@section('title', 'Grade Details')
@section('content')
<div class="container-fluid py-4">
    <h1 class="h3 mb-4">Grade Details</h1>
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5>{{ $grade->subject_name }}</h5>
                    <p class="text-muted">{{ $grade->subject_code }}</p>
                    <div class="row mt-3">
                        <div class="col-md-3">
                            <strong>Grade:</strong><br>
                            <span class="badge bg-{{ $grade->grade >= 10 ? 'success' : 'danger' }} fs-4">{{ $grade->grade }}/20</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Credits:</strong><br>{{ $grade->credits }}
                        </div>
                        <div class="col-md-3">
                            <strong>Semester:</strong><br>{{ $grade->semester }}
                        </div>
                        <div class="col-md-3">
                            <strong>Status:</strong><br>
                            <span class="badge bg-{{ $grade->status === 'verified' ? 'success' : 'warning' }}">{{ ucfirst($grade->status) }}</span>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('grades.index') }}" class="btn btn-outline-secondary">Back to Grades</a>
                        @if($grade->status === 'pending')
                            <a href="{{ route('grades.edit', $grade) }}" class="btn btn-warning">Edit</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection