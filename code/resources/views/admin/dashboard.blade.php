@extends('layouts.admin')

@section('page-title', 'Dashboard')

@section('content')
<!-- Clean Header -->
<div class="mb-8">
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white mb-1">Welcome back, {{ Auth::user()->name }}</h1>
                <p class="text-slate-600 dark:text-slate-400">Here's an overview of your portfolio.</p>
            </div>
            <div class="hidden md:block">
                <div class="text-right">
                    <div class="text-2xl font-bold text-slate-900 dark:text-white">{{ date('d') }}</div>
                    <div class="text-sm text-slate-600 dark:text-slate-400">{{ date('M Y') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Overview -->
<div class="mb-8">
    <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Overview</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 p-4">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $stats['courses'] ?? 0 }}</p>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Courses</p>
                </div>
            </div>
            <div class="mt-3">
                <a href="{{ route('admin.courses.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">View all →</a>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 p-4">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $stats['projects'] ?? 0 }}</p>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Projects</p>
                </div>
            </div>
            <div class="mt-3">
                <a href="{{ route('admin.projects.index') }}" class="text-sm text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300">View all →</a>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 p-4">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $stats['publications'] ?? 0 }}</p>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Publications</p>
                </div>
            </div>
            <div class="mt-3">
                <a href="{{ route('admin.publications.index') }}" class="text-sm text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300">View all →</a>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 p-4">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $stats['blog_posts'] ?? 0 }}</p>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Blog Posts</p>
                </div>
            </div>
            <div class="mt-3">
                <a href="{{ route('admin.blog.index') }}" class="text-sm text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300">View all →</a>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 p-4">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-pink-100 dark:bg-pink-900/30 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-pink-600 dark:text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $stats['total_messages'] ?? 0 }}</p>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Messages</p>
                </div>
            </div>
            <div class="mt-3">
                <a href="{{ route('admin.contact.index') }}" class="text-sm text-pink-600 dark:text-pink-400 hover:text-pink-700 dark:hover:text-pink-300">View all →</a>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="mb-8">
    <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Quick Actions</h2>
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <a href="{{ route('admin.courses.create') }}" class="flex flex-col items-center p-4 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mb-2">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </div>
                <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Add Course</span>
            </a>

            <a href="{{ route('admin.projects.create') }}" class="flex flex-col items-center p-4 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center mb-2">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </div>
                <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Add Project</span>
            </a>

            <a href="{{ route('admin.blog.create') }}" class="flex flex-col items-center p-4 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center mb-2">
                    <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </div>
                <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Write Post</span>
            </a>

            <a href="{{ route('admin.publications.create') }}" class="flex flex-col items-center p-4 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center mb-2">
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </div>
                <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Add Publication</span>
            </a>

            <a href="{{ route('admin.tags.create') }}" class="flex flex-col items-center p-4 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                <div class="w-10 h-10 bg-cyan-100 dark:bg-cyan-900/30 rounded-lg flex items-center justify-center mb-2">
                    <svg class="w-5 h-5 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </div>
                <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Add Tag</span>
            </a>

            <a href="{{ route('admin.profile.edit') }}" class="flex flex-col items-center p-4 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                <div class="w-10 h-10 bg-slate-100 dark:bg-slate-700 rounded-lg flex items-center justify-center mb-2">
                    <svg class="w-5 h-5 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Edit Profile</span>
            </a>
        </div>
    </div>
</div>

<!-- Recent Activity & Messages -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Activity -->
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Recent Activity</h3>
        @if($recentActivity && count($recentActivity) > 0)
            <div class="space-y-3">
                @foreach($recentActivity->take(5) as $activity)
                    <div class="flex items-center space-x-3 p-3 bg-slate-50 dark:bg-slate-700 rounded-lg">
                        <div class="w-8 h-8 bg-{{ $activity['type'] === 'course' ? 'blue' : ($activity['type'] === 'project' ? 'green' : 'orange') }}-100 dark:bg-{{ $activity['type'] === 'course' ? 'blue' : ($activity['type'] === 'project' ? 'green' : 'orange') }}-900/30 rounded-full flex items-center justify-center">
                            <div class="w-2 h-2 bg-{{ $activity['type'] === 'course' ? 'blue' : ($activity['type'] === 'project' ? 'green' : 'orange') }}-600 rounded-full"></div>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $activity['action'] }}</p>
                            <p class="text-xs text-slate-600 dark:text-slate-400">{{ $activity['title'] }}</p>
                        </div>
                        <span class="text-xs text-slate-500 dark:text-slate-400">{{ $activity['date'] }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-slate-600 dark:text-slate-400">No recent activity</p>
        @endif
    </div>

    <!-- Messages -->
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Messages</h3>
        <div class="space-y-3">
            <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-700 rounded-lg">
                <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Unread Messages</span>
                <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-sm font-medium rounded">{{ $stats['unread_messages'] ?? 0 }}</span>
            </div>
            <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-700 rounded-lg">
                <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Total Messages</span>
                <span class="px-2 py-1 bg-slate-100 dark:bg-slate-600 text-slate-700 dark:text-slate-300 text-sm font-medium rounded">{{ $stats['total_messages'] ?? 0 }}</span>
            </div>
            <a href="{{ route('admin.contact.index') }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors text-center text-sm">
                View All Messages
            </a>
        </div>
    </div>
</div>
@endsection