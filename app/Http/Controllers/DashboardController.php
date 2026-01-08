<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WellTest;
use App\Models\User;
use App\Services\MPFMCalculationService;

class DashboardController extends Controller
{
    protected $calculationService;

    public function __construct(MPFMCalculationService $calculationService)
    {
        $this->calculationService = $calculationService;
    }

    public function index()
    {
        $wellTests = WellTest::latest()->limit(5)->get();
        $totalTests = WellTest::count();
        $totalUsers = User::count(); // Add total users
        $recentTests = WellTest::where('created_at', '>=', now()->subDays(7))->count(); // Tests in last 7 days

        // Get tests grouped by client for chart
        $testsByClient = WellTest::selectRaw('client, COUNT(*) as count')
            ->groupBy('client')
            ->whereNotNull('client')
            ->get();

        // Get tests over time for chart (last 30 days)
        $testsOverTime = WellTest::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Calculate summary metrics from recent tests
        $summaryMetrics = $this->calculateSummaryMetrics($wellTests);

        return view('dashboard', compact('wellTests', 'totalTests', 'totalUsers', 'recentTests', 'testsByClient', 'testsOverTime', 'summaryMetrics'));
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

    private function calculateSummaryMetrics($wellTests)
    {
        $summary = [
            'total_oil_rate' => 0,
            'total_gas_rate' => 0,
            'total_water_rate' => 0,
            'avg_gor' => 0,
            'avg_bsw' => 0,
            'test_count' => 0,
        ];

        $gorSum = 0;
        $bswSum = 0;
        $validTestsCount = 0;

        foreach ($wellTests as $test) {
            $calculatedMetrics = $test->calculated_metrics ?? [];

            $oilRate = $calculatedMetrics['oil_rate'] ?? 0;
            $gasRate = $calculatedMetrics['gas_rate'] ?? 0;
            $waterRate = $calculatedMetrics['water_rate'] ?? 0;

            $summary['total_oil_rate'] += is_numeric($oilRate) ? $oilRate : 0;
            $summary['total_gas_rate'] += is_numeric($gasRate) ? $gasRate : 0;
            $summary['total_water_rate'] += is_numeric($waterRate) ? $waterRate : 0;

            // Calculate GOR and BSW if possible
            if (is_numeric($oilRate) && $oilRate > 0) {
                $gor = is_numeric($gasRate) ? $gasRate / $oilRate : 0;
                $gorSum += $gor;
            }

            if (is_numeric($oilRate) && is_numeric($waterRate) && ($oilRate + $waterRate) > 0) {
                $bsw = ($waterRate / ($oilRate + $waterRate)) * 100;
                $bswSum += $bsw;
            }

            $validTestsCount++;
        }

        if ($validTestsCount > 0) {
            $summary['avg_gor'] = $gorSum / $validTestsCount;
            $summary['avg_bsw'] = $bswSum / $validTestsCount;
        }

        $summary['test_count'] = $validTestsCount;

        return $summary;
    }
}
