@props([
    'name',
    'label' => null,
    'type' => 'text',
    'required' => false,
    'help' => null,
    'icon' => null
])

<div class="mb-3">
    @if($label)
        <label for="{{ $name }}" class="form-label">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    @if($icon)
        <div class="input-group">
            <span class="input-group-text">
                <i class="bi {{ $icon }}"></i>
            </span>
            <input
                type="{{ $type }}"
                name="{{ $name }}"
                id="{{ $name }}"
                {{ $attributes->merge(['class' => 'form-control' . ($errors->has($name) ? ' is-invalid' : '')]) }}
                value="{{ old($name, $attributes->get('value')) }}"
                {{ $required ? 'required' : '' }}
            >
        </div>
    @else
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $name }}"
            {{ $attributes->merge(['class' => 'form-control' . ($errors->has($name) ? ' is-invalid' : '')]) }}
            value="{{ old($name, $attributes->get('value')) }}"
            {{ $required ? 'required' : '' }}
        >
    @endif

    @if($errors->has($name))
        <div class="invalid-feedback">
            {{ $errors->first($name) }}
        </div>
    @endif

    @if($help)
        <div class="form-text">{{ $help }}</div>
    @endif
</div>