@extends('layouts.app')

@section('title', 'Blog')

@section('content')
<!-- Header Section -->
<section class="py-5 bg-primary text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-5 fw-bold">Blog</h1>
                <p class="lead mb-0">Thoughts, insights, and reflections on teaching, research, and technology.</p>
            </div>
            <div class="col-lg-4 text-center">
                <i class="bi bi-pencil-square" style="font-size: 6rem; opacity: 0.7;"></i>
            </div>
        </div>
    </div>
</section>

<!-- Blog Content -->
<section class="py-5">
    <div class="container">
        <!-- Search and Filter -->
        <div class="row mb-4">
            <div class="col-md-8">
                <form method="GET" action="{{ route('blog.index') }}" class="d-flex gap-2">
                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Search blog posts by title or content..."
                           value="{{ request('search') }}">
                    <select name="tag" class="form-select" style="max-width: 200px;">
                        <option value="">All Tags</option>
                        @foreach($availableTags ?? [] as $tag)
                            <option value="{{ $tag->slug }}" {{ request('tag') === $tag->slug ? 'selected' : '' }}>
                                {{ $tag->name }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-search"></i>
                    </button>
                    @if(request('search') || request('tag'))
                        <a href="{{ route('blog.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </form>
            </div>
            <div class="col-md-4 text-md-end">
                <span class="text-muted">{{ $posts->total() }} post(s) found</span>
            </div>
        </div>

        @if(request('search') || request('tag'))
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                @if(request('search'))
                    Showing results for "<strong>{{ request('search') }}</strong>"
                @endif
                @if(request('tag'))
                    @if(request('search')) with tag @else Showing posts tagged @endif
                    <strong>{{ $availableTags->where('slug', request('tag'))->first()?->name ?? request('tag') }}</strong>
                @endif
            </div>
        @endif

        <!-- Blog Posts -->
        @if($posts->count() > 0)
            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-8">
                    @foreach($posts as $post)
                        <article class="card blog-card mb-4">
                            @if($post->featured_image)
                                <div class="blog-image-container">
                                    <img src="{{ asset('storage/' . $post->featured_image) }}"
                                         class="card-img-top blog-image"
                                         alt="{{ $post->title }}"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
                                    <div class="blog-placeholder" style="display: none;">
                                        <i class="bi bi-file-text text-muted"></i>
                                    </div>
                                </div>
                            @endif

                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <small class="text-muted">
                                        <i class="bi bi-calendar me-1"></i>
                                        {{ $post->created_at->format('M j, Y') }}
                                        @if($post->created_at->diffInDays() <= 7)
                                            <span class="badge bg-warning ms-2">New</span>
                                        @endif
                                    </small>

                                    @if($post->status === 'published')
                                        <span class="badge bg-success">Published</span>
                                    @else
                                        <span class="badge bg-secondary">Draft</span>
                                    @endif
                                </div>

                                <h2 class="h4 card-title mb-3">
                                    <a href="{{ route('blog.show', $post->slug) }}" class="text-decoration-none">
                                        {{ $post->title }}
                                    </a>
                                </h2>

                                @if($post->excerpt)
                                    <p class="card-text text-muted mb-3">
                                        {{ $post->excerpt }}
                                    </p>
                                @else
                                    <p class="card-text text-muted mb-3">
                                        {{ Str::limit(strip_tags($post->content), 200) }}
                                    </p>
                                @endif

                                <!-- Tags -->
                                @if($post->tags && $post->tags->count() > 0)
                                    <div class="mb-3">
                                        @foreach($post->tags as $tag)
                                            <a href="{{ route('blog.index', ['tag' => $tag->slug]) }}"
                                               class="badge bg-secondary text-decoration-none me-1">
                                                {{ $tag->name }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="{{ route('blog.show', $post->slug) }}" class="btn btn-primary">
                                        <i class="bi bi-book-open me-1"></i>Read More
                                    </a>

                                    <small class="text-muted">
                                        <i class="bi bi-clock me-1"></i>
                                        {{ $post->reading_time ?? ceil(str_word_count(strip_tags($post->content)) / 200) }} min read
                                    </small>
                                </div>
                            </div>
                        </article>
                    @endforeach

                    <!-- Pagination -->
                    @if($posts->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $posts->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Search Widget -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="bi bi-search text-primary me-2"></i>
                                Search Posts
                            </h5>
                            <form method="GET" action="{{ route('blog.index') }}">
                                <div class="input-group">
                                    <input type="text"
                                           name="search"
                                           class="form-control"
                                           placeholder="Search..."
                                           value="{{ request('search') }}">
                                    <button class="btn btn-outline-primary" type="submit">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Recent Posts -->
                    @if($recentPosts ?? null)
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="bi bi-clock-history text-primary me-2"></i>
                                    Recent Posts
                                </h5>
                                @foreach($recentPosts as $recentPost)
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">
                                                <a href="{{ route('blog.show', $recentPost->slug) }}" class="text-decoration-none">
                                                    {{ Str::limit($recentPost->title, 50) }}
                                                </a>
                                            </h6>
                                            <small class="text-muted">{{ $recentPost->created_at->format('M j, Y') }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Popular Tags -->
                    @if($popularTags ?? null)
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="bi bi-tags text-primary me-2"></i>
                                    Popular Tags
                                </h5>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($popularTags as $tag)
                                        <a href="{{ route('blog.index', ['tag' => $tag->slug]) }}"
                                           class="badge bg-light text-dark border text-decoration-none">
                                            {{ $tag->name }}
                                            @if($tag->posts_count ?? null)
                                                <span class="text-muted">({{ $tag->posts_count }})</span>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Archive -->
                    @if($archive ?? null)
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="bi bi-archive text-primary me-2"></i>
                                    Archive
                                </h5>
                                @foreach($archive as $month)
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <a href="{{ route('blog.index', ['month' => $month->month, 'year' => $month->year]) }}"
                                           class="text-decoration-none">
                                            {{ \Carbon\Carbon::create($month->year, $month->month)->format('F Y') }}
                                        </a>
                                        <span class="badge bg-light text-dark">{{ $month->count }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- About -->
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="bi bi-person text-primary me-2"></i>
                                About This Blog
                            </h5>
                            <p class="text-muted mb-3">
                                Welcome to my blog where I share insights on education, technology,
                                research, and life as an academic. Join the conversation and feel
                                free to share your thoughts.
                            </p>
                            <div class="d-grid gap-2">
                                <a href="{{ route('about') }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-person me-1"></i>About Me
                                </a>
                                <a href="{{ route('contact.show') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-envelope me-1"></i>Contact
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-5">
                <i class="bi bi-pencil-square text-muted mb-3" style="font-size: 4rem;"></i>

                @if(request('search') || request('tag'))
                    <h3 class="h4 text-muted mb-3">No posts found</h3>
                    <p class="text-muted mb-4">
                        No blog posts match your search criteria. Try adjusting your filters.
                    </p>
                    <a href="{{ route('blog.index') }}" class="btn btn-primary">
                        <i class="bi bi-arrow-left me-1"></i>View All Posts
                    </a>
                @else
                    <h3 class="h4 text-muted mb-3">No blog posts yet</h3>
                    <p class="text-muted mb-4">
                        Blog posts will appear here once they are published. Check back soon!
                    </p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ route('home') }}" class="btn btn-primary">
                            <i class="bi bi-house me-1"></i>Back to Home
                        </a>
                        <a href="{{ route('publications.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-journal-text me-1"></i>View Publications
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>
</section>
@endsection

@section('styles')
<style>
    .blog-card {
        border: 1px solid #e5e7eb;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
    }

    .blog-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .blog-image-container {
        height: 250px;
        overflow: hidden;
        position: relative;
    }

    .blog-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .blog-card:hover .blog-image {
        transform: scale(1.05);
    }

    .blog-placeholder {
        height: 250px;
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 4rem;
    }

    .card-title a {
        color: inherit;
        transition: color 0.3s ease;
    }

    .card-title a:hover {
        color: #2563eb;
    }

    .badge {
        font-size: 0.75rem;
        transition: all 0.3s ease;
    }

    .badge:hover {
        transform: scale(1.05);
    }

    article {
        scroll-margin-top: 2rem;
    }

    .sidebar .card {
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
</style>
@endsection