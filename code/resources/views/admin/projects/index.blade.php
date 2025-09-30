@extends('layouts.admin-modern')

@section('title', 'Projects Management')
@section('page-title', 'Projects')

@section('content')
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 lg:mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Projects</h1>
            <p class="text-gray-600 mt-1">Manage your professional web development portfolio projects</p>
        </div>
        <a href="{{ route('admin.projects.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
            <i class="fas fa-plus mr-2"></i>
            Add New Project
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 lg:p-6 mb-6 lg:mb-8">
        <form method="GET" action="{{ route('admin.projects.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="skill" class="block text-sm font-medium text-gray-700 mb-2">Skill</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" id="skill" name="skill">
                    <option value="">All Skills</option>
                    @if(isset($skills))
                        @foreach($skills as $skill)
                            <option value="{{ $skill->id }}" {{ request('skill') == $skill->id ? 'selected' : '' }}>
                                {{ $skill->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div>
                <label for="tag" class="block text-sm font-medium text-gray-700 mb-2">Tag</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" id="tag" name="tag">
                    <option value="">All Tags</option>
                    @foreach($tags as $tag)
                        <option value="{{ $tag->id }}" {{ request('tag') == $tag->id ? 'selected' : '' }}>
                            {{ $tag->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" id="status" name="status">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="on-hold" {{ request('status') == 'on-hold' ? 'selected' : '' }}>On Hold</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-search"></i>
                </button>
                <a href="{{ route('admin.projects.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Projects Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-4 lg:p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h2 class="text-lg lg:text-xl font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-project-diagram mr-2 text-blue-600"></i>
                    Projects ({{ $projects->total() }})
                </h2>
                @if(request()->hasAny(['skill', 'tag', 'status']))
                    <span class="text-sm text-gray-500">Filtered results</span>
                @endif
            </div>
        </div>

        @if($projects->count() > 0)
            <!-- Desktop Table -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-sm text-gray-600 border-b border-gray-100">
                            <th class="pb-3 px-6 font-medium">Project</th>
                            <th class="pb-3 px-3 font-medium">Status</th>
                            <th class="pb-3 px-3 font-medium">Start Date</th>
                            <th class="pb-3 px-3 font-medium">Skills & Tags</th>
                            <th class="pb-3 px-6 font-medium w-32">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @foreach($projects as $project)
                            <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                                <td class="py-4 px-6">
                                    <div class="flex items-center">
                                        @if($project->featured_image)
                                            <img src="{{ Storage::url($project->featured_image) }}"
                                                 alt="{{ $project->title }}"
                                                 class="w-12 h-12 rounded-lg object-cover mr-3">
                                        @else
                                            <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                                                <i class="fas fa-project-diagram text-gray-400"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <h3 class="font-medium text-gray-900 mb-1">
                                                <a href="{{ route('admin.projects.show', $project) }}" class="hover:text-blue-600 transition-colors">
                                                    {{ $project->title }}
                                                </a>
                                            </h3>
                                            @if($project->description)
                                                <p class="text-gray-500 text-xs">{{ Str::limit($project->description, 80) }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-3">
                                    @if($project->status === 'active')
                                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">Active</span>
                                    @elseif($project->status === 'completed')
                                        <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">Completed</span>
                                    @elseif($project->status === 'on-hold')
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium">On Hold</span>
                                    @else
                                        <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-medium">{{ ucfirst($project->status) }}</span>
                                    @endif
                                </td>
                                <td class="py-4 px-3 text-gray-600">
                                    @if($project->start_date)
                                        <span class="text-xs">{{ $project->start_date->format('M j, Y') }}</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="py-4 px-3">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($project->skills->take(3) as $skill)
                                            <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-medium">
                                                @if($skill->icon)
                                                    <i class="{{ $skill->icon }} mr-1" style="color: {{ $skill->color ?? '#1E40AF' }}"></i>
                                                @endif
                                                {{ $skill->name }}
                                            </span>
                                        @endforeach
                                        @if($project->skills->count() > 3)
                                            <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs font-medium">
                                                +{{ $project->skills->count() - 3 }} more
                                            </span>
                                        @endif
                                        @foreach($project->tags->take(2) as $tag)
                                            <span class="px-2 py-1 bg-purple-100 text-purple-700 rounded text-xs font-medium">{{ $tag->name }}</span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('admin.projects.show', $project) }}" class="text-blue-600 hover:text-blue-700 transition-colors" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.projects.edit', $project) }}" class="text-green-600 hover:text-green-700 transition-colors" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($project->slug)
                                            <a href="{{ route('projects.show', $project->slug) }}" target="_blank" class="text-purple-600 hover:text-purple-700 transition-colors" title="View on Site">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        @endif
                                        <form method="POST" action="{{ route('admin.projects.destroy', $project) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-700 transition-colors" title="Delete" onclick="return confirm('Are you sure you want to delete this project?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="lg:hidden divide-y divide-gray-100">
                @foreach($projects as $project)
                    <div class="p-4 lg:p-6">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <div class="flex items-center mb-2">
                                    @if($project->featured_image)
                                        <img src="{{ Storage::url($project->featured_image) }}"
                                             alt="{{ $project->title }}"
                                             class="w-10 h-10 rounded-lg object-cover mr-3">
                                    @else
                                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                                            <i class="fas fa-project-diagram text-gray-400"></i>
                                        </div>
                                    @endif
                                    <h3 class="font-medium text-gray-900">
                                        <a href="{{ route('admin.projects.show', $project) }}" class="hover:text-blue-600 transition-colors">
                                            {{ $project->title }}
                                        </a>
                                    </h3>
                                </div>
                                @if($project->description)
                                    <p class="text-gray-500 text-sm mb-2">{{ Str::limit($project->description, 100) }}</p>
                                @endif
                            </div>
                            <div class="ml-4">
                                @if($project->status === 'active')
                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">Active</span>
                                @elseif($project->status === 'completed')
                                    <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">Completed</span>
                                @elseif($project->status === 'on-hold')
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium">On Hold</span>
                                @else
                                    <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-medium">{{ ucfirst($project->status) }}</span>
                                @endif
                            </div>
                        </div>

                        @if($project->start_date)
                            <div class="text-sm text-gray-600 mb-3">
                                <i class="fas fa-calendar mr-1"></i>
                                Started {{ $project->start_date->format('M j, Y') }}
                            </div>
                        @endif

                        @if($project->skills->count() > 0 || $project->tags->count() > 0)
                            <div class="flex flex-wrap gap-1 mb-3">
                                @foreach($project->skills->take(3) as $skill)
                                    <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-medium">
                                        @if($skill->icon)
                                            <i class="{{ $skill->icon }} mr-1" style="color: {{ $skill->color ?? '#1E40AF' }}"></i>
                                        @endif
                                        {{ $skill->name }}
                                    </span>
                                @endforeach
                                @if($project->skills->count() > 3)
                                    <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs font-medium">
                                        +{{ $project->skills->count() - 3 }} more
                                    </span>
                                @endif
                                @foreach($project->tags->take(2) as $tag)
                                    <span class="px-2 py-1 bg-purple-100 text-purple-700 rounded text-xs font-medium">{{ $tag->name }}</span>
                                @endforeach
                            </div>
                        @endif

                        <div class="flex items-center space-x-4">
                            <a href="{{ route('admin.projects.show', $project) }}" class="text-blue-600 hover:text-blue-700 transition-colors text-sm font-medium">
                                <i class="fas fa-eye mr-1"></i>View
                            </a>
                            <a href="{{ route('admin.projects.edit', $project) }}" class="text-green-600 hover:text-green-700 transition-colors text-sm font-medium">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </a>
                            @if($project->slug)
                                <a href="{{ route('projects.show', $project->slug) }}" target="_blank" class="text-purple-600 hover:text-purple-700 transition-colors text-sm font-medium">
                                    <i class="fas fa-external-link-alt mr-1"></i>View on Site
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($projects->hasPages())
                <div class="p-4 lg:p-6 border-t border-gray-100">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-sm text-gray-600 mb-4 sm:mb-0">
                            Showing {{ $projects->firstItem() }} to {{ $projects->lastItem() }} of {{ $projects->total() }} results
                        </p>
                        <div class="flex items-center space-x-2">
                            {{ $projects->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="p-8 lg:p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-project-diagram text-gray-400 text-2xl"></i>
                </div>
                @if(request()->hasAny(['skill', 'tag', 'status']))
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No projects found</h3>
                    <p class="text-gray-600 mb-4">No projects match your current filters.</p>
                    <a href="{{ route('admin.projects.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Clear Filters
                    </a>
                @else
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No projects yet</h3>
                    <p class="text-gray-600 mb-4">Start building your professional web development portfolio.</p>
                    <a href="{{ route('admin.projects.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Add Your First Project
                    </a>
                @endif
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-submit search form on select changes
        const skillSelect = document.getElementById('skill');
        const tagSelect = document.getElementById('tag');
        const statusSelect = document.getElementById('status');

        [skillSelect, tagSelect, statusSelect].forEach(function(select) {
            if (select) {
                select.addEventListener('change', function() {
                    this.form.submit();
                });
            }
        });
    });
</script>
@endpush