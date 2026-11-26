    {{-- ═════ WIRE LINE ═════ --}}
    @if($wl)
    <div class="sech"><h2>Wire Line</h2><span class="ln"></span><a href="{{ route('wireline.dashboard') }}">Open Wire Line →</a></div>
    <div class="card ill" style="margin-bottom:16px">
        <div class="ct"><span>Wire Line operation · memory-gauge survey</span><a href="{{ route('wireline.create') }}">New operation →</a></div>
        @include('partials.wireline_rig')
    </div>
    <div class="row r3">
        <div class="card"><header><div><h3>Operations per month</h3><small>Last 12 months</small></div></header><div class="cv s"><canvas id="cWlMonth"></canvas></div></div>
        <div class="card"><header><div><h3>Operations by type</h3><small>{{ $wl['stats']['total'] }} total on {{ $wl['stats']['wells'] }} well(s)</small></div></header>
            @if($wl['byType']->isEmpty()) <div class="empty">No operations recorded.</div> @else <div class="cv s"><canvas id="cWlType"></canvas></div> @endif</div>
        <div class="card"><header><div><h3>Surveys overdue</h3><small>Active wells, no survey in 180 days</small></div><span class="tag">{{ $wl['overdueCount'] }}</span></header>
            @if($wl['overdue']->isEmpty()) <div class="ok"><i class="fe fe-check-circle"></i> All active wells are up to date.</div>
            @else <ul class="list">@foreach($wl['overdue']->take(6) as $o)<li><span class="tx"><a href="{{ route('mpfm_tests.string_analysis', $o['well']->id) }}" style="color:#fff;font-weight:600">{{ $o['well']->name }}</a><br><small style="color:var(--mute)">{{ $o['last'] ? $o['days'] . ' days since last survey' : 'never surveyed' }}</small></span><a class="go" href="{{ route('wireline.create', ['well' => $o['well']->id]) }}">Schedule →</a></li>@endforeach</ul> @endif</div>
    </div>
    <div class="row r2">
        <div class="card"><header><div><h3>Latest memory-gauge surveys</h3><small>Final bottom-hole pressure &amp; temperature</small></div></header>
            @if($wl['runs']->isEmpty()) <div class="empty">No gauge data uploaded yet.</div>
            @else <div class="scroll"><table><thead><tr><th>Well</th><th>Date</th><th>Type</th><th>BHP (psi)</th><th>BHT (°C)</th><th></th></tr></thead><tbody>
                @foreach($wl['runs']->take(6) as $r)
                <tr><td>{{ $r->operation->oilString->name ?? '—' }}</td><td>{{ ($r->started_at ?? $r->operation->operation_date)->format('d M Y') }}</td><td>{{ strtoupper($r->survey_type) }}</td>
                    <td>{{ $nf($r->bhp, 1) }}</td><td>{{ $nf($r->bht, 1) }}</td><td><a href="{{ route('wireline.show', [$r->operation, 'run' => $r->id]) }}">Open</a></td></tr>
                @endforeach</tbody></table></div> @endif</div>
        <div class="card"><header><div><h3>Recent Wire Line operations</h3><small>Latest interventions across the field</small></div></header>
            @if($wl['recent']->isEmpty()) <div class="empty">No operations yet.</div>
            @else <div class="scroll"><table><thead><tr><th>Date</th><th>Well</th><th>Operation</th><th>Status</th><th></th></tr></thead><tbody>
                @foreach($wl['recent']->take(6) as $o)
                <tr><td>{{ $o->operation_date->format('d M') }}</td><td>{{ $o->oilString->name ?? '—' }}</td><td>{{ $o->typeLabel() }}</td><td><span class="pill {{ $o->status }}">{{ $o->statusLabel() }}</span></td><td><a href="{{ route('wireline.show', $o) }}">Open</a></td></tr>
                @endforeach</tbody></table></div> @endif</div>
    </div>
    @endif
