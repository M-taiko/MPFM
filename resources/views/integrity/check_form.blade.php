@extends('layouts.master')

@section('title', 'New Wellhead Check - ' . $well->name)

@section('css')
@include('integrity._style')
<style>
.ofp .vt { width:100%; border-collapse:separate; border-spacing:0 8px; }
.ofp .vt th { font-size:10.5px; letter-spacing:.1em; text-transform:uppercase; color:var(--mute); text-align:left; padding:0 8px; font-weight:700; }
.ofp .vt td { padding:0 6px; background:rgba(255,255,255,.03); }
.ofp .vt td:first-child { border-radius:12px 0 0 12px; padding-left:14px; } .ofp .vt td:last-child { border-radius:0 12px 12px 0; }
.ofp .vt .fc { height:40px; border-radius:10px; font-size:13px; min-width:84px; }
.ofp .vt .cd { font:700 14px 'Space Grotesk'; color:#fff; white-space:nowrap; } .ofp .vt .cd small { display:block; font:500 11px 'Inter'; color:var(--mute); }
.ofp .gr { display:flex; align-items:center; gap:6px; font-size:12px; color:var(--sub); white-space:nowrap; cursor:pointer; }
.ofp .gr input { width:17px; height:17px; accent-color:#22d3ee; }
.ofp .scroll { overflow-x:auto; }
</style>
@endsection

@section('content')
@php
    use App\Models\WellheadCheck as Wc;
    $v = fn ($k, $d = '') => old($k, $d);
    $lastItem = fn ($code) => $last?->items->firstWhere('code', $code);
@endphp
<div class="ofp" id="ofp">
    <div class="bgl"><div class="orb o1"></div><div class="orb o2"></div></div>

    <div class="hero">
        <div class="who">
            <span class="lg"></span>
            <div>
                <div class="eyebrow"><i></i> Operator wellhead check</div>
                <h1>New <em>wellhead check</em></h1>
                <div class="meta"><span>{{ $well->name }} · {{ $well->tower?->oilField?->name }}</span>@if($last)<span>Previous check {{ $last->check_date->format('d M Y') }} — its values are shown as hints</span>@endif</div>
            </div>
        </div>
        <div class="actions"><a class="btn2" href="{{ route('integrity.show', $well) }}"><i class="fe fe-arrow-left"></i> Back</a></div>
    </div>

    @if($errors->any())<div class="err"><b><i class="fe fe-alert-circle"></i> Please fix the following:</b><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

    <form method="POST" action="{{ route('integrity.checks.store', $well) }}" style="display:block">@csrf

        <div class="panel" style="margin-top:18px">
            <div class="ph num"><span class="n">1</span><div><h3 style="font-size:16px;font-weight:800;margin:0">Well &amp; wellhead</h3><small>Header of the report</small></div></div>
            <div class="row">
                <div class="f"><label class="lb"><i class="fe fe-calendar"></i> Date <b>*</b></label><input class="fc" type="date" name="check_date" required max="{{ now()->format('Y-m-d') }}" value="{{ $v('check_date', now()->format('Y-m-d')) }}"></div>
                <div class="f"><label class="lb"><i class="fe fe-tag"></i> WHT</label><input class="fc" name="wht" maxlength="60" placeholder="e.g. US 255" value="{{ $v('wht', $last->wht ?? '') }}"></div>
                <div class="f"><label class="lb"><i class="fe fe-droplet"></i> Well no.</label><input class="fc" name="well_no" maxlength="80" value="{{ $v('well_no', $last->well_no ?? $well->name) }}"></div>
                <div class="f"><label class="lb"><i class="fe fe-settings"></i> OEM</label><input class="fc" name="oem" maxlength="60" placeholder="e.g. FMC" value="{{ $v('oem', $last->oem ?? '') }}"></div>
                <div class="f"><label class="lb"><i class="fe fe-hash"></i> String <b>*</b></label><input class="fc" name="string_no" required maxlength="10" value="{{ $v('string_no', $last->string_no ?? '1') }}"></div>
                <div class="f"><label class="lb"><i class="fe fe-power"></i> Status <b>*</b></label>
                    <select class="fc" name="well_status">@foreach(Wc::STATUSES as $k => $l)<option value="{{ $k }}" @selected($v('well_status', $last->well_status ?? 'online') === $k)>{{ $l }}</option>@endforeach</select></div>
                <div class="f"><label class="lb"><i class="fe fe-gauge"></i> WHCIP (psi)</label><input class="fc" type="number" step="0.1" min="0" name="whcip" value="{{ $v('whcip') }}" @if($last?->whcip !== null) placeholder="prev {{ $last->whcip }}" @endif></div>
                <div class="f"><label class="lb"><i class="fe fe-check-circle"></i> Tree cap</label>
                    <select class="fc" name="tree_cap">@foreach(Wc::TREE_CAP as $k => $l)<option value="{{ $k }}" @selected($v('tree_cap', 'ok') === $k)>{{ $l }}</option>@endforeach</select></div>
            </div>
        </div>

        <div class="panel">
            <div class="ph num"><span class="n">2</span><div><h3 style="font-size:16px;font-weight:800;margin:0">Valves operated</h3><small>Condition, hand-wheel turns, turns/time and grease — leave a row empty if not operated</small></div></div>
            <div class="scroll"><table class="vt"><thead><tr><th>Valve</th><th>Condition</th><th>Turns</th><th>Time (sec)</th><th></th><th>Note</th></tr></thead><tbody>
                @php $n = 0; @endphp
                @foreach(Wc::VALVES as $code => [$label])
                @php $p = $lastItem($code); @endphp
                <tr>
                    <td class="cd">{{ $code }}<small>{{ $label }}</small>
                        <input type="hidden" name="items[{{ $n }}][code]" value="{{ $code }}"><input type="hidden" name="items[{{ $n }}][item_type]" value="valve"></td>
                    <td><select class="fc" name="items[{{ $n }}][condition]"><option value="">—</option>@foreach(Wc::CONDITIONS as $k => $l)<option value="{{ $k }}" @selected(old("items.$n.condition", 'ok') === $k)>{{ $l }}</option>@endforeach</select></td>
                    <td><input class="fc" type="number" step="0.1" min="0" name="items[{{ $n }}][turns]" value="{{ old("items.$n.turns") }}" placeholder="{{ $p?->turns !== null ? 'prev ' . $p->turns : '' }}"></td>
                    <td><input class="fc" type="number" min="0" name="items[{{ $n }}][time_sec]" value="{{ old("items.$n.time_sec") }}" placeholder="{{ $p?->time_sec !== null ? 'prev ' . $p->time_sec : '' }}"></td>
                    <td><label class="gr"><input type="checkbox" name="items[{{ $n }}][greased]" value="1" @checked(old("items.$n.greased"))> Greased</label></td>
                    <td><input class="fc" name="items[{{ $n }}][notes]" maxlength="255" value="{{ old("items.$n.notes") }}" style="min-width:160px"></td>
                </tr>
                @php $n++; @endphp
                @endforeach
            </tbody></table></div>
        </div>

        <div class="panel">
            <div class="ph num"><span class="n">3</span><div><h3 style="font-size:16px;font-weight:800;margin:0">Annuli</h3><small>Pressure read, then the annulus valve operated with DBB (double block &amp; bleed) side</small></div></div>
            <div class="scroll"><table class="vt"><thead><tr><th>Annulus</th><th>Pressure (psi)</th><th>Valve</th><th>Turns</th><th>DBB at</th><th>Valve condition</th><th></th></tr></thead><tbody>
                @foreach(Wc::ANNULI as $code => [$ref, $turns, $side])
                @php $p = $lastItem($code); @endphp
                <tr>
                    <td class="cd">Annulus {{ $code }}
                        <input type="hidden" name="items[{{ $n }}][code]" value="{{ $code }}"><input type="hidden" name="items[{{ $n }}][item_type]" value="annulus"></td>
                    <td><input class="fc" type="number" step="0.1" min="0" name="items[{{ $n }}][pressure]" value="{{ old("items.$n.pressure") }}" placeholder="{{ $p?->pressure !== null ? 'prev ' . $p->pressure : '' }}"></td>
                    <td><input class="fc" name="items[{{ $n }}][valve_ref]" maxlength="20" value="{{ old("items.$n.valve_ref", $p->valve_ref ?? $ref) }}" style="min-width:80px"></td>
                    <td><input class="fc" type="number" step="0.1" min="0" name="items[{{ $n }}][turns]" value="{{ old("items.$n.turns", $p->turns ?? $turns) }}"></td>
                    <td><select class="fc" name="items[{{ $n }}][dbb_side]"><option value="">—</option>@foreach(['RHS','LHS'] as $s)<option value="{{ $s }}" @selected(old("items.$n.dbb_side", $p->dbb_side ?? $side) === $s)>{{ $s }}</option>@endforeach</select></td>
                    <td><select class="fc" name="items[{{ $n }}][condition]"><option value="">—</option>@foreach(Wc::CONDITIONS as $k => $l)<option value="{{ $k }}" @selected(old("items.$n.condition") === $k)>{{ $l }}</option>@endforeach</select></td>
                    <td><label class="gr"><input type="checkbox" name="items[{{ $n }}][greased]" value="1" @checked(old("items.$n.greased"))> Greased</label></td>
                </tr>
                @php $n++; @endphp
                @endforeach
            </tbody></table></div>
        </div>

        <div class="panel">
            <div class="ph num"><span class="n">4</span><div><h3 style="font-size:16px;font-weight:800;margin:0">Remarks</h3><small>Who checked, and anything worth noting</small></div></div>
            <div class="row">
                <div class="f"><label class="lb"><i class="fe fe-edit-3"></i> Remarks</label><textarea class="fc" name="remarks" rows="3" maxlength="3000" placeholder="e.g. Suspended well">{{ $v('remarks') }}</textarea></div>
                <div class="f"><label class="lb"><i class="fe fe-user"></i> Checked by</label><input class="fc" name="checked_by" maxlength="255" value="{{ $v('checked_by', auth()->user()->name) }}"></div>
            </div>
        </div>

        <div class="bar">
            <button class="btn2 p" type="submit"><i class="fe fe-save"></i> Save check</button>
            <a class="btn2" href="{{ route('integrity.show', $well) }}"><i class="fe fe-x"></i> Cancel</a>
            <span class="sp"></span><span class="hint">Saving updates the barrier elements, annulus readings and the well's integrity status.</span>
        </div>
    </form>
</div>
@endsection
