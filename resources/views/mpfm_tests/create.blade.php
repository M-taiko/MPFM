@extends('layouts.master')

@section('title', 'Create MPFM Test - Enhanced Log Processing')

@push('styles')
<style>
/* ===== Styles مختصرة وواضحة ===== */
.upload-zone {
    border: 3px dashed #cbd5e0; border-radius: 12px; padding: 3rem; text-align: center;
    background: #f7fafc; cursor: pointer; transition: all 0.3s;
}
.upload-zone:hover { border-color: #4299e1; background: #ebf8ff; }
.upload-zone.dragover { border-color: #3182ce; background: #bee3f8; }
.upload-zone.success { border-color: #48bb78; background: #f0fff4; }

.file-info { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1rem; margin-top: 1rem; }

.processing-indicator { background: #fff; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }

.chart-container { position: relative; height: 400px; margin-bottom: 2rem; }

.kpi-card { background: #fff; border-radius: 8px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 1rem; }
.kpi-label { font-weight: 600; color: #4a5568; margin-bottom: 0.5rem; }
.kpi-value { font-size: 1.5rem; font-weight: 700; color: #2d3748; }

.raw-data-preview { background: #2d3748; color: #e2e8f0; padding: 1rem; border-radius: 8px;
    font-family: 'Courier New', monospace; font-size: 0.875rem; overflow-x: auto; max-height: 400px; overflow-y: auto; }

.step-indicator { display: flex; justify-content: space-between; margin-bottom: 2rem; position: relative; }
.step { flex: 1; text-align: center; padding: 1rem; position: relative; }
.step::before { content: ''; position: absolute; top: 20px; left: -50%; right: 50%; height: 2px; background: #e2e8f0; z-index: -1; }
.step:first-child::before { display: none; }
.step.active::before { background: #4299e1; }
.step-number { display: inline-block; width: 40px; height: 40px; line-height: 40px; border-radius: 50%; background: #e2e8f0; color: #4a5568; font-weight: 600; margin-bottom: 0.5rem; }
.step.active .step-number { background: #4299e1; color: #fff; }
.step.completed .step-number { background: #48bb78; color: #fff; }

.alert-floating { position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 500px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">

    <!-- Header & Breadcrumb -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-2">Create MPFM Test - Enhanced Log Processing</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('mpfm_tests.index') }}">MPFM Tests</a></li>
                    <li class="breadcrumb-item active">Create with Log Processing</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Step Indicator -->
    <div class="step-indicator">
        <div class="step active" id="step1Indicator"><div class="step-number">1</div><div class="step-title">Upload Log</div></div>
        <div class="step" id="step2Indicator"><div class="step-number">2</div><div class="step-title">Review Data</div></div>
        <div class="step" id="step3Indicator"><div class="step-number">3</div><div class="step-title">Save Test</div></div>
    </div>

    <!-- Step 1: Upload -->
    <div id="uploadSection" class="card shadow-sm mb-4">
        <div class="card-body">
            <h4 class="card-title mb-4">Step 1: Upload & Process Log File</h4>
            <div id="uploadZone" class="upload-zone">
                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                <h5>Drop MPFM log file here or click to browse</h5>
                <p class="text-muted">Supported formats: .txt, .vxlog, .log, .csv (Max 10MB)</p>
                <input type="file" id="logFileInput" accept=".txt,.vxlog,.log,.csv" style="display: none;">
            </div>
            <div id="fileInfo" class="file-info" style="display: none;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-file-alt text-primary mr-2"></i>
                        <strong>Selected File:</strong> <span id="fileName"></span>
                        <span class="badge badge-secondary ml-2" id="fileSize"></span>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearFile()">
                        <i class="fas fa-times"></i> Remove
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Processing Indicator -->
    <div id="processingIndicator" class="processing-indicator text-center" style="display: none;">
        <div class="spinner-border text-primary mb-3" role="status"><span class="sr-only">Processing...</span></div>
        <h5>Processing Log File...</h5>
        <p class="text-muted">Extracting MPFM data and generating visualizations</p>
        <div class="progress" style="height: 25px;">
            <div id="processingProgress" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
        </div>
    </div>

    <!-- Step 2: Results -->
    <div id="resultsSection" style="display: none;">
        <ul class="nav nav-tabs mb-4" role="tablist">
            <li class="nav-item"><a class="nav-link active" id="kpi-tab" data-toggle="tab" href="#kpiPane"><i class="fas fa-chart-line"></i> KPIs & Data</a></li>
            <li class="nav-item"><a class="nav-link" id="charts-tab" data-toggle="tab" href="#chartsPane"><i class="fas fa-chart-area"></i> Charts</a></li>
            <li class="nav-item"><a class="nav-link" id="raw-tab" data-toggle="tab" href="#rawPane"><i class="fas fa-file-code"></i> Raw Data</a></li>
            <li class="nav-item"><a class="nav-link" id="save-tab" data-toggle="tab" href="#savePane"><i class="fas fa-save"></i> Save Test</a></li>
        </ul>

        <div class="tab-content">
            <!-- KPIs -->
            <div class="tab-pane fade show active" id="kpiPane">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white"><h5 class="mb-0">Extracted MPFM Parameters</h5></div>
                    <div class="card-body">
                        <table class="table table-hover"><tbody id="kpiTableBody"></tbody></table>
                    </div>
                </div>
            </div>

            <!-- Charts -->
            <div class="tab-pane fade" id="chartsPane">
                <div class="row">
                    <div class="col-lg-6 mb-4"><div class="card shadow-sm"><div class="card-header bg-info text-white"><h6 class="mb-0">Production Rates</h6></div><div class="card-body"><div class="chart-container"><canvas id="productionChart"></canvas></div></div></div></div>
                    <div class="col-lg-6 mb-4"><div class="card shadow-sm"><div class="card-header bg-info text-white"><h6 class="mb-0">GOR</h6></div><div class="card-body"><div class="chart-container"><canvas id="gorChart"></canvas></div></div></div></div>
                    <div class="col-lg-6 mb-4" id="waterCutChartContainer" style="display:none;"><div class="card shadow-sm"><div class="card-header bg-info text-white"><h6 class="mb-0">Water Cut</h6></div><div class="card-body"><div class="chart-container"><canvas id="waterCutChart"></canvas></div></div></div></div>
                    <div class="col-lg-6 mb-4" id="temperatureChartContainer" style="display:none;"><div class="card shadow-sm"><div class="card-header bg-info text-white"><h6 class="mb-0">Temperature</h6></div><div class="card-body"><div class="chart-container"><canvas id="temperatureChart"></canvas></div></div></div></div>
                    <div class="col-lg-6 mb-4" id="pressureChartContainer" style="display:none;"><div class="card shadow-sm"><div class="card-header bg-info text-white"><h6 class="mb-0">Pressure</h6></div><div class="card-body"><div class="chart-container"><canvas id="pressureChart"></canvas></div></div></div></div>
                </div>
            </div>

            <!-- Raw Data -->
            <div class="tab-pane fade" id="rawPane"><div class="card shadow-sm"><div class="card-header bg-dark text-white"><h5 class="mb-0">Raw Log File Content</h5></div><div class="card-body p-0"><pre id="rawDataPreview" class="raw-data-preview mb-0"></pre></div></div></div>

            <!-- Save Test -->
            <div class="tab-pane fade" id="savePane">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white"><h5 class="mb-0">Step 3: Save MPFM Test</h5></div>
                    <div class="card-body">
                        <form id="mpfmTestForm" method="POST" action="{{ route('mpfm_tests.store') }}">
                            @csrf
                            <input type="hidden" name="temp_file_path" id="tempFilePath">
                            <input type="hidden" name="mpfm_data" id="mpfmDataInput">
                            <input type="hidden" name="parsed_data" id="parsedDataInput">
                            <input type="hidden" name="processing_result" id="processingResultInput">

                            <div class="row">
                                <div class="col-md-6 mb-3"><label for="testDateInput">Test Date *</label><input type="date" class="form-control" id="testDateInput" name="test_date" required></div>
                                <div class="col-md-6 mb-3"><label for="shiftInput">Shift *</label><select class="form-control" id="shiftInput" name="shift" required><option value="morning">Morning</option><option value="afternoon">Afternoon</option><option value="night">Night</option></select></div>
                            </div>
                            <div class="mb-3"><label for="testAliasInput">Test Alias (Optional)</label><input type="text" class="form-control" id="testAliasInput" name="test_alias" placeholder="e.g., Monthly Production Test"></div>
                            <div class="mb-4"><label for="notesInput">Notes</label><textarea class="form-control" id="notesInput" name="notes" rows="3" placeholder="Additional test notes or observations"></textarea></div>

                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-outline-secondary" onclick="resetForm()"><i class="fas fa-redo"></i> Start Over</button>
                                <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-save"></i> Save MPFM Test</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
let selectedFile = null, processedData = null, charts = {};

document.addEventListener('DOMContentLoaded', function() {
    initializeUploadZone();
    initializeFormValidation();
});

// === Upload Zone ===
function initializeUploadZone() {
    const uploadZone = document.getElementById('uploadZone');
    const fileInput = document.getElementById('logFileInput');

    uploadZone.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', e => { if(e.target.files[0]) handleFileSelection(e.target.files[0]); });

    ['dragenter','dragover','dragleave','drop'].forEach(ev => uploadZone.addEventListener(ev, e => { e.preventDefault(); e.stopPropagation(); }, false));
    ['dragenter','dragover'].forEach(ev => uploadZone.addEventListener(ev, () => uploadZone.classList.add('dragover')));
    ['dragleave','drop'].forEach(ev => uploadZone.addEventListener(ev, () => uploadZone.classList.remove('dragover')));
    uploadZone.addEventListener('drop', e => { if(e.dataTransfer.files.length>0) handleFileSelection(e.dataTransfer.files[0]); }, false);
}

function handleFileSelection(file) {
    const allowed = ['.txt','.vxlog','.log','.csv'];
    const ext = file.name.slice(file.name.lastIndexOf('.')).toLowerCase();
    if(!allowed.includes(ext)){ showAlert('Please select a valid log file','danger'); return; }
    if(file.size>10*1024*1024){ showAlert('File size must be <10MB','danger'); return; }

    selectedFile=file;
    document.getElementById('fileName').textContent=file.name;
    document.getElementById('fileSize').textContent=(file.size/1024/1024).toFixed(2)+' MB';
    document.getElementById('fileInfo').style.display='block';
    document.getElementById('uploadZone').classList.add('success');
    if(!document.getElementById('processBtn')){
        const btn=document.createElement('button'); btn.id='processBtn'; btn.className='btn btn-primary mt-3'; btn.innerHTML='<i class="fas fa-cog"></i> Process Log File';
        btn.onclick=e=>{ e.stopPropagation(); processLogFile(); }; document.getElementById('uploadZone').appendChild(btn);
    }
}

function clearFile(){
    selectedFile=null; document.getElementById('logFileInput').value='';
    document.getElementById('fileInfo').style.display='none'; document.getElementById('uploadZone').classList.remove('success');
    const btn=document.getElementById('processBtn'); if(btn) btn.remove();
}

// === Processing ===
function processLogFile(){
    if(!selectedFile){ showAlert('Please select a log file first','warning'); return; }
    document.getElementById('processingIndicator').style.display='block';
    document.getElementById('uploadSection').style.display='none';

    const formData=new FormData(); formData.append('logfile',selectedFile); formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    let progress=0; const interval=setInterval(()=>{ progress+=Math.random()*20; if(progress>90) progress=90; document.getElementById('processingProgress').style.width=progress+'%'; },300);

    fetch('{{ route("mpfm_tests.process_upload") }}',{ method:'POST', body:formData })
    .then(r=>r.json())
    .then(data=>{
        clearInterval(interval); document.getElementById('processingProgress').style.width='100%';
        setTimeout(()=>{
            document.getElementById('processingIndicator').style.display='none';
            if(data.success){ processedData=data.data; displayResults(data.data); document.getElementById('resultsSection').style.display='block'; updateStepIndicator(2); document.getElementById('kpi-tab').click(); showAlert('Log processed successfully!','success'); }
            else { showAlert('Error processing log: '+data.error,'danger'); document.getElementById('uploadSection').style.display='block'; }
        },500);
    }).catch(err=>{ clearInterval(interval); document.getElementById('processingIndicator').style.display='none'; document.getElementById('uploadSection').style.display='block'; showAlert('Error processing log','danger'); console.error(err); });
}

// === Display Results ===
function displayResults(data){ displayKPIs(data); initializeCharts(data); displayRawData(data); autoFillForm(data); }

function displayKPIs(data){
    const mpfm = data.processed_data?.mpfm_data || data.mpfm_data || {};
    const tbody=document.getElementById('kpiTableBody');
    const rows=[
        ['Test Date', mpfm.test_date||'N/A'],
        ['Shift', mpfm.shift?.charAt(0).toUpperCase()+mpfm.shift?.slice(1)||'N/A'],
        ['Oil Rate (STB/day)', mpfm.oil_rate||'N/A'],
        ['Gas Rate (MMSCFD)', mpfm.gas_rate||'N/A'],
        ['Water Rate (STB/day)', mpfm.water_rate||'N/A'],
        ['GOR (scf/STB)', mpfm.gor||'N/A'],
        ['Water Cut (%)', mpfm.water_cut||'N/A'],
        ['Temperature (°F)', mpfm.temperature||'N/A'],
        ['Pressure (psi)', mpfm.pressure||'N/A']
    ];
    tbody.innerHTML=''; rows.forEach(r=>{ const tr=document.createElement('tr'); tr.innerHTML=`<th>${r[0]}</th><td>${r[1]}</td>`; tbody.appendChild(tr); });
}

function displayRawData(data){ document.getElementById('rawDataPreview').textContent=data.raw_data||''; }

function autoFillForm(data){
    const mpfm = data.processed_data?.mpfm_data || data.mpfm_data || {};
    if(mpfm.test_date) document.getElementById('testDateInput').value=mpfm.test_date;
    if(mpfm.shift) document.getElementById('shiftInput').value=mpfm.shift;
    document.getElementById('tempFilePath').value=data.temp_file_path||'';
    document.getElementById('mpfmDataInput').value=JSON.stringify(mpfm);
    document.getElementById('parsedDataInput').value=JSON.stringify(data.processed_data||{});
    document.getElementById('processingResultInput').value=JSON.stringify(data);
}

// === Charts ===
function initializeCharts(data){
    const chartData = data.processed_data?.chart_data || data.chart_data;
    if(!chartData) return;
    Object.values(charts).forEach(c=>{ if(c) c.destroy(); }); charts={};

    const chartConfigs = [
        { id: 'productionChart', container: null, data: chartData.production_comparison_chart, yLabel: 'Rate', displayContainer: false },
        { id: 'gorChart', container: null, data: chartData.gor_chart, yLabel: 'GOR (scf/STB)', displayContainer: false },
        { id: 'waterCutChart', container: 'waterCutChartContainer', data: chartData.water_cut_chart, yLabel: 'Water Cut (%)', displayContainer: true },
        { id: 'temperatureChart', container: 'temperatureChartContainer', data: chartData.temperature_chart, yLabel: 'Temperature (°F)', displayContainer: true },
        { id: 'pressureChart', container: 'pressureChartContainer', data: chartData.pressure_chart, yLabel: 'Pressure (psi)', displayContainer: true },
    ];

    chartConfigs.forEach(cfg=>{
        if(cfg.data?.datasets && cfg.data.datasets.length>0){
            const ctx=document.getElementById(cfg.id);
            const container=cfg.container?document.getElementById(cfg.container):null;
            if(ctx){
                if(container && cfg.displayContainer) container.style.display='block';
                charts[cfg.id]=new Chart(ctx,{
                    type:'line',
                    data:cfg.data,
                    options:{
                        responsive:true, maintainAspectRatio:false,
                        plugins:{ legend:{display:true,position:'top'}, tooltip:{mode:'index',intersect:false} },
                        scales:{ x:{ title:{display:true,text:'Time'}, ticks:{maxTicksLimit:10} }, y:{ title:{display:true,text:cfg.yLabel}, beginAtZero:true } }
                    }
                });
            }
        }
    });
}

// === Alerts ===
function showAlert(msg,type='info'){
    const alertDiv=document.createElement('div'); alertDiv.className=`alert alert-${type} alert-floating shadow`; alertDiv.innerHTML=msg;
    document.body.appendChild(alertDiv); setTimeout(()=>alertDiv.remove(),4000);
}

// === Step Indicator ===
function updateStepIndicator(step){
    for(let i=1;i<=3;i++){ const el=document.getElementById('step'+i+'Indicator'); el.classList.remove('active'); el.classList.remove('completed'); if(i<step) el.classList.add('completed'); if(i===step) el.classList.add('active'); }
}

// === Reset ===
function resetForm(){ location.reload(); }

function initializeFormValidation(){ const form=document.getElementById('mpfmTestForm'); form.addEventListener('submit', e=>{ if(!selectedFile){ e.preventDefault(); showAlert('Please upload and process a log file before saving.','warning'); } }); }

</script>
@endpush
