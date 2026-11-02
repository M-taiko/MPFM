<?php

namespace App\Services;

use App\Models\OilString;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Integrated well analysis: production performance, reservoir condition (from Wire Line surveys),
 * overall health, rule-based recommendations, chart series and a unified activity timeline.
 *
 * Units follow the rest of the app: oil B/D, gas MMscf/D, water cut %, pressure psi, temperature °C.
 * Everything is derived from recorded data only; sections with too little data say so instead of guessing.
 */
class WellAnalysisService
{
    private const STALE_TEST_DAYS = 45;
    private const SURVEY_INTERVAL_DAYS = 180;
    private const STATIC_SURVEYS = ['bhcip', 'static_gradient', 'pbu'];
    private const FLOWING_SURVEYS = ['bhfp', 'flowing_gradient', 'drawdown'];

    public function analyze(OilString $well, Collection $mpfmTests, Collection $tvTests, array $wireline, Collection $planTasks, Collection $tasks): array
    {
        $points = $this->usablePoints($mpfmTests, $tvTests);
        $production = $this->production($points);
        $reservoir = $this->reservoir($wireline, $points);
        $meters = $this->meterAgreement($mpfmTests, $tvTests);
        $status = $this->status($well, $production, $reservoir, $wireline, $meters);
        $recs = $this->recommendations($production, $reservoir, $wireline, $meters, $status, $mpfmTests);

        return [
            'status'          => $status,
            'production'      => $production,
            'reservoir'       => $reservoir,
            'meters'          => $meters,
            'recommendations' => $recs,
            'charts'          => $this->charts($points, $production, $reservoir),
            'timeline'        => $this->timeline($mpfmTests, $tvTests, $wireline['operations'], $planTasks, $tasks),
            'breakdown'       => $this->breakdown($wireline['operations'], $planTasks, $tasks, $mpfmTests, $tvTests),
        ];
    }

    // ── Data preparation ─────────────────────────────────────────
    /** One averaged point per test date, from approved tests (or all non-rejected if too few). */
    private function usablePoints(Collection $mpfm, Collection $tv): array
    {
        $rows = collect();
        foreach ([['mpfm', $mpfm], ['tv', $tv]] as [$src, $coll]) {
            foreach ($coll as $t) {
                $rows->push([
                    'date' => $t->test_date->copy()->startOfDay(), 'src' => $src, 'status' => $t->status,
                    'oil' => $this->num($t->oil_rate), 'gas' => $this->num($t->gas_rate), 'wc' => $this->num($t->water_cut),
                    'gor' => $this->num($t->gor), 'p' => $this->num($t->pressure), 't' => $this->num($t->temperature),
                ]);
            }
        }
        // Trends use one instrument family: MPFM when available (test-vessel data only as a fallback),
        // otherwise two different meters would show up as false trends.
        $source = $rows->where('src', 'mpfm')->isNotEmpty() ? 'mpfm' : 'tv';
        $rows = $rows->where('src', $source);
        $approved = $rows->where('status', 'approved');
        $basis = $approved->groupBy(fn ($r) => $r['date']->format('Y-m-d'))->count() >= 3 ? 'approved' : 'all';
        $use = $basis === 'approved' ? $approved : $rows->reject(fn ($r) => $r['status'] === 'rejected');

        $byDate = $use->groupBy(fn ($r) => $r['date']->format('Y-m-d'))->map(function ($g, $d) {
            $avg = fn ($k) => ($v = $g->pluck($k)->filter(fn ($x) => $x !== null))->isEmpty() ? null : $v->avg();
            return ['date' => Carbon::parse($d), 'oil' => $avg('oil'), 'gas' => $avg('gas'), 'wc' => $avg('wc'),
                'gor' => $avg('gor'), 'p' => $avg('p'), 't' => $avg('t'), 'n' => $g->count()];
        })->sortKeys()->values()->all();

        return ['basis' => $basis, 'source' => $source, 'rows' => $byDate, 'count_all' => $rows->count()];
    }

    private function num($v): ?float
    {
        return is_numeric($v) ? (float) $v : null;
    }

    // ── Production ───────────────────────────────────────────────
    private function production(array $pts): array
    {
        $rows = collect($pts['rows']);
        if ($rows->isEmpty()) {
            return ['has_data' => false, 'n' => 0, 'basis' => $pts['basis']];
        }
        $latest = $rows->last();
        $first = $rows->first();
        $withOil = $rows->filter(fn ($r) => $r['oil'] !== null);
        $peak = $withOil->sortByDesc('oil')->first();

        $out = [
            'has_data' => true, 'n' => $rows->count(), 'basis' => $pts['basis'], 'source' => $pts['source'],
            'first_date' => $first['date'], 'latest' => $latest,
            'avg_oil' => $withOil->avg('oil'),
            'peak_oil' => $peak['oil'] ?? null, 'peak_date' => $peak['date'] ?? null,
            'oil_change_pct' => ($first['oil'] && $latest['oil'] !== null && $rows->count() > 1) ? round(($latest['oil'] - $first['oil']) / $first['oil'] * 100, 1) : null,
            'cum_oil_bbl' => $this->cumulative($withOil->values()->all()),
            'decline' => $this->decline($withOil->values()->all()),
            'wc' => $this->metricTrend($rows, 'wc', 30.4),
            'gor' => $this->metricTrend($rows, 'gor', 30.4),
            'whfp' => $this->metricTrend($rows, 'p', 30.4),
            'temp' => $this->metricTrend($rows, 't', 30.4),
            'span_days' => (int) $first['date']->diffInDays($latest['date']),
        ];
        $out['gas_oil_note'] = ($latest['oil'] ?? 0) > 0 && $latest['gas'] !== null ? $latest['gas'] * 1e6 / $latest['oil'] : null; // scf/bbl from rates
        return $out;
    }

    /** Trapezoidal integration of the rate over test dates (barrels, approximate). */
    private function cumulative(array $rows): float
    {
        $cum = 0.0;
        for ($i = 1; $i < count($rows); $i++) {
            $days = $rows[$i - 1]['date']->diffInDays($rows[$i]['date']);
            $cum += ($rows[$i - 1]['oil'] + $rows[$i]['oil']) / 2 * $days;
        }
        return round($cum, 0);
    }

    /** Exponential decline fit on the most recent points: q = q0 * exp(b * t). */
    private function decline(array $rows): ?array
    {
        $rows = array_values(array_filter($rows, fn ($r) => $r['oil'] > 0));
        $rows = array_slice($rows, -12);
        if (count($rows) < 3) {
            return null;
        }
        $t0 = $rows[0]['date'];
        $span = $t0->diffInDays(end($rows)['date']);
        if ($span < 14) {
            return null;
        }
        $x = array_map(fn ($r) => (float) $t0->diffInDays($r['date']), $rows);
        $y = array_map(fn ($r) => log($r['oil']), $rows);
        [$b, $a, $r2] = $this->fit($x, $y);
        $last = end($x);
        $q = fn ($d) => round(exp($a + $b * ($last + $d)), 1);

        return [
            'pct_per_year' => round((1 - exp($b * 365)) * 100, 1),   // negative = rate is increasing
            'r2'           => round($r2, 2),
            'n'            => count($rows),
            'fc'           => ['d90' => $q(90), 'd180' => $q(180)],
            'fit'          => ['a' => $a, 'b' => $b, 't0' => $t0->format('Y-m-d'), 'last' => $last],
        ];
    }

    private function metricTrend(Collection $rows, string $key, float $perDays): ?array
    {
        $v = $rows->filter(fn ($r) => $r[$key] !== null)->values();
        if ($v->isEmpty()) {
            return null;
        }
        $first = $v->first();
        $latest = $v->last();
        $res = ['latest' => $latest[$key], 'first' => $first[$key], 'max' => $v->max($key), 'min' => $v->min($key), 'slope_per_month' => null,
            'change_pct' => ($first[$key] && $v->count() >= 3) ? round(($latest[$key] - $first[$key]) / abs($first[$key]) * 100, 1) : null];
        if ($v->count() >= 3 && $first['date']->diffInDays($latest['date']) >= 14) {
            $x = $v->map(fn ($r) => (float) $first['date']->diffInDays($r['date']))->all();
            [$b] = $this->fit($x, $v->pluck($key)->all());
            $res['slope_per_month'] = round($b * $perDays, 3);
        }
        return $res;
    }

    // ── Reservoir (Wire Line surveys) ────────────────────────────
    private function reservoir(array $wireline, array $pts): array
    {
        $runs = $wireline['runs'];
        if ($runs->isEmpty()) {
            return ['has_data' => false];
        }
        $dateOf = fn ($r) => ($r->started_at ?? $r->operation->operation_date)->copy()->startOfDay();
        $static = $runs->filter(fn ($r) => in_array($r->survey_type, self::STATIC_SURVEYS, true))->values();
        $series = $static->isNotEmpty() ? $static : $runs;

        $first = $series->first();
        $cur = $series->last();
        $p0 = $first->analysis['stats']['bhp'];
        $p1 = $cur->analysis['stats']['bhp'];
        $d0 = $dateOf($first);
        $d1 = $dateOf($cur);
        $months = max(0.0, $d0->diffInDays($d1) / 30.4);

        $res = [
            'has_data' => true, 'n_surveys' => $runs->count(), 'static_only' => $static->isNotEmpty(),
            'initial_p' => $p0, 'initial_date' => $d0, 'current_p' => $p1, 'current_date' => $d1,
            'depletion_psi' => round($p0 - $p1, 1), 'depletion_pct' => $p0 > 0 ? round(($p0 - $p1) / $p0 * 100, 1) : null,
            'psi_per_month' => ($series->count() > 1 && $months >= 0.5) ? round(($p1 - $p0) / $months, 1) : null,
            'bht' => $cur->analysis['stats']['bht'] ?? null,
            'stabilised' => (bool) ($cur->analysis['stats']['stabilised'] ?? false),
            'survey_type' => $cur->survey_type, 'run_id' => $cur->id, 'operation_id' => $cur->wireline_operation_id,
            'days_since' => (int) $d1->diffInDays(now()),
            'calibration_old' => $cur->calibration_date && $cur->calibration_date->diffInMonths($cur->started_at ?? now()) > 12,
        ];
        $res['proj_6m'] = $res['psi_per_month'] !== null ? round($p1 + $res['psi_per_month'] * 6, 0) : null;

        // Productivity index from a flowing survey + a test rate close in time
        $flow = $runs->filter(fn ($r) => in_array($r->survey_type, self::FLOWING_SURVEYS, true))->last();
        if ($flow && $static->isNotEmpty()) {
            $pwf = $flow->analysis['stats']['bhp'];
            $fd = $dateOf($flow);
            $near = collect($pts['rows'])->filter(fn ($r) => $r['oil'] > 0)->sortBy(fn ($r) => abs($r['date']->diffInDays($fd)))->first();
            if ($near && abs($near['date']->diffInDays($fd)) <= 30 && $p1 > $pwf) {
                $res['pi'] = ['value' => round($near['oil'] / ($p1 - $pwf), 3), 'drawdown' => round($p1 - $pwf, 0), 'pwf' => $pwf, 'q' => $near['oil']];
            }
        }

        // Fluid levels from gradient surveys
        $res['fluids'] = [];
        foreach ($runs as $r) {
            foreach ($r->analysis['gradients'] ?? [] as $g) {
                $res['fluids'][] = $g + ['date' => $dateOf($r)->format('Y-m-d')];
            }
        }
        $latestG = collect($res['fluids'])->where('date', collect($res['fluids'])->max('date'));
        $water = $latestG->first(fn ($g) => $g['fluid'] === 'Water');
        $gas = $latestG->first(fn ($g) => $g['fluid'] === 'Gas');
        $res['water_level_m'] = $water['from_depth'] ?? null;
        $res['gas_level_m'] = $gas['to_depth'] ?? null;

        return $res;
    }

    // ── MPFM vs test vessel agreement ────────────────────────────
    private function meterAgreement(Collection $mpfm, Collection $tv): ?array
    {
        $m = $mpfm->where('status', 'approved')->filter(fn ($t) => $t->oil_rate > 0);
        $dev = [];
        foreach ($tv->filter(fn ($t) => $t->oil_rate > 0) as $t) {
            $near = $m->sortBy(fn ($x) => abs($x->test_date->diffInDays($t->test_date)))->first();
            if ($near && abs($near->test_date->diffInDays($t->test_date)) <= 3) {
                $dev[] = ($near->oil_rate - $t->oil_rate) / $t->oil_rate * 100;
            }
        }
        if (!$dev) {
            return null;
        }
        return ['pairs' => count($dev), 'mean_pct' => round(array_sum($dev) / count($dev), 1), 'mean_abs_pct' => round(array_sum(array_map('abs', $dev)) / count($dev), 1)];
    }

    // ── Overall status & health score ────────────────────────────
    private function status(OilString $well, array $prod, array $res, array $wl, ?array $meters): array
    {
        $factors = [];
        $score = 100;
        $add = function (int $delta, string $text) use (&$score, &$factors) {
            $score += $delta;
            $factors[] = ['delta' => $delta, 'text' => $text];
        };

        $lastTest = $prod['has_data'] ? $prod['latest']['date'] : null;
        $daysTest = $lastTest ? (int) $lastTest->diffInDays(now()) : null;

        if (!$prod['has_data']) {
            $state = ['No test data', 'bad'];
            $add(-30, 'No usable well tests recorded');
        } elseif (($prod['latest']['oil'] ?? null) !== null && $prod['latest']['oil'] <= 0) {
            $state = ['Not producing', 'bad'];
            $add(-40, 'Latest test shows zero oil rate');
        } elseif ($daysTest > self::STALE_TEST_DAYS) {
            $state = ['Producing — test overdue', 'warn'];
            $add($daysTest > 90 ? -20 : -10, "Last test was $daysTest days ago");
        } else {
            $state = ['Producing', 'ok'];
        }

        if ($prod['has_data']) {
            $d = $prod['decline'];
            if ($d && $d['pct_per_year'] > 30) { $add(-15, "Oil decline {$d['pct_per_year']}%/yr"); }
            elseif ($d && $d['pct_per_year'] > 15) { $add(-8, "Oil decline {$d['pct_per_year']}%/yr"); }
            $wc = $prod['wc'];
            if ($wc && $wc['latest'] >= 50) { $add(-15, "High water cut ({$wc['latest']}%)"); }
            elseif ($wc && $wc['latest'] >= 30) { $add(-8, "Water cut {$wc['latest']}%"); }
            if ($wc && ($wc['slope_per_month'] ?? 0) > 2) { $add(-7, 'Water cut rising ' . $wc['slope_per_month'] . ' pts/month'); }
            $gor = $prod['gor'];
            if ($gor && ($gor['change_pct'] ?? 0) > 25) { $add(-8, "GOR up {$gor['change_pct']}% since first test"); }
        }
        if ($res['has_data']) {
            if (($res['depletion_pct'] ?? 0) > 10) { $add(-10, "Reservoir pressure down {$res['depletion_pct']}%"); }
            if ($res['days_since'] > self::SURVEY_INTERVAL_DAYS) { $add(-8, "Last pressure survey {$res['days_since']} days ago"); }
        } else {
            $add(-8, 'No memory-gauge pressure survey on record');
        }
        if ($meters && $meters['mean_abs_pct'] > 10) { $add(-7, "MPFM vs test vessel differ by {$meters['mean_abs_pct']}%"); }
        if ($well->status !== 'active') { $add(-10, 'Well is marked ' . $well->status); }

        $score = max(0, min(100, $score));
        return [
            'operational' => $well->status, 'production_state' => $state[0], 'production_class' => $state[1],
            'last_test_date' => $lastTest, 'days_since_test' => $daysTest,
            'score' => $score, 'label' => $score >= 80 ? 'Good' : ($score >= 60 ? 'Fair' : 'Needs attention'),
            'class' => $score >= 80 ? 'ok' : ($score >= 60 ? 'warn' : 'bad'), 'factors' => $factors,
        ];
    }

    // ── Recommendations ──────────────────────────────────────────
    private function recommendations(array $prod, array $res, array $wl, ?array $meters, array $status, Collection $mpfm): array
    {
        $r = [];
        $add = function (string $sev, string $area, string $title, string $detail) use (&$r) {
            $r[] = ['severity' => $sev, 'area' => $area, 'title' => $title, 'detail' => $detail];
        };

        if (!$prod['has_data']) {
            $add('high', 'Production', 'Run a well test', 'No usable MPFM or test-vessel data exists for this well. Schedule an MPFM test to establish a baseline.');
        } else {
            if ($status['days_since_test'] > self::STALE_TEST_DAYS) {
                $add('medium', 'Production', 'Schedule a new well test', "The last test is {$status['days_since_test']} days old. Re-test to confirm current rates.");
            }
            $d = $prod['decline'];
            if ($d && $d['pct_per_year'] > 20) {
                $add($d['pct_per_year'] > 35 ? 'high' : 'medium', 'Production', 'Investigate oil-rate decline',
                    "Oil is declining about {$d['pct_per_year']}%/yr (fit R² {$d['r2']}, {$d['n']} tests). Review choke size, artificial-lift options and consider a stimulation or a flowing-pressure survey. Projected {$d['fc']['d180']} B/D in 180 days.");
            } elseif ($d && $d['pct_per_year'] < -5) {
                $add('info', 'Production', 'Rate is increasing', 'Oil rate is trending up; confirm it is not a choke change before treating it as improvement.');
            }
            $wc = $prod['wc'];
            if ($wc && ($wc['slope_per_month'] ?? 0) > 2 || ($wc && $wc['latest'] >= 40)) {
                $add($wc['latest'] >= 50 ? 'high' : 'medium', 'Production', 'Water cut needs attention',
                    "Water cut is {$wc['latest']}%" . (($wc['slope_per_month'] ?? null) !== null ? " and moving {$wc['slope_per_month']} pts/month" : '') . '. Run a gradient survey or PLT to locate the water source; evaluate water shut-off or rate management.');
            }
            $gor = $prod['gor'];
            if ($gor && ($gor['change_pct'] ?? 0) > 25) {
                $add('medium', 'Production', 'Rising gas-oil ratio', "GOR is up {$gor['change_pct']}% since the first test: check for gas breakthrough/coning and consider a smaller choke.");
            }
            $wh = $prod['whfp'];
            if ($wh && ($wh['change_pct'] ?? 0) < -15) {
                $add('medium', 'Reservoir', 'Wellhead pressure falling', "WHFP is down " . abs($wh['change_pct']) . '% since the first test. Confirm with a static bottom-hole pressure survey.');
            }
            if ($prod['n'] < 3) {
                $add('info', 'Data', 'Limited test history', 'Trend, decline and forecast need at least 3 tests spread over 2+ weeks; the figures shown so far are indicative only.');
            }
        }

        if (!$res['has_data']) {
            $add('medium', 'Reservoir', 'Run a BHCIP / static gradient survey', 'No memory-gauge survey is recorded, so reservoir pressure, depletion and fluid levels are unknown.');
        } else {
            if ($res['days_since'] > self::SURVEY_INTERVAL_DAYS) {
                $add('medium', 'Reservoir', 'Pressure survey overdue', "The last survey was {$res['days_since']} days ago (policy: every " . self::SURVEY_INTERVAL_DAYS . ' days).');
            }
            if (($res['depletion_pct'] ?? 0) > 10) {
                $add('high', 'Reservoir', 'Significant pressure depletion',
                    "Reservoir pressure fell {$res['depletion_psi']} psi ({$res['depletion_pct']}%) since " . $res['initial_date']->format('d M Y') . ($res['proj_6m'] ? "; at this rate ≈ {$res['proj_6m']} psi in 6 months" : '') . '. Evaluate pressure maintenance and review offtake rate.');
            } elseif (($res['psi_per_month'] ?? 0) < -10) {
                $add('medium', 'Reservoir', 'Pressure declining', "Static pressure is falling about " . abs($res['psi_per_month']) . ' psi/month. Keep surveys on schedule.');
            }
            if (!$res['stabilised']) {
                $add('medium', 'Survey', 'Latest BHP not stabilised', 'The last survey was still trending at the end of the bottom section; repeat with a longer shut-in before using it as reservoir pressure.');
            }
            if ($res['water_level_m'] !== null) {
                $add('high', 'Reservoir', 'Water detected in the gradient survey', "The latest gradient survey indicates water below about {$res['water_level_m']} m. Compare with perforation depths and consider isolation.");
            }
            if ($res['calibration_old']) {
                $add('low', 'Survey', 'Gauge calibration out of date', 'The gauge used for the last survey was calibrated more than 12 months earlier; verify before relying on the pressure.');
            }
        }

        if ($meters && $meters['mean_abs_pct'] > 10) {
            $add('medium', 'Metering', 'MPFM and test vessel disagree', "Across {$meters['pairs']} paired test(s) the oil rates differ by {$meters['mean_abs_pct']}% on average (MPFM " . ($meters['mean_pct'] >= 0 ? 'over' : 'under') . '-reading). Check MPFM calibration and PVT inputs.');
        }
        $rejected = $mpfm->where('status', 'rejected')->count();
        if ($rejected >= 2) {
            $add('low', 'Data', 'Several rejected MPFM tests', "$rejected MPFM tests were rejected for this well - review operating procedure and data quality.");
        }
        $open = $wl['operations']->whereIn('status', ['planned', 'in_progress'])->count();
        if ($open) {
            $add('info', 'Wire Line', 'Open Wire Line operations', "$open Wire Line operation(s) are planned or in progress on this well.");
        }

        if (!$r) {
            $add('info', 'Overall', 'No action required', 'No adverse indicators were found. Continue routine testing and surveys.');
        }

        $order = ['high' => 0, 'medium' => 1, 'low' => 2, 'info' => 3];
        usort($r, fn ($a, $b) => $order[$a['severity']] <=> $order[$b['severity']]);
        return $r;
    }

    // ── Charts ───────────────────────────────────────────────────
    private function charts(array $pts, array $prod, array $res): array
    {
        $rows = collect($pts['rows'])->take(-40)->values();
        $labels = $rows->map(fn ($r) => $r['date']->format('Y-m-d'))->all();
        $col = fn ($k, $d = 2) => $rows->map(fn ($r) => $r[$k] === null ? null : round($r[$k], $d))->all();

        // cumulative oil along the same dates
        $cum = [];
        $run = 0.0;
        $prev = null;
        foreach ($rows as $r) {
            if ($r['oil'] !== null) {
                if ($prev) {
                    $run += ($prev['oil'] + $r['oil']) / 2 * $prev['date']->diffInDays($r['date']);
                }
                $prev = $r;
            }
            $cum[] = round($run, 0);
        }

        // decline fit + forecast
        $fitLabels = $labels;
        $actual = $col('oil');
        $fit = array_fill(0, count($labels), null);
        $d = $prod['decline'] ?? null;
        if ($d) {
            $t0 = Carbon::parse($d['fit']['t0']);
            foreach ($rows as $i => $r) {
                if ($r['date'] >= $t0) {
                    $fit[$i] = round(exp($d['fit']['a'] + $d['fit']['b'] * $t0->diffInDays($r['date'])), 1);
                }
            }
            $lastDate = $rows->last()['date'];
            foreach ([30, 60, 90, 120, 150, 180] as $add) {
                $dt = $lastDate->copy()->addDays($add);
                $fitLabels[] = $dt->format('Y-m-d');
                $actual[] = null;
                $fit[] = round(exp($d['fit']['a'] + $d['fit']['b'] * $t0->diffInDays($dt)), 1);
            }
        }

        return [
            'labels' => $labels,
            'oil' => $col('oil'), 'gas' => $col('gas', 3), 'wc' => $col('wc'), 'gor' => $col('gor'), 'whfp' => $col('p', 1), 'temp' => $col('t', 1),
            'cum' => $cum,
            'decline' => ['labels' => $fitLabels, 'actual' => $actual, 'fit' => $fit, 'enabled' => (bool) $d],
        ];
    }

    // ── Timeline & breakdown ─────────────────────────────────────
    private function timeline(Collection $mpfm, Collection $tv, Collection $ops, Collection $planTasks, Collection $tasks): array
    {
        $ev = collect();
        foreach ($mpfm as $t) {
            $ev->push(['date' => $t->test_date, 'kind' => 'MPFM test', 'color' => '#22d3ee', 'icon' => 'fe-bar-chart-2',
                'text' => sprintf('Oil %s B/D · WC %s%%', $this->f($t->oil_rate), $this->f($t->water_cut)), 'status' => $t->status, 'url' => route('mpfm_tests.show', $t->id)]);
        }
        foreach ($tv as $t) {
            $ev->push(['date' => $t->test_date, 'kind' => 'Test vessel', 'color' => '#fbbf24', 'icon' => 'fe-anchor',
                'text' => sprintf('Oil %s B/D · WC %s%%', $this->f($t->oil_rate), $this->f($t->water_cut)), 'status' => $t->status, 'url' => route('tv_tests.show', $t->id)]);
        }
        foreach ($ops as $o) {
            $r = $o->gaugeRuns->first();
            $ev->push(['date' => $o->operation_date, 'kind' => 'Wire Line', 'color' => $o->typeColor(), 'icon' => 'fe-git-branch',
                'text' => $o->typeLabel() . ' #' . $o->run_number . ($r && $r->bhp ? ' · BHP ' . number_format($r->bhp, 0) . ' psi' : ''), 'status' => $o->status, 'url' => route('wireline.show', $o)]);
        }
        foreach ($planTasks as $p) {
            if ($p->scheduled_date) {
                $ev->push(['date' => $p->scheduled_date, 'kind' => $p->department->name ?? 'Plan task', 'color' => '#a78bfa', 'icon' => 'fe-clipboard',
                    'text' => $p->category ?? $p->well_full_name ?? 'Planned task', 'status' => $p->completion_status ?? 'pending', 'url' => null]);
            }
        }
        foreach ($tasks as $t) {
            $d = $t->scheduled_date ?? $t->created_at;
            if ($d) {
                $ev->push(['date' => $d, 'kind' => $t->department->name ?? 'Task', 'color' => '#34d399', 'icon' => 'fe-check-square',
                    'text' => $t->title, 'status' => is_object($t->state) ? ($t->state->value ?? $t->state->name) : $t->state, 'url' => route('tasks.show', $t->id)]);
            }
        }
        return $ev->map(function ($e) {
            $e['date'] = $e['date'] instanceof Carbon ? $e['date'] : Carbon::parse($e['date']);
            return $e;
        })->sortByDesc('date')->take(60)->values()->all();
    }

    private function breakdown(Collection $ops, Collection $planTasks, Collection $tasks, Collection $mpfm, Collection $tv): array
    {
        $byOp = $ops->groupBy('operation_type')->map(fn ($g, $k) => [
            'label' => \App\Models\WirelineOperation::TYPES[$k][0] ?? $k, 'color' => \App\Models\WirelineOperation::TYPES[$k][2] ?? '#94a3b8', 'count' => $g->count(),
        ])->sortByDesc('count')->values()->all();

        $dept = collect();
        foreach ($planTasks as $p) { $dept->push($p->department->name ?? 'Unassigned'); }
        foreach ($tasks as $t) { $dept->push($t->department->name ?? 'Unassigned'); }
        $byDept = $dept->countBy()->sortDesc()->map(fn ($c, $n) => ['label' => $n, 'count' => $c])->values()->all();

        return [
            'wireline' => $byOp, 'departments' => $byDept,
            'tests' => ['mpfm' => $mpfm->count(), 'tv' => $tv->count(), 'approved' => $mpfm->where('status', 'approved')->count() + $tv->where('status', 'approved')->count()],
        ];
    }

    // ── math ─────────────────────────────────────────────────────
    private function f($v): string
    {
        return is_numeric($v) ? number_format((float) $v, 1) : '—';
    }

    /** Least squares y = a + b x. Returns [b, a, r2]. */
    private function fit(array $x, array $y): array
    {
        $n = count($x);
        $mx = array_sum($x) / $n;
        $my = array_sum($y) / $n;
        $sxx = $sxy = $syy = 0.0;
        foreach ($x as $i => $xi) {
            $sxx += ($xi - $mx) ** 2;
            $sxy += ($xi - $mx) * ($y[$i] - $my);
            $syy += ($y[$i] - $my) ** 2;
        }
        $b = $sxx == 0.0 ? 0.0 : $sxy / $sxx;
        $a = $my - $b * $mx;
        $r2 = ($sxx == 0.0 || $syy == 0.0) ? 0.0 : ($sxy ** 2) / ($sxx * $syy);
        return [$b, $a, $r2];
    }
}
