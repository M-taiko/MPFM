<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LogFileController extends Controller
{
    /**
     * Show the form for uploading log files.
     */
    public function showUploadForm()
    {
        return view('logs.upload');
    }

    /**
     * Handle log file upload.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'logfile' => 'required|file|mimes:txt,vxlog|max:10240', // 10MB max
        ]);

        $file = $request->file('logfile');
        $filename = time() . '_' . $file->getClientOriginalName();

        // Store the file
        $path = $file->storeAs('logfiles', $filename, 'public');

        // Process the file to extract well testing data
        $data = $this->processLogFile(storage_path('app/public/' . $path));

        return view('logs.results', [
            'data' => $data,
            'filename' => $filename
        ]);
    }

    /**
     * Process the log file and extract well testing data.
     */
    private function processLogFile($filepath)
    {
        $content = file_get_contents($filepath);
        $lines = explode("\n", $content);

        // Parse the header line (column names)
        $headers = [];
        if (isset($lines[0])) {
            $headers = preg_split('/\s+/', trim($lines[0]));
        }

        // Parse data lines
        $data = [];
        for ($i = 1; $i < min(101, count($lines)); $i++) { // Limit to first 100 data rows
            if (isset($lines[$i])) {
                $values = preg_split('/\s+/', trim($lines[$i]));
                $row = [];
                foreach ($headers as $index => $header) {
                    $row[$header] = isset($values[$index]) ? $values[$index] : '';
                }
                $data[] = $row;
            }
        }

        return [
            'headers' => $headers,
            'data' => $data
        ];
    }
}
