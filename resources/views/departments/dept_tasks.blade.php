@extends('layouts.master')

@push('styles')
<style>
:root {
    --dept-color: {{ $config['color'] }};
    --dept-bg: {{ $config['bg'] ?? '#f8fafc' }};
}

.dept-page { background: #f0f4f8; min-height: 100vh; padding: 1.5rem 0 3rem; }

/* Hero */
.dept-hero {
    border-radius: 20px; padding: 1.75rem 2.5rem; margin-bottom: 2rem;
    position: relative; overflow: hidden;
    background: linear-gradient(135deg, color-mix(in srgb, var(--dept-color) 85%, #000), var(--dept-color));
    box-shadow: 0 10px 40px color-mix(in srgb, var(--dept-color) 30%, transparent);
}
.dept-hero::before {
    content: ''; position: absolute; top: -40%; right: -5%;
    width: 280px; height: 280px;
    background: rgba(255,255,255,.06); border-radius: 50%;
}
.dept-hero h1 { color: #fff; font-size: 1.5rem; font-weight: 700; margin: 0 0 .25rem; }
.dept-hero p  { color: rgba(255,255,255,.72); margin: 0; font-size: .88rem; }
.dept-hero .back-btn {
    position: absolute; top: 1.25rem; right: 1.5rem;
    background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.22);
    color: #fff; border-radius: 10px; padding: .4rem 1rem;
    font-size: .8rem; font-weight: 500; text-decoration: none; transition: all .2s;
}
.dept-hero .back-btn:hover { background: rgba(255,255,255,.22); color: #fff; }

/* Filters card */
.filter-card {
    background: #fff; border-radius: 16px; border: 1px solid #e8edf2;
    box-shadow: 0 2px 10px rgba(0,0,0,.05); padding: 1.1rem 1.5rem; margin-bottom: 1.5rem;
}
.filter-card form { display: flex; gap: .75rem; flex-wrap: wrap; align-items: flex-end; }
.filter-card .form-group { display: flex; flex-direction: column; gap: .3rem; }
.filter-card label { font-size: .72rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: .4px; }
.filter-card select,
.filter-card input[type=text] {
    border: 1.5px solid #e2e8f0; border-radius: 10px; padding: .45rem .9rem;
    font-size: .84rem; color: #1e293b; background: #f8fafc; outline: none;
    transition: border-color .2s;
}
.filter-card select:focus,
.filter-card input[type=text]:focus { border-color: var(--dept-color); background: #fff; }
.filter-card .btn-filter {
    background: var(--dept-color); color: #fff; border: none; border-radius: 10px;
    padding: .5rem 1.25rem; font-size: .84rem; font-weight: 600; cursor: pointer;
    display: inline-flex; align-items: center; gap: .4rem;
}
.filter-card .btn-reset {
    background: #f1f5f9; color: #64748b; border: none; border-radius: 10px;
    padding: .5rem 1rem; font-size: .84rem; font-weight: 500; cursor: pointer; text-decoration: none;
    display: inline-flex; align-items: center; gap: .4rem;
}

/* Table card */
.tasks-card {
    background: #fff; border-radius: 18px; border: 1px solid #e8edf2;
    box-shadow: 0 2px 10px rgba(0,0,0,.05); overflow: hidden;
}
.tasks-card-head {
    padding: 1.1rem 1.5rem; border-bottom: 1px solid #f1f5f9;
    display: flex; align-items: center; justify-content: space-between;
}
.tasks-card-head h5 { margin: 0; font-size: .95rem; font-weight: 700; color: #1e293b; }

/* Rows */
.task-row-wrap { padding: .6rem 1.2rem; }
.task-row-item {
    display: flex; align-items: center; gap: .9rem;
    padding: .75rem 1rem; border-radius: 12px; margin-bottom: .4rem;
    border: 1px solid #f1f5f9; background: #fafbfc;
    transition: all .2s; text-decoration: none; color: inherit;
}
.task-row-item:hover { border-color: var(--dept-color); background: var(--dept-bg); transform: translateX(2px); }
.task-icon {
    width: 36px; height: 36px; border-radius: 9px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center; font-size: .85rem;
    background: var(--dept-bg); color: var(--dept-color);
}
.task-title { font-size: .86rem; font-weight: 600; color: #1e293b; }
.task-sub   { font-size: .73rem; color: #9ca3af; margin-top: .1rem; }
.pri-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.pri-low      { background: #22c55e; }
.pri-medium   { background: #f59e0b; }
.pri-normal   { background: #6366f1; }
.pri-high     { background: #f97316; }
.pri-critical, .pri-top { background: #ef4444; }
.task-badge {
    margin-left: auto; flex-shrink: 0;
    border-radius: 20px; padding: .2rem .65rem; font-size: .7rem; font-weight: 700;
}
.badge-pending     { background: #fef3c7; color: #92400e; }
.badge-in_progress { background: #dbeafe; color: #1e40af; }
.badge-completed   { background: #d1fae5; color: #065f46; }
.badge-failed      { background: #fee2e2; color: #991b1b; }

.task-date {
    font-size: .73rem; color: #94a3b8; flex-shrink: 0;
    background: #f8fafc; border-radius: 6px; padding: .15rem .5rem;
}
.task-date.overdue { background: #fef2f2; color: #ef4444; font-weight: 600; }

/* Empty state */
.empty-state { padding: 3.5rem; text-align: center; color: #9ca3af; }
.empty-state i { font-size: 2.5rem; display: block; margin-bottom: .75rem; }
.empty-state p { margin: 0; font-size: .9rem; }

/* Action btn */
.btn-dept {
    background: var(--dept-color); color: #fff; border: none; border-radius: 12px;
    padding: .55rem 1.25rem; font-size: .82rem; font-weight: 600;
    text-decoration: none; display: inline-flex; align-items: center; gap: .45rem;
    transition: all .2s; box-shadow: 0 4px 12px color-mix(in srgb, var(--dept-color) 30%, transparent);
}
.btn-dept:hover { color: #fff; opacity: .9; transform: translateY(-1px); }

/* Row action buttons */
.task-actions { display: flex; gap: .4rem; flex-shrink: 0; align-items: center; }
.btn-start-test {
    background: linear-gradient(135deg, #10b981, #059669);
    color: #fff; border: none; border-radius: 8px;
    padding: .3rem .75rem; font-size: .74rem; font-weight: 600;
    text-decoration: none; display: inline-flex; align-items: center; gap: .3rem;
    white-space: nowrap; transition: all .2s;
}
.btn-start-test:hover { color: #fff; opacity: .88; transform: translateY(-1px); }
.btn-assign {
    background: linear-gradient(135deg, #6366f1, #4f46e5);
    color: #fff; border: none; border-radius: 8px;
    padding: .3rem .75rem; font-size: .74rem; font-weight: 600;
    display: inline-flex; align-items: center; gap: .3rem;
    white-space: nowrap; cursor: pointer; transition: all .2s;
}
.btn-assign:hover { color: #fff; opacity: .88; transform: translateY(-1px); }

/* Assign modal */
.assign-modal-overlay {
    display: none; position: fixed; inset: 0; z-index: 9999;
    background: rgba(0,0,0,.45); align-items: center; justify-content: center;
}
.assign-modal-overlay.active { display: flex; }
.assign-modal {
    background: #fff; border-radius: 18px; width: 500px; max-width: 96vw;
    box-shadow: 0 20px 60px rgba(0,0,0,.2); padding: 1.75rem;
    max-height: 90vh; overflow-y: auto;
}
.assign-modal h5 { margin: 0 0 1rem; font-size: 1rem; font-weight: 700; color: #1e293b; }
.assign-modal select, .assign-modal textarea {
    width: 100%; border: 1.5px solid #e2e8f0; border-radius: 10px;
    padding: .55rem .85rem; font-size: .85rem; color: #1e293b;
    background: #f8fafc; margin-bottom: .9rem; outline: none;
}
.assign-modal select:focus, .assign-modal textarea:focus { border-color: #6366f1; background: #fff; }
.assign-modal-footer { display: flex; gap: .6rem; justify-content: flex-end; margin-top: .5rem; }
.btn-modal-cancel {
    background: #f1f5f9; color: #64748b; border: none; border-radius: 8px;
    padding: .5rem 1.1rem; font-size: .83rem; font-weight: 600; cursor: pointer;
}
.btn-modal-confirm {
    background: linear-gradient(135deg, #6366f1, #4f46e5);
    color: #fff; border: none; border-radius: 8px;
    padding: .5rem 1.25rem; font-size: .83rem; font-weight: 600; cursor: pointer;
}
</style>
@endpush

@section('content')
<div class="dept-page">
<div class="container-fluid" style="max-width: 1200px;">

    {{-- Hero --}}
    <div class="dept-hero">
        <a href="{{ route('dept.dashboard', $code) }}" class="back-btn">
            <i class="fas fa-arrow-left me-1"></i> Dashboard
        </a>
        <div style="display:flex; align-items:center; gap:.9rem; margin-bottom:.3rem;">
            <div style="width:42px; height:42px; border-radius:12px; background:rgba(255,255,255,.15);
                        display:flex; align-items:center; justify-content:center; font-size:1.2rem; color:#fff; flex-shrink:0;">
                <i class="fas {{ $config['icon'] }}"></i>
            </div>
            <div>
                <h1>{{ $config['label'] }} — Tasks</h1>
                <p>{{ $tasks->total() }} task{{ $tasks->total() !== 1 ? 's' : '' }} found</p>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('dept.tasks', $code) }}">
            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <option value="">All Statuses</option>
                    <option value="pending"     {{ request('status') === 'pending'     ? 'selected' : '' }}>Pending</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed"   {{ request('status') === 'completed'   ? 'selected' : '' }}>Completed</option>
                    <option value="failed"      {{ request('status') === 'failed'      ? 'selected' : '' }}>Failed</option>
                </select>
            </div>
            <div class="form-group">
                <label>Priority</label>
                <select name="priority">
                    <option value="">All Priorities</option>
                    <option value="low"      {{ request('priority') === 'low'      ? 'selected' : '' }}>Low</option>
                    <option value="medium"   {{ request('priority') === 'medium'   ? 'selected' : '' }}>Medium</option>
                    <option value="normal"   {{ request('priority') === 'normal'   ? 'selected' : '' }}>Normal</option>
                    <option value="high"     {{ request('priority') === 'high'     ? 'selected' : '' }}>High</option>
                    <option value="critical" {{ request('priority') === 'critical' ? 'selected' : '' }}>Critical</option>
                </select>
            </div>
            <div class="form-group" style="flex:1; min-width:200px;">
                <label>Search</label>
                <input type="text" name="search" placeholder="Well name / number…" value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn-filter" style="margin-top:auto;">
                <i class="fas fa-search"></i> Filter
            </button>
            @if(request()->hasAny(['status','priority','search']))
            <a href="{{ route('dept.tasks', $code) }}" class="btn-reset" style="margin-top:auto;">
                <i class="fas fa-times"></i> Clear
            </a>
            @endif
        </form>
    </div>

    {{-- Tasks Table --}}
    <div class="tasks-card">
        <div class="tasks-card-head">
            <h5><i class="fas fa-list-check me-2" style="color: var(--dept-color);"></i>Task List</h5>
            <a href="{{ route('tasks.create') }}" class="btn-dept" style="font-size:.78rem; padding:.4rem 1rem;">
                <i class="fas fa-plus"></i> New Task
            </a>
        </div>

        <div class="task-row-wrap">
            @forelse($tasks as $task)
            @php
                $isOverdue = $task->completion_status !== 'completed'
                             && $task->scheduled_date
                             && \Carbon\Carbon::parse($task->scheduled_date)->isPast();
                $taskLink = $task->is_new_task
                    ? route('tasks.show', $task->id)
                    : route('dept.complete', $task->id);

                // Show "Start Test" when: new task, ASSIGNED_TO_DEPARTMENT or IN_PROGRESS, operator role
                $showStartTest = $task->is_new_task
                    && in_array($task->task_state ?? '', ['assigned_to_department', 'in_progress'])
                    && $task->completion_status !== 'completed'
                    && Auth::user()->hasAnyRole(['operator', 'mpfm_operator', 'department_supervisor', 'admin']);

                // Show "Assign" when: surveillance_supervisor or dept_supervisor, task not yet completed
                $showAssign = $task->is_new_task
                    && in_array($task->task_state ?? '', ['assigned_to_department', 'in_progress'])
                    && $task->completion_status !== 'completed'
                    && Auth::user()->hasAnyRole(['surveillance_supervisor', 'department_supervisor', 'admin']);
            @endphp
            <div class="task-row-item" style="cursor:default;">
                {{-- Clickable area --}}
                <a href="{{ $taskLink }}" style="display:flex; align-items:center; gap:.9rem; flex:1; min-width:0; text-decoration:none; color:inherit;">
                    <div class="task-icon"><i class="fas {{ $config['icon'] }}"></i></div>
                    <div class="pri-dot pri-{{ $task->priority ?? 'medium' }}"></div>
                    <div style="flex:1; min-width:0;">
                        <div class="task-title text-truncate">
                            {{ $task->well_full_name ?? ('Task #' . $task->id) }}
                            @if($task->is_new_task)
                            <span style="background:#eef2ff;color:#6366f1;border-radius:6px;padding:.1rem .45rem;font-size:.65rem;font-weight:700;margin-left:.35rem;">NEW</span>
                            @endif
                        </div>
                        <div class="task-sub">
                            {{ $task->towerModel->name ?? $task->tower ?? '—' }}
                            @if($task->oilString)· {{ $task->oilString->name }}@endif
                            @if($task->assignedOperator)
                                · <span style="color:#6366f1;"><i class="fas fa-user" style="font-size:.65rem;"></i> {{ $task->assignedOperator->name }}</span>
                            @else
                                <span style="color:#f59e0b;"> · <i class="fas fa-exclamation-circle" style="font-size:.65rem;"></i> Unassigned</span>
                            @endif
                        </div>
                    </div>
                    @if($task->scheduled_date)
                    <div class="task-date {{ $isOverdue ? 'overdue' : '' }}">
                        <i class="fas fa-calendar fa-xs me-1"></i>
                        {{ \Carbon\Carbon::parse($task->scheduled_date)->format('d M Y') }}
                        @if($isOverdue)<br><span style="font-size:.65rem;">Overdue</span>@endif
                    </div>
                    @endif
                    <span class="task-badge badge-{{ $task->completion_status ?? 'pending' }}">
                        {{ ucfirst(str_replace('_', ' ', $task->completion_status ?? 'pending')) }}
                    </span>
                </a>

                {{-- Action Buttons --}}
                <div class="task-actions">
                    @if($showStartTest)
                    <a href="{{ route('upload.form') }}?task_id={{ $task->id }}"
                       class="btn-start-test"
                       title="Start Test — Upload MPFM log">
                        <i class="fas fa-play"></i> Start Test
                    </a>
                    @endif

                    @if($showAssign)
                    <button type="button" class="btn-assign"
                            onclick="openAssignModal({{ $task->id }}, '{{ addslashes($task->well_full_name ?? 'Task #'.$task->id) }}', {{ $task->assigned_to_user_id ?? 'null' }})"
                            title="Assign operators">
                        <i class="fas fa-user-plus"></i> Assign
                    </button>
                    @endif
                </div>
            </div>
            @empty
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <p>No tasks found for this department.</p>
                @if(request()->hasAny(['status','priority','search']))
                <a href="{{ route('dept.tasks', $code) }}" style="color: var(--dept-color); font-size:.85rem; margin-top:.5rem; display:inline-block;">Clear filters</a>
                @endif
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($tasks->hasPages())
        <div style="padding: .75rem 1.5rem; border-top: 1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center;">
            <div style="font-size:.8rem; color:#94a3b8;">
                Showing {{ $tasks->firstItem() }}–{{ $tasks->lastItem() }} of {{ $tasks->total() }}
            </div>
            <div>{{ $tasks->links() }}</div>
        </div>
        @endif
    </div>

</div>
</div>

{{-- ===== Assign Operators Modal ===== --}}
<div class="assign-modal-overlay" id="assignModalOverlay">
    <div class="assign-modal" style="width:500px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
            <h5 style="margin:0;"><i class="fas fa-user-plus" style="color:#6366f1; margin-right:.4rem;"></i>Assign Operators</h5>
            <button type="button" onclick="closeAssignModal()" style="background:none;border:none;font-size:1.2rem;color:#94a3b8;cursor:pointer;">✕</button>
        </div>
        <p style="font-size:.83rem; color:#64748b; margin-bottom:1.25rem; padding:.6rem .9rem; background:#f8fafc; border-radius:8px;" id="assignModalTaskName"></p>

        <form id="assignForm" method="POST">
            @csrf
            <input type="hidden" name="primary_operator_id" id="primaryOperatorId">

            {{-- Operator list with checkboxes + primary radio --}}
            <label style="font-size:.75rem; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:.4px; display:block; margin-bottom:.6rem;">
                Select Operators <span style="color:#ef4444;">*</span>
                <span style="font-weight:400; text-transform:none; font-size:.7rem; margin-left:.3rem;">(choose one or more)</span>
            </label>

            <div id="operatorList" style="border:1.5px solid #e2e8f0; border-radius:10px; overflow:hidden; margin-bottom:1rem; max-height:260px; overflow-y:auto;">
                @foreach($deptOperators as $op)
                <div class="op-row" data-id="{{ $op->id }}" style="display:flex; align-items:center; gap:.75rem; padding:.6rem 1rem; border-bottom:1px solid #f1f5f9; cursor:pointer; transition:background .15s;"
                     onmouseenter="this.style.background='#f8fafc'" onmouseleave="this.style.background=''">
                    {{-- Checkbox: selected --}}
                    <input type="checkbox" name="operator_ids[]" value="{{ $op->id }}"
                           id="op_chk_{{ $op->id }}"
                           onchange="onOperatorCheck(this)"
                           style="width:16px; height:16px; cursor:pointer; accent-color:#6366f1;">
                    {{-- Name --}}
                    <label for="op_chk_{{ $op->id }}" style="flex:1; margin:0; font-size:.87rem; font-weight:500; color:#1e293b; cursor:pointer;">
                        {{ $op->name }}
                    </label>
                    {{-- Primary radio (hidden until checked) --}}
                    <label id="primary_label_{{ $op->id }}" style="display:none; align-items:center; gap:.3rem; font-size:.75rem; color:#6366f1; font-weight:600; cursor:pointer; white-space:nowrap;">
                        <input type="radio" name="_primary_radio" value="{{ $op->id }}"
                               id="op_pri_{{ $op->id }}"
                               onchange="document.getElementById('primaryOperatorId').value = this.value"
                               style="accent-color:#6366f1; cursor:pointer;">
                        Primary
                    </label>
                </div>
                @endforeach
            </div>

            {{-- Validation hint --}}
            <div id="assignValidationMsg" style="display:none; color:#ef4444; font-size:.78rem; margin-bottom:.75rem;">
                <i class="fas fa-exclamation-circle"></i> Select at least one operator and designate a primary.
            </div>

            <label style="font-size:.75rem; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:.4px; display:block; margin-bottom:.3rem;">
                Notes (optional)
            </label>
            <textarea name="notes" rows="2" style="width:100%;border:1.5px solid #e2e8f0;border-radius:10px;padding:.55rem .85rem;font-size:.85rem;background:#f8fafc;outline:none;margin-bottom:1rem;" placeholder="Instructions for the operators..."></textarea>

            <div class="assign-modal-footer">
                <button type="button" class="btn-modal-cancel" onclick="closeAssignModal()">Cancel</button>
                <button type="button" class="btn-modal-confirm" onclick="submitAssignForm()">
                    <i class="fas fa-user-check"></i> Assign &amp; Send to Department
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ── Open modal ──
function openAssignModal(taskId, taskName, currentPrimaryId) {
    document.getElementById('assignModalTaskName').textContent = taskName;
    document.getElementById('assignForm').action = '/tasks/' + taskId + '/assign';

    // Reset checkboxes, radios, primary
    document.querySelectorAll('#operatorList input[type=checkbox]').forEach(cb => {
        cb.checked = false;
        cb.closest('.op-row').querySelector('[id^="primary_label_"]').style.display = 'none';
    });
    document.querySelectorAll('#operatorList input[type=radio]').forEach(r => r.checked = false);
    document.getElementById('primaryOperatorId').value = '';
    document.getElementById('assignValidationMsg').style.display = 'none';

    // Pre-select current primary if exists
    if (currentPrimaryId) {
        const chk = document.getElementById('op_chk_' + currentPrimaryId);
        if (chk) {
            chk.checked = true;
            onOperatorCheck(chk);
            const radio = document.getElementById('op_pri_' + currentPrimaryId);
            if (radio) {
                radio.checked = true;
                document.getElementById('primaryOperatorId').value = currentPrimaryId;
            }
        }
    }

    document.getElementById('assignModalOverlay').classList.add('active');
}

// ── When a checkbox is toggled ──
function onOperatorCheck(checkbox) {
    const opId = checkbox.value;
    const label = document.getElementById('primary_label_' + opId);
    const radio  = document.getElementById('op_pri_' + opId);

    if (checkbox.checked) {
        label.style.display = 'flex';
        // If this is the only checked operator, auto-select as primary
        const checkedCount = document.querySelectorAll('#operatorList input[type=checkbox]:checked').length;
        if (checkedCount === 1) {
            radio.checked = true;
            document.getElementById('primaryOperatorId').value = opId;
        }
    } else {
        label.style.display = 'none';
        radio.checked = false;
        // If this was the primary, clear
        if (document.getElementById('primaryOperatorId').value == opId) {
            document.getElementById('primaryOperatorId').value = '';
            // Auto-assign primary to the first remaining checked operator
            const remaining = document.querySelector('#operatorList input[type=checkbox]:checked');
            if (remaining) {
                const remRadio = document.getElementById('op_pri_' + remaining.value);
                remRadio.checked = true;
                document.getElementById('primaryOperatorId').value = remaining.value;
            }
        }
    }
}

// ── Validate & submit ──
function submitAssignForm() {
    const checkedOps = document.querySelectorAll('#operatorList input[type=checkbox]:checked');
    const primaryId  = document.getElementById('primaryOperatorId').value;

    if (checkedOps.length === 0 || !primaryId) {
        document.getElementById('assignValidationMsg').style.display = 'block';
        return;
    }
    document.getElementById('assignValidationMsg').style.display = 'none';
    document.getElementById('assignForm').submit();
}

// ── Close ──
function closeAssignModal() {
    document.getElementById('assignModalOverlay').classList.remove('active');
}

document.getElementById('assignModalOverlay').addEventListener('click', function(e) {
    if (e.target === this) closeAssignModal();
});
</script>
@endpush
