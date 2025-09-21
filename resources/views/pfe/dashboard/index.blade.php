@extends('layouts.admin')

@section('title', __('Dashboard'))
@section('page-title', __('Dashboard'))

@section('breadcrumbs')
<span class="text-gray-500">{{ __('Home') }}</span>
@endsection

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Welcome Section -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
            <h1 class="text-3xl font-bold mb-2">
                {{ __('Welcome back, :name!', ['name' => auth()->user()->first_name]) }}
            </h1>
            <p class="text-indigo-100">
                {{ __('Here\'s an overview of the PFE platform activities.') }}
            </p>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Subjects -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">{{ __('Total Subjects') }}</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['subjects_count'] ?? 0 }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-book text-blue-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-green-600 text-sm font-medium">
                    <i class="fas fa-arrow-up"></i>
                    {{ $stats['subjects_growth'] ?? '0%' }}
                </span>
                <span class="text-gray-500 text-sm ml-2">{{ __('from last month') }}</span>
            </div>
        </div>

        <!-- Active Teams -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">{{ __('Active Teams') }}</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['teams_count'] ?? 0 }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-users text-green-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-green-600 text-sm font-medium">
                    <i class="fas fa-arrow-up"></i>
                    {{ $stats['teams_growth'] ?? '0%' }}
                </span>
                <span class="text-gray-500 text-sm ml-2">{{ __('from last month') }}</span>
            </div>
        </div>

        <!-- Ongoing Projects -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">{{ __('Ongoing Projects') }}</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['projects_count'] ?? 0 }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <i class="fas fa-project-diagram text-yellow-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-yellow-600 text-sm font-medium">
                    <i class="fas fa-minus"></i>
                    {{ $stats['projects_status'] ?? __('Stable') }}
                </span>
                <span class="text-gray-500 text-sm ml-2">{{ __('progress rate') }}</span>
            </div>
        </div>

        <!-- Scheduled Defenses -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">{{ __('Scheduled Defenses') }}</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['defenses_count'] ?? 0 }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <i class="fas fa-graduation-cap text-purple-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-purple-600 text-sm font-medium">
                    <i class="fas fa-calendar"></i>
                    {{ $stats['next_defense'] ?? __('No upcoming') }}
                </span>
                <span class="text-gray-500 text-sm ml-2">{{ __('next defense') }}</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Activities -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Recent Activities') }}</h3>
            </div>
            <div class="p-6">
                @if(isset($activities) && $activities->count() > 0)
                <div class="space-y-4">
                    @foreach($activities->take(5) as $activity)
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-{{ $activity->icon ?? 'info' }} text-indigo-600 text-sm"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900">{{ $activity->description }}</p>
                            <p class="text-xs text-gray-500">{{ $activity->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-6">
                    <a href="#" class="text-indigo-600 text-sm font-medium hover:text-indigo-500">
                        {{ __('View all activities') }} →
                    </a>
                </div>
                @else
                <div class="text-center py-8">
                    <i class="fas fa-inbox text-gray-400 text-4xl mb-4"></i>
                    <p class="text-gray-500">{{ __('No recent activities') }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Quick Actions') }}</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 gap-4">
                    @can('create', App\Models\Subject::class)
                    <a href="{{ route('pfe.subjects.create') }}"
                       class="flex flex-col items-center justify-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-indigo-500 hover:bg-indigo-50 transition-colors">
                        <i class="fas fa-plus-circle text-indigo-600 text-2xl mb-2"></i>
                        <span class="text-sm font-medium text-gray-700">{{ __('Add Subject') }}</span>
                    </a>
                    @endcan

                    @can('create', App\Models\Team::class)
                    <a href="{{ route('pfe.teams.create') }}"
                       class="flex flex-col items-center justify-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-green-500 hover:bg-green-50 transition-colors">
                        <i class="fas fa-users-plus text-green-600 text-2xl mb-2"></i>
                        <span class="text-sm font-medium text-gray-700">{{ __('Create Team') }}</span>
                    </a>
                    @endcan

                    @hasrole('admin_pfe')
                    <a href="{{ route('pfe.defenses.schedule') }}"
                       class="flex flex-col items-center justify-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-purple-500 hover:bg-purple-50 transition-colors">
                        <i class="fas fa-calendar-plus text-purple-600 text-2xl mb-2"></i>
                        <span class="text-sm font-medium text-gray-700">{{ __('Schedule Defense') }}</span>
                    </a>
                    @endhasrole

                    <a href="{{ route('pfe.reports.index') }}"
                       class="flex flex-col items-center justify-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-yellow-500 hover:bg-yellow-50 transition-colors">
                        <i class="fas fa-chart-bar text-yellow-600 text-2xl mb-2"></i>
                        <span class="text-sm font-medium text-gray-700">{{ __('View Reports') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- System Status -->
    @hasrole('admin_pfe')
    <div class="mt-8">
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">{{ __('System Status') }}</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-server text-green-600 text-2xl"></i>
                        </div>
                        <h4 class="text-sm font-medium text-gray-900">{{ __('Server Status') }}</h4>
                        <p class="text-xs text-green-600 mt-1">{{ __('Operational') }}</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-database text-blue-600 text-2xl"></i>
                        </div>
                        <h4 class="text-sm font-medium text-gray-900">{{ __('Database') }}</h4>
                        <p class="text-xs text-blue-600 mt-1">{{ __('Connected') }}</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-cloud text-yellow-600 text-2xl"></i>
                        </div>
                        <h4 class="text-sm font-medium text-gray-900">{{ __('Storage') }}</h4>
                        <p class="text-xs text-yellow-600 mt-1">{{ $stats['storage_usage'] ?? '75%' }} {{ __('used') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endhasrole
</div>
@endsection

@push('scripts')
<script>
// Auto-refresh stats every 5 minutes
setInterval(function() {
    // You can implement AJAX refresh for real-time stats here
}, 300000);
</script>
@endpush