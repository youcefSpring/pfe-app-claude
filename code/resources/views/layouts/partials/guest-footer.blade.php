<footer class="bg-gray-900 text-white">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8">
        <div class="xl:grid xl:grid-cols-3 xl:gap-8">
            <!-- Company Info -->
            <div class="space-y-8 xl:col-span-1">
                <div>
                    <h3 class="text-2xl font-bold">{{ config('app.name', 'PFE Platform') }}</h3>
                    <p class="mt-4 text-gray-300 max-w-md">
                        {{ __('A comprehensive platform for managing final year projects, facilitating collaboration between students, teachers, and administrators.') }}
                    </p>
                </div>
                <div class="flex space-x-6">
                    <!-- Social Media Links -->
                    <a href="#" class="text-gray-400 hover:text-gray-300">
                        <span class="sr-only">Facebook</span>
                        <i class="fab fa-facebook-f text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-gray-300">
                        <span class="sr-only">Twitter</span>
                        <i class="fab fa-twitter text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-gray-300">
                        <span class="sr-only">LinkedIn</span>
                        <i class="fab fa-linkedin-in text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-gray-300">
                        <span class="sr-only">GitHub</span>
                        <i class="fab fa-github text-xl"></i>
                    </a>
                </div>
            </div>

            <!-- Links -->
            <div class="mt-12 grid grid-cols-2 gap-8 xl:mt-0 xl:col-span-2">
                <div class="md:grid md:grid-cols-2 md:gap-8">
                    <!-- Platform -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">
                            {{ __('Platform') }}
                        </h3>
                        <ul class="mt-4 space-y-4">
                            <li>
                                <a href="{{ route('home') }}" class="text-base text-gray-300 hover:text-white">
                                    {{ __('Home') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('about') }}" class="text-base text-gray-300 hover:text-white">
                                    {{ __('About') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('courses.index') }}" class="text-base text-gray-300 hover:text-white">
                                    {{ __('Courses') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('projects.index') }}" class="text-base text-gray-300 hover:text-white">
                                    {{ __('Projects') }}
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Resources -->
                    <div class="mt-12 md:mt-0">
                        <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">
                            {{ __('Resources') }}
                        </h3>
                        <ul class="mt-4 space-y-4">
                            <li>
                                <a href="{{ route('publications.index') }}" class="text-base text-gray-300 hover:text-white">
                                    {{ __('Publications') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('blog.index') }}" class="text-base text-gray-300 hover:text-white">
                                    {{ __('Blog') }}
                                </a>
                            </li>
                            <li>
                                <a href="#" class="text-base text-gray-300 hover:text-white">
                                    {{ __('Documentation') }}
                                </a>
                            </li>
                            <li>
                                <a href="#" class="text-base text-gray-300 hover:text-white">
                                    {{ __('Help Center') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="md:grid md:grid-cols-2 md:gap-8">
                    <!-- Support -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">
                            {{ __('Support') }}
                        </h3>
                        <ul class="mt-4 space-y-4">
                            <li>
                                <a href="{{ route('contact.show') }}" class="text-base text-gray-300 hover:text-white">
                                    {{ __('Contact Us') }}
                                </a>
                            </li>
                            <li>
                                <a href="#" class="text-base text-gray-300 hover:text-white">
                                    {{ __('FAQ') }}
                                </a>
                            </li>
                            <li>
                                <a href="#" class="text-base text-gray-300 hover:text-white">
                                    {{ __('Support Center') }}
                                </a>
                            </li>
                            <li>
                                <a href="#" class="text-base text-gray-300 hover:text-white">
                                    {{ __('Status') }}
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Legal -->
                    <div class="mt-12 md:mt-0">
                        <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">
                            {{ __('Legal') }}
                        </h3>
                        <ul class="mt-4 space-y-4">
                            <li>
                                <a href="#" class="text-base text-gray-300 hover:text-white">
                                    {{ __('Privacy Policy') }}
                                </a>
                            </li>
                            <li>
                                <a href="#" class="text-base text-gray-300 hover:text-white">
                                    {{ __('Terms of Service') }}
                                </a>
                            </li>
                            <li>
                                <a href="#" class="text-base text-gray-300 hover:text-white">
                                    {{ __('Cookie Policy') }}
                                </a>
                            </li>
                            <li>
                                <a href="#" class="text-base text-gray-300 hover:text-white">
                                    {{ __('Accessibility') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Section -->
        <div class="mt-12 border-t border-gray-700 pt-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-base text-gray-400">
                    &copy; {{ date('Y') }} {{ config('app.name', 'PFE Platform') }}. {{ __('All rights reserved.') }}
                </p>

                <!-- Language Selector -->
                <div class="mt-4 md:mt-0">
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-400">{{ __('Language:') }}</span>
                        <div class="relative">
                            <select class="bg-gray-800 border border-gray-600 text-white text-sm rounded-md focus:ring-blue-500 focus:border-blue-500 px-3 py-1"
                                    onchange="changeLanguage(this.value)">
                                <option value="en" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>English</option>
                                <option value="fr" {{ app()->getLocale() == 'fr' ? 'selected' : '' }}>Français</option>
                                <option value="ar" {{ app()->getLocale() == 'ar' ? 'selected' : '' }}>العربية</option>
                            </select>
                        </div>
                    </div>
                </div>
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