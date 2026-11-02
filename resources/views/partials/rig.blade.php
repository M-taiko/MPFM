    <svg class="rig" viewBox="0 0 560 545" fill="none" aria-hidden="true">
        <defs>
            <radialGradient id="rgGlow" cx="50%" cy="50%" r="50%"><stop offset="0" stop-color="#2563eb" stop-opacity=".28"/><stop offset="1" stop-color="#2563eb" stop-opacity="0"/></radialGradient>
            <radialGradient id="rgFlame" cx="50%" cy="50%" r="50%"><stop offset="0" stop-color="#fbbf24" stop-opacity=".55"/><stop offset="1" stop-color="#f97316" stop-opacity="0"/></radialGradient>
            <linearGradient id="rgUg" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#10234a"/><stop offset="1" stop-color="#050a15"/></linearGradient>
            <linearGradient id="rgSteel" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="#16315f"/><stop offset=".5" stop-color="#274a8a"/><stop offset="1" stop-color="#16315f"/></linearGradient>
            <clipPath id="rgAbove"><rect x="0" y="0" width="560" height="394"/></clipPath>
        </defs>

        <circle cx="250" cy="300" r="250" fill="url(#rgGlow)"/>

        <!-- underground -->
        <rect x="0" y="490" width="560" height="55" fill="url(#rgUg)"/>
        <path d="M0 503 Q140 496 280 505 T560 500 M0 522 Q140 515 280 524 T560 519" stroke="#1e3a6e" stroke-width="1.5"/>
        <ellipse class="res" cx="250" cy="534" rx="130" ry="8" fill="#f59e0b" opacity=".3"/>
        <path d="M0 490 H560" stroke="#2f5db0" stroke-width="2.5"/>

        <!-- pumpjack (animated by script) -->
        <rect x="22" y="482" width="104" height="8" rx="2" fill="#12306a" stroke="#3b82f6" stroke-width="1.5"/>
        <rect x="6" y="474" width="14" height="16" rx="2" fill="#12306a" stroke="#22d3ee" stroke-width="1.5"/>
        <path d="M50 482 L70 420 L90 482 M57 462 H83" stroke="#60a5fa" stroke-width="3" stroke-linejoin="round"/>
        <rect x="92" y="464" width="30" height="20" rx="3" fill="#0d1b3a" stroke="#3b82f6" stroke-width="1.5"/>
        <line id="pjRod" x1="14" y1="438" x2="14" y2="476" stroke="#cbd5e1" stroke-width="2.5"/>
        <line id="pjPit" x1="122" y1="420" x2="115" y2="474" stroke="#94a3b8" stroke-width="3" stroke-linecap="round"/>
        <g id="pjCrank"><circle cx="107" cy="474" r="13" fill="#0d1b3a" stroke="#22d3ee" stroke-width="2"/><rect x="92" y="469" width="9" height="10" rx="2" fill="#fbbf24"/><circle cx="115" cy="474" r="3" fill="#22d3ee"/></g>
        <circle cx="107" cy="474" r="3" fill="#60a5fa"/>
        <g id="pjBeam"><rect x="16" y="415" width="110" height="9" rx="3" fill="url(#rgSteel)" stroke="#60a5fa" stroke-width="1.5"/><path d="M18 409 Q2 418 10 442 L22 442 Q18 426 30 416Z" fill="#12306a" stroke="#22d3ee" stroke-width="1.5"/></g>
        <circle cx="70" cy="420" r="5" fill="#22d3ee"/>

        <!-- engine house + exhaust smoke -->
        <rect x="326" y="446" width="78" height="44" rx="3" fill="#0a1630" stroke="#3b82f6" stroke-width="1.5"/>
        <path d="M322 446 H408 L398 434 H332Z" fill="#12306a" stroke="#60a5fa" stroke-width="1.5"/>
        <path d="M336 458 V484 M346 458 V484 M356 458 V484" stroke="#1e3a6e" stroke-width="3"/>
        <rect x="370" y="458" width="26" height="26" rx="3" fill="#12306a" stroke="#22d3ee" stroke-width="1.2"/>
        <path d="M383 462 V480 M374 471 H392" stroke="#22d3ee" stroke-width="1" class="blink"/>
        <path d="M350 434 V418" stroke="#64748b" stroke-width="5"/>
        <circle class="smoke" cx="350" cy="414" r="7" fill="#64748b"/>
        <circle class="smoke" style="animation-delay:1.3s" cx="350" cy="414" r="7" fill="#64748b"/>
        <circle class="smoke" style="animation-delay:2.6s" cx="350" cy="414" r="7" fill="#64748b"/>

        <!-- mud tanks + standpipe -->
        <rect x="414" y="458" width="96" height="32" rx="3" fill="#0a1630" stroke="#3b82f6" stroke-width="1.5"/>
        <path d="M414 470 H510" stroke="#1e3a6e"/>
        <path class="wv" d="M420 477 q6 -4 12 0 t12 0 t12 0 t12 0 t12 0 t12 0 t12 0" stroke="#b45309" stroke-width="2"/>
        <path d="M440 458 V436 H312 L281 150 H266" stroke="#7c3a06" stroke-width="5" stroke-linejoin="round"/>
        <path class="mud" d="M440 458 V436 H312 L281 150 H266" stroke="#fbbf24" stroke-width="2" stroke-dasharray="6 10" stroke-linejoin="round"/>

        <!-- flare stack -->
        <path d="M534 300 V490 M534 300 L510 490 M534 300 L552 490" stroke="#64748b" stroke-width="3"/>
        <path d="M526 300 H542 L539 308 H529Z" fill="#475a85" stroke="none"/>
        <circle class="gp" cx="534" cy="274" r="46" fill="url(#rgFlame)"/>
        <g class="fl">
            <path d="M534 300 C514 274 548 262 534 228 C564 258 560 286 534 300Z" fill="#f59e0b"/>
            <path d="M534 298 C524 282 541 274 534 252 C548 270 546 288 534 298Z" fill="#fde68a"/>
        </g>
        <circle class="smoke" style="animation-duration:5s" cx="534" cy="236" r="8" fill="#475569"/>
        <circle class="smoke" style="animation-duration:5s;animation-delay:2.5s" cx="534" cy="236" r="8" fill="#475569"/>

        <!-- derrick -->
        <path d="M190 490 L235 70 H265 L310 490Z" fill="rgba(37,99,235,.09)"/>
        <path d="M190.0 490.0H310.0M194.5 448.0H305.5M199.0 406.0H301.0M203.5 364.0H296.5M208.0 322.0H292.0M212.5 280.0H287.5M217.0 238.0H283.0M221.5 196.0H278.5M226.0 154.0H274.0M230.5 112.0H269.5M235.0 70.0H265.0" stroke="#3b82f6" stroke-width="1.6"/>
        <path d="M190.0 490.0L305.5 448.0M310.0 490.0L194.5 448.0M194.5 448.0L301.0 406.0M305.5 448.0L199.0 406.0M199.0 406.0L296.5 364.0M301.0 406.0L203.5 364.0M203.5 364.0L292.0 322.0M296.5 364.0L208.0 322.0M208.0 322.0L287.5 280.0M292.0 322.0L212.5 280.0M212.5 280.0L283.0 238.0M287.5 280.0L217.0 238.0M217.0 238.0L278.5 196.0M283.0 238.0L221.5 196.0M221.5 196.0L274.0 154.0M278.5 196.0L226.0 154.0M226.0 154.0L269.5 112.0M274.0 154.0L230.5 112.0M230.5 112.0L265.0 70.0M269.5 112.0L235.0 70.0" stroke="#2f63c7" stroke-width="1.3"/>
        <path d="M190 490 L235 70 M310 490 L265 70" stroke="#7fb4ff" stroke-width="3.6" stroke-linecap="round"/>
        <rect x="220" y="150" width="60" height="5" rx="1" fill="#22d3ee" opacity=".7"/>
        <circle class="twk" cx="199" cy="406" r="2.4" fill="#fbbf24"/><circle class="twk" style="animation-delay:.6s" cx="301" cy="406" r="2.4" fill="#fbbf24"/>
        <circle class="twk" style="animation-delay:1.2s" cx="208" cy="322" r="2.4" fill="#fbbf24"/><circle class="twk" style="animation-delay:.3s" cx="292" cy="322" r="2.4" fill="#fbbf24"/>
        <circle class="twk" style="animation-delay:.9s" cx="217" cy="238" r="2.4" fill="#fbbf24"/><circle class="twk" style="animation-delay:1.5s" cx="283" cy="238" r="2.4" fill="#fbbf24"/>
        <circle class="twk" style="animation-delay:.2s" cx="226" cy="154" r="2.4" fill="#fbbf24"/><circle class="twk" style="animation-delay:1s" cx="274" cy="154" r="2.4" fill="#fbbf24"/>

        <!-- crown block + beacon -->
        <rect x="226" y="54" width="48" height="15" rx="3" fill="url(#rgSteel)" stroke="#7fb4ff" stroke-width="1.5"/>
        <circle class="sh" cx="237" cy="61.5" r="5" stroke="#22d3ee" stroke-width="1.6" stroke-dasharray="4 3"/>
        <circle class="sh" cx="250" cy="61.5" r="5" stroke="#22d3ee" stroke-width="1.6" stroke-dasharray="4 3"/>
        <circle class="sh" cx="263" cy="61.5" r="5" stroke="#22d3ee" stroke-width="1.6" stroke-dasharray="4 3"/>
        <path d="M250 54 V38" stroke="#7fb4ff" stroke-width="2.5"/>
        <circle class="halo" cx="250" cy="34" r="8" fill="#ef4444"/>
        <circle class="beacon" cx="250" cy="34" r="4" fill="#ef4444"/>

        <!-- travelling block, cables, drill pipe (above rig floor) -->
        <g clip-path="url(#rgAbove)">
            <g class="cab" stroke="#cbd5e1" stroke-width="1.3"><path d="M240 66 V166 M246 66 V166 M254 66 V166 M260 66 V166"/></g>
            <g class="tb">
                <path d="M250 214 V520" stroke="#94a3b8" stroke-width="6"/>
                <rect x="246" y="262" width="8" height="7" fill="#e2e8f0"/><rect x="246" y="350" width="8" height="7" fill="#e2e8f0"/><rect x="246" y="438" width="8" height="7" fill="#e2e8f0"/>
                <rect x="235" y="166" width="30" height="22" rx="3" fill="#1e3a8a" stroke="#22d3ee" stroke-width="1.6"/>
                <circle class="sh" cx="243" cy="177" r="4" stroke="#22d3ee" stroke-width="1.3" stroke-dasharray="3 2.4"/>
                <circle class="sh" cx="257" cy="177" r="4" stroke="#22d3ee" stroke-width="1.3" stroke-dasharray="3 2.4"/>
                <rect x="240" y="188" width="20" height="26" rx="3" fill="#0f2a5e" stroke="#22d3ee" stroke-width="1.5"/>
                <circle cx="250" cy="201" r="4" fill="#22d3ee" class="blink"/>
            </g>
        </g>

        <!-- substructure, floor, doghouse -->
        <rect x="186" y="402" width="128" height="88" fill="#0a1630" stroke="#3b82f6" stroke-width="1.5"/>
        <path d="M186 402 L314 490 M314 402 L186 490" stroke="#1e4a99" stroke-width="1.5"/>
        <rect x="174" y="394" width="152" height="9" rx="2" fill="#16376e" stroke="#7fb4ff" stroke-width="1.5"/>
        <path d="M176 394 V378 H206 M324 394 V378 H294 M190 378 V394 M310 378 V394" stroke="#60a5fa" stroke-width="1.5"/>
        <rect x="176" y="362" width="32" height="32" rx="2" fill="#0d1b3a" stroke="#60a5fa" stroke-width="1.5"/>
        <path d="M174 362 H210 L192 350Z" fill="#12306a" stroke="#60a5fa" stroke-width="1.5"/>
        <rect class="win" x="184" y="371" width="14" height="10" rx="1" fill="#fbbf24"/>
        <ellipse class="tbl" cx="250" cy="397" rx="24" ry="5" stroke="#22d3ee" stroke-width="2" stroke-dasharray="5 4" fill="#0a1630"/>

        <!-- BOP + wellhead + pipe below floor -->
        <rect x="238" y="456" width="24" height="18" rx="3" fill="#16376e" stroke="#22d3ee" stroke-width="1.5"/>
        <rect x="233" y="462" width="34" height="6" rx="2" fill="#0d1b3a" stroke="#60a5fa" stroke-width="1.2"/>
        <rect x="242" y="474" width="16" height="16" fill="#12306a" stroke="#60a5fa" stroke-width="1.2"/>
        <path d="M250 400 V530" stroke="#94a3b8" stroke-width="6"/>
        <path class="mud" d="M250 400 V530" stroke="#e2e8f0" stroke-width="3" stroke-dasharray="3 13"/>
        <circle class="gp" cx="250" cy="532" r="15" fill="#22d3ee" opacity=".35"/>
        <path d="M244 526 H256 L250 538Z" fill="#22d3ee"/>

        <!-- floating callouts -->
        <g class="bob">
            <path d="M330 108 H281" stroke="#22d3ee" stroke-width="1" stroke-dasharray="3 3" opacity=".6"/>
            <rect x="330" y="95" width="124" height="26" rx="13" fill="#0b1a3a" stroke="#22d3ee" stroke-width="1.2"/>
            <circle class="blink" cx="346" cy="108" r="4" fill="#34d399"/>
            <text x="358" y="112" fill="#cfe0ff" font-family="Inter,sans-serif" font-size="10.5" font-weight="600">MPFM · ONLINE</text>
        </g>
        <g class="bob" style="animation-delay:-2s">
            <path d="M338 205 H280" stroke="#22d3ee" stroke-width="1" stroke-dasharray="3 3" opacity=".6"/>
            <rect x="338" y="192" width="146" height="26" rx="13" fill="#0b1a3a" stroke="#fbbf24" stroke-width="1.2"/>
            <circle class="blink" cx="354" cy="205" r="4" fill="#fbbf24"/>
            <text x="366" y="209" fill="#cfe0ff" font-family="Inter,sans-serif" font-size="10.5" font-weight="600">4,215 BOPD · RIG-01</text>
        </g>
    </svg>
