<nav class="bg-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo and Primary Navigation -->
            <div class="flex">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="text-2xl font-bold text-indigo-600">
                        {{ config('app.name', 'PFE Platform') }}
                    </a>
                </div>

                <!-- Primary Navigation -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <a href="{{ route('home') }}"
                       class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('home') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 transition duration-150 ease-in-out">
                        {{ __('Home') }}
                    </a>

                    <a href="{{ route('about') }}"
                       class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('about') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 transition duration-150 ease-in-out">
                        {{ __('About') }}
                    </a>

                    <a href="{{ route('courses.index') }}"
                       class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('courses*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 transition duration-150 ease-in-out">
                        {{ __('Courses') }}
                    </a>

                    <a href="{{ route('projects.index') }}"
                       class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('projects*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 transition duration-150 ease-in-out">
                        {{ __('Projects') }}
                    </a>

                    <a href="{{ route('publications.index') }}"
                       class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('publications*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 transition duration-150 ease-in-out">
                        {{ __('Publications') }}
                    </a>

                    <a href="{{ route('blog.index') }}"
                       class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('blog*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 transition duration-150 ease-in-out">
                        {{ __('Blog') }}
                    </a>

                    <a href="{{ route('contact.show') }}"
                       class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('contact*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 transition duration-150 ease-in-out">
                        {{ __('Contact') }}
                    </a>
                </div>
            </div>

            <!-- Authentication Links -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                @guest
                <div class="space-x-4">
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                        {{ __('Login') }}
                    </a>
                </div>
                @else
                <!-- User Menu for Authenticated Users -->
                <div class="relative">
                    <button type="button"
                            class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            onclick="toggleUserMenu()">
                        <img class="h-8 w-8 rounded-full"
                             src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) }}"
                             alt="{{ auth()->user()->name }}">
                        <span class="ml-2 text-gray-700 font-medium">{{ auth()->user()->name }}</span>
                        <i class="ml-1 fas fa-chevron-down text-gray-500"></i>
                    </button>

                    <!-- User Dropdown -->
                    <div id="user-menu-dropdown"
                         class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50">
                        <div class="py-1">
                            <a href="{{ route('pfe.dashboard') }}"
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-tachometer-alt mr-2"></i>
                                {{ __('Dashboard') }}
                            </a>
                            <a href="{{ route('pfe.profile.show') }}"
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user-circle mr-2"></i>
                                {{ __('Profile') }}
                            </a>
                            <div class="border-t border-gray-100"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-2"></i>
                                    {{ __('Logout') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endguest
            </div>

            <!-- Mobile menu button -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button type="button"
                        class="bg-white inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        onclick="toggleMobileMenu()">
                    <span class="sr-only">{{ __('Open main menu') }}</span>
                    <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div id="mobile-menu" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('home') }}"
               class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('home') ? 'border-indigo-500 text-indigo-700 bg-indigo-50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300' }} text-base font-medium">
                {{ __('Home') }}
            </a>
            <a href="{{ route('about') }}"
               class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('about') ? 'border-indigo-500 text-indigo-700 bg-indigo-50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300' }} text-base font-medium">
                {{ __('About') }}
            </a>
            <a href="{{ route('courses.index') }}"
               class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('courses*') ? 'border-indigo-500 text-indigo-700 bg-indigo-50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300' }} text-base font-medium">
                {{ __('Courses') }}
            </a>
            <a href="{{ route('projects.index') }}"
               class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('projects*') ? 'border-indigo-500 text-indigo-700 bg-indigo-50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300' }} text-base font-medium">
                {{ __('Projects') }}
            </a>
            <a href="{{ route('contact.show') }}"
               class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('contact*') ? 'border-indigo-500 text-indigo-700 bg-indigo-50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300' }} text-base font-medium">
                {{ __('Contact') }}
            </a>
        </div>

        @guest
        <div class="pt-4 pb-3 border-t border-gray-200">
            <div class="space-y-1">
                <a href="{{ route('login') }}"
                   class="block pl-3 pr-4 py-2 text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50">
                    {{ __('Login') }}
                </a>
            </div>
        </div>
        @else
        <div class="pt-4 pb-3 border-t border-gray-200">
            <div class="flex items-center px-4">
                <div class="flex-shrink-0">
                    <img class="h-10 w-10 rounded-full"
                         src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) }}"
                         alt="{{ auth()->user()->name }}">
                </div>
                <div class="ml-3">
                    <div class="text-base font-medium text-gray-800">{{ auth()->user()->name }}</div>
                    <div class="text-sm font-medium text-gray-500">{{ auth()->user()->email }}</div>
                </div>
            </div>
            <div class="mt-3 space-y-1">
                <a href="{{ route('pfe.dashboard') }}"
                   class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">
                    {{ __('Dashboard') }}
                </a>
                <a href="{{ route('pfe.profile.show') }}"
                   class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">
                    {{ __('Profile') }}
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="block w-full text-left px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">
                        {{ __('Logout') }}
                    </button>
                </form>
            </div>
        </div>
        @endguest
    </div>
</nav>

@push('scripts')
<script>
function toggleMobileMenu() {
    const menu = document.getElementById('mobile-menu');
    menu.classList.toggle('hidden');
}

function toggleUserMenu() {
    const dropdown = document.getElementById('user-menu-dropdown');
    dropdown.classList.toggle('hidden');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const userMenuDropdown = document.getElementById('user-menu-dropdown');
    if (userMenuDropdown && !event.target.closest('[onclick="toggleUserMenu()"]') && !userMenuDropdown.contains(event.target)) {
        userMenuDropdown.classList.add('hidden');
    }
});
</script>
@endpush