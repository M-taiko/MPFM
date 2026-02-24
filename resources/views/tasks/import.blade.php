@extends('layouts.master')

@push('styles')
<style>
/* ===========================
   IMPORT PAGE – MODERN UI
   =========================== */

.import-page {
    background: #f0f4f8;
    min-height: 100vh;
    padding: 2rem 0 4rem;
}

/* ─── Hero ─── */
.import-hero {
    background: linear-gradient(135deg, #065f46 0%, #047857 45%, #059669 100%);
    border-radius: 20px;
    padding: 2rem 2.5rem;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(5, 150, 105, 0.28);
}
.import-hero::before {
    content: '';
    position: absolute;
    top: -40%; right: -5%;
    width: 300px; height: 300px;
    background: rgba(255,255,255,0.06);
    border-radius: 50%;
}
.import-hero::after {
    content: '';
    position: absolute;
    bottom: -50%; right: 18%;
    width: 180px; height: 180px;
    background: rgba(255,255,255,0.04);
    border-radius: 50%;
}
.import-hero .hero-badge {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    background: rgba(255,255,255,.15);
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 100px;
    padding: .3rem .9rem;
    font-size: .75rem;
    font-weight: 600;
    color: rgba(255,255,255,.9);
    margin-bottom: .85rem;
    backdrop-filter: blur(8px);
}
.import-hero h1 {
    color: #fff;
    font-size: 1.7rem;
    font-weight: 700;
    margin: 0 0 .35rem;
    letter-spacing: -.3px;
}
.import-hero p {
    color: rgba(255,255,255,.72);
    margin: 0;
    font-size: .93rem;
}
.import-hero .back-btn {
    position: absolute;
    top: 1.5rem; right: 1.5rem;
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.22);
    color: #fff;
    border-radius: 10px;
    padding: .45rem 1.1rem;
    font-size: .82rem;
    font-weight: 500;
    text-decoration: none;
    transition: all .2s;
    backdrop-filter: blur(10px);
}
.import-hero .back-btn:hover {
    background: rgba(255,255,255,.22);
    color: #fff;
}

/* ─── How-it-works bar ─── */
.how-bar {
    display: flex;
    gap: 0;
    background: #fff;
    border-radius: 16px;
    padding: .45rem;
    margin-bottom: 1.75rem;
    box-shadow: 0 2px 12px rgba(0,0,0,.06);
    border: 1px solid #e8edf2;
}
.how-step {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: .35rem;
    padding: .85rem .5rem;
    border-radius: 12px;
    position: relative;
}
.how-step:not(:last-child)::after {
    content: '';
    position: absolute;
    right: 0; top: 50%;
    transform: translateY(-50%);
    width: 1px; height: 50%;
    background: #e2e8f0;
}
.how-step .step-num {
    width: 28px; height: 28px;
    border-radius: 50%;
    background: linear-gradient(135deg, #059669, #047857);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .73rem;
    font-weight: 700;
    box-shadow: 0 3px 8px rgba(5,150,105,.3);
}
.how-step .step-label {
    font-size: .75rem;
    font-weight: 600;
    color: #374151;
    text-align: center;
}
.how-step .step-sub {
    font-size: .68rem;
    color: #9ca3af;
    text-align: center;
}

/* ─── Cards ─── */
.imp-card {
    background: #fff;
    border-radius: 20px;
    border: 1px solid #e8edf2;
    box-shadow: 0 2px 12px rgba(0,0,0,.06);
    overflow: hidden;
    margin-bottom: 1.5rem;
    transition: box-shadow .2s;
}
.imp-card:hover { box-shadow: 0 4px 24px rgba(0,0,0,.09); }

.card-head {
    padding: 1.2rem 1.75rem;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    gap: .8rem;
}
.card-head .ch-icon {
    width: 38px; height: 38px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .88rem;
    flex-shrink: 0;
}
.ch-green  { background: #ecfdf5; color: #059669; }
.ch-blue   { background: #eff6ff; color: #2563eb; }
.ch-orange { background: #fff7ed; color: #ea580c; }

.card-head h5 {
    margin: 0;
    font-size: .97rem;
    font-weight: 700;
    color: #1e293b;
}
.card-head p {
    margin: 0;
    font-size: .77rem;
    color: #94a3b8;
}
.card-body-pad { padding: 1.75rem; }

/* ─── Template download tile ─── */
.template-tile {
    display: flex;
    align-items: center;
    gap: 1.25rem;
    padding: 1.25rem;
    border: 1.5px dashed #a7f3d0;
    border-radius: 16px;
    background: linear-gradient(135deg, #f0fdf4, #ecfdf5);
    text-decoration: none;
    transition: all .22s;
}
.template-tile:hover {
    border-color: #059669;
    background: #d1fae5;
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(5,150,105,.12);
}
.template-tile .tpl-icon {
    width: 52px; height: 52px;
    border-radius: 14px;
    background: #fff;
    box-shadow: 0 3px 10px rgba(5,150,105,.15);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
    color: #059669;
    flex-shrink: 0;
}
.template-tile .tpl-body strong {
    display: block;
    font-size: .9rem;
    font-weight: 700;
    color: #065f46;
    margin-bottom: .15rem;
}
.template-tile .tpl-body span {
    font-size: .78rem;
    color: #6ee7b7;
    font-weight: 500;
}
.template-tile .tpl-badge {
    margin-left: auto;
    background: #059669;
    color: #fff;
    border-radius: 8px;
    padding: .35rem .8rem;
    font-size: .75rem;
    font-weight: 600;
    white-space: nowrap;
    display: flex;
    align-items: center;
    gap: .35rem;
    flex-shrink: 0;
}

/* Column info pills */
.col-info-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: .5rem;
    margin-top: 1rem;
}
.col-pill {
    display: flex;
    align-items: center;
    gap: .4rem;
    padding: .45rem .75rem;
    border-radius: 8px;
    font-size: .77rem;
    font-weight: 500;
    border: 1px solid;
}
.col-pill.required { background: #fef2f2; border-color: #fecaca; color: #dc2626; }
.col-pill.optional  { background: #f8fafc; border-color: #e2e8f0; color: #64748b; }
.col-pill i { font-size: .65rem; }

/* ─── Drop Zone ─── */
.drop-zone {
    border: 2px dashed #cbd5e1;
    border-radius: 18px;
    padding: 3rem 2rem;
    text-align: center;
    background: #fafbfc;
    cursor: pointer;
    transition: all .25s;
    position: relative;
    overflow: hidden;
}
.drop-zone.dragover {
    border-color: #059669;
    background: #f0fdf4;
    box-shadow: 0 0 0 4px rgba(5,150,105,.1);
}
.drop-zone.has-file {
    border-color: #059669;
    background: linear-gradient(135deg, #f0fdf4, #ecfdf5);
    border-style: solid;
}
.drop-zone input[type="file"] {
    position: absolute;
    inset: 0;
    opacity: 0;
    cursor: pointer;
    width: 100%;
    height: 100%;
}
.drop-zone .dz-icon {
    width: 70px; height: 70px;
    border-radius: 50%;
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    margin: 0 auto 1.2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    color: #059669;
    transition: all .25s;
}
.drop-zone.dragover .dz-icon {
    transform: scale(1.1);
    background: linear-gradient(135deg, #a7f3d0, #6ee7b7);
}
.drop-zone .dz-title {
    font-size: 1rem;
    font-weight: 700;
    color: #374151;
    margin-bottom: .4rem;
}
.drop-zone .dz-sub {
    font-size: .83rem;
    color: #9ca3af;
}
.drop-zone .dz-accepted {
    display: inline-flex;
    gap: .4rem;
    margin-top: .75rem;
}
.dz-accepted span {
    background: #e2e8f0;
    border-radius: 6px;
    padding: .2rem .55rem;
    font-size: .72rem;
    font-weight: 600;
    color: #475569;
}

/* File preview strip */
.file-preview {
    display: none;
    align-items: center;
    gap: 1rem;
    padding: .85rem 1.25rem;
    background: #fff;
    border: 1.5px solid #a7f3d0;
    border-radius: 14px;
    margin-top: 1rem;
}
.file-preview.show { display: flex; }
.file-preview .fp-icon {
    width: 44px; height: 44px;
    border-radius: 10px;
    background: linear-gradient(135deg, #059669, #047857);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
}
.file-preview .fp-info strong {
    display: block;
    font-size: .87rem;
    font-weight: 700;
    color: #065f46;
}
.file-preview .fp-info span {
    font-size: .75rem;
    color: #6ee7b7;
    font-weight: 500;
}
.file-preview .fp-remove {
    margin-left: auto;
    width: 30px; height: 30px;
    border-radius: 50%;
    background: #fee2e2;
    color: #ef4444;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .8rem;
    transition: all .2s;
}
.file-preview .fp-remove:hover { background: #ef4444; color: #fff; }

/* ─── Submit button ─── */
.btn-import {
    background: linear-gradient(135deg, #059669, #047857);
    color: #fff;
    border: none;
    border-radius: 14px;
    padding: .9rem 2.5rem;
    font-size: .95rem;
    font-weight: 700;
    cursor: pointer;
    transition: all .22s;
    display: flex;
    align-items: center;
    gap: .6rem;
    box-shadow: 0 6px 20px rgba(5,150,105,.3);
    justify-content: center;
    width: 100%;
}
.btn-import:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 24px rgba(5,150,105,.4);
}
.btn-import:disabled {
    opacity: .6;
    cursor: not-allowed;
    transform: none;
}

/* ─── Progress bar ─── */
.upload-progress {
    display: none;
    margin-top: 1rem;
}
.upload-progress.show { display: block; }
.progress-track {
    height: 6px;
    background: #e2e8f0;
    border-radius: 100px;
    overflow: hidden;
}
.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #059669, #10b981);
    border-radius: 100px;
    width: 0%;
    transition: width .4s ease;
    animation: shimmer 1.5s infinite;
}
@keyframes shimmer {
    0% { background-position: -200% center; }
    100% { background-position: 200% center; }
}
.progress-label {
    font-size: .78rem;
    color: #059669;
    font-weight: 600;
    margin-top: .4rem;
    text-align: center;
}

/* ─── Result Banner ─── */
.result-banner {
    border-radius: 16px;
    padding: 1.25rem 1.5rem;
    margin-bottom: 1.5rem;
    display: flex;
    gap: 1rem;
    align-items: flex-start;
}
.result-banner.success {
    background: linear-gradient(135deg, #f0fdf4, #ecfdf5);
    border: 1px solid #a7f3d0;
}
.result-banner.partial {
    background: linear-gradient(135deg, #fffbeb, #fef3c7);
    border: 1px solid #fcd34d;
}
.result-banner.error {
    background: #fef2f2;
    border: 1px solid #fecaca;
}
.rb-icon {
    width: 42px; height: 42px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
}
.success .rb-icon { background: #059669; color: #fff; }
.partial .rb-icon { background: #d97706; color: #fff; }
.error .rb-icon   { background: #ef4444; color: #fff; }

.rb-content strong {
    display: block;
    font-size: .95rem;
    font-weight: 700;
    margin-bottom: .2rem;
}
.success .rb-content strong { color: #065f46; }
.partial .rb-content strong { color: #92400e; }
.error .rb-content strong   { color: #991b1b; }
.rb-content p {
    margin: 0;
    font-size: .82rem;
}
.success .rb-content p { color: #047857; }
.partial .rb-content p { color: #b45309; }
.error .rb-content p   { color: #b91c1c; }

/* Stats row */
.stats-row {
    display: flex;
    gap: .75rem;
    margin-bottom: 1.5rem;
}
.stat-box {
    flex: 1;
    background: #fff;
    border-radius: 14px;
    padding: 1.1rem;
    border: 1px solid #e8edf2;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0,0,0,.04);
}
.stat-box .stat-val {
    font-size: 2rem;
    font-weight: 800;
    line-height: 1;
    margin-bottom: .3rem;
}
.stat-box .stat-lbl {
    font-size: .75rem;
    font-weight: 600;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: .4px;
}
.stat-box.green .stat-val { color: #059669; }
.stat-box.red   .stat-val { color: #ef4444; }
.stat-box.blue  .stat-val { color: #2563eb; }

/* Error table */
.error-table-wrap {
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #fecaca;
}
.error-table {
    width: 100%;
    border-collapse: collapse;
    font-size: .82rem;
}
.error-table thead th {
    background: #fef2f2;
    color: #991b1b;
    font-weight: 700;
    padding: .7rem 1rem;
    text-align: left;
    border-bottom: 1px solid #fecaca;
    font-size: .75rem;
    text-transform: uppercase;
    letter-spacing: .4px;
}
.error-table tbody tr {
    border-bottom: 1px solid #fef2f2;
}
.error-table tbody tr:last-child { border-bottom: none; }
.error-table tbody td {
    padding: .65rem 1rem;
    vertical-align: top;
    color: #374151;
}
.error-table .row-num {
    background: #fee2e2;
    color: #dc2626;
    font-weight: 700;
    border-radius: 6px;
    padding: .15rem .5rem;
    font-size: .75rem;
    display: inline-block;
}
.error-list {
    list-style: none;
    padding: 0; margin: 0;
}
.error-list li {
    display: flex;
    align-items: flex-start;
    gap: .35rem;
    color: #b91c1c;
    font-size: .8rem;
    margin-bottom: .2rem;
}
.error-list li:last-child { margin-bottom: 0; }
.error-list li::before {
    content: '•';
    color: #ef4444;
    font-weight: 900;
    flex-shrink: 0;
    margin-top: .05rem;
}

/* Success tasks list */
.success-list {
    max-height: 250px;
    overflow-y: auto;
    border-radius: 12px;
    border: 1px solid #a7f3d0;
    background: #f0fdf4;
}
.success-list table {
    width: 100%;
    border-collapse: collapse;
    font-size: .82rem;
}
.success-list table thead th {
    background: #d1fae5;
    color: #065f46;
    font-weight: 700;
    padding: .6rem 1rem;
    text-align: left;
    border-bottom: 1px solid #a7f3d0;
    font-size: .73rem;
    text-transform: uppercase;
    letter-spacing: .4px;
    position: sticky;
    top: 0;
}
.success-list table tbody td {
    padding: .5rem 1rem;
    color: #374151;
    border-bottom: 1px solid #d1fae5;
}
.success-list table tbody tr:last-child td { border-bottom: none; }

/* Responsive */
@media (max-width: 768px) {
    .how-bar { display: none; }
    .stats-row { flex-wrap: wrap; }
    .import-hero .back-btn { position: static; margin-bottom: .5rem; display: inline-flex; }
    .template-tile { flex-wrap: wrap; }
}
</style>
@endpush

@section('content')
<div class="import-page">
<div class="container-lg" style="max-width: 100%;">

    {{-- Hero --}}
    <div class="import-hero">
        <a href="{{ route('tasks.index') }}" class="back-btn">
            <i class="fas fa-arrow-left me-1"></i> Back to Tasks
        </a>
        <div class="hero-badge">
            <i class="fas fa-file-excel"></i> Bulk Import
        </div>
        <h1>Import Tasks from Excel</h1>
        <p>Upload an Excel sheet to create multiple tasks at once across all departments — for a full plan or entire month</p>
    </div>

    {{-- How it works bar --}}
    <div class="how-bar">
        <div class="how-step">
            <div class="step-num">1</div>
            <div class="step-label">Download Template</div>
            <div class="step-sub">Get the pre-built .xlsx file</div>
        </div>
        <div class="how-step">
            <div class="step-num">2</div>
            <div class="step-label">Fill in Tasks</div>
            <div class="step-sub">Add tasks row by row</div>
        </div>
        <div class="how-step">
            <div class="step-num">3</div>
            <div class="step-label">Upload File</div>
            <div class="step-sub">Drag & drop or browse</div>
        </div>
        <div class="how-step">
            <div class="step-num">4</div>
            <div class="step-label">Review Results</div>
            <div class="step-sub">See successes & errors</div>
        </div>
    </div>

    {{-- ===== Import Result ===== --}}
    @if(session('import_success'))
        @php
            $successCount = session('import_success_count', 0);
            $failCount    = session('import_fail_count', 0);
            $importErrors = session('import_errors', []);
            $importedRows = session('import_imported', []);
            $bannerClass  = $failCount === 0 ? 'success' : ($successCount > 0 ? 'partial' : 'error');
        @endphp

        {{-- Result banner --}}
        <div class="result-banner {{ $bannerClass }}">
            <div class="rb-icon">
                <i class="fas fa-{{ $failCount === 0 ? 'check' : ($successCount > 0 ? 'exclamation-triangle' : 'times') }}"></i>
            </div>
            <div class="rb-content">
                <strong>
                    @if($failCount === 0)
                        Import Completed Successfully!
                    @elseif($successCount > 0)
                        Import Completed with Errors
                    @else
                        Import Failed
                    @endif
                </strong>
                <p>
                    @if($successCount > 0 && $failCount === 0)
                        All {{ $successCount }} tasks were imported and saved as Draft.
                    @elseif($successCount > 0 && $failCount > 0)
                        {{ $successCount }} tasks imported successfully. {{ $failCount }} rows had errors and were skipped.
                    @else
                        No tasks were imported. Please fix the errors and try again.
                    @endif
                </p>
            </div>
        </div>

        {{-- Stats --}}
        <div class="stats-row">
            <div class="stat-box blue">
                <div class="stat-val">{{ $successCount + $failCount }}</div>
                <div class="stat-lbl">Total Rows</div>
            </div>
            <div class="stat-box green">
                <div class="stat-val">{{ $successCount }}</div>
                <div class="stat-lbl">Imported</div>
            </div>
            <div class="stat-box red">
                <div class="stat-val">{{ $failCount }}</div>
                <div class="stat-lbl">Errors</div>
            </div>
        </div>

        {{-- Error Details --}}
        @if(!empty($importErrors))
        <div class="imp-card">
            <div class="card-head">
                <div class="ch-icon ch-orange"><i class="fas fa-exclamation-triangle"></i></div>
                <div>
                    <h5>Error Details</h5>
                    <p>{{ count($importErrors) }} row(s) could not be imported — fix and re-upload</p>
                </div>
            </div>
            <div class="card-body-pad" style="padding: 0;">
                <div class="error-table-wrap">
                    <table class="error-table">
                        <thead>
                            <tr>
                                <th style="width: 70px;">Row</th>
                                <th>Title</th>
                                <th>Errors</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($importErrors as $err)
                            <tr>
                                <td><span class="row-num">Row {{ $err['row'] }}</span></td>
                                <td style="max-width: 200px; word-break: break-word;">
                                    {{ $err['data']['title'] ?? '—' }}
                                </td>
                                <td>
                                    <ul class="error-list">
                                        @foreach($err['errors'] as $e)
                                            <li>{{ $e }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        {{-- Imported tasks list --}}
        @if(!empty($importedRows))
        <div class="imp-card">
            <div class="card-head">
                <div class="ch-icon ch-green"><i class="fas fa-check-circle"></i></div>
                <div>
                    <h5>Successfully Imported Tasks</h5>
                    <p>{{ count($importedRows) }} tasks created — all in Draft state</p>
                </div>
            </div>
            <div class="card-body-pad" style="padding: 0;">
                <div class="success-list">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Priority</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($importedRows as $i => $task)
                            <tr>
                                <td style="color: #9ca3af; font-size: .75rem;">{{ $i + 1 }}</td>
                                <td><strong>{{ $task->title }}</strong></td>
                                <td>
                                    <span style="background: #eff6ff; color: #2563eb; border-radius: 6px; padding: .15rem .5rem; font-size: .72rem; font-weight: 600;">
                                        {{ ucwords(str_replace('_', ' ', $task->task_type)) }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $pc = ['low' => '#22c55e', 'medium' => '#f59e0b', 'high' => '#f97316', 'critical' => '#ef4444'];
                                        $col = $pc[$task->priority] ?? '#94a3b8';
                                    @endphp
                                    <span style="background: {{ $col }}22; color: {{ $col }}; border-radius: 6px; padding: .15rem .5rem; font-size: .72rem; font-weight: 600;">
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('tasks.show', $task) }}"
                                       style="font-size: .75rem; color: #2563eb; font-weight: 600; text-decoration: none;">
                                        View <i class="fas fa-arrow-right fa-xs"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    @endif

    {{-- Import error (file level) --}}
    @if(session('import_error'))
    <div class="result-banner error" style="margin-bottom: 1.5rem;">
        <div class="rb-icon"><i class="fas fa-times"></i></div>
        <div class="rb-content">
            <strong>Upload Error</strong>
            <p>{{ session('import_error') }}</p>
        </div>
    </div>
    @endif

    {{-- Validation errors --}}
    @if($errors->any())
    <div class="result-banner error" style="margin-bottom: 1.5rem;">
        <div class="rb-icon"><i class="fas fa-exclamation-circle"></i></div>
        <div class="rb-content">
            <strong>Validation Error</strong>
            @foreach($errors->all() as $err)
                <p>{{ $err }}</p>
            @endforeach
        </div>
    </div>
    @endif

    <div class="row g-4">

        {{-- LEFT COLUMN: Template + Upload --}}
        <div class="col-lg-7">

            {{-- Step 1: Download Template --}}
            <div class="imp-card">
                <div class="card-head">
                    <div class="ch-icon ch-green"><i class="fas fa-cloud-download-alt"></i></div>
                    <div>
                        <h5>Step 1 — Download Template</h5>
                        <p>Pre-filled with reference data from the system</p>
                    </div>
                </div>
                <div class="card-body-pad">
                    <a href="{{ route('tasks.import.template') }}" class="template-tile">
                        <div class="tpl-icon">
                            <i class="fas fa-file-excel"></i>
                        </div>
                        <div class="tpl-body">
                            <strong>tasks_import_template.xlsx</strong>
                            <span>Excel 2007+ format (.xlsx)</span>
                        </div>
                        <div class="tpl-badge">
                            <i class="fas fa-download"></i> Download
                        </div>
                    </a>

                    <div style="margin-top: 1rem; padding: .9rem 1rem; background: #fffbeb; border: 1px solid #fde68a; border-radius: 12px; font-size: .8rem; color: #92400e; display: flex; gap: .6rem; align-items: flex-start;">
                        <i class="fas fa-lightbulb" style="color: #d97706; margin-top: .1rem; flex-shrink: 0;"></i>
                        <span>The template includes a <strong>Reference Data</strong> sheet with all valid department names, monthly plans, task types, and priority values from the system.</span>
                    </div>
                </div>
            </div>

            {{-- Step 2: Upload --}}
            <div class="imp-card">
                <div class="card-head">
                    <div class="ch-icon ch-blue"><i class="fas fa-cloud-upload-alt"></i></div>
                    <div>
                        <h5>Step 2 — Upload Your File</h5>
                        <p>Drag & drop or click to browse — max 5MB</p>
                    </div>
                </div>
                <div class="card-body-pad">
                    <form action="{{ route('tasks.import.store') }}" method="POST"
                          enctype="multipart/form-data" id="importForm">
                        @csrf

                        {{-- Drop Zone --}}
                        <div class="drop-zone" id="dropZone">
                            <input type="file" name="excel_file" id="excelFile"
                                   accept=".xlsx,.xls,.csv" />
                            <div class="dz-icon">
                                <i class="fas fa-cloud-upload-alt" id="dzIcon"></i>
                            </div>
                            <div class="dz-title" id="dzTitle">Drop your Excel file here</div>
                            <div class="dz-sub" id="dzSub">or click anywhere to browse your computer</div>
                            <div class="dz-accepted">
                                <span>.xlsx</span>
                                <span>.xls</span>
                                <span>.csv</span>
                            </div>
                        </div>

                        {{-- File preview --}}
                        <div class="file-preview" id="filePreview">
                            <div class="fp-icon"><i class="fas fa-file-excel"></i></div>
                            <div class="fp-info">
                                <strong id="fpName">filename.xlsx</strong>
                                <span id="fpSize">0 KB</span>
                            </div>
                            <button type="button" class="fp-remove" id="fpRemove" title="Remove file">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        {{-- Progress --}}
                        <div class="upload-progress" id="uploadProgress">
                            <div class="progress-track">
                                <div class="progress-fill" id="progressFill"></div>
                            </div>
                            <div class="progress-label" id="progressLabel">Uploading & processing...</div>
                        </div>

                        <div style="margin-top: 1.25rem;">
                            <button type="submit" class="btn-import" id="importBtn" disabled>
                                <i class="fas fa-file-import"></i>
                                Import Tasks
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        {{-- RIGHT COLUMN: Column info --}}
        <div class="col-lg-5">

            {{-- Required columns --}}
            <div class="imp-card">
                <div class="card-head">
                    <div class="ch-icon ch-blue"><i class="fas fa-columns"></i></div>
                    <div>
                        <h5>Column Reference</h5>
                        <p>Use these exact column headers in your file</p>
                    </div>
                </div>
                <div class="card-body-pad">
                    <p style="font-size: .78rem; color: #64748b; margin-bottom: .75rem;">
                        <span style="background: #fef2f2; border-radius: 5px; padding: .1rem .45rem; border: 1px solid #fecaca; color: #dc2626; font-weight: 700; font-size: .7rem;">*</span>
                        Required columns
                    </p>

                    <div class="col-info-list">
                        <div class="col-pill required"><i class="fas fa-asterisk"></i> title</div>
                        <div class="col-pill required"><i class="fas fa-asterisk"></i> monthly_plan</div>
                        <div class="col-pill required"><i class="fas fa-asterisk"></i> department</div>
                        <div class="col-pill required"><i class="fas fa-asterisk"></i> task_type</div>
                        <div class="col-pill required"><i class="fas fa-asterisk"></i> priority</div>
                        <div class="col-pill optional"><i class="fas fa-minus"></i> description</div>
                        <div class="col-pill optional"><i class="fas fa-minus"></i> notes</div>
                        <div class="col-pill optional"><i class="fas fa-minus"></i> assigned_to</div>
                        <div class="col-pill optional"><i class="fas fa-minus"></i> expected_completion_at</div>
                        <div class="col-pill optional"><i class="fas fa-minus"></i> oil_string</div>
                        <div class="col-pill optional"><i class="fas fa-minus"></i> tower</div>
                    </div>

                    <hr style="border-color: #f1f5f9; margin: 1.2rem 0;">

                    {{-- Valid values --}}
                    <div style="font-size: .77rem;">
                        <p style="font-weight: 700; color: #374151; margin-bottom: .75rem; font-size: .8rem;">
                            <i class="fas fa-info-circle text-primary me-1"></i> Valid Values
                        </p>

                        <div style="margin-bottom: .85rem;">
                            <span style="font-weight: 700; color: #374151; display: block; margin-bottom: .3rem; font-size: .75rem; text-transform: uppercase; letter-spacing: .3px;">Task Type</span>
                            <div style="display: flex; gap: .35rem; flex-wrap: wrap;">
                                @foreach(['plan_task', 'daily_plan', 'operator_plan'] as $t)
                                <code style="background: #f1f5f9; border-radius: 5px; padding: .15rem .45rem; font-size: .72rem; color: #1e293b;">{{ $t }}</code>
                                @endforeach
                            </div>
                        </div>

                        <div style="margin-bottom: .85rem;">
                            <span style="font-weight: 700; color: #374151; display: block; margin-bottom: .3rem; font-size: .75rem; text-transform: uppercase; letter-spacing: .3px;">Priority</span>
                            <div style="display: flex; gap: .35rem; flex-wrap: wrap;">
                                @foreach(['low' => '#22c55e', 'medium' => '#f59e0b', 'high' => '#f97316', 'critical' => '#ef4444'] as $p => $c)
                                <code style="background: {{ $c }}18; border-radius: 5px; padding: .15rem .45rem; font-size: .72rem; color: {{ $c }}; font-weight: 700; border: 1px solid {{ $c }}40;">{{ $p }}</code>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <span style="font-weight: 700; color: #374151; display: block; margin-bottom: .3rem; font-size: .75rem; text-transform: uppercase; letter-spacing: .3px;">Date Format</span>
                            <code style="background: #f1f5f9; border-radius: 5px; padding: .2rem .6rem; font-size: .72rem; color: #1e293b; display: inline-block;">YYYY-MM-DD HH:MM</code>
                            <div style="color: #9ca3af; font-size: .72rem; margin-top: .3rem;">e.g. 2026-03-15 08:00</div>
                        </div>
                    </div>

                    <hr style="border-color: #f1f5f9; margin: 1.2rem 0;">

                    {{-- Tips --}}
                    <div style="font-size: .77rem; color: #64748b;">
                        <p style="font-weight: 700; color: #374151; margin-bottom: .6rem; font-size: .8rem;">
                            <i class="fas fa-magic" style="color: #7c3aed; margin-right: 4px;"></i> Tips
                        </p>
                        <ul style="padding-left: 1rem; margin: 0; line-height: 1.9;">
                            <li>Use exact department & plan names from the Reference sheet</li>
                            <li>All tasks import as <strong>Draft</strong> state</li>
                            <li>Empty rows are automatically skipped</li>
                            <li>Max <strong>1,000 rows</strong> per upload</li>
                            <li>Errors are shown row-by-row after upload</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const dropZone   = document.getElementById('dropZone');
    const fileInput  = document.getElementById('excelFile');
    const filePreview = document.getElementById('filePreview');
    const fpName     = document.getElementById('fpName');
    const fpSize     = document.getElementById('fpSize');
    const fpRemove   = document.getElementById('fpRemove');
    const importBtn  = document.getElementById('importBtn');
    const importForm = document.getElementById('importForm');
    const dzTitle    = document.getElementById('dzTitle');
    const dzSub      = document.getElementById('dzSub');
    const dzIcon     = document.getElementById('dzIcon');
    const progress   = document.getElementById('uploadProgress');
    const progressFill = document.getElementById('progressFill');

    function formatBytes(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    }

    function showFile(file) {
        fpName.textContent = file.name;
        fpSize.textContent = formatBytes(file.size);
        filePreview.classList.add('show');
        dropZone.classList.add('has-file');
        dzTitle.textContent = 'File selected';
        dzSub.textContent   = 'Click "Import Tasks" below to proceed';
        dzIcon.className    = 'fas fa-check-circle';
        dzIcon.style.color  = '#059669';
        importBtn.disabled  = false;
    }

    function clearFile() {
        fileInput.value     = '';
        filePreview.classList.remove('show');
        dropZone.classList.remove('has-file');
        dzTitle.textContent = 'Drop your Excel file here';
        dzSub.textContent   = 'or click anywhere to browse your computer';
        dzIcon.className    = 'fas fa-cloud-upload-alt';
        dzIcon.style.color  = '';
        importBtn.disabled  = true;
    }

    fileInput.addEventListener('change', () => {
        if (fileInput.files[0]) showFile(fileInput.files[0]);
    });

    fpRemove.addEventListener('click', clearFile);

    // Drag events
    ['dragenter', 'dragover'].forEach(evt => {
        dropZone.addEventListener(evt, (e) => {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });
    });
    ['dragleave', 'drop'].forEach(evt => {
        dropZone.addEventListener(evt, () => dropZone.classList.remove('dragover'));
    });
    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        const file = e.dataTransfer?.files[0];
        if (!file) return;
        const allowed = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                          'application/vnd.ms-excel', 'text/csv', 'application/csv'];
        const ext = file.name.split('.').pop().toLowerCase();
        if (!['xlsx','xls','csv'].includes(ext)) {
            alert('Only .xlsx, .xls, or .csv files are supported.');
            return;
        }
        // Create a DataTransfer to assign to input
        const dt = new DataTransfer();
        dt.items.add(file);
        fileInput.files = dt.files;
        showFile(file);
    });

    // Form submit loading state
    importForm.addEventListener('submit', function () {
        importBtn.disabled = true;
        importBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        progress.classList.add('show');
        let pct = 0;
        const interval = setInterval(() => {
            pct = Math.min(pct + Math.random() * 8, 90);
            progressFill.style.width = pct + '%';
        }, 200);
        // Let form submit naturally
    });
});
</script>
@endpush
