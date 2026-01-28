@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2>Create New Task</h2>
            <p class="text-muted">Select tower, oil string, and department to see dynamic requirements</p>
        </div>
    </div>

    <form action="{{ route('tasks.store-advanced') }}" method="POST" id="taskForm">
        @csrf

        <div class="row">
            <!-- Left Column: Basic Info -->
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">Basic Information</h5>
                    </div>
                    <div class="card-body">
                        <!-- Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Task Title *</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror"
                                   id="title" name="title" required value="{{ old('title') }}"
                                   placeholder="Enter task title">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3"
                                      placeholder="Task description">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Priority -->
                        <div class="mb-3">
                            <label for="priority" class="form-label">Priority *</label>
                            <select class="form-select @error('priority') is-invalid @enderror"
                                    id="priority" name="priority" required>
                                <option value="">Select priority...</option>
                                <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                                <option value="critical" {{ old('priority') === 'critical' ? 'selected' : '' }}>Critical</option>
                            </select>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Expected Completion -->
                        <div class="mb-3">
                            <label for="expected_completion_at" class="form-label">Expected Completion</label>
                            <input type="date"
                                   class="form-control @error('expected_completion_at') is-invalid @enderror"
                                   id="expected_completion_at" name="expected_completion_at"
                                   value="{{ old('expected_completion_at') }}">
                            @error('expected_completion_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Location Selection -->
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">Location & Asset Selection</h5>
                    </div>
                    <div class="card-body">
                        <!-- Tower Selection -->
                        <div class="mb-3">
                            <label for="tower_id" class="form-label">Tower *</label>
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
                            <label for="oil_string_id" class="form-label">Oil String *</label>
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
                            <label for="department_id" class="form-label">Department *</label>
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
