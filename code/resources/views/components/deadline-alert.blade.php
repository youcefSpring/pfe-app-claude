@props([
    'type' => 'info',
    'icon' => null,
    'title' => '',
    'message' => '',
    'time' => null,
    'showCountdown' => false
])

@php
    $iconMap = [
        'success' => 'bi-check-circle-fill',
        'warning' => 'bi-exclamation-triangle-fill',
        'danger' => 'bi-x-circle-fill',
        'info' => 'bi-info-circle-fill'
    ];

    $displayIcon = $icon ?? $iconMap[$type] ?? 'bi-info-circle-fill';
@endphp

<div class="deadline-alert deadline-alert-{{ $type }}">
    <div class="deadline-alert-icon">
        <i class="bi {{ $displayIcon }}"></i>
    </div>
    <div class="deadline-alert-content">
        @if($title)
            <div class="deadline-alert-title">
                {{ $title }}
            </div>
        @endif

        @if($message)
            <div class="deadline-alert-message">
                {{ $message }}
            </div>
        @endif

        @if($time)
            <div class="deadline-alert-time">
                <i class="bi bi-clock"></i> {{ $time }}
            </div>
        @endif

        @if($showCountdown && $time)
            <div class="deadline-countdown">
                <i class="bi bi-hourglass-split"></i>
                <span>{{ $time }}</span>
            </div>
        @endif

        {{ $slot }}
    </div>
</div>
