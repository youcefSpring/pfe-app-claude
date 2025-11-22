{{-- Modern Setting Field Component --}}
<div class="setting-card">
    @if($setting->type === 'boolean')
        {{-- Toggle Switch Style --}}
        <div class="setting-toggle">
            <div class="setting-info">
                <div class="setting-header">
                    <i class="bi bi-toggle-on setting-icon"></i>
                    <label class="setting-title" for="{{ $setting->key }}">
                        {{ __('settings.' . $setting->key . '_label', $setting->description) }}
                    </label>
                </div>
                @if(__('settings.' . $setting->key . '_desc') !== 'settings.' . $setting->key . '_desc')
                    <p class="setting-description">{{ __('settings.' . $setting->key . '_desc') }}</p>
                @else
                    <p class="setting-description">{{ $setting->description }}</p>
                @endif
            </div>
            <div class="setting-control">
                <div class="form-check form-switch form-switch-lg">
                    <input class="form-check-input" type="checkbox" role="switch"
                           id="{{ $setting->key }}"
                           name="{{ $setting->key }}"
                           value="1"
                           {{ $setting->value == '1' ? 'checked' : '' }}>
                </div>
            </div>
        </div>
    
    @elseif($setting->type === 'integer')
        {{-- Number Input Style --}}
        <div class="setting-input">
            <div class="setting-header">
                <i class="bi bi-123 setting-icon"></i>
                <label for="{{ $setting->key }}" class="setting-title">
                    {{ __('settings.' . $setting->key . '_label', $setting->description) }}
                </label>
            </div>
            @if(__('settings.' . $setting->key . '_desc') !== 'settings.' . $setting->key . '_desc')
                <p class="setting-description">{{ __('settings.' . $setting->key . '_desc') }}</p>
            @else
                <p class="setting-description">{{ $setting->description }}</p>
            @endif
            <div class="input-group input-group-modern">
                <span class="input-group-text"><i class="bi bi-hash"></i></span>
                <input type="number" class="form-control form-control-modern"
                       id="{{ $setting->key }}"
                       name="{{ $setting->key }}"
                       value="{{ $setting->value }}"
                       min="0"
                       placeholder="Enter value">
            </div>
        </div>
    
    @elseif($setting->type === 'text')
        {{-- Textarea Style --}}
        <div class="setting-input">
            <div class="setting-header">
                <i class="bi bi-text-paragraph setting-icon"></i>
                <label for="{{ $setting->key }}" class="setting-title">
                    {{ __('settings.' . $setting->key . '_label', $setting->description) }}
                </label>
            </div>
            @if(__('settings.' . $setting->key . '_desc') !== 'settings.' . $setting->key . '_desc')
                <p class="setting-description">{{ __('settings.' . $setting->key . '_desc') }}</p>
            @else
                <p class="setting-description">{{ $setting->description }}</p>
            @endif
            <textarea class="form-control form-control-modern"
                      id="{{ $setting->key }}"
                      name="{{ $setting->key }}"
                      rows="3"
                      placeholder="Enter text">{{ $setting->value }}</textarea>
        </div>
    
    @else
        {{-- Text Input Style --}}
        <div class="setting-input">
            <div class="setting-header">
                <i class="bi bi-input-cursor-text setting-icon"></i>
                <label for="{{ $setting->key }}" class="setting-title">
                    {{ __('settings.' . $setting->key . '_label', $setting->description) }}
                </label>
            </div>
            @if(__('settings.' . $setting->key . '_desc') !== 'settings.' . $setting->key . '_desc')
                <p class="setting-description">{{ __('settings.' . $setting->key . '_desc') }}</p>
            @else
                <p class="setting-description">{{ $setting->description }}</p>
            @endif
            <div class="input-group input-group-modern">
                <span class="input-group-text"><i class="bi bi-pencil"></i></span>
                <input type="text" class="form-control form-control-modern"
                       id="{{ $setting->key }}"
                       name="{{ $setting->key }}"
                       value="{{ $setting->value }}"
                       placeholder="Enter value">
            </div>
        </div>
    @endif
</div>

<style>
/* Modern Setting Card Styles */
.setting-card {
    background: #ffffff;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 1.25rem;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.setting-card:hover {
    border-color: #667eea;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
    transform: translateY(-2px);
}

.setting-toggle {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1.5rem;
}

.setting-info {
    flex: 1;
}

.setting-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.5rem;
}

.setting-icon {
    font-size: 1.25rem;
    color: #667eea;
    flex-shrink: 0;
}

.setting-title {
    font-size: 1rem;
    font-weight: 600;
    color: #2d3748;
    margin: 0;
    cursor: pointer;
}

.setting-description {
    font-size: 0.875rem;
    color: #718096;
    margin: 0;
    line-height: 1.5;
}

.setting-control {
    flex-shrink: 0;
}

/* Large Toggle Switch */
.form-switch-lg .form-check-input {
    width: 3.5rem;
    height: 1.75rem;
    cursor: pointer;
    border: 2px solid #cbd5e0;
    background-color: #e2e8f0;
    transition: all 0.3s ease;
}

.form-switch-lg .form-check-input:checked {
    background-color: #667eea;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
}

.form-switch-lg .form-check-input:focus {
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.25);
    border-color: #667eea;
}

/* Modern Input Groups */
.input-group-modern {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.input-group-modern .input-group-text {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 0.625rem 1rem;
}

.form-control-modern {
    border: 1px solid #e2e8f0;
    padding: 0.625rem 1rem;
    font-size: 0.9375rem;
    transition: all 0.2s ease;
}

.form-control-modern:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.setting-input .setting-header {
    margin-bottom: 0.75rem;
}

.setting-input .setting-description {
    margin-bottom: 1rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .setting-toggle {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .setting-control {
        width: 100%;
        display: flex;
        justify-content: flex-end;
    }
}
</style>
