@extends('layouts.master')

@section('title', 'MPFM Test Details - ' . ($mpfmTest->oilString->name ?? 'N/A'))

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<style>
    .test-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 1rem;
        padding: 2.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
    }

    .test-header h1 {
        margin: 0;
        font-size: 2.5rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .test-header p {
        margin: 0.5rem 0 0 0;
        opacity: 0.95;
        font-size: 1rem;
    }

    .status-badge {
        display: inline-block;
        padding: 0.5rem 1.25rem;
        border-radius: 0.5rem;
        font-weight: 600;
        font-size: 0.9rem;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
    }

    .breadcrumb-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-top: 1rem;
        opacity: 0.9;
        font-size: 0.95rem;
    }

    .breadcrumb-header a {
        color: white;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .breadcrumb-header a:hover {
        text-decoration: underline;
    }

    .action-buttons {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        margin-top: 1.5rem;
    }

    .btn-header {
        padding: 0.6rem 1.25rem;
        border-radius: 0.5rem;
        border: none;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
    }

    .btn-header.primary {
        background: white;
        color: #667eea;
    }

    .btn-header.primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .btn-header.secondary {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .btn-header.secondary:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    .metric-card-modern {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        background: white;
        margin-bottom: 1.5rem;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .metric-card-modern:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    }

    .metric-card-header {
        background: linear-gradient(135deg, var(--header-color-1) 0%, var(--header-color-2) 100%);
        color: white;
        padding: 1.25rem 1.5rem;
        font-weight: 600;
        font-size: 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .metric-card-header i {
        font-size: 1.25rem;
    }

    .metric-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 1.5rem;
        padding: 1.5rem;
    }

    .metric-item {
        text-align: center;
    }

    .metric-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 0.25rem;
        word-break: break-word;
    }

    .metric-label {
        font-size: 0.75rem;
        color: #718096;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
    }

    .metric-unit {
        font-size: 0.8rem;
        color: #a0aec0;
        font-weight: 500;
        margin-top: 0.25rem;
    }

    .info-card {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        background: white;
        margin-bottom: 1.5rem;
    }

    .info-card-header {
        background: transparent;
        border-bottom: 2px solid #e2e8f0;
        padding: 1.5rem;
        font-size: 1.15rem;
        font-weight: 700;
        color: #2d3748;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .info-card-header i {
        color: #667eea;
        font-size: 1.25rem;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        padding: 1.5rem;
    }

    .info-item {
        display: flex;
        flex-direction: column;
    }

    .info-label {
        font-size: 0.75rem;
        color: #718096;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .info-value {
        font-size: 1rem;
        color: #2d3748;
        font-weight: 500;
    }

    .chart-card {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        background: white;
        margin-bottom: 1.5rem;
        padding: 1.5rem;
    }

    .chart-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .chart-title i {
        color: #667eea;
        font-size: 1.25rem;
    }

    .alert-custom {
        border-left: 4px solid;
        border-radius: 0.75rem;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
    }

    .alert-warning-custom {
        border-color: #f59e0b;
        background: #fffbeb;
        color: #d97706;
    }

    .alert-title {
        font-weight: 600;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .section-divider {
        margin: 2rem 0;
        border: none;
        border-top: 2px solid #e2e8f0;
    }

    .table-modern {
        width: 100%;
        border-collapse: collapse;
    }

    .table-modern thead {
        background: #f7fafc;
    }

    .table-modern th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #4a5568;
        border: none;
    }

    .table-modern tbody tr {
        border-bottom: 1px solid #e2e8f0;
        transition: all 0.2s ease;
    }

    .table-modern tbody tr:hover {
        background: #f7fafc;
    }

    .table-modern td {
        padding: 1rem;
        font-size: 0.95rem;
        color: #4a5568;
    }

    .badge-status {
        display: inline-block;
        padding: 0.4rem 0.8rem;
        border-radius: 0.35rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .badge-approved {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-submitted {
        background: #dbeafe;
        color: #1e40af;
    }

    .badge-draft {
        background: #e5e7eb;
        color: #374151;
    }

    .badge-rejected {
        background: #fee2e2;
        color: #991b1b;
    }

    .link-modern {
        color: #667eea;
        text-decoration: none;
        font-weight: 500;
    }

    .link-modern:hover {
        text-decoration: underline;
        color: #764ba2;
    }

    .container-modern {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem;
    }
</style>
@endpush

@section('content')
<div class="container-modern">
    <!-- Header Section -->
    <div class="test-header">
        <h1>
            <i class="fe fe-bar-chart-2"></i>
            MPFM Test #{{ $mpfmTest->id }}
        </h1>
        <p>
            {{ $mpfmTest->oilString->name ?? 'Unknown' }} •
            {{ $mpfmTest->test_date->format('Y-m-d') }} •
            {{ ucfirst($mpfmTest->shift) }} Shift
        </p>
        <div class="breadcrumb-header">
            <a href="{{ route('oil_fields.show', $mpfmTest->oilField->id) }}">
                <i class="fe fe-home"></i> {{ $mpfmTest->oilField->name }}
            </a>
            <span>/</span>
            <a href="{{ route('towers.show', $mpfmTest->tower->id) }}">
                <i class="fe fe-layers"></i> {{ $mpfmTest->tower->name }}
            </a>
            <span>/</span>
            <a href="{{ route('mpfm_tests.string_analysis', $mpfmTest->oil_string_id) }}">
                <i class="fe fe-anchor"></i> {{ $mpfmTest->oilString->name }}
            </a>
        </div>

        <div class="status-badge">
            @if($mpfmTest->status == 'approved')
                <i class="fe fe-check-circle"></i> Approved
            @elseif($mpfmTest->status == 'submitted')
                <i class="fe fe-clock"></i> Submitted
            @elseif($mpfmTest->status == 'draft')
                <i class="fe fe-edit"></i> Draft
            @else
                <i class="fe fe-alert-circle"></i> Rejected
            @endif
        </div>

        <div class="action-buttons">
            @if($mpfmTest && $mpfmTest->test_data)
            <a href="{{ route('mpfm-tests.view', $mpfmTest->id) }}" class="btn-header primary" target="_blank">
                <i class="fe fe-external-link"></i> Full Report
            </a>
            @endif
            <a href="{{ route('mpfm_tests.show_history', $mpfmTest->id) }}" class="btn-header secondary">
                <i class="fe fe-check-circle"></i> Validation
            </a>
            <a href="{{ route('mpfm_tests.string_analysis', $mpfmTest->oil_string_id) }}" class="btn-header secondary">
                <i class="fe fe-trending-up"></i> String Analysis
            </a>
            @if(auth()->check() && (auth()->user()->hasRole(['department_supervisor', 'surveillance_supervisor', 'hq_supervisor']) || auth()->user()->id === 1))
                @if(in_array($mpfmTest->status, ['submitted', 'approved_by_department', 'draft']))
                <button type="button" class="btn-header secondary" data-toggle="modal" data-target="#supervisorReviewModal">
                    <i class="fe fe-check-square"></i> Review
                </button>
                @endif
            @endif
            @can('update', $mpfmTest)
            <a href="{{ route('mpfm_tests.edit', $mpfmTest->id) }}" class="btn-header secondary">
                <i class="fe fe-edit"></i> Edit
            </a>
            @endcan
            <a href="{{ route('mpfm_tests.index') }}" class="btn-header secondary">
                <i class="fe fe-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <!-- Test Information Card -->
    <div class="info-card">
        <div class="info-card-header">
            <i class="fe fe-info"></i>
            Test Information
        </div>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Test Date</div>
                <div class="info-value">{{ $mpfmTest->test_date->format('Y-m-d H:i') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Shift</div>
                <div class="info-value">{{ ucfirst($mpfmTest->shift) }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Operator</div>
                <div class="info-value">{{ $mpfmTest->user->name ?? 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Oil Field</div>
                <div class="info-value">
                    <a href="{{ route('oil_fields.show', $mpfmTest->oilField->id) }}" class="link-modern">
                        {{ $mpfmTest->oilField->name }}
                    </a>
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Tower</div>
                <div class="info-value">
                    <a href="{{ route('towers.show', $mpfmTest->tower->id) }}" class="link-modern">
                        {{ $mpfmTest->tower->name }}
                    </a>
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Oil String</div>
                <div class="info-value">
                    <a href="{{ route('oil_strings.show', $mpfmTest->oilString->id) }}" class="link-modern">
                        {{ $mpfmTest->oilString->name }}
                    </a>
                </div>
            </div>
            @if($mpfmTest->mpfmOperationalPlan)
            <div class="info-item">
                <div class="info-label">Operational Plan</div>
                <div class="info-value">
                    <a href="{{ route('mpfm_operational_plans.show', $mpfmTest->mpfmOperationalPlan->id) }}" class="link-modern">
                        {{ $mpfmTest->mpfmOperationalPlan->name }}
                    </a>
                </div>
            </div>
            @endif
            <div class="info-item">
                <div class="info-label">Status</div>
                <div class="info-value">
                    @if($mpfmTest->status == 'approved')
                        <span class="badge-status badge-approved">Approved</span>
                    @elseif($mpfmTest->status == 'submitted')
                        <span class="badge-status badge-submitted">Submitted</span>
                    @elseif($mpfmTest->status == 'draft')
                        <span class="badge-status badge-draft">Draft</span>
                    @else
                        <span class="badge-status badge-rejected">Rejected</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Production Data Metrics -->
    <div class="metric-card-modern" style="--header-color-1: #667eea; --header-color-2: #764ba2;">
        <div class="metric-card-header">
            <i class="fe fe-activity"></i>
            Production Measurements
        </div>
        <div class="metric-grid">
            <div class="metric-item">
                <div class="metric-label">Oil Rate</div>
                <div class="metric-value">{{ number_format($kpis['oil_rate'] ?? 0, 1) }}</div>
                <div class="metric-unit">STB/d</div>
            </div>
            <div class="metric-item">
                <div class="metric-label">Gas Rate</div>
                <div class="metric-value">{{ number_format($kpis['gas_rate'] ?? 0, 1) }}</div>
                <div class="metric-unit">Mscf/d</div>
            </div>
            <div class="metric-item">
                <div class="metric-label">Water Cut</div>
                <div class="metric-value">{{ number_format($mpfmTest->water_cut ?? 0, 2) }}</div>
                <div class="metric-unit">%</div>
            </div>
            <div class="metric-item">
                <div class="metric-label">Pressure</div>
                <div class="metric-value">{{ number_format($kpis['avg_pressure'] ?? $kpis['pressure'] ?? 0, 0) }}</div>
                <div class="metric-unit">psi</div>
            </div>
            <div class="metric-item">
                <div class="metric-label">Temperature</div>
                <div class="metric-value">{{ number_format($kpis['avg_temperature'] ?? $kpis['temperature'] ?? 0, 1) }}</div>
                <div class="metric-unit">°F</div>
            </div>
        </div>
    </div>

    @if($mpfmTest->calculated_kpis)
    <!-- Calculated KPIs -->
    <div class="metric-card-modern" style="--header-color-1: #10b981; --header-color-2: #059669;">
        <div class="metric-card-header">
            <i class="fe fe-trending-up"></i>
            Calculated KPIs
        </div>
        <div class="metric-grid">
            <div class="metric-item">
                <div class="metric-label">GOR</div>
                <div class="metric-value">{{ number_format($mpfmTest->calculated_kpis['gor'] ?? 0, 1) }}</div>
                <div class="metric-unit">Scf/STB</div>
            </div>
            <div class="metric-item">
                <div class="metric-label">WLR</div>
                <div class="metric-value">{{ number_format($mpfmTest->calculated_kpis['wlr'] ?? 0, 3) }}</div>
                <div class="metric-unit">ratio</div>
            </div>
            <div class="metric-item">
                <div class="metric-label">GLR</div>
                <div class="metric-value">{{ number_format($mpfmTest->calculated_kpis['glr'] ?? 0, 1) }}</div>
                <div class="metric-unit">Scf/STB</div>
            </div>
            @if(isset($mpfmTest->calculated_kpis['gor_correlation']))
            <div class="metric-item">
                <div class="metric-label">GOR Correlation</div>
                <div class="metric-value">{{ number_format($mpfmTest->calculated_kpis['gor_correlation'] ?? 0, 1) }}</div>
                <div class="metric-unit">Scf/STB</div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <hr class="section-divider">

    <!-- Charts Section -->
    @if($mpfmTest->test_data && count($mpfmTest->test_data['rows'] ?? []) > 0)
    <div class="row">
        <div class="col-lg-6">
            <div class="chart-card">
                <div class="chart-title">
                    <i class="fe fe-bar-chart-2"></i>
                    Production Rates
                </div>
                <canvas id="ratesChart" height="80"></canvas>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="chart-card">
                <div class="chart-title">
                    <i class="fe fe-trending-up"></i>
                    Well Conditions
                </div>
                <canvas id="conditionsChart" height="80"></canvas>
            </div>
        </div>
    </div>
    @endif

    <!-- Detailed Data Table -->
    @if($mpfmTest->test_data && count($mpfmTest->test_data['rows'] ?? []) > 0)
    <div class="info-card">
        <div class="info-card-header">
            <i class="fe fe-list"></i>
            Detailed Test Data
        </div>
        <div style="overflow-x: auto;">
            <table class="table-modern">
                <thead>
                    <tr>
                        @foreach($mpfmTest->test_data['columns'] ?? [] as $column)
                        <th>{{ $column }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach(array_slice($mpfmTest->test_data['rows'] ?? [], 0, 50) as $row)
                    <tr>
                        @foreach($row as $value)
                        <td>{{ is_numeric($value) ? number_format($value, 2) : $value }}</td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if(count($mpfmTest->test_data['rows'] ?? []) > 50)
        <div style="padding: 1rem; text-align: center; color: #718096; background: #f7fafc; border-top: 1px solid #e2e8f0;">
            Showing 50 of {{ count($mpfmTest->test_data['rows']) }} rows. <a href="{{ route('mpfm-tests.view', $mpfmTest->id) }}" class="link-modern">View all</a>
        </div>
        @endif
    </div>
    @endif
</div>

<!-- Supervisor Review Modal (from original) -->
@if(auth()->check() && (auth()->user()->hasRole(['department_supervisor', 'surveillance_supervisor', 'hq_supervisor']) || auth()->user()->id === 1))
    @include('mpfm_tests.supervisor-review-modal')
@endif

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if($mpfmTest->test_data && isset($mpfmTest->test_data['rows']))
    // Prepare chart data
    const testData = @json($mpfmTest->test_data);
    const columns = testData.columns || [];
    const rows = testData.rows || [];

    // Find column indices
    const timeCol = columns.findIndex(c => c.toLowerCase().includes('time'));
    const oilCol = columns.findIndex(c => c.toLowerCase().includes('oil rate') || c.toLowerCase().includes('oil_rate'));
    const gasCol = columns.findIndex(c => c.toLowerCase().includes('gas rate') || c.toLowerCase().includes('gas_rate'));
    const presCol = columns.findIndex(c => c.toLowerCase().includes('pressure'));
    const tempCol = columns.findIndex(c => c.toLowerCase().includes('temperature'));

    // Extract data for charts
    const chartLabels = rows.slice(0, 20).map((row, idx) => `${idx + 1}`);
    const oilData = rows.slice(0, 20).map(row => parseFloat(row[oilCol]) || 0);
    const gasData = rows.slice(0, 20).map(row => parseFloat(row[gasCol]) || 0);
    const presData = rows.slice(0, 20).map(row => parseFloat(row[presCol]) || 0);
    const tempData = rows.slice(0, 20).map(row => parseFloat(row[tempCol]) || 0);

    // Rates Chart
    if (document.getElementById('ratesChart')) {
        const ratesCtx = document.getElementById('ratesChart').getContext('2d');
        new Chart(ratesCtx, {
            type: 'bar',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Oil Rate (STB/d)',
                    data: oilData,
                    backgroundColor: '#667eea',
                    borderRadius: 6,
                    borderSkipped: false
                }, {
                    label: 'Gas Rate (Mscf/d)',
                    data: gasData,
                    backgroundColor: '#764ba2',
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    // Conditions Chart
    if (document.getElementById('conditionsChart')) {
        const condCtx = document.getElementById('conditionsChart').getContext('2d');
        new Chart(condCtx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Pressure (psi)',
                    data: presData,
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Temperature (°F)',
                    data: tempData,
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: { beginAtZero: false }
                }
            }
        });
    }
    @endif
});
</script>
@endpush
