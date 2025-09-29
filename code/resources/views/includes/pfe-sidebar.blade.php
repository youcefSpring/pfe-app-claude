<aside class="main-sidebar sidebar-dark-primary elevation-4">
    @if(auth()->user()->hasRole('student'))
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
                        </ul>
                    </li>

                    <!-- My Project -->
                    <li class="nav-item">
                        <a href="{{ route('pfe.student.projects.my-project') }}" class="nav-link {{ request()->routeIs('pfe.student.projects.my-project*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-project-diagram"></i>
                            <p>My Project</p>
                        </a>
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
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>

    @elseif(auth()->user()->hasRole('teacher'))
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
                        </ul>
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

    @else
        <!-- Admin Sidebar -->
        <a href="{{ route('pfe.dashboard') }}" class="brand-link">
            <img src="{{ asset('images/logo.svg') }}" alt="PFE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-light">PFE Platform</span>
        </a>

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
    @endif
</aside>