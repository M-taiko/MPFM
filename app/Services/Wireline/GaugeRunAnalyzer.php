<?php

namespace App\Services\Wireline;

/**
 * Turns a raw memory-gauge pressure/temperature trace into a well-survey analysis:
 * run phases (surface / RIH / bottom / POOH), final BHP & BHT, stability, pressure events,
 * build-up recovery, gauge-stop detection, fluid gradients (once stop depths are entered)
 * and data-quality flags.
 *
 * Pressure is in psi, temperature in degC, depths in metres, time in seconds since the first sample.
 */
class GaugeRunAnalyzer
{
    private const BOTTOM_FRACTION = 0.95;   // "at bottom" = within 5% of the max pressure
    private const STOP_MIN_SECONDS = 180;   // a gauge stop must last at least 3 minutes
    private const STOP_MAX_RANGE_PSI = 3.0; // ...and stay within this pressure band
    private const PSI_PER_FT_TO_GCC = 0.43353;

    /**
     * @param array<int, array{0:int,1:float,2:?float,3:?float}> $readings [t_sec, pressure, temperature, internal_temp]
     * @param array<int, array<string,mixed>> $existingStops previously saved stops (to keep user-entered depths)
     */
    public function analyze(array $readings, int $interval, ?float $maxRange = null, ?float $targetDepth = null, array $existingStops = []): array
    {
        $n = count($readings);
        $t = array_column($readings, 0);
        $p = array_column($readings, 1);
        $temp = array_map(fn ($v) => $v ?? 0.0, array_column($readings, 2));

        $ps = $this->rollingMedian($p, 5);
        $pmax = max($ps);
        $surface = $this->median(array_slice($ps, 0, min(20, $n)));
        $threshold = $surface + max(15.0, 0.01 * $pmax);

        $in = $this->firstSustainedAbove($ps, $threshold);
        $out = $this->lastAbove($ps, $threshold);
        $quality = [];
        $summary = [];

        if ($in === null || $out === null || $pmax < 50) {
            return $this->noRunResult($n, $t, $p, $temp, $ps, $quality);
        }

        $bottomLevel = $surface + self::BOTTOM_FRACTION * ($pmax - $surface);
        $reach = $this->firstIndexAtOrAbove($ps, $bottomLevel, $in);
        $leave = $this->lastIndexAtOrAbove($ps, $bottomLevel, $out);
        $reachedBottom = $reach !== null && $leave !== null && $leave > $reach;

        if (!$reachedBottom) {
            $reach = $leave = (int) array_search($pmax, $ps, true);
        }

        // ── Phases ───────────────────────────────────────────────
        $phaseDefs = [
            ['pre',    'Surface (pre-run)', 0,      $in],
            ['rih',    'Run In Hole',       $in,    $reach],
            ['bottom', 'On Bottom',         $reach, $leave],
            ['pooh',   'Pull Out Of Hole',  $leave, $out],
            ['post',   'Surface (post-run)', $out,  $n - 1],
        ];
        $phases = [];
        foreach ($phaseDefs as [$key, $label, $a, $b]) {
            if ($b <= $a) {
                continue;
            }
            $phases[] = [
                'key'          => $key,
                'label'        => $label,
                'start_sec'    => $t[$a],
                'end_sec'      => $t[$b],
                'duration_min' => round(($t[$b] - $t[$a]) / 60, 1),
                'p_start'      => round($ps[$a], 1),
                'p_end'        => round($ps[$b], 1),
            ];
        }

        // ── Bottom-hole statistics ───────────────────────────────
        $window = (int) max(8, min(round(1200 / $interval), ($leave - $reach) / 3)); // last ~20 min of bottom
        $endSlice = range(max($reach, $leave - $window), $leave);
        $bhp = $this->median(array_map(fn ($i) => $p[$i], $endSlice));
        $bht = $this->median(array_map(fn ($i) => $temp[$i], $endSlice));
        $maxIdx = (int) array_search($pmax, $ps, true);

        $slope = $this->slopePerHour($t, $ps, max($reach, $leave - (int) round(1800 / $interval)), $leave);
        $stabilised = $reachedBottom && abs($slope) <= 2.0;

        $preSurface = $this->median(array_slice($p, 0, min(40, $in)) ?: [$surface]);
        $postSlice = array_slice($p, min($out + 20, $n - 1));
        $postSurface = $postSlice ? $this->median($postSlice) : null;

        $stats = [
            'bhp'                => round($bhp, 1),
            'bht'                => round($bht, 2),
            'max_pressure'       => round($pmax, 1),
            'max_pressure_min'   => round($t[$maxIdx] / 60, 1),
            'max_temperature'    => round(max($temp), 2),
            'min_pressure'       => round(min($p), 2),
            'surface_pre'        => round($preSurface, 2),
            'surface_post'       => $postSurface !== null ? round($postSurface, 2) : null,
            'bottom_reached_min' => round($t[$reach] / 60, 1),
            'bottom_leave_min'   => round($t[$leave] / 60, 1),
            'bottom_duration_min'=> round(($t[$leave] - $t[$reach]) / 60, 1),
            'rih_duration_min'   => round(($t[$reach] - $t[$in]) / 60, 1),
            'pooh_duration_min'  => round(($t[$out] - $t[$leave]) / 60, 1),
            'total_duration_h'   => round(($t[$n - 1] - $t[0]) / 3600, 2),
            'trend_psi_per_hr'   => round($slope, 2),
            'stabilised'         => $stabilised,
            'reached_bottom'     => $reachedBottom,
        ];
        if ($targetDepth && $targetDepth > 0) {
            $rihMin = max(0.1, ($t[$reach] - $t[$in]) / 60);
            $poohMin = max(0.1, ($t[$out] - $t[$leave]) / 60);
            $stats['avg_rih_speed_m_min'] = round($targetDepth / $rihMin, 1);
            $stats['avg_pooh_speed_m_min'] = round($targetDepth / $poohMin, 1);
        }

        // ── Pressure events & build-up on bottom ─────────────────
        $events = $reachedBottom ? $this->zigzag($t, $ps, $reach, $leave, max(25.0, 0.01 * $pmax)) : [];
        // the approach to bottom is the tail of the run-in, not a well event
        if ($events && $events[0]['type'] === 'rise' && $events[0]['start_sec'] === $t[$reach]) {
            array_shift($events);
        }
        $buildUp = $this->buildUp($events, $t, $ps, $leave);

        // ── Gauge stops (RIH / POOH) ─────────────────────────────
        $stops = array_merge(
            $this->detectStops($t, $ps, $temp, $in, $reach, 'RIH', $interval),
            $reachedBottom ? $this->detectStops($t, $ps, $temp, $leave, $out, 'POOH', $interval) : []
        );
        $stops = $this->mergeExistingDepths($stops, $existingStops);
        $gradients = $this->gradients($stops);

        // ── Quality flags ────────────────────────────────────────
        $drift = $postSurface !== null ? $postSurface - $preSurface : null;
        if ($drift !== null && abs($drift) > 1.0) {
            $quality[] = ['level' => 'warn', 'text' => sprintf('Surface zero drift of %+.2f psi between pre-run and post-run readings.', $drift)];
        } elseif ($drift !== null) {
            $quality[] = ['level' => 'ok', 'text' => sprintf('Gauge returned to surface zero (drift %+.2f psi).', $drift)];
        }
        $neg = count(array_filter($p, fn ($v) => $v < -1.0));
        if ($neg > 0) {
            $quality[] = ['level' => 'warn', 'text' => "$neg readings below -1 psi (sensor offset at surface)."];
        }
        if ($maxRange && $pmax > $maxRange) {
            $quality[] = ['level' => 'bad', 'text' => 'Measured pressure exceeded the gauge maximum range.'];
        } elseif ($maxRange && $pmax > 0.9 * $maxRange) {
            $quality[] = ['level' => 'warn', 'text' => 'Pressure is above 90% of the gauge range.'];
        } elseif ($maxRange) {
            $quality[] = ['level' => 'ok', 'text' => sprintf('Peak pressure is %.0f%% of the %s psi gauge range.', 100 * $pmax / $maxRange, number_format($maxRange)) ];
        }
        $gaps = 0;
        for ($i = 1; $i < $n; $i++) {
            if ($t[$i] - $t[$i - 1] > 2 * $interval) {
                $gaps++;
            }
        }
        if ($gaps) {
            $quality[] = ['level' => 'warn', 'text' => "$gaps time gaps larger than twice the sample interval."];
        }
        if (!$reachedBottom) {
            $quality[] = ['level' => 'warn', 'text' => 'No stable bottom-hole plateau detected; results use the peak pressure.'];
        }
        if ($reachedBottom && !$stabilised) {
            $quality[] = ['level' => 'warn', 'text' => sprintf('Pressure was still changing at %+.1f psi/hr at the end of the bottom section.', $slope)];
        }

        // ── Narrative summary ────────────────────────────────────
        $summary[] = sprintf('Gauge went in hole after %s and reached bottom %s later.', $this->hm($t[$in]), $this->hm($t[$reach] - $t[$in]));
        $summary[] = sprintf('Final bottom-hole pressure %s psi at %s °C after %s on bottom.', number_format($bhp, 1), number_format($bht, 1), $this->hm($t[$leave] - $t[$reach]));
        if ($reachedBottom) {
            $summary[] = $stabilised
                ? sprintf('Pressure had stabilised (%+.2f psi/hr over the last 30 min), so the BHP is representative.', $slope)
                : sprintf('Pressure was still trending (%+.1f psi/hr) - treat BHP as a lower/upper bound.', $slope);
        }
        if ($buildUp) {
            $summary[] = sprintf('Build-up detected: +%.0f psi recovered from %.0f psi%s.', $buildUp['delta'], $buildUp['p_start'], $buildUp['t90_min'] !== null ? sprintf(', 90%% recovery in %.0f min', $buildUp['t90_min']) : '');
        } elseif ($events) {
            $summary[] = count($events) . ' significant pressure movement(s) recorded while on bottom.';
        }
        if ($stops) {
            $summary[] = count($stops) . ' gauge stop(s) detected - enter their depths to compute fluid gradients.';
        }

        return [
            'stats'      => $stats,
            'phases'     => $phases,
            'events'     => $events,
            'build_up'   => $buildUp,
            'stops'      => $stops,
            'gradients'  => $gradients,
            'quality'    => $quality,
            'summary'    => $summary,
            'analyzed_at' => now()->toDateTimeString(),
        ];
    }

    /** Recompute gradients after the user supplies stop depths. */
    public function gradients(array $stops): array
    {
        $out = [];
        foreach (['RIH', 'POOH'] as $dir) {
            $pts = array_values(array_filter($stops, fn ($s) => $s['direction'] === $dir && isset($s['depth']) && $s['depth'] !== null && $s['depth'] !== ''));
            usort($pts, fn ($a, $b) => $a['depth'] <=> $b['depth']);
            for ($i = 1; $i < count($pts); $i++) {
                $dd = ((float) $pts[$i]['depth'] - (float) $pts[$i - 1]['depth']) * 3.28084; // ft
                if ($dd <= 0) {
                    continue;
                }
                $g = ($pts[$i]['pressure'] - $pts[$i - 1]['pressure']) / $dd; // psi/ft
                $out[] = [
                    'direction' => $dir,
                    'from_depth' => (float) $pts[$i - 1]['depth'],
                    'to_depth'   => (float) $pts[$i]['depth'],
                    'psi_per_ft' => round($g, 4),
                    'density_gcc'=> round($g / self::PSI_PER_FT_TO_GCC, 3),
                    'fluid'      => $this->fluidFor($g),
                ];
            }
        }
        return $out;
    }

    private function fluidFor(float $g): string
    {
        return match (true) {
            $g < 0.0   => 'Invalid (pressure decreasing with depth)',
            $g < 0.20  => 'Gas',
            $g < 0.38  => 'Oil',
            $g < 0.42  => 'Oil / Water mix',
            default    => 'Water',
        };
    }

    private function mergeExistingDepths(array $stops, array $existing): array
    {
        foreach ($stops as &$s) {
            foreach ($existing as $e) {
                if (($e['direction'] ?? null) === $s['direction'] && abs(($e['start_sec'] ?? -1) - $s['start_sec']) <= 60 && isset($e['depth'])) {
                    $s['depth'] = $e['depth'];
                    break;
                }
            }
        }
        return $stops;
    }

    private function detectStops(array $t, array $ps, array $temp, int $from, int $to, string $dir, int $interval): array
    {
        if ($to - $from < 4) {
            return [];
        }
        $half = (int) max(2, round(self::STOP_MIN_SECONDS / $interval / 2));
        $still = array_fill($from, $to - $from + 1, false);
        for ($i = $from + $half; $i <= $to - $half; $i++) {
            $win = array_slice($ps, $i - $half, 2 * $half + 1);
            $still[$i] = (max($win) - min($win)) <= self::STOP_MAX_RANGE_PSI;
        }

        $stops = [];
        $i = $from;
        while ($i <= $to) {
            if (!$still[$i]) {
                $i++;
                continue;
            }
            $j = $i;
            while ($j + 1 <= $to && $still[$j + 1]) {
                $j++;
            }
            $a = max($from, $i - $half);
            $b = min($to, $j + $half);
            if ($t[$b] - $t[$a] >= self::STOP_MIN_SECONDS) {
                $seg = array_slice($ps, $a, $b - $a + 1);
                $stops[] = [
                    'direction'    => $dir,
                    'start_sec'    => $t[$a],
                    'end_sec'      => $t[$b],
                    'duration_min' => round(($t[$b] - $t[$a]) / 60, 1),
                    'pressure'     => round($this->median($seg), 1),
                    'temperature'  => round($this->median(array_slice($temp, $a, $b - $a + 1)), 1),
                    'depth'        => null,
                ];
            }
            $i = $j + 1;
        }

        // merge overlapping / back-to-back windows that sit at the same pressure
        $merged = [];
        foreach ($stops as $s) {
            $k = count($merged) - 1;
            if ($k >= 0 && $s['start_sec'] <= $merged[$k]['end_sec'] + 60 && abs($s['pressure'] - $merged[$k]['pressure']) < 5) {
                $m = &$merged[$k];
                $m['end_sec'] = max($m['end_sec'], $s['end_sec']);
                $m['duration_min'] = round(($m['end_sec'] - $m['start_sec']) / 60, 1);
                $m['pressure'] = round(($m['pressure'] + $s['pressure']) / 2, 1);
                $m['temperature'] = round(($m['temperature'] + $s['temperature']) / 2, 1);
                unset($m);
            } else {
                $merged[] = $s;
            }
        }
        return $merged;
    }

    /** Alternating up/down movements of at least $threshold psi. */
    private function zigzag(array $t, array $ps, int $from, int $to, float $threshold): array
    {
        $events = [];
        $pivot = $from;
        $dir = 0;           // +1 rising, -1 falling
        $extreme = $from;

        for ($i = $from + 1; $i <= $to; $i++) {
            if ($dir >= 0 && $ps[$i] > $ps[$extreme]) {
                if ($dir === 0 || $dir === 1) { $extreme = $i; }
            }
            if ($dir <= 0 && $ps[$i] < $ps[$extreme]) {
                if ($dir === 0 || $dir === -1) { $extreme = $i; }
            }

            if ($dir === 0) {
                if (abs($ps[$i] - $ps[$pivot]) >= $threshold) {
                    $dir = $ps[$i] > $ps[$pivot] ? 1 : -1;
                    $extreme = $i;
                }
                continue;
            }

            if ($dir === 1 && $ps[$extreme] - $ps[$i] >= $threshold) {
                $events[] = $this->event($t, $ps, $pivot, $extreme, 'rise');
                $pivot = $extreme; $dir = -1; $extreme = $i;
            } elseif ($dir === -1 && $ps[$i] - $ps[$extreme] >= $threshold) {
                $events[] = $this->event($t, $ps, $pivot, $extreme, 'drop');
                $pivot = $extreme; $dir = 1; $extreme = $i;
            }
        }
        if ($dir !== 0 && abs($ps[$extreme] - $ps[$pivot]) >= $threshold) {
            $events[] = $this->event($t, $ps, $pivot, $extreme, $dir === 1 ? 'rise' : 'drop');
        }
        return $events;
    }

    private function event(array $t, array $ps, int $a, int $b, string $type): array
    {
        return [
            'type'      => $type,
            'start_sec' => $t[$a],
            'end_sec'   => $t[$b],
            'start_min' => round($t[$a] / 60, 1),
            'end_min'   => round($t[$b] / 60, 1),
            'p_from'    => round($ps[$a], 1),
            'p_to'      => round($ps[$b], 1),
            'delta'     => round($ps[$b] - $ps[$a], 1),
        ];
    }

    private function buildUp(array $events, array $t, array $ps, int $leave): ?array
    {
        // largest recovery that follows a pressure drop
        $last = null;
        foreach ($events as $i => $e) {
            if ($e['type'] === 'rise' && $i > 0 && $events[$i - 1]['type'] === 'drop' && (!$last || $e['delta'] > $last['delta'])) {
                $last = $e;
            }
        }
        if (!$last || $last['delta'] < 10) {
            return null;
        }
        $target = $last['p_from'] + 0.9 * $last['delta'];
        $t90 = null;
        foreach ($t as $i => $ti) {
            if ($ti >= $last['start_sec'] && $ti <= $last['end_sec'] + 7200 && $ps[$i] >= $target) {
                $t90 = round(($ti - $last['start_sec']) / 60, 1);
                break;
            }
        }
        return [
            'start_min' => $last['start_min'],
            'p_start'   => $last['p_from'],
            'p_end'     => $last['p_to'],
            'delta'     => $last['delta'],
            'duration_min' => round($last['end_min'] - $last['start_min'], 1),
            't90_min'   => $t90,
        ];
    }

    private function noRunResult(int $n, array $t, array $p, array $temp, array $ps, array $quality): array
    {
        return [
            'stats' => [
                'bhp' => null, 'bht' => null,
                'max_pressure' => round(max($p), 1), 'max_temperature' => round(max($temp), 2), 'min_pressure' => round(min($p), 2),
                'total_duration_h' => round(($t[$n - 1] - $t[0]) / 3600, 2),
                'stabilised' => false, 'reached_bottom' => false,
            ],
            'phases' => [], 'events' => [], 'build_up' => null, 'stops' => [], 'gradients' => [],
            'quality' => array_merge($quality, [['level' => 'bad', 'text' => 'No downhole pressure change detected - the gauge appears to have stayed at surface.']]),
            'summary' => ['The recording shows no run into the well (pressure never rose above surface level).'],
            'analyzed_at' => now()->toDateTimeString(),
        ];
    }

    // ── numeric helpers ──────────────────────────────────────────
    private function rollingMedian(array $v, int $w): array
    {
        $n = count($v);
        $h = intdiv($w, 2);
        $out = [];
        for ($i = 0; $i < $n; $i++) {
            $out[$i] = $this->median(array_slice($v, max(0, $i - $h), min($n, $i + $h + 1) - max(0, $i - $h)));
        }
        return $out;
    }

    private function median(array $v): float
    {
        sort($v);
        $c = count($v);
        if ($c === 0) {
            return 0.0;
        }
        $m = intdiv($c, 2);
        return $c % 2 ? (float) $v[$m] : ($v[$m - 1] + $v[$m]) / 2;
    }

    private function firstSustainedAbove(array $v, float $thr, int $run = 5): ?int
    {
        $n = count($v);
        for ($i = 0; $i < $n - $run; $i++) {
            $ok = true;
            for ($k = 0; $k < $run; $k++) {
                if ($v[$i + $k] <= $thr) { $ok = false; break; }
            }
            if ($ok) {
                return $i;
            }
        }
        return null;
    }

    private function lastAbove(array $v, float $thr): ?int
    {
        for ($i = count($v) - 1; $i >= 0; $i--) {
            if ($v[$i] > $thr) {
                return $i;
            }
        }
        return null;
    }

    private function firstIndexAtOrAbove(array $v, float $level, int $from): ?int
    {
        for ($i = $from, $n = count($v); $i < $n; $i++) {
            if ($v[$i] >= $level) {
                return $i;
            }
        }
        return null;
    }

    private function lastIndexAtOrAbove(array $v, float $level, int $from): ?int
    {
        for ($i = $from; $i >= 0; $i--) {
            if ($v[$i] >= $level) {
                return $i;
            }
        }
        return null;
    }

    /** Least-squares slope of pressure vs time over [a,b], in psi per hour. */
    private function slopePerHour(array $t, array $v, int $a, int $b): float
    {
        $n = $b - $a + 1;
        if ($n < 3) {
            return 0.0;
        }
        $sx = $sy = $sxx = $sxy = 0.0;
        for ($i = $a; $i <= $b; $i++) {
            $x = $t[$i] / 3600;
            $sx += $x; $sy += $v[$i]; $sxx += $x * $x; $sxy += $x * $v[$i];
        }
        $den = $n * $sxx - $sx * $sx;
        return $den == 0.0 ? 0.0 : ($n * $sxy - $sx * $sy) / $den;
    }

    private function hm(int|float $sec): string
    {
        $m = (int) round($sec / 60);
        return $m >= 60 ? intdiv($m, 60) . 'h ' . ($m % 60) . 'm' : $m . ' min';
    }
}
