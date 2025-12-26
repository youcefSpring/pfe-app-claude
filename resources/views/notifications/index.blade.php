@extends('layouts.pfe-app')

@section('page-title', __('app.notifications'))

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-bell me-2"></i>{{ __('app.notifications') }}
                </h5>
                @if($notifications->count() > 0)
                    <button class="btn btn-sm btn-outline-primary" onclick="markAllAsRead()">
                        <i class="bi bi-check-all me-1"></i>{{ __('app.mark_all_read') }}
                    </button>
                @endif
            </div>
            <div class="card-body">
                @if($notifications->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($notifications as $notification)
                            <div class="list-group-item {{ $notification->read_at ? '' : 'list-group-item-light border-start border-primary border-3' }}">
                                <div class="d-flex w-100 justify-content-between">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">
                                            {{ $notification->data['title'] ?? __('app.notification') }}
                                            @if(!$notification->read_at)
                                                <span class="badge bg-primary ms-2">{{ __('app.new') }}</span>
                                            @endif
                                        </h6>
                                        <p class="mb-1">{{ $notification->data['message'] ?? '' }}</p>
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            {{ $notification->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    <div class="ms-3">
                                        @if(!$notification->read_at)
                                            <button class="btn btn-sm btn-outline-success"
                                                    onclick="markAsRead('{{ $notification->id }}')">
                                                <i class="bi bi-check"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $notifications->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-bell-slash text-muted" style="font-size: 3rem;"></i>
                        <h6 class="mt-3 text-muted">{{ __('app.no_notifications') }}</h6>
                        <p class="text-muted">{{ __('app.no_notifications_yet') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function markAsRead(notificationId) {
    fetch(`/notifications/${notificationId}/read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function markAllAsRead() {
    fetch('/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
</script>
@endpush