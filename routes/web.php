<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Decks;
use App\Livewire\Flashcards;

Route::redirect('/', '/login');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')
        ->name('dashboard');
    Route::get('decks', Decks::class)
        ->name('decks');
    Route::get('decks/{deck}/flashcards', Flashcards::class)
        ->name('flashcards');
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__ . '/auth.php';
