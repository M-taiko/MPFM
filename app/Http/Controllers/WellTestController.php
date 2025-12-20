<?php

namespace App\Http\Controllers;

use App\Models\WellTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class WellTestController extends Controller
{
    /**
     * Display listing page
     */
    public function index(Request $request)
    {
        if (!$request->ajax()) {
            $fields  = WellTest::distinct()->pluck('field')->filter()->sort();
            $clients = WellTest::distinct()->pluck('client')->filter()->sort();

            return view('well_tests.index', compact('fields', 'clients'));
        }

        return $this->getDatatables($request);
    }

    /**
     * Datatables AJAX
     */
    public function getDatatables(Request $request)
    {
        $query = WellTest::query();

        if ($request->filled('field')) {
            $query->where('field', 'like', '%' . $request->field . '%');
        }

        if ($request->filled('client')) {
            $query->where('client', 'like', '%' . $request->client . '%');
        }

        return DataTables::eloquent($query)
            ->editColumn('created_at', fn ($w) =>
                Carbon::parse($w->created_at)->format('Y-m-d H:i')
            )
            ->addColumn('display_name', fn ($w) =>
                $w->well . ' - ' . $w->client
            )
            ->addColumn('action', fn ($w) =>
                "<a href='" . route('well_tests.show', $w->id) . "' class='btn btn-sm btn-primary'>View</a>"
            )
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Show single well test
     */
    public function show(WellTest $wellTest)
    {
        $chartData       = $this->prepareChartDataFromSaved($wellTest);
        $gorChartData    = $this->prepareGorChartDataFromSaved($wellTest);
        $fullAverageRow  = $this->calculateFullAverageFromSaved($wellTest);

        $logContent = null;
        if ($wellTest->log_file_path && Storage::disk('public')->exists($wellTest->log_file_path)) {
            $logContent = Storage::disk('public')->get($wellTest->log_file_path);
        }

        return view('well_tests.show', compact(
            'wellTest',
            'chartData',
            'gorChartData',
            'fullAverageRow',
            'logContent'
        ));
    }

    /**
     * Compare
     */
 public function compare(Request $request)
{
    $testIds = $request->input('test_ids', []);

    if (count($testIds) < 2) {
        return back()->with('error', 'Please select at least two tests.');
    }

    $tests = WellTest::whereIn('id', $testIds)->get();

    $comparisonData = $tests->map(fn ($t) => [
        'id'         => $t->id,
        'client'     => $t->client,
        'field'      => $t->field,
        'well'       => $t->well,
        'start_time' => optional($t->start_time)->format('Y-m-d H:i'),
        'end_time'   => optional($t->end_time)->format('Y-m-d H:i'),
        'metadata'   => $t->metadata ?? [],
        'test_data'  => $t->test_data ?? [],
        'metrics'    => $t->calculated_metrics ?? [],
    ]);

    return view('well_tests.compare-results', compact('comparisonData'));
}
    /* ===================== Helpers ===================== */

    private function prepareChartDataFromSaved($wellTest)
    {
        $labels = $oil = $water = $gas = [];

        foreach ($wellTest->test_data['rows'] ?? [] as $row) {
            $labels[] = $row['Date & Time'] ?? 'N/A';
            $oil[]    = $this->parseNumeric($row['qo_lc[bbl/d]'] ?? 0);
            $water[]  = $this->parseNumeric($row['qw_lc[bbl/d]'] ?? 0);
            $gas[]    = $this->parseNumeric($row['qg_lc[Mcf/d]'] ?? 0);
        }

        return [
            'labels' => $labels,
            'datasets' => [
                ['label' => 'Oil Rate',   'data' => $oil,   'borderColor' => 'green',  'tension' => 0.2],
                ['label' => 'Water Rate', 'data' => $water, 'borderColor' => 'blue',   'tension' => 0.2],
                ['label' => 'Gas Rate',   'data' => $gas,   'borderColor' => 'red',    'tension' => 0.2],
            ]
        ];
    }

    private function prepareGorChartDataFromSaved($wellTest)
    {
        $labels = $gor = [];

        foreach ($wellTest->test_data['rows'] ?? [] as $row) {
            $labels[] = $row['Date & Time'] ?? 'N/A';
            $gor[]    = $this->parseNumeric($row['GOR[Scf/STB]'] ?? 0);
        }

        return [
            'labels' => $labels,
            'datasets' => [
                ['label' => 'GOR', 'data' => $gor, 'borderColor' => 'purple', 'tension' => 0.2]
            ]
        ];
    }

    private function calculateFullAverageFromSaved($wellTest)
    {
        $avg = [];

        foreach ($wellTest->test_data['columns'] ?? [] as $col) {
            $values = array_map(
                fn ($r) => $this->parseNumeric($r[$col] ?? 0),
                $wellTest->test_data['rows'] ?? []
            );

            // لو القيم array (بسبب parseNumeric الجديد)، حولها لمجموعها أو متوسطها
            $values = array_map(fn($v) => is_array($v) ? array_sum($v) : $v, $values);

            $avg[$col] = count($values)
                ? round(array_sum($values) / count($values), 2)
                : 0;
        }

        return $avg;
    }

    private function parseNumeric($value)
    {
        // لو القيمة array، طبق parseNumeric على كل عنصر فيها
        if (is_array($value)) {
            return array_map(fn($v) => $this->parseNumeric($v), $value);
        }

        // لو القيمة null أو فارغة أو تحتوي على أحد الكلمات
        // if (!$value || str_contains((string)$value, ['N/A', '#DIV', '#VALUE'])) {
        //     return 0;
        // }

        return (float) preg_replace('/[^\d.\-]/', '', $value);
    }
}
