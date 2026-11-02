<?php

namespace App\Services;

use App\Models\MPFMTest;
use App\Models\OilString;
use App\Models\User;
use App\Models\WellTest;
use Illuminate\Support\Carbon;

/**
 * Every archived well test should also be an MPFM test, so it shows up in the MPFM Tests list and the
 * review workflow. Tests uploaded with a task get their MPFM test from the task flow; manual uploads
 * only produced a WellTest, which is why they never appeared on /mpfm-tests.
 */
class WellTestMpfmSync
{
    /** Create the MPFM test for a well test. Returns null when it already exists or the oil string can't be resolved. */
    public function create(WellTest $wellTest, ?User $user = null): ?MPFMTest
    {
        if (MPFMTest::where('well_test_id', $wellTest->id)->exists()) {
            return null;
        }

        $string = $this->resolveString($wellTest);
        if (! $string || ! $string->tower) {
            return null;
        }

        $m = $wellTest->calculated_metrics ?? [];
        $num = fn ($k) => isset($m[$k]) && is_numeric($m[$k]) ? (float) $m[$k] : null;
        $start = $wellTest->start_time;
        $end = $wellTest->end_time;
        $user ??= User::where('name', $wellTest->operator)->first() ?? auth()->user() ?? User::role('admin')->first() ?? User::first();

        return MPFMTest::create([
            'user_id' => $user?->id,
            'oil_field_id' => $string->tower->oil_field_id,
            'tower_id' => $string->tower_id,
            'oil_string_id' => $string->id,
            'well_test_id' => $wellTest->id,
            'test_date' => $wellTest->test_date ? Carbon::parse($wellTest->test_date)->toDateString() : now()->toDateString(),
            'shift' => $this->shift($start),
            'status' => 'draft',
            'oil_rate' => $num('oil_rate'),
            'gas_rate' => $num('gas_rate'),
            'water_cut' => $num('bsw') ?? $num('water_cut'),
            'pressure' => $num('avg_pressure') ?? $num('pressure'),
            'temperature' => $num('avg_temperature') ?? $num('temperature'),
            'gor' => $num('gor'),
            'bsw' => $num('bsw'),
            'wlr' => $num('wlr'),
            'gvf' => $num('gvf'),
            'pressure_avg' => $num('avg_pressure'),
            'temperature_avg' => $num('avg_temperature'),
            'calculated_kpis' => $m,
            'log_file_path' => $wellTest->log_file_path,
            'raw_log_data' => ['metadata' => $wellTest->metadata ?? [], 'test_data' => $wellTest->test_data ?? []],
            'test_started_at' => $start,
            'test_completed_at' => $end,
            'test_duration_minutes' => ($start && $end) ? (int) round(abs($end->diffInMinutes($start))) : null,
            'test_total_data_points' => count($wellTest->test_data['rows'] ?? []),
        ]);
    }

    private function resolveString(WellTest $wellTest): ?OilString
    {
        $name = trim((string) $wellTest->wellhead_string);
        if ($name === '') {
            return null;
        }

        $query = OilString::with('tower')->where('name', $name);
        if ($tower = trim((string) $wellTest->wellhead_tower)) {
            $scoped = (clone $query)->whereHas('tower', fn ($q) => $q->where('name', $tower))->first();
            if ($scoped) {
                return $scoped;
            }
        }

        return $query->first();
    }

    private function shift(?Carbon $time): string
    {
        $h = $time?->hour ?? 8;

        return $h >= 6 && $h < 14 ? 'morning' : ($h >= 14 && $h < 22 ? 'afternoon' : 'night');
    }
}
