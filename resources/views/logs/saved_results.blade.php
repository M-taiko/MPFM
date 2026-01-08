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

<div class="row">
    <!-- Average Production Metrics -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header"><h6 class="card-title mb-0">Average Production Metrics</h6></div>
            <div class="card-body">
                <table class="table table-bordered table-striped w-auto" id="metricsTable">
                    <thead>
                        <tr>
                            <th>Oil Rate</th>
                            <th>Water Rate</th>
                            <th>Gas Rate</th>
                            <th>GOR</th>
                            <th>BSW</th>
                            <th>WLR</th>
                            <th>GVF</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td data-metric="oil_rate">@if(is_numeric($wellTest->calculated_metrics['oil_rate'])){{ number_format($wellTest->calculated_metrics['oil_rate'], 2) }}@else{{ $wellTest->calculated_metrics['oil_rate'] }}@endif</td>
                            <td data-metric="water_rate">@if(is_numeric($wellTest->calculated_metrics['water_rate'])){{ number_format($wellTest->calculated_metrics['water_rate'], 2) }}@else{{ $wellTest->calculated_metrics['water_rate'] }}@endif</td>
                            <td data-metric="gas_rate">@if(is_numeric($wellTest->calculated_metrics['gas_rate'])){{ number_format($wellTest->calculated_metrics['gas_rate'], 2) }}@else{{ $wellTest->calculated_metrics['gas_rate'] }}@endif</td>
                            <td>@if(is_numeric($wellTest->calculated_metrics['gor'])){{ number_format($wellTest->calculated_metrics['gor'], 2) }}@else{{ $wellTest->calculated_metrics['gor'] }}@endif</td>
                            <td>@if(is_numeric($wellTest->calculated_metrics['bsw'])){{ number_format($wellTest->calculated_metrics['bsw'], 2) }}@else{{ $wellTest->calculated_metrics['bsw'] }}@endif</td>
                            <td>@if(is_numeric($wellTest->calculated_metrics['wlr'])){{ number_format($wellTest->calculated_metrics['wlr'], 2) }}@else{{ $wellTest->calculated_metrics['wlr'] }}@endif</td>
                            <td>@if(is_numeric($wellTest->calculated_metrics['gvf'])){{ number_format($wellTest->calculated_metrics['gvf'], 2) }}@else{{ $wellTest->calculated_metrics['gvf'] }}@endif</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Unit selection for oil, water, and gas rates -->
                <div class="mt-3">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="oilUnitSelect" class="form-label">Oil Rate Unit</label>
                            <select id="oilUnitSelect" class="form-select form-control">
                                <option value="stb">STB/d</option>
                                <option value="bbl">bbl/d</option>
                                <option value="m3">m³/d</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="waterUnitSelect" class="form-label">Water Rate Unit</label>
                            <select id="waterUnitSelect" class="form-select form-control">
                                <option value="stb">STB/d</option>
                                <option value="bbl">bbl/d</option>
                                <option value="m3">m³/d</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="gasUnitSelect" class="form-label">Gas Rate Unit</label>
                            <select id="gasUnitSelect" class="form-select form-control">
                                <option value="mmscf">MMScf/d</option>
                                <option value="mcf">Mcf/d</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Derived Metrics Table -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header"><h6 class="card-title mb-0">Derived Metrics</h6></div>
            <div class="card-body">
                <table class="table table-bordered table-striped w-auto">
                    <thead>
                        <tr>
                            <th>Calculated WLR (%)</th>
                            <th>Calculated GOR</th>
                            <th>Calculated BSW (%)</th>
                            <th>Calculated GVF (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>@if(isset($wellTest->calculated_metrics['wlr_formula']) && is_numeric($wellTest->calculated_metrics['wlr_formula'])){{ number_format($wellTest->calculated_metrics['wlr_formula'], 2) }}@else{{ $wellTest->calculated_metrics['wlr_formula'] ?? 'N/A' }}@endif</td>
                            <td>@if(isset($wellTest->calculated_metrics['gor_formula']) && is_numeric($wellTest->calculated_metrics['gor_formula'])){{ number_format($wellTest->calculated_metrics['gor_formula'], 2) }}@else{{ $wellTest->calculated_metrics['gor_formula'] ?? 'N/A' }}@endif</td>
                            <td>@if(isset($wellTest->calculated_metrics['bsw_formula']) && is_numeric($wellTest->calculated_metrics['bsw_formula'])){{ number_format($wellTest->calculated_metrics['bsw_formula'], 2) }}@else{{ $wellTest->calculated_metrics['bsw_formula'] ?? 'N/A' }}@endif</td>
                            <td>@if(isset($wellTest->calculated_metrics['gvf_formula']) && is_numeric($wellTest->calculated_metrics['gvf_formula'])){{ number_format($wellTest->calculated_metrics['gvf_formula'], 2) }}@else{{ $wellTest->calculated_metrics['gvf_formula'] ?? 'N/A' }}@endif</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Pressure and Temperature Metrics -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header"><h6 class="card-title mb-0">Pressure & Temperature Metrics</h6></div>
            <div class="card-body">
                <table class="table table-bordered table-striped w-auto">
                    <thead>
                        <tr>
                            <th>Max Pressure</th>
                            <th>Min Pressure</th>
                            <th>Avg Pressure</th>
                            <th>Max Temperature</th>
                            <th>Min Temperature</th>
                            <th>Avg Temperature</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>@if(isset($wellTest->calculated_metrics['max_pressure']) && is_numeric($wellTest->calculated_metrics['max_pressure'])){{ number_format($wellTest->calculated_metrics['max_pressure'], 2) }}@else{{ $wellTest->calculated_metrics['max_pressure'] ?? 'N/A' }}@endif</td>
                            <td>@if(isset($wellTest->calculated_metrics['min_pressure']) && is_numeric($wellTest->calculated_metrics['min_pressure'])){{ number_format($wellTest->calculated_metrics['min_pressure'], 2) }}@else{{ $wellTest->calculated_metrics['min_pressure'] ?? 'N/A' }}@endif</td>
                            <td>@if(isset($wellTest->calculated_metrics['avg_pressure']) && is_numeric($wellTest->calculated_metrics['avg_pressure'])){{ number_format($wellTest->calculated_metrics['avg_pressure'], 2) }}@else{{ $wellTest->calculated_metrics['avg_pressure'] ?? 'N/A' }}@endif</td>
                            <td>@if(isset($wellTest->calculated_metrics['max_temperature']) && is_numeric($wellTest->calculated_metrics['max_temperature'])){{ number_format($wellTest->calculated_metrics['max_temperature'], 2) }}@else{{ $wellTest->calculated_metrics['max_temperature'] ?? 'N/A' }}@endif</td>
                            <td>@if(isset($wellTest->calculated_metrics['min_temperature']) && is_numeric($wellTest->calculated_metrics['min_temperature'])){{ number_format($wellTest->calculated_metrics['min_temperature'], 2) }}@else{{ $wellTest->calculated_metrics['min_temperature'] ?? 'N/A' }}@endif</td>
                            <td>@if(isset($wellTest->calculated_metrics['avg_temperature']) && is_numeric($wellTest->calculated_metrics['avg_temperature'])){{ number_format($wellTest->calculated_metrics['avg_temperature'], 2) }}@else{{ $wellTest->calculated_metrics['avg_temperature'] ?? 'N/A' }}@endif</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- H2S and Volume Metrics -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header"><h6 class="card-title mb-0">H2S & Volume Metrics</h6></div>
            <div class="card-body">
                <table class="table table-bordered table-striped w-auto">
                    <thead>
                        <tr>
                            <th>H2S Avg</th>
                            <th>H2S Max</th>
                            <th>H2S Min</th>
                            <th>Cumulative Oil (bbl)</th>
                            <th>Cumulative Water (bbl)</th>
                            <th>Cumulative Gas (Mcf)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>@if(isset($wellTest->calculated_metrics['h2s_avg']) && is_numeric($wellTest->calculated_metrics['h2s_avg'])){{ number_format($wellTest->calculated_metrics['h2s_avg'], 2) }}@else{{ $wellTest->calculated_metrics['h2s_avg'] ?? 'N/A' }}@endif</td>
                            <td>@if(isset($wellTest->calculated_metrics['h2s_max']) && is_numeric($wellTest->calculated_metrics['h2s_max'])){{ number_format($wellTest->calculated_metrics['h2s_max'], 2) }}@else{{ $wellTest->calculated_metrics['h2s_max'] ?? 'N/A' }}@endif</td>
                            <td>@if(isset($wellTest->calculated_metrics['h2s_min']) && is_numeric($wellTest->calculated_metrics['h2s_min'])){{ number_format($wellTest->calculated_metrics['h2s_min'], 2) }}@else{{ $wellTest->calculated_metrics['h2s_min'] ?? 'N/A' }}@endif</td>
                            <td>@if(isset($wellTest->calculated_metrics['cumulative_oil_volume']) && is_numeric($wellTest->calculated_metrics['cumulative_oil_volume'])){{ number_format($wellTest->calculated_metrics['cumulative_oil_volume'], 2) }}@else{{ $wellTest->calculated_metrics['cumulative_oil_volume'] ?? 'N/A' }}@endif</td>
                            <td>@if(isset($wellTest->calculated_metrics['cumulative_water_volume']) && is_numeric($wellTest->calculated_metrics['cumulative_water_volume'])){{ number_format($wellTest->calculated_metrics['cumulative_water_volume'], 2) }}@else{{ $wellTest->calculated_metrics['cumulative_water_volume'] ?? 'N/A' }}@endif</td>
                            <td>@if(isset($wellTest->calculated_metrics['cumulative_gas_volume']) && is_numeric($wellTest->calculated_metrics['cumulative_gas_volume'])){{ number_format($wellTest->calculated_metrics['cumulative_gas_volume'], 2) }}@else{{ $wellTest->calculated_metrics['cumulative_gas_volume'] ?? 'N/A' }}@endif</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Safety Issues Summary -->
@if(isset($wellTest->calculated_metrics['safety_issues']) && !empty($wellTest->calculated_metrics['safety_issues']))
<div class="card mb-4">
    <div class="card-header bg-warning"><h6 class="card-title mb-0">Safety Issues Summary</h6></div>
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Issue Type</th>
                    <th>Count</th>
                    <th>Average Value</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($wellTest->calculated_metrics['safety_issues']['voltage_issues']))
                <tr>
                    <td>Voltage Issues</td>
                    <td>{{ count($wellTest->calculated_metrics['safety_issues']['voltage_issues']) }}</td>
                    <td>
                        @php
                            $voltages = array_column($wellTest->calculated_metrics['safety_issues']['voltage_issues'], 'value');
                            $avgVoltage = count($voltages) > 0 ? array_sum($voltages) / count($voltages) : 0;
                        @endphp
                        {{ number_format($avgVoltage, 2) }}
                    </td>
                </tr>
                @endif

                @if(isset($wellTest->calculated_metrics['safety_issues']['pressure_issues']))
                <tr>
                    <td>Pressure Issues</td>
                    <td>{{ count($wellTest->calculated_metrics['safety_issues']['pressure_issues']) }}</td>
                    <td>
                        @php
                            $pressures = array_column($wellTest->calculated_metrics['safety_issues']['pressure_issues'], 'value');
                            $avgPressure = count($pressures) > 0 ? array_sum($pressures) / count($pressures) : 0;
                        @endphp
                        {{ number_format($avgPressure, 2) }}
                    </td>
                </tr>
                @endif

                @if(isset($wellTest->calculated_metrics['safety_issues']['temperature_issues']))
                <tr>
                    <td>Temperature Issues</td>
                    <td>{{ count($wellTest->calculated_metrics['safety_issues']['temperature_issues']) }}</td>
                    <td>
                        @php
                            $temperatures = array_column($wellTest->calculated_metrics['safety_issues']['temperature_issues'], 'value');
                            $avgTemperature = count($temperatures) > 0 ? array_sum($temperatures) / count($temperatures) : 0;
                        @endphp
                        {{ number_format($avgTemperature, 2) }}
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- Production Chart -->
<div class="card mb-4">
    <div class="card-header"><h6 class="card-title mb-0">Production Rates Chart</h6></div>
    <div class="card-body">
        <canvas id="ratesChart" style="height:200px;"></canvas>
        <div class="mt-3">
            <label for="unitSelect" class="form-label">Chart Unit</label>
            <select id="unitSelect" class="form-select form-control w-auto d-inline-block">
                <option value="stb" selected>STB/d</option>
                <option value="m3">m³/d</option>
                <option value="bbl">bbl/d</option>
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
                        // Check for voltage issues
                        if(stripos($col, 'Voltage') !== false && is_numeric($value) && ($value < 100 || $value > 250)) {
                            $class='table-warning';
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
const ratesDataOriginal=@json($rates_chart_data);
let ratesData=JSON.parse(JSON.stringify(ratesDataOriginal));

// Store original metric values
const originalMetrics = {
    oil_rate: {{ is_numeric($wellTest->calculated_metrics['oil_rate'] ?? 0) ? $wellTest->calculated_metrics['oil_rate'] : 0 }},
    water_rate: {{ is_numeric($wellTest->calculated_metrics['water_rate'] ?? 0) ? $wellTest->calculated_metrics['water_rate'] : 0 }},
    gas_rate: {{ is_numeric($wellTest->calculated_metrics['gas_rate'] ?? 0) ? $wellTest->calculated_metrics['gas_rate'] : 0 }}
};

// Conversion factors
const conversionFactors = {
    oil: {
        stb: 1,
        bbl: 1,
        m3: 0.158987
    },
    water: {
        stb: 1,
        bbl: 1,
        m3: 0.158987
    },
    gas: {
        mmscf: 0.001,
        mcf: 1
    }
};

// Update metrics table based on selected units
function updateMetricsTable() {
    const oilUnit = document.getElementById('oilUnitSelect').value;
    const waterUnit = document.getElementById('waterUnitSelect').value;
    const gasUnit = document.getElementById('gasUnitSelect').value;

    // Update oil rate
    const oilFactor = conversionFactors.oil[oilUnit];
    const oilValue = originalMetrics.oil_rate * oilFactor;
    const oilCell = document.querySelector('[data-metric="oil_rate"]');
    if (oilCell && !isNaN(oilValue)) {
        oilCell.textContent = oilValue.toFixed(2);
    }

    // Update water rate
    const waterFactor = conversionFactors.water[waterUnit];
    const waterValue = originalMetrics.water_rate * waterFactor;
    const waterCell = document.querySelector('[data-metric="water_rate"]');
    if (waterCell && !isNaN(waterValue)) {
        waterCell.textContent = waterValue.toFixed(2);
    }

    // Update gas rate
    const gasFactor = conversionFactors.gas[gasUnit];
    const gasValue = originalMetrics.gas_rate * gasFactor;
    const gasCell = document.querySelector('[data-metric="gas_rate"]');
    if (gasCell && !isNaN(gasValue)) {
        gasCell.textContent = gasValue.toFixed(2);
    }
}

// Add event listeners to unit selectors
document.getElementById('oilUnitSelect').addEventListener('change', updateMetricsTable);
document.getElementById('waterUnitSelect').addEventListener('change', updateMetricsTable);
document.getElementById('gasUnitSelect').addEventListener('change', updateMetricsTable);

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
const gorData=@json($gor_chart_data);
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
    const factor=unit==='m3'?0.158987:(unit==='bbl'?1:1);
    ratesChart.data.datasets.forEach((ds,idx)=>{
        if(unit==='bbl' && ds.label.includes('STB/d')) {
            ds.label = ds.label.replace('STB/d', 'bbl/d');
        } else if(unit==='stb' && ds.label.includes('bbl/d')) {
            ds.label = ds.label.replace('bbl/d', 'STB/d');
        } else if(unit==='m3' && (ds.label.includes('STB/d') || ds.label.includes('bbl/d'))) {
            ds.label = ds.label.replace('STB/d', 'm³/d').replace('bbl/d', 'm³/d');
        }
        if(idx<2) ds.data=ratesDataOriginal.datasets[idx].data.map(v=>v*factor);
    });
    ratesChart.options.scales.y.title.text=unit==='stb'?'Rate (STB/d or Mcf/d)':(unit==='m3'?'Rate (m³/d or Mcf/d)':'Rate (bbl/d or Mcf/d)');
    ratesChart.update();
});

$(document).ready(function(){
    $('#vxDetailTable').DataTable({scrollX:true,pageLength:10,lengthMenu:[10,25,50,100]});
});
</script>
@endsection
