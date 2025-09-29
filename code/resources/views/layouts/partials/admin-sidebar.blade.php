<!-- Brand Logo -->
<a href="{{ route('pfe.dashboard') }}" class="brand-link">
    <img src="{{ asset('images/logo.svg') }}" alt="PFE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
    <span class="brand-text font-weight-light">PFE Platform</span>
</a>

<!-- Sidebar -->
<div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
            <img src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->first_name . ' ' . auth()->user()->last_name) }}"
                 class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
            <a href="{{ route('pfe.profile.show') }}" class="d-block">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</a>
            <small class="text-muted">{{ auth()->user()->role }}</small>
        </div>
    </div>

    <!-- SidebarSearch Form -->
    <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
            <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
            <div class="input-group-append">
                <button class="btn btn-sidebar">
                    <i class="fas fa-search fa-fw"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Dashboard -->
            <li class="nav-item">
                <a href="{{ route('pfe.dashboard') }}" class="nav-link {{ request()->routeIs('pfe.dashboard*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>Dashboard</p>
                </a>
            </li>

            @can('manage', App\Models\Subject::class)
            <!-- Subjects -->
            <li class="nav-item">
                <a href="{{ route('pfe.subjects.index') }}" class="nav-link {{ request()->routeIs('pfe.subjects*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-book"></i>
                    <p>Subjects</p>
                </a>
            </li>
            @endcan

            @can('manage', App\Models\Team::class)
            <!-- Teams -->
            <li class="nav-item">
                <a href="{{ route('pfe.teams.index') }}" class="nav-link {{ request()->routeIs('pfe.teams*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-users"></i>
                    <p>Teams</p>
                </a>
            </li>
            @endcan

            @can('manage', App\Models\Project::class)
            <!-- Projects -->
            <li class="nav-item">
                <a href="{{ route('pfe.projects.index') }}" class="nav-link {{ request()->routeIs('pfe.projects*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-project-diagram"></i>
                    <p>Projects</p>
                </a>
            </li>
            @endcan

            @hasrole('admin_pfe|chef_master|teacher')
            <!-- Defenses -->
            <li class="nav-item">
                <a href="{{ route('pfe.defenses.index') }}" class="nav-link {{ request()->routeIs('pfe.defenses*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-graduation-cap"></i>
                    <p>Defenses</p>
                </a>
            </li>
            @endhasrole

            @hasrole('admin_pfe|chef_master')
            <!-- Administration -->
            <li class="nav-header">ADMINISTRATION</li>
            <li class="nav-item">
                <a href="{{ route('pfe.admin.users.index') }}" class="nav-link {{ request()->routeIs('pfe.admin.users*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-user-cog"></i>
                    <p>Users</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('pfe.admin.rooms.index') }}" class="nav-link {{ request()->routeIs('pfe.admin.rooms*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-door-open"></i>
                    <p>Rooms</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('pfe.admin.conflicts.index') }}" class="nav-link {{ request()->routeIs('pfe.admin.conflicts*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-exclamation-triangle"></i>
                    <p>Conflicts</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('pfe.admin.assignments.index') }}" class="nav-link {{ request()->routeIs('pfe.admin.assignments*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-tasks"></i>
                    <p>Assignments</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('pfe.admin.students.import.index') }}" class="nav-link {{ request()->routeIs('pfe.admin.students.import*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-file-import"></i>
                    <p>Import Students</p>
                </a>
            </li>
            @hasrole('admin_pfe')
            <li class="nav-item">
                <a href="{{ route('pfe.admin.settings') }}" class="nav-link {{ request()->routeIs('pfe.admin.settings*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-cog"></i>
                    <p>Settings</p>
                </a>
            </li>
            @endhasrole
            @endhasrole

            @hasrole('admin_pfe|chef_master|teacher')
            <!-- Reports -->
            <li class="nav-header">REPORTS</li>
            <li class="nav-item">
                <a href="{{ route('pfe.reports.index') }}" class="nav-link {{ request()->routeIs('pfe.reports*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-chart-bar"></i>
                    <p>Analytics</p>
                </a>
            </li>
            @endhasrole
        </ul>
    </nav>
</div>