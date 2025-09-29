@extends('layouts.admin')

@section('title', __('Rooms Management'))
@section('page-title', __('Rooms Management'))

@section('breadcrumbs')
<span class="text-muted">{{ __('Home') }} / {{ __('Administration') }} / {{ __('Rooms') }}</span>
@endsection

@section('content')
<div class="container-fluid p-4">
    <!-- Header Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">{{ __('Rooms Management') }}</h1>
            <p class="text-muted">{{ __('Manage defense rooms and their availability') }}</p>
        </div>
        <div>
            <a href="{{ route('pfe.admin.rooms.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>{{ __('Add Room') }}
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-door-open fa-2x me-3"></i>
                        <div>
                            <h4 class="mb-0">{{ $stats['total_rooms'] ?? 12 }}</h4>
                            <small>{{ __('Total Rooms') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle fa-2x me-3"></i>
                        <div>
                            <h4 class="mb-0">{{ $stats['available_rooms'] ?? 8 }}</h4>
                            <small>{{ __('Available') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-calendar-alt fa-2x me-3"></i>
                        <div>
                            <h4 class="mb-0">{{ $stats['booked_rooms'] ?? 4 }}</h4>
                            <small>{{ __('Currently Booked') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-tools fa-2x me-3"></i>
                        <div>
                            <h4 class="mb-0">{{ $stats['maintenance_rooms'] ?? 0 }}</h4>
                            <small>{{ __('Under Maintenance') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('pfe.admin.rooms.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="search" class="form-label">{{ __('Search') }}</label>
                        <input type="text" class="form-control" id="search" name="search"
                               value="{{ request('search') }}" placeholder="{{ __('Search by name or location...') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="capacity" class="form-label">{{ __('Minimum Capacity') }}</label>
                        <select class="form-select" id="capacity" name="capacity">
                            <option value="">{{ __('Any Capacity') }}</option>
                            <option value="10" {{ request('capacity') == '10' ? 'selected' : '' }}>{{ __('10+ people') }}</option>
                            <option value="20" {{ request('capacity') == '20' ? 'selected' : '' }}>{{ __('20+ people') }}</option>
                            <option value="30" {{ request('capacity') == '30' ? 'selected' : '' }}>{{ __('30+ people') }}</option>
                            <option value="50" {{ request('capacity') == '50' ? 'selected' : '' }}>{{ __('50+ people') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">{{ __('Status') }}</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">{{ __('All Status') }}</option>
                            <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>{{ __('Available') }}</option>
                            <option value="booked" {{ request('status') == 'booked' ? 'selected' : '' }}>{{ __('Booked') }}</option>
                            <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>{{ __('Maintenance') }}</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-search me-1"></i>{{ __('Filter') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Rooms Grid/List -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">{{ __('Rooms List') }}</h5>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-secondary active" id="grid-view">
                    <i class="fas fa-th-large"></i>
                </button>
                <button type="button" class="btn btn-outline-secondary" id="list-view">
                    <i class="fas fa-list"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Grid View -->
            <div id="rooms-grid" class="row g-4">
                @forelse($rooms ?? [] as $room)
                <div class="col-xl-4 col-lg-6 col-md-6">
                    <div class="card h-100 room-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">{{ $room->name ?? 'Room A101' }}</h6>
                            <span class="badge bg-{{ ($room->status ?? 'available') == 'available' ? 'success' : (($room->status ?? 'available') == 'booked' ? 'warning' : 'danger') }}">
                                {{ __(ucfirst($room->status ?? 'available')) }}
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="room-info">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-map-marker-alt text-muted me-2"></i>
                                    <span class="small">{{ $room->location ?? 'Building A, Floor 1' }}</span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-users text-muted me-2"></i>
                                    <span class="small">{{ __('Capacity:') }} {{ $room->capacity ?? 25 }} {{ __('people') }}</span>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-door-open text-muted me-2"></i>
                                    <span class="small">{{ __('Type:') }} {{ $room->type ?? 'Conference Room' }}</span>
                                </div>

                                @if($room->features ?? ['Projector', 'Whiteboard'])
                                <div class="mb-3">
                                    <small class="text-muted d-block mb-1">{{ __('Features:') }}</small>
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($room->features ?? ['Projector', 'Whiteboard'] as $feature)
                                        <span class="badge bg-light text-dark">{{ $feature }}</span>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                @if(($room->status ?? 'available') == 'booked')
                                <div class="alert alert-warning py-2 mb-0">
                                    <small>
                                        <i class="fas fa-calendar me-1"></i>
                                        {{ __('Next available:') }} {{ $room->next_available ?? 'Tomorrow 10:00 AM' }}
                                    </small>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="btn-group w-100" role="group">
                                <a href="{{ route('pfe.admin.rooms.show', $room->id ?? 1) }}"
                                   class="btn btn-sm btn-outline-info" title="{{ __('View') }}">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('pfe.admin.rooms.edit', $room->id ?? 1) }}"
                                   class="btn btn-sm btn-outline-primary" title="{{ __('Edit') }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-success"
                                        title="{{ __('Book Room') }}" onclick="bookRoom({{ $room->id ?? 1 }})">
                                    <i class="fas fa-calendar-plus"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger"
                                        title="{{ __('Delete') }}" onclick="confirmDelete({{ $room->id ?? 1 }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <i class="fas fa-door-open text-muted mb-3" style="font-size: 4rem;"></i>
                    <h5 class="text-muted">{{ __('No rooms found') }}</h5>
                    <p class="text-muted">{{ __('Start by adding your first room to the system') }}</p>
                    <a href="{{ route('pfe.admin.rooms.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>{{ __('Add First Room') }}
                    </a>
                </div>
                @endforelse
            </div>

            <!-- List View (Hidden by default) -->
            <div id="rooms-list" class="d-none">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('Room') }}</th>
                                <th>{{ __('Location') }}</th>
                                <th>{{ __('Capacity') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Next Available') }}</th>
                                <th class="text-end">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rooms ?? [] as $room)
                            <tr>
                                <td>
                                    <h6 class="mb-0">{{ $room->name ?? 'Room A101' }}</h6>
                                </td>
                                <td>{{ $room->location ?? 'Building A, Floor 1' }}</td>
                                <td>{{ $room->capacity ?? 25 }} {{ __('people') }}</td>
                                <td>{{ $room->type ?? 'Conference Room' }}</td>
                                <td>
                                    <span class="badge bg-{{ ($room->status ?? 'available') == 'available' ? 'success' : (($room->status ?? 'available') == 'booked' ? 'warning' : 'danger') }}">
                                        {{ __(ucfirst($room->status ?? 'available')) }}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ ($room->status ?? 'available') == 'available' ? __('Available now') : ($room->next_available ?? 'Tomorrow 10:00 AM') }}
                                    </small>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('pfe.admin.rooms.show', $room->id ?? 1) }}"
                                           class="btn btn-sm btn-outline-info" title="{{ __('View') }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('pfe.admin.rooms.edit', $room->id ?? 1) }}"
                                           class="btn btn-sm btn-outline-primary" title="{{ __('Edit') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-success"
                                                title="{{ __('Book Room') }}" onclick="bookRoom({{ $room->id ?? 1 }})">
                                            <i class="fas fa-calendar-plus"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                title="{{ __('Delete') }}" onclick="confirmDelete({{ $room->id ?? 1 }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-door-open text-muted mb-3" style="font-size: 3rem;"></i>
                                    <p class="text-muted">{{ __('No rooms found') }}</p>
                                    <a href="{{ route('pfe.admin.rooms.create') }}" class="btn btn-primary">
                                        {{ __('Add First Room') }}
                                    </a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @if(isset($rooms) && method_exists($rooms, 'links'))
        <div class="card-footer">
            {{ $rooms->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
// View toggle functionality
document.getElementById('grid-view').addEventListener('click', function() {
    document.getElementById('rooms-grid').classList.remove('d-none');
    document.getElementById('rooms-list').classList.add('d-none');
    this.classList.add('active');
    document.getElementById('list-view').classList.remove('active');
});

document.getElementById('list-view').addEventListener('click', function() {
    document.getElementById('rooms-list').classList.remove('d-none');
    document.getElementById('rooms-grid').classList.add('d-none');
    this.classList.add('active');
    document.getElementById('grid-view').classList.remove('active');
});

function bookRoom(roomId) {
    // Open booking modal or redirect to booking page
    window.location.href = `/pfe/admin/rooms/${roomId}/book`;
}

function confirmDelete(roomId) {
    if (confirm('{{ __("Are you sure you want to delete this room?") }}')) {
        // Create and submit a delete form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/pfe/admin/rooms/${roomId}`;

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';

        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';

        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush