<?php

namespace App\Http\Controllers;

use App\Models\OilString;
use App\Models\WirelineGaugeRun;
use App\Models\WirelineOperation;
use App\Services\Wireline\GaugeLogParser;
use App\Services\Wireline\GaugeRunAnalyzer;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use InvalidArgumentException;

class WirelineController extends Controller
{
    public function __construct(
        private GaugeLogParser $parser,
        private GaugeRunAnalyzer $analyzer,
    ) {
    }

    // ── Access ───────────────────────────────────────────────────
    private function canManage(): bool
    {
        $u = auth()->user();
        return $u && ($u->hasAnyRole(['admin', 'department_manager', 'job_officer', 'surveillance_supervisor'])
            || $u->department?->code === 'WIRELINE');
    }

    private function canDelete(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'department_manager', 'job_officer']) ?? false;
    }

    private function requireManage(): void
    {
        abort_unless($this->canManage(), 403, 'You are not allowed to manage Wire Line operations.');
    }

    // ── Dashboard ────────────────────────────────────────────────
    public function dashboard()
    {
        return view('wireline.dashboard', app(\App\Services\Wireline\WirelineOverview::class)->build() + [
            'canManage' => $this->canManage(),
        ]);
    }

    // ── Operations list ──────────────────────────────────────────
    public function index(Request $request)
    {
        $q = WirelineOperation::with(['oilString.tower.oilField', 'gaugeRuns'])
            ->orderByDesc('operation_date')->orderByDesc('id');

        if ($request->filled('well')) {
            $q->where('oil_string_id', $request->integer('well'));
        }
        if ($request->filled('type')) {
            $q->where('operation_type', $request->input('type'));
        }
        if ($request->filled('status')) {
            $q->where('status', $request->input('status'));
        }
        if ($request->filled('from')) {
            $q->whereDate('operation_date', '>=', $request->input('from'));
        }
        if ($request->filled('to')) {
            $q->whereDate('operation_date', '<=', $request->input('to'));
        }

        return view('wireline.index', [
            'operations' => $q->paginate(15)->withQueryString(),
            'wells'      => OilString::orderBy('name')->get(['id', 'name', 'code']),
            'canManage'  => $this->canManage(),
        ]);
    }

    // ── Create / store ───────────────────────────────────────────
    public function create(Request $request)
    {
        $this->requireManage();

        return view('wireline.create', [
            'operation' => null,
            'wells'     => OilString::active()->with('tower.oilField')->orderBy('name')->get(),
            'selectedWell' => $request->integer('well') ?: null,
        ]);
    }

    public function store(Request $request)
    {
        $this->requireManage();
        $data = $this->validateOperation($request);
        $request->validate($this->gaugeRules());

        try {
            $operation = DB::transaction(function () use ($data, $request) {
                $operation = WirelineOperation::create($data + ['created_by' => auth()->id()]);
                if ($request->hasFile('gauge_file')) {
                    $this->storeGaugeRun($operation, $request->file('gauge_file'), $request->input('survey_type', 'bhcip'));
                }
                return $operation;
            });
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['gauge_file' => $e->getMessage()]);
        }

        return redirect()->route('wireline.show', $operation)
            ->with('success', 'Wire Line operation recorded' . ($operation->gaugeRuns()->exists() ? ' and gauge data analysed.' : '.'));
    }

    // ── Show (analysis) ──────────────────────────────────────────
    public function show(Request $request, WirelineOperation $operation)
    {
        $operation->load(['oilString.tower.oilField', 'gaugeRuns', 'creator']);

        $run = $operation->gaugeRuns->firstWhere('id', $request->integer('run')) ?? $operation->gaugeRuns->first();

        $series = null;
        if ($run) {
            $rows = $run->readings()->get(['t_sec', 'pressure', 'temperature']);
            $series = [
                'min'  => $rows->map(fn ($r) => round($r->t_sec / 60, 2))->all(),
                'p'    => $rows->map(fn ($r) => round($r->pressure, 2))->all(),
                't'    => $rows->map(fn ($r) => round($r->temperature, 2))->all(),
            ];
        }

        return view('wireline.show', [
            'operation' => $operation,
            'run'       => $run,
            'series'    => $series,
            'canManage' => $this->canManage(),
            'canDelete' => $this->canDelete(),
        ]);
    }

    // ── Edit / update / delete ───────────────────────────────────
    public function edit(WirelineOperation $operation)
    {
        $this->requireManage();

        return view('wireline.create', [
            'operation' => $operation,
            'wells'     => OilString::with('tower.oilField')->orderBy('name')->get(),
            'selectedWell' => $operation->oil_string_id,
        ]);
    }

    public function update(Request $request, WirelineOperation $operation)
    {
        $this->requireManage();
        $operation->update($this->validateOperation($request));

        // depth_to feeds the RIH/POOH speed estimate - refresh stored analyses
        foreach ($operation->gaugeRuns as $run) {
            $this->reanalyse($run, $operation);
        }

        return redirect()->route('wireline.show', $operation)->with('success', 'Operation updated.');
    }

    public function destroy(WirelineOperation $operation)
    {
        abort_unless($this->canDelete(), 403);

        foreach ($operation->gaugeRuns as $run) {
            $this->deleteRunFile($run);
        }
        $operation->delete();

        return redirect()->route('wireline.operations')->with('success', 'Operation deleted.');
    }

    // ── Gauge runs ───────────────────────────────────────────────
    public function uploadRun(Request $request, WirelineOperation $operation)
    {
        $this->requireManage();
        $request->validate($this->gaugeRules() + ['gauge_file' => 'required|file|max:51200']);

        try {
            $run = DB::transaction(fn () => $this->storeGaugeRun($operation, $request->file('gauge_file'), $request->input('survey_type', 'bhcip')));
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['gauge_file' => $e->getMessage()]);
        }

        return redirect()->route('wireline.show', [$operation, 'run' => $run->id])
            ->with('success', 'Gauge data uploaded and analysed.');
    }

    public function destroyRun(WirelineGaugeRun $run)
    {
        abort_unless($this->canDelete(), 403);
        $operation = $run->operation;
        $this->deleteRunFile($run);
        $run->delete();

        return redirect()->route('wireline.show', $operation)->with('success', 'Gauge run removed.');
    }

    public function reanalyseRun(WirelineGaugeRun $run)
    {
        $this->requireManage();
        $this->reanalyse($run, $run->operation);

        return redirect()->route('wireline.show', [$run->operation, 'run' => $run->id])->with('success', 'Analysis refreshed.');
    }

    public function saveStops(Request $request, WirelineGaugeRun $run)
    {
        $this->requireManage();
        $request->validate(['depths' => 'array', 'depths.*' => 'nullable|numeric|min:0|max:20000']);

        $analysis = $run->analysis ?? [];
        $stops = $analysis['stops'] ?? [];
        foreach ($stops as $i => &$stop) {
            $d = $request->input("depths.$i");
            $stop['depth'] = ($d === null || $d === '') ? null : (float) $d;
        }
        unset($stop);

        $analysis['stops'] = $stops;
        $analysis['gradients'] = $this->analyzer->gradients($stops);
        $run->update(['analysis' => $analysis]);

        return redirect()->route('wireline.show', [$run->operation, 'run' => $run->id, 'tab' => 'gradients'])
            ->with('success', 'Stop depths saved - gradients recalculated.');
    }

    // ── internals ────────────────────────────────────────────────
    private function validateOperation(Request $request): array
    {
        return $request->validate([
            'oil_string_id'  => 'required|exists:oil_strings,id',
            'operation_type' => ['required', Rule::in(array_keys(WirelineOperation::TYPES))],
            'run_number'     => 'required|integer|min:1|max:999',
            'operation_date' => 'required|date',
            'status'         => ['required', Rule::in(array_keys(WirelineOperation::STATUSES))],
            'tool_string'    => 'nullable|string|max:255',
            'depth_from'     => 'nullable|numeric|min:0|max:20000',
            'depth_to'       => array_filter(['nullable', 'numeric', 'min:0', 'max:20000', $request->filled('depth_from') ? 'gte:depth_from' : null]),
            'crew_leader'    => 'nullable|string|max:255',
            'objective'      => 'nullable|string|max:2000',
            'remarks'        => 'nullable|string|max:5000',
        ]);
    }

    private function gaugeRules(): array
    {
        return [
            'gauge_file'  => 'nullable|file|max:51200',
            'survey_type' => ['nullable', Rule::in(array_keys(WirelineGaugeRun::SURVEY_TYPES))],
        ];
    }

    private function storeGaugeRun(WirelineOperation $operation, UploadedFile $file, string $surveyType): WirelineGaugeRun
    {
        $parsed = $this->parser->parse(file_get_contents($file->getRealPath()), $file->getClientOriginalName());
        $meta = $parsed['meta'];
        $readings = $parsed['readings'];

        $analysis = $this->analyzer->analyze($readings, $meta['sample_interval_sec'], $meta['max_range'], $operation->depth_to);

        $stored = $file->storeAs("wireline/{$operation->id}", now()->format('YmdHis') . '_' . preg_replace('/[^A-Za-z0-9._-]/', '_', $file->getClientOriginalName()));
        $last = end($readings);

        $run = $operation->gaugeRuns()->create([
            'survey_type'         => $surveyType,
            'gauge_serial'        => $meta['gauge_serial'],
            'gauge_model'         => $meta['gauge_model'],
            'gauge_manufacturer'  => $meta['gauge_manufacturer'],
            'calibration_date'    => $meta['calibration_date'],
            'max_range'           => $meta['max_range'],
            'pressure_unit'       => $meta['pressure_unit'],
            'temperature_unit'    => $meta['temperature_unit'],
            'sample_interval_sec' => $meta['sample_interval_sec'],
            'started_at'          => $meta['started_at'],
            'ended_at'            => $meta['started_at']?->copy()->addSeconds($last[0]),
            'sample_count'        => count($readings),
            'source_file'         => $file->getClientOriginalName(),
            'file_path'           => $stored,
            'analysis'            => $analysis,
        ]);

        $batch = [];
        foreach ($readings as [$t, $p, $temp, $it]) {
            $batch[] = [
                'wireline_gauge_run_id' => $run->id,
                't_sec' => $t, 'pressure' => $p, 'temperature' => $temp, 'internal_temp' => $it,
            ];
            if (count($batch) === 500) {
                DB::table('wireline_gauge_readings')->insert($batch);
                $batch = [];
            }
        }
        if ($batch) {
            DB::table('wireline_gauge_readings')->insert($batch);
        }

        return $run;
    }

    private function reanalyse(WirelineGaugeRun $run, WirelineOperation $operation): void
    {
        $readings = $run->readings()->get(['t_sec', 'pressure', 'temperature', 'internal_temp'])
            ->map(fn ($r) => [$r->t_sec, $r->pressure, $r->temperature, $r->internal_temp])->all();
        if (!$readings) {
            return;
        }
        $run->update(['analysis' => $this->analyzer->analyze(
            $readings, $run->sample_interval_sec, $run->max_range, $operation->depth_to, $run->analysis['stops'] ?? []
        )]);
        // gradients depend on saved depths, which analyze() carries over
    }

    private function deleteRunFile(WirelineGaugeRun $run): void
    {
        if ($run->file_path) {
            Storage::delete($run->file_path);
        }
    }
}
