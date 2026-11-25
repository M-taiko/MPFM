{{-- Shared create/edit form for test vessels. Vars: $oilFields, $testVessel (nullable) --}}
@php
    $edit = isset($testVessel) && $testVessel && $testVessel->exists;
    $val = fn ($k, $d = '') => old($k, $edit ? ($testVessel->{$k} ?? $d) : $d);
    $stSel = $val('status', 'active');
@endphp
<link rel="stylesheet" href="{{ asset('assets/css/ofpage.css') }}">
<style>
.ofp .grid { display:grid; grid-template-columns:1fr 400px; gap:20px; margin-top:20px; align-items:start; }
.ofp .side { position:sticky; top:84px; display:grid; gap:16px; }
.ofp .panel { margin-bottom:0; }
.ofp .main > .panel { margin-bottom:16px; }
.ofp .ph .n { width:32px; height:32px; border-radius:50%; background:var(--grad); color:#04111f; font:700 14px 'Space Grotesk'; display:grid; place-items:center; flex:none; }
.ofp .ph.num { justify-content:flex-start; gap:12px; }
.ofp .ph.num small { display:block; color:var(--mute); font-size:12px; font-weight:500; margin-top:1px; }
.ofp .row { display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:16px; }
.ofp .f label.lb { display:flex; align-items:center; gap:7px; font-size:10.5px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--mute); margin-bottom:7px; }
.ofp .f label.lb b { color:var(--red); } .ofp .f label.lb i { color:var(--cyan); font-size:13px; }
.ofp .fc { width:100%; height:46px; padding:0 14px; border-radius:14px; border:1px solid var(--line); background:rgba(255,255,255,.06); color:#fff; font:500 14px 'Inter',sans-serif; transition:border-color .25s, box-shadow .25s, background .25s; outline:none; appearance:none; -webkit-appearance:none; }
.ofp textarea.fc { height:auto; padding:12px 14px; resize:vertical; line-height:1.5; }
.ofp .fc:focus { border-color:var(--cyan); box-shadow:0 0 0 4px rgba(34,211,238,.14); background:rgba(255,255,255,.09); }
.ofp .fc::placeholder { color:#52627d; }
.ofp select.fc { padding-right:36px; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' fill='none' stroke='%2322d3ee' stroke-width='2'%3E%3Cpath d='M1 1l5 5 5-5'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 14px center; }
.ofp select.fc option { background:#0b1426; color:#fff; }
.ofp .f.has-err .fc, .ofp .fc.bad { border-color:var(--red); }
.ofp .em { color:#fca5a5; font-size:12px; margin-top:5px; display:block; }
.ofp .err { margin-top:18px; padding:14px 18px; border-radius:16px; border:1px solid rgba(248,113,113,.45); background:rgba(248,113,113,.08); color:#fecaca; font-size:13px; }
.ofp .err ul { margin:8px 0 0; padding-left:18px; }
.ofp .tiles { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; }
.ofp .tiles input { position:absolute; opacity:0; pointer-events:none; }
.ofp .tiles label { margin:0; cursor:pointer; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:6px; height:92px; border-radius:16px; border:1px solid var(--line); background:rgba(255,255,255,.05); color:var(--sub); font-size:13px; font-weight:700; transition:all .3s var(--ease); text-align:center; }
.ofp .tiles label i { font-size:20px; }
.ofp .tiles label small { font-size:10.5px; font-weight:500; color:var(--mute); }
.ofp .tiles label:hover { transform:translateY(-2px); border-color:var(--cyan); }
.ofp .tiles input:checked + label { color:#fff; border-color:var(--tc); background:color-mix(in srgb, var(--tc) 14%, transparent); box-shadow:0 0 22px color-mix(in srgb, var(--tc) 28%, transparent); }
.ofp .tiles input:focus-visible + label { outline:2px solid var(--cyan); }
.ofp .bar { display:flex; gap:12px; flex-wrap:wrap; align-items:center; padding:18px 22px; border-radius:22px; border:1px solid var(--line); background:rgba(255,255,255,.04); }
.ofp .bar .sp { flex:1; } .ofp .bar .hint { font-size:12px; color:var(--mute); }
.ofp .live svg { width:100%; height:auto; display:block; } .ofp .live text { font-family:'Inter',sans-serif; }
.ofp .pvflow { fill:none; stroke-width:2; stroke-linecap:round; stroke-dasharray:3 8; animation:pvDash 1.1s linear infinite; }
@keyframes pvDash { to { stroke-dashoffset:-22; } }
.ofp .pvwave { animation:pvWave 3s linear infinite; } @keyframes pvWave { to { transform:translateX(-30px); } }
.ofp .live[data-st="inactive"] svg .pvdyn { display:none; } .ofp .live[data-st="maintenance"] svg .pvdyn { display:none; }
.ofp .live[data-st="inactive"] svg { filter:grayscale(.9) brightness(.8); }
.ofp .pvwr { display:none; animation:pvWr 2.4s ease-in-out infinite; transform-origin:208px 62px; } @keyframes pvWr { 50% { transform:rotate(-18deg); } }
.ofp .live[data-st="maintenance"] .pvwr { display:block; }
.ofp .pvcard { margin-top:10px; padding:14px 16px; border-radius:16px; border:1px solid var(--line); background:var(--glass); }
.ofp .pvcard b { display:block; font-size:16px; color:#fff; } .ofp .pvcard small { color:var(--sub); font-size:12px; }
.ofp .pvcard .chip { margin-top:8px; }
.ofp .tips { margin:0; padding:0; list-style:none; display:grid; gap:9px; } .ofp .tips li { display:flex; gap:10px; font-size:12.5px; color:var(--sub); line-height:1.5; } .ofp .tips i { color:var(--cyan); margin-top:2px; }
@media (max-width:1200px) { .ofp .grid { grid-template-columns:1fr; } .ofp .side { position:static; grid-template-columns:1fr 1fr; } }
@media (max-width:760px) { .ofp .side { grid-template-columns:1fr; } .ofp .tiles { grid-template-columns:1fr; } }
</style>

<div class="ofp" id="ofp">
    <div class="bgl"><div class="orb o1"></div><div class="orb o2"></div></div>

    <div class="hero rv">
        <div class="who">
            <span class="lg"></span>
            <div>
                <div class="eyebrow"><i></i> Well Testing · Vessel</div>
                <h1>{{ $edit ? 'Edit' : 'Add a' }} <em>{{ $edit ? $testVessel->name : 'vessel' }}</em></h1>
                <div class="meta"><span>{{ $edit ? 'Update the vessel details — the drawing follows what you type.' : 'Register a separator for well tests — the drawing follows what you type.' }}</span></div>
            </div>
        </div>
        <div class="actions"><a class="btn2" href="{{ route('test_vessels.index') }}"><i class="fe fe-arrow-left"></i> Back to vessels</a></div>
    </div>

    @if($errors->any())
        <div class="err"><b><i class="fe fe-alert-circle"></i> Please fix the following:</b><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <form method="POST" action="{{ $edit ? route('test_vessels.update', $testVessel) : route('test_vessels.store') }}" id="vForm" novalidate>
        @csrf
        @if($edit) @method('PUT') @endif
        <div class="grid">
            <div class="main">
                <div class="panel rv">
                    <div class="ph num"><span class="n">1</span><div><h3 style="font-size:16px;font-weight:800;margin:0">Identity</h3><small>How the vessel is named and found</small></div></div>
                    <div class="row">
                        <div class="f {{ $errors->has('name') ? 'has-err' : '' }}"><label class="lb" for="name"><i class="fe fe-anchor"></i> Vessel name <b>*</b></label>
                            <input class="fc" type="text" name="name" id="name" value="{{ $val('name') }}" placeholder="e.g. Swissco" required autofocus maxlength="255">
                            @error('name')<span class="em">{{ $message }}</span>@enderror</div>
                        <div class="f {{ $errors->has('code') ? 'has-err' : '' }}"><label class="lb" for="code"><i class="fe fe-hash"></i> Vessel code <b>*</b></label>
                            <input class="fc" type="text" name="code" id="code" value="{{ $val('code') }}" placeholder="e.g. TV-101" required maxlength="255">
                            @error('code')<span class="em">{{ $message }}</span>@enderror</div>
                        <div class="f {{ $errors->has('oil_field_id') ? 'has-err' : '' }}"><label class="lb" for="oil_field_id"><i class="fe fe-map-pin"></i> Oil field</label>
                            <select class="fc" name="oil_field_id" id="oil_field_id">
                                <option value="">No oil field</option>
                                @foreach($oilFields as $f)<option value="{{ $f->id }}" @selected($val('oil_field_id') == $f->id)>{{ $f->name }}</option>@endforeach
                            </select>
                            @error('oil_field_id')<span class="em">{{ $message }}</span>@enderror</div>
                    </div>
                </div>

                <div class="panel rv" style="--d:1">
                    <div class="ph num"><span class="n">2</span><div><h3 style="font-size:16px;font-weight:800;margin:0">Status</h3><small>Only active vessels can be picked for new tests</small></div></div>
                    <div class="tiles">
                        <input type="radio" name="status" id="st1" value="active" @checked($stSel === 'active')><label for="st1" style="--tc:#34d399"><i class="fe fe-play-circle" style="color:#34d399"></i>Active<small>Ready for tests</small></label>
                        <input type="radio" name="status" id="st2" value="maintenance" @checked($stSel === 'maintenance')><label for="st2" style="--tc:#fbbf24"><i class="fe fe-settings" style="color:#fbbf24"></i>Maintenance<small>Temporarily out</small></label>
                        <input type="radio" name="status" id="st3" value="inactive" @checked($stSel === 'inactive')><label for="st3" style="--tc:#94a3b8"><i class="fe fe-pause-circle" style="color:#94a3b8"></i>Inactive<small>Not in use</small></label>
                    </div>
                    @error('status')<span class="em">{{ $message }}</span>@enderror
                </div>

                <div class="panel rv" style="--d:2">
                    <div class="ph num"><span class="n">3</span><div><h3 style="font-size:16px;font-weight:800;margin:0">Notes</h3><small>Optional — capacity, location, calibration…</small></div></div>
                    <div class="f {{ $errors->has('description') ? 'has-err' : '' }}"><label class="lb" for="description"><i class="fe fe-edit-3"></i> Description</label>
                        <textarea class="fc" name="description" id="description" rows="4" placeholder="Anything worth knowing about this vessel…">{{ $val('description') }}</textarea>
                        @error('description')<span class="em">{{ $message }}</span>@enderror</div>
                </div>

                <div class="bar rv" style="--d:3">
                    <button type="submit" class="btn2 p"><i class="fe fe-save"></i> {{ $edit ? 'Update vessel' : 'Create vessel' }}</button>
                    <a href="{{ route('test_vessels.index') }}" class="btn2"><i class="fe fe-x"></i> Cancel</a>
                    <span class="sp"></span><span class="hint">Fields marked * are required</span>
                </div>
            </div>

            <aside class="side">
                <div class="panel live rv" id="pv" data-st="{{ $stSel }}">
                    <div class="ph" style="margin-bottom:4px"><h3 style="font-size:16px;font-weight:800;margin:0">Live preview</h3></div>
                    <svg viewBox="0 0 260 150" aria-label="Vessel preview">
                        <defs>
                            <linearGradient id="pvShell" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#a8b6cb"/><stop offset=".16" stop-color="#f1f5f9"/><stop offset=".5" stop-color="#7c8aa1"/><stop offset="1" stop-color="#1b2433"/></linearGradient>
                            <linearGradient id="pvOil" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#f59e0b"/><stop offset="1" stop-color="#78350f"/></linearGradient>
                            <linearGradient id="pvWat" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#0ea5e9"/><stop offset="1" stop-color="#0c4a6e"/></linearGradient>
                            <linearGradient id="pvCyl" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#fff" stop-opacity=".22"/><stop offset=".35" stop-color="#fff" stop-opacity=".02"/><stop offset="1" stop-color="#000" stop-opacity=".45"/></linearGradient>
                            <clipPath id="pvClip"><path d="M62,44 H182 A18,30 0 0 1 182,104 H62 A18,30 0 0 1 62,44 Z"/></clipPath>
                            <path id="pvIn" d="M6,60 H54 V48"/><path id="pvGas" d="M166,44 V22 H256"/><path id="pvOut" d="M166,104 V130 H256"/>
                        </defs>
                        <g fill="none"><use href="#pvIn" stroke="#0b1220" stroke-width="10"/><use href="#pvIn" stroke="#64748b" stroke-width="7"/><use href="#pvGas" stroke="#0b1220" stroke-width="10"/><use href="#pvGas" stroke="#64748b" stroke-width="7"/><use href="#pvOut" stroke="#0b1220" stroke-width="10"/><use href="#pvOut" stroke="#64748b" stroke-width="7"/></g>
                        <rect x="82" y="104" width="26" height="26" fill="#334155"/><rect x="140" y="104" width="26" height="26" fill="#334155"/><rect x="66" y="128" width="116" height="6" rx="2" fill="#475569"/>
                        <path d="M58,40 H186 A22,34 0 0 1 186,108 H58 A22,34 0 0 1 58,40 Z" fill="url(#pvShell)" stroke="#0b1220" stroke-width="1.5"/>
                        <g clip-path="url(#pvClip)">
                            <rect x="36" y="40" width="180" height="70" fill="#04101f"/><rect x="36" y="40" width="180" height="70" fill="#22d3ee" opacity=".08"/>
                            <rect x="36" y="62" width="130" height="50" fill="url(#pvOil)"/><rect x="36" y="88" width="130" height="24" fill="url(#pvWat)"/>
                            <rect x="166" y="62" width="3" height="50" fill="#cbd5e1"/><rect x="169" y="78" width="50" height="34" fill="url(#pvOil)"/>
                            <g class="pvdyn"><g class="pvwave"><path d="M30,62 q7,-3 14,0 t14,0 t14,0 t14,0 t14,0 t14,0 t14,0 t14,0 t14,0 t14,0 t14,0 t14,0 t14,0" fill="none" stroke="#fde68a" stroke-opacity=".8" stroke-width="1.2"/></g></g>
                            <rect x="36" y="40" width="180" height="70" fill="url(#pvCyl)"/>
                        </g>
                        <g class="pvwr" fill="none" stroke="#fbbf24" stroke-width="3.5" stroke-linecap="round"><path d="M196,76 l18,-18"/><circle cx="218" cy="54" r="8"/></g>
                        <g class="pvdyn">
                            @foreach([0, .9, 1.8, 2.7] as $b)
                                <circle r="2.8" fill="#fde68a"><animateMotion dur="2.8s" begin="{{ $b }}s" repeatCount="indefinite"><mpath href="#pvIn"/></animateMotion></circle>
                                <circle r="2.4" fill="#a5f3fc"><animateMotion dur="2.8s" begin="{{ $b }}s" repeatCount="indefinite"><mpath href="#pvGas"/></animateMotion></circle>
                                <circle r="2.8" fill="#f59e0b"><animateMotion dur="3s" begin="{{ $b }}s" repeatCount="indefinite"><mpath href="#pvOut"/></animateMotion></circle>
                            @endforeach
                        </g>
                        <text id="pvCode" x="122" y="82" text-anchor="middle" font-size="10" font-weight="800" letter-spacing="2" fill="#fff" opacity=".85">{{ strtoupper($val('code', 'CODE')) ?: 'CODE' }}</text>
                    </svg>
                    <div class="pvcard">
                        <b id="pvName">{{ $val('name') ?: 'Vessel name' }}</b>
                        <small id="pvField">{{ $edit && $testVessel->oilField ? $testVessel->oilField->name : 'No oil field' }}</small><br>
                        <span class="chip" id="pvChip">{{ ucfirst($stSel) }}</span>
                    </div>
                </div>
                <div class="panel rv" style="--d:1">
                    <div class="ph" style="margin-bottom:10px"><h3 style="font-size:15px;font-weight:800;margin:0">Good to know</h3></div>
                    <ul class="tips">
                        <li><i class="fe fe-check-circle"></i> <span>The code must be unique — it is what operators see on test requests.</span></li>
                        <li><i class="fe fe-check-circle"></i> <span>Put a vessel in <b style="color:#fde68a">Maintenance</b> to stop it being chosen for new tests without losing its history.</span></li>
                    </ul>
                </div>
            </aside>
        </div>
    </form>
</div>

<script>
(function () {
    var $ = function (id) { return document.getElementById(id); };
    var pv = $('pv'), chip = $('pvChip');
    var cls = { active: 'ok', maintenance: 'warn', inactive: '' };
    function upd() {
        $('pvName').textContent = $('name').value.trim() || 'Vessel name';
        var c = $('code').value.trim().toUpperCase().slice(0, 10); $('pvCode').textContent = c || 'CODE';
        var o = $('oil_field_id'); $('pvField').textContent = o.value ? o.options[o.selectedIndex].text : 'No oil field';
        var st = (document.querySelector('input[name=status]:checked') || {}).value || 'active';
        pv.dataset.st = st; chip.textContent = st.charAt(0).toUpperCase() + st.slice(1); chip.className = 'chip ' + (cls[st] || '');
    }
    ['name', 'code', 'oil_field_id'].forEach(function (id) { $(id).addEventListener('input', upd); $(id).addEventListener('change', upd); });
    document.querySelectorAll('input[name=status]').forEach(function (r) { r.addEventListener('change', upd); });
    upd();
    $('vForm').addEventListener('submit', function (e) {
        var bad = false;
        ['name', 'code'].forEach(function (id) { var el = $(id), empty = !el.value.trim(); el.classList.toggle('bad', empty); if (empty && !bad) { bad = true; el.focus(); } });
        if (bad) e.preventDefault();
    });
    var io = new IntersectionObserver(function (es) { es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } }); }, { threshold: .05 });
    document.querySelectorAll('#ofp .rv').forEach(function (el) { io.observe(el); });
    if (window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches) document.querySelectorAll('#ofp svg').forEach(function (s) { s.pauseAnimations && s.pauseAnimations(); });
})();
</script>
