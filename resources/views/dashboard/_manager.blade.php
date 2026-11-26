    {{-- ═════ OPERATIONS MANAGEMENT ═════ --}}
    <div class="sech"><h2>Operations at a glance</h2><span class="ln"></span><a href="{{ route('mpfm_tests.index') }}">Open tests →</a></div>
    <div class="row r21">
        <div class="card"><header><div><h3>Tests per month</h3><small>MPFM and well testing · last 12 months</small></div></header><div class="cv t"><canvas id="cMonthly"></canvas></div></div>
        <div class="card"><header><div><h3>Approval pipeline</h3><small>{{ $mpfmTotal }} MPFM tests by status</small></div></header>
            @php
                $pipe = ['draft' => ['Draft', '#64748b'], 'submitted' => ['Submitted', '#fbbf24'], 'approved' => ['Approved', '#34d399'], 'rejected' => ['Rejected', '#f87171']];
                $pmax = max(1, max($mpfmByStatus ?: [1]));
            @endphp
            <ul class="list">@foreach($pipe as $k => [$pl, $pc])
                <li><span class="tx"><b style="color:#fff;font-weight:600">{{ $pl }}</b><div class="bar" style="margin-top:6px"><i style="--w:{{ max(2, ($mpfmByStatus[$k] ?? 0) / $pmax * 100) }}%;--bc:{{ $pc }}"></i></div></span>
                    <span class="n" style="--c:{{ $pc }}">{{ $mpfmByStatus[$k] ?? 0 }}</span></li>
            @endforeach</ul>
            <div style="display:flex;gap:14px;align-items:center;flex-wrap:wrap;margin-top:12px;font-size:11.5px;color:var(--sub);white-space:nowrap"><span><i style="display:inline-block;width:9px;height:9px;border-radius:2px;background:#a78bfa;margin-right:6px"></i>Average review turnaround: {{ $turnaround ? $nf($turnaround, 0) . ' h' : '—' }}</span></div>
        </div>
    </div>
    <div class="row r3">
        <div class="card"><header><div><h3>Test calendar</h3><small>Tests per day · last 12 weeks</small></div></header>@include('dashboard._calendar')</div>
        <div class="card"><header><div><h3>Upcoming test requests</h3><small>Planned and in progress</small></div><a href="{{ route('tv_test_requests.index') }}" style="font-size:12px;color:var(--cyan)">All →</a></header>
            @if($upcomingRequests->isEmpty()) <div class="empty">No open requests.</div>
            @else <ul class="list">@foreach($upcomingRequests as $r)
                <li><span class="n" style="--c:#22d3ee;font-size:14px">{{ $r->test_date->format('d M') }}</span><span class="tx">{{ $r->testVessel->name ?? 'Testing unit' }}<br><small style="color:var(--mute)">{{ $r->tower->name ?? '' }} · {{ $r->shift }}</small></span><span class="pill {{ $r->status }}">{{ str_replace('_', ' ', $r->status) }}</span></li>
            @endforeach</ul> @endif
        </div>
        <div class="card"><header><div><h3>Upcoming Wire Line work</h3><small>Planned and in progress</small></div>@if($wl)<a href="{{ route('wireline.operations') }}" style="font-size:12px;color:var(--cyan)">All →</a>@endif</header>
            @if($upcomingWl->isEmpty()) <div class="empty">Nothing planned.</div>
            @else <ul class="list">@foreach($upcomingWl as $o)
                <li><span class="n" style="--c:{{ $o->typeColor() }};font-size:14px">{{ $o->operation_date->format('d M') }}</span><span class="tx">{{ $o->typeLabel() }}<br><small style="color:var(--mute)">{{ $o->oilString->name ?? '' }}</small></span><span class="pill {{ $o->status }}">{{ $o->statusLabel() }}</span></li>
            @endforeach</ul> @endif
        </div>
    </div>
