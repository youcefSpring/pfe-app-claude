{{-- Render different field types based on setting type --}}
<div class="mb-3">
    @if($setting['type'] === 'boolean')
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch"
                   id="{{ $setting['key'] }}"
                   name="{{ $setting['key'] }}"
                   value="1"
                   {{ $setting['value'] == '1' ? 'checked' : '' }}>
            <label class="form-check-label" for="{{ $setting['key'] }}">
                <strong>{{ ucfirst(str_replace('_', ' ', $setting['key'])) }}</strong>
                @if($setting['description'])
                    <br><small class="text-muted">{{ $setting['description'] }}</small>
                @endif
            </label>
        </div>
    @elseif($setting['type'] === 'integer')
        <label for="{{ $setting['key'] }}" class="form-label">
            <strong>{{ ucfirst(str_replace('_', ' ', $setting['key'])) }}</strong>
        </label>
        <input type="number" class="form-control"
               id="{{ $setting['key'] }}"
               name="{{ $setting['key'] }}"
               value="{{ $setting['value'] }}"
               min="0">
        @if($setting['description'])
            <small class="form-text text-muted">{{ $setting['description'] }}</small>
        @endif
    @elseif($setting['type'] === 'text')
        <label for="{{ $setting['key'] }}" class="form-label">
            <strong>{{ ucfirst(str_replace('_', ' ', $setting['key'])) }}</strong>
        </label>
        <textarea class="form-control"
                  id="{{ $setting['key'] }}"
                  name="{{ $setting['key'] }}"
                  rows="3">{{ $setting['value'] }}</textarea>
        @if($setting['description'])
            <small class="form-text text-muted">{{ $setting['description'] }}</small>
        @endif
    @else
        {{-- Default: string --}}
        <label for="{{ $setting['key'] }}" class="form-label">
            <strong>{{ ucfirst(str_replace('_', ' ', $setting['key'])) }}</strong>
        </label>
        <input type="text" class="form-control"
               id="{{ $setting['key'] }}"
               name="{{ $setting['key'] }}"
               value="{{ $setting['value'] }}">
        @if($setting['description'])
            <small class="form-text text-muted">{{ $setting['description'] }}</small>
        @endif
    @endif
</div>
