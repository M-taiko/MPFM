@extends('layouts.master')
@use('App\Support\RoleStyle', 'RS')

@section('title', $user->name)

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/ofpage.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/offorms.css') }}">
<style>
.ofp .big { display:flex; align-items:center; gap:22px; }
.ofp .big .av { width:92px; height:92px; font-size:32px; box-shadow:0 0 0 4px rgba(255,255,255,.1),0 12px 40px rgba(0,0,0,.5); }
.ofp .big h1 { margin:0 0 6px; }
.ofp .chips { display:flex; gap:8px; flex-wrap:wrap; align-items:center; }
.ofp .addrow { display:flex; gap:10px; flex-wrap:wrap; align-items:center; margin-top:14px; }
.ofp .addrow select { height:42px; flex:1; min-width:180px; }
.ofp .tabs2 { display:flex; gap:7px; flex-wrap:wrap; margin-bottom:14px; }
.ofp .perms { display:flex; flex-wrap:wrap; gap:8px; }
.ofp .perm { display:inline-flex; align-items:center; gap:8px; padding:7px 13px; border-radius:12px; border:1px solid var(--line); background:var(--glass); font-size:12.5px; color:#e8eefc; }
.ofp .perm small { color:var(--mute); font-size:10.5px; } .ofp .perm i.dot { width:8px; height:8px; border-radius:50%; background:var(--rc); flex:none; }
.ofp .perm.direct { border-color:rgba(251,191,36,.5); background:rgba(251,191,36,.08); }
.ofp .perm button { background:none; border:0; color:#fca5a5; cursor:pointer; font-size:13px; padding:0 0 0 2px; }
.ofp .perm.hide { display:none; }
.ofp .stt { display:grid; grid-template-columns:repeat(auto-fit,minmax(130px,1fr)); gap:10px; }
.ofp .stt > div { padding:12px 14px; border-radius:14px; background:var(--glass); border:1px solid var(--line); } .ofp .stt small { display:block; font-size:10px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--mute); } .ofp .stt b { font:700 22px 'Space Grotesk'; color:#fff; }
.ofp .danger { border-color:rgba(248,113,113,.4); background:rgba(248,113,113,.05); }
.ofp .toast { display:none; margin-top:12px; padding:11px 15px; border-radius:14px; font-size:13px; border:1px solid rgba(248,113,113,.45); background:rgba(248,113,113,.08); color:#fecaca; } .ofp .toast.on { display:block; }
</style>
@endsection

@section('content')
@php
    $me = $user->id === auth()->id();
    $roleNames = $user->roles->pluck('name');
    $direct = $user->permissions->pluck('name');
    $eff = collect();
    foreach ($user->roles as $r) { foreach ($r->permissions as $p) { $eff[$p->name] = ($eff[$p->name] ?? collect())->push($r->name); } }
    foreach ($direct as $p) { $eff[$p] = ($eff[$p] ?? collect())->push('__direct'); }
    $eff = $eff->sortKeys();
    $groups = ['view' => 'View', 'create' => 'Create', 'edit' => 'Edit', 'delete' => 'Delete', 'manage' => 'Manage', 'other' => 'Other'];
    $count = fn ($g) => $eff->filter(fn ($s, $n) => RS::group($n) === $g)->count();
    $freeRoles = $allRoles->whereNotIn('name', $roleNames);
    $freePerms = $allPermissions->whereNotIn('name', $eff->keys());
@endphp
<div class="ofp" id="ofp" data-user="{{ $user->id }}">
    <div class="bgl"><div class="orb o1"></div><div class="orb o2"></div></div>

    <div class="hero rv">
        <div class="big">
            <div class="av" style="--a:{{ RS::avatar($user->id) }}">{{ RS::initials($user->name) }}</div>
            <div>
                <div class="eyebrow"><i></i> User profile</div>
                <h1>{{ $user->name }}</h1>
                <div class="meta">
                    <span><i class="fe fe-mail"></i> {{ $user->email }}</span>
                    <span><i class="fe fe-briefcase"></i> {{ $user->department->name ?? 'No department' }}</span>
                    <span><i class="fe fe-calendar"></i> Joined {{ $user->created_at?->format('d M Y') }}</span>
                    @if($me)<span class="chip info">This is you</span>@endif
                </div>
                <div class="chips" style="margin-top:10px">@forelse($user->roles as $r)<span class="rc" style="--rc:{{ RS::color($r->name) }}">{{ RS::label($r->name) }}</span>@empty<span class="tg">No role assigned</span>@endforelse</div>
            </div>
        </div>
        <div class="actions">
            <a class="btn2 p" href="{{ route('users.edit', $user->id) }}"><i class="fe fe-edit"></i> Edit user</a>
            <a class="btn2" href="{{ route('users.index') }}"><i class="fe fe-arrow-left"></i> All users</a>
        </div>
    </div>

    <div class="sec rv">
        <div class="stt">
            <div><small>Roles</small><b>{{ $roleNames->count() }}</b></div>
            <div><small>Effective permissions</small><b>{{ $eff->count() }}</b></div>
            <div><small>Direct permissions</small><b>{{ $direct->count() }}</b></div>
            <div><small>MPFM tests uploaded</small><b>{{ $activity['mpfm'] }}</b></div>
            <div><small>Vessel tests entered</small><b>{{ $activity['tv'] }}</b></div>
            <div><small>Last MPFM test</small><b style="font-size:15px">{{ $activity['last_mpfm'] ? \Illuminate\Support\Carbon::parse($activity['last_mpfm'])->format('d M Y') : '—' }}</b></div>
        </div>
    </div>

    <div class="sec g21">
        <div class="panel rv">
            <div class="ph"><h3><i class="fe fe-unlock"></i> Effective access <span class="sub" style="margin-left:6px">everything this user is allowed to do</span></h3></div>
            <div class="tabs2">
                <button class="tab on" data-g="">All ({{ $eff->count() }})</button>
                @foreach($groups as $k => $l)@if($count($k))<button class="tab" data-g="{{ $k }}">{{ $l }} ({{ $count($k) }})</button>@endif @endforeach
            </div>
            <div class="perms" id="perms">
                @forelse($eff as $name => $src)
                    @php $isDirect = $src->contains('__direct'); $via = $src->reject(fn ($s) => $s === '__direct')->values(); @endphp
                    <span class="perm {{ $isDirect ? 'direct' : '' }}" data-g="{{ RS::group($name) }}" style="--rc:{{ $via->isNotEmpty() ? RS::color($via->first()) : '#fbbf24' }}"><i class="dot"></i>{{ str_replace('_', ' ', $name) }}<small>{{ $isDirect ? 'direct' : 'via '.RS::label($via->first()) }}{{ $via->count() > 1 ? ' +'.($via->count() - 1) : '' }}</small>@if($isDirect)<button type="button" data-rm-perm="{{ $name }}" title="Remove direct permission">✕</button>@endif</span>
                @empty
                    <div class="empty" style="width:100%">This user has no permissions yet — assign a role.</div>
                @endforelse
            </div>
            @if($freePerms->count())
                <div class="addrow"><select class="fc" id="permSel"><option value="">Give a permission directly…</option>@foreach($freePerms as $p)<option value="{{ $p->name }}">{{ str_replace('_', ' ', $p->name) }}</option>@endforeach</select><button class="btn2" type="button" id="permAdd"><i class="fe fe-plus"></i> Give</button></div>
            @endif
            <div class="toast" id="toast"></div>
        </div>

        <div style="display:grid;gap:18px;align-content:start">
            <div class="panel rv" style="--d:1">
                <div class="ph"><h3><i class="fe fe-shield"></i> Roles</h3></div>
                <div class="chips">@forelse($user->roles as $r)<span class="rc" style="--rc:{{ RS::color($r->name) }}">{{ RS::label($r->name) }} <small style="opacity:.7">{{ $r->permissions->count() }}</small>@unless($me && $r->name === 'admin')<button type="button" data-rm-role="{{ $r->name }}" title="Remove role">✕</button>@endunless</span>@empty<span class="tg">No role assigned</span>@endforelse</div>
                @if($freeRoles->count())
                    <div class="addrow"><select class="fc" id="roleSel"><option value="">Add a role…</option>@foreach($freeRoles as $r)<option value="{{ $r->name }}">{{ RS::label($r->name) }}</option>@endforeach</select><button class="btn2 p" type="button" id="roleAdd"><i class="fe fe-plus"></i> Add</button></div>
                @endif
            </div>
            @unless($me)
            <div class="panel danger rv" style="--d:2">
                <div class="ph"><h3 style="color:#fecaca"><i class="fe fe-alert-triangle" style="background:linear-gradient(135deg,#ef4444,#f87171);color:#fff"></i> Danger zone</h3></div>
                <p style="color:var(--sub);font-size:13px;margin-bottom:12px">Deleting a user removes their access immediately. Tests they uploaded stay in the archive.</p>
                <form method="POST" action="{{ route('users.destroy', $user->id) }}" onsubmit="return confirm('Delete {{ addslashes($user->name) }}? This cannot be undone.')">@csrf @method('DELETE')<button class="btn2" style="border-color:rgba(248,113,113,.6);color:#fca5a5" type="submit"><i class="fe fe-trash-2"></i> Delete user</button></form>
            </div>
            @endunless
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
(function () {
    var root = document.getElementById('ofp'), id = root.dataset.user, toast = document.getElementById('toast');
    var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    function call(method, url, body) {
        return fetch(url, { method: method, headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'Content-Type': 'application/json' }, body: body ? JSON.stringify(body) : undefined, credentials: 'same-origin' })
            .then(function (r) { return r.json().then(function (j) { if (!r.ok) throw new Error((j.message || 'Request failed') + (j.errors ? ' — ' + Object.values(j.errors)[0] : '')); return j; }); })
            .then(function () { location.reload(); })
            .catch(function (e) { toast.textContent = e.message; toast.classList.add('on'); });
    }
    var ra = document.getElementById('roleAdd'); if (ra) ra.addEventListener('click', function () { var v = document.getElementById('roleSel').value; if (v) call('POST', '/users/' + id + '/assign-role', { role: v }); });
    var pa = document.getElementById('permAdd'); if (pa) pa.addEventListener('click', function () { var v = document.getElementById('permSel').value; if (v) call('POST', '/users/' + id + '/assign-permission', { permission: v }); });
    root.querySelectorAll('[data-rm-role]').forEach(function (b) { b.addEventListener('click', function () { if (confirm('Remove the role "' + b.dataset.rmRole + '" from this user?')) call('DELETE', '/users/' + id + '/remove-role/' + encodeURIComponent(b.dataset.rmRole)); }); });
    root.querySelectorAll('[data-rm-perm]').forEach(function (b) { b.addEventListener('click', function () { call('DELETE', '/users/' + id + '/remove-permission/' + encodeURIComponent(b.dataset.rmPerm)); }); });
    root.querySelectorAll('.tabs2 .tab').forEach(function (t) { t.addEventListener('click', function () { root.querySelectorAll('.tabs2 .tab').forEach(function (x) { x.classList.remove('on'); }); t.classList.add('on'); root.querySelectorAll('#perms .perm').forEach(function (p) { p.classList.toggle('hide', t.dataset.g && p.dataset.g !== t.dataset.g); }); }); });
    var io = new IntersectionObserver(function (es) { es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } }); }, { threshold: .08 });
    root.querySelectorAll('.rv').forEach(function (el) { io.observe(el); });
})();
</script>
@endsection
