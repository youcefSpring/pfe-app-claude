<footer class="bg-dark text-white">
    <div class="container py-5">
        <div class="row g-4">
            <!-- Company Info -->
            <div class="col-lg-4">
                <h3 class="h4 fw-bold mb-3">{{ config('app.name', 'PFE Platform') }}</h3>
                <p class="text-light mb-4">
                    {{ __('A comprehensive platform for managing final year projects, facilitating collaboration between students, teachers, and administrators.') }}
                </p>
                <div class="d-flex gap-3">
                    <!-- Social Media Links -->
                    <a href="#" class="text-light">
                        <i class="fab fa-facebook-f fs-5"></i>
                    </a>
                    <a href="#" class="text-light">
                        <i class="fab fa-twitter fs-5"></i>
                    </a>
                    <a href="#" class="text-light">
                        <i class="fab fa-linkedin-in fs-5"></i>
                    </a>
                    <a href="#" class="text-light">
                        <i class="fab fa-github fs-5"></i>
                    </a>
                </div>
            </div>

            <!-- Platform -->
            <div class="col-md-6 col-lg-2">
                <h5 class="text-uppercase fw-semibold mb-3">{{ __('Platform') }}</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="{{ route('home') }}" class="text-light text-decoration-none">
                            {{ __('Home') }}
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('about') }}" class="text-light text-decoration-none">
                            {{ __('About') }}
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Resources -->
            <div class="col-md-6 col-lg-2">
                <h5 class="text-uppercase fw-semibold mb-3">{{ __('Resources') }}</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="#" class="text-light text-decoration-none">
                            {{ __('Documentation') }}
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="text-light text-decoration-none">
                            {{ __('Help Center') }}
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Support -->
            <div class="col-md-6 col-lg-2">
                <h5 class="text-uppercase fw-semibold mb-3">{{ __('Support') }}</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="#" class="text-light text-decoration-none">
                            {{ __('FAQ') }}
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="text-light text-decoration-none">
                            {{ __('Support Center') }}
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Legal -->
            <div class="col-md-6 col-lg-2">
                <h5 class="text-uppercase fw-semibold mb-3">{{ __('Legal') }}</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="#" class="text-light text-decoration-none">
                            {{ __('Privacy Policy') }}
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="text-light text-decoration-none">
                            {{ __('Terms of Service') }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Bottom Section -->
        <hr class="my-4 border-secondary">
        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="mb-0 text-light">
                    &copy; {{ date('Y') }} {{ config('app.name', 'PFE Platform') }}. {{ __('All rights reserved.') }}
                </p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <span class="text-light me-3">{{ __('Language:') }}</span>
                <select class="form-select form-select-sm d-inline-block w-auto bg-dark text-white border-secondary"
                        onchange="changeLanguage(this.value)">
                    <option value="en" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>English</option>
                    <option value="fr" {{ app()->getLocale() == 'fr' ? 'selected' : '' }}>Français</option>
                    <option value="ar" {{ app()->getLocale() == 'ar' ? 'selected' : '' }}>العربية</option>
                </select>
            </div>
        </div>
    </div>
</footer>

@push('scripts')
<script>
function changeLanguage(locale) {
    // You can implement language switching logic here
    // For example, redirect to a route that changes the locale
    window.location.href = '{{ url("/") }}/change-language/' + locale;
}
</script>
@endpush