@extends('layouts.admin')

@section('title', __('Administration Dashboard'))
@section('page-title', __('Administration Dashboard'))

@section('breadcrumbs')
<span class="text-gray-500">{{ __('Home') }} / {{ __('Admin Dashboard') }}</span>
@endsection

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- System Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Subjects -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">{{ __('Total Subjects') }}</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['total_subjects'] ?? 0 }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-book text-blue-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center justify-between">
                <span class="text-green-600 text-sm font-medium">
                    {{ $stats['approved_subjects'] ?? 0 }} {{ __('approved') }}
                </span>
                <span class="text-yellow-600 text-sm font-medium">
                    {{ $stats['pending_subjects'] ?? 0 }} {{ __('pending') }}
                </span>
            </div>
        </div>

        <!-- Active Teams -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">{{ __('Active Teams') }}</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['total_teams'] ?? 0 }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-users text-green-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center justify-between">
                <span class="text-green-600 text-sm font-medium">
                    {{ $stats['validated_teams'] ?? 0 }} {{ __('validated') }}
                </span>
                <span class="text-gray-600 text-sm font-medium">
                    {{ $stats['avg_team_size'] ?? 0 }} {{ __('avg size') }}
                </span>
            </div>
        </div>

        <!-- Running Projects -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">{{ __('Running Projects') }}</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['active_projects'] ?? 0 }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <i class="fas fa-project-diagram text-yellow-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center justify-between">
                <span class="text-yellow-600 text-sm font-medium">
                    {{ $stats['avg_progress'] ?? 0 }}% {{ __('avg progress') }}
                </span>
                <span class="text-green-600 text-sm font-medium">
                    {{ $stats['completed_projects'] ?? 0 }} {{ __('completed') }}
                </span>
            </div>
        </div>

        <!-- Defenses Scheduled -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">{{ __('Defenses Scheduled') }}</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['scheduled_defenses'] ?? 0 }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <i class="fas fa-graduation-cap text-purple-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center justify-between">
                <span class="text-purple-600 text-sm font-medium">
                    {{ $stats['upcoming_defenses'] ?? 0 }} {{ __('this month') }}
                </span>
                <span class="text-green-600 text-sm font-medium">
                    {{ $stats['completed_defenses'] ?? 0 }} {{ __('completed') }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Pending Actions -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Pending Actions') }}</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @if(isset($pendingActions))
                    @foreach($pendingActions as $action)
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-{{ $action['color'] }}-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-{{ $action['icon'] }} text-{{ $action['color'] }}-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $action['title'] }}</p>
                                <p class="text-xs text-gray-500">{{ $action['description'] }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $action['color'] }}-100 text-{{ $action['color'] }}-800">
                                {{ $action['count'] }}
                            </span>
                            <a href="{{ $action['url'] }}"
                               class="text-indigo-600 text-sm hover:text-indigo-500">
                                {{ __('View') }} →
                            </a>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <!-- Default pending actions -->
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-clock text-yellow-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ __('Subject Validations') }}</p>
                                <p class="text-xs text-gray-500">{{ __('Subjects waiting for approval') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                {{ $stats['pending_subjects'] ?? 0 }}
                            </span>
                            <a href="{{ route('pfe.subjects.index') }}?status=pending"
                               class="text-indigo-600 text-sm hover:text-indigo-500">
                                {{ __('View') }} →
                            </a>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-exclamation-triangle text-red-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ __('Team Conflicts') }}</p>
                                <p class="text-xs text-gray-500">{{ __('Subject assignment conflicts to resolve') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                {{ $stats['conflicts'] ?? 0 }}
                            </span>
                            <a href="{{ route('pfe.admin.conflicts') }}"
                               class="text-indigo-600 text-sm hover:text-indigo-500">
                                {{ __('Resolve') }} →
                            </a>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-calendar text-blue-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ __('Defense Scheduling') }}</p>
                                <p class="text-xs text-gray-500">{{ __('Projects ready for defense scheduling') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $stats['ready_for_defense'] ?? 0 }}
                            </span>
                            <a href="{{ route('pfe.defenses.schedule') }}"
                               class="text-indigo-600 text-sm hover:text-indigo-500">
                                {{ __('Schedule') }} →
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Recent System Activity') }}</h3>
            </div>
            <div class="p-6">
                @if(isset($recentActivities) && $recentActivities->count() > 0)
                <div class="space-y-4">
                    @foreach($recentActivities->take(6) as $activity)
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-{{ $activity->icon ?? 'info' }} text-indigo-600 text-sm"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900">{{ $activity->description }}</p>
                            <div class="flex items-center space-x-2 mt-1">
                                <p class="text-xs text-gray-500">{{ $activity->created_at->diffForHumans() }}</p>
                                <span class="text-xs text-gray-400">•</span>
                                <p class="text-xs text-gray-500">{{ $activity->user->name ?? 'System' }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <i class="fas fa-history text-gray-400 text-4xl mb-4"></i>
                    <p class="text-gray-500">{{ __('No recent activities') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Statistics Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Project Progress Chart -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Project Progress Overview') }}</h3>
            </div>
            <div class="p-6">
                <canvas id="projectProgressChart" width="400" height="300"></canvas>
            </div>
        </div>

        <!-- Defense Timeline -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Defense Schedule') }}</h3>
            </div>
            <div class="p-6">
                <canvas id="defenseTimelineChart" width="400" height="300"></canvas>
            </div>
        </div>
    </div>

    <!-- System Health -->
    @hasrole('admin_pfe')
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">{{ __('System Health & Resources') }}</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Database -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-database text-green-600 text-2xl"></i>
                    </div>
                    <h4 class="text-sm font-medium text-gray-900">{{ __('Database') }}</h4>
                    <p class="text-xs text-green-600 mt-1">{{ __('Connected') }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $stats['db_size'] ?? '2.3 GB' }}</p>
                </div>

                <!-- Storage -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-hdd text-blue-600 text-2xl"></i>
                    </div>
                    <h4 class="text-sm font-medium text-gray-900">{{ __('Storage') }}</h4>
                    <p class="text-xs text-blue-600 mt-1">{{ $stats['storage_used'] ?? '75%' }} {{ __('used') }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $stats['storage_size'] ?? '45 GB' }} {{ __('total') }}</p>
                </div>

                <!-- Users Online -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-users text-yellow-600 text-2xl"></i>
                    </div>
                    <h4 class="text-sm font-medium text-gray-900">{{ __('Active Users') }}</h4>
                    <p class="text-xs text-yellow-600 mt-1">{{ $stats['online_users'] ?? 24 }} {{ __('online') }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $stats['total_users'] ?? 456 }} {{ __('total') }}</p>
                </div>

                <!-- System Load -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-server text-purple-600 text-2xl"></i>
                    </div>
                    <h4 class="text-sm font-medium text-gray-900">{{ __('System Load') }}</h4>
                    <p class="text-xs text-purple-600 mt-1">{{ $stats['cpu_usage'] ?? '45%' }} {{ __('CPU') }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $stats['memory_usage'] ?? '68%' }} {{ __('Memory') }}</p>
                </div>
            </div>
        </div>
    </div>
    @endhasrole
</div>
@endsection

@push('scripts')
<script>
// Project Progress Chart
const progressCtx = document.getElementById('projectProgressChart').getContext('2d');
const progressChart = new Chart(progressCtx, {
    type: 'doughnut',
    data: {
        labels: ['{{ __("Completed") }}', '{{ __("In Progress") }}', '{{ __("Not Started") }}'],
        datasets: [{
            data: [{{ $stats['completed_projects'] ?? 0 }}, {{ $stats['active_projects'] ?? 0 }}, {{ $stats['pending_projects'] ?? 0 }}],
            backgroundColor: ['#10B981', '#F59E0B', '#EF4444']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

// Defense Timeline Chart
const defenseCtx = document.getElementById('defenseTimelineChart').getContext('2d');
const defenseChart = new Chart(defenseCtx, {
    type: 'line',
    data: {
        labels: @json($stats['defense_timeline_labels'] ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']),
        datasets: [{
            label: '{{ __("Scheduled Defenses") }}',
            data: @json($stats['defense_timeline_data'] ?? [5, 8, 12, 15, 20, 18]),
            borderColor: '#8B5CF6',
            backgroundColor: 'rgba(139, 92, 246, 0.1)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
@endpush