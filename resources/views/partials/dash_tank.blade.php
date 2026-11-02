{{-- Cut-away oil storage tank. Vars: $snap (oil, water, wc), optional $fill (0-100 visual level) --}}
@php
    $fill = $fill ?? 78;
    $top = 240; $liqH = 150 * $fill / 100;
    $wcv = max(0, min(100, (float) $snap['wc']));
    $wH = $wcv > 0 ? max(5, $liqH * $wcv / 100) : 0;
    $yLiq = $top - $liqH; $yW = $top - $wH;
@endphp
<svg class="dpl tank" viewBox="0 0 240 290" role="img" aria-label="Storage tank cut-away showing the oil and water split of current production">
    <defs>
        <linearGradient id="tk-shell" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="#2e3a50"/><stop offset=".3" stop-color="#8d9db6"/><stop offset=".5" stop-color="#d7e1ef"/><stop offset=".75" stop-color="#7b8ba5"/><stop offset="1" stop-color="#2a3448"/></linearGradient>
        <linearGradient id="tk-oil" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="#7a4a06"/><stop offset=".4" stop-color="#d98e0a"/><stop offset=".55" stop-color="#f6b93b"/><stop offset="1" stop-color="#6b4204"/></linearGradient>
        <linearGradient id="tk-wat" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="#0b3a68"/><stop offset=".5" stop-color="#2b8fd6"/><stop offset="1" stop-color="#0a3560"/></linearGradient>
        <linearGradient id="tk-glass" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="#0a1426" stop-opacity=".95"/><stop offset=".5" stop-color="#13243f" stop-opacity=".78"/><stop offset="1" stop-color="#0a1426" stop-opacity=".95"/></linearGradient>
        <clipPath id="tk-in"><path d="M62 78 H178 V240 H62 Z"/></clipPath>
    </defs>
    <ellipse cx="120" cy="270" rx="92" ry="9" fill="#000" opacity=".4"/>
    {{-- shell --}}
    <path d="M46 66 V250 A74 12 0 0 0 194 250 V66 Z" fill="url(#tk-shell)"/>
    {{-- cutaway window --}}
    <rect x="58" y="74" width="124" height="172" rx="6" fill="url(#tk-glass)" stroke="#cfd9e8" stroke-width="2"/>
    <g clip-path="url(#tk-in)">
        <rect x="58" y="{{ $yLiq }}" width="124" height="{{ $top - $yLiq + 8 }}" fill="url(#tk-oil)"/>
        @if($wH > 0)<rect x="58" y="{{ $yW }}" width="124" height="{{ $wH + 8 }}" fill="url(#tk-wat)"/>
        <path d="M58 {{ $yW }} H182" stroke="#8fd3ff" stroke-width="1.2" opacity=".6"/>@endif
        <g class="tk-wave" fill="none" stroke="#ffe7a3" stroke-width="1.4" opacity=".8">
            <path d="M40 {{ $yLiq }} q10 -4 20 0 t20 0 t20 0 t20 0 t20 0 t20 0 t20 0 t20 0 t20 0"/>
        </g>
        <rect x="58" y="{{ $yLiq - 2 }}" width="124" height="5" fill="#fff" opacity=".12"/>
        @foreach([[84, 0], [112, 1.2], [146, .5], [168, 1.9]] as [$bx, $bd])
            <circle cx="{{ $bx }}" cy="{{ $top }}" r="2.2" fill="#fff" opacity="0"><animate attributeName="cy" values="{{ $top - 4 }};{{ $yLiq }}" dur="4.5s" begin="{{ $bd }}s" repeatCount="indefinite"/><animate attributeName="opacity" values="0;.5;0" dur="4.5s" begin="{{ $bd }}s" repeatCount="indefinite"/></circle>
        @endforeach
        <rect x="64" y="78" width="10" height="164" fill="#fff" opacity=".07"/>
    </g>
    {{-- roof --}}
    <ellipse cx="120" cy="66" rx="74" ry="14" fill="url(#tk-shell)"/>
    <ellipse cx="120" cy="64" rx="66" ry="9" fill="#a9b8cf" opacity=".6"/>
    <rect x="112" y="44" width="16" height="14" fill="url(#tk-shell)"/><ellipse cx="120" cy="44" rx="8" ry="3" fill="#cfd9e8"/>
    <path d="M60 62 q60 -18 120 0" stroke="#566580" stroke-width="2" fill="none"/>
    <path d="M62 52 V64 M90 47 V60 M120 45 V58 M150 47 V60 M178 52 V64" stroke="#566580" stroke-width="1.2"/>
    {{-- spiral ladder --}}
    <g stroke="#5c6c86" stroke-width="2.2" fill="none"><path d="M190 238 L174 218 M190 238 L194 214 M174 218 L192 196 M192 196 L176 178"/></g>
    {{-- level gauge --}}
    <rect x="20" y="86" width="9" height="152" rx="4" fill="#0a1426" stroke="#8d9db6"/>
    <rect x="22" y="{{ $yLiq }}" width="5" height="{{ $top - $yLiq - 2 }}" fill="#f6b93b"/>
    @if($wH > 0)<rect x="22" y="{{ $yW }}" width="5" height="{{ $wH - 2 }}" fill="#38bdf8"/>@endif
    <path d="M29 90 H46 M29 234 H46" stroke="#8d9db6" stroke-width="1.5"/>
    @foreach([100, 75, 50, 25] as $t)<text x="14" y="{{ 240 - 150 * $t / 100 * $fill / 78 + 3 }}" font-size="7" fill="#64748b" text-anchor="end" font-family="Inter">{{ $t }}</text>@endforeach
    {{-- base piping --}}
    <rect x="30" y="246" width="180" height="7" rx="3" fill="#2a3448"/>
    <path d="M194 262 H222 V254" stroke="#d98e0a" stroke-width="6" fill="none"/>
    <circle cx="208" cy="262" r="6" fill="#ef4444"/><path d="M202 262 H214 M208 256 V268" stroke="#fff" stroke-width="1.2" opacity=".7"/>
    {{-- legend --}}
    <g font-family="Inter,sans-serif" font-size="10">
        <rect x="78" y="14" width="9" height="9" rx="2" fill="#f6b93b"/><text x="92" y="22" fill="#cbd5e1">Oil {{ number_format(100 - $wcv, 1) }}%</text>
        <rect x="148" y="14" width="9" height="9" rx="2" fill="#38bdf8"/><text x="162" y="22" fill="#cbd5e1">Water {{ number_format($wcv, 1) }}%</text>
    </g>
</svg>
