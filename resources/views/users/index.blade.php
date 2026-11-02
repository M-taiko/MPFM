@extends('layouts.master')
@use('App\Support\RoleStyle', 'RS')

@section('title', 'Users')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/ofpage.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/offorms.css') }}">
<style>
.ofp .ctl { display:flex; gap:8px; flex-wrap:wrap; align-items:center; margin-top:18px; }
.ofp .ctl .sp { flex:1; }
.ofp .ctl select { height:40px; padding:0 34px 0 14px; border-radius:14px; border:1px solid var(--line); background:rgba(255,255,255,.06); color:#fff; font:600 13px 'Inter'; appearance:none; -webkit-appearance:none; outline:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' fill='none' stroke='%2322d3ee' stroke-width='2'%3E%3Cpath d='M1 1l5 5 5-5'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 12px center; }
.ofp .ctl select option { background:#0b1426; }
.ofp .ctl .tab { padding:7px 14px; font-size:12.5px; }
.ofp .ctl .tab i { width:8px; height:8px; border-radius:50%; background:var(--rc,#64748b); display:inline-block; margin-right:7px; }
.ofp .users { display:grid; grid-template-columns:repeat(auto-fill,minmax(340px,1fr)); gap:16px; margin-top:16px; }
.ofp .uc { position:relative; padding:18px; border-radius:22px; border:1px solid var(--line); background:linear-gradient(180deg,rgba(255,255,255,.05),rgba(255,255,255,.015)); display:flex; flex-direction:column; gap:14px; transition:transform .35s var(--ease), border-color .3s, box-shadow .3s; overflow:hidden; }
.ofp .uc::before { content:""; position:absolute; left:0; top:0; right:0; height:3px; background:var(--top,var(--cyan)); }
.ofp .uc:hover { transform:translateY(-5px); border-color:var(--top,var(--cyan)); box-shadow:0 18px 40px rgba(0,0,0,.35); }
.ofp .uc.hide { display:none; }
.ofp .uc .hd { display:flex; gap:14px; align-items:center; }
.ofp .uc .av { width:54px; height:54px; font-size:18px; }
.ofp .uc .nm { font-size:16px; font-weight:800; color:#fff; line-height:1.2; display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
.ofp .uc .nm em { font-style:normal; font-size:9.5px; letter-spacing:.1em; text-transform:uppercase; padding:2px 8px; border-radius:50px; background:rgba(34,211,238,.14); border:1px solid rgba(34,211,238,.4); color:#67e8f9; }
.ofp .uc .em2 { font-size:12.5px; color:var(--sub); word-break:break-all; margin-top:2px; }
.ofp .uc .rl { display:flex; flex-wrap:wrap; gap:6px; min-height:26px; }
.ofp .uc .st { display:grid; grid-template-columns:repeat(3,1fr); gap:8px; }
.ofp .uc .st div { padding:9px 10px; border-radius:12px; background:var(--glass); border:1px solid var(--line); text-align:center; } .ofp .uc .st b { display:block; font:700 15px 'Space Grotesk'; color:#fff; } .ofp .uc .st small { font-size:9.5px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:var(--mute); }
.ofp .uc .ft { display:flex; gap:8px; flex-wrap:wrap; margin-top:auto; }
.ofp .lnk.d { color:var(--red); cursor:pointer; font-family:inherit; } .ofp .lnk.d:hover { border-color:var(--red); }
.ofp .bars2 { display:grid; gap:11px; }
.ofp .br { display:grid; grid-template-columns:150px 1fr 28px; gap:12px; align-items:center; font-size:12.5px; color:var(--sub); }
.ofp .br span:first-child { white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.ofp .br .tr { height:12px; border-radius:6px; background:rgba(255,255,255,.07); overflow:hidden; } .ofp .br .tr i { display:block; height:100%; width:0; border-radius:6px; background:var(--rc); transition:width 1.2s var(--ease); }
.ofp .in .br .tr i { width:var(--w); } .ofp .br b { color:#fff; font:700 13px 'Space Grotesk'; text-align:right; }
.ofp .donut { width:150px; height:150px; flex:none; } .ofp .donut .arc { stroke-width:15; }
.ofp .dw { display:flex; align-items:center; gap:22px; flex-wrap:wrap; justify-content:center; }
@media (max-width:640px) { .ofp .users { grid-template-columns:1fr; } .ofp .br { grid-template-columns:110px 1fr 24px; } }
</style>
@endsection

@section('content')
@php
    $total = $users->count();
    $admins = $users->filter(fn ($u) => $u->hasRole('admin'))->count();
    $rolesUsed = $users->flatMap(fn ($u) => $u->roles->pluck('name'))->unique()->count();
    $noRole = $users->filter(fn ($u) => $u->roles->isEmpty())->count();
    $deptCount = $users->pluck('department_id')->filter()->unique()->count();
    $byRole = $roles->filter(fn ($r) => $r->users_count > 0)->sortByDesc('users_count')->values();
    $maxR = max(1, (int) $byRole->max('users_count'));
@endphp
<div class="ofp" id="ofp">
    <div class="bgl"><div class="orb o1"></div><div class="orb o2"></div></div>

    <div class="hero rv">
        <div class="who">
            <span class="lg"></span>
            <div>
                <div class="eyebrow"><i></i> Administration</div>
                <h1>Users <em>&amp; access</em></h1>
                <div class="meta"><span>Who can sign in, which role they hold and what that role lets them do</span></div>
            </div>
        </div>
        <div class="actions">
            <a class="btn2 p" href="{{ route('users.create') }}"><i class="fe fe-user-plus"></i> Add user</a>
            <a class="btn2" href="{{ route('roles.index') }}"><i class="fe fe-shield"></i> Roles</a>
            <a class="btn2" href="{{ route('permissions.index') }}"><i class="fe fe-lock"></i> Permissions</a>
        </div>
    </div>

    @if(session('success'))<div class="okb"><i class="fe fe-check-circle"></i> {{ session('success') }}</div>@endif
    @if(session('error'))<div class="err"><i class="fe fe-alert-circle"></i> {{ session('error') }}</div>@endif

    <div class="kpis">
        @foreach([
            ['Total users', $total, 'accounts', 'fe-users', '#22d3ee'],
            ['Administrators', $admins, 'full access', 'fe-shield', '#f87171'],
            ['Roles in use', $rolesUsed, 'of '.$roles->count().' roles', 'fe-layers', '#a78bfa'],
            ['Departments', $deptCount, 'with members', 'fe-briefcase', '#fbbf24'],
            ['Without a role', $noRole, 'no access yet', 'fe-user-x', $noRole ? '#fb923c' : '#34d399'],
        ] as $i => [$l, $v, $u, $ic, $c])
            <div class="kpi rv" style="--d:{{ $i + 1 }};--c:{{ $c }}"><div class="ic"><i class="fe {{ $ic }}"></i></div><div class="l">{{ $l }}</div><div class="v cu" data-to="{{ $v }}" data-dec="0">{{ $v }}</div><div class="u">{{ $u }}</div></div>
        @endforeach
    </div>

    <div class="sec g2">
        <div class="panel rv">
            <div class="ph"><h3><i class="fe fe-pie-chart"></i> Users per role</h3><span class="sub">A user can hold several roles</span></div>
            <div class="bars2">
                @forelse($byRole as $r)
                    <div class="br" style="--rc:{{ RS::color($r->name) }}"><span title="{{ RS::label($r->name) }}">{{ RS::label($r->name) }}</span><div class="tr"><i style="--w:{{ round($r->users_count / $maxR * 100) }}%"></i></div><b>{{ $r->users_count }}</b></div>
                @empty
                    <div class="empty">No role has been assigned yet</div>
                @endforelse
            </div>
        </div>
        <div class="panel rv" style="--d:1">
            <div class="ph"><h3><i class="fe fe-briefcase"></i> By department</h3></div>
            @php $byDept = $users->groupBy(fn ($u) => $u->department->name ?? 'No department')->map->count()->sortDesc(); $dTot = max(1, $byDept->sum()); $off = 0; $pal = ['#22d3ee', '#fbbf24', '#a78bfa', '#34d399', '#f472b6', '#fb923c']; @endphp
            <div class="dw">
                <svg class="donut" viewBox="0 0 200 200" role="img" aria-label="Users by department">
                    <circle class="arc t" cx="100" cy="100" r="76" pathLength="100" style="fill:none;stroke:rgba(255,255,255,.08)"/>
                    @foreach($byDept as $name => $n)
                        @php $p = $n / $dTot * 100; $c = $pal[$loop->index % count($pal)]; @endphp
                        <circle class="arc" cx="100" cy="100" r="76" pathLength="100" stroke="{{ $c }}" style="stroke-dasharray:{{ round($p, 2) }} 100;stroke-dashoffset:-{{ round($off, 2) }}"/>
                        @php $off += $p; @endphp
                    @endforeach
                    <text x="100" y="106" text-anchor="middle" font-family="Space Grotesk" font-weight="700" font-size="34" fill="#fff">{{ $total }}</text>
                </svg>
                <div class="lgd" style="min-width:170px">@foreach($byDept as $name => $n)<div><b style="background:{{ $pal[$loop->index % count($pal)] }}"></b>{{ $name }}<strong>{{ $n }}</strong></div>@endforeach</div>
            </div>
        </div>
    </div>

    <div class="ctl rv">
        <button class="tab on" data-r="">All ({{ $total }})</button>
        @foreach($roles->where('users_count', '>', 0) as $r)<button class="tab" data-r="{{ $r->name }}" style="--rc:{{ RS::color($r->name) }}"><i></i>{{ RS::label($r->name) }} ({{ $r->users_count }})</button>@endforeach
        <span class="sp"></span>
        @if($departments->count())<select id="deptF" aria-label="Department"><option value="">All departments</option>@foreach($departments as $d)<option value="{{ $d->id }}">{{ $d->name }}</option>@endforeach</select>@endif
        <select id="sortS" aria-label="Sort"><option value="name">Name A–Z</option><option value="newest">Newest first</option><option value="access">Most access</option></select>
        <span class="srch"><i class="fe fe-search"></i><input type="text" id="q" placeholder="Search name or email…" autocomplete="off"></span>
    </div>

    <div class="users" id="users">
        @forelse($users as $u)
            @php $perms = $u->getAllPermissions()->count(); $first = $u->roles->first(); $top = $first ? RS::color($first->name) : '#64748b'; $me = $u->id === auth()->id(); @endphp
            <div class="uc rv" style="--top:{{ $top }}" data-roles="{{ $u->roles->pluck('name')->implode(',') }}" data-dept="{{ $u->department_id }}" data-n="{{ strtolower($u->name.' '.$u->email) }}" data-name="{{ strtolower($u->name) }}" data-new="{{ $u->created_at?->timestamp }}" data-acc="{{ $perms }}">
                <div class="hd">
                    <div class="av" style="--a:{{ RS::avatar($u->id) }}">{{ RS::initials($u->name) }}</div>
                    <div style="min-width:0"><div class="nm">{{ $u->name }} @if($me)<em>You</em>@endif</div><div class="em2"><i class="fe fe-mail"></i> {{ $u->email }}</div></div>
                </div>
                <div class="rl">@forelse($u->roles as $r)<span class="rc" style="--rc:{{ RS::color($r->name) }}">{{ RS::label($r->name) }}</span>@empty<span class="tg">No role assigned</span>@endforelse</div>
                <div class="st">
                    <div><b>{{ $perms }}</b><small>Permissions</small></div>
                    <div><b style="font-size:12.5px">{{ $u->department->name ?? '—' }}</b><small>Department</small></div>
                    <div><b style="font-size:12.5px">{{ $u->created_at?->format('d M Y') ?? '—' }}</b><small>Joined</small></div>
                </div>
                <div class="ft">
                    <a class="lnk" href="{{ route('users.show', $u->id) }}"><i class="fe fe-eye"></i> View</a>
                    <a class="lnk" href="{{ route('users.edit', $u->id) }}"><i class="fe fe-edit"></i> Edit</a>
                    @unless($me)
                        <form method="POST" action="{{ route('users.destroy', $u->id) }}" onsubmit="return confirm('Delete {{ addslashes($u->name) }}? This cannot be undone.')">@csrf @method('DELETE')<button class="lnk d" type="submit"><i class="fe fe-trash-2"></i> Delete</button></form>
                    @endunless
                </div>
            </div>
        @empty
            <div class="panel empty" style="grid-column:1/-1"><svg viewBox="0 0 120 90"><circle cx="60" cy="34" r="16" fill="none" stroke="#64748b" stroke-width="3" stroke-dasharray="6 5"/><path d="M26,80 C30,58 90,58 94,80" fill="none" stroke="#64748b" stroke-width="3" stroke-dasharray="6 5"/></svg>No users yet.</div>
        @endforelse
    </div>
    <div class="empty" id="noMatch" style="display:none">No users match this filter.</div>
</div>
@endsection

@section('js')
<script>
(function () {
    var root = document.getElementById('ofp'), reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
    var box = document.getElementById('users'), cards = [].slice.call(box.querySelectorAll('.uc')), role = '', dept = '', q = '', sort = 'name';
    function apply() {
        var n = 0;
        cards.forEach(function (c) { var ok = (!role || c.dataset.roles.split(',').indexOf(role) > -1) && (!dept || c.dataset.dept === dept) && (!q || c.dataset.n.indexOf(q) > -1); c.classList.toggle('hide', !ok); if (ok) n++; });
        cards.slice().sort(function (a, b) {
            if (sort === 'newest') return b.dataset.new - a.dataset.new;
            if (sort === 'access') return b.dataset.acc - a.dataset.acc;
            return a.dataset.name < b.dataset.name ? -1 : 1;
        }).forEach(function (c) { box.appendChild(c); });
        document.getElementById('noMatch').style.display = n || !cards.length ? 'none' : 'block';
    }
    root.querySelectorAll('.ctl .tab').forEach(function (t) { t.addEventListener('click', function () { root.querySelectorAll('.ctl .tab').forEach(function (x) { x.classList.remove('on'); }); t.classList.add('on'); role = t.dataset.r; apply(); }); });
    var df = document.getElementById('deptF'); if (df) df.addEventListener('change', function () { dept = this.value; apply(); });
    document.getElementById('sortS').addEventListener('change', function () { sort = this.value; apply(); });
    document.getElementById('q').addEventListener('input', function () { q = this.value.toLowerCase(); apply(); });
    function countUp(el) { var to = parseFloat(el.dataset.to) || 0, t0 = null; if (reduce) return; function step(t) { if (t0 === null) t0 = t; var p = Math.min((t - t0) / 1200, 1), e = 1 - Math.pow(1 - p, 4); el.textContent = Math.round(to * e).toLocaleString('en-US'); if (p < 1) requestAnimationFrame(step); } requestAnimationFrame(step); }
    var io = new IntersectionObserver(function (es) { es.forEach(function (e) { if (!e.isIntersecting) return; e.target.classList.add('in'); e.target.querySelectorAll('.cu').forEach(countUp); io.unobserve(e.target); }); }, { threshold: .08 });
    root.querySelectorAll('.rv').forEach(function (el) { io.observe(el); });
    if (reduce) root.querySelectorAll('svg').forEach(function (s) { if (s.pauseAnimations) s.pauseAnimations(); });
})();
</script>
@endsection
