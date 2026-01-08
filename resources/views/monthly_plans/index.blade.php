@extends('layouts.master')

@section('title', 'Monthly Plans')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div>
        <h1 class="page-title">Monthly Plans</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Monthly Plans</li>
        </ol>
    </div>
    <div class="ml-auto">
        <a href="{{ route('monthly-plans.create') }}" class="btn btn-primary">
            <i class="fe fe-plus mr-2"></i> Create New Plan
        </a>
    </div>
</div>

<!-- Success/Error Messages -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fe fe-check-circle mr-2"></i> {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fe fe-x-circle mr-2"></i> {{ session('error') }}
    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
</div>
@endif

<!-- Plans List -->
<div class="row">
    @forelse($plans as $plan)
    <div class="col-lg-6 col-xl-4">
        <div class="card" style="border-left: 4px solid {{ $plan->status === 'active' ? '#10b981' : ($plan->status === 'draft' ? '#fbbf24' : ($plan->status === 'under_review' ? '#3b82f6' : '#ef4444')) }};">
            <div class="card-body">
                <!-- Plan Header -->
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h4 class="card-title mb-1">{{ $plan->plan_name }}</h4>
                        <p class="text-muted small mb-0">{{ $plan->plan_month }}</p>
                    </div>
                    <span class="badge badge-{{ $plan->status === 'active' ? 'success' : ($plan->status === 'draft' ? 'warning' : ($plan->status === 'under_review' ? 'info' : 'danger')) }}">
                        {{ ucfirst(str_replace('_', ' ', $plan->status)) }}
                    </span>
                </div>

                <!-- Statistics -->
                <div class="row text-center mb-3">
                    <div class="col-4">
                        <div class="p-2" style="background: #f3f4f6; border-radius: 8px;">
                            <h3 class="mb-0 font-weight-bold" style="color: #3b82f6;">{{ $plan->total_tests }}</h3>
                            <small class="text-muted">Total Tests</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2" style="background: #f3f4f6; border-radius: 8px;">
                            <h3 class="mb-0 font-weight-bold" style="color: #10b981;">{{ $plan->total_completed }}</h3>
                            <small class="text-muted">Completed</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2" style="background: #f3f4f6; border-radius: 8px;">
                            <h3 class="mb-0 font-weight-bold" style="color: #fbbf24;">{{ $plan->total_pending }}</h3>
                            <small class="text-muted">Pending</small>
                        </div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted">Progress</small>
                        <small class="text-muted">{{ $plan->total_tests > 0 ? round(($plan->total_completed / $plan->total_tests) * 100) : 0 }}%</small>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-success" style="width: {{ $plan->total_tests > 0 ? ($plan->total_completed / $plan->total_tests) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <!-- KPI -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted small">KPI Target:</span>
                    <span class="font-weight-semibold">{{ $plan->monthly_target_kpi }} tests</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted small">KPI Progress:</span>
                    <span class="font-weight-semibold {{ $plan->kpi_progress >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ $plan->kpi_progress > 0 ? '+' : '' }}{{ $plan->kpi_progress }}
                    </span>
                </div>

                <!-- Meta Info -->
                <div class="text-muted small mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span><i class="fe fe-user mr-1"></i> Created by:</span>
                        <span>{{ $plan->creator->name }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span><i class="fe fe-calendar mr-1"></i> Created:</span>
                        <span>{{ $plan->created_at->format('d M Y') }}</span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="btn-group d-flex" role="group">
                    <a href="{{ route('monthly-plans.show', $plan) }}" class="btn btn-sm btn-primary flex-fill">
                        <i class="fe fe-eye mr-1"></i> View
                    </a>
                    @if($plan->isEditable())
                    <a href="{{ route('monthly-plans.edit', $plan) }}" class="btn btn-sm btn-info flex-fill">
                        <i class="fe fe-edit mr-1"></i> Edit
                    </a>
                    @endif
                    @if($plan->status === 'draft')
                    <button type="button" class="btn btn-sm btn-danger flex-fill" onclick="confirmDelete({{ $plan->id }})">
                        <i class="fe fe-trash mr-1"></i> Delete
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fe fe-inbox" style="font-size: 64px; color: #cbd5e1;"></i>
                <h3 class="mt-3 mb-2">No Monthly Plans Yet</h3>
                <p class="text-muted mb-4">Get started by creating your first monthly plan</p>
                <a href="{{ route('monthly-plans.create') }}" class="btn btn-primary">
                    <i class="fe fe-plus mr-2"></i> Create First Plan
                </a>
            </div>
        </div>
    </div>
    @endforelse
</div>

<!-- Pagination -->
@if($plans->hasPages())
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-center">
            {{ $plans->links() }}
        </div>
    </div>
</div>
@endif

<!-- Delete Confirmation Modal -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('scripts')
<script>
function confirmDelete(planId) {
    if (confirm('Are you sure you want to delete this plan? This action cannot be undone.')) {
        const form = document.getElementById('deleteForm');
        form.action = `/monthly-plans/${planId}`;
        form.submit();
    }
}
</script>
@endpush
