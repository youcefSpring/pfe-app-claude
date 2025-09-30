@extends('layouts.admin-modern')

@section('title', 'Edit Skill')
@section('page-title', 'Edit Skill')

@section('content')
<!-- Header -->
<div class="flex flex-col lg:flex-row lg:justify-between lg:items-start gap-4 mb-6 lg:mb-8">
    <div class="flex-1">
        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-2">Edit Skill</h1>
        <p class="text-gray-600">Update skill information and proficiency details</p>
    </div>
    <div class="flex flex-col sm:flex-row gap-3">
        <a href="{{ route('admin.skills.show', $skill) }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
            <i class="fas fa-eye mr-2"></i>View Skill
        </a>
        <a href="{{ route('admin.skills.index') }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Back to Skills
        </a>
    </div>
</div>

<form method="POST" action="{{ route('admin.skills.update', $skill) }}">
    @csrf
    @method('PUT')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 lg:p-6">
                    <div class="space-y-6">
                        <!-- Skill Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Skill Name <span class="text-red-500">*</span></label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('name') border-red-500 @enderror"
                                   id="name" name="name" value="{{ old('name', $skill->name) }}" required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Slug -->
                        <div>
                            <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('slug') border-red-500 @enderror"
                                   id="slug" name="slug" value="{{ old('slug', $skill->slug) }}">
                            <p class="mt-1 text-sm text-gray-500">Leave empty to auto-generate from skill name</p>
                            @error('slug')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('description') border-red-500 @enderror"
                                      id="description" name="description" rows="4">{{ old('description', $skill->description) }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">Describe your experience and expertise with this skill</p>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Icon and Color -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="icon" class="block text-sm font-medium text-gray-700 mb-2">Icon</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('icon') border-red-500 @enderror"
                                       id="icon" name="icon" value="{{ old('icon', $skill->icon) }}" placeholder="fas fa-code">
                                <p class="mt-1 text-sm text-gray-500">Font Awesome icon class (e.g., fas fa-code)</p>
                                @error('icon')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="color" class="block text-sm font-medium text-gray-700 mb-2">Color</label>
                                <input type="color" class="w-full h-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('color') border-red-500 @enderror"
                                       id="color" name="color" value="{{ old('color', $skill->color ?? '#3B82F6') }}">
                                @error('color')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-1 space-y-6">
            <!-- Skill Details -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 lg:p-6 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Skill Details</h3>
                </div>
                <div class="p-4 lg:p-6 space-y-4">
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category <span class="text-red-500">*</span></label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('category') border-red-500 @enderror"
                                id="category" name="category" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $value => $label)
                                <option value="{{ $value }}" {{ old('category', $skill->category) === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('category')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="proficiency_level" class="block text-sm font-medium text-gray-700 mb-2">Proficiency Level <span class="text-red-500">*</span></label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('proficiency_level') border-red-500 @enderror"
                                id="proficiency_level" name="proficiency_level" required>
                            <option value="">Select Level</option>
                            <option value="1" {{ old('proficiency_level', $skill->proficiency_level) == '1' ? 'selected' : '' }}>1 - Beginner</option>
                            <option value="2" {{ old('proficiency_level', $skill->proficiency_level) == '2' ? 'selected' : '' }}>2 - Novice</option>
                            <option value="3" {{ old('proficiency_level', $skill->proficiency_level) == '3' ? 'selected' : '' }}>3 - Intermediate</option>
                            <option value="4" {{ old('proficiency_level', $skill->proficiency_level) == '4' ? 'selected' : '' }}>4 - Advanced</option>
                            <option value="5" {{ old('proficiency_level', $skill->proficiency_level) == '5' ? 'selected' : '' }}>5 - Expert</option>
                        </select>
                        @error('proficiency_level')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="years_experience" class="block text-sm font-medium text-gray-700 mb-2">Years of Experience</label>
                        <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('years_experience') border-red-500 @enderror"
                               id="years_experience" name="years_experience" value="{{ old('years_experience', $skill->years_experience) }}" min="0" max="50">
                        @error('years_experience')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">Sort Order</label>
                        <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('sort_order') border-red-500 @enderror"
                               id="sort_order" name="sort_order" value="{{ old('sort_order', $skill->sort_order ?? 0) }}">
                        <p class="mt-1 text-sm text-gray-500">Lower numbers appear first</p>
                        @error('sort_order')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded @error('is_featured') border-red-500 @enderror"
                               id="is_featured" name="is_featured" value="1" {{ old('is_featured', $skill->is_featured) ? 'checked' : '' }}>
                        <label for="is_featured" class="ml-2 block text-sm text-gray-900">
                            Featured Skill
                        </label>
                        @error('is_featured')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Skill Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 lg:p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-info-circle text-blue-600 mr-3"></i>
                        Skill Info
                    </h3>

                    <div class="space-y-3">
                        <div>
                            <span class="text-sm text-gray-500">Created:</span>
                            <div class="text-sm text-gray-900">{{ $skill->created_at->format('F d, Y \a\t g:i A') }}</div>
                        </div>

                        <div>
                            <span class="text-sm text-gray-500">Last Updated:</span>
                            <div class="text-sm text-gray-900">{{ $skill->updated_at->format('F d, Y \a\t g:i A') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col gap-3">
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                    <i class="fas fa-check mr-2"></i>Update Skill
                </button>
                <a href="{{ route('admin.skills.show', $skill) }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                    Cancel
                </a>
            </div>

            <!-- Danger Zone -->
            <div class="bg-white rounded-xl shadow-sm border border-red-200">
                <div class="p-4 lg:p-6">
                    <h3 class="text-lg font-semibold text-red-600 mb-4 flex items-center">
                        <i class="fas fa-exclamation-triangle mr-3"></i>
                        Danger Zone
                    </h3>
                    <p class="text-sm text-gray-600 mb-4">
                        Permanently delete this skill. This action cannot be undone.
                    </p>
                    <form method="POST" action="{{ route('admin.skills.destroy', $skill) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center justify-center w-full px-3 py-2 text-sm font-medium text-red-700 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors" data-confirm-delete>
                            <i class="fas fa-trash mr-2"></i>Delete Skill
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</form>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate slug from skill name
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');

    nameInput.addEventListener('input', function() {
        if (!slugInput.dataset.manual) {
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

    // Form submission handling
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const submitButton = form.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;

        submitButton.innerHTML = '<span class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>Updating...';
        submitButton.disabled = true;

        // Re-enable after 10 seconds as fallback
        setTimeout(function() {
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
        }, 10000);
    });
});
</script>
@endsection
@endsection