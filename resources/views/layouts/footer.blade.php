<footer class="bg-white border-t border-gray-200 mt-auto">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <div class="text-center md:text-left">
                <p class="text-sm text-gray-600">
                    &copy; {{ date('Y') }} {{ config('app.name', 'PFE Platform') }}. {{ __('All rights reserved.') }}
                </p>
            </div>

            <div class="mt-4 md:mt-0 flex space-x-6">
                <a href="{{ route('about') }}" class="text-sm text-gray-600 hover:text-gray-900">
                    {{ __('About') }}
                </a>
                <a href="{{ route('contact.show') }}" class="text-sm text-gray-600 hover:text-gray-900">
                    {{ __('Contact') }}
                </a>
                <a href="#" class="text-sm text-gray-600 hover:text-gray-900">
                    {{ __('Privacy Policy') }}
                </a>
                <a href="#" class="text-sm text-gray-600 hover:text-gray-900">
                    {{ __('Terms of Service') }}
                </a>
            </div>
        </div>
    </div>
</footer>