<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Deck;
use Illuminate\Support\Facades\DB;

final class UpdateDeck
{
    public function __invoke(Deck $deck, array $data): Deck
    {
        return DB::transaction(function () use ($deck, $data): Deck {
            $deck->update([
                'name'   => $data['name'] ?? $deck->name,
                'public' => array_key_exists('public', $data) ? (bool) $data['public'] : $deck->public,
            ]);

            return $deck->refresh();
        });
    }
}
