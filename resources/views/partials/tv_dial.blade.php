{{-- Pressure/temperature dial for the well testing drawing. Vars: $cx $cy $r $val $max $text $unit --}}
@php
    $frac = $max > 0 ? min(max($val / $max, 0), 1) : 0;
    $ang = round(-120 + 240 * $frac, 1);
    $pt = function ($deg, $rad) {
        $a = deg2rad($deg - 90);
        return round(cos($a) * $rad, 2) . ',' . round(sin($a) * $rad, 2);
    };
    $ar = $r - 5;
@endphp
<g transform="translate({{ $cx }},{{ $cy }})">
    <circle r="{{ $r + 5 }}" fill="url(#tvSteel)" stroke="#0b1220" stroke-width="1"/>
    <circle r="{{ $r }}" fill="#0a1222" stroke="#475569" stroke-width="1.2"/>
    <path d="M {{ $pt(-120, $ar) }} A {{ $ar }} {{ $ar }} 0 0 1 {{ $pt(48, $ar) }}" fill="none" stroke="#34d399" stroke-width="3" opacity=".7"/>
    <path d="M {{ $pt(48, $ar) }} A {{ $ar }} {{ $ar }} 0 0 1 {{ $pt(72, $ar) }}" fill="none" stroke="#fbbf24" stroke-width="3" opacity=".8"/>
    <path d="M {{ $pt(72, $ar) }} A {{ $ar }} {{ $ar }} 0 0 1 {{ $pt(120, $ar) }}" fill="none" stroke="#ef4444" stroke-width="3" opacity=".85"/>
    @for($i = 0; $i <= 10; $i++)
        @php $d = -120 + 24 * $i; @endphp
        <line x1="{{ explode(',', $pt($d, $r - 3))[0] }}" y1="{{ explode(',', $pt($d, $r - 3))[1] }}" x2="{{ explode(',', $pt($d, $r - ($i % 5 == 0 ? 10 : 7)))[0] }}" y2="{{ explode(',', $pt($d, $r - ($i % 5 == 0 ? 10 : 7)))[1] }}" stroke="#94a3b8" stroke-width="{{ $i % 5 == 0 ? 1.4 : .8 }}"/>
    @endfor
    <text y="{{ round($r * .5) }}" text-anchor="middle" class="tvx-dv" style="font-size:{{ round($r * .36, 1) }}px">{{ $text }}</text>
    <text y="{{ round($r * .78) }}" text-anchor="middle" class="tvx-du" style="font-size:{{ round($r * .24, 1) }}px">{{ $unit }}</text>
    <g class="tvx-needle" style="--a:{{ $ang }}deg">
        <g class="tvx-jit">
            <path d="M -1.8,5 L 0,-{{ $r - 6 }} L 1.8,5 Z" fill="#fbbf24"/>
        </g>
    </g>
    <circle r="3.4" fill="#e2e8f0" stroke="#0b1220"/>
    <ellipse cx="-{{ round($r * .28) }}" cy="-{{ round($r * .5) }}" rx="{{ round($r * .5) }}" ry="{{ round($r * .22) }}" fill="#fff" opacity=".06"/>
</g>
