<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskImage extends Model
{
    protected $fillable = [
        'task_id',
        'path',
        'original_name',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}
