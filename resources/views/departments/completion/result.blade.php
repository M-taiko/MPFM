@extends('layouts.master')

@push('styles')
<style>
:root { --dept-color: {{ $config['color'] ?? '#059669' }}; --dept-bg: {{ $config['bg'] ?? '#f0fdf4' }}; }
.result-page { background: #f0f4f8; min-height: 100vh; padding: 1.5rem 0 3rem; }

.success-banner {
    background: linear-gradient(135deg, #065f46, #059669);
    border-radius: 20px; padding: 2rem 2.5rem; margin-bottom: 2rem;
    position: relative; overflow: hidden;
    box-shadow: 0 8px 30px rgba(5,150,105,.25);
    display: flex; align-items: center; gap: 1.5rem;
}
.success-banner::before {
    content: ''; position: absolute; top: -40%; right: -5%;
    width: 250px; height: 250px; background: rgba(255,255,255,.06); border-radius: 50%;
}
.success-banner .sb-icon {
    width: 64px; height: 64px; border-radius: 50%;
    background: rgba(255,255,255,.15); border: 2px solid rgba(255,255,255,.25);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.6rem; color: #fff; flex-shrink: 0;
}
.success-banner h1 { color: #fff; font-size: 1.5rem; font-weight: 700; margin: 0 0 .3rem; }
.success-banner p  { color: rgba(255,255,255,.72); margin: 0; font-size: .88rem; }

.res-card {
    background: #fff; border-radius: 18px; border: 1px solid #e8edf2;
    box-shadow: 0 2px 10px rgba(0,0,0,.05); overflow: hidden; margin-bottom: 1.5rem;
}
.res-card-head {
    padding: 1.1rem 1.5rem; border-bottom: 1px solid #f1f5f9;
    display: flex; align-items: center; gap: .75rem;
}
.res-card-head .ch-icon {
    width: 36px; height: 36px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center; font-size: .85rem;
    background: var(--dept-bg); color: var(--dept-color);
}
.res-card-head h5 { margin: 0; font-size: .95rem; font-weight: 700; color: #1e293b; }
.res-card-body    { padding: 1.5rem; }

.data-grid {
    display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 1rem;
}
.data-item {
    background: #f8fafc; border-radius: 12px; padding: .9rem 1rem;
    border: 1px solid #f1f5f9;
}
.data-item .di-lbl {
    font-size: .68rem; font-weight: 700; color: #9ca3af;
    text-transform: uppercase; letter-spacing: .4px; margin-bottom: .3rem;
}
.data-item .di-val { font-size: .9rem; font-weight: 600; color: #1e293b; }

.action-row { display: flex; gap: .75rem; flex-wrap: wrap; }
.btn-action {
    border-radius: 12px; padding: .7rem 1.5rem; font-size: .85rem; font-weight: 600;
    text-decoration: none; display: inline-flex; align-items: center; gap: .45rem;
    transition: all .2s;
}
.btn-primary-action {
    background: var(--dept-color); color: #fff; border: none;
    box-shadow: 0 4px 12px color-mix(in srgb, var(--dept-color) 30%, transparent);
}
.btn-primary-action:hover { color: #fff; opacity: .9; transform: translateY(-1px); }
.btn-secondary-action { background: #f8fafc; color: #64748b; border: 1.5px solid #e2e8f0; }
.btn-secondary-action:hover { background: #f1f5f9; color: #374151; }
</style>
@endpush

@section('content')
<div class="result-page">
<div class="container-lg" style="max-width: 820px;">

    @if(session('success'))
    {{-- Success Banner --}}
    <div class="success-banner">
        <div class="sb-icon"><i class="fas fa-circle-check"></i></div>
        <div>
            <h1>Task Completed!</h1>
            <p>{{ session('success') }} · {{ now()->format('d M Y H:i') }}</p>
        </div>
    </div>
    @endif

    {{-- Task Summary --}}
    <div class="res-card">
        <div class="res-card-head">
            <div class="ch-icon"><i class="fas fa-info-circle"></i></div>
            <h5>Task Information</h5>
        </div>
        <div class="res-card-body">
            <div class="data-grid">
                <div class="data-item">
                    <div class="di-lbl">Well</div>
                    <div class="di-val">{{ $planTask->well_full_name ?? '—' }}</div>
                </div>
                <div class="data-item">
                    <div class="di-lbl">Tower</div>
                    <div class="di-val">{{ $planTask->towerModel->name ?? $planTask->tower ?? '—' }}</div>
                </div>
                <div class="data-item">
                    <div class="di-lbl">Monthly Plan</div>
                    <div class="di-val">{{ $planTask->monthlyPlan->plan_name ?? '—' }}</div>
                </div>
                <div class="data-item">
                    <div class="di-lbl">Department</div>
                    <div class="di-val">{{ $config['label'] ?? $code }}</div>
                </div>
                <div class="data-item">
                    <div class="di-lbl">Completed At</div>
                    <div class="di-val">{{ $planTask->completed_at ? \Carbon\Carbon::parse($planTask->completed_at)->format('d M Y H:i') : now()->format('d M Y H:i') }}</div>
                </div>
                <div class="data-item">
                    <div class="di-lbl">Status</div>
                    <div class="di-val" style="color:#059669;"><i class="fas fa-circle-check me-1"></i> Completed</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Completion Data --}}
    @if($taskData && !empty($taskData->data) && !empty($completionFields))
    <div class="res-card">
        <div class="res-card-head">
            <div class="ch-icon"><i class="fas {{ $config['icon'] ?? 'fa-building' }}"></i></div>
            <h5>{{ $config['label'] ?? '' }} — Recorded Data</h5>
        </div>
        <div class="res-card-body">
            <div class="data-grid">
                @foreach($completionFields as $field)
                @php $val = $taskData->data[$field['key']] ?? null; @endphp
                @if($val !== null && $val !== '')
                <div class="data-item {{ in_array($field['type'], ['textarea']) ? 'col-span-2' : '' }}"
                     style="{{ in_array($field['type'], ['textarea']) ? 'grid-column:span 2;' : '' }}">
                    <div class="di-lbl">{{ $field['label'] }}</div>
                    <div class="di-val">{{ $val }}</div>
                </div>
                @endif
                @endforeach
            </div>

            @if($taskData->completion_notes)
            <div style="margin-top:1rem; padding:1rem; background:#f8fafc; border-radius:12px; border:1px solid #f1f5f9;">
                <div style="font-size:.7rem;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.4px;margin-bottom:.4rem;">Notes</div>
                <div style="font-size:.88rem;color:#374151;">{{ $taskData->completion_notes }}</div>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Actions --}}
    <div class="res-card">
        <div class="res-card-body">
            <div class="action-row">
                <a href="{{ route('dept.dashboard', $code) }}" class="btn-action btn-primary-action">
                    <i class="fas fa-gauge-high"></i> Go to {{ $config['label'] ?? '' }} Dashboard
                </a>
                <a href="{{ route('operator.dashboard') }}" class="btn-action btn-secondary-action">
                    <i class="fas fa-home"></i> My Dashboard
                </a>
                <a href="{{ route('tasks.index') }}" class="btn-action btn-secondary-action">
                    <i class="fas fa-list"></i> All Tasks
                </a>
            </div>
        </div>
    </div>

</div>
</div>
@endsection
