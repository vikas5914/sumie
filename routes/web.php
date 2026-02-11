<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImageProxyController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\MangaController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UserMangaController;
use App\Http\Controllers\UserPreferenceController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [OnboardingController::class, 'show'])->name('onboarding');
Route::post('/onboarding', [OnboardingController::class, 'store'])->name('onboarding.store');

// Image proxy route (public, no auth required for caching)
Route::get('/images/proxy/{encodedUrl}', ImageProxyController::class)->name('image.proxy')->where('encodedUrl', '.*');

Route::middleware(['auth'])->group(function () {
    $slugPattern = '[A-Za-z0-9][A-Za-z0-9\-]*';

    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::post('/home/refresh', [HomeController::class, 'refresh'])->name('home.refresh');
    Route::get('/search', [SearchController::class, 'index'])->name('search');
    Route::get('/library', [LibraryController::class, 'index'])->name('library');
    Route::get('/manga/{id}/read/{chapterId}', [MangaController::class, 'read'])->name('manga.read')
        ->where([
            'id' => $slugPattern,
            'chapterId' => '[A-Za-z0-9]+',
        ]);
    Route::get('/manga/{id}', [MangaController::class, 'show'])->name('manga.show')
        ->where('id', $slugPattern);

    Route::get('/me', function () {
        return Inertia::render('me');
    })->name('me');
    Route::patch('/me/preferences/image-proxy', [UserPreferenceController::class, 'updateImageProxy'])->name('me.preferences.image-proxy');

    // Library management routes
    Route::post('/library/manga/{mangaId}', [UserMangaController::class, 'store'])->name('library.store')
        ->where('mangaId', $slugPattern);
    Route::patch('/library/{id}/status', [UserMangaController::class, 'updateStatus'])->name('library.update-status');
    Route::patch('/library/{id}/favorite', [UserMangaController::class, 'toggleFavorite'])->name('library.toggle-favorite');
    Route::delete('/library/{id}', [UserMangaController::class, 'destroy'])->name('library.destroy');
});
