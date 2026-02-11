<?php

namespace Database\Factories;

use App\Models\Manga;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Chapter>
 */
class ChapterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'manga_id' => Manga::factory(),
            'chapter_number' => fake()->randomFloat(2, 1, 300),
            'chapter_label' => (string) fake()->randomFloat(1, 1, 300),
            'volume_number' => (string) fake()->numberBetween(1, 40),
            'title' => fake()->sentence(4),
            'page_count' => fake()->numberBetween(10, 45),
            'release_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'is_published' => true,
            'language' => 'en',
            'source_url' => 'https://comix.to/chapter/'.fake()->numberBetween(1000, 9999999),
            'external_id' => (string) fake()->numberBetween(1000, 9999999),
        ];
    }
}
