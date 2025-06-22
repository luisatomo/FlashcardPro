<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Deck;
use Illuminate\Support\Facades\DB;

final class DeleteDeck
{
    public function __invoke(Deck $deck): void
    {
        DB::transaction(fn () => $deck->delete());
    }
}
