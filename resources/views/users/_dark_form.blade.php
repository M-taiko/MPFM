{{-- Shared create/edit form for users. Vars: $departments, $roles (with permissions), $user (nullable), $userRoles (edit) --}}
@use('App\Support\RoleStyle', 'RS')
@php
    $edit = isset($user) && $user && $user->exists;
    $val = fn ($k, $d = '') => old($k, $edit ? ($user->{$k} ?? $d) : $d);
    $sel = collect(old('roles', $edit ? ($userRoles ?? []) : []));
    $self = $edit && $user->id === auth()->id();
    $rolesJs = $roles->map(fn ($r) => ['name' => $r->name, 'label' => RS::label($r->name), 'color' => RS::color($r->name), 'perms' => $r->permissions->pluck('name')->values()])->values();
    $totalPerms = \Spatie\Permission\Models\Permission::count();
@endphp
<link rel="stylesheet" href="{{ asset('assets/css/ofpage.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/offorms.css') }}">
<style>
.ofp .rtiles { display:grid; grid-template-columns:repeat(auto-fill,minmax(220px,1fr)); gap:10px; }
.ofp .rtiles input { position:absolute; opacity:0; pointer-events:none; }
.ofp .rtiles label { margin:0; cursor:pointer; display:block; padding:13px 15px; border-radius:16px; border:1px solid var(--line); background:rgba(255,255,255,.04); transition:all .3s var(--ease); position:relative; }
.ofp .rtiles label b { display:flex; align-items:center; gap:8px; font-size:13.5px; color:#fff; } .ofp .rtiles label b i { width:10px; height:10px; border-radius:50%; background:var(--rc); }
.ofp .rtiles label small { display:block; margin-top:5px; color:var(--mute); font-size:11.5px; line-height:1.4; }
.ofp .rtiles label:hover { transform:translateY(-2px); border-color:var(--rc); }
.ofp .rtiles input:checked + label { border-color:var(--rc); background:color-mix(in srgb, var(--rc) 13%, transparent); box-shadow:0 0 22px color-mix(in srgb, var(--rc) 25%, transparent); }
.ofp .rtiles input:checked + label::after { content:"\2713"; position:absolute; right:12px; top:11px; width:20px; height:20px; border-radius:50%; background:var(--rc); color:#04111f; font-size:12px; font-weight:900; display:grid; place-items:center; }
.ofp .rtiles input:focus-visible + label { outline:2px solid var(--cyan); }
.ofp .rtiles input:disabled + label { opacity:.7; cursor:not-allowed; }
.ofp .pv { text-align:center; }
.ofp .pv .av { width:84px; height:84px; font-size:28px; margin:6px auto 12px; }
.ofp .pv h4 { margin:0; color:#fff; font-size:18px; font-weight:800; } .ofp .pv p { color:var(--sub); font-size:13px; margin:4px 0 12px; word-break:break-all; }
.ofp .pv .chips { display:flex; gap:6px; flex-wrap:wrap; justify-content:center; min-height:26px; }
.ofp .acc { display:flex; align-items:center; gap:16px; margin-top:16px; padding-top:16px; border-top:1px solid var(--line); text-align:left; }
.ofp .acc svg { width:70px; height:70px; flex:none; } .ofp .acc .arc { fill:none; stroke-width:9; stroke-linecap:round; transform:rotate(-90deg); transform-origin:50% 50%; transition:stroke-dasharray .6s var(--ease); animation:none; }
.ofp .acc b { display:block; font-size:14px; color:#fff; } .ofp .acc small { color:var(--mute); font-size:12px; line-height:1.5; display:block; margin-top:2px; }
.ofp .tips { margin:0; padding:0; list-style:none; display:grid; gap:9px; } .ofp .tips li { display:flex; gap:10px; font-size:12.5px; color:var(--sub); line-height:1.5; } .ofp .tips i { color:var(--cyan); margin-top:2px; }
.ofp .lockn { margin-top:12px; padding:10px 14px; border-radius:12px; border:1px solid rgba(251,191,36,.35); background:rgba(251,191,36,.07); color:#fde68a; font-size:12.5px; }
</style>

<div class="ofp" id="ofp">
    <div class="bgl"><div class="orb o1"></div><div class="orb o2"></div></div>

    <div class="hero rv">
        <div class="who">
            <span class="lg"></span>
            <div>
                <div class="eyebrow"><i></i> Administration</div>
                <h1>{{ $edit ? 'Edit' : 'Add a' }} <em>{{ $edit ? $user->name : 'user' }}</em></h1>
                <div class="meta"><span>{{ $edit ? 'Change the account details or the roles — the preview shows what the user will be able to do.' : 'Create an account and give it the roles it needs — the preview shows the resulting access.' }}</span></div>
            </div>
        </div>
        <div class="actions"><a class="btn2" href="{{ $edit ? route('users.show', $user->id) : route('users.index') }}"><i class="fe fe-arrow-left"></i> {{ $edit ? 'Back to profile' : 'All users' }}</a></div>
    </div>

    @if($errors->any())<div class="err"><b><i class="fe fe-alert-circle"></i> Please fix the following:</b><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

    <form method="POST" action="{{ $edit ? route('users.update', $user->id) : route('users.store') }}" id="uForm" novalidate autocomplete="off">
        @csrf
        @if($edit) @method('PUT') @endif
        <div class="grid">
            <div class="main">
                <div class="panel rv">
                    <div class="ph num"><span class="n">1</span><div><h3>Account</h3><small>How the user signs in</small></div></div>
                    <div class="row">
                        <div class="f {{ $errors->has('name') ? 'has-err' : '' }}"><label class="lb" for="name"><i class="fe fe-user"></i> Full name <b>*</b></label>
                            <input class="fc" type="text" name="name" id="name" value="{{ $val('name') }}" placeholder="e.g. Ahmed Hassan" required maxlength="255">@error('name')<span class="em">{{ $message }}</span>@enderror</div>
                        <div class="f {{ $errors->has('email') ? 'has-err' : '' }}"><label class="lb" for="email"><i class="fe fe-mail"></i> Email <b>*</b></label>
                            <input class="fc" type="email" name="email" id="email" value="{{ $val('email') }}" placeholder="name@company.com" required maxlength="255">@error('email')<span class="em">{{ $message }}</span>@enderror</div>
                        <div class="f {{ $errors->has('password') ? 'has-err' : '' }}"><label class="lb" for="password"><i class="fe fe-lock"></i> {{ $edit ? 'New password' : 'Password' }} @unless($edit)<b>*</b>@endunless</label>
                            <div class="pw"><input class="fc" type="password" name="password" id="password" placeholder="{{ $edit ? 'Leave empty to keep the current one' : 'At least 8 characters' }}" {{ $edit ? '' : 'required' }} minlength="8" autocomplete="new-password"><button type="button" id="pwShow" aria-label="Show password"><i class="fe fe-eye"></i></button></div>
                            <div class="meter"><i id="pwBar"></i></div><span class="hintt" id="pwHint">{{ $edit ? 'Only fill this in to change the password.' : 'Use letters, numbers and a symbol for a strong password.' }}</span>
                            @error('password')<span class="em">{{ $message }}</span>@enderror</div>
                        <div class="f"><label class="lb" for="password_confirmation"><i class="fe fe-check-circle"></i> Confirm password</label>
                            <input class="fc" type="password" name="password_confirmation" id="password_confirmation" placeholder="Repeat the password" autocomplete="new-password"><span class="hintt" id="pwMatch"></span></div>
                    </div>
                </div>

                <div class="panel rv" style="--d:1">
                    <div class="ph num"><span class="n">2</span><div><h3>Department</h3><small>Optional — where the user works</small></div></div>
                    <div class="f {{ $errors->has('department_id') ? 'has-err' : '' }}"><label class="lb" for="department_id"><i class="fe fe-briefcase"></i> Department</label>
                        <select class="fc" name="department_id" id="department_id"><option value="">No department</option>@foreach($departments as $d)<option value="{{ $d->id }}" @selected($val('department_id') == $d->id)>{{ $d->name }}</option>@endforeach</select>@error('department_id')<span class="em">{{ $message }}</span>@enderror</div>
                </div>

                <div class="panel rv" style="--d:2">
                    <div class="ph num"><span class="n">3</span><div><h3>Roles</h3><small>A role is a bundle of permissions — pick one or more</small></div></div>
                    <div class="rtiles">
                        @foreach($roles as $r)
                            @php $lock = $self && $r->name === 'admin' && $sel->contains('admin'); @endphp
                            <input type="checkbox" name="roles[]" id="r_{{ $r->id }}" value="{{ $r->name }}" @checked($sel->contains($r->name) || $lock) @disabled($lock)>
                            <label for="r_{{ $r->id }}" style="--rc:{{ RS::color($r->name) }}"><b><i></i>{{ RS::label($r->name) }}</b><small>{{ $r->permissions->count() }} permission{{ $r->permissions->count() === 1 ? '' : 's' }}@if($r->permissions->count()) · {{ $r->permissions->take(2)->map(fn ($p) => str_replace('_', ' ', $p->name))->implode(', ') }}…@endif</small></label>
                            @if($lock)<input type="hidden" name="roles[]" value="admin">@endif
                        @endforeach
                    </div>
                    @if($self && $sel->contains('admin'))<div class="lockn"><i class="fe fe-lock"></i> You can't remove your own admin role — that would lock you out.</div>@endif
                    @error('roles')<span class="em">{{ $message }}</span>@enderror
                </div>

                <div class="bar rv" style="--d:3">
                    <button type="submit" class="btn2 p"><i class="fe fe-save"></i> {{ $edit ? 'Save changes' : 'Create user' }}</button>
                    <a href="{{ $edit ? route('users.show', $user->id) : route('users.index') }}" class="btn2"><i class="fe fe-x"></i> Cancel</a>
                    <span class="sp"></span><span class="hint">Fields marked * are required</span>
                </div>
            </div>

            <aside class="side">
                <div class="panel pv rv">
                    <div class="av" id="pvAv">U</div>
                    <h4 id="pvName">{{ $val('name') ?: 'New user' }}</h4>
                    <p id="pvEmail">{{ $val('email') ?: 'email@company.com' }}</p>
                    <div class="chips" id="pvRoles"></div>
                    <div class="acc">
                        <svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="40" fill="none" stroke="rgba(255,255,255,.08)" stroke-width="9"/><circle class="arc" id="pvArc" cx="50" cy="50" r="40" pathLength="100" stroke="#22d3ee" style="stroke-dasharray:0 100"/><text id="pvPct" x="50" y="56" text-anchor="middle" font-family="Space Grotesk" font-weight="700" font-size="19" fill="#fff">0</text></svg>
                        <div><b id="pvCount">0 permissions</b><small id="pvSub">Pick a role to see what this user can do.</small></div>
                    </div>
                </div>
                <div class="panel rv" style="--d:1">
                    <div class="ph" style="margin-bottom:10px"><h3 style="font-size:15px">Good to know</h3></div>
                    <ul class="tips">
                        <li><i class="fe fe-check-circle"></i> <span>Permissions come from roles. Give a one-off permission from the user's profile page.</span></li>
                        <li><i class="fe fe-check-circle"></i> <span>The email must be unique — it is the sign-in name.</span></li>
                    </ul>
                </div>
            </aside>
        </div>
    </form>
</div>

<script>
(function () {
    var $ = function (id) { return document.getElementById(id); };
    var ROLES = @json($rolesJs), TOTAL = {{ $totalPerms }}, NEWPW = {{ $edit ? 'false' : 'true' }};
    var grads = ['linear-gradient(135deg,#3b82f6,#22d3ee)', 'linear-gradient(135deg,#f59e0b,#fbbf24)', 'linear-gradient(135deg,#a78bfa,#f472b6)', 'linear-gradient(135deg,#10b981,#34d399)', 'linear-gradient(135deg,#f87171,#fb923c)', 'linear-gradient(135deg,#38bdf8,#818cf8)'];
    function esc(s) { return String(s).replace(/[&<>"]/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]; }); }
    function chosen() { return [].slice.call(document.querySelectorAll('input[name="roles[]"]:checked')).map(function (c) { return c.value; }); }
    function upd() {
        var name = $('name').value.trim(), email = $('email').value.trim();
        $('pvName').textContent = name || 'New user'; $('pvEmail').textContent = email || 'email@company.com';
        var ini = name.split(/\s+/).filter(Boolean).slice(0, 2).map(function (w) { return w[0].toUpperCase(); }).join('') || 'U';
        $('pvAv').textContent = ini; $('pvAv').style.background = grads[(name.length + (email.charCodeAt(0) || 0)) % grads.length];
        var sel = chosen(), perms = {};
        $('pvRoles').innerHTML = sel.map(function (n) { var r = ROLES.filter(function (x) { return x.name === n; })[0]; if (!r) return ''; r.perms.forEach(function (p) { perms[p] = 1; }); return '<span class="rc" style="--rc:' + r.color + '">' + esc(r.label) + '</span>'; }).join('') || '<span class="tg">No role selected</span>';
        var n = Object.keys(perms).length, pc = TOTAL ? Math.round(n / TOTAL * 100) : 0;
        $('pvArc').style.strokeDasharray = pc + ' 100'; $('pvArc').setAttribute('stroke', pc > 80 ? '#f87171' : pc > 40 ? '#fbbf24' : '#22d3ee'); $('pvPct').textContent = n;
        $('pvCount').textContent = n + ' permission' + (n === 1 ? '' : 's');
        $('pvSub').textContent = n ? (pc + '% of all ' + TOTAL + ' permissions in the system.') : 'Pick a role to see what this user can do.';
    }
    function strength(v) { var s = 0; if (v.length >= 8) s++; if (v.length >= 12) s++; if (/[a-z]/.test(v) && /[A-Z]/.test(v)) s++; if (/\d/.test(v)) s++; if (/[^A-Za-z0-9]/.test(v)) s++; return s; }
    function pw() {
        var v = $('password').value, s = strength(v), bar = $('pwBar'), cols = ['#f87171', '#f87171', '#fbbf24', '#34d399', '#22d3ee', '#22d3ee'], labs = ['', 'Too weak', 'Weak', 'Fair', 'Strong', 'Very strong'];
        bar.style.width = v ? (s / 5 * 100) + '%' : '0'; bar.style.background = cols[s];
        if (v) $('pwHint').textContent = labs[s] || 'Too weak';
        var c = $('password_confirmation').value; $('pwMatch').textContent = c ? (c === v ? '✓ Passwords match' : 'Passwords do not match') : ''; $('pwMatch').style.color = c && c !== v ? '#fca5a5' : '#6ee7b7';
    }
    document.querySelectorAll('#uForm input, #uForm select').forEach(function (e) { e.addEventListener('input', function () { upd(); pw(); }); e.addEventListener('change', upd); });
    $('pwShow').addEventListener('click', function () { var t = $('password').type === 'password'; $('password').type = t ? 'text' : 'password'; $('password_confirmation').type = t ? 'text' : 'password'; });
    $('uForm').addEventListener('submit', function (e) {
        var bad = false;
        [['name', $('name').value.trim() === ''], ['email', !/^\S+@\S+\.\S+$/.test($('email').value)]].forEach(function (p) { var el = $(p[0]); el.classList.toggle('bad', p[1]); if (p[1] && !bad) { bad = true; el.focus(); } });
        var v = $('password').value;
        if (NEWPW && v.length < 8) { $('password').classList.add('bad'); if (!bad) { bad = true; $('password').focus(); } }
        if (v && v !== $('password_confirmation').value) { $('password_confirmation').classList.add('bad'); if (!bad) { bad = true; $('password_confirmation').focus(); } }
        if (bad) e.preventDefault();
    });
    var io = new IntersectionObserver(function (es) { es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } }); }, { threshold: .05 });
    document.querySelectorAll('#ofp .rv').forEach(function (el) { io.observe(el); });
    upd(); pw();
})();
</script>
