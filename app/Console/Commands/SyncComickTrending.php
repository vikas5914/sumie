<?php

namespace App\Console\Commands;

use App\Models\Manga;
use App\Services\ComickApiService;
use Illuminate\Console\Command;

class SyncComickTrending extends Command
{
    protected $signature = 'comick:sync-trending
                            {--limit=10 : Number of trending manga to sync}
                            {--sync-chapters : Also sync chapter data}';

    protected $description = 'Sync trending manga from Comix API to local database';

    public function handle(ComickApiService $apiService): int
    {
        $limit = (int) $this->option('limit');
        $syncChapters = (bool) $this->option('sync-chapters');

        $this->info("Fetching {$limit} trending manga from Comix...");

        try {
            $mangas = $apiService->getTrendingManga($limit);

            $this->info("Found {$mangas->count()} manga. Syncing to database...");

            $progressBar = $this->output->createProgressBar($mangas->count());
            $progressBar->start();

            foreach ($mangas as $mangaData) {
                $manga = $apiService->syncMangaToDatabase($mangaData);

                if ($syncChapters) {
                    $this->syncChaptersForManga($apiService, $manga);
                }

                $progressBar->advance();
            }

            $progressBar->finish();
            $this->newLine();
            $this->info('Sync completed successfully!');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Error syncing manga: '.$e->getMessage());

            return self::FAILURE;
        }
    }

    private function syncChaptersForManga(ComickApiService $apiService, Manga $manga): void
    {
        try {
            $chapters = $apiService->getMangaChaptersBySlug($manga->id);
            $apiService->syncChapters($manga, $chapters);
        } catch (\Throwable $e) {
            $this->warn("Failed to sync chapters for {$manga->title}: {$e->getMessage()}");
        }
    }
}
