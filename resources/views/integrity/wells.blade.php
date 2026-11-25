@extends('layouts.master')

@section('title', 'Well Integrity - Wells')

@section('css')
@include('integrity._style')
@endsection

@section('content')
<div class="ofp" id="ofp">
    <div class="bgl"><div class="orb o1"></div><div class="orb o2"></div></div>

    <div class="hero">
        <div class="who">
            <span class="lg"></span>
            <div>
                <div class="eyebrow"><i></i> Well Integrity Management</div>
                <h1>Wells <em>register</em></h1>
                <div class="meta"><a href="{{ route('integrity.dashboard') }}">&larr; Integrity dashboard</a><span>{{ $rows->count() }} wells</span></div>
            </div>
        </div>
    </div>

    <form method="GET" class="panel" style="margin-top:18px;display:block">
        <div class="flt">
            <div class="f"><label class="lb">Search</label><input class="fc" type="text" name="q" value="{{ request('q') }}" placeholder="Well name or code"></div>
            <div class="f"><label class="lb">Status</label>
                <select class="fc" name="status"><option value="">All</option>
                    @foreach(['red'=>'Red','amber'=>'Amber','green'=>'Green','none'=>'Not assessed'] as $k => $l)<option value="{{ $k }}" @selected(request('status') === $k)>{{ $l }}</option>@endforeach
                </select></div>
            <button class="btn2 p" type="submit"><i class="fe fe-filter"></i> Filter</button>
            <a class="btn2" href="{{ route('integrity.wells') }}">Reset</a>
        </div>
    </form>

    <div class="panel" style="margin-top:16px">
        @if($rows->isEmpty())
            <div class="empty">No wells match.</div>
        @else
        <div class="scroll"><table class="t"><thead><tr>
            <th>Well</th><th>Status</th><th>Score</th><th>Annuli</th><th>Failed barriers</th><th>Tests overdue</th><th>Open anomalies</th><th>Rate limit</th><th></th></tr></thead><tbody>
            @foreach($rows as $r)
            @php $a = $r['a']; $c = $a['counts']; @endphp
            <tr>
                <td>{{ $r['well']->name }}<div class="mini">{{ $r['well']->tower?->oilField?->name }} · {{ $r['well']->tower?->name }}</div></td>
                <td><span class="chip {{ $a['class'] }}">{{ $a['label'] }}</span></td>
                <td>{{ $a['score'] ?? '—' }}</td>
                <td>{{ count($a['annuli']) ? ($c['annuli_alert'] ? $c['annuli_alert'] . ' alert(s)' : 'OK') : '—' }}</td>
                <td>{{ $c['failed'] ?: '—' }}</td>
                <td>{{ $c['overdue_tests'] ?: '—' }}</td>
                <td>{{ $c['open_anomalies'] ? $c['open_anomalies'] . ($c['critical'] ? ' (' . $c['critical'] . ' critical)' : '') : '—' }}</td>
                <td>{{ $a['profile']?->max_oil_rate ? number_format($a['profile']->max_oil_rate) . ' B/D' : '—' }}</td>
                <td><a class="lnk" href="{{ route('integrity.show', $r['well']) }}">Open</a></td>
            </tr>
            @endforeach
        </tbody></table></div>
        @endif
    </div>
</div>
@endsection
