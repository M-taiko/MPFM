@extends('layouts.app')

@section('content')
<style>
    .modern-form {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
    }

    .modern-form h1 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .modern-form p {
        font-size: 1.1rem;
        opacity: 0.9;
    }

    .card {
        border: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    }

    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 10px 10px 0 0;
    }

    .card-header h5 {
        color: white;
        font-weight: 600;
    }

    .form-label {
        font-weight: 600;
        color: #333;
        margin-bottom: 0.75rem;
    }

    .form-control, .form-select {
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .badge-info {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 0.6rem 1rem;
        border-radius: 20px;
        font-size: 0.85rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 8px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        color: white;
    }

    .section-divider {
        text-align: center;
        margin: 2rem 0;
        position: relative;
    }

    .section-divider::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        width: 100%;
        height: 1px;
        background: #e0e0e0;
    }

    .section-divider span {
        background: white;
        padding: 0 1rem;
        position: relative;
        font-weight: 600;
        color: #667eea;
    }

    .step-indicator {
        display: flex;
        justify-content: space-around;
        margin: 2rem 0;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 10px;
    }

    .step {
        text-align: center;
        flex: 1;
    }

    .step-number {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 0.5rem;
        font-weight: bold;
    }

    .step-text {
        font-size: 0.9rem;
        color: #666;
    }
</style>

<div class="container-fluid">
    <!-- Modern Header -->
    <div class="modern-form">
        <h1><i class="fe fe-plus-square"></i> Create New Task</h1>
        <p>Dynamic task creation with department-specific requirements</p>
    </div>

    <!-- Step Indicator -->
    <div class="step-indicator">
        <div class="step">
            <div class="step-number">1</div>
            <div class="step-text">Basic Info</div>
        </div>
        <div class="step">
            <div class="step-number">2</div>
            <div class="step-text">Location</div>
        </div>
        <div class="step">
            <div class="step-number">3</div>
            <div class="step-text">Department</div>
        </div>
        <div class="step">
            <div class="step-number">4</div>
            <div class="step-text">Submit</div>
        </div>
    </div>

    <form action="{{ route('tasks.store-advanced') }}" method="POST" id="taskForm">
        @csrf

        <!-- Display Errors -->
        @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong><i class="fe fe-alert-circle"></i> Validation Errors:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="row">
            <!-- Left Column: Basic Info -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fe fe-info"></i> Basic Information</h5>
                    </div>
                    <div class="card-body">
                        <!-- Monthly Plan -->
                        <div class="mb-3">
                            <label for="monthly_plan_id" class="form-label">Monthly Plan *</label>
                            <select class="form-select @error('monthly_plan_id') is-invalid @enderror"
                                    id="monthly_plan_id" name="monthly_plan_id" required>
                                <option value="">Select monthly plan...</option>
                                @forelse($monthlyPlans ?? [] as $plan)
                                    <option value="{{ $plan->id }}"
                                            {{ old('monthly_plan_id') == $plan->id ? 'selected' : '' }}>
                                        {{ $plan->title }} ({{ $plan->planned_start_date->format('M Y') }})
                                    </option>
                                @empty
                                    <option value="" disabled>No plans available</option>
                                @endforelse
                            </select>
                            <small class="text-muted">Select which monthly plan this task belongs to</small>
                            @error('monthly_plan_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label"><i class="fe fe-type"></i> Task Title *</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror"
                                   id="title" name="title" required value="{{ old('title') }}"
                                   placeholder="Enter descriptive task title">
                            @error('title')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label"><i class="fe fe-file-text"></i> Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="4"
                                      placeholder="Add detailed task description...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Priority -->
                        <div class="mb-3">
                            <label for="priority" class="form-label"><i class="fe fe-flag"></i> Priority *</label>
                            <select class="form-select @error('priority') is-invalid @enderror"
                                    id="priority" name="priority" required>
                                <option value="">Select priority level...</option>
                                <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>
                                    <span class="badge bg-info">Low</span> - Regular tasks
                                </option>
                                <option value="medium" {{ old('priority') === 'medium' ? 'selected' : '' }}>
                                    <span class="badge bg-warning">Medium</span> - Important tasks
                                </option>
                                <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>
                                    <span class="badge bg-danger">High</span> - Urgent tasks
                                </option>
                                <option value="critical" {{ old('priority') === 'critical' ? 'selected' : '' }}>
                                    <span class="badge bg-dark">Critical</span> - Emergency tasks
                                </option>
                            </select>
                            @error('priority')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Expected Completion -->
                        <div class="mb-3">
                            <label for="expected_completion_at" class="form-label"><i class="fe fe-calendar"></i> Expected Completion</label>
                            <input type="datetime-local"
                                   class="form-control @error('expected_completion_at') is-invalid @enderror"
                                   id="expected_completion_at" name="expected_completion_at"
                                   value="{{ old('expected_completion_at') }}">
                            <small class="text-muted">Date and time when task should be completed</small>
                            @error('expected_completion_at')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Location Selection -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fe fe-map-pin"></i> Location & Asset Selection</h5>
                    </div>
                    <div class="card-body">
                        <!-- Tower Selection -->
                        <div class="mb-3">
                            <label for="tower_id" class="form-label"><i class="fe fe-map"></i> Tower *</label>
                            <select class="form-select @error('tower_id') is-invalid @enderror"
                                    id="tower_id" name="tower_id" required>
                                <option value="">Select tower...</option>
                                @foreach($towers as $tower)
                                    <option value="{{ $tower->id }}"
                                            data-tower-id="{{ $tower->id }}"
                                            {{ old('tower_id') == $tower->id ? 'selected' : '' }}>
                                        {{ $tower->name }} ({{ $tower->code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('tower_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Oil String Selection (Dynamic) -->
                        <div class="mb-3">
                            <label for="oil_string_id" class="form-label"><i class="fe fe-layers"></i> Oil String *</label>
                            <select class="form-select @error('oil_string_id') is-invalid @enderror"
                                    id="oil_string_id" name="oil_string_id" required disabled>
                                <option value="">First select a tower...</option>
                            </select>
                            <small class="text-muted d-none" id="oilStringLoading">
                                <i class="fas fa-spinner fa-spin"></i> Loading oil strings...
                            </small>
                            @error('oil_string_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Department Selection -->
                        <div class="mb-3">
                            <label for="department_id" class="form-label"><i class="fe fe-briefcase"></i> Department *</label>
                            <select class="form-select @error('department_id') is-invalid @enderror"
                                    id="department_id" name="department_id" required>
                                <option value="">Select department...</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}"
                                            {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }} ({{ $dept->code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Department Info -->
                        <div id="departmentInfoBox" style="display: none;">
                            <div class="alert alert-info">
                                <strong>Department:</strong> <span id="departmentName"></span><br>
                                <strong>Code:</strong> <span id="departmentCode"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dynamic Department Requirements -->
        <div class="row" id="requirementsSection" style="display: none;">
            <div class="col-12">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list-check"></i> Department Requirements
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Test Type Selection -->
                        <div class="mb-4">
                            <label for="test_type_id" class="form-label">Test Type</label>
                            <select class="form-select" id="test_type_id" name="test_type_id">
                                <option value="">Select test type...</option>
                            </select>
                            <div id="testTypeInfo" style="display: none;" class="mt-2 alert alert-light">
                                <strong>Description:</strong> <span id="testTypeDescription"></span><br>
                                <strong>Estimated Duration:</strong> <span id="testTypeDuration"></span> minutes
                            </div>
                        </div>

                        <!-- Dynamic Fields Container -->
                        <div id="dynamicFieldsContainer">
                            <!-- Will be populated by JavaScript -->
                        </div>

                        <!-- Required Fields List -->
                        <div id="requiredFieldsList" style="display: none;">
                            <h6>Required Fields:</h6>
                            <div class="badge-list" id="requiredFieldsBadges">
                                <!-- Will be populated by JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="row">
            <div class="col-12">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save"></i> Create Task
                </button>
                <a href="{{ route('tasks.index') }}" class="btn btn-secondary btn-lg">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const towerSelect = document.getElementById('tower_id');
    const oilStringSelect = document.getElementById('oil_string_id');
    const departmentSelect = document.getElementById('department_id');
    const testTypeSelect = document.getElementById('test_type_id');
    const dynamicFieldsContainer = document.getElementById('dynamicFieldsContainer');
    const requirementsSection = document.getElementById('requirementsSection');
    const departmentInfoBox = document.getElementById('departmentInfoBox');

    // Load oil strings when tower is selected
    towerSelect.addEventListener('change', async function() {
        const towerId = this.value;

        if (!towerId) {
            oilStringSelect.innerHTML = '<option value="">Select tower first...</option>';
            oilStringSelect.disabled = true;
            return;
        }

        document.getElementById('oilStringLoading').classList.remove('d-none');
        oilStringSelect.disabled = true;

        try {
            const response = await fetch('{{ route("tasks.get-oil-strings") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ tower_id: towerId })
            });

            const data = await response.json();

            if (data.success) {
                oilStringSelect.innerHTML = '<option value="">Select oil string...</option>';
                data.data.forEach(string => {
                    const option = document.createElement('option');
                    option.value = string.id;
                    option.textContent = `${string.name} (${string.code})`;
                    oilStringSelect.appendChild(option);
                });
                oilStringSelect.disabled = false;
            }
        } catch (error) {
            console.error('Error loading oil strings:', error);
            alert('Failed to load oil strings');
        } finally {
            document.getElementById('oilStringLoading').classList.add('d-none');
        }
    });

    // Load department requirements when department is selected
    departmentSelect.addEventListener('change', async function() {
        const departmentId = this.value;

        if (!departmentId) {
            requirementsSection.style.display = 'none';
            departmentInfoBox.style.display = 'none';
            return;
        }

        try {
            const response = await fetch('{{ route("tasks.get-department-requirements") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ department_id: departmentId })
            });

            const data = await response.json();

            if (data.success) {
                // Show department info
                document.getElementById('departmentName').textContent = data.department.name;
                document.getElementById('departmentCode').textContent = data.department.code;
                departmentInfoBox.style.display = 'block';

                // Populate test types
                testTypeSelect.innerHTML = '<option value="">Select test type...</option>';
                data.testTypes.forEach(testType => {
                    const option = document.createElement('option');
                    option.value = testType.id;
                    option.textContent = testType.name;
                    option.dataset.description = testType.description || '';
                    option.dataset.fields = JSON.stringify(testType.required_fields);
                    testTypeSelect.appendChild(option);
                });

                // Show required fields
                if (data.requiredFields.length > 0) {
                    const badgesHtml = data.requiredFields.map(field =>
                        `<span class="badge bg-info me-2 mb-2">${field}</span>`
                    ).join('');
                    document.getElementById('requiredFieldsBadges').innerHTML = badgesHtml;
                    document.getElementById('requiredFieldsList').style.display = 'block';
                }

                // Create dynamic input fields for custom requirements
                createDynamicFields(data.customFields, data.department.id);

                requirementsSection.style.display = 'block';
            }
        } catch (error) {
            console.error('Error loading department requirements:', error);
            alert('Failed to load department requirements');
        }
    });

    // Load test type details
    testTypeSelect.addEventListener('change', async function() {
        const testTypeId = this.value;
        const testTypeInfo = document.getElementById('testTypeInfo');

        if (!testTypeId) {
            testTypeInfo.style.display = 'none';
            return;
        }

        try {
            const response = await fetch('{{ route("tasks.get-test-type-details") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ test_type_id: testTypeId })
            });

            const data = await response.json();

            if (data.success) {
                document.getElementById('testTypeDescription').textContent =
                    data.testType.description || 'No description';
                document.getElementById('testTypeDuration').textContent =
                    data.testType.estimatedDuration;
                testTypeInfo.style.display = 'block';
            }
        } catch (error) {
            console.error('Error loading test type:', error);
        }
    });

    // Create dynamic input fields
    function createDynamicFields(customFields, departmentId) {
        dynamicFieldsContainer.innerHTML = '';

        if (!customFields || Object.keys(customFields).length === 0) {
            return;
        }

        Object.entries(customFields).forEach(([key, field]) => {
            const fieldDiv = document.createElement('div');
            fieldDiv.className = 'mb-3';

            let inputHtml = '';
            const fieldName = `department_requirements[${key}]`;

            if (field.type === 'select') {
                inputHtml = `
                    <select class="form-select" name="${fieldName}">
                        <option value="">Select ${field.label}...</option>
                        ${field.options.map(opt =>
                            `<option value="${opt}">${opt}</option>`
                        ).join('')}
                    </select>
                `;
            } else if (field.type === 'checkbox') {
                inputHtml = `
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="${fieldName}" id="${key}">
                        <label class="form-check-label" for="${key}">
                            ${field.label}
                        </label>
                    </div>
                `;
            } else if (field.type === 'textarea') {
                inputHtml = `
                    <textarea class="form-control" name="${fieldName}"
                              rows="${field.rows || 3}"
                              placeholder="${field.placeholder || ''}"></textarea>
                `;
            } else {
                // text, number, date, etc.
                inputHtml = `
                    <input type="${field.type || 'text'}"
                           class="form-control"
                           name="${fieldName}"
                           placeholder="${field.placeholder || ''}">
                `;
            }

            fieldDiv.innerHTML = `
                <label for="${key}" class="form-label">${field.label}</label>
                ${inputHtml}
            `;

            dynamicFieldsContainer.appendChild(fieldDiv);
        });
    }
});
</script>
@endpush

<style>
.badge-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

#dynamicFieldsContainer {
    background-color: #f8f9fa;
    padding: 1.5rem;
    border-radius: 0.25rem;
}
</style>
