@extends('layouts.app')

@section('title', 'Create MPFM Plan')

@section('content')
<div class="container">
    <h2>Create MPFM Plan for Well Test: {{ $wellTest->well ?? 'N/A' }}</h2>
    
    <form action="{{ route('mpfm_plans.store', $wellTest->id) }}" method="POST">
        @csrf
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="name">Plan Name</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="expected_choke">Expected Choke Size</label>
                    <input type="text" name="expected_choke" id="expected_choke" class="form-control">
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="planned_oil_rate">Planned Oil Rate (STB/d)</label>
                    <input type="number" step="0.01" name="planned_oil_rate" id="planned_oil_rate" class="form-control">
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="planned_gas_rate">Planned Gas Rate (MMScf/d)</label>
                    <input type="number" step="0.01" name="planned_gas_rate" id="planned_gas_rate" class="form-control">
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="planned_water_rate">Planned Water Rate (STB/d)</label>
                    <input type="number" step="0.01" name="planned_water_rate" id="planned_water_rate" class="form-control">
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="expected_gor">Expected GOR (Scf/STB)</label>
                    <input type="number" step="0.01" name="expected_gor" id="expected_gor" class="form-control">
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="expected_bsw">Expected BSW (%)</label>
                    <input type="number" step="0.01" name="expected_bsw" id="expected_bsw" class="form-control">
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="expected_duration">Expected Duration (minutes)</label>
                    <input type="number" name="expected_duration" id="expected_duration" class="form-control">
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label for="test_objective">Test Objective</label>
            <textarea name="test_objective" id="test_objective" class="form-control"></textarea>
        </div>
        
        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" class="form-control"></textarea>
        </div>
        
        <h3>Plan Steps</h3>
        <div id="steps-container">
            <div class="step-item">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Step #</label>
                            <input type="number" name="steps[0][step_number]" class="form-control" value="1" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Step Name</label>
                            <input type="text" name="steps[0][name]" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Choke Setting</label>
                            <input type="text" name="steps[0][choke]" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Time Window</label>
                            <input type="text" name="steps[0][time_window]" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Planned Oil Rate (STB/d)</label>
                            <input type="number" step="0.01" name="steps[0][planned_rates][oil_rate]" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Planned Gas Rate (MMScf/d)</label>
                            <input type="number" step="0.01" name="steps[0][planned_rates][gas_rate]" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Planned Water Rate (STB/d)</label>
                            <input type="number" step="0.01" name="steps[0][planned_rates][water_rate]" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="steps[0][description]" class="form-control"></textarea>
                </div>
            </div>
        </div>
        
        <button type="button" id="add-step" class="btn btn-secondary">Add Step</button>
        
        <div class="form-group mt-3">
            <button type="submit" class="btn btn-primary">Create Plan</button>
            <a href="{{ route('well_tests.show', $wellTest->id) }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
document.getElementById('add-step').addEventListener('click', function() {
    const container = document.getElementById('steps-container');
    const stepCount = container.children.length;
    
    const stepHtml = `
        <div class="step-item border p-3 mb-3">
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Step #</label>
                        <input type="number" name="steps[${stepCount}][step_number]" class="form-control" value="${stepCount + 1}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Step Name</label>
                        <input type="text" name="steps[${stepCount}][name]" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Choke Setting</label>
                        <input type="text" name="steps[${stepCount}][choke]" class="form-control">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Time Window</label>
                        <input type="text" name="steps[${stepCount}][time_window]" class="form-control">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Planned Oil Rate (STB/d)</label>
                        <input type="number" step="0.01" name="steps[${stepCount}][planned_rates][oil_rate]" class="form-control">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Planned Gas Rate (MMScf/d)</label>
                        <input type="number" step="0.01" name="steps[${stepCount}][planned_rates][gas_rate]" class="form-control">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Planned Water Rate (STB/d)</label>
                        <input type="number" step="0.01" name="steps[${stepCount}][planned_rates][water_rate]" class="form-control">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="steps[${stepCount}][description]" class="form-control"></textarea>
            </div>
            <button type="button" class="btn btn-danger remove-step">Remove Step</button>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', stepHtml);
    
    // Add event listener to the new remove button
    container.lastElementChild.querySelector('.remove-step').addEventListener('click', function() {
        this.parentElement.remove();
    });
});

// Add event listeners to existing remove buttons
document.querySelectorAll('.remove-step').forEach(button => {
    button.addEventListener('click', function() {
        this.parentElement.remove();
    });
});
</script>
@endsection