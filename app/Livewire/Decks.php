<?php

namespace App\Livewire;

use App\Actions\CreateDeck;
use Illuminate\Support\Collection;
use Livewire\Component;

class Decks extends Component
{
    public string $name = '';
    public bool $public = false;
    public Collection $decks;
    public bool $showForm = false;

    protected array $rules = [
        'name'   => 'required|string|min:3|max:100',
        'public' => 'boolean',
    ];

    public function mount(): void
    {
        $this->loadDecks();
    }

    public function loadDecks(): void
    {
        $this->decks = auth()->user()->decks()->latest()->get() ?? collect();
    }

    public function createDeck(CreateDeck $createDeck): void
    {
        $this->validate();              // <-- Livewire validation

        $createDeck([
            'name'   => $this->name,
            'public' => $this->public,
        ]);

        $this->reset(['name', 'public']);   // clear form
        $this->loadDecks();                 // refresh list

        session()->flash('success', 'Deck created!');
    }

    public function showCreateForm(): void
    {
        $this->showForm = true;
    }

    public function render()
    {
        return view('livewire.decks')
            ->layout('layouts.app');
    }
}
