@props(['type' => 'info', 'dismissible' => true, 'icon' => true])

@php
    $alertClasses = [
        'success' => 'alert-success',
        'error' => 'alert-danger',
        'warning' => 'alert-warning',
        'info' => 'alert-info'
    ];

    $alertIcons = [
        'success' => 'bi-check-circle',
        'error' => 'bi-exclamation-triangle',
        'warning' => 'bi-exclamation-triangle',
        'info' => 'bi-info-circle'
    ];

    $alertClass = $alertClasses[$type] ?? 'alert-info';
    $alertIcon = $alertIcons[$type] ?? 'bi-info-circle';
@endphp

<div {{ $attributes->merge(['class' => 'alert ' . $alertClass . ($dismissible ? ' alert-dismissible fade show' : '')]) }} role="alert">
    @if($icon)
        <i class="bi {{ $alertIcon }} me-2"></i>
    @endif

    {{ $slot }}

    @if($dismissible)
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    @endif
</div>