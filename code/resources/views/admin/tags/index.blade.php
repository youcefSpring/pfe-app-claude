@extends('layouts.admin-modern')

@section('title', 'Tags Management')
@section('page-title', 'Tags')

@section('content')
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 lg:mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Tags Management</h1>
            <p class="text-gray-600 mt-1">Organize your content with tags and categories</p>
        </div>
        <a href="{{ route('admin.tags.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
            <i class="fas fa-plus mr-2"></i>
            Add New Tag
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-6 lg:mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 lg:p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Tags</p>
                    <p class="text-2xl lg:text-3xl font-bold text-gray-900">{{ $tags->total() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-tags text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 lg:p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Most Used</p>
                    @if(isset($mostUsedTag))
                        <p class="text-lg font-bold text-gray-900">{{ $mostUsedTag->name }}</p>
                        <p class="text-xs text-gray-500">{{ $mostUsedTag->usage_count }} items</p>
                    @else
                        <p class="text-sm text-gray-500">No usage data</p>
                    @endif
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-bar text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 lg:p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Blog Tags</p>
                    <p class="text-2xl lg:text-3xl font-bold text-gray-900">{{ $blogTagsCount ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-blog text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 lg:p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Project Tags</p>
                    <p class="text-2xl lg:text-3xl font-bold text-gray-900">{{ $projectTagsCount ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-project-diagram text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 lg:p-6 mb-6 lg:mb-8">
        <form method="GET" action="{{ route('admin.tags.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="md:col-span-2">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search Tags</label>
                <input type="text" id="search" name="search" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" placeholder="Search tags..." value="{{ request('search') }}">
            </div>
            <div>
                <label for="sort" class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                <select id="sort" name="sort" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name</option>
                    <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Date Created</option>
                    <option value="usage" {{ request('sort') == 'usage' ? 'selected' : '' }}>Usage Count</option>
                </select>
            </div>
            <div>
                <label for="order" class="block text-sm font-medium text-gray-700 mb-2">Order</label>
                <select id="order" name="order" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    <option value="asc" {{ request('order') == 'asc' ? 'selected' : '' }}>Ascending</option>
                    <option value="desc" {{ request('order') == 'desc' ? 'selected' : '' }}>Descending</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-search mr-2"></i>Search
                </button>
                <a href="{{ route('admin.tags.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Tags Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-4 lg:p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h2 class="text-lg lg:text-xl font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-tags mr-2 text-blue-600"></i>
                    Tags ({{ $tags->total() }})
                </h2>
                @if(request()->hasAny(['search', 'sort', 'order']))
                    <span class="text-sm text-gray-500">Filtered results</span>
                @endif
            </div>
        </div>

        @if($tags->count() > 0)
            <!-- Desktop Table -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-sm text-gray-600 border-b border-gray-100">
                            <th class="pb-3 px-6 font-medium">Tag</th>
                            <th class="pb-3 px-3 font-medium">Slug</th>
                            <th class="pb-3 px-3 font-medium">Description</th>
                            <th class="pb-3 px-3 font-medium">Usage</th>
                            <th class="pb-3 px-3 font-medium">Created</th>
                            <th class="pb-3 px-6 font-medium w-32">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @foreach($tags as $tag)
                            <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                                <td class="py-4 px-6">
                                    <div class="flex items-center">
                                        <div class="w-4 h-4 rounded-full mr-3"
                                             style="background-color: {{ $tag->color ?? '#6b7280' }};"></div>
                                        <div>
                                            <h3 class="font-medium text-gray-900 mb-1">{{ $tag->name }}</h3>
                                            @if($tag->color)
                                                <p class="text-gray-500 text-xs">{{ $tag->color }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-3">
                                    <code class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs font-mono">{{ $tag->slug }}</code>
                                </td>
                                <td class="py-4 px-3 text-gray-600">
                                    @if($tag->description)
                                        {{ Str::limit($tag->description, 50) }}
                                    @else
                                        <span class="text-gray-400">No description</span>
                                    @endif
                                </td>
                                <td class="py-4 px-3">
                                    @php
                                        $totalUsage = ($tag->posts_count ?? 0) + ($tag->projects_count ?? 0) + ($tag->courses_count ?? 0);
                                    @endphp
                                    <div class="flex flex-col space-y-1">
                                        <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium inline-block w-fit">
                                            {{ $totalUsage }} items
                                        </span>
                                        @if($totalUsage > 0)
                                            <div class="text-xs text-gray-500">
                                                @if($tag->posts_count ?? 0 > 0)
                                                    {{ $tag->posts_count }} blog{{ ($tag->posts_count ?? 0) !== 1 ? 's' : '' }}
                                                @endif
                                                @if($tag->projects_count ?? 0 > 0)
                                                    @if($tag->posts_count ?? 0 > 0), @endif
                                                    {{ $tag->projects_count }} project{{ ($tag->projects_count ?? 0) !== 1 ? 's' : '' }}
                                                @endif
                                                @if($tag->courses_count ?? 0 > 0)
                                                    @if(($tag->posts_count ?? 0) > 0 || ($tag->projects_count ?? 0) > 0), @endif
                                                    {{ $tag->courses_count }} course{{ ($tag->courses_count ?? 0) !== 1 ? 's' : '' }}
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-3 text-gray-600">
                                    <span class="text-xs">{{ $tag->created_at->format('M j, Y') }}</span>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('admin.tags.edit', $tag) }}" class="text-green-600 hover:text-green-700 transition-colors" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.tags.destroy', $tag) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-700 transition-colors" title="Delete" data-confirm-delete="{{ $totalUsage > 0 ? 'has-items' : '' }}">
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
                @foreach($tags as $tag)
                    <div class="p-4 lg:p-6">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <div class="flex items-center mb-2">
                                    <div class="w-4 h-4 rounded-full mr-3"
                                         style="background-color: {{ $tag->color ?? '#6b7280' }};"></div>
                                    <h3 class="font-medium text-gray-900">{{ $tag->name }}</h3>
                                </div>
                                @if($tag->description)
                                    <p class="text-gray-500 text-sm mb-2">{{ Str::limit($tag->description, 100) }}</p>
                                @endif
                                <div class="text-sm text-gray-600 mb-2">
                                    <code class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs font-mono">{{ $tag->slug }}</code>
                                </div>
                            </div>
                            @php
                                $totalUsage = ($tag->posts_count ?? 0) + ($tag->projects_count ?? 0) + ($tag->courses_count ?? 0);
                            @endphp
                            <div class="ml-4">
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">
                                    {{ $totalUsage }} items
                                </span>
                            </div>
                        </div>

                        @if($totalUsage > 0)
                            <div class="text-xs text-gray-500 mb-3">
                                @if($tag->posts_count ?? 0 > 0)
                                    {{ $tag->posts_count }} blog post{{ ($tag->posts_count ?? 0) !== 1 ? 's' : '' }}
                                @endif
                                @if($tag->projects_count ?? 0 > 0)
                                    @if($tag->posts_count ?? 0 > 0), @endif
                                    {{ $tag->projects_count }} project{{ ($tag->projects_count ?? 0) !== 1 ? 's' : '' }}
                                @endif
                                @if($tag->courses_count ?? 0 > 0)
                                    @if(($tag->posts_count ?? 0) > 0 || ($tag->projects_count ?? 0) > 0), @endif
                                    {{ $tag->courses_count }} course{{ ($tag->courses_count ?? 0) !== 1 ? 's' : '' }}
                                @endif
                            </div>
                        @endif

                        <div class="flex items-center justify-between">
                            <div class="text-xs text-gray-500">
                                <i class="fas fa-calendar mr-1"></i>
                                {{ $tag->created_at->format('M j, Y') }}
                            </div>
                            <div class="flex items-center space-x-4">
                                <a href="{{ route('admin.tags.edit', $tag) }}" class="text-green-600 hover:text-green-700 transition-colors text-sm font-medium">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </a>
                                <form method="POST" action="{{ route('admin.tags.destroy', $tag) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700 transition-colors text-sm font-medium" data-confirm-delete="{{ $totalUsage > 0 ? 'has-items' : '' }}">
                                        <i class="fas fa-trash mr-1"></i>Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($tags->hasPages())
                <div class="p-4 lg:p-6 border-t border-gray-100">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-sm text-gray-600 mb-4 sm:mb-0">
                            Showing {{ $tags->firstItem() }} to {{ $tags->lastItem() }} of {{ $tags->total() }} results
                        </p>
                        <div class="flex items-center space-x-2">
                            {{ $tags->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="p-8 lg:p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-tags text-gray-400 text-2xl"></i>
                </div>
                @if(request()->hasAny(['search', 'sort', 'order']))
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No tags found</h3>
                    <p class="text-gray-600 mb-4">No tags match your current filters.</p>
                    <a href="{{ route('admin.tags.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Clear Filters
                    </a>
                @else
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No tags yet</h3>
                    <p class="text-gray-600 mb-4">Start organizing your content with tags and categories.</p>
                    <a href="{{ route('admin.tags.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Create Your First Tag
                    </a>
                @endif
            </div>
        @endif
    </div>

    <!-- Bulk Actions (if there are tags) -->
    @if($tags->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 mt-6 lg:mt-8">
            <div class="p-4 lg:p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-cogs text-blue-600 mr-2"></i>
                    Bulk Actions
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('admin.tags.index', ['sort' => 'usage', 'order' => 'desc']) }}"
                       class="inline-flex items-center justify-center px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors">
                        <i class="fas fa-sort-amount-down mr-2"></i>Show Most Used Tags
                    </a>
                    <a href="{{ route('admin.tags.index', ['unused' => 'true']) }}"
                       class="inline-flex items-center justify-center px-4 py-2 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition-colors">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Show Unused Tags
                    </a>
                    <button type="button" class="inline-flex items-center justify-center px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors" onclick="bulkDeleteUnused()">
                        <i class="fas fa-trash mr-2"></i>Delete Unused Tags
                    </button>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-submit search form on select changes for better UX
        const sortSelect = document.getElementById('sort');
        const orderSelect = document.getElementById('order');

        [sortSelect, orderSelect].forEach(function(select) {
            if (select) {
                select.addEventListener('change', function() {
                    this.form.submit();
                });
            }
        });

        // Enhanced delete confirmation for tags with items
        const deleteButtons = document.querySelectorAll('[data-confirm-delete]');
        deleteButtons.forEach(function(button) {
            button.addEventListener('click', function(e) {
                const hasItems = this.dataset.confirmDelete === 'has-items';

                let message = 'Are you sure you want to delete this tag?';
                if (hasItems) {
                    message = 'This tag is currently being used by posts, projects, or courses. Deleting it will remove the tag from all associated content. Are you sure you want to continue?';
                }

                if (!confirm(message)) {
                    e.preventDefault();
                }
            });
        });
    });

    function bulkDeleteUnused() {
        if (confirm('Are you sure you want to delete all unused tags? This action cannot be undone.')) {
            // Create a form dynamically to handle bulk delete
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("admin.tags.bulk-delete-unused") }}';

            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);

            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endpush