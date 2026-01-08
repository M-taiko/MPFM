@extends('layouts.master')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card border-0 shadow-sm">
                <!-- Header -->
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fe fe-upload-cloud me-2"></i>Upload Task Log & Complete
                    </h4>
                </div>

                <div class="card-body">
                    <!-- Task Information Display -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-box">
                                <label class="text-muted small fw-bold">WELL</label>
                                <p class="mb-2">
                                    <strong>{{ $task->towerModel?->well_name ?? 'N/A' }}</strong>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <label class="text-muted small fw-bold">TOWER</label>
                                <p class="mb-2">
                                    <strong>{{ $task->towerModel?->name ?? 'N/A' }}</strong>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-box">
                                <label class="text-muted small fw-bold">OIL STRING</label>
                                <p class="mb-2">
                                    <strong>{{ $task->oilString?->string_identifier ?? 'N/A' }}</strong>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <label class="text-muted small fw-bold">CATEGORY</label>
                                <p class="mb-2">
                                    <strong>{{ $task->category ?? 'MPFM Test' }}</strong>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-box">
                                <label class="text-muted small fw-bold">PRIORITY</label>
                                <p class="mb-2">
                                    <span class="badge badge-{{ $task->priority === 'urgent' ? 'danger' : ($task->priority === 'high' ? 'warning' : 'info') }}">
                                        {{ ucfirst($task->priority ?? 'normal') }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <label class="text-muted small fw-bold">SCHEDULED DATE</label>
                                <p class="mb-2">
                                    <strong>{{ $task->scheduled_date ? \Carbon\Carbon::parse($task->scheduled_date)->format('Y-m-d') : 'N/A' }}</strong>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Team Members -->
                    @if($task->operators->isNotEmpty())
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="info-box">
                                    <label class="text-muted small fw-bold">TEAM MEMBERS</label>
                                    <div class="mt-2">
                                        @foreach($task->operators as $operator)
                                            <span class="badge badge-{{ $operator->pivot->is_primary ? 'primary' : 'secondary' }} me-2">
                                                {{ $operator->name }}
                                                @if($operator->pivot->is_primary)
                                                    <i class="fe fe-star ms-1"></i> (Primary)
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <hr class="my-4">

                    <!-- Upload Form -->
                    <form action="{{ route('operator.tasks.upload.store', $task) }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                        @csrf

                        <!-- Log File Input -->
                        <div class="form-group mb-3">
                            <label for="log_file" class="form-label fw-bold">
                                <i class="fe fe-paperclip me-2"></i>Log File
                                <span class="text-danger">*</span>
                            </label>
                            <div class="upload-area border-2 border-dashed rounded p-4 text-center" id="uploadArea" style="cursor: pointer; transition: all 0.3s;">
                                <input type="file" id="log_file" name="log_file" class="form-control d-none" accept=".txt,.csv,.pdf,.jpg,.jpeg,.png" required>
                                <div id="uploadDefault">
                                    <i class="fe fe-cloud-upload text-primary" style="font-size: 2em;"></i>
                                    <p class="mt-2 mb-1"><strong>Click to upload or drag and drop</strong></p>
                                    <p class="text-muted small">TXT, CSV, PDF, JPG, JPEG, PNG (Max 5MB)</p>
                                </div>
                                <div id="uploadSuccess" style="display: none;">
                                    <i class="fe fe-check-circle text-success" style="font-size: 2em;"></i>
                                    <p class="mt-2 mb-1"><strong id="fileName"></strong></p>
                                </div>
                            </div>
                            @error('log_file')
                                <div class="alert alert-danger mt-2 mb-0">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Remarks Textarea -->
                        <div class="form-group mb-3">
                            <label for="remarks" class="form-label fw-bold">
                                <i class="fe fe-message-square me-2"></i>Remarks / Notes
                            </label>
                            <textarea id="remarks" name="remarks" class="form-control" rows="4" placeholder="Add any notes about the task completion...">{{ old('remarks') }}</textarea>
                            <small class="text-muted">Optional - any additional information about the task execution</small>
                            @error('remarks')
                                <div class="alert alert-danger mt-2 mb-0">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('operator.tasks.show', $task) }}" class="btn btn-outline-secondary">
                                <i class="fe fe-arrow-left me-2"></i>Back to Task
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fe fe-check-circle me-2"></i>Upload & Complete Task
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Progress Indicator -->
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="progress flex-grow-1" style="height: 8px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <span class="ms-3 text-muted small">
                            <i class="fe fe-info me-1"></i>Step 3 of 4: Upload & Complete
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .info-box {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 0.375rem;
        border-left: 4px solid #0d6efd;
    }

    .upload-area {
        background: #f8f9fa;
        transition: all 0.3s ease;
    }

    .upload-area:hover {
        background: #e7f1ff;
        border-color: #0d6efd !important;
    }

    .upload-area.dragover {
        background: #e7f1ff;
        border-color: #0d6efd !important;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('log_file');
    const uploadDefault = document.getElementById('uploadDefault');
    const uploadSuccess = document.getElementById('uploadSuccess');
    const fileNameSpan = document.getElementById('fileName');
    const form = document.getElementById('uploadForm');

    // Click to upload
    uploadArea.addEventListener('click', function() {
        fileInput.click();
    });

    // File selection
    fileInput.addEventListener('change', function(e) {
        handleFileSelect(e.target.files);
    });

    // Drag and drop
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });

    uploadArea.addEventListener('dragleave', function() {
        uploadArea.classList.remove('dragover');
    });

    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        handleFileSelect(e.dataTransfer.files);
    });

    // Handle file selection
    function handleFileSelect(files) {
        if (files.length > 0) {
            const file = files[0];
            const maxSize = 5 * 1024 * 1024; // 5MB
            const allowedTypes = ['text/plain', 'text/csv', 'application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];

            // Validate file
            if (!allowedTypes.includes(file.type) && !file.name.match(/\.(txt|csv|pdf|jpg|jpeg|png)$/i)) {
                alert('Invalid file type. Please upload: TXT, CSV, PDF, JPG, JPEG, or PNG');
                return;
            }

            if (file.size > maxSize) {
                alert('File size exceeds 5MB limit');
                return;
            }

            // Update input
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            fileInput.files = dataTransfer.files;

            // Show success state
            uploadDefault.style.display = 'none';
            uploadSuccess.style.display = 'block';
            fileNameSpan.textContent = file.name;
        }
    }

    // Form submission with validation
    form.addEventListener('submit', function(e) {
        if (!fileInput.files || fileInput.files.length === 0) {
            e.preventDefault();
            alert('Please select a file to upload');
        }
    });
});
</script>
@endsection
