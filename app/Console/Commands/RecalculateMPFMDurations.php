<?php

namespace App\Console\Commands;

use App\Models\MPFMTest;
use Illuminate\Console\Command;
use Carbon\Carbon;

class RecalculateMPFMDurations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mpfm:recalculate-durations {test_id? : The ID of the test to recalculate (optional, all if not provided)}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Recalculate and update duration fields for MPFM tests from raw log data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $testId = $this->argument('test_id');

        if ($testId) {
            $test = MPFMTest::find($testId);
            if (!$test) {
                $this->error("❌ MPFM Test with ID {$testId} not found");
                return 1;
            }

            $this->recalculateDuration($test);
            $this->info("✅ Durations recalculated for test {$testId}");
            return 0;
        }

        // Recalculate for all tests
        $tests = MPFMTest::all();

        if ($tests->isEmpty()) {
            $this->warn("⚠️ No MPFM tests found");
            return 0;
        }

        $bar = $this->output->createProgressBar($tests->count());
        $bar->start();

        foreach ($tests as $test) {
            $this->recalculateDuration($test);
            $bar->advance();
        }

        $bar->finish();
        $this->info("\n✅ Recalculated durations for {$tests->count()} tests");
        return 0;
    }

    /**
     * Recalculate durations for a single test
     */
    private function recalculateDuration(MPFMTest $test)
    {
        try {
            // Get raw log data
            $rawLogData = $test->raw_log_data ?? [];

            if (empty($rawLogData) || !isset($rawLogData['rows'])) {
                // If no raw log data, try to calculate from timestamps
                if ($test->test_started_at && $test->test_completed_at) {
                    $durationMinutes = $test->test_started_at->diffInMinutes($test->test_completed_at);
                    $test->update([
                        'test_duration_hours' => round($durationMinutes / 60, 2),
                    ]);
                }
                return;
            }

            $rows = $rawLogData['rows'];

            if (empty($rows)) {
                return;
            }

            // Extract timestamps
            $firstRow = $rows[0] ?? [];
            $lastRow = end($rows);

            $startTime = $this->extractTimestamp($firstRow);
            $endTime = $this->extractTimestamp($lastRow);

            if (!$startTime || !$endTime) {
                return;
            }

            // Calculate duration
            $durationMinutes = $startTime->diffInMinutes($endTime);
            $durationHours = round($durationMinutes / 60, 2);

            // Calculate stable duration
            $stableDurationHours = $this->calculateStableDuration($rows, $startTime, $endTime);

            // Update test
            $test->update([
                'test_started_at' => $startTime,
                'test_completed_at' => $endTime,
                'test_duration_hours' => $durationHours,
                'test_stable_duration_hours' => $stableDurationHours,
                'test_total_data_points' => count($rows),
            ]);

        } catch (\Exception $e) {
            $this->error("Error processing test {$test->id}: " . $e->getMessage());
        }
    }

    /**
     * Extract timestamp from a log row
     */
    private function extractTimestamp(array $row): ?Carbon
    {
        $timeKeys = ['Date & Time', 'datetime', 'time', 'timestamp', 'DateTime'];

        foreach ($timeKeys as $key) {
            if (isset($row[$key]) && !empty($row[$key])) {
                return $this->parseDateTime($row[$key]);
            }
        }

        return null;
    }

    /**
     * Parse datetime string safely
     */
    private function parseDateTime(?string $dateTimeStr, Carbon $default = null): ?Carbon
    {
        if (!$dateTimeStr) {
            return $default;
        }

        $formats = [
            'Y-m-d H:i:s',
            'm/d/Y H:i:s',
            'd/m/Y H:i:s',
            'Y-m-d H:i',
            'H:i:s',
            'H:i'
        ];

        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, $dateTimeStr);
            } catch (\Exception $e) {
                continue;
            }
        }

        return $default;
    }

    /**
     * Calculate stable duration from log rows
     */
    private function calculateStableDuration(array $rows, Carbon $startTime, Carbon $endTime): ?float
    {
        if (count($rows) < 5) {
            return null;
        }

        // Try to find oil rate column
        $firstRow = $rows[0];
        $oilRateColumn = null;

        foreach (array_keys($firstRow) as $columnName) {
            if (stripos($columnName, 'qo_sc') !== false ||
                stripos($columnName, 'qo_lc') !== false ||
                stripos($columnName, 'oil_rate') !== false) {
                $oilRateColumn = $columnName;
                break;
            }
        }

        if (!$oilRateColumn) {
            // Default to 70% of total duration
            $totalMinutes = $startTime->diffInMinutes($endTime);
            return $totalMinutes ? round(($totalMinutes * 0.7) / 60, 2) : null;
        }

        // Extract oil rates
        $oilRates = [];
        $timestamps = [];

        foreach ($rows as $row) {
            if (isset($row[$oilRateColumn]) && is_numeric($row[$oilRateColumn])) {
                $oilRates[] = (float) $row[$oilRateColumn];

                $timeKey = $this->findTimeKey($row);
                if ($timeKey && isset($row[$timeKey])) {
                    $timestamp = $this->parseDateTime($row[$timeKey]);
                    if ($timestamp) {
                        $timestamps[] = $timestamp;
                    }
                }
            }
        }

        if (count($oilRates) < 5 || count($timestamps) < 5) {
            $totalMinutes = $startTime->diffInMinutes($endTime);
            return $totalMinutes ? round(($totalMinutes * 0.7) / 60, 2) : null;
        }

        // Detect stability using rolling window
        $windowSize = max(3, (int)(count($oilRates) * 0.1));
        $stabilityThreshold = 5; // 5% coefficient of variation

        $stabilityStartIndex = null;

        for ($i = $windowSize; $i < count($oilRates); $i++) {
            $window = array_slice($oilRates, $i - $windowSize, $windowSize);
            $avg = array_sum($window) / count($window);

            if ($avg == 0) continue;

            $variance = array_sum(array_map(function($x) use ($avg) {
                return pow($x - $avg, 2);
            }, $window)) / count($window);

            $stdDev = sqrt($variance);
            $cv = ($stdDev / $avg) * 100;

            if ($cv < $stabilityThreshold) {
                $stabilityStartIndex = $i;
                break;
            }
        }

        if ($stabilityStartIndex && isset($timestamps[$stabilityStartIndex])) {
            $stableStartTime = $timestamps[$stabilityStartIndex];
            $stableDurationMinutes = $stableStartTime->diffInMinutes($endTime);
            return round($stableDurationMinutes / 60, 2);
        }

        // Default fallback
        $totalMinutes = $startTime->diffInMinutes($endTime);
        return $totalMinutes ? round(($totalMinutes * 0.7) / 60, 2) : null;
    }

    /**
     * Find the time key in a row
     */
    private function findTimeKey(array $row): ?string
    {
        $timeKeys = ['Date & Time', 'datetime', 'time', 'timestamp', 'DateTime'];

        foreach ($timeKeys as $key) {
            if (isset($row[$key])) {
                return $key;
            }
        }

        return null;
    }
}
