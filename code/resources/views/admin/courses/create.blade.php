@extends('layouts.admin-modern')

@section('title', 'Create Course')
@section('page-title', 'Create Course')

@section('content')
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 lg:mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Create New Course</h1>
            <p class="text-gray-600 mt-1">Add a new course to your portfolio</p>
        </div>
        <a href="{{ route('admin.courses.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back to Courses
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 lg:p-6 border-b border-gray-100">
                    <h2 class="text-lg lg:text-xl font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-book mr-2 text-blue-600"></i>
                        Course Information
                    </h2>
                </div>
                <div class="p-4 lg:p-6">
                    <form method="POST" action="{{ route('admin.courses.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="space-y-6">
                            <!-- Course Title -->
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Course Title <span class="text-red-500">*</span></label>
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

                            <!-- Course Description -->
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Course Description <span class="text-red-500">*</span></label>
                                <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('description') border-red-500 @enderror"
                                          id="description"
                                          name="description"
                                          rows="4"
                                          required>{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Status and Level -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('status') border-red-500 @enderror" id="status" name="status">
                                        <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="upcoming" {{ old('status') === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                                        <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                    </select>
                                    @error('status')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="level" class="block text-sm font-medium text-gray-700 mb-2">Level</label>
                                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('level') border-red-500 @enderror" id="level" name="level">
                                        <option value="">Select Level</option>
                                        <option value="beginner" {{ old('level') === 'beginner' ? 'selected' : '' }}>Beginner</option>
                                        <option value="intermediate" {{ old('level') === 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                        <option value="advanced" {{ old('level') === 'advanced' ? 'selected' : '' }}>Advanced</option>
                                        <option value="graduate" {{ old('level') === 'graduate' ? 'selected' : '' }}>Graduate</option>
                                    </select>
                                    @error('level')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Dates -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                                    <input type="date"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('start_date') border-red-500 @enderror"
                                           id="start_date"
                                           name="start_date"
                                           value="{{ old('start_date') }}">
                                    @error('start_date')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                                    <input type="date"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('end_date') border-red-500 @enderror"
                                           id="end_date"
                                           name="end_date"
                                           value="{{ old('end_date') }}">
                                    @error('end_date')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Credits and Syllabus -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="credits" class="block text-sm font-medium text-gray-700 mb-2">Credits</label>
                                    <input type="number"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('credits') border-red-500 @enderror"
                                           id="credits"
                                           name="credits"
                                           min="1"
                                           max="10"
                                           value="{{ old('credits') }}">
                                    @error('credits')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="syllabus_file" class="block text-sm font-medium text-gray-700 mb-2">Syllabus (PDF)</label>
                                    <input type="file"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('syllabus_file') border-red-500 @enderror"
                                           id="syllabus_file"
                                           name="syllabus_file"
                                           accept=".pdf">
                                    @error('syllabus_file')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                    <p class="text-gray-500 text-sm mt-1">Upload a PDF syllabus file (max 10MB)</p>
                                </div>
                            </div>

                            <!-- Content -->
                            <div>
                                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Course Content</label>
                                <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('content') border-red-500 @enderror"
                                          id="content"
                                          name="content"
                                          rows="6">{{ old('content') }}</textarea>
                                @error('content')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-gray-500 text-sm mt-1">Detailed course description and overview</p>
                            </div>

                            <!-- Learning Objectives -->
                            <div>
                                <label for="learning_objectives" class="block text-sm font-medium text-gray-700 mb-2">Learning Objectives</label>
                                <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('learning_objectives') border-red-500 @enderror"
                                          id="learning_objectives"
                                          name="learning_objectives"
                                          rows="4">{{ old('learning_objectives') }}</textarea>
                                @error('learning_objectives')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Prerequisites -->
                            <div>
                                <label for="prerequisites" class="block text-sm font-medium text-gray-700 mb-2">Prerequisites</label>
                                <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('prerequisites') border-red-500 @enderror"
                                          id="prerequisites"
                                          name="prerequisites"
                                          rows="3">{{ old('prerequisites') }}</textarea>
                                @error('prerequisites')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Assessment Methods -->
                            <div>
                                <label for="assessment_methods" class="block text-sm font-medium text-gray-700 mb-2">Assessment Methods</label>
                                <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('assessment_methods') border-red-500 @enderror"
                                          id="assessment_methods"
                                          name="assessment_methods"
                                          rows="3">{{ old('assessment_methods') }}</textarea>
                                @error('assessment_methods')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Resources -->
                            <div>
                                <label for="resources" class="block text-sm font-medium text-gray-700 mb-2">Course Resources</label>
                                <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('resources') border-red-500 @enderror"
                                          id="resources"
                                          name="resources"
                                          rows="4">{{ old('resources') }}</textarea>
                                @error('resources')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-gray-500 text-sm mt-1">Required textbooks, materials, and additional resources</p>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="flex flex-col sm:flex-row sm:justify-between pt-6 border-t border-gray-100 gap-4">
                                <a href="{{ route('admin.courses.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                                    <i class="fas fa-times mr-2"></i>Cancel
                                </a>
                                <div class="flex flex-col sm:flex-row gap-2">
                                    <button type="submit" name="action" value="draft" class="inline-flex items-center justify-center px-4 py-2 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 transition-colors font-medium">
                                        <i class="fas fa-file mr-2"></i>Save as Draft
                                    </button>
                                    <button type="submit" name="action" value="publish" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                        <i class="fas fa-check mr-2"></i>Create Course
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="lg:col-span-1">
            <!-- Publishing Options -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
                <div class="p-4 lg:p-6 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-cog mr-2 text-blue-600"></i>
                        Publishing Options
                    </h2>
                </div>
                <div class="p-4 lg:p-6">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Visibility</label>
                        <div class="space-y-2">
                            <div class="flex items-start">
                                <input class="mt-1 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" type="radio" name="visibility" id="public" value="public" checked>
                                <label class="ml-3 block text-sm" for="public">
                                    <span class="font-medium text-gray-900">Public</span>
                                    <span class="text-gray-500 block text-xs">Visible to all visitors</span>
                                </label>
                            </div>
                            <div class="flex items-start">
                                <input class="mt-1 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" type="radio" name="visibility" id="private" value="private">
                                <label class="ml-3 block text-sm" for="private">
                                    <span class="font-medium text-gray-900">Private</span>
                                    <span class="text-gray-500 block text-xs">Only visible to you</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">SEO Settings</label>
                        <input type="text"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors mb-3"
                               name="meta_title"
                               placeholder="Custom page title"
                               value="{{ old('meta_title') }}">
                        <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                  name="meta_description"
                                  rows="2"
                                  placeholder="Meta description for search engines">{{ old('meta_description') }}</textarea>
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
                            <span>Use clear, descriptive titles for better discoverability</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5"></i>
                            <span>Include detailed learning objectives to set expectations</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5"></i>
                            <span>Upload a comprehensive syllabus PDF when available</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5"></i>
                            <span>Specify prerequisites to help students prepare</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5"></i>
                            <span>Set appropriate course level and credit hours</span>
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
        // Auto-generate slug from title
        const titleInput = document.getElementById('title');
        const slugPreview = document.createElement('div');
        slugPreview.className = 'text-gray-500 text-sm mt-1';
        slugPreview.id = 'slug-preview';
        titleInput.parentNode.appendChild(slugPreview);

        function updateSlugPreview() {
            const slug = titleInput.value
                .toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim('-');

            if (slug) {
                slugPreview.innerHTML = `<i class="fas fa-link mr-1"></i>URL: /courses/${slug}`;
            } else {
                slugPreview.innerHTML = '';
            }
        }

        titleInput.addEventListener('input', updateSlugPreview);
        updateSlugPreview();

        // Date validation
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');

        function validateDates() {
            if (startDateInput.value && endDateInput.value) {
                const startDate = new Date(startDateInput.value);
                const endDate = new Date(endDateInput.value);

                if (endDate < startDate) {
                    endDateInput.setCustomValidity('End date must be after start date');
                } else {
                    endDateInput.setCustomValidity('');
                }
            }
        }

        startDateInput.addEventListener('change', validateDates);
        endDateInput.addEventListener('change', validateDates);

        // Form submission handling
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const submitButton = e.submitter;
            const originalText = submitButton.innerHTML;

            submitButton.innerHTML = '<span class="animate-spin inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full mr-2"></span>Saving...';
            submitButton.disabled = true;

            // Re-enable after 10 seconds as fallback
            setTimeout(function() {
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
            }, 10000);
        });

        // File size validation
        const syllabusInput = document.getElementById('syllabus_file');
        syllabusInput.addEventListener('change', function() {
            if (this.files[0]) {
                const fileSize = this.files[0].size / 1024 / 1024; // Size in MB
                if (fileSize > 10) {
                    this.setCustomValidity('File size must be less than 10MB');
                    this.classList.add('border-red-500');
                    this.classList.remove('border-gray-300');
                } else {
                    this.setCustomValidity('');
                    this.classList.remove('border-red-500');
                    this.classList.add('border-gray-300');
                }
            }
        });
    });
</script>
@endpush