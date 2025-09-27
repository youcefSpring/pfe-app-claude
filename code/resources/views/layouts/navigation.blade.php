<nav class="bg-white shadow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo and Primary Navigation -->
            <div class="flex">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="text-xl font-bold text-gray-900">
                        {{ config('app.name', 'PFE Platform') }}
                    </a>
                </div>

                @auth
                <!-- Primary Navigation -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <!-- Dashboard Link -->
                    @hasrole('student')
                    <a href="{{ route('pfe.dashboard.student') }}"
                       class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('pfe.dashboard*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                        {{ __('Dashboard') }}
                    </a>
                    @endhasrole

                    @hasrole('teacher')
                    <a href="{{ route('pfe.dashboard.teacher') }}"
                       class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('pfe.dashboard*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                        {{ __('Dashboard') }}
                    </a>
                    @endhasrole

                    @hasrole('admin_pfe|chef_master')
                    <a href="{{ route('pfe.dashboard.admin') }}"
                       class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('pfe.dashboard*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                        {{ __('Dashboard') }}
                    </a>
                    @endhasrole

                    @hasrole('student')
                    <!-- Student Navigation -->
                    <a href="{{ route('pfe.subjects.available') }}"
                       class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('pfe.subjects.available') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5">
                        {{ __('Available Subjects') }}
                    </a>
                    <a href="{{ route('pfe.teams.my-team') }}"
                       class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('pfe.teams.my-team') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5">
                        {{ __('My Team') }}
                    </a>
                    <a href="{{ route('pfe.projects.my-project') }}"
                       class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('pfe.projects.my-project') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5">
                        {{ __('My Project') }}
                    </a>
                    @endhasrole

                    @hasrole('teacher|admin_pfe|chef_master')
                    <!-- Teacher/Admin Navigation -->
                    <a href="{{ route('pfe.subjects.index') }}"
                       class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('pfe.subjects*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5">
                        {{ __('Subjects') }}
                    </a>
                    <a href="{{ route('pfe.teams.index') }}"
                       class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('pfe.teams*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5">
                        {{ __('Teams') }}
                    </a>
                    <a href="{{ route('pfe.projects.index') }}"
                       class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('pfe.projects*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5">
                        {{ __('Projects') }}
                    </a>
                    @endhasrole
                </div>
                @endauth
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                @auth
                <!-- Notifications -->
                <div class="relative mr-4">
                    <a href="{{ route('pfe.notifications.index') }}"
                       class="p-2 text-gray-500 hover:text-gray-700 focus:outline-none focus:text-gray-700">
                        <i class="fas fa-bell"></i>
                        @if(auth()->user()->unreadNotifications->count() > 0)
                        <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
                            {{ auth()->user()->unreadNotifications->count() }}
                        </span>
                        @endif
                    </a>
                </div>

                <!-- User Dropdown -->
                <div class="relative">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                <div>{{ Auth::user()->name }}</div>
                                <div class="ml-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('pfe.profile.show')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('pfe.profile.edit')">
                                {{ __('Settings') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
                @else
                <!-- Guest Navigation -->
                <div class="space-x-4">
                    <a href="{{ route('login') }}" class="text-gray-500 hover:text-gray-700">{{ __('Login') }}</a>
                </div>
                @endauth
            </div>

            <!-- Hamburger Menu -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                        onclick="toggleMobileMenu()">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div id="mobile-menu" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @auth
            @hasrole('student')
            <x-responsive-nav-link :href="route('pfe.dashboard.student')" :active="request()->routeIs('pfe.dashboard*')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            @endhasrole

            @hasrole('teacher')
            <x-responsive-nav-link :href="route('pfe.dashboard.teacher')" :active="request()->routeIs('pfe.dashboard*')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            @endhasrole

            @hasrole('admin_pfe|chef_master')
            <x-responsive-nav-link :href="route('pfe.dashboard.admin')" :active="request()->routeIs('pfe.dashboard*')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            @endhasrole
            @endauth
        </div>

        @auth
        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('pfe.profile.show')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
        @endauth
    </div>
</nav>

@push('scripts')
<script>
function toggleMobileMenu() {
    const menu = document.getElementById('mobile-menu');
    menu.classList.toggle('hidden');
}
</script>
@endpush