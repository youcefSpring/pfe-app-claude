@extends('layouts.admin-modern')

@section('title', 'Publications Management')
@section('page-title', 'Publications')

@section('content')
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 lg:mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Publications</h1>
            <p class="text-gray-600 mt-1">Showcase your research papers, articles, and academic publications</p>
        </div>
        <a href="{{ route('admin.publications.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
            <i class="fas fa-plus mr-2"></i>
            New Publication
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 lg:p-6 mb-6 lg:mb-8">
        <form method="GET" action="{{ route('admin.publications.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" id="type" name="type">
                    <option value="">All Types</option>
                    <option value="journal" {{ request('type') == 'journal' ? 'selected' : '' }}>Journal Article</option>
                    <option value="conference" {{ request('type') == 'conference' ? 'selected' : '' }}>Conference Paper</option>
                    <option value="book" {{ request('type') == 'book' ? 'selected' : '' }}>Book</option>
                    <option value="book_chapter" {{ request('type') == 'book_chapter' ? 'selected' : '' }}>Book Chapter</option>
                    <option value="thesis" {{ request('type') == 'thesis' ? 'selected' : '' }}>Thesis</option>
                    <option value="report" {{ request('type') == 'report' ? 'selected' : '' }}>Report</option>
                    <option value="preprint" {{ request('type') == 'preprint' ? 'selected' : '' }}>Preprint</option>
                </select>
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" id="status" name="status">
                    <option value="">All Status</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                    <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                    <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>Under Review</option>
                    <option value="in_preparation" {{ request('status') == 'in_preparation' ? 'selected' : '' }}>In Preparation</option>
                </select>
            </div>
            <div>
                <label for="year" class="block text-sm font-medium text-gray-700 mb-2">Year</label>
                <input type="number" name="year" id="year" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" placeholder="Publication Year" value="{{ request('year') }}">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-search"></i>
                </button>
                <a href="{{ route('admin.publications.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Publications Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-4 lg:p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h2 class="text-lg lg:text-xl font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-file-alt mr-2 text-blue-600"></i>
                    Publications ({{ $publications->total() }})
                </h2>
                @if(request()->hasAny(['type', 'status', 'year']))
                    <span class="text-sm text-gray-500">Filtered results</span>
                @endif
            </div>
        </div>

        @if($publications->count() > 0)
            <!-- Desktop Table -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-sm text-gray-600 border-b border-gray-100">
                            <th class="pb-3 px-6 font-medium">Publication</th>
                            <th class="pb-3 px-3 font-medium">Type</th>
                            <th class="pb-3 px-3 font-medium">Status</th>
                            <th class="pb-3 px-3 font-medium">Year</th>
                            <th class="pb-3 px-3 font-medium">Journal/Venue</th>
                            <th class="pb-3 px-6 font-medium w-32">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @foreach($publications as $publication)
                            <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                                <td class="py-4 px-6">
                                    <div>
                                        <h3 class="font-medium text-gray-900 mb-1">
                                            <a href="{{ route('admin.publications.show', $publication) }}" class="hover:text-blue-600 transition-colors">
                                                {{ $publication->title }}
                                            </a>
                                        </h3>
                                        @if($publication->authors)
                                            <p class="text-gray-500 text-xs">{{ Str::limit($publication->authors, 80) }}</p>
                                        @else
                                            <p class="text-gray-400 text-xs">No authors specified</p>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-3">
                                    <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-medium">
                                        {{ ucwords(str_replace('_', ' ', $publication->type)) }}
                                    </span>
                                </td>
                                <td class="py-4 px-3">
                                    @if($publication->status === 'published')
                                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">Published</span>
                                    @elseif($publication->status === 'accepted')
                                        <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">Accepted</span>
                                    @elseif($publication->status === 'under_review')
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium">Under Review</span>
                                    @elseif($publication->status === 'in_preparation')
                                        <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-medium">In Preparation</span>
                                    @else
                                        <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-medium">{{ ucfirst($publication->status) }}</span>
                                    @endif
                                </td>
                                <td class="py-4 px-3 text-gray-600">
                                    @if($publication->year)
                                        <span class="text-xs">{{ $publication->year }}</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="py-4 px-3 text-gray-600">
                                    @if($publication->journal_name || $publication->venue)
                                        <span class="text-xs">{{ $publication->journal_name ?: $publication->venue }}</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('admin.publications.show', $publication) }}" class="text-blue-600 hover:text-blue-700 transition-colors" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.publications.edit', $publication) }}" class="text-green-600 hover:text-green-700 transition-colors" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($publication->slug)
                                            <a href="{{ route('publications.show', $publication->slug) }}" target="_blank" class="text-purple-600 hover:text-purple-700 transition-colors" title="View on Site">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        @endif
                                        <form method="POST" action="{{ route('admin.publications.destroy', $publication) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-700 transition-colors" title="Delete" onclick="return confirm('Are you sure you want to delete this publication?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="lg:hidden divide-y divide-gray-100">
                @foreach($publications as $publication)
                    <div class="p-4 lg:p-6">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h3 class="font-medium text-gray-900 mb-1">
                                    <a href="{{ route('admin.publications.show', $publication) }}" class="hover:text-blue-600 transition-colors">
                                        {{ $publication->title }}
                                    </a>
                                </h3>
                                @if($publication->authors)
                                    <p class="text-gray-500 text-sm mb-2">{{ Str::limit($publication->authors, 100) }}</p>
                                @endif
                            </div>
                            <div class="ml-4">
                                @if($publication->status === 'published')
                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">Published</span>
                                @elseif($publication->status === 'accepted')
                                    <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">Accepted</span>
                                @elseif($publication->status === 'under_review')
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium">Under Review</span>
                                @elseif($publication->status === 'in_preparation')
                                    <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-medium">In Preparation</span>
                                @else
                                    <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-medium">{{ ucfirst($publication->status) }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-sm text-gray-500 mb-3">
                            <div class="flex items-center space-x-4">
                                <span class="flex items-center">
                                    <i class="fas fa-tag mr-1"></i>
                                    {{ ucwords(str_replace('_', ' ', $publication->type)) }}
                                </span>
                                @if($publication->year)
                                    <span class="flex items-center">
                                        <i class="fas fa-calendar mr-1"></i>
                                        {{ $publication->year }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        @if($publication->journal_name || $publication->venue)
                            <div class="text-sm text-gray-600 mb-3">
                                <i class="fas fa-journal-whills mr-1"></i>
                                {{ $publication->journal_name ?: $publication->venue }}
                            </div>
                        @endif

                        <div class="flex items-center space-x-4">
                            <a href="{{ route('admin.publications.show', $publication) }}" class="text-blue-600 hover:text-blue-700 transition-colors text-sm font-medium">
                                <i class="fas fa-eye mr-1"></i>View
                            </a>
                            <a href="{{ route('admin.publications.edit', $publication) }}" class="text-green-600 hover:text-green-700 transition-colors text-sm font-medium">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </a>
                            @if($publication->slug)
                                <a href="{{ route('publications.show', $publication->slug) }}" target="_blank" class="text-purple-600 hover:text-purple-700 transition-colors text-sm font-medium">
                                    <i class="fas fa-external-link-alt mr-1"></i>View on Site
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($publications->hasPages())
                <div class="p-4 lg:p-6 border-t border-gray-100">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-sm text-gray-600 mb-4 sm:mb-0">
                            Showing {{ $publications->firstItem() }} to {{ $publications->lastItem() }} of {{ $publications->total() }} results
                        </p>
                        <div class="flex items-center space-x-2">
                            {{ $publications->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="p-8 lg:p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-file-alt text-gray-400 text-2xl"></i>
                </div>
                @if(request()->hasAny(['type', 'status', 'year']))
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No publications found</h3>
                    <p class="text-gray-600 mb-4">No publications match your current filters.</p>
                    <a href="{{ route('admin.publications.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Clear Filters
                    </a>
                @else
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No publications yet</h3>
                    <p class="text-gray-600 mb-4">Start showcasing your research papers and academic publications.</p>
                    <a href="{{ route('admin.publications.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Add Your First Publication
                    </a>
                @endif
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-submit search form on select changes
        const typeSelect = document.getElementById('type');
        const statusSelect = document.getElementById('status');
        const yearInput = document.getElementById('year');

        [typeSelect, statusSelect].forEach(function(select) {
            if (select) {
                select.addEventListener('change', function() {
                    this.form.submit();
                });
            }
        });

        // Optional: Auto-submit on year input change (after typing stops)
        if (yearInput) {
            let timeout;
            yearInput.addEventListener('input', function() {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    if (this.value.length === 4 || this.value === '') {
                        this.form.submit();
                    }
                }, 1000);
            });
        }
    });
</script>
@endpush