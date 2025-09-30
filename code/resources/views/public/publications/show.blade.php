@extends('layouts.app')

@section('title', $publication->title ?? 'Publication Details')

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
                            <a href="{{ route('publications.index') }}" class="text-white text-decoration-none">Publications</a>
                        </li>
                        <li class="breadcrumb-item active text-white-50" aria-current="page">
                            {{ Str::limit($publication->title ?? 'Publication', 50) }}
                        </li>
                    </ol>
                </nav>

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

                <!-- Publication Title -->
                <h1 class="display-5 fw-bold mb-4">{{ $publication->title ?? 'Publication Title' }}</h1>

                <!-- Authors -->
                @if($publication->authors ?? false)
                    <p class="lead mb-4">
                        <strong>Authors:</strong> {{ $publication->authors }}
                    </p>
                @endif

                <!-- Publication Info -->
                <div class="row text-center">
                    @if($publication->year)
                    <div class="col-md-3 col-6 mb-3">
                        <div class="text-white-50">
                            <i class="bi bi-calendar" style="font-size: 1.5rem;"></i>
                            <div class="mt-2">
                                <strong class="text-white d-block">{{ $publication->year }}</strong>
                                <small>Published</small>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($publication->citation_count)
                    <div class="col-md-3 col-6 mb-3">
                        <div class="text-white-50">
                            <i class="bi bi-quote" style="font-size: 1.5rem;"></i>
                            <div class="mt-2">
                                <strong class="text-white d-block">{{ $publication->citation_count }}</strong>
                                <small>Citations</small>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($publication->download_count)
                    <div class="col-md-3 col-6 mb-3">
                        <div class="text-white-50">
                            <i class="bi bi-download" style="font-size: 1.5rem;"></i>
                            <div class="mt-2">
                                <strong class="text-white d-block">{{ $publication->download_count }}</strong>
                                <small>Downloads</small>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($publication->impact_factor)
                    <div class="col-md-3 col-6 mb-3">
                        <div class="text-white-50">
                            <i class="bi bi-graph-up" style="font-size: 1.5rem;"></i>
                            <div class="mt-2">
                                <strong class="text-white d-block">{{ $publication->impact_factor }}</strong>
                                <small>Impact Factor</small>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Publication Content -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Publication Details -->
                <div class="card shadow-sm border-0 mb-5">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0"><i class="bi bi-info-circle me-2"></i>Publication Details</h4>
                    </div>
                    <div class="card-body p-4">
                        <!-- Journal/Conference Information -->
                        @if($publication->journal || $publication->conference)
                            <div class="mb-4">
                                @if($publication->journal)
                                    <h6>Journal Information</h6>
                                    <p class="mb-2">
                                        <strong>Journal:</strong> <em>{{ $publication->journal }}</em>
                                    </p>
                                    @if($publication->volume)
                                        <p class="mb-2"><strong>Volume:</strong> {{ $publication->volume }}</p>
                                    @endif
                                    @if($publication->issue)
                                        <p class="mb-2"><strong>Issue:</strong> {{ $publication->issue }}</p>
                                    @endif
                                    @if($publication->pages)
                                        <p class="mb-2"><strong>Pages:</strong> {{ $publication->pages }}</p>
                                    @endif
                                @elseif($publication->conference)
                                    <h6>Conference Information</h6>
                                    <p class="mb-2">
                                        <strong>Conference:</strong> <em>{{ $publication->conference }}</em>
                                    </p>
                                    @if($publication->conference_location)
                                        <p class="mb-2"><strong>Location:</strong> {{ $publication->conference_location }}</p>
                                    @endif
                                @endif

                                @if($publication->publisher)
                                    <p class="mb-2"><strong>Publisher:</strong> {{ $publication->publisher }}</p>
                                @endif

                                @if($publication->isbn)
                                    <p class="mb-2"><strong>ISBN:</strong> {{ $publication->isbn }}</p>
                                @endif

                                @if($publication->doi)
                                    <p class="mb-2">
                                        <strong>DOI:</strong>
                                        <a href="https://doi.org/{{ $publication->doi }}" target="_blank" rel="noopener" class="text-decoration-none">
                                            {{ $publication->doi }}
                                        </a>
                                    </p>
                                @endif
                            </div>
                        @endif

                        <!-- Publication Actions -->
                        <div class="d-flex flex-wrap gap-2 mb-4">
                            @if($publication->pdf_file)
                                <a href="{{ Storage::url($publication->pdf_file) }}" target="_blank" class="btn btn-danger">
                                    <i class="bi bi-file-pdf me-2"></i>Download PDF
                                </a>
                            @endif

                            @if($publication->doi)
                                <a href="https://doi.org/{{ $publication->doi }}" target="_blank" rel="noopener" class="btn btn-outline-secondary">
                                    <i class="bi bi-link me-2"></i>View on Publisher Site
                                </a>
                            @endif

                            @if($publication->url)
                                <a href="{{ $publication->url }}" target="_blank" rel="noopener" class="btn btn-outline-info">
                                    <i class="bi bi-globe me-2"></i>External Link
                                </a>
                            @endif

                            <button type="button" class="btn btn-outline-primary" onclick="copyToClipboard('{{ request()->url() }}')">
                                <i class="bi bi-share me-2"></i>Share
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Abstract -->
                @if($publication->abstract ?? false)
                <div class="card shadow-sm border-0 mb-5">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="bi bi-file-text me-2"></i>Abstract</h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="abstract-content">
                            {!! nl2br(e($publication->abstract)) !!}
                        </div>
                    </div>
                </div>
                @endif

                <!-- Keywords -->
                @if($publication->keywords ?? false)
                <div class="card shadow-sm border-0 mb-5">
                    <div class="card-header bg-warning text-white">
                        <h4 class="mb-0"><i class="bi bi-tags me-2"></i>Keywords</h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex flex-wrap gap-2">
                            @foreach(explode(',', $publication->keywords) as $keyword)
                                <span class="badge bg-light text-dark">#{{ trim($keyword) }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Bibtex Citation -->
                <div class="card shadow-sm border-0 mb-5">
                    <div class="card-header bg-secondary text-white">
                        <h4 class="mb-0"><i class="bi bi-quote me-2"></i>Citation</h4>
                    </div>
                    <div class="card-body p-4">
                        <!-- APA Citation -->
                        <div class="mb-3">
                            <h6>APA Format:</h6>
                            <div class="bg-light p-3 rounded">
                                <small class="font-monospace">
                                    {{ $publication->authors ?? 'Author, A.' }}
                                    ({{ $publication->year ?: 'Year' }}).
                                    {{ $publication->title }}.
                                    @if($publication->journal)
                                        <em>{{ $publication->journal }}</em>@if($publication->volume), {{ $publication->volume }}@endif@if($publication->issue))({{ $publication->issue }})@endif@if($publication->pages), {{ $publication->pages }}@endif.
                                    @elseif($publication->conference)
                                        In <em>{{ $publication->conference }}</em>.
                                    @endif
                                    @if($publication->doi)
                                        https://doi.org/{{ $publication->doi }}
                                    @endif
                                </small>
                            </div>
                        </div>

                        <!-- BibTeX Citation -->
                        @if($publication->bibtex ?? false)
                            <div class="mb-3">
                                <h6>BibTeX:</h6>
                                <div class="bg-dark text-light p-3 rounded">
                                    <pre class="mb-0"><code>{{ $publication->bibtex }}</code></pre>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-secondary mt-2" onclick="copyToClipboard(`{{ addslashes($publication->bibtex) }}`)">
                                    <i class="bi bi-clipboard me-1"></i>Copy BibTeX
                                </button>
                            </div>
                        @else
                            <!-- Generate basic BibTeX -->
                            <div class="mb-3">
                                <h6>BibTeX:</h6>
                                <div class="bg-dark text-light p-3 rounded">
                                    <pre class="mb-0"><code>@article{{{ Str::slug($publication->title ?? 'publication') . ($publication->year ?? date('Y')) }},
  title={{"{"}}{{ $publication->title ?? 'Publication Title' }}{{"}"}},
  author={{"{"}}{{ $publication->authors ?? 'Author Name' }}{{"}"}},
  @if($publication->journal)journal={{"{"}}{{ $publication->journal }}{{"}"}},@endif
  @if($publication->volume)volume={{"{"}}{{ $publication->volume }}{{"}"}},@endif
  @if($publication->issue)number={{"{"}}{{ $publication->issue }}{{"}"}},@endif
  @if($publication->pages)pages={{"{"}}{{ $publication->pages }}{{"}"}},@endif
  year={{"{"}}{{ $publication->year ?: date('Y') }}{{"}"}},
  @if($publication->publisher)publisher={{"{"}}{{ $publication->publisher }}{{"}"}},@endif
  @if($publication->doi)doi={{"{"}}{{ $publication->doi }}{{"}"}},@endif
}</code></pre>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Related Publications -->
                @if(isset($relatedPublications) && $relatedPublications->count() > 0)
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-info text-white">
                        <h4 class="mb-0"><i class="bi bi-journals me-2"></i>Related Publications</h4>
                    </div>
                    <div class="card-body p-0">
                        @foreach($relatedPublications->take(3) as $relatedPub)
                            <div class="p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <h6 class="mb-2">
                                    <a href="{{ route('publications.show', $relatedPub->slug) }}"
                                       class="text-decoration-none">
                                        {{ $relatedPub->title }}
                                    </a>
                                </h6>
                                <small class="text-muted">
                                    {{ $relatedPub->year ?: 'Recent' }} •
                                    {{ ucfirst($relatedPub->type) }}
                                </small>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Publication Metrics -->
                <div class="card shadow-sm border-0 mb-4 sticky-top" style="top: 2rem;">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Publication Metrics</h5>
                    </div>
                    <div class="card-body">
                        @if($publication->year)
                        <div class="mb-3">
                            <strong>Publication Year:</strong><br>
                            <span class="text-muted">{{ $publication->year }}</span>
                        </div>
                        @endif

                        @if($publication->citation_count)
                        <div class="mb-3">
                            <strong>Citations:</strong><br>
                            <span class="text-muted">{{ $publication->citation_count }}</span>
                        </div>
                        @endif

                        @if($publication->download_count)
                        <div class="mb-3">
                            <strong>Downloads:</strong><br>
                            <span class="text-muted">{{ $publication->download_count }}</span>
                        </div>
                        @endif

                        @if($publication->impact_factor)
                        <div class="mb-3">
                            <strong>Impact Factor:</strong><br>
                            <span class="text-muted">{{ $publication->impact_factor }}</span>
                        </div>
                        @endif

                        @if($publication->quartile)
                        <div class="mb-3">
                            <strong>Journal Quartile:</strong><br>
                            <span class="text-muted">Q{{ $publication->quartile }}</span>
                        </div>
                        @endif

                        <div class="mb-3">
                            <strong>Type:</strong><br>
                            <span class="text-muted">{{ ucfirst($publication->type ?? 'Publication') }}</span>
                        </div>

                        @if($publication->language)
                        <div class="mb-3">
                            <strong>Language:</strong><br>
                            <span class="text-muted">{{ $publication->language }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Author Information -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-person me-2"></i>Author</h5>
                    </div>
                    <div class="card-body text-center">
                        @if($teacher->avatar ?? false)
                            <img src="{{ Storage::url($teacher->avatar) }}"
                                 alt="{{ $teacher->name ?? 'Author' }}"
                                 class="rounded-circle mb-3"
                                 style="width: 80px; height: 80px; object-fit: cover;">
                        @else
                            <div class="bg-light rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                                 style="width: 80px; height: 80px;">
                                <i class="bi bi-person-circle text-muted" style="font-size: 2.5rem;"></i>
                            </div>
                        @endif

                        <h6 class="mb-2">{{ $teacher->name ?? 'Dr. [Your Name]' }}</h6>
                        <p class="text-muted mb-3">{{ $teacher->title ?? 'Professor' }}</p>

                        <div class="d-flex gap-2 justify-content-center">
                            <a href="{{ route('about') }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-person me-1"></i>Profile
                            </a>
                            <a href="{{ route('contact.show') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-envelope me-1"></i>Contact
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Share Publication -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0"><i class="bi bi-share me-2"></i>Share This Publication</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($publication->title) }}"
                               target="_blank" rel="noopener"
                               class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-twitter me-2"></i>Share on Twitter
                            </a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->url()) }}"
                               target="_blank" rel="noopener"
                               class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-linkedin me-2"></i>Share on LinkedIn
                            </a>
                            <button type="button" class="btn btn-outline-secondary btn-sm"
                                    onclick="copyToClipboard('{{ request()->url() }}')">
                                <i class="bi bi-link me-2"></i>Copy Link
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Research Areas -->
                @if($publication->research_areas ?? false)
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="bi bi-lightbulb me-2"></i>Research Areas</h5>
                    </div>
                    <div class="card-body">
                        @foreach(explode(',', $publication->research_areas) as $area)
                            <span class="badge bg-light text-dark me-1 mb-1">{{ trim($area) }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Navigation -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="row">
            @if(isset($previousPublication))
                <div class="col-md-6 mb-3">
                    <div class="card h-100 border-0">
                        <div class="card-body">
                            <small class="text-muted">Previous Publication</small>
                            <h6 class="mt-2">
                                <a href="{{ route('publications.show', $previousPublication->slug) }}"
                                   class="text-decoration-none">
                                    <i class="bi bi-arrow-left me-1"></i>{{ Str::limit($previousPublication->title, 60) }}
                                </a>
                            </h6>
                        </div>
                    </div>
                </div>
            @endif

            @if(isset($nextPublication))
                <div class="col-md-6 mb-3">
                    <div class="card h-100 border-0">
                        <div class="card-body text-end">
                            <small class="text-muted">Next Publication</small>
                            <h6 class="mt-2">
                                <a href="{{ route('publications.show', $nextPublication->slug) }}"
                                   class="text-decoration-none">
                                    {{ Str::limit($nextPublication->title, 60) }}<i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </h6>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="text-center mt-3">
            <a href="{{ route('publications.index') }}" class="btn btn-success">
                <i class="bi bi-grid me-2"></i>View All Publications
            </a>
        </div>
    </div>
</section>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('Copied to clipboard!');
    }, function(err) {
        console.error('Could not copy text: ', err);
    });
}
</script>
@endsection