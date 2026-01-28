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

            <!-- UNIFIED TASK MANAGEMENT (Phase 2 - Enterprise Refactoring) -->
            <li class="side-item side-item-category">Task Management</li>

            <li class="slide">
                <a class="side-menu__item {{ request()->is('tasks*') ? 'active' : '' }}" data-toggle="slide" href="javascript:void(0);">
                    <i class="side-menu__icon fe fe-check-square"></i>
                    <span class="side-menu__label">Tasks</span>
                    <i class="angle fe fe-chevron-down"></i>
                </a>
                <ul class="slide-menu">
                    <li><a class="slide-item {{ request()->is('tasks') && !request()->is('tasks/create') ? 'active' : '' }}" href="{{ route('tasks.index') }}">All Tasks</a></li>
                    @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('job_officer'))
                    <li><a class="slide-item {{ request()->is('tasks/create') ? 'active' : '' }}" href="{{ route('tasks.create') }}">Create Task</a></li>
                    @endif
                </ul>
            </li>

            <!-- Monthly Planning (Job Officer & Admin) -->
            @if(auth()->user()->hasRole('admin') || auth()->user()->can('view_production_supervisor_reports'))
            <li class="side-item side-item-category">Planning & Oversight</li>

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

            <!-- Surveillance & Monitoring (Surveillance Supervisor & Admin) -->
            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('surveillance_supervisor') || auth()->user()->hasRole('production_supervisor'))
            @if(!auth()->user()->hasRole('admin') && !auth()->user()->can('view_production_supervisor_reports'))
            <li class="side-item side-item-category">Monitoring & Review</li>
            @endif

            <li class="slide">
                <a class="side-menu__item {{ request()->is('surveillance*') ? 'active' : '' }}" data-toggle="slide" href="javascript:void(0);">
                    <i class="side-menu__icon fe fe-eye"></i>
                    <span class="side-menu__label">Surveillance</span>
                    <i class="angle fe fe-chevron-down"></i>
                </a>
                <ul class="slide-menu">
                    <li><a class="slide-item {{ request()->is('surveillance/dashboard') ? 'active' : '' }}" href="{{ route('surveillance.dashboard') }}">Dashboard</a></li>
                    <li><a class="slide-item {{ request()->is('surveillance') && !request()->is('surveillance/dashboard') ? 'active' : '' }}" href="{{ route('surveillance.index') }}">Plans for Review</a></li>
                </ul>
            </li>
            @endif

            <!-- Department Operations Section -->
            @php
                $departments = App\Models\Department::where('is_active', true)->orderBy('display_order')->get();
                $userDepartmentId = auth()->user()->department_id;
            @endphp

            @if($departments->count() > 0 && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('department_manager') || auth()->user()->hasRole('operator')))
            <li class="side-item side-item-category">Department Operations</li>

            @foreach($departments as $dept)
                @php
                    $canViewDept = auth()->user()->hasRole('admin') ||
                                   (auth()->user()->hasRole('department_manager') && $userDepartmentId == $dept->id) ||
                                   (auth()->user()->hasRole('operator') && $userDepartmentId == $dept->id);
                @endphp

                @if($canViewDept)
                <li class="slide">
                    <a class="side-menu__item {{ request()->is('departments/' . $dept->id . '*') ? 'active' : '' }}" data-toggle="slide" href="javascript:void(0);">
                        <i class="side-menu__icon fe fe-layers"></i>
                        <span class="side-menu__label">{{ $dept->name }}</span>
                        <i class="angle fe fe-chevron-down"></i>
                    </a>
                    <ul class="slide-menu">
                        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('department_manager'))
                        <li><a class="slide-item" href="{{ route('departments.dashboard', $dept->id) }}">Dashboard</a></li>
                        <li><a class="slide-item" href="{{ route('departments.tasks', $dept->id) }}">Tasks</a></li>
                        <li><a class="slide-item" href="{{ route('departments.users.index', $dept->id) }}">Users</a></li>
                        <li><a class="slide-item" href="{{ route('departments.operators', $dept->id) }}">Team Members</a></li>
                        <li><a class="slide-item" href="{{ route('departments.reports', $dept->id) }}">Reports</a></li>
                        @elseif(auth()->user()->hasRole('operator'))
                        <li><a class="slide-item" href="{{ route('operator.dashboard') }}">My Tasks</a></li>
                        <li><a class="slide-item" href="{{ route('operator.tasks') }}">All Tasks</a></li>
                        @endif
                    </ul>
                </li>
                @endif
            @endforeach
            @endif

            <!-- MPFM Testing & Analysis -->
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

            <!-- MPFM Validation & Analytics (Admin & Supervisors only) -->
            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('surveillance_supervisor') || auth()->user()->hasRole('mpfm_supervisor'))
            <li class="slide">
                <a class="side-menu__item {{ request()->is('mpfm-tests/trending*') ? 'active' : '' }}" data-toggle="slide" href="javascript:void(0);">
                    <i class="side-menu__icon fe fe-trending-up"></i>
                    <span class="side-menu__label">Analytics & Trending</span>
                    <i class="angle fe fe-chevron-down"></i>
                </a>
                <ul class="slide-menu">
                    <li><a class="slide-item {{ request()->is('mpfm-tests/trending-dashboard') ? 'active' : '' }}" href="{{ route('mpfm_tests.trending_dashboard') }}">Trending Dashboard</a></li>
                    <li><a class="slide-item" href="{{ route('mpfm_tests.index') }}">All Tests</a></li>
                </ul>
            </li>
            @endif

            <!-- Field Infrastructure (Admin only) -->
            @if(auth()->user()->hasRole('admin'))
            <li class="side-item side-item-category">Infrastructure</li>

            <li class="slide">
                <a class="side-menu__item {{ request()->is('oil-fields*') || request()->is('towers*') || request()->is('oil-strings*') ? 'active' : '' }}" data-toggle="slide" href="javascript:void(0);">
                    <i class="side-menu__icon fe fe-map"></i>
                    <span class="side-menu__label">Field Setup</span>
                    <i class="angle fe fe-chevron-down"></i>
                </a>
                <ul class="slide-menu">
                    <li><a class="slide-item {{ request()->is('oil-fields*') ? 'active' : '' }}" href="{{ route('oil_fields.index') }}">Oil Fields</a></li>
                    <li><a class="slide-item {{ request()->is('towers*') ? 'active' : '' }}" href="{{ route('towers.index') }}">Towers</a></li>
                    <li><a class="slide-item {{ request()->is('oil-strings*') ? 'active' : '' }}" href="{{ route('oil_strings.index') }}">Oil Strings</a></li>
                </ul>
            </li>
            @endif

            <!-- Administration (Admin only) -->
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
