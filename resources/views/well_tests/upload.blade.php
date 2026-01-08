@extends('layouts.master')

@section('title', 'Upload Well Test Log File')

@push('styles')
<style>
/* ==========================================================================
   Well Test Log Upload - Enhanced Styles
   ========================================================================== */

/* Upload Zone */
.upload-zone {
    border: 3px dashed #4e73df;
    border-radius: 15px;
    padding: 3rem 2rem;
    text-align: center;
    background: linear-gradient(135deg, rgba(78, 115, 223, 0.1) 0%, rgba(78, 115, 223, 0.05) 100%);
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.upload-zone:hover {
    border-color: #224abe;
    background: linear-gradient(135deg, rgba(78, 115, 223, 0.15) 0%, rgba(78, 115, 223, 0.1) 100%);
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(78, 115, 223, 0.2);
}

.upload-zone.dragover {
    border-color: #28a745;
    background: linear-gradient(135deg, rgba(40, 167, 69, 0.15) 0%, rgba(40, 167, 69, 0.1) 100%);
    box-shadow: 0 0 20px rgba(40, 167, 69, 0.3);
}

.upload-zone .upload-icon {
    font-size: 4rem;
    color: #4e73df;
    margin-bottom: 1rem;
}

.upload-zone h4 {
    color: #4e73df;
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.upload-zone p {
    color: #6c757d;
    margin-bottom: 0;
}

/* File Info */
.file-info {
    background: rgba(40, 167, 69, 0.1);
    border: 1px solid rgba(40, 167, 69, 0.2);
    border-radius: 10px;
    padding: 1rem;
    margin-top: 1rem;
}

.file-info .file-name {
    font-weight: 600;
    color: #28a745;
}

.file-info .file-size {
    color: #6c757d;
    font-size: 0.9rem;
}

/* Processing Indicator */
.processing-indicator {
    text-align: center;
    padding: 3rem 2rem;
}

.processing-spinner {
    width: 4rem;
    height: 4rem;
    border: 4px solid rgba(78, 115, 223, 0.3);
    border-top: 4px solid #4e73df;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 1rem;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Results Section */
.results-section {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    margin-top: 2rem;
}

/* KPI Display */
.kpi-display {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 10px;
    padding: 1.5rem;
    margin-bottom: 1rem;
}

.kpi-display h5 {
    color: #495057;
    margin-bottom: 1rem;
    font-weight: 600;
}

.kpi-table {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.kpi-table th {
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    color: white;
    padding: 10px;
    border: none;
}

.kpi-table td {
    padding: 10px;
    border: none;
    vertical-align: middle;
}

.kpi-table tr:nth-child(even) {
    background: rgba(78, 115, 223, 0.05);
}

/* Chart Containers */
.chart-container {
    position: relative;
    height: 300px;
    width: 100%;
    margin-bottom: 1rem;
}

/* Form Styling */
.asset-form {
    background: rgba(248, 249, 250, 0.8);
    border-radius: 10px;
    padding: 2rem;
    margin-top: 1rem;
}

/* Step Navigation */
.step-indicator {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 2rem;
}

.step {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e9ecef;
    color: #6c757d;
    font-weight: 600;
    margin: 0 0.5rem;
    transition: all 0.3s ease;
}

.step.active {
    background: #4e73df;
    color: white;
}

.step.completed {
    background: #28a745;
    color: white;
}

.step-line {
    width: 60px;
    height: 2px;
    background: #e9ecef;
    margin: 0 0.5rem;
}

.step-line.completed {
    background: #28a745;
}

/* Responsive */
@media (max-width: 768px) {
    .upload-zone {
        padding: 2rem 1rem;
    }

    .upload-zone .upload-icon {
        font-size: 3rem;
    }

    .processing-indicator {
        padding: 2rem 1rem;
    }

    .results-section {
        padding: 1rem;
    }

    .step-indicator {
        flex-direction: column;
    }

    .step-line {
        width: 2px;
        height: 40px;
        margin: 0.5rem 0;
    }
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-leftheader">
            <h4 class="page-title">
                <i class="fe fe-upload mr-2"></i>Upload Well Test Log File
            </h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('well_tests.index') }}">Well Tests</a></li>
                <li class="breadcrumb-item active">Upload Log</li>
            </ol>
        </div>
        <div class="page-rightheader">
            <a href="{{ route('well_tests.index') }}" class="btn btn-secondary">
                <i class="fe fe-arrow-left mr-1"></i>Back to Tests
            </a>
        </div>
    </div>

    <!-- Step Indicator -->
    <div class="step-indicator">
        <div class="step active" id="step1">1</div>
        <div class="step-line" id="line1"></div>
        <div class="step" id="step2">2</div>
        <div class="step-line" id="line2"></div>
        <div class="step" id="step3">3</div>
    </div>

    <!-- Step 1: File Upload -->
    <div id="uploadSection" class="step-content">
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-10">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="fe fe-file mr-2"></i>Step 1: Upload Log File
                        </h4>
                    </div>
                    <div class="card-body">
                        <div id="uploadZone" class="upload-zone">
                            <i class="fe fe-upload-cloud upload-icon"></i>
                            <h4>Drop your log file here</h4>
                            <p>or click to browse. Supports DAFC, MPFM, and other well test formats (.vxlog, .log, .csv, .txt)</p>
                            <input type="file" id="logFileInput" accept=".vxlog,.log,.csv,.txt,.dat" style="display: none;">
                        </div>

                        <div id="fileInfo" class="file-info" style="display: none;">
                            <div class="d-flex align-items-center">
                                <i class="fe fe-file mr-3 text-success" style="font-size: 1.5rem;"></i>
                                <div>
                                    <div class="file-name" id="fileName">No file selected</div>
                                    <div class="file-size" id="fileSize">0 KB</div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger ms-auto" id="removeFile">
                                    <i class="fe fe-x"></i>
                                </button>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button type="button" id="processButton" class="btn btn-primary btn-lg" disabled>
                                <i class="fe fe-play mr-2"></i>Process Log File
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 2: Processing Indicator -->
    <div id="processingSection" class="step-content" style="display: none;">
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <div class="processing-indicator">
                            <div class="processing-spinner"></div>
                            <h4>Processing Log File</h4>
                            <p class="text-muted">Analyzing data, calculating metrics, and generating charts...</p>
                            <div class="progress mt-3">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 100%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 3: Results and Form -->
    <div id="resultsSection" class="step-content" style="display: none;">
        <div class="results-section">
            <!-- Navigation Tabs -->
            <ul class="nav nav-tabs" id="resultsTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="kpis-tab" data-bs-toggle="tab" data-bs-target="#kpis" type="button" role="tab">
                        <i class="fe fe-bar-chart mr-1"></i>KPIs & Data
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="charts-tab" data-bs-toggle="tab" data-bs-target="#charts" type="button" role="tab">
                        <i class="fe fe-trending-up mr-1"></i>Charts
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="raw-tab" data-bs-toggle="tab" data-bs-target="#raw" type="button" role="tab">
                        <i class="fe fe-file-text mr-1"></i>Raw Data
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="save-tab" data-bs-toggle="tab" data-bs-target="#save" type="button" role="tab">
                        <i class="fe fe-save mr-1"></i>Save Test
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content mt-4" id="resultsContent">
                <!-- KPIs & Data Tab -->
                <div class="tab-pane fade show active" id="kpis" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="kpi-display">
                                <h5>Test Information</h5>
                                <table class="table table-sm kpi-table">
                                    <tr><th width="40%">Filename:</th><td id="displayFilename">N/A</td></tr>
                                    <tr><th>File Size:</th><td id="displayFileSize">N/A</td></tr>
                                    <tr><th>Test Date:</th><td id="displayTestDate">N/A</td></tr>
                                    <tr><th>Shift:</th><td id="displayShift">N/A</td></tr>
                                    <tr><th>Duration:</th><td id="displayDuration">N/A</td></tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="kpi-display">
                                <h5>Production Data</h5>
                                <table class="table table-sm kpi-table">
                                    <tr class="table-warning"><th>Oil Rate:</th><td id="displayOilRate">N/A</td></tr>
                                    <tr class="table-warning"><th>Gas Rate:</th><td id="displayGasRate">N/A</td></tr>
                                    <tr class="table-warning"><th>Water Rate:</th><td id="displayWaterRate">N/A</td></tr>
                                    <tr class="table-warning"><th>Water Cut:</th><td id="displayWaterCut">N/A</td></tr>
                                    <tr><th>Pressure:</th><td id="displayPressure">N/A</td></tr>
                                    <tr><th>Temperature:</th><td id="displayTemperature">N/A</td></tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="kpi-display">
                                <h5>Calculated KPIs</h5>
                                <table class="table table-sm kpi-table">
                                    <tr class="table-warning"><th>GOR (Gas-Oil Ratio):</th><td id="displayGOR">N/A</td></tr>
                                    <tr class="table-warning"><th>WLR (Water-Liquid Ratio):</th><td id="displayWLR">N/A</td></tr>
                                    <tr class="table-warning"><th>BSW (Basic Sediment & Water):</th><td id="displayBSW">N/A</td></tr>
                                    <tr><th>H2S Concentration:</th><td id="displayH2S">N/A</td></tr>
                                    <tr><th>CO2 Percentage:</th><td id="displayCO2">N/A</td></tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Tab -->
                <div class="tab-pane fade" id="charts" role="tabpanel">
                    <div class="row">
                        <div class="col-xl-12 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Production Rates</h5>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="productionChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Gas-Oil Ratio (GOR)</h5>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="gorChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Water Cut (WLR)</h5>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="waterCutChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Temperature</h5>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="temperatureChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Pressure</h5>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="pressureChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Raw Data Tab -->
                <div class="tab-pane fade" id="raw" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            <pre id="rawDataContent" class="bg-light p-3 rounded" style="max-height: 500px; overflow-y: auto; font-size: 0.85rem;">No data available</pre>
                        </div>
                    </div>
                </div>

                <!-- Save Test Tab -->
                <div class="tab-pane fade" id="save" role="tabpanel">
                    <div class="asset-form">
                        <h4 class="mb-4">
                            <i class="fe fe-save mr-2"></i>Save Well Test
                        </h4>

                        <form id="saveWellTestForm" action="{{ route('well_tests.store_from_log') }}" method="POST">
                            @csrf
                            <input type="hidden" name="processing_result" id="processingResultInput">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="client">Client *</label>
                                        <input type="text" class="form-control" id="client" name="client" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="field">Field *</label>
                                        <input type="text" class="form-control" id="field" name="field" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="well">Well *</label>
                                        <input type="text" class="form-control" id="well" name="well" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="operator">Operator *</label>
                                        <input type="text" class="form-control" id="operator" name="operator" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="test_date">Test Date *</label>
                                        <input type="date" class="form-control" id="test_date" name="test_date" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="test_type">Test Type</label>
                                        <select class="form-control" id="test_type" name="test_type">
                                            <option value="">Select Type</option>
                                            <option value="DAFC">DAFC</option>
                                            <option value="MPFM">MPFM</option>
                                            <option value="Well_Test">Well Test</option>
                                            <option value="Production_Test">Production Test</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="notes">Notes</label>
                                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Add any additional notes about this test..."></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fe fe-save mr-2"></i>Save Well Test
                                </button>
                                <button type="button" class="btn btn-secondary btn-lg ms-2" onclick="resetForm()">
                                    <i class="fe fe-refresh mr-2"></i>Start Over
                                </button>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let selectedFile = null;
let processingResult = null;

// Step management
function goToStep(step) {
    // Hide all steps
    document.querySelectorAll('.step-content').forEach(el => el.style.display = 'none');

    // Update step indicators
    document.querySelectorAll('.step').forEach((el, index) => {
        if (index + 1 < step) {
            el.classList.add('completed');
            el.classList.remove('active');
        } else if (index + 1 === step) {
            el.classList.add('active');
            el.classList.remove('completed');
        } else {
            el.classList.remove('active', 'completed');
        }
    });

    document.querySelectorAll('.step-line').forEach((el, index) => {
        if (index + 1 < step) {
            el.classList.add('completed');
        } else {
            el.classList.remove('completed');
        }
    });

    // Show current step
    document.getElementById('step' + step + 'Section').style.display = 'block';
}

// File upload handling
document.addEventListener('DOMContentLoaded', function() {
    const uploadZone = document.getElementById('uploadZone');
    const fileInput = document.getElementById('logFileInput');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const processButton = document.getElementById('processButton');
    const removeFile = document.getElementById('removeFile');

    // Upload zone events
    uploadZone.addEventListener('click', () => fileInput.click());

    uploadZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadZone.classList.add('dragover');
    });

    uploadZone.addEventListener('dragleave', () => {
        uploadZone.classList.remove('dragover');
    });

    uploadZone.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadZone.classList.remove('dragover');

        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleFileSelection(files[0]);
        }
    });

    // File input change
    fileInput.addEventListener('change', (e) => {
        if (e.target.files[0]) {
            handleFileSelection(e.target.files[0]);
        }
    });

    // Remove file
    removeFile.addEventListener('click', () => {
        selectedFile = null;
        fileInput.value = '';
        fileInfo.style.display = 'none';
        processButton.disabled = true;
        updateUploadZone();
    });

    // Process button
    processButton.addEventListener('click', processLogFile);

    function handleFileSelection(file) {
        selectedFile = file;
        fileName.textContent = file.name;
        fileSize.textContent = formatFileSize(file.size);
        fileInfo.style.display = 'block';
        processButton.disabled = false;
        updateUploadZone();
    }

    function updateUploadZone() {
        const hasFile = selectedFile !== null;
        uploadZone.style.display = hasFile ? 'none' : 'block';
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
});

// Process log file
function processLogFile() {
    if (!selectedFile) return;

    goToStep(2); // Show processing

    const formData = new FormData();
    formData.append('logfile', selectedFile);
    formData.append('_token', '{{ csrf_token() }}');

    fetch('{{ route("well_tests.process_log") }}', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            processingResult = data.data;
            displayResults(data.data);
            goToStep(3); // Show results
        } else {
            alert('Error processing file: ' + data.error);
            goToStep(1); // Back to upload
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while processing the file.');
        goToStep(1); // Back to upload
    });
}

// Display processed results
function displayResults(data) {
    // Update KPI table
    updateKPIValues(data.processed_data || {});

    // Update raw data
    document.getElementById('rawDataContent').textContent = data.raw_log_content || 'No raw data available';

    // Generate and display charts
    generateCharts(data.processed_data || {});

    // Store processing result for form submission
    document.getElementById('processingResultInput').value = JSON.stringify(data);
}

function updateKPIValues(processedData) {
    const averages = processedData.averages || {};
    const mpfmData = processedData.mpfm_data || {};

    // Test info
    document.getElementById('displayFilename').textContent = processingResult.filename || 'N/A';
    document.getElementById('displayFileSize').textContent = formatFileSize(processingResult.file_size) || 'N/A';
    document.getElementById('displayTestDate').textContent = mpfmData.test_date || 'N/A';
    document.getElementById('displayShift').textContent = mpfmData.shift || 'N/A';
    document.getElementById('displayDuration').textContent = mpfmData.duration || 'N/A';

    // Production data
    document.getElementById('displayOilRate').textContent = formatValue(averages.oil_rate_avg, 'STB/d');
    document.getElementById('displayGasRate').textContent = formatValue(averages.gas_rate_avg, 'Mscf/d');
    document.getElementById('displayWaterRate').textContent = formatValue(averages.water_rate_avg, 'STB/d');
    document.getElementById('displayWaterCut').textContent = formatValue(averages.water_cut_avg, '%');
    document.getElementById('displayPressure').textContent = formatValue(averages.pressure_avg, 'psi');
    document.getElementById('displayTemperature').textContent = formatValue(averages.temperature_avg, '°F');

    // Calculated KPIs
    document.getElementById('displayGOR').textContent = formatValue(averages.gor_avg, 'scf/STB');
    document.getElementById('displayWLR').textContent = formatValue(averages.wlr_avg, '%');
    document.getElementById('displayBSW').textContent = formatValue(averages.bsw_avg, '%');
    document.getElementById('displayH2S').textContent = formatValue(averages.h2s_avg, 'ppm');
    document.getElementById('displayCO2').textContent = formatValue(averages.co2_avg, '%');
}

function formatValue(value, unit = '') {
    if (value === null || value === undefined || value === '') return 'N/A';
    return Number(value).toFixed(2) + (unit ? ' ' + unit : '');
}

function formatFileSize(bytes) {
    if (!bytes) return 'N/A';
    const kb = bytes / 1024;
    return kb.toFixed(1) + ' KB';
}

// Generate charts from processed data
function generateCharts(processedData) {
    const chartData = processedData.chart_data || {};

    // Production rates chart
    if (chartData.production_rates) {
        createChart('productionChart', chartData.production_rates, 'Production Rates Over Time');
    }

    // GOR chart
    if (chartData.gor) {
        createChart('gorChart', chartData.gor, 'Gas-Oil Ratio');
    }

    // Water cut chart
    if (chartData.water_cut) {
        createChart('waterCutChart', chartData.water_cut, 'Water Cut');
    }

    // Temperature chart
    if (chartData.temperature) {
        createChart('temperatureChart', chartData.temperature, 'Temperature');
    }

    // Pressure chart
    if (chartData.pressure) {
        createChart('pressureChart', chartData.pressure, 'Pressure');
    }
}

function createChart(canvasId, data, title) {
    const canvas = document.getElementById(canvasId);
    if (!canvas || !data) return;

    // Destroy existing chart if it exists
    if (canvas.chart) {
        canvas.chart.destroy();
    }

    canvas.chart = new Chart(canvas, {
        type: 'line',
        data: data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true, position: 'top' },
                title: { display: true, text: title }
            },
            scales: {
                x: { title: { display: true, text: 'Time' } },
                y: { title: { display: true, text: 'Value' }, beginAtZero: true }
            }
        }
    });
}

// Reset form
function resetForm() {
    selectedFile = null;
    processingResult = null;

    document.getElementById('logFileInput').value = '';
    document.getElementById('fileInfo').style.display = 'none';
    document.getElementById('processButton').disabled = true;
    document.getElementById('processingResultInput').value = '';
    document.getElementById('saveWellTestForm').reset();

    // Clear KPI displays
    document.querySelectorAll('[id^="display"]').forEach(el => {
        el.textContent = 'N/A';
    });

    // Clear raw data
    document.getElementById('rawDataContent').textContent = 'No data available';

    goToStep(1);
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    goToStep(1);
});
</script>
@endpush
