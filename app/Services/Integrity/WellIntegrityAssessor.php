<?php

namespace App\Services\Integrity;

use App\Models\OilString;
use App\Models\WellBarrierElement;
use Carbon\Carbon;

/**
 * Well integrity assessment (API RP 90 / NORSOK D-010 style traffic light).
 *
 *  RED    - unacceptable: annulus above MAASP, both barrier envelopes compromised,
 *           critical open anomaly or severe wall loss.
 *  AMBER  - one barrier envelope compromised or degraded margins - operate with mitigation.
 *  GREEN  - no adverse integrity indicator.
 *  NONE   - nothing recorded yet, so the well is "not assessed" rather than assumed healthy.
 *
 * Everything is derived from recorded data only.
 */
class WellIntegrityAssessor
{
    public const READING_STALE_DAYS = 30;
    public const NEAR_MAASP_PCT = 80;

    public function assess(OilString $well): array
    {
        $well->loadMissing(['integrityProfile', 'integrityAnnuli.readings', 'barrierElements', 'integrityInspections', 'integrityAnomalies', 'wellheadChecks.items']);

        $profile = $well->integrityProfile;
        $today = now()->startOfDay();

        $reasons = [];
        $score = 100;
        $add = function (string $sev, int $delta, string $text, string $area) use (&$reasons, &$score) {
            $score += $delta;
            $reasons[] = ['sev' => $sev, 'delta' => $delta, 'text' => $text, 'area' => $area];
        };

        // ── Annuli ───────────────────────────────────────────────
        $annuli = [];
        foreach ($well->integrityAnnuli as $a) {
            $readings = $a->readings->values();
            $last = $readings->last();
            $pct = ($last && $a->maasp > 0) ? round($last->pressure / $a->maasp * 100, 1) : null;
            $scp = $last && $last->pressure > 0 && $last->rebuilt();

            $slope = null;
            $recent = $readings->slice(-6)->values();
            if ($recent->count() >= 2) {
                $days = $recent->first()->reading_date->diffInDays($recent->last()->reading_date);
                if ($days >= 7) {
                    $slope = round(($recent->last()->pressure - $recent->first()->pressure) / ($days / 30.4), 1);
                }
            }
            $stale = $last ? $last->reading_date->diffInDays($today) > self::READING_STALE_DAYS : false;
            $over = $pct !== null && $pct > 100;
            $near = $pct !== null && $pct >= self::NEAR_MAASP_PCT && !$over;

            $state = !$last ? 'none' : ($over ? 'bad' : (($near || $scp) ? 'warn' : 'ok'));
            $annuli[] = [
                'model' => $a, 'name' => $a->name, 'maasp' => $a->maasp, 'last' => $last, 'pct' => $pct, 'scp' => $scp,
                'slope' => $slope, 'stale' => $stale, 'state' => $state, 'readings' => $readings,
            ];

            if ($over) {
                $add('red', -30, "Annulus {$a->name} at {$last->pressure} psi exceeds its MAASP of {$a->maasp} psi", 'Annulus');
            } elseif ($near) {
                $add('amber', -12, "Annulus {$a->name} at {$pct}% of MAASP", 'Annulus');
            }
            if ($scp) {
                $add('amber', -10, "Annulus {$a->name} shows sustained casing pressure (rebuilds after bleed-down)", 'Annulus');
            }
            if ($last && $stale) {
                $add('amber', -3, "Annulus {$a->name} not read for " . $last->reading_date->diffInDays($today) . ' days', 'Annulus');
            }
        }

        // ── Barrier envelopes ────────────────────────────────────
        $envelopes = [];
        foreach (WellBarrierElement::ENVELOPES as $key => $label) {
            $els = $well->barrierElements->where('envelope', $key)->values();
            $failed = $els->where('status', 'failed');
            $degraded = $els->where('status', 'degraded');
            $overdue = $els->filter(fn ($e) => $e->isOverdue());
            $untested = $els->where('status', 'untested');
            $state = $els->isEmpty() ? 'none' : ($failed->isNotEmpty() ? 'bad' : (($degraded->isNotEmpty() || $overdue->isNotEmpty()) ? 'warn' : 'ok'));
            $envelopes[$key] = compact('label', 'els', 'failed', 'degraded', 'overdue', 'untested', 'state');

            foreach ($degraded as $e) {
                $add('amber', -5, "{$e->name} degraded (marginal test)", 'Barriers');
            }
            foreach ($overdue as $e) {
                $add('amber', -4, "{$e->name} test overdue since " . $e->next_due_date->format('d M Y'), 'Barriers');
            }
            if ($untested->isNotEmpty()) {
                $add('info', -2, $untested->count() . " {$key} element(s) never tested", 'Barriers');
            }
        }
        $pFail = $envelopes['primary']['failed']->isNotEmpty();
        $sFail = $envelopes['secondary']['failed']->isNotEmpty();
        if ($pFail && $sFail) {
            $add('red', -40, 'Both barrier envelopes are compromised (' . $envelopes['primary']['failed']->pluck('name')->merge($envelopes['secondary']['failed']->pluck('name'))->implode(', ') . ')', 'Barriers');
        } elseif ($pFail || $sFail) {
            $env = $pFail ? 'primary' : 'secondary';
            $add('amber', -22, 'Single-barrier operation - ' . ucfirst($env) . ' envelope failed: ' . $envelopes[$env]['failed']->pluck('name')->implode(', '), 'Barriers');
        }

        // ── Operator wellhead checks (valves, turns, tree cap) ───
        $checks = $well->wellheadChecks->take(8)->values();
        $latestCheck = $checks->first();
        $checkIssues = 0;
        if ($latestCheck) {
            $days = $latestCheck->check_date->diffInDays($today);
            if ($latestCheck->tree_cap === 'not_ok') {
                $checkIssues++;
                $add('amber', -6, 'Tree cap not ok at last wellhead check (' . $latestCheck->check_date->format('d M') . ')', 'Wellhead');
            }
            foreach ($latestCheck->items as $it) {
                if ($it->condition === 'stiff') {
                    $checkIssues++;
                    $add('amber', -4, ($it->item_type === 'annulus' ? "Annulus {$it->code} valve" : $it->code) . ' stiff to operate', 'Wellhead');
                } elseif (in_array($it->condition, ['leaking', 'seized'], true)) {
                    $checkIssues++;
                    $add('amber', -8, ($it->item_type === 'annulus' ? "Annulus {$it->code} valve" : $it->code) . ($it->condition === 'leaking' ? ' leaking' : ' not operating'), 'Wellhead');
                }
            }
            // Turns / closing time drifting from the valve's own norm = wear, debris or hydrate/scale in the body
            $prior = $checks->slice(1);
            foreach ($latestCheck->items->where('item_type', 'valve') as $it) {
                $hist = $prior->flatMap->items->where('code', $it->code);
                $turns = $hist->pluck('turns')->filter(fn ($v) => $v !== null)->sort()->values();
                if ($it->turns !== null && $turns->count() >= 2) {
                    $med = $turns[intdiv($turns->count(), 2)];
                    if ($med > 0 && abs($it->turns - $med) / $med > 0.20) {
                        $checkIssues++;
                        $add('amber', -4, "{$it->code} took {$it->turns} turns vs usual ~{$med} (" . round(($it->turns - $med) / $med * 100) . '%)', 'Wellhead');
                    }
                }
                $secs = $hist->pluck('time_sec')->filter(fn ($v) => $v !== null)->sort()->values();
                if ($it->time_sec !== null && $secs->count() >= 2) {
                    $med = $secs[intdiv($secs->count(), 2)];
                    if ($med > 0 && $it->time_sec > $med * 1.5) {
                        $checkIssues++;
                        $add('amber', -3, "{$it->code} closing time {$it->time_sec}s vs usual ~{$med}s", 'Wellhead');
                    }
                }
            }
            if ($days > 35) {
                $add('info', -3, "Wellhead check overdue - last done {$days} days ago", 'Wellhead');
            }
        }

        // ── Inspections ──────────────────────────────────────────
        $inspections = $well->integrityInspections;
        $worstWall = $inspections->max('max_wall_loss_pct');
        $latestByType = $inspections->groupBy('inspection_type')->map->first();
        if ($worstWall !== null && $worstWall >= 50) {
            $add('red', -25, "Wall loss of {$worstWall}% recorded", 'Inspection');
        } elseif ($worstWall !== null && $worstWall >= 30) {
            $add('amber', -12, "Wall loss of {$worstWall}% recorded", 'Inspection');
        }
        foreach ($latestByType as $i) {
            if ($i->status === 'action_required') {
                $add('amber', -10, $i->typeLabel() . ' inspection requires action', 'Inspection');
            }
            if ($i->next_due_date && $i->next_due_date->lt($today)) {
                $add('info', -3, $i->typeLabel() . ' inspection overdue since ' . $i->next_due_date->format('d M Y'), 'Inspection');
            }
        }

        // ── Anomalies ────────────────────────────────────────────
        $active = $well->integrityAnomalies->whereIn('status', ['open', 'mitigated'])->values();
        foreach ($active as $an) {
            $weight = $an->status === 'mitigated' ? 0.5 : 1;
            if ($an->risk_score >= 15) {
                $add($an->status === 'open' ? 'red' : 'amber', (int) round(-25 * $weight), "Critical anomaly: {$an->title}" . ($an->status === 'mitigated' ? ' (mitigated)' : ''), 'Anomaly');
            } elseif ($an->risk_score >= 10) {
                $add('amber', (int) round(-12 * $weight), "High-risk anomaly: {$an->title}" . ($an->status === 'mitigated' ? ' (mitigated)' : ''), 'Anomaly');
            } elseif ($an->risk_score >= 5) {
                $add('info', (int) round(-5 * $weight), "Open anomaly: {$an->title}", 'Anomaly');
            }
            if ($an->isOverdue()) {
                $add('amber', -3, "Anomaly '{$an->title}' past due date", 'Anomaly');
            }
        }

        // ── Review ───────────────────────────────────────────────
        $nextReview = $profile?->nextReviewDate();
        $reviewOverdue = $nextReview && $nextReview->lt($today);
        if ($reviewOverdue) {
            $add('info', -3, 'Integrity review overdue since ' . $nextReview->format('d M Y'), 'Review');
        }

        // ── Verdict ──────────────────────────────────────────────
        $hasData = $profile || $well->integrityAnnuli->flatMap->readings->isNotEmpty() || $well->barrierElements->isNotEmpty()
            || $inspections->isNotEmpty() || $well->integrityAnomalies->isNotEmpty() || $checks->isNotEmpty();

        $score = max(0, min(100, $score));
        $hasRed = collect($reasons)->contains('sev', 'red');
        $hasAmber = collect($reasons)->contains('sev', 'amber');

        if (!$hasData) {
            $key = 'none';
        } elseif ($hasRed || $score < 50) {
            $key = 'red';
        } elseif ($hasAmber || $score < 85) {
            $key = 'amber';
        } else {
            $key = 'green';
        }
        $meta = [
            'green' => ['Green', 'ok', '#34d399', 'Integrity maintained - operate normally'],
            'amber' => ['Amber', 'warn', '#fbbf24', 'Operate within mitigation limits - action plan required'],
            'red'   => ['Red', 'bad', '#f87171', 'Unacceptable integrity risk - review for shut-in or waiver'],
            'none'  => ['Not assessed', 'none', '#94a3b8', 'No integrity data recorded yet'],
        ][$key];

        usort($reasons, fn ($a, $b) => $a['delta'] <=> $b['delta']);

        return [
            'key' => $key, 'label' => $meta[0], 'class' => $meta[1], 'color' => $meta[2], 'guidance' => $meta[3],
            'score' => $hasData ? $score : null,
            'reasons' => $reasons,
            'has_data' => (bool) $hasData,
            'profile' => $profile,
            'annuli' => $annuli,
            'envelopes' => $envelopes,
            'inspections' => $inspections,
            'worst_wall_loss' => $worstWall,
            'anomalies' => $active,
            'all_anomalies' => $well->integrityAnomalies,
            'checks' => $checks,
            'latest_check' => $latestCheck,
            'next_review' => $nextReview,
            'review_overdue' => (bool) $reviewOverdue,
            'counts' => [
                'annuli_alert'   => collect($annuli)->whereIn('state', ['bad', 'warn'])->count(),
                'failed'         => $envelopes['primary']['failed']->count() + $envelopes['secondary']['failed']->count(),
                'degraded'       => $envelopes['primary']['degraded']->count() + $envelopes['secondary']['degraded']->count(),
                'check_issues'   => $checkIssues,
                'overdue_tests'  => $envelopes['primary']['overdue']->count() + $envelopes['secondary']['overdue']->count(),
                'open_anomalies' => $active->count(),
                'critical'       => $active->where('risk_score', '>=', 15)->count(),
            ],
        ];
    }
}
