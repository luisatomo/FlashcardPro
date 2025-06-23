<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Flashcard;
use Illuminate\Support\Facades\DB;

final class DeleteFlashcard
{
    public function __invoke(Flashcard $flashcard): void
    {
        DB::transaction(fn () => $flashcard->delete());
    }
}
