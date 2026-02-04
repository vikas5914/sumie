<?php

namespace App\Console\Commands;

use App\Services\MangaDexApiService;
use Illuminate\Console\Command;

class SyncMangaDexTrending extends Command
{
    protected $signature = 'mangadex:sync-trending 
                            {--limit=10 : Number of trending manga to sync}
                            {--sync-chapters : Also sync chapter data}';

    protected $description = 'Sync trending manga from MangaDex API to local database';

    public function handle(MangaDexApiService $apiService): int
    {
        $limit = (int) $this->option('limit');
        $syncChapters = $this->option('sync-chapters');

        $this->info("Fetching {$limit} trending manga from MangaDex...");

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

        } catch (\Exception $e) {
            $this->error('Error syncing manga: '.$e->getMessage());

            return self::FAILURE;
        }
    }

    private function syncChaptersForManga(MangaDexApiService $apiService, $manga): void
    {
        try {
            $chapters = $apiService->getMangaFeed($manga->external_id, 10);
            $apiService->syncChapters($manga, $chapters);
        } catch (\Exception $e) {
            $this->warn("Failed to sync chapters for {$manga->title}: {$e->getMessage()}");
        }
    }
}
