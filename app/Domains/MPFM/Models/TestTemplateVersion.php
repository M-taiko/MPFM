<?php

namespace App\Domains\MPFM\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestTemplateVersion extends Model
{
    protected $table = 'test_template_versions';

    public $timestamps = true;

    protected $fillable = [
        'test_template_id',
        'version_number',
        'template_data',
        'change_notes',
        'changed_by_user_id',
    ];

    protected $casts = [
        'template_data' => 'json',
    ];

    /**
     * Relationships
     */
    public function testTemplate(): BelongsTo
    {
        return $this->belongsTo(TestTemplate::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }

    /**
     * Scopes
     */
    public function scopeByVersion($query, int $versionNumber)
    {
        return $query->where('version_number', $versionNumber);
    }
}
