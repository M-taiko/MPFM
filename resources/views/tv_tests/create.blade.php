@extends('layouts.master')

@section('title', 'Create Test Vessel Test')

@push('styles')
<style>
    .modern-form-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2.5rem 2rem;
        border-radius: 0.75rem;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.2);
    }

    .modern-form-header h1 {
        margin: 0;
        font-size: 2rem;
        font-weight: 700;
    }

    .modern-form-header p {
        margin: 0.5rem 0 0 0;
        opacity: 0.9;
    }

    .form-section {
        background: white;
        border-radius: 0.75rem;
        padding: 2rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid #e2e8f0;
    }

    .section-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #667eea;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .section-title i {
        color: #667eea;
        font-size: 1.5rem;
    }

    .form-group-modern {
        margin-bottom: 1.5rem;
    }

    .form-group-modern label {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.95rem;
    }

    .form-group-modern .text-danger {
        color: #e53e3e;
        font-weight: bold;
    }

    .form-control-modern {
        width: 100%;
        padding: 0.875rem;
        border: 2px solid #e2e8f0;
        border-radius: 0.5rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background-color: white;
        font-family: inherit;
    }

    .form-control-modern:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }

    .form-control-modern:disabled,
    .form-control-modern[readonly] {
        background-color: #f7fafc;
        color: #718096;
        cursor: not-allowed;
    }

    .input-with-icon {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-with-icon i {
        position: absolute;
        right: 1rem;
        color: #a0aec0;
        pointer-events: none;
    }

    .input-with-icon input,
    .input-with-icon select {
        padding-right: 2.75rem;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }

    .info-badge {
        display: inline-block;
        padding: 0.5rem 1rem;
        background: #edf2f7;
        border-left: 4px solid #667eea;
        border-radius: 0.4rem;
        font-size: 0.9rem;
        color: #2d3748;
        margin-top: 0.5rem;
    }

    .info-badge strong {
        color: #1a202c;
        display: block;
        font-size: 1rem;
    }

    .btn-modern {
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        font-size: 0.95rem;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-primary-modern {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-primary-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
        color: white;
        text-decoration: none;
    }

    .btn-secondary-modern {
        background: #e2e8f0;
        color: #2d3748;
    }

    .btn-secondary-modern:hover {
        background: #cbd5e0;
        color: #1a202c;
        text-decoration: none;
    }

    .form-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-start;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid #e2e8f0;
    }

    .alert-modern {
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
        border-left: 4px solid;
    }

    .alert-danger-modern {
        background: #fff5f5;
        border-left-color: #e53e3e;
        color: #742a2a;
    }

    .alert-info-modern {
        background: #ebf8ff;
        border-left-color: #2c7a7b;
        color: #234e52;
    }

    .data-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .data-card {
        background: #f7fafc;
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        padding: 1.25rem;
    }

    .data-card-label {
        font-size: 0.85rem;
        color: #718096;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .data-card-value {
        font-size: 1.1rem;
        color: #1a202c;
        font-weight: 600;
    }

    .loading-spinner {
        display: inline-block;
        width: 1rem;
        height: 1rem;
        border: 2px solid #e2e8f0;
        border-top-color: #667eea;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
        margin-right: 0.5rem;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .success-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.4rem 0.8rem;
        background: #c6f6d5;
        color: #22543d;
        border-radius: 0.3rem;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .success-badge i {
        color: #22863a;
    }
</style>
@endpush

@section('content')
<!-- Modern Header -->
<div class="modern-form-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fe fe-plus-circle" style="margin-right: 0.5rem;"></i>Create Test Vessel Test</h1>
        <p>Fill in the test measurements from your test vessel readings</p>
    </div>
    <a href="{{ route('tv_tests.index') }}" class="btn btn-modern btn-secondary-modern">
        <i class="fe fe-arrow-left"></i> Back to List
    </a>
</div>

@if($errors->any())
<div class="alert-modern alert-danger-modern">
    <strong><i class="fe fe-alert-circle"></i> Please fix the following errors:</strong>
    <ul class="mb-0" style="margin-top: 0.75rem;">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ route('tv_tests.store') }}" method="POST" id="testForm">
    @csrf

    <!-- Vessel & Location Information -->
    <div class="form-section">
        <div class="section-title">
            <i class="fe fe-anchor"></i>
            Vessel & Location Information
        </div>

        <div class="data-grid">
            <div class="data-card">
                <div class="data-card-label"><i class="fe fe-anchor"></i> Test Vessel</div>
                <div class="data-card-value" id="vesselDisplay">-</div>
            </div>
            <div class="data-card">
                <div class="data-card-label"><i class="fe fe-layers"></i> Oil Field</div>
                <div class="data-card-value" id="oilFieldDisplay">-</div>
            </div>
            <div class="data-card">
                <div class="data-card-label"><i class="fe fe-corner-down-right"></i> Tower</div>
                <div class="data-card-value" id="towerDisplay">-</div>
            </div>
            <div class="data-card">
                <div class="data-card-label"><i class="fe fe-trending-up"></i> Oil String</div>
                <div class="data-card-value" id="oilStringDisplay">-</div>
            </div>
        </div>

        <!-- Hidden fields for vessel/location (auto-populated) -->
        <input type="hidden" name="test_vessel_id" id="test_vessel_id" value="{{ old('test_vessel_id', $tvTest->test_vessel_id ?? '') }}">
        <input type="hidden" name="oil_field_id" id="oil_field_id" value="{{ old('oil_field_id', $tvTest->oil_field_id ?? '') }}">
        <input type="hidden" name="tower_id" id="tower_id" value="{{ old('tower_id', $tvTest->tower_id ?? '') }}">
        <input type="hidden" name="oil_string_id" id="oil_string_id" value="{{ old('oil_string_id', $tvTest->oil_string_id ?? '') }}">
        <input type="hidden" name="tv_test_request_well_id" id="tv_test_request_well_id" value="{{ old('tv_test_request_well_id', $tvTest->tv_test_request_well_id ?? '') }}">
    </div>

    <!-- Test Date & Shift -->
    <div class="form-section">
        <div class="section-title">
            <i class="fe fe-calendar"></i>
            Test Details
        </div>

        <div class="form-row">
            <div class="form-group-modern">
                <label for="test_date">
                    <i class="fe fe-calendar"></i>
                    Test Date <span class="text-danger">*</span>
                </label>
                <div class="input-with-icon">
                    <input type="date" name="test_date" id="test_date" class="form-control-modern"
                        value="{{ old('test_date', $tvTest->test_date?->format('Y-m-d') ?? '') }}"
                        required>
                    <i class="fe fe-calendar"></i>
                </div>
                @error('test_date')
                <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group-modern">
                <label for="shift">
                    <i class="fe fe-clock"></i>
                    Shift <span class="text-danger">*</span>
                </label>
                <div class="input-with-icon">
                    <select name="shift" id="shift" class="form-control-modern" required>
                        <option value="">-- Select Shift --</option>
                        <option value="morning" {{ old('shift', $tvTest->shift ?? '') == 'morning' ? 'selected' : '' }}>
                            <i class="fe fe-sunrise"></i> Morning
                        </option>
                        <option value="afternoon" {{ old('shift', $tvTest->shift ?? '') == 'afternoon' ? 'selected' : '' }}>
                            <i class="fe fe-sun"></i> Afternoon
                        </option>
                        <option value="night" {{ old('shift', $tvTest->shift ?? '') == 'night' ? 'selected' : '' }}>
                            <i class="fe fe-moon"></i> Night
                        </option>
                    </select>
                    <i class="fe fe-chevron-down"></i>
                </div>
                @error('shift')
                <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    <!-- Production Measurements -->
    <div class="form-section">
        <div class="section-title">
            <i class="fe fe-bar-chart-2"></i>
            Production Measurements
        </div>

        <div class="form-row">
            <div class="form-group-modern">
                <label for="oil_rate">
                    <i class="fe fe-droplets"></i>
                    Oil Rate (B/D) <span class="text-danger">*</span>
                </label>
                <div class="input-with-icon">
                    <input type="number" name="oil_rate" id="oil_rate" class="form-control-modern" step="0.01" min="0"
                        value="{{ old('oil_rate', $tvTest->oil_rate ?? '') }}" required>
                    <i class="fe fe-droplet"></i>
                </div>
                @error('oil_rate')
                <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group-modern">
                <label for="gas_rate">
                    <i class="fe fe-wind"></i>
                    Gas Rate (MMscf/D) <span class="text-danger">*</span>
                </label>
                <div class="input-with-icon">
                    <input type="number" name="gas_rate" id="gas_rate" class="form-control-modern" step="0.01" min="0"
                        value="{{ old('gas_rate', $tvTest->gas_rate ?? '') }}" required>
                    <i class="fe fe-wind"></i>
                </div>
                @error('gas_rate')
                <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group-modern">
                <label for="water_cut">
                    <i class="fe fe-percent"></i>
                    Water Cut (%) <span class="text-danger">*</span>
                </label>
                <div class="input-with-icon">
                    <input type="number" name="water_cut" id="water_cut" class="form-control-modern" step="0.01" min="0" max="100"
                        value="{{ old('water_cut', $tvTest->water_cut ?? '') }}" required>
                    <i class="fe fe-percent"></i>
                </div>
                @error('water_cut')
                <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    <!-- Pressure & Temperature -->
    <div class="form-section">
        <div class="section-title">
            <i class="fe fe-thermometer"></i>
            Pressure & Temperature
        </div>

        <div class="form-row">
            <div class="form-group-modern">
                <label for="pressure">
                    <i class="fe fe-trending-up"></i>
                    Pressure (psia) <span class="text-danger">*</span>
                </label>
                <div class="input-with-icon">
                    <input type="number" name="pressure" id="pressure" class="form-control-modern" step="0.01" min="0"
                        value="{{ old('pressure', $tvTest->pressure ?? '') }}" required>
                    <i class="fe fe-trending-up"></i>
                </div>
                @error('pressure')
                <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group-modern">
                <label for="temperature">
                    <i class="fe fe-thermometer"></i>
                    Temperature (°F) <span class="text-danger">*</span>
                </label>
                <div class="input-with-icon">
                    <input type="number" name="temperature" id="temperature" class="form-control-modern" step="0.01" min="-50" max="150"
                        value="{{ old('temperature', $tvTest->temperature ?? '') }}" required>
                    <i class="fe fe-thermometer"></i>
                </div>
                @error('temperature')
                <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group-modern">
                <label for="gor">
                    <i class="fe fe-activity"></i>
                    GOR (scf/stb)
                </label>
                <div class="input-with-icon">
                    <input type="number" name="gor" id="gor" class="form-control-modern" step="0.01" min="0"
                        value="{{ old('gor', $tvTest->gor ?? '') }}">
                    <i class="fe fe-activity"></i>
                </div>
                @error('gor')
                <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    <!-- Optional Linkage -->
    <div class="form-section">
        <div class="section-title">
            <i class="fe fe-link-2"></i>
            MPFM Correlation
        </div>

        <div class="form-group-modern">
            <label for="mpfm_test_id">
                <i class="fe fe-link-2"></i>
                Link to MPFM Test (optional)
            </label>
            <div class="input-with-icon">
                <select name="mpfm_test_id" id="mpfm_test_id" class="form-control-modern">
                    <option value="">-- No Link --</option>
                    @if(isset($mpfmTests))
                        @foreach($mpfmTests as $test)
                        <option value="{{ $test->id }}"
                            {{ old('mpfm_test_id', $tvTest->mpfm_test_id ?? '') == $test->id ? 'selected' : '' }}>
                            Test #{{ $test->id }} - {{ $test->test_date->format('Y-m-d') }}
                        </option>
                        @endforeach
                    @endif
                </select>
                <i class="fe fe-chevron-down"></i>
            </div>
            <div class="alert-modern alert-info-modern" style="margin-top: 0.75rem;">
                <i class="fe fe-info"></i> Link this test to an MPFM test to enable automatic comparison
            </div>
        </div>
    </div>

    <!-- Notes -->
    <div class="form-section">
        <div class="section-title">
            <i class="fe fe-edit-3"></i>
            Additional Information
        </div>

        <div class="form-group-modern">
            <label for="notes">
                <i class="fe fe-edit-3"></i>
                Notes & Observations
            </label>
            <textarea name="notes" id="notes" rows="4" class="form-control-modern" style="resize: vertical;"
                placeholder="Add any notes or observations about this test...">{{ old('notes', $tvTest->notes ?? '') }}</textarea>
            @error('notes')
            <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="form-actions">
        <button type="submit" name="action" value="save_draft" class="btn btn-modern btn-primary-modern">
            <i class="fe fe-save"></i> Save as Draft
        </button>
        @if($tvTest->tv_test_request_well_id)
        <button type="submit" name="action" value="submit" class="btn btn-modern" style="background: #48bb78; color: white;">
            <i class="fe fe-check"></i> Save & Submit for Review
        </button>
        @endif
        <a href="{{ route('tv_tests.index') }}" class="btn btn-modern btn-secondary-modern">
            <i class="fe fe-x"></i> Cancel
        </a>
    </div>

    <!-- Hidden status field -->
    <input type="hidden" name="status" id="status" value="draft">
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-populate vessel/location display from hidden fields
    function updateDisplay() {
        const vesselId = document.getElementById('test_vessel_id').value;
        const oilFieldId = document.getElementById('oil_field_id').value;
        const towerId = document.getElementById('tower_id').value;
        const oilStringId = document.getElementById('oil_string_id').value;

        // Get names from data attributes
        @if(isset($testVessels))
        const vessels = {
            @foreach($testVessels as $v)
            '{{ $v->id }}': '{{ $v->name }}',
            @endforeach
        };
        document.getElementById('vesselDisplay').textContent = vessels[vesselId] || '-';
        @endif

        @if(isset($oilFields))
        const fields = {
            @foreach($oilFields as $f)
            '{{ $f->id }}': '{{ $f->name }}',
            @endforeach
        };
        document.getElementById('oilFieldDisplay').textContent = fields[oilFieldId] || '-';
        @endif

        // Towers and oil strings are from collections, so we'll use AJAX
        if (towerId && oilFieldId) {
            $.get('/ajax/operational-plans/towers/' + oilFieldId, function(data) {
                const tower = data.find(t => t.id == towerId);
                document.getElementById('towerDisplay').textContent = tower?.name || '-';
            });
        }

        if (oilStringId && towerId) {
            $.get('/ajax/operational-plans/oil-strings/' + towerId, function(data) {
                const string = data.find(s => s.id == oilStringId);
                document.getElementById('oilStringDisplay').textContent = string?.name || '-';
            });
        }
    }

    // Update display on load
    updateDisplay();

    // Handle form submission
    document.getElementById('testForm').addEventListener('submit', function(e) {
        const action = e.submitter?.value;
        if (action === 'submit') {
            document.getElementById('status').value = 'submitted';
        }
    });
});
</script>
@endpush
