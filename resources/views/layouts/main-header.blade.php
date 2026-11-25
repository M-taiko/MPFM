<!-- main-header opened -->
@php
    $ofsUser = auth()->user();
    $ofsName = trim($ofsUser->name ?? 'User');
    $ofsInit = collect(preg_split('/\s+/', $ofsName))->filter()->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
    $ofsRole = str_replace('_', ' ', $ofsUser->roles->pluck('name')->first() ?? 'User');
    $unreadNotifications = $ofsUser->unreadNotifications;
    $unreadCount = $unreadNotifications->count();
    $pageTitle = trim(\Illuminate\Support\Str::before(trim(strip_tags($__env->yieldContent('title'))), ' - ')) ?: 'Oil Field Management System';
    $section = request()->is('users*', 'roles*', 'permissions*') ? 'Administration' : (request()->is('mpfm-tests*', 'upload', 'well-tests*') ? 'MPFM'
        : (request()->is('test-vessels*', 'tv-tests*', 'tv-test-requests*') ? 'Well Testing'
        : (request()->is('dashboard') ? 'Overview' : 'Workspace')));

    // quick-jump destinations (same visibility rules as the sidebar)
    $ofsIsAdmin = $ofsUser->hasRole('admin');
    $ofsOfficer = $ofsUser->hasRole('job_officer');
    $ofsOp = $ofsUser->hasAnyRole(['operator', 'mpfm_operator']);
    $ofsSurv = $ofsUser->hasRole('surveillance_supervisor');
    $ofsMpfm = $ofsIsAdmin || $ofsUser->hasAnyRole(['department_manager', 'job_officer', 'operator', 'mpfm_operator', 'surveillance_supervisor', 'production_supervisor', 'mpfm_supervisor']);
    $ofsTv = $ofsIsAdmin || $ofsOfficer || $ofsOp || $ofsSurv;
    $jump = [['Dashboard', 'fe-home', url('/dashboard'), 'Overview']];
    if ($ofsMpfm) {
        $jump[] = ['Upload Log', 'fe-upload', url('/upload'), 'MPFM'];
        $jump[] = ['MPFM Tests', 'fe-activity', route('mpfm_tests.index'), 'MPFM'];
        $jump[] = ['Test Archive', 'fe-folder', route('well_tests.index'), 'MPFM'];
        if ($ofsIsAdmin || $ofsSurv || $ofsUser->hasRole('mpfm_supervisor')) $jump[] = ['Analytics / Trending', 'fe-trending-up', route('mpfm_tests.trending_dashboard'), 'MPFM'];
    }
    if ($ofsTv) {
        $jump[] = ['Vessels', 'fe-anchor', route('test_vessels.index'), 'Well Testing'];
        if ($ofsIsAdmin || $ofsOfficer || $ofsSurv) $jump[] = ['Add Vessel', 'fe-plus-circle', route('test_vessels.create'), 'Well Testing'];
        $jump[] = ['Test Requests', 'fe-clipboard', route('tv_test_requests.index'), 'Well Testing'];
        $jump[] = ['TV Tests', 'fe-list', route('tv_tests.index'), 'Well Testing'];
        $jump[] = ['New TV Test', 'fe-plus-square', route('tv_tests.create'), 'Well Testing'];
        $jump[] = ['Compare vs MPFM', 'fe-repeat', route('tv_tests.compare'), 'Well Testing'];
    }
    if ($ofsUser->can('manage_users')) {
        $jump[] = ['Users', 'fe-users', route('users.index'), 'Administration'];
        $jump[] = ['Roles', 'fe-shield', route('roles.index'), 'Administration'];
        $jump[] = ['Permissions', 'fe-lock', route('permissions.index'), 'Administration'];
    }
    $jump[] = ['Notifications', 'fe-bell', route('notifications.index'), 'Account'];
@endphp
<div class="main-header sticky side-header nav nav-item">
    <div class="container-fluid">
        <div class="main-header-left">
            <div class="responsive-logo">
                <a href="{{ url('/dashboard') }}"><span class="ofs-badge"></span></a>
            </div>
            <div class="app-sidebar__toggle" data-toggle="sidebar">
                <a class="open-toggle" href="#" aria-label="Toggle menu"><i class="header-icon fe fe-align-left"></i></a>
                <a class="close-toggle" href="#" aria-label="Close menu"><i class="header-icons fe fe-x"></i></a>
            </div>
            <div class="ofs-crumb">
                <small>{{ $section }}</small>
                <b title="{{ $pageTitle }}">{{ $pageTitle }}</b>
            </div>
        </div>

        <div class="main-header-center mr-3 ml-3 d-sm-none d-md-none d-lg-block" style="margin:0 auto">
            <div class="ofs-search" id="ofsSearch" role="search">
                <i class="fe fe-search"></i>
                <input type="text" id="ofsQ" placeholder="Jump to a page…" autocomplete="off" aria-label="Quick jump">
                <kbd>/</kbd>
                <div class="ofs-res" id="ofsRes"></div>
            </div>
        </div>

        <div class="main-header-right">
            <div class="nav nav-item navbar-nav-right ml-auto">
                <span class="ofs-live"><i></i> Live</span>
                <div class="ofs-clock" aria-label="Local time"><b id="ofsTime">--:--</b><small id="ofsDate">&nbsp;</small></div>

                {{-- notifications --}}
                <div class="dropdown nav-item main-header-notification">
                    <a class="new nav-link" href="#" role="button" data-toggle="dropdown" aria-label="Notifications">
                        <svg xmlns="http://www.w3.org/2000/svg" class="header-icon-svgs" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                        @if($unreadCount > 0)<span class="badge badge-danger badge-counter">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>@endif
                    </a>
                    <div class="dropdown-menu dropdown-menu-right notification-dropdown">
                        <div class="notification-dropdown-header">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="notification-dropdown-title">Notifications</h6>
                                    <small class="notification-dropdown-subtitle">You have {{ $unreadCount }} unread notification{{ $unreadCount != 1 ? 's' : '' }}</small>
                                </div>
                                @if($unreadCount > 0)<span class="notification-badge-new">{{ $unreadCount }} new</span>@endif
                            </div>
                        </div>
                        <div class="notification-dropdown-list">
                            @forelse($unreadNotifications as $notification)
                                @php
                                    $nd = $notification->data;
                                    $ndType = $nd['type'] ?? 'info';
                                    $ndTitle = $nd['title'] ?? ($ndType === 'test_uploaded' ? 'Test Uploaded' : 'Notification');
                                    $ndMessage = $nd['message'] ?? 'New notification';
                                    $ndUrl = $nd['url'] ?? null;
                                    $ndIcon = ['success' => 'fe-check-circle', 'warning' => 'fe-alert-triangle', 'danger' => 'fe-x-circle', 'info' => 'fe-bell', 'test_uploaded' => 'fe-upload'][$ndType] ?? 'fe-bell';
                                    $ndColor = ['success' => '#10b981', 'warning' => '#f59e0b', 'danger' => '#ef4444', 'info' => '#3b82f6', 'test_uploaded' => '#06b6d4'][$ndType] ?? '#3b82f6';
                                @endphp
                                <a class="notification-item" href="{{ $ndUrl ?? route('notifications.index') }}" onclick="markNotificationAsRead('{{ $notification->id }}'); return true;">
                                    <div class="notification-item-icon" style="background:linear-gradient(135deg,{{ $ndColor }},{{ $ndColor }}99)"><i class="fe {{ $ndIcon }}"></i></div>
                                    <div class="notification-item-content">
                                        <h6 class="notification-item-title">{{ $ndTitle }}</h6>
                                        <p class="notification-item-message">{{ \Illuminate\Support\Str::limit($ndMessage, 80) }}</p>
                                        <small class="notification-item-time"><i class="fe fe-clock" style="font-size:.7rem"></i> {{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                    @if(! $notification->read_at)<div class="notification-item-unread"></div>@endif
                                </a>
                            @empty
                                <div class="notification-empty">
                                    <svg viewBox="0 0 96 80" aria-hidden="true"><path d="M48,10 C34,10 26,22 26,36 C26,52 20,56 16,62 H80 C76,56 70,52 70,36 C70,22 62,10 48,10 Z" fill="none" stroke="#64748b" stroke-width="3" stroke-linejoin="round" stroke-dasharray="5 5"/><path d="M40,68 Q48,76 56,68" fill="none" stroke="#64748b" stroke-width="3" stroke-linecap="round"/><path d="M60,28 l5,5 l10,-10" fill="none" stroke="#34d399" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    <p class="notification-empty-text">You're all caught up</p>
                                    <small>No new notifications</small>
                                </div>
                            @endforelse
                        </div>
                        <div class="notification-dropdown-footer"><a href="{{ route('notifications.index') }}"><i class="fe fe-eye"></i> View all notifications</a></div>
                    </div>
                </div>

                <div class="nav-item full-screen fullscreen-button">
                    <a class="new nav-link full-screen-link" href="#" aria-label="Fullscreen"><svg xmlns="http://www.w3.org/2000/svg" class="header-icon-svgs" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"></path></svg></a>
                </div>

                {{-- profile --}}
                <div class="dropdown main-profile-menu nav nav-item ofs-pm">
                    <a class="profile-user d-flex nav-link" href="#" role="button" data-toggle="dropdown" aria-label="Account menu">
                        <span class="ofs-av">{{ $ofsInit ?: 'U' }}</span>
                        <span class="nm"><b>{{ $ofsName }}</b><small>{{ $ofsRole }}</small></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right ofs-pmenu">
                        <div class="hd">
                            <span class="ofs-av">{{ $ofsInit ?: 'U' }}</span>
                            <div><b>{{ $ofsName }}</b><small>{{ $ofsUser->email }}</small></div>
                        </div>
                        <a class="it" href="{{ url('/dashboard') }}"><i class="fe fe-home"></i> Dashboard</a>
                        <a class="it" href="{{ route('notifications.index') }}"><i class="fe fe-bell"></i> Notifications</a>
                        <a class="it out" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i class="fe fe-log-out"></i> Log out</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none">@csrf</form>
                    </div>
                </div>

                <div class="dropdown main-header-message right-toggle">
                    <a class="nav-link pr-0" data-toggle="sidebar-left" data-target=".sidebar-left" aria-label="Side panel">
                        <svg xmlns="http://www.w3.org/2000/svg" class="header-icon-svgs" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function markNotificationAsRead(notificationId) {
    var csrf = document.querySelector('meta[name="csrf-token"]'); csrf = csrf ? csrf.getAttribute('content') : '';
    fetch('/notifications/' + notificationId + '/mark-as-read', { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json', 'Accept': 'application/json' } })
        .then(function (r) { return r.json(); })
        .then(function () {
            var badge = document.querySelector('.badge-counter');
            if (badge) { var n = Math.max(0, (parseInt(badge.textContent) || 0) - 1); if (n > 0) badge.textContent = n > 99 ? '99+' : n; else badge.style.display = 'none'; }
        })
        .catch(function (e) { console.error('Error marking notification as read:', e); });
}
(function () {
    // clock
    var t = document.getElementById('ofsTime'), d = document.getElementById('ofsDate');
    function tick() { var n = new Date(); t.textContent = n.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' }); d.textContent = n.toLocaleDateString('en-GB', { weekday: 'short', day: '2-digit', month: 'short' }); }
    if (t && d) { tick(); setInterval(tick, 20000); }

    // quick jump
    var DEST = @json($jump);
    var box = document.getElementById('ofsSearch'), q = document.getElementById('ofsQ'), res = document.getElementById('ofsRes'), sel = 0, list = [];
    if (!q) return;
    function esc(s) { return String(s).replace(/[&<>"]/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]; }); }
    function render() {
        var s = q.value.trim().toLowerCase();
        list = DEST.filter(function (x) { return !s || (x[0] + ' ' + x[3]).toLowerCase().indexOf(s) > -1; });
        sel = 0;
        res.innerHTML = list.length ? list.map(function (x, i) { return '<a href="' + esc(x[2]) + '" class="' + (i === 0 ? 'sel' : '') + '"><i class="fe ' + x[1] + '"></i>' + esc(x[0]) + '<small>' + esc(x[3]) + '</small></a>'; }).join('') : '<div class="none">No matching page</div>';
        res.classList.add('on');
    }
    function mark() { [].forEach.call(res.querySelectorAll('a'), function (a, i) { a.classList.toggle('sel', i === sel); }); }
    q.addEventListener('focus', render); q.addEventListener('input', render);
    q.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowDown') { e.preventDefault(); sel = Math.min(sel + 1, list.length - 1); mark(); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); sel = Math.max(sel - 1, 0); mark(); }
        else if (e.key === 'Enter' && list[sel]) { window.location.href = list[sel][2]; }
        else if (e.key === 'Escape') { res.classList.remove('on'); q.blur(); }
    });
    document.addEventListener('click', function (e) { if (!box.contains(e.target)) res.classList.remove('on'); });
    document.addEventListener('keydown', function (e) {
        if (e.key === '/' && !/^(INPUT|TEXTAREA|SELECT)$/.test((document.activeElement || {}).tagName || '') && !(document.activeElement || {}).isContentEditable) { e.preventDefault(); q.focus(); }
    });
})();
</script>
<!-- /main-header -->
