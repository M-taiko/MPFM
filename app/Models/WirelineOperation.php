<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class WirelineOperation extends Model
{
    use SoftDeletes;

    /** key => [label, has gauge data, color] */
    public const TYPES = [
        'memory_gauge' => ['Memory Gauge Survey', true,  '#22d3ee'],
        'gauge_cutter' => ['Gauge Cutter / Drift', false, '#a78bfa'],
        'tag_td'       => ['Tag TD / Depth Check', false, '#60a5fa'],
        'sampling'     => ['Bottom Hole Sampling', false, '#34d399'],
        'plug_setting' => ['Plug / Valve Setting', false, '#fbbf24'],
        'fishing'      => ['Fishing', false, '#f87171'],
        'perforating'  => ['Perforating', false, '#fb923c'],
        'logging'      => ['Logging (PLT / Other)', false, '#f472b6'],
        'other'        => ['Other', false, '#94a3b8'],
    ];

    public const STATUSES = [
        'planned'     => 'Planned',
        'in_progress' => 'In Progress',
        'completed'   => 'Completed',
        'cancelled'   => 'Cancelled',
    ];

    protected $fillable = [
        'oil_string_id', 'operation_type', 'run_number', 'operation_date', 'status', 'tool_string',
        'depth_from', 'depth_to', 'crew_leader', 'objective', 'remarks', 'created_by',
    ];

    protected $casts = [
        'operation_date' => 'date',
        'depth_from'     => 'float',
        'depth_to'       => 'float',
    ];

    public function oilString(): BelongsTo
    {
        return $this->belongsTo(OilString::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function gaugeRuns(): HasMany
    {
        return $this->hasMany(WirelineGaugeRun::class);
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->operation_type][0] ?? ucfirst(str_replace('_', ' ', $this->operation_type));
    }

    public function typeColor(): string
    {
        return self::TYPES[$this->operation_type][2] ?? '#94a3b8';
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }
}
