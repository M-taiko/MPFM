<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OperatorPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'well_test_id',
        'step_number',
        'step_type',
        'step_description',
        'step_parameters',
        'is_completed',
        'completed_at',
        'completed_by',
        'step_results',
        'order',
    ];

    protected $casts = [
        'step_parameters' => 'array',
        'step_results' => 'array',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
        'order' => 'integer',
        'step_number' => 'integer',
    ];

    /**
     * Get the well test that this operator plan belongs to
     */
    public function wellTest(): BelongsTo
    {
        return $this->belongsTo(WellTest::class, 'well_test_id');
    }

    /**
     * Mark the step as completed
     */
    public function markAsCompleted(string $completedBy = null, array $results = []): void
    {
        $this->update([
            'is_completed' => true,
            'completed_at' => now(),
            'completed_by' => $completedBy,
            'step_results' => $results,
        ]);
    }

    /**
     * Mark the step as pending
     */
    public function markAsPending(): void
    {
        $this->update([
            'is_completed' => false,
            'completed_at' => null,
            'completed_by' => null,
            'step_results' => null,
        ]);
    }

    /**
     * Scope to get only completed steps
     */
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    /**
     * Scope to get only pending steps
     */
    public function scopePending($query)
    {
        return $query->where('is_completed', false);
    }

    /**
     * Scope to order by step number
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('step_number');
    }
}
