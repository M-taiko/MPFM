{{-- Governance: review workflow + audit trail + role access matrix (decorative, animated inline SVG) --}}
@php
    $node = [
        // key => x, y, w, title, sub, pill, colour, fill
        'draft'     => [24, 80, 150, 'Draft', 'Log parsed, not yet sent', 'OPERATOR', '#94a3b8', '#0d1730'],
        'submitted' => [300, 80, 150, 'Submitted', 'Waiting in review queue', 'SUPERVISOR', '#fbbf24', '#0d1730'],
        'approved'  => [660, 20, 160, 'Approved', 'Counted in reports', 'SUPERVISOR', '#34d399', '#0d2a22'],
        'archived'  => [850, 20, 130, 'Archived', 'Locked, audit stamped', 'SYSTEM', '#6ee7b7', '#0d2a22'],
        'rejected'  => [660, 140, 160, 'Rejected', 'Reason is recorded', 'SUPERVISOR', '#f87171', '#2a1216'],
        'retest'    => [850, 140, 130, 'Re-test', 'Back to the operator', 'JOB OFFICER', '#a78bfa', '#17142e'],
    ];
    $log = [
        ['08:12', 'operator1',  '#94a3b8', 'created test #412 as draft'],
        ['08:40', 'operator1',  '#94a3b8', 'submitted for review'],
        ['08:41', 'system',     '#22d3ee', '12 validation rules passed · quality 96'],
        ['09:15', 'supervisor', '#fbbf24', 'approved · within ±5% of the vessel'],
        ['09:15', 'system',     '#6ee7b7', 'archived · record locked'],
    ];
    $cols = ['MPFM', 'Testing', 'Wire Line', 'Integrity', 'Users'];
    // 2 = manage, 1 = view, 0 = none
    $roles = [
        ['Operator',          [2, 2, 0, 0, 0]],
        ['Supervisor',        [2, 2, 2, 2, 0]],
        ['Reservoir engineer', [1, 1, 1, 1, 0]],
        ['Admin',             [2, 2, 2, 2, 2]],
    ];
@endphp
<svg viewBox="0 0 1000 432" role="img" aria-label="Review workflow from draft through validation, submission and review to approved, rejected or re-test, with an audit trail and a role access matrix">
    <defs>
        <linearGradient id="gvOk" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="#34d399" stop-opacity=".9"/><stop offset="1" stop-color="#34d399" stop-opacity=".2"/></linearGradient>
        <pattern id="gvGrid" width="24" height="24" patternUnits="userSpaceOnUse"><path d="M24 0H0V24" fill="none" stroke="#5b7bb5" stroke-opacity=".12"/></pattern>
    </defs>
    <rect width="1000" height="432" fill="url(#gvGrid)"/>

    <!-- workflow links -->
    <g fill="none" stroke-linecap="round" stroke-linejoin="round">
        <path id="gvMain" d="M99 115 H540 C610 115 600 55 660 55 H915" stroke="none"/>
        <path id="gvRej" d="M540 115 C610 115 600 175 660 175 H915 V232 H99 V150" stroke="none"/>
        <g class="flowd" stroke-width="1.7">
            <path d="M174 115 H211" stroke="#64748b"/><path d="M263 115 H300" stroke="#64748b"/><path d="M450 115 H496" stroke="#fbbf24"/>
            <path d="M584 115 C620 115 620 55 660 55" stroke="#34d399"/><path d="M584 115 C620 115 620 175 660 175" stroke="#f87171"/>
            <path d="M820 55 H850" stroke="#34d399"/><path d="M820 175 H850" stroke="#a78bfa"/>
            <path d="M915 210 V232 H99 V152" stroke="#a78bfa" stroke-opacity=".7" stroke-dasharray="3 7"/>
        </g>
    </g>
    <g font-size="10" fill="#9aaccb" text-anchor="middle" font-weight="600">
        <text x="192" y="107">send</text><text x="474" y="107">review</text><text x="626" y="62">approve</text><text x="626" y="182">reject</text><text x="835" y="49">lock</text><text x="835" y="169">loop</text>
        <text x="500" y="226" fill="#c4b5fd">re-test loop: a new test is requested for the same well</text>
    </g>

    <!-- validation gate -->
    <g transform="translate(237,115)">
        <circle r="30" fill="none" stroke="#22d3ee" stroke-opacity=".35" class="pulse"/>
        <circle r="24" fill="#0b1a3a" stroke="#22d3ee" stroke-width="1.6"/>
        <path d="M0 -13 l11 4 v7 c0 8 -4.5 12 -11 15 -6.5 -3 -11 -7 -11 -15 v-7z" fill="none" stroke="#22d3ee" stroke-width="1.8" stroke-linejoin="round"/>
        <path d="M-5 0 l4 4 8 -8" fill="none" stroke="#a5f3fc" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><animate attributeName="stroke-dasharray" values="0 20;20 0;20 0" keyTimes="0;.4;1" dur="3s" repeatCount="indefinite"/></path>
        <text y="46" font-size="9.5" font-weight="800" text-anchor="middle" fill="#67e8f9">12 RULES</text>
    </g>

    <!-- review decision -->
    <g transform="translate(540,115)">
        <path d="M0 -46 L46 0 L0 46 L-46 0Z" fill="#0d1730" stroke="#a78bfa" stroke-width="1.8"/>
        <path d="M0 -56 L56 0 L0 56 L-56 0Z" fill="none" stroke="#a78bfa" stroke-opacity=".3" class="pulse"/>
        <text y="-2" font-size="13" font-weight="800" text-anchor="middle" fill="#fff">Review</text>
        <text y="13" font-size="9" text-anchor="middle" fill="#9aaccb">±5% · rules</text>
    </g>

    <!-- state nodes -->
    @foreach($node as $k => [$x, $y, $w, $t, $sub, $pill, $c, $fill])
    <g>
        <rect x="{{ $x }}" y="{{ $y }}" width="{{ $w }}" height="70" rx="14" fill="{{ $fill }}" stroke="{{ $c }}" stroke-opacity=".75"/>
        <rect x="{{ $x }}" y="{{ $y + 14 }}" width="3.5" height="42" rx="1.7" fill="{{ $c }}"/>
        <text x="{{ $x + 18 }}" y="{{ $y + 25 }}" font-size="14" font-weight="800" fill="#fff">{{ $t }}</text>
        <text x="{{ $x + 18 }}" y="{{ $y + 40 }}" font-size="9.5" fill="#9aaccb">{{ $sub }}</text>
        <rect x="{{ $x + 18 }}" y="{{ $y + 47 }}" width="{{ strlen($pill) * 6.1 + 14 }}" height="15" rx="7.5" fill="{{ $c }}" fill-opacity=".14" stroke="{{ $c }}" stroke-opacity=".5"/>
        <text x="{{ $x + 25 }}" y="{{ $y + 57.5 }}" font-size="8.5" font-weight="800" letter-spacing="1" fill="{{ $c }}">{{ $pill }}</text>
    </g>
    @endforeach
    <!-- SLA timer on the submitted node -->
    <g transform="translate(428,97)">
        <circle r="9" fill="none" stroke="#3b4f7a" stroke-width="2.4"/>
        <circle r="9" fill="none" stroke="#fbbf24" stroke-width="2.4" stroke-linecap="round" stroke-dasharray="56.5" transform="rotate(-90)"><animate attributeName="stroke-dashoffset" values="56.5;0;0" keyTimes="0;.8;1" dur="7s" repeatCount="indefinite"/></circle>
        <path d="M0 -4 V0 L3 2" fill="none" stroke="#fbbf24" stroke-width="1.4" stroke-linecap="round"/>
    </g>
    <!-- lock on the archived node -->
    <g transform="translate(958,34)" fill="none" stroke="#6ee7b7" stroke-width="1.6" stroke-linecap="round"><rect x="-6" y="-1" width="12" height="9" rx="2"/><path d="M-3.5 -1 V-4 a3.5 3.5 0 0 1 7 0 V-1"/></g>

    <!-- travelling records -->
    <g opacity="0"><animate attributeName="opacity" values="0;1;1;0" keyTimes="0;.04;.94;1" dur="8s" repeatCount="indefinite"/>
        <animateMotion dur="8s" repeatCount="indefinite"><mpath href="#gvMain"/></animateMotion>
        <rect x="-7" y="-9" width="14" height="18" rx="2.5" fill="#e8f1ff" stroke="#34d399" stroke-width="1.4"/><path d="M-3.5 -3 H3.5 M-3.5 1 H3.5 M-3.5 5 H0" stroke="#0b1530" stroke-width="1.2" stroke-linecap="round"/></g>
    <g opacity="0"><animate attributeName="opacity" values="0;1;1;0" keyTimes="0;.04;.94;1" dur="13s" begin="4s" repeatCount="indefinite"/>
        <animateMotion dur="13s" begin="4s" repeatCount="indefinite"><mpath href="#gvRej"/></animateMotion>
        <rect x="-7" y="-9" width="14" height="18" rx="2.5" fill="#fde2e2" stroke="#f87171" stroke-width="1.4"/><path d="M-3.5 -3 H3.5 M-3.5 1 H3.5 M-3.5 5 H0" stroke="#0b1530" stroke-width="1.2" stroke-linecap="round"/></g>

    <!-- audit trail -->
    <rect x="20" y="254" width="590" height="162" rx="14" fill="rgba(7,16,38,.75)" stroke="#2a3b66"/>
    <circle cx="42" cy="276" r="4" fill="#22d3ee" class="pulse"/><text x="54" y="280" font-size="10" font-weight="800" letter-spacing="1.8" fill="#22d3ee">AUDIT TRAIL</text>
    <text x="590" y="280" font-size="9.5" text-anchor="end" fill="#64748b">13 Nov 2026 · test #412 · every step is stamped</text>
    <path d="M36 290 H594" stroke="#2a3b66"/>
    @foreach($log as $i => [$time, $who, $col, $what])
    <g opacity="0">
        <animate attributeName="opacity" values="0;0;1;1;0" keyTimes="0;{{ .06 + $i * .13 }};{{ .12 + $i * .13 }};.93;1" dur="12s" repeatCount="indefinite"/>
        <text x="40" y="{{ 314 + $i * 20 }}" font-size="10.5" fill="#64748b" font-family="ui-monospace,Menlo,Consolas,monospace">{{ $time }}</text>
        <rect x="86" y="{{ 303 + $i * 20 }}" width="{{ strlen($who) * 6.3 + 14 }}" height="15" rx="7.5" fill="{{ $col }}" fill-opacity=".14" stroke="{{ $col }}" stroke-opacity=".5"/>
        <text x="93" y="{{ 314 + $i * 20 }}" font-size="9.5" font-weight="700" fill="{{ $col }}">{{ $who }}</text>
        <text x="190" y="{{ 314 + $i * 20 }}" font-size="11" fill="#dbe8ff">{{ $what }}</text>
    </g>
    @endforeach

    <!-- access matrix -->
    <rect x="630" y="254" width="350" height="162" rx="14" fill="rgba(7,16,38,.75)" stroke="#2a3b66"/>
    <circle cx="652" cy="276" r="4" fill="#f87171" class="pulse"/><text x="664" y="280" font-size="10" font-weight="800" letter-spacing="1.8" fill="#fca5a5">ACCESS BY ROLE</text>
    @foreach($cols as $j => $cname)<text x="{{ 770 + $j * 44 }}" y="301" font-size="8.5" font-weight="700" text-anchor="middle" fill="#9aaccb">{{ $cname }}</text>@endforeach
    <rect x="640" y="0" width="330" height="22" rx="6" fill="#f87171" fill-opacity=".1" stroke="#f87171" stroke-opacity=".35"><animate attributeName="y" values="306;329;352;375;306" keyTimes="0;.25;.5;.75;1" calcMode="discrete" dur="8s" repeatCount="indefinite"/></rect>
    @foreach($roles as $i => [$rname, $cells])
    <text x="650" y="{{ 322 + $i * 23 }}" font-size="10.5" font-weight="600" fill="#e8eefc">{{ $rname }}</text>
    @foreach($cells as $j => $lv)
    @php $cx = 770 + $j * 44; $cy = 318 + $i * 23; @endphp
    @if($lv === 2)<circle cx="{{ $cx }}" cy="{{ $cy }}" r="6.5" fill="#34d399"/>@elseif($lv === 1)<circle cx="{{ $cx }}" cy="{{ $cy }}" r="6" fill="none" stroke="#fbbf24" stroke-width="2"/><path d="M{{ $cx }} {{ $cy - 6 }} A6 6 0 0 1 {{ $cx }} {{ $cy + 6 }}Z" fill="#fbbf24"/>@else<circle cx="{{ $cx }}" cy="{{ $cy }}" r="5" fill="none" stroke="#475569" stroke-width="1.4" stroke-dasharray="2 2.5"/>@endif
    @endforeach
    @endforeach
    <g font-size="9" fill="#9aaccb"><circle cx="650" cy="405" r="4.5" fill="#34d399"/><text x="660" y="408">manage</text><circle cx="716" cy="405" r="4.2" fill="none" stroke="#fbbf24" stroke-width="1.6"/><path d="M716 400.8 A4.2 4.2 0 0 1 716 409.2Z" fill="#fbbf24"/><text x="726" y="408">view</text><circle cx="768" cy="405" r="4" fill="none" stroke="#475569" stroke-dasharray="2 2.5"/><text x="778" y="408">no access</text></g>
</svg>
