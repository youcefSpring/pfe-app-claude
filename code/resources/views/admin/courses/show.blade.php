@extends('layouts.admin-modern')

@section('title', 'View Course')
@section('page-title', 'View Course')

@section('content')
<!-- Header -->
<div class="flex flex-col lg:flex-row lg:justify-between lg:items-start gap-4 mb-6 lg:mb-8">
    <div class="flex-1">
        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-2">{{ $course->title }}</h1>
        <p class="text-gray-600">Course Details</p>
    </div>
    <div class="flex flex-col sm:flex-row gap-3">
        <a href="{{ route('admin.courses.edit', $course) }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
            <i class="fas fa-edit mr-2"></i>Edit Course
        </a>
        <a href="{{ route('admin.courses.index') }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Back to Courses
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
    <div class="lg:col-span-2 space-y-6">
        <!-- Course Description -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 lg:p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-align-left text-blue-600 mr-3"></i>
                    Description
                </h3>
                <p class="text-gray-700 leading-relaxed">{{ $course->description }}</p>
            </div>
        </div>

        <!-- Course Content -->
        @if($course->content)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 lg:p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-file-text text-blue-600 mr-3"></i>
                        Course Content
                    </h3>
                    <div class="text-gray-700 leading-relaxed">
                        {!! nl2br(e($course->content)) !!}
                    </div>
                </div>
            </div>
        @endif

        <!-- Prerequisites -->
        @if($course->prerequisites)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 lg:p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-list-check text-blue-600 mr-3"></i>
                        Prerequisites
                    </h3>
                    <div class="text-gray-700 leading-relaxed">
                        {!! nl2br(e($course->prerequisites)) !!}
                    </div>
                </div>
            </div>
        @endif

        <!-- Learning Objectives -->
        @if($course->learning_objectives)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 lg:p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-target text-blue-600 mr-3"></i>
                        Learning Objectives
                    </h3>
                    <div class="text-gray-700 leading-relaxed">
                        {!! nl2br(e($course->learning_objectives)) !!}
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
                    @if($course->syllabus_file)
                        <a href="{{ Storage::url($course->syllabus_file) }}" target="_blank" class="inline-flex items-center px-3 py-2 text-sm font-medium text-green-700 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                            <i class="fas fa-file-pdf mr-2"></i>Download Syllabus
                        </a>
                    @endif
                    @if($course->is_published)
                        <a href="{{ route('courses.show', $course->slug) }}" target="_blank" class="inline-flex items-center px-3 py-2 text-sm font-medium text-blue-700 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                            <i class="fas fa-external-link-alt mr-2"></i>View Public Page
                        </a>
                    @endif
                    <a href="{{ route('admin.courses.edit', $course) }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-yellow-700 bg-yellow-50 border border-yellow-200 rounded-lg hover:bg-yellow-100 focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition-colors">
                        <i class="fas fa-edit mr-2"></i>Edit Course
                    </a>
                    <form method="POST" action="{{ route('admin.courses.destroy', $course) }}" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-3 py-2 text-sm font-medium text-red-700 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors" data-confirm-delete>
                            <i class="fas fa-trash mr-2"></i>Delete Course
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="lg:col-span-1 space-y-6">
        <!-- Course Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 lg:p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-info-circle text-blue-600 mr-3"></i>
                    Course Information
                </h3>

                <div class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-900 mb-1">Status</dt>
                        <dd>
                            @if($course->status === 'active')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">Active</span>
                            @elseif($course->status === 'upcoming')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">Upcoming</span>
                            @elseif($course->status === 'completed')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">Completed</span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">{{ ucfirst($course->status) }}</span>
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-900 mb-1">Slug</dt>
                        <dd class="text-sm text-gray-600 font-mono bg-gray-50 px-2 py-1 rounded">{{ $course->slug }}</dd>
                    </div>

                    @if($course->semester)
                        <div>
                            <dt class="text-sm font-medium text-gray-900 mb-1">Semester</dt>
                            <dd class="text-sm text-gray-600">{{ $course->semester }}</dd>
                        </div>
                    @endif

                    @if($course->year)
                        <div>
                            <dt class="text-sm font-medium text-gray-900 mb-1">Year</dt>
                            <dd class="text-sm text-gray-600">{{ $course->year }}</dd>
                        </div>
                    @endif

                    @if($course->credits)
                        <div>
                            <dt class="text-sm font-medium text-gray-900 mb-1">Credits</dt>
                            <dd class="text-sm text-gray-600">{{ $course->credits }}</dd>
                        </div>
                    @endif

                    @if($course->instructor)
                        <div>
                            <dt class="text-sm font-medium text-gray-900 mb-1">Instructor</dt>
                            <dd class="text-sm text-gray-600">{{ $course->instructor }}</dd>
                        </div>
                    @endif

                    <div>
                        <dt class="text-sm font-medium text-gray-900 mb-1">Visibility</dt>
                        <dd>
                            @if($course->is_published)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">Published</span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">Draft</span>
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-900 mb-1">Created</dt>
                        <dd class="text-sm text-gray-600">{{ $course->created_at->format('F d, Y \\a\\t g:i A') }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-900 mb-1">Last Updated</dt>
                        <dd class="text-sm text-gray-600">{{ $course->updated_at->format('F d, Y \\a\\t g:i A') }}</dd>
                    </div>
                </div>
            </div>
        </div>

        <!-- Course Files -->
        @if($course->syllabus_file)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 lg:p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-file text-blue-600 mr-3"></i>
                        Course Files
                    </h3>

                    <div class="space-y-3">
                        <a href="{{ Storage::url($course->syllabus_file) }}" target="_blank" class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-green-700 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                            <i class="fas fa-file-pdf mr-2"></i>Download Syllabus
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <!-- Public Visibility -->
        @if($course->is_published)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 lg:p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-chart-bar text-blue-600 mr-3"></i>
                        Public Visibility
                    </h3>
                    <div class="text-center">
                        <p class="text-gray-600 mb-4">This course is visible to the public</p>
                        <a href="{{ route('courses.show', $course->slug) }}" target="_blank" class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-blue-700 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                            <i class="fas fa-eye mr-2"></i>View Public Page
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection