{{-- Shared create/edit form for roles. Vars: $permissions, $role (nullable) --}}
@use('App\Support\RoleStyle', 'RS')
@php
    $edit = isset($role) && $role && $role->exists;
    $name = old('name', $edit ? $role->name : '');
    $have = collect(old('permissions', $edit ? $role->permissions->pluck('name')->all() : []));
    $groups = ['view' => 'View', 'create' => 'Create', 'edit' => 'Edit', 'delete' => 'Delete', 'manage' => 'Manage', 'other' => 'Other'];
    $grouped = $permissions->groupBy(fn ($p) => RS::group($p->name));
    $locked = $edit && $role->name === 'admin';
@endphp
<link rel="stylesheet" href="{{ asset('assets/css/ofpage.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/offorms.css') }}">
<style>
.ofp .pg { margin-bottom:18px; } .ofp .pg:last-child { margin-bottom:0; }
.ofp .pg .gh { display:flex; align-items:center; gap:10px; margin-bottom:10px; }
.ofp .pg .gh b { font-size:12px; letter-spacing:.14em; text-transform:uppercase; color:var(--cyan); } .ofp .pg .gh span { color:var(--mute); font-size:12px; }
.ofp .pg .gh button { margin-left:auto; padding:4px 12px; border-radius:50px; border:1px solid var(--line); background:var(--glass); color:var(--sub); font:600 11.5px 'Inter'; cursor:pointer; } .ofp .pg .gh button:hover { border-color:var(--cyan); color:#fff; }
.ofp .pl2 { display:flex; flex-wrap:wrap; gap:8px; }
.ofp .pl2 input { position:absolute; opacity:0; pointer-events:none; }
.ofp .pl2 label { margin:0; cursor:pointer; display:inline-flex; align-items:center; gap:8px; padding:8px 14px; border-radius:12px; border:1px solid var(--line); background:rgba(255,255,255,.04); color:var(--sub); font-size:12.5px; font-weight:600; transition:all .2s var(--ease); user-select:none; }
.ofp .pl2 label::before { content:""; width:14px; height:14px; border-radius:5px; border:2px solid rgba(148,163,184,.5); flex:none; transition:all .2s; }
.ofp .pl2 label:hover { border-color:var(--rc); color:#fff; }
.ofp .pl2 input:checked + label { border-color:var(--rc); background:color-mix(in srgb, var(--rc) 14%, transparent); color:#fff; }
.ofp .pl2 input:checked + label::before { background:var(--rc); border-color:var(--rc); box-shadow:inset 0 0 0 2px #0b1426; }
.ofp .pl2 input:focus-visible + label { outline:2px solid var(--cyan); }
.ofp .pl2 label.hide { display:none; }
.ofp .rcv { text-align:center; } .ofp .rcv svg { width:130px; height:130px; } .ofp .rcv .arc { fill:none; stroke-width:11; stroke-linecap:round; transform:rotate(-90deg); transform-origin:50% 50%; transition:stroke-dasharray .6s var(--ease); animation:none; }
.ofp .rcv h4 { margin:8px 0 2px; color:#fff; font-size:18px; } .ofp .rcv small { color:var(--sub); font-size:12.5px; }
.ofp .ul { display:grid; gap:10px; } .ofp .ul div { display:flex; align-items:center; gap:10px; font-size:13px; color:#fff; } .ofp .ul .av { width:32px; height:32px; font-size:12px; box-shadow:none; }
.ofp .tips { margin:0; padding:0; list-style:none; display:grid; gap:9px; } .ofp .tips li { display:flex; gap:10px; font-size:12.5px; color:var(--sub); line-height:1.5; } .ofp .tips i { color:var(--cyan); margin-top:2px; }
.ofp .fc[readonly] { opacity:.65; cursor:not-allowed; border-style:dashed; }
</style>

<div class="ofp" id="ofp">
    <div class="bgl"><div class="orb o1"></div><div class="orb o2"></div></div>

    <div class="hero rv">
        <div class="who">
            <span class="lg"></span>
            <div>
                <div class="eyebrow"><i></i> Administration</div>
                <h1>{{ $edit ? 'Edit role' : 'Add a role' }} <em>{{ $edit ? RS::label($role->name) : '' }}</em></h1>
                <div class="meta"><span>Choose exactly what members of this role are allowed to do.</span></div>
            </div>
        </div>
        <div class="actions"><a class="btn2" href="{{ route('roles.index') }}"><i class="fe fe-arrow-left"></i> All roles</a></div>
    </div>

    @if($errors->any())<div class="err"><b><i class="fe fe-alert-circle"></i> Please fix the following:</b><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

    <form method="POST" action="{{ $edit ? route('roles.update', $role->id) : route('roles.store') }}" id="rForm" novalidate>
        @csrf
        @if($edit) @method('PUT') @endif
        <div class="grid">
            <div class="main">
                <div class="panel rv">
                    <div class="ph num"><span class="n">1</span><div><h3>Role name</h3><small>Lower-case with underscores, e.g. field_engineer</small></div></div>
                    <div class="f {{ $errors->has('name') ? 'has-err' : '' }}"><label class="lb" for="name"><i class="fe fe-shield"></i> Name <b>*</b></label>
                        <input class="fc" type="text" name="name" id="name" value="{{ $name }}" placeholder="e.g. field_engineer" required maxlength="100" {{ $locked ? 'readonly' : '' }} autocomplete="off">
                        @if($locked)<span class="hintt">The admin role keeps its name — the system relies on it.</span>@endif
                        @error('name')<span class="em">{{ $message }}</span>@enderror</div>
                </div>

                <div class="panel rv" style="--d:1">
                    <div class="ph num"><span class="n">2</span><div><h3>Permissions</h3><small>Tick what this role can do</small></div>
                        <span class="srch" style="margin-left:auto"><i class="fe fe-search"></i><input type="text" id="pq" placeholder="Find…" autocomplete="off"></span></div>
                    @foreach($groups as $k => $l)
                        @if(isset($grouped[$k]))
                            <div class="pg" data-g="{{ $k }}">
                                <div class="gh"><b>{{ $l }}</b><span>{{ $grouped[$k]->count() }}</span><button type="button" data-all="{{ $k }}">Select all</button></div>
                                <div class="pl2" style="--rc:#22d3ee">
                                    @foreach($grouped[$k] as $p)
                                        <input type="checkbox" name="permissions[]" id="p_{{ $p->id }}" value="{{ $p->name }}" data-g="{{ $k }}" @checked($have->contains($p->name))>
                                        <label for="p_{{ $p->id }}" data-n="{{ str_replace('_', ' ', $p->name) }}">{{ str_replace('_', ' ', $p->name) }}</label>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                    @error('permissions')<span class="em">{{ $message }}</span>@enderror
                </div>

                <div class="bar rv" style="--d:2">
                    <button type="submit" class="btn2 p"><i class="fe fe-save"></i> {{ $edit ? 'Save role' : 'Create role' }}</button>
                    <a href="{{ route('roles.index') }}" class="btn2"><i class="fe fe-x"></i> Cancel</a>
                    <span class="sp"></span><span class="hint">Fields marked * are required</span>
                </div>
            </div>

            <aside class="side">
                <div class="panel rcv rv">
                    <svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="40" fill="none" stroke="rgba(255,255,255,.08)" stroke-width="11"/><circle class="arc" id="arc" cx="50" cy="50" r="40" pathLength="100" stroke="#22d3ee" style="stroke-dasharray:0 100"/><text id="cnt" x="50" y="57" text-anchor="middle" font-family="Space Grotesk" font-weight="700" font-size="24" fill="#fff">0</text></svg>
                    <h4 id="rn">{{ $name ?: 'new_role' }}</h4><small id="rs">0 of {{ $permissions->count() }} permissions</small>
                </div>
                @if($edit)
                <div class="panel rv" style="--d:1">
                    <div class="ph" style="margin-bottom:12px"><h3 style="font-size:15px">Members <span class="sub" style="margin-left:6px">{{ $role->users->count() }}</span></h3></div>
                    <div class="ul">@forelse($role->users as $u)<div><span class="av" style="--a:{{ RS::avatar($u->id) }}">{{ RS::initials($u->name) }}</span><a href="{{ route('users.show', $u->id) }}" style="color:#fff">{{ $u->name }}</a></div>@empty<div style="color:var(--mute)">Nobody holds this role yet.</div>@endforelse</div>
                </div>
                @endif
                <div class="panel rv" style="--d:2">
                    <div class="ph" style="margin-bottom:10px"><h3 style="font-size:15px">Good to know</h3></div>
                    <ul class="tips">
                        <li><i class="fe fe-check-circle"></i> <span>Changes apply to every member of the role right away.</span></li>
                        <li><i class="fe fe-check-circle"></i> <span>Prefer small, focused roles and give a user several of them.</span></li>
                    </ul>
                </div>
            </aside>
        </div>
    </form>
</div>

<script>
(function () {
    var $ = function (id) { return document.getElementById(id); }, TOTAL = {{ $permissions->count() }};
    var boxes = [].slice.call(document.querySelectorAll('input[name="permissions[]"]'));
    function upd() {
        var n = boxes.filter(function (b) { return b.checked; }).length, pc = TOTAL ? Math.round(n / TOTAL * 100) : 0;
        $('cnt').textContent = n; $('arc').style.strokeDasharray = pc + ' 100'; $('arc').setAttribute('stroke', pc > 80 ? '#f87171' : pc > 40 ? '#fbbf24' : '#22d3ee');
        $('rs').textContent = n + ' of ' + TOTAL + ' permissions'; $('rn').textContent = $('name').value.trim() || 'new_role';
        document.querySelectorAll('[data-all]').forEach(function (b) { var g = boxes.filter(function (x) { return x.dataset.g === b.dataset.all; }); b.textContent = g.every(function (x) { return x.checked; }) ? 'Clear all' : 'Select all'; });
    }
    document.querySelectorAll('[data-all]').forEach(function (b) { b.addEventListener('click', function () { var g = boxes.filter(function (x) { return x.dataset.g === b.dataset.all && !x.nextElementSibling.classList.contains('hide'); }), all = g.every(function (x) { return x.checked; }); g.forEach(function (x) { x.checked = !all; }); upd(); }); });
    boxes.forEach(function (b) { b.addEventListener('change', upd); });
    $('name').addEventListener('input', function () { this.value = this.value.toLowerCase().replace(/[^a-z0-9_ ]/g, '').replace(/\s+/g, '_'); upd(); });
    $('pq').addEventListener('input', function () { var q = this.value.toLowerCase(); document.querySelectorAll('.pl2 label').forEach(function (l) { l.classList.toggle('hide', !!q && l.dataset.n.indexOf(q) === -1); }); });
    $('rForm').addEventListener('submit', function (e) { var el = $('name'), bad = el.value.trim() === ''; el.classList.toggle('bad', bad); if (bad) { e.preventDefault(); el.focus(); } });
    var io = new IntersectionObserver(function (es) { es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } }); }, { threshold: .05 });
    document.querySelectorAll('#ofp .rv').forEach(function (el) { io.observe(el); });
    upd();
})();
</script>
