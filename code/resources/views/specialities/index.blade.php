@extends('layouts.pfe-app')

@section('title', __('app.specialities'))

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">{{ __('app.specialities') }}</h1>
            @if(auth()->user()?->role === 'admin' || auth()->user()?->role === 'department_head')
                <a href="{{ route('specialities.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus"></i> {{ __('app.create') }} {{ __('app.speciality') }}
                </a>
            @endif
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('specialities.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label for="level" class="form-label">{{ __('app.level') }}</label>
                        <select class="form-select" id="level" name="level">
                            <option value="">{{ __('app.all_levels') }}</option>
                            <option value="licence" {{ request('level') === 'licence' ? 'selected' : '' }}>{{ __('app.licence') }}</option>
                            <option value="master" {{ request('level') === 'master' ? 'selected' : '' }}>{{ __('app.master') }}</option>
                            <option value="ingenieur" {{ request('level') === 'ingenieur' ? 'selected' : '' }}>{{ __('app.ingenieur') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="academic_year" class="form-label">{{ __('app.academic_year') }}</label>
                        <select class="form-select" id="academic_year" name="academic_year">
                            <option value="">{{ __('app.all_years') }}</option>
                            @foreach($specialities->pluck('academic_year')->unique()->sort() as $year)
                                <option value="{{ $year }}" {{ request('academic_year') === $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">{{ __('app.status') }}</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">{{ __('app.all_statuses') }}</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>{{ __('app.active') }}</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>{{ __('app.inactive') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="search" class="form-label">{{ __('app.search') }}</label>
                        <input type="text" class="form-control" id="search" name="search"
                               value="{{ request('search') }}" placeholder="{{ __('app.search') }} {{ __('app.specialities') }}...">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-outline-primary">{{ __('app.filter') }}</button>
                        <a href="{{ route('specialities.index') }}" class="btn btn-outline-secondary">{{ __('app.reset') }}</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Specialities List -->
        <div class="card">
            <div class="card-body">
                @if($specialities->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('app.name') }}</th>
                                    <th>{{ __('app.code') }}</th>
                                    <th>{{ __('app.level') }}</th>
                                    <th>{{ __('app.academic_year') }}</th>
                                    <th>{{ __('app.semester') }}</th>
                                    <th>{{ __('app.students_count') }}</th>
                                    <th>{{ __('app.status') }}</th>
                                    <th>{{ __('app.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($specialities as $speciality)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $speciality->name }}</div>
                                            @if($speciality->description)
                                                <small class="text-muted">{{ Str::limit($speciality->description, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($speciality->code)
                                                <span class="badge bg-secondary">{{ $speciality->code }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ ucfirst($speciality->level) }}</span>
                                        </td>
                                        <td>{{ $speciality->academic_year }}</td>
                                        <td>{{ $speciality->semester ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-primary">{{ $speciality->student_count }}</span>
                                        </td>
                                        <td>
                                            @if($speciality->is_active)
                                                <span class="badge bg-success">{{ __('app.active') }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ __('app.inactive') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('specialities.show', $speciality) }}"
                                                   class="btn btn-sm btn-outline-primary" title="{{ __('app.view') }}">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                @if(auth()->user()?->role === 'admin' || auth()->user()?->role === 'department_head')
                                                    <a href="{{ route('specialities.edit', $speciality) }}"
                                                       class="btn btn-sm btn-outline-warning" title="{{ __('app.edit') }}">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('specialities.toggleActive', $speciality) }}"
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                                class="btn btn-sm btn-outline-{{ $speciality->is_active ? 'warning' : 'success' }}"
                                                                title="{{ $speciality->is_active ? __('app.deactivate') : __('app.activate') }}">
                                                            <i class="bi bi-{{ $speciality->is_active ? 'pause' : 'play' }}"></i>
                                                        </button>
                                                    </form>
                                                    @if($speciality->users()->count() === 0)
                                                        <form action="{{ route('specialities.destroy', $speciality) }}"
                                                              method="POST" class="d-inline"
                                                              onsubmit="return confirm('{{ __('app.confirm_delete') }}')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                    class="btn btn-sm btn-outline-danger"
                                                                    title="{{ __('app.delete') }}">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $specialities->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-mortarboard display-1 text-muted"></i>
                        <h4 class="mt-3">{{ __('app.no_specialities_found') }}</h4>
                        <p class="text-muted">{{ __('app.no_specialities_message') }}</p>
                        @if(auth()->user()?->role === 'admin' || auth()->user()?->role === 'department_head')
                            <a href="{{ route('specialities.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus"></i> {{ __('app.create_first_speciality') }}
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Auto-submit form when filters change
    document.querySelectorAll('#level, #academic_year, #status').forEach(element => {
        element.addEventListener('change', function() {
            this.form.submit();
        });
    });
</script>
@endsection