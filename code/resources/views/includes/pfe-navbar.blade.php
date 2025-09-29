<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route('pfe.dashboard') }}" class="nav-link">Dashboard</a>
        </li>
        @if(auth()->user()->hasRole('admin_pfe'))
            <li class="nav-item d-none d-sm-inline-block">
                <a href="{{ route('admin.dashboard') }}" class="nav-link">Admin Panel</a>
            </li>
        @endif
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">

        <!-- Navbar Search -->
        <li class="nav-item">
            <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                <i class="fas fa-search"></i>
            </a>
            <div class="navbar-search-block">
                <form class="form-inline" action="{{ route('pfe.search') }}" method="GET">
                    <div class="input-group input-group-sm">
                        <input class="form-control form-control-navbar" type="search" name="q" placeholder="Search" aria-label="Search">
                        <div class="input-group-append">
                            <button class="btn btn-navbar" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                            <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </li>

        <!-- Messages Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-comments"></i>
                <span class="badge badge-danger navbar-badge">3</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <a href="#" class="dropdown-item">
                    <!-- Message Start -->
                    <div class="media">
                        <img src="https://ui-avatars.com/api/?name=Supervisor" alt="User Avatar" class="img-size-50 mr-3 img-circle">
                        <div class="media-body">
                            <h3 class="dropdown-item-title">
                                Supervisor Message
                                <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
                            </h3>
                            <p class="text-sm">Please submit your deliverable...</p>
                            <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 2 Hours Ago</p>
                        </div>
                    </div>
                    <!-- Message End -->
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
            </div>
        </li>

        <!-- Notifications Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-bell"></i>
                @if(auth()->user()->unreadNotifications->count() > 0)
                <span class="badge badge-warning navbar-badge">{{ auth()->user()->unreadNotifications->count() }}</span>
                @endif
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header">{{ auth()->user()->unreadNotifications->count() }} Notifications</span>
                <div class="dropdown-divider"></div>
                @forelse(auth()->user()->notifications->take(5) as $notification)
                <a href="#" class="dropdown-item">
                    <i class="fas fa-envelope mr-2"></i> {{ Str::limit($notification->data['message'] ?? $notification->type, 50) }}
                    <span class="float-right text-muted text-sm">{{ $notification->created_at->diffForHumans() }}</span>
                </a>
                <div class="dropdown-divider"></div>
                @empty
                <span class="dropdown-item">No notifications</span>
                @endforelse
                <a href="{{ route('pfe.notifications.index') }}" class="dropdown-item dropdown-footer">See All Notifications</a>
            </div>
        </li>

        <!-- User Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <img src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->first_name . ' ' . auth()->user()->last_name) }}"
                     alt="User Avatar" class="img-size-32 img-circle">
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <div class="dropdown-header">
                    <strong>{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</strong><br>
                    <small class="text-muted">{{ auth()->user()->role }}</small>
                </div>
                <div class="dropdown-divider"></div>
                <a href="{{ route('pfe.profile.show') }}" class="dropdown-item">
                    <i class="fas fa-user mr-2"></i> Profile
                </a>
                <a href="{{ route('pfe.profile.edit') }}" class="dropdown-item">
                    <i class="fas fa-cogs mr-2"></i> Settings
                </a>
                <div class="dropdown-divider"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </button>
                </form>
            </div>
        </li>

        <!-- Fullscreen -->
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>

    </ul>
</nav>