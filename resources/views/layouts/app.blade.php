<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Oil Field Management System') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Valex Theme CSS -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/skin-modes.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/icons.css') }}" rel="stylesheet">

    <!-- Fevicon -->
    <link rel="icon" href="{{ asset('assets/img/brand/favicon.svg') }}" type="image/svg+xml"/>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="app sidebar-mini">
    <!-- Loader -->
    <div id="global-loader">
        <img src="{{ asset('assets/img/loader.svg') }}" class="loader-img" alt="Loader">
    </div>
    <!-- /Loader -->

    <!-- Page -->
    <div class="page">
        <div class="page-main">
            <!-- App Header -->
            <div class="app-header header sticky">
                <div class="container-fluid main-container">
                    <div class="d-flex">
                        <a aria-label="Hide Sidebar" class="app-sidebar__toggle" data-bs-toggle="sidebar" href="#"></a>
                        <!-- sidebar-toggle-->
                        <a class="header-brand" href="{{ route('dashboard') }}">
                            <img src="{{ asset('assets/img/brand/logo.svg') }}" class="header-brand-img desktop-logo" alt="logo">
                            <img src="{{ asset('assets/img/brand/favicon.svg') }}" class="header-brand-img mobile-view-logo" alt="logo">
                        </a>
                        <div class="d-flex order-lg-2 ms-auto header-right-icons">
                            <div class="dropdown d-none">
                                <a href="#" class="nav-link icon" data-bs-toggle="dropdown">
                                    <i class="fe fe-search"></i>
                                </a>
                                <div class="dropdown-menu header-search dropdown-menu-start">
                                    <div class="input-group w-100 p-2">
                                        <input type="text" class="form-control" placeholder="Search....">
                                        <div class="input-group-text btn btn-primary">
                                            <i class="fe fe-search" aria-hidden="true"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button class="navbar-toggler navresponsive-toggler d-lg-none ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent-4" aria-controls="navbarSupportedContent-4" aria-expanded="false" aria-label="Toggle navigation">
                                <span class="navbar-toggler-icon fe fe-more-vertical"></span>
                            </button>
                            <div class="navbar navbar-collapse responsive-navbar p-0" id="navbarSupportedContent-4">
                                <div class="d-flex order-lg-2">
                                    <div class="dropdown d-lg-none d-flex">
                                        <a href="#" class="nav-link icon" data-bs-toggle="dropdown">
                                            <i class="fe fe-search"></i>
                                        </a>
                                        <div class="dropdown-menu header-search dropdown-menu-start">
                                            <div class="input-group w-100 p-2">
                                                <input type="text" class="form-control" placeholder="Search....">
                                                <div class="input-group-text btn btn-primary">
                                                    <i class="fe fe-search" aria-hidden="true"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex country">
                                        <a class="nav-link icon text-dark" data-bs-target="#modal-language" data-bs-toggle="modal">
                                            <i class="fe fe-globe"></i>
                                        </a>
                                    </div>
                                    <!-- FULL-SCREEN -->
                                    <div class="dropdown  d-flex notifications">
                                        <a class="nav-link icon" data-bs-toggle="dropdown">
                                            <i class="fe fe-bell"></i>
                                            <span class=" pulse"></span>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                            <div class="drop-heading border-bottom">
                                                <div class="d-flex">
                                                    <h6 class="mt-1 mb-0 fs-16 fw-semibold text-dark">Notifications</h6>
                                                    <div class="ms-auto">
                                                        <span class="badge bg-success rounded-pill">3</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="notifications-menu">
                                                <a class="dropdown-item d-flex" href="#">
                                                    <div class="me-3 notifyimg  bg-primary-gradient brround box-shadow-primary">
                                                        <i class="fe fe-mail"></i>
                                                    </div>
                                                    <div class="mt-1 wd-80p">
                                                        <h5 class="notification-label mb-1">New Application received</h5>
                                                        <span class="notification-subtext">3 days ago</span>
                                                    </div>
                                                </a>
                                                <a class="dropdown-item d-flex" href="#">
                                                    <div class="me-3 notifyimg  bg-secondary-gradient brround box-shadow-primary">
                                                        <i class="fe fe-shopping-cart"></i>
                                                    </div>
                                                    <div class="mt-1 wd-80p">
                                                        <h5 class="notification-label mb-1">New Order Received</h5>
                                                        <span class="notification-subtext">1 hour ago</span>
                                                    </div>
                                                </a>
                                                <a class="dropdown-item d-flex" href="#">
                                                    <div class="me-3 notifyimg  bg-success-gradient brround box-shadow-primary">
                                                        <i class="fe fe-message-square"></i>
                                                    </div>
                                                    <div class="mt-1 wd-80p">
                                                        <h5 class="notification-label mb-1">New review received</h5>
                                                        <span class="notification-subtext">2 hour ago</span>
                                                    </div>
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <a href="#" class="dropdown-item text-center">See all Notifications</a>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- NOTIFICATIONS -->
                                    <div class="dropdown d-flex profile-1">
                                        @auth
                                        <a href="#" data-bs-toggle="dropdown" class="nav-link leading-none d-flex">
                                            <img src="{{ asset('assets/img/users/1.jpg') }}" alt="profile-user" class="avatar  profile-user brround cover-image">
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                            <div class="drop-heading">
                                                <div class="text-center">
                                                    <h5 class="text-dark mb-0 fs-14 fw-semibold">{{ Auth::user()->name }}</h5>
                                                    <small class="text-muted">Administrator</small>
                                                </div>
                                            </div>
                                            <div class="dropdown-divider m-0"></div>
                                            <a class="dropdown-item" href="#">
                                                <i class="dropdown-icon fe fe-user"></i> Profile
                                            </a>
                                            <a class="dropdown-item" href="#">
                                                <i class="dropdown-icon fe fe-settings"></i> Settings
                                            </a>
                                            <a class="dropdown-item" href="#">
                                                <i class="dropdown-icon fe fe-mail"></i> Inbox
                                                <span class="badge bg-danger rounded-pill float-end">3</span>
                                            </a>
                                            <div class="dropdown-divider mt-0"></div>
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <button type="submit" class="dropdown-item">
                                                    <i class="dropdown-icon fe fe-log-out"></i> Sign out
                                                </button>
                                            </form>
                                        </div>
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /App Header -->

            <!-- Sidebar Menu -->
            <div class="app-sidebar__overlay" data-bs-toggle="sidebar"></div>
            <aside class="app-sidebar sidebar-scroll">
                <div class="app-sidebar__user clearfix">
                    <div class="dropdown user-pro-body">
                        <div class="">
                            <img alt="user-img" class="avatar avatar-xl brround cover-image" src="{{ asset('assets/img/users/1.jpg') }}">
                            <span class="avatar-status profile-status bg-green"></span>
                        </div>
                        <div class="user-info">
                            <h4 class="font-weight-semibold mt-3 mb-0">{{ Auth::user()->name ?? 'Guest' }}</h4>
                            <span class="mb-0 text-muted">Administrator</span>
                        </div>
                    </div>
                </div>
                <ul class="side-menu">
                    <li class="side-item side-item-category">Main</li>
                    <li class="slide">
                        <a class="side-menu__item {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="side-menu__icon fe fe-home"></i>
                            <span class="side-menu__label">Dashboard</span>
                        </a>
                    </li>
                    <li class="slide">
                        <a class="side-menu__item {{ request()->routeIs('upload.form') ? 'active' : '' }}" href="{{ route('upload.form') }}">
                            <i class="side-menu__icon fe fe-upload"></i>
                            <span class="side-menu__label">Upload Log</span>
                        </a>
                    </li>
                    <li class="side-item side-item-category">Access Control</li>
                    <li class="slide">
                        <a class="side-menu__item {{ request()->routeIs('roles.index') ? 'active' : '' }}" href="{{ route('roles.index') }}">
                            <i class="side-menu__icon fe fe-lock"></i>
                            <span class="side-menu__label">Roles</span>
                        </a>
                    </li>
                    <li class="slide">
                        <a class="side-menu__item {{ request()->routeIs('permissions.index') ? 'active' : '' }}" href="{{ route('permissions.index') }}">
                            <i class="side-menu__icon fe fe-key"></i>
                            <span class="side-menu__label">Permissions</span>
                        </a>
                    </li>
                    <li class="slide">
                        <a class="side-menu__item {{ request()->routeIs('users.index') ? 'active' : '' }}" href="{{ route('users.index') }}">
                            <i class="side-menu__icon fe fe-users"></i>
                            <span class="side-menu__label">Users</span>
                        </a>
                    </li>
                </ul>
                <div class="slide-leftRTL" id="slide-left"><i class="fa fa-angle-left"></i></div>
                <div class="slide-rightRTL" id="slide-right"><i class="fa fa-angle-right"></i></div>
            </aside>
            <!-- /Sidebar Menu -->

            <!-- Main Content -->
            <div class="main-content app-content mt-0">
                <div class="side-app">
                    <!-- Page Header -->
                    <div class="page-header">
                        <div class="page-leftheader">
                            <h4 class="page-title">@yield('title', 'MPFM')</h4>
                            @hasSection('breadcrumb')
                            <ol class="breadcrumb">
                                @yield('breadcrumb')
                            </ol>
                            @endif
                        </div>
                    </div>
                    <!-- /Page Header -->

                    <!-- Page Content -->
                    <div class="container-fluid pt-3">
                        <!-- Session Status -->
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <span class="alert-inner--icon"><i class="fe fe-check-circle"></i></span>
                                <span class="alert-inner--text"><strong>Success!</strong> {{ session('success') }}</span>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <span class="alert-inner--icon"><i class="fe fe-alert-triangle"></i></span>
                                <span class="alert-inner--text"><strong>Error!</strong> {{ session('error') }}</span>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                        @endif

                        @yield('content')
                    </div>
                </div>
            </div>
            <!-- /Main Content -->

            <!-- Footer -->
            <footer class="footer">
                <div class="container">
                    <div class="row align-items-center flex-row-reverse">
                        <div class="col-md-12 col-sm-12 text-center">
                            Copyright © {{ date('Y') }} <a href="#">MPFM</a>. Designed by <a href="#">Spruko</a> (Valex Theme)
                        </div>
                    </div>
                </div>
            </footer>
            <!-- /Footer -->
        </div>
    </div>
    <!-- /Page -->

    <!-- Back to Top -->
    <a href="#top" id="back-to-top"><i class="fa fa-angle-up"></i></a>

    <!-- Scripts -->
    <script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/popper.js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/sidemenu.js') }}"></script>
    <script src="{{ asset('assets/js/sticky.js') }}"></script>
    <script src="{{ asset('assets/js/custom.js') }}"></script>

    @yield('scripts')
</body>
</html>
