{{-- Well Analysis: health score and its factors, decline forecast, reservoir pressure, ranked recommendations (decorative, animated inline SVG) --}}
@php
    $circ = 2 * M_PI * 54;
    $factors = [
        ['Oil decline 9.4 %/yr',           8, '#fbbf24', 'MPFM'],
        ['Water cut rising +0.6 pt/month', 7, '#f87171', 'MPFM'],
        ['Last pressure survey 38 days ago', 3, '#fbbf24', 'WIRE LINE'],
        ['Meters agree within 2.4 %',      0, '#34d399', 'VESSEL'],
        ['Well integrity: Green',          0, '#2dd4bf', 'INTEGRITY'],
    ];
    $hist = [3480, 3440, 3430, 3390, 3350, 3340, 3300, 3270, 3250, 3210];
    $oy = fn ($v) => round(324 - ($v - 2900) / 650 * 80, 1);
    $hp = [];
    foreach ($hist as $i => $v) { $hp[] = [28 + $i * 18, $oy($v)]; }
    $line = fn ($p) => 'M' . implode(' L', array_map(fn ($q) => $q[0] . ' ' . $q[1], $p));
    $py = fn ($v) => round(324 - ($v - 2300) / 500 * 80, 1);
    $sv = [[336, 2680], [404, 2600], [472, 2540], [526, 2497]];
    $recs = [
        ['HIGH', '#f87171', 'Investigate oil-rate decline', 'MPFM', '#22d3ee'],
        ['MEDIUM', '#fbbf24', 'Water cut needs attention: run a gradient survey', 'WIRE LINE', '#a78bfa'],
        ['MEDIUM', '#fbbf24', 'SCSSV function test due in 12 days', 'INTEGRITY', '#2dd4bf'],
        ['LOW', '#60a5fa', 'Gauge calibration older than 12 months', 'WIRE LINE', '#a78bfa'],
    ];
@endphp
<svg viewBox="0 0 600 480" role="img" aria-label="A well health score of 82 with the factors that affect it, an oil decline forecast, a reservoir pressure trend and ranked recommendations tagged by their source module">
    <defs>
        <linearGradient id="anBand" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="#fbbf24" stop-opacity=".08"/><stop offset="1" stop-color="#fbbf24" stop-opacity=".26"/></linearGradient>
        <linearGradient id="anArea" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#22d3ee" stop-opacity=".25"/><stop offset="1" stop-color="#22d3ee" stop-opacity="0"/></linearGradient>
    </defs>

    <!-- health ring -->
    <rect x="12" y="12" width="188" height="184" rx="16" fill="rgba(7,16,38,.7)" stroke="#2a3b66"/>
    <text x="28" y="34" font-size="10" font-weight="800" letter-spacing="1.6" fill="#34d399">HEALTH SCORE</text>
    <g transform="translate(106,108)">
        <circle r="54" fill="none" stroke="#1d2b4f" stroke-width="12"/>
        <circle r="54" fill="none" stroke="#34d399" stroke-width="12" stroke-linecap="round" stroke-dasharray="{{ $circ }}" transform="rotate(-90)"><animate attributeName="stroke-dashoffset" values="{{ $circ }};{{ $circ * (1 - .82) }};{{ $circ * (1 - .82) }};{{ $circ }}" keyTimes="0;.3;.9;1" dur="9s" repeatCount="indefinite"/></circle>
        <circle r="66" fill="none" stroke="#34d399" stroke-opacity=".25" class="pulse"/>
        <text y="9" font-size="34" font-weight="800" text-anchor="middle" fill="#fff">82</text><text y="26" font-size="9" letter-spacing="1.6" text-anchor="middle" fill="#9aaccb">OUT OF 100</text>
    </g>
    <rect x="70" y="172" width="72" height="18" rx="9" fill="#34d399" fill-opacity=".16" stroke="#34d399"/><text x="106" y="184.5" font-size="9.5" font-weight="800" text-anchor="middle" fill="#6ee7b7">GOOD</text>

    <!-- factors -->
    <rect x="212" y="12" width="376" height="184" rx="16" fill="rgba(7,16,38,.7)" stroke="#2a3b66"/>
    <text x="228" y="34" font-size="10" font-weight="800" letter-spacing="1.6" fill="#22d3ee">WHAT AFFECTS THE SCORE</text>
    <text x="572" y="34" font-size="9" text-anchor="end" fill="#64748b">starts at 100</text>
    @foreach($factors as $i => [$fl, $fd, $fc, $fs])
    @php $fy = 48 + $i * 29; @endphp
    <text x="228" y="{{ $fy + 11 }}" font-size="9.8" font-weight="600" fill="#e8eefc">{{ $fl }}</text>
    <rect x="228" y="{{ $fy + 17 }}" width="{{ 120 }}" height="5" rx="2.5" fill="#1d2b4f"/>
    <rect x="228" y="{{ $fy + 17 }}" width="0" height="5" rx="2.5" fill="{{ $fc }}"><animate attributeName="width" values="0;{{ max(4, $fd * 15) }};{{ max(4, $fd * 15) }};0" keyTimes="0;.3;.9;1" dur="9s" repeatCount="indefinite"/></rect>
    <rect x="428" y="{{ $fy + 4 }}" width="62" height="15" rx="7.5" fill="{{ ['MPFM' => '#22d3ee', 'WIRE LINE' => '#a78bfa', 'VESSEL' => '#fbbf24', 'INTEGRITY' => '#2dd4bf'][$fs] }}" fill-opacity=".12" stroke="{{ ['MPFM' => '#22d3ee', 'WIRE LINE' => '#a78bfa', 'VESSEL' => '#fbbf24', 'INTEGRITY' => '#2dd4bf'][$fs] }}" stroke-opacity=".5"/>
    <text x="459" y="{{ $fy + 14.5 }}" font-size="7.6" font-weight="800" letter-spacing=".8" text-anchor="middle" fill="{{ ['MPFM' => '#67e8f9', 'WIRE LINE' => '#c4b5fd', 'VESSEL' => '#fcd34d', 'INTEGRITY' => '#5eead4'][$fs] }}">{{ $fs }}</text>
    <text x="572" y="{{ $fy + 20 }}" font-size="13" font-weight="800" text-anchor="end" fill="{{ $fd ? $fc : '#34d399' }}">{{ $fd ? '-' . $fd : '0' }}</text>
    @endforeach

    <!-- decline forecast -->
    <rect x="12" y="208" width="284" height="140" rx="16" fill="rgba(7,16,38,.7)" stroke="#2a3b66"/>
    <text x="28" y="230" font-size="10" font-weight="800" letter-spacing="1.6" fill="#22d3ee">OIL RATE · DECLINE FIT</text>
    <g stroke="#2a3b66">@foreach([0, 1, 2] as $g)<path d="M28 {{ 244 + $g * 40 }} H284"/>@endforeach</g>
    <path d="M190 {{ $oy(3210) }} L284 {{ $oy(3010) - 14 }} V{{ $oy(3010) + 14 }} L190 {{ $oy(3210) }}Z" fill="url(#anBand)"/>
    <path d="{{ $line($hp) }} V324 H28Z" fill="url(#anArea)"/>
    <path d="{{ $line($hp) }}" fill="none" stroke="#22d3ee" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" pathLength="1" stroke-dasharray="1" stroke-dashoffset="1"><animate attributeName="stroke-dashoffset" values="1;0;0;1" keyTimes="0;.35;.92;1" dur="9s" repeatCount="indefinite"/></path>
    <path d="M28 {{ $oy(3490) }} L190 {{ $oy(3205) }}" stroke="#fff" stroke-opacity=".45" stroke-dasharray="2 4" fill="none"/>
    <path d="M190 {{ $oy(3210) }} L284 {{ $oy(3010) }}" stroke="#fbbf24" stroke-width="2.2" stroke-dasharray="6 5" fill="none" class="flowd"/>
    <line x1="190" y1="240" x2="190" y2="326" stroke="#5b7bb5" stroke-dasharray="3 4"/><text x="194" y="250" font-size="8" fill="#64748b">forecast</text>
    @foreach($hp as $p)<circle cx="{{ $p[0] }}" cy="{{ $p[1] }}" r="2.4" fill="#0d1730" stroke="#22d3ee" stroke-width="1.4"/>@endforeach
    <rect x="198" y="{{ $oy(3010) - 40 }}" width="84" height="16" rx="8" fill="#17142e" stroke="#fbbf24" stroke-opacity=".7"/><text x="240" y="{{ $oy(3010) - 28.5 }}" font-size="8.5" font-weight="800" text-anchor="middle" fill="#fcd34d">180 d: 3,010 B/D</text>
    <text x="28" y="340" font-size="8.5" fill="#64748b">fit on the last 10 tests · R² 0.97</text>

    <!-- reservoir pressure -->
    <rect x="304" y="208" width="284" height="140" rx="16" fill="rgba(7,16,38,.7)" stroke="#2a3b66"/>
    <text x="320" y="230" font-size="10" font-weight="800" letter-spacing="1.6" fill="#a78bfa">RESERVOIR PRESSURE · BHP</text>
    <g stroke="#2a3b66">@foreach([0, 1, 2] as $g)<path d="M320 {{ 244 + $g * 40 }} H574"/>@endforeach</g>
    <path d="{{ $line(array_map(fn ($s) => [$s[0], $py($s[1])], $sv)) }}" fill="none" stroke="#a78bfa" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" pathLength="1" stroke-dasharray="1" stroke-dashoffset="1"><animate attributeName="stroke-dashoffset" values="1;0;0;1" keyTimes="0;.35;.92;1" dur="9s" repeatCount="indefinite"/></path>
    <path d="M526 {{ $py(2497) }} L574 {{ $py(2410) }}" stroke="#a78bfa" stroke-width="2" stroke-dasharray="5 4" fill="none" class="flowd"/>
    @foreach($sv as $s)<circle cx="{{ $s[0] }}" cy="{{ $py($s[1]) }}" r="4" fill="#0d1730" stroke="#a78bfa" stroke-width="2"/>@endforeach
    <circle cx="526" cy="{{ $py(2497) }}" r="4" fill="#a78bfa"/><circle cx="526" cy="{{ $py(2497) }}" r="4" fill="none" stroke="#a78bfa"><animate attributeName="r" values="4;14" dur="2s" repeatCount="indefinite"/><animate attributeName="opacity" values=".9;0" dur="2s" repeatCount="indefinite"/></circle>
    <text x="526" y="{{ $py(2497) - 12 }}" font-size="9" font-weight="800" text-anchor="middle" fill="#c4b5fd">2,497 psi</text>
    <g font-size="8" fill="#64748b" text-anchor="middle"><text x="336" y="338">2023</text><text x="404" y="338">2024</text><text x="472" y="338">2025</text><text x="526" y="338">2026</text></g>
    <rect x="320" y="302" width="156" height="16" rx="8" fill="#17142e" stroke="#a78bfa" stroke-opacity=".6"/><text x="398" y="313.5" font-size="8.5" font-weight="800" text-anchor="middle" fill="#c4b5fd">depletion 3.1 % · -14 psi/mo</text>

    <!-- recommendations -->
    <rect x="12" y="360" width="576" height="108" rx="16" fill="rgba(7,16,38,.7)" stroke="#2a3b66"/>
    <text x="28" y="379" font-size="10" font-weight="800" letter-spacing="1.6" fill="#fbbf24">RANKED RECOMMENDATIONS</text>
    <text x="572" y="379" font-size="9" text-anchor="end" fill="#64748b">from every data source</text>
    @foreach($recs as $i => [$sev, $sc, $title, $src, $srcc])
    @php $ry = 386 + $i * 20; @endphp
    <g opacity="0"><animate attributeName="opacity" values="0;0;1;1;0" keyTimes="0;{{ .04 + $i * .1 }};{{ .1 + $i * .1 }};.94;1" dur="9s" repeatCount="indefinite"/>
        <rect x="24" y="{{ $ry }}" width="552" height="17" rx="8.5" fill="#0d1730" stroke="#2a3b66"/><rect x="24" y="{{ $ry + 3 }}" width="3" height="11" rx="1.5" fill="{{ $sc }}"/>
        <rect x="34" y="{{ $ry + 2.5 }}" width="48" height="12" rx="6" fill="{{ $sc }}" fill-opacity=".16" stroke="{{ $sc }}" stroke-opacity=".6"/><text x="58" y="{{ $ry + 11.4 }}" font-size="7.4" font-weight="800" letter-spacing=".6" text-anchor="middle" fill="{{ $sc }}">{{ $sev }}</text>
        <text x="92" y="{{ $ry + 12 }}" font-size="9.6" font-weight="600" fill="#e8eefc">{{ $title }}</text>
        <rect x="496" y="{{ $ry + 2.5 }}" width="70" height="12" rx="6" fill="{{ $srcc }}" fill-opacity=".12" stroke="{{ $srcc }}" stroke-opacity=".5"/><text x="531" y="{{ $ry + 11.4 }}" font-size="7.4" font-weight="800" letter-spacing=".8" text-anchor="middle" fill="{{ $srcc }}">{{ $src }}</text>
    </g>
    @endforeach
</svg>
