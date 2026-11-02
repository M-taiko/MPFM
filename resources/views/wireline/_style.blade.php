<link rel="stylesheet" href="{{ asset('assets/css/ofpage.css') }}">
<style>
.ofp .wl-grid { display:grid; gap:18px; margin-top:20px; }
.ofp .wl-g3 { display:grid; grid-template-columns:1.6fr 1fr; gap:18px; margin-top:18px; }
.ofp .row { display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:16px; }
.ofp .f label.lb { display:flex; align-items:center; gap:7px; font-size:10.5px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--mute); margin-bottom:7px; }
.ofp .f label.lb b { color:var(--red); } .ofp .f label.lb i { color:var(--cyan); font-size:13px; }
.ofp .fc { width:100%; height:46px; padding:0 14px; border-radius:14px; border:1px solid var(--line); background:rgba(255,255,255,.06); color:#fff; font:500 14px 'Inter',sans-serif; transition:border-color .25s, box-shadow .25s; outline:none; }
.ofp textarea.fc { height:auto; padding:12px 14px; resize:vertical; line-height:1.5; }
.ofp input[type=file].fc { padding:11px 14px; height:auto; }
.ofp .fc:focus { border-color:var(--cyan); box-shadow:0 0 0 4px rgba(34,211,238,.14); background:rgba(255,255,255,.09); }
.ofp .fc::placeholder { color:#52627d; }
.ofp select.fc { appearance:none; -webkit-appearance:none; padding-right:36px; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' fill='none' stroke='%2322d3ee' stroke-width='2'%3E%3Cpath d='M1 1l5 5 5-5'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 14px center; }
.ofp select.fc option { background:#0b1426; color:#fff; }
.ofp .f.has-err .fc { border-color:var(--red); }
.ofp .em { color:#fca5a5; font-size:12px; margin-top:5px; display:block; }
.ofp .err { margin-top:18px; padding:14px 18px; border-radius:16px; border:1px solid rgba(248,113,113,.45); background:rgba(248,113,113,.08); color:#fecaca; font-size:13px; }
.ofp .err ul { margin:8px 0 0; padding-left:18px; }
.ofp .flash { margin-top:18px; }
.ofp .bar { display:flex; gap:12px; flex-wrap:wrap; align-items:center; padding:18px 22px; border-radius:22px; border:1px solid var(--line); background:rgba(255,255,255,.04); margin-top:16px; }
.ofp .bar .sp { flex:1; } .ofp .bar .hint { font-size:12px; color:var(--mute); }
.ofp .ph .n { width:32px; height:32px; border-radius:50%; background:var(--grad); color:#04111f; font:700 14px 'Space Grotesk'; display:grid; place-items:center; flex:none; }
.ofp .ph.num { justify-content:flex-start; gap:12px; }
.ofp .ph.num small { display:block; color:var(--mute); font-size:12px; font-weight:500; margin-top:1px; }
.ofp .panel + .panel { margin-top:16px; }
.ofp .tdot { display:inline-block; width:9px; height:9px; border-radius:50%; margin-right:8px; background:var(--tc,#94a3b8); box-shadow:0 0 10px var(--tc,#94a3b8); }
.ofp .flt { display:flex; gap:10px; flex-wrap:wrap; align-items:flex-end; }
.ofp .flt .f { min-width:150px; flex:1; }
.ofp .flt .fc { height:40px; border-radius:12px; font-size:13px; }
.ofp .cw { position:relative; height:320px; } .ofp .cw.sm { height:240px; }
.ofp .mini { font:600 12px 'Space Grotesk',sans-serif; color:var(--sub); }
.ofp .list { list-style:none; margin:0; padding:0; display:grid; gap:9px; }
.ofp .list li { display:flex; gap:10px; font-size:13px; color:var(--sub); line-height:1.55; }
.ofp .list li i { color:var(--cyan); margin-top:3px; flex:none; }
.ofp .list.q li.ok i { color:var(--green); } .ofp .list.q li.warn i { color:var(--amber); } .ofp .list.q li.bad i { color:var(--red); }
.ofp .kv { display:grid; grid-template-columns:repeat(auto-fit,minmax(170px,1fr)); gap:12px; }
.ofp .kv div { padding:11px 13px; border-radius:14px; background:var(--glass); border:1px solid var(--line); }
.ofp .kv small { display:block; font-size:10px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--mute); }
.ofp .kv b { display:block; font:600 14px 'Space Grotesk',sans-serif; color:#fff; margin-top:2px; word-break:break-word; }
.ofp .sdep { width:110px; height:34px; border-radius:10px; font-size:13px; padding:0 10px; }
.ofp .pg { margin-top:14px; display:flex; justify-content:center; }
.ofp .pg nav svg { width:18px; height:18px; } .ofp .pg nav a, .ofp .pg nav span { color:var(--sub); }
.ofp .pg .pagination { display:flex; gap:6px; list-style:none; padding:0; flex-wrap:wrap; }
.ofp .pg .page-link { display:block; padding:6px 12px; border-radius:10px; border:1px solid var(--line); background:var(--glass); color:var(--sub); font-size:12px; }
.ofp .pg .active .page-link { background:rgba(34,211,238,.15); border-color:var(--cyan); color:#fff; }
.ofp .tile { display:grid; grid-template-columns:repeat(auto-fit,minmax(150px,1fr)); gap:10px; }
.ofp .tile input { position:absolute; opacity:0; pointer-events:none; }
.ofp .tile label { margin:0; cursor:pointer; display:flex; align-items:center; gap:8px; padding:11px 14px; border-radius:14px; border:1px solid var(--line); background:rgba(255,255,255,.04); color:var(--sub); font-size:12.5px; font-weight:600; transition:all .25s; }
.ofp .tile input:checked + label { color:#fff; border-color:var(--tc); background:color-mix(in srgb, var(--tc) 14%, transparent); box-shadow:0 0 18px color-mix(in srgb, var(--tc) 25%, transparent); }
@media (max-width:1100px) { .ofp .wl-g3 { grid-template-columns:1fr; } }
</style>
