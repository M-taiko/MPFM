{{-- Offshore platform cross-section. Vars: $snap (oil, gas, water, wc, strings) --}}
@php
    $fmtK = fn ($v) => $v >= 1000 ? number_format($v / 1000, 1).'k' : number_format($v, 0);
    // lattice bracing for the derrick (trapezoid x: 404..446 at the base, 416..434 at the top)
    $lat = [];
    for ($i = 0; $i < 9; $i++) {
        $y1 = 182 - $i * 14; $y2 = $y1 - 14;
        $w1 = 21 - $i * 1.15; $w2 = $w1 - 1.15;
        $lat[] = [425 - $w1, $y1, 425 + $w2, $y2, 425 + $w1, $y1, 425 - $w2, $y2, $y2, 425 - $w2, 425 + $w2];
    }
    // jacket bracing levels
    $levels = [236, 268, 300, 336];
@endphp
<svg class="dpl" viewBox="0 0 900 360" role="img" aria-label="Offshore platform cross-section showing wells, the jacket, the flare and the export pipeline" preserveAspectRatio="xMidYMid slice">
    <defs>
        <linearGradient id="dp-sky" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0" stop-color="#020611"/><stop offset=".45" stop-color="#0a1f44"/><stop offset=".8" stop-color="#27508a"/><stop offset="1" stop-color="#e0903f"/>
        </linearGradient>
        <radialGradient id="dp-sun" cx=".5" cy=".5" r=".5"><stop offset="0" stop-color="#ffd58a" stop-opacity=".95"/><stop offset=".25" stop-color="#ffb04a" stop-opacity=".45"/><stop offset="1" stop-color="#ff8a2a" stop-opacity="0"/></radialGradient>
        <linearGradient id="dp-sea" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#14588f"/><stop offset=".35" stop-color="#0a3a68"/><stop offset="1" stop-color="#031428"/></linearGradient>
        <linearGradient id="dp-steel" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="#46546c"/><stop offset=".45" stop-color="#9fb0c8"/><stop offset=".6" stop-color="#d4deec"/><stop offset="1" stop-color="#3c485d"/></linearGradient>
        <linearGradient id="dp-steelv" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#b9c6da"/><stop offset=".5" stop-color="#7d8da6"/><stop offset="1" stop-color="#45536b"/></linearGradient>
        <linearGradient id="dp-red" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#e2574c"/><stop offset="1" stop-color="#8f2018"/></linearGradient>
        <linearGradient id="dp-white" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#f1f5fb"/><stop offset="1" stop-color="#a8b6cb"/></linearGradient>
        <linearGradient id="dp-seabed" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#6d5a3d"/><stop offset="1" stop-color="#2a2114"/></linearGradient>
        <linearGradient id="dp-pipe" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#fbbf24"/><stop offset=".4" stop-color="#d98e0a"/><stop offset="1" stop-color="#6b4204"/></linearGradient>
        <radialGradient id="dp-flame" cx=".5" cy=".75" r=".6"><stop offset="0" stop-color="#fff7c2"/><stop offset=".35" stop-color="#ffb02e"/><stop offset=".75" stop-color="#ff5a1f" stop-opacity=".85"/><stop offset="1" stop-color="#ff3d00" stop-opacity="0"/></radialGradient>
        <radialGradient id="dp-flare-glow" cx=".5" cy=".5" r=".5"><stop offset="0" stop-color="#ffb02e" stop-opacity=".55"/><stop offset="1" stop-color="#ff7a1f" stop-opacity="0"/></radialGradient>
        <linearGradient id="dp-fade" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="#050a15" stop-opacity=".8"/><stop offset=".25" stop-color="#050a15" stop-opacity="0"/></linearGradient>
        <clipPath id="dp-seaclip"><rect x="0" y="236" width="900" height="124"/></clipPath>
        <path id="dp-wave" d="M0 0 q15 -5 30 0 t30 0 t30 0 t30 0 t30 0 t30 0 t30 0 t30 0 t30 0 t30 0 t30 0 t30 0 t30 0 t30 0 t30 0 t30 0 t30 0 t30 0 t30 0 t30 0 t30 0 t30 0 t30 0 t30 0 t30 0 t30 0 t30 0 t30 0 t30 0 t30 0 t30 0"/>
        <path id="dp-fl1" d="M150 331 H330"/><path id="dp-fl2" d="M215 331 H330"/>
        <path id="dp-exp" d="M545 336 V322 Q545 316 552 316 H900"/>
    </defs>

    {{-- sky --}}
    <rect width="900" height="236" fill="url(#dp-sky)"/>
    <g fill="#fff">
        @foreach([[40, 30, 1.1], [95, 70, .8], [170, 24, 1], [250, 60, .7], [330, 18, 1.2], [610, 28, 1], [700, 54, .8], [780, 22, 1.2], [850, 66, .7], [560, 64, .6], [120, 110, .6], [455, 22, .9]] as [$sx, $sy, $sr])
            <circle cx="{{ $sx }}" cy="{{ $sy }}" r="{{ $sr }}" opacity=".7"><animate attributeName="opacity" values=".25;.9;.25" dur="{{ 2.4 + ($sx % 5) * .6 }}s" repeatCount="indefinite"/></circle>
        @endforeach
    </g>
    <circle cx="760" cy="232" r="120" fill="url(#dp-sun)"/>
    <g opacity=".5" fill="#13294f"><path d="M0 232 Q120 218 240 230 T470 228 T700 231 T900 226 V236 H0 Z"/></g>
    {{-- distant tanker --}}
    <g transform="translate(70 222)" opacity=".85">
        <path d="M0 6 H62 L68 0 H6 Z" fill="#0d1b35"/><rect x="40" y="-8" width="10" height="8" fill="#0d1b35"/><rect x="43" y="-13" width="3" height="5" fill="#0d1b35"/>
        <circle cx="58" cy="-1" r="1" fill="#ffd58a"><animate attributeName="opacity" values="1;.2;1" dur="2s" repeatCount="indefinite"/></circle>
    </g>

    {{-- platform: flare boom --}}
    <g>
        <path d="M578 176 L704 70" stroke="#6f7f98" stroke-width="3.5" stroke-linecap="round"/>
        <path d="M584 190 L712 78" stroke="#4d5b73" stroke-width="2" stroke-linecap="round"/>
        @for($i = 0; $i < 9; $i++)
            <path d="M{{ 580 + $i * 14 }} {{ 175 - $i * 11.2 + 4 }} l9 -6" stroke="#566580" stroke-width="1"/>
        @endfor
        <circle cx="706" cy="56" r="46" fill="url(#dp-flare-glow)"><animate attributeName="r" values="40;54;42;50;40" dur="2.4s" repeatCount="indefinite"/></circle>
        <path d="M698 74 h16 l-2 -6 h-12 z" fill="#6f7f98"/>
        <g class="dp-flame" style="transform-origin:706px 68px">
            <path d="M706 68 C694 58 696 44 706 22 C716 44 718 58 706 68 Z" fill="url(#dp-flame)">
                <animate attributeName="d" dur="1.3s" repeatCount="indefinite" values="M706 68 C694 58 696 44 706 22 C716 44 718 58 706 68 Z;M706 68 C692 60 700 46 703 28 C712 42 722 58 706 68 Z;M706 68 C696 56 692 46 708 24 C718 42 716 60 706 68 Z;M706 68 C694 58 696 44 706 22 C716 44 718 58 706 68 Z"/>
            </path>
            <path d="M706 66 C700 60 702 52 706 42 C710 52 712 60 706 66 Z" fill="#fff6c9" opacity=".9"/>
        </g>
        <g fill="#8896ad" opacity=".28">
            <circle cx="712" cy="14" r="6"><animate attributeName="cy" values="22;-8" dur="4s" repeatCount="indefinite"/><animate attributeName="opacity" values=".32;0" dur="4s" repeatCount="indefinite"/></circle>
            <circle cx="722" cy="20" r="8"><animate attributeName="cy" values="26;-12" dur="5s" begin="1s" repeatCount="indefinite"/><animate attributeName="opacity" values=".3;0" dur="5s" begin="1s" repeatCount="indefinite"/></circle>
        </g>
    </g>

    {{-- cellar deck beam + legs above water --}}
    <g>
        <rect x="334" y="208" width="256" height="10" fill="url(#dp-steelv)"/>
        <rect x="334" y="216" width="256" height="2" fill="#2c3750"/>
        @foreach([352, 440, 530] as $lx)
            <rect x="{{ $lx }}" y="216" width="9" height="22" fill="url(#dp-steel)"/>
        @endforeach
        {{-- helideck --}}
        <path d="M336 204 L262 196 L262 191 L338 194 Z" fill="url(#dp-steelv)"/>
        <path d="M300 208 L270 196 M318 208 L286 196" stroke="#5c6c86" stroke-width="2"/>
        <ellipse cx="292" cy="190" rx="36" ry="5.5" fill="#2a364d"/>
        <ellipse cx="292" cy="189" rx="31" ry="4.2" fill="#3a475f" stroke="#fbbf24" stroke-width=".8"/>
        <text x="292" y="192" text-anchor="middle" font-size="7.5" font-weight="800" fill="#fff" font-family="Inter,sans-serif">H</text>
        @foreach([258, 276, 308, 326] as $hx)
            <circle cx="{{ $hx }}" cy="190" r="1.4" fill="#34d399"><animate attributeName="opacity" values="1;.2;1" dur="1.6s" begin="{{ ($hx % 7) * .1 }}s" repeatCount="indefinite"/></circle>
        @endforeach
        {{-- modules --}}
        <rect x="340" y="172" width="62" height="36" fill="url(#dp-white)"/><rect x="340" y="172" width="62" height="4" fill="#cfd9e8"/>
        @foreach([348, 362, 376, 390] as $wx)<rect x="{{ $wx }}" y="186" width="7" height="9" fill="#fcd34d" opacity=".9"/>@endforeach
        <rect x="402" y="188" width="52" height="20" fill="url(#dp-red)"/><rect x="402" y="188" width="52" height="3" fill="#ef7a70"/>
        <rect x="456" y="168" width="56" height="40" fill="url(#dp-white)"/><rect x="456" y="168" width="56" height="4" fill="#cfd9e8"/>
        <rect x="462" y="178" width="18" height="30" fill="#8896ad"/><rect x="484" y="178" width="22" height="12" fill="#5c6c86"/>
        <rect x="516" y="146" width="58" height="62" fill="url(#dp-white)"/><rect x="512" y="142" width="66" height="6" fill="url(#dp-steelv)"/>
        @foreach([0, 1, 2, 3] as $r)@foreach([0, 1, 2] as $c)
            <rect x="{{ 523 + $c * 17 }}" y="{{ 153 + $r * 13 }}" width="10" height="7" fill="#fcd34d" opacity="{{ ($r * 3 + $c) % 4 === 1 ? .35 : .95 }}"/>
        @endforeach @endforeach
        <rect x="528" y="122" width="10" height="20" fill="#7b8aa3"/><rect x="526" y="120" width="14" height="3" fill="#5c6c86"/>
        {{-- crane --}}
        <rect x="478" y="156" width="14" height="12" fill="#f59e0b"/>
        <path d="M485 158 L572 98" stroke="#f59e0b" stroke-width="4.5" stroke-linecap="round"/><path d="M485 158 L572 98" stroke="#fde68a" stroke-width="1" stroke-linecap="round" opacity=".7"/>
        <path d="M488 158 L486 144" stroke="#f59e0b" stroke-width="3"/>
        <path d="M486 144 L568 100" stroke="#b97708" stroke-width="1"/>
        <path d="M570 99 V138" stroke="#cbd5e1" stroke-width="1"><animate attributeName="d" values="M570 99 V138;M570 99 V128;M570 99 V138" dur="7s" repeatCount="indefinite"/></path>
        <rect x="567" y="136" width="6" height="5" fill="#fbbf24" rx="1"><animate attributeName="y" values="136;126;136" dur="7s" repeatCount="indefinite"/></rect>
        {{-- derrick --}}
        <g stroke="#8c9bb4" stroke-width="1.4" fill="none">
            <path d="M404 186 L416 44 M446 186 L434 44"/>
            @foreach($lat as $L)
                <path d="M{{ $L[0] }} {{ $L[1] }} L{{ $L[2] }} {{ $L[3] }} M{{ $L[4] }} {{ $L[1] }} L{{ $L[6] }} {{ $L[3] }} M{{ $L[0] }} {{ $L[3] }} H{{ $L[4] }}" stroke-width=".9"/>
            @endforeach
        </g>
        <rect x="412" y="38" width="26" height="8" rx="2" fill="url(#dp-red)"/><rect x="420" y="30" width="10" height="8" fill="#5c6c86"/>
        <path d="M425 46 V176" stroke="#cbd5e1" stroke-width="1" stroke-dasharray="3 3"><animate attributeName="stroke-dashoffset" from="0" to="-12" dur="1.2s" repeatCount="indefinite"/></path>
        <rect x="419" y="120" width="12" height="9" rx="1" fill="#fbbf24"><animate attributeName="y" values="120;60;120" dur="9s" repeatCount="indefinite"/></rect>
        <circle cx="425" cy="25" r="3" fill="#ff2d2d"><animate attributeName="opacity" values="1;.1;1" dur="1.4s" repeatCount="indefinite"/></circle>
        <circle cx="425" cy="25" r="10" fill="#ff2d2d" opacity=".2"><animate attributeName="opacity" values=".35;0;.35" dur="1.4s" repeatCount="indefinite"/></circle>
    </g>

    {{-- sea --}}
    <rect x="0" y="236" width="900" height="124" fill="url(#dp-sea)"/>
    {{-- sun glitter --}}
    <g clip-path="url(#dp-seaclip)">
        <g fill="#ffd58a">
            @for($i = 0; $i < 14; $i++)
                <rect x="{{ 730 + (($i * 37) % 70) - 20 }}" y="{{ 240 + $i * 6 }}" width="{{ 30 - $i * 1.4 }}" height="1.6" rx=".8" opacity=".5"><animate attributeName="opacity" values=".15;.75;.15" dur="{{ 1.6 + ($i % 4) * .5 }}s" begin="{{ $i * .17 }}s" repeatCount="indefinite"/></rect>
            @endfor
        </g>
        <g class="dp-waves" fill="none" stroke="#8fd3ff" stroke-width="1.1" opacity=".5">
            <use href="#dp-wave" x="-30" y="241"/><use href="#dp-wave" x="-12" y="251" opacity=".6"/>
        </g>
    </g>
    <path d="M0 236 H900" stroke="#9bdcff" stroke-width="1.2" opacity=".55"/>

    {{-- submerged jacket --}}
    <g opacity=".92">
        <path d="M356 236 L330 336 M446 236 L446 336 M536 236 L562 336" stroke="url(#dp-steel)" stroke-width="9" stroke-linecap="butt" fill="none"/>
        @foreach($levels as $li => $ly)
            <path d="M{{ 356 - ($ly - 236) * .26 }} {{ $ly }} H{{ 536 + ($ly - 236) * .26 }}" stroke="#6b7b95" stroke-width="3"/>
        @endforeach
        @for($i = 0; $i < 3; $i++)
            @php $a = $levels[$i]; $b = $levels[$i + 1]; $ka = ($a - 236) * .26; $kb = ($b - 236) * .26; @endphp
            <path d="M{{ 356 - $ka }} {{ $a }} L{{ 446 }} {{ $b }} M{{ 446 }} {{ $a }} L{{ 356 - $kb }} {{ $b }} M{{ 446 }} {{ $a }} L{{ 536 + $kb }} {{ $b }} M{{ 536 + $ka }} {{ $a }} L{{ 446 }} {{ $b }}" stroke="#51607a" stroke-width="2" fill="none"/>
        @endfor
    </g>
    {{-- water tint over the jacket (depth) --}}
    <rect x="0" y="236" width="900" height="124" fill="#04223f" opacity=".32"/>

    {{-- seabed --}}
    <path d="M0 338 Q60 332 130 337 T270 336 T420 340 T600 335 T760 339 T900 334 V360 H0 Z" fill="url(#dp-seabed)"/>
    <path d="M0 338 Q60 332 130 337 T270 336 T420 340 T600 335 T760 339 T900 334" stroke="#a78a5b" stroke-width="1.2" fill="none" opacity=".6"/>
    @foreach([[40, 348], [188, 350], [300, 352], [610, 349], [700, 352], [820, 347]] as [$px, $py])
        <ellipse cx="{{ $px }}" cy="{{ $py }}" rx="{{ 5 + $px % 4 }}" ry="2" fill="#1a1409" opacity=".6"/>
    @endforeach

    {{-- subsea wells (christmas trees) + flowlines --}}
    @foreach([150, 215] as $wi => $wx)
        <g transform="translate({{ $wx }} 331)">
            <rect x="-11" y="4" width="22" height="5" rx="2" fill="#2f3a4f"/>
            <rect x="-4" y="-22" width="8" height="26" fill="url(#dp-steel)"/>
            <rect x="-9" y="-12" width="18" height="4" fill="#6b7b95"/><rect x="-9" y="-20" width="18" height="3" fill="#6b7b95"/>
            <circle cx="0" cy="-27" r="4" fill="#ef4444"/><rect x="5" y="-10" width="9" height="3" fill="#6b7b95"/>
            <circle cx="0" cy="-27" r="9" fill="#38bdf8" opacity="0"><animate attributeName="opacity" values="0;.25;0" dur="2.6s" begin="{{ $wi * .8 }}s" repeatCount="indefinite"/></circle>
        </g>
        <path d="M{{ $wx + 10 }} 331 H330" stroke="#2a1d06" stroke-width="7" fill="none"/>
        <path d="M{{ $wx + 10 }} 331 H330" stroke="url(#dp-pipe)" stroke-width="5" fill="none"/>
        <path d="M{{ $wx + 10 }} 329.4 H330" stroke="#fff3c4" stroke-width="1" opacity=".6"/>
    @endforeach
    <path d="M150 331 H215" stroke="#2a1d06" stroke-width="0"/>
    @foreach(['dp-fl1' => 0, 'dp-fl2' => .9] as $pid => $dl)
        @for($k = 0; $k < 3; $k++)
            <circle r="2.4" fill="#2b1900" opacity=".85"><animateMotion dur="3.6s" begin="{{ $dl + $k * 1.2 }}s" repeatCount="indefinite"><mpath href="#{{ $pid }}"/></animateMotion></circle>
        @endfor
    @endforeach
    {{-- riser + export pipeline --}}
    <path d="M545 218 V322 Q545 332 556 332 H900" stroke="#2a1d06" stroke-width="9" fill="none"/>
    <path d="M545 218 V322 Q545 332 556 332 H900" stroke="url(#dp-pipe)" stroke-width="6.5" fill="none"/>
    <path d="M543 218 V322" stroke="#fff3c4" stroke-width="1" opacity=".55"/>
    <path id="dp-exp2" d="M545 222 V322 Q545 332 556 332 H900" fill="none"/>
    @for($k = 0; $k < 6; $k++)
        <circle r="3" fill="#2b1900"><animateMotion dur="7s" begin="{{ $k * 1.17 }}s" repeatCount="indefinite"><mpath href="#dp-exp2"/></animateMotion></circle>
    @endfor
    {{-- bubbles --}}
    @foreach([[372, 290], [466, 262], [330, 318], [588, 300]] as $bi => [$bx, $by])
        <circle cx="{{ $bx }}" cy="{{ $by }}" r="2" fill="none" stroke="#9bdcff" stroke-width=".8" opacity="0"><animate attributeName="cy" values="{{ $by }};244" dur="{{ 4 + $bi }}s" begin="{{ $bi * 1.3 }}s" repeatCount="indefinite"/><animate attributeName="opacity" values="0;.7;0" dur="{{ 4 + $bi }}s" begin="{{ $bi * 1.3 }}s" repeatCount="indefinite"/></circle>
    @endforeach

    <rect width="900" height="360" fill="url(#dp-fade)"/>

    {{-- live data tags --}}
    <g font-family="Inter,sans-serif" font-size="11" class="dpt">
        <g transform="translate(40 252)"><path d="M110 42 L110 56" stroke="#22d3ee" stroke-width="1"/>
            <rect width="132" height="42" rx="10" fill="#050a15" fill-opacity=".82" stroke="#22d3ee" stroke-opacity=".6"/>
            <text x="12" y="17" fill="#9fb0cf" font-size="9.5" letter-spacing=".1em">PRODUCING STRINGS</text><text x="12" y="34" fill="#fff" font-size="15" font-weight="800">{{ $snap['strings'] }} <tspan font-size="10" fill="#64748b" font-weight="600">with recent test</tspan></text></g>
        <g transform="translate(246 100)"><path d="M132 20 L158 40" stroke="#fbbf24" stroke-width="1"/>
            <rect width="132" height="42" rx="10" fill="#050a15" fill-opacity=".82" stroke="#fbbf24" stroke-opacity=".6"/>
            <text x="12" y="17" fill="#9fb0cf" font-size="9.5" letter-spacing=".1em">OIL RATE</text><text x="12" y="34" fill="#fbbf24" font-size="15" font-weight="800">{{ number_format($snap['oil'], 0) }} <tspan font-size="10" fill="#64748b" font-weight="600">B/D</tspan></text></g>
        <g transform="translate(722 106)"><path d="M26 42 L-14 54" stroke="#fb923c" stroke-width="1"/>
            <rect width="150" height="42" rx="10" fill="#050a15" fill-opacity=".82" stroke="#fb923c" stroke-opacity=".6"/>
            <text x="12" y="17" fill="#9fb0cf" font-size="9.5" letter-spacing=".1em">GAS (FLARE / SALES)</text><text x="12" y="34" fill="#fb923c" font-size="15" font-weight="800">{{ number_format($snap['gas'], 1) }} <tspan font-size="10" fill="#64748b" font-weight="600">MMscf/d</tspan></text></g>
        <g transform="translate(640 286)"><path d="M30 42 L10 48" stroke="#38bdf8" stroke-width="1" opacity="0"/>
            <rect width="132" height="42" rx="10" fill="#050a15" fill-opacity=".82" stroke="#38bdf8" stroke-opacity=".6"/>
            <text x="12" y="17" fill="#9fb0cf" font-size="9.5" letter-spacing=".1em">WATER CUT</text><text x="12" y="34" fill="#38bdf8" font-size="15" font-weight="800">{{ $snap['wc'] }}<tspan font-size="10" fill="#64748b" font-weight="600"> %  ·  export line</tspan></text></g>
    </g>
</svg>
