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

    protected $fillable = [
        'manga_id',
        'chapter_number',
        'chapter_label',
        'volume_number',
        'title',
        'page_count',
        'release_date',
        'is_published',
        'language',
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

    public function readingProgress(): HasMany
    {
        return $this->hasMany(ReadingProgress::class);
    }
}
