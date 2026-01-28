<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BulkUpload extends Model
{
    use SoftDeletes;

    protected $table = 'bulk_uploads';

    protected $fillable = [
        'department_id',
        'uploaded_by_user_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size_bytes',
        'file_hash',
        'status',
        'total_rows',
        'processed_rows',
        'successful_rows',
        'failed_rows',
        'error_details',
        'processing_started_at',
        'processing_completed_at',
        'processing_duration_seconds',
        'notes',
    ];

    protected $casts = [
        'error_details' => 'json',
        'processing_started_at' => 'datetime',
        'processing_completed_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }

    public function rows(): HasMany
    {
        return $this->hasMany(BulkUploadRow::class);
    }

    /**
     * Scopes
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeForDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Methods
     */
    public function markAsProcessing()
    {
        $this->update([
            'status' => 'processing',
            'processing_started_at' => now(),
        ]);
    }

    public function markAsCompleted()
    {
        $duration = now()->diffInSeconds($this->processing_started_at);
        $this->update([
            'status' => 'completed',
            'processing_completed_at' => now(),
            'processing_duration_seconds' => $duration,
        ]);
    }

    public function markAsFailed(array $errors = [])
    {
        $duration = now()->diffInSeconds($this->processing_started_at);
        $this->update([
            'status' => 'failed',
            'processing_completed_at' => now(),
            'processing_duration_seconds' => $duration,
            'error_details' => $errors,
        ]);
    }

    public function getSuccessRate(): float
    {
        if ($this->total_rows == 0) {
            return 0;
        }

        return ($this->successful_rows / $this->total_rows) * 100;
    }

    public function getFailureRate(): float
    {
        return 100 - $this->getSuccessRate();
    }

    public function isProcessed(): bool
    {
        return in_array($this->status, ['completed', 'failed']);
    }

    public function getProcessingTime(): string
    {
        if (!$this->processing_duration_seconds) {
            return '—';
        }

        $seconds = (int) $this->processing_duration_seconds;
        if ($seconds < 60) {
            return $seconds . 's';
        }

        $minutes = floor($seconds / 60);
        $secs = $seconds % 60;

        return $minutes . 'm ' . $secs . 's';
    }
}
