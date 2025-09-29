<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route('admin.dashboard') }}" class="nav-link">{{ __('navbar.home') }}</a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route('logout') }}" class="nav-link">{{ __('navbar.logout') }}</a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">

        <!-- Messages Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-comments"></i>
                <span class="badge badge-danger navbar-badge">3</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <!-- ... messages list (can be translated as well) ... -->
                <a href="#" class="dropdown-item dropdown-footer">{{ __('navbar.see_all_messages') }}</a>
            </div>
        </li>


        <!-- Notifications Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-bell"></i>
                <span class="badge badge-warning navbar-badge">15</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header">15 {{ __('navbar.notifications') }}</span>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-envelope mr-2"></i> 4 {{ __('navbar.new_messages') }}
                    <span class="float-right text-muted text-sm">3 {{ __('navbar.minutes') }}</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-users mr-2"></i> 8 {{ __('navbar.friend_requests') }}
                    <span class="float-right text-muted text-sm">12 {{ __('navbar.hours') }}</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-file mr-2"></i> 3 {{ __('navbar.new_reports') }}
                    <span class="float-right text-muted text-sm">2 {{ __('navbar.days') }}</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item dropdown-footer">{{ __('navbar.see_all_notifications') }}</a>
            </div>
        </li>

        <!-- Language Dropdown -->
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button">
                🌐 {{ strtoupper(app()->getLocale()) }}
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                    <a class="dropdown-item @if(app()->getLocale() == $localeCode) active @endif"
                       rel="alternate" hreflang="{{ $localeCode }}"
                       href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                        {{ $properties['native'] }}
                    </a>
                @endforeach
            </div>
        </li>



    </ul>
</nav>
