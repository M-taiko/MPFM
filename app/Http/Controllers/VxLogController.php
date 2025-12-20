<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Models\WellTest;

class VxLogController extends Controller
{
    public function showUploadForm()
    {
        return view('logs.upload');
    }

    // public function processLogFile(Request $request)
    // {
    //     $request->validate([
    //         'logfile' => 'required|file|mimes:txt,vxlog,log|max:10240',
    //         'wellhead_tower' => 'nullable|string',
    //         'wellhead_string' => 'nullable|string',
    //         'choke_size' => 'nullable|string',
    //         'upstream_choke' => 'nullable|string',
    //         'downstream_choke' => 'nullable|string',
    //         'client' => 'nullable|string',
    //         'field' => 'nullable|string',
    //         'well' => 'nullable|string',
    //         'save_test' => 'nullable|boolean'
    //     ]);

    //     $file = $request->file('logfile');
    //     if (!$file->isValid()) {
    //         return redirect()->back()->withErrors(['logfile' => 'Invalid file upload']);
    //     }

    //     $content = file_get_contents($file->getRealPath());
    //     $lines = explode("\n", $content);
    //     $parsedData = $this->parseVxLog($lines);
    //     $calculatedMetrics = $this->calculateMetrics($parsedData);

    //     $wellheadTower = $request->input('wellhead_tower','');
    //     $wellheadString = $request->input('wellhead_string','');
    //     $chokeSize = $request->input('choke_size','N/A');
    //     $upstreamChoke = $request->input('upstream_choke','N/A');
    //     $downstreamChoke = $request->input('downstream_choke','N/A');

    //     $calculatedMetrics['choke_size'] = $chokeSize;
    //     $calculatedMetrics['upstream_choke'] = $upstreamChoke;
    //     $calculatedMetrics['downstream_choke'] = $downstreamChoke;

    //     $startTimeStr = $parsedData['test_data']['rows'][0]['Date & Time'] ?? null;
    //     $endTimeStr = end($parsedData['test_data']['rows'])['Date & Time'] ?? null;
    //     $testDate = isset($parsedData['metadata']['Test Date']) ? Carbon::parse($parsedData['metadata']['Test Date']) : Carbon::today();
    //     $startTime = $this->parseDateTimeSafe($startTimeStr, $testDate);
    //     $endTime = $this->parseDateTimeSafe($endTimeStr, $testDate);

    //     $chartData = $this->prepareChartData($parsedData);
    //     $gorChartData = $this->prepareGorChartData($parsedData);
    //     $fullAverageRow = $this->calculateFullAverage($parsedData);

    //     // Save test if requested
    //     if ($request->input('save_test', false)) {
    //         $wellTest = WellTest::create([
    //             'client' => $request->input('client','N/A'),
    //             'field' => $request->input('field','N/A'),
    //             'well' => $request->input('well','N/A'),
    //             'wellhead_tower' => $wellheadTower,
    //             'wellhead_string' => $wellheadString,
    //             'choke_size' => $chokeSize,
    //             'upstream_choke' => $upstreamChoke,
    //             'downstream_choke' => $downstreamChoke,
    //             'start_time' => $startTime,
    //             'end_time' => $endTime,
    //             'metadata' => $parsedData['metadata'],
    //             'test_data' => $parsedData['test_data'],
    //             'calculated_metrics' => $calculatedMetrics,
    //             'filename' => $file->getClientOriginalName()
    //         ]);

    //         return redirect()->route('well_tests.show', $wellTest->id)
    //             ->with('success', 'Well test saved successfully!');
    //     }

    //     return view('logs.results', [
    //         'parsedData' => $parsedData,
    //         'calculatedMetrics' => $calculatedMetrics,
    //         'chartData' => $chartData,
    //         'gorChartData' => $gorChartData,
    //         'filename' => $file->getClientOriginalName(),
    //         'wellheadTower' => $wellheadTower,
    //         'wellheadString' => $wellheadString,
    //         'startTime' => $startTime,
    //         'endTime' => $endTime,
    //         'chokeSize' => $chokeSize,
    //         'upstreamChoke' => $upstreamChoke,
    //         'downstreamChoke' => $downstreamChoke,
    //         'wellTest' => (object)[
    //             'client' => $request->input('client','N/A'),
    //             'field' => $request->input('field','N/A'),
    //             'well' => $request->input('well','N/A'),
    //         ],
    //         'fullAverageRow' => $fullAverageRow
    //     ]);
    // }

    public function processLogFile(Request $request)
    {
        $request->validate([
            'logfile' => 'required|file|mimes:txt,vxlog,log|max:10240',
            'wellhead_tower' => 'nullable|string',
            'wellhead_string' => 'nullable|string',
            'choke_size' => 'nullable|string',
            'upstream_choke' => 'nullable|string',
            'downstream_choke' => 'nullable|string',
            'client' => 'nullable|string',
            'field' => 'nullable|string',
            'well' => 'nullable|string',
        ]);

        $file = $request->file('logfile');
        if (!$file->isValid()) {
            return redirect()->back()->withErrors(['logfile' => 'Invalid file upload']);
        }

        // === حفظ الملف في مجلد مؤقت ===
        $fileName = time() . '_' . $file->getClientOriginalName();
        $tempFilePath = $file->storeAs('temp', $fileName, 'public');
        // === نهاية حفظ الملف المؤقت ===

        $content = file_get_contents($file->getRealPath());

        // === إصلاح مشكلة الترميز ===
        $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
        if ($encoding !== 'UTF-8') {
            $content = mb_convert_encoding($content, 'UTF-8', $encoding);
        }
        // === نهاية إصلاح الترميز ===

        $lines = explode("\n", $content);
        $parsedData = $this->parseVxLog($lines);
        $calculatedMetrics = $this->calculateMetrics($parsedData);

        $wellheadTower = $request->input('wellhead_tower','');
        $wellheadString = $request->input('wellhead_string','');
        $chokeSize = $request->input('choke_size','N/A');
        $upstreamChoke = $request->input('upstream_choke','N/A');
        $downstreamChoke = $request->input('downstream_choke','N/A');

        $calculatedMetrics['choke_size'] = $chokeSize;
        $calculatedMetrics['upstream_choke'] = $upstreamChoke;
        $calculatedMetrics['downstream_choke'] = $downstreamChoke;

        $startTimeStr = $parsedData['test_data']['rows'][0]['Date & Time'] ?? null;
        $endTimeStr = end($parsedData['test_data']['rows'])['Date & Time'] ?? null;
        $testDate = isset($parsedData['metadata']['Test Date']) ? Carbon::parse($parsedData['metadata']['Test Date']) : Carbon::today();
        $startTime = $this->parseDateTimeSafe($startTimeStr, $testDate);
        $endTime = $this->parseDateTimeSafe($endTimeStr, $testDate);

        $chartData = $this->prepareChartData($parsedData);
        $gorChartData = $this->prepareGorChartData($parsedData);
        $fullAverageRow = $this->calculateFullAverage($parsedData);

        // === عرض صفحة النتائج مع جميع البيانات والمسار المؤقت للملف ===
        return view('logs.results', [
            'parsedData' => $parsedData,
            'calculatedMetrics' => $calculatedMetrics,
            'chartData' => $chartData,
            'gorChartData' => $gorChartData,
            'filename' => $file->getClientOriginalName(),
            'wellheadTower' => $wellheadTower,
            'wellheadString' => $wellheadString,
            'startTime' => $startTime,
            'endTime' => $endTime,
            'chokeSize' => $chokeSize,
            'upstreamChoke' => $upstreamChoke,
            'downstreamChoke' => $downstreamChoke,
            'wellTest' => (object)[
                'client' => $request->input('client','N/A'),
                'field' => $request->input('field','N/A'),
                'well' => $request->input('well','N/A'),
            ],
            'fullAverageRow' => $fullAverageRow,
            'tempFilePath' => $tempFilePath // تمرير المسار المؤقت للملف
        ]);
    }

    /**
     * دالة جديدة لحفظ الاختبار بشكل نهائي في قاعدة البيانات.
     */
  public function storeTest(Request $request)
{
    $request->validate([
        'client' => 'required|string',
        'field' => 'required|string',
        'well' => 'required|string',
        'temp_file_path' => 'required|string',
    ]);

    $data = $request->all();
    $tempFilePath = $data['temp_file_path'];

    // === نقل الملف من المجلد المؤقت إلى الدائم ===
    $permanentPath = null;
    if (Storage::disk('public')->exists($tempFilePath)) {
        $newFileName = basename($tempFilePath);
        $permanentPath = 'logs/' . $newFileName;
        Storage::disk('public')->move($tempFilePath, $permanentPath);
    } else {
        // إذا لم يتم العثور على الملف المؤقت، أرجع خطأ
        return redirect()->back()->with('error', 'Temporary file not found. Could not save test.');
    }
    // === نهاية نقل الملف ===

    // === إنشاء سجل الاختبار في قاعدة البيانات مع معالجة أفضل لـ JSON ===
    try {
        $wellTest = WellTest::create([
            'client' => $data['client'],
            'field' => $data['field'],
            'well' => $data['well'],
            'wellhead_tower' => $data['wellhead_tower'],
            'wellhead_string' => $data['wellhead_string'],
            'choke_size' => $data['choke_size'],
            'upstream_choke' => $data['upstream_choke'],
            'downstream_choke' => $data['downstream_choke'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'metadata' => json_decode($data['metadata'], true),
            'test_data' => json_decode($data['test_data'], true),
            'calculated_metrics' => json_decode($data['calculated_metrics'], true),
            'filename' => $data['filename'],
            'log_file_path' => $permanentPath
        ]);
    } catch (\Exception $e) {
        // في حالة فشل حفظ قاعدة البيانات، احذف الملف المنقول وأرجع خطأ
        if ($permanentPath && Storage::disk('public')->exists($permanentPath)) {
            Storage::disk('public')->delete($permanentPath);
        }
        return redirect()->back()->with('error', 'Failed to save test data to the database: ' . $e->getMessage());
    }
    // === نهاية إنشاء السجل ===

    return redirect()->route('well_tests.show', $wellTest->id)
        ->with('success', 'Well test saved successfully!');
}


    public function index()
    {
        $wellTests = WellTest::orderBy('created_at', 'desc')->paginate(10);
        return view('logs.index', compact('wellTests'));
    }

    public function show($id)
    {
        $wellTest = WellTest::findOrFail($id);

        // Prepare chart data from saved test
        $chartData = $this->prepareChartDataFromSaved($wellTest);
        $gorChartData = $this->prepareGorChartDataFromSaved($wellTest);

        return view('logs.saved_results', [
            'wellTest' => $wellTest,
            'chartData' => $chartData,
            'gorChartData' => $gorChartData,
            'fullAverageRow' => $this->calculateFullAverageFromSaved($wellTest)
        ]);
    }

    private function prepareChartDataFromSaved($wellTest)
    {
        $chartData = ['labels'=>[],'datasets'=>[]];
        if (!empty($wellTest->test_data['rows'])) {
            $labels=[]; $oilRates=[]; $waterRates=[]; $gasRates=[];
            foreach ($wellTest->test_data['rows'] as $row) {
                $labels[] = $row['Date & Time'] ?? 'N/A';
                $oilRates[] = $this->parseNumeric($row['qo_lc[bbl/d]'] ?? 0);
                $waterRates[] = $this->parseNumeric($row['qw_lc[bbl/d]'] ?? 0);
                $gasRates[] = $this->parseNumeric($row['qg_lc[Mcf/d]'] ?? 0);
            }
            $chartData['labels']=$labels;
            $chartData['datasets']=[
                ['label'=>'Oil Rate','data'=>$oilRates,'borderColor'=>'green','tension'=>0.2],
                ['label'=>'Water Rate','data'=>$waterRates,'borderColor'=>'blue','tension'=>0.2],
                ['label'=>'Gas Rate','data'=>$gasRates,'borderColor'=>'red','tension'=>0.2],
            ];
        }
        return $chartData;
    }

    private function prepareGorChartDataFromSaved($wellTest)
    {
        $chartData = ['labels'=>[],'datasets'=>[]];
        if (!empty($wellTest->test_data['rows'])) {
            $labels=[]; $gorValues=[];
            foreach ($wellTest->test_data['rows'] as $row) {
                $labels[] = $row['Date & Time'] ?? 'N/A';
                $gorValues[] = $this->parseNumeric($row['GOR[Scf/STB]'] ?? 0);
            }
            $chartData['labels']=$labels;
            $chartData['datasets']=[
                ['label'=>'GOR','data'=>$gorValues,'borderColor'=>'purple','tension'=>0.2],
            ];
        }
        return $chartData;
    }

    private function calculateFullAverageFromSaved($wellTest)
    {
        $avgRow = [];
        if(!empty($wellTest->test_data['rows'])){
            $columns = $wellTest->test_data['columns'];
            foreach($columns as $col){
                $values = array_map(fn($r)=>$this->parseNumeric($r[$col]??0), $wellTest->test_data['rows']);
                $avgRow[$col] = count($values)?array_sum($values)/count($values):'#N/A';
            }
        }
        return $avgRow;
    }

    private function prepareGorChartData($parsedData)
    {
        $chartData = ['labels'=>[],'datasets'=>[]];
        if (!empty($parsedData['test_data']['rows'])) {
            $labels=[]; $gorValues=[];
            foreach ($parsedData['test_data']['rows'] as $row) {
                $labels[] = $row['Date & Time'] ?? 'N/A';
                $gorValues[] = $this->parseNumeric($row['GOR[Scf/STB]'] ?? 0);
            }
            $chartData['labels']=$labels;
            $chartData['datasets']=[
                ['label'=>'GOR','data'=>$gorValues,'borderColor'=>'purple','tension'=>0.2],
            ];
        }
        return $chartData;
    }

    // Rest of your existing methods...
    private function parseDateTimeSafe($dateTimeStr, $defaultDate)
    {
        if (!$dateTimeStr) return null;
        try {
            if (preg_match('/^\d{4}-\d{2}-\d{2}/', $dateTimeStr)) {
                return Carbon::parse($dateTimeStr);
            } else {
                return Carbon::parse($defaultDate->format('Y-m-d') . ' ' . $dateTimeStr);
            }
        } catch (\Exception $e) {
            return null;
        }
    }

    private function parseVxLog($lines)
    {
        $data = ['metadata' => [], 'test_data' => ['columns' => [], 'rows' => []], 'notes' => []];
        $currentSection = '';
        $columnHeaders = [];
        $metadataKeys = ['DAFC Serial Number','DAFC HW Version','DAFC SW Version','DAFC FW Version','MCA Build Date','GD Serial Number','Configuration name','Test Date'];

        foreach ($lines as $lineNum => $line) {
            $line = trim($line);
            if (empty($line)) continue;

            foreach ($metadataKeys as $key) {
                if (strpos($line,$key)!==false) {
                    $parts = preg_split('/\s{2,}/', $line, 2);
                    $data['metadata'][$key] = $parts[1] ?? trim($lines[$lineNum+1] ?? '');
                    continue 2;
                }
            }

            if (strpos($line,'Date & Time')!==false) {
                $currentSection='test_data';
                $columnHeaders = preg_split('/\t|\s{2,}/', $line);
                $data['test_data']['columns'] = array_filter(array_map('trim',$columnHeaders));
                continue;
            }

            if (!empty($data['test_data']['columns'])) {
                if (preg_match('/\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}:\d{2}/',$line) || preg_match('/\d+-\d{2}:\d{2}/',$line)) {
                    $values = preg_split('/\t|\s{2,}/', $line);
                    $row = [];
                    foreach ($data['test_data']['columns'] as $i=>$col) {
                        $row[$col] = $values[$i] ?? '';
                    }
                    $data['test_data']['rows'][] = $row;
                }
            }

            if (strpos($line,'NOTES:')!==false) {
                $currentSection='notes';
                $data['notes'][]=$line;
                continue;
            }

            if ($currentSection==='notes') $data['notes'][]=$line;
        }

        return $data;
    }

    private function parseNumeric($value)
    {
        if (is_null($value) || $value==='' || stripos($value,'N/A')!==false || stripos($value,'#VALUE!')!==false || stripos($value,'#DIV/0!')!==false) return 0;
        return floatval(preg_replace('/[^\d\.\-]/','',$value));
    }

    private function calculateMetrics($parsedData)
    {
        $metrics = [
            'oil_rate_avg'=>0,'water_rate_avg'=>0,'gas_rate_avg'=>0,
            'wlr_avg'=>0,'gvf_avg'=>0,'bsw_avg'=>0,'total_gor_avg'=>0,
            'whp_avg'=>0,'temp_avg'=>0,'press_avg'=>0
        ];

        if (!empty($parsedData['test_data']['rows'])) {
            $oil=$water=$gas=$wlr=$gvf=$bsw=$gor=$whp=$temp=$press=[];
            foreach ($parsedData['test_data']['rows'] as $row) {
                $oil[] = $this->parseNumeric($row['qo_lc[bbl/d]'] ?? 0);
                $water[] = $this->parseNumeric($row['qw_lc[bbl/d]'] ?? 0);
                $gas[] = $this->parseNumeric($row['qg_lc[Mcf/d]'] ?? 0);
                $wlr[] = $this->parseNumeric($row['WLR[%]'] ?? 0);
                $gvf[] = $this->parseNumeric($row['GVF[%]'] ?? 0);
                $bsw[] = $this->parseNumeric($row['BSW[%]'] ?? 0);
                $gor[] = $this->parseNumeric($row['GOR[Scf/STB]'] ?? 0);
                $whp[] = $this->parseNumeric($row['WHP[psi]'] ?? 0);
                $temp[] = $this->parseNumeric($row['Temp[F]'] ?? 0);
                $press[] = $this->parseNumeric($row['Press[psi]'] ?? 0);
            }

            $calcAvg = fn($arr)=>count($arr)?array_sum($arr)/count($arr):0;
            $metrics['oil_rate_avg']=$calcAvg($oil);
            $metrics['water_rate_avg']=$calcAvg($water);
            $metrics['gas_rate_avg']=$calcAvg($gas);
            $metrics['wlr_avg']=$calcAvg($wlr);
            $metrics['gvf_avg']=$calcAvg($gvf);
            $metrics['bsw_avg']=$calcAvg($bsw);
            $metrics['total_gor_avg']=$calcAvg($gor);
            $metrics['whp_avg']=$calcAvg($whp);
            $metrics['temp_avg']=$calcAvg($temp);
            $metrics['press_avg']=$calcAvg($press);
        }

        return $metrics;
    }

    private function prepareChartData($parsedData)
    {
        $chartData = ['labels'=>[],'datasets'=>[]];
        if (!empty($parsedData['test_data']['rows'])) {
            $labels=[]; $oilRates=[]; $waterRates=[]; $gasRates=[];
            foreach ($parsedData['test_data']['rows'] as $row) {
                $labels[] = $row['Date & Time'] ?? 'N/A';
                $oilRates[] = $this->parseNumeric($row['qo_lc[bbl/d]'] ?? 0);
                $waterRates[] = $this->parseNumeric($row['qw_lc[bbl/d]'] ?? 0);
                $gasRates[] = $this->parseNumeric($row['qg_lc[Mcf/d]'] ?? 0);
            }
            $chartData['labels']=$labels;
            $chartData['datasets']=[
                ['label'=>'Oil Rate','data'=>$oilRates,'borderColor'=>'green','tension'=>0.2],
                ['label'=>'Water Rate','data'=>$waterRates,'borderColor'=>'blue','tension'=>0.2],
                ['label'=>'Gas Rate','data'=>$gasRates,'borderColor'=>'red','tension'=>0.2],
            ];
        }
        return $chartData;
    }

    private function calculateFullAverage($parsedData)
    {
        $avgRow = [];
        if(!empty($parsedData['test_data']['rows'])){
            $columns = $parsedData['test_data']['columns'];
            foreach($columns as $col){
                $values = array_map(fn($r)=>$this->parseNumeric($r[$col]??0), $parsedData['test_data']['rows']);
                $avgRow[$col] = count($values)?array_sum($values)/count($values):'#N/A';
            }
        }
        return $avgRow;
    }
}
