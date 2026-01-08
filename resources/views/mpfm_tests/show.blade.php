@extends('layouts.master')

@section('title', 'MPFM Test Details')

@section('content')
<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">MPFM Test #{{ $mpfmTest->id }}</h3>
                <div class="card-options">
                    @can('update', $mpfmTest)
                    <a href="{{ route('mpfm_tests.edit', $mpfmTest->id) }}" class="btn btn-primary btn-sm">
                        <i class="fe fe-edit"></i> Edit
                    </a>
                    @endcan
                    <a href="{{ route('mpfm_tests.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fe fe-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Test Information</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th width="40%">Test Date:</th>
                                <td>{{ $mpfmTest->test_date->format('Y-m-d') }}</td>
                            </tr>
                            <tr>
                                <th>Shift:</th>
                                <td>{{ ucfirst($mpfmTest->shift) }}</td>
                            </tr>
                            <tr>
                                <th>Operator:</th>
                                <td>{{ $mpfmTest->user->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Oil Field:</th>
                                <td>
                                    <a href="{{ route('oil_fields.show', $mpfmTest->oilField->id) }}">
                                        {{ $mpfmTest->oilField->name }} ({{ $mpfmTest->oilField->code }})
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <th>Tower:</th>
                                <td>
                                    <a href="{{ route('towers.show', $mpfmTest->tower->id) }}">
                                        {{ $mpfmTest->tower->name }} ({{ $mpfmTest->tower->code }})
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <th>Oil String:</th>
                                <td>
                                    <a href="{{ route('oil_strings.show', $mpfmTest->oilString->id) }}">
                                        {{ $mpfmTest->oilString->name }} ({{ $mpfmTest->oilString->code }})
                                    </a>
                                </td>
                            </tr>
                            @if($mpfmTest->mpfmOperationalPlan)
                            <tr>
                                <th>Operational Plan:</th>
                                <td>
                                    <a href="{{ route('mpfm_operational_plans.show', $mpfmTest->mpfmOperationalPlan->id) }}">
                                        {{ $mpfmTest->mpfmOperationalPlan->name }}
                                    </a>
                                </td>
                            </tr>
                            @endif
                            <tr>
                                <th>Status:</th>
                                <td>
                                    @if($mpfmTest->status == 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @elseif($mpfmTest->status == 'submitted')
                                        <span class="badge bg-info">Submitted</span>
                                    @elseif($mpfmTest->status == 'draft')
                                        <span class="badge bg-secondary">Draft</span>
                                    @else
                                        <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="col-md-6">
                        <h5>Production Data</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th width="40%">Oil Rate:</th>
                                <td>{{ number_format($mpfmTest->oil_rate ?? 0, 2) }} STB/d</td>
                            </tr>
                            <tr>
                                <th>Gas Rate:</th>
                                <td>{{ number_format($mpfmTest->gas_rate ?? 0, 2) }} Mscf/d</td>
                            </tr>
                            <tr>
                                <th>Water Cut:</th>
                                <td>{{ number_format($mpfmTest->water_cut ?? 0, 2) }}%</td>
                            </tr>
                            <tr>
                                <th>Pressure:</th>
                                <td>{{ number_format($mpfmTest->pressure ?? 0, 2) }} psi</td>
                            </tr>
                            <tr>
                                <th>Temperature:</th>
                                <td>{{ number_format($mpfmTest->temperature ?? 0, 2) }} °F</td>
                            </tr>
                        </table>

                        @if($mpfmTest->calculated_kpis)
                        <h5 class="mt-4">Calculated KPIs</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th width="40%">GOR (Gas-Oil Ratio):</th>
                                <td>{{ number_format($mpfmTest->calculated_kpis['gor'] ?? 0, 2) }} Scf/STB</td>
                            </tr>
                            <tr>
                                <th>WLR (Water-Liquid Ratio):</th>
                                <td>{{ number_format($mpfmTest->calculated_kpis['wlr'] ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <th>BSW (Basic Sediment & Water):</th>
                                <td>{{ number_format($mpfmTest->calculated_kpis['bsw'] ?? 0, 2) }}%</td>
                            </tr>
                            <tr>
                                <th>Total Production Rate:</th>
                                <td>{{ number_format($mpfmTest->calculated_kpis['total_production_rate'] ?? 0, 2) }}</td>
                            </tr>
                            @if(isset($mpfmTest->calculated_kpis['productivity_index']))
                            <tr>
                                <th>Productivity Index:</th>
                                <td>{{ number_format($mpfmTest->calculated_kpis['productivity_index'], 2) }}</td>
                            </tr>
                            @endif
                        </table>
                        @endif
                    </div>
                </div>

                @if($mpfmTest->notes)
                <div class="row mt-4">
                    <div class="col-md-12">
                        <h5>Notes</h5>
                        <div class="card">
                            <div class="card-body">
                                <p>{{ $mpfmTest->notes }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

