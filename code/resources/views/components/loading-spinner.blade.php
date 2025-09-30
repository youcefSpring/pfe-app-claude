@props(['size' => 'md', 'text' => 'Loading...'])

@php
    $sizeClasses = [
        'sm' => 'spinner-border-sm',
        'md' => '',
        'lg' => 'spinner-grow'
    ];

    $sizeClass = $sizeClasses[$size] ?? '';
@endphp

<div {{ $attributes->merge(['class' => 'd-flex justify-content-center align-items-center']) }}>
    <div class="spinner-border {{ $sizeClass }}" role="status" aria-hidden="true"></div>
    @if($text)
        <span class="ms-2">{{ $text }}</span>
    @endif
</div>