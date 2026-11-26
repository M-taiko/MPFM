{{-- Well Testing: one well stream measured by the MPFM and by the test separator, then compared (decorative, animated inline SVG) --}}
@php
    $wave = 'M0 0 q10 -4 20 0' . str_repeat(' t20 0', 18);
    $pts = [[2400, 2350], [2650, 2690], [2900, 2860], [3150, 3190], [3350, 3300], [3600, 3640], [3000, 3480]]; // mpfm, vessel
    $sx = fn ($v) => round(46 + ($v - 2200) / 1600 * 250, 1); $sy = fn ($v) => round(446 - ($v - 2200) / 1600 * 130, 1);
    $fleet = [['V-101', 'Onshore', 'Active', '#34d399'], ['V-203', 'Offshore', 'Active', '#34d399'], ['V-118', 'Onshore', 'Maintenance', '#fbbf24']];
@endphp
<svg viewBox="0 0 600 500" role="img" aria-label="One well stream is measured by an MPFM and by a three-phase test separator; the two oil rates are compared, with the agreement gauge and the test vessel fleet">
    <defs>
        <linearGradient id="vsBody" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#2b4f8f"/><stop offset=".5" stop-color="#14305f"/><stop offset="1" stop-color="#0b1c3d"/></linearGradient>
        <linearGradient id="vsOil" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#d97706"/><stop offset="1" stop-color="#7c3a05"/></linearGradient>
        <linearGradient id="vsWat" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#2f8fd0"/><stop offset="1" stop-color="#12466f"/></linearGradient>
        <linearGradient id="vsSteel" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="#1a3566"/><stop offset=".4" stop-color="#7fa6e6"/><stop offset="1" stop-color="#1a3566"/></linearGradient>
        <clipPath id="vsClip"><rect x="152" y="160" width="280" height="68" rx="34"/></clipPath>
        <pattern id="vsMesh" width="4" height="4" patternUnits="userSpaceOnUse"><path d="M0 4 L4 0" stroke="#9db8e4" stroke-width=".8"/></pattern>
    </defs>

    <!-- scene panel -->
    <rect x="12" y="10" width="576" height="262" rx="16" fill="rgba(7,16,38,.7)" stroke="#2a3b66"/>
    <g font-size="9" font-weight="800" letter-spacing="1.2">
        <rect x="24" y="20" width="76" height="20" rx="10" fill="#38bdf8" fill-opacity=".16" stroke="#38bdf8"><animate attributeName="fill-opacity" values=".3;.06;.3" dur="6s" repeatCount="indefinite"/></rect><text x="62" y="33.5" text-anchor="middle" fill="#7dd3fc">OFFSHORE</text>
        <rect x="108" y="20" width="70" height="20" rx="10" fill="#fbbf24" fill-opacity=".16" stroke="#fbbf24"><animate attributeName="fill-opacity" values=".06;.3;.06" dur="6s" repeatCount="indefinite"/></rect><text x="143" y="33.5" text-anchor="middle" fill="#fcd34d">ONSHORE</text>
    </g>

    <!-- well and split -->
    <circle cx="44" cy="138" r="22" fill="#0d1730" stroke="#22d3ee" stroke-width="1.8"/><circle cx="44" cy="138" r="28" fill="none" stroke="#22d3ee" stroke-opacity=".3" class="pulse"/>
    <path d="M44 124 L36 150 M44 124 L52 150 M38 142 H50" stroke="#eaf6ff" stroke-width="1.8" fill="none" stroke-linecap="round"/><path d="M44 124 V120" stroke="#eaf6ff" stroke-width="2.2"/>
    <text x="44" y="178" font-size="8.5" font-weight="800" text-anchor="middle" fill="#9aaccb">WELL ZK-101</text>
    <g fill="none" stroke-linecap="round" stroke-linejoin="round">
        <path d="M66 138 H96 V84 H452 M96 138 V194 H152" stroke="#13264a" stroke-width="7"/>
        <path d="M66 138 H96 V84 H452 M96 138 V194 H152" stroke="#8fb4e8" stroke-width="1.6" class="flowd"/>
    </g>
    <circle cx="96" cy="138" r="5" fill="#0d1730" stroke="#8fb4e8" stroke-width="1.6"/>
    <text x="104" y="160" font-size="8.5" font-weight="700" fill="#9aaccb">two independent lines</text>

    <!-- MPFM on the upper line -->
    <rect x="170" y="62" width="76" height="44" rx="8" fill="#0d1a38" stroke="#22d3ee" stroke-opacity=".5"/>
    <path d="M176 84 H190 L204 76 H214 L228 84 H242" stroke="#13264a" stroke-width="12" fill="none"/>
    <path d="M176 78 H190 L204 70 H214 L228 78 H242 M176 90 H190 L204 98 H214 L228 90 H242" stroke="#a9c6f5" stroke-width="1.8" fill="none" stroke-linejoin="round"/>
    <rect x="198" y="40" width="22" height="20" rx="4" fill="#122248" stroke="#22d3ee"/><circle cx="204" cy="46" r="2.2" fill="#34d399" class="blink"/><path d="M200 53 H218 M200 57 H212" stroke="#6fa0e8" stroke-width="1.2"/>
    <path d="M209 60 V70" stroke="#a9c6f5" stroke-width="2"/>
    <circle cx="209" cy="112" r="6" fill="#0b1530" stroke="#fbbf24" stroke-width="1.4"/><circle cx="209" cy="112" r="6" fill="none" stroke="#fbbf24"><animate attributeName="r" values="6;22" dur="2.4s" repeatCount="indefinite"/><animate attributeName="opacity" values=".8;0" dur="2.4s" repeatCount="indefinite"/></circle>
    <text x="209" y="34" font-size="9" font-weight="800" letter-spacing="1.4" text-anchor="middle" fill="#67e8f9">MPFM</text>
    <text x="209" y="130" font-size="8" text-anchor="middle" fill="#64748b">gamma + venturi</text>

    <!-- separator -->
    <rect x="152" y="160" width="280" height="68" rx="34" fill="url(#vsBody)" stroke="#a9c6f5" stroke-width="1.8"/>
    <g clip-path="url(#vsClip)">
        <rect x="152" y="160" width="280" height="22" fill="#1a3a6e" fill-opacity=".55"/>
        <rect x="152" y="182" width="280" height="30" fill="url(#vsOil)"/><rect x="152" y="206" width="280" height="30" fill="url(#vsWat)"/>
        <g><path d="{{ $wave }}" fill="none" stroke="#fcd34d" stroke-opacity=".8" stroke-width="1.6" transform="translate(150,182)"><animateTransform attributeName="transform" type="translate" from="150 182" to="170 182" dur="3s" repeatCount="indefinite"/></path></g>
        <g><path d="{{ $wave }}" fill="none" stroke="#bfe6ff" stroke-opacity=".8" stroke-width="1.6" transform="translate(150,206)"><animateTransform attributeName="transform" type="translate" from="170 206" to="150 206" dur="3.6s" repeatCount="indefinite"/></path></g>
        <path d="M205 160 V228" stroke="#cfe0ff" stroke-width="2.4" opacity=".8"/><path d="M196 172 H214" stroke="#cfe0ff" stroke-width="2"/>
        <rect x="346" y="196" width="4" height="34" fill="#cfe0ff" opacity=".85"/><rect x="382" y="164" width="34" height="14" fill="url(#vsMesh)" stroke="#9db8e4" stroke-opacity=".6"/>
        @foreach([[176, 0], [196, -1.3], [182, -2.1], [330, -.7], [366, -1.9]] as [$bx, $bd])
        <circle cx="{{ $bx }}" cy="196" r="2" fill="none" stroke="#fde68a" opacity=".8"><animateTransform attributeName="transform" type="translate" values="0 0;0 -26" dur="3s" begin="{{ $bd }}s" repeatCount="indefinite"/><animate attributeName="opacity" values="0;.9;0" dur="3s" begin="{{ $bd }}s" repeatCount="indefinite"/></circle>
        @endforeach
    </g>
    <path d="M152 194 H160" stroke="#8fb4e8" stroke-width="7" stroke-linecap="round"/>
    <text x="292" y="176" font-size="8" font-weight="700" text-anchor="middle" fill="#bcd3ff">GAS</text><text x="292" y="199" font-size="8" font-weight="700" text-anchor="middle" fill="#fde68a">OIL</text><text x="292" y="222" font-size="8" font-weight="700" text-anchor="middle" fill="#bfe6ff">WATER</text>
    <rect x="172" y="236" width="52" height="14" rx="7" fill="#0b1530" stroke="#8fb4e8" stroke-opacity=".5"/><text x="198" y="246" font-size="8" font-weight="800" text-anchor="middle" fill="#cfe0ff">V-101</text>
    <!-- gauges -->
    @foreach([[250, '#34d399', 'P'], [330, '#fbbf24', 'T']] as $gi => [$gx, $gc, $gl])
    <path d="M{{ $gx }} 160 V148" stroke="#8fb4e8" stroke-width="2"/><circle cx="{{ $gx }}" cy="138" r="10" fill="#08122b" stroke="{{ $gc }}" stroke-width="1.6"/>
    <g><animateTransform attributeName="transform" type="rotate" values="-40 {{ $gx }} 138;30 {{ $gx }} 138;-10 {{ $gx }} 138;-40 {{ $gx }} 138" dur="{{ 6 + $gi * 2 }}s" repeatCount="indefinite"/><path d="M{{ $gx }} 138 V130" stroke="{{ $gc }}" stroke-width="1.6" stroke-linecap="round"/></g>
    <text x="{{ $gx }}" y="124" font-size="8" font-weight="800" text-anchor="middle" fill="{{ $gc }}">{{ $gl }}</text>
    @endforeach
    <!-- outlets -->
    <g fill="none" stroke-linecap="round" stroke-linejoin="round">
        <path d="M398 160 V130 H452 M416 224 V246 H452 M300 228 V258 H452" stroke="#13264a" stroke-width="5"/>
        <path d="M398 160 V130 H452" stroke="#22d3ee" stroke-width="1.6" class="flowd"/><path d="M416 224 V246 H452" stroke="#fbbf24" stroke-width="1.6" class="flowd"/><path d="M300 228 V258 H452" stroke="#38bdf8" stroke-width="1.6" class="flowd"/>
    </g>
    <path d="M436 124 l8 6 -8 6" fill="none" stroke="#22d3ee" stroke-width="1.6"/>
    <!-- readouts -->
    <g>
        <rect x="452" y="50" width="124" height="68" rx="12" fill="#0d1730" stroke="#22d3ee" stroke-opacity=".7"/><rect x="452" y="64" width="3.5" height="40" rx="1.7" fill="#22d3ee"/>
        <text x="466" y="68" font-size="9" font-weight="800" letter-spacing="1.3" fill="#67e8f9">MPFM</text>
        <text x="466" y="86" font-size="12.5" font-weight="800" fill="#fff">3,280 B/D</text><text x="466" y="100" font-size="8.6" fill="#9aaccb">WC 18.2 % · GOR 640</text><text x="466" y="111" font-size="8.6" fill="#9aaccb">Gas 1.92 MMscf/D</text>
        <rect x="452" y="150" width="124" height="116" rx="12" fill="#0d1730" stroke="#fbbf24" stroke-opacity=".7"/><rect x="452" y="164" width="3.5" height="88" rx="1.7" fill="#fbbf24"/>
        <text x="466" y="168" font-size="9" font-weight="800" letter-spacing="1.3" fill="#fcd34d">TEST SEPARATOR</text>
        <text x="466" y="186" font-size="12.5" font-weight="800" fill="#fff">3,310 B/D</text><text x="466" y="200" font-size="8.6" fill="#9aaccb">Water 735 B/D</text><text x="466" y="212" font-size="8.6" fill="#9aaccb">Gas 1.89 MMscf/D</text><text x="466" y="224" font-size="8.6" fill="#9aaccb">P 118 psi · T 74 °C</text>
        <text x="466" y="244" font-size="8.6" fill="#9aaccb">Duration 4 h · stabilised</text><path d="M466 252 h96" stroke="#fbbf24" stroke-opacity=".3"/>
    </g>
    <path d="M514 118 C514 128 514 140 514 150" stroke="#a78bfa" stroke-width="1.6" class="flowd" fill="none"/>
    <g transform="translate(514,134)"><rect x="-32" y="-9" width="64" height="18" rx="9" fill="#17142e" stroke="#a78bfa"/><text y="3.5" font-size="9" font-weight="800" text-anchor="middle" fill="#c4b5fd">Δ oil -0.9 %</text></g>

    <!-- comparison: scatter -->
    <rect x="12" y="284" width="326" height="204" rx="16" fill="rgba(7,16,38,.7)" stroke="#2a3b66"/>
    <text x="28" y="308" font-size="10" font-weight="800" letter-spacing="1.6" fill="#22d3ee">MPFM vs VESSEL · OIL RATE</text>
    <g stroke="#2a3b66">@foreach([0, 1, 2, 3] as $g)<path d="M46 {{ 316 + $g * 43.3 }} H296"/>@endforeach</g>
    <path d="M46 446 L296 316" stroke="#34d399" stroke-width="1.6" stroke-dasharray="5 4" fill="none"/>
    <path d="M46 446 L296 282 M46 446 L296 350" stroke="#f87171" stroke-opacity=".5" fill="none"/><path d="M46 446 L296 316 L296 282Z M46 446 L296 316 L296 350Z" fill="#f87171" fill-opacity=".05" stroke="none"/>
    <text x="298" y="280" font-size="8.5" font-weight="800" fill="#fca5a5">+10 %</text><text x="298" y="357" font-size="8.5" font-weight="800" fill="#fca5a5">-10 %</text>
    <text x="171" y="474" font-size="8.5" text-anchor="middle" fill="#64748b">Test vessel (B/D)</text><text x="22" y="380" font-size="8.5" fill="#64748b" transform="rotate(-90 22 380)" text-anchor="middle">MPFM (B/D)</text>
    @foreach($pts as $i => [$m, $v])
    @php $out = $i === 6; @endphp
    <circle cx="{{ $sx($v) }}" cy="{{ $sy($m) }}" r="{{ $out ? 5 : 4 }}" fill="{{ $out ? '#f87171' : '#22d3ee' }}" opacity="0"><animate attributeName="opacity" values="0;0;1;1;0" keyTimes="0;{{ .05 + $i * .08 }};{{ .1 + $i * .08 }};.94;1" dur="9s" repeatCount="indefinite"/></circle>
    @endforeach
    <g transform="translate({{ $sx(3480) - 52 }},{{ $sy(3000) + 14 }})"><rect width="92" height="16" rx="8" fill="#2a1216" stroke="#f87171"/><text x="46" y="11" font-size="8.5" font-weight="800" text-anchor="middle" fill="#fca5a5">outlier: re-test</text></g>

    <!-- agreement gauge -->
    <rect x="350" y="284" width="238" height="108" rx="16" fill="rgba(7,16,38,.7)" stroke="#2a3b66"/>
    <text x="366" y="308" font-size="10" font-weight="800" letter-spacing="1.6" fill="#fbbf24">AGREEMENT</text>
    <g transform="translate(469,368)" fill="none" stroke-linecap="round">
        <path d="M-62 0 A62 62 0 0 1 62 0" stroke="#f87171" stroke-width="9" stroke-opacity=".6"/>
        <path d="M-50 -36.6 A62 62 0 0 1 50 -36.6" stroke="#fbbf24" stroke-width="9" stroke-opacity=".9" transform="rotate(0)"/>
        <path d="M-31 -53.7 A62 62 0 0 1 31 -53.7" stroke="#34d399" stroke-width="9"/>
        <g><animateTransform attributeName="transform" type="rotate" values="-28;-6;-14;-9;-28" dur="9s" repeatCount="indefinite"/><path d="M0 0 L0 -52" stroke="#fff" stroke-width="2.4"/><circle r="5" fill="#fff"/></g>
        <text y="20" x="0" font-size="13" font-weight="800" text-anchor="middle" fill="#fff" stroke="none">-2.4 %</text>
    </g>
    <text x="406" y="384" font-size="8" fill="#64748b" text-anchor="middle">-10</text><text x="532" y="384" font-size="8" fill="#64748b" text-anchor="middle">+10</text>

    <!-- fleet -->
    <rect x="350" y="400" width="238" height="88" rx="16" fill="rgba(7,16,38,.7)" stroke="#2a3b66"/>
    @foreach($fleet as $i => [$vn, $vs, $vst, $vc])
    @php $fy = 410 + $i * 25; @endphp
    <text x="366" y="{{ $fy + 12 }}" font-size="10" font-weight="800" fill="#fff">{{ $vn }}</text>
    <rect x="408" y="{{ $fy + 2 }}" width="58" height="14" rx="7" fill="{{ $vs === 'Offshore' ? '#38bdf8' : '#fbbf24' }}" fill-opacity=".14" stroke="{{ $vs === 'Offshore' ? '#38bdf8' : '#fbbf24' }}" stroke-opacity=".6"/><text x="437" y="{{ $fy + 12 }}" font-size="8" font-weight="800" text-anchor="middle" fill="{{ $vs === 'Offshore' ? '#7dd3fc' : '#fcd34d' }}">{{ strtoupper($vs) }}</text>
    <circle cx="486" cy="{{ $fy + 9 }}" r="3.4" fill="{{ $vc }}" class="pulse" style="animation-delay:-{{ $i * .5 }}s"/><text x="494" y="{{ $fy + 12 }}" font-size="9" font-weight="700" fill="{{ $vc }}">{{ $vst }}</text>
    @endforeach
</svg>
