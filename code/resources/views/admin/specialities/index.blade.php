@extends('layouts.pfe-app')

@section('title', __('app.specialities_management'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-mortarboard me-2"></i>{{ __('app.specialities_management') }}
                    </h4>
                    <a href="{{ route('admin.specialities.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i>{{ __('app.new_speciality') }}
                    </a>
                </div>

                <!-- Search and Filter Section -->
                <div class="card-body border-bottom">
                    <form method="GET" action="{{ route('admin.specialities.index') }}" id="filterForm">
                        <div class="row g-3">
                            <!-- Search -->
                            <div class="col-md-4">
                                <label for="search" class="form-label">{{ __('app.search') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                    <input type="text"
                                           class="form-control"
                                           id="search"
                                           name="search"
                                           value="{{ request('search') }}"
                                           placeholder="{{ __('app.search_specialities_placeholder') }}">
                                </div>
                            </div>

                            <!-- Level Filter -->
                            <div class="col-md-2">
                                <label for="level" class="form-label">{{ __('app.level') }}</label>
                                <select class="form-select" id="level" name="level">
                                    <option value="">{{ __('app.all_levels') }}</option>
                                    @foreach(\App\Models\Speciality::LEVELS as $key => $label)
                                        <option value="{{ $key }}" {{ request('level') === $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Academic Year Filter -->
                            <div class="col-md-2">
                                <label for="academic_year" class="form-label">{{ __('app.academic_year') }}</label>
                                <select class="form-select" id="academic_year" name="academic_year">
                                    <option value="">{{ __('app.all_years') }}</option>
                                    @foreach($academicYears ?? [] as $year)
                                        <option value="{{ $year }}" {{ request('academic_year') === $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Status Filter -->
                            <div class="col-md-2">
                                <label for="status" class="form-label">{{ __('app.status') }}</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">{{ __('app.all_statuses') }}</option>
                                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('app.active') }}</option>
                                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>{{ __('app.inactive') }}</option>
                                </select>
                            </div>

                            <!-- Clear Button -->
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <a href="{{ route('admin.specialities.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-clockwise me-1"></i>{{ __('app.clear') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
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
                                        <th>{{ __('app.students') }}</th>
                                        <th>{{ __('app.status') }}</th>
                                        <th>{{ __('app.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($specialities as $speciality)
                                        <tr>
                                            <td>
                                                <div>
                                                    <h6 class="mb-1">{{ $speciality->name }}</h6>
                                                    @if($speciality->description)
                                                        <small class="text-muted">{{ Str::limit($speciality->description, 50) }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if($speciality->code)
                                                    <span class="badge bg-secondary">{{ $speciality->code }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($speciality->level === 'license')
                                                    <span class="badge bg-primary">{{ __('app.license') }}</span>
                                                @elseif($speciality->level === 'master')
                                                    <span class="badge bg-success">{{ __('app.master') }}</span>
                                                @elseif($speciality->level === 'doctorate')
                                                    <span class="badge bg-warning">{{ __('app.doctorate') }}</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($speciality->level) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div>
                                                    <div>{{ $speciality->academic_year }}</div>
                                                    @if($speciality->semester)
                                                        <small class="text-muted">{{ __('app.semester') }} {{ $speciality->semester }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $speciality->students_count ?? 0 }}</span>
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
                                                    <a href="{{ route('admin.specialities.edit', $speciality) }}"
                                                       class="btn btn-outline-primary btn-sm" title="{{ __('app.edit') }}">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @if($speciality->students_count == 0)
                                                        <button type="button" class="btn btn-outline-danger btn-sm"
                                                                onclick="deleteSpeciality({{ $speciality->id }}, '{{ $speciality->name }}')" title="{{ __('app.delete') }}">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @else
                                                        <button type="button" class="btn btn-outline-secondary btn-sm" disabled title="{{ __('app.cannot_delete_has_students') }}">
                                                            <i class="fas fa-lock"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <x-admin-pagination :paginator="$specialities" />
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">{{ __('app.no_specialities_found') }}</h5>
                            <p class="text-muted">{{ __('app.add_specialities_instruction') }}</p>
                            <a href="{{ route('admin.specialities.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> {{ __('app.add_first_speciality') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    @if($specialities->count() > 0)
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h4>{{ $specialities->total() }}</h4>
                    <small>{{ __('app.total_specialities') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h4>{{ $specialities->where('is_active', true)->count() }}</h4>
                    <small>{{ __('app.active') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h4>{{ $specialities->where('level', 'license')->count() }}</h4>
                    <small>{{ __('app.licence_programs') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body text-center">
                    <h4>{{ $specialities->where('level', 'master')->count() }}</h4>
                    <small>{{ __('app.master_programs') }}</small>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Delete Speciality Modal -->
<div class="modal fade" id="deleteSpecialityModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('app.delete_speciality') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h6>⚠️ {{ __('app.action_cannot_be_undone') }}</h6>
                    <p class="mb-0">{{ __('app.delete_speciality_warning') }}</p>
                </div>
                <p>{{ __('app.confirm_delete_speciality') }} <strong id="deleteSpecialityName"></strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('app.cancel') }}</button>
                <form id="deleteSpecialityForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> {{ __('app.delete_speciality') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function deleteSpeciality(specialityId, specialityName) {
    document.getElementById('deleteSpecialityName').textContent = specialityName;
    document.getElementById('deleteSpecialityForm').action = `/admin/specialities/${specialityId}`;

    const modal = new bootstrap.Modal(document.getElementById('deleteSpecialityModal'));
    modal.show();
}
</script>
@endpush