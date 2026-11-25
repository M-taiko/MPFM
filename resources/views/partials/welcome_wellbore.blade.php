{{-- Well integrity cross-section (decorative, animated). Centre line x = 250, ground level y = 90, 0.16 px per metre. --}}
@php
    $depths = [0 => 90, 500 => 170, 1000 => 250, 1500 => 330, 2000 => 410, 2500 => 490, 3000 => 570];
    $legend = [
        [1, 'Tree & master valves', 'Operated, turns counted, greased', 296, 44],
        [2, 'SCSSV', 'Function-tested on schedule', 261, 172],
        [3, 'Annulus A / B gauges', 'Pressure vs MAASP, bleed & rebuild', 325, 91],
        [4, 'Tubing & packer', 'Primary barrier envelope', 372, 430],
        [5, 'Casing & cement', 'Secondary barrier envelope', 272, 350],
        [6, 'Perforations', 'Oil · gas · water contacts', 300, 515],
    ];
@endphp
<style>
.wb-fig { padding:14px !important; }
.wb-fig svg text { font-family:'Inter',sans-serif; }
.wb-dash { stroke-dasharray:6 6; animation:wb-dash 1.4s linear infinite; } @keyframes wb-dash { to { stroke-dashoffset:-24; } }
.wb-pulse { transform-box:fill-box; transform-origin:50% 50%; animation:wb-pulse 2.2s ease-out infinite; } @keyframes wb-pulse { 0% { transform:scale(.6); opacity:.9; } 100% { transform:scale(2.4); opacity:0; } }
.wb-blink { animation:wb-bl 1.5s steps(1) infinite; } @keyframes wb-bl { 50% { opacity:.15; } }
.wb-pore { animation:wb-pore 3.6s ease-in-out infinite; } @keyframes wb-pore { 50% { opacity:.35; } }
@media (prefers-reduced-motion: reduce) { .wb-dash,.wb-pulse,.wb-blink,.wb-pore { animation:none; } }
</style>

<section id="integrity">
    <div class="wrap mod">
        <div class="txt rv">
            <div class="num">05 · WELL INTEGRITY</div>
            <h3>Know which barriers are holding, before a leak tells you</h3>
            <p>Every well carries two barrier envelopes. The platform tracks the annulus pressures against their MAASP, the operator's valve checks, barrier tests, corrosion logs and the anomaly register, then turns them into a Green, Amber or Red status that feeds the well analysis.</p>
            <ul class="ticks">
                <li>Operator wellhead report: valves operated, turns counted, grease, DBB on the annuli</li>
                <li>Annulus pressure trends with sustained-casing-pressure detection</li>
                <li>Barrier tests with due dates, plus corrosion and cement inspections</li>
                <li>Integrity status lowers the health score and adds recommendations</li>
            </ul>
            <div class="chips"><span class="chip">Barriers</span><span class="chip">Annulus pressure</span><span class="chip">Wellhead checks</span><span class="chip">Anomaly register</span></div>
        </div>
        <figure class="fig wb-fig rv">
            <svg viewBox="0 0 640 640" role="img" aria-label="Well cross-section showing the tree, annuli, casing strings, packer, SCSSV, perforations and the primary and secondary barrier envelopes">
                <defs>
                    <linearGradient id="wbSky" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#060d20"/><stop offset="1" stop-color="#14325e"/></linearGradient>
                    <linearGradient id="wbSteel" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="#1a3566"/><stop offset=".4" stop-color="#8fb4ee"/><stop offset="1" stop-color="#1a3566"/></linearGradient>
                    <linearGradient id="wbOil" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#7a4a10"/><stop offset="1" stop-color="#3d2308"/></linearGradient>
                    <linearGradient id="wbGas" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#14566a"/><stop offset="1" stop-color="#0d3a4b"/></linearGradient>
                    <linearGradient id="wbWat" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#16467a"/><stop offset="1" stop-color="#0a2447"/></linearGradient>
                    <pattern id="wbCem" width="6" height="6" patternUnits="userSpaceOnUse" patternTransform="rotate(45)"><rect width="6" height="6" fill="#59606f"/><path d="M0 0 V6" stroke="#2c3240" stroke-width="2"/></pattern>
                    <pattern id="wbSand" width="10" height="10" patternUnits="userSpaceOnUse"><rect width="10" height="10" fill="#3a3326"/><circle cx="2" cy="3" r=".9" fill="#5b4e36"/><circle cx="7" cy="7" r=".9" fill="#5b4e36"/></pattern>
                    <clipPath id="wbRes"><rect x="0" y="470" width="640" height="130"/></clipPath>
                </defs>

                <!-- sky + earth -->
                <rect width="640" height="90" fill="url(#wbSky)"/>
                <rect x="0" y="90" width="640" height="60" fill="url(#wbSand)"/>
                <rect x="0" y="150" width="640" height="110" fill="#262938"/>
                <rect x="0" y="260" width="640" height="70" fill="#34384c"/>
                <rect x="0" y="330" width="640" height="80" fill="#1b3c57"/>
                <rect x="0" y="410" width="640" height="60" fill="#2a2d3d"/>
                <rect x="0" y="470" width="640" height="18" fill="url(#wbGas)"/>
                <rect x="0" y="488" width="640" height="52" fill="url(#wbOil)"/>
                <rect x="0" y="540" width="640" height="60" fill="url(#wbWat)"/>
                <rect x="0" y="600" width="640" height="40" fill="#101018"/>
                <g clip-path="url(#wbRes)" fill="#f5c76a">@for($i = 0; $i < 46; $i++)<circle class="wb-pore" cx="{{ 14 + ($i * 53) % 612 }}" cy="{{ 492 + ($i * 29) % 46 }}" r="1.6" style="animation-delay:-{{ ($i % 9) * .4 }}s"/>@endfor</g>
                <g fill="none" stroke="#fff" stroke-opacity=".18"><path d="M0 150 H640 M0 260 H640 M0 330 H640 M0 410 H640 M0 470 H640"/><path d="M0 540 H640" stroke-dasharray="6 5" stroke="#7fb4ff" stroke-opacity=".55"/></g>
                <g font-size="10" font-weight="700" fill="#cfe0ff" opacity=".8" letter-spacing="1.4">
                    <text x="84" y="124" fill="#e6cfa0">SAND</text><text x="84" y="208">SHALE</text><text x="84" y="300">CAP ROCK</text>
                    <text x="84" y="376" fill="#7fc0ff">AQUIFER</text><text x="84" y="444">SHALE</text>
                    <text x="84" y="483" fill="#6de1f5">GAS</text><text x="84" y="518" fill="#ffd27a">OIL</text><text x="84" y="573" fill="#7fb4ff">WATER</text>
                </g>
                <text x="470" y="537" font-size="9" fill="#9cc8ff" opacity=".8">OWC</text>

                <!-- depth scale -->
                <g stroke="#9fb4d8" stroke-width="1"><path d="M30 90 V570"/>@foreach($depths as $m => $y)<path d="M30 {{ $y }} H{{ $m % 1000 === 0 ? 40 : 36 }}"/><text x="46" y="{{ $y + 3.5 }}" fill="#9fb4d8" font-size="9.5" stroke="none">{{ $m }} m</text>@endforeach</g>

                <!-- cement behind each casing -->
                <g fill="url(#wbCem)">
                    <rect x="212" y="90" width="5" height="40"/><rect x="283" y="90" width="5" height="40"/>
                    <rect x="217" y="90" width="5" height="160"/><rect x="278" y="90" width="5" height="160"/>
                    <rect x="223" y="190" width="5" height="210"/><rect x="272" y="190" width="5" height="210"/>
                    <rect x="229" y="330" width="5" height="230"/><rect x="266" y="330" width="5" height="230"/>
                </g>
                <!-- annulus fluids -->
                <rect x="243" y="100" width="7" height="370" fill="#6f8fc4" opacity=".28"/><rect x="250" y="100" width="7" height="370" fill="#6f8fc4" opacity=".28"/>
                <rect x="234" y="100" width="9" height="230" fill="#8c7aa8" opacity=".22"/><rect x="257" y="100" width="9" height="230" fill="#8c7aa8" opacity=".22"/>

                <!-- casing strings -->
                <g fill="none" stroke-linecap="round">
                    <path d="M212 90 V130 M288 90 V130" stroke="#6f8fb8" stroke-width="2.4"/>
                    <path d="M217 90 V250 M283 90 V250" stroke="#7d9bc8" stroke-width="2.4"/>
                    <path d="M223 90 V400 M277 90 V400" stroke="#8aa8d6" stroke-width="2.4"/>
                    <path d="M229 90 V560 M271 90 V560" stroke="#a6c1e8" stroke-width="2.6"/>
                    <path d="M212 130 l-6 3 M288 130 l6 3 M217 250 l-6 3 M283 250 l6 3 M223 400 l-6 3 M277 400 l6 3 M229 560 l-6 3 M271 560 l6 3" stroke="#cfe0ff" stroke-width="2.4"/>
                </g>
                <!-- tubing -->
                <path d="M243 90 V505 M257 90 V505" stroke="#e8f1ff" stroke-width="2.6" fill="none"/>
                <rect x="243.5" y="92" width="13" height="411" fill="#0a1226" opacity=".55"/>
                <!-- control line to SCSSV -->
                <path d="M240 100 V172" stroke="#fbbf24" stroke-width="1.2" stroke-dasharray="3 3" fill="none"/>
                <!-- SCSSV -->
                <rect x="239" y="160" width="22" height="26" rx="3" fill="#1d3a72" stroke="#7fb0ff" stroke-width="1.6"/>
                <g><animateTransform attributeName="transform" type="rotate" values="0 250 173;-70 250 173;0 250 173" keyTimes="0;.5;1" dur="6s" repeatCount="indefinite"/><path d="M250 173 H259" stroke="#fbbf24" stroke-width="2.4" stroke-linecap="round"/></g>
                <!-- packer -->
                <g><path d="M229 462 H243 V478 H229Z M257 462 H271 V478 H257Z" fill="#1f2937" stroke="#fbbf24" stroke-width="1.4"/><path d="M229 470 l-5 -5 v10z M271 470 l5 -5 v10z" fill="#cfe0ff"/></g>
                <!-- tailpipe + memory gauge on slickline -->
                <path d="M250 94 V452" stroke="#fde68a" stroke-width="1" stroke-dasharray="2 3" fill="none"/>
                <rect x="245" y="452" width="10" height="16" rx="3" fill="#122248" stroke="#fbbf24" stroke-width="1.4"/><circle cx="250" cy="457" r="1.8" fill="#34d399" class="wb-blink"/>
                <!-- perforations -->
                <g stroke="#fbbf24" stroke-width="2.2" stroke-linecap="round">@for($i = 0; $i < 5; $i++)<path d="M229 {{ 496 + $i * 8 }} H206 M271 {{ 496 + $i * 8 }} H294"/>@endfor</g>
                <g fill="#ff8a2a">@for($i = 0; $i < 5; $i++)<circle cx="{{ $i % 2 ? 204 : 200 }}" cy="{{ 496 + $i * 8 }}" r="2"/><circle cx="{{ $i % 2 ? 296 : 300 }}" cy="{{ 496 + $i * 8 }}" r="2"/>@endfor</g>

                <!-- barrier envelopes -->
                <path d="M241 90 V505 H259 V90" fill="none" stroke="#3b82f6" stroke-width="2.6" class="wb-dash" opacity=".95"/>
                <path d="M227 90 V562 H273 V90" fill="none" stroke="#34d399" stroke-width="2.2" class="wb-dash" opacity=".9"/>
                <g font-size="9.5" font-weight="800" letter-spacing="1.2">
                    <g transform="translate(160,420)"><rect x="-4" y="-12" width="96" height="17" rx="8.5" fill="#06122c" stroke="#34d399"/><text x="44" y="0" fill="#6ee7b7" text-anchor="middle">SECONDARY</text></g>
                    <g transform="translate(304,430)"><rect x="-4" y="-12" width="76" height="17" rx="8.5" fill="#06122c" stroke="#3b82f6"/><text x="34" y="0" fill="#93c5fd" text-anchor="middle">PRIMARY</text></g>
                </g>

                <!-- flow from reservoir to surface -->
                <path id="wbFlowPath" d="M206 515 L250 506 V70 V44 H330 H430" fill="none" stroke="none"/>
                @foreach([0, 1.5, 3, 4.5] as $d)
                <circle r="3.4" fill="#fbbf24" stroke="#7c4a05" stroke-width=".6"><animateMotion dur="6s" begin="{{ $d }}s" repeatCount="indefinite" keyTimes="0;1" calcMode="linear"><mpath href="#wbFlowPath"/></animateMotion><animate attributeName="opacity" values="0;1;1;0" keyTimes="0;.08;.92;1" dur="6s" begin="{{ $d }}s" repeatCount="indefinite"/></circle>
                @endforeach
                @foreach([0.7, 3.7] as $d)
                <circle r="3" fill="#22d3ee"><animateMotion dur="6s" begin="{{ $d }}s" repeatCount="indefinite"><mpath href="#wbFlowPath"/></animateMotion><animate attributeName="opacity" values="0;1;1;0" keyTimes="0;.08;.92;1" dur="6s" begin="{{ $d }}s" repeatCount="indefinite"/></circle>
                @endforeach

                <!-- inspection hot-spot -->
                <g><circle cx="229" cy="318" r="5" fill="#fb923c"/><circle class="wb-pulse" cx="229" cy="318" r="5" fill="none" stroke="#fb923c" stroke-width="1.6"/></g>

                <!-- wellhead -->
                <rect x="218" y="82" width="64" height="20" rx="3" fill="url(#wbSteel)" stroke="#a9c6f5"/>
                <rect x="212" y="98" width="76" height="6" rx="2" fill="#17294d" stroke="#6fa0e8"/>
                <path d="M218 91 H196 M282 91 H304" stroke="#8fb4e8" stroke-width="4" stroke-linecap="round"/>
                <g><circle cx="186" cy="91" r="11" fill="#08122b" stroke="#34d399" stroke-width="1.8"/><g><animateTransform attributeName="transform" type="rotate" values="-30 186 91;18 186 91;-30 186 91" dur="6s" repeatCount="indefinite"/><path d="M186 91 L186 83" stroke="#fbbf24" stroke-width="1.8" stroke-linecap="round"/></g>
                    <text x="186" y="112" text-anchor="middle" font-size="9" font-weight="700" fill="#9fe8c6">A · 0 psi</text></g>
                <g><circle cx="314" cy="91" r="11" fill="#08122b" stroke="#34d399" stroke-width="1.8"/><g><animateTransform attributeName="transform" type="rotate" values="-26 314 91;14 314 91;-26 314 91" dur="7s" repeatCount="indefinite"/><path d="M314 91 L314 83" stroke="#fbbf24" stroke-width="1.8" stroke-linecap="round"/></g>
                    <text x="314" y="112" text-anchor="middle" font-size="9" font-weight="700" fill="#9fe8c6">B · 0 psi</text></g>
                <!-- tree: master valves, wing valve, swab cap -->
                <rect x="244" y="30" width="12" height="52" fill="#0d1a38" stroke="#8fb4e8"/>
                <rect x="237" y="64" width="26" height="13" rx="3" fill="url(#wbSteel)" stroke="#a9c6f5"/>
                <rect x="237" y="46" width="26" height="13" rx="3" fill="url(#wbSteel)" stroke="#a9c6f5"/>
                <rect x="241" y="22" width="18" height="9" rx="2" fill="#27487f" stroke="#a9c6f5"/>
                <rect x="256" y="38" width="58" height="12" fill="#0d1a38" stroke="#8fb4e8"/><rect x="278" y="33" width="18" height="22" rx="3" fill="url(#wbSteel)" stroke="#a9c6f5"/>
                <path d="M314 44 H430" stroke="#8fb4e8" stroke-width="6" stroke-linecap="round"/><path class="wb-dash" d="M314 44 H430" stroke="#fbbf24" stroke-width="2" fill="none"/>
                <path d="M424 38 L436 44 L424 50Z" fill="#fbbf24"/><text x="440" y="48" font-size="10" font-weight="700" fill="#cfe0ff">to separator</text>
                <!-- hand wheels (turned during the operator check) -->
                <g><circle cx="224" cy="70" r="9" fill="none" stroke="#cfe0ff" stroke-width="2"/><path d="M215 70 H233 M224 61 V79" stroke="#cfe0ff" stroke-width="1.6"/><animateTransform attributeName="transform" type="rotate" from="0 224 70" to="360 224 70" dur="8s" repeatCount="indefinite"/></g>
                <path d="M233 70 H237" stroke="#cfe0ff" stroke-width="2"/>
                <g><circle cx="224" cy="52" r="9" fill="none" stroke="#cfe0ff" stroke-width="2"/><path d="M215 52 H233 M224 43 V61" stroke="#cfe0ff" stroke-width="1.6"/><animateTransform attributeName="transform" type="rotate" from="360 224 52" to="0 224 52" dur="9s" repeatCount="indefinite"/></g>
                <path d="M233 52 H237" stroke="#cfe0ff" stroke-width="2"/>
                <g><circle cx="287" cy="22" r="9" fill="none" stroke="#cfe0ff" stroke-width="2"/><path d="M278 22 H296 M287 13 V31" stroke="#cfe0ff" stroke-width="1.6"/><animateTransform attributeName="transform" type="rotate" from="0 287 22" to="360 287 22" dur="10s" repeatCount="indefinite"/></g>
                <path d="M287 31 V33" stroke="#cfe0ff" stroke-width="2"/>
                <!-- tree gauge -->
                <path d="M250 22 V14" stroke="#8fb4e8" stroke-width="2.4"/><circle cx="250" cy="8" r="7" fill="#08122b" stroke="#fbbf24" stroke-width="1.6"/>
                <g><animateTransform attributeName="transform" type="rotate" values="-40 250 8;30 250 8;-10 250 8;-40 250 8" dur="8s" repeatCount="indefinite"/><path d="M250 8 V2" stroke="#fbbf24" stroke-width="1.6" stroke-linecap="round"/></g>

                <!-- legend with leader lines -->
                @foreach($legend as [$n, $t, $d, $tx, $ty])
                @php $ly = 128 + ($n - 1) * 74; @endphp
                <g>
                    <path d="M418 {{ $ly }} H398 L{{ $tx }} {{ $ty }}" fill="none" stroke="#cfe0ff" stroke-opacity=".42" stroke-dasharray="3 3"/><circle cx="{{ $tx }}" cy="{{ $ty }}" r="2.4" fill="#22d3ee"/>
                    <circle cx="430" cy="{{ $ly }}" r="12" fill="#0a1634" stroke="#22d3ee" stroke-width="1.6"/><text x="430" y="{{ $ly + 4.5 }}" text-anchor="middle" font-size="12" font-weight="800" fill="#e8f4ff">{{ $n }}</text>
                    <text x="450" y="{{ $ly - 2 }}" font-size="12.5" font-weight="700" fill="#f1f6ff">{{ $t }}</text>
                    <text x="450" y="{{ $ly + 14 }}" font-size="10.5" fill="#a9bddf">{{ $d }}</text>
                </g>
                @endforeach
            </svg>
            <figcaption>Illustration: primary envelope (blue) and secondary envelope (green) around the producing string.</figcaption>
        </figure>
    </div>
</section>
