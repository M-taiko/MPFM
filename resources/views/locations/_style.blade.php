@include('integrity._style')
<style>
.ofp .lc-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(300px,1fr)); gap:16px; margin-top:16px; }
.ofp .lc { position:relative; padding:18px 20px 16px; border-radius:20px; border:1px solid var(--line); background:linear-gradient(180deg,rgba(255,255,255,.06),rgba(255,255,255,.02)); transition:transform .3s, border-color .3s; overflow:hidden; }
.ofp .lc::before { content:""; position:absolute; inset:0 0 auto 0; height:3px; background:var(--ec); }
.ofp .lc:hover { transform:translateY(-3px); border-color:var(--ec); }
.ofp .lc .tp { display:flex; align-items:center; gap:10px; font-size:11px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--ec); }
.ofp .lc .tp i { font-size:16px; }
.ofp .lc h3 { margin:10px 0 2px; font-size:18px; font-weight:800; color:#fff; }
.ofp .lc .cd { font-size:12px; color:var(--mute); }
.ofp .lc .st { display:grid; grid-template-columns:repeat(var(--n,3),1fr); gap:8px; margin:14px 0; }
.ofp .lc .st div { padding:8px 10px; border-radius:12px; background:var(--glass); border:1px solid var(--line); text-align:center; }
.ofp .lc .st b { display:block; font:700 18px 'Space Grotesk',sans-serif; color:#fff; }
.ofp .lc .st small { font-size:9.5px; letter-spacing:.08em; text-transform:uppercase; color:var(--mute); }
.ofp .lc .ft { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
.ofp .lc .ft .sp { flex:1; }
.ofp .seg { display:flex; gap:8px; flex-wrap:wrap; margin-top:18px; }
.ofp .seg a { display:inline-flex; align-items:center; gap:8px; padding:9px 16px; border-radius:30px; border:1px solid var(--line); background:var(--glass); color:var(--sub); font-size:13px; font-weight:600; transition:all .25s; }
.ofp .seg a:hover { color:#fff; border-color:var(--cyan); }
.ofp .seg a.on { color:#fff; border-color:var(--ec,var(--cyan)); background:color-mix(in srgb, var(--ec,#22d3ee) 16%, transparent); }
.ofp .seg a b { font:700 12px 'Space Grotesk'; opacity:.8; }
.ofp .ib { background:none; border:1px solid var(--line); color:var(--sub); border-radius:10px; padding:6px 10px; font-size:12px; cursor:pointer; display:inline-flex; align-items:center; gap:6px; }
.ofp .ib:hover { color:#fff; border-color:var(--cyan); } .ofp .ib.del:hover { color:#fca5a5; border-color:var(--red); }
.ofp .xb { background:none; border:0; color:#fca5a5; cursor:pointer; font-size:14px; padding:4px 8px; border-radius:8px; }
.ofp .xb:hover { background:rgba(248,113,113,.12); }
.ofp .asg { display:flex; gap:10px; flex-wrap:wrap; align-items:flex-end; margin-top:14px; }
.ofp .asg .f { flex:1; min-width:200px; }
.ofp .asg .fc { height:40px; border-radius:12px; font-size:13px; }
.ofp .crumbs { display:flex; align-items:center; gap:8px; flex-wrap:wrap; font-size:12.5px; margin-top:6px; }
.ofp .crumbs a { color:var(--cyan); } .ofp .crumbs i.sep { color:var(--mute); font-size:12px; } .ofp .crumbs span { color:var(--sub); }
.ofp .ptl { display:grid; gap:10px; grid-template-columns:repeat(auto-fit,minmax(150px,1fr)); }
.ofp .ptl.big { grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); }
.ofp .ptl input { position:absolute; opacity:0; pointer-events:none; }
.ofp .ptl label { margin:0; cursor:pointer; display:flex; align-items:center; gap:8px; padding:11px 14px; border-radius:14px; border:1px solid var(--line); background:rgba(255,255,255,.04); color:var(--sub); font-size:12.5px; font-weight:600; transition:all .25s; }
.ofp .ptl label:hover { border-color:var(--tc); }
.ofp .ptl input:checked + label { color:#fff; border-color:var(--tc); background:color-mix(in srgb, var(--tc) 14%, transparent); box-shadow:0 0 18px color-mix(in srgb, var(--tc) 25%, transparent); }
.ofp .ptl.big label { padding:16px 18px; flex-direction:column; align-items:flex-start; gap:4px; font-size:15px; color:#e8eefc; }
.ofp .ptl.big label small { display:block; font-size:11.5px; font-weight:500; color:var(--mute); line-height:1.4; }
.ofp .ptl.big label i { font-size:22px; color:var(--tc); }
.ofp .sn { display:inline-grid; place-items:center; min-width:62px; height:26px; padding:0 10px; border-radius:9px; font:700 12px 'Space Grotesk'; background:rgba(34,211,238,.14); color:#67e8f9; border:1px solid rgba(34,211,238,.4); }
.ofp .sn.none { background:rgba(148,163,184,.1); color:#94a3b8; border-color:rgba(148,163,184,.3); }
</style>
