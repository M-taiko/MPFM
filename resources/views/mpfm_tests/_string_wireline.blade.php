{{-- Wire Line + other-department work for this well. Shown only to users with Wire Line access. --}}
@include('wireline._style')
@php
    $wl = $wireline['stats'];
    $wlOps = $wireline['operations'];
    $trend = $wireline['trend'];
    $badge = ['completed' => 'ok', 'in_progress' => 'info', 'planned' => 'pending', 'cancelled' => 'bad'];
@endphp
<div class="sec">
    <div class="panel">
        <div class="ph"><h3><i class="fe fe-git-branch"></i> Wire Line</h3>
            <span style="display:flex;gap:8px;flex-wrap:wrap">
                <a class="lnk" href="{{ route('wireline.operations', ['well' => $oilString->id]) }}">All operations</a>
                <a class="lnk" href="{{ route('wireline.create', ['well' => $oilString->id]) }}"><i class="fe fe-plus"></i> New</a>
            </span></div>

        <div class="kpis" style="margin-top:0">
            <div class="kpi" style="--c:#a78bfa"><div class="l">Operations</div><div class="v">{{ $wl['total'] }}</div><div class="u">{{ $wl['completed'] }} completed · {{ $wl['surveys'] }} surveys</div></div>
            <div class="kpi" style="--c:#22d3ee"><div class="l">Latest BHP</div><div class="v">{{ $wl['latest_bhp'] !== null ? number_format($wl['latest_bhp'], 0) : '—' }}</div>
                <div class="u">psi @if($wl['bhp_change'] !== null) · {{ sprintf('%+.0f', $wl['bhp_change']) }} vs previous @endif</div></div>
            <div class="kpi" style="--c:#f87171"><div class="l">Latest BHT</div><div class="v">{{ $wl['latest_bht'] !== null ? number_format($wl['latest_bht'], 1) : '—' }}</div><div class="u">°C</div></div>
            <div class="kpi" style="--c:{{ ($wl['days_since_survey'] ?? 0) > 180 ? '#f87171' : '#34d399' }}"><div class="l">Last survey</div>
                <div class="v">{{ $wl['days_since_survey'] !== null ? $wl['days_since_survey'] . ' d' : '—' }}</div><div class="u">{{ $wl['last_survey_at']?->format('d M Y') ?? 'never surveyed' }}</div></div>
        </div>

        @if(count($trend['labels']) > 1)
            <div class="ph" style="margin-top:20px"><h3><i class="fe fe-trending-up"></i> Bottom-hole pressure trend</h3><span class="sub">psi per survey</span></div>
            <div id="chWlBhp"></div>
        @endif

        @if($wlOps->isEmpty())
            <div class="empty">No Wire Line operations recorded for this well.</div>
        @else
            <div class="scroll" style="margin-top:14px"><table class="t"><thead><tr><th>Date</th><th>Operation</th><th>Run</th><th>Depth (m)</th><th>BHP (psi)</th><th>BHT (°C)</th><th>Status</th><th></th></tr></thead><tbody>
                @foreach($wlOps as $o)
                    @php $r = $o->gaugeRuns->first(); @endphp
                    <tr>
                        <td>{{ $o->operation_date->format('Y-m-d') }}</td>
                        <td><span class="tdot" style="--tc:{{ $o->typeColor() }}"></span>{{ $o->typeLabel() }}</td>
                        <td>#{{ $o->run_number }}</td>
                        <td>{{ $o->depth_to !== null ? number_format($o->depth_to) : '—' }}</td>
                        <td>{{ $r?->bhp !== null ? number_format($r->bhp, 1) : '—' }}</td>
                        <td>{{ $r?->bht !== null ? number_format($r->bht, 1) : '—' }}</td>
                        <td><span class="chip {{ $badge[$o->status] }}">{{ $o->statusLabel() }}</span></td>
                        <td><a class="lnk" href="{{ route('wireline.show', $o) }}"><i class="fe fe-eye"></i> View</a></td>
                    </tr>
                @endforeach
            </tbody></table></div>
        @endif
    </div>
</div>

@if($planTasks->isNotEmpty() || $tasks->isNotEmpty())
<div class="sec">
    <div class="panel">
        <div class="ph"><h3><i class="fe fe-clipboard"></i> Work from all departments</h3><span class="sub">{{ $planTasks->count() + $tasks->count() }} tasks on this well</span></div>
        <div class="scroll"><table class="t"><thead><tr><th>Date</th><th>Department</th><th>Task</th><th>Status</th><th>Assigned</th></tr></thead><tbody>
            @foreach($planTasks as $pt)
                <tr>
                    <td>{{ $pt->scheduled_date?->format('Y-m-d') ?? '—' }}</td>
                    <td>{{ $pt->department->name ?? '—' }}</td>
                    <td>{{ $pt->well_full_name ?? $pt->task_identifier ?? ('#' . $pt->id) }}</td>
                    <td><span class="chip info">{{ ucfirst($pt->completion_status ?? 'pending') }}</span></td>
                    <td>{{ $pt->assignedOperator?->name ?? '—' }}</td>
                </tr>
            @endforeach
            @foreach($tasks as $task)
                <tr>
                    <td>{{ $task->scheduled_date?->format('Y-m-d') ?? $task->created_at?->format('Y-m-d') }}</td>
                    <td>{{ $task->department->name ?? '—' }}</td>
                    <td><a class="lnk" href="{{ route('tasks.show', $task->id) }}">{{ $task->title }}</a></td>
                    <td><span class="chip info">{{ is_object($task->state) ? ($task->state->value ?? $task->state->name) : $task->state }}</span></td>
                    <td>{{ $task->assignedTo->name ?? '—' }}</td>
                </tr>
            @endforeach
        </tbody></table></div>
    </div>
</div>
@endif

@if(count($trend['labels']) > 1)
<script>
window.addEventListener('load', function () {
    var el = document.getElementById('chWlBhp');
    if (!el || !window.OFCharts) return;
    OFCharts.line(el, { labels: @json($trend['labels']), height: 240, series: [
        { name: 'BHP', data: @json($trend['bhp']), color: '#22d3ee', unit: 'psi' }
    ] });
});
</script>
@endif
