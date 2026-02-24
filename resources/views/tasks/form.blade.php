@extends('layouts.master')

@push('styles')
<style>
    /* ===== Page Layout ===== */
    .task-form-page {
        background: #f0f4f8;
        min-height: 100vh;
        padding: 2rem 0 3rem;
    }

    /* ===== Page Header ===== */
    .page-hero {
        background: linear-gradient(135deg, #1a237e 0%, #283593 40%, #1565c0 100%);
        border-radius: 20px;
        padding: 2rem 2.5rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(21, 101, 192, 0.3);
    }
    .page-hero::before {
        content: '';
        position: absolute;
        top: -40%;
        right: -5%;
        width: 350px;
        height: 350px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
    }
    .page-hero::after {
        content: '';
        position: absolute;
        bottom: -50%;
        right: 15%;
        width: 200px;
        height: 200px;
        background: rgba(255,255,255,0.04);
        border-radius: 50%;
    }
    .page-hero .hero-icon {
        width: 56px;
        height: 56px;
        background: rgba(255,255,255,0.15);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: #fff;
        margin-bottom: 1rem;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.2);
    }
    .page-hero h1 {
        color: #fff;
        font-size: 1.75rem;
        font-weight: 700;
        margin: 0 0 0.3rem;
        letter-spacing: -0.3px;
    }
    .page-hero p {
        color: rgba(255,255,255,0.75);
        margin: 0;
        font-size: 0.95rem;
    }
    .page-hero .back-btn {
        position: absolute;
        top: 1.5rem;
        right: 1.5rem;
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.2);
        color: #fff;
        border-radius: 10px;
        padding: 0.5rem 1.2rem;
        font-size: 0.85rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s;
        backdrop-filter: blur(10px);
    }
    .page-hero .back-btn:hover {
        background: rgba(255,255,255,0.22);
        color: #fff;
        transform: translateX(-2px);
    }

    /* ===== Step Indicator ===== */
    .steps-bar {
        display: flex;
        gap: 0;
        background: #fff;
        border-radius: 16px;
        padding: 0.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    }
    .step-item {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.6rem;
        padding: 0.75rem 0.5rem;
        border-radius: 12px;
        font-size: 0.82rem;
        font-weight: 500;
        color: #94a3b8;
        cursor: default;
        transition: all 0.25s;
        position: relative;
    }
    .step-item .step-num {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: #e2e8f0;
        color: #94a3b8;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 700;
        flex-shrink: 0;
        transition: all 0.25s;
    }
    .step-item.active {
        background: linear-gradient(135deg, #eff6ff, #dbeafe);
        color: #1d4ed8;
    }
    .step-item.active .step-num {
        background: #1d4ed8;
        color: #fff;
    }
    .step-item.done {
        color: #16a34a;
    }
    .step-item.done .step-num {
        background: #16a34a;
        color: #fff;
    }

    /* ===== Section Cards ===== */
    .form-section {
        background: #fff;
        border-radius: 20px;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        overflow: hidden;
        border: 1px solid #e8edf2;
        transition: box-shadow 0.2s;
    }
    .form-section:hover {
        box-shadow: 0 4px 24px rgba(0,0,0,0.09);
    }
    .section-header {
        padding: 1.25rem 1.75rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 0.85rem;
    }
    .section-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        flex-shrink: 0;
    }
    .icon-blue   { background: #eff6ff; color: #2563eb; }
    .icon-purple { background: #faf5ff; color: #7c3aed; }
    .icon-amber  { background: #fffbeb; color: #d97706; }
    .icon-green  { background: #f0fdf4; color: #16a34a; }

    .section-header h5 {
        margin: 0;
        font-size: 1rem;
        font-weight: 700;
        color: #1e293b;
        letter-spacing: -0.2px;
    }
    .section-header p {
        margin: 0;
        font-size: 0.78rem;
        color: #94a3b8;
    }
    .section-body {
        padding: 1.75rem;
    }

    /* ===== Form Controls ===== */
    .field-group {
        margin-bottom: 1.4rem;
    }
    .field-group:last-child { margin-bottom: 0; }

    .field-label {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.82rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }
    .field-label .req {
        color: #ef4444;
        font-size: 0.7rem;
    }
    .field-label .opt-tag {
        font-size: 0.68rem;
        background: #f1f5f9;
        color: #64748b;
        padding: 1px 7px;
        border-radius: 20px;
        font-weight: 500;
        text-transform: none;
        letter-spacing: 0;
    }

    .modern-input, .modern-select, .modern-textarea {
        width: 100%;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.7rem 1rem;
        font-size: 0.9rem;
        color: #1e293b;
        background: #fff;
        transition: all 0.2s;
        outline: none;
        appearance: none;
        -webkit-appearance: none;
    }
    .modern-input:focus, .modern-select:focus, .modern-textarea:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
    }
    .modern-input::placeholder, .modern-textarea::placeholder {
        color: #c0ccda;
    }
    .modern-input.is-invalid, .modern-select.is-invalid, .modern-textarea.is-invalid {
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239,68,68,0.1);
    }
    .modern-select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.85rem center;
        padding-right: 2.5rem;
        cursor: pointer;
    }
    .modern-textarea {
        resize: vertical;
        min-height: 90px;
    }
    .field-error {
        color: #ef4444;
        font-size: 0.78rem;
        margin-top: 0.35rem;
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }

    /* ===== Priority Radio Cards ===== */
    .priority-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.6rem;
    }
    .priority-card {
        position: relative;
    }
    .priority-card input[type="radio"] {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }
    .priority-card label {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 0.85rem 0.5rem;
        border: 2px solid #e2e8f0;
        border-radius: 14px;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 0.8rem;
        font-weight: 600;
        color: #64748b;
        gap: 0.4rem;
        background: #fafbfc;
    }
    .priority-card label i {
        font-size: 1.1rem;
    }
    .priority-card input:checked + label {
        border-width: 2px;
        transform: translateY(-1px);
    }
    .priority-card[data-val="low"] input:checked + label    { border-color: #22c55e; background: #f0fdf4; color: #16a34a; box-shadow: 0 4px 12px rgba(34,197,94,0.15); }
    .priority-card[data-val="medium"] input:checked + label { border-color: #f59e0b; background: #fffbeb; color: #d97706; box-shadow: 0 4px 12px rgba(245,158,11,0.15); }
    .priority-card[data-val="high"] input:checked + label   { border-color: #f97316; background: #fff7ed; color: #ea580c; box-shadow: 0 4px 12px rgba(249,115,22,0.15); }
    .priority-card[data-val="critical"] input:checked + label { border-color: #ef4444; background: #fef2f2; color: #dc2626; box-shadow: 0 4px 12px rgba(239,68,68,0.15); }

    /* ===== Task Type Cards ===== */
    .type-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
    }
    .type-card {
        position: relative;
    }
    .type-card input[type="radio"] {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }
    .type-card label {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        padding: 1rem 1.1rem;
        border: 2px solid #e2e8f0;
        border-radius: 14px;
        cursor: pointer;
        transition: all 0.2s;
        background: #fafbfc;
        gap: 0.4rem;
    }
    .type-card label .type-icon {
        width: 34px;
        height: 34px;
        border-radius: 9px;
        background: #f1f5f9;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        transition: all 0.2s;
    }
    .type-card label .type-name {
        font-size: 0.82rem;
        font-weight: 700;
        color: #374151;
        line-height: 1.2;
    }
    .type-card label .type-desc {
        font-size: 0.73rem;
        color: #94a3b8;
        line-height: 1.3;
    }
    .type-card input:checked + label {
        border-color: #2563eb;
        background: #eff6ff;
        box-shadow: 0 4px 16px rgba(37,99,235,0.12);
    }
    .type-card input:checked + label .type-icon {
        background: #2563eb;
        color: #fff;
    }
    .type-card input:checked + label .type-name { color: #1d4ed8; }

    /* ===== Form Footer ===== */
    .form-footer {
        background: #fff;
        border-radius: 20px;
        padding: 1.5rem 1.75rem;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        border: 1px solid #e8edf2;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }
    .footer-hint {
        font-size: 0.8rem;
        color: #94a3b8;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }
    .btn-submit {
        background: linear-gradient(135deg, #1d4ed8, #2563eb);
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: 0.75rem 2rem;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 4px 16px rgba(37,99,235,0.3);
        min-width: 160px;
        justify-content: center;
    }
    .btn-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(37,99,235,0.4);
    }
    .btn-submit:active { transform: translateY(0); }
    .btn-cancel {
        background: #f8fafc;
        color: #64748b;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.75rem 1.5rem;
        font-size: 0.9rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .btn-cancel:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
        color: #374151;
    }

    /* ===== Validation Alerts ===== */
    .validation-banner {
        background: #fef2f2;
        border: 1px solid #fecaca;
        border-radius: 14px;
        padding: 1rem 1.25rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
    }
    .validation-banner i { color: #ef4444; margin-top: 1px; }
    .validation-banner strong { color: #dc2626; font-size: 0.9rem; }
    .validation-banner ul { margin: 0.4rem 0 0; padding-left: 1.2rem; }
    .validation-banner ul li { font-size: 0.82rem; color: #b91c1c; }

    /* ===== Responsive ===== */
    @media (max-width: 768px) {
        .priority-grid { grid-template-columns: repeat(2, 1fr); }
        .type-grid { grid-template-columns: 1fr; }
        .form-footer { flex-direction: column; }
        .steps-bar { display: none; }
        .page-hero { padding: 1.5rem; }
    }
    @media (max-width: 576px) {
        .task-form-page { padding: 1rem 0 2rem; }
        .section-body { padding: 1.25rem; }
        .page-hero h1 { font-size: 1.4rem; }
    }
</style>
@endpush

@section('content')
<div class="task-form-page">
<div class="container-lg" style="max-width: 100%;">

    {{-- Hero Header --}}
    <div class="page-hero">
        <a href="{{ isset($task) ? route('tasks.show', $task) : route('tasks.index') }}" class="back-btn">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
        <div class="hero-icon">
            <i class="fas {{ isset($task) ? 'fa-edit' : 'fa-plus' }}"></i>
        </div>
        <h1>{{ isset($task) ? 'Edit Task' : 'Create New Task' }}</h1>
        <p>{{ isset($task) ? 'Update the details below to modify this task' : 'Fill in the details below to create a new task' }}</p>

        @if(!isset($task))
        <a href="{{ route('tasks.import') }}"
           style="margin-top:1rem; display:inline-flex; align-items:center; gap:.5rem;
                  background:rgba(255,255,255,.13); border:1px solid rgba(255,255,255,.25);
                  color:#fff; border-radius:10px; padding:.45rem 1.1rem;
                  font-size:.82rem; font-weight:600; text-decoration:none; backdrop-filter:blur(10px);
                  transition:all .2s;">
            <i class="fas fa-file-excel"></i> Import from Excel (Bulk)
        </a>
        @endif
    </div>

    {{-- Steps Bar --}}
    <div class="steps-bar">
        <div class="step-item active">
            <span class="step-num">1</span>
            <span>Basic Info</span>
        </div>
        <div class="step-item">
            <span class="step-num">2</span>
            <span>Assignment</span>
        </div>
        <div class="step-item">
            <span class="step-num">3</span>
            <span>Priority & Type</span>
        </div>
        <div class="step-item">
            <span class="step-num">4</span>
            <span>Assets</span>
        </div>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
    <div class="validation-banner">
        <i class="fas fa-exclamation-circle fa-lg"></i>
        <div>
            <strong>Please fix the following errors:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <form action="{{ isset($task) ? route('tasks.update', $task) : route('tasks.store') }}" method="POST" id="taskForm">
        @csrf
        @if (isset($task))
            @method('PUT')
        @endif

        {{-- ===== SECTION 1: Basic Information ===== --}}
        <div class="form-section">
            <div class="section-header">
                <div class="section-icon icon-blue">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div>
                    <h5>Basic Information</h5>
                    <p>Title, description, and notes for this task</p>
                </div>
            </div>
            <div class="section-body">
                {{-- Title --}}
                <div class="field-group">
                    <div class="field-label">
                        Task Title <span class="req">*</span>
                    </div>
                    <input type="text"
                           class="modern-input @error('title') is-invalid @enderror"
                           id="title" name="title"
                           value="{{ old('title', $task->title ?? '') }}"
                           required
                           placeholder="e.g. Monthly MPFM Calibration - Well A-12" />
                    @error('title')
                        <div class="field-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>

                {{-- Description --}}
                <div class="field-group">
                    <div class="field-label">
                        Description <span class="opt-tag">Optional</span>
                    </div>
                    <textarea class="modern-textarea @error('description') is-invalid @enderror"
                              id="description" name="description"
                              placeholder="Describe the scope and objectives of this task...">{{ old('description', $task->description ?? '') }}</textarea>
                    @error('description')
                        <div class="field-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>

                {{-- Notes --}}
                <div class="field-group">
                    <div class="field-label">
                        Additional Notes <span class="opt-tag">Optional</span>
                    </div>
                    <textarea class="modern-textarea @error('notes') is-invalid @enderror"
                              id="notes" name="notes"
                              rows="2"
                              placeholder="Any special instructions or remarks...">{{ old('notes', $task->notes ?? '') }}</textarea>
                    @error('notes')
                        <div class="field-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- ===== SECTION 2: Assignment & Scheduling ===== --}}
        <div class="form-section">
            <div class="section-header">
                <div class="section-icon icon-purple">
                    <i class="fas fa-users-cog"></i>
                </div>
                <div>
                    <h5>Assignment & Scheduling</h5>
                    <p>Plan, department, assignee, and deadline</p>
                </div>
            </div>
            <div class="section-body">
                <div class="row g-3">
                    {{-- Monthly Plan --}}
                    <div class="col-md-6">
                        <div class="field-group">
                            <div class="field-label">Monthly Plan <span class="req">*</span></div>
                            <select class="modern-select @error('monthly_plan_id') is-invalid @enderror"
                                    id="monthly_plan_id" name="monthly_plan_id" required>
                                <option value="">Select a monthly plan...</option>
                                @foreach ($monthlyPlans as $plan)
                                    @php
                                        $preSelected = old('monthly_plan_id', $task->monthly_plan_id ?? $selectedPlanId ?? '');
                                    @endphp
                                    <option value="{{ $plan->id }}"
                                            {{ $preSelected == $plan->id ? 'selected' : '' }}>
                                        {{ $plan->plan_name }} — {{ $plan->plan_month }}
                                    </option>
                                @endforeach
                            </select>
                            @error('monthly_plan_id')
                                <div class="field-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Department --}}
                    <div class="col-md-6">
                        <div class="field-group">
                            <div class="field-label">Department <span class="req">*</span></div>
                            <select class="modern-select @error('department_id') is-invalid @enderror"
                                    id="department_id" name="department_id" required>
                                <option value="">Select a department...</option>
                                @foreach ($departments as $dept)
                                    <option value="{{ $dept->id }}"
                                            {{ old('department_id', $task->department_id ?? '') == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id')
                                <div class="field-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Assigned To --}}
                    <div class="col-md-6">
                        <div class="field-group">
                            <div class="field-label">
                                Assign To <span class="opt-tag">Optional</span>
                            </div>
                            <select class="modern-select @error('assigned_to_user_id') is-invalid @enderror"
                                    id="assigned_to_user_id" name="assigned_to_user_id">
                                <option value="">— Unassigned —</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}"
                                            {{ old('assigned_to_user_id', $task->assigned_to_user_id ?? '') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('assigned_to_user_id')
                                <div class="field-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Expected Completion --}}
                    <div class="col-md-6">
                        <div class="field-group">
                            <div class="field-label">
                                Expected Completion <span class="opt-tag">Optional</span>
                            </div>
                            <input type="datetime-local"
                                   class="modern-input @error('expected_completion_at') is-invalid @enderror"
                                   id="expected_completion_at" name="expected_completion_at"
                                   value="{{ old('expected_completion_at', isset($task) && $task->expected_completion_at ? $task->expected_completion_at->format('Y-m-d\TH:i') : '') }}" />
                            @error('expected_completion_at')
                                <div class="field-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== SECTION 3: Task Type & Priority ===== --}}
        <div class="form-section">
            <div class="section-header">
                <div class="section-icon icon-amber">
                    <i class="fas fa-tags"></i>
                </div>
                <div>
                    <h5>Task Type & Priority</h5>
                    <p>Classify this task and set its urgency level</p>
                </div>
            </div>
            <div class="section-body">

                {{-- Task Type --}}
                <div class="field-group">
                    <div class="field-label">Task Type <span class="req">*</span></div>
                    <div class="type-grid">
                        @php
                            $types = [
                                'plan_task'     => ['icon' => 'fa-calendar-check', 'name' => 'Plan Task',     'desc' => 'Scheduled monthly work'],
                                'daily_plan'    => ['icon' => 'fa-sun',            'name' => 'Daily Plan',    'desc' => 'Day-to-day operations'],
                                'operator_plan' => ['icon' => 'fa-hard-hat',       'name' => 'Operator Plan', 'desc' => 'Field operator tasks'],
                            ];
                            $selectedType = old('task_type', $task->task_type ?? '');
                        @endphp
                        @foreach ($types as $val => $meta)
                        <div class="type-card">
                            <input type="radio" name="task_type" id="type_{{ $val }}" value="{{ $val }}"
                                   {{ $selectedType === $val ? 'checked' : '' }} required>
                            <label for="type_{{ $val }}">
                                <div class="type-icon"><i class="fas {{ $meta['icon'] }}"></i></div>
                                <div class="type-name">{{ $meta['name'] }}</div>
                                <div class="type-desc">{{ $meta['desc'] }}</div>
                            </label>
                        </div>
                        @endforeach
                    </div>
                    @error('task_type')
                        <div class="field-error mt-2"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>

                {{-- Priority --}}
                <div class="field-group" style="margin-top: 1.5rem;">
                    <div class="field-label">Priority Level <span class="req">*</span></div>
                    <div class="priority-grid">
                        @php
                            $priorities = [
                                'low'      => ['icon' => 'fa-arrow-down',    'label' => 'Low'],
                                'medium'   => ['icon' => 'fa-minus',         'label' => 'Medium'],
                                'high'     => ['icon' => 'fa-arrow-up',      'label' => 'High'],
                                'critical' => ['icon' => 'fa-fire',          'label' => 'Critical'],
                            ];
                            $selectedPriority = old('priority', $task->priority ?? 'medium');
                        @endphp
                        @foreach ($priorities as $val => $meta)
                        <div class="priority-card" data-val="{{ $val }}">
                            <input type="radio" name="priority" id="priority_{{ $val }}" value="{{ $val }}"
                                   {{ $selectedPriority === $val ? 'checked' : '' }} required>
                            <label for="priority_{{ $val }}">
                                <i class="fas {{ $meta['icon'] }}"></i>
                                {{ $meta['label'] }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                    @error('priority')
                        <div class="field-error mt-2"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>

            </div>
        </div>

        {{-- ===== SECTION 4: Asset Association ===== --}}
        <div class="form-section">
            <div class="section-header">
                <div class="section-icon icon-green">
                    <i class="fas fa-oil-can"></i>
                </div>
                <div>
                    <h5>Asset Association</h5>
                    <p>Link this task to specific oil infrastructure assets</p>
                </div>
            </div>
            <div class="section-body">
                <div class="row g-3">
                    {{-- Oil String --}}
                    <div class="col-md-6">
                        <div class="field-group">
                            <div class="field-label">
                                Oil String <span class="opt-tag">Optional</span>
                            </div>
                            <select class="modern-select @error('oil_string_id') is-invalid @enderror"
                                    id="oil_string_id" name="oil_string_id">
                                <option value="">— None selected —</option>
                                @foreach ($oilStrings as $string)
                                    <option value="{{ $string->id }}"
                                            {{ old('oil_string_id', $task->oil_string_id ?? '') == $string->id ? 'selected' : '' }}>
                                        {{ $string->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('oil_string_id')
                                <div class="field-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Tower --}}
                    <div class="col-md-6">
                        <div class="field-group">
                            <div class="field-label">
                                Tower <span class="opt-tag">Optional</span>
                            </div>
                            <select class="modern-select @error('tower_id') is-invalid @enderror"
                                    id="tower_id" name="tower_id">
                                <option value="">— None selected —</option>
                                @foreach ($towers as $tower)
                                    <option value="{{ $tower->id }}"
                                            {{ old('tower_id', $task->tower_id ?? '') == $tower->id ? 'selected' : '' }}>
                                        {{ $tower->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tower_id')
                                <div class="field-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== SECTION 5: Department-Specific Fields (Dynamic) ===== --}}
        <div class="form-section" id="deptFieldsSection" style="display:none;">
            <div class="section-header" id="deptFieldsHeader">
                <div class="section-icon" id="deptSectionIcon" style="background:#f8fafc;color:#64748b;">
                    <i class="fas fa-building"></i>
                </div>
                <div>
                    <h5 id="deptSectionTitle">Department-Specific Information</h5>
                    <p id="deptSectionDesc">Additional fields required for this department</p>
                </div>
            </div>
            <div class="section-body" id="deptFieldsBody">
                <div class="row g-3" id="deptFieldsGrid">
                    {{-- Populated dynamically via JS --}}
                </div>
            </div>
        </div>

        {{-- ===== Form Footer ===== --}}
        <div class="form-footer">
            <div class="footer-hint">
                <i class="fas fa-shield-alt text-primary"></i>
                Task will be saved as <strong style="color:#1e293b; margin: 0 3px;">Draft</strong> initially
            </div>
            <div class="d-flex gap-2">
                <a href="{{ isset($task) ? route('tasks.show', $task) : route('tasks.index') }}" class="btn-cancel">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn-submit" id="submitBtn">
                    <i class="fas fa-{{ isset($task) ? 'save' : 'plus-circle' }}"></i>
                    {{ isset($task) ? 'Save Changes' : 'Create Task' }}
                </button>
            </div>
        </div>

    </form>
</div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Animate steps on scroll / field interaction (simple highlight)
    const sections = document.querySelectorAll('.form-section');
    const steps = document.querySelectorAll('.step-item');

    const sectionStepMap = [0, 1, 2, 3]; // section index → step index

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const idx = [...sections].indexOf(entry.target);
                steps.forEach((s, i) => {
                    s.classList.remove('active', 'done');
                    if (i < idx) s.classList.add('done');
                    else if (i === idx) s.classList.add('active');
                });
            }
        });
    }, { threshold: 0.4 });

    sections.forEach(s => observer.observe(s));

    // Submit button loading state
    const form = document.getElementById('taskForm');
    const submitBtn = document.getElementById('submitBtn');

    form.addEventListener('submit', function () {
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
        submitBtn.disabled = true;
    });

    // ─── Department Dynamic Fields ───
    const deptSelect   = document.getElementById('department_id');
    const deptSection  = document.getElementById('deptFieldsSection');
    const deptIcon     = document.getElementById('deptSectionIcon');
    const deptTitle    = document.getElementById('deptSectionTitle');
    const deptDesc     = document.getElementById('deptSectionDesc');
    const deptGrid     = document.getElementById('deptFieldsGrid');

    // Map department names to codes (populated from server select options)
    // We'll fetch by name matching
    const apiBase = '{{ rtrim(url("/"), "/") }}/tasks/api/department-fields/';

    function renderField(field) {
        const col = document.createElement('div');
        col.className = ['textarea'].includes(field.type) ? 'col-12' : 'col-md-6';

        const req = field.required ? '<span style="color:#ef4444;font-size:.65rem;">*</span>' : '<span style="font-size:.68rem;background:#f1f5f9;color:#64748b;padding:1px 7px;border-radius:20px;margin-left:4px;">Optional</span>';
        let inputHtml = '';

        if (field.type === 'select') {
            const opts = field.options.map(o => `<option value="${o}">${o}</option>`).join('');
            inputHtml = `<select name="dept_data[${field.key}]" class="modern-select" ${field.required ? 'required' : ''}>
                <option value="">— Select —</option>${opts}</select>`;
        } else if (field.type === 'textarea') {
            inputHtml = `<textarea name="dept_data[${field.key}]" class="modern-textarea" rows="2"
                placeholder="${field.placeholder || ''}" ${field.required ? 'required' : ''}></textarea>`;
        } else {
            const extras = field.type === 'number' ? 'step="any" min="0"' : '';
            inputHtml = `<input type="${field.type}" name="dept_data[${field.key}]"
                class="modern-input" value=""
                placeholder="${field.placeholder || ''}"
                ${extras} ${field.required ? 'required' : ''}>`;
        }

        col.innerHTML = `
            <div class="field-group">
                <div class="field-label">${field.label} ${req}</div>
                ${inputHtml}
            </div>`;
        return col;
    }

    async function loadDeptFields(deptId) {
        if (!deptId) {
            deptSection.style.display = 'none';
            deptGrid.innerHTML = '';
            return;
        }

        // Get dept name from selected option
        const option = deptSelect.querySelector(`option[value="${deptId}"]`);
        if (!option) return;

        // Extract department code from name (try to match our config codes)
        const deptName = option.textContent.trim().toUpperCase();
        const codeMap = {
            'MPFM': 'MPFM', 'WIRELINE': 'WIRELINE', 'WIRE LINE': 'WIRELINE',
            'PRODUCTION': 'PRODUCTION', 'DRILLING': 'DRILLING',
            'WELL COMPLETION': 'WELL_COMPLETION', 'COMPLETION': 'WELL_COMPLETION',
            'WORKOVER': 'WORKOVER', 'MAINTENANCE': 'MAINTENANCE',
        };

        let code = null;
        for (const [key, val] of Object.entries(codeMap)) {
            if (deptName.includes(key)) { code = val; break; }
        }

        if (!code) {
            deptSection.style.display = 'none';
            return;
        }

        try {
            const res = await fetch(apiBase + code, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();

            if (!data.fields || data.fields.length === 0) {
                deptSection.style.display = 'none';
                return;
            }

            // Update section header
            deptIcon.style.background = data.bg || '#f8fafc';
            deptIcon.style.color      = data.color || '#64748b';
            deptIcon.innerHTML        = `<i class="fas ${data.icon || 'fa-building'}"></i>`;
            deptTitle.textContent     = `${data.label} — Operation Details`;
            deptDesc.textContent      = data.description || '';

            // Render fields
            deptGrid.innerHTML = '';
            data.fields.forEach(f => deptGrid.appendChild(renderField(f)));

            deptSection.style.display = 'block';
            deptSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

        } catch (e) {
            console.warn('Dept fields load failed:', e);
            deptSection.style.display = 'none';
        }
    }

    deptSelect.addEventListener('change', () => loadDeptFields(deptSelect.value));

    // Load on page load if department is pre-selected (edit mode)
    if (deptSelect.value) loadDeptFields(deptSelect.value);

});
</script>
@endpush
