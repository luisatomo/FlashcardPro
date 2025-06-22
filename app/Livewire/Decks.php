<?php

namespace App\Livewire;

use Illuminate\Support\Collection;
use Livewire\Component;

class Decks extends Component
{
    public $name = '';
    public $public = false;
    public Collection $decks;

    public function mount(): void
    {
        $this->loadDecks();
    }

    public function loadDecks(): void
    {
        $this->decks = auth()->user()->decks()->latest()->get() ?? collect();
    }

    public function render()
    {
        return view('livewire.decks')
            ->layout('layouts.app');
    }
}
