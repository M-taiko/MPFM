<?php

namespace App\Services\Wireline;

use Carbon\Carbon;
use InvalidArgumentException;

/**
 * Parses memory-gauge exports (Kuster / Quantum Quartz style).
 *
 *  .log : "key=value" header, "[Data]" marker, "POR, <excel serial>, <interval s>", then "p, t, internalT" rows
 *  .csv : text header, a column-title line, then fixed-width rows
 *         "mm/dd/yyyy hh:mm:ss<delta h><pressure><temp><internal temp>" (no delimiters)
 *
 * Returns ['meta' => [...], 'readings' => [[t_sec, pressure, temperature, internal_temp], ...]]
 */
class GaugeLogParser
{
    public function parse(string $contents, string $filename = ''): array
    {
        $contents = preg_replace('/^\xEF\xBB\xBF/', '', $contents);
        $lines = preg_split('/\r\n|\r|\n/', $contents);

        if (preg_grep('/^\s*\[Data\]\s*$/i', $lines)) {
            return $this->parseLog($lines);
        }
        if (preg_grep('/^\s*\d{2}\/\d{2}\/\d{4}\s+\d{2}:\d{2}:\d{2}-?\d/', $lines)) {
            return $this->parseFixedWidth($lines);
        }

        throw new InvalidArgumentException('Unrecognised gauge file format. Expected a memory-gauge .log or .csv export.');
    }

    private function parseLog(array $lines): array
    {
        $meta = [];
        $i = 0;
        $n = count($lines);

        for (; $i < $n; $i++) {
            $line = trim($lines[$i]);
            if (strcasecmp($line, '[Data]') === 0) {
                $i++;
                break;
            }
            if (str_contains($line, '=')) {
                [$k, $v] = explode('=', $line, 2);
                $meta[trim($k)] = trim($v);
            }
        }

        $interval = 15;
        $startedAt = null;
        $rows = [];

        for (; $i < $n; $i++) {
            $line = trim($lines[$i]);
            if ($line === '') {
                continue;
            }
            $parts = array_map('trim', explode(',', $line));

            if (strtoupper($parts[0]) === 'POR') {
                // POR, <excel serial date>, <sample interval seconds>
                if (isset($parts[1]) && is_numeric($parts[1])) {
                    $startedAt = $this->excelSerialToCarbon((float) $parts[1]);
                }
                if (isset($parts[2]) && is_numeric($parts[2]) && (int) $parts[2] > 0) {
                    $interval = (int) $parts[2];
                }
                continue;
            }

            if (count($parts) < 2 || !is_numeric($parts[0]) || !is_numeric($parts[1])) {
                continue;
            }
            $rows[] = [
                (float) $parts[0],
                (float) $parts[1],
                isset($parts[2]) && is_numeric($parts[2]) ? (float) $parts[2] : null,
            ];
        }

        if (!$rows) {
            throw new InvalidArgumentException('No data rows found in the gauge file.');
        }

        $readings = [];
        foreach ($rows as $idx => [$p, $t, $it]) {
            $readings[] = [$idx * $interval, $p, $t, $it];
        }

        return [
            'meta' => [
                'gauge_serial'        => $meta['SerialNumber'] ?? null,
                'gauge_model'         => $meta['ModelNumber'] ?? null,
                'gauge_manufacturer'  => null,
                'calibration_date'    => $this->parseUsDate($meta['CalibrationDate'] ?? null),
                'max_range'           => isset($meta['MaximumPressure']) ? (float) $meta['MaximumPressure'] : null,
                'pressure_unit'       => 'psig',
                'temperature_unit'    => 'degC',
                'sample_interval_sec' => $interval,
                'started_at'          => $startedAt,
            ],
            'readings' => $readings,
        ];
    }

    private function parseFixedWidth(array $lines): array
    {
        $meta = [];
        $rows = [];
        $re = '/^\s*(\d{2})\/(\d{2})\/(\d{4})\s+(\d{2}):(\d{2}):(\d{2})(\d+\.\d{6})(-?\d+\.\d{3})(-?\d+\.\d{3})(?:(-?\d+\.\d{3}))?/';

        foreach ($lines as $line) {
            if (preg_match($re, $line, $m)) {
                $rows[] = [
                    Carbon::create((int) $m[3], (int) $m[1], (int) $m[2], (int) $m[4], (int) $m[5], (int) $m[6]),
                    (float) $m[8],
                    (float) $m[9],
                    isset($m[10]) && $m[10] !== '' ? (float) $m[10] : null,
                ];
            } elseif (str_contains($line, ':') && !$rows) {
                [$k, $v] = array_map('trim', explode(':', $line, 2));
                $meta[$k] = $v;
            }
        }

        if (!$rows) {
            throw new InvalidArgumentException('No data rows found in the gauge file.');
        }

        $start = $rows[0][0];
        $interval = count($rows) > 1 ? max(1, (int) $start->diffInSeconds($rows[1][0], true)) : 15;

        $readings = [];
        foreach ($rows as [$ts, $p, $t, $it]) {
            $readings[] = [(int) $start->diffInSeconds($ts, true), $p, $t, $it];
        }

        return [
            'meta' => [
                'gauge_serial'        => $meta['Gauge Serial Number'] ?? null,
                'gauge_model'         => $meta['Gauge Model Number'] ?? null,
                'gauge_manufacturer'  => $meta['Gauge Manufacturer'] ?? null,
                'calibration_date'    => $this->parseUsDate($meta['Date of Last Calibration'] ?? null),
                'max_range'           => isset($meta['Maximum Recorder Range']) ? (float) $meta['Maximum Recorder Range'] : null,
                'pressure_unit'       => strtolower($meta['Pressure Units'] ?? 'psig'),
                'temperature_unit'    => 'degC',
                'sample_interval_sec' => $interval,
                'started_at'          => $start,
            ],
            'readings' => $readings,
        ];
    }

    /** Excel 1900 serial (days) -> naive wall-clock Carbon, as recorded by the gauge PC. */
    private function excelSerialToCarbon(float $serial): Carbon
    {
        $unix = (int) round(($serial - 25569) * 86400);
        return Carbon::create(1970, 1, 1, 0, 0, 0)->addSeconds($unix);
    }

    private function parseUsDate(?string $value): ?string
    {
        if (!$value || !preg_match('#^(\d{1,2})/(\d{1,2})/(\d{4})$#', trim($value), $m)) {
            return null;
        }
        return sprintf('%04d-%02d-%02d', $m[3], $m[1], $m[2]);
    }
}
