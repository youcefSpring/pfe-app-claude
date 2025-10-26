@extends('layouts.pfe-app')

@section('page-title', 'Room Management')

@section('content')
        <div class="container-fluid">
            <!-- Page Header -->
            {{-- <div class="row mb-4">
                <div class="col-12">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h4 class="card-title mb-2">Room Management</h4>
                                    <p class="card-text mb-0">Manage classrooms, capacity, and available materials</p>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-building" style="font-size: 3rem; opacity: 0.7;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}

            <!-- Action Bar -->
            {{-- <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title mb-1">Rooms ({{ $rooms->total() }})</h5>
                                    <small class="text-muted">Manage all classroom facilities</small>
                                </div>
                                <div>
                                    <a href="{{ route('admin.rooms.create') }}" class="btn btn-primary">
                                        <i class="bi bi-plus-circle me-2"></i>Add New Room
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}

            <!-- Rooms Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-primary">
                        <div class="card-body text-center">
                            <h3 class="text-primary mb-1">{{ $rooms->total() }}</h3>
                            <small class="text-muted">Total Rooms</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-success">
                        <div class="card-body text-center">
                            @php
    $availableCount = $rooms->where('is_available', true)->count();
                            @endphp
                            <h3 class="text-success mb-1">{{ $availableCount }}</h3>
                            <small class="text-muted">Available</small>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-warning">
                        <div class="card-body text-center">
                            @php
    $roomsWithDefenses = $rooms->where('defenses_count', '>', 0)->count();
                            @endphp
                            <h3 class="text-warning mb-1">{{ $roomsWithDefenses }}</h3>
                            <small class="text-muted">In Use</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-info">
                        <div class="card-body text-center">
                            {{-- @php
                            $totalCapacity = $rooms->sum('capacity');
                            @endphp
                            <h3 class="text-info mb-1">{{ $totalCapacity }}</h3>
                            <small class="text-muted">Total Capacity</small> --}}
                            <a href="{{ route('admin.rooms.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Add New Room
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rooms Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-list me-2"></i>All Rooms
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($rooms->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Room Name</th>
                                                <th>Location</th>
                                                <th>Capacity</th>
                                                <th>Equipment</th>
                                                <th>Status</th>
                                                <th>Defenses</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($rooms as $room)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <i class="bi bi-door-open text-primary me-2"></i>
                                                            <strong>{{ $room->name }}</strong>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="text-muted">{{ $room->location ?? 'Not specified' }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info">{{ $room->capacity }} seats</span>
                                                    </td>
                                                    <td>
                                                        @if($room->equipment)
                                                            <span class="text-truncate d-inline-block" style="max-width: 200px;"
                                                                  title="{{ $room->equipment }}">
                                                                {{ $room->equipment }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">No equipment listed</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($room->is_available)
                                                            <span class="badge bg-success">Available</span>
                                                        @else
                                                            <span class="badge bg-danger">Unavailable</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-secondary">{{ $room->defenses_count }} scheduled</span>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm" role="group">
                                                            <a href="{{ route('admin.rooms.edit', $room) }}"
                                                               class="btn btn-outline-primary" title="Edit Room">
                                                                <i class="bi bi-pencil"></i>
                                                            </a>
                                                            @if($room->defenses_count == 0)
                                                                <form action="{{ route('admin.rooms.destroy', $room) }}"
                                                                      method="POST" class="d-inline"
                                                                      onsubmit="return confirm('Are you sure you want to delete this room?')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-outline-danger" title="Delete Room">
                                                                        <i class="bi bi-trash"></i>
                                                                    </button>
                                                                </form>
                                                            @else
                                                                <button class="btn btn-outline-secondary" disabled title="Cannot delete room with scheduled defenses">
                                                                    <i class="bi bi-trash"></i>
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
                                <x-admin-pagination :paginator="$rooms" />
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-building text-muted" style="font-size: 4rem;"></i>
                                    <h5 class="text-muted mt-3">No Rooms Found</h5>
                                    <p class="text-muted">Start by adding your first classroom.</p>
                                    <a href="{{ route('admin.rooms.create') }}" class="btn btn-primary">
                                        <i class="bi bi-plus-circle me-2"></i>Add First Room
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
