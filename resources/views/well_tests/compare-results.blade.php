@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <h2>Well Tests Comparison</h2>

    @php
        // Chunk into 2 per row
        $chunks = $comparisonData->chunk(2);
    @endphp

    @foreach($chunks as $row)
    <div class="row mb-4">
        @foreach($row as $test)
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <strong>{{ $test['well'] }}</strong> - {{ $test['client'] }}
                    <br><small>{{ $test['start_time'] }} to {{ $test['end_time'] }}</small>
                </div>
                <div class="card-body">
                    <h6>Metrics:</h6>
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>Metric</th>
                                <th>Value</th>
                                <th>Difference</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $metrics = ['oil_rate_avg','water_rate_avg','gas_rate_avg','total_gor_avg'];
                            @endphp
                            @foreach($metrics as $metric)
                                @php
                                    $value = $test['metrics'][$metric] ?? 0;
                                    $diff = '';
                                @endphp
                                <tr>
                                    <td>{{ ucfirst(str_replace('_',' ', $metric)) }}</td>
                                    <td>{{ $value }}</td>
                                    <td>
                                        {{-- نترك مكان الفارق للمعالجة بالـ JS --}}
                                        <span class="metric-diff" data-metric="{{ $metric }}" data-value="{{ $value }}"></span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <canvas id="chart-{{ $test['id'] }}" style="height:200px;"></canvas>


                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endforeach

    {{-- Summary --}}
    <div class="card mb-4">
        <div class="card-header"><h5>Comparison Summary</h5></div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Metric</th>
                        @foreach($comparisonData as $test)
                        <th>{{ $test['well'] }}</th>
                        @endforeach
                        <th>Average</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(['oil_rate_avg','water_rate_avg','gas_rate_avg','total_gor_avg'] as $metric)
                    <tr>
                        <td>{{ ucfirst(str_replace('_',' ', $metric)) }}</td>
                        @php
                            $values = $comparisonData->pluck('metrics.'.$metric)->map(fn($v) => $v ?? 0);
                        @endphp
                        @foreach($values as $v)
                        <td>{{ $v }}</td>
                        @endforeach
                        <td>{{ round($values->avg(),2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const comparisonData = @json($comparisonData);

    // رسم البياني لكل بئر
    comparisonData.forEach(test => {
        const ctx = document.getElementById('chart-' + test.id);
        if(!ctx) return;

        const rows = test.test_data.rows ?? [];
        const labels = rows.map(r => r['Date & Time'] ?? 'N/A');
        const oil = rows.map(r => parseFloat(r['qo_lc[bbl/d]'] || 0));
        const water = rows.map(r => parseFloat(r['qw_lc[bbl/d]'] || 0));
        const gas = rows.map(r => parseFloat(r['qg_lc[Mcf/d]'] || 0));

        new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    { label: 'Oil Rate', data: oil, borderColor: 'green', tension: 0.2 },
                    { label: 'Water Rate', data: water, borderColor: 'blue', tension: 0.2 },
                    { label: 'Gas Rate', data: gas, borderColor: 'red', tension: 0.2 },
                ]
            },
            options: { responsive: true }
        });
    });

    // تمييز الفروقات بالأسهم
    const metrics = ['oil_rate_avg','water_rate_avg','gas_rate_avg','total_gor_avg'];
    metrics.forEach(metric => {
        const values = comparisonData.map(t => t.metrics[metric] ?? 0);
        const max = Math.max(...values);
        const min = Math.min(...values);

        comparisonData.forEach((t, idx) => {
            const span = document.querySelector(`.metric-diff[data-metric="${metric}"][data-value="${t.metrics[metric]}"]`);
            if(span){
                if(t.metrics[metric] === max) span.innerHTML = '↑';
                else if(t.metrics[metric] === min) span.innerHTML = '↓';
                else span.innerHTML = '→';
            }
        });
    });

});
</script>
@endpush
