@extends('layouts.master')

@section('title', 'Upload Well Test Log')

@section('content')
<style>
.upload-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 16px;
    padding: 2rem;
    color: white;
    margin-bottom: 2rem;
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
}

.task-context-card {
    background: linear-gradient(135deg, #dbeafe 0%, #eff6ff 100%);
    border-left: 4px solid #3b82f6;
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 20px rgba(59, 130, 246, 0.2);
}

.expected-values-box {
    background: white;
    border-radius: 12px;
    padding: 1rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.upload-form-card {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    margin-bottom: 2rem;
}

.form-section-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 1.5rem;
    padding-bottom: 0.75rem;
    border-bottom: 3px solid #667eea;
    display: flex;
    align-items: center;
}

.modern-form-group {
    margin-bottom: 1.5rem;
}

.modern-form-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
    display: block;
}

.modern-form-input {
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    font-size: 1rem;
    transition: all 0.3s ease;
    width: 100%;
}

.modern-form-input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    outline: none;
}

.modern-form-input:read-only {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    color: #64748b;
    cursor: not-allowed;
}

.modern-form-input.is-invalid {
    border-color: #ef4444;
}

.file-upload-area {
    border: 3px dashed #cbd5e1;
    border-radius: 16px;
    padding: 3rem 2rem;
    text-align: center;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
}

.file-upload-area:hover {
    border-color: #667eea;
    background: linear-gradient(135deg, #f0f4ff 0%, #ffffff 100%);
}

.file-upload-icon {
    font-size: 3rem;
    color: #667eea;
    margin-bottom: 1rem;
}

.file-upload-text {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 0.5rem;
}

.file-upload-hint {
    font-size: 0.875rem;
    color: #64748b;
}

.file-upload-input {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}

.file-selected {
    margin-top: 1rem;
    padding: 1rem;
    background: linear-gradient(135deg, #d1fae5 0%, #ecfdf5 100%);
    border-radius: 10px;
    display: none;
}

.file-selected.active {
    display: block;
}

.btn-modern-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    padding: 1rem 2.5rem;
    border-radius: 10px;
    font-weight: 600;
    font-size: 1.1rem;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-modern-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
    color: white;
}

.btn-modern-outline {
    background: white;
    border: 2px solid #667eea;
    color: #667eea;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-modern-outline:hover {
    background: #667eea;
    color: white;
}

.alert-modern {
    border-radius: 12px;
    padding: 1rem 1.5rem;
    margin-bottom: 1.5rem;
    border-left: 4px solid;
}

.alert-modern.success {
    background: linear-gradient(135deg, #d1fae5 0%, #ecfdf5 100%);
    border-left-color: #10b981;
    color: #065f46;
}

.alert-modern.error {
    background: linear-gradient(135deg, #fee2e2 0%, #fef2f2 100%);
    border-left-color: #ef4444;
    color: #991b1b;
}

.form-divider {
    height: 2px;
    background: linear-gradient(90deg, transparent 0%, #e2e8f0 50%, transparent 100%);
    margin: 2rem 0;
}

.info-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: white;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-size: 0.875rem;
    margin-right: 1rem;
    margin-bottom: 0.5rem;
}

.breadcrumb-modern {
    background: transparent;
    padding: 0;
    margin-bottom: 1rem;
}

.breadcrumb-modern .breadcrumb-item {
    color: white;
    opacity: 0.9;
}

.breadcrumb-modern .breadcrumb-item.active {
    color: white;
    opacity: 1;
    font-weight: 600;
}

.breadcrumb-modern .breadcrumb-item a {
    color: white;
    opacity: 0.9;
    text-decoration: none;
}

.breadcrumb-modern .breadcrumb-item a:hover {
    opacity: 1;
}
</style>

<div class="main-container container-fluid">

    <!-- Modern Header -->
    <div class="upload-header">
        <ol class="breadcrumb breadcrumb-modern">
            <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}"><i class="fe fe-home mr-2"></i>Home</a></li>
            @if(isset($task))
            <li class="breadcrumb-item"><a href="{{ route('operator.tasks') }}">My Tasks</a></li>
            <li class="breadcrumb-item active">Upload Log for {{ $task->task_identifier }}</li>
            @else
            <li class="breadcrumb-item active">Upload Log</li>
            @endif
        </ol>

        <div class="row align-items-center mt-3">
            <div class="col-md-8">
                <div class="d-flex align-items-center">
                    <i class="fe fe-upload" style="font-size: 2.5rem; margin-right: 1rem;"></i>
                    <div>
                        <h1 class="mb-1" style="font-size: 1.75rem; font-weight: 700;">
                            @if(isset($task))
                                Upload Test Log
                            @else
                                Upload Well Test Log
                            @endif
                        </h1>
                        <div style="opacity: 0.9; font-size: 1rem;">
                            Upload and analyze VX log files from MPFM testing
                        </div>
                    </div>
                </div>
            </div>
            @if(isset($task))
            <div class="col-md-4 text-right">
                <a href="{{ route('operator.tasks.show', $task) }}" class="btn btn-modern-outline">
                    <i class="fe fe-arrow-left"></i>Back to Task
                </a>
            </div>
            @endif
        </div>
    </div>

    @if(isset($task))
    <!-- Task Context Card -->
    <div class="task-context-card">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h5 style="color: #1e40af; font-weight: 700; margin-bottom: 1rem;">
                    <i class="fe fe-info mr-2"></i>Task: {{ $task->task_identifier }}
                </h5>

                <div class="info-badge">
                    <i class="fe fe-map-pin" style="color: #667eea;"></i>
                    <strong>Field:</strong> {{ $task->oilString->tower->oilField->name }}
                </div>
                <div class="info-badge">
                    <i class="fe fe-layers" style="color: #667eea;"></i>
                    <strong>Tower:</strong> {{ $task->oilString->tower->name }}
                </div>
                <div class="info-badge">
                    <i class="fe fe-droplet" style="color: #667eea;"></i>
                    <strong>String:</strong> {{ $task->oilString->name }}
                </div>
                <div class="info-badge">
                    <i class="fe fe-tool" style="color: #667eea;"></i>
                    <strong>MPFM Device:</strong> {{ $task->mpfm_device ?? 'N/A' }}
                </div>

                @if($task->task_instructions)
                <div style="margin-top: 1rem; padding: 1rem; background: white; border-radius: 8px;">
                    <strong style="color: #1e40af;">Instructions:</strong>
                    <p style="margin: 0.5rem 0 0 0; color: #475569;">{{ $task->task_instructions }}</p>
                </div>
                @endif
            </div>
            <div class="col-md-4">
                @if($task->expected_oil_rate || $task->expected_gas_rate)
                <div class="expected-values-box">
                    <div style="font-weight: 700; color: #1e40af; margin-bottom: 0.75rem; font-size: 1rem;">
                        <i class="fe fe-target mr-2"></i>Expected Values
                    </div>
                    @if($task->expected_oil_rate)
                    <div style="margin-bottom: 0.5rem; color: #475569;">
                        <strong>Oil Rate:</strong> {{ number_format($task->expected_oil_rate, 2) }} STB/d
                    </div>
                    @endif
                    @if($task->expected_gas_rate)
                    <div style="margin-bottom: 0.5rem; color: #475569;">
                        <strong>Gas Rate:</strong> {{ number_format($task->expected_gas_rate, 2) }} MMScf/d
                    </div>
                    @endif
                    @if($task->expected_water_cut)
                    <div style="color: #475569;">
                        <strong>Water Cut:</strong> {{ number_format($task->expected_water_cut, 2) }}%
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Alerts -->
    @if (session('success'))
    <div class="alert-modern success">
        <i class="fe fe-check-circle mr-2"></i>
        <strong>Success!</strong> {{ session('success') }}
    </div>
    @endif

    @if (session('error'))
    <div class="alert-modern error">
        <i class="fe fe-alert-circle mr-2"></i>
        <strong>Error!</strong> {{ session('error') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="alert-modern error">
        <div style="font-weight: 700; margin-bottom: 0.5rem;">
            <i class="fe fe-alert-triangle mr-2"></i>There were some problems with your input:
        </div>
        <ul style="margin: 0; padding-left: 1.5rem;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Upload Form Card -->
    <div class="upload-form-card">
        <form action="{{ route('process.log') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
            @csrf

            @if(isset($task))
            <input type="hidden" name="task_id" value="{{ $task->id }}">
            @endif

            <!-- Well Information Section -->
            <div class="form-section-title">
                <i class="fe fe-info mr-2"></i>
                Well Information
                @if(isset($task))
                <span style="font-size: 0.85rem; margin-left: 1rem; opacity: 0.8;">
                    <i class="fe fe-check-circle" style="color: #10b981;"></i> Auto-filled from task
                </span>
                @endif
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="modern-form-group">
                        <label class="modern-form-label">Client</label>
                        <input type="text" name="client" class="modern-form-input @error('client') is-invalid @enderror"
                               placeholder="e.g., ADNOC"
                               value="{{ old('client', $autoFillData['client'] ?? '') }}"
                               {{ isset($task) ? 'readonly' : '' }} required>
                        @error('client')
                            <div style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="modern-form-group">
                        <label class="modern-form-label">Field</label>
                        <input type="text" name="field" class="modern-form-input @error('field') is-invalid @enderror"
                               placeholder="e.g., Upper Zakum"
                               value="{{ old('field', $autoFillData['field'] ?? '') }}"
                               {{ isset($task) ? 'readonly' : '' }} required>
                        @error('field')
                            <div style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="modern-form-group">
                        <label class="modern-form-label">Well Name</label>
                        <input type="text" name="well" class="modern-form-input @error('well') is-invalid @enderror"
                               placeholder="e.g., ZK-23"
                               value="{{ old('well', $autoFillData['well'] ?? '') }}"
                               {{ isset($task) ? 'readonly' : '' }} required>
                        @error('well')
                            <div style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-divider"></div>

            <!-- Test Configuration Section -->
            <div class="form-section-title">
                <i class="fe fe-settings mr-2"></i>
                Test Configuration
                @if(isset($task))
                <span style="font-size: 0.85rem; margin-left: 1rem; opacity: 0.8;">
                    <i class="fe fe-check-circle" style="color: #10b981;"></i> Auto-filled from task
                </span>
                @endif
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="modern-form-group">
                        <label class="modern-form-label">Wellhead Tower</label>
                        <input type="text" name="wellhead_tower" class="modern-form-input"
                               placeholder="Tower Name"
                               value="{{ old('wellhead_tower', $autoFillData['wellhead_tower'] ?? '') }}"
                               {{ isset($task) ? 'readonly' : '' }}>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="modern-form-group">
                        <label class="modern-form-label">Wellhead String</label>
                        <input type="text" name="wellhead_string" class="modern-form-input"
                               placeholder="String Name"
                               value="{{ old('wellhead_string', $autoFillData['wellhead_string'] ?? '') }}"
                               {{ isset($task) ? 'readonly' : '' }}>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="modern-form-group">
                        <label class="modern-form-label">Choke Size (1/64")</label>
                        <input type="number" name="choke_size" class="modern-form-input"
                               placeholder="e.g., 32"
                               value="{{ old('choke_size') }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="modern-form-group">
                        <label class="modern-form-label">Operator Name</label>
                        <input type="text" name="operator" class="modern-form-input @error('operator') is-invalid @enderror"
                               placeholder="Operator Name"
                               value="{{ old('operator', $autoFillData['operator'] ?? '') }}"
                               {{ isset($task) ? 'readonly' : '' }} required>
                        @error('operator')
                            <div style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="modern-form-group">
                        <label class="modern-form-label">Test Date</label>
                        <input type="date" name="test_date" class="modern-form-input @error('test_date') is-invalid @enderror"
                               value="{{ old('test_date', $autoFillData['test_date'] ?? '') }}"
                               {{ isset($task) ? 'readonly' : '' }} required>
                        @error('test_date')
                            <div style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="modern-form-group">
                        <label class="modern-form-label">Test Type</label>
                        <select name="test_type" class="modern-form-input" {{ isset($task) ? 'disabled' : '' }}>
                            <option value="flow" {{ old('test_type', $autoFillData['test_type'] ?? '') == 'flow' ? 'selected' : '' }}>Flow Test</option>
                            <option value="cleanup" {{ old('test_type', $autoFillData['test_type'] ?? '') == 'cleanup' ? 'selected' : '' }}>Clean Up</option>
                            <option value="build-up" {{ old('test_type', $autoFillData['test_type'] ?? '') == 'build-up' ? 'selected' : '' }}>Build Up</option>
                        </select>
                        @if(isset($task))
                        <input type="hidden" name="test_type" value="{{ $autoFillData['test_type'] ?? 'flow' }}">
                        @endif
                    </div>
                </div>
            </div>

            <div class="form-divider"></div>

            <!-- File Upload Section -->
            <div class="form-section-title">
                <i class="fe fe-file-text mr-2"></i>
                VX Log File
            </div>

            <div class="file-upload-area" onclick="document.getElementById('logfile').click()">
                <input type="file" name="logfile" id="logfile" class="file-upload-input @error('logfile') is-invalid @enderror" accept=".vxlog,.txt" required>

                <div class="file-upload-icon">
                    <i class="fe fe-upload-cloud"></i>
                </div>
                <div class="file-upload-text">
                    Click to browse or drag & drop your VX log file
                </div>
                <div class="file-upload-hint">
                    Supported formats: .vxlog, .txt
                </div>

                @error('logfile')
                    <div style="color: #ef4444; font-size: 0.875rem; margin-top: 1rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="file-selected" id="fileSelected">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center;">
                        <i class="fe fe-file-text" style="font-size: 1.5rem; color: #10b981; margin-right: 1rem;"></i>
                        <div>
                            <div style="font-weight: 600; color: #065f46;" id="fileName"></div>
                            <div style="font-size: 0.875rem; color: #059669;" id="fileSize"></div>
                        </div>
                    </div>
                    <i class="fe fe-check-circle" style="font-size: 1.5rem; color: #10b981;"></i>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="text-center mt-5">
                <button type="submit" class="btn-modern-primary">
                    <i class="fe fe-upload"></i>
                    Upload & Analyze Log File
                </button>
            </div>

        </form>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('logfile');
    const fileSelected = document.getElementById('fileSelected');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');

    fileInput.addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            const file = this.files[0];
            fileName.textContent = file.name;

            // Format file size
            const size = file.size;
            let sizeText = '';
            if (size < 1024) {
                sizeText = size + ' bytes';
            } else if (size < 1024 * 1024) {
                sizeText = (size / 1024).toFixed(2) + ' KB';
            } else {
                sizeText = (size / (1024 * 1024)).toFixed(2) + ' MB';
            }
            fileSize.textContent = sizeText;

            fileSelected.classList.add('active');
        }
    });

    // Prevent form submission on drag and drop
    const uploadArea = document.querySelector('.file-upload-area');
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.style.borderColor = '#667eea';
        this.style.background = 'linear-gradient(135deg, #f0f4ff 0%, #ffffff 100%)';
    });

    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.style.borderColor = '#cbd5e1';
        this.style.background = 'linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%)';
    });

    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.style.borderColor = '#cbd5e1';
        this.style.background = 'linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%)';

        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            const event = new Event('change');
            fileInput.dispatchEvent(event);
        }
    });
});
</script>
@endsection
