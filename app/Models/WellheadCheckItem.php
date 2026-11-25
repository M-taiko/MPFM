<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WellheadCheckItem extends Model
{
    protected $fillable = [
        'wellhead_check_id', 'item_type', 'code', 'condition', 'turns', 'time_sec', 'greased',
        'pressure', 'valve_ref', 'dbb_side', 'notes',
    ];

    protected $casts = ['turns' => 'float', 'pressure' => 'float', 'greased' => 'boolean', 'time_sec' => 'integer'];

    public function check(): BelongsTo
    {
        return $this->belongsTo(WellheadCheck::class, 'wellhead_check_id');
    }
}
