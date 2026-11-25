{{-- Offshore + onshore panorama (decorative, animated). Uses only inline SVG + CSS/SMIL animation. --}}
@php
    mt_srand(11);
    $stars = [];
    for ($i = 0; $i < 48; $i++) { $stars[] = [mt_rand(8, 1192), mt_rand(8, 215), mt_rand(5, 14) / 10, mt_rand(0, 40) / 10]; }
    $wave = 'M-80 0 q20 -7 40 0' . str_repeat(' t40 0', 36) . ' V600 H-80 Z';
    $chip = fn ($x, $y, $w, $t, $tx, $ty, $ax = null) => '<g class="sc-tag"><path d="M' . ($ax ?? $x + $w / 2) . ' ' . ($y + 22) . ' L' . $tx . ' ' . $ty . '" /><circle cx="' . $tx . '" cy="' . $ty . '" r="2.6"/>'
        . '<rect x="' . $x . '" y="' . $y . '" width="' . $w . '" height="22" rx="11"/><text x="' . ($x + $w / 2) . '" y="' . ($y + 15) . '" text-anchor="middle">' . $t . '</text></g>';
    $hud = fn ($x, $y, $w, $c, $t, $a, $b, $spark) => '<g transform="translate(' . $x . ',' . $y . ')"><rect width="' . $w . '" height="54" rx="10" fill="rgba(7,16,38,.86)" stroke="' . $c . '" stroke-opacity=".6"/>'
        . '<circle cx="12" cy="13" r="3" fill="' . $c . '" class="sc-blink"/><text x="22" y="16.5" font-size="9" font-weight="800" letter-spacing="1.2" fill="' . $c . '">' . $t . '</text>'
        . '<text x="12" y="33" font-size="11.5" font-weight="700" fill="#eef4ff">' . $a . '</text><text x="12" y="47" font-size="9.5" fill="#9fb4d8">' . $b . '</text>'
        . '<path class="sc-spark" d="' . $spark . '" transform="translate(' . ($w - 60) . ',20)" fill="none" stroke="' . $c . '" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></g>';
@endphp
<style>
.sc-fig { padding:10px !important; overflow:hidden; }
.sc-fig svg text { font-family:'Inter',sans-serif; }
.sc-tag path { stroke:rgba(207,224,255,.55); stroke-width:1; stroke-dasharray:3 3; fill:none; }
.sc-tag circle { fill:#22d3ee; }
.sc-tag rect { fill:rgba(8,16,36,.9); stroke:rgba(120,160,230,.5); }
.sc-tag text { fill:#d6e4ff; font-size:11px; font-weight:600; }
.sc-star { animation:sc-tw 3.4s ease-in-out infinite; }
@keyframes sc-tw { 0%,100% { opacity:.25; } 50% { opacity:1; } }
.sc-wv { animation:sc-wave 6s linear infinite; } .sc-wv.b { animation-duration:8s; animation-direction:reverse; } .sc-wv.c { animation-duration:5s; }
@keyframes sc-wave { to { transform:translateX(-40px); } }
.sc-flame { transform-box:fill-box; transform-origin:50% 100%; animation:sc-flame .55s ease-in-out infinite alternate; }
.sc-flame.in { animation-duration:.4s; animation-delay:-.2s; }
@keyframes sc-flame { 0% { transform:scale(.88,.82) skewX(-4deg); } 100% { transform:scale(1.08,1.18) skewX(5deg); } }
.sc-glow { animation:sc-glow 1.1s ease-in-out infinite alternate; } @keyframes sc-glow { to { opacity:.35; } }
.sc-blink { animation:sc-bl 1.6s steps(1) infinite; } @keyframes sc-bl { 50% { opacity:.12; } }
.sc-win { animation:sc-win 5s ease-in-out infinite; } @keyframes sc-win { 50% { opacity:.55; } }
.sc-flow { stroke-dasharray:5 9; animation:sc-flow 1.1s linear infinite; } @keyframes sc-flow { to { stroke-dashoffset:-28; } }
.sc-flow.up { animation-name:sc-flowup; } @keyframes sc-flowup { to { stroke-dashoffset:28; } }
.sc-ship { animation:sc-ship 44s ease-in-out infinite alternate; } @keyframes sc-ship { from { transform:translateX(0); } to { transform:translateX(200px); } }
.sc-bob { animation:sc-bob 3.4s ease-in-out infinite; transform-box:fill-box; transform-origin:50% 100%; } @keyframes sc-bob { 50% { transform:translateY(-1.6px) rotate(.8deg); } }
.sc-cloud { animation:sc-cloud 90s linear infinite; } @keyframes sc-cloud { from { transform:translateX(-160px); } to { transform:translateX(1360px); } }
.sc-truck { animation:sc-truck 22s linear infinite; } @keyframes sc-truck { 0% { transform:translateX(580px); opacity:0; } 6% { opacity:1; } 94% { opacity:1; } 100% { transform:translateX(1110px); opacity:0; } }
.sc-glint { animation:sc-glint 3s ease-in-out infinite; } @keyframes sc-glint { 50% { opacity:.25; } }
.sc-ray { animation:sc-ray 7s ease-in-out infinite; } .sc-ray.b { animation-delay:-3s; animation-duration:9s; } @keyframes sc-ray { 50% { opacity:.02; } }
.sc-sway { transform-box:fill-box; transform-origin:50% 100%; animation:sc-sway 4s ease-in-out infinite alternate; } @keyframes sc-sway { from { transform:skewX(-7deg); } to { transform:skewX(7deg); } }
.sc-fish { animation:sc-fish 16s ease-in-out infinite alternate; } @keyframes sc-fish { from { transform:translateX(0); } to { transform:translateX(150px); } }
.sc-bub { animation:sc-bub 5s ease-in infinite; } @keyframes sc-bub { 0% { transform:translateY(0); opacity:0; } 15% { opacity:.8; } 100% { transform:translateY(-120px); opacity:0; } }
.sc-smoke { transform-box:fill-box; transform-origin:50% 50%; animation:sc-smoke 4.2s ease-out infinite; } @keyframes sc-smoke { 0% { transform:translate(0,0) scale(.6); opacity:.6; } 100% { transform:translate(26px,-50px) scale(2.8); opacity:0; } }
.sc-spark { stroke-dasharray:90; animation:sc-spark 3.2s ease-in-out infinite; } @keyframes sc-spark { 0% { stroke-dashoffset:90; } 55%,100% { stroke-dashoffset:0; } }
.sc-gull { animation:sc-gull 38s linear infinite; } @keyframes sc-gull { from { transform:translateX(0); } to { transform:translateX(420px); } }
.sc-wing { transform-box:fill-box; transform-origin:50% 100%; animation:sc-wing .7s ease-in-out infinite alternate; } @keyframes sc-wing { to { transform:scaleY(.35); } }
.sc-shoot { animation:sc-shoot 9s ease-in infinite; opacity:0; } @keyframes sc-shoot { 0% { transform:translate(0,0); opacity:0; } 4% { opacity:1; } 12% { transform:translate(-190px,62px); opacity:0; } 100% { opacity:0; } }
.sc-ring { transform-box:fill-box; transform-origin:50% 50%; animation:sc-ringp 3.6s ease-out infinite; } @keyframes sc-ringp { 0% { transform:scale(.5); opacity:.8; } 100% { transform:scale(3.4); opacity:0; } }
.sc-tk { animation:sc-tk 34s linear infinite; } @keyframes sc-tk { 0%,3% { transform:translateX(0); } 44%,53% { transform:translateX(430px); } 94%,100% { transform:translateX(0); } }
.sc-tkf { transform-box:fill-box; transform-origin:50% 50%; animation:sc-tkf 34s steps(1,end) infinite; } @keyframes sc-tkf { 0% { transform:scaleX(1); } 49% { transform:scaleX(-1); } 98% { transform:scaleX(1); } }
.sc-hose { animation:sc-hose 34s linear infinite; opacity:0; } @keyframes sc-hose { 0%,41% { opacity:0; } 44%,52% { opacity:1; } 55%,100% { opacity:0; } }
.sc-pop { opacity:0; animation:sc-pop 7.5s ease-in-out forwards; } @keyframes sc-pop { 0% { opacity:0; transform:translateY(6px); } 8% { opacity:1; transform:none; } 90% { opacity:1; } 100% { opacity:0; } }
@media (prefers-reduced-motion: reduce) { .sc-star,.sc-wv,.sc-flame,.sc-glow,.sc-blink,.sc-win,.sc-flow,.sc-ship,.sc-bob,.sc-cloud,.sc-tk,.sc-tkf,.sc-hose,.sc-glint,.sc-ray,.sc-sway,.sc-fish,.sc-bub,.sc-smoke,.sc-spark,.sc-gull,.sc-wing,.sc-shoot,.sc-ring { animation:none; } }
</style>

<section id="sites">
    <div class="wrap">
        <div class="center rv"><div class="kicker">Offshore · Onshore</div><h2 class="sh">One workflow, wherever the well is</h2>
            <p class="sp">Platform wells and subsea tie-backs, desert wellsites and tank farms: tests, surveys and integrity checks are recorded the same way and land on the same well record.</p></div>
        <figure class="fig sc-fig rv" style="margin-top:44px">
            <svg viewBox="0 0 1200 520" role="img" aria-label="Offshore production platform with subsea wells on the left and an onshore wellsite with wellhead trees, MPFM, test separator and tank farm on the right">
                <defs>
                    <linearGradient id="scSky" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#030816"/><stop offset=".5" stop-color="#0a1b3f"/><stop offset=".82" stop-color="#1c4a80"/><stop offset="1" stop-color="#f2a64f"/></linearGradient>
                    <linearGradient id="scSea" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#1b6a9d"/><stop offset=".35" stop-color="#0d3a68"/><stop offset="1" stop-color="#030f26"/></linearGradient>
                    <linearGradient id="scGround" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#3b2f3a"/><stop offset=".4" stop-color="#241d2b"/><stop offset="1" stop-color="#0d0c14"/></linearGradient>
                    <linearGradient id="scSteel" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#8fb4ee"/><stop offset=".45" stop-color="#2d5aa8"/><stop offset="1" stop-color="#14305f"/></linearGradient>
                    <linearGradient id="scTank" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="#1a3566"/><stop offset=".35" stop-color="#6c97dd"/><stop offset=".7" stop-color="#2b5496"/><stop offset="1" stop-color="#13284f"/></linearGradient>
                    <linearGradient id="scOil" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#fde68a"/><stop offset="1" stop-color="#f97316"/></linearGradient>
                    <linearGradient id="scRefl" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#ffcf7a" stop-opacity=".75"/><stop offset="1" stop-color="#ffcf7a" stop-opacity="0"/></linearGradient>
                    <radialGradient id="scSun" cx=".5" cy=".5" r=".5"><stop offset="0" stop-color="#fff3c9"/><stop offset=".25" stop-color="#ffc766" stop-opacity=".85"/><stop offset="1" stop-color="#ff9a3c" stop-opacity="0"/></radialGradient>
                    <radialGradient id="scFl" cx=".5" cy=".7" r=".6"><stop offset="0" stop-color="#ffe9a8" stop-opacity=".85"/><stop offset="1" stop-color="#ff8a2a" stop-opacity="0"/></radialGradient>
                    <linearGradient id="scFire" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#ffe27a"/><stop offset=".6" stop-color="#ff8a1f"/><stop offset="1" stop-color="#ff3d1f"/></linearGradient>
                    <clipPath id="scSeaClip"><rect x="0" y="300" width="640" height="220"/></clipPath>
                    <path id="scFlame" d="M0 0 C-8 -10 -4 -20 0 -32 C4 -20 8 -10 0 0Z"/>
                    <linearGradient id="scCone" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#ffe9a8" stop-opacity=".42"/><stop offset="1" stop-color="#ffe9a8" stop-opacity="0"/></linearGradient>
                    <linearGradient id="scStreak" x1="1" y1="0" x2="0" y2="0"><stop offset="0" stop-color="#fff"/><stop offset="1" stop-color="#fff" stop-opacity="0"/></linearGradient>
                </defs>

                <!-- sky -->
                <rect width="1200" height="320" fill="url(#scSky)"/>
                @foreach($stars as $s)<circle class="sc-star" cx="{{ $s[0] }}" cy="{{ $s[1] }}" r="{{ $s[2] }}" fill="#dbe8ff" style="animation-delay:-{{ $s[3] }}s"/>@endforeach
                <circle cx="150" cy="70" r="15" fill="#e8f0ff" opacity=".92"/><circle cx="158" cy="64" r="14" fill="#07122b"/>
                <circle cx="610" cy="300" r="170" fill="url(#scSun)" opacity=".8"/>
                <circle cx="610" cy="304" r="44" fill="#ffe5a3"/>
                <g fill="#9fb8e6" opacity=".14"><g class="sc-cloud"><ellipse cx="0" cy="120" rx="90" ry="12"/><ellipse cx="40" cy="112" rx="60" ry="10"/></g>
                    <g class="sc-cloud" style="animation-delay:-45s"><ellipse cx="0" cy="170" rx="110" ry="10"/><ellipse cx="-30" cy="164" rx="60" ry="9"/></g></g>
                <g class="sc-shoot"><path d="M880 22 L930 8" stroke="url(#scStreak)" stroke-width="1.6" stroke-linecap="round"/></g>
                <g class="sc-gull" opacity=".85"><g transform="translate(40,206)"><path class="sc-wing" d="M-7 0 Q-3.5 -6 0 0 Q3.5 -6 7 0" fill="none" stroke="#dbe8ff" stroke-width="1.5" stroke-linecap="round"/></g>
                    <g transform="translate(70,190)"><path class="sc-wing" style="animation-delay:-.3s" d="M-6 0 Q-3 -5 0 0 Q3 -5 6 0" fill="none" stroke="#dbe8ff" stroke-width="1.4" stroke-linecap="round"/></g>
                    <g transform="translate(18,176)"><path class="sc-wing" style="animation-delay:-.5s" d="M-5 0 Q-2.5 -4 0 0 Q2.5 -4 5 0" fill="none" stroke="#dbe8ff" stroke-width="1.3" stroke-linecap="round"/></g></g>
                <g transform="translate(612,40)">
                    <circle r="10" fill="none" stroke="#22d3ee" class="sc-ring"/><circle r="10" fill="none" stroke="#22d3ee" class="sc-ring" style="animation-delay:-1.8s"/>
                    <g><animateTransform attributeName="transform" type="rotate" values="-6 0 0;6 0 0;-6 0 0" dur="9s" repeatCount="indefinite"/>
                        <rect x="-7" y="-5" width="14" height="10" rx="2" fill="#cfe0ff"/><rect x="-32" y="-3" width="22" height="6" fill="#2d5aa8" stroke="#8fb4e8"/><rect x="10" y="-3" width="22" height="6" fill="#2d5aa8" stroke="#8fb4e8"/>
                        <path d="M-27 -3 V3 M-21 -3 V3 M-15 -3 V3 M15 -3 V3 M21 -3 V3 M27 -3 V3" stroke="#0b1530" stroke-width=".8"/><path d="M0 5 L0 11" stroke="#8fb4e8"/><circle cx="0" cy="12" r="3" fill="#fbbf24" class="sc-blink"/></g>
                </g>

                <!-- distant mountains -->
                <path d="M640 314 L710 266 L752 292 L830 236 L902 284 L980 252 L1050 288 L1120 258 L1200 280 V316Z" fill="#0f1b3b"/>
                <path d="M640 314 L760 282 L860 300 L960 274 L1080 300 L1200 286 V316Z" fill="#162650" opacity=".9"/>
                @foreach([[710, 266, 3.6], [830, 236, 4.2], [980, 252, 3.2], [1120, 258, 4.6]] as [$wx, $wy, $wd])
                <g transform="translate({{ $wx }},{{ $wy }})"><path d="M0 0 V-22" stroke="#9db8e4" stroke-width="1.7"/>
                    <g><animateTransform attributeName="transform" type="rotate" from="0 0 -22" to="360 0 -22" dur="{{ $wd }}s" repeatCount="indefinite"/><path d="M0 -22 V-34 M0 -22 L10.4 -16 M0 -22 L-10.4 -16" stroke="#e4efff" stroke-width="1.5" stroke-linecap="round"/></g>
                    <circle cx="0" cy="-22" r="1.6" fill="#cfe0ff"/><circle cx="0" cy="-23" r="1.2" fill="#f87171" class="sc-blink" style="animation-delay:-{{ $wd / 2 }}s"/></g>
                @endforeach

                <!-- sea -->
                <rect x="0" y="300" width="640" height="220" fill="url(#scSea)"/>
                <ellipse class="sc-glint" cx="578" cy="334" rx="13" ry="32" fill="url(#scRefl)"/>
                <g clip-path="url(#scSeaClip)"><g transform="translate(0,303) scale(1,.34)"><path class="sc-wv b" d="{{ $wave }}" fill="#2b86bd" opacity=".35"/></g></g>

                <!-- FPSO -->
                <g transform="translate(458,281)">
                    <path d="M70 22 L50 188 M70 22 L96 188" stroke="#3b6aa8" stroke-width="1" stroke-dasharray="4 4" opacity=".45" fill="none"/>
                    <path d="M0 10 H112 L104 22 H8Z" fill="#1a2a4c" stroke="#6f95d6"/><rect x="2" y="8" width="108" height="3" fill="#2b4a86"/>
                    <rect x="4" y="-6" width="22" height="14" fill="#2a4a84" stroke="#8fb4e8"/><rect x="8" y="-12" width="14" height="6" fill="#33569a" stroke="#8fb4e8"/>
                    <rect class="sc-win" x="7" y="-3" width="4" height="4" fill="#fde68a"/><rect class="sc-win" x="13" y="-3" width="4" height="4" fill="#fde68a" style="animation-delay:-1s"/><rect class="sc-win" x="19" y="-3" width="4" height="4" fill="#fde68a" style="animation-delay:-2s"/>
                    <path d="M15 -12 V-24" stroke="#cfe0ff" stroke-width="1.4"/><circle cx="15" cy="-25" r="1.8" fill="#f87171" class="sc-blink"/>
                    <rect x="32" y="1" width="14" height="7" fill="#27487f" stroke="#6fa0e8"/><rect x="50" y="-4" width="18" height="12" fill="#244273" stroke="#6fa0e8"/><rect x="72" y="1" width="12" height="7" fill="#27487f" stroke="#6fa0e8"/>
                    <path d="M52 -4 V-10 M62 -4 V-8 M32 2 H84" stroke="#8fb4e8" stroke-width="1.4"/>
                    <circle cx="94" cy="3" r="6" fill="#0f2043" stroke="#fbbf24" stroke-width="1.4"/><circle cx="94" cy="3" r="2" fill="#fbbf24"/>
                    <path d="M106 10 V-18 M110 10 V-18 M106 -4 H110 M106 -12 H110" stroke="#8fb4e8" stroke-width="1.3" fill="none"/>
                    <circle cx="108" cy="-26" r="14" fill="url(#scFl)" class="sc-glow"/>
                    <g transform="translate(108,-17) scale(.42)"><use href="#scFlame" class="sc-flame" fill="url(#scFire)"/></g>
                    @foreach([34, 56, 78, 100] as $lx)<circle cx="{{ $lx }}" cy="9" r="1.1" fill="#fde68a" class="sc-win" style="animation-delay:-{{ $lx / 30 }}s"/>@endforeach
                </g>
<!-- seabed, flowlines, subsea trees -->
                <path d="M0 502 C90 488 190 498 290 494 C390 490 480 500 640 494 V520 H0Z" fill="#061a36"/>
                <path d="M0 512 C140 502 300 510 450 506 C540 503 600 509 640 506 V520 H0Z" fill="#04122a"/>
                <g fill="none" stroke-linecap="round">
                    <path d="M139 490 H262" stroke="#0e2a4f" stroke-width="5"/><path class="sc-flow" d="M139 490 H262" stroke="#fbbf24" stroke-width="2"/>
                    <path d="M461 490 H318" stroke="#0e2a4f" stroke-width="5"/><path class="sc-flow" d="M461 490 H318" stroke="#22d3ee" stroke-width="2"/>
                    <path d="M330 495 H540 C580 495 583 468 590 440 C597 414 622 410 650 410" stroke="#0e2a4f" stroke-width="5"/>
                    <path class="sc-flow" d="M330 495 H540 C580 495 583 468 590 440 C597 414 622 410 650 410" stroke="#fb923c" stroke-width="2"/>
                </g>
                @foreach([130, 470] as $tx)
                <g transform="translate({{ $tx }},490)"><rect x="-10" y="-4" width="20" height="6" rx="2" fill="#16294d" stroke="#8fb4e8"/><path d="M0 -4 V-24 M-9 -15 H9" stroke="#8fb4e8" stroke-width="3" stroke-linecap="round"/>
                    <rect x="-6" y="-32" width="12" height="9" rx="2" fill="#1c2f55" stroke="#34d399"/><circle cx="0" cy="-37" r="2" fill="#34d399" class="sc-blink"/></g>
                @endforeach

                <!-- underwater life -->
                <g clip-path="url(#scSeaClip)" fill="#cdeeff">
                    <path class="sc-ray" d="M60 300 L130 300 L80 500 L-20 500Z" opacity=".07"/><path class="sc-ray b" d="M470 300 L540 300 L450 500 L370 500Z" opacity=".06"/>
                    <path class="sc-ray b" d="M150 300 L200 300 L170 500 L90 500Z" opacity=".05"/><path class="sc-ray" d="M560 300 L600 300 L540 500 L500 500Z" opacity=".05"/>
                </g>
                <g stroke="#1f9a6e" stroke-width="3" fill="none" stroke-linecap="round" opacity=".85">
                    <path class="sc-sway" d="M56 504 q-6 -14 0 -28 q6 -14 0 -30"/><path class="sc-sway" style="animation-delay:-1.3s" d="M70 504 q6 -12 0 -24 q-6 -12 0 -24"/>
                    <path class="sc-sway" style="animation-delay:-2.1s" d="M556 506 q-6 -14 0 -28 q6 -14 0 -30"/><path class="sc-sway" style="animation-delay:-.7s" d="M572 506 q6 -10 0 -22"/>
                </g>
                @foreach([[60, 392, 0], [96, 408, -2], [40, 428, -5], [130, 380, -7], [84, 440, -9], [150, 420, -11]] as [$fx, $fy, $fd])
                <g class="sc-fish" style="animation-delay:{{ $fd }}s"><g transform="translate({{ $fx }},{{ $fy }})"><path d="M0 0 q8 -5 14 0 q-6 5 -14 0 M14 0 l5 -4 v8z" fill="#8ad8ff" opacity=".8"/><circle cx="3.2" cy="-.8" r=".9" fill="#04122a"/></g></g>
                @endforeach
                @foreach([[100, 0], [118, -2], [212, -1.2], [488, -3], [512, -.6], [536, -2.4], [300, -4]] as [$bx, $bd])
                <circle class="sc-bub" cx="{{ $bx }}" cy="492" r="1.8" fill="none" stroke="#cdeeff" style="animation-delay:{{ $bd }}s"/>
                @endforeach
                <path d="M206 468 C172 500 132 496 128 458" fill="none" stroke="#a78bfa" stroke-width="2" stroke-dasharray="6 4" opacity=".85"/>
                <g transform="translate(196,490)"><rect x="-16" y="-8" width="32" height="9" rx="2" fill="#16294d" stroke="#8fb4e8"/><path d="M-10 -8 V-20 M0 -8 V-24 M10 -8 V-18" stroke="#8fb4e8" stroke-width="3" stroke-linecap="round"/>
                    <circle cx="-10" cy="-21" r="3" fill="#0b1530" stroke="#fbbf24"/><circle cx="0" cy="-25" r="3" fill="#0b1530" stroke="#34d399"/><circle cx="10" cy="-19" r="3" fill="#0b1530" stroke="#fbbf24"/><circle cx="12" cy="-3" r="1.4" fill="#34d399" class="sc-blink"/></g>
                <g transform="translate(540,492)"><rect x="-13" y="-10" width="26" height="10" rx="2" fill="#16294d" stroke="#8fb4e8"/><path d="M-13 -5 H-22 M13 -5 H22" stroke="#8fb4e8" stroke-width="3"/><circle cx="0" cy="-14" r="3.4" fill="#0b1530" stroke="#fb923c"/><circle cx="8" cy="-4" r="1.4" fill="#fb923c" class="sc-blink"/></g>
                <path d="M178 424 C170 380 184 340 178 304" fill="none" stroke="#e8f4ff" stroke-width="1" stroke-dasharray="2 3" opacity=".6"/>
                <g><animateTransform attributeName="transform" type="translate" values="0 0;0 -4;3 0;0 0" dur="5s" repeatCount="indefinite"/>
                    <polygon points="162,432 118,466 112,440" fill="#fff4be" opacity=".2"/>
                    <g transform="translate(178,428)"><rect x="-17" y="-8" width="34" height="15" rx="6" fill="#f5c518" stroke="#7a5a00"/><rect x="-12" y="-12" width="24" height="5" rx="2" fill="#2a2a2a"/>
                        <path d="M17 2 L27 9 M27 9 l3 -4 M27 9 l4 3" stroke="#cfd6e4" stroke-width="2" fill="none" stroke-linecap="round"/>
                        <circle cx="-15" cy="-10" r="3.4" fill="#0b1530" stroke="#fff"/><circle cx="15" cy="-10" r="3.4" fill="#0b1530" stroke="#fff"/><circle cx="0" cy="9" r="3.4" fill="#0b1530" stroke="#fff"/>
                        <circle cx="-14" cy="1" r="2.6" fill="#fff4be" class="sc-glow"/><circle cx="12" cy="-1" r="1.4" fill="#34d399" class="sc-blink"/></g>
                </g>
<!-- jacket -->
                <g stroke="#86abdf" fill="none" stroke-linecap="round">
                    <path d="M232 252 L205 498 M348 252 L375 498" stroke-width="4.5"/>
                    <path d="M226.7 300 H353.3 M220.2 360 H359.8 M213.6 420 H366.4 M207 480 H373" stroke-width="2.2"/>
                    <path d="M232 252 L353.3 300 M348 252 L226.7 300 M226.7 300 L359.8 360 M353.3 300 L220.2 360 M220.2 360 L366.4 420 M359.8 360 L213.6 420 M213.6 420 L373 480 M366.4 420 L207 480" stroke-width="1.7" opacity=".85"/>
                </g>
                <rect x="196" y="494" width="20" height="6" rx="2" fill="#0d2347"/><rect x="364" y="494" width="20" height="6" rx="2" fill="#0d2347"/>
                <path class="sc-flow up" d="M262 492 V252" fill="none" stroke="#fbbf24" stroke-width="2.4"/>
                <path class="sc-flow up" d="M318 492 V252" fill="none" stroke="#22d3ee" stroke-width="2.4"/>

                <!-- deck -->
                <rect x="212" y="232" width="156" height="14" rx="2" fill="#1b3159" stroke="#6fa0e8"/>
                <rect x="222" y="246" width="136" height="6" fill="#0f2043"/>
                <path d="M222 252 L232 246 M358 252 L348 246" stroke="#6fa0e8" stroke-width="1.5"/>
                <!-- flare boom -->
                <path d="M214 236 L166 178 M222 236 L166 178 M190 207 L206 216" stroke="#8fb4e8" stroke-width="2" fill="none"/>
                <circle cx="166" cy="170" r="34" fill="url(#scFl)" class="sc-glow"/>
                <g transform="translate(166,178)"><use href="#scFlame" class="sc-flame" fill="url(#scFire)"/></g>
                <g transform="translate(166,178) scale(.55)"><use href="#scFlame" class="sc-flame in" fill="#fff4c2"/></g>
                <!-- crane -->
                <rect x="226" y="214" width="10" height="18" fill="#243d6e" stroke="#6fa0e8"/>
                <path d="M231 216 L194 168" stroke="#cfe0ff" stroke-width="3.4" stroke-linecap="round"/><path d="M231 216 L205 197 M231 216 L214 204" stroke="#8fb4e8" stroke-width="1.3"/>
                <line x1="194" y1="168" x2="194" y2="196" stroke="#e8f1ff" stroke-width="1.2"><animate attributeName="y2" values="196;214;196" dur="6s" repeatCount="indefinite"/></line>
                <circle cx="194" cy="166" r="2.4" fill="#f87171" class="sc-blink"/>
                <!-- derrick -->
                <g fill="none" stroke="#cfe0ff" stroke-linecap="round">
                    <path d="M262 142 L246 232 M262 142 L278 232" stroke-width="2.6"/>
                    <path d="M257 170 H267 M252 198 H272 M247.1 226 H276.9" stroke-width="1.8"/>
                    <path d="M257 170 L272 198 M267 170 L252 198 M252 198 L276.9 226 M272 198 L247.1 226" stroke-width="1.3" stroke="#8fb4e8"/>
                </g>
                <rect x="257" y="135" width="10" height="7" rx="1.5" fill="#cfe0ff"/>
                <line x1="262" y1="142" x2="262" y2="196" stroke="#e8f1ff" stroke-width="1"><animate attributeName="y2" values="196;220;196" dur="5s" repeatCount="indefinite"/></line>
                <g><animateTransform attributeName="transform" type="translate" values="0 0;0 24;0 0" dur="5s" repeatCount="indefinite"/><rect x="258" y="194" width="8" height="9" rx="1.5" fill="#fbbf24" stroke="#7c4a05"/></g>
                <circle cx="262" cy="132" r="2.6" fill="#f87171" class="sc-blink"/>
                <!-- process modules -->
                <rect x="284" y="212" width="34" height="20" rx="2" fill="#244273" stroke="#6fa0e8"/>
                <path d="M290 212 V198 M300 212 V204 M310 212 V196" stroke="#8fb4e8" stroke-width="2.4"/><circle cx="290" cy="197" r="2" fill="#cfe0ff"/><circle cx="310" cy="195" r="2" fill="#cfe0ff"/>
                <path d="M284 222 H318" stroke="#0f2043" stroke-width="2"/>
                <!-- accommodation + helideck -->
                <rect x="322" y="198" width="44" height="34" rx="2" fill="#27487f" stroke="#7fb0ff"/>
                @foreach([0,1,2,3] as $k)<rect class="sc-win" x="{{ 327 + $k * 9 }}" y="204" width="5" height="6" fill="#fde68a" style="animation-delay:-{{ $k * 1.3 }}s"/><rect class="sc-win" x="{{ 327 + $k * 9 }}" y="217" width="5" height="6" fill="#fde68a" style="animation-delay:-{{ $k * 0.9 + 1 }}s"/>@endforeach
                <path d="M344 198 V188" stroke="#8fb4e8" stroke-width="2"/>
                <g><animateTransform attributeName="transform" type="rotate" from="0 344 188" to="360 344 188" dur="4s" repeatCount="indefinite"/><path d="M334 188 H354" stroke="#cfe0ff" stroke-width="2" stroke-linecap="round"/></g>
                <rect x="366" y="194" width="64" height="4" fill="#263d68" stroke="#8fb4e8"/>
                <path d="M370 198 L384 232 M426 198 L408 232 M398 198 V232" stroke="#6fa0e8" stroke-width="1.5"/>
                <ellipse cx="398" cy="192.5" rx="24" ry="3.6" fill="none" stroke="#fbbf24" stroke-width="1.4"/><path d="M394 190 V195 M402 190 V195 M394 192.5 H402" stroke="#fbbf24" stroke-width="1.3"/>
                <circle cx="430" cy="193" r="2.2" fill="#fbbf24" class="sc-blink"/>
                <!-- platform extras -->
                <polygon points="262,176 236,300 288,300" fill="url(#scCone)" class="sc-glint"/><polygon points="344,232 318,300 372,300" fill="url(#scCone)" class="sc-glint" style="animation-delay:-1.4s"/>
                <path d="M224 249 H356 M224 251.5 H356" stroke="#fbbf24" stroke-width="1.2" opacity=".55"/><path d="M230 240 H345" stroke="#8fb4e8" stroke-width="1" opacity=".5"/>
                @foreach([238, 346] as $lb)<g transform="translate({{ $lb }},262)"><path d="M-10 0 H10 L7 8 H-7Z" fill="#f97316" stroke="#7c2d12"/><path d="M-6 0 V-9 H6 V0" stroke="#cfe0ff" fill="none"/></g>@endforeach
                <rect x="204" y="290" width="30" height="4" fill="#243d6e" stroke="#6fa0e8"/><path d="M212 294 V306 M218 294 V306 M212 298 H218 M212 302 H218" stroke="#8fb4e8" stroke-width="1.2"/>
                <path d="M232 252 l-5 7 l6 4 l-5 7 l6 4 l-5 7 l6 4" fill="none" stroke="#6fa0e8" stroke-width="1.2" opacity=".8"/>
                <path class="sc-flow" d="M217 246 Q170 232 148 300" fill="none" stroke="#9bd8ff" stroke-width="1.8" opacity=".7"/>
                <path d="M432 193 V180" stroke="#cfe0ff" stroke-width="1.2"/><g transform="translate(432,181)"><path d="M0 0 L12 3 L0 6Z" fill="#f97316"><animateTransform attributeName="transform" type="rotate" values="-8 0 3;10 0 3;-8 0 3" dur="2.6s" repeatCount="indefinite"/></path></g>
                @foreach([0, 1.4, 2.8] as $sd)<circle class="sc-smoke" cx="310" cy="194" r="3.2" fill="#c9d6ee" style="animation-delay:{{ $sd }}s"/>@endforeach
                @foreach([0, 2.1] as $sd)<circle class="sc-smoke" cx="290" cy="196" r="2.4" fill="#e8eefc" style="animation-delay:{{ $sd }}s"/>@endforeach
<!-- foam at the legs -->
                <g fill="none" stroke="#bfe0ff" stroke-width="1.4" opacity=".8"><ellipse cx="226.7" cy="300" rx="9" ry="2.4"/><ellipse cx="353.3" cy="300" rx="9" ry="2.4"/></g>

                <!-- supply vessel -->
                <g class="sc-ship"><g transform="translate(40,288)"><g class="sc-bob">
                    <path d="M0 0 H76 L68 13 H9 Z" fill="#18274a" stroke="#6f95d6"/><rect x="4" y="-2" width="72" height="3" fill="#2b4a86"/>
                    <rect x="8" y="-17" width="20" height="17" fill="#2a4a84" stroke="#8fb4e8"/><rect x="11" y="-13" width="14" height="5" fill="#fde68a" opacity=".85"/><rect x="15" y="-24" width="6" height="7" fill="#e5484d"/>
                    <rect x="34" y="-8" width="12" height="8" fill="#e5484d"/><rect x="47" y="-8" width="12" height="8" fill="#3b82f6"/><rect x="60" y="-8" width="10" height="8" fill="#fbbf24"/>
                    <path d="M18 -24 V-34" stroke="#cfe0ff" stroke-width="1.5"/><circle cx="18" cy="-35" r="1.8" fill="#34d399" class="sc-blink"/>
                </g><path d="M0 12 q-14 3 -34 1 M4 15 q-12 3 -26 3" stroke="#e8f4ff" stroke-width="1.4" fill="none" opacity=".6"/></g></g>

                <!-- helicopter -->
                <g><animateMotion dur="24s" repeatCount="indefinite" calcMode="linear" keyPoints="0;.5;.5;1" keyTimes="0;.42;.58;1" path="M680 178 C570 168 470 192 408 184 C470 162 570 142 680 152"/>
                    <g transform="scale(.9)"><ellipse rx="10" ry="5" fill="#d3e1fb" stroke="#6f95d6"/><path d="M-9 -1 Q-6 -4 -2 -3 L-2 1 Q-6 2 -9 -1Z" fill="#0b1530"/>
                        <path d="M9 -1 L26 -4 L26 0 L9 2Z" fill="#c4d6f5" stroke="#6f95d6"/><circle cx="26" cy="-2" r="3" fill="none" stroke="#cfe0ff"/>
                        <path d="M0 -5 V-9" stroke="#cfe0ff" stroke-width="1.5"/><g><animateTransform attributeName="transform" type="scale" values="1 1;.15 1;1 1" dur=".14s" repeatCount="indefinite"/><path d="M-20 -9 H20" stroke="#e8f1ff" stroke-width="1.8" stroke-linecap="round"/></g>
                        <path d="M-8 7 H8 M-6 5 V7 M6 5 V7" stroke="#8fb4e8" stroke-width="1.3"/><circle cx="-4" cy="6" r="1.4" fill="#f87171" class="sc-blink"/><circle cx="14" cy="-1" r="1.2" fill="#34d399" class="sc-blink" style="animation-delay:-.6s"/></g>
                </g>
<!-- front waves (cover the submerged part of the jacket) -->
                <g clip-path="url(#scSeaClip)">
                    <g transform="translate(0,309) scale(1,.55)"><path class="sc-wv" d="{{ $wave }}" fill="#1f7db3" opacity=".42"/></g>
                    <g transform="translate(0,331) scale(1,.85)"><path class="sc-wv c" d="{{ $wave }}" fill="#0f4f86" opacity=".5"/></g>
                    <g stroke="#bfe0ff" stroke-width="1.2" fill="none" opacity=".35"><path d="M20 322 q20 -4 40 0 M120 340 q20 -4 40 0 M440 328 q20 -4 40 0 M520 352 q20 -4 40 0 M60 372 q20 -4 40 0"/></g>
                </g>

                <!-- onshore ground -->
                <path d="M588 318 C606 300 680 294 760 304 C860 314 1040 306 1200 304 V520 H566 C584 470 556 392 588 318Z" fill="url(#scGround)"/>
                <path d="M588 318 C556 392 584 470 566 520" fill="none" stroke="#c9a86a" stroke-width="3" opacity=".55"/>
                <path d="M618 346 C700 322 790 338 900 326 C1010 318 1100 334 1200 326 V392 H618Z" fill="#241f2e"/>
                <path d="M606 392 C690 362 770 374 850 386 C960 364 1070 374 1200 368 V404 H606Z" fill="#2c2634"/>
                <rect x="610" y="398" width="590" height="16" fill="#3a3340"/>
                <!-- power line -->
                <g stroke="#52618a" stroke-width="2.4" fill="none"><path d="M690 372 V330 M680 336 H700 M790 372 V330 M780 336 H800 M890 374 V330 M880 336 H900"/></g>
                <g stroke="#52618a" stroke-width="1" fill="none"><path d="M681 336 Q735 352 781 336 Q835 352 881 336 M699 336 Q745 352 799 336 Q845 352 899 336"/></g>

                <!-- workover rig on the back dune -->
                <g transform="translate(742,368)" opacity=".8">
                    <rect x="-42" y="-9" width="24" height="9" fill="#1c2a49" stroke="#52618a"/><rect x="12" y="-20" width="14" height="14" fill="#22345f" stroke="#52618a"/><rect class="sc-win" x="15" y="-17" width="5" height="4" fill="#fde68a"/>
                    <path d="M-9 0 L0 -62 L9 0 M-6 -20 H6 M-4 -40 H4 M-6 -20 L4 -40 M6 -20 L-4 -40" fill="none" stroke="#7f98cc" stroke-width="1.6"/>
                    <rect x="-3" y="-67" width="6" height="5" fill="#9db8e4"/><path d="M0 -62 V-30" stroke="#cfe0ff" stroke-width=".9"><animate attributeName="d" values="M0 -62 V-30;M0 -62 V-18;M0 -62 V-30" dur="6s" repeatCount="indefinite"/></path>
                    <rect x="-14" y="-9" width="28" height="4" fill="#33466f"/><path d="M0 -56 L-34 0 M0 -56 L38 0" stroke="#52618a" stroke-width=".8" stroke-dasharray="3 3"/><circle cx="0" cy="-69" r="1.8" fill="#f87171" class="sc-blink"/>
                </g>
                @foreach([905, 1052] as $px)
                <polygon points="{{ $px }},344 {{ $px - 26 }},400 {{ $px + 26 }},400" fill="url(#scCone)" class="sc-glint"/>
                <path d="M{{ $px }} 400 V344" stroke="#7f98cc" stroke-width="2"/><rect x="{{ $px - 6 }}" y="340" width="12" height="5" rx="1.5" fill="#cfe0ff"/>
                @endforeach
<!-- wellhead trees -->
                @foreach([646, 700, 790] as $tx)
                <g transform="translate({{ $tx }},398)">
                    <path d="M-22 0 V-13 M22 0 V-13 M-22 -10 H22" stroke="#52618a" stroke-width="1" fill="none"/>
                    <rect x="-13" y="-5" width="26" height="5" rx="1.5" fill="#243653" stroke="#6fa0e8"/>
                    <rect x="-6" y="-13" width="12" height="8" fill="url(#scSteel)" stroke="#a9c6f5"/>
                    <rect x="-3.5" y="-44" width="7" height="32" fill="#0d1a38" stroke="#8fb4e8"/>
                    <rect x="-7" y="-25" width="14" height="8" rx="2" fill="url(#scSteel)" stroke="#a9c6f5"/><rect x="-7" y="-36" width="14" height="8" rx="2" fill="url(#scSteel)" stroke="#a9c6f5"/>
                    <rect x="-5.5" y="-51" width="11" height="7" rx="1.5" fill="#27487f" stroke="#a9c6f5"/>
                    <path d="M3.5 -40 H17 V6" fill="none" stroke="#13264a" stroke-width="4.5"/><path d="M3.5 -40 H17 V6" fill="none" stroke="#8fb4e8" stroke-width="2"/>
                    <rect x="10" y="-44" width="14" height="8" rx="2" fill="url(#scSteel)" stroke="#a9c6f5"/>
                    <circle cx="-13" cy="-32" r="5.5" fill="none" stroke="#cfe0ff" stroke-width="1.6"/><path d="M-18.5 -32 H-7.5 M-13 -37.5 V-26.5" stroke="#cfe0ff" stroke-width="1.2"/>
                    <g><animateTransform attributeName="transform" type="rotate" from="0 -13 -32" to="360 -13 -32" dur="{{ 7 + $loop->index * 2 }}s" repeatCount="indefinite"/><path d="M-13 -32 L-8 -35" stroke="#fbbf24" stroke-width="1.4" stroke-linecap="round"/></g>
                    <path d="M0 -51 V-58" stroke="#8fb4e8" stroke-width="2"/><circle cx="0" cy="-63" r="5.6" fill="#08122b" stroke="#34d399" stroke-width="1.5"/>
                    <g><animateTransform attributeName="transform" type="rotate" values="-40 0 -63;32 0 -63;-12 0 -63;-40 0 -63" dur="{{ 6 + $loop->index }}s" repeatCount="indefinite"/><path d="M0 -63 V-68" stroke="#fbbf24" stroke-width="1.4" stroke-linecap="round"/></g>
                    <circle cx="19" cy="-40" r="1.6" fill="#34d399" class="sc-blink" style="animation-delay:-{{ $loop->index }}s"/>
                </g>
                @endforeach
                <!-- flowline -->
                <g fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M646 392 V404 H868 V390" stroke="#13264a" stroke-width="5"/><path class="sc-flow" d="M646 392 V404 H868 V390" stroke="#fbbf24" stroke-width="2"/>
                    <path d="M868 330 V316 H930 V356" stroke="#13264a" stroke-width="5"/><path class="sc-flow" d="M868 330 V316 H930 V356" stroke="#fbbf24" stroke-width="2"/>
                    <path d="M992 386 V404 H1172" stroke="#13264a" stroke-width="5"/><path class="sc-flow" d="M992 386 V404 H1172" stroke="#fb923c" stroke-width="2"/>
                    <path d="M996 356 V332 H1026 V322" stroke="#13264a" stroke-width="4"/><path class="sc-flow up" d="M996 356 V332 H1026 V322" stroke="#22d3ee" stroke-width="1.8"/>
                    <path d="M650 410 H1058 V398" stroke="#2b2130" stroke-width="5"/><path class="sc-flow" d="M650 410 H1058 V398" stroke="#c9a86a" stroke-width="1.6" opacity=".7"/>
                </g>
                <!-- MPFM -->
                <rect x="852" y="388" width="44" height="10" rx="2" fill="#243653" stroke="#6fa0e8"/>
                <rect x="860" y="332" width="20" height="56" rx="5" fill="url(#scSteel)" stroke="#a9c6f5"/>
                <path d="M860 352 H880 M860 366 H880" stroke="#0b1530" stroke-width="1.4"/>
                <circle cx="870" cy="359" r="5" fill="#0b1530" stroke="#fbbf24" stroke-width="1.4"/>
                <circle cx="870" cy="359" r="5" fill="none" stroke="#fbbf24"><animate attributeName="r" values="5;16" dur="2.4s" repeatCount="indefinite"/><animate attributeName="opacity" values=".8;0" dur="2.4s" repeatCount="indefinite"/></circle>
                <path d="M880 344 H888 M880 374 H888" stroke="#8fb4e8" stroke-width="2"/>
                <rect x="886" y="338" width="14" height="42" rx="3" fill="#122248" stroke="#6fa0e8"/><circle cx="893" cy="346" r="2.4" fill="#34d399" class="sc-blink"/><path d="M890 356 H896 M890 362 H896 M890 368 H896" stroke="#6fa0e8"/>
                <path d="M870 332 V318" stroke="#8fb4e8" stroke-width="2"/>
                <!-- test separator -->
                <rect x="918" y="386" width="7" height="14" fill="#243653"/><rect x="990" y="386" width="7" height="14" fill="#243653"/>
                <rect x="910" y="356" width="94" height="31" rx="15.5" fill="url(#scSteel)" stroke="#a9c6f5"/>
                <path d="M936 357 V386 M978 357 V386" stroke="#0b1530" stroke-width="1" opacity=".5"/>
                <rect x="950" y="364" width="7" height="17" rx="3" fill="#0b1530" stroke="#8fb4e8"/>
                <rect x="951.5" y="372" width="4" height="7" rx="1.5" fill="url(#scOil)"><animate attributeName="y" values="372;367;372" dur="4s" repeatCount="indefinite"/><animate attributeName="height" values="7;12;7" dur="4s" repeatCount="indefinite"/></rect>
                <path d="M964 356 V346" stroke="#8fb4e8" stroke-width="2"/>
                <circle cx="964" cy="340" r="8" fill="#0b1530" stroke="#34d399" stroke-width="1.6"/>
                <g><animateTransform attributeName="transform" type="rotate" values="-50 964 340;35 964 340;-20 964 340;-50 964 340" dur="7s" repeatCount="indefinite"/><path d="M964 340 L964 333" stroke="#fbbf24" stroke-width="1.6" stroke-linecap="round"/></g>
                <path d="M984 356 V344 M980 344 H988" stroke="#8fb4e8" stroke-width="2"/>
                <!-- flare -->
                <g stroke="#8fb4e8" fill="none"><path d="M1022 400 L1025 322 M1030 400 L1027 322 M1023 380 H1029 M1024 350 H1028 M1022 400 L1030 380 M1030 400 L1022 380" stroke-width="1.6"/><path d="M1026 322 L1008 400 M1026 322 L1046 400" stroke-width=".8" opacity=".6"/></g>
                <circle cx="1026" cy="304" r="22" fill="url(#scFl)" class="sc-glow"/>
                <g transform="translate(1026,322) scale(.8)"><use href="#scFlame" class="sc-flame" fill="url(#scFire)"/></g>
                <!-- tank farm -->
                <path d="M1052 398 H1196 V404 H1052Z" fill="#2a2a3d" stroke="#445" stroke-width=".6"/>
                @foreach([1080, 1126, 1172] as $i => $c)
                <path d="M{{ $c - 19 }} 340 V396 a19 5 0 0 0 38 0 V340Z" fill="url(#scTank)" stroke="#a9c6f5" stroke-width=".8"/>
                <ellipse cx="{{ $c }}" cy="340" rx="19" ry="5" fill="#4a73b8" stroke="#a9c6f5" stroke-width=".8"/>
                <path d="M{{ $c - 19 }} 358 a19 5 0 0 0 38 0 M{{ $c - 19 }} 376 a19 5 0 0 0 38 0" fill="none" stroke="#0b1530" stroke-width=".8" opacity=".5"/>
                <path d="M{{ $c + 19 }} 392 L{{ $c + 7 }} 380 L{{ $c + 19 }} 368 L{{ $c + 7 }} 356 L{{ $c + 19 }} 344" fill="none" stroke="#cfe0ff" stroke-width="1" opacity=".7"/>
                <rect x="{{ $c - 14 }}" y="348" width="5" height="40" rx="2.5" fill="#0b1530" stroke="#8fb4e8" stroke-width=".8"/>
                <rect x="{{ $c - 13 }}" y="372" width="3" height="14" rx="1.5" fill="url(#scOil)"><animate attributeName="y" values="{{ 372 - $i * 3 }};{{ 358 - $i * 2 }};{{ 372 - $i * 3 }}" dur="{{ 9 + $i * 2 }}s" repeatCount="indefinite"/><animate attributeName="height" values="{{ 14 + $i * 3 }};{{ 28 + $i * 2 }};{{ 14 + $i * 3 }}" dur="{{ 9 + $i * 2 }}s" repeatCount="indefinite"/></rect>
                @endforeach
                <!-- control room + flow packets + loading -->
                <g transform="translate(604,362)">
                    <rect x="0" y="14" width="36" height="22" rx="2" fill="#1d3768" stroke="#7fb0ff"/><rect x="-2" y="11" width="40" height="5" rx="1.5" fill="#2b4a86" stroke="#7fb0ff"/>
                    <rect class="sc-win" x="4" y="20" width="6" height="7" fill="#9be7ff"/><rect class="sc-win" x="13" y="20" width="6" height="7" fill="#9be7ff" style="animation-delay:-1.3s"/><rect x="24" y="22" width="7" height="14" fill="#0b1530"/>
                    <path d="M18 11 V-58" stroke="#9db8e4" stroke-width="2.2"/><path d="M14 11 L18 -20 L22 11 M15 -4 H21" stroke="#7f98cc" stroke-width="1" fill="none"/>
                    <g transform="translate(18,-34)"><g><animateTransform attributeName="transform" type="rotate" values="-14;14;-14" dur="7s" repeatCount="indefinite"/><path d="M-9 0 Q0 -12 9 0 Z" fill="#cfe0ff"/><path d="M0 -3 L8 -13" stroke="#cfe0ff" stroke-width="1.4"/></g></g>
                    <circle cx="18" cy="-60" r="2" fill="#f87171" class="sc-blink"/>
                </g>
                @foreach([[760, 404], [842, 404], [1000, 404]] as [$vx, $vy])
                <g transform="translate({{ $vx }},{{ $vy }})"><path d="M-6 -5 L6 5 V-5 L-6 5Z" fill="#0d1b3a" stroke="#22d3ee" stroke-width="1.3" stroke-linejoin="round"/><path d="M0 0 V-10 M-4 -10 H4" stroke="#22d3ee" stroke-width="1.3"/></g>
                @endforeach
                @foreach([0, 1.7, 3.4] as $pd)
                <circle r="2.6" fill="#fff1b8"><animateMotion dur="5.2s" begin="{{ $pd }}s" repeatCount="indefinite" path="M646 392 V404 H868 V390"/></circle>
                <circle r="2.4" fill="#ffd6a5"><animateMotion dur="3.6s" begin="{{ $pd }}s" repeatCount="indefinite" path="M992 386 V404 H1172"/></circle>
                @endforeach
<!-- road, truck, foreground -->
                <rect x="566" y="468" width="634" height="26" fill="#121219"/>
                <path d="M566 481 H1200" stroke="#fbbf24" stroke-width="1.6" stroke-dasharray="14 12" opacity=".7"/>
                <g class="sc-hose"><path d="M1080 396 C1074 424 1097 428 1099 449" fill="none" stroke="#13264a" stroke-width="4.5"/><path class="sc-flow" d="M1080 396 C1074 424 1097 428 1099 449" fill="none" stroke="#fb923c" stroke-width="1.8"/></g>
                <g transform="translate(630,486)"><g class="sc-tk"><g class="sc-tkf">
                    <polygon points="103,-17 156,-30 156,-4" fill="#fff4be" opacity=".2"/>
                    <rect x="2" y="-12" width="104" height="4" fill="#1a2238" stroke="#52618a"/>
                    <rect x="2" y="-33" width="73" height="23" rx="11.5" fill="url(#scSteel)" stroke="#a9c6f5"/>
                    <path d="M20 -33 V-10 M38 -33 V-10 M56 -33 V-10" stroke="#0b1530" stroke-width="1" opacity=".45"/>
                    <rect x="34" y="-38" width="9" height="5" rx="1.5" fill="#cfe0ff"/><path d="M8 -38 H26 M8 -36 V-33 M26 -36 V-33" stroke="#8fb4e8" stroke-width="1" fill="none"/>
                    <path d="M62 -28 C62 -28 57.5 -22.5 57.5 -20 a4.5 4.5 0 0 0 9 0 C66.5 -22.5 62 -28 62 -28Z" fill="url(#scOil)"/>
                    <rect x="0" y="-22" width="3" height="5" fill="#f87171" class="sc-blink"/>
                    <path d="M76 -12 V-33 H92 L104 -21 V-12Z" fill="#c2410c" stroke="#fdba74"/><path d="M80 -30 H91 L98.5 -22.5 H80Z" fill="#0b1530" opacity=".85"/>
                    <rect x="73" y="-46" width="3" height="14" fill="#cfd6e4"/>
                    <circle cx="103" cy="-17" r="2.6" fill="#fde68a" class="sc-glow"/>
                    @foreach([14, 28, 90] as $wx)
                    <circle cx="{{ $wx }}" cy="-6" r="6" fill="#05080f" stroke="#cfe0ff"/>
                    <path d="M{{ $wx }} -11 V-1 M{{ $wx - 5 }} -6 H{{ $wx + 5 }}" stroke="#cfe0ff" stroke-width="1"><animateTransform attributeName="transform" type="rotate" from="0 {{ $wx }} -6" to="360 {{ $wx }} -6" dur=".5s" repeatCount="indefinite"/></path>
                    @endforeach
                </g></g></g>
                <g fill="#1c1a26"><ellipse cx="640" cy="508" rx="10" ry="4"/><ellipse cx="760" cy="512" rx="14" ry="4"/><ellipse cx="900" cy="506" rx="9" ry="3"/><ellipse cx="1040" cy="512" rx="13" ry="4"/><ellipse cx="1150" cy="506" rx="10" ry="3"/></g>
                <g stroke="#3d4a32" stroke-width="1.6" fill="none" stroke-linecap="round"><path d="M610 504 q4 -10 8 0 M614 504 q0 -12 0 -14 M700 498 q4 -10 8 0 M1100 500 q4 -10 8 0 M1010 504 q4 -12 8 0"/></g>

                <!-- live telemetry -->
                <g fill="none" stroke="#22d3ee" stroke-width="1.2" stroke-dasharray="4 6" opacity=".55">
                    <path id="scT1" d="M262 130 C340 30 470 14 600 38"/><path id="scT2" d="M624 46 C648 130 628 220 622 302"/><path id="scT3" d="M870 316 C850 220 760 96 630 44"/>
                </g>
                @foreach([['scT1', 0], ['scT1', 2.4], ['scT2', 1.1], ['scT2', 3.3], ['scT3', 0.6], ['scT3', 2.9]] as [$tp, $td])
                <circle r="2.6" fill="#22d3ee"><animateMotion dur="4.8s" begin="{{ $td }}s" repeatCount="indefinite"><mpath href="#{{ $tp }}"/></animateMotion></circle>
                @endforeach
                {!! $hud(434, 78, 156, '#22d3ee', 'PLATFORM · ZK-101', 'Oil 3,280 B/D', 'WC 18% · GOR 640', 'M0 18 L8 15 L16 17 L24 9 L32 12 L40 5 L46 7') !!}
                {!! $hud(742, 106, 160, '#fbbf24', 'ONSHORE · OH-14', 'WHP 1,240 psi', 'Valves ok · 18.5 turns', 'M0 6 L8 9 L16 8 L24 14 L32 11 L40 16 L46 14') !!}
                {!! $hud(918, 106, 184, '#34d399', 'INTEGRITY · GREEN', 'Ann. A 0 / 1,500 psi', 'Barriers 9 of 9 tested', 'M0 14 L8 14 L16 13 L24 14 L32 12 L40 13 L46 12') !!}
<g id="scData"></g>

<!-- labels -->
                <g><circle cx="34" cy="36" r="5" fill="#22d3ee" class="sc-blink"/><text x="48" y="41" fill="#dbe8ff" font-size="15" font-weight="800" letter-spacing="3">OFFSHORE</text></g>
                <g><text x="1166" y="41" fill="#dbe8ff" font-size="15" font-weight="800" letter-spacing="3" text-anchor="end">ONSHORE</text><circle cx="1180" cy="36" r="5" fill="#fbbf24" class="sc-blink"/></g>
                {!! $chip(120, 92, 150, 'Production platform', 262, 146, 195) !!}
                {!! $chip(24, 404, 176, 'Subsea trees · flowlines', 130, 452) !!}
                {!! $chip(414, 420, 116, 'Export pipeline', 470, 492) !!}
                {!! $chip(640, 236, 108, 'Wellhead trees', 700, 338) !!}
                {!! $chip(804, 256, 94, 'MPFM meter', 870, 330) !!}
                {!! $chip(928, 250, 112, 'Test separator', 960, 337) !!}
                {!! $chip(1088, 252, 82, 'Tank farm', 1126, 334) !!}
                {!! $chip(458, 246, 58, 'FPSO', 500, 281) !!}
                {!! $chip(552, 196, 112, 'Control room', 622, 316) !!}
                {!! $chip(28, 352, 92, 'ROV survey', 178, 422, 74) !!}
                {!! $chip(706, 198, 102, 'Workover rig', 742, 304) !!}
                {!! $chip(1100, 424, 100, 'Oil tanker', 1150, 450) !!}
            </svg>
            <figcaption>Illustration: offshore platform with subsea tie-backs and an onshore wellsite - same tests, same surveys, same well record.</figcaption>
        </figure>
        <script>
        (function () {
            var svg = document.querySelector('#sites svg'), box = document.getElementById('scData');
            if (!svg || !box) return;
            var NS = 'http://www.w3.org/2000/svg';
            var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            var snippets = [
                ['WELL TEST REPORT', '#22d3ee', 'ZK-101 · 12 Nov 2026', 'Oil 3,280 B/D · WC 18% · GOR 640'],
                ['MPFM vs TEST VESSEL', '#fbbf24', 'Oil Δ -2.4% · Gas Δ +1.8%', 'Within ±5% · validated'],
                ['WIRE LINE SURVEY', '#a78bfa', 'BHCIP 2,497 psi @ 2,950 m', 'BHT 96 °C · stabilised'],
                ['GRADIENT SURVEY', '#a78bfa', 'Oil 0.31 psi/ft · 14 stops', 'Water contact below 2,640 m'],
                ['WELLHEAD CHECK', '#34d399', 'UMV ok · 18.5 turns · 5 s', 'Annulus A 0 psi · DBB at RHS'],
                ['BARRIER TEST', '#34d399', 'SCSSV function test: PASS', 'Next due 12 May 2027'],
                ['ANOMALY REGISTER', '#f87171', 'SCP on annulus B · risk 12', 'High · mitigation due 30 Nov'],
                ['WELL ANALYSIS', '#38bdf8', 'Health score 82 / 100', 'Decline 9.4 %/yr · WC +0.6 pt/mo'],
                ['TANK FARM', '#fb923c', 'Tank 2 · level 62%', '4,150 bbl · loading 14:20'],
                ['TEST SEPARATOR', '#fbbf24', 'P 118 psi · T 74 °C', 'Gas 1.9 MMscf/D · oil 3,310 B/D'],
                ['PRESSURE SURVEY', '#a78bfa', 'Depletion 3.1% since 2024', 'Next survey due in 38 days'],
                ['TEST REQUEST', '#fbbf24', 'ZK-115 · choke 48/64', 'Scheduled 14 Nov · offshore'],
                ['INSPECTION', '#2dd4bf', 'Caliper · max wall loss 12%', 'Status: monitor · next due Jun'],
                ['SHIFT REPORT', '#22d3ee', '6 tests · 5 approved · 1 review', 'Night shift · onshore']
            ];
            // x, y of the card, x, y of the equipment it refers to
            var slots = [[20, 128, 168, 178], [440, 170, 398, 190], [1030, 58, 1126, 334], [1030, 150, 1026, 306], [20, 436, 130, 462],
                         [392, 352, 362, 374], [430, 456, 470, 488], [640, 420, 700, 402], [850, 420, 958, 390]];
            var busySlot = {}, busyText = {}, shown = 0, onScreen = true, H = 46;

            function el(n, a, t) {
                var e = document.createElementNS(NS, n);
                for (var k in a) e.setAttribute(k, a[k]);
                if (t != null) e.textContent = t;
                return e;
            }
            function pick(count, busy) {
                var free = [];
                for (var i = 0; i < count; i++) if (!busy[i]) free.push(i);
                return free.length ? free[Math.floor(Math.random() * free.length)] : -1;
            }
            function spawn() {
                var si = pick(slots.length, busySlot), ti = pick(snippets.length, busyText);
                if (si < 0 || ti < 0) return;
                busySlot[si] = busyText[ti] = true; shown++;
                var s = slots[si], d = snippets[ti], g = el('g', { 'class': 'sc-pop' });
                if (reduce) g.setAttribute('style', 'animation:none;opacity:1');
                var line = el('path', { fill: 'none', stroke: d[1], 'stroke-opacity': '.7', 'stroke-dasharray': '3 3' });
                var dot = el('circle', { cx: s[2], cy: s[3], r: 3, fill: d[1] });
                var ring = el('circle', { cx: s[2], cy: s[3], r: 3, fill: 'none', stroke: d[1], 'class': 'sc-ring' });
                var rect = el('rect', { x: s[0], y: s[1], width: 160, height: H, rx: 8, fill: 'rgba(7,16,38,.92)', stroke: d[1], 'stroke-opacity': '.65' });
                var bar = el('rect', { x: s[0], y: s[1] + 8, width: 3, height: H - 16, rx: 1.5, fill: d[1] });
                var fold = el('path', { d: 'M0 0 L10 0 L10 10Z', fill: d[1], 'fill-opacity': '.35' });
                var tag = el('text', { x: s[0] + 12, y: s[1] + 14, 'font-size': 8.5, 'font-weight': 800, 'letter-spacing': 1.1, fill: d[1] }, d[0]);
                var l1 = el('text', { x: s[0] + 12, y: s[1] + 28, 'font-size': 10.5, 'font-weight': 700, fill: '#eef4ff' }, d[2]);
                var l2 = el('text', { x: s[0] + 12, y: s[1] + 40, 'font-size': 9.5, fill: '#9fb4d8' }, d[3]);
                [line, dot, ring, rect, bar, fold, tag, l1, l2].forEach(function (n) { g.appendChild(n); });
                box.appendChild(g);
                var w = Math.max(tag.getComputedTextLength(), l1.getComputedTextLength(), l2.getComputedTextLength()) + 26;
                var x = Math.min(s[0], 1194 - w), dx = x - s[0];
                [rect, bar, tag, l1, l2].forEach(function (n) {
                    n.setAttribute(n === rect || n === bar ? 'x' : 'x', parseFloat(n.getAttribute('x')) + dx);
                });
                rect.setAttribute('width', w);
                fold.setAttribute('transform', 'translate(' + (x + w - 10) + ',' + s[1] + ')');
                var cx = x + w / 2, ay = s[3];
                var sx = cx, sy = ay > s[1] + H ? s[1] + H : (ay < s[1] ? s[1] : s[1] + H / 2);
                if (ay >= s[1] && ay <= s[1] + H) { sx = s[2] > cx ? x + w : x; }
                line.setAttribute('d', 'M' + sx + ' ' + sy + ' L' + s[2] + ' ' + s[3]);
                if (!reduce) {
                    g.addEventListener('animationend', function () {
                        box.removeChild(g); delete busySlot[si]; delete busyText[ti]; shown--;
                    });
                }
            }
            if ('IntersectionObserver' in window) {
                new IntersectionObserver(function (es) { onScreen = es[0].isIntersecting; }, { threshold: .15 }).observe(svg);
            }
            if (reduce) { spawn(); spawn(); spawn(); return; }
            spawn(); setTimeout(spawn, 900);
            setInterval(function () { if (onScreen && !document.hidden && shown < 4) spawn(); }, 2300);
        })();
        </script>
    </div>
</section>
