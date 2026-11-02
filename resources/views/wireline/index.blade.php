@extends('layouts.master')

@section('title', 'Wire Line Operations')

@section('css')
@include('wireline._style')
@endsection

@section('content')
@php use App\Models\WirelineOperation as Op; @endphp
<div class="ofp" id="ofp">
    <div class="bgl"><div class="orb o1"></div><div class="orb o2"></div></div>

    <div class="hero">
        <div class="who">
            <span class="lg"></span>
            <div>
                <div class="eyebrow"><i></i> Wire Line Services</div>
                <h1>All <em>operations</em></h1>
                <div class="meta"><a href="{{ route('wireline.dashboard') }}">&larr; Wire Line dashboard</a><span>{{ $operations->total() }} records</span></div>
            </div>
        </div>
        <div class="actions">@if($canManage)<a class="btn2 p" href="{{ route('wireline.create') }}"><i class="fe fe-plus"></i> New operation</a>@endif</div>
    </div>

    @if(session('success'))<div class="msg ok flash"><i class="fe fe-check-circle"></i><span>{{ session('success') }}</span></div>@endif

    <form method="GET" class="panel" style="margin-top:18px;display:block">
        <div class="flt">
            <div class="f"><label class="lb">Well</label>
                <select class="fc" name="well"><option value="">All wells</option>
                    @foreach($wells as $w)<option value="{{ $w->id }}" @selected(request('well') == $w->id)>{{ $w->name }}</option>@endforeach
                </select></div>
            <div class="f"><label class="lb">Type</label>
                <select class="fc" name="type"><option value="">All types</option>
                    @foreach(Op::TYPES as $k => $t)<option value="{{ $k }}" @selected(request('type') === $k)>{{ $t[0] }}</option>@endforeach
                </select></div>
            <div class="f"><label class="lb">Status</label>
                <select class="fc" name="status"><option value="">Any</option>
                    @foreach(Op::STATUSES as $k => $l)<option value="{{ $k }}" @selected(request('status') === $k)>{{ $l }}</option>@endforeach
                </select></div>
            <div class="f"><label class="lb">From</label><input class="fc" type="date" name="from" value="{{ request('from') }}"></div>
            <div class="f"><label class="lb">To</label><input class="fc" type="date" name="to" value="{{ request('to') }}"></div>
            <button class="btn2 p" type="submit"><i class="fe fe-filter"></i> Filter</button>
            <a class="btn2" href="{{ route('wireline.operations') }}">Reset</a>
        </div>
    </form>

    <div class="panel" style="margin-top:16px">
        @if($operations->isEmpty())
            <div class="empty">No operations match.</div>
        @else
        <div class="scroll"><table class="t"><thead><tr>
            <th>Date</th><th>Well</th><th>Operation</th><th>Run</th><th>Depth (m)</th><th>Gauge data</th><th>Status</th><th></th></tr></thead><tbody>
            @foreach($operations as $o)
            <tr>
                <td>{{ $o->operation_date->format('d M Y') }}</td>
                <td>{{ $o->oilString->name ?? '—' }}<div class="mini">{{ $o->oilString?->tower?->oilField?->name }}</div></td>
                <td><span class="tdot" style="--tc:{{ $o->typeColor() }}"></span>{{ $o->typeLabel() }}</td>
                <td>#{{ $o->run_number }}</td>
                <td>{{ $o->depth_to !== null ? number_format($o->depth_to) : '—' }}</td>
                <td>@if($o->gaugeRuns->isNotEmpty())<span class="chip info">{{ $o->gaugeRuns->count() }} run(s)</span>@else<span class="mini">—</span>@endif</td>
                <td><span class="chip {{ ['completed'=>'ok','in_progress'=>'info','planned'=>'pending','cancelled'=>'bad'][$o->status] }}">{{ $o->statusLabel() }}</span></td>
                <td><a class="lnk" href="{{ route('wireline.show', $o) }}">Open</a></td>
            </tr>
            @endforeach
        </tbody></table></div>
        <div class="pg">{{ $operations->links('pagination::bootstrap-4') }}</div>
        @endif
    </div>
</div>
@endsection
