@extends('layouts.master')

@section('title', 'Well Integrity - ' . $well->name)

@section('css')
@include('integrity._style')
@endsection

@section('content')
@php
    use App\Models\WellIntegrityProfile as Pf;
    use App\Models\WellIntegrityAnnulus as Ann;
    use App\Models\WellIntegrityAnnulusReading as Rd;
    use App\Models\WellBarrierElement as El;
    use App\Models\WellIntegrityTest as Ts;
    use App\Models\WellIntegrityInspection as Ins;
    use App\Models\WellIntegrityAnomaly as An;
    $p = $a['profile'];
    $nf = fn ($v, $d = 0) => is_numeric($v) ? number_format((float) $v, $d) : '—';
    $circ = 2 * M_PI * 52;
    $elChip = ['healthy' => 'ok', 'degraded' => 'warn', 'failed' => 'bad', 'untested' => 'none'];
    $elColor = ['healthy' => '#34d399', 'degraded' => '#fbbf24', 'failed' => '#f87171', 'untested' => '#94a3b8'];
    $stateChip = ['ok' => 'ok', 'warn' => 'warn', 'bad' => 'bad', 'none' => 'none'];
    $allElements = $a['envelopes']['primary']['els']->merge($a['envelopes']['secondary']['els']);
    $today = now()->format('Y-m-d');
@endphp
<div class="ofp" id="ofp">
    <div class="bgl"><div class="orb o1"></div><div class="orb o2"></div></div>

    <div class="hero">
        <div class="who">
            <span class="lg"></span>
            <div>
                <div class="eyebrow"><i></i> Well Integrity File</div>
                <h1>{{ $well->name }} <em>· integrity</em></h1>
                <div class="meta">
                    <a href="{{ route('integrity.wells') }}">&larr; Wells register</a>
                    <span>{{ $well->tower?->oilField?->name }} · {{ $well->tower?->name }}</span>
                    <span class="chip {{ $a['class'] }}">{{ $a['label'] }}</span>
                </div>
            </div>
        </div>
        <div class="actions">
            <a class="btn2" href="{{ route('mpfm_tests.string_analysis', $well) }}"><i class="fe fe-activity"></i> Well analysis</a>
            @if($canManage)<a class="btn2 p" href="{{ route('integrity.checks.create', $well) }}"><i class="fe fe-clipboard"></i> Wellhead check</a>@endif
            @if($canManage && $allElements->isEmpty())
                <form method="POST" action="{{ route('integrity.barriers.template', $well) }}" style="display:inline">@csrf
                    <button class="btn2 p" type="submit"><i class="fe fe-layers"></i> Apply standard barrier template</button></form>
            @endif
        </div>
    </div>

    @if(session('success'))<div class="msg ok flash"><i class="fe fe-check-circle"></i><span>{{ session('success') }}</span></div>@endif
    @if($errors->any())<div class="err"><b><i class="fe fe-alert-circle"></i> Please fix the following:</b><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

    {{-- ═════ STATUS ═════ --}}
    <div class="panel" style="margin-top:18px">
        <div class="ph"><h3><i class="fe fe-shield"></i> Integrity status</h3><span class="sub">{{ $a['guidance'] }}</span></div>
        <div class="ig-top">
            <div style="text-align:center">
                <div class="big-ring">
                    <svg viewBox="0 0 120 120"><circle cx="60" cy="60" r="52" fill="none" stroke="rgba(255,255,255,.08)" stroke-width="10"/>
                        <circle cx="60" cy="60" r="52" fill="none" stroke="{{ $a['color'] }}" stroke-width="10" stroke-linecap="round" stroke-dasharray="{{ $circ }}" stroke-dashoffset="{{ $circ * (1 - ($a['score'] ?? 0) / 100) }}"/></svg>
                    <b><span>{{ $a['score'] ?? '—' }}<small>integrity</small></span></b>
                </div>
                <div style="margin-top:10px"><span class="chip {{ $a['class'] }}" style="font-size:13px">{{ strtoupper($a['label']) }}</span></div>
            </div>
            <div>
                <div class="kv">
                    <div><small>Annuli in alert</small><b>{{ $a['counts']['annuli_alert'] }} / {{ count($a['annuli']) }}</b></div>
                    <div><small>Failed / degraded</small><b style="color:{{ $a['counts']['failed'] ? '#f87171' : '#fff' }}">{{ $a['counts']['failed'] }} / {{ $a['counts']['degraded'] }}</b></div>
                    <div><small>Tests overdue</small><b>{{ $a['counts']['overdue_tests'] }}</b></div>
                    <div><small>Open anomalies</small><b>{{ $a['counts']['open_anomalies'] }}@if($a['counts']['critical']) <span style="color:#f87171">({{ $a['counts']['critical'] }} critical)</span>@endif</b></div>
                    <div><small>Worst wall loss</small><b>{{ $a['worst_wall_loss'] !== null ? $a['worst_wall_loss'] . '%' : '—' }}</b></div>
                    <div><small>Next review</small><b @if($a['review_overdue']) style="color:#fca5a5" @endif>{{ $a['next_review']?->format('d M Y') ?? 'Not scheduled' }}</b></div>
                </div>
                @if($a['reasons'])
                    <div class="mini" style="margin-top:16px;text-transform:uppercase;letter-spacing:.1em">What drives the status</div>
                    @foreach($a['reasons'] as $r)
                        <div class="rs {{ $r['sev'] }}"><b>{{ $r['delta'] }}</b><span><span class="mini">{{ $r['area'] }} ·</span> {{ $r['text'] }}</span></div>
                    @endforeach
                @elseif($a['has_data'])
                    <div class="msg ok" style="margin-top:14px"><i class="fe fe-check-circle"></i><span>No adverse integrity indicators for this well.</span></div>
                @else
                    <div class="msg warn" style="margin-top:14px"><i class="fe fe-alert-triangle"></i><span>Nothing recorded yet. Apply the standard barrier template, set annulus MAASP values and record the first tests.</span></div>
                @endif
                @if($p && ($p->max_oil_rate || $p->mawhp))
                    <div class="msg warn" style="margin-top:14px"><i class="fe fe-sliders"></i><span><b>Operating limits:</b>
                        @if($p->max_oil_rate) max oil {{ $nf($p->max_oil_rate) }} B/D @endif @if($p->mawhp) · MAWHP {{ $nf($p->mawhp) }} psi @endif
                        @if($p->restriction_note) · {{ $p->restriction_note }} @endif — these limits are applied in the well analysis.</span></div>
                @endif
            </div>
        </div>
    </div>

    {{-- ═════ PROFILE ═════ --}}
    <div class="panel" style="margin-top:18px">
        <div class="ph"><h3><i class="fe fe-sliders"></i> Integrity profile &amp; operating limits</h3><span class="sub">drives rate / pressure checks in the well analysis</span></div>
        @if($p)
        <div class="kv">
            <div><small>Well type</small><b>{{ Pf::WELL_TYPES[$p->well_type] ?? $p->well_type }}</b></div>
            <div><small>Lift method</small><b>{{ Pf::LIFT_METHODS[$p->lift_method] ?? $p->lift_method }}</b></div>
            <div><small>Tubing</small><b>{{ $p->tubing_size ?: '—' }}</b></div>
            <div><small>Production casing</small><b>{{ $p->casing_size ?: '—' }}</b></div>
            <div><small>MAWHP</small><b>{{ $p->mawhp ? $nf($p->mawhp) . ' psi' : '—' }}</b></div>
            <div><small>Max oil rate</small><b>{{ $p->max_oil_rate ? $nf($p->max_oil_rate) . ' B/D' : '—' }}</b></div>
            <div><small>Last review</small><b>{{ $p->last_review_date?->format('d M Y') ?? '—' }}{{ $p->reviewed_by ? ' · ' . $p->reviewed_by : '' }}</b></div>
            <div><small>Review interval</small><b>{{ $p->review_interval_days }} days</b></div>
        </div>
        @else
            <div class="empty">No profile yet.</div>
        @endif
        @if($canManage)
        <details class="frm"><summary><i class="fe fe-edit-3"></i> {{ $p ? 'Edit profile' : 'Create profile' }}</summary>
        <form class="in" method="POST" action="{{ route('integrity.profile', $well) }}">@csrf
            <div class="row">
                <div class="f"><label class="lb">Well type</label><select class="fc" name="well_type">@foreach(Pf::WELL_TYPES as $k => $l)<option value="{{ $k }}" @selected(old('well_type', $p->well_type ?? 'producer') === $k)>{{ $l }}</option>@endforeach</select></div>
                <div class="f"><label class="lb">Lift method</label><select class="fc" name="lift_method">@foreach(Pf::LIFT_METHODS as $k => $l)<option value="{{ $k }}" @selected(old('lift_method', $p->lift_method ?? 'natural_flow') === $k)>{{ $l }}</option>@endforeach</select></div>
                <div class="f"><label class="lb">Tubing size</label><input class="fc" name="tubing_size" maxlength="40" placeholder='e.g. 4-1/2" 12.6#' value="{{ old('tubing_size', $p->tubing_size ?? '') }}"></div>
                <div class="f"><label class="lb">Production casing</label><input class="fc" name="casing_size" maxlength="60" placeholder='e.g. 9-5/8" 47# L80' value="{{ old('casing_size', $p->casing_size ?? '') }}"></div>
                <div class="f"><label class="lb">MAWHP (psi)</label><input class="fc" type="number" step="0.1" min="0" name="mawhp" value="{{ old('mawhp', $p->mawhp ?? '') }}"></div>
                <div class="f"><label class="lb">Max oil rate (B/D)</label><input class="fc" type="number" step="0.1" min="0" name="max_oil_rate" value="{{ old('max_oil_rate', $p->max_oil_rate ?? '') }}"></div>
                <div class="f"><label class="lb">Restriction reason</label><input class="fc" name="restriction_note" maxlength="255" value="{{ old('restriction_note', $p->restriction_note ?? '') }}"></div>
                <div class="f"><label class="lb">Review interval (days)</label><input class="fc" type="number" min="30" max="1825" name="review_interval_days" required value="{{ old('review_interval_days', $p->review_interval_days ?? 365) }}"></div>
                <div class="f"><label class="lb">Last review date</label><input class="fc" type="date" name="last_review_date" value="{{ old('last_review_date', $p?->last_review_date?->format('Y-m-d')) }}"></div>
                <div class="f"><label class="lb">Reviewed by</label><input class="fc" name="reviewed_by" maxlength="255" value="{{ old('reviewed_by', $p->reviewed_by ?? '') }}"></div>
            </div>
            <div class="f" style="margin-top:14px"><label class="lb">Notes</label><textarea class="fc" name="notes" rows="2" maxlength="5000">{{ old('notes', $p->notes ?? '') }}</textarea></div>
            <div style="margin-top:14px"><button class="btn2 p" type="submit"><i class="fe fe-save"></i> Save profile</button></div>
        </form></details>
        @endif
    </div>

    {{-- ═════ ANNULI ═════ --}}
    <div class="panel" style="margin-top:18px" id="annuli">
        <div class="ph"><h3><i class="fe fe-gauge"></i> Annulus pressure monitoring</h3><span class="sub">pressure vs MAASP · sustained casing pressure check</span></div>
        @if(empty($a['annuli']))
            <div class="empty">No annuli configured. Add annulus A (and B/C/D) with its MAASP.</div>
        @else
        <div class="scroll"><table class="t"><thead><tr><th>Annulus</th><th>MAASP (psi)</th><th>Latest (psi)</th><th>% of MAASP</th><th>Trend</th><th>SCP</th><th>Last read</th><th>State</th><th></th></tr></thead><tbody>
            @foreach($a['annuli'] as $an)
            @php $pc = $an['pct']; $bc = $pc === null ? '#64748b' : ($pc > 100 ? '#f87171' : ($pc >= 80 ? '#fbbf24' : '#34d399')); @endphp
            <tr>
                <td><b>{{ $an['name'] }}</b></td>
                <td>{{ $an['maasp'] ? $nf($an['maasp']) : '—' }}</td>
                <td>{{ $an['last'] ? $nf($an['last']->pressure, 1) : '—' }}</td>
                <td style="min-width:140px">@if($pc !== null)<div class="pbar"><i style="--w:{{ min(100, $pc) }}%;--bc:{{ $bc }}"></i></div><span class="mini">{{ $pc }}%</span>@else<span class="mini">—</span>@endif</td>
                <td>{{ $an['slope'] !== null ? sprintf('%+.1f', $an['slope']) . ' psi/mo' : '—' }}</td>
                <td>@if($an['last'])<span class="chip {{ $an['scp'] ? 'warn' : 'ok' }}">{{ $an['scp'] ? 'Rebuilds' : 'No' }}</span>@else — @endif</td>
                <td @if($an['stale']) style="color:#fbbf24" @endif>{{ $an['last']?->reading_date->format('d M Y') ?? '—' }}</td>
                <td><span class="chip {{ $stateChip[$an['state']] }}">{{ ['ok'=>'Within limits','warn'=>'Watch','bad'=>'Above MAASP','none'=>'No data'][$an['state']] }}</span></td>
                <td>@if($canDelete)<form method="POST" action="{{ route('integrity.annulus.destroy', $an['model']) }}" onsubmit="return confirm('Remove annulus {{ $an['name'] }} and all its readings?')">@csrf @method('DELETE')<button class="x" title="Remove"><i class="fe fe-trash-2"></i></button></form>@endif</td>
            </tr>
            @endforeach
        </tbody></table></div>
        @if($chart->contains(fn ($c) => count($c['points']) > 0))
            <div class="cw sm" style="margin-top:16px"><canvas id="cAnn"></canvas></div>
        @endif
        @endif

        @if($canManage)
        <details class="frm"><summary><i class="fe fe-plus-circle"></i> Add / update annulus &amp; MAASP</summary>
        <form class="in" method="POST" action="{{ route('integrity.annulus.save', $well) }}">@csrf
            <div class="row">
                <div class="f"><label class="lb">Annulus</label><select class="fc" name="name">@foreach(Ann::NAMES as $k => $l)<option value="{{ $k }}">{{ $l }}</option>@endforeach</select></div>
                <div class="f"><label class="lb">MAASP (psi)</label><input class="fc" type="number" step="0.1" min="0" name="maasp"></div>
                <div class="f"><label class="lb">Notes</label><input class="fc" name="notes" maxlength="255"></div>
            </div>
            <div style="margin-top:14px"><button class="btn2 p" type="submit"><i class="fe fe-save"></i> Save annulus</button></div>
        </form></details>
        @if(!empty($a['annuli']))
        <details class="frm"><summary><i class="fe fe-edit"></i> Record annulus reading</summary>
        <form class="in" method="POST" action="{{ route('integrity.readings.store', $well) }}">@csrf
            <div class="row">
                <div class="f"><label class="lb">Annulus <b>*</b></label><select class="fc" name="well_integrity_annulus_id" required>@foreach($a['annuli'] as $an)<option value="{{ $an['model']->id }}">{{ $an['name'] }}</option>@endforeach</select></div>
                <div class="f"><label class="lb">Date <b>*</b></label><input class="fc" type="date" name="reading_date" required max="{{ $today }}" value="{{ $today }}"></div>
                <div class="f"><label class="lb">Pressure (psi) <b>*</b></label><input class="fc" type="number" step="0.1" min="0" name="pressure" required></div>
                <div class="f"><label class="lb">After bleed-down (psi)</label><input class="fc" type="number" step="0.1" min="0" name="bleed_down_pressure"></div>
                <div class="f"><label class="lb">Rebuild pressure (psi)</label><input class="fc" type="number" step="0.1" min="0" name="rebuild_pressure"></div>
                <div class="f"><label class="lb">Rebuild time (h)</label><input class="fc" type="number" step="0.1" min="0" name="rebuild_hours"></div>
                <div class="f"><label class="lb">Fluid bled</label><select class="fc" name="fluid"><option value="">—</option>@foreach(Rd::FLUIDS as $k => $l)<option value="{{ $k }}">{{ $l }}</option>@endforeach</select></div>
                <div class="f"><label class="lb">Remarks</label><input class="fc" name="remarks" maxlength="2000"></div>
            </div>
            <div style="margin-top:14px"><button class="btn2 p" type="submit"><i class="fe fe-save"></i> Save reading</button>
                <span class="mini" style="margin-left:10px">Filling the rebuild pressure above the bleed-down value flags sustained casing pressure.</span></div>
        </form></details>
        @endif
        @endif

        @if($readings->isNotEmpty())
        <details class="frm"><summary><i class="fe fe-list"></i> Reading history ({{ $readings->count() }} latest)</summary>
        <div class="in scroll"><table class="t"><thead><tr><th>Date</th><th>Annulus</th><th>Pressure</th><th>Bleed-down</th><th>Rebuild</th><th>Fluid</th><th>Remarks</th><th></th></tr></thead><tbody>
            @foreach($readings as $r)
            <tr><td>{{ $r->reading_date->format('d M Y') }}</td><td>{{ $r->annulus->name ?? '—' }}</td><td>{{ $nf($r->pressure, 1) }}</td>
                <td>{{ $r->bleed_down_pressure !== null ? $nf($r->bleed_down_pressure, 1) : '—' }}</td>
                <td>{{ $r->rebuild_pressure !== null ? $nf($r->rebuild_pressure, 1) . ($r->rebuild_hours ? ' / ' . $r->rebuild_hours . ' h' : '') : '—' }}</td>
                <td>{{ Rd::FLUIDS[$r->fluid] ?? '—' }}</td><td class="mini">{{ $r->remarks }}</td>
                <td>@if($canDelete)<form method="POST" action="{{ route('integrity.readings.destroy', $r) }}" onsubmit="return confirm('Delete this reading?')">@csrf @method('DELETE')<button class="x"><i class="fe fe-trash-2"></i></button></form>@endif</td></tr>
            @endforeach
        </tbody></table></div></details>
        @endif
    </div>

    {{-- ═════ WELLHEAD CHECKS ═════ --}}
    @php $lc = $a['latest_check']; @endphp
    <div class="panel" style="margin-top:18px" id="wellhead">
        <div class="ph"><h3><i class="fe fe-clipboard"></i> Wellhead checks</h3>
            <span class="sub">operator valve checks · turns · grease · annulus DBB</span>
            @if($canManage)<a class="btn2 p" href="{{ route('integrity.checks.create', $well) }}"><i class="fe fe-plus"></i> New check</a>@endif</div>
        @if($wh['count'] === 0)
            <div class="empty">No wellhead checks yet.@if($canManage) <a class="lnk" href="{{ route('integrity.checks.create', $well) }}">Record the first one</a>@endif</div>
        @else
        <div class="kv">
            <div><small>Last check</small><b>{{ $lc->check_date->format('d M Y') }} <span style="color:var(--mute);font-weight:500">({{ $lc->check_date->diffInDays(now()) }} d ago)</span></b></div>
            <div><small>Well status</small><b>{{ \App\Models\WellheadCheck::STATUSES[$lc->well_status] }}</b></div>
            <div><small>WHCIP</small><b>{{ $lc->whcip !== null ? $nf($lc->whcip, 1) . ' psi' : '—' }}</b></div>
            <div><small>Tree cap</small><b>{{ \App\Models\WellheadCheck::TREE_CAP[$lc->tree_cap] }}</b></div>
            <div><small>Issues in last check</small><b style="color:{{ $a['counts']['check_issues'] ? '#fbbf24' : '#34d399' }}">{{ $a['counts']['check_issues'] ?: 'None' }}</b></div>
            <div><small>Greased valves</small><b>{{ $lc->items->where('greased', true)->pluck('code')->implode(', ') ?: '—' }}</b></div>
        </div>

        @if(count($wh['matrix']['rows']))
        <div class="mini" style="margin:18px 0 8px;text-transform:uppercase;letter-spacing:.1em">Valve condition matrix · last {{ count($wh['matrix']['dates']) }} checks (click a cell to open the report)</div>
        <div class="scroll"><table class="t" style="min-width:420px"><thead><tr><th>Valve</th>@foreach($wh['matrix']['dates'] as $d)<th style="text-align:center">{{ $d }}</th>@endforeach</tr></thead><tbody>
            @foreach($wh['matrix']['rows'] as $row)
            <tr><td><b>{{ $row['code'] }}</b></td>
                @foreach($row['cells'] as $k => $cell)
                @php $cc = ['ok'=>'#34d399','stiff'=>'#fbbf24','leaking'=>'#f87171','seized'=>'#f87171'][$cell] ?? null; @endphp
                <td style="text-align:center"><a href="{{ route('integrity.checks.show', $wh['matrix']['ids'][$k]) }}" title="{{ $cell ? \App\Models\WellheadCheck::CONDITIONS[$cell] : 'not checked' }}"
                    style="display:inline-block;width:22px;height:22px;border-radius:7px;background:{{ $cc ?? 'rgba(255,255,255,.07)' }};{{ $cc ? 'box-shadow:0 0 10px ' . $cc . '66' : '' }}"></a></td>
                @endforeach
            </tr>
            @endforeach
        </tbody></table></div>
        @endif

        <div class="g2c">
            <div><div class="mini" style="margin-bottom:6px">Hand-wheel turns per valve</div><div class="cw sm"><canvas id="cTurns"></canvas></div></div>
            <div><div class="mini" style="margin-bottom:6px">Closing time per valve (sec)</div><div class="cw sm"><canvas id="cTime"></canvas></div></div>
            <div><div class="mini" style="margin-bottom:6px">WHCIP (psi)</div><div class="cw sm"><canvas id="cWhcip"></canvas></div></div>
            <div><div class="mini" style="margin-bottom:6px">Annulus pressures at checks (psi)</div><div class="cw sm"><canvas id="cWhAnn"></canvas></div></div>
        </div>

        <div class="scroll" style="margin-top:16px"><table class="t"><thead><tr><th>Date</th><th>Status</th><th>WHCIP</th><th>Tree cap</th><th>Valves</th><th>By</th><th></th></tr></thead><tbody>
            @foreach($a['checks'] as $c)
            <tr>
                <td>{{ $c->check_date->format('d M Y') }}</td>
                <td>{{ \App\Models\WellheadCheck::STATUSES[$c->well_status] }}</td>
                <td>{{ $c->whcip !== null ? $nf($c->whcip, 1) : '—' }}</td>
                <td>{{ \App\Models\WellheadCheck::TREE_CAP[$c->tree_cap] }}</td>
                <td><span class="chip {{ $c->hasIssues() ? 'warn' : 'ok' }}">{{ $c->hasIssues() ? 'Issues' : 'All ok' }}</span></td>
                <td class="mini">{{ $c->checked_by }}</td>
                <td><a class="lnk" href="{{ route('integrity.checks.show', $c) }}">Report</a></td>
            </tr>
            @endforeach
        </tbody></table></div>
        @endif
    </div>

    {{-- ═════ BARRIERS ═════ --}}
    <div class="g2c" id="barriers">
        @foreach($a['envelopes'] as $key => $env)
        <div class="panel" style="margin-top:0">
            <div class="ph"><h3><i class="fe fe-layers"></i> {{ $env['label'] }}</h3><span class="chip {{ $stateChip[$env['state']] }}">{{ ['ok'=>'Intact','warn'=>'Degraded','bad'=>'Failed','none'=>'No elements'][$env['state']] }}</span></div>
            @if($env['els']->isEmpty())
                <div class="empty">No {{ $key }} barrier elements defined.</div>
            @else
            <div class="env">
                @foreach($env['els'] as $e)
                <div class="el" style="--ec:{{ $elColor[$e->status] }}">
                    <div style="flex:1">
                        <div class="nm">{{ $e->name }} <span class="chip {{ $elChip[$e->status] }}">{{ El::STATUSES[$e->status] }}</span>
                            @if($e->isOverdue())<span class="chip bad">overdue</span>@endif</div>
                        <div class="mt">Last test {{ $e->last_test_date?->format('d M Y') ?? 'never' }} · next due {{ $e->next_due_date?->format('d M Y') ?? '—' }} · every {{ $e->test_interval_days }} d</div>
                        @if($canManage)
                        <details class="frm sm"><summary><i class="fe fe-check-square"></i> Record test</summary>
                        <form class="in" method="POST" action="{{ route('integrity.tests.store', $e) }}">@csrf
                            <div class="row">
                                <div class="f"><label class="lb">Date <b>*</b></label><input class="fc" type="date" name="test_date" required max="{{ $today }}" value="{{ $today }}"></div>
                                <div class="f"><label class="lb">Test type</label><select class="fc" name="test_type">@foreach(Ts::TYPES as $k => $l)<option value="{{ $k }}" @selected($k === 'pressure')>{{ $l }}</option>@endforeach</select></div>
                                <div class="f"><label class="lb">Result <b>*</b></label><select class="fc" name="result" required>@foreach(Ts::RESULTS as $k => $l)<option value="{{ $k }}">{{ $l }}</option>@endforeach</select></div>
                                <div class="f"><label class="lb">Test pressure (psi)</label><input class="fc" type="number" step="0.1" min="0" name="test_pressure"></div>
                                <div class="f"><label class="lb">Leak rate</label><input class="fc" type="number" step="0.01" min="0" name="leak_rate"></div>
                                <div class="f"><label class="lb">Duration (min)</label><input class="fc" type="number" min="0" name="duration_min"></div>
                                <div class="f"><label class="lb">Tested by</label><input class="fc" name="tested_by" maxlength="255"></div>
                                <div class="f"><label class="lb">Remarks</label><input class="fc" name="remarks" maxlength="2000"></div>
                            </div>
                            <div style="margin-top:12px"><button class="btn2 p" type="submit"><i class="fe fe-save"></i> Save test</button></div>
                        </form></details>
                        @endif
                    </div>
                    @if($canDelete)<form method="POST" action="{{ route('integrity.barriers.destroy', $e) }}" onsubmit="return confirm('Remove {{ addslashes($e->name) }} and its test history?')">@csrf @method('DELETE')<button class="x" title="Remove"><i class="fe fe-trash-2"></i></button></form>@endif
                </div>
                @endforeach
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @if($canManage)
    <div class="panel" style="margin-top:14px">
        <details class="frm" style="margin-top:0"><summary><i class="fe fe-plus-circle"></i> Add barrier element</summary>
        <form class="in" method="POST" action="{{ route('integrity.barriers.store', $well) }}">@csrf
            <div class="row">
                <div class="f"><label class="lb">Element type</label><select class="fc" name="element_type">@foreach(El::TYPES as $k => $t)<option value="{{ $k }}">{{ $t[0] }} ({{ $t[1] }})</option>@endforeach</select></div>
                <div class="f"><label class="lb">Custom name</label><input class="fc" name="name" maxlength="255" placeholder="Optional, e.g. Lower master valve"></div>
                <div class="f"><label class="lb">Test interval (days)</label><input class="fc" type="number" min="7" max="1825" name="test_interval_days" placeholder="Default per type"></div>
            </div>
            <div style="margin-top:14px"><button class="btn2 p" type="submit"><i class="fe fe-plus"></i> Add element</button></div>
        </form></details>
        @if($allElements->isNotEmpty())
        <form method="POST" action="{{ route('integrity.barriers.template', $well) }}" style="margin-top:10px">@csrf
            <button class="btn2" type="submit"><i class="fe fe-layers"></i> Add any missing standard elements</button></form>
        @endif
    </div>
    @endif

    {{-- ═════ TEST HISTORY ═════ --}}
    <div class="panel" style="margin-top:18px">
        <div class="ph"><h3><i class="fe fe-check-square"></i> Barrier test history</h3><span class="sub">latest {{ $tests->count() }}</span></div>
        @if($tests->isEmpty())
            <div class="empty">No barrier tests recorded.</div>
        @else
        <div class="scroll"><table class="t"><thead><tr><th>Date</th><th>Element</th><th>Type</th><th>Result</th><th>Pressure</th><th>Leak rate</th><th>Duration</th><th>By</th><th></th></tr></thead><tbody>
            @foreach($tests as $t)
            <tr>
                <td>{{ $t->test_date->format('d M Y') }}</td><td>{{ $t->element->name ?? '—' }}</td><td>{{ Ts::TYPES[$t->test_type] ?? $t->test_type }}</td>
                <td><span class="chip {{ ['pass'=>'ok','marginal'=>'warn','fail'=>'bad'][$t->result] }}">{{ Ts::RESULTS[$t->result] }}</span></td>
                <td>{{ $t->test_pressure !== null ? $nf($t->test_pressure) . ' psi' : '—' }}</td>
                <td>{{ $t->leak_rate !== null ? $t->leak_rate : '—' }}</td>
                <td>{{ $t->duration_min !== null ? $t->duration_min . ' min' : '—' }}</td>
                <td class="mini">{{ $t->tested_by }}</td>
                <td>@if($canDelete)<form method="POST" action="{{ route('integrity.tests.destroy', $t) }}" onsubmit="return confirm('Delete this test record?')">@csrf @method('DELETE')<button class="x"><i class="fe fe-trash-2"></i></button></form>@endif</td>
            </tr>
            @endforeach
        </tbody></table></div>
        @endif
    </div>

    {{-- ═════ INSPECTIONS ═════ --}}
    <div class="panel" style="margin-top:18px" id="inspections">
        <div class="ph"><h3><i class="fe fe-search"></i> Corrosion &amp; inspection</h3><span class="sub">caliper / MTT / USIT / CBL / coupons</span></div>
        @if($a['inspections']->isEmpty())
            <div class="empty">No inspections recorded.</div>
        @else
        <div class="scroll"><table class="t"><thead><tr><th>Date</th><th>Type</th><th>Interval (m)</th><th>Max wall loss</th><th>Status</th><th>Next due</th><th>Finding</th><th></th></tr></thead><tbody>
            @foreach($a['inspections'] as $i)
            <tr>
                <td>{{ $i->inspection_date->format('d M Y') }}</td><td>{{ $i->typeLabel() }}</td>
                <td>{{ $i->depth_from !== null ? $nf($i->depth_from) . ($i->depth_to !== null ? ' - ' . $nf($i->depth_to) : '') : '—' }}</td>
                <td>@if($i->max_wall_loss_pct !== null)<span style="color:{{ $i->max_wall_loss_pct >= 50 ? '#f87171' : ($i->max_wall_loss_pct >= 30 ? '#fbbf24' : '#e2e8f0') }}">{{ $i->max_wall_loss_pct }}%</span>@else — @endif</td>
                <td><span class="chip {{ ['acceptable'=>'ok','monitor'=>'warn','action_required'=>'bad'][$i->status] }}">{{ Ins::STATUSES[$i->status] }}</span></td>
                <td @if($i->next_due_date && $i->next_due_date->isPast()) style="color:#fca5a5" @endif>{{ $i->next_due_date?->format('d M Y') ?? '—' }}</td>
                <td class="mini">{{ \Illuminate\Support\Str::limit($i->finding, 90) }}</td>
                <td>@if($canDelete)<form method="POST" action="{{ route('integrity.inspections.destroy', $i) }}" onsubmit="return confirm('Delete this inspection?')">@csrf @method('DELETE')<button class="x"><i class="fe fe-trash-2"></i></button></form>@endif</td>
            </tr>
            @endforeach
        </tbody></table></div>
        @endif
        @if($canManage)
        <details class="frm"><summary><i class="fe fe-plus-circle"></i> Record inspection</summary>
        <form class="in" method="POST" action="{{ route('integrity.inspections.store', $well) }}">@csrf
            <div class="row">
                <div class="f"><label class="lb">Type <b>*</b></label><select class="fc" name="inspection_type" required>@foreach(Ins::TYPES as $k => $l)<option value="{{ $k }}">{{ $l }}</option>@endforeach</select></div>
                <div class="f"><label class="lb">Date <b>*</b></label><input class="fc" type="date" name="inspection_date" required max="{{ $today }}" value="{{ $today }}"></div>
                <div class="f"><label class="lb">Depth from (m)</label><input class="fc" type="number" step="0.1" min="0" name="depth_from"></div>
                <div class="f"><label class="lb">Depth to (m)</label><input class="fc" type="number" step="0.1" min="0" name="depth_to"></div>
                <div class="f"><label class="lb">Max wall loss (%)</label><input class="fc" type="number" step="0.1" min="0" max="100" name="max_wall_loss_pct"></div>
                <div class="f"><label class="lb">Status <b>*</b></label><select class="fc" name="status" required>@foreach(Ins::STATUSES as $k => $l)<option value="{{ $k }}">{{ $l }}</option>@endforeach</select></div>
                <div class="f"><label class="lb">Next due</label><input class="fc" type="date" name="next_due_date"></div>
            </div>
            <div class="f" style="margin-top:14px"><label class="lb">Finding</label><textarea class="fc" name="finding" rows="2" maxlength="3000"></textarea></div>
            <div style="margin-top:14px"><button class="btn2 p" type="submit"><i class="fe fe-save"></i> Save inspection</button></div>
        </form></details>
        @endif
    </div>

    {{-- ═════ ANOMALIES ═════ --}}
    <div class="panel" style="margin-top:18px" id="anomalies">
        <div class="ph"><h3><i class="fe fe-alert-octagon"></i> Anomalies &amp; risk register</h3><span class="sub">risk = likelihood × consequence (5×5)</span></div>
        @if($a['all_anomalies']->isEmpty())
            <div class="empty">No anomalies raised for this well.</div>
        @else
        <div class="scroll"><table class="t"><thead><tr><th>Risk</th><th>Anomaly</th><th>Category</th><th>L×C</th><th>Identified</th><th>Due</th><th>Status</th><th></th></tr></thead><tbody>
            @foreach($a['all_anomalies'] as $an)
            @php $lv = $an->level(); @endphp
            <tr>
                <td><span class="rk" style="--rk:{{ $lv[2] }}">{{ $an->risk_score }}</span> <span class="mini">{{ $lv[0] }}</span></td>
                <td>{{ $an->title }}@if($an->description)<div class="mini">{{ \Illuminate\Support\Str::limit($an->description, 110) }}</div>@endif
                    @if($an->mitigation)<div class="mini" style="color:#86efac">Mitigation: {{ \Illuminate\Support\Str::limit($an->mitigation, 110) }}</div>@endif</td>
                <td class="mini">{{ An::CATEGORIES[$an->category] ?? $an->category }}</td>
                <td class="mini">{{ $an->likelihood }}×{{ $an->consequence }}</td>
                <td>{{ $an->identified_date->format('d M Y') }}</td>
                <td @if($an->isOverdue()) style="color:#fca5a5" @endif>{{ $an->due_date?->format('d M Y') ?? '—' }}</td>
                <td><span class="chip {{ ['open'=>'bad','mitigated'=>'warn','closed'=>'ok'][$an->status] }}">{{ An::STATUSES[$an->status] }}</span></td>
                <td style="white-space:nowrap">
                    @if($canManage && $an->status !== 'closed')
                    <details class="frm sm" style="min-width:230px"><summary><i class="fe fe-edit-2"></i> Update</summary>
                    <form class="in" method="POST" action="{{ route('integrity.anomalies.update', $an) }}">@csrf @method('PUT')
                        <div class="f"><label class="lb">Status</label><select class="fc" name="status">@foreach(An::STATUSES as $k => $l)<option value="{{ $k }}" @selected($an->status === $k)>{{ $l }}</option>@endforeach</select></div>
                        <div class="f" style="margin-top:10px"><label class="lb">Mitigation / closure note</label><textarea class="fc" name="mitigation" rows="2" maxlength="3000">{{ $an->mitigation }}</textarea></div>
                        <div style="margin-top:10px"><button class="btn2 p" type="submit"><i class="fe fe-save"></i> Save</button></div>
                    </form></details>
                    @endif
                    @if($canDelete)<form method="POST" action="{{ route('integrity.anomalies.destroy', $an) }}" style="display:inline" onsubmit="return confirm('Delete this anomaly?')">@csrf @method('DELETE')<button class="x"><i class="fe fe-trash-2"></i></button></form>@endif
                </td>
            </tr>
            @endforeach
        </tbody></table></div>
        @endif
        @if($canManage)
        <details class="frm"><summary><i class="fe fe-plus-circle"></i> Raise anomaly</summary>
        <form class="in" method="POST" action="{{ route('integrity.anomalies.store', $well) }}">@csrf
            <div class="row">
                <div class="f"><label class="lb">Title <b>*</b></label><input class="fc" name="title" required maxlength="255"></div>
                <div class="f"><label class="lb">Category <b>*</b></label><select class="fc" name="category" required>@foreach(An::CATEGORIES as $k => $l)<option value="{{ $k }}">{{ $l }}</option>@endforeach</select></div>
                <div class="f"><label class="lb">Likelihood (1-5) <b>*</b></label><select class="fc" name="likelihood" required>@for($i = 1; $i <= 5; $i++)<option value="{{ $i }}" @selected($i === 3)>{{ $i }}</option>@endfor</select></div>
                <div class="f"><label class="lb">Consequence (1-5) <b>*</b></label><select class="fc" name="consequence" required>@for($i = 1; $i <= 5; $i++)<option value="{{ $i }}" @selected($i === 3)>{{ $i }}</option>@endfor</select></div>
                <div class="f"><label class="lb">Identified <b>*</b></label><input class="fc" type="date" name="identified_date" required max="{{ $today }}" value="{{ $today }}"></div>
                <div class="f"><label class="lb">Due date</label><input class="fc" type="date" name="due_date"></div>
            </div>
            <div class="row" style="margin-top:14px">
                <div class="f"><label class="lb">Description</label><textarea class="fc" name="description" rows="2" maxlength="3000"></textarea></div>
                <div class="f"><label class="lb">Mitigation</label><textarea class="fc" name="mitigation" rows="2" maxlength="3000"></textarea></div>
            </div>
            <div style="margin-top:14px"><button class="btn2 p" type="submit"><i class="fe fe-flag"></i> Raise anomaly</button></div>
        </form></details>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    if (!window.Chart) return;
    Chart.defaults.color = '#9fb0cf';
    Chart.defaults.borderColor = 'rgba(96,165,250,.12)';
    var cols = ['#22d3ee', '#a78bfa', '#fbbf24', '#fb923c'];

    // wellhead check charts
    var WH = @json($wh);
    function whChart(id, sets, unit) {
        var c = document.getElementById(id);
        if (!c || !sets.length) { if (c) c.parentNode.innerHTML = '<div class="empty">No data recorded.</div>'; return; }
        new Chart(c, { type: 'line',
            data: { labels: WH.labels, datasets: sets.map(function (s, i) {
                return { label: s.code, data: s.data, borderColor: cols[i % 4], backgroundColor: cols[i % 4], tension: .25, spanGaps: true, pointRadius: 3 };
            }) },
            options: { responsive: true, maintainAspectRatio: false, scales: { y: { title: { display: true, text: unit } } }, plugins: { legend: { labels: { boxWidth: 10, font: { size: 11 } } } } } });
    }
    whChart('cTurns', WH.turns, 'turns');
    whChart('cTime', WH.time, 'sec');
    whChart('cWhAnn', WH.annulus.map(function (s) { s.code = 'Annulus ' + s.code; return s; }), 'psi');
    whChart('cWhcip', [{ code: 'WHCIP', data: WH.whcip }].filter(function (s) { return s.data.some(function (v) { return v !== null; }); }), 'psi');

    var el = document.getElementById('cAnn');
    if (!el) return;
    var data = @json($chart);
    var ds = [];
    data.forEach(function (a, i) {
        if (!a.points.length) return;
        ds.push({ label: 'Annulus ' + a.name, data: a.points, borderColor: cols[i % 4], backgroundColor: cols[i % 4], tension: .25, pointRadius: 3 });
        if (a.maasp) {
            ds.push({ label: a.name + ' MAASP', data: a.points.map(function (p) { return { x: p.x, y: a.maasp }; }), borderColor: cols[i % 4], borderDash: [6, 5], pointRadius: 0, borderWidth: 1.5 });
        }
    });
    new Chart(el, {
        type: 'line', data: { datasets: ds },
        options: { responsive: true, maintainAspectRatio: false, parsing: { xAxisKey: 'x', yAxisKey: 'y' },
            scales: { x: { type: 'category', labels: Array.from(new Set(data.flatMap(function (a) { return a.points.map(function (p) { return p.x; }); }))).sort() },
                      y: { beginAtZero: true, title: { display: true, text: 'psi' } } },
            plugins: { legend: { labels: { boxWidth: 10, font: { size: 11 } } } } }
    });
})();
</script>
@endsection
