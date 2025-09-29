<aside class="main-sidebar sidebar-dark-primary elevation-4" >
    <!-- Brand Logo -->
    <a href="{{ route('admin.dashboard') }}" class="brand-link">
        <img src="{{ asset('assets/dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">Rh System </span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ asset('assets/dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                @if(Auth::guard('admin')->check())
                    <a href="#" class="d-block">{{ Auth::guard('admin')->user()->name }}</a>
                @else
                    <a href="#" class="d-block">{{ __('dashboard.Guest') }}</a>
                @endif
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                {{-- <li class="nav-item menu-open">
                    <a href="#" class="nav-link ">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            {{ __('sidebar.dashboard') }}
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.dashboard') }}" class="nav-link ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ __('sidebar.dashboard') }}</p>
                            </a>
                        </li>
                    </ul>
                </li> --}}

                <li class="nav-item menu-open">
                    <a href="#" class="nav-link {{ (request()->is('rh/admin/settings*') || request()->is('rh/admin/finance-calendar*') || request()->is('rh/admin/branches*') || request()->is('rh/admin/shyft_types*') || request()->is('rh/admin/departments*') || request()->is('rh/admin/job_categories*') || request()->is('rh/admin/qualifications*') || request()->is('rh/admin/occasions*') || request()->is('rh/admin/resignations*') || request()->is('rh/admin/nationality*') || request()->is('rh/admin/religions*')) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            {{ __('sidebar.rh_settings_list') }}
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ route('rh.admin.settings.index') }}" class="nav-link {{ (request()->is('rh/admin/settings*')) ? 'active' : '' }}" >
                        <i class="nav-icon fas fa-cog   "></i>
                        <p>
                            {{ __('sidebar.rh_settings') }}
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('rh.admin.finance-calendar.index') }}" class="nav-link {{ (request()->is('rh/admin/finance-calendar*')) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-calendar-alt"></i>
                        <p>
                            {{ __('sidebar.rh_finance_year_calendar') }}
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('rh.admin.branches.index') }}" class="nav-link {{ (request()->is('rh/admin/branches*')) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-calendar-alt"></i>
                        <p>
                            {{ __('sidebar.rh_branches') }}
                        </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('rh.admin.shyft_types.index') }}" class="nav-link {{ (request()->is('rh/admin/shyft_types*')) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-calendar-alt"></i>
                        <p>
                            {{ __('sidebar.rh_shyft_types') }}
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('rh.admin.departments.index') }}"
                        class="nav-link {{ (request()->is('rh/admin/departments*')) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-calendar-alt"></i>
                        <p>
                            {{ __('sidebar.rh_departments') }}
                        </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('rh.admin.job_categories.index') }}"
                        class="nav-link {{ (request()->is('rh/admin/job_categories*')) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-calendar-alt"></i>
                        <p>
                            {{ __('sidebar.rh_job_categories') }}
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('rh.admin.qualifications.index') }}"
                        class="nav-link {{ (request()->is('rh/admin/qualifications*')) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-calendar-alt"></i>
                        <p>
                            {{ __('sidebar.rh_qualifications') }}
                        </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('rh.admin.occasions.index') }}"
                        class="nav-link {{ (request()->is('rh/admin/occasions*')) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-calendar-alt"></i>
                        <p>
                            {{ __('sidebar.official_occasions') }}
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('rh.admin.resignations.index') }}"
                        class="nav-link {{ (request()->is('rh/admin/resignations*')) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-calendar-alt"></i>
                        <p>
                            {{ __('sidebar.resignations_reasons') }}
                        </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('rh.admin.nationalities.index') }}"
                        class="nav-link {{ (request()->is('rh/admin/nationality*')) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-calendar-alt"></i>
                        <p>
                            {{ __('sidebar.nationalities_list') }}
                        </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('rh.admin.religions.index') }}"
                        class="nav-link {{ (request()->is('rh/admin/religions*')) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-calendar-alt"></i>
                        <p>
                            {{ __('sidebar.religions_list') }}
                        </p>
                    </a>
                </li>
            </ul>


            <li class="nav-item menu-open">
                <a href="#" class="nav-link {{ (request()->is('rh/admin/employees*') || request()->is('rh/admin/salary-components*') || request()->is('rh/admin/payrolls*') || request()->is('rh/admin/attendance*')) ? 'active' : '' }}">
                    <i class="nav-icon fas fa-users"></i>
                    <p>
                        {{ __('sidebar.employee_affairs_menu') }}
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('rh.admin.employees.index') }}"
                            class="nav-link {{ (request()->is('rh/admin/employees*')) ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user"></i>
                            <p>
                                {{ __('sidebar.employee_data') }}
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('rh.admin.salary-components.index') }}"
                            class="nav-link {{ (request()->is('rh/admin/salary-components*')) ? 'active' : '' }}">
                            <i class="nav-icon fas fa-money-bill-wave"></i>
                            <p>
                                {{ __('sidebar.salary_components') }}
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('rh.admin.payrolls.index') }}"
                            class="nav-link {{ (request()->is('rh/admin/payrolls*')) ? 'active' : '' }}">
                            <i class="nav-icon fas fa-calculator"></i>
                            <p>
                                {{ __('sidebar.payroll_management') }}
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('rh.admin.attendance.index') }}"
                            class="nav-link {{ (request()->is('rh/admin/attendance*')) ? 'active' : '' }}">
                            <i class="nav-icon fas fa-clock"></i>
                            <p>
                                {{ __('sidebar.attendance_management') }}
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('rh.admin.reports.index') }}"
                            class="nav-link {{ (request()->is('rh/admin/reports*')) ? 'active' : '' }}">
                            <i class="nav-icon fas fa-chart-bar"></i>
                            <p>
                                {{ __('sidebar.reports') }}
                            </p>
                        </a>
                    </li>
                </ul>
            </ul>
        </nav>
    </div>
</aside>
