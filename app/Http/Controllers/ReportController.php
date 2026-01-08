<?php

namespace App\Http\Controllers;

use App\Services\ReportingService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $reportingService;

    public function __construct(ReportingService $reportingService)
    {
        $this->reportingService = $reportingService;
        $this->middleware('auth');
    }

    /**
     * Operator Performance Report
     */
    public function operatorPerformance(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $userId = $request->input('user_id');

        $report = $this->reportingService->getOperatorPerformanceReport($startDate, $endDate, $userId);

        if ($request->ajax()) {
            return response()->json($report);
        }

        $users = \App\Models\User::role('mpfm_operator')->get();
        return view('reports.operator_performance', compact('report', 'users'));
    }

    /**
     * MPFM Tests Count Report
     */
    public function mpfmTestsCount(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $groupBy = $request->input('group_by', 'date');

        $report = $this->reportingService->getMPFMTestsCountReport($startDate, $endDate, $groupBy);

        if ($request->ajax()) {
            return response()->json($report);
        }

        return view('reports.mpfm_tests_count', compact('report'));
    }

    /**
     * Tower Efficiency Report
     */
    public function towerEfficiency(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $towerId = $request->input('tower_id');

        $report = $this->reportingService->getTowerEfficiencyReport($startDate, $endDate, $towerId);

        if ($request->ajax()) {
            return response()->json($report);
        }

        $towers = \App\Models\Tower::active()->get();
        return view('reports.tower_efficiency', compact('report', 'towers'));
    }

    /**
     * Field Production Summary Report
     */
    public function fieldProductionSummary(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $oilFieldId = $request->input('oil_field_id');

        $report = $this->reportingService->getFieldProductionSummaryReport($startDate, $endDate, $oilFieldId);

        if ($request->ajax()) {
            return response()->json($report);
        }

        $oilFields = \App\Models\OilField::active()->get();
        return view('reports.field_production_summary', compact('report', 'oilFields'));
    }

    /**
     * Date and Shift Report
     */
    public function dateShift(Request $request)
    {
        $date = $request->input('date', now()->format('Y-m-d'));
        $shift = $request->input('shift');

        $report = $this->reportingService->getDateShiftReport($date, $shift);

        if ($request->ajax()) {
            return response()->json($report);
        }

        return view('reports.date_shift', compact('report'));
    }
}

