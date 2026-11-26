@extends('layouts.master')

@section('title', 'Field - ' . $field->name)

@section('css')
@include('locations._style')
@endsection

@section('content')
@php
    use App\Models\Location as L;
    use App\Models\Tower as T;
    $stChip = ['active' => 'ok', 'maintenance' => 'warn', 'inactive' => 'none'];
    $byType = $towers->groupBy('tower_type')->map->count();
@endphp
<div class="ofp" id="ofp">
    <div class="bgl"><div class="orb o1"></div><div class="orb o2"></div></div>

    <div class="hero">
        <div class="who">
            <span class="lg"></span>
            <div>
                <div class="eyebrow"><i></i> Oil field</div>
                <h1>{{ $field->name }} <em>· {{ $field->code }}</em></h1>
                <div class="crumbs"><a href="{{ route('locations.index') }}">Locations</a><i class="fe fe-chevron-right sep"></i>
                    <a href="{{ route('locations.show', $location) }}" style="color:{{ $location->envColor() }}">{{ $location->envLabel() }} · {{ $location->name }}</a><i class="fe fe-chevron-right sep"></i><span>{{ $field->name }}</span>
                    <span class="chip {{ $stChip[$field->status] ?? 'none' }}" style="margin-left:6px">{{ ucfirst($field->status) }}</span></div>
            </div>
        </div>
        <div class="actions"><a class="btn2" href="{{ route('locations.show', $location) }}"><i class="fe fe-arrow-left"></i> {{ $location->name }}</a></div>
    </div>

    @if(session('success'))<div class="msg ok flash"><i class="fe fe-check-circle"></i><span>{{ session('success') }}</span></div>@endif
    @if($errors->any())<div class="err"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

    <div class="kpis">
        <div class="kpi" style="--c:#a78bfa"><div class="ic"><i class="fe fe-radio"></i></div><div class="l">Towers</div><div class="v">{{ $towers->count() }}</div><div class="u">in this field</div></div>
        <div class="kpi" style="--c:#34d399"><div class="ic"><i class="fe fe-droplet"></i></div><div class="l">Wells</div><div class="v">{{ $wellCount }}</div><div class="u">strings on those towers</div></div>
        @foreach($byType as $type => $n)
        <div class="kpi" style="--c:#22d3ee"><div class="ic"><i class="fe {{ T::TYPES[$type][1] ?? 'fe-map-pin' }}"></i></div><div class="l">{{ T::TYPES[$type][0] ?? $type }}</div><div class="v">{{ $n }}</div><div class="u">of {{ $towers->count() }} towers</div></div>
        @endforeach
    </div>

    {{-- ═════ TOWERS ═════ --}}
    <div class="panel" style="margin-top:18px">
        <div class="ph"><h3><i class="fe fe-radio"></i> Towers in {{ $field->name }}</h3><span class="sub">wellhead towers, platforms, desert wellsites... open one to see its wells</span></div>
        @if($towers->isEmpty())
            <div class="empty">No towers yet. Add the first one below.</div>
        @else
        <div class="lc-grid" style="margin-top:0">
            @foreach($towers as $t)
            <div class="lc" style="--ec:#a78bfa">
                <div class="tp"><i class="fe {{ $t->typeIcon() }}"></i>{{ $t->typeLabel() }}</div>
                <h3><a href="{{ route('locations.tower', [$location, $field, $t]) }}" style="color:inherit">{{ $t->name }}</a></h3>
                <div class="cd">{{ $t->code }}</div>
                <div class="st" style="--n:1"><div><b>{{ $t->oil_strings_count }}</b><small>Wells</small></div></div>
                <div class="ft"><span class="chip {{ $stChip[$t->status] ?? 'none' }}">{{ ucfirst($t->status) }}</span><span class="sp"></span>
                    <a class="ib" href="{{ route('locations.tower', [$location, $field, $t]) }}">Open <i class="fe fe-arrow-right"></i></a></div>
            </div>
            @endforeach
        </div>
        @endif

        @if($canManage)
        <details class="frm" @if($errors->has('code') && old('tower_type')) open @endif><summary><i class="fe fe-plus-circle"></i> Add a tower</summary>
        <form class="in" method="POST" action="{{ route('locations.tower.store', [$location, $field]) }}">@csrf
            <div class="f"><label class="lb">Tower type <b>*</b></label>
                <div class="ptl">
                    @foreach(T::TYPES as $k => [$tl, $ti])
                    <input type="radio" name="tower_type" id="tt_{{ $k }}" value="{{ $k }}" @checked(old('tower_type', 'wellhead_tower') === $k)><label for="tt_{{ $k }}" style="--tc:#a78bfa"><i class="fe {{ $ti }}" style="color:#a78bfa"></i>{{ $tl }}</label>
                    @endforeach
                </div></div>
            <div class="row" style="margin-top:14px">
                <div class="f"><label class="lb" for="tn">Name <b>*</b></label><input class="fc" id="tn" name="name" required maxlength="255" placeholder="e.g. WHT-05" value="{{ old('name') }}"></div>
                <div class="f"><label class="lb" for="tc">Code <b>*</b></label><input class="fc" id="tc" name="code" required maxlength="255" placeholder="e.g. ZKW-T05" value="{{ old('code') }}"></div>
                <div class="f"><label class="lb" for="ts">Status</label><select class="fc" id="ts" name="status">@foreach(L::STATUSES as $k => $l)<option value="{{ $k }}">{{ $l }}</option>@endforeach</select></div>
            </div>
            <div class="f" style="margin-top:12px"><label class="lb" for="td">Description</label><textarea class="fc" id="td" name="description" rows="2" maxlength="3000"></textarea></div>
            <div style="margin-top:12px"><button class="btn2 p" type="submit"><i class="fe fe-save"></i> Create tower</button></div>
        </form></details>
        @endif
    </div>

    {{-- ═════ EDIT / DELETE FIELD ═════ --}}
    @if($canManage)
    <div class="panel" style="margin-top:18px">
        <div class="ph"><h3><i class="fe fe-edit-2"></i> Edit this field</h3></div>
        <details class="frm" style="margin-top:0"><summary><i class="fe fe-edit-3"></i> Change name, code or status</summary>
        <form class="in" method="POST" action="{{ route('locations.field.update', [$location, $field]) }}">@csrf @method('PUT')
            <div class="row">
                <div class="f"><label class="lb" for="en">Name <b>*</b></label><input class="fc" id="en" name="name" required maxlength="255" value="{{ $field->name }}"></div>
                <div class="f"><label class="lb" for="ec">Code <b>*</b></label><input class="fc" id="ec" name="code" required maxlength="255" value="{{ $field->code }}"></div>
                <div class="f"><label class="lb" for="es">Status</label><select class="fc" id="es" name="status">@foreach(L::STATUSES as $k => $l)<option value="{{ $k }}" @selected($field->status === $k)>{{ $l }}</option>@endforeach</select></div>
            </div>
            <div class="f" style="margin-top:12px"><label class="lb" for="ed">Description</label><textarea class="fc" id="ed" name="description" rows="2" maxlength="3000">{{ $field->description }}</textarea></div>
            <div style="margin-top:12px"><button class="btn2 p" type="submit"><i class="fe fe-save"></i> Save changes</button></div>
        </form></details>
        <form method="POST" action="{{ route('locations.field.destroy', [$location, $field]) }}" style="margin-top:12px" onsubmit="return confirm('Delete field {{ addslashes($field->name) }}?')">@csrf @method('DELETE')
            <button class="ib del" type="submit"><i class="fe fe-trash-2"></i> Delete this field</button> <span class="mini" style="margin-left:8px">only possible when it has no towers</span></form>
    </div>
    @endif
</div>
@endsection
