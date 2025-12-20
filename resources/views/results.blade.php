@extends('layouts.master')

@section('content')
<div class="container-fluid">

    <h2 class="mb-4">Well Test Summary: {{ $wellTest->well }}</h2>

    <!-- Summary Table -->
    <div class="card mb-4">
        <div class="card-header"><h6 class="card-title mb-0">Well Test Summary</h6></div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Client</th><th>Field</th><th>Well Name</th>
                        <th>Wellhead Tower</th><th>Wellhead String</th>
                        <th>Choke Size</th><th>Upstream Choke</th><th>Downstream Choke</th>
                        <th>Start Time</th><th>End Time</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $wellTest->client }}</td>
                        <td>{{ $wellTest->field }}</td>
                        <td>{{ $wellTest->well }}</td>
                        <td>{{ $wellheadTower }}</td>
                        <td>{{ $wellheadString }}</td>
                        <td>{{ $chokeSize }}</td>
                        <td>{{ $upstreamChoke }}</td>
                        <td>{{ $downstreamChoke }}</td>
                        <td>{{ $startTime ? $startTime->format('Y-m-d H:i') : 'N/A' }}</td>
                        <td>{{ $endTime ? $endTime->format('Y-m-d H:i') : 'N/A' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Charts -->
    <div class="mb-4">
        <div class="mb-3">
            <label>Change Unit:</label>
            <select id="unitSelect" class="form-select w-auto d-inline-block">
                <option value="stb" selected>STB/d</option>
                <option value="m3">m³/d</option>
            </select>
        </div>
        <canvas id="ratesChart" style="height:300px;"></canvas>
    </div>

    <!-- VX Log Data Table -->
    <div class="card mb-4">
        <div class="card-header"><h6 class="card-title mb-0">Full VX Log Data</h6></div>
        <div class="card-body">
            <table id="vxLogTable" class="table table-bordered table-striped display nowrap" style="width:100%">
                <thead>
                    <tr>
                        @foreach($parsedData['test_data']['columns'] as $col)
                            <th>{{ $col }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($parsedData['test_data']['rows'] as $row)
                        <tr>
                            @foreach($parsedData['test_data']['columns'] as $col)
                                @php
                                    $value = $row[$col] ?? '';
                                    $class = '';
                                    if(is_numeric($value)){
                                        if(str_contains($col,'WHP') && $value<400) $class='table-danger';
                                        if(str_contains($col,'Temp') && $value>200) $class='table-warning';
                                    }
                                @endphp
                                <td class="{{ $class }}">{{ $value }}</td>
                            @endforeach
                        </tr>
                    @endforeach

                    {{-- Full Average Row --}}
                    <tr class="table-secondary">
                        @foreach($parsedData['test_data']['columns'] as $col)
                            <td>{{ $fullAverageRow[$col] ?? '#N/A' }}</td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script>
let unit = 'stb';
const ratesDataOriginal = @json($chartData);
let ratesData = JSON.parse(JSON.stringify(ratesDataOriginal));

const ctx = document.getElementById('ratesChart').getContext('2d');
const ratesChart = new Chart(ctx, {
    type: 'line',
    data: ratesData,
    options: {
        responsive: true,
        plugins: { title: { display: true, text: 'Production Rates' } },
        scales: { y: { beginAtZero: true, title: { display: true, text: 'Rate (STB/d or Mcf/d)' } } }
    }
});

document.getElementById('unitSelect').addEventListener('change', function(){
    unit = this.value;
    const factor = unit === 'm3' ? 0.158987 : 1;
    ratesChart.data.datasets.forEach((ds, idx)=>{
        if(idx<2){
            ds.data = ratesDataOriginal.datasets[idx].data.map(v=>parseFloat(v)*factor);
        }
    });
    ratesChart.options.scales.y.title.text = unit==='stb'?'Rate (STB/d or Mcf/d)':'Rate (m³/d or Mcf/d)';
    ratesChart.update();
});

$(document).ready(function(){
    $('#vxLogTable').DataTable({ scrollX:true, pageLength:10, lengthMenu:[10,25,50,100] });
});
</script>
@endsection
