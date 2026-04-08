@extends('layouts.master')

@section('title', 'Test Vessel Test #'.$tvTest->id)

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
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
    }

    .modern-header p {
        margin: 0.5rem 0 0 0;
        opacity: 0.9;
    }

    .status-badge {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 0.4rem;
        font-weight: 600;
        font-size: 0.9rem;
        text-transform: capitalize;
    }

    .status-draft { background: #e2e8f0; color: #2d3748; }
    .status-submitted { background: #bee3f8; color: #2c5282; }
    .status-approved { background: #c6f6d5; color: #22543d; }
    .status-rejected { background: #fed7d7; color: #742a2a; }

    .card-modern {
        background: white;
        border-radius: 0.75rem;
        padding: 2rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid #e2e8f0;
    }

    .section-title {
        font-size: 1.25rem;
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
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }

    .info-card {
        background: #f7fafc;
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        padding: 1.25rem;
    }

    .info-label {
        font-size: 0.85rem;
        color: #718096;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .info-value {
        font-size: 1.1rem;
        color: #1a202c;
        font-weight: 600;
    }

    .info-value a {
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
    }

    .info-value a:hover {
        text-decoration: underline;
    }

    .measurements-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
    }

    .measurement-card {
        background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
        border-left: 4px solid #667eea;
        border-radius: 0.5rem;
        padding: 1.5rem;
        text-align: center;
    }

    .measurement-icon {
        font-size: 2rem;
        color: #667eea;
        margin-bottom: 0.5rem;
    }

    .measurement-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1a202c;
        margin: 0.5rem 0;
    }

    .measurement-unit {
        font-size: 0.85rem;
        color: #718096;
        font-weight: 500;
    }

    .charts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .chart-container {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid #e2e8f0;
    }

    .chart-title {
        font-size: 1rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
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

    .btn-comparison {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        color: white;
    }

    .btn-comparison:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(72, 187, 120, 0.3);
        color: white;
        text-decoration: none;
    }

    .alert-modern {
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
        border-left: 4px solid;
    }

    .alert-info-modern {
        background: #ebf8ff;
        border-left-color: #2c7a7b;
        color: #234e52;
    }

    .notes-box {
        background: #f7fafc;
        border-left: 4px solid #667eea;
        border-radius: 0.5rem;
        padding: 1.5rem;
        margin: 1.5rem 0;
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid #e2e8f0;
        flex-wrap: wrap;
    }

    .comparison-alert {
        background: linear-gradient(135deg, #fff5f5 0%, #fed7d7 100%);
        border-left: 4px solid #e53e3e;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }

    .comparison-alert strong {
        color: #742a2a;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .no-mpfm-yet {
        text-align: center;
        padding: 2rem;
        background: #f7fafc;
        border-radius: 0.75rem;
        border: 2px dashed #cbd5e0;
    }

    .no-mpfm-yet i {
        font-size: 3rem;
        color: #cbd5e0;
        margin-bottom: 1rem;
    }

    .comparison-button-group {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }
</style>
@endpush

@section('content')
<!-- Modern Header -->
<div class="modern-header d-flex justify-content-between align-items-center">
    <div>
        <h1>
            <i class="fe fe-activity" style="margin-right: 0.5rem;"></i>
            Test Vessel Test #{{ $tvTest->id }}
        </h1>
        <p>
            <span class="status-badge status-{{ $tvTest->status }}">{{ ucfirst($tvTest->status) }}</span>
            <span style="margin-left: 1rem; opacity: 0.9;">
                {{ $tvTest->test_date->format('Y-m-d') }} • {{ ucfirst($tvTest->shift) }} Shift
            </span>
        </p>
    </div>
    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
        @can('update', $tvTest)
        <a href="{{ route('tv_tests.edit', $tvTest->id) }}" class="btn btn-modern btn-primary-modern">
            <i class="fe fe-edit"></i> Edit
        </a>
        @endcan
        <a href="{{ route('tv_tests.index') }}" class="btn btn-modern btn-secondary-modern">
            <i class="fe fe-arrow-left"></i> Back
        </a>
    </div>
</div>

<!-- Test Information -->
<div class="card-modern">
    <div class="section-title">
        <i class="fe fe-info"></i>
        Test Information
    </div>

    <div class="info-grid">
        <div class="info-card">
            <div class="info-label"><i class="fe fe-anchor"></i> Test Vessel</div>
            <div class="info-value">
                <a href="{{ route('test_vessels.show', $tvTest->testVessel->id) }}">
                    {{ $tvTest->testVessel->name }}
                </a>
            </div>
        </div>

        <div class="info-card">
            <div class="info-label"><i class="fe fe-layers"></i> Oil Field</div>
            <div class="info-value">
                <a href="{{ route('oil_fields.show', $tvTest->oilField->id) }}">
                    {{ $tvTest->oilField->name }}
                </a>
            </div>
        </div>

        <div class="info-card">
            <div class="info-label"><i class="fe fe-corner-down-right"></i> Tower</div>
            <div class="info-value">
                <a href="{{ route('towers.show', $tvTest->tower->id) }}">
                    {{ $tvTest->tower->name }}
                </a>
            </div>
        </div>

        <div class="info-card">
            <div class="info-label"><i class="fe fe-trending-up"></i> Oil String</div>
            <div class="info-value">{{ $tvTest->oilString->name }}</div>
        </div>

        <div class="info-card">
            <div class="info-label"><i class="fe fe-user"></i> Operator</div>
            <div class="info-value">{{ $tvTest->user->name }}</div>
        </div>

        <div class="info-card">
            <div class="info-label"><i class="fe fe-calendar"></i> Created</div>
            <div class="info-value">{{ $tvTest->created_at->format('Y-m-d H:i') }}</div>
        </div>
    </div>
</div>

<!-- Measurements -->
<div class="card-modern">
    <div class="section-title">
        <i class="fe fe-bar-chart-2"></i>
        Measurements
    </div>

    <div class="measurements-grid">
        <div class="measurement-card">
            <div class="measurement-icon"><i class="fe fe-droplet"></i></div>
            <div class="measurement-unit">Oil Rate</div>
            <div class="measurement-value">{{ number_format($tvTest->oil_rate, 2) }}</div>
            <div class="measurement-unit">B/D</div>
        </div>

        <div class="measurement-card">
            <div class="measurement-icon"><i class="fe fe-wind"></i></div>
            <div class="measurement-unit">Gas Rate</div>
            <div class="measurement-value">{{ number_format($tvTest->gas_rate, 2) }}</div>
            <div class="measurement-unit">MMscf/D</div>
        </div>

        <div class="measurement-card">
            <div class="measurement-icon"><i class="fe fe-percent"></i></div>
            <div class="measurement-unit">Water Cut</div>
            <div class="measurement-value">{{ number_format($tvTest->water_cut, 2) }}</div>
            <div class="measurement-unit">%</div>
        </div>

        <div class="measurement-card">
            <div class="measurement-icon"><i class="fe fe-trending-up"></i></div>
            <div class="measurement-unit">Pressure</div>
            <div class="measurement-value">{{ number_format($tvTest->pressure, 2) }}</div>
            <div class="measurement-unit">psia</div>
        </div>

        <div class="measurement-card">
            <div class="measurement-icon"><i class="fe fe-thermometer"></i></div>
            <div class="measurement-unit">Temperature</div>
            <div class="measurement-value">{{ number_format($tvTest->temperature, 2) }}</div>
            <div class="measurement-unit">°F</div>
        </div>

        <div class="measurement-card">
            <div class="measurement-icon"><i class="fe fe-activity"></i></div>
            <div class="measurement-unit">GOR</div>
            <div class="measurement-value">{{ $tvTest->gor ? number_format($tvTest->gor, 2) : 'N/A' }}</div>
            <div class="measurement-unit">scf/stb</div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="card-modern">
    <div class="section-title">
        <i class="fe fe-bar-chart-2"></i>
        Visual Analysis
    </div>

    <div class="charts-grid">
        <!-- Production Metrics Chart -->
        <div class="chart-container">
            <div class="chart-title">
                <i class="fe fe-droplet"></i>
                Production Rates
            </div>
            <canvas id="productionChart"></canvas>
        </div>

        <!-- Conditions Chart -->
        <div class="chart-container">
            <div class="chart-title">
                <i class="fe fe-thermometer"></i>
                Well Conditions
            </div>
            <canvas id="conditionsChart"></canvas>
        </div>

        <!-- Measurement Comparison Radar -->
        <div class="chart-container">
            <div class="chart-title">
                <i class="fe fe-activity"></i>
                Measurement Profile
            </div>
            <canvas id="radarChart"></canvas>
        </div>
    </div>
</div>

<!-- Notes -->
@if($tvTest->notes)
<div class="card-modern">
    <div class="section-title">
        <i class="fe fe-edit-3"></i>
        Notes & Observations
    </div>
    <div class="notes-box">
        {{ $tvTest->notes }}
    </div>
</div>
@endif

<!-- MPFM Comparison Section -->
<div class="card-modern">
    <div class="section-title">
        <i class="fe fe-shuffle"></i>
        MPFM Test Comparison
    </div>

    @php
        // Get MPFM tests for the same well - any status, any date
        $mpfmTestsForWell = \App\Models\MPFMTest::where('oil_string_id', $tvTest->oil_string_id)
            ->orderBy('test_date', 'desc')
            ->get();

        // Also get tests within ±7 days for better match
        $mpfmTestsNearby = \App\Models\MPFMTest::where('oil_string_id', $tvTest->oil_string_id)
            ->where('test_date', '>=', $tvTest->test_date->copy()->subDays(7))
            ->where('test_date', '<=', $tvTest->test_date->copy()->addDays(7))
            ->orderBy('test_date', 'desc')
            ->get();
    @endphp

    @if($tvTest->mpfmTest)
    <!-- Already Linked -->
    <div class="alert-modern alert-info-modern">
        <strong><i class="fe fe-check-circle"></i> Linked MPFM Test</strong>
        <p style="margin-bottom: 1rem;">
            This test is linked to MPFM Test #{{ $tvTest->mpfmTest->id }} ({{ $tvTest->mpfmTest->test_date->format('Y-m-d') }}) - Status: <span class="badge-modern status-{{ $tvTest->mpfmTest->status }}">{{ ucfirst($tvTest->mpfmTest->status) }}</span>
        </p>
        <a href="{{ route('tv_tests.compare_result', ['tv_test_id' => $tvTest->id, 'mpfm_test_id' => $tvTest->mpfmTest->id]) }}"
           class="btn btn-modern btn-comparison">
            <i class="fe fe-bar-chart-2"></i> View Comparison
        </a>
    </div>
    @elseif($mpfmTestsNearby->count() > 0)
    <!-- Available MPFM Tests Within ±7 Days -->
    <div class="alert-modern alert-info-modern" style="background: linear-gradient(135deg, #c6f6d5 0%, #9ae6b4 100%); border-left-color: #22543d; color: #22543d;">
        <strong><i class="fe fe-check-circle"></i> Matching MPFM Tests Found (±7 days)</strong>
        <p style="margin-bottom: 1rem;">
            Found <strong>{{ $mpfmTestsNearby->count() }}</strong> MPFM test(s) on the same oil string within ±7 days
        </p>
    </div>

    <div class="comparison-button-group">
        @foreach($mpfmTestsNearby as $mpfmTest)
        <a href="{{ route('tv_tests.compare_result', ['tv_test_id' => $tvTest->id, 'mpfm_test_id' => $mpfmTest->id]) }}"
           class="btn btn-modern btn-comparison">
            <i class="fe fe-shuffle"></i> Compare with MPFM #{{ $mpfmTest->id }}
            <span style="font-size: 0.85rem; opacity: 0.9;">({{ $mpfmTest->test_date->format('Y-m-d') }} - {{ ucfirst($mpfmTest->status) }})</span>
        </a>
        @endforeach
    </div>
    @elseif($mpfmTestsForWell->count() > 0)
    <!-- Available MPFM Tests (Different Date) -->
    <div class="alert-modern alert-info-modern" style="background: linear-gradient(135deg, #bee3f8 0%, #90cdf4 100%); border-left-color: #2c5282; color: #2c5282;">
        <strong><i class="fe fe-shuffle"></i> MPFM Tests Available (Different Date Range)</strong>
        <p style="margin-bottom: 1rem;">
            Found <strong>{{ $mpfmTestsForWell->count() }}</strong> MPFM test(s) on the same oil string (outside ±7 day range)
        </p>
        <p style="font-size: 0.9rem; margin-bottom: 1rem;">
            You can still compare with these tests, but they are from a different time period.
        </p>
    </div>

    <div class="comparison-button-group">
        @foreach($mpfmTestsForWell->take(10) as $mpfmTest)
        <a href="{{ route('tv_tests.compare_result', ['tv_test_id' => $tvTest->id, 'mpfm_test_id' => $mpfmTest->id]) }}"
           class="btn btn-modern btn-comparison">
            <i class="fe fe-shuffle"></i> Compare with MPFM #{{ $mpfmTest->id }}
            <span style="font-size: 0.85rem; opacity: 0.9;">({{ $mpfmTest->test_date->format('Y-m-d') }} - {{ ucfirst($mpfmTest->status) }})</span>
        </a>
        @endforeach
        @if($mpfmTestsForWell->count() > 10)
        <p style="font-size: 0.85rem; color: #718096; margin-top: 1rem;">
            ... and {{ $mpfmTestsForWell->count() - 10 }} more tests available
        </p>
        @endif
    </div>
    @else
    <!-- No MPFM Tests -->
    <div class="no-mpfm-yet">
        <i class="fe fe-search"></i>
        <h5 style="color: #718096; margin: 0;">No MPFM Tests Available</h5>
        <p style="color: #a0aec0; margin-top: 0.5rem;">
            There are no MPFM tests recorded for this oil string yet.
        </p>
        <p style="color: #a0aec0; font-size: 0.9rem; margin-top: 1rem;">
            Once MPFM tests are created for this well, you'll be able to compare them with this Test Vessel measurement.
        </p>
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Production Chart
    const productionCtx = document.getElementById('productionChart').getContext('2d');
    new Chart(productionCtx, {
        type: 'bar',
        data: {
            labels: ['Oil Rate', 'Gas Rate', 'GOR'],
            datasets: [{
                label: 'Production Metrics',
                data: [
                    {{ $tvTest->oil_rate }},
                    {{ $tvTest->gas_rate }},
                    {{ $tvTest->gor ?? 0 }}
                ],
                backgroundColor: ['#667eea', '#764ba2', '#9b8bb4'],
                borderColor: ['#667eea', '#764ba2', '#9b8bb4'],
                borderWidth: 2,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Conditions Chart
    const conditionsCtx = document.getElementById('conditionsChart').getContext('2d');
    new Chart(conditionsCtx, {
        type: 'line',
        data: {
            labels: ['Pressure', 'Temperature', 'Water Cut'],
            datasets: [{
                label: 'Well Conditions',
                data: [
                    {{ $tvTest->pressure }},
                    {{ $tvTest->temperature }},
                    {{ $tvTest->water_cut }}
                ],
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#667eea',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: true }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Radar Chart
    const radarCtx = document.getElementById('radarChart').getContext('2d');
    const maxValues = {
        oilRate: 1000,
        gasRate: 100,
        waterCut: 100,
        pressure: 5000,
        temperature: 300
    };

    const oilRate = {{ $tvTest->oil_rate }};
    const gasRate = {{ $tvTest->gas_rate }};
    const waterCut = {{ $tvTest->water_cut }};
    const pressure = {{ $tvTest->pressure }};
    const temperature = {{ $tvTest->temperature }};

    new Chart(radarCtx, {
        type: 'radar',
        data: {
            labels: ['Oil Rate\n(B/D)', 'Gas Rate\n(MMscf/D)', 'Water Cut\n(%)', 'Pressure\n(psia)', 'Temp\n(°F)'],
            datasets: [{
                label: 'Current Test',
                data: [
                    (oilRate / maxValues.oilRate) * 100,
                    (gasRate / maxValues.gasRate) * 100,
                    waterCut,
                    (pressure / maxValues.pressure) * 100,
                    (temperature / maxValues.temperature) * 100
                ],
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.2)',
                borderWidth: 2,
                pointBackgroundColor: '#667eea',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: true }
            },
            scales: {
                r: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });
});
</script>
@endpush
