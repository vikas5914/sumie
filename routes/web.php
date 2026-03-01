<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\MangaController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UserMangaController;
use Illuminate\Support\Facades\Route;

Route::get('/', [OnboardingController::class, 'show'])->name('onboarding');
Route::post('/onboarding', [OnboardingController::class, 'store'])->name('onboarding.store');

$slugPattern = '[A-Za-z0-9][A-Za-z0-9\-]*';

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/library', [LibraryController::class, 'index'])->name('library');
Route::get('/manga/{id}/read/{chapterId}', [MangaController::class, 'read'])->name('manga.read')
    ->where([
        'id' => $slugPattern,
        'chapterId' => '[A-Za-z0-9]+',
    ]);
Route::get('/manga/{id}', [MangaController::class, 'show'])->name('manga.show')
    ->where('id', $slugPattern);

Route::get('/me', [ProfileController::class, 'index'])->name('me');

Route::post('/library/manga/{mangaId}', [UserMangaController::class, 'store'])->name('library.store')
    ->where('mangaId', $slugPattern);
Route::post('/library/manga/{mangaId}/bookmark', [UserMangaController::class, 'toggleBookmark'])->name('library.bookmark.toggle')
    ->where('mangaId', $slugPattern);
Route::patch('/library/{id}/status', [UserMangaController::class, 'updateStatus'])->name('library.update-status');
Route::patch('/library/{id}/favorite', [UserMangaController::class, 'toggleFavorite'])->name('library.toggle-favorite');
Route::delete('/library/{id}', [UserMangaController::class, 'destroy'])->name('library.destroy');
