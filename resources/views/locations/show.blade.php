@extends('layouts.master')

@section('title', 'Location - ' . $location->name)

@section('css')
@include('locations._style')
@endsection

@section('content')
@php
    use App\Models\Location as L;
    $ec = $location->envColor();
    $stChip = ['active' => 'ok', 'maintenance' => 'warn', 'inactive' => 'none'];
    $towerTotal = $fields->sum('towers_count'); $wellTotal = $wellsByField->sum();
@endphp
<div class="ofp" id="ofp">
    <div class="bgl"><div class="orb o1"></div><div class="orb o2"></div></div>

    <div class="hero">
        <div class="who">
            <span class="lg"></span>
            <div>
                <div class="eyebrow" style="color:{{ $ec }}"><i></i> {{ $location->envLabel() }} location</div>
                <h1>{{ $location->name }} <em>· {{ $location->code }}</em></h1>
                <div class="crumbs"><a href="{{ route('locations.index') }}">Locations</a><i class="fe fe-chevron-right sep"></i><span>{{ $location->name }}</span>
                    <span class="chip {{ $stChip[$location->status] }}" style="margin-left:6px">{{ L::STATUSES[$location->status] }}</span></div>
            </div>
        </div>
        <div class="actions">
            @if($canManage)
            <a class="btn2 p" href="{{ route('locations.edit', $location) }}"><i class="fe fe-edit-2"></i> Edit</a>
            <form method="POST" action="{{ route('locations.destroy', $location) }}" style="display:inline" onsubmit="return confirm('Delete this location? Its fields stay in the system but become unassigned.')">@csrf @method('DELETE')<button class="btn2" type="submit"><i class="fe fe-trash-2"></i> Delete</button></form>
            @endif
        </div>
    </div>

    @if(session('success'))<div class="msg ok flash"><i class="fe fe-check-circle"></i><span>{{ session('success') }}</span></div>@endif
    @if($errors->any())<div class="err"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

    <div class="kpis">
        <div class="kpi" style="--c:{{ $ec }}"><div class="ic"><i class="fe {{ $location->envIcon() }}"></i></div><div class="l">Type</div><div class="v" style="font-size:22px">{{ $location->envLabel() }}</div><div class="u">{{ $location->water_depth_m !== null ? number_format($location->water_depth_m) . ' m water depth' : 'land' }}</div></div>
        <div class="kpi" style="--c:#38bdf8"><div class="ic"><i class="fe fe-layers"></i></div><div class="l">Oil fields</div><div class="v">{{ $fields->count() }}</div><div class="u">in this location</div></div>
        <div class="kpi" style="--c:#a78bfa"><div class="ic"><i class="fe fe-radio"></i></div><div class="l">Towers</div><div class="v">{{ $towers->count() }}</div><div class="u">across those fields</div></div>
        <div class="kpi" style="--c:#34d399"><div class="ic"><i class="fe fe-droplet"></i></div><div class="l">Wells</div><div class="v">{{ $wellTotal }}</div><div class="u">strings on the towers</div></div>
        <div class="kpi" style="--c:#fbbf24"><div class="ic"><i class="fe fe-anchor"></i></div><div class="l">Testing units</div><div class="v">{{ $location->testVessels->count() }}</div><div class="u">operating here</div></div>
    </div>

    {{-- ═════ TOWERS ═════ --}}
    @php use App\Models\Tower as T; @endphp
    <div class="panel" style="margin-top:18px">
        <div class="ph"><h3><i class="fe fe-radio"></i> Wellhead towers in {{ $location->name }}</h3><span class="sub">{{ $towers->count() }} tower(s) · open one to see its wells and strings</span></div>
        @if($towers->isEmpty())
            <div class="empty">No towers in this location yet. Add the first one below.</div>
        @else
        <div class="scroll"><table class="t"><thead><tr><th>Tower</th><th>Type</th><th>Field</th><th>Wells</th><th>Status</th><th></th></tr></thead><tbody>
            @foreach($towers as $t)
            <tr>
                <td><a class="lnk" href="{{ route('locations.tower', [$location, $t->oilField, $t]) }}"><b>{{ $t->name }}</b></a><div class="mini">{{ $t->code }}</div></td>
                <td><i class="fe {{ $t->typeIcon() }}" style="color:#a78bfa;margin-right:6px"></i>{{ $t->typeLabel() }}</td>
                <td>{{ $t->oilField->name ?? '—' }}</td>
                <td>{{ $t->oil_strings_count }}</td>
                <td><span class="chip {{ $stChip[$t->status] ?? 'none' }}">{{ ucfirst($t->status) }}</span></td>
                <td style="white-space:nowrap">
                    <a class="ib" href="{{ route('locations.tower', [$location, $t->oilField, $t]) }}">Open <i class="fe fe-arrow-right"></i></a>
                    @if($canManage)
                    <details class="frm sm" style="display:inline-block;min-width:0;margin:0 0 0 4px"><summary style="padding:5px 10px" title="Edit"><i class="fe fe-edit-2"></i></summary>
                    <form class="in" method="POST" action="{{ route('locations.towers.update', [$location, $t]) }}" style="min-width:320px">@csrf @method('PUT')
                        <div class="f"><label class="lb">Type</label><select class="fc" name="tower_type">@foreach(T::TYPES as $k => [$tl])<option value="{{ $k }}" @selected($t->tower_type === $k)>{{ $tl }}</option>@endforeach</select></div>
                        <div class="row" style="margin-top:8px;grid-template-columns:1fr 1fr">
                            <div class="f"><label class="lb">Name</label><input class="fc" name="name" required maxlength="255" value="{{ $t->name }}"></div>
                            <div class="f"><label class="lb">Code</label><input class="fc" name="code" required maxlength="255" value="{{ $t->code }}"></div>
                        </div>
                        <div class="row" style="margin-top:8px;grid-template-columns:1fr 1fr">
                            <div class="f"><label class="lb">Field</label><select class="fc" name="oil_field_id">@foreach($fields as $f)<option value="{{ $f->id }}" @selected($t->oil_field_id === $f->id)>{{ $f->name }}</option>@endforeach</select></div>
                            <div class="f"><label class="lb">Status</label><select class="fc" name="status">@foreach(L::STATUSES as $k => $l)<option value="{{ $k }}" @selected($t->status === $k)>{{ $l }}</option>@endforeach</select></div>
                        </div>
                        <input type="hidden" name="description" value="{{ $t->description }}">
                        <div style="margin-top:10px"><button class="btn2 p" type="submit"><i class="fe fe-save"></i> Save</button></div>
                    </form></details>
                    <form method="POST" action="{{ route('locations.towers.destroy', [$location, $t]) }}" style="display:inline" onsubmit="return confirm('Delete {{ addslashes($t->name) }}?')">@csrf @method('DELETE')<button class="xb" title="Delete"><i class="fe fe-trash-2"></i></button></form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody></table></div>
        @endif

        @if($canManage)
        <details class="frm" @if($errors->has('code') && old('tower_type')) open @endif><summary><i class="fe fe-plus-circle"></i> Add a wellhead tower</summary>
        <form class="in" method="POST" action="{{ route('locations.towers.store', $location) }}">@csrf
            <div class="f"><label class="lb">Tower type <b>*</b></label>
                <div class="ptl">
                    @foreach(T::TYPES as $k => [$tl, $ti])
                    <input type="radio" name="tower_type" id="lt_{{ $k }}" value="{{ $k }}" @checked(old('tower_type', 'wellhead_tower') === $k)><label for="lt_{{ $k }}" style="--tc:#a78bfa"><i class="fe {{ $ti }}" style="color:#a78bfa"></i>{{ $tl }}</label>
                    @endforeach
                </div></div>
            <div class="row" style="margin-top:14px">
                <div class="f"><label class="lb" for="ltn">Tower name <b>*</b></label><input class="fc" id="ltn" name="name" required maxlength="255" placeholder="e.g. WHT-05" value="{{ old('name') }}"></div>
                <div class="f"><label class="lb" for="ltc">Code <b>*</b></label><input class="fc" id="ltc" name="code" required maxlength="255" placeholder="e.g. ZKW-T05" value="{{ old('code') }}"></div>
                @if($fields->count() > 1)<div class="f"><label class="lb" for="ltf">Field</label><select class="fc" id="ltf" name="oil_field_id">@foreach($fields as $f)<option value="{{ $f->id }}">{{ $f->name }}</option>@endforeach</select></div>@endif
                <div class="f"><label class="lb" for="lts">Status</label><select class="fc" id="lts" name="status">@foreach(L::STATUSES as $k => $l)<option value="{{ $k }}">{{ $l }}</option>@endforeach</select></div>
            </div>
            @if($fields->isEmpty())<div class="mini" style="margin-top:10px">This location has no oil field yet, so one named "{{ $location->name }}" will be created for the tower automatically.</div>@endif
            <div style="margin-top:12px"><button class="btn2 p" type="submit"><i class="fe fe-save"></i> Add tower</button></div>
        </form></details>
        @endif
    </div>
    {{-- ═════ FIELDS ═════ --}}
    <div class="panel" style="margin-top:18px">
        <div class="ph"><h3><i class="fe fe-layers"></i> Oil fields in this location</h3><span class="sub">open a field to see its towers</span></div>
        @if($fields->isEmpty())
            <div class="empty">No oil fields yet. Add the first one below.</div>
        @else
        <div class="lc-grid" style="margin-top:0">
            @foreach($fields as $f)
            <div class="lc" style="--ec:#38bdf8">
                <div class="tp"><i class="fe fe-layers"></i>Oil field</div>
                <h3><a href="{{ route('locations.field', [$location, $f]) }}" style="color:inherit">{{ $f->name }}</a></h3>
                <div class="cd">{{ $f->code }}</div>
                <div class="st" style="--n:2"><div><b>{{ $f->towers_count }}</b><small>Towers</small></div><div><b>{{ $wellsByField[$f->id] ?? 0 }}</b><small>Wells</small></div></div>
                <div class="ft">
                    <span class="chip {{ $stChip[$f->status] ?? 'none' }}">{{ ucfirst($f->status) }}</span><span class="sp"></span>
                    <a class="ib" href="{{ route('locations.field', [$location, $f]) }}">Open <i class="fe fe-arrow-right"></i></a>
                    @if($canManage)
                    <form method="POST" action="{{ route('locations.release', $location) }}" style="display:inline" onsubmit="return confirm('Remove {{ addslashes($f->name) }} from this location? Nothing is deleted.')">@csrf<input type="hidden" name="kind" value="field"><input type="hidden" name="id" value="{{ $f->id }}"><button class="ib" type="submit" title="Remove from location"><i class="fe fe-x"></i></button></form>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif

        @if($canManage)
        <details class="frm" @if($errors->has('code') || $errors->has('name')) open @endif><summary><i class="fe fe-plus-circle"></i> Add a new oil field</summary>
        <form class="in" method="POST" action="{{ route('locations.field.store', $location) }}">@csrf
            <div class="row">
                <div class="f"><label class="lb" for="fn">Field name <b>*</b></label><input class="fc" id="fn" name="name" required maxlength="255" placeholder="e.g. Zakum West" value="{{ old('name') }}"></div>
                <div class="f"><label class="lb" for="fc">Code <b>*</b></label><input class="fc" id="fc" name="code" required maxlength="255" placeholder="e.g. ZKW" value="{{ old('code') }}"></div>
                <div class="f"><label class="lb" for="fs">Status</label><select class="fc" id="fs" name="status">@foreach(L::STATUSES as $k => $l)<option value="{{ $k }}">{{ $l }}</option>@endforeach</select></div>
            </div>
            <div class="f" style="margin-top:12px"><label class="lb" for="fd">Description</label><textarea class="fc" id="fd" name="description" rows="2" maxlength="3000">{{ old('description') }}</textarea></div>
            <div style="margin-top:12px"><button class="btn2 p" type="submit"><i class="fe fe-save"></i> Create field</button></div>
        </form></details>
        <form method="POST" action="{{ route('locations.assign', $location) }}" class="asg">@csrf<input type="hidden" name="kind" value="field">
            <div class="f"><label class="lb" for="ef">Or move an existing field here</label>
                <select class="fc" name="id" id="ef" required><option value="">Select a field without a location…</option>@foreach($freeFields as $f)<option value="{{ $f->id }}">{{ $f->name }} ({{ $f->code }})</option>@endforeach</select></div>
            <button class="btn2 p" type="submit" @disabled($freeFields->isEmpty())><i class="fe fe-plus"></i> Add to location</button>
        </form>
        @endif
    </div>

    {{-- ═════ DETAILS ═════ --}}
    <div class="panel" style="margin-top:18px">
        <div class="ph"><h3><i class="fe fe-info"></i> Details</h3></div>
        <div class="kv">
            <div><small>Type</small><b style="color:{{ $ec }}">{{ $location->envLabel() }}</b></div>
            <div><small>Latitude</small><b>{{ $location->latitude !== null ? $location->latitude : '—' }}</b></div>
            <div><small>Longitude</small><b>{{ $location->longitude !== null ? $location->longitude : '—' }}</b></div>
            <div><small>Water depth</small><b>{{ $location->water_depth_m !== null ? number_format($location->water_depth_m) . ' m' : '—' }}</b></div>
            <div><small>Created</small><b>{{ $location->created_at->format('d M Y') }}</b></div>
        </div>
        @if($location->description)<div class="msg" style="margin-top:14px"><i class="fe fe-message-square"></i><span>{{ $location->description }}</span></div>@endif
    </div>

    {{-- ═════ TESTING UNITS ═════ --}}
    <div class="panel" style="margin-top:18px">
        <div class="ph"><h3><i class="fe fe-anchor"></i> Testing units in this location</h3><span class="sub">well testing, offshore or onshore</span>
            @if($canManage)<a class="lnk" href="{{ route('test_vessels.create') }}">Add testing unit</a>@endif</div>
        @if($location->testVessels->isEmpty())
            <div class="empty">No testing units operating here yet.</div>
        @else
        <div class="scroll"><table class="t"><thead><tr><th>Unit</th><th>Code</th><th>Status</th><th></th></tr></thead><tbody>
            @foreach($location->testVessels as $v)
            <tr><td><a class="lnk" href="{{ route('test_vessels.show', $v) }}">{{ $v->name }}</a></td><td>{{ $v->code }}</td>
                <td><span class="chip {{ $stChip[$v->status] ?? 'none' }}">{{ ucfirst($v->status) }}</span></td>
                <td>@if($canManage)<form method="POST" action="{{ route('locations.release', $location) }}" onsubmit="return confirm('Remove {{ addslashes($v->name) }} from this location?')">@csrf<input type="hidden" name="kind" value="vessel"><input type="hidden" name="id" value="{{ $v->id }}"><button class="xb" title="Remove from location"><i class="fe fe-x"></i></button></form>@endif</td></tr>
            @endforeach
        </tbody></table></div>
        @endif
        @if($canManage)
        <form method="POST" action="{{ route('locations.assign', $location) }}" class="asg">@csrf<input type="hidden" name="kind" value="vessel">
            <div class="f"><label class="lb" for="vs">Add an unassigned testing unit</label>
                <select class="fc" name="id" id="vs" required><option value="">Select unit…</option>@foreach($freeVessels as $v)<option value="{{ $v->id }}">{{ $v->name }} ({{ $v->code }})</option>@endforeach</select></div>
            <button class="btn2 p" type="submit" @disabled($freeVessels->isEmpty())><i class="fe fe-plus"></i> Assign unit</button>
        </form>
        @endif
    </div>
</div>
@endsection
