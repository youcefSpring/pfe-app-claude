@extends('layouts.pfe-app')

@section('page-title', 'Specialities Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Specialities Management</h4>
                    <div>
                        <a href="{{ route('admin.specialities.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add Speciality
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($specialities->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Code</th>
                                        <th>Level</th>
                                        <th>Academic Year</th>
                                        <th>Students</th>
                                        <th>Status</th>
                                        <th>Actions</th>
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
                                                    <span class="badge bg-primary">License</span>
                                                @elseif($speciality->level === 'master')
                                                    <span class="badge bg-success">Master</span>
                                                @elseif($speciality->level === 'doctorate')
                                                    <span class="badge bg-warning">Doctorate</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($speciality->level) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div>
                                                    <div>{{ $speciality->academic_year }}</div>
                                                    @if($speciality->semester)
                                                        <small class="text-muted">Semester {{ $speciality->semester }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $speciality->students_count ?? 0 }}</span>
                                            </td>
                                            <td>
                                                @if($speciality->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.specialities.edit', $speciality) }}"
                                                       class="btn btn-outline-primary btn-sm" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @if($speciality->students_count == 0)
                                                        <button type="button" class="btn btn-outline-danger btn-sm"
                                                                onclick="deleteSpeciality({{ $speciality->id }}, '{{ $speciality->name }}')" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @else
                                                        <button type="button" class="btn btn-outline-secondary btn-sm" disabled title="Cannot delete - has students">
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

                        {{ $specialities->links() }}
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Specialities Found</h5>
                            <p class="text-muted">Start by adding specialities to organize students by their field of study.</p>
                            <a href="{{ route('admin.specialities.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add First Speciality
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
                    <small>Total Specialities</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h4>{{ $specialities->where('is_active', true)->count() }}</h4>
                    <small>Active</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h4>{{ $specialities->where('level', 'license')->count() }}</h4>
                    <small>License Programs</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body text-center">
                    <h4>{{ $specialities->where('level', 'master')->count() }}</h4>
                    <small>Master Programs</small>
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
                <h5 class="modal-title">Delete Speciality</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h6>⚠️ This action cannot be undone!</h6>
                    <p class="mb-0">Deleting this speciality will remove all associated data.</p>
                </div>
                <p>Are you sure you want to delete speciality <strong id="deleteSpecialityName"></strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteSpecialityForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Delete Speciality
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