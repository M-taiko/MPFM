@extends('layouts.master')

@section('title', $location ? 'Edit Location' : 'Add Location')

@section('css')
@include('locations._style')
@endsection

@section('content')
@php
    use App\Models\Location as L;
    $edit = (bool) $location;
    $val = fn ($k, $d = '') => old($k, $edit ? ($location->{$k} ?? $d) : ($preset[$k] ?? $d));
    $envSel = $val('environment', '') ?: '';
    $stSel = $val('status', 'active');
    $moreOpen = $edit || $errors->hasAny(['latitude', 'longitude', 'water_depth_m', 'description']);
    $short = ['onshore' => 'Desert / land', 'offshore' => 'Sea', 'nearshore' => 'Shallow water', 'island' => 'Island'];
@endphp
<div class="ofp" id="ofp">
    <div class="bgl"><div class="orb o1"></div><div class="orb o2"></div></div>

    <div class="hero">
        <div class="who">
            <span class="lg"></span>
            <div>
                <div class="eyebrow"><i></i> Field Setup</div>
                <h1>{{ $edit ? 'Edit' : 'New' }} <em>location</em></h1>
                <div class="meta"><span>Name it, give it a code, choose sea or desert. Then open it and add its fields, towers and wells.</span></div>
            </div>
        </div>
        <div class="actions"><a class="btn2" href="{{ $edit ? route('locations.show', $location) : route('locations.index') }}"><i class="fe fe-arrow-left"></i> Back</a></div>
    </div>

    @if($errors->any())
        <div class="err"><b><i class="fe fe-alert-circle"></i> Please fix the following:</b><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <form method="POST" style="display:block" action="{{ $edit ? route('locations.update', $location) : route('locations.store') }}">
        @csrf
        @if($edit) @method('PUT') @endif

        <div class="panel" style="margin-top:18px">
            <div class="ph num"><span class="n">1</span><div><h3 style="font-size:16px;font-weight:800;margin:0">Name and code</h3><small>How people will find this location</small></div></div>
            <div class="row">
                <div class="f {{ $errors->has('name') ? 'has-err' : '' }}"><label class="lb" for="name"><i class="fe fe-map-pin"></i> Location name <b>*</b></label>
                    <input class="fc" type="text" name="name" id="name" required maxlength="255" autofocus placeholder="e.g. Offshore Zakum" value="{{ $val('name') }}">@error('name')<span class="em">{{ $message }}</span>@enderror</div>
                <div class="f {{ $errors->has('code') ? 'has-err' : '' }}"><label class="lb" for="code"><i class="fe fe-hash"></i> Code <b>*</b></label>
                    <input class="fc" type="text" name="code" id="code" required maxlength="60" placeholder="e.g. LOC-001" value="{{ $val('code') }}">@error('code')<span class="em">{{ $message }}</span>@enderror</div>
            </div>
        </div>

        <div class="panel">
            <div class="ph num"><span class="n">2</span><div><h3 style="font-size:16px;font-weight:800;margin:0">Sea or desert?</h3><small>Choose the type of location <b style="color:var(--red)">*</b></small></div></div>
            <div class="ptl big">
                @foreach(L::TYPES as $k => [$el, $ec, $ei, $ed])
                <input type="radio" name="environment" id="env_{{ $k }}" value="{{ $k }}" required @checked($envSel === $k)>
                <label for="env_{{ $k }}" style="--tc:{{ $ec }}"><i class="fe {{ $ei }}"></i>{{ $el }} <span style="font-weight:500;color:{{ $ec }}">· {{ $short[$k] }}</span><small>{{ $ed }}</small></label>
                @endforeach
            </div>
            @error('environment')<span class="em">{{ $message }}</span>@enderror
        </div>

        <details class="frm" style="margin-top:16px" @if($moreOpen) open @endif><summary><i class="fe fe-sliders"></i> More details (optional): status, position, water depth, notes</summary>
        <div class="in">
            <div class="row">
                <div class="f"><label class="lb" for="latitude"><i class="fe fe-compass"></i> Latitude</label>
                    <input class="fc" type="number" step="0.000001" min="-90" max="90" name="latitude" id="latitude" placeholder="e.g. 24.4539" value="{{ $val('latitude') }}">@error('latitude')<span class="em">{{ $message }}</span>@enderror</div>
                <div class="f"><label class="lb" for="longitude"><i class="fe fe-compass"></i> Longitude</label>
                    <input class="fc" type="number" step="0.000001" min="-180" max="180" name="longitude" id="longitude" placeholder="e.g. 54.3773" value="{{ $val('longitude') }}">@error('longitude')<span class="em">{{ $message }}</span>@enderror</div>
                <div class="f" id="depthBox"><label class="lb" for="water_depth_m"><i class="fe fe-arrow-down"></i> Water depth (m)</label>
                    <input class="fc" type="number" step="0.1" min="0" name="water_depth_m" id="water_depth_m" value="{{ $val('water_depth_m') }}">@error('water_depth_m')<span class="em">{{ $message }}</span>@enderror</div>
            </div>
            <div class="f" style="margin-top:14px"><label class="lb" for="description"><i class="fe fe-edit-3"></i> Description</label>
                <textarea class="fc" name="description" id="description" rows="2" maxlength="3000">{{ $val('description') }}</textarea></div>
            <div class="f" style="margin-top:14px"><label class="lb"><i class="fe fe-activity"></i> Status</label>
                <div class="ptl">
                    @foreach([['active', '#34d399'], ['maintenance', '#fbbf24'], ['inactive', '#94a3b8']] as [$sk, $sc])
                    <input type="radio" name="status" id="ls_{{ $sk }}" value="{{ $sk }}" @checked($stSel === $sk)><label for="ls_{{ $sk }}" style="--tc:{{ $sc }}">{{ L::STATUSES[$sk] }}</label>
                    @endforeach
                </div></div>
        </div></details>

        <div class="bar">
            <button type="submit" class="btn2 p"><i class="fe fe-check"></i> {{ $edit ? 'Update location' : 'Create location' }}</button>
            <a href="{{ $edit ? route('locations.show', $location) : route('locations.index') }}" class="btn2"><i class="fe fe-x"></i> Cancel</a>
            <span class="sp"></span><span class="hint">{{ $edit ? 'Changes apply right away' : 'After creating, you land inside the location to add its fields' }}</span>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var depth = document.getElementById('depthBox');
    function sync() {
        var env = (document.querySelector('input[name=environment]:checked') || {}).value || '';
        depth.style.display = (env === 'offshore' || env === 'nearshore' || env === '') ? '' : 'none';
    }
    document.querySelectorAll('input[name=environment]').forEach(function (r) { r.addEventListener('change', sync); });
    sync();
})();
</script>
@endsection
