<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'PFE Management') }} - @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --bs-primary: #2563eb;
            --bs-primary-rgb: 37, 99, 235;
        }

        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 0.5rem;
        }

        .sidebar .nav-link i {
            width: 20px;
        }

        .content-wrapper {
            min-height: 100vh;
        }

        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: box-shadow 0.15s ease-in-out;
        }

        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .badge-status {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        .status-draft { background-color: #6c757d; }
        .status-pending { background-color: #ffc107; color: #000; }
        .status-validated { background-color: #198754; }
        .status-rejected { background-color: #dc3545; }
        .status-forming { background-color: #0dcaf0; color: #000; }
        .status-complete { background-color: #198754; }
        .status-assigned { background-color: #6f42c1; }
        .status-active { background-color: #198754; }
        .status-in_progress { background-color: #fd7e14; }
        .status-submitted { background-color: #6f42c1; }
        .status-defended { background-color: #198754; }
        .status-scheduled { background-color: #0d6efd; }
        .status-completed { background-color: #198754; }

        .progress-ring {
            width: 60px;
            height: 60px;
        }

        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            min-width: 18px;
            height: 18px;
            border-radius: 50%;
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>

    @stack('styles')
</head>
<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            @auth
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse p-0">
                <div class="position-sticky pt-3">
                    <!-- Logo -->
                    <div class="text-center mb-4">
                        <h4 class="text-white fw-bold">PFE Manager</h4>
                        <small class="text-white-50">{{ auth()->user()->department ?? 'System' }}</small>
                    </div>

                    <!-- User Info -->
                    <div class="px-3 mb-4">
                        <div class="d-flex align-items-center text-white">
                            <div class="bg-white bg-opacity-25 rounded-circle p-2 me-3">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">{{ auth()->user()?->name ?? 'Guest' }}</div>
                                <small class="text-white-50">{{ auth()->user() ? ucfirst(str_replace('_', ' ', auth()->user()?->role)) : 'Not logged in' }}</small>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <ul class="nav flex-column px-3">
                        <!-- Dashboard -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard*', 'student.dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <i class="bi bi-speedometer2 me-2"></i>
                                {{ __('app.dashboard') }}
                            </a>
                        </li>

                        <!-- Subjects -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('subjects*') ? 'active' : '' }}" href="{{ route('subjects.index') }}">
                                <i class="bi bi-journal-text me-2"></i>
                                {{ __('app.subjects') }}
                                @if(auth()->user()?->role === 'department_head')
                                    <span class="notification-badge bg-warning text-dark" id="pending-subjects">0</span>
                                @endif
                            </a>
                        </li>

                        <!-- Subject Requests -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('subject-requests*') ? 'active' : '' }}" href="{{ route('subject-requests.all') }}">
                                <i class="bi bi-envelope-paper me-2"></i>
                                {{ __('app.subject_requests') }}
                            </a>
                        </li>

                        <!-- Teams (not for students) -->
                        @if(in_array(auth()->user()?->role, ['teacher', 'department_head', 'admin']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('teams*') ? 'active' : '' }}" href="{{ route('teams.index') }}">
                                <i class="bi bi-people me-2"></i>
                                {{ __('app.teams') }}
                            </a>
                        </li>
                        @endif

                        {{-- Projects temporarily removed --}}
                        {{--
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('projects*') ? 'active' : '' }}" href="{{ route('projects.index') }}">
                                <i class="bi bi-folder me-2"></i>
                                Projects
                            </a>
                        </li>
                        --}}

                        <!-- Defenses (not for students) -->
                        @if(in_array(auth()->user()?->role, ['teacher', 'department_head', 'admin']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('defenses*') ? 'active' : '' }}" href="{{ route('defenses.index') }}">
                                <i class="bi bi-shield-check me-2"></i>
                                {{ __('app.defenses') }}
                            </a>
                        </li>
                        @endif

                        <!-- Student-specific navigation -->
                        @if(auth()->user()?->role === 'student')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('teams.my-team') ? 'active' : '' }}" href="{{ route('teams.my-team') }}">
                                <i class="bi bi-people me-2"></i>
                                {{ __('app.my_team') }}
                            </a>
                        </li>

                        @php
                            $hasScheduledDefense = \App\Models\Defense::whereHas('project.team.members', function($q) {
                                $q->where('student_id', auth()->id());
                            })->exists();
                        @endphp

                        @if($hasScheduledDefense)
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('defenses.my-defense') ? 'active' : '' }}" href="{{ route('defenses.my-defense') }}">
                                <i class="bi bi-shield-check me-2"></i>
                                {{ __('app.my_defense') }}
                            </a>
                        </li>
                        @endif
                        @endif

                        <!-- Conflicts (Department Heads only) -->
                        @if(auth()->user()?->role === 'department_head')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('conflicts*') ? 'active' : '' }}" href="{{ route('conflicts.index') }}">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                {{ __('app.conflicts') }}
                                <span class="notification-badge bg-danger" id="pending-conflicts">0</span>
                            </a>
                        </li>
                        @endif

                        <!-- Administration (Admins only) -->
                        @if(auth()->user()?->role === 'admin')
                        <li class="nav-item mt-3">
                            <h6 class="sidebar-heading text-white-50 text-uppercase px-3 mt-4 mb-1 fs-6">
                                {{ __('app.administration') }}
                            </h6>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}" href="{{ route('admin.users') }}">
                                <i class="bi bi-people-fill me-2"></i>
                                {{ __('app.users') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.marks*') ? 'active' : '' }}" href="{{ route('admin.marks') }}">
                                <i class="bi bi-award me-2"></i>
                                {{ __('app.student_marks') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.allocations*') ? 'active' : '' }}" href="{{ route('admin.allocations.index') }}">
                                <i class="bi bi-diagram-3 me-2"></i>
                                {{ __('app.allocations') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.subject-requests*') ? 'active' : '' }}" href="{{ route('admin.subject-requests') }}">
                                <i class="bi bi-envelope-paper me-2"></i>
                                {{ __('app.subject_requests') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.specialities*') ? 'active' : '' }}" href="{{ route('admin.specialities.index') }}">
                                <i class="bi bi-mortarboard me-2"></i>
                                {{ __('app.specialities') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.alerts*') ? 'active' : '' }}" href="{{ route('admin.alerts') }}">
                                <i class="bi bi-bell me-2"></i>
                                {{ __('app.student_alerts') }}
                                @php
                                    $pendingAlertsCount = \App\Models\StudentAlert::where('status', 'pending')->count();
                                @endphp
                                @if($pendingAlertsCount > 0)
                                    <span class="notification-badge bg-danger">{{ $pendingAlertsCount }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.reports*') ? 'active' : '' }}" href="{{ route('admin.reports') }}">
                                <i class="bi bi-bar-chart me-2"></i>
                                {{ __('app.reports') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}" href="{{ route('admin.settings') }}">
                                <i class="bi bi-gear me-2"></i>
                                {{ __('app.settings') }}
                            </a>
                        </li>
                        @endif
                    </ul>

                    <!-- Logout -->
                    <div class="px-3 mt-auto pb-3">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="nav-link btn btn-link text-white text-decoration-none d-flex align-items-center w-100">
                                <i class="bi bi-box-arrow-right me-2"></i>
                                {{ __('app.logout') }}
                            </button>
                        </form>
                    </div>
                </div>
            </nav>
            @endauth

            <!-- Main content -->
            <main class="@auth col-md-9 ms-sm-auto col-lg-10 @else col-12 @endauth px-md-4 content-wrapper">
                @auth
                <!-- Header -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">@yield('page-title', __('app.dashboard'))</h1>

                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <!-- Language Switcher -->
                            @include('partials.language-switcher')
                        </div>

                        <div class="btn-group me-2">
                            <!-- Notifications -->
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle position-relative" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-bell"></i>
                                    <span class="notification-badge bg-danger" id="notification-count" style="display: none;"></span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" style="width: 300px;">
                                    <li><h6 class="dropdown-header">{{ __('app.notifications') }}</h6></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <div id="notifications-list">
                                        <li><span class="dropdown-item-text text-muted">{{ __('app.no_notifications') }}</span></li>
                                    </div>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-center small" href="{{ route('notifications.index') }}">{{ __('app.view_all_notifications') }}</a></li>
                                </ul>
                            </div>
                        </div>

                        <div class="btn-group">
                            <!-- User Menu -->
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-person-circle"></i>
                                    {{ auth()->user()?->name ?? __('app.guest') }}
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('profile.show') }}">
                                        <i class="bi bi-person me-2"></i>{{ __('app.profile') }}
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="bi bi-box-arrow-right me-2"></i>{{ __('app.logout') }}
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                @endauth

                <!-- Flash Messages Component -->
                @include('components.flash-messages')

                <!-- Page Content -->
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Classical JavaScript -->
    <script>
        // Classical web application functionality
        document.addEventListener('DOMContentLoaded', function() {
            console.log('PFE Application loaded - Classical mode');
            // No AJAX calls - using classical page reloads
        });
    </script>

    @stack('scripts')

    <!-- Confirmation Modal Components -->
    <x-confirmation-modal />
    <x-delete-confirmation-modal />
</body>
</html>