@extends('layouts.app')

@section('title', __('Student Dashboard'))

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Welcome Section -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg shadow-lg p-6 text-white">
            <h1 class="text-3xl font-bold mb-2">
                {{ __('Welcome, :name!', ['name' => auth()->user()->first_name]) }}
            </h1>
            <p class="text-blue-100">
                {{ __('Track your PFE journey and stay organized.') }}
            </p>
        </div>
    </div>

    <!-- Progress Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Current Status -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Your PFE Progress') }}</h3>

                @if($userTeam)
                <!-- Progress Steps -->
                <div class="space-y-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-white text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-sm font-medium text-gray-900">{{ __('Team Formation') }}</h4>
                            <p class="text-sm text-gray-500">{{ __('Completed - Team: :name', ['name' => $userTeam->name]) }}</p>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 {{ $userTeam->preferences->count() > 0 ? 'bg-green-500' : 'bg-gray-300' }} rounded-full flex items-center justify-center">
                                <i class="fas fa-{{ $userTeam->preferences->count() > 0 ? 'check' : 'clock' }} text-white text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-sm font-medium text-gray-900">{{ __('Subject Selection') }}</h4>
                            <p class="text-sm text-gray-500">
                                @if($userTeam->preferences->count() > 0)
                                    {{ __('Completed - :count preferences set', ['count' => $userTeam->preferences->count()]) }}
                                @else
                                    {{ __('Pending - Select your preferred subjects') }}
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 {{ $userProject ? 'bg-green-500' : 'bg-gray-300' }} rounded-full flex items-center justify-center">
                                <i class="fas fa-{{ $userProject ? 'check' : 'clock' }} text-white text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-sm font-medium text-gray-900">{{ __('Project Assignment') }}</h4>
                            <p class="text-sm text-gray-500">
                                @if($userProject)
                                    {{ __('Assigned - :title', ['title' => $userProject->subject->title]) }}
                                @else
                                    {{ __('Waiting for assignment') }}
                                @endif
                            </p>
                        </div>
                    </div>

                    @if($userProject)
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 {{ $userProject->status === 'completed' ? 'bg-green-500' : 'bg-yellow-500' }} rounded-full flex items-center justify-center">
                                <i class="fas fa-{{ $userProject->status === 'completed' ? 'check' : 'play' }} text-white text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-sm font-medium text-gray-900">{{ __('Project Development') }}</h4>
                            <p class="text-sm text-gray-500">
                                {{ __('Status: :status', ['status' => __(ucfirst($userProject->status))]) }}
                            </p>
                        </div>
                    </div>

                    @if($userProject->defense)
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 {{ $userProject->defense->status === 'completed' ? 'bg-green-500' : 'bg-blue-500' }} rounded-full flex items-center justify-center">
                                <i class="fas fa-{{ $userProject->defense->status === 'completed' ? 'check' : 'graduation-cap' }} text-white text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-sm font-medium text-gray-900">{{ __('Defense') }}</h4>
                            <p class="text-sm text-gray-500">
                                @if($userProject->defense->status === 'completed')
                                    {{ __('Completed - Grade: :grade', ['grade' => $userProject->defense->final_grade]) }}
                                @else
                                    {{ __('Scheduled for :date', ['date' => $userProject->defense->defense_date->format('M d, Y')]) }}
                                @endif
                            </p>
                        </div>
                    </div>
                    @endif
                    @endif
                </div>
                @else
                <!-- No Team -->
                <div class="text-center py-8">
                    <i class="fas fa-users text-gray-400 text-4xl mb-4"></i>
                    <h4 class="text-lg font-medium text-gray-900 mb-2">{{ __('Join or Create a Team') }}</h4>
                    <p class="text-gray-500 mb-4">{{ __('You need to be part of a team to start your PFE journey.') }}</p>
                    <a href="{{ route('pfe.teams.create') }}"
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        <i class="fas fa-plus mr-2"></i>
                        {{ __('Create Team') }}
                    </a>
                </div>
                @endif
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="space-y-6">
            @if($userTeam)
            <!-- Team Info -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('My Team') }}</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm font-medium text-gray-600">{{ __('Team Name') }}</p>
                        <p class="text-lg text-gray-900">{{ $userTeam->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">{{ __('Members') }}</p>
                        <div class="flex -space-x-2 mt-2">
                            @foreach($userTeam->members as $member)
                            <img class="inline-block h-8 w-8 rounded-full ring-2 ring-white"
                                 src="{{ $member->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($member->name) }}"
                                 alt="{{ $member->name }}"
                                 title="{{ $member->name }}">
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">{{ __('Status') }}</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $userTeam->status === 'validated' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ __(ucfirst($userTeam->status)) }}
                        </span>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('pfe.teams.show', $userTeam) }}"
                       class="text-indigo-600 text-sm font-medium hover:text-indigo-500">
                        {{ __('View Team Details') }} →
                    </a>
                </div>
            </div>
            @endif

            <!-- Available Subjects -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Available Subjects') }}</h3>
                <div class="text-center">
                    <div class="text-3xl font-bold text-indigo-600">{{ $availableSubjects ?? 0 }}</div>
                    <p class="text-sm text-gray-500">{{ __('subjects available') }}</p>
                </div>
                <div class="mt-4">
                    <a href="{{ route('pfe.subjects.available') }}"
                       class="block w-full text-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-600 bg-indigo-50 hover:bg-indigo-100">
                        {{ __('Browse Subjects') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Project -->
    @if($userProject)
    <div class="mb-8">
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Current Project') }}</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-xl font-bold text-gray-900 mb-2">{{ $userProject->subject->title }}</h4>
                        <p class="text-gray-600 mb-4">{{ Str::limit($userProject->subject->description, 200) }}</p>

                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-600">{{ __('Supervisor') }}</span>
                                <span class="text-sm text-gray-900">{{ $userProject->supervisor->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-600">{{ __('Status') }}</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $userProject->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ __(ucfirst($userProject->status)) }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-600">{{ __('Start Date') }}</span>
                                <span class="text-sm text-gray-900">{{ $userProject->start_date->format('M d, Y') }}</span>
                            </div>
                        </div>

                        <div class="mt-6">
                            <a href="{{ route('pfe.projects.show', $userProject) }}"
                               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                {{ __('View Project') }} →
                            </a>
                        </div>
                    </div>

                    <div>
                        <!-- Progress Chart -->
                        <div class="mb-4">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-gray-700">{{ __('Progress') }}</span>
                                <span class="text-sm text-gray-500">{{ $userProject->progress_percentage ?? 0 }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $userProject->progress_percentage ?? 0 }}%"></div>
                            </div>
                        </div>

                        <!-- Recent Deliverables -->
                        <h5 class="text-sm font-medium text-gray-900 mb-3">{{ __('Recent Deliverables') }}</h5>
                        @if($userProject->deliverables->count() > 0)
                        <div class="space-y-2">
                            @foreach($userProject->deliverables->take(3) as $deliverable)
                            <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-file-alt text-gray-400"></i>
                                    <span class="text-sm text-gray-900">{{ $deliverable->title }}</span>
                                </div>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $deliverable->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ __(ucfirst($deliverable->status)) }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-sm text-gray-500">{{ __('No deliverables uploaded yet.') }}</p>
                        @endif

                        <div class="mt-4">
                            <a href="{{ route('pfe.projects.upload', $userProject) }}"
                               class="block w-full text-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                {{ __('Upload Deliverable') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @if(!$userTeam)
        <a href="{{ route('pfe.teams.create') }}"
           class="bg-white p-6 rounded-lg shadow hover:shadow-md transition-shadow">
            <div class="text-center">
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-users-plus text-indigo-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900">{{ __('Create Team') }}</h3>
                <p class="text-sm text-gray-500 mt-1">{{ __('Form your project team') }}</p>
            </div>
        </a>
        @else
        <a href="{{ route('pfe.teams.preferences', $userTeam) }}"
           class="bg-white p-6 rounded-lg shadow hover:shadow-md transition-shadow">
            <div class="text-center">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-heart text-blue-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900">{{ __('Set Preferences') }}</h3>
                <p class="text-sm text-gray-500 mt-1">{{ __('Choose preferred subjects') }}</p>
            </div>
        </a>
        @endif

        <a href="{{ route('pfe.subjects.available') }}"
           class="bg-white p-6 rounded-lg shadow hover:shadow-md transition-shadow">
            <div class="text-center">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-book-open text-green-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900">{{ __('Browse Subjects') }}</h3>
                <p class="text-sm text-gray-500 mt-1">{{ __('Explore available topics') }}</p>
            </div>
        </a>

        @if($userProject)
        <a href="{{ route('pfe.projects.progress', $userProject) }}"
           class="bg-white p-6 rounded-lg shadow hover:shadow-md transition-shadow">
            <div class="text-center">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-chart-line text-yellow-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900">{{ __('Track Progress') }}</h3>
                <p class="text-sm text-gray-500 mt-1">{{ __('Monitor project status') }}</p>
            </div>
        </a>
        @endif

        <a href="{{ route('pfe.notifications.index') }}"
           class="bg-white p-6 rounded-lg shadow hover:shadow-md transition-shadow">
            <div class="text-center">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-bell text-purple-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900">{{ __('Notifications') }}</h3>
                <p class="text-sm text-gray-500 mt-1">
                    {{ auth()->user()->unreadNotifications->count() }} {{ __('unread') }}
                </p>
            </div>
        </a>
    </div>
</div>
@endsection