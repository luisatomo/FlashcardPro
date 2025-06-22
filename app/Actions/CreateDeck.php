<?php
// app/Actions/CreateDeck.php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Deck;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class CreateDeck
{
    /**
     * Crea un deck y lo asocia al usuario dado (o al usuario autenticado).
     *
     * @param  array{
     *     name: string,
     *     public?: bool,
     *     user?: \App\Models\User
     * }  $data
     */
    public function __invoke(array $data): Deck
    {
        /** @var User $user */
        $user = $data['user'] ?? Auth::user();

        return DB::transaction(function () use ($data, $user): Deck {
            return Deck::create([
                'uuid'   => Str::uuid()->toString(),
                'name'   => $data['name'],
                'public' => $data['public'] ?? false,
                'user_id'=> $user->id,
            ]);
        });
    }
}
