<?php

namespace Database\Seeders;

use App\Models\Chapter;
use App\Models\Manga;
use Illuminate\Database\Seeder;

class MangaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mangas = Manga::factory()->count(10)->create();

        foreach ($mangas as $manga) {
            $chapterCount = min(10, max(3, (int) floor($manga->chapters_count / 30)));

            for ($chapterNumber = 1; $chapterNumber <= $chapterCount; $chapterNumber++) {
                Chapter::factory()->create([
                    'manga_id' => $manga->id,
                    'chapter_number' => (string) $chapterNumber,
                    'source_payload' => [
                        'id' => 'seed-'.$manga->id.'-'.$chapterNumber,
                        'chapter' => (string) $chapterNumber,
                    ],
                ]);
            }
        }
    }
}
