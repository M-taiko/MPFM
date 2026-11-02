{{-- Animated Wire Line (slickline) operation: unit + lubricator + wellhead, memory-gauge tool string running in hole,
     and the pressure/temperature recording being drawn in step. Self-contained (own scoped CSS, unique ids). --}}
<style>
.wlx { width:100%; height:auto; display:block; }
.wlx .tx { font:600 11px Inter,sans-serif; fill:#cfe0ff; }
.wlx .tx.s { font-size:9.5px; fill:#9fb0cf; font-weight:500; }
.wlx .tx.h { font:700 10.5px Inter,sans-serif; fill:#9fb0cf; letter-spacing:.12em; }
.wlx .steel { fill:#2a55a4; stroke:#7fb4ff; stroke-width:1.3; }
.wlx .bore { fill:#081226; }
.wlx .eqp { fill:#0d1b3a; stroke:#3b82f6; stroke-width:1.5; }
.wlx .spin { transform-box:fill-box; transform-origin:center; animation:wlxSpin 2.6s linear infinite; }
@keyframes wlxSpin { to { transform:rotate(360deg); } }
.wlx .blink { animation:wlxBlink 1.4s infinite; } @keyframes wlxBlink { 50% { opacity:.25; } }
.wlx .inflow { fill:none; stroke:#d97706; stroke-width:2.4; stroke-dasharray:6 8; animation:wlxDash 1.1s linear infinite; }
@keyframes wlxDash { to { stroke-dashoffset:-28; } }
.wlx .glow { animation:wlxGlow 1.6s ease-in-out infinite; } @keyframes wlxGlow { 50% { opacity:.45; } }
@media (prefers-reduced-motion: reduce) { .wlx .spin, .wlx .blink, .wlx .inflow, .wlx .glow { animation:none; } }
</style>
<svg class="wlx" viewBox="0 0 1000 440" role="img" aria-label="Wire Line operation: a slickline unit runs a memory gauge through the lubricator and wellhead down the well while pressure and temperature are recorded">
    <defs>
        <linearGradient id="wlxSteel" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="#1b3a78"/><stop offset=".45" stop-color="#5b8de0"/><stop offset="1" stop-color="#1b3a78"/></linearGradient>
        <linearGradient id="wlxGround" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#16213d"/><stop offset="1" stop-color="#0a1226"/></linearGradient>
        <linearGradient id="wlxRes" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#92400e" stop-opacity=".55"/><stop offset="1" stop-color="#451a03" stop-opacity=".55"/></linearGradient>
        <radialGradient id="wlxGauge" cx=".5" cy=".5"><stop offset="0" stop-color="#a5f3fc"/><stop offset="1" stop-color="#0891b2"/></radialGradient>
        <clipPath id="wlxChart"><rect x="560" y="64" width="410" height="296"/></clipPath>
    </defs>

    {{-- ═════ SURFACE & SUBSURFACE ═════ --}}
    <rect x="0" y="150" width="520" height="290" fill="url(#wlxGround)"/>
    <path d="M0 150H520" stroke="#2e4170" stroke-width="2"/>
    <g stroke="#1e2d52" stroke-width="1" stroke-dasharray="5 6"><path d="M0 215H520M0 285H520M0 335H520"/></g>
    <rect x="0" y="335" width="520" height="70" fill="url(#wlxRes)"/>
    <text class="tx s" x="14" y="205">CAP ROCK</text><text class="tx s" x="14" y="275">FORMATION</text><text class="tx s" x="14" y="325">SEAL</text>
    <text class="tx" x="14" y="372" style="fill:#fbbf24">RESERVOIR</text><text class="tx s" x="14" y="386">perforated interval</text>

    {{-- casing & tubing --}}
    <rect x="284" y="150" width="32" height="268" fill="#10234a" stroke="#2f55a0"/>
    <rect x="294" y="150" width="12" height="262" class="bore"/>
    <path d="M284 340H294M306 340H316M284 352H294M306 352H316M284 364H294M306 364H316" stroke="#fbbf24" stroke-width="2"/>
    <g class="inflow"><path d="M200 340H283"/><path d="M200 352H283"/><path d="M200 364H283"/><path d="M400 340H317" /><path d="M400 352H317"/><path d="M400 364H317"/></g>

    {{-- wellhead / Christmas tree --}}
    <rect x="278" y="126" width="44" height="26" rx="2" class="steel"/>
    <rect x="248" y="134" width="30" height="12" class="steel"/><rect x="322" y="134" width="30" height="12" class="steel"/>
    <circle cx="240" cy="140" r="8" class="eqp"/><circle cx="360" cy="140" r="8" class="eqp"/>
    <path d="M240 132V122M234 122H246M360 132V122M354 122H366" stroke="#22d3ee" stroke-width="1.6"/>
    <text class="tx s" x="300" y="170" text-anchor="middle" style="fill:#93c5fd">XMAS TREE</text>

    {{-- wireline valve (BOP) --}}
    <rect x="284" y="98" width="32" height="28" rx="3" class="steel"/><rect x="294" y="98" width="12" height="28" class="bore"/>
    <rect x="262" y="106" width="22" height="12" class="steel"/><circle cx="256" cy="112" r="7" class="eqp"/><path d="M256 105V95M251 95H261" stroke="#22d3ee" stroke-width="1.6"/>

    {{-- lubricator --}}
    <rect x="288" y="34" width="24" height="64" class="steel"/><rect x="294" y="34" width="12" height="64" class="bore"/>
    <rect x="283" y="30" width="34" height="8" rx="2" fill="url(#wlxSteel)" stroke="#7fb4ff"/><rect x="283" y="94" width="34" height="8" rx="2" fill="url(#wlxSteel)" stroke="#7fb4ff"/>
    {{-- pressure gauge on lubricator --}}
    <path d="M312 64H336" stroke="#94a3b8" stroke-width="1.6"/><circle cx="346" cy="64" r="11" class="eqp" style="stroke:#fbbf24"/><path d="M346 64L352 59" stroke="#fbbf24" stroke-width="1.6"/>
    {{-- stuffing box + sheave --}}
    <rect x="290" y="16" width="20" height="16" rx="2" class="steel"/>
    <circle cx="262" cy="14" r="13" class="eqp" style="stroke:#a78bfa"/><circle class="spin" cx="262" cy="14" r="8" fill="none" stroke="#a78bfa" stroke-width="1.5" stroke-dasharray="4 5"/>
    <path d="M262 27V48M262 48H300" stroke="#a78bfa" stroke-width="1" opacity="0"/>

    {{-- slickline unit (truck + drum) --}}
    <g transform="translate(18,92)">
        <rect x="0" y="22" width="132" height="34" rx="4" fill="#12306a" stroke="#60a5fa"/>
        <rect x="100" y="8" width="32" height="20" rx="3" fill="#0d1b3a" stroke="#22d3ee"/><rect x="106" y="12" width="20" height="9" fill="#1e3a6e"/>
        <circle cx="26" cy="60" r="8" fill="#0d1b3a" stroke="#60a5fa"/><circle cx="104" cy="60" r="8" fill="#0d1b3a" stroke="#60a5fa"/>
        <rect x="14" y="-6" width="52" height="32" rx="4" fill="#0d1b3a" stroke="#a78bfa"/>
        <circle class="spin" cx="40" cy="10" r="12" fill="none" stroke="#a78bfa" stroke-width="2" stroke-dasharray="6 5"/><circle cx="40" cy="10" r="3" fill="#a78bfa"/>
        <circle class="blink" cx="118" cy="42" r="2.4" fill="#34d399"/>
    </g>
    <text class="tx s" x="84" y="172" text-anchor="middle">SLICKLINE UNIT</text>
    {{-- wire: drum → sheave → down through the lubricator --}}
    <path d="M60 94L252 16" stroke="#cbd5e1" stroke-width="1.1" fill="none"/>
    <path d="M262 27L300 34" stroke="#cbd5e1" stroke-width="1.1" fill="none" opacity="0"/>
    <path d="M272 12Q296 12 300 30" stroke="#cbd5e1" stroke-width="1.1" fill="none"/>
    <line x1="300" y1="30" x2="300" y2="74" stroke="#e2e8f0" stroke-width="1.3">
        <animate attributeName="y2" values="74;74;324;324;74;74" keyTimes="0;.05;.4;.7;.95;1" dur="12s" repeatCount="indefinite"/>
    </line>

    {{-- tool string: rope socket · stem · memory gauge · nose --}}
    <g>
        <animateTransform attributeName="transform" type="translate" values="0 0;0 0;0 250;0 250;0 0;0 0" keyTimes="0;.05;.4;.7;.95;1" dur="12s" repeatCount="indefinite"/>
        <rect x="297" y="74" width="6" height="10" rx="1" fill="#94a3b8"/>
        <rect x="298" y="84" width="4" height="22" fill="#64748b"/>
        <rect x="296" y="106" width="8" height="24" rx="1" fill="#475569" stroke="#94a3b8" stroke-width=".8"/>
        <rect x="295" y="130" width="10" height="26" rx="3" fill="url(#wlxGauge)" stroke="#e0f2fe" stroke-width="1"/>
        <circle class="glow" cx="300" cy="143" r="7" fill="#22d3ee" opacity=".35"/>
        <path d="M296 156L300 164L304 156Z" fill="#64748b"/>
    </g>
    <g class="tx s" fill="#9fb0cf">
        <text x="372" y="100" style="fill:#9fb0cf">LUBRICATOR</text><path d="M366 97L318 80" stroke="#475569" stroke-width="1"/>
        <text x="362" y="24" style="fill:#9fb0cf">STUFFING BOX</text><path d="M356 21L312 24" stroke="#475569" stroke-width="1"/>
        <text x="372" y="124" style="fill:#9fb0cf">WIRELINE VALVE</text><path d="M366 121L318 112" stroke="#475569" stroke-width="1"/>
        <text x="368" y="270" style="fill:#67e8f9">MEMORY GAUGE</text><path d="M364 267L312 267" stroke="#22d3ee" stroke-width="1" stroke-dasharray="3 3"/>
        <text x="368" y="283" style="fill:#9fb0cf">rope socket · stem · gauge</text>
    </g>

    {{-- ═════ RECORDING PANEL ═════ --}}
    <rect x="548" y="14" width="440" height="410" rx="14" fill="#0a1226" stroke="#1d2b4d"/>
    <text class="tx h" x="568" y="42">MEMORY GAUGE RECORDING</text>
    <rect x="880" y="28" width="86" height="20" rx="10" fill="rgba(52,211,153,.1)" stroke="rgba(52,211,153,.35)"/><circle class="blink" cx="894" cy="38" r="3.4" fill="#34d399"/><text class="tx" x="903" y="42" style="fill:#34d399;font-size:9.5px;letter-spacing:.12em">SURVEY</text>
    <rect x="560" y="64" width="410" height="296" fill="#070d1c"/>
    <rect x="580" y="64" width="140" height="296" fill="#3b82f6" opacity=".08"/><rect x="720" y="64" width="120" height="296" fill="#34d399" opacity=".09"/><rect x="840" y="64" width="120" height="296" fill="#fbbf24" opacity=".08"/>
    <g class="tx s" text-anchor="middle"><text x="650" y="80" style="fill:#60a5fa">RUN IN HOLE</text><text x="780" y="80" style="fill:#34d399">ON BOTTOM</text><text x="900" y="80" style="fill:#fbbf24">PULL OUT</text></g>
    <g stroke="#1b2847"><path d="M560 120H970M560 180H970M560 240H970M560 300H970"/></g>
    <g clip-path="url(#wlxChart)">
        <path d="M560 332H590C632 332 676 134 722 126L752 120L762 152L772 114L840 124C880 132 904 332 940 332H970" fill="none" stroke="#f87171" stroke-width="1.6" stroke-dasharray="5 4" opacity=".55" transform="translate(0,14)"/>
        <path d="M560 330H590C632 330 676 130 722 124L752 118L762 150L772 112L840 122C880 130 904 330 940 330H970" fill="none" stroke="#22d3ee" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/>
    </g>
    <g fill="#fff" stroke="#0a1226" stroke-width="2"><circle cx="864" cy="170" r="4"/><circle cx="880" cy="214" r="4"/><circle cx="896" cy="262" r="4"/></g>
    <circle r="6" fill="#22d3ee" stroke="#fff" stroke-width="2">
        <animate attributeName="cx" values="560;590;640;722;780;840;880;940;970" keyTimes="0;.05;.2;.4;.55;.7;.8;.95;1" dur="12s" repeatCount="indefinite"/>
        <animate attributeName="cy" values="330;330;215;124;118;122;250;330;330" keyTimes="0;.05;.2;.4;.55;.7;.8;.95;1" dur="12s" repeatCount="indefinite"/>
    </circle>
    <g><line x1="780" y1="118" x2="780" y2="98" stroke="#a78bfa" stroke-dasharray="3 3"/><rect x="722" y="90" width="116" height="20" rx="6" fill="#1e1b3a" stroke="#a78bfa" transform="translate(0,-14)"/><text class="tx" x="780" y="87" text-anchor="middle" style="fill:#c4b5fd;font-size:10px">BHP  2,497 psi</text></g>
    <g class="tx s"><circle cx="572" cy="382" r="4" fill="#22d3ee"/><text x="582" y="386">Pressure</text><circle cx="650" cy="382" r="4" fill="#f87171"/><text x="660" y="386">Temperature</text><circle cx="750" cy="382" r="4" fill="#fff"/><text x="760" y="386">Gauge stops</text></g>
    <g class="tx s"><text x="572" y="410" style="fill:#64748b">Time →  ·  analysed automatically: phases, BHP / BHT, events, gradients</text></g>
</svg>
