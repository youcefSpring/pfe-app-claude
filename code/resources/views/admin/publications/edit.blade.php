@extends('layouts.admin-modern')

@section('title', 'Edit Publication')
@section('page-title', 'Edit Publication')

@section('content')
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 lg:mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Edit Publication</h1>
            <p class="text-gray-600 mt-1">Update publication information and details</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <a href="{{ route('admin.publications.show', $publication) }}" class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors font-medium">
                <i class="fas fa-eye mr-2"></i>View Publication
            </a>
            <a href="{{ route('admin.publications.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Publications
            </a>
        </div>
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
                    <form method="POST" action="{{ route('admin.publications.update', $publication) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="space-y-6">
                            <!-- Publication Title -->
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Publication Title <span class="text-red-500">*</span></label>
                                <input type="text"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('title') border-red-500 @enderror"
                                       id="title"
                                       name="title"
                                       value="{{ old('title', $publication->title) }}"
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
                                          required>{{ old('authors', $publication->authors) }}</textarea>
                                @error('authors')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-gray-500 text-sm mt-1">List all authors in proper citation format (e.g., "Smith, J., Doe, A., & Johnson, M.")</p>
                            </div>

                        <!-- Type and Status -->
                        <div class="col-md-6">
                            <label for="type" class="form-label">Publication Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="">Select Type</option>
                                <option value="journal" {{ old('type', $publication->type) === 'journal' ? 'selected' : '' }}>Journal Article</option>
                                <option value="conference" {{ old('type', $publication->type) === 'conference' ? 'selected' : '' }}>Conference Paper</option>
                                <option value="book" {{ old('type', $publication->type) === 'book' ? 'selected' : '' }}>Book</option>
                                <option value="book_chapter" {{ old('type', $publication->type) === 'book_chapter' ? 'selected' : '' }}>Book Chapter</option>
                                <option value="thesis" {{ old('type', $publication->type) === 'thesis' ? 'selected' : '' }}>Thesis</option>
                                <option value="report" {{ old('type', $publication->type) === 'report' ? 'selected' : '' }}>Report</option>
                                <option value="preprint" {{ old('type', $publication->type) === 'preprint' ? 'selected' : '' }}>Preprint</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="status" class="form-label">Publication Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                <option value="published" {{ old('status', $publication->status) === 'published' ? 'selected' : '' }}>Published</option>
                                <option value="accepted" {{ old('status', $publication->status) === 'accepted' ? 'selected' : '' }}>Accepted</option>
                                <option value="under_review" {{ old('status', $publication->status) === 'under_review' ? 'selected' : '' }}>Under Review</option>
                                <option value="in_preparation" {{ old('status', $publication->status) === 'in_preparation' ? 'selected' : '' }}>In Preparation</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Journal/Venue Information -->
                        <div class="col-md-6">
                            <label for="journal_name" class="form-label">Journal/Conference Name</label>
                            <input type="text"
                                   class="form-control @error('journal_name') is-invalid @enderror"
                                   id="journal_name"
                                   name="journal_name"
                                   value="{{ old('journal_name', $publication->journal_name) }}">
                            @error('journal_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="venue" class="form-label">Venue/Publisher</label>
                            <input type="text"
                                   class="form-control @error('venue') is-invalid @enderror"
                                   id="venue"
                                   name="venue"
                                   value="{{ old('venue', $publication->venue) }}">
                            @error('venue')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Publication Details -->
                        <div class="col-md-4">
                            <label for="volume" class="form-label">Volume</label>
                            <input type="text"
                                   class="form-control @error('volume') is-invalid @enderror"
                                   id="volume"
                                   name="volume"
                                   value="{{ old('volume', $publication->volume) }}">
                            @error('volume')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="issue" class="form-label">Issue</label>
                            <input type="text"
                                   class="form-control @error('issue') is-invalid @enderror"
                                   id="issue"
                                   name="issue"
                                   value="{{ old('issue', $publication->issue) }}">
                            @error('issue')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="pages" class="form-label">Pages</label>
                            <input type="text"
                                   class="form-control @error('pages') is-invalid @enderror"
                                   id="pages"
                                   name="pages"
                                   value="{{ old('pages', $publication->pages) }}"
                                   placeholder="e.g., 123-145">
                            @error('pages')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                                       value="{{ old('year', $publication->year) }}"
                                       required>
                                @error('year')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                        <!-- DOI and URLs -->
                        <div class="col-md-6">
                            <label for="doi" class="form-label">DOI</label>
                            <input type="text"
                                   class="form-control @error('doi') is-invalid @enderror"
                                   id="doi"
                                   name="doi"
                                   value="{{ old('doi', $publication->doi) }}"
                                   placeholder="e.g., 10.1000/182">
                            @error('doi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="url" class="form-label">Publication URL</label>
                            <input type="url"
                                   class="form-control @error('url') is-invalid @enderror"
                                   id="url"
                                   name="url"
                                   value="{{ old('url', $publication->url) }}">
                            @error('url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- PDF File -->
                        <div class="col-12">
                            <label for="pdf_file" class="form-label">PDF File</label>
                            @if($publication->pdf_file)
                                <div class="mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-file-pdf text-danger"></i>
                                        <span>Current PDF: {{ basename($publication->pdf_file) }}</span>
                                        <a href="{{ Storage::url($publication->pdf_file) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </div>
                                    <p class="form-text mb-2">Upload a new PDF to replace the current one</p>
                                </div>
                            @endif
                            <input type="file"
                                   class="form-control @error('pdf_file') is-invalid @enderror"
                                   id="pdf_file"
                                   name="pdf_file"
                                   accept=".pdf">
                            @error('pdf_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Upload a new PDF file to replace the current one (max 20MB)</div>
                        </div>

                        <!-- Abstract -->
                        <div class="col-12">
                            <label for="abstract" class="form-label">Abstract</label>
                            <textarea class="form-control @error('abstract') is-invalid @enderror"
                                      id="abstract"
                                      name="abstract"
                                      rows="6">{{ old('abstract', $publication->abstract) }}</textarea>
                            @error('abstract')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Publication abstract or summary</div>
                        </div>

                        <!-- Keywords -->
                        <div class="col-12">
                            <label for="keywords" class="form-label">Keywords</label>
                            <textarea class="form-control @error('keywords') is-invalid @enderror"
                                      id="keywords"
                                      name="keywords"
                                      rows="2">{{ old('keywords', $publication->keywords) }}</textarea>
                            @error('keywords')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Comma-separated keywords or research topics</div>
                        </div>

                        <!-- Notes -->
                        <div class="col-12">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                      id="notes"
                                      name="notes"
                                      rows="3">{{ old('notes', $publication->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Additional notes or comments about this publication</div>
                        </div>

                            <!-- Submit Buttons -->
                            <div class="flex flex-col sm:flex-row sm:justify-between pt-6 border-t border-gray-100 gap-4">
                                <a href="{{ route('admin.publications.show', $publication) }}" class="inline-flex items-center justify-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                                    <i class="fas fa-times mr-2"></i>Cancel
                                </a>
                                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                    <i class="fas fa-check mr-2"></i>Update Publication
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
                            <input class="mt-1 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" type="radio" name="is_published" id="published" value="1" {{ old('is_published', $publication->is_published) == '1' ? 'checked' : '' }}>
                            <label class="ml-3 block text-sm" for="published">
                                <span class="font-medium text-gray-900">Published</span>
                                <span class="text-gray-500 block text-xs">Visible to all visitors</span>
                            </label>
                        </div>
                        <div class="flex items-start">
                            <input class="mt-1 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" type="radio" name="is_published" id="draft" value="0" {{ old('is_published', $publication->is_published) == '0' ? 'checked' : '' }}>
                            <label class="ml-3 block text-sm" for="draft">
                                <span class="font-medium text-gray-900">Draft</span>
                                <span class="text-gray-500 block text-xs">Only visible to you</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <input class="mt-1 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured', $publication->is_featured) ? 'checked' : '' }}>
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
                        Citation will appear here as you update the form
                    </div>
                </div>
            </div>

            <!-- Publication Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
                <div class="p-4 lg:p-6 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                        Publication Info
                    </h2>
                </div>
                <div class="p-4 lg:p-6 space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Created:</span>
                        <span class="font-medium text-gray-900">{{ $publication->created_at->format('M j, Y') }}</span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Last Updated:</span>
                        <span class="font-medium text-gray-900">{{ $publication->updated_at->format('M j, Y') }}</span>
                    </div>

                    @if($publication->is_published)
                        <div class="pt-3">
                            <a href="{{ url('/publications/' . Str::slug($publication->title)) }}" target="_blank" class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors font-medium">
                                <i class="fas fa-eye mr-2"></i>View Public Page
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="bg-white rounded-xl shadow-sm border border-red-200">
                <div class="p-4 lg:p-6 border-b border-red-200">
                    <h2 class="text-lg font-semibold text-red-600 flex items-center">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Danger Zone
                    </h2>
                </div>
                <div class="p-4 lg:p-6">
                    <p class="text-sm text-gray-600 mb-4">
                        Permanently delete this publication. This action cannot be undone.
                    </p>
                    <form method="POST" action="{{ route('admin.publications.destroy', $publication) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium" onclick="return confirm('Are you sure you want to delete this publication?')">
                            <i class="fas fa-trash mr-2"></i>Delete Publication
                        </button>
                    </form>
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

            document.getElementById('citation-preview').innerHTML = citation || '<em>Citation will appear here as you update the form</em>';
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

            submitButton.innerHTML = '<span class="animate-spin inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full mr-2"></span>Updating...';
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