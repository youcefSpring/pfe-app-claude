@extends('layouts.admin-modern')

@section('page-title', 'View Skill')

@section('content')
<!-- Header -->
<div class="flex flex-col lg:flex-row lg:justify-between lg:items-start gap-4 mb-6 lg:mb-8">
    <div class="flex-1">
        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-2">{{ $skill->name }}</h1>
        <p class="text-gray-600">Skill Details</p>
    </div>
    <div class="flex flex-col sm:flex-row gap-3">
        <a href="{{ route('admin.skills.edit', $skill) }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
            <i class="fas fa-edit mr-2"></i>Edit Skill
        </a>
        <a href="{{ route('admin.skills.index') }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Back to Skills
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
    <div class="lg:col-span-2 space-y-6">
        <!-- Skill Icon & Color Preview -->
        @if($skill->icon)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 lg:p-6 text-center">
                    <div class="inline-flex items-center justify-center w-24 h-24 rounded-full mb-4" style="background-color: {{ $skill->color ?? '#3B82F6' }}20;">
                        <i class="{{ $skill->icon }} text-4xl" style="color: {{ $skill->color ?? '#3B82F6' }};"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ $skill->name }}</h3>
                    @if($skill->category)
                        <p class="text-sm text-gray-500 mt-1">{{ ucfirst(str_replace('_', ' ', $skill->category)) }}</p>
                    @endif
                </div>
            </div>
        @endif

        <!-- Skill Description -->
        @if($skill->description)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 lg:p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-file-text text-blue-600 mr-3"></i>
                        Description
                    </h3>
                    <p class="text-gray-700 leading-relaxed">{{ $skill->description }}</p>
                </div>
            </div>
        @endif

        <!-- Proficiency Level -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 lg:p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-chart-bar text-blue-600 mr-3"></i>
                    Proficiency Level
                </h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">
                            Level {{ $skill->proficiency_level }} -
                            @switch($skill->proficiency_level)
                                @case(1) Beginner @break
                                @case(2) Novice @break
                                @case(3) Intermediate @break
                                @case(4) Advanced @break
                                @case(5) Expert @break
                                @default Unknown @break
                            @endswitch
                        </span>
                        <span class="text-sm text-gray-500">{{ $skill->proficiency_level }}/5</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="h-3 rounded-full transition-all duration-300 @if($skill->proficiency_level >= 4) bg-green-500 @elseif($skill->proficiency_level >= 3) bg-blue-500 @elseif($skill->proficiency_level >= 2) bg-yellow-500 @else bg-red-500 @endif"
                             style="width: {{ ($skill->proficiency_level / 5) * 100 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Years of Experience -->
        @if($skill->years_experience)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 lg:p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-clock text-blue-600 mr-3"></i>
                        Experience
                    </h3>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-blue-600 mb-2">{{ $skill->years_experience }}</div>
                        <div class="text-gray-600">{{ $skill->years_experience == 1 ? 'Year' : 'Years' }} of Experience</div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 lg:p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-bolt text-blue-600 mr-3"></i>
                    Quick Actions
                </h3>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.skills.edit', $skill) }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-yellow-700 bg-yellow-50 border border-yellow-200 rounded-lg hover:bg-yellow-100 focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition-colors">
                        <i class="fas fa-edit mr-2"></i>Edit Skill
                    </a>
                    <form method="POST" action="{{ route('admin.skills.destroy', $skill) }}" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-3 py-2 text-sm font-medium text-red-700 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors" data-confirm-delete>
                            <i class="fas fa-trash mr-2"></i>Delete Skill
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="lg:col-span-1 space-y-6">
        <!-- Skill Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 lg:p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-info-circle text-blue-600 mr-3"></i>
                    Skill Information
                </h3>

                <div class="space-y-4">
                    @if($skill->category)
                        <div>
                            <dt class="text-sm font-medium text-gray-900 mb-1">Category</dt>
                            <dd class="text-sm text-gray-600">{{ ucfirst(str_replace('_', ' ', $skill->category)) }}</dd>
                        </div>
                    @endif

                    <div>
                        <dt class="text-sm font-medium text-gray-900 mb-1">Proficiency Level</dt>
                        <dd>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                @if($skill->proficiency_level >= 4) bg-green-100 text-green-800
                                @elseif($skill->proficiency_level >= 3) bg-blue-100 text-blue-800
                                @elseif($skill->proficiency_level >= 2) bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800 @endif">
                                Level {{ $skill->proficiency_level }} -
                                @switch($skill->proficiency_level)
                                    @case(1) Beginner @break
                                    @case(2) Novice @break
                                    @case(3) Intermediate @break
                                    @case(4) Advanced @break
                                    @case(5) Expert @break
                                    @default Unknown @break
                                @endswitch
                            </span>
                        </dd>
                    </div>

                    @if($skill->years_experience)
                        <div>
                            <dt class="text-sm font-medium text-gray-900 mb-1">Years of Experience</dt>
                            <dd class="text-sm text-gray-600">{{ $skill->years_experience }} {{ $skill->years_experience == 1 ? 'year' : 'years' }}</dd>
                        </div>
                    @endif

                    @if($skill->sort_order !== null)
                        <div>
                            <dt class="text-sm font-medium text-gray-900 mb-1">Sort Order</dt>
                            <dd class="text-sm text-gray-600">{{ $skill->sort_order }}</dd>
                        </div>
                    @endif

                    @if($skill->is_featured)
                        <div>
                            <dt class="text-sm font-medium text-gray-900 mb-1">Featured</dt>
                            <dd>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">Featured Skill</span>
                            </dd>
                        </div>
                    @endif

                    <div>
                        <dt class="text-sm font-medium text-gray-900 mb-1">Created</dt>
                        <dd class="text-sm text-gray-600">{{ $skill->created_at->format('F d, Y \a\t g:i A') }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-900 mb-1">Last Updated</dt>
                        <dd class="text-sm text-gray-600">{{ $skill->updated_at->format('F d, Y \a\t g:i A') }}</dd>
                    </div>
                </div>
            </div>
        </div>

        <!-- Visual Details -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 lg:p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-palette text-blue-600 mr-3"></i>
                    Visual Details
                </h3>

                <div class="space-y-4">
                    @if($skill->icon)
                        <div>
                            <dt class="text-sm font-medium text-gray-900 mb-1">Icon</dt>
                            <dd class="flex items-center space-x-3">
                                <i class="{{ $skill->icon }} text-xl" style="color: {{ $skill->color ?? '#3B82F6' }};"></i>
                                <span class="text-sm text-gray-600 font-mono">{{ $skill->icon }}</span>
                            </dd>
                        </div>
                    @endif

                    @if($skill->color)
                        <div>
                            <dt class="text-sm font-medium text-gray-900 mb-1">Color</dt>
                            <dd class="flex items-center space-x-3">
                                <div class="w-6 h-6 rounded border border-gray-200" style="background-color: {{ $skill->color }};"></div>
                                <span class="text-sm text-gray-600 font-mono">{{ $skill->color }}</span>
                            </dd>
                        </div>
                    @endif

                    @if($skill->slug)
                        <div>
                            <dt class="text-sm font-medium text-gray-900 mb-1">Slug</dt>
                            <dd class="text-sm text-gray-600 font-mono">{{ $skill->slug }}</dd>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Skill Statistics -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 lg:p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-chart-pie text-blue-600 mr-3"></i>
                    Statistics
                </h3>

                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Skill Strength</span>
                        <span class="text-sm font-medium text-gray-900">
                            @if($skill->proficiency_level >= 4 && $skill->years_experience >= 5)
                                Exceptional
                            @elseif($skill->proficiency_level >= 3 && $skill->years_experience >= 3)
                                Strong
                            @elseif($skill->proficiency_level >= 2 && $skill->years_experience >= 1)
                                Developing
                            @else
                                Emerging
                            @endif
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Confidence Level</span>
                        <span class="text-sm font-medium text-gray-900">{{ $skill->proficiency_level * 20 }}%</span>
                    </div>

                    @if($skill->years_experience)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Experience Level</span>
                            <span class="text-sm font-medium text-gray-900">
                                @if($skill->years_experience >= 10)
                                    Senior
                                @elseif($skill->years_experience >= 5)
                                    Experienced
                                @elseif($skill->years_experience >= 2)
                                    Intermediate
                                @else
                                    Junior
                                @endif
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection