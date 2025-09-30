@extends('layouts.admin-modern')

@section('title', 'Skills Management')
@section('page-title', 'Skills Management')

@section('content')
<!-- Header -->
<div class="flex flex-col lg:flex-row lg:justify-between lg:items-start gap-4 mb-6 lg:mb-8">
    <div class="flex-1">
        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-2">Skills Management</h1>
        <p class="text-gray-600">Manage your professional skills and competencies</p>
    </div>
    <a href="{{ route('admin.skills.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
        <i class="fas fa-plus mr-2"></i>Add New Skill
    </a>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
    <div class="p-4 lg:p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                       placeholder="Search by name or description..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
            </div>
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                <select name="category" id="category" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    <option value="">All Categories</option>
                    @foreach($categories as $value => $label)
                        <option value="{{ $value }}" {{ request('category') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Skills Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($skills as $skill)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="p-6">
                <!-- Header -->
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center">
                        @if($skill->icon)
                            <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg"
                                 style="background-color: {{ $skill->color ?? '#3B82F6' }}20; color: {{ $skill->color ?? '#3B82F6' }};">
                                <i class="{{ $skill->icon }}"></i>
                            </div>
                        @else
                            <div class="flex-shrink-0 w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-code text-gray-500"></i>
                            </div>
                        @endif
                        @if($skill->is_featured)
                            <div class="ml-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-star mr-1"></i>Featured
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Skill Name -->
                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $skill->name }}</h3>

                <!-- Category -->
                <div class="mb-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        {{ $skill->category_label }}
                    </span>
                </div>

                <!-- Proficiency Level -->
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-medium text-gray-700">{{ $skill->proficiency_label }}</span>
                        <span class="text-sm text-gray-500">{{ $skill->proficiency_level }}/5</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($skill->proficiency_level / 5) * 100 }}%"></div>
                    </div>
                </div>

                <!-- Experience -->
                @if($skill->years_experience)
                    <div class="mb-4">
                        <span class="text-sm text-gray-600">
                            <i class="fas fa-clock mr-1"></i>{{ $skill->years_experience }} years experience
                        </span>
                    </div>
                @endif

                <!-- Description -->
                @if($skill->description)
                    <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ Str::limit($skill->description, 100) }}</p>
                @endif

                <!-- Actions -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                    <div class="flex gap-2">
                        <a href="{{ route('admin.skills.show', $skill) }}" class="inline-flex items-center px-3 py-1 text-sm font-medium text-blue-700 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                            <i class="fas fa-eye mr-1"></i>View
                        </a>
                        <a href="{{ route('admin.skills.edit', $skill) }}" class="inline-flex items-center px-3 py-1 text-sm font-medium text-yellow-700 bg-yellow-50 border border-yellow-200 rounded-lg hover:bg-yellow-100 focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition-colors">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </a>
                    </div>
                    <form method="POST" action="{{ route('admin.skills.destroy', $skill) }}" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-3 py-1 text-sm font-medium text-red-700 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors" data-confirm-delete>
                            <i class="fas fa-trash mr-1"></i>Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-code text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No skills found</h3>
                <p class="text-gray-500 mb-6">Get started by adding your first skill.</p>
                <a href="{{ route('admin.skills.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Add First Skill
                </a>
            </div>
        </div>
    @endforelse
</div>

<!-- Pagination -->
@if($skills->hasPages())
    <div class="mt-8">
        {{ $skills->links() }}
    </div>
@endif
@endsection