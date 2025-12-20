@extends('layouts.master')

@section('content')
<div class="container-fluid">

<h2 class="mb-4">Well Test Summary: {{ $wellTest->well ?? 'N/A' }}</h2>

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
                    <td>{{ number_format($wellTest->calculated_metrics['oil_rate_avg'] ?? 0, 2) }}</td>
                    <td>{{ number_format($wellTest->calculated_metrics['water_rate_avg'] ?? 0, 2) }}</td>
                    <td>{{ number_format($wellTest->calculated_metrics['gas_rate_avg'] ?? 0, 2) }}</td>
                    <td>{{ number_format($wellTest->calculated_metrics['total_gor_avg'] ?? 0, 2) }}</td>
                    <td>{{ number_format($wellTest->calculated_metrics['bsw_avg'] ?? 0, 2) }}</td>
                    <td>{{ number_format($wellTest->calculated_metrics['wlr_avg'] ?? 0, 2) }}</td>
                    <td>{{ number_format($wellTest->calculated_metrics['gvf_avg'] ?? 0, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="row">
    <div class="col-6">
        <!-- Production Chart -->
        <div class="card ">
            <div class="card-header">
                <div class="row">
                    <div class="col-6">
                        <h6 class="card-title mb-0">Production Rates Chart</h6>
                    </div>
                    <div class="col-6">
                        <label>Change Unit:</label>
                            <select id="unitSelect" class="form-select form-control w-auto d-inline-block">
                                <option value="stb" selected>STB/d</option>
                                <option value="m3">m³/d</option>
                            </select>
                    </div>
                </div>


            </div>
            <div class="card-body">
                <canvas id="ratesChart" style="height:260px;"></canvas>

            </div>
        </div>
    </div>
    <div class="col-6">
        <!-- GOR Chart -->
        <div class="card mb-4">
            <div class="card-header"><h6 class="card-title mb-0">Gas-Oil Ratio (GOR) Chart</h6></div>
            <div class="card-body">
                <canvas id="gorChart" style="height:250px;"></canvas>
            </div>
        </div>
    </div>
</div>

@if($wellTest->log_file_path)
<!-- Log File Section -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="card-title mb-0">Raw Log File: {{ $wellTest->filename }}</h6>
        <a href="{{ Storage::url($wellTest->log_file_path) }}" class="btn btn-success btn-sm" download>
            <i class="fe fe-download me-1"></i> Download Log File
        </a>
    </div>
    <div class="card-body">
        <pre><code>{{ $logContent ?? 'File content not available.' }}</code></pre>
    </div>
</div>
@endif


<!-- Detailed Table -->
<div class="card mb-4">
    <div class="card-header"><h6 class="card-title mb-0">Detailed VX Log Table</h6></div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped table-sm" id="vxDetailTable">
            <thead>
                <tr>
                    @forelse(($wellTest->test_data['columns'] ?? []) as $col)
                        <th>{{ $col }}</th>
                    @empty
                        <th>No columns available</th>
                    @endforelse
                </tr>
            </thead>
            <tbody>
                @forelse(($wellTest->test_data['rows'] ?? []) as $row)
                <tr>
                    @foreach(($wellTest->test_data['columns'] ?? []) as $col)
                        @php
                        $value = $row[$col] ?? '';
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
                @empty
                    <tr>
                        <td colspan="{{ count($wellTest->test_data['columns'] ?? []) }}" class="text-center">No data available</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
{{-- @dd($chartData) --}}
</div>
@endsection

{{-- === هذا هو القسم الوحيد والصحيح لـ JavaScript === --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    console.log("Page loaded. Initializing charts...");

    // === Production Rates Chart ===
    const ratesDataOriginal = @json($chartData ?? []);
    console.log("Rates Data from PHP:", ratesDataOriginal); // للتحقق من البيانات

    const ratesCtx = document.getElementById('ratesChart');
    console.log("Rates Canvas Element:", ratesCtx); // للتحقق من وجود العنصر

    if (ratesCtx) {
        if (ratesDataOriginal && ratesDataOriginal.labels && ratesDataOriginal.labels.length > 0) {
            console.log("Data is valid, creating chart...");
            const ratesChart = new Chart(ratesCtx.getContext('2d'), {
                type: 'line',
                data: ratesDataOriginal,
                options: {
                    responsive: true,
                    plugins: { title: { display: true, text: 'Production Rates' } },
                    scales: { y: { beginAtZero: true, title: { display: true, text: 'Rate (STB/d or Mcf/d)' } } }
                }
            });
            console.log("Rates chart created successfully.");

            const unitSelect = document.getElementById('unitSelect');
            if (unitSelect) {
                unitSelect.addEventListener('change', function() {
                    console.log("Unit changed to:", this.value);
                    const factor = this.value === 'm3' ? 0.158987 : 1;
                    ratesChart.data.datasets.forEach((ds, idx) => {
                        if (idx < 2) ds.data = ratesDataOriginal.datasets[idx].data.map(v => v * factor);
                    });
                    ratesChart.options.scales.y.title.text = this.value === 'stb' ? 'Rate (STB/d or Mcf/d)' : 'Rate (m³/d or Mcf/d)';
                    ratesChart.update();
                });
            }
        } else {
            console.error("Rates data is empty or invalid.");
        }
    } else {
        console.error("Rates canvas element not found!");
    }

    // === GOR Chart ===
    const gorData = @json($gorChartData ?? []);
    console.log("GOR Data from PHP:", gorData); // للتحقق من البيانات

    const gorCtx = document.getElementById('gorChart');
    console.log("GOR Canvas Element:", gorCtx); // للتحقق من وجود العنصر

    if (gorCtx) {
        if (gorData && gorData.labels && gorData.labels.length > 0) {
            console.log("GOR data is valid, creating chart...");
            const gorChart = new Chart(gorCtx.getContext('2d'), {
                type: 'line',
                data: gorData,
                options: {
                    responsive: true,
                    plugins: { title: { display: true, text: 'Gas-Oil Ratio (GOR)' } },
                    scales: { y: { beginAtZero: true, title: { display: true, text: 'GOR (Scf/STB)' } } }
                }
            });
            console.log("GOR chart created successfully.");
        } else {
            console.error("GOR data is empty or invalid.");
        }
    } else {
        console.error("GOR canvas element not found!");
    }

    // Initialize DataTable
    $('#vxDetailTable').DataTable({scrollX:true,pageLength:10,lengthMenu:[10,25,50,100]});
});
</script>
@endpush
