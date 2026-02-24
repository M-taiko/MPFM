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

        @php
            /* ── Department map: code → [db_id, icon, label] ── */
            $deptMap = [
                'MPFM'        => ['id' => 1, 'icon' => 'fe-activity',   'label' => 'MPFM Operations'],
                'WIRELINE'    => ['id' => 2, 'icon' => 'fe-git-branch',  'label' => 'Wire Line'],
                'PRODUCTION'  => ['id' => 3, 'icon' => 'fe-droplet',     'label' => 'Production Ops'],
                'DRILLING'    => ['id' => 4, 'icon' => 'fe-layers',      'label' => 'Drilling Services'],
                'COMPLETION'  => ['id' => 5, 'icon' => 'fe-anchor',      'label' => 'Well Completion'],
                'WORKOVER'    => ['id' => 6, 'icon' => 'fe-tool',        'label' => 'Workover'],
                'MAINTENANCE' => ['id' => 7, 'icon' => 'fe-settings',    'label' => 'Maintenance'],
            ];
            $isAdmin   = auth()->user()->hasRole('admin');
            $isManager = auth()->user()->hasRole('department_manager');
            $isOfficer = auth()->user()->hasRole('job_officer');
            $isOp      = auth()->user()->hasRole('operator') || auth()->user()->hasRole('mpfm_operator');
            $userDeptId = auth()->user()->department_id;
        @endphp

        <ul class="side-menu">

            {{-- ══════════════════════════════════════
                 Dashboard
            ══════════════════════════════════════ --}}
            <li class="slide">
                <a class="side-menu__item {{ request()->is('dashboard') ? 'active' : '' }}" href="{{ url('/dashboard') }}">
                    <i class="side-menu__icon fe fe-home"></i>
                    <span class="side-menu__label">Dashboard</span>
                </a>
            </li>

            {{-- ══════════════════════════════════════
                 My Tasks — Operator only
            ══════════════════════════════════════ --}}
            @if($isOp && !$isAdmin)
            <li class="side-item side-item-category">My Work</li>
            <li class="slide">
                <a class="side-menu__item {{ request()->is('operator*') ? 'active' : '' }}" data-toggle="slide" href="javascript:void(0);">
                    <i class="side-menu__icon fe fe-clipboard"></i>
                    <span class="side-menu__label">My Tasks</span>
                    <i class="angle fe fe-chevron-down"></i>
                </a>
                <ul class="slide-menu">
                    <li><a class="slide-item {{ request()->routeIs('operator.dashboard') ? 'active' : '' }}" href="{{ route('operator.dashboard') }}">Dashboard</a></li>
                    <li><a class="slide-item {{ request()->routeIs('operator.tasks') ? 'active' : '' }}" href="{{ route('operator.tasks') }}">All Tasks</a></li>
                </ul>
            </li>
            @endif

            {{-- ══════════════════════════════════════
                 Task Management — Admin / Job Officer
            ══════════════════════════════════════ --}}
            @if($isAdmin || $isOfficer)
            <li class="side-item side-item-category">Task Management</li>
            <li class="slide">
                <a class="side-menu__item {{ request()->is('tasks*') ? 'active' : '' }}" data-toggle="slide" href="javascript:void(0);">
                    <i class="side-menu__icon fe fe-check-square"></i>
                    <span class="side-menu__label">Tasks</span>
                    <i class="angle fe fe-chevron-down"></i>
                </a>
                <ul class="slide-menu">
                    <li><a class="slide-item {{ request()->routeIs('tasks.index') ? 'active' : '' }}" href="{{ route('tasks.index') }}">All Tasks</a></li>
                    <li><a class="slide-item {{ request()->routeIs('tasks.create') ? 'active' : '' }}" href="{{ route('tasks.create') }}">Create Task</a></li>
                    <li><a class="slide-item {{ request()->routeIs('tasks.import') ? 'active' : '' }}" href="{{ route('tasks.import') }}">
                        <i class="fe fe-upload" style="font-size:.75rem;margin-right:4px;"></i>Import from Excel
                    </a></li>
                </ul>
            </li>

            <li class="slide">
                <a class="side-menu__item {{ request()->is('monthly-plans*') ? 'active' : '' }}" data-toggle="slide" href="javascript:void(0);">
                    <i class="side-menu__icon fe fe-calendar"></i>
                    <span class="side-menu__label">Monthly Plans</span>
                    <i class="angle fe fe-chevron-down"></i>
                </a>
                <ul class="slide-menu">
                    <li><a class="slide-item {{ request()->routeIs('monthly-plans.index') ? 'active' : '' }}" href="{{ route('monthly-plans.index') }}">All Plans</a></li>
                    <li><a class="slide-item {{ request()->routeIs('monthly-plans.create') ? 'active' : '' }}" href="{{ route('monthly-plans.create') }}">Create New Plan</a></li>
                </ul>
            </li>
            @endif

            {{-- ══════════════════════════════════════
                 Departments — كل قسم submenu مستقل
            ══════════════════════════════════════ --}}
            <li class="side-item side-item-category">Departments</li>

            @foreach($deptMap as $code => $dept)
            @php
                /* الـ operator يشوف قسمه بس، الباقي يشوف الكل */
                $showDept = $isAdmin || $isManager || $isOfficer
                            || auth()->user()->hasRole('surveillance_supervisor')
                            || auth()->user()->hasRole('production_supervisor')
                            || ($isOp && $userDeptId == $dept['id']);

                $deptActive = request()->is('dept/' . $code . '*')
                           || request()->is('departments/' . $dept['id'] . '*');
            @endphp
            @if($showDept)
            <li class="slide">
                <a class="side-menu__item {{ $deptActive ? 'active' : '' }}" data-toggle="slide" href="javascript:void(0);">
                    <i class="side-menu__icon fe {{ $dept['icon'] }}"></i>
                    <span class="side-menu__label">{{ $dept['label'] }}</span>
                    <i class="angle fe fe-chevron-down"></i>
                </a>
                <ul class="slide-menu">
                    {{-- Dashboard --}}
                    <li>
                        <a class="slide-item {{ request()->is('dept/' . $code . '/dashboard') ? 'active' : '' }}"
                           href="{{ route('dept.dashboard', $code) }}">
                            <i class="fe fe-bar-chart-2" style="font-size:.75rem;margin-right:4px;"></i>Dashboard
                        </a>
                    </li>

                    {{-- Tasks --}}
                    <li>
                        <a class="slide-item {{ request()->is('dept/' . $code . '/tasks') ? 'active' : '' }}"
                           href="{{ route('dept.tasks', $code) }}">
                            <i class="fe fe-list" style="font-size:.75rem;margin-right:4px;"></i>Tasks
                        </a>
                    </li>

                    {{-- MPFM: رابط خاص لرفع الـ log --}}
                    @if($code === 'MPFM')
                    <li>
                        <a class="slide-item {{ request()->is('upload') ? 'active' : '' }}"
                           href="{{ url('/upload') }}">
                            <i class="fe fe-upload" style="font-size:.75rem;margin-right:4px;"></i>Upload Log
                        </a>
                    </li>
                    <li>
                        <a class="slide-item {{ request()->is('well-tests*') ? 'active' : '' }}"
                           href="{{ route('well_tests.index') }}">
                            <i class="fe fe-archive" style="font-size:.75rem;margin-right:4px;"></i>Test Archive
                        </a>
                    </li>
                    @if($isAdmin || auth()->user()->hasRole('surveillance_supervisor') || auth()->user()->hasRole('mpfm_supervisor'))
                    <li>
                        <a class="slide-item {{ request()->is('mpfm-tests/trending*') ? 'active' : '' }}"
                           href="{{ route('mpfm_tests.trending_dashboard') }}">
                            <i class="fe fe-trending-up" style="font-size:.75rem;margin-right:4px;"></i>Analytics
                        </a>
                    </li>
                    @endif
                    @endif

                    {{-- Team Members — Manager / Admin فقط --}}
                    @if($isAdmin || ($isManager && $userDeptId == $dept['id']))
                    <li>
                        <a class="slide-item {{ request()->is('departments/' . $dept['id'] . '/operators') ? 'active' : '' }}"
                           href="{{ route('departments.operators', $dept['id']) }}">
                            <i class="fe fe-users" style="font-size:.75rem;margin-right:4px;"></i>Team Members
                        </a>
                    </li>
                    <li>
                        <a class="slide-item {{ request()->is('departments/' . $dept['id'] . '/reports') ? 'active' : '' }}"
                           href="{{ route('departments.reports', $dept['id']) }}">
                            <i class="fe fe-file-text" style="font-size:.75rem;margin-right:4px;"></i>Reports
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif
            @endforeach

            {{-- ══════════════════════════════════════
                 Surveillance
            ══════════════════════════════════════ --}}
            @if($isAdmin || auth()->user()->hasRole('surveillance_supervisor') || auth()->user()->hasRole('production_supervisor'))
            <li class="side-item side-item-category">Surveillance</li>
            <li class="slide">
                <a class="side-menu__item {{ request()->is('surveillance*') ? 'active' : '' }}" data-toggle="slide" href="javascript:void(0);">
                    <i class="side-menu__icon fe fe-eye"></i>
                    <span class="side-menu__label">Surveillance</span>
                    <i class="angle fe fe-chevron-down"></i>
                </a>
                <ul class="slide-menu">
                    <li><a class="slide-item {{ request()->routeIs('surveillance.dashboard') ? 'active' : '' }}" href="{{ route('surveillance.dashboard') }}">Dashboard</a></li>
                    <li><a class="slide-item {{ request()->routeIs('surveillance.index') ? 'active' : '' }}" href="{{ route('surveillance.index') }}">Plans for Review</a></li>
                </ul>
            </li>
            @endif

            {{-- ══════════════════════════════════════
                 Infrastructure — Admin only
            ══════════════════════════════════════ --}}
            @if($isAdmin)
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

            {{-- ══════════════════════════════════════
                 Administration — Admin only
            ══════════════════════════════════════ --}}
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
