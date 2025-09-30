@extends('layouts.app')

@section('title', $publication->title)

@section('content')
<!-- Publication Header -->
<section class="py-5 bg-primary text-white">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb text-white-50">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white-50">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('publications.index') }}" class="text-white-50">Publications</a></li>
                <li class="breadcrumb-item active text-white">{{ Str::limit($publication->title, 50) }}</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-10">
                <h1 class="display-6 fw-bold mb-3">{{ $publication->title }}</h1>

                <div class="d-flex flex-wrap gap-2 mb-3">
                    @if($publication->type === 'article')
                        <span class="badge bg-light text-dark fs-6">Journal Article</span>
                    @elseif($publication->type === 'conference_paper')
                        <span class="badge bg-light text-dark fs-6">Conference Paper</span>
                    @elseif($publication->type === 'book')
                        <span class="badge bg-light text-dark fs-6">Book</span>
                    @elseif($publication->type === 'thesis')
                        <span class="badge bg-light text-dark fs-6">Thesis</span>
                    @elseif($publication->type === 'report')
                        <span class="badge bg-light text-dark fs-6">Report</span>
                    @else
                        <span class="badge bg-light text-dark fs-6">Other</span>
                    @endif

                    @if($publication->status === 'published')
                        <span class="badge bg-success fs-6">Published</span>
                    @else
                        <span class="badge bg-warning fs-6">Draft</span>
                    @endif

                    <span class="badge bg-info fs-6">{{ $publication->year }}</span>
                </div>

                <p class="lead mb-4"><strong>Authors:</strong> {{ $publication->authors }}</p>

                @if($publication->journal)
                    <p class="text-white-50 mb-4">
                        <i class="bi bi-journal me-2"></i>
                        <strong>Published in:</strong> {{ $publication->journal }}
                        @if($publication->volume || $publication->issue || $publication->pages)
                            <span>
                                @if($publication->volume), Vol. {{ $publication->volume }}@endif
                                @if($publication->issue), Issue {{ $publication->issue }}@endif
                                @if($publication->pages), pp. {{ $publication->pages }}@endif
                            </span>
                        @endif
                    </p>
                @endif

                <div class="d-flex flex-wrap gap-3">
                    @if($publication->publication_file_path)
                        <a href="{{ route('publications.download', $publication) }}" class="btn btn-light btn-lg" target="_blank">
                            <i class="bi bi-file-earmark-pdf me-2"></i>Download PDF
                        </a>
                    @endif

                    @if($publication->external_url)
                        <a href="{{ $publication->external_url }}" target="_blank" class="btn btn-outline-light btn-lg">
                            <i class="bi bi-link-45deg me-2"></i>External Link
                        </a>
                    @endif

                    @if($publication->doi)
                        <a href="https://doi.org/{{ $publication->doi }}" target="_blank" class="btn btn-outline-light">
                            <i class="bi bi-link me-2"></i>DOI
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Publication Details -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Abstract -->
                @if($publication->abstract)
                    <div class="card mb-4">
                        <div class="card-body p-4">
                            <h2 class="h4 mb-3">
                                <i class="bi bi-file-text text-primary me-2"></i>
                                Abstract
                            </h2>
                            <div class="abstract-content">
                                {!! nl2br(e($publication->abstract)) !!}
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Description -->
                @if($publication->description)
                    <div class="card mb-4">
                        <div class="card-body p-4">
                            <h2 class="h4 mb-3">
                                <i class="bi bi-info-circle text-primary me-2"></i>
                                Description
                            </h2>
                            <div class="description-content">
                                {!! nl2br(e($publication->description)) !!}
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Keywords -->
                @if($publication->keywords)
                    <div class="card mb-4">
                        <div class="card-body p-4">
                            <h2 class="h4 mb-3">
                                <i class="bi bi-key text-primary me-2"></i>
                                Keywords
                            </h2>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach(explode(',', $publication->keywords) as $keyword)
                                    <span class="badge bg-light text-dark border fs-6">{{ trim($keyword) }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Citation -->
                <div class="card mb-4">
                    <div class="card-body p-4">
                        <h2 class="h4 mb-3">
                            <i class="bi bi-quote text-primary me-2"></i>
                            Citation
                        </h2>

                        <!-- APA Style -->
                        <div class="mb-3">
                            <h6 class="text-muted mb-2">APA Style:</h6>
                            <div class="bg-light p-3 rounded">
                                <small class="font-monospace">
                                    {{ $publication->authors }} ({{ $publication->year }}). {{ $publication->title }}.
                                    @if($publication->journal)
                                        <em>{{ $publication->journal }}</em>@if($publication->volume), {{ $publication->volume }}@endif@if($publication->issue)>({{ $publication->issue }})@endif@if($publication->pages), {{ $publication->pages }}@endif.
                                    @endif
                                    @if($publication->doi) https://doi.org/{{ $publication->doi }}@endif
                                </small>
                            </div>
                        </div>

                        <!-- MLA Style -->
                        <div class="mb-3">
                            <h6 class="text-muted mb-2">MLA Style:</h6>
                            <div class="bg-light p-3 rounded">
                                <small class="font-monospace">
                                    {{ $publication->authors }}. "{{ $publication->title }}."
                                    @if($publication->journal)
                                        <em>{{ $publication->journal }}</em>@if($publication->volume), vol. {{ $publication->volume }}@endif@if($publication->issue), no. {{ $publication->issue }}@endif,
                                    @endif
                                    {{ $publication->year }}@if($publication->pages), pp. {{ $publication->pages }}@endif.
                                    @if($publication->doi) DOI: {{ $publication->doi }}.@endif
                                </small>
                            </div>
                        </div>

                        <!-- BibTeX -->
                        <div class="mb-3">
                            <h6 class="text-muted mb-2">BibTeX:</h6>
                            <div class="bg-light p-3 rounded">
                                <small class="font-monospace">
@{{ $publication->type === 'article' ? 'article' : 'inproceedings' }}{{ '{' }}{{ Str::slug($publication->title) }},<br>
&nbsp;&nbsp;title = {{ '{' }}{{ $publication->title }}{{ '}' }},<br>
&nbsp;&nbsp;author = {{ '{' }}{{ $publication->authors }}{{ '}' }},<br>
@if($publication->journal)
&nbsp;&nbsp;{{ $publication->type === 'article' ? 'journal' : 'booktitle' }} = {{ '{' }}{{ $publication->journal }}{{ '}' }},<br>
@endif
@if($publication->volume)
&nbsp;&nbsp;volume = {{ '{' }}{{ $publication->volume }}{{ '}' }},<br>
@endif
@if($publication->issue)
&nbsp;&nbsp;number = {{ '{' }}{{ $publication->issue }}{{ '}' }},<br>
@endif
@if($publication->pages)
&nbsp;&nbsp;pages = {{ '{' }}{{ $publication->pages }}{{ '}' }},<br>
@endif
&nbsp;&nbsp;year = {{ '{' }}{{ $publication->year }}{{ '}' }},<br>
@if($publication->doi)
&nbsp;&nbsp;doi = {{ '{' }}{{ $publication->doi }}{{ '}' }},<br>
@endif
{{ '}' }}
                                </small>
                            </div>
                        </div>

                        <button class="btn btn-outline-primary btn-sm" onclick="copyToClipboard('apa')">
                            <i class="bi bi-clipboard me-1"></i>Copy APA
                        </button>
                        <button class="btn btn-outline-primary btn-sm" onclick="copyToClipboard('mla')">
                            <i class="bi bi-clipboard me-1"></i>Copy MLA
                        </button>
                        <button class="btn btn-outline-primary btn-sm" onclick="copyToClipboard('bibtex')">
                            <i class="bi bi-clipboard me-1"></i>Copy BibTeX
                        </button>
                    </div>
                </div>

                <!-- Contact -->
                <div class="card">
                    <div class="card-body p-4">
                        <h2 class="h4 mb-3">
                            <i class="bi bi-chat-dots text-primary me-2"></i>
                            Questions About This Research?
                        </h2>
                        <p class="text-muted mb-3">
                            Interested in discussing this research or exploring collaboration opportunities?
                            Feel free to reach out.
                        </p>
                        <a href="{{ route('contact.show') }}" class="btn btn-primary">
                            <i class="bi bi-envelope me-2"></i>Contact Me
                        </a>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Publication Info -->
                <div class="card mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-3">
                            <i class="bi bi-info-circle text-primary me-2"></i>
                            Publication Details
                        </h5>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Type:</span>
                            <span class="fw-medium">{{ ucwords(str_replace('_', ' ', $publication->type)) }}</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Status:</span>
                            @if($publication->status === 'published')
                                <span class="badge bg-success">Published</span>
                            @else
                                <span class="badge bg-warning">Draft</span>
                            @endif
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Publication Date:</span>
                            <span class="fw-medium">{{ $publication->year }}</span>
                        </div>

                        @if($publication->citation_count && $publication->citation_count > 0)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Citations:</span>
                                <span class="fw-medium text-success">{{ $publication->citation_count }}</span>
                            </div>
                        @endif

                        @if($publication->isbn)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">ISBN:</span>
                                <span class="fw-medium small">{{ $publication->isbn }}</span>
                            </div>
                        @endif

                        @if($publication->doi)
                            <div class="mb-2">
                                <span class="text-muted d-block">DOI:</span>
                                <a href="https://doi.org/{{ $publication->doi }}" target="_blank" class="small text-decoration-none">
                                    {{ $publication->doi }}
                                </a>
                            </div>
                        @endif

                        @if($publication->publication_file_path || $publication->external_url)
                            <hr class="my-3">
                            <div class="d-grid gap-2">
                                @if($publication->publication_file_path)
                                    <a href="{{ route('publications.download', $publication) }}" target="_blank" class="btn btn-primary">
                                        <i class="bi bi-file-earmark-pdf me-1"></i>
                                        Download PDF
                                    </a>
                                @endif

                                @if($publication->external_url)
                                    <a href="{{ $publication->external_url }}" target="_blank" class="btn btn-outline-secondary">
                                        <i class="bi bi-link-45deg me-1"></i>
                                        External Link
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Tags -->
                @if($publication->tags && $publication->tags->count() > 0)
                    <div class="card mb-4">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-3">
                                <i class="bi bi-tags text-primary me-2"></i>
                                Tags
                            </h5>

                            <div class="d-flex flex-wrap gap-2">
                                @foreach($publication->tags as $tag)
                                    <span class="badge bg-secondary">{{ $tag->name }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Author -->
                <div class="card mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-3">
                            <i class="bi bi-person-badge text-primary me-2"></i>
                            Author
                        </h5>

                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-person-circle text-muted me-3" style="font-size: 2rem;"></i>
                            <div>
                                <h6 class="mb-0">{{ $publication->user->name ?? 'Author' }}</h6>
                                <small class="text-muted">Researcher</small>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <a href="{{ route('contact.show') }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-envelope me-1"></i>Contact Author
                            </a>
                            <a href="{{ route('about') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-person me-1"></i>About Me
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Related Publications -->
                @if($relatedPublications && $relatedPublications->count() > 0)
                    <div class="card">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-3">
                                <i class="bi bi-collection text-primary me-2"></i>
                                Related Publications
                            </h5>

                            @foreach($relatedPublications as $related)
                                <div class="mb-3">
                                    <h6 class="mb-1">
                                        <a href="{{ route('publications.show', $related) }}" class="text-decoration-none">
                                            {{ Str::limit($related->title, 60) }}
                                        </a>
                                    </h6>
                                    <small class="text-muted">{{ $related->year }}</small>
                                    @if($related->type !== $publication->type)
                                        <span class="badge bg-light text-dark border ms-1">{{ ucwords(str_replace('_', ' ', $related->type)) }}</span>
                                    @endif
                                </div>
                            @endforeach

                            <div class="text-center mt-3">
                                <a href="{{ route('publications.index') }}" class="btn btn-outline-primary btn-sm">
                                    View All Publications <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection

@section('styles')
<style>
    .abstract-content,
    .description-content {
        line-height: 1.7;
        font-size: 1.05rem;
        text-align: justify;
    }

    .breadcrumb-item + .breadcrumb-item::before {
        color: rgba(255, 255, 255, 0.5);
    }

    .breadcrumb-item a {
        text-decoration: none;
    }

    .breadcrumb-item a:hover {
        text-decoration: underline;
    }

    .card {
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .font-monospace {
        font-family: 'Courier New', Courier, monospace;
        font-size: 0.9rem;
        line-height: 1.4;
    }
</style>
@endsection

@section('scripts')
<script>
    function copyToClipboard(type) {
        let text = '';
        const title = "{{ $publication->title }}";
        const authors = "{{ $publication->authors }}";
        const year = "{{ $publication->year }}";
        const journal = "{{ $publication->journal ?? '' }}";
        const volume = "{{ $publication->volume ?? '' }}";
        const issue = "{{ $publication->issue ?? '' }}";
        const pages = "{{ $publication->pages ?? '' }}";
        const doi = "{{ $publication->doi ?? '' }}";

        if (type === 'apa') {
            text = `${authors} (${year}). ${title}.`;
            if (journal) {
                text += ` ${journal}`;
                if (volume) text += `, ${volume}`;
                if (issue) text += `(${issue})`;
                if (pages) text += `, ${pages}`;
                text += '.';
            }
            if (doi) text += ` https://doi.org/${doi}`;
        } else if (type === 'mla') {
            text = `${authors}. "${title}."`;
            if (journal) {
                text += ` ${journal}`;
                if (volume) text += `, vol. ${volume}`;
                if (issue) text += `, no. ${issue}`;
                text += ',';
            }
            text += ` ${year}`;
            if (pages) text += `, pp. ${pages}`;
            text += '.';
            if (doi) text += ` DOI: ${doi}.`;
        } else if (type === 'bibtex') {
            const slug = title.toLowerCase().replace(/[^a-z0-9]+/g, '');
            text = `@article{${slug},\n  title = {${title}},\n  author = {${authors}},`;
            if (journal) text += `\n  journal = {${journal}},`;
            if (volume) text += `\n  volume = {${volume}},`;
            if (issue) text += `\n  number = {${issue}},`;
            if (pages) text += `\n  pages = {${pages}},`;
            text += `\n  year = {${year}},`;
            if (doi) text += `\n  doi = {${doi}},`;
            text += '\n}';
        }

        navigator.clipboard.writeText(text).then(function() {
            // Show toast or alert
            const btn = event.target.closest('button');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="bi bi-check me-1"></i>Copied!';
            btn.classList.add('btn-success');
            btn.classList.remove('btn-outline-primary');

            setTimeout(function() {
                btn.innerHTML = originalText;
                btn.classList.remove('btn-success');
                btn.classList.add('btn-outline-primary');
            }, 2000);
        }).catch(function() {
            alert('Failed to copy to clipboard');
        });
    }
</script>
@endsection