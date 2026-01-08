<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Well Test Analysis Report - {{ $wellTest->well ?? 'Test' }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #4e73df;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #4e73df;
            margin: 0 0 10px 0;
            font-size: 24px;
        }

        .header .subtitle {
            color: #6c757d;
            font-size: 14px;
            margin: 0;
        }

        .section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }

        .section h2 {
            color: #495057;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 5px;
            margin-bottom: 15px;
            font-size: 18px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .info-table th {
            background: #f8f9fa;
            text-align: left;
            padding: 8px 12px;
            border: 1px solid #dee2e6;
            font-weight: 600;
            width: 30%;
        }

        .info-table td {
            padding: 8px 12px;
            border: 1px solid #dee2e6;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10px;
        }

        .data-table th {
            background: #4e73df;
            color: white;
            padding: 6px 8px;
            border: 1px solid #dee2e6;
            text-align: center;
            font-weight: 600;
        }

        .data-table td {
            padding: 4px 6px;
            border: 1px solid #dee2e6;
            text-align: center;
        }

        .data-table tr:nth-child(even) {
            background: #f8f9fa;
        }

        .metrics-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .metrics-row {
            display: table-row;
        }

        .metrics-cell {
            display: table-cell;
            width: 25%;
            padding: 10px;
            vertical-align: top;
        }

        .metric-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            background: #f8f9fa;
        }

        .metric-card h3 {
            margin: 0 0 10px 0;
            color: #4e73df;
            font-size: 16px;
        }

        .metric-card .value {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
            margin-bottom: 5px;
        }

        .metric-card .unit {
            font-size: 12px;
            color: #6c757d;
        }

        .chart-placeholder {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 40px;
            text-align: center;
            background: #f8f9fa;
            margin-bottom: 20px;
        }

        .chart-placeholder p {
            margin: 0;
            color: #6c757d;
            font-style: italic;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            font-size: 10px;
            color: #6c757d;
        }

        .anomaly-alert {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .anomaly-alert .alert-title {
            color: #721c24;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .anomaly-list {
            margin: 0;
            padding-left: 20px;
        }

        .anomaly-list li {
            color: #721c24;
            margin-bottom: 5px;
        }

        @media print {
            body {
                padding: 15px;
            }

            .section {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Well Test Analysis Report</h1>
        <p class="subtitle">
            Well: {{ $wellTest->well ?? 'N/A' }} |
            Field: {{ $wellTest->field ?? 'N/A' }} |
            Test Date: {{ $wellTest->test_date?->format('M d, Y') ?? 'N/A' }}
        </p>
        <p class="subtitle">
            Generated on {{ now()->format('M d, Y H:i') }} |
            Report ID: {{ $wellTest->id }}
        </p>
    </div>

    <!-- Test Information -->
    <div class="section">
        <h2>Test Information</h2>
        <table class="info-table">
            <tr>
                <th>Test ID</th>
                <td>{{ $wellTest->id }}</td>
            </tr>
            <tr>
                <th>Client</th>
                <td>{{ $wellTest->client ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Field</th>
                <td>{{ $wellTest->field ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Well</th>
                <td>{{ $wellTest->well ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Operator</th>
                <td>{{ $wellTest->operator ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Test Date</th>
                <td>{{ $wellTest->test_date?->format('Y-m-d') ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Test Type</th>
                <td>{{ $wellTest->test_type ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Data Points</th>
                <td>{{ count($wellTest->test_data ?? []) }}</td>
            </tr>
            <tr>
                <th>Processed Date</th>
                <td>{{ $wellTest->created_at?->format('Y-m-d H:i') ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <!-- Anomalies Alert -->
    @if(!empty($wellTest->metadata['anomalies']))
    <div class="section">
        <div class="anomaly-alert">
            <div class="alert-title">⚠️ Data Anomalies Detected</div>
            <ul class="anomaly-list">
                @foreach($wellTest->metadata['anomalies'] as $anomaly)
                <li>
                    Row {{ $anomaly['row'] }}, Column "{{ $anomaly['column'] }}":
                    Value {{ number_format($anomaly['value'], 2) }} is {{ $anomaly['type'] }}
                    (threshold: {{ number_format($anomaly['threshold']['min'], 2) }} - {{ number_format($anomaly['threshold']['max'], 2) }} {{ $anomaly['threshold']['unit'] }})
                </li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <!-- Key Metrics -->
    @if(!empty($wellTest->calculated_metrics))
    <div class="section">
        <h2>Key Production Metrics</h2>

        <!-- Petroleum Metrics -->
        @if(isset($wellTest->calculated_metrics['petroleum_metrics']))
        <div class="metrics-grid">
            <div class="metrics-row">
                @if(isset($wellTest->calculated_metrics['petroleum_metrics']['gor']))
                <div class="metrics-cell">
                    <div class="metric-card">
                        <h3>Gas-Oil Ratio</h3>
                        <div class="value">{{ number_format($wellTest->calculated_metrics['petroleum_metrics']['gor']['average'] ?? 0, 2) }}</div>
                        <div class="unit">scf/STB</div>
                    </div>
                </div>
                @endif

                @if(isset($wellTest->calculated_metrics['cumulative_totals']))
                <div class="metrics-cell">
                    <div class="metric-card">
                        <h3>Total Oil</h3>
                        <div class="value">{{ number_format($wellTest->calculated_metrics['cumulative_totals']['oil'] ?? 0, 2) }}</div>
                        <div class="unit">STB</div>
                    </div>
                </div>
                <div class="metrics-cell">
                    <div class="metric-card">
                        <h3>Total Gas</h3>
                        <div class="value">{{ number_format($wellTest->calculated_metrics['cumulative_totals']['gas'] ?? 0, 2) }}</div>
                        <div class="unit">Mscf</div>
                    </div>
                </div>
                <div class="metrics-cell">
                    <div class="metric-card">
                        <h3>Total Water</h3>
                        <div class="value">{{ number_format($wellTest->calculated_metrics['cumulative_totals']['water'] ?? 0, 2) }}</div>
                        <div class="unit">STB</div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Column Statistics -->
        @if(isset($wellTest->calculated_metrics['column_stats']))
        <h3>Column Statistics Summary</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Parameter</th>
                    <th>Average</th>
                    <th>Minimum</th>
                    <th>Maximum</th>
                    <th>Count</th>
                </tr>
            </thead>
            <tbody>
                @foreach($wellTest->calculated_metrics['column_stats'] as $column => $stats)
                    @if(isset($stats['average']))
                    <tr>
                        <td>{{ $column }}</td>
                        <td>{{ number_format($stats['average'], 4) }}</td>
                        <td>{{ number_format($stats['min'] ?? 0, 4) }}</td>
                        <td>{{ number_format($stats['max'] ?? 0, 4) }}</td>
                        <td>{{ $stats['count'] ?? 0 }}</td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
    @endif

    <!-- Raw Data Table -->
    @if(!empty($wellTest->test_data))
    <div class="section">
        <h2>Raw Test Data</h2>
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        @foreach(array_keys($wellTest->test_data[0] ?? []) as $column)
                        <th>{{ $column }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($wellTest->test_data as $index => $row)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        @foreach($row as $value)
                        <td>{{ is_numeric($value) ? number_format($value, 4) : $value }}</td>
                        @endforeach
                    </tr>
                    @if($index >= 49) @break @endif {{-- Limit to first 50 rows for PDF --}}
                    @endforeach
                </tbody>
            </table>
        </div>
        @if(count($wellTest->test_data) > 50)
        <p style="font-style: italic; color: #6c757d; margin-top: 10px;">
            Showing first 50 rows of {{ count($wellTest->test_data) }} total data points.
        </p>
        @endif
    </div>
    @endif

    <!-- Charts Placeholder -->
    <div class="section">
        <h2>Production Charts</h2>
        <div class="chart-placeholder">
            <p>Interactive charts are available in the web interface:</p>
            <ul style="text-align: left; display: inline-block;">
                <li>Production Rates Over Time</li>
                <li>Gas-Oil Ratio Trends</li>
                <li>Water Cut Analysis</li>
                <li>Temperature and Pressure Profiles</li>
                <li>Parameter Distribution Charts</li>
            </ul>
        </div>
    </div>

    <!-- Notes -->
    @if($wellTest->notes)
    <div class="section">
        <h2>Notes</h2>
        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; border-left: 4px solid #4e73df;">
            {{ $wellTest->notes }}
        </div>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>
            MPFM Production Management System |
            Report generated on {{ now()->format('M d, Y H:i:s') }} |
            Confidential Document
        </p>
    </div>
</body>
</html>

