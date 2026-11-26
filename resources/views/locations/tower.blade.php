@extends('layouts.master')

@section('title', 'Tower - ' . $tower->name)

@section('css')
@include('locations._style')
@endsection

@section('content')
@php
    use App\Models\Location as L;
    use App\Models\Tower as T;
    $stChip = ['active' => 'ok', 'maintenance' => 'warn', 'inactive' => 'none'];
    $active = $wells->where('status', 'active')->count();
    $params = [$location, $field, $tower];
@endphp
<div class="ofp" id="ofp">
    <div class="bgl"><div class="orb o1"></div><div class="orb o2"></div></div>

    <div class="hero">
        <div class="who">
            <span class="lg"></span>
            <div>
                <div class="eyebrow"><i></i> {{ $tower->typeLabel() }}</div>
                <h1>{{ $tower->name }} <em>· {{ $tower->code }}</em></h1>
                <div class="crumbs"><a href="{{ route('locations.index') }}">Locations</a><i class="fe fe-chevron-right sep"></i>
                    <a href="{{ route('locations.show', $location) }}" style="color:{{ $location->envColor() }}">{{ $location->envLabel() }} · {{ $location->name }}</a><i class="fe fe-chevron-right sep"></i>
                    <a href="{{ route('locations.field', [$location, $field]) }}">{{ $field->name }}</a><i class="fe fe-chevron-right sep"></i><span>{{ $tower->name }}</span>
                    <span class="chip {{ $stChip[$tower->status] ?? 'none' }}" style="margin-left:6px">{{ ucfirst($tower->status) }}</span></div>
            </div>
        </div>
        <div class="actions"><a class="btn2" href="{{ route('locations.field', [$location, $field]) }}"><i class="fe fe-arrow-left"></i> {{ $field->name }}</a></div>
    </div>

    @if(session('success'))<div class="msg ok flash"><i class="fe fe-check-circle"></i><span>{{ session('success') }}</span></div>@endif
    @if($errors->any())<div class="err"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

    <div class="kpis">
        <div class="kpi" style="--c:#34d399"><div class="ic"><i class="fe fe-droplet"></i></div><div class="l">Wells</div><div class="v">{{ $wells->count() }}</div><div class="u">on this tower</div></div>
        <div class="kpi" style="--c:#22d3ee"><div class="ic"><i class="fe fe-play-circle"></i></div><div class="l">Active</div><div class="v">{{ $active }}</div><div class="u">producing or testable</div></div>
        <div class="kpi" style="--c:#a78bfa"><div class="ic"><i class="fe {{ $tower->typeIcon() }}"></i></div><div class="l">Tower type</div><div class="v" style="font-size:18px">{{ $tower->typeLabel() }}</div><div class="u">{{ $field->name }}</div></div>
        <div class="kpi" style="--c:#fbbf24"><div class="ic"><i class="fe fe-activity"></i></div><div class="l">MPFM tests</div><div class="v">{{ $wells->sum('mpfm_tests_count') }}</div><div class="u">on these wells</div></div>
    </div>

    {{-- ═════ WELLS ═════ --}}
    <div class="panel" style="margin-top:18px">
        <div class="ph"><h3><i class="fe fe-droplet"></i> Wells on {{ $tower->name }}</h3><span class="sub">each well is a string: String 1, String 2, String 3...</span></div>
        @if($wells->isEmpty())
            <div class="empty">No wells yet. Add the first one below.</div>
        @else
        <div class="scroll"><table class="t"><thead><tr><th>String</th><th>Well</th><th>Code</th><th>Status</th><th>Tests</th><th>Last test</th><th></th></tr></thead><tbody>
            @foreach($wells as $w)
            <tr>
                <td><span class="sn {{ $w->string_number ? '' : 'none' }}">{{ $w->stringLabel() }}</span></td>
                <td><a class="lnk" href="{{ route('oil_strings.show', $w) }}">{{ $w->name }}</a></td>
                <td>{{ $w->code }}</td>
                <td><span class="chip {{ $stChip[$w->status] ?? 'none' }}">{{ ucfirst($w->status) }}</span></td>
                <td>{{ $w->mpfm_tests_count }}</td>
                <td>{{ $w->last_test_at ? \Carbon\Carbon::parse($w->last_test_at)->format('d M Y') : '—' }}</td>
                <td style="white-space:nowrap">
                    <a class="ib" href="{{ route('mpfm_tests.string_analysis', $w) }}" title="Well analysis"><i class="fe fe-activity"></i></a>
                    @if($canManage)
                    <details class="frm sm" style="display:inline-block;min-width:0;margin:0 0 0 4px"><summary style="padding:5px 10px"><i class="fe fe-edit-2"></i></summary>
                    <form class="in" method="POST" action="{{ route('locations.well.update', [$location, $field, $tower, $w]) }}" style="min-width:280px">@csrf @method('PUT')
                        <div class="f"><label class="lb">Name</label><input class="fc" name="name" required maxlength="255" value="{{ $w->name }}"></div>
                        <div class="f" style="margin-top:8px"><label class="lb">Code</label><input class="fc" name="code" required maxlength="255" value="{{ $w->code }}"></div>
                        <div class="row" style="margin-top:8px;grid-template-columns:1fr 1fr">
                            <div class="f"><label class="lb">String no.</label><input class="fc" type="number" min="1" max="99" name="string_number" value="{{ $w->string_number }}"></div>
                            <div class="f"><label class="lb">Status</label><select class="fc" name="status">@foreach(L::STATUSES as $k => $l)<option value="{{ $k }}" @selected($w->status === $k)>{{ $l }}</option>@endforeach</select></div>
                        </div>
                        <input type="hidden" name="description" value="{{ $w->description }}">
                        <div style="margin-top:10px"><button class="btn2 p" type="submit"><i class="fe fe-save"></i> Save</button></div>
                    </form></details>
                    <form method="POST" action="{{ route('locations.well.destroy', [$location, $field, $tower, $w]) }}" style="display:inline" onsubmit="return confirm('Delete {{ addslashes($w->name) }}?')">@csrf @method('DELETE')<button class="xb" title="Delete"><i class="fe fe-trash-2"></i></button></form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody></table></div>
        @endif

        @if($canManage)
        <details class="frm" @if($errors->has('code') && old('string_number') !== null) open @endif><summary><i class="fe fe-plus-circle"></i> Add a well</summary>
        <form class="in" method="POST" action="{{ route('locations.well.store', $params) }}">@csrf
            <div class="row">
                <div class="f"><label class="lb" for="wn">Well name <b>*</b></label><input class="fc" id="wn" name="name" required maxlength="255" placeholder="e.g. ZK-101/1" value="{{ old('name') }}"></div>
                <div class="f"><label class="lb" for="wc">Code <b>*</b></label><input class="fc" id="wc" name="code" required maxlength="255" placeholder="e.g. ZK101-S1" value="{{ old('code') }}"></div>
                <div class="f"><label class="lb" for="wsn">String number</label><input class="fc" id="wsn" type="number" min="1" max="99" name="string_number" value="{{ old('string_number', $nextString) }}"></div>
                <div class="f"><label class="lb" for="ws">Status</label><select class="fc" id="ws" name="status">@foreach(L::STATUSES as $k => $l)<option value="{{ $k }}">{{ $l }}</option>@endforeach</select></div>
            </div>
            <div class="f" style="margin-top:12px"><label class="lb" for="wd">Description</label><textarea class="fc" id="wd" name="description" rows="2" maxlength="3000">{{ old('description') }}</textarea></div>
            <div style="margin-top:12px"><button class="btn2 p" type="submit"><i class="fe fe-save"></i> Create well</button></div>
        </form></details>
        @endif
    </div>

    {{-- ═════ EDIT / DELETE TOWER ═════ --}}
    @if($canManage)
    <div class="panel" style="margin-top:18px">
        <div class="ph"><h3><i class="fe fe-edit-2"></i> Edit this tower</h3></div>
        <details class="frm" style="margin-top:0"><summary><i class="fe fe-edit-3"></i> Change type, name, code or status</summary>
        <form class="in" method="POST" action="{{ route('locations.tower.update', $params) }}">@csrf @method('PUT')
            <div class="f"><label class="lb">Tower type</label>
                <div class="ptl">@foreach(T::TYPES as $k => [$tl, $ti])<input type="radio" name="tower_type" id="et_{{ $k }}" value="{{ $k }}" @checked($tower->tower_type === $k)><label for="et_{{ $k }}" style="--tc:#a78bfa"><i class="fe {{ $ti }}" style="color:#a78bfa"></i>{{ $tl }}</label>@endforeach</div></div>
            <div class="row" style="margin-top:14px">
                <div class="f"><label class="lb" for="etn">Name <b>*</b></label><input class="fc" id="etn" name="name" required maxlength="255" value="{{ $tower->name }}"></div>
                <div class="f"><label class="lb" for="etc">Code <b>*</b></label><input class="fc" id="etc" name="code" required maxlength="255" value="{{ $tower->code }}"></div>
                <div class="f"><label class="lb" for="ets">Status</label><select class="fc" id="ets" name="status">@foreach(L::STATUSES as $k => $l)<option value="{{ $k }}" @selected($tower->status === $k)>{{ $l }}</option>@endforeach</select></div>
            </div>
            <div class="f" style="margin-top:12px"><label class="lb" for="etd">Description</label><textarea class="fc" id="etd" name="description" rows="2" maxlength="3000">{{ $tower->description }}</textarea></div>
            <div style="margin-top:12px"><button class="btn2 p" type="submit"><i class="fe fe-save"></i> Save changes</button></div>
        </form></details>
        <form method="POST" action="{{ route('locations.tower.destroy', $params) }}" style="margin-top:12px" onsubmit="return confirm('Delete tower {{ addslashes($tower->name) }}?')">@csrf @method('DELETE')
            <button class="ib del" type="submit"><i class="fe fe-trash-2"></i> Delete this tower</button> <span class="mini" style="margin-left:8px">only possible when it has no wells</span></form>
    </div>
    @endif
</div>
@endsection
