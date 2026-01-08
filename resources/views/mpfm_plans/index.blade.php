@extends('layouts.master')

@section('title', 'MPFM Plans')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>MPFM Plans</h2>
        <a href="{{ route('well_tests.index') }}" class="btn btn-secondary">Back to Well Tests</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Plan Name</th>
                            <th>Well Test</th>
                            <th>Expected Choke</th>
                            <th>Planned Oil Rate</th>
                            <th>Expected GOR</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($plans as $plan)
                        <tr>
                            <td>{{ $plan->id }}</td>
                            <td>{{ $plan->name }}</td>
                            <td>{{ $plan->wellTest->well_name ?? 'N/A' }}</td>
                            <td>{{ $plan->expected_choke }}</td>
                            <td>{{ number_format($plan->planned_oil_rate ?? 0, 2) }} STB/d</td>
                            <td>{{ number_format($plan->expected_gor ?? 0, 2) }} Scf/STB</td>
                            <td>{{ $plan->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <a href="{{ route('mpfm_plans.show', $plan->id) }}" class="btn btn-sm btn-primary">View</a>
                                <a href="{{ route('mpfm_plans.edit', $plan->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <a href="{{ route('mpfm_plans.report', $plan->id) }}" class="btn btn-sm btn-outline-info">Report</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">No MPFM plans found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center">
                {{ $plans->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
