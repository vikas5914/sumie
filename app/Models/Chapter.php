<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chapter extends Model
{
    /** @use HasFactory<\Database\Factories\ChapterFactory> */
    use HasFactory;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'manga_id',
        'chapter_number',
        'volume',
        'title',
        'language',
        'published_at',
        'node',
        'pages',
        'page_count',
        'is_unavailable',
        'source_payload',
        'synced_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'pages' => 'array',
            'source_payload' => 'array',
            'is_unavailable' => 'boolean',
            'page_count' => 'integer',
            'synced_at' => 'datetime',
        ];
    }

    public function manga(): BelongsTo
    {
        return $this->belongsTo(Manga::class);
    }

    public function readingProgress(): HasMany
    {
        return $this->hasMany(ReadingProgress::class);
    }
}
