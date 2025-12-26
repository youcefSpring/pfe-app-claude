@extends('layouts.pfe-app')

@section('title', 'My Grades')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">My Academic Grades</h1>
        <a href="{{ route('grades.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add Grade
        </a>
    </div>

    @if($grades->count() > 0)
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Grade</th>
                                <th>Credits</th>
                                <th>Semester</th>
                                <th>Status</th>
                                <th>Date Added</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($grades as $grade)
                                <tr>
                                    <td>
                                        <strong>{{ $grade->subject_name }}</strong>
                                        @if($grade->subject_code)
                                            <br><small class="text-muted">{{ $grade->subject_code }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $grade->grade >= 10 ? 'success' : 'danger' }} fs-6">
                                            {{ $grade->grade }}/20
                                        </span>
                                    </td>
                                    <td>{{ $grade->credits }}</td>
                                    <td>{{ $grade->semester }}</td>
                                    <td>
                                        <span class="badge bg-{{ $grade->status === 'verified' ? 'success' : ($grade->status === 'rejected' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($grade->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $grade->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('grades.show', $grade) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if($grade->status === 'pending')
                                                <a href="{{ route('grades.edit', $grade) }}" class="btn btn-outline-warning btn-sm">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form method="POST" action="{{ route('grades.destroy', $grade) }}" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm"
                                                            onclick="return confirm('Are you sure?')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- GPA Calculation -->
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card border-primary">
                    <div class="card-body text-center">
                        @php
                            $totalPoints = $grades->where('status', 'verified')->sum(function($grade) {
                                return $grade->grade * $grade->credits;
                            });
                            $totalCredits = $grades->where('status', 'verified')->sum('credits');
                            $gpa = $totalCredits > 0 ? $totalPoints / $totalCredits : 0;
                        @endphp
                        <h3 class="text-primary">{{ number_format($gpa, 2) }}</h3>
                        <p class="text-muted mb-0">Overall GPA</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-success">
                    <div class="card-body text-center">
                        <h3 class="text-success">{{ $grades->where('status', 'verified')->where('grade', '>=', 10)->count() }}</h3>
                        <p class="text-muted mb-0">Passed Subjects</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-info">
                    <div class="card-body text-center">
                        <h3 class="text-info">{{ $grades->where('status', 'verified')->sum('credits') }}</h3>
                        <p class="text-muted mb-0">Total Credits</p>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-book text-muted" style="font-size: 4rem;"></i>
            <h4 class="mt-3">No Grades Yet</h4>
            <p class="text-muted">You haven't added any grades yet. Start by adding your academic grades.</p>
            <a href="{{ route('grades.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add First Grade
            </a>
        </div>
    @endif
</div>
@endsection