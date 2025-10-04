<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Teacher Admin Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Compiled CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <!-- Classical modern admin layout - no AJAX dependencies -->

    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-width-mobile: 70px;
        }

        body {
            font-family: 'Inter', sans-serif;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif;
        }

        /* Sidebar transitions */
        #sidebar {
            transition: all 0.3s ease;
        }

        #content {
            transition: all 0.3s ease;
        }

        /* Hover effects for cards */
        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        /* Mobile sidebar */
        @media (max-width: 1024px) {
            #sidebar {
                width: var(--sidebar-width-mobile);
                overflow: hidden;
            }

            #sidebar:hover, #sidebar.mobile-expanded {
                width: var(--sidebar-width);
                overflow: visible;
            }

            #content {
                margin-left: var(--sidebar-width-mobile);
            }

            #sidebar:hover ~ #content,
            #sidebar.mobile-expanded ~ #content {
                margin-left: var(--sidebar-width);
            }

            .menu-text {
                opacity: 0;
                transition: opacity 0.3s;
                white-space: nowrap;
            }

            #sidebar:hover .menu-text,
            #sidebar.mobile-expanded .menu-text {
                opacity: 1;
            }

            .sidebar-header h3 {
                opacity: 0;
                transition: opacity 0.3s;
            }

            #sidebar:hover .sidebar-header h3,
            #sidebar.mobile-expanded .sidebar-header h3 {
                opacity: 1;
            }
        }

        @media (max-width: 768px) {
            #sidebar {
                transform: translateX(-100%);
                position: fixed;
                z-index: 50;
                width: var(--sidebar-width);
            }

            #sidebar.mobile-expanded {
                transform: translateX(0);
            }

            #content {
                margin-left: 0;
            }

            #sidebar:hover ~ #content,
            #sidebar.mobile-expanded ~ #content {
                margin-left: 0;
            }

            .overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 40;
            }

            #sidebar.mobile-expanded ~ .overlay {
                display: block;
            }
        }
    </style>

    @stack('styles')
</head>
<body class="h-full bg-gray-50 overflow-x-hidden">
    <!-- Sidebar -->
    <nav id="sidebar" class="fixed top-0 left-0 h-full bg-slate-800 text-white shadow-xl z-30" style="width: var(--sidebar-width);">
        <!-- Sidebar Header -->
        <div class="flex items-center p-6 border-b border-slate-700">
            <i class="fas fa-chalkboard-teacher text-2xl text-blue-400 mr-3"></i>
            <h3 class="text-xl font-semibold menu-text">Teacher Dashboard</h3>
        </div>

        <!-- Navigation Menu -->
        <div class="p-4">
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center p-3 rounded-lg text-gray-300 hover:bg-slate-700 hover:text-white transition-all duration-200 border-l-4 {{ request()->routeIs('admin.dashboard') ? 'border-blue-400 bg-slate-700 text-white' : 'border-transparent' }}">
                        <i class="fas fa-home w-6 text-center mr-3"></i>
                        <span class="menu-text">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.courses.index') }}" class="flex items-center p-3 rounded-lg text-gray-300 hover:bg-slate-700 hover:text-white transition-all duration-200 border-l-4 {{ request()->routeIs('admin.courses.*') ? 'border-blue-400 bg-slate-700 text-white' : 'border-transparent' }}">
                        <i class="fas fa-book w-6 text-center mr-3"></i>
                        <span class="menu-text">Courses</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.projects.index') }}" class="flex items-center p-3 rounded-lg text-gray-300 hover:bg-slate-700 hover:text-white transition-all duration-200 border-l-4 {{ request()->routeIs('admin.projects.*') ? 'border-blue-400 bg-slate-700 text-white' : 'border-transparent' }}">
                        <i class="fas fa-project-diagram w-6 text-center mr-3"></i>
                        <span class="menu-text">Projects</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.blog.index') }}" class="flex items-center p-3 rounded-lg text-gray-300 hover:bg-slate-700 hover:text-white transition-all duration-200 border-l-4 {{ request()->routeIs('admin.blog.*') ? 'border-blue-400 bg-slate-700 text-white' : 'border-transparent' }}">
                        <i class="fas fa-blog w-6 text-center mr-3"></i>
                        <span class="menu-text">Blog Posts</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.publications.index') }}" class="flex items-center p-3 rounded-lg text-gray-300 hover:bg-slate-700 hover:text-white transition-all duration-200 border-l-4 {{ request()->routeIs('admin.publications.*') ? 'border-blue-400 bg-slate-700 text-white' : 'border-transparent' }}">
                        <i class="fas fa-file-alt w-6 text-center mr-3"></i>
                        <span class="menu-text">Publications</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.contact.index') }}" class="flex items-center p-3 rounded-lg text-gray-300 hover:bg-slate-700 hover:text-white transition-all duration-200 border-l-4 {{ request()->routeIs('admin.contact.*') ? 'border-blue-400 bg-slate-700 text-white' : 'border-transparent' }}">
                        <i class="fas fa-envelope w-6 text-center mr-3"></i>
                        <span class="menu-text">Messages</span>
                        @if(isset($stats) && ($stats['unread_messages'] ?? 0) > 0)
                            <span class="ml-auto bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                {{ $stats['unread_messages'] }}
                            </span>
                        @endif
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.tags.index') }}" class="flex items-center p-3 rounded-lg text-gray-300 hover:bg-slate-700 hover:text-white transition-all duration-200 border-l-4 {{ request()->routeIs('admin.tags.*') ? 'border-blue-400 bg-slate-700 text-white' : 'border-transparent' }}">
                        <i class="fas fa-tags w-6 text-center mr-3"></i>
                        <span class="menu-text">Tags</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.profile.edit') }}" class="flex items-center p-3 rounded-lg text-gray-300 hover:bg-slate-700 hover:text-white transition-all duration-200 border-l-4 {{ request()->routeIs('admin.profile.*') ? 'border-blue-400 bg-slate-700 text-white' : 'border-transparent' }}">
                        <i class="fas fa-user w-6 text-center mr-3"></i>
                        <span class="menu-text">Profile</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Sidebar Footer -->
        <div class="absolute bottom-0 w-full p-4 border-t border-slate-700">
            <div class="flex items-center">
                <img src="https://images.unsplash.com/photo-1580489944761-15a19d654956?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=200&q=80"
                     alt="User" class="w-10 h-10 rounded-full mr-3">
                <div class="menu-text">
                    <h6 class="text-sm font-medium">Dr. Sarah Johnson</h6>
                    <p class="text-xs text-gray-400">Professor</p>
                </div>
            </div>
        </div>
    </nav>

    <!-- Overlay for mobile -->
    <div class="overlay"></div>

    <!-- Main Content -->
    <div id="content" class="min-h-screen" style="margin-left: var(--sidebar-width);">
        <!-- Top Bar -->
        <div class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <button id="sidebarToggle" class="lg:hidden text-gray-600 hover:text-gray-900 mr-4">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h1 class="text-2xl font-semibold text-gray-900">@yield('page-title', 'Dashboard')</h1>
                </div>

                <div class="flex items-center space-x-4">
                    <!-- Notifications -->
                    <div class="relative">
                        <button class="text-gray-600 hover:text-gray-900 relative">
                            <i class="fas fa-bell text-xl"></i>
                            <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">3</span>
                        </button>
                    </div>

                    <!-- User Dropdown -->
                    <div class="relative group">
                        <button class="flex items-center space-x-2 text-gray-700 hover:text-gray-900">
                            <img src="https://images.unsplash.com/photo-1580489944761-15a19d654956?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=200&q=80"
                                 alt="User" class="w-8 h-8 rounded-full">
                            <span class="hidden md:block text-sm font-medium">Dr. Sarah Johnson</span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-10">
                            <div class="py-2">
                                <a href="{{ route('admin.profile.edit') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-user w-4 mr-3"></i>
                                    Profile
                                </a>
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-cog w-4 mr-3"></i>
                                    Dashboard
                                </a>
                                <hr class="my-1">
                                <form method="POST" action="{{ route('logout') }}" class="block">
                                    @csrf
                                    <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 text-left">
                                        <i class="fas fa-sign-out-alt w-4 mr-3"></i>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <div class="p-6">
            @yield('content')
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const overlay = document.querySelector('.overlay');

            // Toggle sidebar on mobile
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    sidebar.classList.toggle('mobile-expanded');
                });
            }

            // Close sidebar when clicking on overlay
            if (overlay) {
                overlay.addEventListener('click', function() {
                    sidebar.classList.remove('mobile-expanded');
                });
            }

            // Close sidebar when clicking on menu items on mobile
            if (window.innerWidth <= 768) {
                const menuLinks = document.querySelectorAll('#sidebar a[href]');
                menuLinks.forEach(function(link) {
                    link.addEventListener('click', function() {
                        sidebar.classList.remove('mobile-expanded');
                    });
                });
            }

            // Auto-resize handling
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1024) {
                    sidebar.classList.remove('mobile-expanded');
                }
            });
        });
    </script>

    <!-- App JavaScript -->
    <script src="{{ asset('js/app.js') }}"></script>

    @stack('scripts')
</body>
</html>