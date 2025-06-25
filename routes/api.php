<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DeckController;
use App\Http\Controllers\Api\FlashcardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
});

Route::middleware(['auth:sanctum', \App\Http\Middleware\DebugRequestLogger::class])->group(function () {
    Route::get('/last-public-deck', [DeckController::class, 'lastPublic']);;
    Route::get('/public-flashcards', [FlashcardController::class, 'public']);;
});
