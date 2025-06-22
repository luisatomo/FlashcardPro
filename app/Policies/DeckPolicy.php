<?php

namespace App\Policies;

use App\Models\Deck;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DeckPolicy
{
    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, Deck $deck): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Deck $deck): bool
    {
        return false;
    }

    public function delete(User $user, Deck $deck): bool
    {
        return false;
    }

    public function restore(User $user, Deck $deck): bool
    {
        return false;
    }

    public function forceDelete(User $user, Deck $deck): bool
    {
        return false;
    }
}
