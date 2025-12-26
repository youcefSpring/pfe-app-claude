@props(['title' => null, 'icon' => null, 'footer' => null])

<div {{ $attributes->merge(['class' => 'card']) }}>
    @if($title || $icon)
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">
                @if($icon)
                    <i class="bi {{ $icon }} text-primary me-2"></i>
                @endif
                {{ $title }}
            </h5>
        </div>
    @endif

    <div class="card-body">
        {{ $slot }}
    </div>

    @if($footer)
        <div class="card-footer bg-light">
            {{ $footer }}
        </div>
    @endif
</div>