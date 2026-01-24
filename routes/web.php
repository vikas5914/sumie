<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('onboarding');
})->name('onboarding');

Route::get('/home', function () {
    return Inertia::render('home');
})->name('home');

Route::get('/search', function () {
    return Inertia::render('search');
})->name('search');

Route::get('/library', function () {
    return Inertia::render('library');
})->name('library');

Route::get('/me', function () {
    return Inertia::render('me');
})->name('me');

Route::get('/manga/{id}', function ($id) {
    return Inertia::render('manga-detail', ['id' => $id]);
})->name('manga.show');
