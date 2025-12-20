<!-- main-sidebar -->
<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar sidebar-scroll">
    {{-- <div class="main-sidebar-header active">
        <a class="desktop-logo logo-light active" href="{{ url('/dashboard') }}"><img src="{{URL::asset('assets/img/brand/logo.png')}}" class="main-logo" alt="logo"></a>
        <a class="desktop-logo logo-dark active" href="{{ url('/dashboard') }}"><img src="{{URL::asset('assets/img/brand/logo-white.png')}}" class="main-logo dark-theme" alt="logo"></a>
        <a class="logo-icon mobile-logo icon-light active" href="{{ url('/dashboard') }}"><img src="{{URL::asset('assets/img/brand/favicon.png')}}" class="logo-icon" alt="logo"></a>
        <a class="logo-icon mobile-logo icon-dark active" href="{{ url('/dashboard') }}"><img src="{{URL::asset('assets/img/brand/favicon-white.png')}}" class="logo-icon dark-theme" alt="logo"></a>
    </div> --}}
    <div class="main-sidebar-header active">
    <a class="desktop-logo logo-light active" href="{{ url('/dashboard') }}">
        <h1 class="main-logo text-2xl font-bold text-indigo-600"><span class="text-gray-700" style="color: gray">FM</span>MP</h1>
    </a>
    <a class="desktop-logo logo-dark active" href="{{ url('/dashboard') }}">
        <h1 class="main-logo dark-theme text-2xl font-bold text-white">MP<span class="text-gray-300">FM</span></h1>
    </a>
    <a class="logo-icon mobile-logo icon-light active" href="{{ url('/dashboard') }}">
        <h1 class="logo-icon text-2xl font-bold text-indigo-600">MP<span class="text-gray-700">FM</span></h1>
    </a>
    <a class="logo-icon mobile-logo icon-dark active" href="{{ url('/dashboard') }}">
        <h1 class="logo-icon dark-theme text-2xl font-bold text-white">MP<span class="text-gray-300">FM</span></h1>
    </a>
</div>
    <div class="main-sidemenu">
        <div class="app-sidebar__user clearfix">
            <div class="dropdown user-pro-body">
                <div class="">
                    <img alt="user-img" class="avatar avatar-xl brround" src="{{URL::asset('assets/img/faces/6.jpg')}}"><span class="avatar-status profile-status bg-green"></span>
                </div>
                <div class="user-info">
                    <h4 class="font-weight-semibold mt-3 mb-0">{{Auth::user()->name}}</h4>
                    <span class="mb-0 text-muted">{{Auth::user()->email}}</span>
                </div>
            </div>
        </div>
        <ul class="side-menu">
            <!-- Main -->
            <li class="slide">
                <a class="side-menu__item" href="{{ url('/dashboard') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M3 13h8V3H3v10zm0 8h8v-2H3v2zm10-8h8v6h-8V3zm6 4h-2v2h2v-2z" opacity=".3"/><path d="M21 3H3c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM3 19h18V5H3v14z"/></svg>
                    <span class="side-menu__label">Dashboard</span>
                </a>
            </li>

            <!-- Well Tests Section -->
            <li class="side-item side-item-category">Well Tests (MPFM)</li>
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="javascript:void(0);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.89 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z" opacity=".3"/><path d="M6 20h12c1.1 0 2-.9 2-2V8l-6-6H6c-1.1 0-2 .9-2 2v16c0 1.1.89 2 2 2zm2-10V4h7v5l5 5v-6z"/></svg>
                    <span class="side-menu__label">Well Tests</span>
                    <i class="angle fe fe-chevron-down"></i>
                </a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ url('/upload') }}">Upload New Log File</a></li>
                    <li><a class="slide-item" href="{{ route('well_tests.index') }}">Test Archive</a></li>
                </ul>
            </li>

            <!-- System Management Section -->
            @can('manage_system')
            <li class="side-item side-item-category">System Management</li>
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="javascript:void(0);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M12 2l-5.5 9h11z" opacity=".3"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 1.19 0 2.34-.21 3.41-.6l3.59-5.91C21.08 4.52 21 3.28 21 2c0-1.19-.08-2.42-.31-3.41L16.9 2H12zm-1 17.5c-4.14 0-7.5-3.36-7.5-7.5s3.36-7.5 7.5-7.5 7.5 3.36 7.5 7.5-3.36 7.5-7.5-3.36-7.5-7.5-7.5z"/></svg>
                    <span class="side-menu__label">System Management</span>
                    <i class="angle fe fe-chevron-down"></i>
                </a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ route('users.index') }}">Users</a></li>
                    <li><a class="slide-item" href="{{ route('roles.index') }}">Roles & Permissions</a></li>
                </ul>
            </li>
            @endcan
        </ul>
    </div>
</aside>
<!-- main-sidebar -->
