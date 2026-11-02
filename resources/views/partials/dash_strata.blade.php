{{-- Sub-surface strata under the platform cross-section: wellbores, reservoir, gas cap, water leg --}}
<div class="strata">
<svg viewBox="0 0 900 240" preserveAspectRatio="xMidYMin slice" role="img" aria-label="Sub-surface section: wellbores reaching the oil reservoir under a gas cap">
    <defs>
        <linearGradient id="st-bg" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#2a2114"/><stop offset=".5" stop-color="#1b1710"/><stop offset="1" stop-color="#0b0906"/></linearGradient>
        <linearGradient id="st-oil" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#8a5a08"/><stop offset=".5" stop-color="#c47f08"/><stop offset="1" stop-color="#6b4204"/></linearGradient>
        <linearGradient id="st-wat" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#14588f"/><stop offset="1" stop-color="#062a4d"/></linearGradient>
        <linearGradient id="st-gas" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#5b7a96" stop-opacity=".85"/><stop offset="1" stop-color="#7d93ad" stop-opacity=".55"/></linearGradient>
        <pattern id="st-sand" width="14" height="12" patternUnits="userSpaceOnUse"><circle cx="3" cy="3" r=".9" fill="#c9a96a" opacity=".4"/><circle cx="10" cy="8" r=".7" fill="#e0c88f" opacity=".35"/></pattern>
        <path id="st-b1" d="M150 0 V118"/><path id="st-b2" d="M215 0 V118"/>
    </defs>
    <rect width="900" height="240" fill="url(#st-bg)"/>
    <path d="M0 44 Q220 30 450 48 T900 40 V70 Q650 84 450 66 T0 74 Z" fill="#3a2f1c" opacity=".7"/>
    <path d="M0 70 Q220 58 450 74 T900 66 V92 Q650 104 450 90 T0 98 Z" fill="url(#st-sand)"/>
    {{-- reservoir trap (anticline) --}}
    <path d="M0 150 Q200 98 450 92 T900 146 V204 Q650 176 450 170 T0 210 Z" fill="url(#st-wat)"/>
    <path d="M60 146 Q240 100 450 94 T840 142 Q650 176 450 168 T60 190 Z" fill="url(#st-oil)"/>
    <path d="M200 124 Q320 100 450 96 T700 118 Q580 128 450 124 T200 134 Z" fill="url(#st-gas)"/>
    <path d="M200 124 Q320 100 450 96 T700 118" stroke="#cfe3f5" stroke-width="1" fill="none" opacity=".5"/>
    <path d="M60 146 Q240 100 450 94 T840 142" stroke="#fbbf24" stroke-width="1" fill="none" opacity=".6"/>
    <path d="M0 150 Q200 98 450 92 T900 146" stroke="#2a1d06" stroke-width="2" fill="none" opacity=".5"/>
    <g font-family="Inter,sans-serif" font-size="10" letter-spacing=".12em" fill="#e8eefc">
        <text x="452" y="114" text-anchor="middle" opacity=".85">GAS CAP</text>
        <text x="452" y="150" text-anchor="middle" fill="#fde6b0">OIL COLUMN</text>
        <text x="452" y="196" text-anchor="middle" fill="#9fd0f5">WATER LEG</text>
    </g>
    {{-- wellbores --}}
    @foreach([150, 215] as $bi => $bx)
        <path d="M{{ $bx }} 0 V{{ 120 - $bi * 6 }}" stroke="#05070b" stroke-width="7"/>
        <path d="M{{ $bx }} 0 V{{ 120 - $bi * 6 }}" stroke="#aab7cb" stroke-width="3.5"/>
        <path d="M{{ $bx - 1 }} 0 V{{ 120 - $bi * 6 }}" stroke="#fff" stroke-width=".8" opacity=".45"/>
        @for($p = 0; $p < 5; $p++)
            <path d="M{{ $bx - 9 }} {{ 108 - $bi * 6 + $p * 2 }} h5 M{{ $bx + 4 }} {{ 108 - $bi * 6 + $p * 2 }} h5" stroke="#fbbf24" stroke-width="1.4"/>
        @endfor
        @for($k = 0; $k < 4; $k++)
            <circle cx="{{ $bx }}" r="2.2" fill="#fbbf24"><animate attributeName="cy" values="{{ 116 - $bi * 6 }};0" dur="3.8s" begin="{{ $k * .95 + $bi * .4 }}s" repeatCount="indefinite"/><animate attributeName="opacity" values="0;1;1;0" keyTimes="0;.1;.85;1" dur="3.8s" begin="{{ $k * .95 + $bi * .4 }}s" repeatCount="indefinite"/></circle>
        @endfor
    @endforeach
    <g font-family="Inter,sans-serif" font-size="9.5" fill="#cbd5e1"><text x="168" y="34">Well bores</text><text x="700" y="64" fill="#a8946a">Cap rock &amp; sand</text></g>
    {{-- seismic-style pulses --}}
    <g fill="none" stroke="#22d3ee" stroke-width="1" opacity=".5"><circle cx="680" cy="150" r="4"><animate attributeName="r" values="4;60" dur="4.5s" repeatCount="indefinite"/><animate attributeName="opacity" values=".7;0" dur="4.5s" repeatCount="indefinite"/></circle></g>
</svg>
</div>
