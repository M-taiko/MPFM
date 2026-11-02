<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class OilString extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tower_id',
        'name',
        'code',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Get the tower that owns this oil string
     */
    public function tower(): BelongsTo
    {
        return $this->belongsTo(Tower::class);
    }

    /**
     * Get the oil field through tower relationship
     */
    public function oilField()
    {
        return $this->tower ? $this->tower->oilField : null;
    }

    /**
     * Get all MPFM tests for this oil string
     */
    public function mpfmTests(): HasMany
    {
        return $this->hasMany(MPFMTest::class);
    }

    /**
     * Wire Line operations performed on this well
     */
    public function wirelineOperations(): HasMany
    {
        return $this->hasMany(WirelineOperation::class);
    }

    /**
     * Scope to filter active oil strings
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to filter by tower
     */
    public function scopeForTower($query, $towerId)
    {
        return $query->where('tower_id', $towerId);
    }

    /**
     * Scope to filter by oil field (through tower)
     */
    public function scopeForOilField($query, $oilFieldId)
    {
        return $query->whereHas('tower', function ($q) use ($oilFieldId) {
            $q->where('oil_field_id', $oilFieldId);
        });
    }
}

