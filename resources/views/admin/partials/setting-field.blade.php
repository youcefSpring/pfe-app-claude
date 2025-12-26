{{-- Modern Setting Field Component --}}
<div class="setting-card">
    @if($setting->type === 'boolean')
        {{-- Toggle Switch Style --}}
        <div class="setting-toggle">
            <div class="setting-info">
                <div class="setting-header">
                    <i class="bi bi-toggle-on setting-icon"></i>
                    <label class="setting-title" for="{{ $setting->key }}">
                        @if(__('settings.' . $setting->key . '_label') !== 'settings.' . $setting->key . '_label')
                            {{ __('settings.' . $setting->key . '_label') }}
                        @else
                            {{ $setting->description }}
                        @endif
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
                    @if(__('settings.' . $setting->key . '_label') !== 'settings.' . $setting->key . '_label')
                        {{ __('settings.' . $setting->key . '_label') }}
                    @else
                        {{ $setting->description }}
                    @endif
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
                    @if(__('settings.' . $setting->key . '_label') !== 'settings.' . $setting->key . '_label')
                        {{ __('settings.' . $setting->key . '_label') }}
                    @else
                        {{ $setting->description }}
                    @endif
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
                    @if(__('settings.' . $setting->key . '_label') !== 'settings.' . $setting->key . '_label')
                        {{ __('settings.' . $setting->key . '_label') }}
                    @else
                        {{ $setting->description }}
                    @endif
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
/* Modern Setting Card Styles - Enhanced */
.setting-card {
    background: #ffffff;
    border: 1px solid #dee2e6;
    border-radius: 10px;
    padding: 1.5rem;
    margin-bottom: 1.25rem;
    transition: all 0.25s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.04);
}

.setting-card:hover {
    border-color: #a0aec0;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    transform: translateY(-1px);
}

.setting-toggle {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1.5rem;
}

.setting-info {
    flex: 1;
    min-width: 0;
}

.setting-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.5rem;
}

.setting-icon {
    font-size: 1.15rem;
    color: #6366f1;
    flex-shrink: 0;
    opacity: 0.9;
}

.setting-title {
    font-size: 0.95rem;
    font-weight: 600;
    color: #1a202c;
    margin: 0;
    cursor: pointer;
    line-height: 1.4;
}

.setting-description {
    font-size: 0.8125rem;
    color: #64748b;
    margin: 0;
    line-height: 1.5;
    padding-left: 2rem;
}

.setting-control {
    flex-shrink: 0;
    padding-top: 0.125rem;
}

/* Large Toggle Switch - Refined */
.form-switch-lg .form-check-input {
    width: 3.25rem;
    height: 1.65rem;
    cursor: pointer;
    border: 2px solid #cbd5e0;
    background-color: #e2e8f0;
    transition: all 0.25s ease;
    box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
}

.form-switch-lg .form-check-input:checked {
    background-color: #6366f1;
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
}

.form-switch-lg .form-check-input:focus {
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.2);
    border-color: #6366f1;
}

.form-switch-lg .form-check-input:hover:not(:checked) {
    border-color: #a0aec0;
    background-color: #cbd5e0;
}

/* Modern Input Groups - Refined */
.input-group-modern {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}

.input-group-modern .input-group-text {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    color: white;
    border: none;
    padding: 0.625rem 0.875rem;
    font-size: 0.875rem;
}

.form-control-modern {
    border: 1px solid #e2e8f0;
    padding: 0.625rem 0.875rem;
    font-size: 0.875rem;
    transition: all 0.2s ease;
    background-color: #ffffff;
}

.form-control-modern:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    background-color: #ffffff;
}

.form-control-modern::placeholder {
    color: #94a3b8;
    font-size: 0.8125rem;
}

.setting-input .setting-header {
    margin-bottom: 0.625rem;
}

.setting-input .setting-description {
    margin-bottom: 0.875rem;
    padding-left: 2rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .setting-toggle {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .setting-control {
        width: 100%;
        display: flex;
        justify-content: flex-end;
    }
    
    .setting-card {
        padding: 1.25rem;
    }
    
    .setting-description {
        padding-left: 0;
        margin-top: 0.5rem;
    }
}

/* Improved spacing for 2-column layout */
@media (min-width: 768px) {
    .col-md-6 .setting-card {
        height: calc(100% - 1.25rem);
    }
}
</style>
