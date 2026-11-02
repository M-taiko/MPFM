@extends('layouts.master')
@use('App\Support\RoleStyle', 'RS')

@section('title', 'Roles')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/ofpage.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/offorms.css') }}">
<style>
.ofp .rcards { display:grid; grid-template-columns:repeat(auto-fill,minmax(320px,1fr)); gap:16px; margin-top:18px; }
.ofp .rk { position:relative; padding:18px; border-radius:22px; border:1px solid var(--line); background:linear-gradient(180deg,rgba(255,255,255,.05),rgba(255,255,255,.015)); border-top:3px solid var(--rc); display:flex; flex-direction:column; gap:13px; transition:transform .35s var(--ease), border-color .3s, box-shadow .3s; }
.ofp .rk:hover { transform:translateY(-5px); border-color:var(--rc); box-shadow:0 18px 40px rgba(0,0,0,.35); }
.ofp .rk .hd { display:flex; align-items:center; gap:12px; }
.ofp .rk .ic { width:42px; height:42px; border-radius:14px; display:grid; place-items:center; background:color-mix(in srgb, var(--rc) 18%, transparent); color:var(--rc); font-size:19px; flex:none; }
.ofp .rk .nm { font-size:16px; font-weight:800; color:#fff; line-height:1.2; } .ofp .rk .nm small { display:block; font:500 11px 'Space Grotesk'; color:var(--mute); margin-top:2px; }
.ofp .rk .pb { height:8px; border-radius:8px; background:rgba(255,255,255,.07); overflow:hidden; } .ofp .rk .pb i { display:block; height:100%; width:0; border-radius:8px; background:var(--rc); transition:width 1.2s var(--ease); } .ofp .in .rk .pb i, .ofp .rk.in .pb i { width:var(--w); }
.ofp .rk .pf { display:flex; justify-content:space-between; font-size:12px; color:var(--sub); } .ofp .rk .pf b { color:#fff; font-family:'Space Grotesk'; }
.ofp .rk .pl { display:flex; flex-wrap:wrap; gap:6px; min-height:26px; } .ofp .rk .pl span { padding:3px 10px; border-radius:50px; font-size:11px; color:var(--sub); border:1px solid var(--line); background:var(--glass); }
.ofp .rk .us { display:flex; align-items:center; } .ofp .rk .us .av { width:30px; height:30px; font-size:11px; margin-left:-8px; border:2px solid #0b1426; box-shadow:none; } .ofp .rk .us .av:first-child { margin-left:0; } .ofp .rk .us small { margin-left:10px; color:var(--sub); font-size:12px; }
.ofp .rk .ft { display:flex; gap:8px; flex-wrap:wrap; margin-top:auto; }
.ofp .lnk.d { color:var(--red); cursor:pointer; font-family:inherit; } .ofp .lnk.d:hover { border-color:var(--red); }
/* matrix */
.ofp .mtx { overflow:auto; max-height:640px; border-radius:16px; border:1px solid var(--line); scrollbar-width:thin; scrollbar-color:rgba(96,165,250,.35) transparent; }
.ofp .mtx table { border-collapse:separate; border-spacing:0; width:100%; min-width:900px; font-size:12.5px; }
.ofp .mtx th { position:sticky; top:0; background:#0b1426; z-index:3; padding:10px 6px; border-bottom:1px solid var(--line); font-size:10px; letter-spacing:.04em; color:var(--rc,#9fb0cf); text-align:center; white-space:nowrap; font-weight:700; }
.ofp .mtx th:first-child { left:0; z-index:4; text-align:left; padding-left:14px; color:var(--mute); text-transform:uppercase; letter-spacing:.1em; }
.ofp .mtx th .dot { display:block; width:8px; height:8px; border-radius:50%; background:var(--rc); margin:0 auto 5px; }
.ofp .mtx td { padding:7px 6px; border-bottom:1px solid rgba(255,255,255,.05); text-align:center; }
.ofp .mtx td:first-child { position:sticky; left:0; background:#0b1426; z-index:2; text-align:left; padding-left:14px; color:#e8eefc; white-space:nowrap; font-weight:600; }
.ofp .mtx tr.grp td { background:#0e1a32; color:var(--cyan); font-size:10.5px; letter-spacing:.14em; text-transform:uppercase; font-weight:800; padding:6px 14px; text-align:left; position:sticky; left:0; }
.ofp .mtx tr:hover td:not(.grp) { background:rgba(34,211,238,.05); } .ofp .mtx tr:hover td:first-child { background:#0f2236; }
.ofp .mtx tr.hide { display:none; }
.ofp .cb { appearance:none; -webkit-appearance:none; width:20px; height:20px; border-radius:7px; border:2px solid rgba(148,163,184,.4); background:transparent; cursor:pointer; position:relative; transition:all .2s; margin:0; vertical-align:middle; }
.ofp .cb:checked { background:var(--rc); border-color:transparent; } .ofp .cb:checked::after { content:""; position:absolute; left:5px; top:1px; width:5px; height:10px; border:solid #04111f; border-width:0 2.5px 2.5px 0; transform:rotate(45deg); }
.ofp .cb:disabled { opacity:.45; cursor:not-allowed; } .ofp .cb.busy { animation:rkPulse .7s ease-in-out infinite; } @keyframes rkPulse { 50% { opacity:.3; } }
.ofp .toast { position:fixed; right:26px; bottom:26px; z-index:2000; padding:12px 18px; border-radius:14px; font-size:13px; font-weight:600; background:#0b1426; border:1px solid var(--cyan); color:#fff; box-shadow:0 20px 50px rgba(0,0,0,.5); transform:translateY(140%); transition:transform .4s var(--ease); max-width:340px; } .ofp .toast.on { transform:none; } .ofp .toast.bad { border-color:var(--red); color:#fecaca; }
.ofp .mh { display:flex; gap:10px; flex-wrap:wrap; align-items:center; margin-bottom:14px; } .ofp .mh .sp { flex:1; }
@media (max-width:640px) { .ofp .rcards { grid-template-columns:1fr; } }
</style>
@endsection

@section('content')
@php
    $nPerm = $allPermissions->count();
    $withUsers = $roles->sum(fn ($r) => $r->users->count());
    $top = $roles->sortByDesc(fn ($r) => $r->permissions->count())->first();
    $groups = ['view' => 'View', 'create' => 'Create', 'edit' => 'Edit', 'delete' => 'Delete', 'manage' => 'Manage', 'other' => 'Other'];
    $grouped = $allPermissions->groupBy(fn ($p) => RS::group($p->name));
    $roleJs = $roles->mapWithKeys(fn ($r) => [$r->id => $r->permissions->pluck('name')->values()]);
@endphp
<div class="ofp" id="ofp">
    <div class="bgl"><div class="orb o1"></div><div class="orb o2"></div></div>

    <div class="hero rv">
        <div class="who">
            <span class="lg"></span>
            <div>
                <div class="eyebrow"><i></i> Administration</div>
                <h1>Roles <em>&amp; permissions matrix</em></h1>
                <div class="meta"><span>A role bundles permissions — tick a box in the matrix to change what a role can do, instantly</span></div>
            </div>
        </div>
        <div class="actions">
            <a class="btn2 p" href="{{ route('roles.create') }}"><i class="fe fe-plus"></i> Add role</a>
            <a class="btn2" href="{{ route('users.index') }}"><i class="fe fe-users"></i> Users</a>
            <a class="btn2" href="{{ route('permissions.index') }}"><i class="fe fe-lock"></i> Permissions</a>
        </div>
    </div>

    @if(session('success'))<div class="okb"><i class="fe fe-check-circle"></i> {{ session('success') }}</div>@endif
    @if($errors->any())<div class="err">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif

    <div class="kpis">
        @foreach([
            ['Roles', $roles->count(), 'defined', 'fe-shield', '#22d3ee'],
            ['Permissions', $nPerm, 'in the system', 'fe-lock', '#a78bfa'],
            ['Role assignments', $withUsers, 'across all users', 'fe-users', '#34d399'],
            ['Widest role', $top ? $top->permissions->count() : 0, $top ? RS::label($top->name) : '—', 'fe-award', '#fbbf24'],
        ] as $i => [$l, $v, $u, $ic, $c])
            <div class="kpi rv" style="--d:{{ $i + 1 }};--c:{{ $c }}"><div class="ic"><i class="fe {{ $ic }}"></i></div><div class="l">{{ $l }}</div><div class="v cu" data-to="{{ $v }}" data-dec="0">{{ $v }}</div><div class="u">{{ $u }}</div></div>
        @endforeach
    </div>

    <div class="rcards">
        @foreach($roles as $r)
            @php $pc = $r->permissions->count(); $c = RS::color($r->name); @endphp
            <div class="rk rv" style="--rc:{{ $c }}">
                <div class="hd"><span class="ic"><i class="fe {{ $r->name === 'admin' ? 'fe-shield' : 'fe-user-check' }}"></i></span><div class="nm">{{ RS::label($r->name) }}<small>{{ $r->name }}</small></div></div>
                <div><div class="pb"><i style="--w:{{ $nPerm ? round($pc / $nPerm * 100) : 0 }}%"></i></div><div class="pf" style="margin-top:6px"><span><b>{{ $pc }}</b> of {{ $nPerm }} permissions</span><span>{{ $nPerm ? round($pc / $nPerm * 100) : 0 }}%</span></div></div>
                <div class="pl">@forelse($r->permissions->take(3) as $p)<span>{{ str_replace('_', ' ', $p->name) }}</span>@empty<span>No permissions</span>@endforelse @if($pc > 3)<span>+{{ $pc - 3 }} more</span>@endif</div>
                <div class="us">@foreach($r->users->take(5) as $u)<div class="av" style="--a:{{ RS::avatar($u->id) }}" title="{{ $u->name }}">{{ RS::initials($u->name) }}</div>@endforeach<small>{{ $r->users->count() ? $r->users->count().' user'.($r->users->count() === 1 ? '' : 's') : 'No users' }}</small></div>
                <div class="ft">
                    <a class="lnk" href="{{ route('roles.edit', $r->id) }}"><i class="fe fe-edit"></i> Edit</a>
                    @unless($r->name === 'admin')
                        <form method="POST" action="{{ route('roles.destroy', $r->id) }}" onsubmit="return confirm('Delete the role {{ addslashes(RS::label($r->name)) }}?{{ $r->users->count() ? ' '.$r->users->count().' user(s) will lose it.' : '' }}')">@csrf @method('DELETE')<button class="lnk d" type="submit"><i class="fe fe-trash-2"></i> Delete</button></form>
                    @endunless
                </div>
            </div>
        @endforeach
    </div>

    <div class="sec">
        <div class="panel rv">
            <div class="ph"><h3><i class="fe fe-grid"></i> Permission matrix <span class="sub" style="margin-left:6px">changes save as you click</span></h3></div>
            <div class="mh">
                <button class="tab on" data-g="">All ({{ $nPerm }})</button>
                @foreach($groups as $k => $l)@if(isset($grouped[$k]))<button class="tab" data-g="{{ $k }}">{{ $l }} ({{ $grouped[$k]->count() }})</button>@endif @endforeach
                <span class="sp"></span><span class="srch"><i class="fe fe-search"></i><input type="text" id="mq" placeholder="Find a permission…" autocomplete="off"></span>
            </div>
            <div class="mtx"><table id="mtx">
                <thead><tr><th>Permission</th>@foreach($roles as $r)<th style="--rc:{{ RS::color($r->name) }}" title="{{ RS::label($r->name) }}"><i class="dot"></i>{{ \Illuminate\Support\Str::limit(RS::label($r->name), 14) }}</th>@endforeach</tr></thead>
                <tbody>
                @foreach($groups as $k => $l)
                    @if(isset($grouped[$k]))
                        <tr class="grp" data-g="{{ $k }}"><td colspan="{{ $roles->count() + 1 }}">{{ $l }}</td></tr>
                        @foreach($grouped[$k] as $p)
                            <tr data-g="{{ $k }}" data-n="{{ $p->name }}"><td>{{ str_replace('_', ' ', $p->name) }}</td>
                                @foreach($roles as $r)<td style="--rc:{{ RS::color($r->name) }}"><input type="checkbox" class="cb" data-role="{{ $r->id }}" data-perm="{{ $p->name }}" @checked($r->permissions->contains('id', $p->id)) @disabled($r->name === 'admin') aria-label="{{ $p->name }} for {{ $r->name }}"></td>@endforeach
                            </tr>
                        @endforeach
                    @endif
                @endforeach
                </tbody>
            </table></div>
            <p style="margin:12px 0 0;font-size:12px;color:var(--mute)"><i class="fe fe-lock"></i> The admin column is locked so nobody can accidentally remove the administrators' own access.</p>
        </div>
    </div>
    <div class="toast" id="toast"></div>
</div>
@endsection

@section('js')
<script>
(function () {
    var root = document.getElementById('ofp'), reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
    var STATE = @json($roleJs), csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content'), toast = document.getElementById('toast'), tt;
    function say(msg, bad) { toast.textContent = msg; toast.className = 'toast on' + (bad ? ' bad' : ''); clearTimeout(tt); tt = setTimeout(function () { toast.classList.remove('on'); }, 2200); }
    document.getElementById('mtx').addEventListener('change', function (e) {
        var cb = e.target; if (!cb.classList.contains('cb')) return;
        var id = cb.dataset.role, list = STATE[id].slice(), name = cb.dataset.perm, i = list.indexOf(name);
        if (cb.checked && i === -1) list.push(name); else if (!cb.checked && i > -1) list.splice(i, 1);
        cb.classList.add('busy'); cb.disabled = true;
        fetch('/roles/' + id + '/permissions', { method: 'PUT', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'Content-Type': 'application/json' }, body: JSON.stringify({ permissions: list }), credentials: 'same-origin' })
            .then(function (r) { return r.json().then(function (j) { if (!r.ok) throw new Error(j.message || 'Could not save'); return j; }); })
            .then(function () { STATE[id] = list; say((cb.checked ? 'Granted' : 'Removed') + ': ' + name.replace(/_/g, ' ')); })
            .catch(function (err) { cb.checked = !cb.checked; say(err.message, true); })
            .then(function () { cb.classList.remove('busy'); cb.disabled = false; });
    });
    var g = '', q = '';
    function filt() {
        var rows = document.querySelectorAll('#mtx tbody tr');
        [].forEach.call(rows, function (r) {
            if (r.classList.contains('grp')) { r.classList.toggle('hide', !!g && r.dataset.g !== g); return; }
            r.classList.toggle('hide', (!!g && r.dataset.g !== g) || (!!q && r.dataset.n.replace(/_/g, ' ').indexOf(q) === -1));
        });
    }
    root.querySelectorAll('.mh .tab').forEach(function (t) { t.addEventListener('click', function () { root.querySelectorAll('.mh .tab').forEach(function (x) { x.classList.remove('on'); }); t.classList.add('on'); g = t.dataset.g; filt(); }); });
    document.getElementById('mq').addEventListener('input', function () { q = this.value.toLowerCase(); filt(); });
    function countUp(el) { var to = parseFloat(el.dataset.to) || 0, t0 = null; if (reduce) return; function step(t) { if (t0 === null) t0 = t; var p = Math.min((t - t0) / 1200, 1), e = 1 - Math.pow(1 - p, 4); el.textContent = Math.round(to * e).toLocaleString('en-US'); if (p < 1) requestAnimationFrame(step); } requestAnimationFrame(step); }
    var io = new IntersectionObserver(function (es) { es.forEach(function (e) { if (!e.isIntersecting) return; e.target.classList.add('in'); e.target.querySelectorAll('.cu').forEach(countUp); io.unobserve(e.target); }); }, { threshold: .08 });
    root.querySelectorAll('.rv').forEach(function (el) { io.observe(el); });
})();
</script>
@endsection
