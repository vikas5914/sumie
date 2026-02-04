<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReadingProgress extends Model
{
    protected $table = 'reading_progress';

    protected $fillable = [
        'user_id',
        'chapter_id',
        'manga_id',
        'page_number',
        'is_finished',
        'read_percentage',
        'duration_seconds',
        'started_at',
        'finished_at',
    ];

    protected function casts(): array
    {
        return [
            'page_number' => 'integer',
            'is_finished' => 'boolean',
            'read_percentage' => 'decimal:2',
            'duration_seconds' => 'integer',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    public function manga(): BelongsTo
    {
        return $this->belongsTo(Manga::class);
    }

    public function scopeInProgress($query)
    {
        return $query->where('is_finished', false);
    }

    public function scopeRecentlyRead($query, $hours = 24)
    {
        return $query->where('updated_at', '>=', now()->subHours($hours));
    }
}
