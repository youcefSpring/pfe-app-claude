<div class="flex items-center justify-between px-6 py-4">
    <!-- Page Title -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900">
            @yield('page-title', __('Dashboard'))
        </h1>
        @hasSection('breadcrumbs')
        <nav class="text-sm text-gray-500 mt-1">
            @yield('breadcrumbs')
        </nav>
        @endif
    </div>

    <!-- Header Actions -->
    <div class="flex items-center space-x-4">
        <!-- Notifications -->
        <div class="relative">
            <button type="button"
                    class="p-2 text-gray-500 hover:text-gray-700 focus:outline-none focus:text-gray-700"
                    onclick="toggleNotifications()">
                <i class="fas fa-bell text-lg"></i>
                @if(auth()->user()->unreadNotifications->count() > 0)
                <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
                    {{ auth()->user()->unreadNotifications->count() }}
                </span>
                @endif
            </button>

            <!-- Notifications Dropdown -->
            <div id="notifications-dropdown"
                 class="hidden absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50">
                <div class="py-1">
                    <div class="px-4 py-2 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-medium text-gray-900">{{ __('Notifications') }}</h3>
                            @if(auth()->user()->unreadNotifications->count() > 0)
                            <a href="{{ route('pfe.notifications.read-all') }}"
                               class="text-xs text-indigo-600 hover:text-indigo-500"
                               onclick="event.preventDefault(); document.getElementById('mark-all-read-form').submit();">
                                {{ __('Mark all as read') }}
                            </a>
                            <form id="mark-all-read-form" action="{{ route('pfe.notifications.read-all') }}" method="POST" class="hidden">
                                @csrf
                            </form>
                            @endif
                        </div>
                    </div>
                    <div class="max-h-64 overflow-y-auto">
                        @forelse(auth()->user()->notifications->take(5) as $notification)
                        <div class="px-4 py-3 hover:bg-gray-50 {{ $notification->read_at ? '' : 'bg-blue-50' }}">
                            <p class="text-sm text-gray-900">{{ $notification->data['message'] ?? $notification->type }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                        @empty
                        <div class="px-4 py-3 text-sm text-gray-500 text-center">
                            {{ __('No notifications') }}
                        </div>
                        @endforelse
                    </div>
                    @if(auth()->user()->notifications->count() > 0)
                    <div class="border-t border-gray-200 px-4 py-2">
                        <a href="{{ route('pfe.notifications.index') }}"
                           class="block text-sm text-indigo-600 hover:text-indigo-500 text-center">
                            {{ __('View all notifications') }}
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Search -->
        <div class="relative">
            <form action="{{ route('pfe.search') }}" method="GET" class="relative">
                <input type="text"
                       name="q"
                       value="{{ request('q') }}"
                       placeholder="{{ __('Search...') }}"
                       class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
            </form>
        </div>

        <!-- User Menu -->
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
                    <a href="{{ route('pfe.profile.show') }}"
                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-user-circle mr-2"></i>
                        {{ __('Profile') }}
                    </a>
                    <a href="{{ route('pfe.profile.edit') }}"
                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-cog mr-2"></i>
                        {{ __('Settings') }}
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
    </div>
</div>

@push('scripts')
<script>
function toggleNotifications() {
    const dropdown = document.getElementById('notifications-dropdown');
    dropdown.classList.toggle('hidden');
}

function toggleUserMenu() {
    const dropdown = document.getElementById('user-menu-dropdown');
    dropdown.classList.toggle('hidden');
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    const notificationsDropdown = document.getElementById('notifications-dropdown');
    const userMenuDropdown = document.getElementById('user-menu-dropdown');

    if (!event.target.closest('[onclick="toggleNotifications()"]') && !notificationsDropdown.contains(event.target)) {
        notificationsDropdown.classList.add('hidden');
    }

    if (!event.target.closest('[onclick="toggleUserMenu()"]') && !userMenuDropdown.contains(event.target)) {
        userMenuDropdown.classList.add('hidden');
    }
});
</script>
@endpush