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
    border-radius: 20px;
    padding: 2rem 2.5rem;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
    background: linear-gradient(135deg, color-mix(in srgb, var(--dept-color) 85%, #000), var(--dept-color));
    box-shadow: 0 10px 40px color-mix(in srgb, var(--dept-color) 30%, transparent);
}
.dept-hero::before {
    content: ''; position: absolute; top: -40%; right: -5%;
    width: 300px; height: 300px;
    background: rgba(255,255,255,.06); border-radius: 50%;
}
.dept-hero .hero-icon {
    width: 54px; height: 54px; border-radius: 16px;
    background: rgba(255,255,255,.15); border: 1px solid rgba(255,255,255,.2);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem; color: #fff; margin-bottom: .9rem;
}
.dept-hero h1 { color: #fff; font-size: 1.7rem; font-weight: 700; margin: 0 0 .3rem; }
.dept-hero p  { color: rgba(255,255,255,.72); margin: 0; font-size: .9rem; }
.dept-hero .back-btn {
    position: absolute; top: 1.5rem; right: 1.5rem;
    background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.22);
    color: #fff; border-radius: 10px; padding: .45rem 1.1rem;
    font-size: .82rem; font-weight: 500; text-decoration: none; transition: all .2s;
}
.dept-hero .back-btn:hover { background: rgba(255,255,255,.22); color: #fff; }

/* Stat cards */
.stat-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(170px, 1fr)); gap: 1rem; margin-bottom: 1.75rem; }
.stat-card {
    background: #fff; border-radius: 16px; padding: 1.25rem;
    border: 1px solid #e8edf2; box-shadow: 0 2px 10px rgba(0,0,0,.05);
    display: flex; flex-direction: column; gap: .5rem;
}
.stat-card .sc-icon {
    width: 38px; height: 38px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center; font-size: .9rem;
}
.stat-card .sc-val { font-size: 2rem; font-weight: 800; line-height: 1; }
.stat-card .sc-lbl { font-size: .73rem; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: .4px; }
.sc-blue   .sc-icon { background: #eff6ff; color: #2563eb; } .sc-blue   .sc-val { color: #2563eb; }
.sc-amber  .sc-icon { background: #fffbeb; color: #d97706; } .sc-amber  .sc-val { color: #d97706; }
.sc-orange .sc-icon { background: #fff7ed; color: #ea580c; } .sc-orange .sc-val { color: #ea580c; }
.sc-green  .sc-icon { background: #f0fdf4; color: #16a34a; } .sc-green  .sc-val { color: #16a34a; }
.sc-red    .sc-icon { background: #fef2f2; color: #ef4444; } .sc-red    .sc-val { color: #ef4444; }
.sc-indigo .sc-icon { background: #eef2ff; color: #6366f1; } .sc-indigo .sc-val { color: #6366f1; }

/* Cards */
.dash-card {
    background: #fff; border-radius: 18px;
    border: 1px solid #e8edf2; box-shadow: 0 2px 10px rgba(0,0,0,.05);
    overflow: hidden; margin-bottom: 1.5rem;
}
.dash-card-head {
    padding: 1.1rem 1.5rem; border-bottom: 1px solid #f1f5f9;
    display: flex; align-items: center; justify-content: space-between;
}
.dash-card-head h5 { margin: 0; font-size: .95rem; font-weight: 700; color: #1e293b; }
.dash-card-head a  { font-size: .78rem; color: var(--dept-color); font-weight: 600; text-decoration: none; }
.dash-card-body    { padding: 1.25rem 1.5rem; }

/* Task list */
.task-row {
    display: flex; align-items: center; gap: 1rem;
    padding: .8rem 1rem; border-radius: 12px; margin-bottom: .5rem;
    border: 1px solid #f1f5f9; background: #fafbfc;
    transition: all .2s; text-decoration: none; color: inherit;
}
.task-row:hover { border-color: var(--dept-color); background: var(--dept-bg); box-shadow: 0 2px 10px rgba(0,0,0,.06); }
.task-row .tr-icon {
    width: 36px; height: 36px; border-radius: 9px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center; font-size: .85rem;
}
.task-row .tr-title { font-size: .85rem; font-weight: 600; color: #1e293b; }
.task-row .tr-sub   { font-size: .73rem; color: #9ca3af; }
.task-row .tr-badge {
    margin-left: auto; flex-shrink: 0;
    border-radius: 20px; padding: .2rem .65rem; font-size: .7rem; font-weight: 700;
}
.badge-pending     { background: #fef3c7; color: #92400e; }
.badge-in_progress { background: #dbeafe; color: #1e40af; }
.badge-completed   { background: #d1fae5; color: #065f46; }
.badge-failed      { background: #fee2e2; color: #991b1b; }

/* Priority dots */
.pri-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.pri-low      { background: #22c55e; }
.pri-medium   { background: #f59e0b; }
.pri-high     { background: #f97316; }
.pri-critical, .pri-top { background: #ef4444; }
.pri-normal   { background: #6366f1; }

/* Completion ring */
.completion-ring {
    width: 100px; height: 100px; position: relative;
    display: flex; align-items: center; justify-content: center; margin: auto;
}
.completion-ring svg { transform: rotate(-90deg); }
.ring-track { fill: none; stroke: #e2e8f0; stroke-width: 8; }
.ring-fill  { fill: none; stroke: var(--dept-color); stroke-width: 8;
              stroke-linecap: round; transition: stroke-dashoffset .8s ease; }
.ring-label { position: absolute; font-size: 1.2rem; font-weight: 800; color: #1e293b; }

/* Trend chart */
.trend-bars { display: flex; align-items: flex-end; gap: .5rem; height: 80px; }
.trend-bar-wrap { flex: 1; display: flex; flex-direction: column; align-items: center; gap: .3rem; }
.trend-bar {
    width: 100%; border-radius: 6px 6px 0 0;
    background: linear-gradient(180deg, var(--dept-color), color-mix(in srgb, var(--dept-color) 60%, transparent));
    min-height: 4px; transition: height .5s ease;
}
.trend-lbl { font-size: .65rem; color: #9ca3af; text-align: center; }
.trend-val  { font-size: .7rem; font-weight: 700; color: #374151; }

/* Priority bar */
.pri-bar { display: flex; gap: .5rem; margin-top: .5rem; }
.pri-seg { height: 6px; border-radius: 100px; min-width: 4px; }

/* Action button */
.btn-dept {
    background: var(--dept-color); color: #fff; border: none; border-radius: 12px;
    padding: .65rem 1.5rem; font-size: .85rem; font-weight: 600;
    text-decoration: none; display: inline-flex; align-items: center; gap: .5rem;
    transition: all .2s; box-shadow: 0 4px 12px color-mix(in srgb, var(--dept-color) 30%, transparent);
}
.btn-dept:hover { color: #fff; opacity: .9; transform: translateY(-1px); }
</style>
@endpush

@section('content')
<div class="dept-page">
<div class="container-fluid" style="max-width: 1200px;">

    {{-- Hero --}}
    <div class="dept-hero">
        <a href="{{ url()->previous() }}" class="back-btn"><i class="fas fa-arrow-left me-1"></i> Back</a>
        <div class="hero-icon"><i class="fas {{ $config['icon'] }}"></i></div>
        <h1>{{ $config['label'] }}</h1>
        <p>{{ $config['description'] }}</p>
    </div>

    {{-- Stats --}}
    <div class="stat-grid">
        <div class="stat-card sc-blue">
            <div class="sc-icon"><i class="fas fa-list-check"></i></div>
            <div class="sc-val">{{ $stats['total'] }}</div>
            <div class="sc-lbl">Total Tasks</div>
        </div>
        <div class="stat-card sc-amber">
            <div class="sc-icon"><i class="fas fa-clock"></i></div>
            <div class="sc-val">{{ $stats['pending'] }}</div>
            <div class="sc-lbl">Pending</div>
        </div>
        <div class="stat-card sc-orange">
            <div class="sc-icon"><i class="fas fa-spinner"></i></div>
            <div class="sc-val">{{ $stats['in_progress'] }}</div>
            <div class="sc-lbl">In Progress</div>
        </div>
        <div class="stat-card sc-green">
            <div class="sc-icon"><i class="fas fa-circle-check"></i></div>
            <div class="sc-val">{{ $stats['completed'] }}</div>
            <div class="sc-lbl">Completed</div>
        </div>
        <div class="stat-card sc-red">
            <div class="sc-icon"><i class="fas fa-triangle-exclamation"></i></div>
            <div class="sc-val">{{ $stats['overdue'] }}</div>
            <div class="sc-lbl">Overdue</div>
        </div>
        <div class="stat-card sc-indigo">
            <div class="sc-icon"><i class="fas fa-percent"></i></div>
            <div class="sc-val">{{ $stats['completion_rate'] }}%</div>
            <div class="sc-lbl">Completion Rate</div>
        </div>
    </div>

    <div class="row g-4">

        {{-- LEFT: Recent Tasks --}}
        <div class="col-lg-8">
            <div class="dash-card">
                <div class="dash-card-head">
                    <h5><i class="fas fa-bolt me-2" style="color: var(--dept-color);"></i>Recent Tasks</h5>
                    <a href="{{ route('dept.tasks', $code) }}">View All <i class="fas fa-arrow-right fa-xs"></i></a>
                </div>
                <div class="dash-card-body">
                    @forelse($recentTasks as $task)
                    @php
                        $taskLink = ($task->is_new_task ?? false)
                            ? route('tasks.show', $task->id)
                            : route('dept.complete', $task->id);
                    @endphp
                    <a href="{{ $taskLink }}" class="task-row">
                        <div class="tr-icon" style="background: {{ $config['bg'] ?? '#f8fafc' }}; color: {{ $config['color'] }};">
                            <i class="fas {{ $config['icon'] }}"></i>
                        </div>
                        <div class="pri-dot pri-{{ $task->priority ?? 'medium' }}"></div>
                        <div>
                            <div class="tr-title">{{ $task->well_full_name ?? ('Task #' . $task->id) }}</div>
                            <div class="tr-sub">
                                {{ $task->towerModel->name ?? $task->tower ?? '—' }}
                                @if($task->scheduled_date)
                                    · {{ \Carbon\Carbon::parse($task->scheduled_date)->format('d M Y') }}
                                @endif
                            </div>
                        </div>
                        <span class="tr-badge badge-{{ $task->completion_status ?? 'pending' }}">
                            {{ ucfirst(str_replace('_', ' ', $task->completion_status ?? 'pending')) }}
                        </span>
                    </a>
                    @empty
                    <div style="text-align:center; padding: 2.5rem; color: #9ca3af;">
                        <i class="fas fa-inbox fa-2x mb-2" style="display:block;"></i>
                        No tasks for this department yet.
                        <div style="margin-top:.75rem;">
                            <a href="{{ route('tasks.create') }}" class="btn-dept" style="font-size:.8rem; padding:.5rem 1.2rem;">
                                <i class="fas fa-plus"></i> Create Task
                            </a>
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- RIGHT: Charts & Info --}}
        <div class="col-lg-4">

            {{-- Completion Ring --}}
            <div class="dash-card" style="margin-bottom:1rem;">
                <div class="dash-card-head"><h5>Completion Rate</h5></div>
                <div class="dash-card-body" style="text-align:center; padding: 1.5rem;">
                    @php
                        $r = 42; $c = 2 * pi() * $r;
                        $offset = $c - ($stats['completion_rate'] / 100) * $c;
                    @endphp
                    <div class="completion-ring">
                        <svg width="100" height="100" viewBox="0 0 100 100">
                            <circle class="ring-track" cx="50" cy="50" r="{{ $r }}" />
                            <circle class="ring-fill" cx="50" cy="50" r="{{ $r }}"
                                stroke-dasharray="{{ $c }}"
                                stroke-dashoffset="{{ $offset }}" />
                        </svg>
                        <span class="ring-label">{{ $stats['completion_rate'] }}%</span>
                    </div>
                    <div style="margin-top:.75rem; font-size:.82rem; color:#64748b;">
                        {{ $stats['completed'] }} of {{ $stats['total'] }} tasks completed
                    </div>
                </div>
            </div>

            {{-- Monthly Trend --}}
            <div class="dash-card" style="margin-bottom:1rem;">
                <div class="dash-card-head"><h5>6-Month Trend</h5></div>
                <div class="dash-card-body">
                    @php $maxTrend = max(array_column($trend, 'count') ?: [1]); @endphp
                    <div class="trend-bars">
                        @foreach($trend as $t)
                        @php $h = $maxTrend > 0 ? max(4, round(($t['count'] / $maxTrend) * 70)) : 4; @endphp
                        <div class="trend-bar-wrap">
                            <div class="trend-val">{{ $t['count'] }}</div>
                            <div class="trend-bar" style="height: {{ $h }}px;"></div>
                            <div class="trend-lbl">{{ \Carbon\Carbon::parse('01 ' . $t['month'])->format('M') }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Priority Breakdown --}}
            <div class="dash-card">
                <div class="dash-card-head"><h5>By Priority</h5></div>
                <div class="dash-card-body">
                    @php
                        $priColors = ['low' => '#22c55e', 'medium' => '#f59e0b', 'normal' => '#6366f1', 'high' => '#f97316', 'critical' => '#ef4444', 'top' => '#dc2626'];
                        $total = array_sum($byPriority) ?: 1;
                    @endphp
                    @foreach($byPriority as $pri => $cnt)
                    <div style="display:flex; align-items:center; gap:.6rem; margin-bottom:.6rem;">
                        <div class="pri-dot pri-{{ $pri }}"></div>
                        <div style="flex:1; font-size:.8rem; font-weight:500; color:#374151; text-transform:capitalize;">{{ $pri }}</div>
                        <div style="font-size:.8rem; font-weight:700; color:#1e293b;">{{ $cnt }}</div>
                    </div>
                    @endforeach
                    @php $totalP = array_sum($byPriority) ?: 1; @endphp
                    <div class="pri-bar mt-2">
                        @foreach($byPriority as $pri => $cnt)
                        <div class="pri-seg" style="flex: {{ $cnt }}; background: {{ $priColors[$pri] ?? '#94a3b8' }};"></div>
                        @endforeach
                    </div>

                    <div style="margin-top:1.25rem; padding-top:1rem; border-top:1px solid #f1f5f9;">
                        <a href="{{ route('dept.tasks', $code) }}" class="btn-dept" style="width:100%; justify-content:center;">
                            <i class="fas fa-list"></i> View All Tasks
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
</div>
@endsection
