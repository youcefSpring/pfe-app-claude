@extends('layouts.admin-modern')

@section('title', $blogPost->title)
@section('page-title', 'View Blog Post')

@section('content')
<!-- Header -->
<div class="flex flex-col lg:flex-row lg:justify-between lg:items-start gap-4 mb-6 lg:mb-8">
    <div class="flex-1">
        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-2">{{ $blogPost->title }}</h1>
        <div class="flex items-center gap-3 text-gray-600">
            @if($blogPost->is_published)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">Published</span>
            @else
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">Draft</span>
            @endif
            <span class="text-sm">
                @if($blogPost->published_at)
                    {{ $blogPost->published_at->format('M d, Y \a\t g:i A') }}
                @else
                    Created {{ $blogPost->created_at->format('M d, Y \a\t g:i A') }}
                @endif
            </span>
        </div>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.blog.edit', $blogPost) }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
            <i class="fas fa-edit mr-2"></i>Edit Post
        </a>
        <a href="{{ route('admin.blog.index') }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Back to Posts
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            @if($blogPost->featured_image)
                <div class="aspect-video">
                    <img src="{{ Storage::url($blogPost->featured_image) }}"
                         alt="{{ $blogPost->title }}"
                         class="w-full h-full object-cover"
                         style="max-height: 400px; object-fit: cover;">
                </div>
            @endif
            <div class="p-4 lg:p-6">
                @if($blogPost->excerpt)
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                        <h3 class="text-sm font-medium text-blue-800 mb-2">Excerpt</h3>
                        <p class="text-blue-700 italic">{{ $blogPost->excerpt }}</p>
                    </div>
                @endif

                <div class="prose max-w-none text-gray-700 leading-relaxed">
                    {!! nl2br(e($blogPost->content)) !!}
                </div>
            </div>
        </div>
    </div>

    <div class="lg:col-span-1 space-y-6">
        <!-- Post Details -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 lg:p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">Post Details</h3>
            </div>
            <div class="p-4 lg:p-6 space-y-4">
                <div>
                    <dt class="text-sm font-medium text-gray-900 mb-1">Status</dt>
                    <dd>
                        @if($blogPost->is_published)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">Published</span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">Draft</span>
                        @endif
                    </dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-900 mb-1">Slug</dt>
                    <dd class="text-sm text-gray-600 font-mono bg-gray-50 px-2 py-1 rounded">{{ $blogPost->slug }}</dd>
                </div>

                @if($blogPost->published_at)
                    <div>
                        <dt class="text-sm font-medium text-gray-900 mb-1">Published Date</dt>
                        <dd class="text-sm text-gray-600">{{ $blogPost->published_at->format('F j, Y \a\t g:i A') }}</dd>
                    </div>
                @endif

                <div>
                    <dt class="text-sm font-medium text-gray-900 mb-1">Created</dt>
                    <dd class="text-sm text-gray-600">{{ $blogPost->created_at->format('F j, Y \a\t g:i A') }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-900 mb-1">Last Updated</dt>
                    <dd class="text-sm text-gray-600">{{ $blogPost->updated_at->format('F j, Y \a\t g:i A') }}</dd>
                </div>

                @if($blogPost->is_featured)
                    <div>
                        <dt class="text-sm font-medium text-gray-900 mb-1">Featured</dt>
                        <dd>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-star mr-1"></i>Featured Post
                            </span>
                        </dd>
                    </div>
                @endif
            </div>
        </div>

        @if($blogPost->tags->count() > 0)
            <!-- Tags -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 lg:p-6 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Tags</h3>
                </div>
                <div class="p-4 lg:p-6">
                    <div class="flex flex-wrap gap-2">
                        @foreach($blogPost->tags as $tag)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">{{ $tag->name }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Actions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 lg:p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">Actions</h3>
            </div>
            <div class="p-4 lg:p-6">
                <div class="flex flex-col gap-3">
                    @if($blogPost->is_published)
                        <a href="{{ route('public.blog.show', $blogPost->slug) }}"
                           class="inline-flex items-center justify-center px-4 py-2 border border-blue-300 text-blue-700 text-sm font-medium rounded-lg hover:bg-blue-50 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors" target="_blank">
                            <i class="fas fa-eye mr-2"></i>View on Site
                        </a>
                    @endif

                    <a href="{{ route('admin.blog.edit', $blogPost) }}"
                       class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                        <i class="fas fa-edit mr-2"></i>Edit Post
                    </a>

                    <form method="POST" action="{{ route('admin.blog.destroy', $blogPost) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center justify-center w-full px-4 py-2 border border-red-300 text-red-700 text-sm font-medium rounded-lg hover:bg-red-50 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors"
                                data-confirm-delete>
                            <i class="fas fa-trash mr-2"></i>Delete Post
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection