<?php

namespace App\Services\Wireline;

use App\Models\OilString;
use App\Models\WirelineGaugeRun;
use App\Models\WirelineOperation;

/**
 * Wire Line history & statistics for a single well (shown on the well profile).
 */
class WellWirelineSummary
{
    public function build(OilString $well): array
    {
        $operations = WirelineOperation::where('oil_string_id', $well->id)
            ->with('gaugeRuns')
            ->orderByDesc('operation_date')->orderByDesc('id')
            ->get();

        $completed = $operations->where('status', 'completed');
        $runs = $operations->flatMap->gaugeRuns
            ->filter(fn (WirelineGaugeRun $r) => isset($r->analysis['stats']['bhp']))
            ->sortBy(fn ($r) => $r->started_at ?? $r->operation->operation_date)
            ->values();

        $latestRun = $runs->last();
        $previousRun = $runs->count() > 1 ? $runs[$runs->count() - 2] : null;
        $lastSurvey = $operations->firstWhere('operation_type', 'memory_gauge');

        $byType = $operations->groupBy('operation_type')->map->count()->sortDesc();

        return [
            'operations'   => $operations,
            'runs'         => $runs,
            'latest_run'   => $latestRun,
            'stats'        => [
                'total'          => $operations->count(),
                'completed'      => $completed->count(),
                'surveys'        => $runs->count(),
                'last_operation' => $operations->first(),
                'last_survey_at' => $lastSurvey?->operation_date,
                'days_since_survey' => $lastSurvey ? (int) $lastSurvey->operation_date->diffInDays(now()) : null,
                'latest_bhp'     => $latestRun?->analysis['stats']['bhp'] ?? null,
                'latest_bht'     => $latestRun?->analysis['stats']['bht'] ?? null,
                'bhp_change'     => ($latestRun && $previousRun)
                    ? round($latestRun->analysis['stats']['bhp'] - $previousRun->analysis['stats']['bhp'], 1) : null,
                'by_type'        => $byType,
            ],
            // Reservoir pressure / temperature trend across surveys
            'trend' => [
                'labels' => $runs->map(fn ($r) => ($r->started_at ?? $r->operation->operation_date)->format('Y-m-d'))->all(),
                'bhp'    => $runs->map(fn ($r) => $r->analysis['stats']['bhp'])->all(),
                'bht'    => $runs->map(fn ($r) => $r->analysis['stats']['bht'])->all(),
            ],
        ];
    }
}
