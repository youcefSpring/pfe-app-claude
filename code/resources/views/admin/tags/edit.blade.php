@extends('layouts.admin-modern')

@section('title', 'Edit Tag')
@section('page-title', 'Edit Tag')

@section('content')
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 lg:mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Edit Tag</h1>
            <p class="text-gray-600 mt-1">Update tag information and settings</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.tags.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Tags
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 lg:p-8">
                <form method="POST" action="{{ route('admin.tags.update', $tag) }}">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <!-- Tag Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Tag Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('name') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                                   id="name"
                                   name="name"
                                   value="{{ old('name', $tag->name) }}"
                                   required
                                   placeholder="e.g., Web Development, Research, Laravel">
                            @error('name')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tag Slug -->
                        <div>
                            <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
                            <input type="text"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('slug') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                                   id="slug"
                                   name="slug"
                                   value="{{ old('slug', $tag->slug) }}"
                                   placeholder="Auto-generated from name">
                            @error('slug')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-gray-500 text-sm mt-1">
                                Must be URL-friendly (lowercase, hyphens only).
                                <span id="slug-preview" class="text-blue-600 font-medium"></span>
                                @if($tag->slug)
                                    <br><strong>Current URL:</strong> <code class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs font-mono">/tags/{{ $tag->slug }}</code>
                                @endif
                            </p>
                        </div>

                        <!-- Tag Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('description') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                                      id="description"
                                      name="description"
                                      rows="3"
                                      placeholder="Brief description of what this tag represents">{{ old('description', $tag->description) }}</textarea>
                            @error('description')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-gray-500 text-sm mt-1">Optional description to help you and others understand this tag's purpose</p>
                        </div>

                        <!-- Tag Color and Preview -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Tag Color -->
                            <div>
                                <label for="color" class="block text-sm font-medium text-gray-700 mb-2">Tag Color</label>
                                <div class="flex items-center gap-3">
                                    <input type="color"
                                           class="w-12 h-10 border border-gray-300 rounded-lg @error('color') border-red-300 @enderror"
                                           id="color"
                                           name="color"
                                           value="{{ old('color', $tag->color ?? '#6b7280') }}">
                                    <input type="text"
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('color') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                                           id="color-hex"
                                           name="color_hex"
                                           value="{{ old('color', $tag->color ?? '#6b7280') }}"
                                           pattern="^#[0-9a-fA-F]{6}$"
                                           placeholder="#6b7280">
                                    <button type="button" class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors" onclick="randomColor()">
                                        <i class="fas fa-random"></i>
                                    </button>
                                </div>
                                @error('color')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-gray-500 text-sm mt-1">Choose a color to visually identify this tag</p>
                            </div>

                            <!-- Tag Preview -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Preview</label>
                                <div class="flex items-center gap-3">
                                    <span id="tag-preview" class="px-3 py-1 rounded-full text-sm font-medium" style="background-color: {{ $tag->color ?? '#6b7280' }}; color: white;">
                                        {{ $tag->name }}
                                    </span>
                                    <span class="text-gray-500 text-sm">This is how your tag will appear</span>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex flex-col sm:flex-row sm:justify-between gap-4 pt-6 border-t border-gray-100">
                            <a href="{{ route('admin.tags.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                                <i class="fas fa-times mr-2"></i>Cancel
                            </a>
                            <button type="submit" class="inline-flex items-center justify-center px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                <i class="fas fa-check mr-2"></i>Update Tag
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Tag Statistics -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-chart-bar text-blue-600 mr-2"></i>
                    Tag Usage Statistics
                </h3>

                @php
                    $totalUsage = ($tag->posts_count ?? 0) + ($tag->projects_count ?? 0) + ($tag->courses_count ?? 0);
                @endphp

                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="font-medium text-gray-900">Total Usage</span>
                        <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium">{{ $totalUsage }} items</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Blog Posts</span>
                        <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-sm font-medium">{{ $tag->posts_count ?? 0 }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Projects</span>
                        <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-sm font-medium">{{ $tag->projects_count ?? 0 }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Courses</span>
                        <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-sm font-medium">{{ $tag->courses_count ?? 0 }}</span>
                    </div>

                    <div class="pt-3 border-t border-gray-100 space-y-2">
                        <div>
                            <span class="text-xs text-gray-500">Created:</span>
                            <div class="text-sm text-gray-900">{{ $tag->created_at->format('F d, Y \a\t g:i A') }}</div>
                        </div>

                        <div>
                            <span class="text-xs text-gray-500">Last Updated:</span>
                            <div class="text-sm text-gray-900">{{ $tag->updated_at->format('F d, Y \a\t g:i A') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Color Presets -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-palette text-blue-600 mr-2"></i>
                    Color Presets
                </h3>
                <div class="grid grid-cols-3 gap-3">
                    <button type="button" class="flex flex-col items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors color-preset" data-color="#3B82F6">
                        <div class="w-6 h-6 rounded-full mb-1" style="background-color: #3B82F6;"></div>
                        <span class="text-xs text-gray-600">Blue</span>
                    </button>
                    <button type="button" class="flex flex-col items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors color-preset" data-color="#10B981">
                        <div class="w-6 h-6 rounded-full mb-1" style="background-color: #10B981;"></div>
                        <span class="text-xs text-gray-600">Green</span>
                    </button>
                    <button type="button" class="flex flex-col items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors color-preset" data-color="#EF4444">
                        <div class="w-6 h-6 rounded-full mb-1" style="background-color: #EF4444;"></div>
                        <span class="text-xs text-gray-600">Red</span>
                    </button>
                    <button type="button" class="flex flex-col items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors color-preset" data-color="#F59E0B">
                        <div class="w-6 h-6 rounded-full mb-1" style="background-color: #F59E0B;"></div>
                        <span class="text-xs text-gray-600">Yellow</span>
                    </button>
                    <button type="button" class="flex flex-col items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors color-preset" data-color="#8B5CF6">
                        <div class="w-6 h-6 rounded-full mb-1" style="background-color: #8B5CF6;"></div>
                        <span class="text-xs text-gray-600">Purple</span>
                    </button>
                    <button type="button" class="flex flex-col items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors color-preset" data-color="#F97316">
                        <div class="w-6 h-6 rounded-full mb-1" style="background-color: #F97316;"></div>
                        <span class="text-xs text-gray-600">Orange</span>
                    </button>
                </div>
            </div>

            <!-- Related Content -->
            @if($totalUsage > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-link text-blue-600 mr-2"></i>
                        Related Content
                    </h3>

                    <div class="space-y-4">
                        @if(isset($tag->posts) && $tag->posts->count() > 0)
                            <div>
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Recent Blog Posts</h4>
                                <div class="space-y-2">
                                    @foreach($tag->posts->take(3) as $post)
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-gray-600">{{ Str::limit($post->title, 25) }}</span>
                                            <a href="{{ route('admin.blog.edit', $post) }}" class="text-blue-600 hover:text-blue-700 transition-colors">
                                                <i class="fas fa-edit text-xs"></i>
                                            </a>
                                        </div>
                                    @endforeach
                                    @if($tag->posts->count() > 3)
                                        <p class="text-xs text-gray-500">and {{ $tag->posts->count() - 3 }} more...</p>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if(isset($tag->projects) && $tag->projects->count() > 0)
                            <div>
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Recent Projects</h4>
                                <div class="space-y-2">
                                    @foreach($tag->projects->take(3) as $project)
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-gray-600">{{ Str::limit($project->title, 25) }}</span>
                                            <a href="{{ route('admin.projects.edit', $project) }}" class="text-blue-600 hover:text-blue-700 transition-colors">
                                                <i class="fas fa-edit text-xs"></i>
                                            </a>
                                        </div>
                                    @endforeach
                                    @if($tag->projects->count() > 3)
                                        <p class="text-xs text-gray-500">and {{ $tag->projects->count() - 3 }} more...</p>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if(isset($tag->courses) && $tag->courses->count() > 0)
                            <div>
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Recent Courses</h4>
                                <div class="space-y-2">
                                    @foreach($tag->courses->take(3) as $course)
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-gray-600">{{ Str::limit($course->title, 25) }}</span>
                                            <a href="{{ route('admin.courses.edit', $course) }}" class="text-blue-600 hover:text-blue-700 transition-colors">
                                                <i class="fas fa-edit text-xs"></i>
                                            </a>
                                        </div>
                                    @endforeach
                                    @if($tag->courses->count() > 3)
                                        <p class="text-xs text-gray-500">and {{ $tag->courses->count() - 3 }} more...</p>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Danger Zone -->
            <div class="bg-white rounded-xl shadow-sm border border-red-200 p-6">
                <h3 class="text-lg font-semibold text-red-900 mb-4 flex items-center">
                    <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                    Danger Zone
                </h3>

                @if($totalUsage > 0)
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-triangle text-yellow-600 mt-0.5 mr-2"></i>
                            <div class="text-sm text-yellow-800">
                                This tag is currently used by <strong>{{ $totalUsage }} items</strong>.
                                Deleting it will remove the tag from all associated content.
                            </div>
                        </div>
                    </div>
                @endif

                <p class="text-sm text-gray-600 mb-4">
                    Permanently delete this tag. This action cannot be undone.
                </p>
                <form method="POST" action="{{ route('admin.tags.destroy', $tag) }}" class="w-full">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium" data-confirm-delete>
                        <i class="fas fa-trash mr-2"></i>Delete Tag
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nameInput = document.getElementById('name');
        const slugInput = document.getElementById('slug');
        const slugPreview = document.getElementById('slug-preview');
        const colorInput = document.getElementById('color');
        const colorHexInput = document.getElementById('color-hex');
        const tagPreview = document.getElementById('tag-preview');

        // Update slug preview
        function updateSlugPreview() {
            const slug = slugInput.value;
            if (slug) {
                slugPreview.textContent = `New URL: /tags/${slug}`;
            } else {
                slugPreview.textContent = '';
            }
            updatePreview();
        }

        // Update preview
        function updatePreview() {
            const name = nameInput.value || '{{ $tag->name }}';
            const color = colorInput.value || '#6b7280';

            tagPreview.textContent = name;
            tagPreview.style.backgroundColor = color;
            tagPreview.style.color = getContrastColor(color);
        }

        // Get contrast color (white or black) based on background
        function getContrastColor(hexColor) {
            const r = parseInt(hexColor.substr(1, 2), 16);
            const g = parseInt(hexColor.substr(3, 2), 16);
            const b = parseInt(hexColor.substr(5, 2), 16);
            const yiq = ((r * 299) + (g * 587) + (b * 114)) / 1000;
            return (yiq >= 128) ? 'black' : 'white';
        }

        // Sync color inputs
        function syncColorInputs(source) {
            if (source === 'picker') {
                colorHexInput.value = colorInput.value;
            } else if (source === 'hex') {
                if (/^#[0-9a-fA-F]{6}$/.test(colorHexInput.value)) {
                    colorInput.value = colorHexInput.value;
                }
            }
            updatePreview();
        }

        // Event listeners
        nameInput.addEventListener('input', updatePreview);
        slugInput.addEventListener('input', updateSlugPreview);

        colorInput.addEventListener('input', function() {
            syncColorInputs('picker');
        });

        colorHexInput.addEventListener('input', function() {
            syncColorInputs('hex');
        });

        // Color preset buttons
        document.querySelectorAll('.color-preset').forEach(function(button) {
            button.addEventListener('click', function() {
                const color = this.dataset.color;
                colorInput.value = color;
                colorHexInput.value = color;
                updatePreview();
            });
        });

        // Random color function
        window.randomColor = function() {
            const colors = ['#3B82F6', '#10B981', '#EF4444', '#F59E0B', '#8B5CF6', '#F97316', '#06B6D4', '#EC4899', '#6B7280', '#64748B'];
            const randomColor = colors[Math.floor(Math.random() * colors.length)];
            colorInput.value = randomColor;
            colorHexInput.value = randomColor;
            updatePreview();
        };

        // Form submission handling
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const submitButton = form.querySelector('button[type="submit"]');
            const originalHTML = submitButton.innerHTML;

            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Updating...';
            submitButton.disabled = true;

            // Re-enable after 10 seconds as fallback
            setTimeout(function() {
                submitButton.innerHTML = originalHTML;
                submitButton.disabled = false;
            }, 10000);
        });

        // Delete confirmation
        const deleteButton = document.querySelector('[data-confirm-delete]');
        if (deleteButton) {
            deleteButton.addEventListener('click', function(e) {
                const totalUsage = {{ $totalUsage }};
                let message = 'Are you sure you want to delete this tag?';

                if (totalUsage > 0) {
                    message = `This tag is currently used by ${totalUsage} items. Deleting it will remove the tag from all associated content. Are you sure you want to continue?`;
                }

                if (!confirm(message)) {
                    e.preventDefault();
                }
            });
        }

        // Initialize preview
        updateSlugPreview();
        updatePreview();
    });
</script>
@endpush