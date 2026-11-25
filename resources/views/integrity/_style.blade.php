@include('wireline._style')
<style>
.ofp .tl-dot { display:inline-block; width:11px; height:11px; border-radius:50%; margin-right:8px; background:var(--tc); box-shadow:0 0 10px var(--tc); }
.ofp .chip.none { background:rgba(148,163,184,.12); color:#cbd5e1; border-color:rgba(148,163,184,.35); }
.ofp .big-ring { position:relative; width:150px; height:150px; margin:0 auto; }
.ofp .big-ring svg { width:150px; height:150px; transform:rotate(-90deg); }
.ofp .big-ring b { position:absolute; inset:0; display:grid; place-items:center; font:800 34px 'Space Grotesk',sans-serif; color:#fff; text-align:center; }
.ofp .big-ring b small { display:block; font:600 10px 'Inter'; letter-spacing:.12em; color:var(--mute); text-transform:uppercase; margin-top:-6px; }
.ofp .ig-top { display:grid; grid-template-columns:260px 1fr; gap:18px; align-items:stretch; }
.ofp .rs { display:flex; gap:8px; font-size:12.5px; color:var(--sub); line-height:1.5; margin-top:6px; }
.ofp .rs b { font:700 12px 'Space Grotesk'; min-width:30px; color:var(--red); }
.ofp .rs.amber b { color:var(--amber); } .ofp .rs.info b { color:var(--mute); }
.ofp .pbar { height:8px; border-radius:6px; background:rgba(255,255,255,.07); overflow:hidden; min-width:90px; }
.ofp .pbar i { display:block; height:100%; width:var(--w); background:var(--bc,#34d399); border-radius:6px; }
.ofp .env { display:grid; gap:10px; }
.ofp .el { display:flex; gap:12px; align-items:flex-start; padding:12px 14px; border-radius:16px; border:1px solid var(--line); background:var(--glass); border-left:4px solid var(--ec,#94a3b8); }
.ofp .el .nm { font-weight:700; color:#fff; font-size:14px; }
.ofp .el .mt { font-size:12px; color:var(--mute); margin-top:2px; }
.ofp .el .sp { flex:1; }
.ofp details.frm { margin-top:12px; border:1px dashed var(--line); border-radius:16px; padding:0; background:rgba(255,255,255,.02); }
.ofp details.frm > summary { cursor:pointer; list-style:none; padding:11px 16px; font:600 13px 'Inter'; color:var(--cyan); display:flex; align-items:center; gap:8px; }
.ofp details.frm > summary::-webkit-details-marker { display:none; }
.ofp details.frm[open] > summary { border-bottom:1px dashed var(--line); }
.ofp details.frm .in { padding:16px; }
.ofp details.frm .row { grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); }
.ofp details.frm.sm > summary { padding:6px 12px; font-size:12px; }
.ofp details.frm.sm { margin-top:8px; display:inline-block; min-width:100%; }
.ofp .rk { display:inline-grid; place-items:center; min-width:30px; height:26px; padding:0 8px; border-radius:9px; font:700 12px 'Space Grotesk'; color:#04111f; background:var(--rk,#94a3b8); }
.ofp .x { background:none; border:0; color:#fca5a5; cursor:pointer; font-size:14px; padding:2px 6px; }
.ofp .x:hover { color:#f87171; }
.ofp .g2c { display:grid; grid-template-columns:1fr 1fr; gap:18px; margin-top:18px; }
@media (max-width:1000px) { .ofp .ig-top, .ofp .g2c { grid-template-columns:1fr; } }
</style>
