<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DeckResource;
use App\Services\DeckService;
use Illuminate\Http\Resources\Json\JsonResource;

class DeckController extends Controller
{
    public function __construct(private readonly DeckService $deckService)
    {
    }

    /**
     * Display the specified resource.
     */
    public function lastPublic(): JsonResource
    {
        $deck = $this->deckService->lastPublicDeck();

        return DeckResource::make($deck);
    }
}
