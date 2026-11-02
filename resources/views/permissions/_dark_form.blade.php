{{-- Shared create/edit form for permissions. Vars: $permission (nullable) --}}
@use('App\Support\RoleStyle', 'RS')
@php
    $edit = isset($permission) && $permission && $permission->exists;
    $name = old('name', $edit ? $permission->name : '');
@endphp
<link rel="stylesheet" href="{{ asset('assets/css/ofpage.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/offorms.css') }}">
<style>
.ofp .verbs { display:flex; gap:8px; flex-wrap:wrap; margin-top:12px; }
.ofp .verbs button { padding:6px 14px; border-radius:50px; border:1px solid var(--line); background:var(--glass); color:var(--sub); font:600 12px 'Inter'; cursor:pointer; } .ofp .verbs button:hover { border-color:var(--cyan); color:#fff; }
.ofp .pvc { text-align:center; } .ofp .pvc .ic { width:64px; height:64px; border-radius:20px; display:grid; place-items:center; margin:4px auto 12px; background:var(--grad); color:#04111f; font-size:26px; box-shadow:0 14px 40px rgba(34,211,238,.3); }
.ofp .pvc h4 { margin:0; color:#fff; font:700 19px 'Space Grotesk'; word-break:break-all; } .ofp .pvc small { color:var(--mute); font-size:12px; display:block; margin-top:4px; }
.ofp .chips { display:flex; gap:7px; flex-wrap:wrap; }
.ofp .tips { margin:0; padding:0; list-style:none; display:grid; gap:9px; } .ofp .tips li { display:flex; gap:10px; font-size:12.5px; color:var(--sub); line-height:1.5; } .ofp .tips i { color:var(--cyan); margin-top:2px; }
</style>

<div class="ofp" id="ofp">
    <div class="bgl"><div class="orb o1"></div><div class="orb o2"></div></div>

    <div class="hero rv">
        <div class="who">
            <span class="lg"></span>
            <div>
                <div class="eyebrow"><i></i> Administration</div>
                <h1>{{ $edit ? 'Edit permission' : 'Add a permission' }} <em>{{ $edit ? str_replace('_', ' ', $permission->name) : '' }}</em></h1>
                <div class="meta"><span>A permission is one thing the system can allow. Give it to roles, not directly to people.</span></div>
            </div>
        </div>
        <div class="actions"><a class="btn2" href="{{ route('permissions.index') }}"><i class="fe fe-arrow-left"></i> All permissions</a></div>
    </div>

    @if($errors->any())<div class="err"><b><i class="fe fe-alert-circle"></i> Please fix the following:</b><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

    <form method="POST" action="{{ $edit ? route('permissions.update', $permission->id) : route('permissions.store') }}" id="pForm" novalidate>
        @csrf
        @if($edit) @method('PUT') @endif
        <div class="grid">
            <div class="main">
                <div class="panel rv">
                    <div class="ph num"><span class="n">1</span><div><h3>Name</h3><small>Use the pattern <b>verb_thing</b> — e.g. view_reports, edit_vessels</small></div></div>
                    <div class="f {{ $errors->has('name') ? 'has-err' : '' }}"><label class="lb" for="name"><i class="fe fe-lock"></i> Permission name <b>*</b></label>
                        <input class="fc" type="text" name="name" id="name" value="{{ $name }}" placeholder="e.g. view_reports" required maxlength="120" autocomplete="off">
                        @error('name')<span class="em">{{ $message }}</span>@enderror</div>
                    <div class="verbs">@foreach(['view', 'create', 'edit', 'delete', 'manage'] as $v)<button type="button" data-v="{{ $v }}">{{ $v }}_…</button>@endforeach</div>
                    @if($edit)<div class="note" style="margin-top:16px"><small>Careful</small>Roles and code check this exact name. Renaming it changes it everywhere it is already used.</div>@endif
                </div>
                <div class="bar rv" style="--d:1">
                    <button type="submit" class="btn2 p"><i class="fe fe-save"></i> {{ $edit ? 'Save permission' : 'Create permission' }}</button>
                    <a href="{{ route('permissions.index') }}" class="btn2"><i class="fe fe-x"></i> Cancel</a>
                    <span class="sp"></span><span class="hint">Fields marked * are required</span>
                </div>
            </div>
            <aside class="side">
                <div class="panel pvc rv">
                    <div class="ic"><i class="fe fe-lock" id="pvIc"></i></div>
                    <h4 id="pvName">{{ $name ?: 'new_permission' }}</h4><small id="pvHuman">{{ $name ? str_replace('_', ' ', $name) : 'new permission' }}</small>
                </div>
                @if($edit)
                <div class="panel rv" style="--d:1">
                    <div class="ph" style="margin-bottom:12px"><h3 style="font-size:15px">Held by <span class="sub" style="margin-left:6px">{{ $permission->roles->count() }} role{{ $permission->roles->count() === 1 ? '' : 's' }}</span></h3></div>
                    <div class="chips">@forelse($permission->roles as $r)<span class="rc" style="--rc:{{ RS::color($r->name) }}">{{ RS::label($r->name) }}</span>@empty<span style="color:var(--mute);font-size:13px">No role holds this permission yet.</span>@endforelse</div>
                </div>
                @endif
                <div class="panel rv" style="--d:2">
                    <div class="ph" style="margin-bottom:10px"><h3 style="font-size:15px">Good to know</h3></div>
                    <ul class="tips">
                        <li><i class="fe fe-check-circle"></i> <span>After creating it, tick it for the right roles in the Roles matrix.</span></li>
                        <li><i class="fe fe-check-circle"></i> <span>The names are lower-case with underscores.</span></li>
                    </ul>
                </div>
            </aside>
        </div>
    </form>
</div>

<script>
(function () {
    var $ = function (id) { return document.getElementById(id); };
    function upd() { var v = $('name').value.trim(); $('pvName').textContent = v || 'new_permission'; $('pvHuman').textContent = v ? v.replace(/_/g, ' ') : 'new permission'; }
    $('name').addEventListener('input', function () { this.value = this.value.toLowerCase().replace(/[^a-z0-9_ ]/g, '').replace(/\s+/g, '_'); upd(); });
    document.querySelectorAll('[data-v]').forEach(function (b) { b.addEventListener('click', function () { var cur = $('name').value.replace(/^(view|create|edit|delete|manage)_?/, ''); $('name').value = b.dataset.v + '_' + cur; $('name').focus(); upd(); }); });
    $('pForm').addEventListener('submit', function (e) { var el = $('name'), bad = el.value.trim() === ''; el.classList.toggle('bad', bad); if (bad) { e.preventDefault(); el.focus(); } });
    var io = new IntersectionObserver(function (es) { es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } }); }, { threshold: .05 });
    document.querySelectorAll('#ofp .rv').forEach(function (el) { io.observe(el); });
    upd();
})();
</script>
