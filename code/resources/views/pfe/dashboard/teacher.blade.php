@extends('layouts.app')

@section('title', __('Teacher Dashboard'))

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Welcome Section -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-green-500 to-teal-600 rounded-lg shadow-lg p-6 text-white">
            <h1 class="text-3xl font-bold mb-2">
                {{ __('Welcome, Professor :name!', ['name' => auth()->user()->first_name]) }}
            </h1>
            <p class="text-green-100">
                {{ __('Manage your subjects and supervise student projects.') }}
            </p>
        </div>
    </div>

    <!-- Overview Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- My Subjects -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">{{ __('My Subjects') }}</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['my_subjects'] ?? 0 }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-book text-blue-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-sm text-gray-500">
                    {{ $stats['approved_subjects'] ?? 0 }} {{ __('approved') }},
                    {{ $stats['pending_subjects'] ?? 0 }} {{ __('pending') }}
                </span>
            </div>
        </div>

        <!-- Supervised Projects -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">{{ __('Supervised Projects') }}</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['supervised_projects'] ?? 0 }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-project-diagram text-green-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-sm text-gray-500">
                    {{ $stats['active_projects'] ?? 0 }} {{ __('active') }},
                    {{ $stats['completed_projects'] ?? 0 }} {{ __('completed') }}
                </span>
            </div>
        </div>

        <!-- Pending Reviews -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">{{ __('Pending Reviews') }}</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['pending_reviews'] ?? 0 }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <i class="fas fa-clipboard-check text-yellow-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-yellow-600 text-sm font-medium">
                    {{ __('Requires attention') }}
                </span>
            </div>
        </div>

        <!-- Upcoming Defenses -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">{{ __('Upcoming Defenses') }}</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['upcoming_defenses'] ?? 0 }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <i class="fas fa-graduation-cap text-purple-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-sm text-gray-500">
                    {{ $stats['next_defense_date'] ?? __('No upcoming') }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- My Subjects -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">{{ __('My Subjects') }}</h3>
                <a href="{{ route('pfe.subjects.create') }}"
                   class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    <i class="fas fa-plus mr-2"></i>
                    {{ __('Add Subject') }}
                </a>
            </div>
            <div class="p-6">
                @if(isset($mySubjects) && $mySubjects->count() > 0)
                <div class="space-y-4">
                    @foreach($mySubjects->take(5) as $subject)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="text-sm font-medium text-gray-900">{{ $subject->title }}</h4>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $subject->status === 'approved' ? 'bg-green-100 text-green-800' :
                                   ($subject->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ __(ucfirst($subject->status)) }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mb-2">{{ Str::limit($subject->description, 100) }}</p>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-400">{{ $subject->created_at->diffForHumans() }}</span>
                            <a href="{{ route('pfe.subjects.show', $subject) }}"
                               class="text-indigo-600 text-xs font-medium hover:text-indigo-500">
                                {{ __('View') }} →
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
                @if($mySubjects->count() > 5)
                <div class="mt-4">
                    <a href="{{ route('pfe.subjects.index') }}?filter=mine"
                       class="text-indigo-600 text-sm font-medium hover:text-indigo-500">
                        {{ __('View all my subjects') }} →
                    </a>
                </div>
                @endif
                @else
                <div class="text-center py-8">
                    <i class="fas fa-book text-gray-400 text-4xl mb-4"></i>
                    <h4 class="text-lg font-medium text-gray-900 mb-2">{{ __('No subjects yet') }}</h4>
                    <p class="text-gray-500 mb-4">{{ __('Create your first subject to get started.') }}</p>
                    <a href="{{ route('pfe.subjects.create') }}"
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        <i class="fas fa-plus mr-2"></i>
                        {{ __('Create Subject') }}
                    </a>
                </div>
                @endif
            </div>
        </div>

        <!-- Supervised Projects -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Supervised Projects') }}</h3>
            </div>
            <div class="p-6">
                @if(isset($supervisedProjects) && $supervisedProjects->count() > 0)
                <div class="space-y-4">
                    @foreach($supervisedProjects->take(5) as $project)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="text-sm font-medium text-gray-900">{{ $project->subject->title }}</h4>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $project->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ __(ucfirst($project->status)) }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mb-2">{{ __('Team: :name', ['name' => $project->team->name]) }}</p>
                        <div class="flex justify-between items-center">
                            <div class="flex items-center space-x-4">
                                <span class="text-xs text-gray-400">{{ $project->start_date->format('M d, Y') }}</span>
                                @if($project->deliverables->where('status', 'pending')->count() > 0)
                                <span class="text-xs text-yellow-600 font-medium">
                                    {{ $project->deliverables->where('status', 'pending')->count() }} {{ __('pending reviews') }}
                                </span>
                                @endif
                            </div>
                            <a href="{{ route('pfe.projects.show', $project) }}"
                               class="text-indigo-600 text-xs font-medium hover:text-indigo-500">
                                {{ __('View') }} →
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
                @if($supervisedProjects->count() > 5)
                <div class="mt-4">
                    <a href="{{ route('pfe.projects.index') }}?filter=supervised"
                       class="text-indigo-600 text-sm font-medium hover:text-indigo-500">
                        {{ __('View all supervised projects') }} →
                    </a>
                </div>
                @endif
                @else
                <div class="text-center py-8">
                    <i class="fas fa-project-diagram text-gray-400 text-4xl mb-4"></i>
                    <h4 class="text-lg font-medium text-gray-900 mb-2">{{ __('No projects assigned yet') }}</h4>
                    <p class="text-gray-500">{{ __('Projects will appear here once teams are assigned to your subjects.') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Pending Reviews -->
    @if(isset($pendingDeliverables) && $pendingDeliverables->count() > 0)
    <div class="mb-8">
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Pending Deliverable Reviews') }}</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($pendingDeliverables as $deliverable)
                    <div class="border border-yellow-200 bg-yellow-50 rounded-lg p-4">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">{{ $deliverable->title }}</h4>
                                <p class="text-xs text-gray-600">{{ __('Project: :title', ['title' => $deliverable->project->subject->title]) }}</p>
                                <p class="text-xs text-gray-500">{{ __('Team: :name', ['name' => $deliverable->project->team->name]) }}</p>
                            </div>
                            <span class="text-xs text-yellow-600 font-medium">
                                {{ __('Submitted :time', ['time' => $deliverable->created_at->diffForHumans()]) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center mt-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                {{ __('Pending Review') }}
                            </span>
                            <div class="space-x-2">
                                <a href="{{ route('pfe.projects.download-deliverable', $deliverable) }}"
                                   class="inline-flex items-center px-3 py-1 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    <i class="fas fa-download mr-1"></i>
                                    {{ __('Download') }}
                                </a>
                                <a href="{{ route('pfe.projects.review-deliverable', $deliverable) }}"
                                   class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                    <i class="fas fa-eye mr-1"></i>
                                    {{ __('Review') }}
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Upcoming Defenses -->
    @if(isset($upcomingDefenses) && $upcomingDefenses->count() > 0)
    <div class="mb-8">
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Upcoming Defenses') }}</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($upcomingDefenses as $defense)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="text-sm font-medium text-gray-900">{{ $defense->project->subject->title }}</h4>
                            <span class="text-xs text-gray-500">
                                {{ $defense->defense_date->format('M d, Y') }}
                            </span>
                        </div>
                        <div class="space-y-1 text-xs text-gray-600">
                            <p>{{ __('Team: :name', ['name' => $defense->project->team->name]) }}</p>
                            <p>{{ __('Time: :time', ['time' => $defense->start_time]) }}</p>
                            <p>{{ __('Room: :room', ['room' => $defense->room->name ?? 'TBD']) }}</p>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('pfe.defenses.show', $defense) }}"
                               class="text-indigo-600 text-xs font-medium hover:text-indigo-500">
                                {{ __('View Details') }} →
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <a href="{{ route('pfe.subjects.create') }}"
           class="bg-white p-6 rounded-lg shadow hover:shadow-md transition-shadow">
            <div class="text-center">
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-plus text-indigo-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900">{{ __('New Subject') }}</h3>
                <p class="text-sm text-gray-500 mt-1">{{ __('Create a new PFE subject') }}</p>
            </div>
        </a>

        <a href="{{ route('pfe.subjects.index') }}?filter=mine"
           class="bg-white p-6 rounded-lg shadow hover:shadow-md transition-shadow">
            <div class="text-center">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-book text-blue-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900">{{ __('My Subjects') }}</h3>
                <p class="text-sm text-gray-500 mt-1">{{ __('Manage your subjects') }}</p>
            </div>
        </a>

        <a href="{{ route('pfe.projects.index') }}?filter=supervised"
           class="bg-white p-6 rounded-lg shadow hover:shadow-md transition-shadow">
            <div class="text-center">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-project-diagram text-green-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900">{{ __('My Projects') }}</h3>
                <p class="text-sm text-gray-500 mt-1">{{ __('Supervise projects') }}</p>
            </div>
        </a>

        <a href="{{ route('pfe.defenses.index') }}"
           class="bg-white p-6 rounded-lg shadow hover:shadow-md transition-shadow">
            <div class="text-center">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-graduation-cap text-purple-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900">{{ __('Defenses') }}</h3>
                <p class="text-sm text-gray-500 mt-1">{{ __('Jury & evaluations') }}</p>
            </div>
        </a>
    </div>
</div>
@endsection