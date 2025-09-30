@php
    $currentLocale = app()->getLocale();
    $availableLocales = config('app.available_locales', []);
    $currentLanguage = $availableLocales[$currentLocale] ?? $availableLocales['en'];
@endphp

<div class="dropdown">
    <button class="btn btn-outline-secondary dropdown-toggle d-flex align-items-center" type="button"
            id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <span class="me-2">{{ $currentLanguage['flag'] }}</span>
        <span class="d-none d-md-inline">{{ $currentLanguage['name'] }}</span>
        <span class="d-md-none">{{ strtoupper($currentLocale) }}</span>
    </button>
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
        @foreach($availableLocales as $locale => $info)
            <li>
                <a class="dropdown-item d-flex align-items-center {{ $locale === $currentLocale ? 'active' : '' }}"
                   href="{{ route('language.switch', $locale) }}">
                    <span class="me-2">{{ $info['flag'] }}</span>
                    <span>{{ $info['name'] }}</span>
                    @if($locale === $currentLocale)
                        <i class="bi bi-check-lg ms-auto text-success"></i>
                    @endif
                </a>
            </li>
        @endforeach
    </ul>
</div>

@push('styles')
<style>
    /* RTL support for Arabic */
    @if($currentLocale === 'ar')
        body {
            direction: rtl;
            text-align: right;
        }

        .dropdown-menu-end {
            left: 0 !important;
            right: auto !important;
        }

        /* Reverse Bootstrap flex utilities for RTL */
        .justify-content-between {
            flex-direction: row-reverse;
        }

        /* Adjust margins for RTL */
        .me-2 {
            margin-left: 0.5rem !important;
            margin-right: 0 !important;
        }

        .ms-auto {
            margin-right: auto !important;
            margin-left: 0 !important;
        }
    @endif
</style>
@endpush