{{-- "Everything starts at the well": hub-and-spoke diagram + well timeline (decorative, animated inline SVG) --}}
@php
    $hx = 500; $hy = 228;
    // title, sub, live metric, colour, centre, flow direction, 24x24 icon paths
    $mods = [
        ['Well Analysis',  'Health, forecast, advice',   'Score 82 / 100',   '#34d399', 500, 62,  'out', 'M4 20V9M10 20V4M16 20v-8M22 20H2'],
        ['Well Integrity', 'Barriers, annuli, checks',   'Status: Green',    '#2dd4bf', 781, 124, 'in',  'M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6z M9 12l2 2 4-4'],
        ['Governance',     'Roles, review, audit trail', '8 role profiles',  '#f87171', 851, 270, 'out', 'M5 11h14v9H5z M8 11V8a4 4 0 0 1 8 0v3'],
        ['Field Setup',    'Field › Tower › Well',       '42 wells',         '#38bdf8', 656, 386, 'in',  'M12 21s-6-5.3-6-10a6 6 0 0 1 12 0c0 4.7-6 10-6 10z M12 8.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5z'],
        ['Wire Line',      'Memory-gauge surveys',       'BHP 2,497 psi',    '#a78bfa', 344, 386, 'in',  'M12 3v18M8 7l4-4 4 4M12 12.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5z'],
        ['Well Testing',   'Offshore and onshore tests', 'Oil 3,280 B/D',    '#fbbf24', 149, 270, 'in',  'M7 8h10a4 4 0 0 1 0 8H7a4 4 0 0 1 0-8z M7 8V5M17 8V5'],
        ['MPFM',           'Log upload and validation',  'Δ vs vessel -2.4%', '#22d3ee', 219, 124, 'in',  'M3 12h4l3-8 4 16 3-8h4'],
    ];
    $cw = 172; $ch = 66;
    $geo = [];
    foreach ($mods as $i => $m) {
        [$t, $sub, $met, $col, $cx, $cy, $dir, $icon] = $m;
        if (abs($cx - $hx) < 100) { $sx = $cx; $sy = $cy + ($cy < $hy ? $ch / 2 : -$ch / 2); $vertical = true; }
        else { $sx = $cx + ($cx < $hx ? $cw / 2 : -$cw / 2); $sy = $cy; $vertical = false; }
        $dx = $sx - $hx; $dy = $sy - $hy; $len = sqrt($dx * $dx + $dy * $dy);
        $ex = round($hx + 63 * $dx / $len, 1); $ey = round($hy + 63 * $dy / $len, 1);
        $qx = $vertical ? $sx : $ex; $qy = $vertical ? $ey : $sy;
        $geo[$i] = ['in' => "M$sx $sy Q$qx $qy $ex $ey", 'out' => "M$ex $ey Q$qx $qy $sx $sy"];
    }
    $events = [
        [286, '#22d3ee', '12 Nov', 'MPFM test', 1], [372, '#a78bfa', '14 Nov', 'BHCIP survey', 0], [458, '#2dd4bf', '20 Nov', 'Wellhead check', 1],
        [544, '#fbbf24', '24 Nov', 'Vessel test', 0], [630, '#f87171', '02 Dec', 'Anomaly raised', 1], [716, '#34d399', '09 Dec', 'SCSSV test: pass', 0], [802, '#38bdf8', '15 Dec', 'Analysis refreshed', 1],
    ];
@endphp
<svg viewBox="0 0 1000 548" role="img" aria-label="Seven modules (MPFM, Well Testing, Wire Line, Well Integrity, Well Analysis, Field Setup, Governance) exchange data with a single well record, and a timeline below lists everything that happened on the well">
    <defs>
        <radialGradient id="plHub" cx=".5" cy=".35" r=".8"><stop offset="0" stop-color="#2563eb"/><stop offset=".6" stop-color="#0f2a63"/><stop offset="1" stop-color="#07112b"/></radialGradient>
        <radialGradient id="plGlow" cx=".5" cy=".5" r=".5"><stop offset="0" stop-color="#3b82f6" stop-opacity=".38"/><stop offset="1" stop-color="#3b82f6" stop-opacity="0"/></radialGradient>
        <linearGradient id="plOil" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#fde68a"/><stop offset="1" stop-color="#f97316"/></linearGradient>
        <linearGradient id="plLine" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="#22d3ee" stop-opacity=".1"/><stop offset=".15" stop-color="#22d3ee"/><stop offset=".85" stop-color="#a78bfa"/><stop offset="1" stop-color="#a78bfa" stop-opacity=".1"/></linearGradient>
        <pattern id="plDots" width="26" height="26" patternUnits="userSpaceOnUse"><circle cx="2" cy="2" r="1" fill="#5b7bb5" opacity=".28"/></pattern>
        <clipPath id="plHubClip"><circle cx="{{ $hx }}" cy="{{ $hy }}" r="62"/></clipPath>
    </defs>

    <rect width="1000" height="548" fill="url(#plDots)"/>
    <circle cx="{{ $hx }}" cy="{{ $hy }}" r="190" fill="url(#plGlow)"/>

    <!-- field hierarchy -->
    <g font-size="10.5" font-weight="700" text-anchor="middle">
        @foreach(['Field', 'Tower', 'Well', 'String'] as $i => $h)
        @php $x = 24 + $i * 82; $on = $h === 'Well'; @endphp
        <rect x="{{ $x }}" y="16" width="64" height="22" rx="11" fill="{{ $on ? 'rgba(34,211,238,.18)' : 'rgba(13,23,48,.9)' }}" stroke="{{ $on ? '#22d3ee' : '#3b4f7a' }}"/>
        <text x="{{ $x + 32 }}" y="31" fill="{{ $on ? '#a5f3fc' : '#9aaccb' }}">{{ $h }}</text>
        @if($i < 3)<path d="M{{ $x + 68 }} 27 l6 0 m-3 -3 l3 3 -3 3" stroke="#5b7bb5" stroke-width="1.4" fill="none" stroke-linecap="round"/>@endif
        @endforeach
    </g>

    <!-- links (data in: module to well, decisions out: well to module) -->
    <g fill="none" stroke-linecap="round">
        @foreach($mods as $i => $m)
        @php $d = $geo[$i][$m[6]]; @endphp
        <path d="{{ $d }}" stroke="{{ $m[3] }}" stroke-opacity=".18" stroke-width="6"/>
        <path d="{{ $d }}" stroke="{{ $m[3] }}" stroke-width="1.6" class="flowd"/>
        @endforeach
    </g>
    @foreach($mods as $i => $m)
    @php $d = $geo[$i][$m[6]]; @endphp
    <path id="plP{{ $i }}" d="{{ $d }}" fill="none" stroke="none"/>
    @foreach([0, 2.2] as $k)
    <circle r="3.4" fill="{{ $m[3] }}" opacity="0"><animateMotion dur="4.4s" begin="{{ $k + $i * .35 }}s" repeatCount="indefinite"><mpath href="#plP{{ $i }}"/></animateMotion><animate attributeName="opacity" values="0;1;1;0" keyTimes="0;.1;.9;1" dur="4.4s" begin="{{ $k + $i * .35 }}s" repeatCount="indefinite"/></circle>
    @endforeach
    @endforeach

    <!-- hub: rings -->
    <circle cx="{{ $hx }}" cy="{{ $hy }}" r="118" fill="none" stroke="#5b7bb5" stroke-opacity=".5" stroke-dasharray="1 7" stroke-linecap="round" stroke-width="2"><animateTransform attributeName="transform" type="rotate" from="360 {{ $hx }} {{ $hy }}" to="0 {{ $hx }} {{ $hy }}" dur="60s" repeatCount="indefinite"/></circle>
    <circle cx="{{ $hx }}" cy="{{ $hy }}" r="100" fill="none" stroke="#3b82f6" stroke-opacity=".3" class="pulse"/>
    <circle cx="{{ $hx }}" cy="{{ $hy }}" r="82" fill="none" stroke="url(#plLine)" stroke-width="2.4" stroke-dasharray="62 20 10 20" stroke-linecap="round"><animateTransform attributeName="transform" type="rotate" from="0 {{ $hx }} {{ $hy }}" to="360 {{ $hx }} {{ $hy }}" dur="22s" repeatCount="indefinite"/></circle>
    @foreach($mods as $i => $m)
    <circle r="3" fill="{{ $m[3] }}"><animateMotion dur="{{ 14 + $i * 2 }}s" begin="-{{ $i * 2 }}s" repeatCount="indefinite" path="M{{ $hx + 100 }} {{ $hy }} A100 100 0 1 {{ $i % 2 }} {{ $hx - 100 }} {{ $hy }} A100 100 0 1 {{ $i % 2 }} {{ $hx + 100 }} {{ $hy }}"/></circle>
    @endforeach

    <!-- hub: well -->
    <circle cx="{{ $hx }}" cy="{{ $hy }}" r="62" fill="url(#plHub)" stroke="#3b82f6" stroke-width="2"/>
    <g clip-path="url(#plHubClip)">
        <path d="M-10 250 q10 -5 20 0 t20 0 t20 0 t20 0 t20 0 t20 0 t20 0 t20 0 t20 0 t20 0 t20 0 t20 0 t20 0 t20 0 t20 0 t20 0 t20 0 t20 0 t20 0 t20 0 t20 0 t20 0 t20 0 t20 0 t20 0" transform="translate(440,{{ $hy - 228 + 24 }})" fill="none" stroke="url(#plOil)" stroke-width="3" stroke-linecap="round"><animateTransform attributeName="transform" type="translate" from="440 {{ $hy - 198 }}" to="460 {{ $hy - 198 }}" dur="3s" repeatCount="indefinite"/></path>
        <circle cx="{{ $hx }}" cy="{{ $hy + 26 }}" r="6" fill="none" stroke="#22d3ee" stroke-width="1.4"><animate attributeName="r" values="6;40" dur="3s" repeatCount="indefinite"/><animate attributeName="opacity" values=".8;0" dur="3s" repeatCount="indefinite"/></circle>
    </g>
    <g fill="none" stroke-linecap="round" stroke-linejoin="round">
        <path d="M{{ $hx }} {{ $hy - 50 }} L{{ $hx - 21 }} {{ $hy + 24 }} M{{ $hx }} {{ $hy - 50 }} L{{ $hx + 21 }} {{ $hy + 24 }} M{{ $hx - 32 }} {{ $hy + 24 }} H{{ $hx + 32 }}" stroke="#eaf6ff" stroke-width="2.6"/>
        <path d="M{{ $hx - 9.5 }} {{ $hy - 12 }} H{{ $hx + 9.5 }} M{{ $hx - 15 }} {{ $hy + 8 }} H{{ $hx + 15 }}" stroke="#eaf6ff" stroke-width="1.8"/>
        <path d="M{{ $hx - 9.5 }} {{ $hy - 12 }} L{{ $hx + 15 }} {{ $hy + 8 }} M{{ $hx + 9.5 }} {{ $hy - 12 }} L{{ $hx - 15 }} {{ $hy + 8 }}" stroke="#22d3ee" stroke-width="1.3"/>
    </g>
    <rect x="{{ $hx - 4.5 }}" y="{{ $hy - 56 }}" width="9" height="6" rx="1.6" fill="#eaf6ff"/>
    <path d="M{{ $hx }} {{ $hy - 34 }} C{{ $hx }} {{ $hy - 34 }} {{ $hx - 5.4 }} {{ $hy - 27.6 }} {{ $hx - 5.4 }} {{ $hy - 23.8 }} a5.4 5.4 0 0 0 10.8 0 C{{ $hx + 5.4 }} {{ $hy - 27.6 }} {{ $hx }} {{ $hy - 34 }} {{ $hx }} {{ $hy - 34 }}Z" fill="url(#plOil)">
        <animateTransform attributeName="transform" type="translate" values="0 -4;0 40;0 40" keyTimes="0;.72;1" dur="2.8s" repeatCount="indefinite"/>
        <animate attributeName="opacity" values="0;1;1;0;0" keyTimes="0;.12;.6;.74;1" dur="2.8s" repeatCount="indefinite"/>
    </path>
    <text x="{{ $hx }}" y="{{ $hy + 40 }}" text-anchor="middle" font-size="8.5" font-weight="800" letter-spacing="1.6" fill="#bcd3ff">WELL RECORD</text>

    <!-- module cards -->
    @foreach($mods as $i => $m)
    @php [$t, $sub, $met, $col, $cx, $cy, $dir, $icon] = $m; $x = $cx - $cw / 2; $y = $cy - $ch / 2; @endphp
    <g>
        <rect x="{{ $x }}" y="{{ $y }}" width="{{ $cw }}" height="{{ $ch }}" rx="14" fill="#0d1730" stroke="{{ $col }}" stroke-opacity=".65"/>
        <rect x="{{ $x }}" y="{{ $y + 14 }}" width="3.5" height="{{ $ch - 28 }}" rx="1.7" fill="{{ $col }}"/>
        <circle cx="{{ $x + 28 }}" cy="{{ $y + 25 }}" r="15" fill="{{ $col }}" fill-opacity=".14" stroke="{{ $col }}" stroke-opacity=".5"/>
        <g transform="translate({{ $x + 18 }},{{ $y + 15 }}) scale(.83)" fill="none" stroke="{{ $col }}" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $icon }}"/></g>
        <text x="{{ $x + 52 }}" y="{{ $y + 22 }}" font-size="12.5" font-weight="800" fill="#fff">{{ $t }}</text>
        <text x="{{ $x + 52 }}" y="{{ $y + 37 }}" font-size="9.5" fill="#9aaccb">{{ $sub }}</text>
        <rect x="{{ $x + 14 }}" y="{{ $y + 45 }}" width="{{ $cw - 28 }}" height="14" rx="7" fill="{{ $col }}" fill-opacity=".1"/>
        <circle cx="{{ $x + 23 }}" cy="{{ $y + 52 }}" r="2.4" fill="{{ $col }}" class="pulse" style="animation-delay:-{{ $i * .3 }}s"/>
        <text x="{{ $x + 32 }}" y="{{ $y + 55.5 }}" font-size="9.5" font-weight="700" fill="{{ $col }}">{{ $met }}</text>
        <text x="{{ $x + $cw - 20 }}" y="{{ $y + 55.5 }}" font-size="8.5" font-weight="700" text-anchor="end" fill="#64748b">{{ $dir === 'in' ? 'DATA IN ›' : 'OUT ›' }}</text>
    </g>
    @endforeach

    <!-- from the well to its timeline -->
    <path id="plDrop" d="M{{ $hx }} 348 V440" fill="none" stroke="#5b7bb5" stroke-opacity=".6" class="flowd"/>
    @foreach([0, 1.2, 2.4] as $k)<circle r="3" fill="#fde68a" opacity="0"><animateMotion dur="3.6s" begin="{{ $k }}s" repeatCount="indefinite" path="M{{ $hx }} 348 V440"/><animate attributeName="opacity" values="0;1;1;0" keyTimes="0;.1;.9;1" dur="3.6s" begin="{{ $k }}s" repeatCount="indefinite"/></circle>@endforeach

    <!-- timeline -->
    <rect x="24" y="444" width="952" height="92" rx="16" fill="rgba(7,16,38,.7)" stroke="#2a3b66"/>
    <text x="46" y="478" font-size="10" font-weight="800" letter-spacing="1.8" fill="#22d3ee">WELL TIMELINE</text>
    <text x="46" y="496" font-size="11.5" font-weight="600" fill="#e8eefc">Everything that</text><text x="46" y="511" font-size="11.5" font-weight="600" fill="#e8eefc">happened on this well</text>
    <path d="M240 490 H958" stroke="url(#plLine)" stroke-width="2.4" stroke-linecap="round"/>
    @foreach($events as $k => [$ex, $ec, $ed, $et, $up])
    <circle cx="{{ $ex }}" cy="490" r="9" fill="{{ $ec }}" fill-opacity=".16"><animate attributeName="r" values="8;13;8" dur="3.2s" begin="{{ $k * .45 }}s" repeatCount="indefinite"/></circle>
    <circle cx="{{ $ex }}" cy="490" r="5" fill="#0d1730" stroke="{{ $ec }}" stroke-width="2"/>
    <path d="M{{ $ex }} {{ $up ? 485 : 495 }} V{{ $up ? 477 : 503 }}" stroke="{{ $ec }}" stroke-opacity=".6"/>
    <text x="{{ $ex }}" y="{{ $up ? 470 : 515 }}" font-size="9.5" text-anchor="middle"><tspan font-weight="800" fill="{{ $ec }}">{{ $ed }}</tspan><tspan fill="#cfe0ff"> · {{ $et }}</tspan></text>
    @endforeach
    <g><circle cx="926" cy="490" r="6" fill="#34d399"/><circle cx="926" cy="490" r="6" fill="none" stroke="#34d399"><animate attributeName="r" values="6;18" dur="2s" repeatCount="indefinite"/><animate attributeName="opacity" values=".9;0" dur="2s" repeatCount="indefinite"/></circle><text x="926" y="515" font-size="9.5" font-weight="800" text-anchor="middle" fill="#6ee7b7">TODAY</text></g>
    <circle r="3.8" cy="490" fill="#fff"><animate attributeName="cx" values="246;918" dur="8s" repeatCount="indefinite"/><animate attributeName="opacity" values="0;1;1;0" keyTimes="0;.06;.94;1" dur="8s" repeatCount="indefinite"/></circle>
</svg>
