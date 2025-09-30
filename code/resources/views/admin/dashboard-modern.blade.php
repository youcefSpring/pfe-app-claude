@extends('layouts.admin-modern')

@section('title', 'Teacher Admin Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-8">
        <!-- Active Courses -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 lg:p-6 card-hover">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs lg:text-sm text-gray-600 mb-1">Active Courses</p>
                    <p class="text-2xl lg:text-3xl font-bold text-gray-900">{{ $stats['courses'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-book text-blue-600 text-lg lg:text-xl"></i>
                </div>
            </div>
            <div class="mt-3 lg:mt-4 flex items-center text-xs lg:text-sm">
                @if($stats['courses'] > 0)
                    <span class="text-green-600 font-medium">Active</span>
                    <span class="text-gray-600 ml-1">teaching now</span>
                @else
                    <span class="text-gray-600">No courses yet</span>
                @endif
            </div>
        </div>

        <!-- Blog Posts -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 lg:p-6 card-hover">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs lg:text-sm text-gray-600 mb-1">Blog Posts</p>
                    <p class="text-2xl lg:text-3xl font-bold text-gray-900">{{ $stats['blog_posts'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-blog text-green-600 text-lg lg:text-xl"></i>
                </div>
            </div>
            <div class="mt-3 lg:mt-4 flex items-center text-xs lg:text-sm">
                @if($contentStatus['published_posts'] ?? 0 > 0)
                    <span class="text-green-600 font-medium">{{ $contentStatus['published_posts'] ?? 0 }}</span>
                    <span class="text-gray-600 ml-1">published</span>
                @else
                    <span class="text-gray-600">No posts yet</span>
                @endif
            </div>
        </div>

        <!-- Projects -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 lg:p-6 card-hover">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs lg:text-sm text-gray-600 mb-1">Projects</p>
                    <p class="text-2xl lg:text-3xl font-bold text-gray-900">{{ $stats['projects'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-project-diagram text-purple-600 text-lg lg:text-xl"></i>
                </div>
            </div>
            <div class="mt-3 lg:mt-4 flex items-center text-xs lg:text-sm">
                @if($contentStatus['featured_projects'] ?? 0 > 0)
                    <span class="text-purple-600 font-medium">{{ $contentStatus['featured_projects'] ?? 0 }}</span>
                    <span class="text-gray-600 ml-1">featured</span>
                @else
                    <span class="text-gray-600">No projects yet</span>
                @endif
            </div>
        </div>

        <!-- Messages -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 lg:p-6 card-hover">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs lg:text-sm text-gray-600 mb-1">Unread Messages</p>
                    <p class="text-2xl lg:text-3xl font-bold text-gray-900">{{ $stats['unread_messages'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-envelope text-red-600 text-lg lg:text-xl"></i>
                </div>
            </div>
            <div class="mt-3 lg:mt-4 flex items-center text-xs lg:text-sm">
                @if($stats['unread_messages'] > 0)
                    <span class="text-red-600 font-medium">{{ $stats['total_messages'] ?? 0 }}</span>
                    <span class="text-gray-600 ml-1">total received</span>
                @else
                    <span class="text-green-600">All caught up!</span>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
        <!-- Main Content Area -->
        <div class="lg:col-span-2 space-y-8">
            <!-- My Courses -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 lg:p-6 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg lg:text-xl font-semibold text-gray-900">My Courses</h2>
                        @if($recentCourses->count() > 0)
                            <a href="{{ route('admin.courses.index') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">View All</a>
                        @endif
                    </div>
                </div>
                <div class="p-4 lg:p-6">
                    @if($recentCourses->count() > 0)
                        <!-- Desktop Table -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="text-left text-sm text-gray-600 border-b border-gray-100">
                                        <th class="pb-3 font-medium">Course Name</th>
                                        <th class="pb-3 font-medium">Status</th>
                                        <th class="pb-3 font-medium">Created</th>
                                        <th class="pb-3 font-medium">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm">
                                    @foreach($recentCourses as $course)
                                        <tr class="border-b border-gray-50">
                                            <td class="py-4">
                                                <div>
                                                    <p class="font-medium text-gray-900">{{ $course->title }}</p>
                                                    @if($course->description)
                                                        <p class="text-xs text-gray-500 mt-1">{{ Str::limit($course->description, 50) }}</p>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="py-4">
                                                @if($course->is_active ?? true)
                                                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">Active</span>
                                                @else
                                                    <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-medium">Draft</span>
                                                @endif
                                            </td>
                                            <td class="py-4 text-gray-700">{{ $course->created_at->format('M j, Y') }}</td>
                                            <td class="py-4">
                                                <button class="px-4 py-2 bg-blue-50 text-blue-600 rounded-lg text-xs font-medium hover:bg-blue-100 transition-colors">
                                                    View
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Cards -->
                        <div class="md:hidden space-y-4">
                            @foreach($recentCourses as $course)
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex items-start justify-between mb-2">
                                        <h3 class="font-medium text-gray-900 text-sm">{{ $course->title }}</h3>
                                        @if($course->is_active ?? true)
                                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">Active</span>
                                        @else
                                            <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-medium">Draft</span>
                                        @endif
                                    </div>
                                    @if($course->description)
                                        <p class="text-xs text-gray-600 mb-3">{{ Str::limit($course->description, 80) }}</p>
                                    @endif
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-500">{{ $course->created_at->format('M j, Y') }}</span>
                                        <button class="px-3 py-1 bg-blue-50 text-blue-600 rounded text-xs font-medium">View</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-book text-gray-400 text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No courses yet</h3>
                            <p class="text-gray-600 mb-4">Create your first course to get started</p>
                            <a href="{{ route('admin.courses.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors inline-block">
                                Create Course
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Projects -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 lg:p-6 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg lg:text-xl font-semibold text-gray-900">Recent Projects</h2>
                        @if($recentProjects->count() > 0)
                            <a href="{{ route('admin.projects.index') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">View All</a>
                        @endif
                    </div>
                </div>
                <div class="p-4 lg:p-6">
                    @if($recentProjects->count() > 0)
                        <!-- Desktop Table -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="text-left text-sm text-gray-600 border-b border-gray-100">
                                        <th class="pb-3 font-medium">Project Name</th>
                                        <th class="pb-3 font-medium">Technology</th>
                                        <th class="pb-3 font-medium">Status</th>
                                        <th class="pb-3 font-medium">Created</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm">
                                    @foreach($recentProjects as $project)
                                        <tr class="border-b border-gray-50">
                                            <td class="py-4">
                                                <div>
                                                    <p class="font-medium text-gray-900">{{ $project->title }}</p>
                                                    @if($project->description)
                                                        <p class="text-xs text-gray-500 mt-1">{{ Str::limit($project->description, 50) }}</p>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="py-4">
                                                @if($project->tags && $project->tags->count() > 0)
                                                    <div class="flex flex-wrap gap-1">
                                                        @foreach($project->tags->take(2) as $tag)
                                                            <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs">{{ $tag->name }}</span>
                                                        @endforeach
                                                        @if($project->tags->count() > 2)
                                                            <span class="text-xs text-gray-500">+{{ $project->tags->count() - 2 }}</span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-gray-400 text-xs">No tags</span>
                                                @endif
                                            </td>
                                            <td class="py-4">
                                                @if($project->is_featured ?? false)
                                                    <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium">Featured</span>
                                                @else
                                                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">Published</span>
                                                @endif
                                            </td>
                                            <td class="py-4 text-gray-700">{{ $project->created_at->format('M j, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Cards -->
                        <div class="md:hidden space-y-4">
                            @foreach($recentProjects as $project)
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex items-start justify-between mb-2">
                                        <h3 class="font-medium text-gray-900 text-sm">{{ $project->title }}</h3>
                                        @if($project->is_featured ?? false)
                                            <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium">Featured</span>
                                        @else
                                            <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">Published</span>
                                        @endif
                                    </div>
                                    @if($project->description)
                                        <p class="text-xs text-gray-600 mb-3">{{ Str::limit($project->description, 80) }}</p>
                                    @endif
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-500">{{ $project->created_at->format('M j, Y') }}</span>
                                        @if($project->tags && $project->tags->count() > 0)
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($project->tags->take(2) as $tag)
                                                    <span class="px-2 py-1 bg-gray-200 text-gray-600 rounded text-xs">{{ $tag->name }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-project-diagram text-gray-400 text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No projects yet</h3>
                            <p class="text-gray-600 mb-4">Showcase your work by creating your first project</p>
                            <a href="{{ route('admin.projects.create') }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg text-sm font-medium hover:bg-purple-700 transition-colors inline-block">
                                Create Project
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="space-y-6 lg:space-y-8">
            <!-- Recent Activity -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 lg:p-6 border-b border-gray-100">
                    <h2 class="text-lg lg:text-xl font-semibold text-gray-900">Recent Activity</h2>
                </div>
                <div class="p-4 lg:p-6">
                    @if($recentActivity && $recentActivity->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentActivity->take(5) as $activity)
                                <div class="flex items-start space-x-3 p-3
                                    @if($activity['type'] === 'course') bg-blue-50 border-blue-100
                                    @elseif($activity['type'] === 'project') bg-green-50 border-green-100
                                    @elseif($activity['type'] === 'blog') bg-purple-50 border-purple-100
                                    @else bg-orange-50 border-orange-100
                                    @endif
                                    rounded-lg border">
                                    <div class="w-3 h-3
                                        @if($activity['type'] === 'course') bg-blue-500
                                        @elseif($activity['type'] === 'project') bg-green-500
                                        @elseif($activity['type'] === 'blog') bg-purple-500
                                        @else bg-orange-500
                                        @endif
                                        rounded-full mt-2 flex-shrink-0"></div>
                                    <div class="flex-1 min-w-0">
                                        <h6 class="font-medium text-gray-900 mb-1 text-sm">{{ $activity['action'] }}</h6>
                                        <p class="text-xs text-gray-600 mb-1 truncate">{{ $activity['title'] }}</p>
                                        <div class="text-xs
                                            @if($activity['type'] === 'course') text-blue-600
                                            @elseif($activity['type'] === 'project') text-green-600
                                            @elseif($activity['type'] === 'blog') text-purple-600
                                            @else text-orange-600
                                            @endif
                                            font-medium">
                                            <i class="far fa-clock mr-1"></i>{{ $activity['date'] }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-clock text-gray-400 text-lg"></i>
                            </div>
                            <h3 class="text-sm font-medium text-gray-900 mb-1">No recent activity</h3>
                            <p class="text-xs text-gray-600">Your recent actions will appear here</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Content Summary -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 lg:p-6 border-b border-gray-100">
                    <h2 class="text-lg lg:text-xl font-semibold text-gray-900">Content Summary</h2>
                </div>
                <div class="p-4 lg:p-6">
                    <div class="space-y-4">
                        @if($stats['courses'] > 0)
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-gray-700">Courses</span>
                                    <span class="text-sm font-bold text-gray-900">{{ $stats['courses'] }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: {{ min(100, ($stats['courses'] / 10) * 100) }}%"></div>
                                </div>
                            </div>
                        @endif

                        @if($stats['projects'] > 0)
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-gray-700">Projects</span>
                                    <span class="text-sm font-bold text-gray-900">{{ $stats['projects'] }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ min(100, ($stats['projects'] / 20) * 100) }}%"></div>
                                </div>
                            </div>
                        @endif

                        @if($stats['blog_posts'] > 0)
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-gray-700">Blog Posts</span>
                                    <span class="text-sm font-bold text-gray-900">{{ $stats['blog_posts'] }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-purple-500 h-2 rounded-full" style="width: {{ min(100, ($stats['blog_posts'] / 15) * 100) }}%"></div>
                                </div>
                            </div>
                        @endif

                        @if($stats['publications'] ?? 0 > 0)
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-gray-700">Publications</span>
                                    <span class="text-sm font-bold text-gray-900">{{ $stats['publications'] }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-orange-500 h-2 rounded-full" style="width: {{ min(100, ($stats['publications'] / 10) * 100) }}%"></div>
                                </div>
                            </div>
                        @endif

                        @if($stats['courses'] == 0 && $stats['projects'] == 0 && $stats['blog_posts'] == 0 && ($stats['publications'] ?? 0) == 0)
                            <div class="text-center py-4">
                                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-chart-pie text-gray-400 text-lg"></i>
                                </div>
                                <p class="text-sm text-gray-600">No content yet</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 lg:p-6 border-b border-gray-100">
                    <h2 class="text-lg lg:text-xl font-semibold text-gray-900">Quick Actions</h2>
                </div>
                <div class="p-4 lg:p-6">
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('admin.courses.create') }}" class="flex flex-col items-center p-3 lg:p-4 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors text-decoration-none">
                            <i class="fas fa-plus text-blue-600 text-lg lg:text-xl mb-2"></i>
                            <span class="text-xs lg:text-sm font-medium text-blue-700">New Course</span>
                        </a>
                        <a href="{{ route('admin.projects.create') }}" class="flex flex-col items-center p-3 lg:p-4 bg-green-50 hover:bg-green-100 rounded-lg transition-colors text-decoration-none">
                            <i class="fas fa-project-diagram text-green-600 text-lg lg:text-xl mb-2"></i>
                            <span class="text-xs lg:text-sm font-medium text-green-700">New Project</span>
                        </a>
                        <a href="{{ route('admin.blog.create') }}" class="flex flex-col items-center p-3 lg:p-4 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors text-decoration-none">
                            <i class="fas fa-blog text-purple-600 text-lg lg:text-xl mb-2"></i>
                            <span class="text-xs lg:text-sm font-medium text-purple-700">New Post</span>
                        </a>
                        <a href="{{ route('admin.contact.index') }}" class="flex flex-col items-center p-3 lg:p-4 bg-red-50 hover:bg-red-100 rounded-lg transition-colors text-decoration-none relative">
                            <i class="fas fa-envelope text-red-600 text-lg lg:text-xl mb-2"></i>
                            <span class="text-xs lg:text-sm font-medium text-red-700">Messages</span>
                            @if(($stats['unread_messages'] ?? 0) > 0)
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                    {{ $stats['unread_messages'] }}
                                </span>
                            @endif
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection