@extends('layouts.pfe-app')

@section('title', __('app.defense_calendar'))

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">{{ __('app.defense_calendar') }}</h1>
        <div class="btn-group">
            <a href="{{ route('defenses.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-list"></i> {{ __('app.list_view') }}
            </a>
            @if(in_array(auth()->user()?->role, ['admin', 'department_head']))
                <a href="{{ route('defenses.schedule-form') }}" class="btn btn-primary">
                    <i class="bi bi-calendar-plus"></i> {{ __('app.schedule_defense') }}
                </a>
            @endif
        </div>
    </div>

    <!-- Calendar Navigation -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <button class="btn btn-outline-secondary me-2" onclick="changeMonth(-1)">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <h4 class="mb-0 me-3" id="currentMonth">{{ date('F Y') }}</h4>
                        <button class="btn btn-outline-secondary" onclick="changeMonth(1)">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <div class="d-flex justify-content-end align-items-center">
                        <span class="badge bg-primary me-2">{{ __('app.scheduled') }}</span>
                        <span class="badge bg-warning text-dark me-2">{{ __('app.in_progress') }}</span>
                        <span class="badge bg-success me-2">{{ __('app.completed') }}</span>
                        <span class="badge bg-danger">{{ __('app.cancelled') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar Grid -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0" id="calendar">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center p-3">{{ __('app.sunday') }}</th>
                            <th class="text-center p-3">{{ __('app.monday') }}</th>
                            <th class="text-center p-3">{{ __('app.tuesday') }}</th>
                            <th class="text-center p-3">{{ __('app.wednesday') }}</th>
                            <th class="text-center p-3">{{ __('app.thursday') }}</th>
                            <th class="text-center p-3">{{ __('app.friday') }}</th>
                            <th class="text-center p-3">{{ __('app.saturday') }}</th>
                        </tr>
                    </thead>
                    <tbody id="calendarBody">
                        <!-- Calendar days will be populated by JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Upcoming Defenses -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Upcoming Defenses</h5>
                </div>
                <div class="card-body">
                    @if($defenses->count() > 0)
                        <div class="row">
                            @foreach($defenses->take(6) as $defense)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card border-start border-{{ $defense->status === 'completed' ? 'success' : ($defense->status === 'in_progress' ? 'warning' : ($defense->status === 'cancelled' ? 'danger' : 'primary')) }} border-3">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="card-title mb-1">{{ $defense->subject->title ?? 'Defense' }}</h6>
                                                <span class="badge bg-{{ $defense->status === 'completed' ? 'success' : ($defense->status === 'in_progress' ? 'warning' : ($defense->status === 'cancelled' ? 'danger' : 'primary')) }}">
                                                    {{ ucfirst(str_replace('_', ' ', $defense->status)) }}
                                                </span>
                                            </div>
                                            <p class="text-muted small mb-2">{{ $defense->subject->teacher->name ?? 'No Teacher' }}</p>
                                            @if($defense->defense_date && $defense->defense_time)
                                                <p class="small mb-2">
                                                    <i class="bi bi-calendar me-1"></i>
                                                    {{ \Carbon\Carbon::parse($defense->defense_date)->format('M d, Y') }} at {{ \Carbon\Carbon::parse($defense->defense_time)->format('g:i A') }}
                                                </p>
                                            @elseif($defense->defense_date)
                                                <p class="small mb-2">
                                                    <i class="bi bi-calendar me-1"></i>
                                                    {{ \Carbon\Carbon::parse($defense->defense_date)->format('M d, Y') }}
                                                </p>
                                            @endif
                                            @if($defense->room)
                                                <p class="small mb-2">
                                                    <i class="bi bi-geo-alt me-1"></i>
                                                    {{ $defense->room->name }}
                                                </p>
                                            @endif
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">{{ $defense->duration ?? 60 }} min</small>
                                                <a href="{{ route('defenses.show', $defense) }}" class="btn btn-outline-primary btn-sm">
                                                    View Details
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                            <h5 class="mt-3">No Defenses Scheduled</h5>
                            <p class="text-muted">There are no defenses scheduled at the moment.</p>
                            @if(in_array(auth()->user()?->role, ['admin', 'department_head']))
                                <a href="{{ route('defenses.schedule-form') }}" class="btn btn-primary">
                                    <i class="bi bi-calendar-plus"></i> Schedule First Defense
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Defense data from Laravel
const defenses = @json($defenses->map(function($defense) {
    return [
        'id' => $defense->id,
        'title' => $defense->subject->title ?? 'Defense',
        'date' => $defense->defense_date ? \Carbon\Carbon::parse($defense->defense_date)->format('Y-m-d') : null,
        'time' => $defense->defense_time ? \Carbon\Carbon::parse($defense->defense_time)->format('H:i') : null,
        'status' => $defense->status,
        'room' => $defense->room->name ?? 'TBD',
    ];
}));

let currentDate = new Date();

function generateCalendar(year, month) {
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const daysInMonth = lastDay.getDate();
    const startingDayOfWeek = firstDay.getDay();

    const calendarBody = document.getElementById('calendarBody');
    calendarBody.innerHTML = '';

    let date = 1;

    // Generate 6 weeks (42 days)
    for (let week = 0; week < 6; week++) {
        const row = document.createElement('tr');

        for (let day = 0; day < 7; day++) {
            const cell = document.createElement('td');
            cell.className = 'p-2 border-end border-bottom position-relative';
            cell.style.height = '120px';
            cell.style.verticalAlign = 'top';

            if (week === 0 && day < startingDayOfWeek) {
                // Empty cells before month starts
                cell.innerHTML = '';
            } else if (date > daysInMonth) {
                // Empty cells after month ends
                cell.innerHTML = '';
            } else {
                // Days of current month
                const dayNumber = document.createElement('div');
                dayNumber.className = 'fw-bold mb-1';
                dayNumber.textContent = date;
                cell.appendChild(dayNumber);

                // Check for defenses on this date
                const currentDateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(date).padStart(2, '0')}`;
                const dayDefenses = defenses.filter(defense => defense.date === currentDateStr);

                dayDefenses.forEach(defense => {
                    const defenseDiv = document.createElement('div');
                    defenseDiv.className = `small p-1 mb-1 rounded text-white bg-${getStatusColor(defense.status)}`;
                    defenseDiv.style.fontSize = '10px';
                    defenseDiv.innerHTML = `
                        <div class="fw-bold">${defense.time || 'TBD'}</div>
                        <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${defense.title.substring(0, 15)}...</div>
                    `;
                    defenseDiv.onclick = () => window.location.href = `/defenses/${defense.id}`;
                    defenseDiv.style.cursor = 'pointer';
                    cell.appendChild(defenseDiv);
                });

                date++;
            }

            row.appendChild(cell);
        }

        calendarBody.appendChild(row);

        if (date > daysInMonth) break;
    }
}

function getStatusColor(status) {
    switch(status) {
        case 'completed': return 'success';
        case 'in_progress': return 'warning';
        case 'cancelled': return 'danger';
        default: return 'primary';
    }
}

function updateMonthDisplay() {
    const months = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];

    document.getElementById('currentMonth').textContent =
        `${months[currentDate.getMonth()]} ${currentDate.getFullYear()}`;
}

function changeMonth(direction) {
    currentDate.setMonth(currentDate.getMonth() + direction);
    updateMonthDisplay();
    generateCalendar(currentDate.getFullYear(), currentDate.getMonth());
}

// Initialize calendar
document.addEventListener('DOMContentLoaded', function() {
    updateMonthDisplay();
    generateCalendar(currentDate.getFullYear(), currentDate.getMonth());
});
</script>

<style>
.table td {
    border-color: #dee2e6 !important;
}

.defense-event {
    cursor: pointer;
    transition: all 0.2s;
}

.defense-event:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
</style>
@endsection