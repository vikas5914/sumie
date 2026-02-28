<?php

namespace App\Models;

use App\Support\ImageUrlBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Manga extends Model
{
    /** @use HasFactory<\Database\Factories\MangaFactory> */
    use HasFactory;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'title',
        'description',
        'status',
        'demographic',
        'content_rating',
        'year',
        'language',
        'cover_id',
        'cover_ext',
        'cover_image_url',
        'genres',
        'themes',
        'authors',
        'artists',
        'available_languages',
        'links',
        'chapters_count',
        'follows_count',
        'views_count',
        'source_payload',
        'synced_at',
    ];

    protected function casts(): array
    {
        return [
            'genres' => 'array',
            'themes' => 'array',
            'authors' => 'array',
            'artists' => 'array',
            'available_languages' => 'array',
            'links' => 'array',
            'source_payload' => 'array',
            'synced_at' => 'datetime',
            'year' => 'integer',
            'chapters_count' => 'integer',
            'follows_count' => 'integer',
            'views_count' => 'integer',
        ];
    }

    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class);
    }

    public function userMangas(): HasMany
    {
        return $this->hasMany(UserManga::class);
    }

    public function readingProgress(): HasMany
    {
        return $this->hasMany(ReadingProgress::class);
    }

    public function getCoverImageUrl(bool $useProxy = false): ?string
    {
        return ImageUrlBuilder::build($this->cover_image_url, $useProxy);
    }

    public function getWeebdexUrl(): string
    {
        return "https://weebdex.org/title/{$this->id}";
    }
}
