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
    <link href="{{ asset('css/compact-admin.css') }}" rel="stylesheet">

    <style>
        :root {
            --bs-primary: #2563eb;
            --bs-primary-rgb: 37, 99, 235;
            --sidebar-bg-start: #1e293b;
            --sidebar-bg-end: #0f172a;
        }

        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, var(--sidebar-bg-start) 0%, var(--sidebar-bg-end) 100%);
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.75);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 0.5rem 0.75rem;
            margin: 0.15rem 0;
            border-radius: 0.4rem;
            font-weight: 500;
            font-size: 0.9rem;
            position: relative;
            overflow: hidden;
        }

        .sidebar .nav-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 3px;
            background: linear-gradient(180deg, #3b82f6, #8b5cf6);
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .sidebar .nav-link:hover {
            color: white;
            background: rgba(59, 130, 246, 0.15);
            transform: translateX(4px);
        }

        .sidebar .nav-link:hover::before {
            transform: scaleY(1);
        }

        .sidebar .nav-link.active {
            color: white;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(139, 92, 246, 0.2));
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
        }

        .sidebar .nav-link.active::before {
            transform: scaleY(1);
        }

        .sidebar .nav-link i {
            width: 20px;
            font-size: 1rem;
            transition: transform 0.3s ease;
        }

        .sidebar .nav-link:hover i {
            transform: scale(1.1);
        }

        .sidebar-heading {
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            color: rgba(255, 255, 255, 0.5);
            padding: 0.4rem 0.75rem;
            margin-top: 1rem;
            margin-bottom: 0.3rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            gap: 0.4rem;
            cursor: pointer;
            transition: all 0.3s ease;
            user-select: none;
        }
        [data-bs-theme="dark"] {
            --sidebar-bg-start: #1e1e1e;
            --sidebar-bg-end: #1e1e1e;
            --sidebar-text: #e0e0e0;
            --sidebar-text-muted: #a0a0a0;
            --sidebar-border: #2d2d2d;
            --sidebar-hover-bg: #2d2d2d;
            --sidebar-active-bg: #323232;
            --content-bg: #121212;
            --bs-body-bg: #121212;
            --bs-body-color: #e0e0e0;
            --bs-border-color: #2d2d2d;
            --bs-card-bg: #1e1e1e;
            --bs-card-border-color: #2d2d2d;
        }

        [data-bs-theme="dark"] .navbar {
            background-color: #1e1e1e !important;
            border-color: #2d2d2d !important;
        }

        [data-bs-theme="dark"] .card {
            background-color: #1e1e1e;
            border-color: #2d2d2d;
            color: #e0e0e0;
        }

        [data-bs-theme="dark"] .table {
            color: #e0e0e0;
            border-color: #2d2d2d;
        }

        [data-bs-theme="dark"] .table-light {
            background-color: #1e1e1e;
            color: #e0e0e0;
        }

        [data-bs-theme="dark"] .border-bottom {
            border-color: #2d2d2d !important;
        }

        [data-bs-theme="dark"] .btn-outline-secondary {
            color: #a0a0a0;
            border-color: #404040;
        }

        [data-bs-theme="dark"] .btn-outline-secondary:hover {
            background-color: #323232;
            border-color: #323232;
            color: #ffffff;
        }

        [data-bs-theme="dark"] .form-control,
        [data-bs-theme="dark"] .form-select {
            background-color: #2d2d2d;
            border-color: #404040;
            color: #e0e0e0;
        }

        [data-bs-theme="dark"] .form-control:focus,
        [data-bs-theme="dark"] .form-select:focus {
            background-color: #323232;
            border-color: #7289da;
            color: #ffffff;
            box-shadow: 0 0 0 0.2rem rgba(114, 137, 218, 0.25);
        }

        [data-bs-theme="dark"] .text-muted {
            color: #a0a0a0 !important;
        }

        [data-bs-theme="dark"] .bg-light {
            background-color: #121212 !important;
        }

        [data-bs-theme="dark"] .dropdown-menu {
            background-color: #1e1e1e;
            border-color: #2d2d2d;
        }

        [data-bs-theme="dark"] .dropdown-item {
            color: #e0e0e0;
        }

        [data-bs-theme="dark"] .dropdown-item:hover {
            background-color: #2d2d2d;
            color: #ffffff;
        }

        [data-bs-theme="dark"] .dropdown-divider {
            border-color: #2d2d2d;
        }.sidebar-heading .dropdown-icon {
            margin-left: auto;
            transition: transform 0.3s ease;
            font-size: 0.7rem;
        }

        .sidebar-heading.collapsed .dropdown-icon {
            transform: rotate(-90deg);
        }

        .sidebar-section {
            max-height: 1000px;
            overflow: hidden;
            transition: max-height 0.4s ease, opacity 0.3s ease;
            opacity: 1;
        }

        .sidebar-section.collapsed {
            max-height: 0;
            opacity: 0;
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
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            min-width: 20px;
            height: 20px;
            border-radius: 10px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        /* User info section */
        .sidebar .user-info {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 0.5rem;
            padding: 0.65rem;
            margin: 0 0.5rem 1rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar .user-avatar {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .sidebar .user-info .fw-semibold {
            font-size: 0.85rem;
        }

        .sidebar .user-info small {
            font-size: 0.7rem;
        }

        /* Dark mode toggle */
        .dark-mode-toggle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            background: transparent;
            padding: 0;
        }

        .dark-mode-toggle:hover {
            background: rgba(0, 0, 0, 0.05);
        }

        [data-bs-theme="dark"] .dark-mode-toggle:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        /* Compact navbar buttons */
        .navbar .btn-outline-secondary {
            padding: 0.4rem 0.75rem;
            font-size: 0.875rem;
        }

        .navbar .dropdown-toggle::after {
            margin-left: 0.4rem;
        }

        .navbar .btn-group {
            gap: 0.25rem;
        }

        .navbar .d-flex {
            gap: 0.5rem;
        }

        /* Logo section */
        .sidebar .logo-section {
            background: rgba(255, 255, 255, 0.03);
            padding: 1rem 0.75rem;
            margin-bottom: 0.75rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar .logo-section h4 {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
            margin-bottom: 0.25rem;
        }

        .sidebar .logo-section small {
            font-size: 0.7rem;
        }
        /* Horizontal navbar navigation */
        .navbar-nav .nav-link {
            color: var(--sidebar-text-muted);
            transition: all 0.3s ease;
            padding: 0.5rem 1rem;
            border-radius: 0.4rem;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .navbar-nav .nav-link:hover {
            color: var(--sidebar-text);
            background: var(--sidebar-hover-bg);
        }

        .navbar-nav .nav-link.active {
            color: var(--bs-primary);
            background: var(--sidebar-active-bg);
        }

        .navbar-nav .dropdown-menu {
            border: 1px solid var(--sidebar-border);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        [data-bs-theme="dark"] .navbar-nav .dropdown-menu {
            background-color: #1e293b;
        }

        .navbar-nav .dropdown-item {
            color: var(--sidebar-text-muted);
            transition: all 0.2s ease;
            font-size: 0.9rem;
        }

        .navbar-nav .dropdown-item:hover {
            color: var(--sidebar-text);
            background: var(--sidebar-hover-bg);
        }

        .navbar-nav .dropdown-item.active {
            color: var(--bs-primary);
            background: var(--sidebar-active-bg);
        }

        .navbar-nav .dropdown-divider {
            border-color: var(--sidebar-border);
        }

        .navbar-nav .dropdown-header {
            color: var(--sidebar-text-muted);
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        /* Remove old sidebar styles */
        .sidebar {
            display: none;
        }

        .content-wrapper {
            margin-left: 0 !important;
            width: 100% !important;
        }
    </style>

    @stack('styles')
</head>
<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <!-- Main content -->
            <main class="col-12 px-md-4 content-wrapper">
                @auth
                <!-- Navbar Header -->
                <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom mb-3" [data-bs-theme]>
                    <div class="container-fluid">
                        <span class="navbar-brand h2 mb-0">@yield('page-title', __('app.dashboard'))</span>
                        
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        
                        <div class="collapse navbar-collapse" id="navbarNav">
                            <!-- Navigation Links -->
                            <ul class="navbar-nav me-auto">
                                <!-- Dashboard -->
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('dashboard*') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                        <i class="bi bi-speedometer2 me-1"></i> {{ __('app.dashboard') }}
                                    </a>
                                </li>

                                @if(auth()->user()?->role === 'admin')
                                <!-- Academic Dropdown -->
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-mortarboard me-1"></i> {{ __('app.academic_management') }}
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item {{ request()->routeIs('subjects*') ? 'active' : '' }}" href="{{ route('subjects.index') }}">
                                            <i class="bi bi-journal-text me-2"></i> {{ __('app.subjects') }}
                                        </a></li>
                                        <li><a class="dropdown-item {{ request()->routeIs('admin.teams*', 'teams*') ? 'active' : '' }}" href="{{ route('admin.teams') }}">
                                            <i class="bi bi-people me-2"></i> {{ __('app.teams') }}
                                        </a></li>
                                        <li><a class="dropdown-item {{ request()->routeIs('defenses*') ? 'active' : '' }}" href="{{ route('defenses.index') }}">
                                            <i class="bi bi-shield-check me-2"></i> {{ __('app.defenses') }}
                                        </a></li>
                                        <li><a class="dropdown-item {{ request()->routeIs('admin.allocations*') ? 'active' : '' }}" href="{{ route('admin.allocations.index') }}">
                                            <i class="bi bi-diagram-3 me-2"></i> {{ __('app.allocations') }}
                                        </a></li>
                                        <li><a class="dropdown-item {{ request()->routeIs('admin.external-documents*') ? 'active' : '' }}" href="{{ route('admin.external-documents.index') }}">
                                            <i class="bi bi-file-earmark-text me-2"></i> {{ __('External Documents') }}
                                        </a></li>
                                    </ul>
                                </li>

                                <!-- Students Dropdown -->
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-people-fill me-1"></i> {{ __('app.student_management') }}
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}" href="{{ route('admin.users') }}">
                                            <i class="bi bi-person-badge me-2"></i> {{ __('app.users') }}
                                        </a></li>
                                        <li><a class="dropdown-item {{ request()->routeIs('admin.marks*') ? 'active' : '' }}" href="{{ route('admin.marks') }}">
                                            <i class="bi bi-award me-2"></i> {{ __('app.student_marks') }}
                                        </a></li>
                                        <li><a class="dropdown-item {{ request()->routeIs('admin.alerts*') ? 'active' : '' }}" href="{{ route('admin.alerts') }}">
                                            <i class="bi bi-bell me-2"></i> {{ __('app.student_alerts') }}
                                            @php
                                                $pendingAlertsCount = \App\Models\StudentAlert::where('status', 'pending')->count();
                                            @endphp
                                            @if($pendingAlertsCount > 0)
                                                <span class="badge bg-danger ms-1">{{ $pendingAlertsCount }}</span>
                                            @endif
                                        </a></li>
                                    </ul>
                                </li>

                                <!-- System Dropdown -->
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-gear-fill me-1"></i> {{ __('app.system_configuration') }}
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item {{ request()->routeIs('admin.specialities*') ? 'active' : '' }}" href="{{ route('admin.specialities.index') }}">
                                            <i class="bi bi-mortarboard me-2"></i> {{ __('app.specialities') }}
                                        </a></li>
                                        <li><a class="dropdown-item {{ request()->routeIs('admin.academic-years*') ? 'active' : '' }}" href="{{ route('admin.academic-years.index') }}">
                                            <i class="bi bi-calendar-range me-2"></i> {{ __('app.academic_years') }}
                                        </a></li>
                                        <li><a class="dropdown-item {{ request()->routeIs('admin.rooms*') ? 'active' : '' }}" href="{{ route('admin.rooms') }}">
                                            <i class="bi bi-door-open me-2"></i> {{ __('app.manage_rooms') }}
                                        </a></li>
                                        <li><a class="dropdown-item {{ request()->routeIs('admin.reports*') ? 'active' : '' }}" href="{{ route('admin.reports') }}">
                                            <i class="bi bi-bar-chart me-2"></i> {{ __('app.reports') }}
                                        </a></li>
                                        <li><a class="dropdown-item {{ request()->routeIs('admin.settings*') ? 'active' : '' }}" href="{{ route('admin.settings') }}">
                                            <i class="bi bi-sliders me-2"></i> {{ __('app.settings') }}
                                        </a></li>
                                    </ul>
                                </li>

                                @elseif(auth()->user()?->role === 'department_head')
                                <!-- Department Head Navigation -->
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle {{ request()->routeIs('subjects*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-journal-text me-1"></i> {{ __('app.subjects') }}
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item {{ request()->routeIs('subjects.index') ? 'active' : '' }}" href="{{ route('subjects.index') }}">
                                            <i class="bi bi-list-ul me-2"></i> {{ __('app.subject_list') }}
                                        </a></li>
                                        <li><a class="dropdown-item {{ request()->routeIs('subjects.create') ? 'active' : '' }}" href="{{ route('subjects.create') }}">
                                            <i class="bi bi-plus-circle me-2"></i> {{ __('app.create_subject') }}
                                        </a></li>
                                    </ul>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('teams*') ? 'active' : '' }}" href="{{ route('teams.index') }}">
                                        <i class="bi bi-people me-1"></i> {{ __('app.teams') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('defenses*') ? 'active' : '' }}" href="{{ route('defenses.index') }}">
                                        <i class="bi bi-shield-check me-1"></i> {{ __('app.defenses') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('conflicts*') ? 'active' : '' }}" href="{{ route('conflicts.index') }}">
                                        <i class="bi bi-exclamation-triangle me-1"></i> {{ __('app.conflicts') }}
                                    </a>
                                </li>

                                @elseif(auth()->user()?->role === 'teacher')
                                <!-- Teacher Navigation -->
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle {{ request()->routeIs('subjects*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-journal-text me-1"></i> {{ __('app.subjects') }}
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item {{ request()->routeIs('subjects.index') ? 'active' : '' }}" href="{{ route('subjects.index') }}">
                                            <i class="bi bi-list-ul me-2"></i> {{ __('app.subject_list') }}
                                        </a></li>
                                        <li><a class="dropdown-item {{ request()->routeIs('subjects.create') ? 'active' : '' }}" href="{{ route('subjects.create') }}">
                                            <i class="bi bi-plus-circle me-2"></i> {{ __('app.create_subject') }}
                                        </a></li>
                                    </ul>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('teams*') ? 'active' : '' }}" href="{{ route('teams.index') }}">
                                        <i class="bi bi-people me-1"></i> {{ __('app.teams') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('defenses*') ? 'active' : '' }}" href="{{ route('defenses.index') }}">
                                        <i class="bi bi-shield-check me-1"></i> {{ __('app.defenses') }}
                                    </a>
                                </li>

                                @else
                                <!-- Student Navigation -->
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle {{ request()->routeIs('subjects*', 'teams.subject-preferences*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-journal-text me-1"></i> {{ __('app.subjects') }}
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item {{ request()->routeIs('subjects.index') ? 'active' : '' }}" href="{{ route('subjects.index') }}">
                                            <i class="bi bi-list-ul me-2"></i> {{ __('app.subject_list') }}
                                        </a></li>
                                        @if(auth()->user()->teamMember?->team)
                                            <li><a class="dropdown-item {{ request()->routeIs('teams.subject-preferences*') ? 'active' : '' }}" href="{{ route('teams.subject-preferences', auth()->user()->teamMember->team) }}">
                                                <i class="bi bi-star me-2"></i> {{ __('app.manage_preferences') }}
                                            </a></li>
                                        @endif
                                    </ul>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('teams*') && !request()->routeIs('teams.subject-preferences*') ? 'active' : '' }}" href="{{ route('teams.index') }}">
                                        <i class="bi bi-people me-1"></i> {{ __('app.my_team') }}
                                    </a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle {{ request()->routeIs('external-documents*', 'subjects.create') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-building me-1"></i> {{ __('External Subject') }}
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item {{ request()->routeIs('subjects.create') ? 'active' : '' }}" href="{{ route('subjects.create') }}">
                                            <i class="bi bi-plus-circle me-2"></i> {{ __('Propose External Subject') }}
                                        </a></li>
                                        <li><a class="dropdown-item {{ request()->routeIs('external-documents*') ? 'active' : '' }}" href="{{ route('external-documents.index') }}">
                                            <i class="bi bi-file-earmark-text me-2"></i> {{ __('External Subject Documents') }}
                                        </a></li>
                                    </ul>
                                </li>
                                @endif
                            </ul>

                            <!-- Right side items -->
                            <div class="d-flex align-items-center" style="gap: 0.25rem;">
                                <!-- Language Switcher -->
                                @include('partials.language-switcher')

                                <!-- Dark Mode Toggle -->
                                <button class="btn btn-outline-secondary dark-mode-toggle" type="button" id="darkModeToggle" title="Toggle Dark Mode">
                                    <i class="bi bi-moon-stars" id="darkModeIcon"></i>
                                </button>

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

                                <!-- User Menu -->
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-person-circle"></i>
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
                </nav>
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
            
            // Dark mode functionality
            const darkModeToggle = document.getElementById('darkModeToggle');
            const darkModeIcon = document.getElementById('darkModeIcon');
            const htmlElement = document.documentElement;
            
            // Check for saved theme preference or default to light mode
            const currentTheme = localStorage.getItem('theme') || 'light';
            htmlElement.setAttribute('data-bs-theme', currentTheme);
            updateDarkModeIcon(currentTheme);
            
            // Dark mode toggle
            if (darkModeToggle) {
                darkModeToggle.addEventListener('click', function() {
                    const currentTheme = htmlElement.getAttribute('data-bs-theme');
                    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                    
                    htmlElement.setAttribute('data-bs-theme', newTheme);
                    localStorage.setItem('theme', newTheme);
                    updateDarkModeIcon(newTheme);
                });
            }
            
            function updateDarkModeIcon(theme) {
                if (darkModeIcon) {
                    if (theme === 'dark') {
                        darkModeIcon.className = 'bi bi-sun-fill';
                    } else {
                        darkModeIcon.className = 'bi bi-moon-stars';
                    }
                }
            }
            
            // Sidebar dropdown functionality
            const sidebarHeadings = document.querySelectorAll('.sidebar-heading');
            
            sidebarHeadings.forEach(heading => {
                heading.addEventListener('click', function() {
                    const target = this.getAttribute('data-bs-target');
                    const section = document.querySelector(target);
                    
                    if (section) {
                        // Toggle collapsed class on heading for icon rotation
                        this.classList.toggle('collapsed');
                        
                        // Save state to localStorage
                        const sectionId = target.replace('#', '');
                        const isCollapsed = this.classList.contains('collapsed');
                        localStorage.setItem('sidebar_' + sectionId, isCollapsed ? 'collapsed' : 'expanded');
                    }
                });
                
                // Restore state from localStorage
                const target = heading.getAttribute('data-bs-target');
                if (target) {
                    const sectionId = target.replace('#', '');
                    const savedState = localStorage.getItem('sidebar_' + sectionId);
                    
                    if (savedState === 'expanded') {
                        heading.classList.remove('collapsed');
                        const section = document.querySelector(target);
                        if (section) {
                            section.classList.add('show');
                        }
                    }
                }
            });
        });
    </script>

    @stack('scripts')

    <!-- Confirmation Modal Components -->
    <x-confirmation-modal />
    <x-delete-confirmation-modal />
</body>
</html>