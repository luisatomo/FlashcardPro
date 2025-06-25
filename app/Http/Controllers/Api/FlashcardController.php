<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FlashcardResource;
use App\Services\FlashcardService;
use Illuminate\Http\Resources\Json\JsonResource;

class FlashcardController extends Controller
{
    public function __construct(private readonly FlashcardService $flashcardService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function public(): JsonResource
    {
        $flashcards = $this->flashcardService->publicFlashcards();

        return FlashcardResource::collection($flashcards);
    }
}
