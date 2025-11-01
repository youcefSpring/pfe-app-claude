@props(['icon' => 'bi-info-circle'])

<div class="page-description">
    <div class="d-flex align-items-start">
        <div class="page-description-icon">
            <i class="bi {{ $icon }}"></i>
        </div>
        <div class="page-description-text flex-grow-1">
            {{ $slot }}
        </div>
    </div>
</div>
