@extends('layouts.master')

@section('title', 'Compare Test Vessel with MPFM')

@push('styles')
<style>
    .modern-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2.5rem 2rem;
        border-radius: 0.75rem;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.2);
    }

    .modern-header h1 {
        margin: 0;
        font-size: 2rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .modern-header p {
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

    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .form-group-modern {
        display: flex;
        flex-direction: column;
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
    }

    .form-control-modern {
        padding: 0.875rem;
        border: 2px solid #e2e8f0;
        border-radius: 0.5rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background-color: white;
        font-family: inherit;
        cursor: pointer;
    }

    .form-control-modern:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }

    .select-hint {
        font-size: 0.85rem;
        color: #718096;
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .select-hint i {
        color: #a0aec0;
    }

    .filter-section {
        background: #f7fafc;
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .filter-title {
        font-size: 1rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .filter-options {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .filter-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: white;
        border: 1px solid #cbd5e0;
        border-radius: 0.3rem;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 0.9rem;
    }

    .filter-chip:hover {
        background: #667eea;
        color: white;
        border-color: #667eea;
    }

    .filter-chip.active {
        background: #667eea;
        color: white;
        border-color: #667eea;
    }

    .comparison-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .comparison-card {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid #e2e8f0;
    }

    .comparison-card-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: white;
        padding: 1rem;
        margin: -1.5rem -1.5rem 1.5rem -1.5rem;
        border-radius: 0.75rem 0.75rem 0 0;
    }

    .comparison-card-title.tv {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .comparison-card-title.mpfm {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
    }

    .comparison-item {
        padding: 0.75rem 0;
        border-bottom: 1px solid #e2e8f0;
    }

    .comparison-item:last-child {
        border-bottom: none;
    }

    .comparison-label {
        font-size: 0.85rem;
        color: #718096;
        text-transform: uppercase;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .comparison-value {
        font-size: 1.1rem;
        color: #1a202c;
        font-weight: 600;
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
        justify-content: center;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid #e2e8f0;
    }

    .alert-modern {
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
        border-left: 4px solid;
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
    }

    .alert-info-modern {
        background: #ebf8ff;
        border-left-color: #2c7a7b;
        color: #234e52;
    }

    .alert-warning-modern {
        background: #fffaf0;
        border-left-color: #c05621;
        color: #7c2d12;
    }

    .stats-display {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        padding: 1rem;
        background: #f7fafc;
        border-radius: 0.5rem;
        margin-top: 1rem;
    }

    .stat-item {
        text-align: center;
    }

    .stat-label {
        font-size: 0.85rem;
        color: #718096;
        font-weight: 600;
        text-transform: uppercase;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #667eea;
        margin-top: 0.25rem;
    }

    .empty-state {
        text-align: center;
        padding: 2rem;
        background: #f7fafc;
        border-radius: 0.75rem;
        border: 2px dashed #cbd5e0;
    }

    .empty-state i {
        font-size: 3rem;
        color: #cbd5e0;
        margin-bottom: 1rem;
    }

    .empty-state p {
        color: #718096;
        margin: 0;
    }

    .test-info-badge {
        display: inline-block;
        background: #edf2f7;
        border-left: 3px solid #667eea;
        padding: 0.5rem 0.75rem;
        border-radius: 0.3rem;
        font-size: 0.85rem;
        color: #2d3748;
        margin-top: 0.5rem;
    }

    @media (max-width: 768px) {
        .comparison-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<!-- Modern Header -->
<div class="modern-header">
    <h1>
        <i class="fe fe-shuffle"></i>
        Compare Test Vessel with MPFM
    </h1>
    <p>Select a Test Vessel Test and an MPFM Test to compare their measurements side by side</p>
</div>

<!-- Alert -->
<div class="alert-modern alert-info-modern">
    <i class="fe fe-info"></i>
    <div>
        <strong>How to compare:</strong> Choose a Test Vessel Test from your measurements and an MPFM Test from the MPFM database. You can select tests from any status (submitted, draft, or approved) to analyze the differences.
    </div>
</div>

<form method="GET" action="{{ route('tv_tests.compare_result') }}" id="compareForm">
    <!-- Test Vessel Test Selection -->
    <div class="form-section">
        <div class="section-title">
            <i class="fe fe-anchor"></i>
            Select Test Vessel Test
        </div>

        <div class="form-row">
            <div class="form-group-modern">
                <label for="tv_test_id">
                    <i class="fe fe-list"></i>
                    Test Vessel Test <span class="text-danger">*</span>
                </label>
                <select name="tv_test_id" id="tv_test_id" class="form-control-modern" required onchange="updateTVTestInfo()">
                    <option value="">-- Select Test Vessel Test --</option>
                    @foreach($tvTests as $test)
                    <option value="{{ $test->id }}"
                        data-vessel="{{ $test->testVessel->name }}"
                        data-field="{{ $test->oilField->name }}"
                        data-tower="{{ $test->tower->name }}"
                        data-string="{{ $test->oilString->name }}"
                        data-date="{{ $test->test_date->format('Y-m-d') }}"
                        data-status="{{ $test->status }}">
                        TV #{{ $test->id }} - {{ $test->testVessel->name }} - {{ $test->test_date->format('Y-m-d') }} ({{ ucfirst($test->status) }})
                    </option>
                    @endforeach
                </select>
                <div class="select-hint">
                    <i class="fe fe-check"></i>
                    {{ count($tvTests) }} tests available
                </div>
            </div>
        </div>

        <!-- TV Test Details -->
        <div id="tvTestDetails" style="display: none;">
            <div class="stats-display">
                <div class="stat-item">
                    <div class="stat-label">Vessel</div>
                    <div class="stat-value" id="tvVessel">-</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Oil Field</div>
                    <div class="stat-value" id="tvField">-</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Tower</div>
                    <div class="stat-value" id="tvTower">-</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Oil String</div>
                    <div class="stat-value" id="tvString">-</div>
                </div>
            </div>
        </div>
    </div>

    <!-- MPFM Test Selection -->
    <div class="form-section">
        <div class="section-title">
            <i class="fe fe-activity"></i>
            Select MPFM Test
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <div class="filter-title">
                <i class="fe fe-filter"></i>
                Filter by Oil Field (Optional)
            </div>
            <div class="filter-options">
                <div class="filter-chip" onclick="filterMPFMByField(null, this)">
                    <i class="fe fe-check"></i> All Oil Fields
                </div>
                @php
                    $uniqueFields = $mpfmTests->pluck('oilField')->unique();
                @endphp
                @foreach($uniqueFields as $field)
                <div class="filter-chip" onclick="filterMPFMByField({{ $field->id }}, this)">
                    {{ $field->name }}
                </div>
                @endforeach
            </div>
        </div>

        <div class="form-row">
            <div class="form-group-modern">
                <label for="mpfm_test_id">
                    <i class="fe fe-list"></i>
                    MPFM Test <span class="text-danger">*</span>
                </label>
                <select name="mpfm_test_id" id="mpfm_test_id" class="form-control-modern" required onchange="updateMPFMTestInfo()">
                    <option value="">-- Select MPFM Test --</option>
                    @foreach($mpfmTests as $test)
                    <option value="{{ $test->id }}"
                        data-field="{{ $test->oilField->name }}"
                        data-tower="{{ $test->tower->name }}"
                        data-string="{{ $test->oilString->name }}"
                        data-date="{{ $test->test_date->format('Y-m-d') }}"
                        data-status="{{ $test->status }}"
                        data-oil-field-id="{{ $test->oil_field_id }}">
                        MPFM #{{ $test->id }} - {{ $test->oilField->name }} - {{ $test->test_date->format('Y-m-d') }} ({{ ucfirst($test->status) }})
                    </option>
                    @endforeach
                </select>
                <div class="select-hint">
                    <i class="fe fe-check"></i>
                    {{ count($mpfmTests) }} tests available
                </div>
            </div>
        </div>

        <!-- MPFM Test Details -->
        <div id="mpfmTestDetails" style="display: none;">
            <div class="stats-display">
                <div class="stat-item">
                    <div class="stat-label">Oil Field</div>
                    <div class="stat-value" id="mpfmField">-</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Tower</div>
                    <div class="stat-value" id="mpfmTower">-</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Oil String</div>
                    <div class="stat-value" id="mpfmString">-</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Status</div>
                    <div class="stat-value" id="mpfmStatus">-</div>
                </div>
            </div>
        </div>

        <!-- Matching Wells Alert -->
        <div id="matchingAlert" style="display: none; margin-top: 1.5rem;">
            <div class="alert-modern alert-warning-modern">
                <i class="fe fe-alert-triangle"></i>
                <div>
                    <strong>Different Wells Selected:</strong> The selected tests are from different oil strings. Comparison may not be meaningful.
                </div>
            </div>
        </div>

        <div id="matchingSuccess" style="display: none; margin-top: 1.5rem;">
            <div class="alert-modern" style="background: #c6f6d5; border-left-color: #22543d; color: #22543d;">
                <i class="fe fe-check-circle"></i>
                <div>
                    <strong>Same Well:</strong> Both tests are from the same oil string. Comparison is ready!
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="form-section">
        <div class="form-actions">
            <button type="submit" class="btn btn-modern btn-primary-modern" id="submitBtn" disabled>
                <i class="fe fe-arrow-right"></i> Compare Tests
            </button>
            <a href="{{ route('tv_tests.index') }}" class="btn btn-modern btn-secondary-modern">
                <i class="fe fe-x"></i> Cancel
            </a>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
function updateTVTestInfo() {
    const select = document.getElementById('tv_test_id');
    const option = select.options[select.selectedIndex];

    if (option.value) {
        document.getElementById('tvVessel').textContent = option.getAttribute('data-vessel');
        document.getElementById('tvField').textContent = option.getAttribute('data-field');
        document.getElementById('tvTower').textContent = option.getAttribute('data-tower');
        document.getElementById('tvString').textContent = option.getAttribute('data-string');
        document.getElementById('tvTestDetails').style.display = 'block';
        checkMatchingWells();
    } else {
        document.getElementById('tvTestDetails').style.display = 'none';
        document.getElementById('matchingAlert').style.display = 'none';
        document.getElementById('matchingSuccess').style.display = 'none';
    }
    checkFormValidity();
}

function updateMPFMTestInfo() {
    const select = document.getElementById('mpfm_test_id');
    const option = select.options[select.selectedIndex];

    if (option.value) {
        document.getElementById('mpfmField').textContent = option.getAttribute('data-field');
        document.getElementById('mpfmTower').textContent = option.getAttribute('data-tower');
        document.getElementById('mpfmString').textContent = option.getAttribute('data-string');
        document.getElementById('mpfmStatus').textContent = option.getAttribute('data-status').charAt(0).toUpperCase() + option.getAttribute('data-status').slice(1);
        document.getElementById('mpfmTestDetails').style.display = 'block';
        checkMatchingWells();
    } else {
        document.getElementById('mpfmTestDetails').style.display = 'none';
        document.getElementById('matchingAlert').style.display = 'none';
        document.getElementById('matchingSuccess').style.display = 'none';
    }
    checkFormValidity();
}

function checkMatchingWells() {
    const tvSelect = document.getElementById('tv_test_id');
    const mpfmSelect = document.getElementById('mpfm_test_id');

    if (tvSelect.value && mpfmSelect.value) {
        const tvString = tvSelect.options[tvSelect.selectedIndex].getAttribute('data-string');
        const mpfmString = mpfmSelect.options[mpfmSelect.selectedIndex].getAttribute('data-string');

        if (tvString === mpfmString) {
            document.getElementById('matchingSuccess').style.display = 'block';
            document.getElementById('matchingAlert').style.display = 'none';
        } else {
            document.getElementById('matchingAlert').style.display = 'block';
            document.getElementById('matchingSuccess').style.display = 'none';
        }
    }
}

function checkFormValidity() {
    const tvTestId = document.getElementById('tv_test_id').value;
    const mpfmTestId = document.getElementById('mpfm_test_id').value;
    document.getElementById('submitBtn').disabled = !tvTestId || !mpfmTestId;
}

function filterMPFMByField(fieldId, element) {
    // Update active state
    document.querySelectorAll('.filter-chip').forEach(chip => chip.classList.remove('active'));
    element.classList.add('active');

    // Filter options
    const mpfmSelect = document.getElementById('mpfm_test_id');
    const options = mpfmSelect.querySelectorAll('option');

    options.forEach(option => {
        if (option.value === '') {
            option.style.display = 'block';
        } else if (fieldId === null || option.getAttribute('data-oil-field-id') == fieldId) {
            option.style.display = 'block';
        } else {
            option.style.display = 'none';
        }
    });

    // Reset selection if current is hidden
    if (mpfmSelect.selectedIndex > 0 && mpfmSelect.options[mpfmSelect.selectedIndex].style.display === 'none') {
        mpfmSelect.value = '';
        document.getElementById('mpfmTestDetails').style.display = 'none';
    }
    checkFormValidity();
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    checkFormValidity();
});
</script>
@endpush
