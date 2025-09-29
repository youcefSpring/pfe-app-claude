<!-- Brand Logo -->
<a href="{{ route('pfe.teacher.dashboard') }}" class="brand-link">
    <img src="{{ asset('images/logo.svg') }}" alt="PFE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
    <span class="brand-text font-weight-light">PFE Teacher</span>
</a>

<!-- Sidebar -->
<div class="sidebar">
    <!-- Sidebar user panel -->
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
                <a href="{{ route('pfe.teacher.dashboard') }}" class="nav-link {{ request()->routeIs('pfe.teacher.dashboard*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>Dashboard</p>
                </a>
            </li>

            <!-- Subject Management -->
            <li class="nav-item {{ request()->routeIs('pfe.teacher.subjects*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ request()->routeIs('pfe.teacher.subjects*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-book"></i>
                    <p>
                        My Subjects
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('pfe.teacher.subjects.index') }}" class="nav-link {{ request()->routeIs('pfe.teacher.subjects.index') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>All Subjects</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('pfe.teacher.subjects.create') }}" class="nav-link {{ request()->routeIs('pfe.teacher.subjects.create') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Create Subject</p>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Project Supervision -->
            <li class="nav-item {{ request()->routeIs('pfe.teacher.supervision*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ request()->routeIs('pfe.teacher.supervision*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-chalkboard-teacher"></i>
                    <p>
                        Supervision
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('pfe.teacher.supervision.index') }}" class="nav-link {{ request()->routeIs('pfe.teacher.supervision.index') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>My Projects</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('pfe.teacher.supervision.reports') }}" class="nav-link {{ request()->routeIs('pfe.teacher.supervision.reports') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Progress Reports</p>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Deliverable Reviews -->
            <li class="nav-item {{ request()->routeIs('pfe.teacher.deliverables*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ request()->routeIs('pfe.teacher.deliverables*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-file-alt"></i>
                    <p>
                        Deliverables
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('pfe.teacher.deliverables.index') }}" class="nav-link {{ request()->routeIs('pfe.teacher.deliverables.index') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Pending Reviews</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('pfe.teacher.deliverables.analytics') }}" class="nav-link {{ request()->routeIs('pfe.teacher.deliverables.analytics') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Analytics</p>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Defense Management -->
            <li class="nav-item {{ request()->routeIs('pfe.teacher.defenses*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ request()->routeIs('pfe.teacher.defenses*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-graduation-cap"></i>
                    <p>
                        Defenses
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('pfe.teacher.defenses.index') }}" class="nav-link {{ request()->routeIs('pfe.teacher.defenses.index') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>My Defenses</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('pfe.teacher.defenses.calendar') }}" class="nav-link {{ request()->routeIs('pfe.teacher.defenses.calendar') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Calendar</p>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- General Sections -->
            <li class="nav-item">
                <a href="{{ route('pfe.subjects.index') }}" class="nav-link {{ request()->routeIs('pfe.subjects.index') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-list"></i>
                    <p>All Subjects</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('pfe.teams.index') }}" class="nav-link {{ request()->routeIs('pfe.teams.index') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-users"></i>
                    <p>All Teams</p>
                </a>
            </li>

            <!-- Reports Section -->
            <li class="nav-header">REPORTS</li>
            <li class="nav-item">
                <a href="{{ route('pfe.reports.index') }}" class="nav-link {{ request()->routeIs('pfe.reports*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-chart-bar"></i>
                    <p>Analytics</p>
                </a>
            </li>
        </ul>
    </nav>
</div>