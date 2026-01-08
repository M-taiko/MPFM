<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MPFMStep extends Model
{
    protected $table = 'mpfm_steps';
    protected $fillable = [
        'plan_id',
        'step_number',
        'name',
        'choke',
        'time_window',
        'planned_rates',
        'description',
        'settings',
    ];

    protected $casts = [
        'planned_rates' => 'array',
        'settings' => 'array',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(MPFMPlan::class);
    }

    public function readings(): HasMany
    {
        return $this->hasMany(MPFMReading::class);
    }
}