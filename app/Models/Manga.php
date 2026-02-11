<?php

namespace App\Models;

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
        'slug',
        'source_manga_id',
        'title',
        'description',
        'cover_image_url',
        'banner_image_url',
        'author',
        'artist',
        'type',
        'status',
        'content_rating',
        'is_nsfw',
        'genres',
        'themes',
        'demographics',
        'formats',
        'total_chapters',
        'release_year',
        'country_of_origin',
        'rating_average',
        'rating_count',
        'view_count',
        'source_name',
        'source_url',
        'links',
        'last_fetched_at',
        'created_at_api',
        'updated_at_api',
    ];

    protected function casts(): array
    {
        return [
            'source_manga_id' => 'integer',
            'is_nsfw' => 'boolean',
            'genres' => 'array',
            'themes' => 'array',
            'demographics' => 'array',
            'formats' => 'array',
            'links' => 'array',
            'rating_average' => 'decimal:2',
            'last_fetched_at' => 'datetime',
            'created_at_api' => 'datetime',
            'updated_at_api' => 'datetime',
        ];
    }

    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class)->orderBy('chapter_number');
    }

    public function userMangas(): HasMany
    {
        return $this->hasMany(UserManga::class);
    }

    public function readingProgress(): HasMany
    {
        return $this->hasMany(ReadingProgress::class);
    }

    public function scopeOngoing($query)
    {
        return $query->where('status', 'ongoing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByGenre($query, string $genre)
    {
        return $query->whereJsonContains('genres', $genre);
    }

    public function getCoverImageUrl(bool $useProxy = false): ?string
    {
        if (! $this->cover_image_url) {
            return null;
        }

        if (! $useProxy) {
            return $this->cover_image_url;
        }

        return route('image.proxy', ['encodedUrl' => base64_encode($this->cover_image_url)]);
    }

    public function getBannerImageUrl(bool $useProxy = false): ?string
    {
        if (! $this->banner_image_url) {
            return null;
        }

        if (! $useProxy) {
            return $this->banner_image_url;
        }

        return route('image.proxy', ['encodedUrl' => base64_encode($this->banner_image_url)]);
    }

    public function getComixUrl(): ?string
    {
        if (! $this->slug) {
            return null;
        }

        return "https://comix.to/comic/{$this->slug}";
    }

    public function getComickUrl(): ?string
    {
        return $this->getComixUrl();
    }
}
