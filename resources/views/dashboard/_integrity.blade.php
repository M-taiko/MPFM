    {{-- ═════ WELL INTEGRITY ═════ --}}
    @if($integrity)
    @php
        $is = $integrity['stats'];
        $attn = $integrity['rows']->filter(fn ($r) => in_array($r['a']['key'], ['red', 'amber'], true))->take(7);
        $chipMap = ['red' => 'rejected', 'amber' => 'maintenance', 'green' => 'approved', 'none' => ''];
    @endphp
    <div class="sech"><h2>Well integrity</h2><span class="ln"></span><a href="{{ route('integrity.dashboard') }}">Open Well Integrity →</a></div>
    <div class="row r21">
        <div class="card"><header><div><h3>Wells that need attention</h3><small>Amber and red wells, worst first</small></div><span class="tag">{{ $is['red'] + $is['amber'] }}</span></header>
            @if($attn->isEmpty())
                <div class="ok"><i class="fe fe-shield"></i> All assessed wells are green.</div>
            @else
            <div class="scroll"><table><thead><tr><th>Well</th><th>Status</th><th>Score</th><th>Main driver</th><th></th></tr></thead><tbody>
                @foreach($attn as $r)
                <tr><td><b style="color:#fff">{{ $r['well']->name }}</b></td><td><span class="pill {{ $chipMap[$r['a']['key']] }}">{{ $r['a']['label'] }}</span></td><td>{{ $r['a']['score'] ?? '—' }}</td>
                    <td style="color:var(--mute);font-size:12px">{{ \Illuminate\Support\Str::limit($r['a']['reasons'][0]['text'] ?? '—', 64) }}</td>
                    <td><a href="{{ route('integrity.show', $r['well']) }}">Open →</a></td></tr>
                @endforeach
            </tbody></table></div>
            @endif
        </div>
        <div class="card"><header><div><h3>Integrity status</h3><small>{{ $is['wells'] }} wells</small></div></header>
            <div class="cv s"><canvas id="cIntStatus"></canvas></div>
        </div>
    </div>
    <div class="row r3">
        <div class="card"><header><div><h3>Open anomalies</h3><small>Highest risk first</small></div><span class="tag">{{ $is['anomalies'] }}</span></header>
            @if($integrity['topAnomalies']->isEmpty()) <div class="ok"><i class="fe fe-check-circle"></i> No open anomalies.</div>
            @else <ul class="list">@foreach($integrity['topAnomalies']->take(5) as $an)@php $lv = $an->level(); @endphp
                <li><span class="n" style="--c:{{ $lv[2] }}">{{ $an->risk_score }}</span><span class="tx"><a href="{{ route('integrity.show', $an->oil_string_id) }}#anomalies" style="color:#fff;font-weight:600">{{ \Illuminate\Support\Str::limit($an->title, 38) }}</a><br><small style="color:var(--mute)">{{ $an->oilString->name ?? '' }} · {{ $lv[0] }}</small></span></li>
            @endforeach</ul> @endif
        </div>
        <div class="card"><header><div><h3>Annulus pressure alerts</h3><small>Against the MAASP</small></div></header>
            @if($integrity['annulusAlerts']->isEmpty()) <div class="ok"><i class="fe fe-check-circle"></i> All annuli are within limits.</div>
            @else <ul class="list">@foreach($integrity['annulusAlerts']->take(5) as $x)
                <li><span class="n" style="--c:{{ $x['an']['state'] === 'bad' ? '#f87171' : '#fbbf24' }};font-size:15px">{{ $x['an']['pct'] !== null ? round($x['an']['pct']) . '%' : '—' }}</span>
                    <span class="tx"><a href="{{ route('integrity.show', $x['well']) }}" style="color:#fff;font-weight:600">{{ $x['well']->name }}</a> · annulus {{ $x['an']['name'] }}<br><small style="color:var(--mute)">{{ $nf($x['an']['last']->pressure) }} psi{{ $x['an']['scp'] ? ' · rebuilds after bleed-down' : '' }}</small></span></li>
            @endforeach</ul> @endif
        </div>
        <div class="card"><header><div><h3>Barrier tests overdue</h3><small>{{ $is['overdue'] }} element(s) · {{ $is['failed'] }} failed</small></div></header>
            @if($integrity['overdueTests']->isEmpty()) <div class="ok"><i class="fe fe-check-circle"></i> No barrier tests are overdue.</div>
            @else <ul class="list">@foreach($integrity['overdueTests']->take(5) as $e)
                <li><span class="n" style="--c:#fb923c;font-size:14px">{{ (int) $e->next_due_date->diffInDays(now()) }}d</span><span class="tx"><a href="{{ route('integrity.show', $e->oil_string_id) }}" style="color:#fff;font-weight:600">{{ $e->oilString->name ?? '' }}</a><br><small style="color:var(--mute)">{{ $e->name }}</small></span></li>
            @endforeach</ul> @endif
        </div>
    </div>
    @endif
