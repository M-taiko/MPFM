<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WellTest;
use App\Models\MPFMTest;
use App\Models\TestVessel;
use App\Models\TestVesselTest;
use App\Models\TVTestRequest;
use App\Services\MPFMCalculationService;

class DashboardController extends Controller
{
    protected $calculationService;

    public function __construct(MPFMCalculationService $calculationService)
    {
        $this->calculationService = $calculationService;
    }

    /**
     * Dashboard is focused on MPFM and Testing Unit data only for now.
     */
    public function index()
    {
        $statusCounts = fn ($model) => $model::selectRaw('status, COUNT(*) as c')->groupBy('status')->pluck('c', 'status')->all();

        $mpfmByStatus = $statusCounts(MPFMTest::class);
        $tvByStatus = $statusCounts(TestVesselTest::class);
        $requestsByStatus = $statusCounts(TVTestRequest::class);

        $mpfmTotal = array_sum($mpfmByStatus);
        $tvTotal = array_sum($tvByStatus);
        $approvedRate = $mpfmTotal > 0 ? round((($mpfmByStatus['approved'] ?? 0) / $mpfmTotal) * 100) : 0;

        // MPFM vs Testing Unit: every TV test that is linked to an MPFM test
        $allPairs = TestVesselTest::with(['mpfmTest', 'testVessel', 'oilString'])
            ->whereNotNull('mpfm_test_id')
            ->latest('test_date')
            ->limit(200)
            ->get()
            ->filter(fn ($tv) => $tv->mpfmTest && (float) $tv->oil_rate > 0)
            ->map(function ($tv) {
                $m = $tv->mpfmTest;
                $rel = fn ($mpfm, $ref) => (float) $ref > 0 ? (($mpfm - $ref) / $ref) * 100 : 0;

                return [
                    'tv' => $tv,
                    'mpfm' => $m,
                    'oil' => $rel((float) $m->oil_rate, (float) $tv->oil_rate),
                    'gas' => $rel((float) $m->gas_rate, (float) $tv->gas_rate),
                    'wc' => (float) $m->water_cut - (float) $tv->water_cut,
                ];
            })->values();

        $avgOilDeviation = $allPairs->isNotEmpty() ? round($allPairs->avg(fn ($p) => abs($p['oil'])), 1) : null;
        $scatter = $allPairs->map(fn ($p) => [
            'tv' => round((float) $p['tv']->oil_rate, 1),
            'mpfm' => round((float) $p['mpfm']->oil_rate, 1),
            'name' => ($p['tv']->oilString->name ?? 'Well').' · '.optional($p['tv']->test_date)->format('d M'),
        ])->values();

        // ---- time series -------------------------------------------------------------------
        $days = collect(range(29, 0))->map(fn ($i) => now()->subDays($i)->toDateString());
        $perDay = fn ($model) => $model::selectRaw('DATE(test_date) as d, COUNT(*) as c')
            ->where('test_date', '>=', $days->first())->groupBy('d')->pluck('c', 'd');
        $mpfmDaily = $perDay(MPFMTest::class);
        $tvDaily = $perDay(TestVesselTest::class);
        $activity = [
            'labels' => $days->map(fn ($d) => substr($d, 5))->values(),
            'mpfm' => $days->map(fn ($d) => (int) ($mpfmDaily[$d] ?? 0))->values(),
            'tv' => $days->map(fn ($d) => (int) ($tvDaily[$d] ?? 0))->values(),
        ];

        $months = collect(range(11, 0))->map(fn ($i) => now()->startOfMonth()->subMonths($i));
        $perMonth = fn ($model) => $model::selectRaw("DATE_FORMAT(test_date, '%Y-%m') as m, COUNT(*) as c")
            ->where('test_date', '>=', $months->first()->toDateString())->groupBy('m')->pluck('c', 'm');
        $mpfmMonthly = $perMonth(MPFMTest::class);
        $tvMonthly = $perMonth(TestVesselTest::class);
        $monthly = $months->map(fn ($m) => [
            'label' => $m->format('M'),
            'mpfm' => (int) ($mpfmMonthly[$m->format('Y-m')] ?? 0),
            'tv' => (int) ($tvMonthly[$m->format('Y-m')] ?? 0),
        ])->values();

        // calendar heat-map: tests per day over the last 12 weeks
        $heatStart = now()->startOfWeek()->subWeeks(11);
        $heatRaw = [];
        foreach ([MPFMTest::class, TestVesselTest::class] as $model) {
            $rows = $model::selectRaw('DATE(test_date) as d, COUNT(*) as c')
                ->where('test_date', '>=', $heatStart->toDateString())->groupBy('d')->pluck('c', 'd');
            foreach ($rows as $d => $c) {
                $heatRaw[$d] = ($heatRaw[$d] ?? 0) + $c;
            }
        }
        $heat = [];
        for ($w = 0; $w < 12; $w++) {
            for ($d = 0; $d < 7; $d++) {
                $date = $heatStart->copy()->addWeeks($w)->addDays($d);
                $heat[] = ['w' => $w, 'd' => $d, 'date' => $date->toDateString(), 'n' => (int) ($heatRaw[$date->toDateString()] ?? 0), 'future' => $date->isFuture()];
            }
        }

        // ---- production trends (MPFM) ------------------------------------------------------
        $mpfm = MPFMTest::with('oilString.tower.oilField.locationRef', 'user')
            ->orderBy('test_date')->orderBy('id')->get();
        $trend = $mpfm->filter(fn ($t) => $t->oil_rate !== null)->take(-40)->values()->map(fn ($t) => [
            'id' => $t->id,
            'date' => optional($t->test_date)->format('Y-m-d'),
            'name' => $t->oilString->name ?? '—',
            'oil' => (float) $t->oil_rate,
            'gas' => (float) $t->gas_rate,
            'wc' => (float) $t->water_cut,
            'gor' => $t->gor !== null ? (float) $t->gor : null,
            'p' => $t->pressure !== null ? (float) $t->pressure : null,
            't' => $t->temperature !== null ? (float) $t->temperature : null,
        ]);

        // latest test of every oil string = the "current" production picture
        $latestPerString = $mpfm->filter(fn ($t) => $t->oil_string_id)->groupBy('oil_string_id')->map->last()->values();
        $water = fn ($t) => ((float) $t->water_cut < 100 && (float) $t->oil_rate > 0) ? (float) $t->oil_rate * (float) $t->water_cut / (100 - (float) $t->water_cut) : 0;
        $snap = [
            'oil' => round($latestPerString->sum(fn ($t) => (float) $t->oil_rate), 1),
            'gas' => round($latestPerString->sum(fn ($t) => (float) $t->gas_rate), 2),
            'water' => round($latestPerString->sum($water), 1),
            'strings' => $latestPerString->count(),
        ];
        $snap['gor'] = $snap['oil'] > 0 ? round($snap['gas'] * 1000000 / $snap['oil']) : null;
        $snap['wc'] = ($snap['oil'] + $snap['water']) > 0 ? round($snap['water'] / ($snap['oil'] + $snap['water']) * 100, 1) : 0;
        $byField = $latestPerString->groupBy(fn ($t) => $t->oilString->tower->oilField->name ?? 'Unassigned')
            ->map(fn ($g, $name) => ['name' => $name, 'oil' => round($g->sum(fn ($t) => (float) $t->oil_rate), 1), 'strings' => $g->count()])
            ->sortByDesc('oil')->values();
        $topStrings = $latestPerString->sortByDesc(fn ($t) => (float) $t->oil_rate)->take(6)->map(fn ($t) => [
            'name' => $t->oilString->name ?? '—', 'oil' => (float) $t->oil_rate, 'gas' => (float) $t->gas_rate, 'wc' => (float) $t->water_cut,
        ])->values();
        $latest = $mpfm->last();

        // ---- distributions -----------------------------------------------------------------
        $shift = ['morning' => 0, 'afternoon' => 0, 'night' => 0];
        foreach ([MPFMTest::class, TestVesselTest::class] as $model) {
            foreach ($model::selectRaw('shift, COUNT(*) c')->groupBy('shift')->pluck('c', 'shift') as $k => $c) {
                if (isset($shift[$k])) {
                    $shift[$k] += $c;
                }
            }
        }
        $opRows = [];
        foreach ([MPFMTest::class => 'mpfm', TestVesselTest::class => 'tv'] as $model => $key) {
            foreach ($model::selectRaw('user_id, COUNT(*) c')->groupBy('user_id')->get() as $r) {
                $opRows[$r->user_id][$key] = (int) $r->c;
            }
        }
        $names = \App\Models\User::whereIn('id', array_keys($opRows))->pluck('name', 'id');
        $operators = collect($opRows)->map(fn ($v, $id) => [
            'name' => $names[$id] ?? 'User #'.$id,
            'mpfm' => $v['mpfm'] ?? 0,
            'tv' => $v['tv'] ?? 0,
            'total' => ($v['mpfm'] ?? 0) + ($v['tv'] ?? 0),
        ])->sortByDesc('total')->take(5)->values();

        // ---- attention list ----------------------------------------------------------------
        $stale = MPFMTest::where('status', 'draft')->where('created_at', '<', now()->subDays(3))->count();
        $awaiting = ($mpfmByStatus['submitted'] ?? 0) + ($tvByStatus['submitted'] ?? 0);
        $rejected = ($mpfmByStatus['rejected'] ?? 0) + ($tvByStatus['rejected'] ?? 0);
        $oldRequests = TVTestRequest::where('status', '!=', 'completed')->where('created_at', '<', now()->subDays(7))->count();
        $maintenance = TestVessel::where('status', 'maintenance')->count();
        $offTolerance = $allPairs->filter(fn ($p) => abs($p['oil']) > 10)->count();
        $testedStrings = $mpfm->where('test_date', '>=', now()->subDays(30))->pluck('oil_string_id')->unique()->count();
        $attention = collect([
            ['n' => $awaiting, 'label' => 'tests waiting for review', 'icon' => 'clock', 'color' => '#fbbf24', 'url' => route('mpfm_tests.index')],
            ['n' => $stale, 'label' => 'MPFM drafts older than 3 days', 'icon' => 'edit-3', 'color' => '#fb923c', 'url' => route('mpfm_tests.index')],
            ['n' => $rejected, 'label' => 'rejected tests to redo', 'icon' => 'x-circle', 'color' => '#f87171', 'url' => route('mpfm_tests.index')],
            ['n' => $offTolerance, 'label' => 'MPFM vs vessel pairs beyond ±10%', 'icon' => 'alert-triangle', 'color' => '#f87171', 'url' => route('tv_tests.compare')],
            ['n' => $oldRequests, 'label' => 'test requests open for over a week', 'icon' => 'clipboard', 'color' => '#a78bfa', 'url' => route('tv_test_requests.index')],
            ['n' => $maintenance, 'label' => 'vessels in maintenance', 'icon' => 'settings', 'color' => '#38bdf8', 'url' => route('test_vessels.index')],
        ])->filter(fn ($a) => $a['n'] > 0)->values();

        // ---- activity feed -----------------------------------------------------------------
        $feed = collect()
            ->merge(MPFMTest::with('oilString', 'user')->latest()->limit(6)->get()->map(fn ($t) => ['type' => 'mpfm', 'title' => 'MPFM test #'.$t->id.' · '.($t->oilString->name ?? '—'), 'sub' => ucfirst($t->status).' · '.number_format((float) $t->oil_rate, 0).' B/D', 'who' => $t->user->name ?? '', 'when' => $t->created_at, 'url' => route('mpfm_tests.show', $t->id)]))
            ->merge(TestVesselTest::with('oilString', 'user', 'testVessel')->latest()->limit(6)->get()->map(fn ($t) => ['type' => 'tv', 'title' => 'Vessel test #'.$t->id.' · '.($t->oilString->name ?? '—'), 'sub' => ucfirst($t->status).' · '.($t->testVessel->name ?? ''), 'who' => $t->user->name ?? '', 'when' => $t->created_at, 'url' => route('tv_tests.show', $t->id)]))
            ->merge(TVTestRequest::with('testVessel', 'requestedBy')->latest()->limit(4)->get()->map(fn ($r) => ['type' => 'req', 'title' => 'Test request #'.$r->id.' · '.($r->testVessel->name ?? ''), 'sub' => ucfirst(str_replace('_', ' ', $r->status)), 'who' => $r->requestedBy->name ?? '', 'when' => $r->created_at, 'url' => route('tv_test_requests.show', $r->id)]))
            ->merge(WellTest::latest()->limit(4)->get()->map(fn ($w) => ['type' => 'log', 'title' => 'Log uploaded · '.($w->well ?? '—'), 'sub' => (string) $w->filename, 'who' => $w->operator ?? '', 'when' => $w->created_at, 'url' => route('well_tests.show', $w->id)]))
            ->filter(fn ($e) => $e['when'])->sortByDesc('when')->take(9)->values();

        // ═════ Role-based extras ═════════════════════════════════════════════
        $user = auth()->user();
        $persona = $this->resolvePersona($user, request('view'));
        $needs = fn (array $p) => in_array($persona, $p, true);
        $extra = ['persona' => $persona, 'canSwitchView' => $user->hasRole('admin')];

        // production by location (latest test per well)
        $extra['byLocation'] = $latestPerString->groupBy(fn ($t) => optional(optional(optional($t->oilString)->tower)->oilField)->locationRef->name ?? 'No location')
            ->map(fn ($g, $name) => ['name' => $name, 'oil' => round($g->sum(fn ($t) => (float) $t->oil_rate), 1), 'wells' => $g->count()])->sortByDesc('oil')->values();

        // watch lists from the last six tests of every well
        $watch = $mpfm->filter(fn ($t) => $t->oil_string_id && $t->status !== 'rejected' && $t->oil_rate !== null)->groupBy('oil_string_id')->map(function ($g) {
            $g = $g->sortBy('test_date')->values()->take(-6);
            if ($g->count() < 4) { return null; }
            $a = $g->first(); $b = $g->last();
            return [
                'id' => $b->oil_string_id, 'name' => $b->oilString->name ?? '—', 'tower' => $b->oilString->tower->name ?? '',
                'oil_from' => (float) $a->oil_rate, 'oil_to' => (float) $b->oil_rate,
                'oil_pct' => (float) $a->oil_rate > 0 ? round(((float) $b->oil_rate - (float) $a->oil_rate) / (float) $a->oil_rate * 100, 1) : 0,
                'wc_from' => (float) $a->water_cut, 'wc_to' => (float) $b->water_cut, 'wc_delta' => round((float) $b->water_cut - (float) $a->water_cut, 1),
                'days' => $a->test_date->diffInDays($b->test_date), 'tests' => $g->count(),
            ];
        })->filter()->values();
        $extra['declineWatch'] = $watch->sortBy('oil_pct')->filter(fn ($w) => $w['oil_pct'] < -3)->take(6)->values();
        $extra['wcWatch'] = $watch->sortByDesc('wc_delta')->filter(fn ($w) => $w['wc_delta'] > 1.5)->take(6)->values();
        $extra['declineCount'] = $watch->where('oil_pct', '<', -10)->count();
        $extra['wcRiseCount'] = $watch->where('wc_delta', '>', 3)->count();

        // wells that are due a test
        $due = \App\Models\OilString::where('status', 'active')->with('tower')->withMax('mpfmTests as last_test', 'test_date')->get()
            ->filter(fn ($w) => !$w->last_test || \Carbon\Carbon::parse($w->last_test)->lt(now()->subDays(30)))
            ->sortBy(fn ($w) => $w->last_test ?? '0000')->values();
        $extra['dueWells'] = $due->take(8);
        $extra['dueCount'] = $due->count();

        // integrity picture for the roles that may see it
        $extra['integrity'] = ($user->canAccessIntegrity() && $needs(['admin', 'manager', 'supervisor', 'production', 'reservoir']))
            ? app(\App\Services\Integrity\WellIntegrityOverview::class)->build() : null;

        // review desk (supervisors, managers, admin)
        if ($needs(['admin', 'manager', 'supervisor'])) {
            $q = collect();
            foreach (MPFMTest::with('oilString', 'user')->where('status', 'submitted')->orderBy('test_date')->limit(8)->get() as $t) {
                $q->push(['type' => 'MPFM', 'color' => '#22d3ee', 'well' => $t->oilString->name ?? '—', 'date' => $t->test_date, 'who' => $t->user->name ?? '—', 'score' => $t->validation_score, 'vs' => $t->validation_status, 'oil' => $t->oil_rate, 'url' => route('mpfm_tests.show', $t->id)]);
            }
            foreach (TestVesselTest::with('oilString', 'user')->where('status', 'submitted')->orderBy('test_date')->limit(8)->get() as $t) {
                $q->push(['type' => 'Well testing', 'color' => '#fbbf24', 'well' => $t->oilString->name ?? '—', 'date' => $t->test_date, 'who' => $t->user->name ?? '—', 'score' => null, 'vs' => null, 'oil' => $t->oil_rate, 'url' => route('tv_tests.show', $t->id)]);
            }
            $extra['reviewQueue'] = $q->sortBy('date')->take(9)->values();
            $extra['validationAlerts'] = MPFMTest::with('oilString')->whereIn('validation_status', ['warning', 'fail'])->latest('test_date')->limit(6)->get();
            $extra['turnaround'] = round((float) MPFMTest::where('status', 'approved')->whereNotNull('approved_by_surveillance_at')->where('test_date', '>=', now()->subDays(90))
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, approved_by_surveillance_at)) as h')->value('h'), 1);
            $extra['rejected30'] = MPFMTest::where('status', 'rejected')->where('test_date', '>=', now()->subDays(30))->count();
        }

        // manager view
        if ($needs(['admin', 'manager'])) {
            $extra['monthMpfm'] = MPFMTest::whereBetween('test_date', [now()->startOfMonth(), now()])->count();
            $extra['prevMonthMpfm'] = MPFMTest::whereBetween('test_date', [now()->subMonthNoOverflow()->startOfMonth(), now()->subMonthNoOverflow()->endOfMonth()])->count();
            $extra['upcomingWl'] = \App\Models\WirelineOperation::with('oilString')->whereIn('status', ['planned', 'in_progress'])->orderBy('operation_date')->limit(6)->get();
            $extra['upcomingRequests'] = TVTestRequest::with(['testVessel', 'tower'])->whereIn('status', ['open', 'in_progress'])->orderBy('test_date')->limit(6)->get();
        }

        // reservoir view: static pressures from the memory-gauge surveys
        if ($needs(['admin', 'reservoir'])) {
            $runs = \App\Models\WirelineGaugeRun::with('operation.oilString')->whereHas('operation')->get()
                ->filter(fn ($r) => isset($r->analysis['stats']['bhp']))
                ->map(fn ($r) => ['well' => optional($r->operation->oilString)->name ?? '—', 'well_id' => $r->operation->oil_string_id, 'date' => ($r->started_at ?? $r->operation->operation_date)->copy()->startOfDay(),
                    'bhp' => (float) $r->analysis['stats']['bhp'], 'bht' => (float) ($r->analysis['stats']['bht'] ?? 0), 'type' => $r->survey_type, 'op' => $r->operation, 'run' => $r])->sortBy('date')->values();
            $static = $runs->filter(fn ($r) => in_array($r['type'], ['bhcip', 'static_gradient', 'pbu'], true));
            $depl = $static->groupBy('well_id')->map(function ($g) {
                $g = $g->sortBy('date')->values();
                if ($g->count() < 2) { return null; }
                $a = $g->first(); $b = $g->last();
                return ['name' => $b['well'], 'id' => $b['well_id'], 'from' => $a['bhp'], 'to' => $b['bhp'], 'pct' => $a['bhp'] > 0 ? round(($a['bhp'] - $b['bhp']) / $a['bhp'] * 100, 1) : 0, 'months' => max(1, round($a['date']->diffInDays($b['date']) / 30.4)), 'date' => $b['date']];
            })->filter()->sortByDesc('pct')->values();
            $latestStatic = $static->groupBy('well_id')->map->last();
            $monthAvg = $static->groupBy(fn ($r) => $r['date']->format('Y-m'))->map(fn ($g) => round($g->avg('bhp')))->sortKeys();
            $extra['reservoir'] = [
                'runs' => $runs->count(), 'static' => $static->count(), 'recent90' => $runs->filter(fn ($r) => $r['date']->gte(now()->subDays(90)))->count(),
                'avgBhp' => $latestStatic->isNotEmpty() ? round($latestStatic->avg('bhp')) : null, 'avgBht' => $latestStatic->isNotEmpty() ? round($latestStatic->avg('bht'), 1) : null,
                'maxDepl' => $depl->first()['pct'] ?? null, 'deplCount' => $depl->where('pct', '>', 5)->count(), 'depl' => $depl->take(7),
                'trend' => ['labels' => $monthAvg->keys()->map(fn ($m) => \Carbon\Carbon::createFromFormat('Y-m', $m)->format('M y'))->all(), 'bhp' => $monthAvg->values()->all()],
                'latest' => $runs->sortByDesc('date')->take(6)->values(),
            ];
        }

        // operator view: my own work
        if ($needs(['operator', 'admin'])) {
            $mine = MPFMTest::where('user_id', $user->id);
            $extra['mine'] = [
                'byStatus' => (clone $mine)->selectRaw('status, COUNT(*) c')->groupBy('status')->pluck('c', 'status')->all(),
                'recent' => (clone $mine)->with('oilString')->latest('test_date')->latest('id')->limit(7)->get(),
                'todo' => (clone $mine)->with('oilString')->whereIn('status', ['draft', 'rejected'])->latest('test_date')->limit(6)->get(),
                'month' => (clone $mine)->where('test_date', '>=', now()->startOfMonth())->count(),
                'tv' => TestVesselTest::where('user_id', $user->id)->count(),
            ];
            $d14 = collect(range(13, 0))->map(fn ($i) => now()->subDays($i)->toDateString());
            $per = (clone $mine)->where('test_date', '>=', $d14->first())->selectRaw('DATE(test_date) d, COUNT(*) c')->groupBy('d')->pluck('c', 'd');
            $extra['mineDaily'] = ['labels' => $d14->map(fn ($d) => substr($d, 5))->all(), 'data' => $d14->map(fn ($d) => (int) ($per[$d] ?? 0))->all()];
            $extra['myRequests'] = TVTestRequest::with(['testVessel', 'tower'])->whereIn('status', ['open', 'in_progress'])->orderBy('test_date')->limit(5)->get();
        }
        return view('dashboard', $extra + [
            'mpfmTotal' => $mpfmTotal,
            'mpfmWeek' => MPFMTest::where('test_date', '>=', now()->subDays(7))->count(),
            'mpfmPrevWeek' => MPFMTest::whereBetween('test_date', [now()->subDays(14), now()->subDays(7)])->count(),
            'mpfmByStatus' => $mpfmByStatus,
            'tvTotal' => $tvTotal,
            'tvWeek' => TestVesselTest::where('test_date', '>=', now()->subDays(7))->count(),
            'tvByStatus' => $tvByStatus,
            'requestsByStatus' => $requestsByStatus,
            'openRequests' => TVTestRequest::where('status', '!=', 'completed')->count(),
            'vessels' => TestVessel::withCount('tests')->withMax('tests as last_test_at', 'test_date')->orderBy('name')->get(),
            'locSummary' => (function () {
                $locs = \App\Models\Location::orderBy('environment')->orderBy('name')->get();
                $stats = \App\Models\Location::stats();
                return [
                    'total' => $locs->count(),
                    'types' => $locs->countBy('environment'),
                    'stats' => $stats,
                    'fields' => array_sum(array_column($stats, 'fields')),
                    'towers' => array_sum(array_column($stats, 'towers')),
                    'wells' => array_sum(array_column($stats, 'wells')),
                    'list' => $locs->take(6),
                ];
            })(),
            'archiveCount' => WellTest::count(),
            'approvedRate' => $approvedRate,
            'avgOilDeviation' => $avgOilDeviation,
            'pairs' => $allPairs->take(6),
            'pairCount' => $allPairs->count(),
            'scatter' => $scatter,
            'activity' => $activity,
            'monthly' => $monthly,
            'heat' => $heat,
            'trend' => $trend,
            'snap' => $snap,
            'byField' => $byField,
            'topStrings' => $topStrings,
            'latest' => $latest,
            'shift' => $shift,
            'operators' => $operators,
            'attention' => $attention,
            'feed' => $feed,
            'counts' => ['fields' => \App\Models\OilField::count(), 'towers' => \App\Models\Tower::count(), 'strings' => \App\Models\OilString::count(), 'activeStrings' => \App\Models\OilString::where('status', 'active')->count(), 'testedStrings' => $testedStrings],
            'recentMpfm' => MPFMTest::with('oilString')->latest('test_date')->latest('id')->limit(6)->get(),
            'recentTv' => TestVesselTest::with(['testVessel', 'oilString'])->latest('test_date')->latest('id')->limit(6)->get(),
            'wireline' => auth()->user()?->canAccessWireline() ? app(\App\Services\Wireline\WirelineOverview::class)->build() : null,
            'recentRequests' => TVTestRequest::with('testVessel')->where('status', '!=', 'completed')->latest('test_date')->limit(5)->get(),
        ]);
    }

    /** Which dashboard a user gets. Admins may preview any of them with ?view= */
    private function resolvePersona($user, ?string $view): string
    {
        $all = ['admin', 'manager', 'supervisor', 'production', 'reservoir', 'operator'];
        if ($view && in_array($view, $all, true) && $user->hasRole('admin')) {
            return $view;
        }
        return match (true) {
            $user->hasRole('admin') => 'admin',
            $user->hasAnyRole(['department_manager', 'job_officer']) => 'manager',
            $user->hasAnyRole(['surveillance_supervisor', 'mpfm_supervisor', 'department_supervisor']) => 'supervisor',
            $user->hasRole('production_supervisor') => 'production',
            $user->hasRole('reservoir_engineer') => 'reservoir',
            $user->hasAnyRole(['mpfm_operator', 'operator']) => 'operator',
            default => 'production',
        };
    }

    public function jobOfficerReport()
    {
        // Job officer specific report logic
        return view('reports.job_officer');
    }

    public function reservoirEngineerReport()
    {
        // Reservoir engineer specific report logic
        return view('reports.reservoir_engineer');
    }

    public function productionSupervisorReport()
    {
        // Production supervisor specific report logic
        return view('reports.production_supervisor');
    }

    public function mpfmSupervisorReport()
    {
        // MPFM supervisor specific report logic
        return view('reports.mpfm_supervisor');
    }

    public function mpfmOperatorReport()
    {
        // MPFM operator specific report logic
        return view('reports.mpfm_operator');
    }
}
