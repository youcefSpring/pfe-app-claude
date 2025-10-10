@extends('layouts.pfe-app')

@section('title', $speciality->name)

@section('content')
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">{{ $speciality->name }}</h1>
                <div class="d-flex align-items-center gap-2 mt-1">
                    <span class="badge bg-info">{{ ucfirst($speciality->level) }}</span>
                    <span class="badge bg-secondary">{{ $speciality->academic_year }}</span>
                    @if($speciality->code)
                        <span class="badge bg-dark">{{ $speciality->code }}</span>
                    @endif
                    @if($speciality->is_active)
                        <span class="badge bg-success">{{ __('app.active') }}</span>
                    @else
                        <span class="badge bg-danger">{{ __('app.inactive') }}</span>
                    @endif
                </div>
            </div>
            <div class="btn-group">
                @if(auth()->user()?->role === 'admin' || auth()->user()?->role === 'department_head')
                    <a href="{{ route('specialities.edit', $speciality) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> {{ __('app.edit') }}
                    </a>
                @endif
                <a href="{{ route('specialities.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> {{ __('app.back') }}
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Speciality Details -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('app.speciality_details') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="fw-bold text-muted">{{ __('app.name') }}</label>
                                    <div>{{ $speciality->name }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="fw-bold text-muted">{{ __('app.code') }}</label>
                                    <div>{{ $speciality->code ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="fw-bold text-muted">{{ __('app.level') }}</label>
                                    <div>
                                        <span class="badge bg-info">{{ ucfirst($speciality->level) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="fw-bold text-muted">{{ __('app.academic_year') }}</label>
                                    <div>{{ $speciality->academic_year }}</div>
                                </div>
                            </div>
                            @if($speciality->semester)
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="fw-bold text-muted">{{ __('app.semester') }}</label>
                                        <div>{{ $speciality->semester }}</div>
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="fw-bold text-muted">{{ __('app.status') }}</label>
                                    <div>
                                        @if($speciality->is_active)
                                            <span class="badge bg-success">{{ __('app.active') }}</span>
                                        @else
                                            <span class="badge bg-danger">{{ __('app.inactive') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($speciality->description)
                            <div class="mb-3">
                                <label class="fw-bold text-muted">{{ __('app.description') }}</label>
                                <div class="mt-1">{{ $speciality->description }}</div>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="fw-bold text-muted">{{ __('app.created_at') }}</label>
                                    <div>{{ $speciality->created_at->format('d/m/Y H:i') }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="fw-bold text-muted">{{ __('app.updated_at') }}</label>
                                    <div>{{ $speciality->updated_at->format('d/m/Y H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Students List -->
                @if($speciality->students()->count() > 0)
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">{{ __('app.students') }} ({{ $studentCount }})</h5>
                            <a href="{{ route('specialities.index') }}?speciality_id={{ $speciality->id }}&role=student"
                               class="btn btn-sm btn-outline-primary">
                                {{ __('app.view_all') }}
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>{{ __('app.name') }}</th>
                                            <th>{{ __('app.email') }}</th>
                                            <th>{{ __('app.registration_number') }}</th>
                                            <th>{{ __('app.section') }}</th>
                                            <th>{{ __('app.group') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($speciality->students()->limit(10)->get() as $student)
                                            <tr>
                                                <td>{{ $student->name }}</td>
                                                <td>{{ $student->email }}</td>
                                                <td>{{ $student->numero_inscription ?? '-' }}</td>
                                                <td>{{ $student->section ?? '-' }}</td>
                                                <td>{{ $student->groupe ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if($studentCount > 10)
                                <div class="text-center mt-3">
                                    <small class="text-muted">{{ __('app.showing_first_records', ['count' => 10, 'total' => $studentCount]) }}</small>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Statistics Sidebar -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">{{ __('app.statistics') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border rounded p-3">
                                    <h4 class="text-primary mb-1">{{ $studentCount }}</h4>
                                    <small class="text-muted">{{ __('app.students') }}</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-3">
                                    <h4 class="text-info mb-1">{{ $teacherCount }}</h4>
                                    <small class="text-muted">{{ __('app.teachers') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if(auth()->user()?->role === 'admin' || auth()->user()?->role === 'department_head')
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">{{ __('app.quick_actions') }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <form action="{{ route('specialities.toggleActive', $speciality) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-outline-{{ $speciality->is_active ? 'warning' : 'success' }} w-100">
                                        <i class="bi bi-{{ $speciality->is_active ? 'pause' : 'play' }}"></i>
                                        {{ $speciality->is_active ? __('app.deactivate') : __('app.activate') }}
                                    </button>
                                </form>

                                <a href="{{ route('specialities.edit', $speciality) }}" class="btn btn-outline-primary">
                                    <i class="bi bi-pencil"></i> {{ __('app.edit') }}
                                </a>

                                @if($speciality->users()->count() === 0)
                                    <form action="{{ route('specialities.destroy', $speciality) }}" method="POST"
                                          onsubmit="return confirm('{{ __('app.confirm_delete_speciality') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger w-100">
                                            <i class="bi bi-trash"></i> {{ __('app.delete') }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection