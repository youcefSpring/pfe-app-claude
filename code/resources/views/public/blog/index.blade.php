@extends('layouts.app')

@section('title', 'Blog')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-4">Academic Blog</h1>
                <p class="lead mb-4">
                    Thoughts on education, research insights, and academic perspectives.
                    Exploring ideas and sharing knowledge in my field of expertise.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Search and Filters -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <form method="GET" action="{{ route('blog.index') }}" class="row g-3 align-items-end">
                    <!-- Search -->
                    <div class="col-md-4">
                        <label for="search" class="form-label">Search Posts</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="search" name="search"
                                   value="{{ request('search') }}" placeholder="Search titles, content...">
                            <button class="btn btn-outline-primary" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Category Filter -->
                    <div class="col-md-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select" id="category" name="category">
                            <option value="">All Categories</option>
                            @if(isset($categories))
                                @foreach($categories as $category)
                                    <option value="{{ $category->slug }}"
                                            {{ request('category') == $category->slug ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            @else
                                <option value="research">Research</option>
                                <option value="teaching">Teaching</option>
                                <option value="technology">Technology</option>
                                <option value="academic-life">Academic Life</option>
                                <option value="conference">Conference</option>
                                <option value="publication">Publication</option>
                            @endif
                        </select>
                    </div>

                    <!-- Sort By -->
                    <div class="col-md-3">
                        <label for="sort" class="form-label">Sort By</label>
                        <select class="form-select" id="sort" name="sort">
                            <option value="latest" {{ request('sort') == 'latest' || !request('sort') ? 'selected' : '' }}>
                                Latest First
                            </option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>
                                Oldest First
                            </option>
                            <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>
                                Most Popular
                            </option>
                            <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>
                                Title A-Z
                            </option>
                        </select>
                    </div>

                    <!-- Filter Actions -->
                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="{{ route('blog.index') }}" class="btn btn-outline-secondary">Clear</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Blog Posts -->
<section class="py-5 bg-white">
    <div class="container">
        @if(isset($posts) && $posts->count() > 0)
            <!-- Results Summary -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3>Blog Posts</h3>
                        <span class="text-muted">
                            {{ $posts->total() }} {{ Str::plural('post', $posts->total()) }} found
                            @if(request('search'))
                                for "{{ request('search') }}"
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Posts Grid -->
            <div class="row">
                @foreach($posts as $post)
                <div class="col-lg-4 col-md-6 mb-4">
                    <article class="card h-100 shadow-sm border-0">
                        @if($post->featured_image)
                            <img src="{{ Storage::url($post->featured_image) }}"
                                 class="card-img-top" alt="{{ $post->title }}"
                                 style="height: 200px; object-fit: cover;">
                        @else
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center"
                                 style="height: 200px;">
                                <i class="bi bi-file-text text-muted" style="font-size: 3rem;"></i>
                            </div>
                        @endif

                        <div class="card-body d-flex flex-column">
                            <!-- Meta Info -->
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                @if($post->category)
                                    <span class="badge bg-warning text-dark">{{ $post->category->name }}</span>
                                @endif
                                <small class="text-muted">
                                    <i class="bi bi-calendar me-1"></i>
                                    {{ $post->published_at ? $post->published_at->format('M d, Y') : $post->created_at->format('M d, Y') }}
                                </small>
                            </div>

                            <!-- Title and Excerpt -->
                            <h5 class="card-title">
                                <a href="{{ route('blog.show', $post->slug) }}" class="text-decoration-none text-dark">
                                    {{ $post->title }}
                                </a>
                            </h5>

                            <p class="card-text flex-grow-1">
                                {{ $post->excerpt ?? Str::limit(strip_tags($post->content), 120) }}
                            </p>

                            <!-- Post Meta -->
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <div class="d-flex align-items-center text-muted">
                                    <small>
                                        <i class="bi bi-eye me-1"></i>{{ $post->views_count ?? 0 }} views
                                    </small>
                                    @if($post->reading_time)
                                        <small class="ms-3">
                                            <i class="bi bi-clock me-1"></i>{{ $post->reading_time }} min read
                                        </small>
                                    @endif
                                </div>
                                <a href="{{ route('blog.show', $post->slug) }}" class="btn btn-sm btn-outline-primary">
                                    Read More <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>

                            <!-- Tags -->
                            @if($post->tags && $post->tags->count() > 0)
                                <div class="mt-3">
                                    @foreach($post->tags->take(3) as $tag)
                                        <span class="badge bg-light text-dark me-1">#{{ $tag->name }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </article>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-center">
                        {{ $posts->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        @else
            <!-- No Posts Found -->
            <div class="row">
                <div class="col-12 text-center py-5">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-5">
                            <i class="bi bi-file-text text-muted" style="font-size: 4rem;"></i>
                            <h3 class="mt-3">No Blog Posts Found</h3>
                            @if(request()->hasAny(['search', 'category', 'sort']))
                                <p class="text-muted mb-4">
                                    No posts match your current filters. Try adjusting your search criteria.
                                </p>
                                <a href="{{ route('blog.index') }}" class="btn btn-primary">
                                    <i class="bi bi-arrow-left me-2"></i>View All Posts
                                </a>
                            @else
                                <p class="text-muted mb-4">
                                    I haven't published any blog posts yet. Check back soon for new content!
                                </p>
                                <a href="{{ route('home') }}" class="btn btn-primary">
                                    <i class="bi bi-house me-2"></i>Back to Home
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>

<!-- Featured Categories -->
@if(isset($categories) && $categories->count() > 0)
<section class="py-5 bg-light">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h3>Explore by Category</h3>
                <p class="text-muted">Browse posts by topic area</p>
            </div>
        </div>
        <div class="row justify-content-center">
            @foreach($categories->take(6) as $category)
            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3">
                <a href="{{ route('blog.index', ['category' => $category->slug]) }}"
                   class="text-decoration-none">
                    <div class="card text-center h-100 shadow-sm border-0">
                        <div class="card-body py-3">
                            <i class="bi bi-folder text-primary" style="font-size: 2rem;"></i>
                            <h6 class="card-title mt-2 mb-1">{{ $category->name }}</h6>
                            <small class="text-muted">{{ $category->posts_count ?? 0 }} posts</small>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Newsletter Signup -->
<section class="py-5 bg-primary text-white">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 text-center">
                <h3 class="mb-3">Stay Updated</h3>
                <p class="mb-4">
                    Subscribe to get notified about new blog posts and academic updates.
                </p>
                <form method="POST" action="{{ route('newsletter.subscribe') }}" class="row g-2">
                    @csrf
                    <div class="col-md-8">
                        <input type="email" class="form-control" name="email"
                               placeholder="Enter your email address" required>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-light w-100">
                            <i class="bi bi-envelope me-2"></i>Subscribe
                        </button>
                    </div>
                </form>
                <small class="mt-2 d-block opacity-75">
                    <i class="bi bi-shield-check me-1"></i>
                    Your email is safe and will never be shared.
                </small>
            </div>
        </div>
    </div>
</section>
@endsection