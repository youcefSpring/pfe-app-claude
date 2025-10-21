@extends('layouts.pfe-app')

@section('page-title', __('app.birth_certificates_management'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-certificate me-2"></i>
                        {{ __('app.birth_certificates_management') }}
                    </h4>
                    <div class="d-flex gap-2">
                        <span class="badge bg-warning">
                            {{ $students->where('birth_certificate_status', 'pending')->count() }} {{ __('app.pending') }}
                        </span>
                        <span class="badge bg-success">
                            {{ $students->where('birth_certificate_status', 'approved')->count() }} {{ __('app.approved') }}
                        </span>
                        <span class="badge bg-danger">
                            {{ $students->where('birth_certificate_status', 'rejected')->count() }} {{ __('app.rejected') }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <form method="GET" action="{{ route('admin.birth-certificates') }}">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <input type="text"
                                               class="form-control"
                                               name="search"
                                               value="{{ request('search') }}"
                                               placeholder="{{ __('app.search_students') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-select" name="status">
                                            <option value="">{{ __('app.all_statuses') }}</option>
                                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>
                                                {{ __('app.pending') }}
                                            </option>
                                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>
                                                {{ __('app.approved') }}
                                            </option>
                                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>
                                                {{ __('app.rejected') }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i>
                                        </button>
                                        <a href="{{ route('admin.birth-certificates') }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Students List -->
                    @if($students->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>{{ __('app.student') }}</th>
                                        <th>{{ __('app.student_level') }}</th>
                                        <th>{{ __('app.birth_info') }}</th>
                                        <th>{{ __('app.certificate') }}</th>
                                        <th>{{ __('app.status') }}</th>
                                        <th>{{ __('app.submitted_at') }}</th>
                                        <th>{{ __('app.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($students as $student)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm bg-primary text-white rounded-circle me-2">
                                                        {{ substr($student->name, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <strong>{{ $student->name }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $student->email }}</small>
                                                        @if($student->matricule)
                                                            <br><small class="text-muted">{{ $student->matricule }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($student->student_level)
                                                    <span class="badge bg-info">
                                                        {{ __('app.'.$student->student_level) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($student->date_naissance && $student->lieu_naissance)
                                                    <small>
                                                        <strong>{{ __('app.birth_date') }}:</strong> {{ \Carbon\Carbon::parse($student->date_naissance)->format('d/m/Y') }}<br>
                                                        <strong>{{ __('app.birth_place') }}:</strong> {{ $student->lieu_naissance }}
                                                    </small>
                                                @else
                                                    <span class="text-muted">{{ __('app.not_provided') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($student->birth_certificate_path)
                                                    <a href="{{ Storage::url($student->birth_certificate_path) }}"
                                                       target="_blank"
                                                       class="btn btn-outline-primary btn-sm">
                                                        <i class="fas fa-eye me-1"></i>
                                                        {{ __('app.view_document') }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">{{ __('app.no_document') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $statusClass = match($student->birth_certificate_status) {
                                                        'approved' => 'bg-success',
                                                        'rejected' => 'bg-danger',
                                                        default => 'bg-warning'
                                                    };
                                                @endphp
                                                <span class="badge {{ $statusClass }}">
                                                    {{ __('app.'.$student->birth_certificate_status) }}
                                                </span>
                                                @if($student->birth_certificate_approved_at)
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ $student->birth_certificate_approved_at->format('d/m/Y H:i') }}
                                                        @if($student->birthCertificateApprover)
                                                            by {{ $student->birthCertificateApprover->name }}
                                                        @endif
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $student->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('admin.users.details', $student) }}"
                                                       class="btn btn-outline-info"
                                                       title="{{ __('app.view_details') }}">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($student->birth_certificate_status === 'pending')
                                                        <button type="button"
                                                                class="btn btn-outline-success"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#approveModal{{ $student->id }}"
                                                                title="{{ __('app.approve') }}">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button type="button"
                                                                class="btn btn-outline-danger"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#rejectModal{{ $student->id }}"
                                                                title="{{ __('app.reject') }}">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    @endif
                                                </div>

                                                <!-- Approve Modal -->
                                                <div class="modal fade" id="approveModal{{ $student->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">{{ __('app.approve_birth_certificate') }}</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <form action="{{ route('admin.birth-certificates.approve', $student) }}" method="POST">
                                                                @csrf
                                                                <div class="modal-body">
                                                                    <p>{{ __('app.confirm_approve_certificate', ['name' => $student->name]) }}</p>
                                                                    <div class="mb-3">
                                                                        <label for="notes{{ $student->id }}" class="form-label">
                                                                            {{ __('app.notes') }} ({{ __('app.optional') }})
                                                                        </label>
                                                                        <textarea class="form-control"
                                                                                  id="notes{{ $student->id }}"
                                                                                  name="notes"
                                                                                  rows="3"
                                                                                  placeholder="{{ __('app.approval_notes_placeholder') }}"></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                        {{ __('app.cancel') }}
                                                                    </button>
                                                                    <button type="submit" class="btn btn-success">
                                                                        <i class="fas fa-check me-2"></i>
                                                                        {{ __('app.approve') }}
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Reject Modal -->
                                                <div class="modal fade" id="rejectModal{{ $student->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">{{ __('app.reject_birth_certificate') }}</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <form action="{{ route('admin.birth-certificates.reject', $student) }}" method="POST">
                                                                @csrf
                                                                <div class="modal-body">
                                                                    <p>{{ __('app.confirm_reject_certificate', ['name' => $student->name]) }}</p>
                                                                    <div class="mb-3">
                                                                        <label for="reject_notes{{ $student->id }}" class="form-label">
                                                                            {{ __('app.rejection_reason') }} <span class="text-danger">*</span>
                                                                        </label>
                                                                        <textarea class="form-control"
                                                                                  id="reject_notes{{ $student->id }}"
                                                                                  name="notes"
                                                                                  rows="3"
                                                                                  placeholder="{{ __('app.rejection_reason_placeholder') }}"
                                                                                  required></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                        {{ __('app.cancel') }}
                                                                    </button>
                                                                    <button type="submit" class="btn btn-danger">
                                                                        <i class="fas fa-times me-2"></i>
                                                                        {{ __('app.reject') }}
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @if($student->birth_certificate_notes)
                                            <tr>
                                                <td colspan="7" class="bg-light">
                                                    <small>
                                                        <strong>{{ __('app.admin_notes') }}:</strong>
                                                        {{ $student->birth_certificate_notes }}
                                                    </small>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($students->hasPages())
                            <div class="d-flex justify-content-center">
                                {{ $students->appends(request()->query())->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-certificate fa-3x text-muted mb-3"></i>
                            <h5>{{ __('app.no_birth_certificates_found') }}</h5>
                            <p class="text-muted">{{ __('app.no_students_uploaded_certificates') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection