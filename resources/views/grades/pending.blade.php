@extends('layouts.pfe-app')
@section('title', 'Pending Grade Verification')
@section('content')
<div class="container-fluid py-4">
    <h1 class="h3 mb-4">Pending Grade Verification</h1>
    @if($grades->count() > 0)
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Subject</th>
                                <th>Grade</th>
                                <th>Credits</th>
                                <th>Submitted</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($grades as $grade)
                                <tr>
                                    <td>{{ $grade->student->name ?? 'Unknown' }}</td>
                                    <td>{{ $grade->subject_name }}</td>
                                    <td><span class="badge bg-secondary">{{ $grade->grade }}/20</span></td>
                                    <td>{{ $grade->credits }}</td>
                                    <td>{{ $grade->created_at->diffForHumans() }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <form method="POST" action="{{ route('grades.verify', $grade) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm">Verify</button>
                                            </form>
                                            <form method="POST" action="{{ route('grades.reject', $grade) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                            </form>
                                        </div>
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
            <h4>No Pending Grades</h4>
            <p class="text-muted">All grades have been verified.</p>
        </div>
    @endif
</div>
@endsection