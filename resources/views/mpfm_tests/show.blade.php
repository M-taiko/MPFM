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
                                <td>{{ number_format($kpis['oil_rate'] ?? 0, 2) }} STB/d</td>
                            </tr>
                            <tr>
                                <th>Gas Rate:</th>
                                <td>{{ number_format($kpis['gas_rate'] ?? 0, 2) }} Mscf/d</td>
                            </tr>
                            <tr>
                                <th>Water Cut:</th>
                                <td>{{ number_format($mpfmTest->water_cut ?? 0, 2) }}%</td>
                            </tr>
                            <tr>
                                <th>Pressure:</th>
                                <td>{{ number_format($kpis['avg_pressure'] ?? $kpis['pressure'] ?? 0, 2) }} psi</td>
                            </tr>
                            <tr>
                                <th>Temperature:</th>
                                <td>{{ number_format($kpis['avg_temperature'] ?? $kpis['temperature'] ?? 0, 2) }} °F</td>
                            </tr>
                        </table>

                        @if($mpfmTest->calculated_kpis)
                        {{-- @DD($mpfmTest->calculated_kpis) --}}
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

                <!-- Modern Summary Section -->
                <div class="row mt-5">
                    <div class="col-md-12">
                        <!-- Summary Header -->
                        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 12px; margin-bottom: 30px;">
                            <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                                <i class="fe fe-activity" style="font-size: 2rem;"></i>
                                <h3 style="margin: 0; font-size: 1.8rem; font-weight: 600;">Test Summary & Fluid Properties</h3>
                            </div>
                            <p style="margin: 0; opacity: 0.95; font-size: 0.95rem;">Detailed analysis of fluid properties and production rates from the MPFM test</p>
                        </div>

                        <!-- Fluid Properties Section -->
                        @if($mpfmTest->calculated_kpis)
                        <div class="row" style="margin-bottom: 30px;">
                            <div class="col-md-12">
                                <h5 style="margin-bottom: 20px; font-weight: 600; color: #333; border-bottom: 3px solid #667eea; padding-bottom: 10px; display: inline-block;">
                                    <i class="fe fe-droplet" style="margin-right: 8px; color: #667eea;"></i>
                                    Fluid Properties & PVT Data
                                </h5>
                            </div>

                            <!-- Fluid Properties Cards Grid -->
                            <div class="col-md-12">
                                @php
                                    $kpis = $mpfmTest->calculated_kpis;
                                @endphp
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 30px;">

                                    <!-- Temperature Card -->
                                    <div style="background: white; border: 1px solid #e8ecf1; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); transition: all 0.3s ease;">
                                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                                            <div>
                                                <p style="margin: 0; font-size: 0.9rem; color: #667eea; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Temperature (T)</p>
                                                <p style="margin: 0; color: #999; font-size: 0.85rem;">Reservoir Fluid Temp</p>
                                            </div>
                                            <div style="background: #f0f4ff; width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #667eea; font-size: 1.4rem;">
                                                <i class="fe fe-thermometer"></i>
                                            </div>
                                        </div>
                                        <div style="font-size: 1.8rem; font-weight: 700; color: #333; margin-bottom: 8px;">
                                            {{ number_format($kpis['avg_temperature'] ?? $kpis['temperature'] ?? 0, 2) }}
                                        </div>
                                        <p style="margin: 0; color: #999; font-size: 0.9rem;">°F</p>
                                    </div>

                                    <!-- Pressure Card -->
                                    <div style="background: white; border: 1px solid #e8ecf1; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); transition: all 0.3s ease;">
                                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                                            <div>
                                                <p style="margin: 0; font-size: 0.9rem; color: #667eea; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Pressure (P)</p>
                                                <p style="margin: 0; color: #999; font-size: 0.85rem;">Reservoir Pressure</p>
                                            </div>
                                            <div style="background: #f0f4ff; width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #667eea; font-size: 1.4rem;">
                                                <i class="fe fe-gauge"></i>
                                            </div>
                                        </div>
                                        <div style="font-size: 1.8rem; font-weight: 700; color: #333; margin-bottom: 8px;">
                                            {{ number_format($kpis['avg_pressure'] ?? $kpis['pressure'] ?? 0, 2) }}
                                        </div>
                                        <p style="margin: 0; color: #999; font-size: 0.9rem;">psia</p>
                                    </div>

                                    <!-- Oil Volume Factor Card -->
                                    <div style="background: white; border: 1px solid #e8ecf1; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); transition: all 0.3s ease;">
                                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                                            <div>
                                                <p style="margin: 0; font-size: 0.9rem; color: #667eea; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Oil Vol. Factor (bo)</p>
                                                <p style="margin: 0; color: #999; font-size: 0.85rem;">Oil Formation Volume</p>
                                            </div>
                                            <div style="background: #f0f4ff; width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #667eea; font-size: 1.4rem;">
                                                <i class="fe fe-layers"></i>
                                            </div>
                                        </div>
                                        <div style="font-size: 1.8rem; font-weight: 700; color: #333; margin-bottom: 8px;">
                                            {{ number_format($mpfmTest->calculated_kpis['bo'] ?? 0, 4) }}
                                        </div>
                                        <p style="margin: 0; color: #999; font-size: 0.9rem;">STB/STB</p>
                                    </div>

                                    <!-- Water Volume Factor Card -->
                                    <div style="background: white; border: 1px solid #e8ecf1; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); transition: all 0.3s ease;">
                                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                                            <div>
                                                <p style="margin: 0; font-size: 0.9rem; color: #667eea; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Water Vol. Factor (bw)</p>
                                                <p style="margin: 0; color: #999; font-size: 0.85rem;">Water Formation Volume</p>
                                            </div>
                                            <div style="background: #f0f4ff; width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #667eea; font-size: 1.4rem;">
                                                <i class="fe fe-wave"></i>
                                            </div>
                                        </div>
                                        <div style="font-size: 1.8rem; font-weight: 700; color: #333; margin-bottom: 8px;">
                                            {{ number_format($mpfmTest->calculated_kpis['bw'] ?? 0, 4) }}
                                        </div>
                                        <p style="margin: 0; color: #999; font-size: 0.9rem;">STB/STB</p>
                                    </div>

                                    <!-- Gas Volume Factor Card -->
                                    <div style="background: white; border: 1px solid #e8ecf1; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); transition: all 0.3s ease;">
                                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                                            <div>
                                                <p style="margin: 0; font-size: 0.9rem; color: #667eea; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Gas Vol. Factor (bg)</p>
                                                <p style="margin: 0; color: #999; font-size: 0.85rem;">Gas Formation Volume</p>
                                            </div>
                                            <div style="background: #f0f4ff; width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #667eea; font-size: 1.4rem;">
                                                <i class="fe fe-wind"></i>
                                            </div>
                                        </div>
                                        <div style="font-size: 1.8rem; font-weight: 700; color: #333; margin-bottom: 8px;">
                                            {{ number_format($mpfmTest->calculated_kpis['bg'] ?? 0, 6) }}
                                        </div>
                                        <p style="margin: 0; color: #999; font-size: 0.9rem;">Scf/STB</p>
                                    </div>

                                    <!-- Oil Density Card -->
                                    <div style="background: white; border: 1px solid #e8ecf1; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); transition: all 0.3s ease;">
                                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                                            <div>
                                                <p style="margin: 0; font-size: 0.9rem; color: #764ba2; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Oil Density (ρo)</p>
                                                <p style="margin: 0; color: #999; font-size: 0.85rem;">Oil Phase Density</p>
                                            </div>
                                            <div style="background: #f3e5f5; width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #764ba2; font-size: 1.4rem;">
                                                <i class="fe fe-hexagon"></i>
                                            </div>
                                        </div>
                                        <div style="font-size: 1.8rem; font-weight: 700; color: #333; margin-bottom: 8px;">
                                            {{ number_format($mpfmTest->calculated_kpis['po'] ?? 0, 2) }}
                                        </div>
                                        <p style="margin: 0; color: #999; font-size: 0.9rem;">kg/m³</p>
                                    </div>

                                    <!-- Water Density Card -->
                                    <div style="background: white; border: 1px solid #e8ecf1; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); transition: all 0.3s ease;">
                                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                                            <div>
                                                <p style="margin: 0; font-size: 0.9rem; color: #764ba2; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Water Density (ρw)</p>
                                                <p style="margin: 0; color: #999; font-size: 0.85rem;">Water Phase Density</p>
                                            </div>
                                            <div style="background: #f3e5f5; width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #764ba2; font-size: 1.4rem;">
                                                <i class="fe fe-droplet"></i>
                                            </div>
                                        </div>
                                        <div style="font-size: 1.8rem; font-weight: 700; color: #333; margin-bottom: 8px;">
                                            {{ number_format($mpfmTest->calculated_kpis['pw'] ?? 0, 2) }}
                                        </div>
                                        <p style="margin: 0; color: #999; font-size: 0.9rem;">kg/m³</p>
                                    </div>

                                    <!-- Gas Density Card -->
                                    <div style="background: white; border: 1px solid #e8ecf1; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); transition: all 0.3s ease;">
                                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                                            <div>
                                                <p style="margin: 0; font-size: 0.9rem; color: #764ba2; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Gas Density (ρg)</p>
                                                <p style="margin: 0; color: #999; font-size: 0.85rem;">Gas Phase Density</p>
                                            </div>
                                            <div style="background: #f3e5f5; width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #764ba2; font-size: 1.4rem;">
                                                <i class="fe fe-cloud"></i>
                                            </div>
                                        </div>
                                        <div style="font-size: 1.8rem; font-weight: 700; color: #333; margin-bottom: 8px;">
                                            {{ number_format($mpfmTest->calculated_kpis['pg'] ?? 0, 4) }}
                                        </div>
                                        <p style="margin: 0; color: #999; font-size: 0.9rem;">kg/m³</p>
                                    </div>

                                    <!-- Solution Gas-Oil Ratio Card -->
                                    <div style="background: white; border: 1px solid #e8ecf1; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); transition: all 0.3s ease;">
                                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                                            <div>
                                                <p style="margin: 0; font-size: 0.9rem; color: #764ba2; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Solution GOR (Rst)</p>
                                                <p style="margin: 0; color: #999; font-size: 0.85rem;">Solution Gas-Oil Ratio</p>
                                            </div>
                                            <div style="background: #f3e5f5; width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #764ba2; font-size: 1.4rem;">
                                                <i class="fe fe-share-2"></i>
                                            </div>
                                        </div>
                                        <div style="font-size: 1.8rem; font-weight: 700; color: #333; margin-bottom: 8px;">
                                            {{ number_format($mpfmTest->calculated_kpis['rst'] ?? 0, 2) }}
                                        </div>
                                        <p style="margin: 0; color: #999; font-size: 0.9rem;">Scf/STB</p>
                                    </div>

                                    <!-- Compressibility Factor Card -->
                                    <div style="background: white; border: 1px solid #e8ecf1; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); transition: all 0.3s ease;">
                                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                                            <div>
                                                <p style="margin: 0; font-size: 0.9rem; color: #764ba2; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Compressibility (Z)</p>
                                                <p style="margin: 0; color: #999; font-size: 0.85rem;">Gas Compressibility Factor</p>
                                            </div>
                                            <div style="background: #f3e5f5; width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #764ba2; font-size: 1.4rem;">
                                                <i class="fe fe-minimize"></i>
                                            </div>
                                        </div>
                                        <div style="font-size: 1.8rem; font-weight: 700; color: #333; margin-bottom: 8px;">
                                            {{ number_format($mpfmTest->calculated_kpis['z_factor'] ?? 0, 4) }}
                                        </div>
                                        <p style="margin: 0; color: #999; font-size: 0.9rem;">dimensionless</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Production Rates Section -->
                        <div class="row" style="margin-bottom: 30px;">
                            <div class="col-md-12">
                                <h5 style="margin-bottom: 20px; font-weight: 600; color: #333; border-bottom: 3px solid #667eea; padding-bottom: 10px; display: inline-block;">
                                    <i class="fe fe-trending-up" style="margin-right: 8px; color: #667eea;"></i>
                                    Production Rates & Ratios
                                </h5>
                            </div>

                            <!-- Production Rates Grid -->
                            <div class="col-md-12">
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 15px;">

                                    <!-- Oil Rate -->
                                    <div style="background: #f8fff8; border-left: 4px solid #28a745; border-radius: 8px; padding: 16px;">
                                        <p style="margin: 0 0 8px 0; font-size: 0.85rem; color: #28a745; font-weight: 600; text-transform: uppercase;">Oil Rate (qo)</p>
                                        <div style="font-size: 1.6rem; font-weight: 700; color: #28a745;">{{ number_format($kpis['oil_rate'] ?? 0, 2) }}</div>
                                        <p style="margin: 0; color: #666; font-size: 0.85rem;">STB/d</p>
                                    </div>

                                    <!-- Water Rate -->
                                    <div style="background: #f0f8ff; border-left: 4px solid #0066cc; border-radius: 8px; padding: 16px;">
                                        <p style="margin: 0 0 8px 0; font-size: 0.85rem; color: #0066cc; font-weight: 600; text-transform: uppercase;">Water Rate (qw)</p>
                                        <div style="font-size: 1.6rem; font-weight: 700; color: #0066cc;">{{ number_format($mpfmTest->calculated_kpis['water_rate'] ?? 0, 2) }}</div>
                                        <p style="margin: 0; color: #666; font-size: 0.85rem;">STB/d</p>
                                    </div>

                                    <!-- Liquid Rate -->
                                    <div style="background: #fff8f0; border-left: 4px solid #ff9800; border-radius: 8px; padding: 16px;">
                                        <p style="margin: 0 0 8px 0; font-size: 0.85rem; color: #ff9800; font-weight: 600; text-transform: uppercase;">Liquid Rate (ql)</p>
                                        <div style="font-size: 1.6rem; font-weight: 700; color: #ff9800;">{{ number_format(($kpis['oil_rate'] ?? 0) + ($kpis['water_rate'] ?? 0), 2) }}</div>
                                        <p style="margin: 0; color: #666; font-size: 0.85rem;">bbl/d</p>
                                    </div>

                                    <!-- Gas Rate -->
                                    <div style="background: #f5f5f5; border-left: 4px solid #666; border-radius: 8px; padding: 16px;">
                                        <p style="margin: 0 0 8px 0; font-size: 0.85rem; color: #666; font-weight: 600; text-transform: uppercase;">Gas Rate (qg)</p>
                                        <div style="font-size: 1.6rem; font-weight: 700; color: #666;">{{ number_format($kpis['gas_rate'] ?? 0, 2) }}</div>
                                        <p style="margin: 0; color: #666; font-size: 0.85rem;">Mscf/d</p>
                                    </div>

                                    <!-- WLR -->
                                    <div style="background: #fff5f5; border-left: 4px solid #dc3545; border-radius: 8px; padding: 16px;">
                                        <p style="margin: 0 0 8px 0; font-size: 0.85rem; color: #dc3545; font-weight: 600; text-transform: uppercase;">WLR (Water-Liquid)</p>
                                        <div style="font-size: 1.6rem; font-weight: 700; color: #dc3545;">{{ number_format($mpfmTest->calculated_kpis['wlr'] ?? 0, 2) }}</div>
                                        <p style="margin: 0; color: #666; font-size: 0.85rem;">%</p>
                                    </div>

                                    <!-- GVF -->
                                    <div style="background: #f0f4ff; border-left: 4px solid #667eea; border-radius: 8px; padding: 16px;">
                                        <p style="margin: 0 0 8px 0; font-size: 0.85rem; color: #667eea; font-weight: 600; text-transform: uppercase;">GVF (Gas Volume)</p>
                                        <div style="font-size: 1.6rem; font-weight: 700; color: #667eea;">{{ number_format($mpfmTest->calculated_kpis['gvf'] ?? 0, 2) }}</div>
                                        <p style="margin: 0; color: #666; font-size: 0.85rem;">%</p>
                                    </div>

                                    <!-- GOR -->
                                    <div style="background: #fffbf0; border-left: 4px solid #ffc107; border-radius: 8px; padding: 16px;">
                                        <p style="margin: 0 0 8px 0; font-size: 0.85rem; color: #ffc107; font-weight: 600; text-transform: uppercase;">GOR (Gas-Oil)</p>
                                        <div style="font-size: 1.6rem; font-weight: 700; color: #ff9800;">{{ number_format($mpfmTest->calculated_kpis['gor'] ?? 0, 2) }}</div>
                                        <p style="margin: 0; color: #666; font-size: 0.85rem;">Scf/STB</p>
                                    </div>

                                    <!-- BSW -->
                                    <div style="background: #f5f0ff; border-left: 4px solid #9c27b0; border-radius: 8px; padding: 16px;">
                                        <p style="margin: 0 0 8px 0; font-size: 0.85rem; color: #9c27b0; font-weight: 600; text-transform: uppercase;">BSW (Sediment & Water)</p>
                                        <div style="font-size: 1.6rem; font-weight: 700; color: #9c27b0;">{{ number_format($mpfmTest->calculated_kpis['bsw'] ?? 0, 2) }}</div>
                                        <p style="margin: 0; color: #666; font-size: 0.85rem;">%</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Charts Section -->
                @if($mpfmTest && $mpfmTest->test_data)
                <div class="row mt-5">
                    <div class="col-md-12">
                        <h4 style="margin-bottom: 20px; font-weight: 600; color: #333; border-bottom: 3px solid #667eea; padding-bottom: 10px;">
                            <i class="fe fe-bar-chart mr-2" style="color: #667eea;"></i>
                            Production Analysis Charts
                        </h4>
                    </div>

                    <!-- Production Rates Chart -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title" style="margin: 0;">
                                    <i class="fe fe-trending-up mr-2"></i>Production Rates Over Time
                                </h5>
                            </div>
                            <div class="card-body" style="height: 350px; position: relative;">
                                <canvas id="ratesChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- GOR Chart -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title" style="margin: 0;">
                                    <i class="fe fe-bar-chart-2 mr-2"></i>Gas-Oil Ratio (GOR) Trend
                                </h5>
                            </div>
                            <div class="card-body" style="height: 350px; position: relative;">
                                <canvas id="gorChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Pressure Chart -->
                    <div class="col-md-6 mt-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title" style="margin: 0;">
                                    <i class="fe fe-gauge mr-2"></i>Pressure Trend Over Time
                                </h5>
                            </div>
                            <div class="card-body" style="height: 350px; position: relative;">
                                <canvas id="pressureChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Temperature Chart -->
                    <div class="col-md-6 mt-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title" style="margin: 0;">
                                    <i class="fe fe-thermometer mr-2"></i>Temperature Trend Over Time
                                </h5>
                            </div>
                            <div class="card-body" style="height: 350px; position: relative;">
                                <canvas id="temperatureChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- BSW Chart -->
                    <div class="col-md-6 mt-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title" style="margin: 0;">
                                    <i class="fe fe-droplet mr-2"></i>Basic Sediment & Water (BSW) Trend
                                </h5>
                            </div>
                            <div class="card-body" style="height: 350px; position: relative;">
                                <canvas id="bswChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- WLR Chart -->
                    <div class="col-md-6 mt-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title" style="margin: 0;">
                                    <i class="fe fe-activity mr-2"></i>Water-Liquid Ratio (WLR) Trend
                                </h5>
                            </div>
                            <div class="card-body" style="height: 350px; position: relative;">
                                <canvas id="wlrChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- GVF Chart -->
                    <div class="col-md-12 mt-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title" style="margin: 0;">
                                    <i class="fe fe-wind mr-2"></i>Gas Void Fraction (GVF) Trend
                                </h5>
                            </div>
                            <div class="card-body" style="height: 350px; position: relative;">
                                <canvas id="gvfChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

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

                <!-- Supervisor Review Section -->
                @if($mpfmTest->status === 'draft' || $mpfmTest->status === 'rejected')
                <div class="row mt-4">
                    <div class="col-md-12">
                        <h5>Supervisor Review Decision</h5>
                        <div class="card border-primary">
                            <div class="card-body">
                                <form id="testReviewForm" method="POST" action="{{ route('mpfm-tests.approve', $mpfmTest->id) }}">
                                    @csrf

                                    <div class="form-group">
                                        <label><strong>Make a Decision:</strong></label>
                                        <div class="custom-control custom-radio mb-3">
                                            <input type="radio" class="custom-control-input" id="approveRadio" name="action" value="approve">
                                            <label class="custom-control-label" for="approveRadio">
                                                <i class="fe fe-check-circle text-success mr-2"></i>
                                                <strong>Approve Test</strong>
                                                <br><small class="text-muted ml-4">Test data is acceptable and valid</small>
                                            </label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" id="rejectRadio" name="action" value="reject">
                                            <label class="custom-control-label" for="rejectRadio">
                                                <i class="fe fe-x-circle text-danger mr-2"></i>
                                                <strong>Reject Test</strong>
                                                <br><small class="text-muted ml-4">Request operator to re-upload with corrections</small>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="form-group" id="remarksGroup" style="display: none;">
                                        <label for="remarks"><strong>Reason for Rejection (Required)</strong></label>
                                        <textarea class="form-control @error('remarks') is-invalid @enderror" id="remarks" name="remarks" rows="4" placeholder="Explain what needs to be corrected..."></textarea>
                                        @error('remarks')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Be specific about issues and required corrections</small>
                                    </div>

                                    <div class="form-group mt-3">
                                        <button type="submit" class="btn btn-success" id="submitBtn">
                                            <i class="fe fe-check mr-2"></i> Submit Decision
                                        </button>
                                        <a href="{{ route('monthly-plans.show', $task->monthly_plan_id) }}" class="btn btn-secondary">
                                            <i class="fe fe-arrow-left mr-2"></i> Back to Plan
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @elseif($mpfmTest->status === 'approved')
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="alert alert-success" role="alert">
                            <i class="fe fe-check-circle mr-2"></i>
                            <strong>Test Approved!</strong> This test has been approved by the supervisor.
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
// Chart.js initialization
document.addEventListener('DOMContentLoaded', function() {
    @if($mpfmTest && $mpfmTest->test_data)
    const testData = @json($mpfmTest->test_data);
    const rows = testData.rows || [];
    const columns = testData.columns || [];

    if (rows.length === 0) return;

    // Helper function to extract column data
    function extractColumnData(...possibleNames) {
        let foundColumn = null;

        for (let name of possibleNames) {
            if (columns.includes(name)) {
                foundColumn = name;
                break;
            }
        }

        if (!foundColumn) {
            for (let name of possibleNames) {
                foundColumn = columns.find(col =>
                    col.toLowerCase().includes(name.toLowerCase()) ||
                    name.toLowerCase().includes(col.toLowerCase())
                );
                if (foundColumn) break;
            }
        }

        if (!foundColumn) return [];

        return rows.map(row => {
            const value = row[foundColumn];
            return value !== undefined && value !== null && value !== '' ? parseFloat(value) : null;
        }).filter(v => v !== null);
    }

    // Extract data
    const oilRates = extractColumnData('qo_sc[STB/d]', 'qo_lc[bbl/d]');
    const waterRates = extractColumnData('qw_sc[STB/d]', 'qw_lc[bbl/d]');
    const gasRates = extractColumnData('qg_sc[MMScf/d]', 'qg_lc[Mcf/d]');
    const gorData = extractColumnData('GOR[Scf/STB]');
    const bswData = extractColumnData('BSW[%]');
    const wlrData = extractColumnData('WLR[%]');
    const gvfData = extractColumnData('GVF[%]');
    const pressureData = extractColumnData('WHP[psi]', 'Press');
    const tempData = extractColumnData('Temp', 'Temperature');

    // Time labels
    const timeColumn = columns.find(c => c.toLowerCase().includes('time'));
    const timeLabels = timeColumn ? rows.slice(0, Math.max(oilRates.length, gasRates.length)).map(row => row[timeColumn] || '') :
                                   Array.from({length: Math.max(oilRates.length, gasRates.length)}, (_, i) => `T${i+1}`);

    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: { font: { size: 11 }, padding: 15 }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                titleFont: { size: 12, weight: 'bold' },
                bodyFont: { size: 11 }
            }
        },
        scales: {
            x: { grid: { display: false } },
            y: { beginAtZero: true, grid: { color: 'rgba(0, 0, 0, 0.05)' } }
        }
    };

    // Production Rates Chart
    if (document.getElementById('ratesChart') && oilRates.length > 0) {
        new Chart(document.getElementById('ratesChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: timeLabels.slice(0, Math.max(oilRates.length, gasRates.length)),
                datasets: [
                    {
                        label: 'Oil Rate (STB/d)',
                        data: oilRates,
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Water Rate (STB/d)',
                        data: waterRates,
                        borderColor: '#0066cc',
                        backgroundColor: 'rgba(0, 102, 204, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: chartOptions
        });
    }

    // GOR Chart
    if (document.getElementById('gorChart') && gorData.length > 0) {
        new Chart(document.getElementById('gorChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: timeLabels.slice(0, gorData.length),
                datasets: [{
                    label: 'GOR (Scf/STB)',
                    data: gorData,
                    borderColor: '#ff9800',
                    backgroundColor: 'rgba(255, 152, 0, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: chartOptions
        });
    }

    // Pressure Chart
    if (document.getElementById('pressureChart') && pressureData.length > 0) {
        new Chart(document.getElementById('pressureChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: timeLabels.slice(0, pressureData.length),
                datasets: [{
                    label: 'Pressure (psi)',
                    data: pressureData,
                    borderColor: '#ec4899',
                    backgroundColor: 'rgba(236, 72, 153, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: chartOptions
        });
    }

    // Temperature Chart
    if (document.getElementById('temperatureChart') && tempData.length > 0) {
        new Chart(document.getElementById('temperatureChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: timeLabels.slice(0, tempData.length),
                datasets: [{
                    label: 'Temperature (°F)',
                    data: tempData,
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: chartOptions
        });
    }

    // BSW Chart
    if (document.getElementById('bswChart') && bswData.length > 0) {
        new Chart(document.getElementById('bswChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: timeLabels.slice(0, bswData.length),
                datasets: [{
                    label: 'BSW (%)',
                    data: bswData,
                    borderColor: '#9c27b0',
                    backgroundColor: 'rgba(156, 39, 176, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: chartOptions
        });
    }

    // WLR Chart
    if (document.getElementById('wlrChart') && wlrData.length > 0) {
        new Chart(document.getElementById('wlrChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: timeLabels.slice(0, wlrData.length),
                datasets: [{
                    label: 'WLR (%)',
                    data: wlrData,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: chartOptions
        });
    }

    // GVF Chart
    if (document.getElementById('gvfChart') && gvfData.length > 0) {
        new Chart(document.getElementById('gvfChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: timeLabels.slice(0, gvfData.length),
                datasets: [{
                    label: 'GVF (%)',
                    data: gvfData,
                    borderColor: '#14b8a6',
                    backgroundColor: 'rgba(20, 184, 166, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: chartOptions
        });
    }
    @endif
});

// Review form logic
document.querySelectorAll('input[name="action"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const remarksGroup = document.getElementById('remarksGroup');
        const remarksField = document.getElementById('remarks');

        if (this.value === 'reject') {
            remarksGroup.style.display = 'block';
            remarksField.required = true;
        } else {
            remarksGroup.style.display = 'none';
            remarksField.required = false;
            remarksField.value = '';
        }
    });
});

document.getElementById('testReviewForm')?.addEventListener('submit', function(e) {
    const action = document.querySelector('input[name="action"]:checked');
    if (!action) {
        e.preventDefault();
        alert('Please select a decision');
        return false;
    }

    if (action.value === 'reject') {
        const remarks = document.getElementById('remarks').value.trim();
        if (!remarks) {
            e.preventDefault();
            alert('Please provide a reason for rejection');
            return false;
        }
    }
});
</script>
@endpush

