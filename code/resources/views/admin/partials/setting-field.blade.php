{{-- Render different field types based on setting type --}}
{{-- ✅ FIXED: Access object properties instead of array keys --}}
<div class="mb-3">
    @if($setting->type === 'boolean')
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch"
                   id="{{ $setting->key }}"
                   name="{{ $setting->key }}"
                   value="1"
                   {{ $setting->value == '1' ? 'checked' : '' }}>
            <label class="form-check-label" for="{{ $setting->key }}">
                <strong>{{ __('settings.' . $setting->key . '_label') }}</strong>
                @if(__('settings.' . $setting->key . '_desc') !== 'settings.' . $setting->key . '_desc')
                    <br><small class="text-muted">{{ __('settings.' . $setting->key . '_desc') }}</small>
                @endif
            </label>
        </div>
    @elseif($setting->type === 'integer')
        <label for="{{ $setting->key }}" class="form-label">
            <strong>{{ __('settings.' . $setting->key . '_label') }}</strong>
        </label>
        <input type="number" class="form-control"
               id="{{ $setting->key }}"
               name="{{ $setting->key }}"
               value="{{ $setting->value }}"
               min="0">
        @if(__('settings.' . $setting->key . '_desc') !== 'settings.' . $setting->key . '_desc')
            <small class="form-text text-muted">{{ __('settings.' . $setting->key . '_desc') }}</small>
        @endif
    @elseif($setting->type === 'text')
        <label for="{{ $setting->key }}" class="form-label">
            <strong>{{ __('settings.' . $setting->key . '_label') }}</strong>
        </label>
        <textarea class="form-control"
                  id="{{ $setting->key }}"
                  name="{{ $setting->key }}"
                  rows="3">{{ $setting->value }}</textarea>
        @if(__('settings.' . $setting->key . '_desc') !== 'settings.' . $setting->key . '_desc')
            <small class="form-text text-muted">{{ __('settings.' . $setting->key . '_desc') }}</small>
        @endif
    @else
        {{-- Default: string --}}
        <label for="{{ $setting->key }}" class="form-label">
            <strong>{{ __('settings.' . $setting->key . '_label') }}</strong>
        </label>
        <input type="text" class="form-control"
               id="{{ $setting->key }}"
               name="{{ $setting->key }}"
               value="{{ $setting->value }}">
        @if(__('settings.' . $setting->key . '_desc') !== 'settings.' . $setting->key . '_desc')
            <small class="form-text text-muted">{{ __('settings.' . $setting->key . '_desc') }}</small>
        @endif
    @endif
</div>
