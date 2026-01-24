<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MPFMValidationRule extends Model
{
    protected $table = 'mpfm_validation_rules';

    protected $fillable = [
        'rule_name', 'rule_type', 'parameter',
        'oil_field_id', 'tower_id', 'oil_string_id',
        'thresholds', 'severity', 'auto_action', 'is_active',
        'created_by'
    ];

    protected $casts = [
        'thresholds' => 'array',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function oilField()
    {
        return $this->belongsTo(OilField::class);
    }

    public function tower()
    {
        return $this->belongsTo(Tower::class);
    }

    public function oilString()
    {
        return $this->belongsTo(OilString::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForParameter($query, $parameter)
    {
        return $query->where('parameter', $parameter);
    }

    public function scopeForString($query, $stringId)
    {
        return $query->where('oil_string_id', $stringId);
    }

    // Helpers
    public function getSpecificityLevel()
    {
        if ($this->oil_string_id) return 3; // Most specific
        if ($this->tower_id) return 2;
        if ($this->oil_field_id) return 1;
        return 0; // Global
    }
}
