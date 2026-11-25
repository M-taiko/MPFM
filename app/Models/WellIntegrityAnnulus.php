<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WellIntegrityAnnulus extends Model
{
    protected $table = 'well_integrity_annuli';

    public const NAMES = ['A' => 'A (tubing / production casing)', 'B' => 'B (production / intermediate casing)', 'C' => 'C (intermediate / surface casing)', 'D' => 'D (outer annulus)'];

    protected $fillable = ['oil_string_id', 'name', 'maasp', 'notes'];

    protected $casts = ['maasp' => 'float'];

    public function oilString(): BelongsTo
    {
        return $this->belongsTo(OilString::class);
    }

    public function readings(): HasMany
    {
        return $this->hasMany(WellIntegrityAnnulusReading::class, 'well_integrity_annulus_id')->orderBy('reading_date')->orderBy('id');
    }
}
