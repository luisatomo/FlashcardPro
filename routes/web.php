<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Decks;
use App\Livewire\Flashcards;
use App\Livewire\StudyMode;

Route::redirect('/', '/login');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')
        ->name('dashboard');
    Route::get('decks', Decks::class)
        ->name('decks');
    Route::get('decks/{deck}/flashcards', Flashcards::class)
        ->name('flashcards');
    Route::get('decks/{deck}/study-mode', StudyMode::class)
        ->name('study-mode');
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__ . '/auth.php';
