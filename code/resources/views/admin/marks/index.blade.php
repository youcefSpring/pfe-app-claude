@extends('layouts.pfe-app')

@section('page-title', __('app.student_marks_management'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="btn-group" role="group">
                        <a href="{{ route('admin.marks.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>{{ __('app.add_mark') }}
                        </a>
                        <a href="{{ route('admin.marks.bulk-create') }}" class="btn btn-success">
                            <i class="bi bi-journal-plus me-2"></i>{{ __('app.add_student_marks_bulk') }}
                        </a>
                        <a href="{{ route('admin.marks.bulk-all-create') }}" class="btn btn-warning">
                            <i class="bi bi-people-fill me-2"></i>{{ __('app.add_marks_all_students') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($marks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('app.student') }}</th>
                                        <th>{{ __('app.academic_year') }}</th>
                                        <th>{{ __('app.marks') }}</th>
                                        <th>{{ __('app.average') }}</th>
                                        <th>{{ __('app.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($marks as $mark)
                                        @php
                                            // Calculate correct average from 5 marks (exclude 0 and null)
                                            $validMarks = array_filter([
                                                $mark->mark_1,
                                                $mark->mark_2,
                                                $mark->mark_3,
                                                $mark->mark_4,
                                                $mark->mark_5
                                            ], function($m) {
                                                return $m !== null && $m !== '' && $m > 0;
                                            });
                                            $average = count($validMarks) > 0 ? array_sum($validMarks) / count($validMarks) : 0;
                                        @endphp
                                        <tr>
                                            <td>
                                                <div>
                                                    <h6 class="mb-1">{{ $mark->student->name }}</h6>
                                                    @if($mark->student->matricule)
                                                        <small class="text-muted">{{ $mark->student->matricule }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $mark->academic_year }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    @if($mark->mark_1 !== null && $mark->mark_1 > 0)
                                                        <span class="badge bg-primary">{{ number_format($mark->mark_1, 2) }}</span>
                                                    @endif
                                                    @if($mark->mark_2 !== null && $mark->mark_2 > 0)
                                                        <span class="badge bg-primary">{{ number_format($mark->mark_2, 2) }}</span>
                                                    @endif
                                                    @if($mark->mark_3 !== null && $mark->mark_3 > 0)
                                                        <span class="badge bg-secondary">{{ number_format($mark->mark_3, 2) }}</span>
                                                    @endif
                                                    @if($mark->mark_4 !== null && $mark->mark_4 > 0)
                                                        <span class="badge bg-secondary">{{ number_format($mark->mark_4, 2) }}</span>
                                                    @endif
                                                    @if($mark->mark_5 !== null && $mark->mark_5 > 0)
                                                        <span class="badge bg-secondary">{{ number_format($mark->mark_5, 2) }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <strong class="text-{{ $average >= 16 ? 'success' : ($average >= 12 ? 'info' : ($average >= 10 ? 'warning' : 'danger')) }}">
                                                    {{ number_format($average, 2) }} / 20
                                                </strong>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-outline-info"
                                                            onclick="showMarkDetails({{ $mark->id }}, '{{ $mark->student->name }}', '{{ $mark->student->matricule }}', '{{ $mark->academic_year }}', {{ $mark->mark_1 ?? 0 }}, {{ $mark->mark_2 ?? 0 }}, {{ $mark->mark_3 ?? 0 }}, {{ $mark->mark_4 ?? 0 }}, {{ $mark->mark_5 ?? 0 }}, {{ number_format($average, 2) }}, '{{ $mark->creator->name ?? '-' }}', '{{ $mark->created_at->format('d/m/Y H:i') }}', '{{ $mark->updated_at->format('d/m/Y H:i') }}')"
                                                            title="{{ __('app.details') }}">
                                                        <i class="bi bi-info-circle"></i>
                                                    </button>
                                                    <a href="{{ route('admin.marks.edit', $mark) }}"
                                                       class="btn btn-outline-primary" title="{{ __('app.edit') }}">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-outline-danger"
                                                            onclick="deleteMark({{ $mark->id }}, '{{ $mark->student->name }}')"
                                                            title="{{ __('app.delete') }}">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <x-admin-pagination :paginator="$marks" />
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">{{ __('app.no_marks_found') }}</h5>
                            <p class="text-muted">{{ __('app.start_by_adding_marks') }}</p>
                            <a href="{{ route('admin.marks.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>{{ __('app.add_first_mark') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mark Details Modal -->
<div class="modal fade" id="markDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="bi bi-info-circle me-2"></i>{{ __('app.mark_details') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">{{ __('app.student') }}</h6>
                        <p class="mb-0"><strong id="detailStudentName"></strong></p>
                        <small class="text-muted" id="detailStudentMatricule"></small>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">{{ __('app.academic_year') }}</h6>
                        <p class="mb-0"><span class="badge bg-info" id="detailAcademicYear"></span></p>
                    </div>
                </div>

                <hr>

                <div class="mb-3">
                    <h6 class="text-muted mb-3">{{ __('app.marks') }}</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('app.mark') }} 1</th>
                                    <th>{{ __('app.mark') }} 2</th>
                                    <th>{{ __('app.mark') }} 3</th>
                                    <th>{{ __('app.mark') }} 4</th>
                                    <th>{{ __('app.mark') }} 5</th>
                                    <th class="table-info">{{ __('app.average') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td id="detailMark1">-</td>
                                    <td id="detailMark2">-</td>
                                    <td id="detailMark3">-</td>
                                    <td id="detailMark4">-</td>
                                    <td id="detailMark5">-</td>
                                    <td class="table-info"><strong id="detailAverage"></strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted mb-2"><i class="bi bi-person-plus me-2"></i>{{ __('app.added_by') }}</h6>
                        <p class="mb-0" id="detailCreator"></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted mb-2"><i class="bi bi-calendar-plus me-2"></i>{{ __('app.created_at') }}</h6>
                        <p class="mb-0" id="detailCreatedAt"></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2"><i class="bi bi-calendar-check me-2"></i>{{ __('app.updated_at') }}</h6>
                        <p class="mb-0" id="detailUpdatedAt"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('app.close') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Mark Modal -->
<div class="modal fade" id="deleteMarkModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('app.delete_mark') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h6>⚠️ {{ __('app.action_cannot_be_undone') }}</h6>
                    <p class="mb-0">{{ __('app.deleting_mark_warning') }}</p>
                </div>
                <p>{{ __('app.confirm_delete_mark') }} <strong id="deleteMarkName"></strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('app.cancel') }}</button>
                <form id="deleteMarkForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> {{ __('app.delete_mark') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showMarkDetails(markId, studentName, matricule, academicYear, mark1, mark2, mark3, mark4, mark5, average, creator, createdAt, updatedAt) {
    // Fill student info
    document.getElementById('detailStudentName').textContent = studentName;
    document.getElementById('detailStudentMatricule').textContent = matricule ? matricule : '';
    document.getElementById('detailAcademicYear').textContent = academicYear;

    // Fill marks (only show if > 0)
    document.getElementById('detailMark1').textContent = mark1 > 0 ? parseFloat(mark1).toFixed(2) : '-';
    document.getElementById('detailMark2').textContent = mark2 > 0 ? parseFloat(mark2).toFixed(2) : '-';
    document.getElementById('detailMark3').textContent = mark3 > 0 ? parseFloat(mark3).toFixed(2) : '-';
    document.getElementById('detailMark4').textContent = mark4 > 0 ? parseFloat(mark4).toFixed(2) : '-';
    document.getElementById('detailMark5').textContent = mark5 > 0 ? parseFloat(mark5).toFixed(2) : '-';
    document.getElementById('detailAverage').textContent = average + ' / 20';

    // Fill creator and dates
    document.getElementById('detailCreator').textContent = creator;
    document.getElementById('detailCreatedAt').textContent = createdAt;
    document.getElementById('detailUpdatedAt').textContent = updatedAt;

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('markDetailsModal'));
    modal.show();
}

function deleteMark(markId, markName) {
    document.getElementById('deleteMarkName').textContent = markName;
    document.getElementById('deleteMarkForm').action = `/admin/marks/${markId}`;

    const modal = new bootstrap.Modal(document.getElementById('deleteMarkModal'));
    modal.show();
}
</script>
@endpush