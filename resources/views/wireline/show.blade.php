@extends('layouts.master')

@section('title', 'Wire Line - ' . $operation->typeLabel())

@section('css')
@include('wireline._style')
@endsection

@section('content')
@php
    use App\Models\WirelineGaugeRun as Run;
    $well = $operation->oilString;
    $a = $run?->analysis ?? [];
    $st = $a['stats'] ?? [];
    $fmt = fn ($v, $d = 1) => $v === null ? '—' : number_format($v, $d);
    $hm = function ($min) { if ($min === null) return '—'; $m = (int) round($min); return $m >= 60 ? intdiv($m, 60) . 'h ' . ($m % 60) . 'm' : $m . ' min'; };
    $statusChip = ['completed'=>'ok','in_progress'=>'info','planned'=>'pending','cancelled'=>'bad'][$operation->status];
    $tab = request('tab', 'summary');
    $isGauge = \App\Models\WirelineOperation::TYPES[$operation->operation_type][1] ?? false;
@endphp
<div class="ofp" id="ofp">
    <div class="bgl"><div class="orb o1"></div><div class="orb o2"></div></div>

    <div class="hero">
        <div class="who">
            <span class="lg"></span>
            <div>
                <div class="eyebrow"><i></i> Wire Line · Run #{{ $operation->run_number }}</div>
                <h1><em>{{ $operation->typeLabel() }}</em> — {{ $well->name ?? 'Unknown well' }}</h1>
                <div class="meta">
                    <a href="{{ route('wireline.operations') }}">&larr; Operations</a>
                    @if($well)<a href="{{ route('oil_strings.show', $well) }}">Well profile</a>@endif
                    <span>{{ $operation->operation_date->format('d M Y') }}</span>
                    <span class="chip {{ $statusChip }}">{{ $operation->statusLabel() }}</span>
                </div>
            </div>
        </div>
        <div class="actions">
            @if($canManage)<a class="btn2" href="{{ route('wireline.edit', $operation) }}"><i class="fe fe-edit"></i> Edit</a>@endif
            @if($canDelete)
            <form method="POST" action="{{ route('wireline.destroy', $operation) }}" onsubmit="return confirm('Delete this operation and its gauge data?')">
                @csrf @method('DELETE')<button class="btn2 w" type="submit"><i class="fe fe-trash-2"></i> Delete</button>
            </form>
            @endif
        </div>
    </div>

    @if(session('success'))<div class="msg ok flash"><i class="fe fe-check-circle"></i><span>{{ session('success') }}</span></div>@endif
    @if($errors->any())
        <div class="err"><b><i class="fe fe-alert-circle"></i> Problem:</b><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    {{-- Operation details --}}
    <div class="panel" style="margin-top:18px">
        <div class="ph"><h3><i class="fe fe-clipboard"></i> Operation details</h3></div>
        <div class="kv">
            <div><small>Well</small><b>{{ $well->name ?? '—' }}</b></div>
            <div><small>Field / Tower</small><b>{{ $well?->tower?->oilField?->name ?? '—' }} / {{ $well?->tower?->name ?? '—' }}</b></div>
            <div><small>Tool string</small><b>{{ $operation->tool_string ?: '—' }}</b></div>
            <div><small>Depth (m)</small><b>{{ $operation->depth_from !== null ? number_format($operation->depth_from) : '0' }} → {{ $operation->depth_to !== null ? number_format($operation->depth_to) : '—' }}</b></div>
            <div><small>Crew leader</small><b>{{ $operation->crew_leader ?: '—' }}</b></div>
            <div><small>Recorded by</small><b>{{ $operation->creator->name ?? '—' }}</b></div>
        </div>
        @if($operation->objective)<div class="note"><small>Objective</small>{{ $operation->objective }}</div>@endif
        @if($operation->remarks)<div class="note" style="border-left-color:var(--cyan);background:rgba(34,211,238,.06);border-color:rgba(34,211,238,.25)"><small style="color:var(--cyan)">Remarks</small>{{ $operation->remarks }}</div>@endif
    </div>

    @if($isGauge || $operation->gaugeRuns->isNotEmpty())
    @if($operation->gaugeRuns->count() > 1)
    <div class="tabs" style="margin-top:18px">
        @foreach($operation->gaugeRuns as $r)
            <a class="tab {{ $run && $r->id === $run->id ? 'on' : '' }}" href="{{ route('wireline.show', [$operation, 'run' => $r->id]) }}">
                {{ $r->gauge_serial ?: 'Gauge' }} · {{ strtoupper($r->survey_type) }}</a>
        @endforeach
    </div>
    @endif

    @if(!$run)
        <div class="panel" style="margin-top:18px"><div class="empty">No gauge data attached yet.</div></div>
    @else
    {{-- KPIs --}}
    <div class="kpis">
        <div class="kpi" style="--c:#22d3ee"><div class="ic"><i class="fe fe-arrow-down-circle"></i></div><div class="l">Final BHP</div><div class="v">{{ $fmt($st['bhp'] ?? null) }}</div><div class="u">psi · {{ $run->pressure_unit }}</div></div>
        <div class="kpi" style="--c:#f87171"><div class="ic"><i class="fe fe-thermometer"></i></div><div class="l">Bottom-hole temp</div><div class="v">{{ $fmt($st['bht'] ?? null) }}</div><div class="u">°C</div></div>
        <div class="kpi" style="--c:#a78bfa"><div class="ic"><i class="fe fe-trending-up"></i></div><div class="l">Max pressure</div><div class="v">{{ $fmt($st['max_pressure'] ?? null) }}</div><div class="u">psi @ {{ $hm($st['max_pressure_min'] ?? null) }}</div></div>
        <div class="kpi" style="--c:#34d399"><div class="ic"><i class="fe fe-clock"></i></div><div class="l">On bottom</div><div class="v">{{ $hm($st['bottom_duration_min'] ?? null) }}</div><div class="u">RIH {{ $hm($st['rih_duration_min'] ?? null) }} · POOH {{ $hm($st['pooh_duration_min'] ?? null) }}</div></div>
        <div class="kpi" style="--c:{{ ($st['stabilised'] ?? false) ? '#34d399' : '#fbbf24' }}"><div class="ic"><i class="fe fe-activity"></i></div><div class="l">End-of-bottom trend</div><div class="v">{{ isset($st['trend_psi_per_hr']) ? sprintf('%+.1f', $st['trend_psi_per_hr']) : '—' }}</div><div class="u">psi/hr · {{ ($st['stabilised'] ?? false) ? 'stabilised' : 'not stable' }}</div></div>
        <div class="kpi" style="--c:#60a5fa"><div class="ic"><i class="fe fe-database"></i></div><div class="l">Recording</div><div class="v">{{ $fmt($st['total_duration_h'] ?? null, 1) }} h</div><div class="u">{{ number_format($run->sample_count) }} samples / {{ $run->sample_interval_sec }}s</div></div>
    </div>

    {{-- Chart --}}
    <div class="panel" style="margin-top:18px">
        <div class="ph"><h3><i class="fe fe-activity"></i> Pressure &amp; temperature</h3>
            <span class="sub">{{ $run->started_at?->format('d M Y H:i') }} → {{ $run->ended_at?->format('d M Y H:i') }} · {{ $run->surveyLabel() }}</span></div>
        <div class="cw" style="height:380px"><canvas id="mainChart"></canvas></div>
        <div style="display:flex;gap:14px;flex-wrap:wrap;margin-top:10px;font-size:11.5px;color:var(--sub)">
            <span><span class="tdot" style="--tc:#22d3ee"></span>Pressure</span>
            <span><span class="tdot" style="--tc:#f87171"></span>Temperature</span>
            <span><span class="tdot" style="--tc:#60a5fa"></span>RIH</span>
            <span><span class="tdot" style="--tc:#34d399"></span>On bottom</span>
            <span><span class="tdot" style="--tc:#fbbf24"></span>POOH</span>
            <span><span class="tdot" style="--tc:#fff"></span>Gauge stops</span>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="tabs" style="margin-top:18px" id="tabs">
        @foreach(['summary'=>'Analysis','phases'=>'Run phases','events'=>'Events & build-up','gradients'=>'Gauge stops & gradients','gauge'=>'Gauge & file'] as $k => $l)
            <button type="button" class="tab {{ $tab === $k ? 'on' : '' }}" data-tab="{{ $k }}">{{ $l }}</button>
        @endforeach
    </div>

    <div class="pane {{ $tab === 'summary' ? 'on' : '' }}" id="pane-summary">
        <div class="wl-g3" style="margin-top:0">
            <div class="panel">
                <div class="ph"><h3><i class="fe fe-file-text"></i> Interpretation</h3></div>
                <ul class="list">@foreach($a['summary'] ?? [] as $line)<li><i class="fe fe-chevron-right"></i><span>{{ $line }}</span></li>@endforeach</ul>
                @if(isset($st['avg_rih_speed_m_min']))
                    <div class="kv" style="margin-top:14px">
                        <div><small>Avg RIH speed</small><b>{{ $st['avg_rih_speed_m_min'] }} m/min</b></div>
                        <div><small>Avg POOH speed</small><b>{{ $st['avg_pooh_speed_m_min'] }} m/min</b></div>
                        <div><small>Surface zero (pre/post)</small><b>{{ $fmt($st['surface_pre'] ?? null, 2) }} / {{ $fmt($st['surface_post'] ?? null, 2) }} psi</b></div>
                    </div>
                @endif
            </div>
            <div class="panel">
                <div class="ph"><h3><i class="fe fe-shield"></i> Data quality</h3></div>
                <ul class="list q">
                    @forelse($a['quality'] ?? [] as $q)
                    <li class="{{ $q['level'] }}"><i class="fe {{ ['ok'=>'fe-check-circle','warn'=>'fe-alert-triangle','bad'=>'fe-x-circle'][$q['level']] }}"></i><span>{{ $q['text'] }}</span></li>
                    @empty<li><i class="fe fe-info"></i><span>No checks available.</span></li>@endforelse
                </ul>
            </div>
        </div>
    </div>

    <div class="pane {{ $tab === 'phases' ? 'on' : '' }}" id="pane-phases">
        <div class="panel"><div class="ph"><h3><i class="fe fe-layers"></i> Run phases</h3></div>
        @if(empty($a['phases']))<div class="empty">No run detected in this recording.</div>
        @else<div class="scroll"><table class="t"><thead><tr><th>Phase</th><th>Start</th><th>End</th><th>Duration</th><th>P start (psi)</th><th>P end (psi)</th></tr></thead><tbody>
            @foreach($a['phases'] as $p)
            <tr><td>{{ $p['label'] }}</td>
                <td>{{ $run->started_at?->copy()->addSeconds($p['start_sec'])->format('d M H:i') }}</td>
                <td>{{ $run->started_at?->copy()->addSeconds($p['end_sec'])->format('d M H:i') }}</td>
                <td>{{ $hm($p['duration_min']) }}</td><td>{{ $fmt($p['p_start']) }}</td><td>{{ $fmt($p['p_end']) }}</td></tr>
            @endforeach</tbody></table></div>@endif
        </div>
    </div>

    <div class="pane {{ $tab === 'events' ? 'on' : '' }}" id="pane-events">
        <div class="panel"><div class="ph"><h3><i class="fe fe-zap"></i> Pressure events on bottom</h3><span class="sub">movements ≥ 1% of peak pressure</span></div>
        @if(!empty($a['build_up']))
            @php $b = $a['build_up']; @endphp
            <div class="msg ok" style="margin-bottom:14px"><i class="fe fe-trending-up"></i><span><b>Build-up:</b> +{{ $fmt($b['delta'], 0) }} psi ({{ $fmt($b['p_start'], 0) }} → {{ $fmt($b['p_end'], 0) }}) over {{ $hm($b['duration_min']) }}@if($b['t90_min'] !== null); 90% of the recovery in {{ $hm($b['t90_min']) }}@endif.</span></div>
        @endif
        @if(empty($a['events']))<div class="empty">No significant pressure movement while on bottom — the well was steady.</div>
        @else<div class="scroll"><table class="t"><thead><tr><th>#</th><th>Type</th><th>Start</th><th>End</th><th>From (psi)</th><th>To (psi)</th><th>Δ (psi)</th></tr></thead><tbody>
            @foreach($a['events'] as $i => $e)
            <tr><td>{{ $i + 1 }}</td><td><span class="chip {{ $e['type'] === 'rise' ? 'ok' : 'warn' }}">{{ $e['type'] }}</span></td>
                <td>{{ $run->started_at?->copy()->addSeconds($e['start_sec'])->format('H:i') }} ({{ $hm($e['start_min']) }})</td>
                <td>{{ $run->started_at?->copy()->addSeconds($e['end_sec'])->format('H:i') }}</td>
                <td>{{ $fmt($e['p_from']) }}</td><td>{{ $fmt($e['p_to']) }}</td><td>{{ sprintf('%+.1f', $e['delta']) }}</td></tr>
            @endforeach</tbody></table></div>@endif
        </div>
    </div>

    <div class="pane {{ $tab === 'gradients' ? 'on' : '' }}" id="pane-gradients">
        <div class="panel"><div class="ph"><h3><i class="fe fe-bar-chart"></i> Gauge stops &amp; fluid gradients</h3><span class="sub">enter the depth of each stop (m) to compute gradients</span></div>
        @if(empty($a['stops']))<div class="empty">No gauge stops (≥ 3 min stationary) were detected during run-in or pull-out.</div>
        @else
        <form method="POST" action="{{ route('wireline.runs.stops', $run) }}" style="display:block">@csrf
        <div class="scroll"><table class="t"><thead><tr><th>#</th><th>Pass</th><th>Time</th><th>Duration</th><th>Pressure (psi)</th><th>Temp (°C)</th><th>Depth (m)</th></tr></thead><tbody>
            @foreach($a['stops'] as $i => $s)
            <tr><td>{{ $i + 1 }}</td><td><span class="chip {{ $s['direction'] === 'RIH' ? 'info' : 'warn' }}">{{ $s['direction'] }}</span></td>
                <td>{{ $run->started_at?->copy()->addSeconds($s['start_sec'])->format('d M H:i') }}</td>
                <td>{{ $hm($s['duration_min']) }}</td><td>{{ $fmt($s['pressure']) }}</td><td>{{ $fmt($s['temperature']) }}</td>
                <td>@if($canManage)<input class="fc sdep" type="number" step="0.1" min="0" name="depths[{{ $i }}]" value="{{ $s['depth'] ?? '' }}" placeholder="m">@else{{ $s['depth'] ?? '—' }}@endif</td></tr>
            @endforeach</tbody></table></div>
        @if($canManage)<div style="margin-top:12px"><button class="btn2 p" type="submit"><i class="fe fe-save"></i> Save depths &amp; calculate</button></div>@endif
        </form>
        @endif
        @if(!empty($a['gradients']))
            <div class="ph" style="margin-top:22px"><h3><i class="fe fe-droplet"></i> Gradients</h3></div>
            <div class="scroll"><table class="t"><thead><tr><th>Pass</th><th>Interval (m)</th><th>Gradient (psi/ft)</th><th>Density (g/cc)</th><th>Fluid</th></tr></thead><tbody>
                @foreach($a['gradients'] as $g)
                <tr><td>{{ $g['direction'] }}</td><td>{{ number_format($g['from_depth']) }} – {{ number_format($g['to_depth']) }}</td>
                    <td>{{ number_format($g['psi_per_ft'], 4) }}</td><td>{{ number_format($g['density_gcc'], 3) }}</td><td>{{ $g['fluid'] }}</td></tr>
                @endforeach</tbody></table></div>
        @endif
        </div>
    </div>

    <div class="pane {{ $tab === 'gauge' ? 'on' : '' }}" id="pane-gauge">
        <div class="panel"><div class="ph"><h3><i class="fe fe-cpu"></i> Gauge &amp; source file</h3></div>
            <div class="kv">
                <div><small>Serial</small><b>{{ $run->gauge_serial ?: '—' }}</b></div>
                <div><small>Model</small><b>{{ $run->gauge_model ?: '—' }}</b></div>
                <div><small>Manufacturer</small><b>{{ $run->gauge_manufacturer ?: '—' }}</b></div>
                <div><small>Last calibration</small><b>{{ $run->calibration_date?->format('d M Y') ?? '—' }}</b></div>
                <div><small>Max range</small><b>{{ $run->max_range ? number_format($run->max_range) . ' psi' : '—' }}</b></div>
                <div><small>Sample interval</small><b>{{ $run->sample_interval_sec }} s</b></div>
                <div><small>Source file</small><b>{{ $run->source_file }}</b></div>
                <div><small>Survey type</small><b>{{ $run->surveyLabel() }}</b></div>
            </div>
            @if($run->calibration_date && $run->calibration_date->diffInMonths($run->started_at ?? now()) > 12)
                <div class="msg warn" style="margin-top:14px"><i class="fe fe-alert-triangle"></i><span>Gauge calibration was more than 12 months before this run.</span></div>
            @endif
            <div style="display:flex;gap:10px;margin-top:16px;flex-wrap:wrap">
                @if($canManage)<form method="POST" action="{{ route('wireline.runs.reanalyse', $run) }}">@csrf<button class="btn2" type="submit"><i class="fe fe-refresh-cw"></i> Re-run analysis</button></form>@endif
                @if($canDelete)<form method="POST" action="{{ route('wireline.runs.destroy', $run) }}" onsubmit="return confirm('Remove this gauge run?')">@csrf @method('DELETE')<button class="btn2 w" type="submit"><i class="fe fe-trash-2"></i> Remove run</button></form>@endif
            </div>
        </div>
    </div>
    @endif

    {{-- Upload another gauge file --}}
    @if($canManage)
    <div class="panel" style="margin-top:18px">
        <div class="ph"><h3><i class="fe fe-upload"></i> Upload gauge data</h3><span class="sub">.log, .csv or .txt memory-gauge export</span></div>
        <form method="POST" action="{{ route('wireline.runs.upload', $operation) }}" enctype="multipart/form-data" style="display:block">@csrf
            <div class="row">
                <div class="f"><label class="lb" for="gf"><i class="fe fe-file"></i> File</label><input class="fc" type="file" id="gf" name="gauge_file" accept=".log,.csv,.txt" required></div>
                <div class="f"><label class="lb" for="st"><i class="fe fe-activity"></i> Survey type</label>
                    <select class="fc" id="st" name="survey_type">@foreach(Run::SURVEY_TYPES as $k => $l)<option value="{{ $k }}">{{ $l }}</option>@endforeach</select></div>
            </div>
            <div style="margin-top:14px"><button class="btn2 p" type="submit"><i class="fe fe-upload-cloud"></i> Upload &amp; analyse</button></div>
        </form>
    </div>
    @endif
    @endif
</div>
@endsection

@section('scripts')
@if($run && $series)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    document.querySelectorAll('#tabs .tab').forEach(b => b.addEventListener('click', () => {
        document.querySelectorAll('#tabs .tab').forEach(x => x.classList.toggle('on', x === b));
        document.querySelectorAll('.pane').forEach(p => p.classList.toggle('on', p.id === 'pane-' + b.dataset.tab));
    }));
    if (!window.Chart) return;

    const mins = @json($series['min']), P = @json($series['p']), T = @json($series['t']);
    const phases = @json($a['phases'] ?? []);
    const stops = @json($a['stops'] ?? []);
    const startMs = {{ $run->started_at ? $run->started_at->copy()->shiftTimezone('UTC')->timestamp * 1000 : 'null' }};
    const hrs = mins.map(m => m / 60);
    const pp = hrs.map((x, i) => ({ x, y: P[i] })), tt = hrs.map((x, i) => ({ x, y: T[i] }));
    const colors = { rih: 'rgba(96,165,250,.10)', bottom: 'rgba(52,211,153,.10)', pooh: 'rgba(251,191,36,.10)' };

    const shade = {
        id: 'phaseShade',
        beforeDatasetsDraw(chart) {
            const { ctx, chartArea: ca, scales: { x } } = chart;
            ctx.save();
            phases.forEach(ph => {
                if (!colors[ph.key]) return;
                const x0 = x.getPixelForValue(ph.start_sec / 3600), x1 = x.getPixelForValue(ph.end_sec / 3600);
                ctx.fillStyle = colors[ph.key];
                ctx.fillRect(x0, ca.top, x1 - x0, ca.bottom - ca.top);
            });
            ctx.fillStyle = 'rgba(255,255,255,.75)';
            stops.forEach(s => {
                const px = x.getPixelForValue(((s.start_sec + s.end_sec) / 2) / 3600);
                ctx.beginPath(); ctx.arc(px, ca.bottom - 8, 3.5, 0, Math.PI * 2); ctx.fill();
            });
            ctx.restore();
        }
    };

    const clock = h => {
        if (!startMs) return h.toFixed(1) + ' h';
        const d = new Date(startMs + h * 3600000);
        return d.toISOString().substr(11, 5);
    };
    Chart.defaults.color = '#9fb0cf';
    Chart.defaults.borderColor = 'rgba(96,165,250,.12)';
    new Chart(document.getElementById('mainChart'), {
        type: 'line',
        plugins: [shade],
        data: { datasets: [
            { label: 'Pressure (psi)', data: pp, yAxisID: 'y', borderColor: '#22d3ee', borderWidth: 1.6, pointRadius: 0, tension: 0 },
            { label: 'Temperature (°C)', data: tt, yAxisID: 'y1', borderColor: '#f87171', borderWidth: 1.4, pointRadius: 0, tension: 0 }
        ] },
        options: {
            responsive: true, maintainAspectRatio: false, parsing: false, normalized: true, animation: false,
            interaction: { mode: 'nearest', axis: 'x', intersect: false },
            plugins: {
                legend: { display: false },
                decimation: { enabled: true, algorithm: 'lttb', samples: 900 },
                tooltip: { callbacks: { title: i => clock(i[0].parsed.x) + '  (+' + i[0].parsed.x.toFixed(2) + ' h)' } }
            },
            scales: {
                x: { type: 'linear', title: { display: true, text: 'Time (clock)' }, ticks: { callback: v => clock(v), maxTicksLimit: 12 } },
                y: { position: 'left', title: { display: true, text: 'Pressure (psi)' } },
                y1: { position: 'right', title: { display: true, text: 'Temperature (°C)' }, grid: { drawOnChartArea: false } }
            }
        }
    });
})();
</script>
@else
<script>
document.querySelectorAll('#tabs .tab').forEach(b => b.addEventListener('click', () => {}));
</script>
@endif
@endsection
