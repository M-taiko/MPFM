{{-- Process flow: wells -> manifold -> MPFM -> test separator -> storage. Vars: $flow = [mpfm, tv, dev] (nullable values), $snap --}}
@php
    $fv = fn ($v) => $v === null ? '—' : number_format((float) $v, 0);
    $dev = $flow['dev'] ?? null;
    $devColor = $dev === null ? '#64748b' : (abs($dev) <= 5 ? '#34d399' : (abs($dev) <= 10 ? '#fbbf24' : '#f87171'));
@endphp
<svg class="dpl flow" viewBox="0 0 900 250" role="img" aria-label="Process flow from wells through the manifold and the multiphase meter to the test separator and storage">
    <defs>
        <linearGradient id="fl-pipe" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#c9d5e8"/><stop offset=".45" stop-color="#7d8da6"/><stop offset="1" stop-color="#3a475f"/></linearGradient>
        <linearGradient id="fl-oilpipe" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#fbbf24"/><stop offset=".5" stop-color="#c47f08"/><stop offset="1" stop-color="#5e3903"/></linearGradient>
        <linearGradient id="fl-ves" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#e6edf8"/><stop offset=".3" stop-color="#9aa9c2"/><stop offset=".75" stop-color="#4c5a74"/><stop offset="1" stop-color="#2a3448"/></linearGradient>
        <linearGradient id="fl-mtr" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#ef6a5e"/><stop offset="1" stop-color="#8b1f17"/></linearGradient>
        <linearGradient id="fl-oil" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#f6b93b"/><stop offset="1" stop-color="#9a5f06"/></linearGradient>
        <linearGradient id="fl-wat" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#3b9de0"/><stop offset="1" stop-color="#0b3a68"/></linearGradient>
        <clipPath id="fl-vclip"><rect x="418" y="82" width="240" height="84" rx="38"/></clipPath>
        <path id="fl-p1" d="M64 125 H168"/><path id="fl-p2" d="M270 125 H418"/><path id="fl-p3" d="M658 125 H742"/>
    </defs>
    <rect width="900" height="250" fill="none"/>
    <path d="M0 214 H900" stroke="#16305f" stroke-width="1" stroke-dasharray="4 6"/>

    {{-- wells + manifold --}}
    @foreach([60, 125, 190] as $wi => $wy)
        <g transform="translate(18 {{ $wy - 14 }})">
            <rect x="0" y="12" width="26" height="6" rx="2" fill="#2a3448"/><rect x="9" y="-6" width="8" height="20" fill="url(#fl-pipe)"/>
            <rect x="4" y="2" width="18" height="3" fill="#6b7b95"/><circle cx="13" cy="-9" r="4" fill="#ef4444"/>
        </g>
        <path d="M44 {{ $wy }} H{{ 64 }}" stroke="url(#fl-pipe)" stroke-width="6"/>
        <path d="M64 {{ $wy }} H80" stroke="url(#fl-pipe)" stroke-width="6"/>
    @endforeach
    <rect x="76" y="52" width="14" height="148" rx="5" fill="url(#fl-pipe)"/>
    <text x="83" y="222" text-anchor="middle" font-size="9.5" fill="#9fb0cf" letter-spacing=".12em" font-family="Inter">MANIFOLD</text>
    <path d="M90 125 H168" stroke="#1d2840" stroke-width="10"/><path d="M90 125 H168" stroke="url(#fl-oilpipe)" stroke-width="7"/>
    <path d="M90 123 H168" stroke="#fff3c4" stroke-width="1" opacity=".5"/>
    <path d="M124 125 m-9 0 a9 9 0 1 0 18 0 a9 9 0 1 0 -18 0" fill="#0a1426" stroke="#cbd5e1" stroke-width="2"/><path d="M118 119 L130 131 M130 119 L118 131" stroke="#34d399" stroke-width="2"/>

    {{-- MPFM --}}
    <g>
        <rect x="168" y="112" width="102" height="26" fill="url(#fl-pipe)"/>
        <path d="M168 112 L196 118 H242 L270 112 V138 L242 132 H196 L168 138 Z" fill="url(#fl-mtr)"/>
        <rect x="160" y="108" width="10" height="34" rx="2" fill="#6b7b95"/><rect x="268" y="108" width="10" height="34" rx="2" fill="#6b7b95"/>
        <rect x="204" y="74" width="34" height="38" rx="5" fill="url(#fl-ves)" stroke="#2a3448"/>
        <rect x="209" y="80" width="24" height="14" rx="2" fill="#06230f"/><text x="221" y="91" text-anchor="middle" font-size="9" font-weight="700" fill="#4ade80" font-family="Space Grotesk,monospace">{{ $fv($flow['mpfm'] ?? null) }}</text>
        <circle cx="214" cy="103" r="2" fill="#4ade80"><animate attributeName="opacity" values="1;.2;1" dur="1.2s" repeatCount="indefinite"/></circle><circle cx="222" cy="103" r="2" fill="#fbbf24"/>
        <rect x="178" y="150" width="82" height="16" rx="4" fill="#2a3448"/><text x="219" y="161" text-anchor="middle" font-size="8.5" fill="#cbd5e1" font-family="Inter">γ source · detector</text>
        <path d="M219 138 V150" stroke="#6b7b95" stroke-width="3"/>
        <text x="219" y="62" text-anchor="middle" font-size="10" font-weight="700" fill="#22d3ee" letter-spacing=".14em" font-family="Space Grotesk">MPFM</text>
        <g stroke="#22d3ee" stroke-width="1" opacity=".5" fill="none"><circle cx="219" cy="125" r="38" stroke-dasharray="3 5"><animateTransform attributeName="transform" type="rotate" from="0 219 125" to="360 219 125" dur="16s" repeatCount="indefinite"/></circle></g>
    </g>
    <path d="M278 125 H418" stroke="#1d2840" stroke-width="10"/><path d="M278 125 H418" stroke="url(#fl-oilpipe)" stroke-width="7"/><path d="M278 123 H418" stroke="#fff3c4" stroke-width="1" opacity=".5"/>

    {{-- test separator (horizontal vessel) --}}
    <g>
        <path d="M444 168 l-10 46 h28 l-6 -46 M632 168 l-6 46 h28 l-10 -46" fill="#2a3448"/>
        <rect x="418" y="82" width="240" height="84" rx="38" fill="url(#fl-ves)"/>
        <g clip-path="url(#fl-vclip)">
            <rect x="418" y="82" width="240" height="84" fill="#0a1426" opacity=".62"/>
            <rect x="418" y="118" width="240" height="50" fill="url(#fl-oil)"/>
            <rect x="418" y="146" width="240" height="22" fill="url(#fl-wat)"/>
            <g class="fl-wave" fill="none" stroke="#ffe7a3" stroke-width="1.4"><path d="M400 118 q10 -4 20 0 t20 0 t20 0 t20 0 t20 0 t20 0 t20 0 t20 0 t20 0 t20 0 t20 0 t20 0 t20 0 t20 0"/></g>
            <rect x="418" y="82" width="240" height="14" fill="#fff" opacity=".14"/>
        </g>
        <rect x="418" y="82" width="240" height="84" rx="38" fill="none" stroke="#cfd9e8" stroke-opacity=".6" stroke-width="1.5"/>
        <path d="M470 84 V166 M606 84 V166" stroke="#fff" stroke-opacity=".18" stroke-width="3"/>
        <rect x="536" y="64" width="12" height="20" fill="url(#fl-pipe)"/><circle cx="542" cy="52" r="15" fill="#0a1426" stroke="#cbd5e1" stroke-width="3"/>
        <path d="M542 52 L549 44" stroke="#f87171" stroke-width="2" stroke-linecap="round"><animateTransform attributeName="transform" type="rotate" values="-6 542 52;5 542 52;-6 542 52" dur="3s" repeatCount="indefinite"/></path><circle cx="542" cy="52" r="2" fill="#cbd5e1"/>
        <rect x="626" y="62" width="9" height="22" fill="url(#fl-pipe)"/>
        <text x="538" y="196" text-anchor="middle" font-size="10" font-weight="700" fill="#fbbf24" letter-spacing=".14em" font-family="Space Grotesk">TEST VESSEL</text>
    </g>
    {{-- outlets --}}
    <path d="M630 62 V40 H742" stroke="#1d2840" stroke-width="6" fill="none"/><path d="M630 62 V40 H742" stroke="#7d8da6" stroke-width="4" fill="none"/>
    <text x="700" y="33" font-size="8.5" fill="#fb923c" font-family="Inter" letter-spacing=".1em">GAS</text>
    <path d="M658 125 H742" stroke="#1d2840" stroke-width="9"/><path d="M658 125 H742" stroke="url(#fl-oilpipe)" stroke-width="6.5"/>
    <text x="702" y="116" font-size="8.5" fill="#fbbf24" font-family="Inter" letter-spacing=".1em">OIL</text>
    <path d="M640 164 V190 H742" stroke="#1d2840" stroke-width="6" fill="none"/><path d="M640 164 V190 H742" stroke="#3b9de0" stroke-width="4" fill="none"/>
    <text x="700" y="184" font-size="8.5" fill="#38bdf8" font-family="Inter" letter-spacing=".1em">WATER</text>

    {{-- storage + flare --}}
    <g transform="translate(742 0)">
        <rect x="10" y="84" width="84" height="102" rx="6" fill="url(#fl-ves)"/><ellipse cx="52" cy="84" rx="42" ry="9" fill="#b9c6da"/>
        <rect x="10" y="150" width="84" height="36" fill="url(#fl-oil)" opacity=".9"/><rect x="10" y="176" width="84" height="10" fill="url(#fl-wat)"/>
        <path d="M10 84 V186 M94 84 V186" stroke="#2a3448" stroke-opacity=".5"/>
        <text x="52" y="210" text-anchor="middle" font-size="10" font-weight="700" fill="#9fb0cf" letter-spacing=".14em" font-family="Space Grotesk">STORAGE</text>
        <g transform="translate(30 4)"><path d="M0 36 V10" stroke="#7d8da6" stroke-width="3"/><path d="M0 8 C-6 2 -4 -6 0 -14 C4 -6 6 2 0 8 Z" fill="#ff8a2a"><animate attributeName="d" dur="1.1s" repeatCount="indefinite" values="M0 8 C-6 2 -4 -6 0 -14 C4 -6 6 2 0 8 Z;M0 8 C-7 3 -1 -4 1 -10 C5 -4 7 3 0 8 Z;M0 8 C-6 2 -4 -6 0 -14 C4 -6 6 2 0 8 Z"/></path></g>
    </g>

    {{-- flow dots --}}
    @foreach(['fl-p1' => 'oil', 'fl-p2' => 'oil', 'fl-p3' => 'oil'] as $pid => $_)
        @for($k = 0; $k < 3; $k++)
            <circle r="2.8" fill="#2b1900" opacity=".85"><animateMotion dur="{{ $pid === 'fl-p2' ? 4.2 : 2.6 }}s" begin="{{ $k * 1.1 }}s" repeatCount="indefinite"><mpath href="#{{ $pid }}"/></animateMotion></circle>
        @endfor
    @endforeach

    {{-- deviation badge --}}
    <g font-family="Inter,sans-serif" transform="translate(300 14)">
        <rect width="190" height="44" rx="12" fill="#050a15" fill-opacity=".85" stroke="{{ $devColor }}" stroke-opacity=".7"/>
        <text x="14" y="18" font-size="9.5" fill="#9fb0cf" letter-spacing=".1em">MPFM vs TEST VESSEL</text>
        <text x="14" y="36" font-size="15" font-weight="800" fill="{{ $devColor }}">{{ $dev === null ? 'no linked pair' : (($dev >= 0 ? '+' : '').number_format($dev, 1).' % oil') }}</text>
        <text x="176" y="36" font-size="10" fill="#9fb0cf" text-anchor="end">{{ $fv($flow['mpfm'] ?? null) }} / {{ $fv($flow['tv'] ?? null) }}</text>
    </g>
    <path d="M395 58 L330 84" stroke="{{ $devColor }}" stroke-opacity=".5" stroke-dasharray="3 4" fill="none"/>
</svg>
