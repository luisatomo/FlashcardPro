<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Flashcard;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class CreateFlashcard
{
    public function __invoke(array $data): Flashcard
    {
        $deck = $data['deck'];

        return DB::transaction(function () use ($data, $deck): Flashcard {
            return Flashcard::create([
                'uuid'   => Str::uuid()->toString(),
                'question' => $data['question'],
                'answer' => $data['answer'],
                'public' => $data['public'] ?? false,
                'deck_id' => $deck->id,
            ]);
        });
    }
}
