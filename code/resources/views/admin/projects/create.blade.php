@extends('layouts.admin-modern')

@section('page-title', 'Create Project')

@section('content')
<!-- Header -->
<div class="flex flex-col lg:flex-row lg:justify-between lg:items-start gap-4 mb-6 lg:mb-8">
    <div class="flex-1">
        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-2">Create New Project</h1>
        <p class="text-gray-600">Add a new professional web development project to showcase your skills and expertise</p>
    </div>
    <a href="{{ route('admin.projects.index') }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
        <i class="fas fa-arrow-left mr-2"></i>Back to Projects
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 lg:p-6">
                <form method="POST" action="{{ route('admin.projects.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="space-y-6">
                        <!-- Project Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Project Title <span class="text-red-500">*</span></label>
                            <input type="text"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('title') border-red-500 @enderror"
                                   id="title"
                                   name="title"
                                   value="{{ old('title') }}"
                                   required>
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Project Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Project Description <span class="text-red-500">*</span></label>
                            <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('description') border-red-500 @enderror"
                                      id="description"
                                      name="description"
                                      rows="4"
                                      required>{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status and Type -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('status') border-red-500 @enderror" id="status" name="status">
                                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="on-hold" {{ old('status') === 'on-hold' ? 'selected' : '' }}>On Hold</option>
                                    <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Project Type</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('type') border-red-500 @enderror" id="type" name="type">
                                    <option value="">Select Type</option>
                                    <option value="web-application" {{ old('type') === 'web-application' ? 'selected' : '' }}>Web Application</option>
                                    <option value="website" {{ old('type') === 'website' ? 'selected' : '' }}>Website</option>
                                    <option value="ecommerce" {{ old('type') === 'ecommerce' ? 'selected' : '' }}>E-commerce</option>
                                    <option value="api" {{ old('type') === 'api' ? 'selected' : '' }}>API/Backend</option>
                                    <option value="mobile-app" {{ old('type') === 'mobile-app' ? 'selected' : '' }}>Mobile App</option>
                                    <option value="cms" {{ old('type') === 'cms' ? 'selected' : '' }}>CMS/Blog</option>
                                    <option value="dashboard" {{ old('type') === 'dashboard' ? 'selected' : '' }}>Dashboard/Admin Panel</option>
                                    <option value="plugin" {{ old('type') === 'plugin' ? 'selected' : '' }}>Plugin/Extension</option>
                                    <option value="prototype" {{ old('type') === 'prototype' ? 'selected' : '' }}>Prototype/MVP</option>
                                </select>
                                @error('type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
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
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
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
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Funding and Client -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="funding_amount" class="block text-sm font-medium text-gray-700 mb-2">Project Budget ($)</label>
                                <input type="number"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('funding_amount') border-red-500 @enderror"
                                       id="funding_amount"
                                       name="funding_amount"
                                       min="0"
                                       step="0.01"
                                       value="{{ old('funding_amount') }}">
                                @error('funding_amount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="client_organization" class="block text-sm font-medium text-gray-700 mb-2">Client/Company</label>
                                <input type="text"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('client_organization') border-red-500 @enderror"
                                       id="client_organization"
                                       name="client_organization"
                                       value="{{ old('client_organization') }}">
                                @error('client_organization')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- URL Links -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="project_url" class="block text-sm font-medium text-gray-700 mb-2">Live Demo URL</label>
                                <input type="url"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('project_url') border-red-500 @enderror"
                                       id="project_url"
                                       name="project_url"
                                       value="{{ old('project_url') }}">
                                @error('project_url')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="repository_url" class="block text-sm font-medium text-gray-700 mb-2">Repository URL</label>
                                <input type="url"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('repository_url') border-red-500 @enderror"
                                       id="repository_url"
                                       name="repository_url"
                                       value="{{ old('repository_url') }}">
                                @error('repository_url')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Featured Image -->
                        <div>
                            <label for="featured_image" class="block text-sm font-medium text-gray-700 mb-2">Featured Image</label>
                            <input type="file"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 @error('featured_image') border-red-500 @enderror"
                                   id="featured_image"
                                   name="featured_image"
                                   accept="image/*">
                            @error('featured_image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Upload a screenshot or mockup of your project (max 5MB)</p>
                        </div>

                        <!-- Detailed Content -->
                        <div>
                            <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Project Details</label>
                            <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('content') border-red-500 @enderror"
                                      id="content"
                                      name="content"
                                      rows="8">{{ old('content') }}</textarea>
                            @error('content')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Detailed description of the project features, functionality, and technical implementation</p>
                        </div>

                        <!-- Technologies -->
                        <div>
                            <label for="technologies" class="block text-sm font-medium text-gray-700 mb-2">Technologies Used</label>
                            <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('technologies') border-red-500 @enderror"
                                      id="technologies"
                                      name="technologies"
                                      rows="3">{{ old('technologies') }}</textarea>
                            @error('technologies')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">List the programming languages, frameworks, and tools used in this project</p>
                        </div>

                        <!-- Collaborators -->
                        <div>
                            <label for="collaborators" class="block text-sm font-medium text-gray-700 mb-2">Collaborators</label>
                            <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('collaborators') border-red-500 @enderror"
                                      id="collaborators"
                                      name="collaborators"
                                      rows="3">{{ old('collaborators') }}</textarea>
                            @error('collaborators')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">List project collaborators, team members, or contributors</p>
                        </div>

                        <!-- Key Outcomes -->
                        <div>
                            <label for="key_outcomes" class="block text-sm font-medium text-gray-700 mb-2">Key Outcomes</label>
                            <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('key_outcomes') border-red-500 @enderror"
                                      id="key_outcomes"
                                      name="key_outcomes"
                                      rows="4">{{ old('key_outcomes') }}</textarea>
                            @error('key_outcomes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Summary of key features, deliverables, or achievements</p>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex justify-between pt-6">
                            <a href="{{ route('admin.projects.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                <i class="fas fa-times mr-2"></i>Cancel
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                <i class="fas fa-check mr-2"></i>Create Project
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="lg:col-span-1 space-y-6">
        <!-- Skills -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 lg:p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-code text-blue-600 mr-3"></i>
                    Skills Used
                </h3>
                <div class="space-y-3">
                    @if(isset($skills) && $skills->count() > 0)
                        @php
                            $skillsByCategory = $skills->groupBy('category');
                        @endphp
                        @foreach($skillsByCategory as $category => $categorySkills)
                            <div class="border-b border-gray-100 pb-3 last:border-b-0">
                                <h4 class="text-sm font-medium text-gray-800 mb-2">{{ ucfirst($category) }}</h4>
                                <div class="space-y-2">
                                    @foreach($categorySkills as $skill)
                                        <div class="flex items-center">
                                            <input type="checkbox"
                                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                                   name="skill_ids[]" value="{{ $skill->id }}"
                                                   id="skill_{{ $skill->id }}"
                                                   {{ in_array($skill->id, old('skill_ids', [])) ? 'checked' : '' }}>
                                            <label class="ml-2 text-sm text-gray-700 flex items-center" for="skill_{{ $skill->id }}">
                                                @if($skill->icon)
                                                    <i class="{{ $skill->icon }} mr-1" style="color: {{ $skill->color ?? '#6B7280' }}"></i>
                                                @endif
                                                {{ $skill->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-gray-500 text-sm">No skills available. <a href="{{ route('admin.skills.create') }}" class="text-blue-600 hover:text-blue-800">Create skills</a> to showcase your technical expertise.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tags -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 lg:p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-tags text-blue-600 mr-3"></i>
                    Tags
                </h3>
                <div class="space-y-2">
                    @if(isset($tags) && $tags->count() > 0)
                        @foreach($tags as $tag)
                            <div class="flex items-center">
                                <input type="checkbox"
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                       name="tags[]" value="{{ $tag->id }}"
                                       id="tag_{{ $tag->id }}"
                                       {{ in_array($tag->id, old('tags', [])) ? 'checked' : '' }}>
                                <label class="ml-2 text-sm text-gray-700" for="tag_{{ $tag->id }}">
                                    {{ $tag->name }}
                                </label>
                            </div>
                        @endforeach
                    @else
                        <p class="text-gray-500 text-sm">No tags available. <a href="{{ route('admin.tags.create') }}" class="text-blue-600 hover:text-blue-800">Create tags</a> to organize your projects.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Visibility Settings -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 lg:p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-eye text-blue-600 mr-3"></i>
                    Visibility
                </h3>

                <div class="space-y-4">
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <input type="radio" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" name="is_published" id="published" value="1" {{ old('is_published', '1') == '1' ? 'checked' : '' }}>
                            <label class="ml-2 block" for="published">
                                <span class="text-sm font-medium text-gray-900">Published</span>
                                <span class="text-sm text-gray-500 block">Visible to all visitors</span>
                            </label>
                        </div>
                        <div class="flex items-center">
                            <input type="radio" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" name="is_published" id="draft" value="0" {{ old('is_published') == '0' ? 'checked' : '' }}>
                            <label class="ml-2 block" for="draft">
                                <span class="text-sm font-medium text-gray-900">Draft</span>
                                <span class="text-sm text-gray-500 block">Only visible to you</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" id="is_featured" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                        <label class="ml-2 block" for="is_featured">
                            <span class="text-sm font-medium text-gray-900">Featured Project</span>
                            <span class="text-sm text-gray-500 block">Highlight this project on homepage</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tips -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 lg:p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-lightbulb text-yellow-500 mr-3"></i>
                    Tips
                </h3>
                <ul class="space-y-3 text-sm">
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5 flex-shrink-0"></i>
                        <span class="text-gray-700">Use clear, descriptive titles that reflect the project's functionality</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5 flex-shrink-0"></i>
                        <span class="text-gray-700">Select relevant skills to showcase your technical expertise</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5 flex-shrink-0"></i>
                        <span class="text-gray-700">Include live demo and repository links to show your work</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5 flex-shrink-0"></i>
                        <span class="text-gray-700">Upload high-quality screenshots or mockups</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5 flex-shrink-0"></i>
                        <span class="text-gray-700">Describe key features and technical implementation details</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5 flex-shrink-0"></i>
                        <span class="text-gray-700">Add relevant tags to help organize your portfolio</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-generate slug from title
        const titleInput = document.getElementById('title');
        const slugPreview = document.createElement('div');
        slugPreview.className = 'form-text text-muted';
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
                slugPreview.innerHTML = `<i class="fas fa-link mr-1"></i>URL: /projects/${slug}`;
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
            const submitButton = form.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;

            submitButton.innerHTML = '<span class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>Creating...';
            submitButton.disabled = true;

            // Re-enable after 10 seconds as fallback
            setTimeout(function() {
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
            }, 10000);
        });

        // File size validation
        const featuredImageInput = document.getElementById('featured_image');
        featuredImageInput.addEventListener('change', function() {
            if (this.files[0]) {
                const fileSize = this.files[0].size / 1024 / 1024; // Size in MB
                if (fileSize > 5) {
                    this.setCustomValidity('File size must be less than 5MB');
                    this.classList.add('border-red-500');
                } else {
                    this.setCustomValidity('');
                    this.classList.remove('border-red-500');
                }
            }
        });
    });
</script>
@endsection