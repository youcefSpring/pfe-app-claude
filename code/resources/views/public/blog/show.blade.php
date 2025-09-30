@extends('layouts.app')

@section('title', $post->title ?? 'Blog Post')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('home') }}" class="text-white text-decoration-none">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('blog.index') }}" class="text-white text-decoration-none">Blog</a>
                        </li>
                        <li class="breadcrumb-item active text-white-50" aria-current="page">
                            {{ Str::limit($post->title ?? 'Post', 50) }}
                        </li>
                    </ol>
                </nav>

                <!-- Post Meta -->
                <div class="mb-3">
                    @if($post->category ?? false)
                        <span class="badge bg-warning text-dark me-2">{{ $post->category->name }}</span>
                    @endif
                    <span class="text-white-50">
                        <i class="bi bi-calendar me-1"></i>
                        {{ $post->published_at ? $post->published_at->format('F j, Y') : $post->created_at->format('F j, Y') }}
                    </span>
                    @if($post->reading_time ?? false)
                        <span class="text-white-50 ms-3">
                            <i class="bi bi-clock me-1"></i>{{ $post->reading_time }} min read
                        </span>
                    @endif
                </div>

                <!-- Title -->
                <h1 class="display-5 fw-bold mb-4">{{ $post->title ?? 'Blog Post Title' }}</h1>

                <!-- Excerpt -->
                @if($post->excerpt ?? false)
                    <p class="lead mb-4">{{ $post->excerpt }}</p>
                @endif

                <!-- Author and Stats -->
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        @if($teacher->avatar ?? false)
                            <img src="{{ Storage::url($teacher->avatar) }}"
                                 alt="{{ $teacher->name ?? 'Author' }}"
                                 class="rounded-circle me-3"
                                 style="width: 50px; height: 50px; object-fit: cover;">
                        @endif
                        <div>
                            <h6 class="mb-0 text-white">{{ $teacher->name ?? 'Dr. [Your Name]' }}</h6>
                            <small class="text-white-50">{{ $teacher->title ?? 'Professor' }}</small>
                        </div>
                    </div>
                    <div class="text-white-50">
                        <small>
                            <i class="bi bi-eye me-1"></i>{{ $post->views_count ?? 0 }} views
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Post Content -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <article class="card shadow-sm border-0 mb-5">
                    @if($post->featured_image ?? false)
                        <img src="{{ Storage::url($post->featured_image) }}"
                             class="card-img-top" alt="{{ $post->title }}"
                             style="height: 400px; object-fit: cover;">
                    @endif

                    <div class="card-body p-5">
                        <!-- Post Content -->
                        <div class="post-content">
                            @if($post->content ?? false)
                                {!! $post->content !!}
                            @else
                                <p>This is where the blog post content would appear. The content is stored in the database and would be displayed here when available.</p>

                                <p>Blog posts can include rich text formatting, images, code snippets, and academic references. The content management system supports full HTML rendering for comprehensive academic writing.</p>

                                <h3>Sample Section</h3>
                                <p>Academic blog posts often explore complex topics in an accessible way, bridging the gap between scholarly research and public understanding. They may include:</p>

                                <ul>
                                    <li>Research insights and findings</li>
                                    <li>Teaching methodologies and experiences</li>
                                    <li>Conference reflections and takeaways</li>
                                    <li>Commentary on current developments in the field</li>
                                </ul>

                                <blockquote class="blockquote">
                                    <p class="mb-0">"Education is the most powerful weapon which you can use to change the world."</p>
                                    <footer class="blockquote-footer">Nelson Mandela</footer>
                                </blockquote>
                            @endif
                        </div>

                        <!-- Tags -->
                        @if(isset($post->tags) && $post->tags->count() > 0)
                            <div class="mt-5 pt-4 border-top">
                                <h6 class="mb-3">Tags:</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($post->tags as $tag)
                                        <a href="{{ route('blog.index', ['tag' => $tag->slug]) }}"
                                           class="badge bg-light text-dark text-decoration-none">
                                            #{{ $tag->name }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Share Buttons -->
                        <div class="mt-5 pt-4 border-top">
                            <h6 class="mb-3">Share this post:</h6>
                            <div class="d-flex gap-2">
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($post->title) }}"
                                   target="_blank" rel="noopener"
                                   class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-twitter me-1"></i>Twitter
                                </a>
                                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->url()) }}"
                                   target="_blank" rel="noopener"
                                   class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-linkedin me-1"></i>LinkedIn
                                </a>
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}"
                                   target="_blank" rel="noopener"
                                   class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-facebook me-1"></i>Facebook
                                </a>
                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                        onclick="navigator.clipboard.writeText('{{ request()->url() }}'); alert('Link copied to clipboard!')">
                                    <i class="bi bi-link me-1"></i>Copy Link
                                </button>
                            </div>
                        </div>
                    </div>
                </article>

                <!-- Author Bio -->
                <div class="card shadow-sm border-0 mb-5">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-3 text-center mb-3 mb-md-0">
                                @if($teacher->avatar ?? false)
                                    <img src="{{ Storage::url($teacher->avatar) }}"
                                         alt="{{ $teacher->name ?? 'Author' }}"
                                         class="rounded-circle"
                                         style="width: 100px; height: 100px; object-fit: cover;">
                                @else
                                    <div class="bg-light rounded-circle mx-auto d-flex align-items-center justify-content-center"
                                         style="width: 100px; height: 100px;">
                                        <i class="bi bi-person-circle text-muted" style="font-size: 3rem;"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-9">
                                <h5 class="mb-2">About {{ $teacher->name ?? 'the Author' }}</h5>
                                <p class="text-muted mb-3">
                                    {{ $teacher->bio ?? 'Dedicated educator and researcher passionate about sharing knowledge and advancing understanding in the field through innovative teaching methods and cutting-edge research.' }}
                                </p>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('about') }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-person me-1"></i>View Profile
                                    </a>
                                    @if($teacher->email ?? false)
                                        <a href="mailto:{{ $teacher->email }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-envelope me-1"></i>Contact
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="row">
                    @if(isset($previousPost))
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 border-0 bg-light">
                                <div class="card-body">
                                    <small class="text-muted">Previous Post</small>
                                    <h6 class="mt-2">
                                        <a href="{{ route('blog.show', $previousPost->slug) }}"
                                           class="text-decoration-none">
                                            <i class="bi bi-arrow-left me-1"></i>{{ $previousPost->title }}
                                        </a>
                                    </h6>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(isset($nextPost))
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 border-0 bg-light">
                                <div class="card-body text-end">
                                    <small class="text-muted">Next Post</small>
                                    <h6 class="mt-2">
                                        <a href="{{ route('blog.show', $nextPost->slug) }}"
                                           class="text-decoration-none">
                                            {{ $nextPost->title }}<i class="bi bi-arrow-right ms-1"></i>
                                        </a>
                                    </h6>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Table of Contents -->
                <div class="card shadow-sm border-0 mb-4 sticky-top" style="top: 2rem;">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="bi bi-list-ul me-2"></i>Table of Contents</h6>
                    </div>
                    <div class="card-body">
                        <div id="table-of-contents">
                            <!-- This would be dynamically generated from the post content -->
                            <ul class="list-unstyled">
                                <li><a href="#introduction" class="text-decoration-none">Introduction</a></li>
                                <li><a href="#main-content" class="text-decoration-none">Main Content</a></li>
                                <li><a href="#conclusion" class="text-decoration-none">Conclusion</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Related Posts -->
                @if(isset($relatedPosts) && $relatedPosts->count() > 0)
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="bi bi-journals me-2"></i>Related Posts</h6>
                    </div>
                    <div class="card-body p-0">
                        @foreach($relatedPosts->take(3) as $relatedPost)
                            <div class="p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <h6 class="mb-2">
                                    <a href="{{ route('blog.show', $relatedPost->slug) }}"
                                       class="text-decoration-none">
                                        {{ $relatedPost->title }}
                                    </a>
                                </h6>
                                <small class="text-muted">
                                    <i class="bi bi-calendar me-1"></i>
                                    {{ $relatedPost->published_at ? $relatedPost->published_at->format('M d, Y') : $relatedPost->created_at->format('M d, Y') }}
                                </small>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Recent Posts -->
                @if(isset($recentPosts) && $recentPosts->count() > 0)
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="bi bi-clock me-2"></i>Recent Posts</h6>
                    </div>
                    <div class="card-body p-0">
                        @foreach($recentPosts->take(3) as $recentPost)
                            @if($recentPost->id !== $post->id)
                                <div class="p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                    <h6 class="mb-2">
                                        <a href="{{ route('blog.show', $recentPost->slug) }}"
                                           class="text-decoration-none">
                                            {{ $recentPost->title }}
                                        </a>
                                    </h6>
                                    <small class="text-muted">
                                        <i class="bi bi-calendar me-1"></i>
                                        {{ $recentPost->published_at ? $recentPost->published_at->format('M d, Y') : $recentPost->created_at->format('M d, Y') }}
                                    </small>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Categories -->
                @if(isset($categories) && $categories->count() > 0)
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-warning text-white">
                        <h6 class="mb-0"><i class="bi bi-folder me-2"></i>Categories</h6>
                    </div>
                    <div class="card-body">
                        @foreach($categories->take(5) as $category)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <a href="{{ route('blog.index', ['category' => $category->slug]) }}"
                                   class="text-decoration-none">
                                    {{ $category->name }}
                                </a>
                                <span class="badge bg-light text-dark">{{ $category->posts_count ?? 0 }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Newsletter Signup -->
<section class="py-5 bg-light">
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
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-envelope me-2"></i>Subscribe
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection