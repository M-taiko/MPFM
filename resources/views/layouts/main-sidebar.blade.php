<!-- main-sidebar -->
<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar sidebar-scroll">
    <div class="main-sidebar-header active">
        <a class="ofs-brand" href="{{ url('/dashboard') }}#top" title="Oil Field Management System">
            <span class="ofs-badge"></span>
            <span class="ofs-name"><b>Oil Field</b><small>Management System</small></span>
        </a>
    </div>

    <div class="main-sidemenu">
        @php
            $ofsName = trim(Auth::user()->name ?? 'User');
            $ofsInit = collect(preg_split('/\s+/', $ofsName))->filter()->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
        @endphp
        <div class="ofs-user">
            <div class="ofs-av">{{ $ofsInit ?: 'U' }}<i></i></div>
            <div class="ofs-uinfo">
                <h4>{{ $ofsName }}</h4>
                <span>{{ str_replace('_', ' ', Auth::user()->roles->pluck('name')->first() ?? 'User') }}</span>
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
                'WORKOVER'    => ['id' => 6, 'icon' => 'fe-settings',        'label' => 'Workover'],
                'MAINTENANCE' => ['id' => 7, 'icon' => 'fe-settings',    'label' => 'Maintenance'],
            ];
            $isAdmin   = auth()->user()->hasRole('admin');
            $isManager = auth()->user()->hasRole('department_manager');
            $isOfficer = auth()->user()->hasRole('job_officer');
            $isOp      = auth()->user()->hasRole('operator') || auth()->user()->hasRole('mpfm_operator');
            $userDeptId = auth()->user()->department_id;

            /* Focus mode: only MPFM + Test Vessel are enabled, everything else is greyed out (config/app.php → focus_mpfm_tv) */
            $focus = config('app.focus_mpfm_tv', true);
        @endphp

        {{-- greyed-out, non-clickable menu entry used for every module outside MPFM / Test Vessel --}}
        @once
        <style>
            .side-menu .is-disabled { opacity:.42; cursor:not-allowed; }
            .side-menu .is-disabled > a, .side-menu .is-disabled a.side-menu__item, .side-menu a.slide-item.is-disabled { pointer-events:none; }
            .side-menu .soon { margin-left:auto; font-size:9px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; padding:2px 7px; border-radius:20px; background:rgba(100,116,139,.18); color:#64748b; }
            .side-menu .slide-item.is-disabled .soon { margin-left:8px; }
        </style>
        @endonce
        @php
            $disabledItem = fn ($icon, $label) =>
                '<li class="slide is-disabled" title="Coming soon"><a class="side-menu__item" href="javascript:void(0);" aria-disabled="true" tabindex="-1">'
                . '<i class="side-menu__icon fe ' . e($icon) . '"></i><span class="side-menu__label">' . e($label) . '</span><span class="soon">Soon</span></a></li>';
            $disabledSub = fn ($icon, $label) =>
                '<li class="is-disabled" title="Coming soon"><a class="slide-item is-disabled" href="javascript:void(0);" aria-disabled="true" tabindex="-1">'
                . '<i class="fe ' . e($icon) . '" style="font-size:.75rem;margin-right:4px;"></i>' . e($label) . '<span class="soon">Soon</span></a></li>';
        @endphp

        <ul class="side-menu">

            {{-- ══════════ Dashboard ══════════ --}}
            <li class="slide">
                <a class="side-menu__item {{ request()->is('dashboard') ? 'active' : '' }}" href="{{ url('/dashboard') }}">
                    <i class="side-menu__icon fe fe-home"></i>
                    <span class="side-menu__label">Dashboard</span>
                </a>
            </li>

            @if($focus)
            {{-- ══════════ Focus mode: flat lists — Dashboard, MPFM links, Test Vessel links, then everything else as "Soon" ══════════ --}}
            @php
                $link = fn ($icon, $label, $href, $active) =>
                    '<li class="slide"><a class="side-menu__item ' . ($active ? 'active' : '') . '" href="' . e($href) . '">'
                    . '<i class="side-menu__icon fe ' . e($icon) . '"></i><span class="side-menu__label">' . e($label) . '</span></a></li>';
                $canMpfm = $isAdmin || $isManager || $isOfficer || $isOp
                    || auth()->user()->hasRole('surveillance_supervisor') || auth()->user()->hasRole('production_supervisor') || auth()->user()->hasRole('mpfm_supervisor');
                $canTv = $isAdmin || $isOfficer || $isOp || auth()->user()->hasRole('surveillance_supervisor');
                $canTvManage = $isAdmin || $isOfficer || auth()->user()->hasRole('surveillance_supervisor');
            @endphp

            @if($canMpfm)
            <li class="side-item side-item-category">MPFM</li>
            {!! $link('fe-upload', 'Upload Log', url('/upload'), request()->is('upload')) !!}
            {!! $link('fe-activity', 'MPFM Tests', route('mpfm_tests.index'), request()->is('mpfm-tests') || (request()->is('mpfm-tests/*') && !request()->is('mpfm-tests/trending*'))) !!}
            {!! $link('fe-folder', 'Test Archive', route('well_tests.index'), request()->is('well-tests*')) !!}
            @if($isAdmin || auth()->user()->hasRole('surveillance_supervisor') || auth()->user()->hasRole('mpfm_supervisor'))
                {!! $link('fe-trending-up', 'Analytics', route('mpfm_tests.trending_dashboard'), request()->is('mpfm-tests/trending*')) !!}
            @endif
            @endif

            @if($canTv)
            <li class="side-item side-item-category">Test Vessel</li>
            {!! $link('fe-anchor', 'Vessels', route('test_vessels.index'), request()->is('test-vessels*') && !request()->routeIs('test_vessels.create')) !!}
            @if($canTvManage)
                {!! $link('fe-plus-circle', 'Add Vessel', route('test_vessels.create'), request()->routeIs('test_vessels.create')) !!}
            @endif
            {!! $link('fe-clipboard', 'Test Requests', route('tv_test_requests.index'), request()->is('tv-test-requests*')) !!}
            {!! $link('fe-list', 'TV Tests', route('tv_tests.index'), request()->is('tv-tests*') && !request()->routeIs('tv_tests.create', 'tv_tests.compare', 'tv_tests.compare_result')) !!}
            {!! $link('fe-plus-square', 'New TV Test', route('tv_tests.create'), request()->routeIs('tv_tests.create')) !!}
            {!! $link('fe-repeat', 'Compare vs MPFM', route('tv_tests.compare'), request()->routeIs('tv_tests.compare', 'tv_tests.compare_result')) !!}
            @endif

            @php
                $canWl = $isAdmin || $isManager || $isOfficer || auth()->user()->hasRole('surveillance_supervisor') || auth()->user()->hasRole('production_supervisor')
                    || ($isOp && auth()->user()->department?->code === 'WIRELINE');
                $canWlManage = $isAdmin || $isManager || $isOfficer || auth()->user()->hasRole('surveillance_supervisor') || auth()->user()->department?->code === 'WIRELINE';
            @endphp
            @if($canWl)
            <li class="side-item side-item-category">Wire Line</li>
            {!! $link('fe-git-branch', 'Dashboard', route('wireline.dashboard'), request()->routeIs('wireline.dashboard')) !!}
            {!! $link('fe-list', 'Operations', route('wireline.operations'), request()->routeIs('wireline.operations', 'wireline.show', 'wireline.edit')) !!}
            @if($canWlManage)
                {!! $link('fe-plus-circle', 'New Operation', route('wireline.create'), request()->routeIs('wireline.create')) !!}
            @endif
            @endif

            @can('manage_users')
            <li class="side-item side-item-category">Administration</li>
            {!! $link('fe-users', 'Users', route('users.index'), request()->is('users*')) !!}
            {!! $link('fe-shield', 'Roles', route('roles.index'), request()->is('roles*')) !!}
            {!! $link('fe-lock', 'Permissions', route('permissions.index'), request()->is('permissions*')) !!}
            @endcan
            <li class="side-item side-item-category">Coming soon</li>
            {!! $disabledItem('fe-clipboard', 'My Tasks') !!}
            {!! $disabledItem('fe-check-square', 'Tasks') !!}
            {!! $disabledItem('fe-calendar', 'Monthly Plans') !!}
            @foreach($deptMap as $code => $dept)
                @if(!in_array($code, ['MPFM', 'WIRELINE'])) {!! $disabledItem($dept['icon'], $dept['label']) !!} @endif
            @endforeach
            {!! $disabledItem('fe-eye', 'Surveillance') !!}
            {!! $disabledItem('fe-map', 'Field Setup') !!}
            @else
            {{-- ══════════ My Tasks — Operator only ══════════ --}}
            @if($isOp && !$isAdmin)
            <li class="side-item side-item-category">My Work</li>
            @if($focus)
                {!! $disabledItem('fe-clipboard', 'My Tasks') !!}
            @else
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
            @endif

            {{-- ══════════ Task Management — Admin / Job Officer ══════════ --}}
            @if($isAdmin || $isOfficer)
            <li class="side-item side-item-category">Task Management</li>
            @if($focus)
                {!! $disabledItem('fe-check-square', 'Tasks') !!}
                {!! $disabledItem('fe-calendar', 'Monthly Plans') !!}
            @else
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
            @endif

            {{-- ══════════ Departments ══════════ --}}
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
            @if($focus && $code !== 'MPFM')
                {!! $disabledItem($dept['icon'], $dept['label']) !!}
            @else
            <li class="slide">
                <a class="side-menu__item {{ $deptActive ? 'active' : '' }}" data-toggle="slide" href="javascript:void(0);">
                    <i class="side-menu__icon fe {{ $dept['icon'] }}"></i>
                    <span class="side-menu__label">{{ $dept['label'] }}</span>
                    <i class="angle fe fe-chevron-down"></i>
                </a>
                <ul class="slide-menu">
                    {{-- Dashboard + Tasks belong to the task workflow, so they are disabled in focus mode --}}
                    @if($focus)
                        {!! $disabledSub('fe-bar-chart-2', 'Dashboard') !!}
                        {!! $disabledSub('fe-list', 'Tasks') !!}
                    @else
                    <li>
                        <a class="slide-item {{ request()->is('dept/' . $code . '/dashboard') ? 'active' : '' }}"
                           href="{{ route('dept.dashboard', $code) }}">
                            <i class="fe fe-bar-chart-2" style="font-size:.75rem;margin-right:4px;"></i>Dashboard
                        </a>
                    </li>
                    <li>
                        <a class="slide-item {{ request()->is('dept/' . $code . '/tasks') ? 'active' : '' }}"
                           href="{{ route('dept.tasks', $code) }}">
                            <i class="fe fe-list" style="font-size:.75rem;margin-right:4px;"></i>Tasks
                        </a>
                    </li>
                    @endif

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
                            <i class="fe fe-folder" style="font-size:.75rem;margin-right:4px;"></i>Test Archive
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
                    @if($focus)
                        {!! $disabledSub('fe-users', 'Team Members') !!}
                        {!! $disabledSub('fe-file-text', 'Reports') !!}
                    @else
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
                    @endif
                </ul>
            </li>
            @endif
            @endif
            @endforeach

            {{-- ══════════ Surveillance ══════════ --}}
            @if($isAdmin || auth()->user()->hasRole('surveillance_supervisor') || auth()->user()->hasRole('production_supervisor'))
            <li class="side-item side-item-category">Surveillance</li>
            @if($focus)
                {!! $disabledItem('fe-eye', 'Surveillance') !!}
            @else
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
            @endif

            {{-- ══════════ Test Vessel (enabled) ══════════ --}}
            @if($isAdmin || auth()->user()->hasRole('surveillance_supervisor') || auth()->user()->hasRole('job_officer') || $isOp)
            <li class="side-item side-item-category">Test Vessel</li>
            <li class="slide">
                <a class="side-menu__item {{ request()->is('test-vessels*') || request()->is('tv-tests*') ? 'active' : '' }}"
                   data-toggle="slide" href="javascript:void(0);">
                    <i class="side-menu__icon fe fe-anchor"></i>
                    <span class="side-menu__label">Test Vessel</span>
                    <i class="angle fe fe-chevron-down"></i>
                </a>
                <ul class="slide-menu">
                    <li><a class="slide-item {{ request()->is('test-vessels*') ? 'active' : '' }}"
                           href="{{ route('test_vessels.index') }}">Vessels</a></li>
                    @if($isAdmin || auth()->user()->hasRole('surveillance_supervisor') || auth()->user()->hasRole('job_officer'))
                    <li><a class="slide-item {{ request()->routeIs('test_vessels.create') ? 'active' : '' }}"
                           href="{{ route('test_vessels.create') }}">Add Vessel</a></li>
                    @endif
                    @if($isAdmin || auth()->user()->hasRole('surveillance_supervisor') || auth()->user()->hasRole('job_officer') || $isOp)
                    <li><a class="slide-item {{ request()->is('tv-test-requests*') ? 'active' : '' }}"
                           href="{{ route('tv_test_requests.index') }}">Test Requests</a></li>
                    @endif
                    <li><a class="slide-item {{ request()->is('tv-tests*') && !request()->routeIs('tv_tests.compare', 'tv_tests.compare_result') ? 'active' : '' }}"
                           href="{{ route('tv_tests.index') }}">TV Tests</a></li>
                    <li><a class="slide-item {{ request()->routeIs('tv_tests.create') ? 'active' : '' }}"
                           href="{{ route('tv_tests.create') }}">New TV Test</a></li>
                    <li><a class="slide-item {{ request()->routeIs('tv_tests.compare') ? 'active' : '' }}"
                           href="{{ route('tv_tests.compare') }}">Compare vs MPFM</a></li>
                </ul>
            </li>
            @endif

            {{-- ══════════ Infrastructure — Admin only ══════════ --}}
            @if($isAdmin)
            <li class="side-item side-item-category">Infrastructure</li>
            @if($focus)
                {!! $disabledItem('fe-map', 'Field Setup') !!}
            @else
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
            @endif

            {{-- ══════════ Administration — Admin only ══════════ --}}
            @can('manage_users')
            <li class="side-item side-item-category">Administration</li>
            @if($focus)
                @else
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
            @endif
            @endcan
            @endif

        </ul>

        @if($focus)
        <div class="ofs-foot">
            <svg viewBox="0 0 120 70" aria-hidden="true">
                <path d="M0,62 H120" stroke="#334155" stroke-width="3"/>
                <path d="M34,62 L42,30 L50,62 M38,50 H46" fill="none" stroke="#64748b" stroke-width="2.5" stroke-linejoin="round"/>
                <g class="ofs-pj-beam"><path d="M10,22 H62" stroke="#22d3ee" stroke-width="5" stroke-linecap="round"/><path d="M10,22 q-6,2 -6,10 q6,2 12,-4" fill="#22d3ee"/></g>
                <path d="M58,22 V60" stroke="#fbbf24" stroke-width="2" stroke-dasharray="3 3"><animate attributeName="stroke-dashoffset" from="0" to="-12" dur="1s" repeatCount="indefinite"/></path>
                <circle cx="92" cy="50" r="10" fill="#0a1222" stroke="#34d399" stroke-width="2"/><path d="M92,50 L96,45" stroke="#fbbf24" stroke-width="2" stroke-linecap="round"/>
                <path d="M92,60 V62" stroke="#64748b" stroke-width="3"/>
            </svg>
            <b>MPFM &amp; Test Vessel</b>
            <small>Demo mode — other modules are coming soon</small>
        </div>
        @endif
    </div>
</aside>
<!-- main-sidebar -->
