@extends('layouts.app')

@section('title', 'Publications')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-4">Publications & Research</h1>
                <p class="lead mb-4">
                    Explore my scholarly contributions to the academic community. From peer-reviewed articles to conference proceedings,
                    these publications represent my ongoing research and commitment to advancing knowledge in my field.
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
                <form method="GET" action="{{ route('publications.index') }}" class="row g-3 align-items-end">
                    <!-- Search -->
                    <div class="col-md-4">
                        <label for="search" class="form-label">Search Publications</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="search" name="search"
                                   value="{{ request('search') }}" placeholder="Search titles, abstracts...">
                            <button class="btn btn-outline-primary" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Type Filter -->
                    <div class="col-md-2">
                        <label for="type" class="form-label">Type</label>
                        <select class="form-select" id="type" name="type">
                            <option value="">All Types</option>
                            <option value="journal" {{ request('type') == 'journal' ? 'selected' : '' }}>
                                Journal Article
                            </option>
                            <option value="conference" {{ request('type') == 'conference' ? 'selected' : '' }}>
                                Conference Paper
                            </option>
                            <option value="book" {{ request('type') == 'book' ? 'selected' : '' }}>
                                Book
                            </option>
                            <option value="chapter" {{ request('type') == 'chapter' ? 'selected' : '' }}>
                                Book Chapter
                            </option>
                            <option value="thesis" {{ request('type') == 'thesis' ? 'selected' : '' }}>
                                Thesis/Dissertation
                            </option>
                            <option value="preprint" {{ request('type') == 'preprint' ? 'selected' : '' }}>
                                Preprint
                            </option>
                        </select>
                    </div>

                    <!-- Year Filter -->
                    <div class="col-md-2">
                        <label for="year" class="form-label">Year</label>
                        <select class="form-select" id="year" name="year">
                            <option value="">All Years</option>
                            @for($y = date('Y'); $y >= date('Y') - 10; $y--)
                                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <!-- Sort By -->
                    <div class="col-md-2">
                        <label for="sort" class="form-label">Sort By</label>
                        <select class="form-select" id="sort" name="sort">
                            <option value="latest" {{ request('sort') == 'latest' || !request('sort') ? 'selected' : '' }}>
                                Latest First
                            </option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>
                                Oldest First
                            </option>
                            <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>
                                Title A-Z
                            </option>
                            <option value="citations" {{ request('sort') == 'citations' ? 'selected' : '' }}>
                                Most Cited
                            </option>
                        </select>
                    </div>

                    <!-- Filter Actions -->
                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="{{ route('publications.index') }}" class="btn btn-outline-secondary">Clear</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Publications List -->
<section class="py-5 bg-white">
    <div class="container">
        @if(isset($publications) && $publications->count() > 0)
            <!-- Results Summary -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3>Publications</h3>
                        <span class="text-muted">
                            {{ $publications->total() }} {{ Str::plural('publication', $publications->total()) }} found
                            @if(request('search'))
                                for "{{ request('search') }}"
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Publications List -->
            <div class="row">
                @foreach($publications as $publication)
                <div class="col-12 mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body p-4">
                            <div class="row">
                                <!-- Publication Info -->
                                <div class="col-lg-9">
                                    <!-- Publication Meta -->
                                    <div class="d-flex flex-wrap gap-2 mb-3">
                                        @if($publication->type === 'journal')
                                            <span class="badge bg-success">Journal Article</span>
                                        @elseif($publication->type === 'conference')
                                            <span class="badge bg-info">Conference Paper</span>
                                        @elseif($publication->type === 'book')
                                            <span class="badge bg-warning text-dark">Book</span>
                                        @elseif($publication->type === 'chapter')
                                            <span class="badge bg-secondary">Book Chapter</span>
                                        @elseif($publication->type === 'thesis')
                                            <span class="badge bg-primary">Thesis</span>
                                        @else
                                            <span class="badge bg-light text-dark">{{ ucfirst($publication->type ?? 'Publication') }}</span>
                                        @endif

                                        @if($publication->peer_reviewed)
                                            <span class="badge bg-warning text-dark">Peer Reviewed</span>
                                        @endif

                                        @if($publication->open_access)
                                            <span class="badge bg-primary">Open Access</span>
                                        @endif
                                    </div>

                                    <!-- Title -->
                                    <h5 class="card-title mb-3">
                                        <span class="text-dark">
                                            {{ $publication->title }}
                                        </span>
                                    </h5>

                                    <!-- Authors -->
                                    @if($publication->authors)
                                        <p class="text-muted mb-2">
                                            <strong>Authors:</strong> {{ $publication->authors }}
                                        </p>
                                    @endif

                                    <!-- Journal/Conference Info -->
                                    <div class="mb-3">
                                        @if($publication->journal)
                                            <p class="text-muted mb-1">
                                                <em>{{ $publication->journal }}</em>
                                                @if($publication->volume || $publication->issue)
                                                    @if($publication->volume), Vol. {{ $publication->volume }}@endif
                                                    @if($publication->issue), Issue {{ $publication->issue }}@endif
                                                @endif
                                                @if($publication->pages), pp. {{ $publication->pages }}@endif
                                            </p>
                                        @elseif($publication->conference)
                                            <p class="text-muted mb-1">
                                                <em>{{ $publication->conference }}</em>
                                                @if($publication->conference_location), {{ $publication->conference_location }}@endif
                                            </p>
                                        @elseif($publication->publisher)
                                            <p class="text-muted mb-1">
                                                <em>{{ $publication->publisher }}</em>
                                            </p>
                                        @endif

                                        @if($publication->year)
                                            <p class="text-muted mb-1">
                                                <i class="bi bi-calendar me-1"></i>
                                                {{ $publication->year }}
                                            </p>
                                        @endif
                                    </div>

                                    <!-- Abstract -->
                                    @if($publication->abstract)
                                        <p class="card-text">
                                            {{ Str::limit($publication->abstract, 200) }}
                                        </p>
                                    @endif

                                    <!-- Keywords -->
                                    @if($publication->keywords)
                                        <div class="mb-3">
                                            <small class="text-muted">
                                                <strong>Keywords:</strong>
                                                @foreach(explode(',', $publication->keywords) as $keyword)
                                                    <span class="badge bg-light text-dark me-1">{{ trim($keyword) }}</span>
                                                @endforeach
                                            </small>
                                        </div>
                                    @endif

                                    <!-- Links -->
                                    <div class="d-flex flex-wrap gap-2">
                                        <button class="btn btn-outline-primary btn-sm" onclick="alert('Publication details: {{ $publication->title }}')">
                                            <i class="bi bi-eye me-1"></i>View Details
                                        </button>

                                        @if($publication->pdf_file)
                                            <a href="{{ Storage::url($publication->pdf_file) }}" target="_blank" class="btn btn-outline-danger btn-sm">
                                                <i class="bi bi-file-pdf me-1"></i>PDF
                                            </a>
                                        @endif

                                        @if($publication->doi)
                                            <a href="https://doi.org/{{ $publication->doi }}" target="_blank" rel="noopener" class="btn btn-outline-secondary btn-sm">
                                                <i class="bi bi-link me-1"></i>DOI
                                            </a>
                                        @endif

                                        @if($publication->url)
                                            <a href="{{ $publication->url }}" target="_blank" rel="noopener" class="btn btn-outline-info btn-sm">
                                                <i class="bi bi-globe me-1"></i>URL
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                <!-- Metrics -->
                                <div class="col-lg-3 text-end">
                                    @if($publication->citation_count || $publication->download_count)
                                        <div class="mt-3">
                                            @if($publication->citation_count)
                                                <div class="mb-2">
                                                    <small class="text-muted">Citations</small>
                                                    <h6 class="mb-0">{{ $publication->citation_count }}</h6>
                                                </div>
                                            @endif

                                            @if($publication->download_count)
                                                <div class="mb-2">
                                                    <small class="text-muted">Downloads</small>
                                                    <h6 class="mb-0">{{ $publication->download_count }}</h6>
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    @if($publication->impact_factor)
                                        <div class="mt-3">
                                            <small class="text-muted">Impact Factor</small>
                                            <h6 class="mb-0">{{ $publication->impact_factor }}</h6>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-center">
                        {{ $publications->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        @else
            <!-- No Publications Found -->
            <div class="row">
                <div class="col-12 text-center py-5">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-5">
                            <i class="bi bi-journal-text text-muted" style="font-size: 4rem;"></i>
                            <h3 class="mt-3">No Publications Found</h3>
                            @if(request()->hasAny(['search', 'type', 'year', 'sort']))
                                <p class="text-muted mb-4">
                                    No publications match your current filters. Try adjusting your search criteria.
                                </p>
                                <a href="{{ route('publications.index') }}" class="btn btn-primary">
                                    <i class="bi bi-arrow-left me-2"></i>View All Publications
                                </a>
                            @else
                                <p class="text-muted mb-4">
                                    Publication information will be available here. Check back for updates on recent research publications.
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

<!-- Publication Statistics -->
@if(isset($publicationStats))
<section class="py-5 bg-light">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h3>Publication Metrics</h3>
                <p class="text-muted">Impact and reach of my research publications</p>
            </div>
        </div>
        <div class="row text-center">
            <div class="col-md-3 col-6 mb-4">
                <div class="card border-0 h-100">
                    <div class="card-body">
                        <i class="bi bi-journal-text text-success" style="font-size: 2.5rem;"></i>
                        <h3 class="mt-3 mb-1">{{ $publicationStats['total_publications'] ?? 0 }}</h3>
                        <p class="text-muted mb-0">Total Publications</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4">
                <div class="card border-0 h-100">
                    <div class="card-body">
                        <i class="bi bi-quote text-primary" style="font-size: 2.5rem;"></i>
                        <h3 class="mt-3 mb-1">{{ $publicationStats['total_citations'] ?? 0 }}</h3>
                        <p class="text-muted mb-0">Total Citations</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4">
                <div class="card border-0 h-100">
                    <div class="card-body">
                        <i class="bi bi-graph-up text-warning" style="font-size: 2.5rem;"></i>
                        <h3 class="mt-3 mb-1">{{ $publicationStats['h_index'] ?? 0 }}</h3>
                        <p class="text-muted mb-0">H-Index</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4">
                <div class="card border-0 h-100">
                    <div class="card-body">
                        <i class="bi bi-award text-info" style="font-size: 2.5rem;"></i>
                        <h3 class="mt-3 mb-1">{{ $publicationStats['peer_reviewed'] ?? 0 }}</h3>
                        <p class="text-muted mb-0">Peer Reviewed</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endif

<!-- Research Impact -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h3 class="mb-4">Research Impact & Contribution</h3>
                <div class="card shadow-sm border-0">
                    <div class="card-body p-5">
                        <blockquote class="blockquote">
                            <p class="lead mb-4">
                                "My research aims to bridge theoretical knowledge with practical applications,
                                contributing to both academic understanding and real-world solutions that benefit
                                society and advance the field."
                            </p>
                        </blockquote>
                        <div class="row mt-4">
                            <div class="col-md-4 mb-3">
                                <i class="bi bi-lightbulb text-success" style="font-size: 2rem;"></i>
                                <h6 class="mt-2">Innovation</h6>
                                <small class="text-muted">Developing novel approaches and methodologies</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <i class="bi bi-people text-primary" style="font-size: 2rem;"></i>
                                <h6 class="mt-2">Collaboration</h6>
                                <small class="text-muted">Working with international research networks</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <i class="bi bi-globe text-info" style="font-size: 2rem;"></i>
                                <h6 class="mt-2">Impact</h6>
                                <small class="text-muted">Creating meaningful change in the field</small>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('projects.index') }}" class="btn btn-success me-2">
                                <i class="bi bi-lightbulb me-2"></i>View Research Projects
                            </a>
                            <a href="{{ route('contact.show') }}" class="btn btn-outline-success">
                                <i class="bi bi-envelope me-2"></i>Collaborate With Me
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Publication Types -->
@if(isset($publicationTypes) && count($publicationTypes) > 0)
<section class="py-5 bg-light">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h3>Publication Types</h3>
                <p class="text-muted">Explore publications by category</p>
            </div>
        </div>
        <div class="row justify-content-center">
            @foreach($publicationTypes as $type => $count)
            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3">
                <a href="{{ route('publications.index', ['type' => $type]) }}" class="text-decoration-none">
                    <div class="card text-center h-100 shadow-sm border-0">
                        <div class="card-body py-3">
                            @if($type === 'journal')
                                <i class="bi bi-journal text-success" style="font-size: 2rem;"></i>
                            @elseif($type === 'conference')
                                <i class="bi bi-people text-info" style="font-size: 2rem;"></i>
                            @elseif($type === 'book')
                                <i class="bi bi-book text-warning" style="font-size: 2rem;"></i>
                            @else
                                <i class="bi bi-file-text text-primary" style="font-size: 2rem;"></i>
                            @endif
                            <h6 class="card-title mt-2 mb-1">{{ ucfirst($type) }}</h6>
                            <small class="text-muted">{{ $count }} publications</small>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Call to Action -->
<section class="py-5 bg-primary text-white">
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h3 class="mb-4">Stay Updated on My Research</h3>
                <p class="lead mb-4">
                    Follow my latest publications and research findings. Subscribe to get notified about
                    new publications and research updates directly in your inbox.
                </p>
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <a href="{{ route('contact.show') }}" class="btn btn-light btn-lg">
                        <i class="bi bi-envelope me-2"></i>Contact Me
                    </a>
                    @if($teacher->google_scholar_url ?? false)
                        <a href="{{ $teacher->google_scholar_url }}" target="_blank" rel="noopener" class="btn btn-outline-light btn-lg">
                            <i class="bi bi-mortarboard me-2"></i>Google Scholar
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection