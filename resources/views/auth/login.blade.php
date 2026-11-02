<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign In - Oil Field Management System</title>
    <link rel="icon" href="{{ asset('assets/img/brand/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/oilfield-anim.css') }}">
<style>
*, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
:root {
    --bg:#050a15; --ink:#e8eefc; --sub:#9fb0cf; --mute:#64748b;
    --blue:#3b82f6; --blue2:#2563eb; --cyan:#22d3ee; --amber:#fbbf24; --green:#34d399; --red:#f87171;
    --line:rgba(96,165,250,.2); --glass:rgba(255,255,255,.05);
    --grad:linear-gradient(135deg,#3b82f6,#22d3ee);
    --ease:cubic-bezier(.22,1,.36,1);
}
html, body { min-height:100%; }
body { font-family:'Inter',sans-serif; background:var(--bg); color:var(--ink); line-height:1.6; overflow-x:hidden; -webkit-font-smoothing:antialiased; }
a { text-decoration:none; color:inherit; }

/* ── background ── */
.bg { position:fixed; inset:0; z-index:0; pointer-events:none; overflow:hidden; }
.bg::before { content:""; position:absolute; inset:-50%; background-image:linear-gradient(rgba(59,130,246,.07) 1px,transparent 1px),linear-gradient(90deg,rgba(59,130,246,.07) 1px,transparent 1px); background-size:56px 56px; transform:perspective(700px) rotateX(58deg) translateY(-10%); animation:gridMove 14s linear infinite; mask-image:radial-gradient(ellipse at 50% 40%,#000 10%,transparent 70%); -webkit-mask-image:radial-gradient(ellipse at 50% 40%,#000 10%,transparent 70%); }
@keyframes gridMove { to { background-position:0 56px; } }
.orb { position:absolute; border-radius:50%; filter:blur(90px); opacity:.35; animation:float 16s ease-in-out infinite; }
.orb.o1 { width:520px; height:520px; background:#2563eb; top:-120px; left:-120px; }
.orb.o2 { width:460px; height:460px; background:#0891b2; bottom:-140px; right:-100px; animation-delay:-6s; }
.orb.o3 { width:300px; height:300px; background:#7c3aed; top:40%; left:45%; opacity:.16; animation-delay:-10s; }
@keyframes float { 50% { transform:translate(40px,-50px) scale(1.12); } }
#drops { position:fixed; inset:0; z-index:0; pointer-events:none; }
.cursor-glow { position:fixed; left:0; top:0; width:420px; height:420px; margin:-210px 0 0 -210px; border-radius:50%; background:radial-gradient(circle,rgba(34,211,238,.13),transparent 65%); pointer-events:none; z-index:0; will-change:transform; }

/* ── company logo (cropped to the mark, inside a white badge) ── */
.lg { --h:64px; display:inline-block; flex:none; height:var(--h); aspect-ratio:600/940; border-radius:calc(var(--h) * .14); background:#fff url('/assets/img/brand/logo.png') 50% 50.8%/333.33% auto no-repeat; border:2px solid rgba(255,255,255,.9); box-shadow:0 0 0 1px rgba(59,130,246,.5),0 8px 30px rgba(0,0,0,.45),0 0 26px rgba(34,211,238,.35); position:relative; overflow:hidden; }
.lg::after { content:""; position:absolute; top:0; left:-130%; width:60%; height:100%; background:linear-gradient(90deg,transparent,rgba(34,211,238,.45),transparent); transform:skewX(-20deg); animation:shine 4.5s infinite; }
@keyframes shine { 60%,100% { left:160%; } }

/* ── layout ── */
.shell { position:relative; z-index:1; min-height:100vh; display:grid; grid-template-columns:1.15fr .85fr; }
.stage { position:relative; overflow:hidden; padding:44px 5vw 110px 6vw; display:flex; flex-direction:column; justify-content:center; background:linear-gradient(90deg,rgba(5,10,21,.35),var(--bg) 96%),url('/assets/img/brand/Offshore.jpg') center/cover; }
.stage::after { content:""; position:absolute; inset:0; background:linear-gradient(transparent 55%,var(--bg)); z-index:0; pointer-events:none; }
.stage > *:not(.rig):not(.marquee) { position:relative; z-index:2; }
.stage .rig { position:absolute; right:-2vw; bottom:64px; width:min(32vw,460px); opacity:.85; z-index:1; }
.brand { display:flex; align-items:center; gap:20px; margin-bottom:34px; }
.brand .lg { --h:112px; animation:glowlg 3.2s ease-in-out infinite; }
@keyframes glowlg { 50% { box-shadow:0 0 0 1px rgba(59,130,246,.6),0 10px 40px rgba(0,0,0,.5),0 0 60px rgba(34,211,238,.7); transform:translateY(-4px); } }
.brand .nm { font:800 clamp(16px,1.7vw,24px) 'Space Grotesk',sans-serif; letter-spacing:.08em; text-transform:uppercase; line-height:1.25; }
.brand .nm small { display:block; margin-top:6px; font-size:11px; font-weight:500; letter-spacing:.3em; color:var(--cyan); }
.live { display:inline-flex; align-items:center; gap:10px; font-size:13px; font-weight:600; color:var(--green); margin-bottom:16px; }
.live i { width:9px; height:9px; border-radius:50%; background:var(--green); box-shadow:0 0 0 0 rgba(52,211,153,.7); animation:ping 1.8s infinite; }
@keyframes ping { to { box-shadow:0 0 0 14px rgba(52,211,153,0); } }
h1 { font-size:clamp(34px,4.6vw,66px); font-weight:900; line-height:1.04; letter-spacing:-.03em; max-width:640px; }
h1 em { font-style:normal; background:linear-gradient(120deg,#60a5fa,#22d3ee,#fbbf24,#60a5fa); background-size:250% 100%; -webkit-background-clip:text; background-clip:text; color:transparent; animation:flow 6s linear infinite; }
@keyframes flow { to { background-position:250% 0; } }
.rot { font:600 clamp(16px,1.6vw,21px) 'Space Grotesk',sans-serif; color:var(--sub); margin:12px 0 0; min-height:1.6em; }
.rot b { color:var(--cyan); border-right:2px solid var(--cyan); padding-right:4px; animation:caret .8s steps(1) infinite; }
@keyframes caret { 50% { border-color:transparent; } }
.lead { color:var(--sub); font-size:clamp(14px,1.2vw,17px); max-width:520px; margin-top:14px; }
.feats { display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-top:28px; max-width:560px; }
.feat { display:flex; gap:12px; align-items:flex-start; padding:14px; border-radius:14px; background:var(--glass); border:1px solid var(--line); backdrop-filter:blur(8px); transition:transform .4s var(--ease), border-color .3s; }
.feat:hover { transform:translateY(-4px); border-color:rgba(34,211,238,.5); }
.feat .e { flex:none; width:38px; height:38px; display:grid; place-items:center; font-size:19px; border-radius:10px; background:rgba(59,130,246,.18); transition:transform .4s var(--ease); }
.feat:hover .e { transform:scale(1.2) rotate(-8deg); }
.feat b { display:block; font-size:14px; } .feat span { font-size:12px; color:var(--sub); line-height:1.4; display:block; }
.stats { display:flex; gap:38px; margin-top:30px; flex-wrap:wrap; }
.stats b { display:block; font:700 clamp(26px,2.6vw,36px) 'Space Grotesk',sans-serif; color:#fff; }
.stats b small { color:var(--cyan); font-size:.6em; }
.stats span { font-size:11px; letter-spacing:.12em; text-transform:uppercase; color:var(--sub); }
.marquee { position:absolute; left:0; right:0; bottom:44px; z-index:2; overflow:hidden; border-block:1px solid var(--line); background:rgba(5,10,21,.55); backdrop-filter:blur(6px); mask-image:linear-gradient(90deg,transparent,#000 10%,#000 90%,transparent); -webkit-mask-image:linear-gradient(90deg,transparent,#000 10%,#000 90%,transparent); }
.marquee div { display:flex; gap:48px; width:max-content; padding:10px 0; animation:marq 30s linear infinite; font:600 11px 'Space Grotesk',sans-serif; letter-spacing:.22em; text-transform:uppercase; color:var(--sub); }
.marquee div span::before { content:"◆"; color:var(--cyan); margin-right:48px; }
@keyframes marq { to { transform:translateX(-50%); } }

/* reveal on load */
[data-a] { opacity:0; transform:translateY(40px); animation:rise .9s var(--ease) forwards; animation-delay:calc(var(--d,0) * 110ms + 150ms); }
[data-a="left"] { transform:translateX(-50px); } [data-a="right"] { transform:translateX(50px); } [data-a="zoom"] { transform:scale(.8); }
@keyframes rise { to { opacity:1; transform:none; } }
.w { display:inline-block; opacity:0; transform:translateY(.7em) rotate(4deg); filter:blur(6px); animation:wIn .8s var(--ease) forwards; animation-delay:calc(var(--wi) * 55ms + 350ms); }
@keyframes wIn { to { opacity:1; transform:none; filter:none; } }

/* ── form side ── */
.side { position:relative; display:flex; align-items:center; justify-content:center; padding:40px 4vw; }
.card { position:relative; width:100%; max-width:440px; padding:2px; border-radius:26px; overflow:hidden; box-shadow:0 30px 80px rgba(0,0,0,.55); transform-style:preserve-3d; will-change:transform; }
.card::before { content:""; position:absolute; inset:-60%; background:conic-gradient(from 0deg,transparent 0 60%,var(--cyan) 75%,var(--blue) 90%,transparent); animation:spin 6s linear infinite; }
.card-in { position:relative; border-radius:24px; padding:38px 36px 30px; background:linear-gradient(180deg,#0b1730,#07102a); }
.card-in::after { content:""; position:absolute; inset:0; border-radius:24px; background:radial-gradient(circle at var(--mx,50%) var(--my,0%),rgba(34,211,238,.12),transparent 55%); pointer-events:none; transition:opacity .3s; }
.mhead { display:none; align-items:center; gap:14px; margin-bottom:22px; }
.mhead .lg { --h:72px; }
.title { font-size:30px; font-weight:800; letter-spacing:-.02em; }
.title em { font-style:normal; background:var(--grad); -webkit-background-clip:text; background-clip:text; color:transparent; }
.subtitle { color:var(--sub); font-size:14px; margin:6px 0 26px; }
.field { position:relative; margin-bottom:20px; }
.field input { width:100%; padding:22px 46px 8px 16px; font:500 15px 'Inter',sans-serif; color:#fff; background:rgba(255,255,255,.05); border:1.5px solid var(--line); border-radius:14px; outline:0; transition:border-color .3s, box-shadow .3s, background .3s; }
.field label { position:absolute; left:16px; top:50%; transform:translateY(-50%); font-size:14px; color:var(--mute); pointer-events:none; transition:.25s var(--ease); }
.field input:focus ~ label, .field input:not(:placeholder-shown) ~ label { top:15px; font-size:10.5px; letter-spacing:.1em; text-transform:uppercase; color:var(--cyan); }
.field input:focus { border-color:var(--cyan); background:rgba(34,211,238,.06); box-shadow:0 0 0 4px rgba(34,211,238,.12); }
.field .ico { position:absolute; right:14px; top:50%; transform:translateY(-50%); width:26px; height:26px; display:grid; place-items:center; color:var(--mute); font-size:15px; border:0; background:none; cursor:pointer; transition:color .25s, transform .25s; }
.field .ico:hover { color:var(--cyan); transform:translateY(-50%) scale(1.15); }
.field.err input { border-color:var(--red); background:rgba(248,113,113,.07); animation:shake .45s; }
@keyframes shake { 20%,60% { transform:translateX(-6px); } 40%,80% { transform:translateX(6px); } }
.msg { display:block; margin-top:7px; font-size:12.5px; font-weight:600; color:var(--red); }
.row { display:flex; align-items:center; justify-content:space-between; gap:12px; margin:4px 0 22px; font-size:13px; }
.chk { display:flex; align-items:center; gap:9px; cursor:pointer; user-select:none; color:var(--sub); }
.chk input { position:absolute; opacity:0; }
.chk i { width:20px; height:20px; border-radius:6px; border:1.5px solid var(--line); display:grid; place-items:center; transition:.25s; font-style:normal; font-size:12px; color:transparent; }
.chk input:checked + i { background:var(--grad); border-color:transparent; color:#04111f; transform:scale(1.1); }
.chk input:focus-visible + i { box-shadow:0 0 0 3px rgba(34,211,238,.35); }
.link { color:var(--cyan); font-weight:600; transition:color .2s; } .link:hover { color:#fff; }
.btn { position:relative; width:100%; padding:16px; border:0; border-radius:14px; font:800 15px 'Inter',sans-serif; letter-spacing:.08em; text-transform:uppercase; color:#04111f; background:var(--grad); cursor:pointer; overflow:hidden; box-shadow:0 10px 30px rgba(34,211,238,.28); transition:transform .3s var(--ease), box-shadow .3s; }
.btn::after { content:""; position:absolute; top:0; left:-120%; width:60%; height:100%; background:linear-gradient(90deg,transparent,rgba(255,255,255,.55),transparent); transform:skewX(-20deg); animation:shine 3.4s infinite; }
.btn:hover { transform:translateY(-3px); box-shadow:0 16px 40px rgba(34,211,238,.42); }
.btn .sp { display:none; width:18px; height:18px; border:3px solid rgba(4,17,31,.3); border-top-color:#04111f; border-radius:50%; margin:0 auto; animation:spin .7s linear infinite; }
.btn.loading span { display:none; } .btn.loading .sp { display:block; } .btn.loading { pointer-events:none; opacity:.9; }
.secure { display:flex; align-items:center; justify-content:center; gap:8px; margin-top:16px; font-size:12px; color:var(--green); }
.secure i { width:8px; height:8px; border-radius:50%; background:var(--green); animation:blink 1.4s infinite; }
.help { margin-top:20px; padding-top:18px; border-top:1px dashed var(--line); text-align:center; font-size:12.5px; color:var(--sub); line-height:1.7; }
.foot { position:absolute; left:0; right:0; bottom:16px; text-align:center; font-size:11.5px; color:var(--mute); padding:0 20px; }

@media (max-width:1000px) {
    .shell { grid-template-columns:1fr; }
    .stage { display:none; }
    .side { min-height:100vh; padding:30px 18px 70px; }
    .mhead { display:flex; }
    .card-in { padding:30px 22px 24px; }
}
@media (prefers-reduced-motion:reduce) { *, *::before, *::after { animation-duration:.01ms !important; animation-iteration-count:1 !important; transition-duration:.01ms !important; } [data-a], .w { opacity:1; transform:none; filter:none; } }
</style>
</head>
<body>

<div class="bg"><div class="orb o1"></div><div class="orb o2"></div><div class="orb o3"></div></div>
<canvas id="drops"></canvas>
<div class="cursor-glow" id="glow"></div>

<div class="shell">
    <!-- brand / presentation side -->
    <section class="stage">
        @include('partials.rig')
        <div class="brand" data-a="zoom" style="--d:0">
            <span class="lg" role="img" aria-label="Company logo"></span>
            <div class="nm">Oil Field Management System<small>PETROLEUM WELLS PLATFORM</small></div>
        </div>
        <div class="live" data-a="up" style="--d:1"><i></i> Live operations platform</div>
        <h1 id="ttl">Field operations, <em>unified.</em></h1>
        <div class="rot" data-a="up" style="--d:4">Manage <b id="rot"></b></div>
        <p class="lead" data-a="up" style="--d:5">Multiphase flow testing, task workflows and departmental oversight — on one intelligent platform.</p>
        <div class="feats">
            <div class="feat" data-a="up" style="--d:6"><span class="e">⚡</span><div><b>Real-time monitoring</b><span>Live data tracking and instant alerts</span></div></div>
            <div class="feat" data-a="up" style="--d:7"><span class="e">🔐</span><div><b>Enterprise security</b><span>Role-based access, encrypted sessions</span></div></div>
            <div class="feat" data-a="up" style="--d:8"><span class="e">📊</span><div><b>Advanced analytics</b><span>Reports and KPI tracking</span></div></div>
            <div class="feat" data-a="up" style="--d:9"><span class="e">✓</span><div><b>Workflow management</b><span>Automated routing and approvals</span></div></div>
        </div>
        <div class="stats" data-a="up" style="--d:10">
            <div><b><span data-count="6">0</span><small>+</small></b><span>Departments</span></div>
            <div><b><span data-count="24">0</span><small>/7</small></b><span>Monitoring</span></div>
            <div><b><span data-count="100">0</span><small>%</small></b><span>Audit trail</span></div>
        </div>
        <div class="marquee" aria-hidden="true"><div>
            <span>MPFM Testing</span><span>Wire Line</span><span>Production</span><span>Drilling</span><span>Completion</span><span>Workover</span><span>SLA Tracking</span><span>Audit Trail</span><span>Live KPIs</span><span>Approvals</span>
            <span>MPFM Testing</span><span>Wire Line</span><span>Production</span><span>Drilling</span><span>Completion</span><span>Workover</span><span>SLA Tracking</span><span>Audit Trail</span><span>Live KPIs</span><span>Approvals</span>
        </div></div>
    </section>

    <!-- form side -->
    <main class="side">
        <div class="card" id="card" data-a="right" style="--d:3">
            <div class="card-in">
                <div class="mhead"><span class="lg" role="img" aria-label="Company logo"></span><div style="font:800 15px 'Space Grotesk',sans-serif;letter-spacing:.08em;text-transform:uppercase;line-height:1.3">Oil Field<br>Management System</div></div>
                <h2 class="title">Welcome <em>back</em></h2>
                <p class="subtitle">Sign in to access your dashboard</p>

                <form method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf

                    <div class="field @error('email') err @enderror">
                        <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder=" " required autocomplete="email" autofocus>
                        <label for="email">Email address</label>
                        <span class="ico" aria-hidden="true">✉</span>
                        @error('email')<span class="msg">{{ $message }}</span>@enderror
                    </div>

                    <div class="field @error('password') err @enderror">
                        <input id="password" type="password" name="password" placeholder=" " required autocomplete="current-password">
                        <label for="password">Password</label>
                        <button type="button" class="ico" id="pwToggle" aria-label="Show password">👁</button>
                        @error('password')<span class="msg">{{ $message }}</span>@enderror
                    </div>

                    <div class="row">
                        <label class="chk"><input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}><i>✓</i>Remember me for 30 days</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="link">Forgot password?</a>
                        @endif
                    </div>

                    <button type="submit" class="btn" id="submitBtn"><span>Sign in now</span><div class="sp"></div></button>
                    <div class="secure"><i></i>Encrypted &amp; secure connection</div>

                    <div class="help">
                        Forgot your credentials or need access?<br>
                        <a href="#" class="link">Contact Support</a> or call your system administrator.
                    </div>
                </form>
            </div>
        </div>
        <div class="foot">&copy; {{ date('Y') }} Oil Field Management System · Engineering Precision &bull; Operational Excellence</div>
    </main>
</div>

<script>
(function () {
    var reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;

    // headline word-by-word reveal
    var wi = 0;
    (function split(node) {
        [].slice.call(node.childNodes).forEach(function (n) {
            if (n.nodeType === 3) {
                var f = document.createDocumentFragment();
                n.textContent.split(/(\s+)/).forEach(function (t) {
                    if (!t) return;
                    if (/^\s+$/.test(t)) { f.appendChild(document.createTextNode(' ')); return; }
                    var w = document.createElement('span'); w.className = 'w'; w.style.setProperty('--wi', wi++); w.textContent = t; f.appendChild(w);
                });
                node.replaceChild(f, n);
            } else if (n.nodeType === 1) split(n);
        });
    })(document.getElementById('ttl'));

    // typing rotator
    var rot = document.getElementById('rot'), words = ['wells', 'MPFM tests', 'approvals', 'SLAs', 'production reports', 'every department'], wk = 0, ch = 0, del = false;
    (function type() {
        var w = words[wk]; rot.textContent = w.slice(0, ch);
        if (!del && ch === w.length) { del = true; return setTimeout(type, 1400); }
        if (del && ch === 0) { del = false; wk = (wk + 1) % words.length; }
        ch += del ? -1 : 1; setTimeout(type, del ? 35 : 80);
    })();

    // counters
    document.querySelectorAll('[data-count]').forEach(function (el) {
        var end = +el.dataset.count, t0 = null;
        setTimeout(function () {
            (function tick(t) {
                if (!t0) t0 = t;
                var p = Math.min((t - t0) / 1800, 1);
                el.textContent = Math.round(end * (1 - Math.pow(1 - p, 3)));
                if (p < 1) requestAnimationFrame(tick);
            })(performance.now());
        }, 1200);
    });

    // password visibility
    var pw = document.getElementById('password'), tg = document.getElementById('pwToggle');
    tg.addEventListener('click', function () {
        var show = pw.type === 'password';
        pw.type = show ? 'text' : 'password'; tg.textContent = show ? '🙈' : '👁'; tg.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
    });

    // loading state on submit
    var form = document.getElementById('loginForm'), btn = document.getElementById('submitBtn');
    form.addEventListener('submit', function () { if (form.checkValidity()) btn.classList.add('loading'); });

    if (!reduce) {
        // cursor glow + card spotlight / tilt
        var glow = document.getElementById('glow'), card = document.getElementById('card'), cin = card.querySelector('.card-in');
        addEventListener('mousemove', function (e) { glow.style.transform = 'translate(' + e.clientX + 'px,' + e.clientY + 'px)'; });
        card.addEventListener('mousemove', function (e) {
            var r = card.getBoundingClientRect(), x = (e.clientX - r.left) / r.width - .5, y = (e.clientY - r.top) / r.height - .5;
            card.style.animation = 'none'; card.style.opacity = 1;
            card.style.transition = 'transform .08s';
            card.style.transform = 'perspective(900px) rotateX(' + (-y * 4) + 'deg) rotateY(' + (x * 5) + 'deg)';
            cin.style.setProperty('--mx', (e.clientX - r.left) + 'px'); cin.style.setProperty('--my', (e.clientY - r.top) + 'px');
        });
        card.addEventListener('mouseleave', function () { card.style.transition = 'transform .6s'; card.style.transform = ''; });

        // floating oil-drop particles
        var cv = document.getElementById('drops'), ctx = cv.getContext('2d'), ps = [];
        function size() { cv.width = innerWidth; cv.height = innerHeight; }
        size(); addEventListener('resize', size);
        for (var i = 0; i < 38; i++) ps.push({ x: Math.random() * innerWidth, y: Math.random() * innerHeight, r: Math.random() * 2.2 + .6, v: Math.random() * .35 + .12, a: Math.random() * .5 + .15 });
        (function draw() {
            ctx.clearRect(0, 0, cv.width, cv.height);
            ps.forEach(function (p) {
                p.y -= p.v; if (p.y < -10) { p.y = cv.height + 10; p.x = Math.random() * cv.width; }
                ctx.beginPath(); ctx.arc(p.x, p.y, p.r, 0, 6.283); ctx.fillStyle = 'rgba(34,211,238,' + p.a + ')'; ctx.fill();
            });
            requestAnimationFrame(draw);
        })();
    }
})();
</script>
<script src="{{ asset('assets/js/rig.js') }}"></script>
</body>
</html>
