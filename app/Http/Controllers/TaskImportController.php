<?php

namespace App\Http\Controllers;

use App\Imports\TasksImport;
use App\Models\Department;
use App\Models\MonthlyPlan;
use App\Models\OilString;
use App\Models\Tower;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class TaskImportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the import page
     */
    public function index()
    {
        $departments  = Department::active()->orderBy('name')->get(['id', 'name']);
        $monthlyPlans = MonthlyPlan::orderByDesc('created_at')->get(['id', 'plan_name', 'plan_month']);

        return view('tasks.import', compact('departments', 'monthlyPlans'));
    }

    /**
     * Handle the Excel file upload and import
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ], [
            'excel_file.required' => 'Please select an Excel file to upload.',
            'excel_file.mimes'    => 'Only .xlsx, .xls, and .csv files are allowed.',
            'excel_file.max'      => 'File size must not exceed 5MB.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $file        = $request->file('excel_file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet       = $spreadsheet->getActiveSheet();
            $rows        = $sheet->toArray(null, true, true, false);

            if (count($rows) < 2) {
                return back()->with('import_error', 'The file appears to be empty or has no data rows.');
            }

            // Extract header row and map column positions
            $headers = array_map(fn($h) => strtolower(trim((string)$h)), $rows[0]);
            $importer = new TasksImport();
            $normalizedHeaders = $importer->normalizeHeaders($rows[0]);

            // Build associative rows
            $dataRows = [];
            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                // Skip fully empty rows
                if (empty(array_filter(array_map('strval', $row)))) {
                    continue;
                }
                $assoc = [];
                foreach ($normalizedHeaders as $colIdx => $key) {
                    $assoc[$key] = isset($row[$colIdx]) ? trim((string)$row[$colIdx]) : '';
                }
                $dataRows[] = $assoc;
            }

            if (empty($dataRows)) {
                return back()->with('import_error', 'No valid data rows found in the file.');
            }

            $importer->import($dataRows);

            return back()->with([
                'import_success'       => true,
                'import_success_count' => $importer->successCount,
                'import_fail_count'    => $importer->failCount,
                'import_errors'        => $importer->errors,
                'import_imported'      => $importer->imported,
            ]);

        } catch (\Exception $e) {
            return back()->with('import_error', 'Failed to read the file: ' . $e->getMessage());
        }
    }

    /**
     * Download the Excel template
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();

        // ===== Sheet 1: Template =====
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Tasks Template');

        // Header row styling
        $headers = [
            'A1' => 'title',
            'B1' => 'description',
            'C1' => 'notes',
            'D1' => 'monthly_plan',
            'E1' => 'department',
            'F1' => 'task_type',
            'G1' => 'priority',
            'H1' => 'assigned_to',
            'I1' => 'expected_completion_at',
            'J1' => 'oil_string',
            'K1' => 'tower',
        ];

        $headerLabels = [
            'A1' => 'Title *',
            'B1' => 'Description',
            'C1' => 'Notes',
            'D1' => 'Monthly Plan *',
            'E1' => 'Department *',
            'F1' => 'Task Type *',
            'G1' => 'Priority *',
            'H1' => 'Assigned To',
            'I1' => 'Expected Completion',
            'J1' => 'Oil String',
            'K1' => 'Tower',
        ];

        foreach ($headerLabels as $cell => $label) {
            $sheet->setCellValue($cell, $label);
        }

        // Header styling
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1D4ED8']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FFFFFF']]],
        ];
        $sheet->getStyle('A1:K1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(28);

        // Required column highlighting
        $requiredStyle = [
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A8A']],
        ];
        foreach (['A1', 'D1', 'E1', 'F1', 'G1'] as $cell) {
            $sheet->getStyle($cell)->applyFromArray($requiredStyle);
        }

        // Add 5 sample rows
        $sampleRows = [
            ['Monthly MPFM Calibration - Well A-12', 'Calibrate MPFM sensors on well A-12', 'Requires shutdown window', 'Plan Jan 2026', 'Measurement', 'plan_task', 'high', '', '2026-02-15 08:00', 'String-A', 'Tower-1'],
            ['Daily Flow Test - Station B', 'Routine daily flow verification', '', 'Plan Jan 2026', 'Operations', 'daily_plan', 'medium', '', '', '', ''],
            ['Operator Inspection Round', 'Field operator visual inspection', 'Check valve positions', 'Plan Jan 2026', 'Production', 'operator_plan', 'low', '', '', '', ''],
        ];

        foreach ($sampleRows as $i => $row) {
            $rowNum = $i + 2;
            $sheet->fromArray($row, null, 'A' . $rowNum);

            // Zebra striping
            $bg = $i % 2 === 0 ? 'F8FAFC' : 'FFFFFF';
            $sheet->getStyle('A' . $rowNum . ':K' . $rowNum)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
            ]);
        }

        // Column widths
        $colWidths = ['A' => 40, 'B' => 35, 'C' => 25, 'D' => 20, 'E' => 20, 'F' => 16, 'G' => 12, 'H' => 22, 'I' => 22, 'J' => 18, 'K' => 15];
        foreach ($colWidths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        // ===== Sheet 2: Reference Data =====
        $refSheet = $spreadsheet->createSheet();
        $refSheet->setTitle('Reference Data');

        // Headers for ref sheet
        $refHeaders = ['A' => 'Monthly Plans', 'B' => 'Departments', 'C' => 'Task Types', 'D' => 'Priority Levels', 'E' => 'Date Format'];
        foreach ($refHeaders as $col => $label) {
            $refSheet->setCellValue($col . '1', $label);
            $refSheet->getStyle($col . '1')->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            $refSheet->getColumnDimension($col)->setWidth(25);
        }
        $refSheet->getRowDimension(1)->setRowHeight(24);

        // Populate reference data
        $plans = MonthlyPlan::orderBy('plan_name')->pluck('plan_name')->toArray();
        $depts = Department::active()->orderBy('name')->pluck('name')->toArray();
        $taskTypes = ['plan_task', 'daily_plan', 'operator_plan'];
        $priorities = ['low', 'medium', 'high', 'critical'];
        $dateFormats = ['YYYY-MM-DD HH:MM', 'Example:', '2026-03-15 08:00'];

        $maxRows = max(count($plans), count($depts), count($taskTypes), count($priorities), count($dateFormats));

        for ($r = 0; $r < $maxRows; $r++) {
            $rowNum = $r + 2;
            if (isset($plans[$r]))       $refSheet->setCellValue('A' . $rowNum, $plans[$r]);
            if (isset($depts[$r]))       $refSheet->setCellValue('B' . $rowNum, $depts[$r]);
            if (isset($taskTypes[$r]))   $refSheet->setCellValue('C' . $rowNum, $taskTypes[$r]);
            if (isset($priorities[$r]))  $refSheet->setCellValue('D' . $rowNum, $priorities[$r]);
            if (isset($dateFormats[$r])) $refSheet->setCellValue('E' . $rowNum, $dateFormats[$r]);

            $bg = $r % 2 === 0 ? 'F0FDF4' : 'FFFFFF';
            $refSheet->getStyle('A' . $rowNum . ':E' . $rowNum)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
            ]);
        }

        // Set active sheet back to template
        $spreadsheet->setActiveSheetIndex(0);

        // Output
        $writer = new Xlsx($spreadsheet);
        $filename = 'tasks_import_template_' . date('Ymd') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }
}
