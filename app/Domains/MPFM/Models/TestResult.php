<?php

namespace App\Domains\MPFM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestResult extends Model
{
    protected $table = 'test_results';

    protected $fillable = [
        'mpfm_test_id',
        'parameter_name',
        'value',
        'unit',
        'status',
        'notes',
    ];

    protected $casts = [
        'value' => 'decimal:4',
    ];

    /**
     * Relationships
     */
    public function mpfmTest(): BelongsTo
    {
        return $this->belongsTo(MPFMTest::class);
    }

    /**
     * Scopes
     */
    public function scopeForParameter($query, string $parameterName)
    {
        return $query->where('parameter_name', $parameterName);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeRecorded($query)
    {
        return $query->where('status', 'recorded');
    }

    public function scopeValidated($query)
    {
        return $query->where('status', 'validated');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Methods
     */
    public function validate(): bool
    {
        $this->update(['status' => 'validated']);
        return true;
    }

    public function approve(): bool
    {
        $this->update(['status' => 'approved']);
        return true;
    }

    public function reject(): bool
    {
        $this->update(['status' => 'recorded']);
        return true;
    }

    public function isValid(): bool
    {
        return $this->status === 'validated' || $this->status === 'approved';
    }
}
