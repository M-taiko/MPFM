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
                    <td>{{ $wellTest->wellhead_tower }}</td>
                    <td>{{ $wellTest->wellhead_string }}</td>
                    <td>{{ $wellTest->choke_size }}</td>
                    <td>{{ $wellTest->upstream_choke }}</td>
                    <td>{{ $wellTest->downstream_choke }}</td>
                    <td>{{ $wellTest->start_time ? $wellTest->start_time->format('Y-m-d H:i') : 'N/A' }}</td>
                    <td>{{ $wellTest->end_time ? $wellTest->end_time->format('Y-m-d H:i') : 'N/A' }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Average Table -->
<div class="card mb-4">
    <div class="card-header"><h6 class="card-title mb-0">Average Production Metrics</h6></div>
    <div class="card-body">
        <table class="table table-bordered table-striped w-auto">
            <thead>
                <tr>
                    <th>Oil Rate (STB/d)</th>
                    <th>Water Rate (STB/d)</th>
                    <th>Gas Rate (Mcf/d)</th>
                    <th>GOR (Scf/STB)</th>
                    <th>BSW (%)</th>
                    <th>WLR (%)</th>
                    <th>GVF (%)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ number_format($wellTest->calculated_metrics['oil_rate_avg'], 2) }}</td>
                    <td>{{ number_format($wellTest->calculated_metrics['water_rate_avg'], 2) }}</td>
                    <td>{{ number_format($wellTest->calculated_metrics['gas_rate_avg'], 2) }}</td>
                    <td>{{ number_format($wellTest->calculated_metrics['total_gor_avg'], 2) }}</td>
                    <td>{{ number_format($wellTest->calculated_metrics['bsw_avg'], 2) }}</td>
                    <td>{{ number_format($wellTest->calculated_metrics['wlr_avg'], 2) }}</td>
                    <td>{{ number_format($wellTest->calculated_metrics['gvf_avg'], 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Production Chart -->
<div class="card mb-4">
    <div class="card-header"><h6 class="card-title mb-0">Production Rates Chart</h6></div>
    <div class="card-body">
        <canvas id="ratesChart" style="height:200px;"></canvas>
        <div class="mt-3">
            <label>Change Unit:</label>
            <select id="unitSelect" class="form-select w-auto d-inline-block">
                <option value="stb" selected>STB/d</option>
                <option value="m3">m³/d</option>
            </select>
        </div>
    </div>
</div>

<!-- GOR Chart -->
<div class="card mb-4">
    <div class="card-header"><h6 class="card-title mb-0">Gas-Oil Ratio (GOR) Chart</h6></div>
    <div class="card-body">
        <canvas id="gorChart" style="height:200px;"></canvas>
    </div>
</div>

<!-- Detailed Table -->
<div class="card mb-4">
    <div class="card-header"><h6 class="card-title mb-0">Detailed VX Log Table</h6></div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped table-sm" id="vxDetailTable">
            <thead>
                <tr>
                    @foreach($wellTest->test_data['columns'] as $col)
                        <th>{{ $col }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($wellTest->test_data['rows'] as $row)
                <tr>
                    @foreach($wellTest->test_data['columns'] as $col)
                        @php
                        $value = $row[$col]??'';
                        $class='';
                        if(in_array($col,['WHP[psi]','Temp[F]','Press[psi]']) && is_numeric($value)){
                            if($col==='WHP[psi]' && $value<400) $class='table-danger';
                            if($col==='Temp[F]' && $value>200) $class='table-warning';
                            if($col==='Press[psi]' && $value<200) $class='table-danger';
                        }
                        @endphp
                        <td class="{{ $class }}">{{ $value }}</td>
                    @endforeach
                </tr>
                @endforeach
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
let unit='stb';
const ratesDataOriginal=@json($chartData);
let ratesData=JSON.parse(JSON.stringify(ratesDataOriginal));

const ctx=document.getElementById('ratesChart').getContext('2d');
const ratesChart=new Chart(ctx,{
    type:'line',
    data:ratesData,
    options:{
        responsive:true,
        plugins:{title:{display:true,text:'Production Rates'}},
        scales:{y:{beginAtZero:true,title:{display:true,text:'Rate (STB/d or Mcf/d)'}}}
    }
});

// GOR Chart
const gorCtx=document.getElementById('gorChart').getContext('2d');
const gorData=@json($gorChartData);
const gorChart=new Chart(gorCtx,{
    type:'line',
    data:gorData,
    options:{
        responsive:true,
        plugins:{title:{display:true,text:'Gas-Oil Ratio (GOR)'}},
        scales:{y:{beginAtZero:true,title:{display:true,text:'GOR (Scf/STB)'}}}
    }
});

document.getElementById('unitSelect').addEventListener('change',function(){
    unit=this.value;
    const factor=unit==='m3'?0.158987:1;
    ratesChart.data.datasets.forEach((ds,idx)=>{
        if(idx<2) ds.data=ratesDataOriginal.datasets[idx].data.map(v=>v*factor);
    });
    ratesChart.options.scales.y.title.text=unit==='stb'?'Rate (STB/d or Mcf/d)':'Rate (m³/d or Mcf/d)';
    ratesChart.update();
});

 $(document).ready(function(){
    $('#vxDetailTable').DataTable({scrollX:true,pageLength:10,lengthMenu:[10,25,50,100]});
});
</script>
@endsection
