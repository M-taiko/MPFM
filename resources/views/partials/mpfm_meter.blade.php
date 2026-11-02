{{-- MPFM drawing with live readouts. Vars: $oil $gas $wrate $pAvg $tAvg $pScale $tScale $tUnit --}}
@php $f = fn ($v, $d = 2) => is_numeric($v) ? number_format((float) $v, $d) : '—'; @endphp
<svg viewBox="0 0 900 350" role="img" aria-label="MPFM drawing">
<defs>
    <linearGradient id="tvSteel" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#9aa8bd"/><stop offset=".18" stop-color="#e2e8f0"/><stop offset=".5" stop-color="#64748b"/><stop offset="1" stop-color="#1e293b"/></linearGradient>
    <linearGradient id="msBody" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#a8b6cb"/><stop offset=".16" stop-color="#f1f5f9"/><stop offset=".5" stop-color="#7c8aa1"/><stop offset="1" stop-color="#1b2433"/></linearGradient>
    <radialGradient id="msGlow" cx=".5" cy=".5" r=".5"><stop offset="0" stop-color="#22d3ee" stop-opacity=".25"/><stop offset="1" stop-color="#22d3ee" stop-opacity="0"/></radialGradient>
    <path id="mPipe" d="M16,230 H884"/>
</defs>
<ellipse cx="450" cy="230" rx="300" ry="110" fill="url(#msGlow)"/>
{{-- stems for the dials --}}
<rect x="106" y="142" width="8" height="74" fill="#64748b" stroke="#0b1220"/><rect x="206" y="142" width="8" height="74" fill="#64748b" stroke="#0b1220"/>
<g fill="none"><use href="#mPipe" stroke="#0b1220" stroke-width="32"/><use href="#mPipe" stroke="#64748b" stroke-width="26"/><use href="#mPipe" stroke="#e2e8f0" stroke-opacity=".3" stroke-width="3" transform="translate(0,-6)"/></g>
{{-- venturi --}}
<path d="M290,200 H350 L400,216 H500 L550,200 H610 V260 H550 L500,244 H400 L350,260 H290 Z" fill="url(#msBody)" stroke="#0b1220" stroke-width="1.6"/>
<path d="M350,200 V260 M550,200 V260" stroke="#0b1220" stroke-opacity=".4"/>
<rect x="283" y="196" width="10" height="68" rx="2" fill="url(#tvSteel)" stroke="#0b1220"/><rect x="607" y="196" width="10" height="68" rx="2" fill="url(#tvSteel)" stroke="#0b1220"/>
<text x="450" y="236" text-anchor="middle" class="tg" style="fill:#0b1220;font-size:8px">VENTURI</text>
{{-- DP transmitter + impulse lines --}}
<path d="M322,200 V150 H350 V122 M410,216 V150 H380 V122" fill="none" stroke="#94a3b8" stroke-width="2.4"/>
<rect x="337" y="88" width="56" height="34" rx="7" fill="#0a1222" stroke="#22d3ee" stroke-width="1.8"/><text x="365" y="109" text-anchor="middle" font-size="11" font-weight="800" fill="#67e8f9">DP</text>
{{-- gamma densitometer --}}
<rect x="441" y="132" width="58" height="46" rx="7" fill="#0a1222" stroke="#34d399" stroke-width="1.8"/><text x="470" y="160" text-anchor="middle" font-size="9.5" font-weight="800" fill="#6ee7b7">γ DETECTOR</text>
<rect x="445" y="180" width="50" height="22" fill="#64748b" stroke="#0b1220"/>
<rect x="440" y="284" width="60" height="50" rx="8" fill="#0a1222" stroke="#fbbf24" stroke-width="1.8"/>
<g transform="translate(470,309)"><circle r="14" fill="none" stroke="#fbbf24" stroke-width="1.4"/><g class="mspin"><path d="M0,-11 L5,-1 H-5 Z M9.5,5.5 L0,3 L5,-4 Z M-9.5,5.5 L-5,-4 L0,3 Z" fill="#fbbf24"/></g></g>
<rect x="445" y="262" width="50" height="22" fill="#64748b" stroke="#0b1220"/>
<path class="mbeam" d="M458,262 V204 M470,262 V204 M482,262 V204"/>
<text x="470" y="346" text-anchor="middle" class="tg" style="fill:#fcd34d">γ SOURCE</text>
{{-- PT --}}
<path d="M598,196 V158" stroke="#94a3b8" stroke-width="2.4"/><circle cx="598" cy="140" r="19" fill="#0a1222" stroke="#a78bfa" stroke-width="1.8"/><text x="598" y="144" text-anchor="middle" font-size="11" font-weight="800" fill="#c4b5fd">PT</text>
{{-- signal lines to flow computer --}}
<path class="mflow" d="M393,105 H640 M470,132 V105 M598,121 V105" stroke="#22d3ee"/>
{{-- flow computer --}}
<rect x="640" y="22" width="244" height="136" rx="12" fill="#0a1222" stroke="#22d3ee" stroke-opacity=".7" stroke-width="1.6"/>
<circle cx="866" cy="38" r="3.6" fill="#34d399" class="mpulse"/>
<text x="656" y="42" class="cl" fill="#67e8f9">FLOW COMPUTER</text>
<text x="656" y="72" class="cl" fill="#fbbf24">OIL</text><text x="790" y="74" text-anchor="end" class="cv cu" data-to="{{ $oil ?? 0 }}" data-dec="1">{{ $f($oil, 1) }}</text><text x="798" y="74" class="cu2">STB/d</text>
<text x="656" y="102" class="cl" fill="#67e8f9">GAS</text><text x="790" y="104" text-anchor="end" class="cv cu" data-to="{{ $gas ?? 0 }}" data-dec="2">{{ $f($gas, 2) }}</text><text x="798" y="104" class="cu2">MMScf/d</text>
<text x="656" y="132" class="cl" fill="#7dd3fc">WATER</text><text x="790" y="134" text-anchor="end" class="cv cu" data-to="{{ $wrate ?? 0 }}" data-dec="1">{{ $f($wrate, 1) }}</text><text x="798" y="134" class="cu2">STB/d</text>
{{-- dials --}}
@include('partials.tv_dial', ['cx' => 110, 'cy' => 104, 'r' => 30, 'val' => $pAvg ?? 0, 'max' => $pScale, 'text' => $f($pAvg, 0), 'unit' => 'PSI'])
@include('partials.tv_dial', ['cx' => 210, 'cy' => 104, 'r' => 30, 'val' => $tAvg ?? 0, 'max' => $tScale, 'text' => $f($tAvg, 0), 'unit' => $tUnit])
<text x="110" y="56" text-anchor="middle" class="tg">PI · AVG PRESSURE</text><text x="210" y="56" text-anchor="middle" class="tg">TI · AVG TEMP</text>
{{-- moving fluid --}}
@foreach([0, .8, 1.6, 2.4, 3.2, 4] as $b)
    <circle r="3.6" fill="#fde68a"><animateMotion dur="5s" begin="{{ $b }}s" repeatCount="indefinite"><mpath href="#mPipe"/></animateMotion></circle>
    <circle r="2.6" fill="#a5f3fc"><animateMotion dur="5s" begin="{{ $b + .4 }}s" repeatCount="indefinite"><mpath href="#mPipe"/></animateMotion></circle>
@endforeach
<text x="16" y="204" class="tg" style="fill:#fcd34d">WELL STREAM</text>
<text x="884" y="262" text-anchor="end" class="tg" style="fill:#67e8f9">→ PRODUCTION HEADER</text>
</svg>
