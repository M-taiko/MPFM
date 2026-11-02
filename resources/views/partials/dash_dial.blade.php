{{-- Instrument dial. Vars: $dId, $dVal (0-100), $dText, $dLabel, $dSub, optional $dZones [[from,to,color],...] (percent) --}}
@php
    $dZones = $dZones ?? [[0, 33, '#f87171'], [33, 66, '#fbbf24'], [66, 100, '#34d399']];
    $cx = 100; $cy = 104;
    $pt = function ($p, $r) use ($cx, $cy) { $a = deg2rad(180 - 180 * $p / 100); return [round($cx + $r * cos($a), 2), round($cy - $r * sin($a), 2)]; };
    $arc = function ($p1, $p2, $r) use ($pt) { [$x1, $y1] = $pt($p1, $r); [$x2, $y2] = $pt($p2, $r); return "M$x1 $y1 A$r $r 0 0 1 $x2 $y2"; };
    $v = max(0, min(100, (float) $dVal));
@endphp
<svg class="dpl dial" viewBox="0 0 200 138" role="img" aria-label="{{ $dLabel }}: {{ $dText }}">
    <defs>
        <radialGradient id="dd-bz-{{ $dId }}" cx=".5" cy=".4" r=".7"><stop offset="0" stop-color="#8d9db6"/><stop offset=".6" stop-color="#3a475f"/><stop offset="1" stop-color="#151d2e"/></radialGradient>
        <radialGradient id="dd-face-{{ $dId }}" cx=".5" cy=".9" r=".9"><stop offset="0" stop-color="#16294a"/><stop offset="1" stop-color="#070d1a"/></radialGradient>
        <linearGradient id="dd-gl-{{ $dId }}" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#fff" stop-opacity=".22"/><stop offset=".5" stop-color="#fff" stop-opacity="0"/></linearGradient>
    </defs>
    <path d="M6 108 A94 94 0 0 1 194 108 Z" fill="url(#dd-bz-{{ $dId }})"/>
    <path d="M14 106 A86 86 0 0 1 186 106 Z" fill="url(#dd-face-{{ $dId }})"/>
    @foreach($dZones as $z)<path d="{{ $arc($z[0], $z[1], 74) }}" stroke="{{ $z[2] }}" stroke-width="7" fill="none" opacity=".85"/>@endforeach
    @for($i = 0; $i <= 20; $i++)
        @php [$x1, $y1] = $pt($i * 5, $i % 5 === 0 ? 60 : 64); [$x2, $y2] = $pt($i * 5, 68); @endphp
        <path d="M{{ $x1 }} {{ $y1 }} L{{ $x2 }} {{ $y2 }}" stroke="#cbd5e1" stroke-width="{{ $i % 5 === 0 ? 1.6 : .8 }}" opacity="{{ $i % 5 === 0 ? .9 : .5 }}"/>
    @endfor
    @foreach([0, 50, 100] as $m)
        @php [$tx, $ty] = $pt($m, 51); @endphp
        <text x="{{ $tx }}" y="{{ $ty + 3 }}" font-size="8" fill="#9fb0cf" text-anchor="middle" font-family="Inter">{{ $m }}</text>
    @endforeach
    <text x="100" y="88" text-anchor="middle" font-size="17" font-weight="800" fill="#fff" font-family="Space Grotesk,Inter,sans-serif">{{ $dText }}</text>
    <text x="100" y="124" text-anchor="middle" font-size="9" fill="#9fb0cf" letter-spacing=".12em" font-family="Inter">{{ strtoupper($dLabel) }}</text>
    <g class="dd-needle" data-v="{{ $v }}" style="transform-origin:100px 104px;transform:rotate(-90deg);transition:transform 1.8s cubic-bezier(.34,1.56,.64,1) .4s">
        <path d="M100 104 L97 100 L100 36 L103 100 Z" fill="#f87171"/><path d="M100 104 L100 36" stroke="#fff" stroke-width=".6" opacity=".55"/>
    </g>
    <circle cx="100" cy="104" r="7" fill="#cbd5e1"/><circle cx="100" cy="104" r="3.2" fill="#334155"/>
    <path d="M14 106 A86 86 0 0 1 186 106 Z" fill="url(#dd-gl-{{ $dId }})"/>
</svg>
