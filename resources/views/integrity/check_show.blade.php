@extends('layouts.master')

@section('title', 'Wellhead Check - ' . $well->name)

@section('css')
@include('integrity._style')
<style>
.ofp .wa { display:grid; grid-template-columns:minmax(300px,380px) 1fr; gap:18px; align-items:start; margin-top:18px; }
.ofp .wa-card { background:#134e3a; border:1px solid rgba(255,255,255,.12); border-radius:18px; padding:22px 24px; color:#f3f8f5; font:400 15.5px/1.55 'Inter',sans-serif; white-space:pre-wrap; box-shadow:0 18px 40px rgba(0,0,0,.35); }
.ofp .cmp { display:grid; gap:9px; }
.ofp .cmp div { display:grid; grid-template-columns:130px 1fr 1fr 90px; gap:10px; align-items:center; font-size:13px; color:var(--sub); padding:9px 12px; border-radius:12px; background:var(--glass); border:1px solid var(--line); }
.ofp .cmp b { color:#fff; font-family:'Space Grotesk'; }
.ofp .cmp .hd { background:none; border:0; font-size:10.5px; letter-spacing:.1em; text-transform:uppercase; color:var(--mute); padding:0 12px; }
@media (max-width:1000px) { .ofp .wa { grid-template-columns:1fr; } }
@media print { .ofp .hero .actions, .ofp .bar, header, aside, .main-sidebar { display:none !important; } .ofp .wa { grid-template-columns:1fr; } }
</style>
@endsection

@section('content')
@php
    use App\Models\WellheadCheck as Wc;
    $chip = ['ok' => 'ok', 'stiff' => 'warn', 'leaking' => 'bad', 'seized' => 'bad'];
    $pv = fn ($code) => $prev?->items->firstWhere('code', $code);
    $delta = function ($now, $before, $unit = '') {
        if ($now === null || $before === null) return '—';
        $d = round($now - $before, 1);
        return ($d > 0 ? '+' : '') . $d . $unit;
    };
@endphp
<div class="ofp" id="ofp">
    <div class="bgl"><div class="orb o1"></div><div class="orb o2"></div></div>

    <div class="hero">
        <div class="who">
            <span class="lg"></span>
            <div>
                <div class="eyebrow"><i></i> Wellhead check report</div>
                <h1>{{ $well->name }} <em>· {{ $check->check_date->format('d M Y') }}</em></h1>
                <div class="meta"><a href="{{ route('integrity.show', $well) }}#wellhead">&larr; Well integrity file</a>
                    <span>Checked by {{ $check->checked_by ?: '—' }}</span>
                    <span class="chip {{ $check->hasIssues() ? 'warn' : 'ok' }}">{{ $check->hasIssues() ? 'Issues found' : 'All ok' }}</span></div>
            </div>
        </div>
        <div class="actions">
            <button class="btn2 p" id="copyBtn" type="button"><i class="fe fe-copy"></i> Copy report</button>
            <button class="btn2" type="button" onclick="window.print()"><i class="fe fe-printer"></i> Print</button>
            @if($canDelete)
            <form method="POST" action="{{ route('integrity.checks.destroy', $check) }}" style="display:inline" onsubmit="return confirm('Delete this check?')">@csrf @method('DELETE')<button class="btn2"><i class="fe fe-trash-2"></i> Delete</button></form>
            @endif
        </div>
    </div>

    @if(session('success'))<div class="msg ok flash"><i class="fe fe-check-circle"></i><span>{{ session('success') }}</span></div>@endif

    <div class="wa">
        <div class="wa-card" id="reportText">{{ $check->reportText() }}</div>

        <div>
            <div class="panel" style="margin-top:0">
                <div class="ph"><h3><i class="fe fe-git-pull-request"></i> Compared with previous check</h3>
                    <span class="sub">{{ $prev ? $prev->check_date->format('d M Y') : 'no earlier check' }}</span></div>
                <div class="kv" style="margin-bottom:14px">
                    <div><small>Status</small><b>{{ Wc::STATUSES[$check->well_status] }}</b></div>
                    <div><small>WHCIP</small><b>{{ $check->whcip !== null ? $check->whcip . ' psi' : '—' }} <span style="color:var(--mute);font-weight:500">{{ $prev ? '(' . $delta($check->whcip, $prev->whcip) . ')' : '' }}</span></b></div>
                    <div><small>Tree cap</small><b>{{ Wc::TREE_CAP[$check->tree_cap] }}</b></div>
                    <div><small>OEM / WHT</small><b>{{ $check->oem ?: '—' }} / {{ $check->wht ?: '—' }}</b></div>
                </div>
                <div class="cmp">
                    <div class="hd"><span>Item</span><span>Now</span><span>Previous</span><span>Δ</span></div>
                    @foreach($check->items as $i)
                    @php $p = $pv($i->code); @endphp
                    <div>
                        <span><b>{{ $i->item_type === 'annulus' ? 'Annulus ' . $i->code : $i->code }}</b></span>
                        <span>
                            @if($i->condition)<span class="chip {{ $chip[$i->condition] }}">{{ Wc::CONDITIONS[$i->condition] }}</span>@endif
                            @if($i->item_type === 'annulus' && $i->pressure !== null) {{ $i->pressure }} psi @endif
                            @if($i->turns !== null) · {{ $i->turns }} turns @endif
                            @if($i->time_sec !== null) · {{ $i->time_sec }} s @endif
                            @if($i->greased) · greased @endif
                        </span>
                        <span class="mini">
                            @if($p)
                                {{ $p->condition ? Wc::CONDITIONS[$p->condition] : '' }}
                                @if($p->item_type === 'annulus' && $p->pressure !== null) {{ $p->pressure }} psi @endif
                                @if($p->turns !== null) · {{ $p->turns }} turns @endif
                                @if($p->time_sec !== null) · {{ $p->time_sec }} s @endif
                            @else — @endif
                        </span>
                        <span>
                            @if($p && $i->item_type === 'annulus') {{ $delta($i->pressure, $p->pressure, ' psi') }}
                            @elseif($p) {{ $i->turns !== null && $p->turns !== null ? $delta($i->turns, $p->turns, ' t') : $delta($i->time_sec, $p->time_sec, ' s') }}
                            @else — @endif
                        </span>
                    </div>
                    @endforeach
                </div>
                @if($check->remarks)<div class="msg" style="margin-top:14px"><i class="fe fe-message-square"></i><span>{{ $check->remarks }}</span></div>@endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('copyBtn').addEventListener('click', function () {
    var t = document.getElementById('reportText').innerText, b = this;
    var done = function () { b.innerHTML = '<i class="fe fe-check"></i> Copied'; setTimeout(function () { b.innerHTML = '<i class="fe fe-copy"></i> Copy report'; }, 1800); };
    if (navigator.clipboard) { navigator.clipboard.writeText(t).then(done); }
    else { var ta = document.createElement('textarea'); ta.value = t; document.body.appendChild(ta); ta.select(); document.execCommand('copy'); ta.remove(); done(); }
});
</script>
@endsection
