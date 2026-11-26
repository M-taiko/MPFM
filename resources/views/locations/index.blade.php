@extends('layouts.master')

@section('title', 'Locations')

@section('css')
@include('locations._style')
@endsection

@section('content')
@php use App\Models\Location as L; @endphp
<div class="ofp" id="ofp">
    <div class="bgl"><div class="orb o1"></div><div class="orb o2"></div></div>

    <div class="hero">
        <div class="who">
            <span class="lg"></span>
            <div>
                <div class="eyebrow"><i></i> Field Setup</div>
                <h1>Well <em>locations</em></h1>
                <div class="meta"><span>Location › Oil field › Tower › Well string. Open a location to see the fields inside it.</span></div>
            </div>
        </div>
        <div class="actions">
            @if($canManage)<a class="btn2 p" href="{{ route('locations.create') }}"><i class="fe fe-plus"></i> Add location</a>@endif
        </div>
    </div>

    @if(session('success'))<div class="msg ok flash"><i class="fe fe-check-circle"></i><span>{{ session('success') }}</span></div>@endif

    <div class="kpis">
        <div class="kpi" style="--c:#22d3ee"><div class="ic"><i class="fe fe-map-pin"></i></div><div class="l">Locations</div><div class="v">{{ $totals['locations'] }}</div><div class="u">onshore, offshore...</div></div>
        <div class="kpi" style="--c:#38bdf8"><div class="ic"><i class="fe fe-layers"></i></div><div class="l">Oil fields</div><div class="v">{{ $totals['fields'] }}</div><div class="u">inside locations</div></div>
        <div class="kpi" style="--c:#a78bfa"><div class="ic"><i class="fe fe-radio"></i></div><div class="l">Towers</div><div class="v">{{ $totals['towers'] }}</div><div class="u">wellhead towers and more</div></div>
        <div class="kpi" style="--c:#34d399"><div class="ic"><i class="fe fe-droplet"></i></div><div class="l">Wells</div><div class="v">{{ $totals['wells'] }}</div><div class="u">strings 1, 2, 3...</div></div>
    </div>

    <div class="seg">
        <a href="{{ route('locations.index') }}" class="{{ request('environment') ? '' : 'on' }}"><i class="fe fe-globe"></i> All <b>{{ $totals['locations'] }}</b></a>
        @foreach(L::TYPES as $k => [$el, $ec, $ei])
            <a href="{{ route('locations.index', ['environment' => $k]) }}" class="{{ request('environment') === $k ? 'on' : '' }}" style="--ec:{{ $ec }}"><i class="fe {{ $ei }}" style="color:{{ $ec }}"></i> {{ $el }} <b>{{ $byType[$k] ?? 0 }}</b></a>
        @endforeach
    </div>

    <form method="GET" class="panel" style="margin-top:16px;display:block">
        <div class="flt">
            <div class="f"><label class="lb">Search</label><input class="fc" type="text" name="q" value="{{ request('q') }}" placeholder="Name or code"></div>
            <input type="hidden" name="environment" value="{{ request('environment') }}">
            <div class="f"><label class="lb">Status</label>
                <select class="fc" name="status"><option value="">Any</option>
                    @foreach(L::STATUSES as $k => $l)<option value="{{ $k }}" @selected(request('status') === $k)>{{ $l }}</option>@endforeach
                </select></div>
            <button class="btn2 p" type="submit"><i class="fe fe-filter"></i> Filter</button>
            <a class="btn2" href="{{ route('locations.index') }}">Reset</a>
        </div>
    </form>

    @if($locations->isEmpty())
        <div class="panel" style="margin-top:16px"><div class="empty">No locations yet.@if($canManage) <a class="lnk" href="{{ route('locations.create') }}">Add the first one</a>@endif</div></div>
    @else
    <div class="lc-grid">
        @foreach($locations as $l)
        @php $s = $stats[$l->id] ?? ['fields' => 0, 'towers' => 0, 'wells' => 0]; @endphp
        <div class="lc" style="--ec:{{ $l->envColor() }}">
            <div class="tp"><i class="fe {{ $l->envIcon() }}"></i>{{ $l->envLabel() }}</div>
            <h3><a href="{{ route('locations.show', $l) }}" style="color:inherit">{{ $l->name }}</a></h3>
            <div class="cd">{{ $l->code }}@if($l->water_depth_m !== null) · {{ number_format($l->water_depth_m) }} m water @endif</div>
            <div class="st">
                <div><b>{{ $s['fields'] }}</b><small>Fields</small></div>
                <div><b>{{ $s['towers'] }}</b><small>Towers</small></div>
                <div><b>{{ $s['wells'] }}</b><small>Wells</small></div>
            </div>
            <div class="ft">
                <span class="chip {{ ['active' => 'ok', 'maintenance' => 'warn', 'inactive' => 'none'][$l->status] }}">{{ L::STATUSES[$l->status] }}</span>
                <span class="sp"></span>
                <a class="ib" href="{{ route('locations.show', $l) }}" title="Open">Open <i class="fe fe-arrow-right"></i></a>
                @if($canManage)
                <a class="ib" href="{{ route('locations.edit', $l) }}" title="Edit"><i class="fe fe-edit-2"></i></a>
                <form method="POST" action="{{ route('locations.destroy', $l) }}" style="display:inline" onsubmit="return confirm('Delete {{ addslashes($l->name) }}? Its fields stay in the system but become unassigned.')">@csrf @method('DELETE')<button class="ib del" type="submit" title="Delete"><i class="fe fe-trash-2"></i></button></form>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    <div class="pg">{{ $locations->links('pagination::bootstrap-4') }}</div>
    @endif
</div>
@endsection
