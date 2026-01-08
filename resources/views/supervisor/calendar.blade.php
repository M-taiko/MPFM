@extends('layouts.master')

@section('title', 'Plan Calendar')

@section('css')
<style>
    .calendar-container {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 10px;
    }
    .calendar-day-header {
        text-align: center;
        font-weight: bold;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 5px;
    }
    .calendar-day {
        min-height: 120px;
        border: 1px solid #e9ecef;
        border-radius: 5px;
        padding: 10px;
        background: white;
        transition: all 0.3s ease;
    }
    .calendar-day:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    .calendar-day.other-month {
        background: #f8f9fa;
        opacity: 0.5;
    }
    .calendar-day.today {
        border: 2px solid #667eea;
        background: #f0f4ff;
    }
    .day-number {
        font-weight: bold;
        color: #495057;
        margin-bottom: 5px;
    }
    .plan-badge {
        display: block;
        font-size: 11px;
        padding: 3px 6px;
        margin-bottom: 3px;
        border-radius: 3px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .plan-badge:hover {
        transform: scale(1.05);
    }
    .plan-badge.draft { background: #6c757d; color: white; }
    .plan-badge.active { background: #17a2b8; color: white; }
    .plan-badge.in_progress { background: #ffc107; color: #333; }
    .plan-badge.completed { background: #28a745; color: white; }
    .plan-badge.cancelled { background: #dc3545; color: white; }
</style>
@endsection

@section('content')
<div class="main-container container-fluid">
    <div class="page-header">
        <div class="page-leftheader">
            <h4 class="page-title mb-0">Plan Calendar</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}"><i class="fe fe-home mr-2 fs-14"></i>Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('supervisor.dashboard') }}">Supervisor</a></li>
                <li class="breadcrumb-item active" aria-current="page">Calendar</li>
            </ol>
        </div>
        <div class="page-rightheader">
            <a href="{{ route('daily-plans.create') }}" class="btn btn-primary">
                <i class="fe fe-plus mr-2"></i>Create New Plan
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="calendar-container">
                <div class="calendar-header">
                    <a href="{{ route('supervisor.calendar', ['month' => $month == 1 ? 12 : $month - 1, 'year' => $month == 1 ? $year - 1 : $year]) }}"
                       class="btn btn-sm btn-outline-primary">
                        <i class="fe fe-chevron-left"></i> Previous
                    </a>
                    <h3 class="mb-0">{{ \Carbon\Carbon::create($year, $month)->format('F Y') }}</h3>
                    <a href="{{ route('supervisor.calendar', ['month' => $month == 12 ? 1 : $month + 1, 'year' => $month == 12 ? $year + 1 : $year]) }}"
                       class="btn btn-sm btn-outline-primary">
                        Next <i class="fe fe-chevron-right"></i>
                    </a>
                </div>

                <div class="calendar-grid">
                    <!-- Day headers -->
                    <div class="calendar-day-header">Sun</div>
                    <div class="calendar-day-header">Mon</div>
                    <div class="calendar-day-header">Tue</div>
                    <div class="calendar-day-header">Wed</div>
                    <div class="calendar-day-header">Thu</div>
                    <div class="calendar-day-header">Fri</div>
                    <div class="calendar-day-header">Sat</div>

                    @php
                        $startDate = \Carbon\Carbon::create($year, $month, 1);
                        $endDate = $startDate->copy()->endOfMonth();
                        $startDay = $startDate->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);
                        $endDay = $endDate->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);

                        $currentDate = $startDay->copy();
                        $today = \Carbon\Carbon::today();
                    @endphp

                    @while($currentDate <= $endDay)
                        @php
                            $dateKey = $currentDate->format('Y-m-d');
                            $dayPlans = $plans->get($dateKey, collect());
                            $isCurrentMonth = $currentDate->month == $month;
                            $isToday = $currentDate->isSameDay($today);
                        @endphp

                        <div class="calendar-day {{ !$isCurrentMonth ? 'other-month' : '' }} {{ $isToday ? 'today' : '' }}">
                            <div class="day-number">{{ $currentDate->day }}</div>

                            @foreach($dayPlans as $plan)
                                <a href="{{ route('daily-plans.show', $plan) }}"
                                   class="plan-badge {{ $plan->status }}"
                                   title="{{ $plan->plan_name }} - {{ ucfirst($plan->status) }}">
                                    {{ Str::limit($plan->plan_name, 15) }}
                                </a>
                            @endforeach

                            @if($dayPlans->isEmpty() && $isCurrentMonth)
                                <a href="{{ route('daily-plans.create', ['date' => $dateKey]) }}"
                                   class="text-muted"
                                   style="font-size: 11px; text-decoration: none;">
                                    <i class="fe fe-plus"></i> Add plan
                                </a>
                            @endif
                        </div>

                        @php
                            $currentDate->addDay();
                        @endphp
                    @endwhile
                </div>

                <!-- Legend -->
                <div class="mt-4">
                    <h6>Status Legend:</h6>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="plan-badge draft">Draft</span>
                        <span class="plan-badge active">Active</span>
                        <span class="plan-badge in_progress">In Progress</span>
                        <span class="plan-badge completed">Completed</span>
                        <span class="plan-badge cancelled">Cancelled</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
