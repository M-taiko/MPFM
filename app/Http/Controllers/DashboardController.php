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
     * Dashboard is focused on MPFM and Test Vessel data only for now.
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

        // MPFM vs Test Vessel: every TV test that is linked to an MPFM test
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
        $mpfm = MPFMTest::with('oilString.tower.oilField', 'user')
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

        return view('dashboard', [
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
