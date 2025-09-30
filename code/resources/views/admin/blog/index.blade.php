@extends('layouts.admin-modern')

@section('title', 'Blog Posts Management')
@section('page-title', 'Blog Posts')

@section('content')
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 lg:mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Blog Posts</h1>
            <p class="text-gray-600 mt-1">Create and manage your blog posts</p>
        </div>
        <a href="{{ route('admin.blog.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
            <i class="fas fa-plus mr-2"></i>
            New Post
        </a>
    </div>

    <!-- Blog Posts Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-4 lg:p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h2 class="text-lg lg:text-xl font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-blog mr-2 text-blue-600"></i>
                    Blog Posts ({{ $posts->total() }})
                </h2>
            </div>
        </div>

        @if($posts->count() > 0)
            <!-- Desktop Table -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-sm text-gray-600 border-b border-gray-100">
                            <th class="pb-3 px-6 font-medium">Post</th>
                            <th class="pb-3 px-3 font-medium">Status</th>
                            <th class="pb-3 px-3 font-medium">Published Date</th>
                            <th class="pb-3 px-3 font-medium">Tags</th>
                            <th class="pb-3 px-6 font-medium w-32">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @foreach($posts as $post)
                            <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                                <td class="py-4 px-6">
                                    <div class="flex items-center">
                                        @if($post->featured_image)
                                            <img src="{{ Storage::url($post->featured_image) }}"
                                                 alt="{{ $post->title }}"
                                                 class="w-12 h-12 rounded-lg object-cover mr-3">
                                        @else
                                            <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                                                <i class="fas fa-blog text-gray-400"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <h3 class="font-medium text-gray-900 mb-1">
                                                <a href="{{ route('admin.blog.show', $post) }}" class="hover:text-blue-600 transition-colors">
                                                    {{ $post->title }}
                                                </a>
                                            </h3>
                                            @if($post->excerpt)
                                                <p class="text-gray-500 text-xs">{{ Str::limit($post->excerpt, 80) }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-3">
                                    @if($post->is_published)
                                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">Published</span>
                                    @else
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium">Draft</span>
                                    @endif
                                </td>
                                <td class="py-4 px-3 text-gray-600">
                                    @if($post->published_at)
                                        <span class="text-xs">{{ $post->published_at->format('M j, Y') }}</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="py-4 px-3">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($post->tags as $tag)
                                            <span class="px-2 py-1 bg-purple-100 text-purple-700 rounded text-xs font-medium">{{ $tag->name }}</span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('admin.blog.show', $post) }}" class="text-blue-600 hover:text-blue-700 transition-colors" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.blog.edit', $post) }}" class="text-green-600 hover:text-green-700 transition-colors" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($post->slug)
                                            <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="text-purple-600 hover:text-purple-700 transition-colors" title="View on Site">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        @endif
                                        <form method="POST" action="{{ route('admin.blog.destroy', $post) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-700 transition-colors" title="Delete" onclick="return confirm('Are you sure you want to delete this blog post?')">
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
                @foreach($posts as $post)
                    <div class="p-4 lg:p-6">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <div class="flex items-center mb-2">
                                    @if($post->featured_image)
                                        <img src="{{ Storage::url($post->featured_image) }}"
                                             alt="{{ $post->title }}"
                                             class="w-10 h-10 rounded-lg object-cover mr-3">
                                    @else
                                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                                            <i class="fas fa-blog text-gray-400"></i>
                                        </div>
                                    @endif
                                    <h3 class="font-medium text-gray-900">
                                        <a href="{{ route('admin.blog.show', $post) }}" class="hover:text-blue-600 transition-colors">
                                            {{ $post->title }}
                                        </a>
                                    </h3>
                                </div>
                                @if($post->excerpt)
                                    <p class="text-gray-500 text-sm mb-2">{{ Str::limit($post->excerpt, 100) }}</p>
                                @endif
                            </div>
                            <div class="ml-4">
                                @if($post->is_published)
                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">Published</span>
                                @else
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium">Draft</span>
                                @endif
                            </div>
                        </div>

                        @if($post->published_at)
                            <div class="text-sm text-gray-600 mb-3">
                                <i class="fas fa-calendar mr-1"></i>
                                Published {{ $post->published_at->format('M j, Y') }}
                            </div>
                        @endif

                        @if($post->tags->count() > 0)
                            <div class="flex flex-wrap gap-1 mb-3">
                                @foreach($post->tags as $tag)
                                    <span class="px-2 py-1 bg-purple-100 text-purple-700 rounded text-xs font-medium">{{ $tag->name }}</span>
                                @endforeach
                            </div>
                        @endif

                        <div class="flex items-center space-x-4">
                            <a href="{{ route('admin.blog.show', $post) }}" class="text-blue-600 hover:text-blue-700 transition-colors text-sm font-medium">
                                <i class="fas fa-eye mr-1"></i>View
                            </a>
                            <a href="{{ route('admin.blog.edit', $post) }}" class="text-green-600 hover:text-green-700 transition-colors text-sm font-medium">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </a>
                            @if($post->slug)
                                <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="text-purple-600 hover:text-purple-700 transition-colors text-sm font-medium">
                                    <i class="fas fa-external-link-alt mr-1"></i>View on Site
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($posts->hasPages())
                <div class="p-4 lg:p-6 border-t border-gray-100">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-sm text-gray-600 mb-4 sm:mb-0">
                            Showing {{ $posts->firstItem() }} to {{ $posts->lastItem() }} of {{ $posts->total() }} results
                        </p>
                        <div class="flex items-center space-x-2">
                            {{ $posts->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="p-8 lg:p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-blog text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No blog posts yet</h3>
                <p class="text-gray-600 mb-4">Start creating engaging content for your audience.</p>
                <a href="{{ route('admin.blog.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Create Your First Post
                </a>
            </div>
        @endif
    </div>
@endsection