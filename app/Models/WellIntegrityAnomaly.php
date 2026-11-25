<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WellIntegrityAnomaly extends Model
{
    public const CATEGORIES = [
        'scp' => 'Sustained casing pressure', 'tubing_leak' => 'Tubing leak', 'packer_leak' => 'Packer leak',
        'valve_failure' => 'Valve / SCSSV failure', 'casing_integrity' => 'Casing integrity', 'corrosion' => 'Corrosion / wall loss',
        'wellhead_leak' => 'Wellhead / tree leak', 'cement' => 'Cement / zonal isolation', 'other' => 'Other',
    ];

    public const STATUSES = ['open' => 'Open', 'mitigated' => 'Mitigated', 'closed' => 'Closed'];

    protected $fillable = [
        'oil_string_id', 'title', 'category', 'likelihood', 'consequence', 'risk_score', 'status',
        'identified_date', 'due_date', 'closed_date', 'description', 'mitigation', 'created_by',
    ];

    protected $casts = ['identified_date' => 'date', 'due_date' => 'date', 'closed_date' => 'date'];

    protected static function booted(): void
    {
        static::saving(function (self $a) {
            $a->risk_score = max(1, (int) $a->likelihood) * max(1, (int) $a->consequence);
        });
    }

    public function oilString(): BelongsTo
    {
        return $this->belongsTo(OilString::class);
    }

    public function scopeActive($q)
    {
        return $q->whereIn('status', ['open', 'mitigated']);
    }

    /** 5x5 matrix: 15+ critical, 10+ high, 5+ medium */
    public static function levelFor(int $score): array
    {
        return match (true) {
            $score >= 15 => ['Critical', 'bad', '#f87171'],
            $score >= 10 => ['High', 'warn', '#fb923c'],
            $score >= 5  => ['Medium', 'info', '#fbbf24'],
            default      => ['Low', 'ok', '#34d399'],
        };
    }

    public function level(): array
    {
        return self::levelFor((int) $this->risk_score);
    }

    public function isOverdue(): bool
    {
        return $this->status !== 'closed' && $this->due_date && $this->due_date->isPast() && !$this->due_date->isToday();
    }
}
