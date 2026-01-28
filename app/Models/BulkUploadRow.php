<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BulkUploadRow extends Model
{
    protected $table = 'bulk_upload_rows';

    public $timestamps = true;

    protected $fillable = [
        'bulk_upload_id',
        'row_number',
        'raw_data',
        'parsed_data',
        'status',
        'error_message',
        'created_record_id',
    ];

    protected $casts = [
        'raw_data' => 'json',
        'parsed_data' => 'json',
    ];

    /**
     * Relationships
     */
    public function bulkUpload(): BelongsTo
    {
        return $this->belongsTo(BulkUpload::class);
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

    public function scopeProcessed($query)
    {
        return $query->where('status', 'processed');
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', 'successful');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Methods
     */
    public function markAsProcessed()
    {
        $this->update(['status' => 'processed']);
    }

    public function markAsSuccessful($recordId = null)
    {
        $this->update([
            'status' => 'successful',
            'created_record_id' => $recordId,
            'error_message' => null,
        ]);
    }

    public function markAsFailed(string $errorMessage)
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
        ]);
    }

    public function isSuccessful(): bool
    {
        return $this->status === 'successful';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function getErrorMessage(): ?string
    {
        return $this->error_message;
    }
}
