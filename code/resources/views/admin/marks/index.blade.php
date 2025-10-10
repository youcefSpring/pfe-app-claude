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
                                        <th>{{ __('app.subject_name') }}</th>
                                        <th>{{ __('app.mark') }}</th>
                                        <th>{{ __('app.percentage') }}</th>
                                        <th>{{ __('app.letter_grade') }}</th>
                                        <th>{{ __('app.semester') }}</th>
                                        <th>{{ __('app.academic_year') }}</th>
                                        <th>{{ __('app.added_by') }}</th>
                                        <th>{{ __('app.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($marks as $mark)
                                        <tr>
                                            <td>
                                                <div>
                                                    <h6 class="mb-1">{{ $mark->student->name }}</h6>
                                                    @if($mark->student->matricule)
                                                        <small class="text-muted">{{ $mark->student->matricule }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>{{ $mark->subject_name }}</td>
                                            <td>
                                                <strong>{{ $mark->mark }}/{{ $mark->max_mark }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $mark->percentage >= 70 ? 'success' : ($mark->percentage >= 50 ? 'warning' : 'danger') }}">
                                                    {{ $mark->percentage }}%
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $mark->letter_grade === 'F' ? 'danger' : ($mark->percentage >= 70 ? 'success' : 'warning') }}">
                                                    {{ $mark->letter_grade }}
                                                </span>
                                            </td>
                                            <td>{{ $mark->semester ?? '-' }}</td>
                                            <td>{{ $mark->academic_year ?? '-' }}</td>
                                            <td>{{ $mark->creator->name ?? '-' }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('admin.marks.edit', $mark) }}"
                                                       class="btn btn-outline-primary" title="{{ __('app.edit') }}">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-outline-danger"
                                                            onclick="deleteMark({{ $mark->id }}, '{{ $mark->student->name }} - {{ $mark->subject_name }}')"
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

                        <div class="d-flex justify-content-center mt-4">
                            <nav aria-label="Marks pagination">
                                {{ $marks->links('pagination::bootstrap-4') }}
                            </nav>
                        </div>
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
function deleteMark(markId, markName) {
    document.getElementById('deleteMarkName').textContent = markName;
    document.getElementById('deleteMarkForm').action = `/admin/marks/${markId}`;

    const modal = new bootstrap.Modal(document.getElementById('deleteMarkModal'));
    modal.show();
}
</script>
@endpush