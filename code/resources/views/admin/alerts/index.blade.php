@extends('layouts.pfe-app')

@section('page-title', __('app.student_alerts'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">{{ __('app.student_alerts_management') }}</h4>
                    <div class="d-flex gap-2">
                        <span class="badge bg-warning">{{ $alerts->where('status', 'pending')->count() }} {{ __('app.pending') }}</span>
                        <span class="badge bg-success">{{ $alerts->where('status', 'responded')->count() }} {{ __('app.responded') }}</span>
                    </div>
                </div>
                <div class="card-body">
                    @if($alerts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('app.student') }}</th>
                                        <th>{{ __('app.message') }}</th>
                                        <th>{{ __('app.status') }}</th>
                                        <th>{{ __('app.sent_at') }}</th>
                                        <th>{{ __('app.responded_by') }}</th>
                                        <th>{{ __('app.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($alerts as $alert)
                                        <tr class="{{ $alert->status === 'pending' ? 'table-warning' : '' }}">
                                            <td>
                                                <div>
                                                    <strong>{{ $alert->student->name }}</strong>
                                                    <br><small class="text-muted">{{ $alert->student->email }}</small>
                                                    @if($alert->student->matricule)
                                                        <br><small class="text-muted">{{ $alert->student->matricule }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="message-preview">
                                                    {{ Str::limit($alert->message, 100) }}
                                                    @if(strlen($alert->message) > 100)
                                                        <button type="button" class="btn btn-link btn-sm p-0"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#messageModal"
                                                                data-message="{{ $alert->message }}"
                                                                data-student="{{ $alert->student->name }}">
                                                            {{ __('app.read_more') }}
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if($alert->status === 'pending')
                                                    <span class="badge bg-warning">{{ __('app.pending') }}</span>
                                                @else
                                                    <span class="badge bg-success">{{ __('app.responded') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div>{{ $alert->created_at->format('M d, Y') }}</div>
                                                <small class="text-muted">{{ $alert->created_at->format('H:i') }}</small>
                                            </td>
                                            <td>
                                                @if($alert->respondedBy)
                                                    <div>
                                                        <strong>{{ $alert->respondedBy->name }}</strong>
                                                        <br><small class="text-muted">{{ $alert->responded_at->diffForHumans() }}</small>
                                                    </div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('admin.alerts.show', $alert) }}"
                                                       class="btn btn-outline-primary"
                                                       title="{{ __('app.view_details') }}">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    @if($alert->status === 'pending')
                                                        <button type="button"
                                                                class="btn btn-outline-success"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#responseModal"
                                                                data-alert-id="{{ $alert->id }}"
                                                                data-student="{{ $alert->student->name }}"
                                                                data-message="{{ $alert->message }}"
                                                                title="{{ __('app.respond') }}">
                                                            <i class="bi bi-reply"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <x-admin-pagination :paginator="$alerts" />
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-bell fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">{{ __('app.no_alerts_received') }}</h5>
                            <p class="text-muted">{{ __('app.students_can_send_alerts') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Message Modal -->
<div class="modal fade" id="messageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('app.full_message') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <strong>{{ __('app.from') }}:</strong>
                    <span id="messageModalStudent"></span>
                </div>
                <div class="border p-3 bg-light rounded">
                    <div id="messageModalContent"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('app.close') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Response Modal -->
<div class="modal fade" id="responseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('app.respond_to_alert') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="responseForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <strong>{{ __('app.student') }}:</strong>
                        <span id="responseModalStudent"></span>
                    </div>
                    <div class="mb-3">
                        <strong>{{ __('app.original_message') }}:</strong>
                        <div class="border p-3 bg-light rounded">
                            <div id="responseModalMessage"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="admin_response" class="form-label">{{ __('app.your_response') }} <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="admin_response" name="admin_response" rows="4"
                                  placeholder="{{ __('app.type_your_response') }}" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('app.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send"></i> {{ __('app.send_response') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Message modal handler
    const messageModal = document.getElementById('messageModal');
    messageModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const message = button.getAttribute('data-message');
        const student = button.getAttribute('data-student');

        document.getElementById('messageModalStudent').textContent = student;
        document.getElementById('messageModalContent').textContent = message;
    });

    // Response modal handler
    const responseModal = document.getElementById('responseModal');
    responseModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const alertId = button.getAttribute('data-alert-id');
        const student = button.getAttribute('data-student');
        const message = button.getAttribute('data-message');

        document.getElementById('responseModalStudent').textContent = student;
        document.getElementById('responseModalMessage').textContent = message;
        document.getElementById('responseForm').action = `/admin/alerts/${alertId}/respond`;
        document.getElementById('admin_response').value = '';
    });
});
</script>
@endpush