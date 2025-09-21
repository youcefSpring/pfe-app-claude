<div class="h-full flex flex-col">
    <!-- Logo -->
    <div class="flex items-center justify-center h-16 bg-indigo-600">
        <a href="{{ route('pfe.dashboard') }}" class="text-white text-xl font-bold">
            {{ __('PFE Admin') }}
        </a>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
        <!-- Dashboard -->
        <a href="{{ route('pfe.dashboard') }}"
           class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('pfe.dashboard*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }}">
            <i class="fas fa-tachometer-alt mr-3"></i>
            {{ __('Dashboard') }}
        </a>

        @can('manage', App\Models\Subject::class)
        <!-- Subjects -->
        <div class="space-y-1">
            <a href="{{ route('pfe.subjects.index') }}"
               class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('pfe.subjects*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }}">
                <i class="fas fa-book mr-3"></i>
                {{ __('Subjects') }}
            </a>
        </div>
        @endcan

        @can('manage', App\Models\Team::class)
        <!-- Teams -->
        <a href="{{ route('pfe.teams.index') }}"
           class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('pfe.teams*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }}">
            <i class="fas fa-users mr-3"></i>
            {{ __('Teams') }}
        </a>
        @endcan

        @can('manage', App\Models\Project::class)
        <!-- Projects -->
        <a href="{{ route('pfe.projects.index') }}"
           class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('pfe.projects*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }}">
            <i class="fas fa-project-diagram mr-3"></i>
            {{ __('Projects') }}
        </a>
        @endcan

        @hasrole('admin_pfe|chef_master|teacher')
        <!-- Defenses -->
        <a href="{{ route('pfe.defenses.index') }}"
           class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('pfe.defenses*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }}">
            <i class="fas fa-graduation-cap mr-3"></i>
            {{ __('Defenses') }}
        </a>
        @endhasrole

        @hasrole('admin_pfe|chef_master')
        <!-- Administration -->
        <div class="pt-4">
            <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                {{ __('Administration') }}
            </h3>
            <div class="mt-2 space-y-1">
                <a href="{{ route('pfe.admin.users.index') }}"
                   class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('pfe.admin.users*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }}">
                    <i class="fas fa-user-cog mr-3"></i>
                    {{ __('Users') }}
                </a>
                <a href="{{ route('pfe.admin.rooms.index') }}"
                   class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('pfe.admin.rooms*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }}">
                    <i class="fas fa-door-open mr-3"></i>
                    {{ __('Rooms') }}
                </a>
                <a href="{{ route('pfe.admin.conflicts') }}"
                   class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('pfe.admin.conflicts*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }}">
                    <i class="fas fa-exclamation-triangle mr-3"></i>
                    {{ __('Conflicts') }}
                </a>
                @hasrole('admin_pfe')
                <a href="{{ route('pfe.admin.settings') }}"
                   class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('pfe.admin.settings*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }}">
                    <i class="fas fa-cog mr-3"></i>
                    {{ __('Settings') }}
                </a>
                @endhasrole
            </div>
        </div>
        @endhasrole

        @hasrole('admin_pfe|chef_master|teacher')
        <!-- Reports -->
        <div class="pt-4">
            <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                {{ __('Reports') }}
            </h3>
            <div class="mt-2 space-y-1">
                <a href="{{ route('pfe.reports.index') }}"
                   class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('pfe.reports*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }}">
                    <i class="fas fa-chart-bar mr-3"></i>
                    {{ __('Analytics') }}
                </a>
            </div>
        </div>
        @endhasrole
    </nav>

    <!-- User Profile -->
    <div class="p-4 border-t border-gray-200">
        <div class="flex items-center">
            <img class="h-8 w-8 rounded-full"
                 src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) }}"
                 alt="{{ auth()->user()->name }}">
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-700">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
            </div>
        </div>
    </div>
</div>