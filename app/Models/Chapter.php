<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Chapter extends Model
{
    /** @use HasFactory<\Database\Factories\ChapterFactory> */
    use HasFactory;

    protected $fillable = [
        'manga_id',
        'chapter_number',
        'volume_number',
        'title',
        'description',
        'page_count',
        'release_date',
        'is_published',
        'source_url',
        'external_id',
    ];

    protected function casts(): array
    {
        return [
            'chapter_number' => 'decimal:2',
            'release_date' => 'datetime',
            'is_published' => 'boolean',
        ];
    }

    public function manga(): BelongsTo
    {
        return $this->belongsTo(Manga::class);
    }

    public function readingProgress(): BelongsTo
    {
        return $this->belongsTo(ReadingProgress::class);
    }
}
