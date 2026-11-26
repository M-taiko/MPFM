{{-- Field Setup: field map with towers (pads), wells and flowlines, plus the Field > Tower > Well tree (decorative, animated inline SVG) --}}
@php
    $st = ['a' => ['#34d399', 'Active'], 'm' => ['#fbbf24', 'Maintenance'], 'i' => ['#64748b', 'Inactive']];
    $pads = [
        ['A', 130, 112, 'aaamai', 20],
        ['B', 312, 88, 'aaaam', 100],
        ['C', 452, 214, 'aaaaami', 60],
        ['D', 168, 226, 'aama', 140],
    ];
    $gs = [296, 170];
    $contours = [];
    foreach ([44, 82, 120, 158, 196, 234] as $k => $r) {
        $p = [];
        for ($a = 0; $a < 360; $a += 15) {
            $rad = $r * (1 + .11 * sin(deg2rad(3 * $a + $k * 40)) + .06 * cos(deg2rad(2 * $a + $k * 70)));
            $p[] = round(300 + $rad * 1.25 * cos(deg2rad($a)), 1) . ' ' . round(172 + $rad * .78 * sin(deg2rad($a)), 1);
        }
        $contours[] = 'M' . implode(' L', $p) . 'Z';
    }
    $towers = [['Tower A', 84, 'aaamai'], ['Tower B', 228, 'aaaam'], ['Tower C', 372, 'aaaaami'], ['Tower D', 516, 'aama']];
@endphp
<svg viewBox="0 0 600 472" role="img" aria-label="A field map with four towers, their wells coloured by status, flowlines to a gathering station and an export line, with the Field, Tower, Well hierarchy below">
    <defs>
        <clipPath id="fdMap"><rect x="12" y="12" width="576" height="298" rx="16"/></clipPath>
        <radialGradient id="fdGlow" cx=".5" cy=".5" r=".5"><stop offset="0" stop-color="#22d3ee" stop-opacity=".18"/><stop offset="1" stop-color="#22d3ee" stop-opacity="0"/></radialGradient>
    </defs>

    <rect x="12" y="12" width="576" height="298" rx="16" fill="#08142b" stroke="#2a3b66"/>
    <g clip-path="url(#fdMap)">
        <rect x="12" y="12" width="576" height="298" fill="url(#fdGlow)"/>
        <g fill="none" stroke="#5b7bb5" stroke-opacity=".16" stroke-width="1.2">@foreach($contours as $c)<path d="{{ $c }}"/>@endforeach</g>
        <g stroke="#5b7bb5" stroke-opacity=".08">@for($x = 36; $x < 588; $x += 36)<path d="M{{ $x }} 12 V310"/>@endfor @for($y = 36; $y < 310; $y += 36)<path d="M12 {{ $y }} H588"/>@endfor</g>
        <path d="M46 62 C120 26 250 30 330 36 C430 44 520 70 566 120 C586 170 570 232 520 268 C440 300 300 296 200 288 C110 280 56 252 40 190 C32 140 30 96 46 62Z" fill="rgba(34,211,238,.035)" stroke="#22d3ee" stroke-opacity=".45" stroke-dasharray="7 6" stroke-width="1.4"/>

        <!-- flowlines to the gathering station -->
        @foreach($pads as $pi => [$pn, $px, $py])
        @php $d = "M$px $py C" . round($px + ($gs[0] - $px) * .6) . " $py " . $gs[0] . ' ' . round($py + ($gs[1] - $py) * .6) . ' ' . $gs[0] . ' ' . $gs[1]; @endphp
        <path d="{{ $d }}" fill="none" stroke="#13264a" stroke-width="6" stroke-linecap="round"/><path d="{{ $d }}" fill="none" stroke="#22d3ee" stroke-width="1.8" class="flowd"/>
        <path id="fdF{{ $pi }}" d="{{ $d }}" fill="none" stroke="none"/>
        <circle r="3" fill="#a5f3fc" opacity="0"><animateMotion dur="3.6s" begin="{{ $pi * .8 }}s" repeatCount="indefinite"><mpath href="#fdF{{ $pi }}"/></animateMotion><animate attributeName="opacity" values="0;1;1;0" keyTimes="0;.1;.9;1" dur="3.6s" begin="{{ $pi * .8 }}s" repeatCount="indefinite"/></circle>
        @endforeach
        <path d="M296 170 C380 150 470 150 580 152" fill="none" stroke="#13264a" stroke-width="7" stroke-linecap="round"/><path d="M296 170 C380 150 470 150 580 152" fill="none" stroke="#fb923c" stroke-width="2" class="flowd"/>
        <path d="M572 146 l10 6 -10 6" fill="none" stroke="#fb923c" stroke-width="1.8" stroke-linecap="round"/>
        <text x="560" y="141" font-size="8.5" font-weight="800" letter-spacing="1.2" text-anchor="end" fill="#fdba74">EXPORT TO TERMINAL</text>

        <!-- gathering station -->
        <g transform="translate({{ $gs[0] }},{{ $gs[1] }})">
            <circle r="26" fill="none" stroke="#fb923c" stroke-opacity=".4" stroke-dasharray="3 5"><animateTransform attributeName="transform" type="rotate" from="0" to="360" dur="14s" repeatCount="indefinite"/></circle>
            <circle r="17" fill="#0d1730" stroke="#fb923c" stroke-width="1.8"/>
            <rect x="-9" y="-6" width="18" height="12" rx="6" fill="none" stroke="#fdba74" stroke-width="1.5"/><path d="M-9 0 H9" stroke="#fdba74" stroke-width="1"/>
            <text y="32" font-size="8" font-weight="800" letter-spacing="1" text-anchor="middle" fill="#fdba74">GATHERING STATION</text>
        </g>

        <!-- towers and wells -->
        @foreach($pads as $pi => [$pn, $px, $py, $ws, $ph])
        @php $n = strlen($ws); @endphp
        @foreach(str_split($ws) as $wi => $wc)
        @php $ang = deg2rad($ph + $wi * 360 / $n); $wx = round($px + 42 * cos($ang), 1); $wy = round($py + 42 * sin($ang), 1); $col = $st[$wc][0]; $sel = ($pn === 'B' && $wi === 1); @endphp
        <path d="M{{ $px }} {{ $py }} L{{ $wx }} {{ $wy }}" stroke="{{ $col }}" stroke-opacity=".5" stroke-width="1.2"/>
        <circle cx="{{ $wx }}" cy="{{ $wy }}" r="{{ $sel ? 6 : 4.6 }}" fill="#0d1730" stroke="{{ $col }}" stroke-width="2"/><circle cx="{{ $wx }}" cy="{{ $wy }}" r="1.8" fill="{{ $col }}" @if($wc === 'a') class="pulse" style="animation-delay:-{{ ($pi + $wi) * .45 }}s" @endif/>
        @if($sel)<circle cx="{{ $wx }}" cy="{{ $wy }}" r="6" fill="none" stroke="#38bdf8" stroke-width="1.6"><animate attributeName="r" values="6;20" dur="2s" repeatCount="indefinite"/><animate attributeName="opacity" values=".9;0" dur="2s" repeatCount="indefinite"/></circle>@php $selX = $wx; $selY = $wy; @endphp @endif
        @endforeach
        <rect x="{{ $px - 29 }}" y="{{ $py - 10 }}" width="58" height="20" rx="10" fill="#0d1730" stroke="#38bdf8"/><text x="{{ $px }}" y="{{ $py + 3.8 }}" font-size="9" font-weight="800" letter-spacing="1" text-anchor="middle" fill="#7dd3fc">TOWER {{ $pn }}</text>
        @endforeach

        <!-- selected well profile -->
        <path d="M{{ $selX }} {{ $selY }} L400 56" stroke="#38bdf8" stroke-opacity=".7" stroke-dasharray="3 3"/>
        <g transform="translate(388,24)"><rect width="188" height="62" rx="11" fill="rgba(7,16,38,.94)" stroke="#38bdf8" stroke-opacity=".7"/><rect x="0" y="12" width="3.5" height="38" rx="1.7" fill="#38bdf8"/>
            <text x="14" y="19" font-size="9.5" font-weight="800" fill="#fff">ZK-101</text><text x="56" y="19" font-size="8.5" fill="#7dd3fc">Tower B · oil string</text>
            <circle cx="16" cy="32" r="3.2" fill="#34d399" class="pulse"/><text x="24" y="35" font-size="9" font-weight="700" fill="#34d399">Active</text><text x="68" y="35" font-size="9" fill="#cfe0ff">Health 82 / 100</text>
            <text x="14" y="50" font-size="8.5" fill="#9aaccb">Last test 12 Nov · BHP 2,497 psi</text></g>

        <!-- legend -->
        <g font-size="8.5" fill="#9aaccb">@foreach(array_values($st) as $li => [$lc, $ln])<circle cx="{{ 30 + $li * 84 }}" cy="294" r="4" fill="none" stroke="{{ $lc }}" stroke-width="2"/><circle cx="{{ 30 + $li * 84 }}" cy="294" r="1.6" fill="{{ $lc }}"/><text x="{{ 39 + $li * 84 }}" y="297">{{ $ln }}</text>@endforeach</g>
        <text x="570" y="297" font-size="8.5" text-anchor="end" fill="#64748b">stylised field map</text>
    </g>

    <!-- hierarchy -->
    <g>
        <rect x="236" y="324" width="128" height="26" rx="13" fill="#0d1730" stroke="#22d3ee" stroke-width="1.6"/><circle cx="254" cy="337" r="3.6" fill="#22d3ee" class="pulse"/><text x="266" y="341" font-size="10.5" font-weight="800" fill="#fff">Oil Field</text>
        <text x="348" y="341" font-size="8.5" text-anchor="end" fill="#64748b">1</text>
    </g>
    @foreach($towers as $ti => [$tn, $tx, $tw])
    <path d="M300 350 V362 H{{ $tx }} V380" fill="none" stroke="#38bdf8" stroke-opacity=".6" stroke-width="1.6" class="flowd"/>
    <rect x="{{ $tx - 62 }}" y="380" width="124" height="28" rx="14" fill="#0d1730" stroke="#38bdf8"/><text x="{{ $tx - 6 }}" y="398" font-size="10" font-weight="800" text-anchor="middle" fill="#fff">{{ $tn }}</text><text x="{{ $tx + 36 }}" y="398" font-size="9" font-weight="700" text-anchor="middle" fill="#7dd3fc">{{ strlen($tw) }}</text>
    @foreach(str_split($tw) as $di => $dc)
    <path d="M{{ $tx }} 408 V418" stroke="#38bdf8" stroke-opacity=".25" @if($di) stroke-width="0" @endif/>
    <circle cx="{{ round($tx - (strlen($tw) - 1) * 6 + $di * 12, 1) }}" cy="426" r="4" fill="{{ $st[$dc][0] }}" @if($dc === 'a') class="pulse" style="animation-delay:-{{ ($ti + $di) * .4 }}s" @endif/>
    @endforeach
    @endforeach
    <g font-size="9.5" font-weight="700" text-anchor="middle">
        <rect x="104" y="442" width="392" height="22" rx="11" fill="rgba(7,16,38,.8)" stroke="#2a3b66"/>
        <text x="300" y="456.5" fill="#cfe0ff"><tspan fill="#fff" font-weight="800">22</tspan> wells · <tspan fill="#34d399" font-weight="800">16</tspan> active · <tspan fill="#fbbf24" font-weight="800">4</tspan> maintenance · <tspan fill="#94a3b8" font-weight="800">2</tspan> inactive</text>
    </g>
</svg>
