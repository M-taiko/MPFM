@extends('layouts.master')

@section('title', 'Surveillance Supervisor - Plans Review')

@section('css')
<style>
    .plan-card {
        border-left: 4px solid #667eea;
        transition: all 0.3s ease;
    }

    .plan-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .pending-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: #dc3545;
        color: white;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .status-badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .progress-bar-wrapper {
        margin: 15px 0;
    }

    .task-count {
        display: flex;
        justify-content: space-around;
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid #eee;
    }

    .task-count-item {
        text-align: center;
    }

    .count-number {
        font-size: 1.5rem;
        font-weight: bold;
        color: #667eea;
    }

    .count-label {
        font-size: 0.8rem;
        color: #666;
        margin-top: 3px;
    }
</style>
@endsection

@section('content')
<div class="page-header">
    <div class="page-leftheader">
        <h4 class="page-title mb-0">
            <i class="fe fe-eye mr-2"></i>Surveillance Review
        </h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}"><i class="fe fe-home mr-2 fs-14"></i>Home</a></li>
            <li class="breadcrumb-item active">Plans for Review</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        @if ($plans->count() > 0)
            @foreach ($plans as $plan)
            <div class="card plan-card" style="position: relative;">
                @if ($plan->tasks->count() > 0)
                <div class="pending-badge">
                    {{ $plan->tasks->count() }} Pending
                </div>
                @endif

                <div class="card-header">
                    <h5 class="card-title mb-0">{{ $plan->plan_name }}</h5>
                    <small class="text-muted">{{ $plan->plan_month }}</small>
                </div>

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Created by:</strong><br>
                            {{ $plan->creator?->name ?? 'N/A' }}
                        </div>
                        <div class="col-md-4">
                            <strong>Created at:</strong><br>
                            {{ $plan->created_at->format('d M Y H:i') }}
                        </div>
                        <div class="col-md-4">
                            <strong>Status:</strong><br>
                            <span class="status-badge" style="
                                background-color:
                                @switch($plan->status)
                                    @case('submitted_to_surveillance')
                                        #ffc107
                                    @break
                                    @case('under_surveillance_review')
                                        #17a2b8
                                    @break
                                    @case('surveillance_approved')
                                        #28a745
                                    @break
                                    @case('surveillance_rejected')
                                        #dc3545
                                    @break
                                    @default
                                        #6c757d
                                @endswitch
                            ; color: white;">
                                {{ str_replace('_', ' ', ucfirst($plan->status)) }}
                            </span>
                        </div>
                    </div>

                    @if ($plan->tasks->count() > 0)
                    <div class="task-count">
                        <div class="task-count-item">
                            <div class="count-number">{{ $plan->tasks->count() }}</div>
                            <div class="count-label">Tasks to Review</div>
                        </div>
                        <div class="task-count-item">
                            <div class="count-number">{{ $plan->total_tests ?? 0 }}</div>
                            <div class="count-label">Total Tests</div>
                        </div>
                        <div class="task-count-item">
                            <div class="count-number">{{ $plan->total_completed ?? 0 }}</div>
                            <div class="count-label">Completed</div>
                        </div>
                    </div>

                    <div style="margin-top: 15px;">
                        <a href="{{ route('surveillance.review-plan', $plan) }}" class="btn btn-primary">
                            <i class="fe fe-eye mr-2"></i>Review Plan & Tasks
                        </a>
                    </div>
                    @else
                    <div class="alert alert-info mb-0">
                        <i class="fe fe-check-circle mr-2"></i>
                        All tasks have been reviewed
                    </div>
                    @endif
                </div>
            </div>
            @endforeach

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $plans->links() }}
            </div>
        @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fe fe-inbox" style="font-size: 64px; color: #cbd5e1;"></i>
                <h3 class="mt-3 mb-2">No Plans Awaiting Review</h3>
                <p class="text-muted">All monthly plans have been reviewed or are not ready for surveillance review yet.</p>
            </div>
        </div>
        @endif
    </div>
</div>

@endsection
