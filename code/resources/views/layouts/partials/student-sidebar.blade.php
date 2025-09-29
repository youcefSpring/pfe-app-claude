<!-- Brand Logo -->
<a href="{{ route('pfe.student.dashboard') }}" class="brand-link">
    <img src="{{ asset('images/logo.svg') }}" alt="PFE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
    <span class="brand-text font-weight-light">PFE Student</span>
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
                <a href="{{ route('pfe.student.dashboard') }}" class="nav-link {{ request()->routeIs('pfe.student.dashboard*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>Dashboard</p>
                </a>
            </li>

            <!-- My Team -->
            <li class="nav-item">
                <a href="{{ route('pfe.student.teams.my-team') }}" class="nav-link {{ request()->routeIs('pfe.student.teams.my-team*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-users"></i>
                    <p>My Team</p>
                </a>
            </li>

            <!-- Teams Management -->
            <li class="nav-item {{ request()->routeIs('pfe.student.teams*') && !request()->routeIs('pfe.student.teams.my-team*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ request()->routeIs('pfe.student.teams*') && !request()->routeIs('pfe.student.teams.my-team*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-user-friends"></i>
                    <p>
                        Teams
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('pfe.student.teams.index') }}" class="nav-link {{ request()->routeIs('pfe.student.teams.index') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Browse Teams</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('pfe.student.teams.create') }}" class="nav-link {{ request()->routeIs('pfe.student.teams.create') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Create Team</p>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Subjects -->
            <li class="nav-item {{ request()->routeIs('pfe.student.subjects*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ request()->routeIs('pfe.student.subjects*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-book"></i>
                    <p>
                        Subjects
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('pfe.student.subjects.browse') }}" class="nav-link {{ request()->routeIs('pfe.student.subjects.browse') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Browse Subjects</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('pfe.student.subjects.preferences') }}" class="nav-link {{ request()->routeIs('pfe.student.subjects.preferences') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Set Preferences</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('pfe.student.subjects.propose-external') }}" class="nav-link {{ request()->routeIs('pfe.student.subjects.propose-external') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Propose Subject</p>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- My Project -->
            <li class="nav-item">
                <a href="{{ route('pfe.student.projects.my-project') }}" class="nav-link {{ request()->routeIs('pfe.student.projects.my-project*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-project-diagram"></i>
                    <p>My Project</p>
                </a>
            </li>

            <!-- Project Management -->
            <li class="nav-item {{ request()->routeIs('pfe.student.projects*') && !request()->routeIs('pfe.student.projects.my-project*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ request()->routeIs('pfe.student.projects*') && !request()->routeIs('pfe.student.projects.my-project*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-tasks"></i>
                    <p>
                        Project Work
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('pfe.student.projects.progress', ['project' => 'current']) }}" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Progress Tracking</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('pfe.student.projects.deliverables', ['project' => 'current']) }}" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Deliverables</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('pfe.student.projects.timeline', ['project' => 'current']) }}" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Timeline</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('pfe.student.projects.communication', ['project' => 'current']) }}" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Communication</p>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Defense -->
            <li class="nav-item {{ request()->routeIs('pfe.student.defense*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ request()->routeIs('pfe.student.defense*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-graduation-cap"></i>
                    <p>
                        Defense
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('pfe.student.defense.index') }}" class="nav-link {{ request()->routeIs('pfe.student.defense.index') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Defense Schedule</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('pfe.student.defense.preparation', ['project' => 'current']) }}" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Preparation</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('pfe.student.defense.assessment', ['project' => 'current']) }}" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Readiness Check</p>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
</div>