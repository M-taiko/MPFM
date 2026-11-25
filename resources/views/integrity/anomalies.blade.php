@extends('layouts.master')

@section('title', 'Well Integrity - Anomalies')

@section('css')
@include('integrity._style')
@endsection

@section('content')
@php use App\Models\WellIntegrityAnomaly as An; @endphp
<div class="ofp" id="ofp">
    <div class="bgl"><div class="orb o1"></div><div class="orb o2"></div></div>

    <div class="hero">
        <div class="who">
            <span class="lg"></span>
            <div>
                <div class="eyebrow"><i></i> Well Integrity Management</div>
                <h1>Anomaly <em>register</em></h1>
                <div class="meta"><a href="{{ route('integrity.dashboard') }}">&larr; Integrity dashboard</a><span>{{ $anomalies->total() }} records</span></div>
            </div>
        </div>
    </div>

    @if(session('success'))<div class="msg ok flash"><i class="fe fe-check-circle"></i><span>{{ session('success') }}</span></div>@endif

    <form method="GET" class="panel" style="margin-top:18px;display:block">
        <div class="flt">
            <div class="f"><label class="lb">Well</label>
                <select class="fc" name="well"><option value="">All wells</option>
                    @foreach($wells as $w)<option value="{{ $w->id }}" @selected(request('well') == $w->id)>{{ $w->name }}</option>@endforeach
                </select></div>
            <div class="f"><label class="lb">Category</label>
                <select class="fc" name="category"><option value="">All</option>
                    @foreach(An::CATEGORIES as $k => $l)<option value="{{ $k }}" @selected(request('category') === $k)>{{ $l }}</option>@endforeach
                </select></div>
            <div class="f"><label class="lb">Status</label>
                <select class="fc" name="status"><option value="">Open + mitigated</option>
                    @foreach(An::STATUSES as $k => $l)<option value="{{ $k }}" @selected(request('status') === $k)>{{ $l }}</option>@endforeach
                    <option value="all" @selected(request('status') === 'all')>All</option>
                </select></div>
            <button class="btn2 p" type="submit"><i class="fe fe-filter"></i> Filter</button>
            <a class="btn2" href="{{ route('integrity.anomalies') }}">Reset</a>
        </div>
    </form>

    <div class="panel" style="margin-top:16px">
        @if($anomalies->isEmpty())
            <div class="empty">No anomalies match.</div>
        @else
        <div class="scroll"><table class="t"><thead><tr><th>Risk</th><th>Well</th><th>Anomaly</th><th>Category</th><th>Identified</th><th>Due</th><th>Status</th><th></th></tr></thead><tbody>
            @foreach($anomalies as $an)
            @php $lv = $an->level(); @endphp
            <tr>
                <td><span class="rk" style="--rk:{{ $lv[2] }}">{{ $an->risk_score }}</span> <span class="mini">{{ $lv[0] }}</span></td>
                <td>{{ $an->oilString->name ?? '—' }}</td>
                <td>{{ $an->title }}@if($an->mitigation)<div class="mini">{{ \Illuminate\Support\Str::limit($an->mitigation, 90) }}</div>@endif</td>
                <td class="mini">{{ An::CATEGORIES[$an->category] ?? $an->category }}</td>
                <td>{{ $an->identified_date->format('d M Y') }}</td>
                <td @if($an->isOverdue()) style="color:#fca5a5" @endif>{{ $an->due_date?->format('d M Y') ?? '—' }}</td>
                <td><span class="chip {{ ['open'=>'bad','mitigated'=>'warn','closed'=>'ok'][$an->status] }}">{{ An::STATUSES[$an->status] }}</span></td>
                <td><a class="lnk" href="{{ route('integrity.show', $an->oil_string_id) }}#anomalies">Open</a></td>
            </tr>
            @endforeach
        </tbody></table></div>
        <div class="pg">{{ $anomalies->links('pagination::bootstrap-4') }}</div>
        @endif
    </div>
</div>
@endsection
