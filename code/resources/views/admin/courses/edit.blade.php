@extends('layouts.admin-modern')

@section('title', 'Edit Course')
@section('page-title', 'Edit Course')

@section('content')
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 lg:mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Edit Course</h1>
            <p class="text-gray-600 mt-1">{{ $course->title }}</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <a href="{{ route('admin.courses.show', $course) }}" class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors font-medium">
                <i class="fas fa-eye mr-2"></i>View Course
            </a>
            <a href="{{ route('admin.courses.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Courses
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.courses.update', $course) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-4 lg:p-6 border-b border-gray-100">
                        <h2 class="text-lg lg:text-xl font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-book mr-2 text-blue-600"></i>
                            Course Information
                        </h2>
                    </div>
                    <div class="p-4 lg:p-6 space-y-6">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Course Title <span class="text-red-500">*</span></label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('title') border-red-500 @enderror"
                                   id="title" name="title" value="{{ old('title', $course->title) }}" required>
                            @error('title')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('slug') border-red-500 @enderror"
                                   id="slug" name="slug" value="{{ old('slug', $course->slug) }}">
                            <p class="text-gray-500 text-sm mt-1">URL-friendly version of the title</p>
                            @error('slug')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="course_code" class="block text-sm font-medium text-gray-700 mb-2">Course Code</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('course_code') border-red-500 @enderror"
                                       id="course_code" name="course_code" value="{{ old('course_code', $course->course_code) }}">
                                <p class="text-gray-500 text-sm mt-1">e.g., CS101, MATH201</p>
                                @error('course_code')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="credits" class="block text-sm font-medium text-gray-700 mb-2">Credits</label>
                                <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('credits') border-red-500 @enderror"
                                       id="credits" name="credits" value="{{ old('credits', $course->credits) }}" min="1" max="10">
                                @error('credits')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description <span class="text-red-500">*</span></label>
                            <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('description') border-red-500 @enderror"
                                      id="description" name="description" rows="5" required>{{ old('description', $course->description) }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="objectives" class="block text-sm font-medium text-gray-700 mb-2">Learning Objectives</label>
                            <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('objectives') border-red-500 @enderror"
                                      id="objectives" name="objectives" rows="4">{{ old('objectives', $course->objectives) }}</textarea>
                            <p class="text-gray-500 text-sm mt-1">List the main learning outcomes (one per line)</p>
                            @error('objectives')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="prerequisites" class="block text-sm font-medium text-gray-700 mb-2">Prerequisites</label>
                            <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('prerequisites') border-red-500 @enderror"
                                      id="prerequisites" name="prerequisites" rows="3">{{ old('prerequisites', $course->prerequisites) }}</textarea>
                            <p class="text-gray-500 text-sm mt-1">Required knowledge or courses before taking this course</p>
                            @error('prerequisites')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="syllabus_content" class="block text-sm font-medium text-gray-700 mb-2">Syllabus Content</label>
                            <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('syllabus_content') border-red-500 @enderror"
                                      id="syllabus_content" name="syllabus_content" rows="10">{{ old('syllabus_content', $course->syllabus_content) }}</textarea>
                            <p class="text-gray-500 text-sm mt-1">Detailed course content, topics covered, schedule, etc.</p>
                            @error('syllabus_content')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
                    <div class="p-4 lg:p-6 border-b border-gray-100">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-cog mr-2 text-blue-600"></i>
                            Course Settings
                        </h2>
                    </div>
                    <div class="p-4 lg:p-6 space-y-4">
                        <div>
                            <label for="level" class="block text-sm font-medium text-gray-700 mb-2">Level</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('level') border-red-500 @enderror" id="level" name="level">
                                <option value="">Select Level</option>
                                <option value="undergraduate" {{ old('level', $course->level) === 'undergraduate' ? 'selected' : '' }}>Undergraduate</option>
                                <option value="graduate" {{ old('level', $course->level) === 'graduate' ? 'selected' : '' }}>Graduate</option>
                                <option value="phd" {{ old('level', $course->level) === 'phd' ? 'selected' : '' }}>PhD</option>
                                <option value="continuing_education" {{ old('level', $course->level) === 'continuing_education' ? 'selected' : '' }}>Continuing Education</option>
                            </select>
                            @error('level')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="department" class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('department') border-red-500 @enderror"
                                   id="department" name="department" value="{{ old('department', $course->department) }}">
                            @error('department')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                                <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('start_date') border-red-500 @enderror"
                                       id="start_date" name="start_date"
                                       value="{{ old('start_date', $course->start_date ? $course->start_date->format('Y-m-d') : '') }}">
                                @error('start_date')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                                <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('end_date') border-red-500 @enderror"
                                       id="end_date" name="end_date"
                                       value="{{ old('end_date', $course->end_date ? $course->end_date->format('Y-m-d') : '') }}">
                                @error('end_date')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="is_active" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('is_active') border-red-500 @enderror" id="is_active" name="is_active">
                                <option value="1" {{ old('is_active', $course->is_active) == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('is_active', $course->is_active) == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('is_active')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <div class="flex items-center">
                                <input class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('is_featured') border-red-500 @enderror"
                                       type="checkbox" id="is_featured" name="is_featured" value="1"
                                       {{ old('is_featured', $course->is_featured) ? 'checked' : '' }}>
                                <label class="ml-2 text-sm text-gray-700" for="is_featured">
                                    Featured Course
                                </label>
                            </div>
                            @error('is_featured')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
                    <div class="p-4 lg:p-6 border-b border-gray-100">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-image mr-2 text-blue-600"></i>
                            Course Image
                        </h2>
                    </div>
                    <div class="p-4 lg:p-6">
                        @if($course->image)
                            <div class="mb-4">
                                <img src="{{ Storage::url($course->image) }}"
                                     alt="Current course image"
                                     class="w-full h-48 object-cover rounded-lg">
                                <div class="flex items-center mt-2">
                                    <input class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" type="checkbox" name="remove_image" id="remove_image">
                                    <label class="ml-2 text-sm text-gray-700" for="remove_image">
                                        Remove current image
                                    </label>
                                </div>
                            </div>
                        @endif

                        <div>
                            <input type="file" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('image') border-red-500 @enderror"
                                   id="image" name="image" accept="image/*">
                            <p class="text-gray-500 text-sm mt-1">
                                @if($course->image)
                                    Upload a new image to replace the current one
                                @else
                                    Recommended: 800x400px
                                @endif
                            </p>
                            @error('image')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
                    <div class="p-4 lg:p-6 border-b border-gray-100">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-paperclip mr-2 text-blue-600"></i>
                            Attachments
                        </h2>
                    </div>
                    <div class="p-4 lg:p-6">
                        <div>
                            <label for="syllabus_file" class="block text-sm font-medium text-gray-700 mb-2">Syllabus PDF</label>
                            @if($course->syllabus_file)
                                <div class="mb-3">
                                    <a href="{{ Storage::url($course->syllabus_file) }}"
                                       target="_blank" class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors text-sm">
                                        <i class="fas fa-file-pdf mr-1"></i>View Current Syllabus
                                    </a>
                                    <div class="flex items-center mt-2">
                                        <input class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" type="checkbox" name="remove_syllabus" id="remove_syllabus">
                                        <label class="ml-2 text-sm text-gray-700" for="remove_syllabus">
                                            Remove current syllabus
                                        </label>
                                    </div>
                                </div>
                            @endif
                            <input type="file" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('syllabus_file') border-red-500 @enderror"
                                   id="syllabus_file" name="syllabus_file" accept=".pdf">
                            @error('syllabus_file')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        <i class="fas fa-check mr-2"></i>Update Course
                    </button>
                    <a href="{{ route('admin.courses.show', $course) }}" class="w-full inline-flex items-center justify-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate slug from title (only if slug is empty)
    const titleInput = document.getElementById('title');
    const slugInput = document.getElementById('slug');
    const originalSlug = slugInput.value;

    titleInput.addEventListener('input', function() {
        if (!slugInput.dataset.manual && !originalSlug) {
            const slug = this.value
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
            slugInput.value = slug;
        }
    });

    slugInput.addEventListener('input', function() {
        this.dataset.manual = 'true';
    });

    // Validate date range
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');

    function validateDates() {
        if (startDate.value && endDate.value && startDate.value > endDate.value) {
            endDate.setCustomValidity('End date must be after start date');
        } else {
            endDate.setCustomValidity('');
        }
    }

    startDate.addEventListener('change', validateDates);
    endDate.addEventListener('change', validateDates);
});
</script>
@endpush