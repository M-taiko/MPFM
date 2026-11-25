<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WellIntegrityInspection extends Model
{
    public const TYPES = [
        'caliper' => 'Multi-finger caliper', 'mtt' => 'Magnetic thickness (MTT)', 'usit' => 'Ultrasonic (USIT)',
        'cbl' => 'Cement bond log (CBL/VDL)', 'corrosion' => 'Corrosion coupon / probe', 'cathodic' => 'Cathodic protection',
        'visual' => 'Visual / wellhead inspection',
    ];

    public const STATUSES = ['acceptable' => 'Acceptable', 'monitor' => 'Monitor', 'action_required' => 'Action required'];

    protected $fillable = [
        'oil_string_id', 'inspection_type', 'inspection_date', 'depth_from', 'depth_to', 'max_wall_loss_pct',
        'status', 'next_due_date', 'finding', 'remarks', 'created_by',
    ];

    protected $casts = [
        'inspection_date' => 'date', 'next_due_date' => 'date',
        'depth_from' => 'float', 'depth_to' => 'float', 'max_wall_loss_pct' => 'float',
    ];

    public function oilString(): BelongsTo
    {
        return $this->belongsTo(OilString::class);
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->inspection_type] ?? $this->inspection_type;
    }
}
