@extends('layouts.app')

@section('title', 'Publications')

@section('content')
<!-- Header Section -->
<section class="py-5 bg-primary text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-5 fw-bold">Publications</h1>
                <p class="lead mb-0">Explore my research publications, academic papers, and scholarly contributions.</p>
            </div>
            <div class="col-lg-4 text-center">
                <i class="bi bi-journal-text" style="font-size: 6rem; opacity: 0.7;"></i>
            </div>
        </div>
    </div>
</section>

<!-- Publications Content -->
<section class="py-5">
    <div class="container">
        <!-- Search and Filter -->
        <div class="row mb-4">
            <div class="col-md-8">
                <form method="GET" action="{{ route('publications.index') }}" class="d-flex gap-2">
                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Search by title, authors, journal, or keywords..."
                           value="{{ request('search') }}">
                    <select name="type" class="form-select" style="max-width: 200px;">
                        <option value="">All Types</option>
                        <option value="article" {{ request('type') === 'article' ? 'selected' : '' }}>Journal Articles</option>
                        <option value="conference_paper" {{ request('type') === 'conference_paper' ? 'selected' : '' }}>Conference Papers</option>
                        <option value="book" {{ request('type') === 'book' ? 'selected' : '' }}>Books</option>
                        <option value="thesis" {{ request('type') === 'thesis' ? 'selected' : '' }}>Thesis</option>
                        <option value="report" {{ request('type') === 'report' ? 'selected' : '' }}>Reports</option>
                        <option value="other" {{ request('type') === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-search"></i>
                    </button>
                    @if(request('search') || request('type'))
                        <a href="{{ route('publications.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </form>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="btn-group" role="group">
                    <input type="radio" class="btn-check" name="sort" id="newest" {{ !request('sort') || request('sort') === 'newest' ? 'checked' : '' }}>
                    <label class="btn btn-outline-secondary btn-sm" for="newest">Newest</label>

                    <input type="radio" class="btn-check" name="sort" id="oldest" {{ request('sort') === 'oldest' ? 'checked' : '' }}>
                    <label class="btn btn-outline-secondary btn-sm" for="oldest">Oldest</label>

                    <input type="radio" class="btn-check" name="sort" id="citations" {{ request('sort') === 'citations' ? 'checked' : '' }}>
                    <label class="btn btn-outline-secondary btn-sm" for="citations">Citations</label>
                </div>
            </div>
        </div>

        @if(request('search') || request('type'))
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                @if(request('search'))
                    Showing results for "<strong>{{ request('search') }}</strong>"
                @endif
                @if(request('type'))
                    @if(request('search')) in @endif
                    <strong>{{ ucwords(str_replace('_', ' ', request('type'))) }}</strong>
                @endif
                - {{ $publications->total() }} publication(s) found
            </div>
        @endif

        <!-- Publications List -->
        @if($publications->count() > 0)
            <div class="row g-4">
                @foreach($publications as $publication)
                    <div class="col-12">
                        <div class="card publication-card">
                            <div class="card-body p-4">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="flex-grow-1">
                                                <h5 class="card-title mb-2">
                                                    <span class="text-dark">
                                                        {{ $publication->title }}
                                                    </span>
                                                </h5>

                                                <div class="d-flex flex-wrap gap-2 mb-2">
                                                    @if($publication->type === 'article')
                                                        <span class="badge bg-primary">Journal Article</span>
                                                    @elseif($publication->type === 'conference_paper')
                                                        <span class="badge bg-success">Conference Paper</span>
                                                    @elseif($publication->type === 'book')
                                                        <span class="badge bg-warning">Book</span>
                                                    @elseif($publication->type === 'thesis')
                                                        <span class="badge bg-info">Thesis</span>
                                                    @elseif($publication->type === 'report')
                                                        <span class="badge bg-secondary">Report</span>
                                                    @else
                                                        <span class="badge bg-light text-dark">Other</span>
                                                    @endif

                                                    @if($publication->status === 'published')
                                                        <span class="badge bg-success">Published</span>
                                                    @else
                                                        <span class="badge bg-warning">Draft</span>
                                                    @endif
                                                </div>

                                                <p class="text-muted mb-2">
                                                    <strong>Authors:</strong> {{ $publication->authors }}
                                                </p>

                                                @if($publication->journal)
                                                    <p class="text-muted mb-2">
                                                        <strong>Published in:</strong> {{ $publication->journal }}
                                                        @if($publication->volume || $publication->issue || $publication->pages)
                                                            <span class="text-secondary">
                                                                @if($publication->volume), Vol. {{ $publication->volume }}@endif
                                                                @if($publication->issue), Issue {{ $publication->issue }}@endif
                                                                @if($publication->pages), pp. {{ $publication->pages }}@endif
                                                            </span>
                                                        @endif
                                                    </p>
                                                @endif

                                                @if($publication->abstract)
                                                    <p class="text-muted">
                                                        {{ Str::limit($publication->abstract, 200) }}
                                                    </p>
                                                @elseif($publication->description)
                                                    <p class="text-muted">
                                                        {{ Str::limit($publication->description, 200) }}
                                                    </p>
                                                @endif

                                                <!-- Keywords -->
                                                @if($publication->keywords)
                                                    <div class="mt-2">
                                                        <small class="text-muted">
                                                            <strong>Keywords:</strong>
                                                            @foreach(explode(',', $publication->keywords) as $keyword)
                                                                <span class="badge bg-light text-dark border me-1">{{ trim($keyword) }}</span>
                                                            @endforeach
                                                        </small>
                                                    </div>
                                                @endif

                                                <!-- Tags -->
                                                @if($publication->tags && $publication->tags->count() > 0)
                                                    <div class="mt-2">
                                                        @foreach($publication->tags as $tag)
                                                            <span class="badge bg-secondary me-1">{{ $tag->name }}</span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 text-md-end">
                                        <div class="publication-meta">
                                            <div class="mb-3">
                                                <div class="text-muted small mb-1">Publication Year</div>
                                                <div class="fw-bold">{{ $publication->year }}</div>
                                            </div>

                                            @if($publication->citation_count && $publication->citation_count > 0)
                                                <div class="mb-3">
                                                    <div class="text-muted small mb-1">Citations</div>
                                                    <div class="fw-bold text-success">{{ $publication->citation_count }}</div>
                                                </div>
                                            @endif

                                            @if($publication->doi)
                                                <div class="mb-3">
                                                    <div class="text-muted small mb-1">DOI</div>
                                                    <div class="small">
                                                        <a href="https://doi.org/{{ $publication->doi }}" target="_blank" class="text-decoration-none">
                                                            {{ Str::limit($publication->doi, 20) }}
                                                        </a>
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="d-grid gap-2">
                                                <button class="btn btn-primary btn-sm" onclick="alert('Publication: {{ $publication->title }}')">
                                                    <i class="bi bi-eye me-1"></i>View Details
                                                </button>

                                                @if($publication->publication_file_path)
                                                    <button class="btn btn-outline-secondary btn-sm" onclick="alert('PDF download for: {{ $publication->title }}')">
                                                        <i class="bi bi-file-earmark-pdf me-1"></i>Download PDF
                                                    </button>
                                                @endif

                                                @if($publication->external_url)
                                                    <a href="{{ $publication->external_url }}" target="_blank" class="btn btn-outline-info btn-sm">
                                                        <i class="bi bi-link-45deg me-1"></i>External Link
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($publications->hasPages())
                <div class="d-flex justify-content-center mt-5">
                    {{ $publications->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-5">
                <i class="bi bi-journal-text text-muted mb-3" style="font-size: 4rem;"></i>

                @if(request('search') || request('type'))
                    <h3 class="h4 text-muted mb-3">No publications found</h3>
                    <p class="text-muted mb-4">
                        No publications match your search criteria. Try adjusting your filters.
                    </p>
                    <a href="{{ route('publications.index') }}" class="btn btn-primary">
                        <i class="bi bi-arrow-left me-1"></i>View All Publications
                    </a>
                @else
                    <h3 class="h4 text-muted mb-3">No publications available yet</h3>
                    <p class="text-muted">
                        Research publications and academic papers will be available here once added.
                    </p>
                @endif
            </div>
        @endif

        <!-- Research Areas Info -->
        @if($publications->count() > 0)
            <div class="row mt-5">
                <div class="col-12">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="bi bi-lightbulb text-primary me-2"></i>
                                Research Areas
                            </h5>
                            <div class="row g-4">
                                <div class="col-md-3">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-cpu text-primary me-2 mt-1"></i>
                                        <div>
                                            <h6 class="mb-1">Computer Science</h6>
                                            <small class="text-muted">Software engineering and systems</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-graph-up text-success me-2 mt-1"></i>
                                        <div>
                                            <h6 class="mb-1">Data Science</h6>
                                            <small class="text-muted">Analytics and machine learning</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-mortarboard text-info me-2 mt-1"></i>
                                        <div>
                                            <h6 class="mb-1">Educational Technology</h6>
                                            <small class="text-muted">Learning systems and pedagogy</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-gear text-warning me-2 mt-1"></i>
                                        <div>
                                            <h6 class="mb-1">Applied Research</h6>
                                            <small class="text-muted">Industry applications and solutions</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection

@section('styles')
<style>
    .publication-card {
        border: 1px solid #e5e7eb;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .publication-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .publication-meta {
        border-left: 1px solid #e5e7eb;
        padding-left: 1rem;
    }

    .badge {
        font-size: 0.75rem;
    }

    .btn-check:checked + .btn {
        background-color: #2563eb;
        border-color: #2563eb;
        color: white;
    }

    @media (max-width: 768px) {
        .publication-meta {
            border-left: none;
            border-top: 1px solid #e5e7eb;
            padding-left: 0;
            padding-top: 1rem;
            margin-top: 1rem;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle sort buttons
        const sortButtons = document.querySelectorAll('input[name="sort"]');
        sortButtons.forEach(function(button) {
            button.addEventListener('change', function() {
                const url = new URL(window.location);
                if (this.id === 'newest') {
                    url.searchParams.delete('sort');
                } else {
                    url.searchParams.set('sort', this.id);
                }
                window.location.href = url.toString();
            });
        });
    });
</script>
@endsection