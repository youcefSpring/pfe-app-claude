@extends('layouts.app')

@section('title', $subject->title)

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Header -->
    <div class="mb-8">
        <nav class="text-sm text-gray-500 mb-4">
            <a href="{{ route('pfe.subjects.index') }}" class="hover:text-gray-700">{{ __('Subjects') }}</a>
            <span class="mx-2">/</span>
            <span class="text-gray-900">{{ $subject->title }}</span>
        </nav>

        <div class="flex items-start justify-between">
            <div class="flex-1">
                <div class="flex items-center space-x-3 mb-2">
                    <h1 class="text-3xl font-bold text-gray-900">{{ $subject->title }}</h1>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        {{ $subject->status === 'published' ? 'bg-blue-100 text-blue-800' :
                           ($subject->status === 'approved' ? 'bg-green-100 text-green-800' :
                           ($subject->status === 'rejected' ? 'bg-red-100 text-red-800' :
                           ($subject->status === 'submitted' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'))) }}">
                        {{ __(ucfirst($subject->status)) }}
                    </span>
                </div>
                <div class="flex items-center space-x-6 text-sm text-gray-500">
                    <span>{{ __('Created :date', ['date' => $subject->created_at->format('M d, Y')]) }}</span>
                    <span>{{ __('Department: :dept', ['dept' => __(ucfirst($subject->department ?? 'N/A'))]) }}</span>
                    <span>{{ __('Max Teams: :max', ['max' => $subject->max_teams]) }}</span>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center space-x-2">
                @can('update', $subject)
                <a href="{{ route('pfe.subjects.edit', $subject) }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-edit mr-2"></i>
                    {{ __('Edit') }}
                </a>
                @endcan

                @hasrole('chef_master')
                @if($subject->status === 'submitted')
                <a href="{{ route('pfe.subjects.validation', $subject) }}"
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ __('Review') }}
                </a>
                @endif

                @if($subject->status === 'approved')
                <form method="POST" action="{{ route('pfe.subjects.publish', $subject) }}" class="inline">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700"
                            onclick="return confirm('{{ __('Are you sure you want to publish this subject?') }}')">
                        <i class="fas fa-paper-plane mr-2"></i>
                        {{ __('Publish') }}
                    </button>
                </form>
                @endif
                @endhasrole

                @if($subject->status === 'draft' && auth()->user()->id === $subject->supervisor_id)
                <form method="POST" action="{{ route('pfe.subjects.submit', $subject) }}" class="inline">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        <i class="fas fa-paper-plane mr-2"></i>
                        {{ __('Submit for Review') }}
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Description -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ __('Description') }}</h2>
                <div class="prose max-w-none text-gray-700">
                    {!! nl2br(e($subject->description)) !!}
                </div>
            </div>

            <!-- Keywords -->
            @if($subject->keywords)
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ __('Keywords') }}</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach($subject->keywords as $keyword)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        {{ $keyword }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Technical Details -->
            @if($subject->required_tools || $subject->prerequisites || $subject->expected_deliverables)
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ __('Technical Details') }}</h2>

                @if($subject->required_tools)
                <div class="mb-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Required Tools & Technologies') }}</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach(explode(',', $subject->required_tools) as $tool)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ trim($tool) }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($subject->prerequisites)
                <div class="mb-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Prerequisites') }}</h3>
                    <p class="text-gray-700">{{ $subject->prerequisites }}</p>
                </div>
                @endif

                @if($subject->expected_deliverables)
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Expected Deliverables') }}</h3>
                    <div class="prose max-w-none text-gray-700">
                        {!! nl2br(e($subject->expected_deliverables)) !!}
                    </div>
                </div>
                @endif
            </div>
            @endif

            <!-- Validation Notes -->
            @if($subject->validation_notes && ($subject->status === 'rejected' || $subject->status === 'needs_correction'))
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                <h2 class="text-xl font-semibold text-yellow-800 mb-4">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    {{ __('Validation Feedback') }}
                </h2>
                <div class="text-yellow-700">
                    {!! nl2br(e($subject->validation_notes)) !!}
                </div>
            </div>
            @endif

            <!-- Assigned Teams -->
            @if($subject->projects && $subject->projects->count() > 0)
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ __('Assigned Teams') }}</h2>
                <div class="space-y-4">
                    @foreach($subject->projects as $project)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">{{ $project->team->name }}</h3>
                                <p class="text-sm text-gray-500">
                                    {{ __(':count members', ['count' => $project->team->members->count()]) }} •
                                    {{ __('Started :date', ['date' => $project->start_date->format('M d, Y')]) }}
                                </p>
                            </div>
                            <div class="flex items-center space-x-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $project->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ __(ucfirst($project->status)) }}
                                </span>
                                <a href="{{ route('pfe.projects.show', $project) }}"
                                   class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                                    {{ __('View Project') }} →
                                </a>
                            </div>
                        </div>
                        <div class="flex -space-x-2 mt-3">
                            @foreach($project->team->members as $member)
                            <img class="inline-block h-8 w-8 rounded-full ring-2 ring-white"
                                 src="{{ $member->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($member->name) }}"
                                 alt="{{ $member->name }}"
                                 title="{{ $member->name }}">
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Supervisor Info -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Supervisor') }}</h3>
                <div class="flex items-center space-x-3">
                    <img class="h-12 w-12 rounded-full"
                         src="{{ $subject->supervisor->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($subject->supervisor->name) }}"
                         alt="{{ $subject->supervisor->name }}">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $subject->supervisor->name }}</p>
                        <p class="text-sm text-gray-500">{{ $subject->supervisor->email }}</p>
                        @if($subject->supervisor->department)
                        <p class="text-sm text-gray-500">{{ $subject->supervisor->department }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- External Collaboration -->
            @if($subject->external_supervisor || $subject->external_company)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('External Collaboration') }}</h3>
                @if($subject->external_supervisor)
                <div class="mb-3">
                    <p class="text-sm font-medium text-gray-700">{{ __('External Supervisor') }}</p>
                    <p class="text-sm text-gray-900">{{ $subject->external_supervisor }}</p>
                </div>
                @endif
                @if($subject->external_company)
                <div>
                    <p class="text-sm font-medium text-gray-700">{{ __('Company/Organization') }}</p>
                    <p class="text-sm text-gray-900">{{ $subject->external_company }}</p>
                </div>
                @endif
            </div>
            @endif

            <!-- Subject Statistics -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Statistics') }}</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">{{ __('Teams Assigned') }}</span>
                        <span class="text-sm font-medium text-gray-900">
                            {{ $subject->projects->count() }} / {{ $subject->max_teams }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">{{ __('Difficulty Level') }}</span>
                        <span class="text-sm font-medium text-gray-900">
                            {{ __(ucfirst($subject->difficulty_level ?? 'intermediate')) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">{{ __('Recommended Team Size') }}</span>
                        <span class="text-sm font-medium text-gray-900">
                            {{ $subject->recommended_team_size ?? 2 }} {{ __('students') }}
                        </span>
                    </div>
                    @if($subject->preferences && $subject->preferences->count() > 0)
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">{{ __('Team Preferences') }}</span>
                        <span class="text-sm font-medium text-gray-900">{{ $subject->preferences->count() }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Status History -->
            @if($subject->statusHistory && $subject->statusHistory->count() > 0)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Status History') }}</h3>
                <div class="space-y-3">
                    @foreach($subject->statusHistory->sortByDesc('created_at') as $history)
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-{{ $history->icon ?? 'clock' }} text-indigo-600 text-sm"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900">{{ $history->description }}</p>
                            <p class="text-xs text-gray-500">{{ $history->created_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Quick Actions -->
            @hasrole('student')
            @if($subject->status === 'published' && auth()->user()->team && !auth()->user()->team->project)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Quick Actions') }}</h3>
                <a href="{{ route('pfe.teams.preferences', auth()->user()->team) }}?subject={{ $subject->id }}"
                   class="block w-full text-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                    <i class="fas fa-heart mr-2"></i>
                    {{ __('Add to Preferences') }}
                </a>
            </div>
            @endif
            @endhasrole
        </div>
    </div>
</div>
@endsection