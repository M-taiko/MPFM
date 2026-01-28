<?php

namespace App\Domains\MPFM\Models;

use App\Models\User;
use App\Models\WellTest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestLog extends Model
{
    protected $table = 'mpfm_test_logs';

    protected $fillable = [
        'well_test_id',
        'mpfm_test_id',
        'uploaded_by_user_id',
        'file_name',
        'file_path',
        'file_size_bytes',
        'file_hash',
        'parsed_data',
        'parsing_status',
        'parsing_errors',
        'parsed_at',
        'imported_at',
        'records_count',
        'records_imported',
        'records_failed',
    ];

    protected $casts = [
        'parsed_data' => 'json',
        'parsing_errors' => 'json',
        'parsed_at' => 'datetime',
        'imported_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function wellTest(): BelongsTo
    {
        return $this->belongsTo(WellTest::class);
    }

    public function mpfmTest(): BelongsTo
    {
        return $this->belongsTo(MPFMTest::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }

    /**
     * Scopes
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('parsing_status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('parsing_status', 'pending');
    }

    public function scopeSuccessful($query)
    {
        return $query->where('parsing_status', 'success');
    }

    public function scopeFailed($query)
    {
        return $query->where('parsing_status', 'failed');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('uploaded_by_user_id', $userId);
    }

    /**
     * Methods
     */
    public function markAsParsing()
    {
        $this->update(['parsing_status' => 'pending']);
    }

    public function markAsSuccessful(array $data, int $recordCount)
    {
        $this->update([
            'parsing_status' => 'success',
            'parsed_data' => $data,
            'parsed_at' => now(),
            'records_count' => $recordCount,
        ]);
    }

    public function markAsFailed(array $errors)
    {
        $this->update([
            'parsing_status' => 'failed',
            'parsing_errors' => $errors,
            'parsed_at' => now(),
        ]);
    }

    public function markAsImported(int $imported, int $failed)
    {
        $this->update([
            'imported_at' => now(),
            'records_imported' => $imported,
            'records_failed' => $failed,
        ]);
    }

    public function getSuccessRate(): float
    {
        if ($this->records_count == 0) {
            return 0;
        }

        return ($this->records_imported / $this->records_count) * 100;
    }

    public function isParsed(): bool
    {
        return $this->parsing_status !== 'pending';
    }

    public function isParsedSuccessfully(): bool
    {
        return $this->parsing_status === 'success';
    }

    public function isImported(): bool
    {
        return $this->imported_at !== null;
    }
}
