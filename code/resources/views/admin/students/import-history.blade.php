@extends('layouts.pfe-app')

@section('page-title', __('app.import_history'))

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-clock-history me-2"></i>{{ __('app.import_history') }} {{ __('app.students') }}
                </h5>
                <a href="{{ route('admin.students.upload') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-upload me-1"></i>{{ __('app.new') }} {{ __('app.import') }}
                </a>
            </div>
            <div class="card-body">
                @if($recentStudents->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('app.matricule') }}</th>
                                    <th>{{ __('app.full_name') }}</th>
                                    <th>{{ __('app.email') }}</th>
                                    <th>{{ __('app.section') }}</th>
                                    <th>{{ __('app.group') }}</th>
                                    <th>{{ __('app.creation_date') }}</th>
                                    <th>{{ __('app.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentStudents as $student)
                                    <tr>
                                        <td>
                                            <span class="badge bg-secondary">{{ $student->matricule ?? 'N/A' }}</span>
                                        </td>
                                        <td>{{ $student->name }}</td>
                                        <td>{{ $student->email }}</td>
                                        <td>{{ $student->section ?? 'N/A' }}</td>
                                        <td>{{ $student->groupe ?? 'N/A' }}</td>
                                        <td>
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($student->created_at)->format('d/m/Y H:i') }}
                                            </small>
                                        </td>
                                        <td>
                                            @if($student->status === 'active')
                                                <span class="badge bg-success">{{ __('app.active') }}</span>
                                            @else
                                                <span class="badge bg-warning">{{ ucfirst($student->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-inbox display-1 text-muted"></i>
                        <h5 class="mt-3">{{ __('app.no_recent_imports') }}</h5>
                        <p class="text-muted">{{ __('app.last_50_imported_students') }}</p>
                        <a href="{{ route('admin.students.upload') }}" class="btn btn-primary">
                            <i class="bi bi-upload me-2"></i>{{ __('app.start_import') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection