<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserManga extends Model
{
    protected $fillable = [
        'user_id',
        'manga_id',
        'status',
        'current_chapter_id',
        'progress_percentage',
        'rating',
        'notes',
        'is_favorite',
        'notify_on_update',
        'started_at',
        'completed_at',
        'last_read_at',
    ];

    protected function casts(): array
    {
        return [
            'progress_percentage' => 'decimal:2',
            'rating' => 'integer',
            'is_favorite' => 'boolean',
            'notify_on_update' => 'boolean',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'last_read_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function manga(): BelongsTo
    {
        return $this->belongsTo(Manga::class);
    }

    public function currentChapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class, 'current_chapter_id');
    }

    public function scopeReading($query)
    {
        return $query->where('status', 'reading');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeDropped($query)
    {
        return $query->where('status', 'dropped');
    }

    public function scopeFavorites($query)
    {
        return $query->where('is_favorite', true);
    }
}
