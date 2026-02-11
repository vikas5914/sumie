<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Manga>
 */
class MangaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->words(3, true);
        $slug = Str::slug($title.'-'.fake()->unique()->numberBetween(1000, 9999));
        $hashId = fake()->unique()->regexify('[a-z0-9]{5}');

        return [
            'id' => $hashId,
            'slug' => $slug,
            'source_manga_id' => fake()->numberBetween(1, 200000),
            'title' => $title,
            'description' => fake()->paragraph(),
            'cover_image_url' => 'https://static.comix.to/'.fake()->lexify('????').'/i/'.fake()->lexify('?').'/'.fake()->lexify('??').'/'.fake()->lexify('????????????').'.jpg',
            'banner_image_url' => null,
            'author' => fake()->name(),
            'artist' => fake()->name(),
            'type' => fake()->randomElement(['manga', 'manhwa', 'manhua']),
            'status' => fake()->randomElement(['ongoing', 'completed', 'hiatus']),
            'content_rating' => fake()->randomElement(['safe', 'suggestive']),
            'is_nsfw' => false,
            'genres' => fake()->randomElements(['Action', 'Adventure', 'Comedy', 'Drama', 'Fantasy', 'Horror', 'Romance', 'Sci-Fi'], 3),
            'themes' => fake()->randomElements(['Magic', 'School Life', 'Supernatural', 'Reincarnation'], 2),
            'demographics' => fake()->randomElements(['Shounen', 'Seinen', 'Shoujo', 'Josei'], 1),
            'formats' => fake()->randomElements(['Web Comic', 'Long Strip', 'Oneshot'], 1),
            'total_chapters' => fake()->numberBetween(10, 300),
            'release_year' => fake()->numberBetween(1990, 2026),
            'country_of_origin' => fake()->randomElement(['Japan', 'Korea', 'China']),
            'rating_average' => fake()->randomFloat(2, 6, 9.8),
            'rating_count' => fake()->numberBetween(50, 5000),
            'view_count' => fake()->numberBetween(100, 50000),
            'source_name' => 'Comix',
            'source_url' => 'https://comix.to/comic/'.$slug,
            'links' => [],
            'last_fetched_at' => now(),
            'created_at_api' => now()->subDays(10),
            'updated_at_api' => now()->subDay(),
        ];
    }
}
