@extends('layouts.master')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h2 class="mb-4">{{ isset($task) ? 'Edit Task' : 'Create New Task' }}</h2>

            <form action="{{ isset($task) ? route('tasks.update', $task) : route('tasks.store') }}" method="POST">
                @csrf
                @if (isset($task))
                    @method('PUT')
                @endif

                <!-- Basic Information Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Basic Information</h5>
                    </div>
                    <div class="card-body">
                        <!-- Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror"
                                   id="title" name="title" value="{{ old('title', $task->title ?? '') }}"
                                   required placeholder="Enter task title" />
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3"
                                      placeholder="Enter task description">{{ old('description', $task->description ?? '') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                      id="notes" name="notes" rows="2"
                                      placeholder="Additional notes...">{{ old('notes', $task->notes ?? '') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Assignment & Scheduling Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Assignment & Scheduling</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Monthly Plan -->
                            <div class="col-md-6 mb-3">
                                <label for="monthly_plan_id" class="form-label">Monthly Plan <span class="text-danger">*</span></label>
                                <select class="form-select @error('monthly_plan_id') is-invalid @enderror"
                                        id="monthly_plan_id" name="monthly_plan_id" required>
                                    <option value="">Select a monthly plan...</option>
                                    @foreach ($monthlyPlans as $plan)
                                        <option value="{{ $plan->id }}"
                                                {{ old('monthly_plan_id', $task->monthly_plan_id ?? '') == $plan->id ? 'selected' : '' }}>
                                            {{ $plan->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('monthly_plan_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Department -->
                            <div class="col-md-6 mb-3">
                                <label for="department_id" class="form-label">Department <span class="text-danger">*</span></label>
                                <select class="form-select @error('department_id') is-invalid @enderror"
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
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Task Type -->
                            <div class="col-md-6 mb-3">
                                <label for="task_type" class="form-label">Task Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('task_type') is-invalid @enderror"
                                        id="task_type" name="task_type" required>
                                    <option value="">Select task type...</option>
                                    @foreach (['plan_task' => 'Plan Task', 'daily_plan' => 'Daily Plan', 'operator_plan' => 'Operator Plan'] as $value => $label)
                                        <option value="{{ $value }}"
                                                {{ old('task_type', $task->task_type ?? '') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('task_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Priority -->
                            <div class="col-md-6 mb-3">
                                <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                                <select class="form-select @error('priority') is-invalid @enderror"
                                        id="priority" name="priority" required>
                                    @foreach (['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'critical' => 'Critical'] as $value => $label)
                                        <option value="{{ $value }}"
                                                {{ old('priority', $task->priority ?? 'medium') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Assigned To User -->
                            <div class="col-md-6 mb-3">
                                <label for="assigned_to_user_id" class="form-label">Assign To (Optional)</label>
                                <select class="form-select @error('assigned_to_user_id') is-invalid @enderror"
                                        id="assigned_to_user_id" name="assigned_to_user_id">
                                    <option value="">Unassigned</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                                {{ old('assigned_to_user_id', $task->assigned_to_user_id ?? '') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('assigned_to_user_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Expected Completion -->
                            <div class="col-md-6 mb-3">
                                <label for="expected_completion_at" class="form-label">Expected Completion</label>
                                <input type="datetime-local" class="form-control @error('expected_completion_at') is-invalid @enderror"
                                       id="expected_completion_at" name="expected_completion_at"
                                       value="{{ old('expected_completion_at', isset($task) && $task->expected_completion_at ? $task->expected_completion_at->format('Y-m-d\TH:i') : '') }}" />
                                @error('expected_completion_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Asset Association Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Asset Association (Optional)</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Oil String -->
                            <div class="col-md-6 mb-3">
                                <label for="oil_string_id" class="form-label">Oil String</label>
                                <select class="form-select @error('oil_string_id') is-invalid @enderror"
                                        id="oil_string_id" name="oil_string_id">
                                    <option value="">Select oil string...</option>
                                    @foreach ($oilStrings as $string)
                                        <option value="{{ $string->id }}"
                                                {{ old('oil_string_id', $task->oil_string_id ?? '') == $string->id ? 'selected' : '' }}>
                                            {{ $string->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('oil_string_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Tower -->
                            <div class="col-md-6 mb-3">
                                <label for="tower_id" class="form-label">Tower</label>
                                <select class="form-select @error('tower_id') is-invalid @enderror"
                                        id="tower_id" name="tower_id">
                                    <option value="">Select tower...</option>
                                    @foreach ($towers as $tower)
                                        <option value="{{ $tower->id }}"
                                                {{ old('tower_id', $task->tower_id ?? '') == $tower->id ? 'selected' : '' }}>
                                            {{ $tower->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tower_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-save"></i>
                                    {{ isset($task) ? 'Update Task' : 'Create Task' }}
                                </button>
                            </div>
                            <div class="col-md-6">
                                <a href="{{ isset($task) ? route('tasks.show', $task) : route('tasks.index') }}"
                                   class="btn btn-secondary w-100">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
