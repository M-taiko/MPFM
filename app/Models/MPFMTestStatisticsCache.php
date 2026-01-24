<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MPFMTestStatisticsCache extends Model
{
    protected $table = 'mpfm_test_statistics_cache';

    protected $fillable = [
        'oil_string_id', 'period_start', 'period_end',
        'statistics', 'expires_at'
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'expires_at' => 'datetime',
        'statistics' => 'array',
    ];

    public function oilString()
    {
        return $this->belongsTo(OilString::class);
    }

    public function scopeNotExpired($query)
    {
        return $query->where('expires_at', '>', Carbon::now());
    }

    public function isExpired()
    {
        return $this->expires_at < Carbon::now();
    }
}
