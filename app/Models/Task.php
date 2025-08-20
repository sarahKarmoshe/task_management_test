<?php

namespace App\Models;

use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => TaskStatus::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->hasMany(TaskImage::class);
    }

    /** Filter by status (accepts enum or string). */
    public function scopeStatus(Builder $q, TaskStatus|string|null $status): Builder
    {
        if (!$status) return $q;

        $value = $status instanceof TaskStatus ? $status->value : $status;

        return $q->where('status', $value);
    }

    /** Search in title/description using LIKE (no full-text). */
    public function scopeSearch(Builder $q, ?string $term): Builder
    {
        if (!$term) return $q;

        $term = trim($term);

        return $q->where(function (Builder $qq) use ($term) {
            $qq->where('title', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
        });
    }
}
