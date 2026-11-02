<?php

namespace App\Services\Wireline;

use App\Models\OilString;
use App\Models\WirelineGaugeRun;
use App\Models\WirelineOperation;
use Carbon\Carbon;

/**
 * Field-wide Wire Line figures, shared by the Wire Line dashboard and the main dashboard.
 */
class WirelineOverview
{
    public const SURVEY_INTERVAL_DAYS = 180;

    public function build(): array
    {
        $ops = WirelineOperation::with('oilString')->get();
        $monthStart = now()->startOfMonth();

        $months = collect(range(11, 0))->map(fn ($i) => now()->startOfMonth()->subMonths($i));
        $perMonth = $months->map(fn ($m) => $ops->filter(
            fn ($o) => $o->operation_date->isSameMonth($m) && $o->operation_date->isSameYear($m)
        )->count());

        $runs = WirelineGaugeRun::with('operation.oilString')
            ->whereHas('operation')
            ->orderByDesc('started_at')->limit(8)->get();

        // active wells with no completed memory-gauge survey inside the interval
        $surveyed = WirelineOperation::where('operation_type', 'memory_gauge')
            ->where('status', 'completed')
            ->selectRaw('oil_string_id, MAX(operation_date) as last_date')
            ->groupBy('oil_string_id')->pluck('last_date', 'oil_string_id');
        $overdue = OilString::active()->with('tower.oilField')->get()
            ->filter(fn ($w) => !isset($surveyed[$w->id]) || now()->diffInDays($surveyed[$w->id]) > self::SURVEY_INTERVAL_DAYS)
            ->map(fn ($w) => [
                'well' => $w,
                'last' => $surveyed[$w->id] ?? null,
                'days' => isset($surveyed[$w->id]) ? (int) Carbon::parse($surveyed[$w->id])->diffInDays(now()) : null,
            ])->values();

        return [
            'stats' => [
                'total'     => $ops->count(),
                'month'     => $ops->filter(fn ($o) => $o->operation_date >= $monthStart)->count(),
                'completed' => $ops->where('status', 'completed')->count(),
                'open'      => $ops->whereIn('status', ['planned', 'in_progress'])->count(),
                'runs'      => WirelineGaugeRun::count(),
                'wells'     => $ops->pluck('oil_string_id')->unique()->count(),
            ],
            'byType'       => $ops->groupBy('operation_type')->map->count(),
            'monthLabels'  => $months->map->format('M y')->all(),
            'perMonth'     => $perMonth->all(),
            'recent'       => $ops->sortByDesc('operation_date')->take(8),
            'runs'         => $runs,
            'overdue'      => $overdue->take(10),
            'overdueCount' => $overdue->count(),
        ];
    }
}
