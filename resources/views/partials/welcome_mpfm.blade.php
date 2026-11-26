{{-- MPFM: raw log -> parsed -> validated -> approved, plus the trend and recent tests (decorative, animated inline SVG) --}}
@php
    $sx = [12, 162, 312, 462]; $sw = 128;
    $logLines = [['OIL', '3280.4 B/D'], ['GAS', '1.92 MMscf'], ['WC', '18.2 %'], ['P', '1240 psi'], ['T', '74.1 C'], ['dP', '212 mbar'], ['GOR', '640 scf'], ['CHK', '48/64']];
    $parsed = [['Oil', '3,280 B/D', '#22d3ee'], ['Gas', '1.92 MMscf/D', '#fbbf24'], ['Water cut', '18.2 %', '#38bdf8'], ['GOR', '640 scf/bbl', '#a78bfa'], ['WHP', '1,240 psi', '#f87171'], ['Temp', '74.1 °C', '#fb923c']];
    $rules = ['Stable flow for 2 h', 'Water cut 0 to 100 %', 'GOR within ±25 % of history', 'dP and temperature in range', 'Data complete (98 %)'];
    $series = [
        ['oil', '#22d3ee', [3050, 3120, 3180, 3150, 3240, 3280, 3330, 3300, 3360, 3410], 3000, 3500],
        ['wc',  '#38bdf8', [14, 14.5, 15, 15.8, 16.2, 16.8, 17.1, 17.6, 18, 18.2], 10, 25],
        ['gas', '#fbbf24', [1.6, 1.62, 1.7, 1.7, 1.78, 1.8, 1.86, 1.9, 1.9, 1.92], 1.4, 2.2],
    ];
    $cx0 = 44; $cx1 = 352; $cy0 = 416; $cy1 = 266;
    $pts = [];
    foreach ($series as [$k, $c, $vals, $lo, $hi]) {
        foreach ($vals as $i => $v) { $pts[$k][] = [round($cx0 + $i * ($cx1 - $cx0) / 9, 1), round($cy0 - ($v - $lo) / ($hi - $lo) * ($cy0 - $cy1), 1)]; }
    }
    $path = fn ($p) => 'M' . implode(' L', array_map(fn ($q) => $q[0] . ' ' . $q[1], $p));
    $recent = [['12 Nov', 'ZK-101', '3,280', 'Approved', '#34d399'], ['10 Nov', 'ZK-115', '2,940', 'In review', '#fbbf24'], ['08 Nov', 'ZK-087', '1,860', 'Approved', '#34d399'], ['05 Nov', 'ZK-101', '3,350', 'Rejected', '#f87171']];
@endphp
<svg viewBox="0 0 600 470" role="img" aria-label="An MPFM log is uploaded, parsed, validated against rules and reviewed; below, the oil, water cut and gas trend and the latest tests with their review status">
    <defs>
        <clipPath id="mpLog"><rect x="{{ $sx[0] + 8 }}" y="62" width="{{ $sw - 16 }}" height="96" rx="6"/></clipPath>
        <linearGradient id="mpArea" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#22d3ee" stop-opacity=".28"/><stop offset="1" stop-color="#22d3ee" stop-opacity="0"/></linearGradient>
    </defs>

    <!-- stage connectors -->
    @foreach([0, 1, 2] as $i)
    <path d="M{{ $sx[$i] + $sw }} 102 H{{ $sx[$i + 1] }}" stroke="#3b82f6" stroke-width="1.8" class="flowd" fill="none"/>
    <circle r="3" fill="#22d3ee" opacity="0"><animateMotion dur="2.6s" begin="{{ $i * .5 }}s" repeatCount="indefinite" path="M{{ $sx[$i] + $sw }} 102 H{{ $sx[$i + 1] }}"/><animate attributeName="opacity" values="0;1;1;0" keyTimes="0;.1;.9;1" dur="2.6s" begin="{{ $i * .5 }}s" repeatCount="indefinite"/></circle>
    @endforeach

    <!-- stage cards -->
    @foreach([['UPLOAD', '#22d3ee'], ['PARSE', '#3b82f6'], ['VALIDATE', '#fbbf24'], ['REVIEW', '#a78bfa']] as $i => [$name, $col])
    <rect x="{{ $sx[$i] }}" y="14" width="{{ $sw }}" height="176" rx="14" fill="#0d1730" stroke="{{ $col }}" stroke-opacity=".6"/>
    <circle cx="{{ $sx[$i] + 16 }}" cy="32" r="8" fill="{{ $col }}" fill-opacity=".18" stroke="{{ $col }}"/><text x="{{ $sx[$i] + 16 }}" y="35.5" font-size="9.5" font-weight="800" text-anchor="middle" fill="{{ $col }}">{{ $i + 1 }}</text>
    <text x="{{ $sx[$i] + 31 }}" y="35.5" font-size="10" font-weight="800" letter-spacing="1.4" fill="{{ $col }}">{{ $name }}</text>
    @endforeach

    <!-- 1 upload: scrolling raw log -->
    <text x="{{ $sx[0] + 12 }}" y="54" font-size="8.5" fill="#9aaccb">ZK-101_12Nov.vx · 2.4 MB</text>
    <rect x="{{ $sx[0] + 8 }}" y="62" width="{{ $sw - 16 }}" height="96" rx="6" fill="#060d1f" stroke="#1f2f55"/>
    <g clip-path="url(#mpLog)"><g>
        <animateTransform attributeName="transform" type="translate" from="0 0" to="0 -{{ count($logLines) * 13 }}" dur="7s" repeatCount="indefinite"/>
        @foreach(array_merge($logLines, $logLines) as $i => [$lk, $lv])
        <text x="{{ $sx[0] + 14 }}" y="{{ 76 + $i * 13 }}" font-size="8.2" font-family="ui-monospace,Menlo,Consolas,monospace" fill="#6b86b8">12:{{ 4 + intdiv($i, 3) }}:{{ 11 + ($i * 7) % 40 }} <tspan fill="#22d3ee" font-weight="700">{{ $lk }}</tspan> {{ $lv }}</text>
        @endforeach
    </g></g>
    <rect x="{{ $sx[0] + 8 }}" y="166" width="{{ $sw - 16 }}" height="5" rx="2.5" fill="#1a2a50"/>
    <rect x="{{ $sx[0] + 8 }}" y="166" width="0" height="5" rx="2.5" fill="#22d3ee"><animate attributeName="width" values="0;{{ $sw - 16 }};{{ $sw - 16 }}" keyTimes="0;.5;1" dur="5s" repeatCount="indefinite"/></rect>

    <!-- 2 parse: extracted fields -->
    <rect x="{{ $sx[1] + 8 }}" y="46" width="{{ $sw - 16 }}" height="16" rx="5" fill="#3b82f6" fill-opacity=".16"><animate attributeName="y" values="48;66;84;102;120;138;48" keyTimes="0;.16;.33;.5;.66;.83;1" calcMode="discrete" dur="6s" repeatCount="indefinite"/></rect>
    @foreach($parsed as $i => [$pl, $pv, $pc])
    <text x="{{ $sx[1] + 14 }}" y="{{ 59 + $i * 18 }}" font-size="9" fill="#9aaccb">{{ $pl }}</text>
    <text x="{{ $sx[1] + $sw - 12 }}" y="{{ 59 + $i * 18 }}" font-size="9.5" font-weight="800" text-anchor="end" fill="{{ $pc }}">{{ $pv }}</text>
    @endforeach
    <text x="{{ $sx[1] + 14 }}" y="176" font-size="8.5" fill="#64748b">6 fields from 1,440 samples</text>

    <!-- 3 validate: rules tick off one by one + quality score -->
    @foreach($rules as $i => $rule)
    <path d="M{{ $sx[2] + 14 }} {{ 62 + $i * 17 }} l3.5 3.5 6.5 -7" fill="none" stroke="#34d399" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="16" stroke-dashoffset="16"><animate attributeName="stroke-dashoffset" values="16;16;0;0;16" keyTimes="0;{{ .05 + $i * .12 }};{{ .12 + $i * .12 }};.92;1" dur="8s" repeatCount="indefinite"/></path>
    <text x="{{ $sx[2] + 30 }}" y="{{ 61 + $i * 17 }}" font-size="8.2" fill="#cfe0ff">{{ $rule }}</text>
    @endforeach
    <g transform="translate({{ $sx[2] + 30 }},162)"><circle r="13" fill="none" stroke="#1f2f55" stroke-width="4"/><circle r="13" fill="none" stroke="#fbbf24" stroke-width="4" stroke-linecap="round" stroke-dasharray="78.5" transform="rotate(-90)"><animate attributeName="stroke-dashoffset" values="78.5;3;3;78.5" keyTimes="0;.5;.92;1" dur="8s" repeatCount="indefinite"/></circle><text y="3.5" font-size="9.5" font-weight="800" text-anchor="middle" fill="#fff">96</text></g>
    <text x="{{ $sx[2] + 52 }}" y="160" font-size="9" font-weight="800" fill="#fbbf24">Quality score</text><text x="{{ $sx[2] + 52 }}" y="172" font-size="8" fill="#64748b">all 5 rules passed</text>

    <!-- 4 review: status moves from draft to approved -->
    @foreach([['Draft', '#94a3b8', 0, .3], ['Submitted', '#fbbf24', .3, .6], ['Approved', '#34d399', .6, 1]] as [$st, $stc, $a, $b])
    <g opacity="0"><animate attributeName="opacity" values="0;0;1;1;0;0" keyTimes="0;{{ $a }};{{ $a + .03 }};{{ $b - .03 }};{{ $b }};1" dur="9s" repeatCount="indefinite"/>
        <rect x="{{ $sx[3] + 14 }}" y="50" width="100" height="22" rx="11" fill="{{ $stc }}" fill-opacity=".16" stroke="{{ $stc }}"/>
        <text x="{{ $sx[3] + 64 }}" y="65" font-size="10.5" font-weight="800" text-anchor="middle" fill="{{ $stc }}">{{ $st }}</text></g>
    @endforeach
    <circle cx="{{ $sx[3] + 26 }}" cy="104" r="12" fill="#a78bfa" fill-opacity=".2" stroke="#a78bfa"/><text x="{{ $sx[3] + 26 }}" y="107.5" font-size="8.5" font-weight="800" text-anchor="middle" fill="#c4b5fd">SUP</text>
    <text x="{{ $sx[3] + 46 }}" y="102" font-size="9.5" font-weight="700" fill="#fff">Supervisor</text><text x="{{ $sx[3] + 46 }}" y="114" font-size="8.2" fill="#64748b">09:15 · 13 Nov</text>
    <text x="{{ $sx[3] + 14 }}" y="138" font-size="8.8" fill="#9aaccb">Vessel delta</text><text x="{{ $sx[3] + $sw - 12 }}" y="138" font-size="9.5" font-weight="800" text-anchor="end" fill="#34d399">-2.4 %</text>
    <text x="{{ $sx[3] + 14 }}" y="156" font-size="8.8" fill="#9aaccb">Comment</text><text x="{{ $sx[3] + $sw - 12 }}" y="156" font-size="9" text-anchor="end" fill="#cfe0ff">within ±5 %</text>
    <path d="M{{ $sx[3] + 14 }} 170 l5 5 9 -10" fill="none" stroke="#34d399" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="22" stroke-dashoffset="22"><animate attributeName="stroke-dashoffset" values="22;22;0;0;22" keyTimes="0;.62;.7;.97;1" dur="9s" repeatCount="indefinite"/></path>
    <text x="{{ $sx[3] + 36 }}" y="174" font-size="9" font-weight="800" fill="#34d399" opacity="0"><animate attributeName="opacity" values="0;0;1;1;0" keyTimes="0;.62;.7;.97;1" dur="9s" repeatCount="indefinite"/>Counted in reports</text>

    <!-- trend panel -->
    <rect x="12" y="206" width="576" height="252" rx="16" fill="rgba(7,16,38,.7)" stroke="#2a3b66"/>
    <text x="30" y="232" font-size="10" font-weight="800" letter-spacing="1.6" fill="#22d3ee">TREND · ZK-101</text>
    <g font-size="9" fill="#9aaccb"><circle cx="150" cy="229" r="3.6" fill="#22d3ee"/><text x="158" y="232">Oil</text><circle cx="186" cy="229" r="3.6" fill="#38bdf8"/><text x="194" y="232">Water cut</text><circle cx="252" cy="229" r="3.6" fill="#fbbf24"/><text x="260" y="232">Gas</text></g>
    <g stroke="#2a3b66" stroke-width="1">@foreach([0, 1, 2, 3] as $g)<path d="M{{ $cx0 }} {{ $cy1 + $g * 50 }} H{{ $cx1 }}"/>@endforeach</g>
    <path d="{{ $path($pts['oil']) }} V{{ $cy0 }} H{{ $cx0 }}Z" fill="url(#mpArea)" stroke="none"/>
    @foreach($series as [$k, $c])
    <path d="{{ $path($pts[$k]) }}" fill="none" stroke="{{ $c }}" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" pathLength="1" stroke-dasharray="1" stroke-dashoffset="1"><animate attributeName="stroke-dashoffset" values="1;0;0;1" keyTimes="0;.4;.9;1" dur="9s" repeatCount="indefinite"/></path>
    @endforeach
    <g fill="#0d1730" stroke="#22d3ee" stroke-width="1.6">@foreach($pts['oil'] as $i => $p)@if($i !== 3)<circle cx="{{ $p[0] }}" cy="{{ $p[1] }}" r="3"/>@endif @endforeach</g>
    <circle cx="{{ $pts['oil'][3][0] }}" cy="{{ $pts['oil'][3][1] }}" r="4.2" fill="#f87171"/><circle cx="{{ $pts['oil'][3][0] }}" cy="{{ $pts['oil'][3][1] }}" r="4.2" fill="none" stroke="#f87171"><animate attributeName="r" values="4;13" dur="2s" repeatCount="indefinite"/><animate attributeName="opacity" values=".9;0" dur="2s" repeatCount="indefinite"/></circle>
    <g transform="translate({{ $pts['oil'][3][0] - 4 }},{{ $pts['oil'][3][1] + 22 }})"><rect x="-4" y="-11" width="104" height="17" rx="8.5" fill="#2a1216" stroke="#f87171"/><text x="48" y="1" font-size="8.5" font-weight="700" text-anchor="middle" fill="#fca5a5">rejected: WC out of range</text></g>
    <circle cx="{{ $pts['oil'][9][0] }}" cy="{{ $pts['oil'][9][1] }}" r="5" fill="#22d3ee"/><circle cx="{{ $pts['oil'][9][0] }}" cy="{{ $pts['oil'][9][1] }}" r="5" fill="none" stroke="#22d3ee"><animate attributeName="r" values="5;15" dur="2s" repeatCount="indefinite"/><animate attributeName="opacity" values=".9;0" dur="2s" repeatCount="indefinite"/></circle>
    <g font-size="8.5" fill="#64748b" text-anchor="middle">@foreach([0, 3, 6, 9] as $i)<text x="{{ $pts['oil'][$i][0] }}" y="436">{{ ['01 Sep', '', '', '14 Oct', '', '', '28 Oct', '', '', '12 Nov'][$i] }}</text>@endforeach</g>

    <!-- recent tests -->
    <text x="384" y="232" font-size="10" font-weight="800" letter-spacing="1.6" fill="#a78bfa">RECENT TESTS</text>
    @foreach($recent as $i => [$rd, $rw, $ro, $rs, $rc])
    @php $ry = 244 + $i * 34; @endphp
    <rect x="378" y="{{ $ry }}" width="196" height="28" rx="9" fill="#0d1730" stroke="#2a3b66"/>
    <text x="388" y="{{ $ry + 12 }}" font-size="9.5" font-weight="700" fill="#fff">{{ $rw }}</text><text x="388" y="{{ $ry + 23 }}" font-size="8" fill="#64748b">{{ $rd }} · {{ $ro }} B/D</text>
    <rect x="496" y="{{ $ry + 7 }}" width="68" height="15" rx="7.5" fill="{{ $rc }}" fill-opacity=".14" stroke="{{ $rc }}" stroke-opacity=".6"/><text x="530" y="{{ $ry + 17.5 }}" font-size="8.5" font-weight="800" text-anchor="middle" fill="{{ $rc }}">{{ $rs }}</text>
    @endforeach
    <rect x="378" y="386" width="196" height="54" rx="10" fill="#f87171" fill-opacity=".08" stroke="#f87171" stroke-opacity=".4"/>
    <path d="M392 404 l6 -10 6 10z" fill="none" stroke="#fca5a5" stroke-width="1.6" stroke-linejoin="round"/><path d="M398 399 V402" stroke="#fca5a5" stroke-width="1.6" stroke-linecap="round"/>
    <text x="412" y="402" font-size="9.5" font-weight="800" fill="#fca5a5">Trend alert</text><text x="390" y="420" font-size="8.8" fill="#e8c6c6">Water cut +0.6 pt/month</text><text x="390" y="432" font-size="8.2" fill="#9aaccb">flagged for engineer review</text>
</svg>
