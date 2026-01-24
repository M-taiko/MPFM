<?php

namespace App\Services;

use App\Models\MPFMTestStatisticsCache;
use Carbon\Carbon;

class MPFMTestStatisticsCacheService
{
    public function getStatistics($oilStringId, $periodStart, $periodEnd)
    {
        // Try to get from cache
        $cached = MPFMTestStatisticsCache::notExpired()
            ->where('oil_string_id', $oilStringId)
            ->where('period_start', $periodStart)
            ->where('period_end', $periodEnd)
            ->first();

        if ($cached) {
            return $cached->statistics;
        }

        return null;
    }

    public function cacheStatistics($oilStringId, $periodStart, $periodEnd, $statistics, $ttlHours = 24)
    {
        return MPFMTestStatisticsCache::updateOrCreate(
            [
                'oil_string_id' => $oilStringId,
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
            ],
            [
                'statistics' => $statistics,
                'expires_at' => Carbon::now()->addHours($ttlHours),
            ]
        );
    }

    public function clearExpiredCache()
    {
        return MPFMTestStatisticsCache::where('expires_at', '<', Carbon::now())->delete();
    }
}
