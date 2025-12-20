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
                    <td>{{ $calculatedMetrics['choke_size']??'N/A' }}</td>
                    <td>{{ $calculatedMetrics['upstream_choke']??'N/A' }}</td>
                    <td>{{ $calculatedMetrics['downstream_choke']??'N/A' }}</td>
                    <td>{{ $startTime?$startTime->format('Y-m-d H:i'):'N/A' }}</td>
                    <td>{{ $endTime?$endTime->format('Y-m-d H:i'):'N/A' }}</td>
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
                    <td>{{ number_format($calculatedMetrics['oil_rate_avg'], 2) }}</td>
                    <td>{{ number_format($calculatedMetrics['water_rate_avg'], 2) }}</td>
                    <td>{{ number_format($calculatedMetrics['gas_rate_avg'], 2) }}</td>
                    <td>{{ number_format($calculatedMetrics['total_gor_avg'], 2) }}</td>
                    <td>{{ number_format($calculatedMetrics['bsw_avg'], 2) }}</td>
                    <td>{{ number_format($calculatedMetrics['wlr_avg'], 2) }}</td>
                    <td>{{ number_format($calculatedMetrics['gvf_avg'], 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="row">
    <div class="col-6">
            <!-- Production Chart -->
            <div class="card mb-4">
                <div class="card-header"><h6 class="card-title mb-0">Production Rates Chart</h6></div>
                <div class="card-body">
                    <canvas id="ratesChart" style="height:200px;"></canvas>
                    <div class="mt-3">
                        <label>Change Unit:</label>
                        <select id="unitSelect" class="form-select w-auto d-inline-block form-control ">
                            <option value="stb" selected>STB/d</option>
                            <option value="m3">m³/d</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    <div class="col-6">
        <!-- GOR Chart -->
        <div class="card mb-4">
            <div class="card-header"><h6 class="card-title mb-0">Gas-Oil Ratio (GOR) Chart</h6></div>
            <div class="card-body">
                <canvas id="gorChart" style="height:200px;"></canvas>
            </div>
        </div>
    </div>
</div>


        <!-- Save Test Form -->
        <div class="card mb-4">
            <div class="card-header"><h6 class="card-title mb-0">Save Test</h6></div>
            <div class="card-body">
               <form action="{{ route('tests.store') }}" method="POST">
                @csrf

                {{-- الحقول المخفية لجميع البيانات --}}
                <input type="hidden" name="client" value="{{ $wellTest->client }}">
                <input type="hidden" name="field" value="{{ $wellTest->field }}">
                <input type="hidden" name="well" value="{{ $wellTest->well }}">
                <input type="hidden" name="wellhead_tower" value="{{ $wellheadTower }}">
                <input type="hidden" name="wellhead_string" value="{{ $wellheadString }}">
                <input type="hidden" name="choke_size" value="{{ $chokeSize }}">
                <input type="hidden" name="upstream_choke" value="{{ $upstreamChoke }}">
                <input type="hidden" name="downstream_choke" value="{{ $downstreamChoke }}">
                <input type="hidden" name="start_time" value="{{ $startTime ? $startTime->toDateTimeString() : '' }}">
                <input type="hidden" name="end_time" value="{{ $endTime ? $endTime->toDateTimeString() : '' }}">
                <input type="hidden" name="filename" value="{{ $filename }}">
                <input type="hidden" name="temp_file_path" value="{{ $tempFilePath }}">

                {{-- تحويل البيانات الكبيرة إلى JSON --}}
                <input type="hidden" name="metadata" value="{{ json_encode($parsedData['metadata']) }}">
                <input type="hidden" name="test_data" value="{{ json_encode($parsedData['test_data']) }}">
                <input type="hidden" name="calculated_metrics" value="{{ json_encode($calculatedMetrics) }}">

                <button type="submit" class="btn btn-primary">
                    <i class="fe fe-save me-2"></i> Save Test to Archive
                </button>
            </form>
            </div>
        </div>





<!-- Detailed Table -->
<div class="card mb-4">
    <div class="card-header"><h6 class="card-title mb-0">Detailed VX Log Table</h6></div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped table-sm" id="vxDetailTable">
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
// تأكد من أن متغيرات البيانات موجودة وليست فارغة
const ratesDataOriginal = @json($chartData);
const gorData = @json($gorChartData);

// تحقق من وجود عنصر ratesChart قبل إنشاء المخطط
const ratesCtx = document.getElementById('ratesChart');
if (ratesCtx) {
    const ratesChart = new Chart(ratesCtx.getContext('2d'), {
        type: 'line',
        data: ratesDataOriginal,
        options: {
            responsive: true,
            plugins: { title: { display: true, text: 'Production Rates' } },
            scales: { y: { beginAtZero: true, title: { display: true, text: 'Rate (STB/d or Mcf/d)' } } }
        }
    });

    // تحقق من وجود عنصر unitSelect قبل إضافة event listener
    const unitSelect = document.getElementById('unitSelect');
    if (unitSelect) {
        unitSelect.addEventListener('change', function() {
            unit = this.value;
            const factor = unit === 'm3' ? 0.158987 : 1;
            ratesChart.data.datasets.forEach((ds, idx) => {
                if (idx < 2) ds.data = ratesDataOriginal.datasets[idx].data.map(v => v * factor);
            });
            ratesChart.options.scales.y.title.text = unit === 'stb' ? 'Rate (STB/d or Mcf/d)' : 'Rate (m³/d or Mcf/d)';
            ratesChart.update();
        });
    }
}

// تحقق من وجود عنصر gorChart قبل إنشاء المخطط
const gorCtx = document.getElementById('gorChart');
if (gorCtx) {
    const gorChart = new Chart(gorCtx.getContext('2d'), {
        type: 'line',
        data: gorData,
        options: {
            responsive: true,
            plugins: { title: { display: true, text: 'Gas-Oil Ratio (GOR)' } },
            scales: { y: { beginAtZero: true, title: { display: true, text: 'GOR (Scf/STB)' } } }
        }
    });
}

 $(document).ready(function() {
    $('#vxDetailTable').DataTable({scrollX:true,pageLength:10,lengthMenu:[10,25,50,100]});
});
</script>
@endsection

{{-- @section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>

<script>
// Enable save button when checkbox is checked
document.getElementById('confirmSave').addEventListener('change', function() {
    document.getElementById('saveTestBtn').disabled = !this.checked;
});

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
@endsection --}}
