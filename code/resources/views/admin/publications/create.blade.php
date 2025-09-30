@extends('layouts.admin-modern')

@section('title', 'Create Publication')
@section('page-title', 'Create Publication')

@section('content')
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 lg:mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Add New Publication</h1>
            <p class="text-gray-600 mt-1">Add a research paper, article, or academic publication to your portfolio</p>
        </div>
        <a href="{{ route('admin.publications.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back to Publications
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 lg:p-6 border-b border-gray-100">
                    <h2 class="text-lg lg:text-xl font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-file-alt mr-2 text-blue-600"></i>
                        Publication Information
                    </h2>
                </div>
                <div class="p-4 lg:p-6">
                    <form method="POST" action="{{ route('admin.publications.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="space-y-6">
                            <!-- Publication Title -->
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Publication Title <span class="text-red-500">*</span></label>
                                <input type="text"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('title') border-red-500 @enderror"
                                       id="title"
                                       name="title"
                                       value="{{ old('title') }}"
                                       required>
                                @error('title')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Authors -->
                            <div>
                                <label for="authors" class="block text-sm font-medium text-gray-700 mb-2">Authors <span class="text-red-500">*</span></label>
                                <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('authors') border-red-500 @enderror"
                                          id="authors"
                                          name="authors"
                                          rows="2"
                                          required>{{ old('authors') }}</textarea>
                                @error('authors')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-gray-500 text-sm mt-1">List all authors in proper citation format (e.g., "Smith, J., Doe, A., & Johnson, M.")</p>
                            </div>

                            <!-- Type and Status -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Publication Type <span class="text-red-500">*</span></label>
                                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('type') border-red-500 @enderror" id="type" name="type" required>
                                        <option value="">Select Type</option>
                                        <option value="journal" {{ old('type') === 'journal' ? 'selected' : '' }}>Journal Article</option>
                                        <option value="conference" {{ old('type') === 'conference' ? 'selected' : '' }}>Conference Paper</option>
                                        <option value="book" {{ old('type') === 'book' ? 'selected' : '' }}>Book</option>
                                        <option value="book_chapter" {{ old('type') === 'book_chapter' ? 'selected' : '' }}>Book Chapter</option>
                                        <option value="thesis" {{ old('type') === 'thesis' ? 'selected' : '' }}>Thesis</option>
                                        <option value="report" {{ old('type') === 'report' ? 'selected' : '' }}>Report</option>
                                        <option value="preprint" {{ old('type') === 'preprint' ? 'selected' : '' }}>Preprint</option>
                                    </select>
                                    @error('type')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Publication Status</label>
                                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('status') border-red-500 @enderror" id="status" name="status">
                                        <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published</option>
                                        <option value="accepted" {{ old('status') === 'accepted' ? 'selected' : '' }}>Accepted</option>
                                        <option value="under_review" {{ old('status') === 'under_review' ? 'selected' : '' }}>Under Review</option>
                                        <option value="in_preparation" {{ old('status') === 'in_preparation' ? 'selected' : '' }}>In Preparation</option>
                                    </select>
                                    @error('status')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Journal/Venue Information -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="journal_name" class="block text-sm font-medium text-gray-700 mb-2">Journal/Conference Name</label>
                                    <input type="text"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('journal_name') border-red-500 @enderror"
                                           id="journal_name"
                                           name="journal_name"
                                           value="{{ old('journal_name') }}">
                                    @error('journal_name')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="venue" class="block text-sm font-medium text-gray-700 mb-2">Venue/Publisher</label>
                                    <input type="text"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('venue') border-red-500 @enderror"
                                           id="venue"
                                           name="venue"
                                           value="{{ old('venue') }}">
                                    @error('venue')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Publication Details -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="volume" class="block text-sm font-medium text-gray-700 mb-2">Volume</label>
                                    <input type="text"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('volume') border-red-500 @enderror"
                                           id="volume"
                                           name="volume"
                                           value="{{ old('volume') }}">
                                    @error('volume')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="issue" class="block text-sm font-medium text-gray-700 mb-2">Issue</label>
                                    <input type="text"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('issue') border-red-500 @enderror"
                                           id="issue"
                                           name="issue"
                                           value="{{ old('issue') }}">
                                    @error('issue')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="pages" class="block text-sm font-medium text-gray-700 mb-2">Pages</label>
                                    <input type="text"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('pages') border-red-500 @enderror"
                                           id="pages"
                                           name="pages"
                                           value="{{ old('pages') }}"
                                           placeholder="e.g., 123-145">
                                    @error('pages')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Publication Year -->
                            <div>
                                <label for="year" class="block text-sm font-medium text-gray-700 mb-2">Publication Year <span class="text-red-500">*</span></label>
                                <input type="number"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('year') border-red-500 @enderror"
                                       id="year"
                                       name="year"
                                       min="1900"
                                       max="{{ date('Y') + 5 }}"
                                       value="{{ old('year', date('Y')) }}"
                                       required>
                                @error('year')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- DOI and URLs -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="doi" class="block text-sm font-medium text-gray-700 mb-2">DOI</label>
                                    <input type="text"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('doi') border-red-500 @enderror"
                                           id="doi"
                                           name="doi"
                                           value="{{ old('doi') }}"
                                           placeholder="e.g., 10.1000/182">
                                    @error('doi')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="url" class="block text-sm font-medium text-gray-700 mb-2">Publication URL</label>
                                    <input type="url"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('url') border-red-500 @enderror"
                                           id="url"
                                           name="url"
                                           value="{{ old('url') }}">
                                    @error('url')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- PDF File -->
                            <div>
                                <label for="pdf_file" class="block text-sm font-medium text-gray-700 mb-2">PDF File</label>
                                <input type="file"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('pdf_file') border-red-500 @enderror"
                                       id="pdf_file"
                                       name="pdf_file"
                                       accept=".pdf">
                                @error('pdf_file')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-gray-500 text-sm mt-1">Upload the publication PDF file (max 20MB)</p>
                            </div>

                            <!-- Abstract -->
                            <div>
                                <label for="abstract" class="block text-sm font-medium text-gray-700 mb-2">Abstract</label>
                                <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('abstract') border-red-500 @enderror"
                                          id="abstract"
                                          name="abstract"
                                          rows="6">{{ old('abstract') }}</textarea>
                                @error('abstract')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-gray-500 text-sm mt-1">Publication abstract or summary</p>
                            </div>

                            <!-- Keywords -->
                            <div>
                                <label for="keywords" class="block text-sm font-medium text-gray-700 mb-2">Keywords</label>
                                <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('keywords') border-red-500 @enderror"
                                          id="keywords"
                                          name="keywords"
                                          rows="2">{{ old('keywords') }}</textarea>
                                @error('keywords')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-gray-500 text-sm mt-1">Comma-separated keywords or research topics</p>
                            </div>

                            <!-- Notes -->
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                                <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('notes') border-red-500 @enderror"
                                          id="notes"
                                          name="notes"
                                          rows="3">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-gray-500 text-sm mt-1">Additional notes or comments about this publication</p>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="flex flex-col sm:flex-row sm:justify-between pt-6 border-t border-gray-100 gap-4">
                                <a href="{{ route('admin.publications.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                                    <i class="fas fa-times mr-2"></i>Cancel
                                </a>
                                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                    <i class="fas fa-check mr-2"></i>Create Publication
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="lg:col-span-1">
            <!-- Visibility Settings -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
                <div class="p-4 lg:p-6 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-eye mr-2 text-blue-600"></i>
                        Visibility
                    </h2>
                </div>
                <div class="p-4 lg:p-6">
                    <div class="space-y-3 mb-4">
                        <div class="flex items-start">
                            <input class="mt-1 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" type="radio" name="is_published" id="published" value="1" {{ old('is_published', '1') == '1' ? 'checked' : '' }}>
                            <label class="ml-3 block text-sm" for="published">
                                <span class="font-medium text-gray-900">Published</span>
                                <span class="text-gray-500 block text-xs">Visible to all visitors</span>
                            </label>
                        </div>
                        <div class="flex items-start">
                            <input class="mt-1 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" type="radio" name="is_published" id="draft" value="0" {{ old('is_published') == '0' ? 'checked' : '' }}>
                            <label class="ml-3 block text-sm" for="draft">
                                <span class="font-medium text-gray-900">Draft</span>
                                <span class="text-gray-500 block text-xs">Only visible to you</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <input class="mt-1 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                        <label class="ml-3 block text-sm" for="is_featured">
                            <span class="font-medium text-gray-900">Featured Publication</span>
                            <span class="text-gray-500 block text-xs">Highlight this publication</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Citation Preview -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
                <div class="p-4 lg:p-6 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-quote-left mr-2 text-blue-600"></i>
                        Citation Preview
                    </h2>
                </div>
                <div class="p-4 lg:p-6">
                    <div id="citation-preview" class="text-sm text-gray-600 italic">
                        Citation will appear here as you fill in the form
                    </div>
                </div>
            </div>

            <!-- Tips -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 lg:p-6 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-lightbulb mr-2 text-yellow-500"></i>
                        Tips
                    </h2>
                </div>
                <div class="p-4 lg:p-6">
                    <ul class="space-y-3 text-sm">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5"></i>
                            <span>Include the DOI for easier reference and citation</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5"></i>
                            <span>Write a comprehensive abstract to attract readers</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5"></i>
                            <span>Use relevant keywords to improve discoverability</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5"></i>
                            <span>Upload the PDF for easy access by visitors</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5"></i>
                            <span>Ensure author names follow proper academic formatting</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // No auto-generation needed since we only use year field now

        // Citation preview update
        function updateCitationPreview() {
            const title = document.getElementById('title').value;
            const authors = document.getElementById('authors').value;
            const year = document.getElementById('year').value;
            const journal = document.getElementById('journal_name').value;
            const volume = document.getElementById('volume').value;
            const issue = document.getElementById('issue').value;
            const pages = document.getElementById('pages').value;
            const doi = document.getElementById('doi').value;

            let citation = '';

            if (authors) citation += authors;
            if (year) citation += ` (${year}).`;
            if (title) citation += ` ${title}.`;
            if (journal) citation += ` <em>${journal}</em>`;
            if (volume) citation += `, ${volume}`;
            if (issue) citation += `(${issue})`;
            if (pages) citation += `, ${pages}`;
            if (doi) citation += `. https://doi.org/${doi}`;

            document.getElementById('citation-preview').innerHTML = citation || '<em>Citation will appear here as you fill in the form</em>';
        }

        // Add event listeners for real-time preview
        ['title', 'authors', 'year', 'journal_name', 'volume', 'issue', 'pages', 'doi'].forEach(function(id) {
            const element = document.getElementById(id);
            if (element) {
                element.addEventListener('input', updateCitationPreview);
            }
        });

        // Form submission handling
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const submitButton = form.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;

            submitButton.innerHTML = '<span class="animate-spin inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full mr-2"></span>Creating...';
            submitButton.disabled = true;

            // Re-enable after 10 seconds as fallback
            setTimeout(function() {
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
            }, 10000);
        });

        // File size validation
        const pdfFileInput = document.getElementById('pdf_file');
        pdfFileInput.addEventListener('change', function() {
            if (this.files[0]) {
                const fileSize = this.files[0].size / 1024 / 1024; // Size in MB
                if (fileSize > 20) {
                    this.setCustomValidity('File size must be less than 20MB');
                    this.classList.add('border-red-500');
                    this.classList.remove('border-gray-300');
                } else {
                    this.setCustomValidity('');
                    this.classList.remove('border-red-500');
                    this.classList.add('border-gray-300');
                }
            }
        });

        // Initialize citation preview
        updateCitationPreview();
    });
</script>
@endpush