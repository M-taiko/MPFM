@extends('layouts.master')
@use('App\Support\RoleStyle', 'RS')

@section('title', 'Permissions')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/ofpage.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/offorms.css') }}">
<style>
.ofp .ctl { display:flex; gap:8px; flex-wrap:wrap; align-items:center; margin-bottom:16px; } .ofp .ctl .sp { flex:1; }
.ofp .gh { display:flex; align-items:center; gap:10px; margin:22px 0 10px; } .ofp .gh:first-of-type { margin-top:4px; }
.ofp .gh b { font-size:12px; letter-spacing:.14em; text-transform:uppercase; color:var(--cyan); } .ofp .gh span { color:var(--mute); font-size:12px; } .ofp .gh::after { content:""; flex:1; height:1px; background:linear-gradient(90deg,var(--line),transparent); }
.ofp .plist2 { display:grid; grid-template-columns:repeat(auto-fill,minmax(360px,1fr)); gap:10px; }
.ofp .pr { display:flex; align-items:center; gap:12px; padding:12px 14px; border-radius:16px; border:1px solid var(--line); background:rgba(255,255,255,.04); transition:transform .25s var(--ease), border-color .25s; }
.ofp .pr:hover { transform:translateX(4px); border-color:rgba(34,211,238,.5); }
.ofp .pr.hide { display:none; }
.ofp .pr .ic { width:34px; height:34px; border-radius:11px; display:grid; place-items:center; background:rgba(34,211,238,.1); color:var(--cyan); flex:none; }
.ofp .pr .nm { font-size:13.5px; font-weight:700; color:#fff; } .ofp .pr .nm small { display:block; font:500 11px 'Space Grotesk'; color:var(--mute); margin-top:1px; }
.ofp .pr .rs { display:flex; flex-wrap:wrap; gap:4px; margin-top:6px; } .ofp .pr .rs .rc { font-size:9px; padding:2px 8px; }
.ofp .pr .ac { margin-left:auto; display:flex; gap:6px; flex:none; }
.ofp .ic2 { width:32px; height:32px; border-radius:10px; border:1px solid var(--line); background:var(--glass); color:var(--sub); display:grid; place-items:center; cursor:pointer; transition:border-color .2s, color .2s; text-decoration:none; font-size:14px; } .ofp .ic2:hover { border-color:var(--cyan); color:#fff; } .ofp .ic2.d:hover { border-color:var(--red); color:#fca5a5; }
.ofp form { display:inline; }
.ofp .unused { font-size:9.5px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:var(--amber); border:1px solid rgba(251,191,36,.45); border-radius:50px; padding:2px 8px; margin-left:8px; }
@media (max-width:640px) { .ofp .plist2 { grid-template-columns:1fr; } }
</style>
@endsection

@section('content')
@php
    $groups = ['view' => 'View', 'create' => 'Create', 'edit' => 'Edit', 'delete' => 'Delete', 'manage' => 'Manage', 'other' => 'Other'];
    $grouped = $permissions->groupBy(fn ($p) => RS::group($p->name));
    $unused = $permissions->filter(fn ($p) => $p->roles->isEmpty())->count();
    $most = $permissions->sortByDesc(fn ($p) => $p->roles->count())->first();
@endphp
<div class="ofp" id="ofp">
    <div class="bgl"><div class="orb o1"></div><div class="orb o2"></div></div>

    <div class="hero rv">
        <div class="who">
            <span class="lg"></span>
            <div>
                <div class="eyebrow"><i></i> Administration</div>
                <h1>Permissions <em>catalogue</em></h1>
                <div class="meta"><span>Every single thing the system can allow — and which roles hold it</span></div>
            </div>
        </div>
        <div class="actions">
            <a class="btn2 p" href="{{ route('permissions.create') }}"><i class="fe fe-plus"></i> Add permission</a>
            <a class="btn2" href="{{ route('roles.index') }}"><i class="fe fe-shield"></i> Roles matrix</a>
            <a class="btn2" href="{{ route('users.index') }}"><i class="fe fe-users"></i> Users</a>
        </div>
    </div>

    @if(session('success'))<div class="okb"><i class="fe fe-check-circle"></i> {{ session('success') }}</div>@endif
    @if($errors->any())<div class="err">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif

    <div class="kpis">
        @foreach([
            ['Permissions', $permissions->count(), 'in the system', 'fe-lock', '#22d3ee'],
            ['Groups', $grouped->count(), 'by action', 'fe-layers', '#a78bfa'],
            ['Not used by any role', $unused, $unused ? 'worth reviewing' : 'all in use', 'fe-alert-circle', $unused ? '#fbbf24' : '#34d399'],
            ['Most shared', $most ? $most->roles->count() : 0, $most ? str_replace('_', ' ', $most->name) : '—', 'fe-share-2', '#34d399'],
        ] as $i => [$l, $v, $u, $ic, $c])
            <div class="kpi rv" style="--d:{{ $i + 1 }};--c:{{ $c }}"><div class="ic"><i class="fe {{ $ic }}"></i></div><div class="l">{{ $l }}</div><div class="v cu" data-to="{{ $v }}" data-dec="0">{{ $v }}</div><div class="u">{{ $u }}</div></div>
        @endforeach
    </div>

    <div class="sec">
        <div class="panel rv">
            <div class="ctl">
                <button class="tab on" data-g="">All ({{ $permissions->count() }})</button>
                @foreach($groups as $k => $l)@if(isset($grouped[$k]))<button class="tab" data-g="{{ $k }}">{{ $l }} ({{ $grouped[$k]->count() }})</button>@endif @endforeach
                <span class="sp"></span><span class="srch"><i class="fe fe-search"></i><input type="text" id="q" placeholder="Search permissions or roles…" autocomplete="off"></span>
            </div>
            @foreach($groups as $k => $l)
                @if(isset($grouped[$k]))
                    <div class="gh" data-g="{{ $k }}"><b>{{ $l }}</b><span>{{ $grouped[$k]->count() }}</span></div>
                    <div class="plist2" data-g="{{ $k }}">
                        @foreach($grouped[$k] as $p)
                            <div class="pr" data-g="{{ $k }}" data-s="{{ strtolower(str_replace('_', ' ', $p->name).' '.$p->roles->pluck('name')->implode(' ')) }}">
                                <span class="ic"><i class="fe fe-{{ ['view' => 'eye', 'create' => 'plus-circle', 'edit' => 'edit-2', 'delete' => 'trash-2', 'manage' => 'settings'][$k] ?? 'zap' }}"></i></span>
                                <div style="min-width:0"><div class="nm">{{ str_replace('_', ' ', $p->name) }}@if($p->roles->isEmpty())<span class="unused">unused</span>@endif<small>{{ $p->name }}</small></div>
                                    @if($p->roles->count())<div class="rs">@foreach($p->roles as $r)<span class="rc" style="--rc:{{ RS::color($r->name) }}">{{ RS::label($r->name) }}</span>@endforeach</div>@endif</div>
                                <div class="ac">
                                    <a class="ic2" href="{{ route('permissions.edit', $p->id) }}" title="Edit"><i class="fe fe-edit"></i></a>
                                    <form method="POST" action="{{ route('permissions.destroy', $p->id) }}" onsubmit="return confirm('Delete the permission {{ addslashes($p->name) }}?{{ $p->roles->count() ? ' It will be removed from '.$p->roles->count().' role(s).' : '' }}')">@csrf @method('DELETE')<button class="ic2 d" type="submit" title="Delete"><i class="fe fe-trash-2"></i></button></form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endforeach
            <div class="empty" id="noMatch" style="display:none">No permission matches.</div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
(function () {
    var root = document.getElementById('ofp'), reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches, g = '', q = '';
    function apply() {
        var n = 0;
        root.querySelectorAll('.pr').forEach(function (r) { var ok = (!g || r.dataset.g === g) && (!q || r.dataset.s.indexOf(q) > -1); r.classList.toggle('hide', !ok); if (ok) n++; });
        root.querySelectorAll('.gh').forEach(function (h) { var any = root.querySelector('.plist2[data-g="' + h.dataset.g + '"] .pr:not(.hide)'); h.style.display = any ? '' : 'none'; });
        document.getElementById('noMatch').style.display = n ? 'none' : 'block';
    }
    root.querySelectorAll('.ctl .tab').forEach(function (t) { t.addEventListener('click', function () { root.querySelectorAll('.ctl .tab').forEach(function (x) { x.classList.remove('on'); }); t.classList.add('on'); g = t.dataset.g; apply(); }); });
    document.getElementById('q').addEventListener('input', function () { q = this.value.toLowerCase(); apply(); });
    function countUp(el) { var to = parseFloat(el.dataset.to) || 0, t0 = null; if (reduce) return; function step(t) { if (t0 === null) t0 = t; var p = Math.min((t - t0) / 1200, 1), e = 1 - Math.pow(1 - p, 4); el.textContent = Math.round(to * e).toLocaleString('en-US'); if (p < 1) requestAnimationFrame(step); } requestAnimationFrame(step); }
    var io = new IntersectionObserver(function (es) { es.forEach(function (e) { if (!e.isIntersecting) return; e.target.classList.add('in'); e.target.querySelectorAll('.cu').forEach(countUp); io.unobserve(e.target); }); }, { threshold: .08 });
    root.querySelectorAll('.rv').forEach(function (el) { io.observe(el); });
})();
</script>
@endsection
