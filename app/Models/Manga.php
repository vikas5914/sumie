<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Manga extends Model
{
    /** @use HasFactory<\Database\Factories\MangaFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'author',
        'artist',
        'description',
        'cover_image_url',
        'banner_image_url',
        'status',
        'content_rating',
        'genres',
        'themes',
        'demographics',
        'total_chapters',
        'release_year',
        'country_of_origin',
        'rating_average',
        'rating_count',
        'view_count',
        'source_name',
        'source_url',
        'external_id',
        'is_featured',
        'is_licensed',
        'published_at',
        'latest_uploaded_chapter',
        'last_fetched_at',
        // New fields from MangaDex API
        'alt_titles',
        'original_language',
        'last_volume',
        'last_chapter',
        'links',
        'available_translated_languages',
        'is_locked',
        'api_version',
        'created_at_api',
        'updated_at_api',
        'chapter_numbers_reset_on_new_volume',
        'state',
        'content_tags',
        'format_tags',
        'latest_uploaded_chapter_uuid',
        'demographics_data',
    ];

    protected function casts(): array
    {
        return [
            'genres' => 'array',
            'themes' => 'array',
            'demographics' => 'array',
            'demographics_data' => 'array',
            'rating_average' => 'decimal:2',
            'progress_percentage' => 'decimal:2',
            'is_featured' => 'boolean',
            'is_licensed' => 'boolean',
            'is_locked' => 'boolean',
            'chapter_numbers_reset_on_new_volume' => 'boolean',
            'published_at' => 'datetime',
            'latest_uploaded_chapter' => 'datetime',
            'last_fetched_at' => 'datetime',
            'created_at_api' => 'datetime',
            'updated_at_api' => 'datetime',
            // New fields
            'alt_titles' => 'array',
            'links' => 'array',
            'available_translated_languages' => 'array',
            'content_tags' => 'array',
            'format_tags' => 'array',
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

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
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

    /**
     * Get proxied cover image URL
     */
    public function getProxiedCoverUrl(): ?string
    {
        if (! $this->cover_image_url) {
            return null;
        }

        return route('image.proxy', ['encodedUrl' => base64_encode($this->cover_image_url)]);
    }

    /**
     * Get proxied banner image URL
     */
    public function getProxiedBannerUrl(): ?string
    {
        if (! $this->banner_image_url) {
            return null;
        }

        return route('image.proxy', ['encodedUrl' => base64_encode($this->banner_image_url)]);
    }

    /**
     * Get MangaDex URL for this manga
     */
    public function getMangaDexUrl(): ?string
    {
        if (! $this->external_id) {
            return null;
        }

        return "https://mangadex.org/title/{$this->external_id}";
    }

    /**
     * Get MyAnimeList URL from links
     */
    public function getMyAnimeListUrl(): ?string
    {
        $malId = $this->links['mal'] ?? null;
        if (! $malId) {
            return null;
        }

        return "https://myanimelist.net/manga/{$malId}";
    }

    /**
     * Get AniList URL from links
     */
    public function getAniListUrl(): ?string
    {
        $alId = $this->links['al'] ?? null;
        if (! $alId) {
            return null;
        }

        return "https://anilist.co/manga/{$alId}";
    }

    /**
     * Get all alternative titles as a flat array
     */
    public function getAllTitles(): array
    {
        $titles = [$this->title];

        if ($this->alt_titles) {
            foreach ($this->alt_titles as $localizedTitle) {
                foreach ($localizedTitle as $language => $title) {
                    if (! in_array($title, $titles)) {
                        $titles[] = $title;
                    }
                }
            }
        }

        return $titles;
    }

    /**
     * Check if manga has specific content rating
     */
    public function isContentRating(string $rating): bool
    {
        return $this->content_rating === $rating;
    }

    /**
     * Get content tag names
     */
    public function getContentTags(): array
    {
        return $this->content_tags ?? [];
    }

    /**
     * Get format tag names
     */
    public function getFormatTags(): array
    {
        return $this->format_tags ?? [];
    }

    /**
     * Get available translation languages as array
     */
    public function getAvailableLanguages(): array
    {
        return $this->available_translated_languages ?? [];
    }

    /**
     * Check if translation is available in specific language
     */
    public function hasTranslation(string $language): bool
    {
        return in_array($language, $this->getAvailableLanguages(), true);
    }

    /**
     * Get demographics as string (singular value from API)
     */
    public function getDemographics(): ?string
    {
        $demographics = $this->demographics_data ?? $this->demographics ?? [];

        return $demographics[0] ?? null;
    }

    /**
     * Get last chapter number as integer (if numeric)
     */
    public function getLastChapterNumber(): ?int
    {
        if (! $this->last_chapter) {
            return null;
        }

        return is_numeric($this->last_chapter) ? (int) $this->last_chapter : null;
    }
}
