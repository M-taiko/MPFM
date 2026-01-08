@extends('layouts.master')

@section('title', 'Import Daily Plan from plan.txt')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="page-header">
                <h1 class="page-title">Import Daily Plan</h1>
                <div>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('daily-plans.index') }}">Daily Plans</a></li>
                        <li class="breadcrumb-item active">Import from plan.txt</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
            <!-- Import Instructions Card -->
            <div class="card mb-4" style="border-left: 4px solid #3b82f6;">
                <div class="card-header" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white;">
                    <h3 class="card-title mb-0">
                        <i class="fe fe-info"></i> Import Instructions
                    </h3>
                </div>
                <div class="card-body">
                    <h5 class="mb-3">Plan.txt Format Requirements:</h5>
                    <ul class="mb-3">
                        <li>File must be in <strong>tab-separated values (TSV)</strong> format</li>
                        <li>Export from Excel using "Save As" → "Text (Tab delimited)"</li>
                        <li>First 10 lines contain header information (total tests, KPI targets)</li>
                        <li>Task rows start from line 11 onwards</li>
                    </ul>

                    <h5 class="mb-3">Required Columns:</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="small">
                                <li>SNO</li>
                                <li>WELL</li>
                                <li>Tower</li>
                                <li>Flow route</li>
                                <li>Reservoir Layer</li>
                                <li>Test Location (Locally/SCADA)</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="small">
                                <li>MPFM Type</li>
                                <li>DHSV</li>
                                <li>Category / Sub Category</li>
                                <li>Required WHFP</li>
                                <li>Test Date</li>
                                <li>Operator Name</li>
                            </ul>
                        </div>
                    </div>

                    <div class="alert alert-info mb-0 mt-3">
                        <i class="fe fe-alert-circle"></i>
                        <strong>Note:</strong> The system will automatically create towers and oil strings if they don't exist. Operators will be matched by name.
                    </div>
                </div>
            </div>

            <!-- Import Form Card -->
            <div class="card" style="border-left: 4px solid #10b981;">
                <div class="card-header" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
                    <h3 class="card-title mb-0">
                        <i class="fe fe-upload"></i> Upload Plan File
                    </h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fe fe-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fe fe-x-circle"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('import_errors'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <h5><i class="fe fe-alert-triangle"></i> Import Warnings</h5>
                            <ul class="mb-0">
                                @foreach(session('import_errors') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h5><i class="fe fe-x-circle"></i> Validation Errors</h5>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('daily-plans.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                        @csrf

                        <!-- Plan Name -->
                        <div class="mb-4">
                            <label for="plan_name" class="form-label">
                                <strong>Plan Name</strong> <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="form-control @error('plan_name') is-invalid @enderror"
                                   id="plan_name"
                                   name="plan_name"
                                   value="{{ old('plan_name', 'Monthly Plan - ' . now()->format('F Y')) }}"
                                   placeholder="e.g., December 2025 MPFM Plan"
                                   required>
                            @error('plan_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Give this plan a descriptive name</small>
                        </div>

                        <!-- Plan Date -->
                        <div class="mb-4">
                            <label for="plan_date" class="form-label">
                                <strong>Plan Date</strong> <span class="text-danger">*</span>
                            </label>
                            <input type="date"
                                   class="form-control @error('plan_date') is-invalid @enderror"
                                   id="plan_date"
                                   name="plan_date"
                                   value="{{ old('plan_date', now()->format('Y-m-d')) }}"
                                   required>
                            @error('plan_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Select the date this plan will be executed</small>
                        </div>

                        <!-- File Upload -->
                        <div class="mb-4">
                            <label for="plan_file" class="form-label">
                                <strong>Plan.txt File</strong> <span class="text-danger">*</span>
                            </label>
                            <div class="custom-file-upload-zone"
                                 id="dropZone"
                                 style="border: 2px dashed #cbd5e1;
                                        border-radius: 8px;
                                        padding: 40px 20px;
                                        text-align: center;
                                        background: #f8fafc;
                                        cursor: pointer;
                                        transition: all 0.3s ease;">
                                <div class="upload-icon mb-3">
                                    <i class="fe fe-upload" style="font-size: 48px; color: #3b82f6;"></i>
                                </div>
                                <h5 class="mb-2">Drag & Drop plan.txt here</h5>
                                <p class="text-muted mb-3">or click to browse</p>
                                <input type="file"
                                       class="d-none @error('plan_file') is-invalid @enderror"
                                       id="plan_file"
                                       name="plan_file"
                                       accept=".txt"
                                       required>
                                <button type="button"
                                        class="btn btn-primary"
                                        onclick="document.getElementById('plan_file').click()">
                                    <i class="fe fe-folder"></i> Browse Files
                                </button>
                                <div id="fileInfo" class="mt-3 d-none">
                                    <div class="alert alert-info mb-0">
                                        <i class="fe fe-file-text"></i>
                                        <strong>Selected:</strong> <span id="fileName"></span>
                                        <span class="badge bg-info ms-2" id="fileSize"></span>
                                    </div>
                                </div>
                            </div>
                            @error('plan_file')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="fe fe-info"></i> Max file size: 2MB | Accepted format: .txt
                            </small>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3" style="border-top: 1px solid #e5e7eb;">
                            <a href="{{ route('daily-plans.index') }}" class="btn btn-light">
                                <i class="fe fe-arrow-left"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-success btn-lg" id="submitBtn">
                                <i class="fe fe-upload"></i> Import Plan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .custom-file-upload-zone:hover {
        border-color: #3b82f6 !important;
        background: #eff6ff !important;
    }

    .custom-file-upload-zone.drag-over {
        border-color: #10b981 !important;
        background: #d1fae5 !important;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('plan_file');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const submitBtn = document.getElementById('submitBtn');

    // Click to upload
    dropZone.addEventListener('click', function(e) {
        if (e.target.id !== 'plan_file') {
            fileInput.click();
        }
    });

    // File input change
    fileInput.addEventListener('change', function(e) {
        handleFiles(this.files);
    });

    // Drag and drop
    dropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.add('drag-over');
    });

    dropZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.remove('drag-over');
    });

    dropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.remove('drag-over');

        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            handleFiles(files);
        }
    });

    function handleFiles(files) {
        if (files.length === 0) return;

        const file = files[0];

        // Validate file type
        if (!file.name.endsWith('.txt')) {
            alert('Please select a .txt file');
            fileInput.value = '';
            return;
        }

        // Validate file size (2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('File size must be less than 2MB');
            fileInput.value = '';
            return;
        }

        // Display file info
        fileName.textContent = file.name;
        fileSize.textContent = formatBytes(file.size);
        fileInfo.classList.remove('d-none');
    }

    function formatBytes(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }

    // Form submission
    document.getElementById('importForm').addEventListener('submit', function(e) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Importing...';
    });
});
</script>
@endsection
