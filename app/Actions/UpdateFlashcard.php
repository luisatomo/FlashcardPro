<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Flashcard;
use Illuminate\Support\Facades\DB;

final class UpdateFlashcard
{
    public function __invoke(Flashcard $flashcard, array $data): Flashcard
    {
        return DB::transaction(function () use ($flashcard, $data): Flashcard {
            $flashcard->update([
                'question'   => $data['question'] ?? $flashcard->question,
                'answer'   => $data['answer'] ?? $flashcard->answer,
                'public' => array_key_exists('public', $data) ? (bool) $data['public'] : $flashcard->public,
            ]);

            return $flashcard->refresh();
        });
    }
}
