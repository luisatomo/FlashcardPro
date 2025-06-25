<?php

namespace App\Services;

use App\Models\Deck;
use Illuminate\Database\Eloquent\Model;

class DeckService
{
    /**
     * Get the last public deck.
     *
     * @return Model|null
     */
    public function lastPublicDeck(): ?Model
    {
        return Deck::query()->where('public', true)->latest()->first() ?? null;
    }
}
