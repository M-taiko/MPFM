@extends('layouts.master')

@section('title', $operation ? 'Edit Wire Line Operation' : 'New Wire Line Operation')

@section('css')
@include('wireline._style')
@endsection

@section('content')
@php
    use App\Models\WirelineOperation as Op;
    use App\Models\WirelineGaugeRun as Run;
    $edit = (bool) $operation;
    $val = fn ($k, $d = '') => old($k, $edit ? ($operation->{$k} ?? $d) : $d);
    $typeSel = $val('operation_type', 'memory_gauge');
    $wellSel = old('oil_string_id', $selectedWell);
@endphp
<div class="ofp" id="ofp">
    <div class="bgl"><div class="orb o1"></div><div class="orb o2"></div></div>

    <div class="hero">
        <div class="who">
            <span class="lg"></span>
            <div>
                <div class="eyebrow"><i></i> Wire Line Services</div>
                <h1>{{ $edit ? 'Edit' : 'New' }} <em>operation</em></h1>
                <div class="meta"><span>Record the job, then attach the memory-gauge file to render the well analysis.</span></div>
            </div>
        </div>
        <div class="actions"><a class="btn2" href="{{ $edit ? route('wireline.show', $operation) : route('wireline.operations') }}"><i class="fe fe-arrow-left"></i> Back</a></div>
    </div>

    @if($errors->any())
        <div class="err"><b><i class="fe fe-alert-circle"></i> Please fix the following:</b><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <form method="POST" enctype="multipart/form-data" style="display:block"
          action="{{ $edit ? route('wireline.update', $operation) : route('wireline.store') }}">
        @csrf
        @if($edit) @method('PUT') @endif

        <div class="panel" style="margin-top:18px">
            <div class="ph num"><span class="n">1</span><div><h3 style="font-size:16px;font-weight:800;margin:0">Well &amp; job</h3><small>Where and what</small></div></div>
            <div class="row">
                <div class="f {{ $errors->has('oil_string_id') ? 'has-err' : '' }}"><label class="lb" for="oil_string_id"><i class="fe fe-droplet"></i> Well <b>*</b></label>
                    <select class="fc" name="oil_string_id" id="oil_string_id" required>
                        <option value="">Select well…</option>
                        @foreach($wells as $w)
                            <option value="{{ $w->id }}" @selected($wellSel == $w->id)>{{ $w->name }}{{ $w->tower?->oilField ? ' — ' . $w->tower->oilField->name : '' }}</option>
                        @endforeach
                    </select>@error('oil_string_id')<span class="em">{{ $message }}</span>@enderror</div>
                <div class="f"><label class="lb" for="operation_date"><i class="fe fe-calendar"></i> Date <b>*</b></label>
                    <input class="fc" type="date" name="operation_date" id="operation_date" required value="{{ $edit ? $operation->operation_date->format('Y-m-d') : old('operation_date', now()->format('Y-m-d')) }}">
                    @error('operation_date')<span class="em">{{ $message }}</span>@enderror</div>
                <div class="f"><label class="lb" for="run_number"><i class="fe fe-hash"></i> Run no. <b>*</b></label>
                    <input class="fc" type="number" min="1" name="run_number" id="run_number" required value="{{ $val('run_number', 1) }}">
                    @error('run_number')<span class="em">{{ $message }}</span>@enderror</div>
                <div class="f"><label class="lb" for="status"><i class="fe fe-flag"></i> Status <b>*</b></label>
                    <select class="fc" name="status" id="status">
                        @foreach(Op::STATUSES as $k => $l)<option value="{{ $k }}" @selected($val('status', 'completed') === $k)>{{ $l }}</option>@endforeach
                    </select></div>
            </div>
            <div class="f" style="margin-top:16px"><label class="lb"><i class="fe fe-tool"></i> Operation type <b>*</b></label>
                <div class="tile">
                    @foreach(Op::TYPES as $k => $t)
                        <input type="radio" name="operation_type" id="ot_{{ $k }}" value="{{ $k }}" data-gauge="{{ $t[1] ? 1 : 0 }}" @checked($typeSel === $k)>
                        <label for="ot_{{ $k }}" style="--tc:{{ $t[2] }}"><span class="tdot" style="--tc:{{ $t[2] }};margin:0"></span>{{ $t[0] }}</label>
                    @endforeach
                </div>@error('operation_type')<span class="em">{{ $message }}</span>@enderror</div>
        </div>

        <div class="panel">
            <div class="ph num"><span class="n">2</span><div><h3 style="font-size:16px;font-weight:800;margin:0">Tool string &amp; depth</h3><small>Used for run-in speed and gradient calculations</small></div></div>
            <div class="row">
                <div class="f"><label class="lb" for="tool_string"><i class="fe fe-layers"></i> Tool string</label>
                    <input class="fc" type="text" name="tool_string" id="tool_string" maxlength="255" placeholder="e.g. Rope socket + Stem + Memory gauge" value="{{ $val('tool_string') }}"></div>
                <div class="f"><label class="lb" for="depth_from"><i class="fe fe-arrow-down"></i> Depth from (m)</label>
                    <input class="fc" type="number" step="0.01" min="0" name="depth_from" id="depth_from" value="{{ $val('depth_from') }}"></div>
                <div class="f"><label class="lb" for="depth_to"><i class="fe fe-arrow-down-circle"></i> Depth to / gauge depth (m)</label>
                    <input class="fc" type="number" step="0.01" min="0" name="depth_to" id="depth_to" value="{{ $val('depth_to') }}">
                    @error('depth_to')<span class="em">{{ $message }}</span>@enderror</div>
                <div class="f"><label class="lb" for="crew_leader"><i class="fe fe-user"></i> Crew leader</label>
                    <input class="fc" type="text" name="crew_leader" id="crew_leader" maxlength="255" value="{{ $val('crew_leader') }}"></div>
            </div>
        </div>

        <div class="panel" id="gaugePanel">
            <div class="ph num"><span class="n">3</span><div><h3 style="font-size:16px;font-weight:800;margin:0">Memory gauge data</h3>
                <small>{{ $edit ? 'Add more gauge files from the operation page' : 'Optional - you can also upload later. Supports Kuster / Quantum .log and .csv exports' }}</small></div></div>
            @unless($edit)
            <div class="row">
                <div class="f {{ $errors->has('gauge_file') ? 'has-err' : '' }}"><label class="lb" for="gauge_file"><i class="fe fe-upload"></i> Gauge file (.log / .csv / .txt)</label>
                    <input class="fc" type="file" name="gauge_file" id="gauge_file" accept=".log,.csv,.txt">
                    @error('gauge_file')<span class="em">{{ $message }}</span>@enderror</div>
                <div class="f"><label class="lb" for="survey_type"><i class="fe fe-activity"></i> Survey type</label>
                    <select class="fc" name="survey_type" id="survey_type">
                        @foreach(Run::SURVEY_TYPES as $k => $l)<option value="{{ $k }}" @selected(old('survey_type', 'bhcip') === $k)>{{ $l }}</option>@endforeach
                    </select></div>
            </div>
            @else
            <div class="msg"><i class="fe fe-info"></i><span>Use <b>Upload gauge data</b> on the operation page to attach or replace recordings.</span></div>
            @endunless
        </div>

        <div class="panel">
            <div class="ph num"><span class="n">4</span><div><h3 style="font-size:16px;font-weight:800;margin:0">Notes</h3><small>Objective and remarks</small></div></div>
            <div class="row">
                <div class="f"><label class="lb" for="objective"><i class="fe fe-target"></i> Objective</label>
                    <textarea class="fc" name="objective" id="objective" rows="3" maxlength="2000">{{ $val('objective') }}</textarea></div>
                <div class="f"><label class="lb" for="remarks"><i class="fe fe-edit-3"></i> Remarks</label>
                    <textarea class="fc" name="remarks" id="remarks" rows="3" maxlength="5000">{{ $val('remarks') }}</textarea></div>
            </div>
        </div>

        <div class="bar">
            <button type="submit" class="btn2 p"><i class="fe fe-save"></i> {{ $edit ? 'Update operation' : 'Save operation' }}</button>
            <a href="{{ $edit ? route('wireline.show', $operation) : route('wireline.operations') }}" class="btn2"><i class="fe fe-x"></i> Cancel</a>
            <span class="sp"></span><span class="hint">Fields marked * are required</span>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
(function () {
    const panel = document.getElementById('gaugePanel');
    const sync = () => {
        const sel = document.querySelector('input[name=operation_type]:checked');
        panel.style.display = sel && sel.dataset.gauge === '1' ? '' : 'none';
    };
    document.querySelectorAll('input[name=operation_type]').forEach(r => r.addEventListener('change', sync));
    sync();
})();
</script>
@endsection
