@extends('layouts.master')

@push('styles')
<style>
:root { --dept-color: {{ $config['color'] }}; --dept-bg: {{ $config['bg'] ?? '#f8fafc' }}; }

.comp-page { background: #f0f4f8; min-height: 100vh; padding: 1.5rem 0 3rem; }

.comp-hero {
    background: linear-gradient(135deg, color-mix(in srgb, var(--dept-color) 85%, #000), var(--dept-color));
    border-radius: 20px; padding: 1.75rem 2rem; margin-bottom: 2rem;
    position: relative; overflow: hidden;
    box-shadow: 0 8px 30px color-mix(in srgb, var(--dept-color) 25%, transparent);
}
.comp-hero::before {
    content: ''; position: absolute; top: -50%; right: -5%;
    width: 250px; height: 250px; background: rgba(255,255,255,.06); border-radius: 50%;
}
.comp-hero h1 { color: #fff; font-size: 1.5rem; font-weight: 700; margin: .5rem 0 .3rem; }
.comp-hero p  { color: rgba(255,255,255,.72); margin: 0; font-size: .88rem; }
.comp-hero .back-btn {
    display: inline-flex; align-items: center; gap: .4rem;
    background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.22);
    color: rgba(255,255,255,.9); border-radius: 8px; padding: .35rem .9rem;
    font-size: .78rem; font-weight: 500; text-decoration: none; margin-bottom: .6rem;
}

/* Task info strip */
.task-strip {
    background: #fff; border-radius: 16px; border: 1px solid #e8edf2;
    box-shadow: 0 2px 10px rgba(0,0,0,.05); padding: 1.25rem 1.5rem;
    display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 1rem; margin-bottom: 1.5rem;
}
.ts-item .ts-lbl { font-size: .68rem; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: .4px; margin-bottom: .2rem; }
.ts-item .ts-val { font-size: .88rem; font-weight: 600; color: #1e293b; }

/* Form card */
.comp-card {
    background: #fff; border-radius: 18px; border: 1px solid #e8edf2;
    box-shadow: 0 2px 12px rgba(0,0,0,.06); overflow: hidden; margin-bottom: 1.5rem;
}
.comp-card-head {
    padding: 1.1rem 1.5rem; border-bottom: 1px solid #f1f5f9;
    display: flex; align-items: center; gap: .75rem;
}
.comp-card-head .ch-icon {
    width: 36px; height: 36px; border-radius: 10px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center; font-size: .85rem;
    background: var(--dept-bg); color: var(--dept-color);
}
.comp-card-head h5 { margin: 0; font-size: .93rem; font-weight: 700; color: #1e293b; }
.comp-card-head p  { margin: 0; font-size: .75rem; color: #94a3b8; }
.comp-card-body    { padding: 1.5rem; }

/* Fields grid */
.fields-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 1.1rem;
}
.field-group { display: flex; flex-direction: column; }
.field-label {
    font-size: .75rem; font-weight: 700; color: #374151;
    text-transform: uppercase; letter-spacing: .4px; margin-bottom: .45rem;
    display: flex; align-items: center; gap: .35rem;
}
.field-label .req { color: #ef4444; font-size: .65rem; }

.modern-input, .modern-select, .modern-textarea {
    border: 1.5px solid #e2e8f0; border-radius: 11px;
    padding: .65rem .95rem; font-size: .88rem; color: #1e293b;
    background: #fff; outline: none; width: 100%; transition: all .2s;
}
.modern-input:focus, .modern-select:focus, .modern-textarea:focus {
    border-color: var(--dept-color);
    box-shadow: 0 0 0 3px color-mix(in srgb, var(--dept-color) 12%, transparent);
}
.modern-select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: right .85rem center;
    padding-right: 2.5rem; cursor: pointer;
}
.modern-textarea { resize: vertical; min-height: 85px; }
.field-span2 { grid-column: span 2; }

/* Footer */
.comp-footer {
    background: #fff; border-radius: 16px; border: 1px solid #e8edf2;
    box-shadow: 0 2px 10px rgba(0,0,0,.05); padding: 1.25rem 1.5rem;
    display: flex; align-items: center; justify-content: space-between; gap: 1rem;
}
.btn-complete {
    background: var(--dept-color); color: #fff; border: none;
    border-radius: 12px; padding: .75rem 2rem; font-size: .9rem; font-weight: 700;
    cursor: pointer; transition: all .22s; display: flex; align-items: center; gap: .5rem;
    box-shadow: 0 4px 14px color-mix(in srgb, var(--dept-color) 35%, transparent);
}
.btn-complete:hover { opacity:.9; transform: translateY(-1px); }
.btn-back {
    background: #f8fafc; color: #64748b; border: 1.5px solid #e2e8f0;
    border-radius: 12px; padding: .7rem 1.5rem; font-size: .88rem; font-weight: 500;
    text-decoration: none; transition: all .2s; display: flex; align-items: center; gap: .4rem;
}
.btn-back:hover { background: #f1f5f9; color: #374151; }
</style>
@endpush

@section('content')
<div class="comp-page">
<div class="container-lg" style="max-width: 860px;">

    {{-- Hero --}}
    <div class="comp-hero">
        <a href="{{ url()->previous() }}" class="back-btn"><i class="fas fa-arrow-left"></i> Back</a>
        <div style="display:flex; align-items:center; gap:.75rem;">
            <div style="width:44px;height:44px;border-radius:12px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:#fff;">
                <i class="fas {{ $config['icon'] }}"></i>
            </div>
            <div>
                <h1>Complete Task — {{ $config['label'] }}</h1>
                <p>Fill in the operation details to mark this task as complete</p>
            </div>
        </div>
    </div>

    {{-- Task Info Strip --}}
    <div class="task-strip">
        <div class="ts-item">
            <div class="ts-lbl">Well</div>
            <div class="ts-val">{{ $planTask->well_full_name ?? '—' }}</div>
        </div>
        <div class="ts-item">
            <div class="ts-lbl">Tower</div>
            <div class="ts-val">{{ $planTask->towerModel->name ?? $planTask->tower ?? '—' }}</div>
        </div>
        <div class="ts-item">
            <div class="ts-lbl">Oil String</div>
            <div class="ts-val">{{ $planTask->oilString->name ?? '—' }}</div>
        </div>
        <div class="ts-item">
            <div class="ts-lbl">Scheduled</div>
            <div class="ts-val">{{ $planTask->scheduled_date ? \Carbon\Carbon::parse($planTask->scheduled_date)->format('d M Y') : '—' }}</div>
        </div>
        <div class="ts-item">
            <div class="ts-lbl">Priority</div>
            <div class="ts-val" style="text-transform:capitalize;">{{ $planTask->priority ?? '—' }}</div>
        </div>
        <div class="ts-item">
            <div class="ts-lbl">Monthly Plan</div>
            <div class="ts-val">{{ $planTask->monthlyPlan->plan_name ?? '—' }}</div>
        </div>
    </div>

    {{-- Errors --}}
    @if($errors->any())
    <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:14px;padding:1rem 1.25rem;margin-bottom:1.5rem;display:flex;gap:.75rem;align-items:flex-start;">
        <i class="fas fa-exclamation-circle" style="color:#ef4444;margin-top:.1rem;"></i>
        <div>
            <strong style="color:#dc2626;font-size:.88rem;">Please fix the errors below:</strong>
            <ul style="margin:.4rem 0 0;padding-left:1.2rem;">
                @foreach($errors->all() as $err)<li style="font-size:.8rem;color:#b91c1c;">{{ $err }}</li>@endforeach
            </ul>
        </div>
    </div>
    @endif

    <form action="{{ route('dept.complete.store', $planTask) }}" method="POST" id="completeForm">
        @csrf

        {{-- Department-Specific Fields --}}
        @if(!empty($completionFields))
        <div class="comp-card">
            <div class="comp-card-head">
                <div class="ch-icon"><i class="fas {{ $config['icon'] }}"></i></div>
                <div>
                    <h5>{{ $config['label'] }} — Operation Details</h5>
                    <p>Required information for this department's task completion</p>
                </div>
            </div>
            <div class="comp-card-body">
                <div class="fields-grid">
                    @foreach($completionFields as $field)
                    @php
                        $val = old('data.' . $field['key'], $prefill[$field['key']] ?? '');
                        $isWide = in_array($field['type'], ['textarea']);
                    @endphp
                    <div class="field-group {{ $isWide ? 'field-span2' : '' }}">
                        <div class="field-label">
                            {{ $field['label'] }}
                            @if($field['required'])<span class="req">*</span>@endif
                        </div>

                        @if($field['type'] === 'select')
                            <select name="data[{{ $field['key'] }}]"
                                    class="modern-select"
                                    {{ $field['required'] ? 'required' : '' }}>
                                <option value="">— Select —</option>
                                @foreach($field['options'] as $opt)
                                <option value="{{ $opt }}" {{ $val === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                @endforeach
                            </select>

                        @elseif($field['type'] === 'textarea')
                            <textarea name="data[{{ $field['key'] }}]"
                                      class="modern-textarea"
                                      placeholder="{{ $field['placeholder'] ?? '' }}"
                                      {{ $field['required'] ? 'required' : '' }}>{{ $val }}</textarea>

                        @elseif($field['type'] === 'date')
                            <input type="date" name="data[{{ $field['key'] }}]"
                                   class="modern-input" value="{{ $val }}"
                                   {{ $field['required'] ? 'required' : '' }}>

                        @else
                            <input type="{{ $field['type'] }}"
                                   name="data[{{ $field['key'] }}]"
                                   class="modern-input"
                                   value="{{ $val }}"
                                   placeholder="{{ $field['placeholder'] ?? '' }}"
                                   {{ $field['required'] ? 'required' : '' }}
                                   {{ $field['type'] === 'number' ? 'step=any min=0' : '' }}>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Notes --}}
        <div class="comp-card">
            <div class="comp-card-head">
                <div class="ch-icon" style="background:#f8fafc;color:#64748b;">
                    <i class="fas fa-note-sticky"></i>
                </div>
                <div>
                    <h5>Completion Notes</h5>
                    <p>Optional — any additional remarks or observations</p>
                </div>
            </div>
            <div class="comp-card-body">
                <textarea name="completion_notes" class="modern-textarea" rows="3"
                          placeholder="Add any additional notes, observations, or remarks about this task completion...">{{ old('completion_notes') }}</textarea>
            </div>
        </div>

        {{-- Footer --}}
        <div class="comp-footer">
            <a href="{{ url()->previous() }}" class="btn-back">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" class="btn-complete" id="submitBtn">
                <i class="fas fa-circle-check"></i> Complete Task
            </button>
        </div>

    </form>
</div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('completeForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
    btn.disabled = true;
});
</script>
@endpush
