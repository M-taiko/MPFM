<?php

namespace App\Http\Controllers;

use App\Models\OilString;
use App\Models\WellBarrierElement;
use App\Models\WellIntegrityAnnulus;
use App\Models\WellIntegrityAnnulusReading;
use App\Models\WellIntegrityAnomaly;
use App\Models\WellIntegrityInspection;
use App\Models\WellIntegrityProfile;
use App\Models\WellIntegrityTest;
use App\Models\WellheadCheck;
use App\Services\Integrity\WellIntegrityAssessor;
use App\Services\Integrity\WellIntegrityOverview;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WellIntegrityController extends Controller
{
    public function __construct(private WellIntegrityAssessor $assessor, private WellIntegrityOverview $overview)
    {
    }

    private function canManage(): bool
    {
        return auth()->user()?->canManageIntegrity() ?? false;
    }

    private function requireManage(): void
    {
        abort_unless($this->canManage(), 403, 'You are not allowed to manage Well Integrity records.');
    }

    private function requireDelete(): void
    {
        abort_unless(auth()->user()?->hasAnyRole(['admin', 'department_manager', 'job_officer']), 403);
    }

    // ── Dashboard ────────────────────────────────────────────────
    public function dashboard()
    {
        return view('integrity.dashboard', $this->overview->build() + ['canManage' => $this->canManage()]);
    }

    // ── Wells register ───────────────────────────────────────────
    public function wells(Request $request)
    {
        $rows = $this->overview->wells();
        if ($request->filled('status')) {
            $rows = $rows->filter(fn ($r) => $r['a']['key'] === $request->input('status'))->values();
        }
        if ($request->filled('q')) {
            $q = mb_strtolower($request->input('q'));
            $rows = $rows->filter(fn ($r) => str_contains(mb_strtolower($r['well']->name . ' ' . $r['well']->code), $q))->values();
        }

        return view('integrity.wells', ['rows' => $rows, 'canManage' => $this->canManage()]);
    }

    // ── Anomaly register ─────────────────────────────────────────
    public function anomalies(Request $request)
    {
        $q = WellIntegrityAnomaly::with('oilString.tower.oilField')->orderByDesc('risk_score')->orderBy('due_date');
        $request->filled('status') ? $q->where('status', $request->input('status')) : $q->active();
        if ($request->input('status') === 'all') {
            $q = WellIntegrityAnomaly::with('oilString.tower.oilField')->orderByDesc('risk_score');
        }
        if ($request->filled('category')) {
            $q->where('category', $request->input('category'));
        }
        if ($request->filled('well')) {
            $q->where('oil_string_id', $request->integer('well'));
        }

        return view('integrity.anomalies', [
            'anomalies' => $q->paginate(20)->withQueryString(),
            'wells'     => OilString::orderBy('name')->get(['id', 'name']),
            'canManage' => $this->canManage(),
        ]);
    }

    // ── Well integrity file ──────────────────────────────────────
    public function show(OilString $well)
    {
        $well->load('tower.oilField');
        $a = $this->assessor->assess($well);

        $chart = collect($a['annuli'])->map(fn ($an) => [
            'name'  => $an['name'], 'maasp' => $an['maasp'],
            'points' => $an['readings']->map(fn ($r) => ['x' => $r->reading_date->format('Y-m-d'), 'y' => $r->pressure])->all(),
        ])->values();

        return view('integrity.show', [
            'well'      => $well,
            'a'         => $a,
            'chart'     => $chart,
            'wh'        => $this->checkCharts($well),
            'tests'     => $well->integrityTests()->with('element')->orderByDesc('test_date')->orderByDesc('id')->limit(25)->get(),
            'readings'  => \App\Models\WellIntegrityAnnulusReading::with('annulus')->where('oil_string_id', $well->id)->orderByDesc('reading_date')->orderByDesc('id')->limit(20)->get(),
            'canManage' => $this->canManage(),
            'canDelete' => auth()->user()?->hasAnyRole(['admin', 'department_manager', 'job_officer']) ?? false,
        ]);
    }

    // ── Operator wellhead checks ─────────────────────────────────
    public function createCheck(OilString $well)
    {
        $this->requireManage();
        $last = $well->wellheadChecks()->with('items')->first();

        return view('integrity.check_form', ['well' => $well->load('tower.oilField'), 'last' => $last]);
    }

    public function storeCheck(Request $request, OilString $well)
    {
        $this->requireManage();
        $data = $request->validate([
            'check_date'  => 'required|date|before_or_equal:today',
            'wht'         => 'nullable|string|max:60',
            'well_no'     => 'nullable|string|max:80',
            'oem'         => 'nullable|string|max:60',
            'string_no'   => 'required|string|max:10',
            'well_status' => ['required', Rule::in(array_keys(WellheadCheck::STATUSES))],
            'whcip'       => 'nullable|numeric|min:0|max:50000',
            'tree_cap'    => ['required', Rule::in(array_keys(WellheadCheck::TREE_CAP))],
            'remarks'     => 'nullable|string|max:3000',
            'checked_by'  => 'nullable|string|max:255',
            'items'                 => 'nullable|array',
            'items.*.code'          => 'required|string|max:20',
            'items.*.item_type'     => ['required', Rule::in(['valve', 'annulus'])],
            'items.*.condition'     => ['nullable', Rule::in(array_keys(WellheadCheck::CONDITIONS))],
            'items.*.turns'         => 'nullable|numeric|min:0|max:500',
            'items.*.time_sec'      => 'nullable|integer|min:0|max:3600',
            'items.*.greased'       => 'nullable|boolean',
            'items.*.pressure'      => 'nullable|numeric|min:0|max:50000',
            'items.*.valve_ref'     => 'nullable|string|max:20',
            'items.*.dbb_side'      => ['nullable', Rule::in(['RHS', 'LHS'])],
            'items.*.notes'         => 'nullable|string|max:255',
        ]);

        $check = \DB::transaction(function () use ($data, $well) {
            $check = WellheadCheck::create(collect($data)->except('items')->all() + ['oil_string_id' => $well->id, 'created_by' => auth()->id()]);

            $worst = []; // barrier element type => worst result of the day
            $rank = ['pass' => 0, 'marginal' => 1, 'fail' => 2];
            foreach ($data['items'] ?? [] as $i) {
                $isAnn = $i['item_type'] === 'annulus';
                // skip rows the operator left empty
                if (empty($i['condition']) && ($i['turns'] ?? '') === '' && ($i['time_sec'] ?? '') === '' && ($i['pressure'] ?? '') === '') {
                    continue;
                }
                $check->items()->create([
                    'item_type' => $i['item_type'], 'code' => $i['code'], 'condition' => $i['condition'] ?? null,
                    'turns' => $i['turns'] ?? null, 'time_sec' => $i['time_sec'] ?? null, 'greased' => !empty($i['greased']),
                    'pressure' => $isAnn ? ($i['pressure'] ?? null) : null, 'valve_ref' => $i['valve_ref'] ?? null,
                    'dbb_side' => $i['dbb_side'] ?? null, 'notes' => $i['notes'] ?? null,
                ]);

                // annulus pressure -> monitoring series (annulus is created on first use)
                if ($isAnn && ($i['pressure'] ?? '') !== '') {
                    $annulus = WellIntegrityAnnulus::firstOrCreate(['oil_string_id' => $well->id, 'name' => $i['code']]);
                    WellIntegrityAnnulusReading::create([
                        'well_integrity_annulus_id' => $annulus->id, 'oil_string_id' => $well->id, 'reading_date' => $data['check_date'],
                        'pressure' => $i['pressure'], 'remarks' => 'From wellhead check', 'created_by' => auth()->id(),
                    ]);
                }

                // valve function -> barrier element test
                if (!empty($i['condition'])) {
                    $type = $isAnn ? 'annulus_valve' : (WellheadCheck::VALVES[$i['code']][1] ?? null);
                    $res = ['ok' => 'pass', 'stiff' => 'marginal', 'leaking' => 'fail', 'seized' => 'fail'][$i['condition']];
                    if ($type && $rank[$res] >= ($rank[$worst[$type] ?? 'pass'] ?? 0)) {
                        $worst[$type] = $res;
                    }
                }
            }

            foreach ($worst as $type => $res) {
                $element = WellBarrierElement::where('oil_string_id', $well->id)->where('element_type', $type)->first();
                if ($element) {
                    $element->tests()->create([
                        'oil_string_id' => $well->id, 'test_date' => $data['check_date'], 'test_type' => 'function', 'result' => $res,
                        'tested_by' => $data['checked_by'] ?? null, 'remarks' => 'Wellhead check #' . $check->id, 'created_by' => auth()->id(),
                    ]);
                    $element->syncFromTests();
                }
            }

            return $check;
        });

        return redirect()->route('integrity.checks.show', $check)->with('success', 'Wellhead check saved - barrier elements and annulus readings updated.');
    }

    public function showCheck(WellheadCheck $check)
    {
        $check->load(['items', 'oilString.tower.oilField']);
        $prev = WellheadCheck::with('items')->where('oil_string_id', $check->oil_string_id)
            ->where(fn ($q) => $q->where('check_date', '<', $check->check_date)->orWhere(fn ($q2) => $q2->where('check_date', $check->check_date)->where('id', '<', $check->id)))
            ->orderByDesc('check_date')->orderByDesc('id')->first();

        return view('integrity.check_show', [
            'check' => $check, 'prev' => $prev, 'well' => $check->oilString,
            'canDelete' => auth()->user()?->hasAnyRole(['admin', 'department_manager', 'job_officer']) ?? false,
        ]);
    }

    public function destroyCheck(WellheadCheck $check)
    {
        $this->requireDelete();
        $wellId = $check->oil_string_id;
        $check->delete();

        return $this->back($wellId, 'Wellhead check deleted.');
    }

    /** Series for the wellhead charts on the well page */
    private function checkCharts(OilString $well): array
    {
        $checks = WellheadCheck::with('items')->where('oil_string_id', $well->id)
            ->orderBy('check_date')->orderBy('id')->get()->take(-24)->values();
        $labels = $checks->map(fn ($c) => $c->check_date->format('d M y'))->all();

        $series = function (string $field, string $type) use ($checks) {
            $codes = $checks->flatMap->items->where('item_type', $type)->pluck('code')->unique()->values();

            return $codes->map(fn ($code) => [
                'code' => $code,
                'data' => $checks->map(fn ($c) => optional($c->items->where('code', $code)->first())->{$field})->all(),
            ])->filter(fn ($s) => collect($s['data'])->filter(fn ($v) => $v !== null)->isNotEmpty())->values()->all();
        };

        $recent = $checks->take(-10)->values();
        $matrixCodes = $checks->flatMap->items->filter(fn ($i) => $i->condition)->pluck('code')->unique()->values();

        return [
            'labels'   => $labels,
            'turns'    => $series('turns', 'valve'),
            'time'     => $series('time_sec', 'valve'),
            'annulus'  => $series('pressure', 'annulus'),
            'whcip'    => $checks->map(fn ($c) => $c->whcip)->all(),
            'matrix'   => [
                'dates' => $recent->map(fn ($c) => $c->check_date->format('d M'))->all(),
                'ids'   => $recent->pluck('id')->all(),
                'rows'  => $matrixCodes->map(fn ($code) => [
                    'code'  => $code,
                    'cells' => $recent->map(fn ($c) => optional($c->items->where('code', $code)->first())->condition)->all(),
                ])->all(),
            ],
            'count'    => $checks->count(),
        ];
    }

    // ── Profile ──────────────────────────────────────────────────
    public function saveProfile(Request $request, OilString $well)
    {
        $this->requireManage();
        $data = $request->validate([
            'well_type'            => ['required', Rule::in(array_keys(WellIntegrityProfile::WELL_TYPES))],
            'lift_method'          => ['required', Rule::in(array_keys(WellIntegrityProfile::LIFT_METHODS))],
            'tubing_size'          => 'nullable|string|max:40',
            'casing_size'          => 'nullable|string|max:60',
            'mawhp'                => 'nullable|numeric|min:0|max:50000',
            'max_oil_rate'         => 'nullable|numeric|min:0|max:1000000',
            'restriction_note'     => 'nullable|string|max:255',
            'review_interval_days' => 'required|integer|min:30|max:1825',
            'last_review_date'     => 'nullable|date',
            'reviewed_by'          => 'nullable|string|max:255',
            'notes'                => 'nullable|string|max:5000',
        ]);
        WellIntegrityProfile::updateOrCreate(['oil_string_id' => $well->id], $data);

        return $this->back($well, 'Integrity profile saved.');
    }

    // ── Annuli ───────────────────────────────────────────────────
    public function saveAnnulus(Request $request, OilString $well)
    {
        $this->requireManage();
        $data = $request->validate([
            'name'  => ['required', Rule::in(array_keys(WellIntegrityAnnulus::NAMES))],
            'maasp' => 'nullable|numeric|min:0|max:50000',
            'notes' => 'nullable|string|max:255',
        ]);
        WellIntegrityAnnulus::updateOrCreate(['oil_string_id' => $well->id, 'name' => $data['name']], $data);

        return $this->back($well, "Annulus {$data['name']} saved.");
    }

    public function destroyAnnulus(WellIntegrityAnnulus $annulus)
    {
        $this->requireDelete();
        $annulus->delete();

        return $this->back($annulus->oil_string_id, 'Annulus removed.');
    }

    public function storeReading(Request $request, OilString $well)
    {
        $this->requireManage();
        $data = $request->validate([
            'well_integrity_annulus_id' => ['required', Rule::exists('well_integrity_annuli', 'id')->where('oil_string_id', $well->id)],
            'reading_date'              => 'required|date|before_or_equal:today',
            'pressure'                  => 'required|numeric|min:0|max:50000',
            'bleed_down_pressure'       => 'nullable|numeric|min:0|max:50000',
            'rebuild_pressure'          => 'nullable|numeric|min:0|max:50000',
            'rebuild_hours'             => 'nullable|numeric|min:0|max:10000',
            'fluid'                     => ['nullable', Rule::in(array_keys(WellIntegrityAnnulusReading::FLUIDS))],
            'remarks'                   => 'nullable|string|max:2000',
        ]);
        WellIntegrityAnnulusReading::create($data + ['oil_string_id' => $well->id, 'created_by' => auth()->id()]);

        return $this->back($well, 'Annulus reading recorded.');
    }

    public function destroyReading(WellIntegrityAnnulusReading $reading)
    {
        $this->requireDelete();
        $reading->delete();

        return $this->back($reading->oil_string_id, 'Reading deleted.');
    }

    // ── Barriers & tests ─────────────────────────────────────────
    public function seedBarriers(OilString $well)
    {
        $this->requireManage();
        $made = 0;
        foreach (WellBarrierElement::TYPES as $type => [$label, $env, $interval]) {
            $el = WellBarrierElement::firstOrCreate(
                ['oil_string_id' => $well->id, 'element_type' => $type],
                ['envelope' => $env, 'name' => $label, 'test_interval_days' => $interval, 'status' => 'untested']
            );
            $made += $el->wasRecentlyCreated ? 1 : 0;
        }
        foreach (['A', 'B'] as $name) {
            WellIntegrityAnnulus::firstOrCreate(['oil_string_id' => $well->id, 'name' => $name]);
        }

        return $this->back($well, $made ? "Standard barrier template applied ($made elements added). Set the MAASP for each annulus." : 'All standard barrier elements already exist.');
    }

    public function storeElement(Request $request, OilString $well)
    {
        $this->requireManage();
        $data = $request->validate([
            'element_type'       => ['required', Rule::in(array_keys(WellBarrierElement::TYPES))],
            'name'               => 'nullable|string|max:255',
            'test_interval_days' => 'nullable|integer|min:7|max:1825',
        ]);
        [$label, $env, $interval] = WellBarrierElement::TYPES[$data['element_type']];
        WellBarrierElement::create([
            'oil_string_id' => $well->id, 'envelope' => $env, 'element_type' => $data['element_type'],
            'name' => $data['name'] ?: $label, 'test_interval_days' => $data['test_interval_days'] ?: $interval,
        ]);

        return $this->back($well, 'Barrier element added.');
    }

    public function destroyElement(WellBarrierElement $element)
    {
        $this->requireDelete();
        $element->delete();

        return $this->back($element->oil_string_id, 'Barrier element removed.');
    }

    public function storeTest(Request $request, WellBarrierElement $element)
    {
        $this->requireManage();
        $data = $request->validate([
            'test_date'     => 'required|date|before_or_equal:today',
            'test_type'     => ['required', Rule::in(array_keys(WellIntegrityTest::TYPES))],
            'result'        => ['required', Rule::in(array_keys(WellIntegrityTest::RESULTS))],
            'test_pressure' => 'nullable|numeric|min:0|max:50000',
            'leak_rate'     => 'nullable|numeric|min:0|max:1000000',
            'duration_min'  => 'nullable|integer|min:0|max:10000',
            'tested_by'     => 'nullable|string|max:255',
            'remarks'       => 'nullable|string|max:2000',
        ]);
        $element->tests()->create($data + ['oil_string_id' => $element->oil_string_id, 'created_by' => auth()->id()]);
        $element->syncFromTests();

        $msg = $data['result'] === 'fail'
            ? "{$element->name} FAILED its test - consider raising an anomaly."
            : 'Test recorded.';

        return $this->back($element->oil_string_id, $msg);
    }

    public function destroyTest(WellIntegrityTest $test)
    {
        $this->requireDelete();
        $element = $test->element;
        $wellId = $test->oil_string_id;
        $test->delete();
        $element?->syncFromTests();

        return $this->back($wellId, 'Test deleted.');
    }

    // ── Inspections ──────────────────────────────────────────────
    public function storeInspection(Request $request, OilString $well)
    {
        $this->requireManage();
        $data = $request->validate([
            'inspection_type'   => ['required', Rule::in(array_keys(WellIntegrityInspection::TYPES))],
            'inspection_date'   => 'required|date|before_or_equal:today',
            'depth_from'        => 'nullable|numeric|min:0|max:20000',
            'depth_to'          => 'nullable|numeric|min:0|max:20000',
            'max_wall_loss_pct' => 'nullable|numeric|min:0|max:100',
            'status'            => ['required', Rule::in(array_keys(WellIntegrityInspection::STATUSES))],
            'next_due_date'     => 'nullable|date|after_or_equal:inspection_date',
            'finding'           => 'nullable|string|max:3000',
            'remarks'           => 'nullable|string|max:3000',
        ]);
        WellIntegrityInspection::create($data + ['oil_string_id' => $well->id, 'created_by' => auth()->id()]);

        return $this->back($well, 'Inspection recorded.');
    }

    public function destroyInspection(WellIntegrityInspection $inspection)
    {
        $this->requireDelete();
        $inspection->delete();

        return $this->back($inspection->oil_string_id, 'Inspection deleted.');
    }

    // ── Anomalies ────────────────────────────────────────────────
    public function storeAnomaly(Request $request, OilString $well)
    {
        $this->requireManage();
        WellIntegrityAnomaly::create($this->validateAnomaly($request) + [
            'oil_string_id' => $well->id, 'created_by' => auth()->id(), 'status' => 'open',
        ]);

        return $this->back($well, 'Anomaly raised.');
    }

    public function updateAnomaly(Request $request, WellIntegrityAnomaly $anomaly)
    {
        $this->requireManage();
        $data = $request->validate([
            'status'     => ['required', Rule::in(array_keys(WellIntegrityAnomaly::STATUSES))],
            'mitigation' => 'nullable|string|max:3000',
            'likelihood' => 'nullable|integer|min:1|max:5',
            'consequence' => 'nullable|integer|min:1|max:5',
            'due_date'   => 'nullable|date',
        ]);
        $data = array_filter($data, fn ($v) => $v !== null);
        $data['closed_date'] = $data['status'] === 'closed' ? ($anomaly->closed_date ?? now()->toDateString()) : null;
        $anomaly->update($data);

        return $this->back($anomaly->oil_string_id, 'Anomaly updated.');
    }

    public function destroyAnomaly(WellIntegrityAnomaly $anomaly)
    {
        $this->requireDelete();
        $anomaly->delete();

        return $this->back($anomaly->oil_string_id, 'Anomaly deleted.');
    }

    // ── internals ────────────────────────────────────────────────
    private function validateAnomaly(Request $request): array
    {
        return $request->validate([
            'title'           => 'required|string|max:255',
            'category'        => ['required', Rule::in(array_keys(WellIntegrityAnomaly::CATEGORIES))],
            'likelihood'      => 'required|integer|min:1|max:5',
            'consequence'     => 'required|integer|min:1|max:5',
            'identified_date' => 'required|date|before_or_equal:today',
            'due_date'        => 'nullable|date|after_or_equal:identified_date',
            'description'     => 'nullable|string|max:3000',
            'mitigation'      => 'nullable|string|max:3000',
        ]);
    }

    private function back(OilString|int $well, string $msg)
    {
        $id = $well instanceof OilString ? $well->id : $well;

        return redirect()->route('integrity.show', $id)->with('success', $msg);
    }
}
