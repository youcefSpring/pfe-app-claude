@extends('layouts.pfe-app')

@section('page-title', __('app.user_details'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="btn-group">
                        @if($user->role === 'student')
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMarkModal">
                                <i class="bi bi-plus-circle me-2"></i>{{ __('app.add_marks') }}
                            </button>
                        @endif
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-primary">
                            <i class="bi bi-pencil me-2"></i>{{ __('app.edit') }}
                        </a>
                        <a href="{{ route('admin.users') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i>{{ __('app.back_to_users') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- User Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="mb-3">{{ __('app.user_information') }}</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>{{ __('app.name') }}:</strong></td>
                                    <td>{{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('app.email') }}:</strong></td>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('app.role') }}:</strong></td>
                                    <td>
                                        @if($user->role === 'student')
                                            <span class="badge bg-primary">{{ __('app.student') }}</span>
                                        @elseif($user->role === 'teacher')
                                            <span class="badge bg-success">{{ __('app.teacher') }}</span>
                                        @elseif($user->role === 'department_head')
                                            <span class="badge bg-warning">{{ __('app.department_head') }}</span>
                                        @elseif($user->role === 'admin')
                                            <span class="badge bg-danger">{{ __('app.admin') }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @if($user->matricule)
                                <tr>
                                    <td><strong>{{ __('app.matricule') }}:</strong></td>
                                    <td>{{ $user->matricule }}</td>
                                </tr>
                                @endif
                                @if($user->department)
                                <tr>
                                    <td><strong>{{ __('app.department') }}:</strong></td>
                                    <td>{{ $user->department }}</td>
                                </tr>
                                @endif
                                @php
                                    $speciality = $user->speciality;
                                    if (!$speciality && $user->speciality_id) {
                                        $speciality = \App\Models\Speciality::find($user->speciality_id);
                                    }
                                @endphp
                                @if($speciality && is_object($speciality) && $speciality->name)
                                <tr>
                                    <td><strong>{{ __('app.speciality') }}:</strong></td>
                                    <td>
                                        <span class="badge bg-info">{{ $speciality->name }}</span>
                                        @if($speciality->level)
                                            <span class="badge bg-secondary ms-1">{{ $speciality->level }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @elseif($user->speciality && is_string($user->speciality) && !empty($user->speciality))
                                <tr>
                                    <td><strong>{{ __('app.speciality') }}:</strong></td>
                                    <td><span class="badge bg-secondary">{{ $user->speciality }}</span></td>
                                </tr>
                                @elseif($user->speciality_id)
                                <tr>
                                    <td><strong>{{ __('app.speciality') }}:</strong></td>
                                    <td><small class="text-muted">{{ __('app.speciality_id') }}: {{ $user->speciality_id }}</small></td>
                                </tr>
                                @endif
                                <tr>
                                    <td><strong>{{ __('app.created_at') }}:</strong></td>
                                    <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            @if($user->first_name || $user->last_name || $user->date_naissance || $user->lieu_naissance)
                            <h5 class="mb-3">{{ __('app.additional_information') }}</h5>
                            <table class="table table-borderless">
                                @if($user->first_name)
                                <tr>
                                    <td><strong>{{ __('app.first_name') }}:</strong></td>
                                    <td>{{ $user->first_name }}</td>
                                </tr>
                                @endif
                                @if($user->last_name)
                                <tr>
                                    <td><strong>{{ __('app.last_name') }}:</strong></td>
                                    <td>{{ $user->last_name }}</td>
                                </tr>
                                @endif
                                @if($user->date_naissance)
                                <tr>
                                    <td><strong>{{ __('app.date_of_birth') }}:</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($user->date_naissance)->format('d/m/Y') }}</td>
                                </tr>
                                @endif
                                @if($user->lieu_naissance)
                                <tr>
                                    <td><strong>{{ __('app.place_of_birth') }}:</strong></td>
                                    <td>{{ $user->lieu_naissance }}</td>
                                </tr>
                                @endif
                                @if($user->grade)
                                <tr>
                                    <td><strong>{{ __('app.grade') }}:</strong></td>
                                    <td>{{ $user->grade }}</td>
                                </tr>
                                @endif
                                @if($user->position)
                                <tr>
                                    <td><strong>{{ __('app.position') }}:</strong></td>
                                    <td>{{ $user->position }}</td>
                                </tr>
                                @endif
                            </table>
                            @endif
                        </div>
                    </div>

                    <!-- Student Marks (only for students) -->
                    @if($user->role === 'student')
                    <div class="row">
                        <div class="col-12">
                            <h5 class="mb-3">{{ __('app.student_marks') }}</h5>
                            @if($marks->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>{{ __('app.subject_name') }}</th>
                                                <th>{{ __('app.mark') }} 1</th>
                                                <th>{{ __('app.mark') }} 2</th>
                                                <th>{{ __('app.mark') }} 3</th>
                                                <th>{{ __('app.mark') }} 4</th>
                                                <th>{{ __('app.mark') }} 5</th>
                                                <th>{{ __('app.average') }}</th>
                                                <th>{{ __('app.academic_year') }}</th>
                                                <th>{{ __('app.created_at') }}</th>
                                                <th>{{ __('app.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($marks as $mark)
                                            <tr>
                                                <td>{{ $mark->subject_name }}</td>
                                                <td>
                                                    @if($mark->mark_1)
                                                        <span class="badge bg-primary">{{ $mark->mark_1 }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($mark->mark_2)
                                                        <span class="badge bg-primary">{{ $mark->mark_2 }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($mark->mark_3)
                                                        <span class="badge bg-secondary">{{ $mark->mark_3 }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($mark->mark_4)
                                                        <span class="badge bg-secondary">{{ $mark->mark_4 }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($mark->mark_5)
                                                        <span class="badge bg-secondary">{{ $mark->mark_5 }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($mark->mark)
                                                        @php
                                                            $average = $mark->mark;
                                                            $badgeClass = $average >= 16 ? 'bg-success' :
                                                                         ($average >= 12 ? 'bg-warning' :
                                                                         ($average >= 10 ? 'bg-info' : 'bg-danger'));
                                                        @endphp
                                                        <span class="badge {{ $badgeClass }}">{{ number_format($average, 2) }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>{{ $mark->academic_year ?? 'N/A' }}</td>
                                                <td>{{ $mark->created_at->format('d/m/Y') }}</td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('admin.marks.edit', $mark) }}"
                                                           class="btn btn-outline-primary btn-sm"
                                                           title="{{ __('app.edit') }}">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <form action="{{ route('admin.marks.destroy', $mark) }}"
                                                              method="POST" class="d-inline"
                                                              onsubmit="return confirm('{{ __('app.confirm_delete_mark') }}')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                    class="btn btn-outline-danger btn-sm"
                                                                    title="{{ __('app.delete') }}">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-award display-1 text-muted"></i>
                                    <h4 class="mt-3">{{ __('app.no_marks_found') }}</h4>
                                    <p class="text-muted">{{ __('app.start_by_adding_marks') }}</p>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMarkModal">
                                        <i class="bi bi-plus-circle me-2"></i>{{ __('app.add_first_mark') }}
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Mark Modal -->
@if($user->role === 'student')
<div class="modal fade" id="addMarkModal" tabindex="-1" aria-labelledby="addMarkModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMarkModalLabel">{{ __('app.add_marks') }} - {{ $user->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.marks.store') }}" method="POST" id="addMarkForm">
                @csrf
                <input type="hidden" name="user_id" value="{{ $user->id }}">

                <div class="modal-body">
                    <!-- 5 Mark Inputs -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="mb-3">{{ __('app.marks') }}</h6>
                            <p class="text-muted small">{{ __('app.marks_simple_description') }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-2">
                            <label for="modal_mark_1" class="form-label">{{ __('app.mark') }} 1 <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" max="20"
                                   class="form-control modal-mark-input"
                                   id="modal_mark_1" name="mark_1"
                                   placeholder="0.00" required>
                        </div>
                        <div class="col-md-2">
                            <label for="modal_mark_2" class="form-label">{{ __('app.mark') }} 2 <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" max="20"
                                   class="form-control modal-mark-input"
                                   id="modal_mark_2" name="mark_2"
                                   placeholder="0.00" required>
                        </div>
                        <div class="col-md-2">
                            <label for="modal_mark_3" class="form-label">{{ __('app.mark') }} 3</label>
                            <input type="number" step="0.01" min="0" max="20"
                                   class="form-control modal-mark-input"
                                   id="modal_mark_3" name="mark_3"
                                   placeholder="0.00">
                        </div>
                        <div class="col-md-2">
                            <label for="modal_mark_4" class="form-label">{{ __('app.mark') }} 4</label>
                            <input type="number" step="0.01" min="0" max="20"
                                   class="form-control modal-mark-input"
                                   id="modal_mark_4" name="mark_4"
                                   placeholder="0.00">
                        </div>
                        <div class="col-md-2">
                            <label for="modal_mark_5" class="form-label">{{ __('app.mark') }} 5</label>
                            <input type="number" step="0.01" min="0" max="20"
                                   class="form-control modal-mark-input"
                                   id="modal_mark_5" name="mark_5"
                                   placeholder="0.00">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">{{ __('app.average') }}</label>
                            <input type="text" class="form-control fw-bold text-center" id="modal_average_display" readonly>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('app.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>{{ __('app.add_marks') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Modal mark calculation
    function calculateModalAverage() {
        let total = 0;
        let count = 0;

        for (let i = 1; i <= 5; i++) {
            const markInput = document.getElementById(`modal_mark_${i}`);
            if (markInput) {
                const mark = parseFloat(markInput.value) || 0;
                if (mark > 0) {
                    total += mark;
                    count++;
                }
            }
        }

        const averageDisplay = document.getElementById('modal_average_display');
        if (count > 0) {
            const average = (total / count).toFixed(2);
            averageDisplay.value = average;

            // Color coding
            if (average >= 16) {
                averageDisplay.className = 'form-control fw-bold text-center text-success';
            } else if (average >= 12) {
                averageDisplay.className = 'form-control fw-bold text-center text-warning';
            } else if (average >= 10) {
                averageDisplay.className = 'form-control fw-bold text-center text-primary';
            } else {
                averageDisplay.className = 'form-control fw-bold text-center text-danger';
            }
        } else {
            averageDisplay.value = '';
            averageDisplay.className = 'form-control fw-bold text-center';
        }
    }

    // Add event listeners to modal mark inputs
    for (let i = 1; i <= 5; i++) {
        const markInput = document.getElementById(`modal_mark_${i}`);
        if (markInput) {
            markInput.addEventListener('input', calculateModalAverage);
        }
    }

    // Reset modal when closed
    document.getElementById('addMarkModal').addEventListener('hidden.bs.modal', function () {
        document.getElementById('addMarkForm').reset();
        calculateModalAverage();
    });

    // Initial calculation
    calculateModalAverage();
});
</script>
@endpush