@extends('layouts.admin-modern')

@section('page-title', 'View Publication')

@section('content')
<!-- Header -->
<div class="flex flex-col lg:flex-row lg:justify-between lg:items-start gap-4 mb-6 lg:mb-8">
    <div class="flex-1">
        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-2">{{ $publication->title }}</h1>
        <p class="text-gray-600">Publication Details</p>
    </div>
    <div class="flex flex-col sm:flex-row gap-3">
        <a href="{{ route('admin.publications.edit', $publication) }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
            <i class="fas fa-edit mr-2"></i>Edit Publication
        </a>
        <a href="{{ route('admin.publications.index') }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Back to Publications
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
    <div class="lg:col-span-2 space-y-6">
        <!-- Publication Title and Authors -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 lg:p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ $publication->title }}</h2>
                @if($publication->authors)
                    <p class="text-gray-600 mb-4">
                        <strong class="text-gray-900">Authors:</strong> {{ $publication->authors }}
                    </p>
                @endif

                <!-- Publication Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($publication->journal_name || $publication->venue)
                        <div>
                            <dt class="text-sm font-medium text-gray-900 mb-1">Journal/Venue</dt>
                            <dd class="text-sm text-gray-600">{{ $publication->journal_name ?: $publication->venue }}</dd>
                        </div>
                    @endif

                    @if($publication->year)
                        <div>
                            <dt class="text-sm font-medium text-gray-900 mb-1">Publication Year</dt>
                            <dd class="text-sm text-gray-600">{{ $publication->year }}</dd>
                        </div>
                    @endif

                    @if($publication->volume || $publication->issue || $publication->pages)
                        <div>
                            <dt class="text-sm font-medium text-gray-900 mb-1">Volume/Issue/Pages</dt>
                            <dd class="text-sm text-gray-600">
                                @if($publication->volume)Vol. {{ $publication->volume }}@endif
                                @if($publication->issue), Issue {{ $publication->issue }}@endif
                                @if($publication->pages), pp. {{ $publication->pages }}@endif
                            </dd>
                        </div>
                    @endif

                    @if($publication->doi)
                        <div>
                            <dt class="text-sm font-medium text-gray-900 mb-1">DOI</dt>
                            <dd class="text-sm">
                                <a href="https://doi.org/{{ $publication->doi }}" target="_blank" class="text-blue-600 hover:text-blue-800 transition-colors">
                                    {{ $publication->doi }} <i class="fas fa-external-link-alt ml-1"></i>
                                </a>
                            </dd>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Abstract -->
        @if($publication->abstract)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 lg:p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-file-text text-blue-600 mr-3"></i>
                        Abstract
                    </h3>
                    <div class="text-gray-700 leading-relaxed">
                        {!! nl2br(e($publication->abstract)) !!}
                    </div>
                </div>
            </div>
        @endif

        <!-- Keywords -->
        @if($publication->keywords)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 lg:p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-tags text-blue-600 mr-3"></i>
                        Keywords
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach(explode(',', $publication->keywords) as $keyword)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">{{ trim($keyword) }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Notes -->
        @if($publication->notes)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 lg:p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-sticky-note text-blue-600 mr-3"></i>
                        Notes
                    </h3>
                    <div class="text-gray-700 leading-relaxed">
                        {!! nl2br(e($publication->notes)) !!}
                    </div>
                </div>
            </div>
        @endif

        <!-- Citation -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 lg:p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-quote-left text-blue-600 mr-3"></i>
                    Citation
                </h3>
                <div class="bg-gray-50 p-4 rounded-lg border">
                    <div id="citation-text" class="font-mono text-sm text-gray-800 leading-relaxed">
                        @if($publication->authors){{ $publication->authors }}@endif
                        @if($publication->year) ({{ $publication->year }}).@endif
                        @if($publication->title) {{ $publication->title }}.@endif
                        @if($publication->journal_name || $publication->venue) <em>{{ $publication->journal_name ?: $publication->venue }}</em>@endif
                        @if($publication->volume), {{ $publication->volume }}@endif
                        @if($publication->issue)({{ $publication->issue }})@endif
                        @if($publication->pages), {{ $publication->pages }}@endif
                        @if($publication->doi). https://doi.org/{{ $publication->doi }}@endif
                    </div>
                    <button class="inline-flex items-center px-3 py-2 mt-4 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors" onclick="copyCitation()">
                        <i class="fas fa-clipboard mr-2"></i>Copy Citation
                    </button>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 lg:p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-bolt text-blue-600 mr-3"></i>
                    Quick Actions
                </h3>
                <div class="flex flex-wrap gap-3">
                    @if($publication->url)
                        <a href="{{ $publication->url }}" target="_blank" class="inline-flex items-center px-3 py-2 text-sm font-medium text-blue-700 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                            <i class="fas fa-external-link-alt mr-2"></i>View Publication
                        </a>
                    @endif
                    @if($publication->doi)
                        <a href="https://doi.org/{{ $publication->doi }}" target="_blank" class="inline-flex items-center px-3 py-2 text-sm font-medium text-cyan-700 bg-cyan-50 border border-cyan-200 rounded-lg hover:bg-cyan-100 focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 transition-colors">
                            <i class="fas fa-search mr-2"></i>View DOI
                        </a>
                    @endif
                    @if($publication->pdf_file)
                        <a href="{{ Storage::url($publication->pdf_file) }}" target="_blank" class="inline-flex items-center px-3 py-2 text-sm font-medium text-green-700 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                            <i class="fas fa-file-pdf mr-2"></i>Download PDF
                        </a>
                    @endif
                    <a href="{{ route('admin.publications.edit', $publication) }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-yellow-700 bg-yellow-50 border border-yellow-200 rounded-lg hover:bg-yellow-100 focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition-colors">
                        <i class="fas fa-edit mr-2"></i>Edit Publication
                    </a>
                    <form method="POST" action="{{ route('admin.publications.destroy', $publication) }}" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-3 py-2 text-sm font-medium text-red-700 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors" data-confirm-delete>
                            <i class="fas fa-trash mr-2"></i>Delete Publication
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="lg:col-span-1 space-y-6">
        <!-- Publication Info -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 lg:p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-info-circle text-blue-600 mr-3"></i>
                    Publication Information
                </h3>

                <div class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-900 mb-1">Type</dt>
                        <dd>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                {{ ucwords(str_replace('_', ' ', $publication->type)) }}
                            </span>
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-900 mb-1">Status</dt>
                        <dd>
                            @if($publication->status === 'published')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">Published</span>
                            @elseif($publication->status === 'accepted')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">Accepted</span>
                            @elseif($publication->status === 'under_review')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">Under Review</span>
                            @elseif($publication->status === 'in_preparation')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">In Preparation</span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">{{ ucfirst($publication->status) }}</span>
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-900 mb-1">Visibility</dt>
                        <dd>
                            @if($publication->is_published)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">Published</span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">Draft</span>
                            @endif
                        </dd>
                    </div>

                    @if($publication->is_featured)
                        <div>
                            <dt class="text-sm font-medium text-gray-900 mb-1">Featured</dt>
                            <dd>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">Featured Publication</span>
                            </dd>
                        </div>
                    @endif

                    <div>
                        <dt class="text-sm font-medium text-gray-900 mb-1">Created</dt>
                        <dd class="text-sm text-gray-600">{{ $publication->created_at->format('F d, Y \a\t g:i A') }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-900 mb-1">Last Updated</dt>
                        <dd class="text-sm text-gray-600">{{ $publication->updated_at->format('F d, Y \a\t g:i A') }}</dd>
                    </div>
                </div>
            </div>
        </div>

        <!-- Files and Links -->
        @if($publication->pdf_file || $publication->url || $publication->doi)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 lg:p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-link text-blue-600 mr-3"></i>
                        Files & Links
                    </h3>

                    <div class="space-y-3">
                        @if($publication->pdf_file)
                            <a href="{{ Storage::url($publication->pdf_file) }}" target="_blank" class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-green-700 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                                <i class="fas fa-file-pdf mr-2"></i>Download PDF
                            </a>
                        @endif

                        @if($publication->url)
                            <a href="{{ $publication->url }}" target="_blank" class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-blue-700 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                <i class="fas fa-external-link-alt mr-2"></i>View Publication
                            </a>
                        @endif

                        @if($publication->doi)
                            <a href="https://doi.org/{{ $publication->doi }}" target="_blank" class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-cyan-700 bg-cyan-50 border border-cyan-200 rounded-lg hover:bg-cyan-100 focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 transition-colors">
                                <i class="fas fa-search mr-2"></i>View DOI
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Public Visibility -->
        @if($publication->is_published)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 lg:p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-chart-bar text-blue-600 mr-3"></i>
                        Public Visibility
                    </h3>
                    <div class="text-center">
                        <p class="text-gray-600 mb-4">This publication is visible to the public</p>
                        <a href="{{ url('/publications/' . Str::slug($publication->title)) }}" target="_blank" class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-blue-700 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                            <i class="fas fa-eye mr-2"></i>View Public Page
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection


@section('scripts')
<script>
    function copyCitation() {
        const citationText = document.getElementById('citation-text').textContent;

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(citationText).then(function() {
                showCopyFeedback();
            }).catch(function(err) {
                console.error('Failed to copy: ', err);
                fallbackCopyTextToClipboard(citationText);
            });
        } else {
            fallbackCopyTextToClipboard(citationText);
        }
    }

    function fallbackCopyTextToClipboard(text) {
        const textArea = document.createElement("textarea");
        textArea.value = text;

        textArea.style.top = "0";
        textArea.style.left = "0";
        textArea.style.position = "fixed";

        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();

        try {
            const successful = document.execCommand('copy');
            if (successful) {
                showCopyFeedback();
            }
        } catch (err) {
            console.error('Failed to copy: ', err);
        }

        document.body.removeChild(textArea);
    }

    function showCopyFeedback() {
        const button = document.querySelector('button[onclick="copyCitation()"]');
        const originalText = button.innerHTML;

        button.innerHTML = '<i class="fas fa-check mr-2"></i>Copied!';
        button.classList.remove('text-gray-700', 'bg-white', 'border-gray-300', 'hover:bg-gray-50');
        button.classList.add('text-green-700', 'bg-green-50', 'border-green-200', 'hover:bg-green-100');

        setTimeout(function() {
            button.innerHTML = originalText;
            button.classList.remove('text-green-700', 'bg-green-50', 'border-green-200', 'hover:bg-green-100');
            button.classList.add('text-gray-700', 'bg-white', 'border-gray-300', 'hover:bg-gray-50');
        }, 2000);
    }
</script>
@endsection