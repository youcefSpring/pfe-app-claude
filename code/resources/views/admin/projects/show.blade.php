@extends('layouts.admin-modern')

@section('page-title', 'View Project')

@section('content')
<!-- Header -->
<div class="flex flex-col lg:flex-row lg:justify-between lg:items-start gap-4 mb-6 lg:mb-8">
    <div class="flex-1">
        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-2">{{ $project->title }}</h1>
        <p class="text-gray-600">Professional Portfolio Project</p>
    </div>
    <div class="flex flex-col sm:flex-row gap-3">
        <a href="{{ route('admin.projects.edit', $project) }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
            <i class="fas fa-edit mr-2"></i>Edit Project
        </a>
        <a href="{{ route('admin.projects.index') }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Back to Projects
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
    <div class="lg:col-span-2 space-y-6">
        <!-- Project Image -->
        @if($project->featured_image)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <img src="{{ Storage::url($project->featured_image) }}"
                     alt="{{ $project->title }}"
                     class="w-full h-auto"
                     style="max-height: 400px; object-fit: cover;">
            </div>
        @endif

        <!-- Project Skills -->
        @if($project->skills && $project->skills->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 lg:p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-code text-blue-600 mr-3"></i>
                        Technical Skills Used
                    </h3>
                    @php
                        $skillsByCategory = $project->skills->groupBy('category');
                    @endphp
                    <div class="space-y-4">
                        @foreach($skillsByCategory as $category => $categorySkills)
                            <div>
                                <h4 class="text-sm font-medium text-gray-800 mb-3">{{ ucfirst($category) }}</h4>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($categorySkills as $skill)
                                        <div class="inline-flex items-center px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm">
                                            @if($skill->icon)
                                                <i class="{{ $skill->icon }} mr-2" style="color: {{ $skill->color ?? '#6B7280' }}"></i>
                                            @endif
                                            <span class="font-medium text-gray-900">{{ $skill->name }}</span>
                                            @if($skill->pivot && $skill->pivot->proficiency_level)
                                                <span class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">
                                                    {{ ucfirst($skill->pivot->proficiency_level) }}
                                                </span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Project Description -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 lg:p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-file-text text-blue-600 mr-3"></i>
                    Description
                </h3>
                <p class="text-gray-700 leading-relaxed">{{ $project->description }}</p>
            </div>
        </div>

        <!-- Project Details -->
        @if($project->content)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 lg:p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-file-alt text-blue-600 mr-3"></i>
                        Project Details
                    </h3>
                    <div class="text-gray-700 leading-relaxed">
                        {!! nl2br(e($project->content)) !!}
                    </div>
                </div>
            </div>
        @endif

        <!-- Technologies -->
        @if($project->technologies)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 lg:p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-code text-blue-600 mr-3"></i>
                        Technologies & Tools
                    </h3>
                    <div class="text-gray-700 leading-relaxed">
                        {!! nl2br(e($project->technologies)) !!}
                    </div>
                </div>
            </div>
        @endif

        <!-- Collaborators -->
        @if($project->collaborators)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 lg:p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-users text-blue-600 mr-3"></i>
                        Collaborators
                    </h3>
                    <div class="text-gray-700 leading-relaxed">
                        {!! nl2br(e($project->collaborators)) !!}
                    </div>
                </div>
            </div>
        @endif

        <!-- Key Outcomes -->
        @if($project->key_outcomes)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 lg:p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-trophy text-blue-600 mr-3"></i>
                        Key Outcomes
                    </h3>
                    <div class="text-gray-700 leading-relaxed">
                        {!! nl2br(e($project->key_outcomes)) !!}
                    </div>
                </div>
            </div>
        @endif

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 lg:p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-bolt text-blue-600 mr-3"></i>
                    Quick Actions
                </h3>
                <div class="flex flex-wrap gap-3">
                    @if($project->project_url)
                        <a href="{{ $project->project_url }}" target="_blank" class="inline-flex items-center px-3 py-2 text-sm font-medium text-blue-700 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                            <i class="fas fa-external-link-alt mr-2"></i>Live Demo
                        </a>
                    @endif
                    @if($project->repository_url)
                        <a href="{{ $project->repository_url }}" target="_blank" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                            <i class="fab fa-github mr-2"></i>Source Code
                        </a>
                    @endif
                    <a href="{{ route('admin.projects.edit', $project) }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-yellow-700 bg-yellow-50 border border-yellow-200 rounded-lg hover:bg-yellow-100 focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition-colors">
                        <i class="fas fa-edit mr-2"></i>Edit Project
                    </a>
                    <form method="POST" action="{{ route('admin.projects.destroy', $project) }}" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-3 py-2 text-sm font-medium text-red-700 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors" data-confirm-delete>
                            <i class="fas fa-trash mr-2"></i>Delete Project
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="lg:col-span-1 space-y-6">
        <!-- Project Info -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 lg:p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-info-circle text-blue-600 mr-3"></i>
                    Project Information
                </h3>

                <div class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-900 mb-1">Status</dt>
                        <dd>
                            @if($project->status === 'active')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">Active</span>
                            @elseif($project->status === 'completed')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">Completed</span>
                            @elseif($project->status === 'on-hold')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">On Hold</span>
                            @elseif($project->status === 'cancelled')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">Cancelled</span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">{{ ucfirst($project->status) }}</span>
                            @endif
                        </dd>
                    </div>

                    @if($project->type)
                        <div>
                            <dt class="text-sm font-medium text-gray-900 mb-1">Project Type</dt>
                            <dd class="text-sm text-gray-600">{{ ucfirst($project->type) }}</dd>
                        </div>
                    @endif

                    @if($project->start_date)
                        <div>
                            <dt class="text-sm font-medium text-gray-900 mb-1">Start Date</dt>
                            <dd class="text-sm text-gray-600">{{ $project->start_date->format('F d, Y') }}</dd>
                        </div>
                    @endif

                    @if($project->end_date)
                        <div>
                            <dt class="text-sm font-medium text-gray-900 mb-1">End Date</dt>
                            <dd class="text-sm text-gray-600">{{ $project->end_date->format('F d, Y') }}</dd>
                        </div>
                    @endif

                    @if($project->funding_amount)
                        <div>
                            <dt class="text-sm font-medium text-gray-900 mb-1">Project Budget</dt>
                            <dd class="text-sm text-gray-600">${{ number_format($project->funding_amount, 2) }}</dd>
                        </div>
                    @endif

                    @if($project->client_organization)
                        <div>
                            <dt class="text-sm font-medium text-gray-900 mb-1">Client/Company</dt>
                            <dd class="text-sm text-gray-600">{{ $project->client_organization }}</dd>
                        </div>
                    @endif

                    <div>
                        <dt class="text-sm font-medium text-gray-900 mb-1">Visibility</dt>
                        <dd>
                            @if($project->is_published)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">Published</span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">Draft</span>
                            @endif
                        </dd>
                    </div>

                    @if($project->is_featured)
                        <div>
                            <dt class="text-sm font-medium text-gray-900 mb-1">Featured</dt>
                            <dd>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">Featured Project</span>
                            </dd>
                        </div>
                    @endif

                    <div>
                        <dt class="text-sm font-medium text-gray-900 mb-1">Created</dt>
                        <dd class="text-sm text-gray-600">{{ $project->created_at->format('F d, Y \a\t g:i A') }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-900 mb-1">Last Updated</dt>
                        <dd class="text-sm text-gray-600">{{ $project->updated_at->format('F d, Y \a\t g:i A') }}</dd>
                    </div>
                </div>
            </div>
        </div>

        <!-- Project Links -->
        @if($project->project_url || $project->repository_url)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 lg:p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-link text-blue-600 mr-3"></i>
                        Project Links
                    </h3>

                    <div class="space-y-3">
                        @if($project->project_url)
                            <a href="{{ $project->project_url }}" target="_blank" class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-blue-700 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                <i class="fas fa-external-link-alt mr-2"></i>Live Demo
                            </a>
                        @endif

                        @if($project->repository_url)
                            <a href="{{ $project->repository_url }}" target="_blank" class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                                <i class="fab fa-github mr-2"></i>Source Code
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Tags -->
        @if($project->tags && $project->tags->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 lg:p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-tags text-blue-600 mr-3"></i>
                        Tags
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($project->tags as $tag)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">{{ $tag->name }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Public Visibility -->
        @if($project->is_published)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 lg:p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-chart-bar text-blue-600 mr-3"></i>
                        Public Visibility
                    </h3>
                    <div class="text-center">
                        <p class="text-gray-600 mb-4">This project is visible to the public</p>
                        <a href="{{ url('/projects/' . Str::slug($project->title)) }}" target="_blank" class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-blue-700 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                            <i class="fas fa-eye mr-2"></i>View Public Page
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

