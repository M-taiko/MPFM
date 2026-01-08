@extends('layouts.master')

@section('title', 'Upload MPFM Test Log')

@push('styles')
<style>
    .upload-zone {
        border: 3px dashed #4e73df;
        border-radius: 15px;
        padding: 3rem;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
        background: linear-gradient(135deg, #f8f9ff 0%, #e8f2ff 100%);
        position: relative;
        overflow: hidden;
    }

    .upload-zone::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgba(78, 115, 223, 0.1), transparent);
        transform: rotate(45deg);
        transition: all 0.6s ease;
        opacity: 0;
    }

    .upload-zone:hover {
        border-color: #2e59d9;
        background: linear-gradient(135deg, #e8f2ff 0%, #d4e4ff 100%);
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(78, 115, 223, 0.2);
    }

    .upload-zone:hover::before {
        opacity: 1;
    }

    .upload-zone.dragover {
        border-color: #28a745;
        background: linear-gradient(135deg, #f0fff4 0%, #e6fffa 100%);
        transform: scale(1.02);
    }

    .upload-zone.success {
        border-color: #28a745;
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    }

    .upload-zone.processing {
        border-color: #ffc107;
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        pointer-events: none;
    }

    .upload-icon {
        font-size: 4rem;
        color: #4e73df;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }

    .upload-zone:hover .upload-icon {
        color: #2e59d9;
        transform: scale(1.1);
    }

    .upload-zone.processing .upload-icon {
        color: #ffc107;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    .file-info {
        background: rgba(255, 255, 255, 0.9);
        border-radius: 10px;
        padding: 1rem;
        margin-top: 1rem;
        border: 2px solid #4e73df;
        backdrop-filter: blur(10px);
    }

    .feature-list {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 12px;
        padding: 1.5rem;
        margin-top: 2rem;
        border: 1px solid #e9ecef;
    }

    .feature-item {
        display: flex;
        align-items: center;
        margin-bottom: 0.75rem;
    }

    .feature-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        font-size: 1.2rem;
    }

    .btn-upload {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        border: none;
        border-radius: 25px;
        padding: 12px 30px;
        font-size: 1.1rem;
        font-weight: 600;
        color: white;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(78, 115, 223, 0.3);
    }

    .btn-upload:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(78, 115, 223, 0.4);
    }

    .btn-upload:disabled {
        background: #6c757d;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .progress-indicator {
        display: none;
        margin-top: 2rem;
    }

    .progress-bar {
        height: 8px;
        background: linear-gradient(90deg, #4e73df 0%, #28a745 100%);
        border-radius: 4px;
        transition: width 0.3s ease;
    }

    .compatibility-table {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 12px;
        padding: 2rem;
        margin-top: 2rem;
        border: 1px solid #e9ecef;
    }

    .compatibility-table table {
        margin: 0;
    }

    .compatibility-table th {
        background: #4e73df;
        color: white;
        border: none;
        padding: 12px;
    }

    .compatibility-table td {
        border: none;
        padding: 12px;
        vertical-align: middle;
    }

    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .status-supported {
        background: #d4edda;
        color: #155724;
    }

    .status-partial {
        background: #fff3cd;
        color: #856404;
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 15px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-leftheader">
            <h4 class="page-title">Upload MPFM Test Log</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('mpfm_tests.index') }}">MPFM Tests</a></li>
                <li class="breadcrumb-item active">Upload Log</li>
            </ol>
        </div>
        <div class="page-rightheader">
            <a href="{{ route('mpfm_tests.create') }}" class="btn btn-outline-primary">
                <i class="fe fe-plus"></i> Manual Entry
            </a>
            <a href="{{ route('mpfm_tests.index') }}" class="btn btn-secondary">
                <i class="fe fe-arrow-left"></i> Back to Tests
            </a>
        </div>
    </div>

    <!-- Main Upload Interface -->
    <div class="row">
        <div class="col-xl-8 col-lg-8 col-md-12">
            <div class="card glass-card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">
                        <i class="fe fe-upload-cloud mr-2"></i>
                        Log File Upload
                    </h3>
                </div>
                <div class="card-body">
                    <form id="uploadForm" action="{{ route('mpfm_tests.process_upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Upload Zone -->
                        <div class="upload-zone" id="uploadZone">
                            <div class="upload-content">
                                <div class="upload-icon">
                                    <i class="fe fe-file-text"></i>
                                </div>
                                <h3 class="mb-3">Drop your MPFM log file here</h3>
                                <p class="text-muted mb-4">Or click to browse files from your computer</p>

                                <input type="file" id="logFileInput" name="logfile" class="d-none" accept=".txt,.vxlog,.log,.csv">

                                <button type="button" class="btn btn-upload" onclick="document.getElementById('logFileInput').click()">
                                    <i class="fe fe-folder mr-2"></i> Browse Files
                                </button>

                                <div class="mt-3 text-sm text-muted">
                                    Supported formats: .txt, .vxlog, .log, .csv (Max 10MB)
                                </div>
                            </div>
                        </div>

                        <!-- File Info -->
                        <div class="file-info d-none" id="fileInfo">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h6 class="mb-1" id="fileName">No file selected</h6>
                                    <small class="text-muted" id="fileSize">0 KB</small>
                                </div>
                                <div class="col-md-4 text-end">
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearFile()">
                                        <i class="fe fe-x"></i> Remove
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Indicator -->
                        <div class="progress-indicator" id="progressIndicator">
                            <div class="text-center mb-3">
                                <h6 class="text-primary">Processing Log File...</h6>
                                <small class="text-muted">This may take a few moments</small>
                            </div>
                            <div class="progress">
                                <div class="progress-bar" id="progressBar" style="width: 0%"></div>
                            </div>
                        </div>

                        <!-- Upload Button -->
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-upload btn-lg d-none" id="uploadBtn">
                                <i class="fe fe-play mr-2"></i> Process Log File
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Features -->
            <div class="feature-list glass-card mt-4">
                <h5 class="mb-4 text-center">
                    <i class="fe fe-zap text-warning mr-2"></i>
                    What happens when you upload?
                </h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="feature-item">
                            <div class="feature-icon bg-primary text-white">
                                <i class="fe fe-cpu"></i>
                            </div>
                            <div>
                                <strong>Automatic Parsing</strong>
                                <br><small class="text-muted">Extracts MPFM data using advanced algorithms</small>
                            </div>
                        </div>

                        <div class="feature-item">
                            <div class="feature-icon bg-success text-white">
                                <i class="fe fe-trending-up"></i>
                            </div>
                            <div>
                                <strong>KPI Calculations</strong>
                                <br><small class="text-muted">Computes GOR, WLR, BSW, and productivity index</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="feature-item">
                            <div class="feature-icon bg-info text-white">
                                <i class="fe fe-bar-chart-2"></i>
                            </div>
                            <div>
                                <strong>Visual Charts</strong>
                                <br><small class="text-muted">Interactive graphs for data validation</small>
                            </div>
                        </div>

                        <div class="feature-item">
                            <div class="feature-icon bg-warning text-white">
                                <i class="fe fe-eye"></i>
                            </div>
                            <div>
                                <strong>Data Preview</strong>
                                <br><small class="text-muted">Review before saving to database</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Information -->
        <div class="col-xl-4 col-lg-4 col-md-12">
            <!-- Device Compatibility -->
            <div class="card glass-card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fe fe-check-circle mr-2"></i>
                        Compatible Devices
                    </h5>
                </div>
                <div class="card-body">
                    <div class="compatibility-table">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>Device/Model</th>
                                    <th>Compatibility</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>VX Technology</strong></td>
                                    <td><span class="status-badge status-supported">Fully Supported</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Schlumberger</strong></td>
                                    <td><span class="status-badge status-supported">Fully Supported</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Weatherford</strong></td>
                                    <td><span class="status-badge status-supported">Fully Supported</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Halliburton</strong></td>
                                    <td><span class="status-badge status-partial">Partial Support</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Generic CSV/TXT</strong></td>
                                    <td><span class="status-badge status-supported">Supported</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Data Fields Extracted -->
            <div class="card glass-card mt-4">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fe fe-database mr-2"></i>
                        Data Extracted
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <h6 class="text-info">Production Rates</h6>
                            <ul class="list-unstyled small">
                                <li><i class="fe fe-check text-success mr-1"></i> Oil Rate (STB/d)</li>
                                <li><i class="fe fe-check text-success mr-1"></i> Gas Rate (Mscf/d)</li>
                                <li><i class="fe fe-check text-success mr-1"></i> Water Rate (STB/d)</li>
                            </ul>
                        </div>
                        <div class="col-6">
                            <h6 class="text-info">Calculated KPIs</h6>
                            <ul class="list-unstyled small">
                                <li><i class="fe fe-check text-success mr-1"></i> GOR (Scf/STB)</li>
                                <li><i class="fe fe-check text-success mr-1"></i> WLR (%)</li>
                                <li><i class="fe fe-check text-success mr-1"></i> BSW (%)</li>
                            </ul>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <small class="text-muted">
                            <i class="fe fe-info mr-1"></i>
                            Missing data fields are handled gracefully
                        </small>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card glass-card mt-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="fe fe-settings mr-2"></i>
                        Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('mpfm_tests.index') }}" class="btn btn-outline-primary">
                            <i class="fe fe-list mr-2"></i> View All Tests
                        </a>
                        <a href="{{ route('mpfm_operational_plans.index') }}" class="btn btn-outline-success">
                            <i class="fe fe-calendar mr-2"></i> View Plans
                        </a>
                        <a href="{{ route('reports.operator_performance') }}" class="btn btn-outline-info">
                            <i class="fe fe-bar-chart-2 mr-2"></i> Performance Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let selectedFile = null;

document.addEventListener('DOMContentLoaded', function() {
    initializeFileUpload();
});

function initializeFileUpload() {
    const uploadZone = document.getElementById('uploadZone');
    const fileInput = document.getElementById('logFileInput');
    const fileInfo = document.getElementById('fileInfo');
    const uploadBtn = document.getElementById('uploadBtn');
    const progressIndicator = document.getElementById('progressIndicator');

    // File input change
    fileInput.addEventListener('change', function(e) {
        handleFileSelection(e.target.files[0]);
    });

    // Drag and drop events
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        uploadZone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        uploadZone.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        uploadZone.addEventListener(eventName, unhighlight, false);
    });

    function highlight(e) {
        uploadZone.classList.add('dragover');
    }

    function unhighlight(e) {
        uploadZone.classList.remove('dragover');
    }

    uploadZone.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;

        if (files.length > 0) {
            handleFileSelection(files[0]);
        }
    }
}

function handleFileSelection(file) {
    if (!file) return;

    // Validate file type
    const allowedTypes = ['text/plain', 'text/csv', 'application/octet-stream'];
    const allowedExtensions = ['.txt', '.vxlog', '.log', '.csv'];
    const fileExtension = file.name.toLowerCase().substring(file.name.lastIndexOf('.'));

    const isValidType = allowedTypes.includes(file.type) || allowedExtensions.includes(fileExtension);

    if (!isValidType) {
        showAlert('Please select a valid log file (.txt, .vxlog, .log, or .csv)', 'danger');
        return;
    }

    // Validate file size (10MB)
    if (file.size > 10 * 1024 * 1024) {
        showAlert('File size must be less than 10MB', 'danger');
        return;
    }

    selectedFile = file;
    updateFileInfo(file);
    document.getElementById('uploadBtn').classList.remove('d-none');
}

function updateFileInfo(file) {
    document.getElementById('fileName').textContent = file.name;
    document.getElementById('fileSize').textContent = formatFileSize(file.size);
    document.getElementById('fileInfo').classList.remove('d-none');
    document.getElementById('uploadZone').classList.add('success');
}

function clearFile() {
    selectedFile = null;
    document.getElementById('logFileInput').value = '';
    document.getElementById('fileInfo').classList.add('d-none');
    document.getElementById('uploadZone').classList.remove('success');
    document.getElementById('uploadBtn').classList.add('d-none');
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Form submission with progress
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    if (!selectedFile) {
        e.preventDefault();
        showAlert('Please select a log file first', 'warning');
        return;
    }

    const uploadZone = document.getElementById('uploadZone');
    const progressIndicator = document.getElementById('progressIndicator');
    const progressBar = document.getElementById('progressBar');

    uploadZone.classList.add('processing');
    progressIndicator.style.display = 'block';

    // Simulate progress (in real implementation, this would be handled by the server)
    let progress = 0;
    const progressInterval = setInterval(() => {
        progress += Math.random() * 15;
        if (progress > 90) progress = 90;
        progressBar.style.width = progress + '%';
    }, 200);
});

function showAlert(message, type = 'info') {
    // Simple alert implementation - you can enhance this with a proper notification system
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(alertDiv);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}
</script>
@endpush
