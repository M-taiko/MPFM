<!-- main-sidebar -->
<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar sidebar-scroll">
    <div class="main-sidebar-header active">
        <a class="desktop-logo logo-light active" href="{{ url('/dashboard') }}">
            <h1 class="main-logo text-2xl font-bold" style="color: #667eea;">
                <span style="color: #764ba2;">MP</span>FM
            </h1>
        </a>
        <a class="desktop-logo logo-dark active" href="{{ url('/dashboard') }}">
            <h1 class="main-logo dark-theme text-2xl font-bold text-white">
                MP<span class="text-gray-300">FM</span>
            </h1>
        </a>
        <a class="logo-icon mobile-logo icon-light active" href="{{ url('/dashboard') }}">
            <h1 class="logo-icon text-2xl font-bold" style="color: #667eea;">
                <span style="color: #764ba2;">MP</span>FM
            </h1>
        </a>
        <a class="logo-icon mobile-logo icon-dark active" href="{{ url('/dashboard') }}">
            <h1 class="logo-icon dark-theme text-2xl font-bold text-white">
                MP<span class="text-gray-300">FM</span>
            </h1>
        </a>
    </div>

    <div class="main-sidemenu">
        <div class="app-sidebar__user clearfix">
            <div class="dropdown user-pro-body">
                <div class="">
                    <img alt="user-img" class="avatar avatar-xl brround" src="{{URL::asset('assets/img/faces/6.jpg')}}">
                    <span class="avatar-status profile-status bg-green"></span>
                </div>
                <div class="user-info">
                    <h4 class="font-weight-semibold mt-3 mb-0">{{Auth::user()->name}}</h4>
                    <span class="mb-0 text-muted small">{{ Auth::user()->roles->pluck('name')->first() ?? 'User' }}</span>
                </div>
            </div>
        </div>

        <ul class="side-menu">
            <!-- Dashboard -->
            <li class="slide">
                <a class="side-menu__item {{ request()->is('dashboard') ? 'active' : '' }}" href="{{ url('/dashboard') }}">
                    <i class="side-menu__icon fe fe-home"></i>
                    <span class="side-menu__label">Dashboard</span>
                </a>
            </li>

            <!-- Monthly Planning Section -->
            <li class="side-item side-item-category">Monthly Planning</li>

            <!-- Job Officer - Monthly Plans (Admin or Production Supervisor can see) -->
            @if(auth()->user()->hasRole('admin') || auth()->user()->can('view_production_supervisor_reports'))
            <li class="slide">
                <a class="side-menu__item {{ request()->is('monthly-plans*') ? 'active' : '' }}" data-toggle="slide" href="javascript:void(0);">
                    <i class="side-menu__icon fe fe-clipboard"></i>
                    <span class="side-menu__label">Monthly Plans</span>
                    <i class="angle fe fe-chevron-down"></i>
                </a>
                <ul class="slide-menu">
                    <li><a class="slide-item {{ request()->is('monthly-plans') && !request()->is('monthly-plans/create') ? 'active' : '' }}" href="{{ route('monthly-plans.index') }}">All Plans</a></li>
                    <li><a class="slide-item {{ request()->is('monthly-plans/create') ? 'active' : '' }}" href="{{ route('monthly-plans.create') }}">Create New Plan</a></li>
                </ul>
            </li>
            @endif

            <!-- Surveillance Supervisor - Review Plans (Admin or Surveillance Supervisor can see) -->
            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('surveillance_supervisor') || auth()->user()->hasRole('production_supervisor'))
            <li class="slide">
                <a class="side-menu__item {{ request()->is('surveillance*') ? 'active' : '' }}" data-toggle="slide" href="javascript:void(0);">
                    <i class="side-menu__icon fe fe-eye"></i>
                    <span class="side-menu__label">Surveillance Review</span>
                    <i class="angle fe fe-chevron-down"></i>
                </a>
                <ul class="slide-menu">
                    <li><a class="slide-item {{ request()->is('surveillance') ? 'active' : '' }}" href="{{ route('surveillance.index') }}">Plans for Review</a></li>
                </ul>
            </li>
            @endif

            <!-- MPFM Supervisor - Review & Assign (Admin or MPFM Supervisor can see) -->
            @if(auth()->user()->hasRole('admin') || auth()->user()->can('view_mpfm_supervisor_reports'))
            <li class="slide">
                <a class="side-menu__item {{ request()->is('mpfm-supervisor*') ? 'active' : '' }}" data-toggle="slide" href="javascript:void(0);">
                    <i class="side-menu__icon fe fe-user-check"></i>
                    <span class="side-menu__label">MPFM Supervisor</span>
                    <i class="angle fe fe-chevron-down"></i>
                </a>
                <ul class="slide-menu">
                    <li><a class="slide-item {{ request()->is('mpfm-supervisor/dashboard') ? 'active' : '' }}" href="{{ route('mpfm-supervisor.dashboard') }}">Dashboard</a></li>
                    <li><a class="slide-item" href="{{ route('mpfm-supervisor.dashboard') }}#reviews">Plans for Review</a></li>
                    <li><a class="slide-item" href="{{ route('mpfm-supervisor.dashboard') }}#unassigned">Unassigned Tasks</a></li>
                </ul>
            </li>
            @endif

            <!-- Operator - Tasks & Execution (Admin or MPFM Operator can see) -->
            @if(auth()->user()->hasRole('admin') || auth()->user()->can('view_mpfm_operator_reports'))
            <li class="slide">
                <a class="side-menu__item {{ request()->is('operator*') ? 'active' : '' }}" data-toggle="slide" href="javascript:void(0);">
                    <i class="side-menu__icon fe fe-check-square"></i>
                    <span class="side-menu__label">My Tasks</span>
                    <i class="angle fe fe-chevron-down"></i>
                </a>
                <ul class="slide-menu">
                    <li><a class="slide-item {{ request()->is('operator/dashboard') ? 'active' : '' }}" href="{{ route('operator.dashboard') }}">Dashboard</a></li>
                    <li><a class="slide-item {{ request()->is('operator/tasks') && !request()->is('operator/tasks/*') ? 'active' : '' }}" href="{{ route('operator.tasks') }}">All Tasks</a></li>
                    <li><a class="slide-item {{ request()->is('operator/calendar') ? 'active' : '' }}" href="{{ route('operator.calendar') }}">Calendar</a></li>
                    <li><a class="slide-item {{ request()->is('operator/history') ? 'active' : '' }}" href="{{ route('operator.history') }}">History</a></li>
                </ul>
            </li>
            @endif

            <!-- MPFM Testing -->
            <li class="side-item side-item-category">MPFM Operations</li>

            <li class="slide">
                <a class="side-menu__item {{ request()->is('upload') || request()->is('well-tests*') ? 'active' : '' }}" data-toggle="slide" href="javascript:void(0);">
                    <i class="side-menu__icon fe fe-activity"></i>
                    <span class="side-menu__label">MPFM Tests</span>
                    <i class="angle fe fe-chevron-down"></i>
                </a>
                <ul class="slide-menu">
                    <li><a class="slide-item {{ request()->is('upload') ? 'active' : '' }}" href="{{ url('/upload') }}">Upload Log File</a></li>
                    <li><a class="slide-item {{ request()->is('well-tests') ? 'active' : '' }}" href="{{ route('well_tests.index') }}">Test Archive</a></li>
                    <li><a class="slide-item" href="{{ route('well_tests.compare') }}">Compare Tests</a></li>
                </ul>
            </li>

            <!-- Infrastructure Management -->
            <li class="side-item side-item-category">Field Infrastructure</li>

            <li class="slide">
                <a class="side-menu__item {{ request()->is('oil-fields*') || request()->is('towers*') || request()->is('oil-strings*') ? 'active' : '' }}" data-toggle="slide" href="javascript:void(0);">
                    <i class="side-menu__icon fe fe-map"></i>
                    <span class="side-menu__label">Infrastructure</span>
                    <i class="angle fe fe-chevron-down"></i>
                </a>
                <ul class="slide-menu">
                    <li><a class="slide-item {{ request()->is('oil-fields*') ? 'active' : '' }}" href="{{ route('oil_fields.index') }}">Oil Fields</a></li>
                    <li><a class="slide-item {{ request()->is('towers*') ? 'active' : '' }}" href="{{ route('towers.index') }}">Towers</a></li>
                    <li><a class="slide-item {{ request()->is('oil-strings*') ? 'active' : '' }}" href="{{ route('oil_strings.index') }}">Oil Strings</a></li>
                </ul>
            </li>

            <!-- Reports & Analytics -->
            <li class="side-item side-item-category">Reporting & Analytics</li>

            <li class="slide">
                <a class="side-menu__item {{ request()->is('reports*') ? 'active' : '' }}" data-toggle="slide" href="javascript:void(0);">
                    <i class="side-menu__icon fe fe-bar-chart"></i>
                    <span class="side-menu__label">Reports</span>
                    <i class="angle fe fe-chevron-down"></i>
                </a>
                {{-- <ul class="slide-menu">
                    <li><a class="slide-item {{ request()->is('reports/job-officer') ? 'active' : '' }}" href="{{ route('reports.job-officer') }}">Job Officer Report</a></li>
                    <li><a class="slide-item {{ request()->is('reports/mpfm-supervisor') ? 'active' : '' }}" href="{{ route('reports.mpfm-supervisor') }}">MPFM Supervisor Report</a></li>
                    <li><a class="slide-item {{ request()->is('reports/production') ? 'active' : '' }}" href="{{ route('reports.production') }}">Production Report</a></li>
                </ul> --}}
            </li>

            <!-- SLA Monitoring -->
            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('job_officer') || auth()->user()->hasRole('mpfm_supervisor'))
            <li class="slide">
                <a class="side-menu__item {{ request()->is('sla*') ? 'active' : '' }}" href="{{ url('/sla/dashboard') }}">
                    <i class="side-menu__icon fe fe-alert-circle"></i>
                    <span class="side-menu__label">SLA Monitoring</span>
                </a>
            </li>
            @endif

            <!-- User Management (Admin Only) -->
            @can('manage_users')
            <li class="side-item side-item-category">Administration</li>

            <li class="slide">
                <a class="side-menu__item {{ request()->is('users*') || request()->is('roles*') || request()->is('permissions*') ? 'active' : '' }}" data-toggle="slide" href="javascript:void(0);">
                    <i class="side-menu__icon fe fe-users"></i>
                    <span class="side-menu__label">User Management</span>
                    <i class="angle fe fe-chevron-down"></i>
                </a>
                <ul class="slide-menu">
                    <li><a class="slide-item {{ request()->is('users*') ? 'active' : '' }}" href="{{ route('users.index') }}">Users</a></li>
                    <li><a class="slide-item {{ request()->is('roles*') ? 'active' : '' }}" href="{{ route('roles.index') }}">Roles</a></li>
                    <li><a class="slide-item {{ request()->is('permissions*') ? 'active' : '' }}" href="{{ route('permissions.index') }}">Permissions</a></li>
                </ul>
            </li>
            @endcan
        </ul>
    </div>
</aside>
<!-- main-sidebar -->
