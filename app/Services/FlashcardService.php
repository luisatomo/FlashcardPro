<?php

namespace App\Services;

use App\Models\Flashcard;
use Illuminate\Database\Eloquent\Collection;

class FlashcardService
{
    /**
     * Get all public flashcards.
     *
     * @return Collection
     */
    public function publicFlashcards(): Collection
    {
        return Flashcard::query()->where('public', true)->get();
    }
}
