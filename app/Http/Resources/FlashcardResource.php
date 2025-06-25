<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FlashcardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if (empty($this->uuid)) {
            return [];
        }

        return [
            'uuid' => $this->uuid,
            'question' => $this->question,
            'answer' => $this->answer,
            'public' => $this->public,
            'deck' => DeckResource::make($this->whenLoaded('deck')),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
