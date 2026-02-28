<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

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
        $id = fake()->unique()->regexify('[a-z0-9]{10}');
        $coverId = fake()->regexify('[a-z0-9]{10}');
        $coverExt = fake()->randomElement(['.jpg', '.png', '.webp']);
        $coverImageUrl = sprintf('https://srv.weebdex.net/covers/%s/%s%s', $id, $coverId, $coverExt);
        $tags = fake()->randomElements(['Action', 'Adventure', 'Comedy', 'Drama', 'Fantasy', 'Horror', 'Romance', 'Sci-Fi'], 3);

        return [
            'id' => $id,
            'title' => $title,
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(['ongoing', 'completed', 'hiatus']),
            'content_rating' => fake()->randomElement(['safe', 'suggestive']),
            'demographic' => fake()->randomElement(['shounen', 'seinen', 'shoujo', 'josei']),
            'year' => fake()->numberBetween(1990, 2026),
            'language' => fake()->randomElement(['ja', 'ko', 'zh']),
            'cover_id' => $coverId,
            'cover_ext' => $coverExt,
            'cover_image_url' => $coverImageUrl,
            'genres' => $tags,
            'themes' => fake()->randomElements(['Magic', 'School Life', 'Supernatural', 'Reincarnation'], 2),
            'authors' => [fake()->name()],
            'artists' => [fake()->name()],
            'available_languages' => ['en'],
            'links' => [
                'al' => 'https://anilist.co',
            ],
            'chapters_count' => fake()->numberBetween(10, 300),
            'follows_count' => fake()->numberBetween(50, 10000),
            'views_count' => fake()->numberBetween(100, 50000),
            'source_payload' => [
                'id' => $id,
                'title' => $title,
            ],
            'synced_at' => now(),
        ];
    }
}
