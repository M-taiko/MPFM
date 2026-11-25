<?php

namespace App\Services\Integrity;

use App\Models\OilString;
use App\Models\WellBarrierElement;
use App\Models\WellIntegrityAnomaly;

/**
 * Field-wide integrity picture for the Well Integrity dashboard and register.
 */
class WellIntegrityOverview
{
    public function __construct(private WellIntegrityAssessor $assessor)
    {
    }

    /** Assessed rows for every well (red first) */
    public function wells(): \Illuminate\Support\Collection
    {
        $order = ['red' => 0, 'amber' => 1, 'none' => 2, 'green' => 3];

        return OilString::with(['tower.oilField', 'integrityProfile', 'integrityAnnuli.readings', 'barrierElements', 'integrityInspections', 'integrityAnomalies', 'wellheadChecks.items'])
            ->orderBy('name')->get()
            ->map(fn (OilString $w) => ['well' => $w, 'a' => $this->assessor->assess($w)])
            ->sortBy(fn ($r) => [$order[$r['a']['key']], $r['a']['score'] ?? 101])
            ->values();
    }

    public function build(): array
    {
        $rows = $this->wells();
        $by = $rows->countBy(fn ($r) => $r['a']['key']);

        $overdue = WellBarrierElement::with('oilString')
            ->whereNotNull('next_due_date')->whereDate('next_due_date', '<', now()->toDateString())
            ->orderBy('next_due_date')->limit(10)->get();

        $anomalies = WellIntegrityAnomaly::with('oilString')->active()
            ->orderByDesc('risk_score')->orderBy('due_date')->limit(8)->get();

        $annulusAlerts = $rows->flatMap(fn ($r) => collect($r['a']['annuli'])
            ->whereIn('state', ['bad', 'warn'])
            ->map(fn ($an) => ['well' => $r['well'], 'an' => $an]))
            ->sortByDesc(fn ($x) => $x['an']['pct'] ?? 0)->take(8)->values();

        return [
            'rows'  => $rows,
            'stats' => [
                'wells'    => $rows->count(),
                'green'    => $by['green'] ?? 0,
                'amber'    => $by['amber'] ?? 0,
                'red'      => $by['red'] ?? 0,
                'none'     => $by['none'] ?? 0,
                'failed'   => $rows->sum(fn ($r) => $r['a']['counts']['failed']),
                'overdue'  => WellBarrierElement::whereNotNull('next_due_date')->whereDate('next_due_date', '<', now()->toDateString())->count(),
                'anomalies' => WellIntegrityAnomaly::active()->count(),
                'critical' => WellIntegrityAnomaly::active()->where('risk_score', '>=', 15)->count(),
            ],
            'overdueTests'  => $overdue,
            'topAnomalies'  => $anomalies,
            'annulusAlerts' => $annulusAlerts,
        ];
    }
}
