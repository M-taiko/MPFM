<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DepartmentTaskData extends Model
{
    protected $table = 'department_task_data';

    protected $fillable = [
        'plan_task_id',
        'task_id',
        'department_code',
        'data',
        'completed_by_user_id',
        'completion_notes',
        'completed_at',
    ];

    protected $casts = [
        'data'         => 'array',
        'completed_at' => 'datetime',
    ];

    public function planTask(): BelongsTo
    {
        return $this->belongsTo(PlanTask::class);
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by_user_id');
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }
}
