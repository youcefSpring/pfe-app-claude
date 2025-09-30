@extends('layouts.admin-modern')

@section('title', 'Create New Tag')
@section('page-title', 'Create Tag')

@section('content')
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 lg:mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Create New Tag</h1>
            <p class="text-gray-600 mt-1">Add a new tag to organize your content</p>
        </div>
        <a href="{{ route('admin.tags.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Tags
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 lg:p-8">
                <form method="POST" action="{{ route('admin.tags.store') }}">
                    @csrf

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
                                   value="{{ old('name') }}"
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
                                   value="{{ old('slug') }}"
                                   placeholder="Auto-generated from name">
                            @error('slug')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-gray-500 text-sm mt-1">
                                Leave empty to auto-generate from tag name. Must be URL-friendly (lowercase, hyphens only).
                                <span id="slug-preview" class="text-blue-600 font-medium"></span>
                            </p>
                        </div>

                        <!-- Tag Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('description') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                                      id="description"
                                      name="description"
                                      rows="3"
                                      placeholder="Brief description of what this tag represents">{{ old('description') }}</textarea>
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
                                           value="{{ old('color', '#6b7280') }}">
                                    <input type="text"
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('color') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                                           id="color-hex"
                                           name="color_hex"
                                           value="{{ old('color', '#6b7280') }}"
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
                                    <span id="tag-preview" class="px-3 py-1 rounded-full text-sm font-medium" style="background-color: #6b7280; color: white;">
                                        Sample Tag
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
                                <i class="fas fa-check mr-2"></i>Create Tag
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Tag Usage Guide -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-question-circle text-blue-600 mr-2"></i>
                    Tag Usage Guide
                </h3>
                <div class="space-y-4 text-sm">
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">What are tags?</h4>
                        <p class="text-gray-600">Tags help categorize and organize your content, making it easier for visitors to find related posts, projects, and courses.</p>
                    </div>

                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Best Practices:</h4>
                        <ul class="text-gray-600 space-y-1 list-disc list-inside">
                            <li>Use descriptive, specific names</li>
                            <li>Keep names concise (1-3 words)</li>
                            <li>Use consistent naming conventions</li>
                            <li>Avoid overly generic terms</li>
                            <li>Check for existing similar tags</li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Examples:</h4>
                        <div class="flex flex-wrap gap-2">
                            <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">Laravel</span>
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">Web Development</span>
                            <span class="px-2 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-medium">Research</span>
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium">Machine Learning</span>
                            <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-medium">JavaScript</span>
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

            <!-- Existing Tags -->
            @if(isset($existingTags) && $existingTags->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-tags text-blue-600 mr-2"></i>
                        Existing Tags
                    </h3>
                    <p class="text-sm text-gray-600 mb-3">{{ $existingTags->count() }} tags already created</p>
                    <div class="max-h-48 overflow-y-auto">
                        <div class="flex flex-wrap gap-2">
                            @foreach($existingTags->take(20) as $tag)
                                <span class="px-2 py-1 rounded-full text-xs font-medium"
                                      style="background-color: {{ $tag->color ?? '#6b7280' }}; color: white;"
                                      title="{{ $tag->description ?? $tag->name }}">
                                    {{ $tag->name }}
                                </span>
                            @endforeach
                            @if($existingTags->count() > 20)
                                <span class="text-xs text-gray-500">and {{ $existingTags->count() - 20 }} more...</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
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

        // Auto-generate slug from name
        function updateSlug() {
            if (!slugInput.dataset.manual && nameInput.value) {
                const slug = nameInput.value
                    .toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .replace(/^-+|-+$/g, '');

                slugInput.value = slug;
                slugPreview.textContent = slug ? `URL: /tags/${slug}` : '';
            }
            updatePreview();
        }

        // Update preview
        function updatePreview() {
            const name = nameInput.value || 'Sample Tag';
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
        nameInput.addEventListener('input', updateSlug);

        slugInput.addEventListener('input', function() {
            this.dataset.manual = 'true';
            updatePreview();
        });

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

            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating...';
            submitButton.disabled = true;

            // Re-enable after 10 seconds as fallback
            setTimeout(function() {
                submitButton.innerHTML = originalHTML;
                submitButton.disabled = false;
            }, 10000);
        });

        // Initialize preview
        updateSlug();
        updatePreview();
    });
</script>
@endpush