<?php

namespace App\Livewire;

use App\Actions\CreateDeck;
use App\Actions\DeleteDeck;
use App\Actions\UpdateDeck;
use App\Models\Deck;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Livewire\Component;

class Decks extends Component
{
    use AuthorizesRequests;

    public string $name = '';
    public bool $public = false;
    public Collection $decks;
    public bool $showForm = false;
    public ?int $editingId  = null;

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

    public function saveDeck(CreateDeck $create, UpdateDeck $update): void
    {
        $this->validate();

        if ($this->editingId) {
            $deck = Deck::findOrFail($this->editingId);
            $this->authorize('update', Deck::class);
            $update($deck, [
                'name'   => $this->name,
                'public' => $this->public,
            ]);
            session()->flash('success', 'Deck updated!');
        } else {
            $this->authorize('create', Deck::class);
            $create([
                'name'   => $this->name,
                'public' => $this->public,
            ]);
            session()->flash('success', 'Deck created!');
        }

        $this->cancelForm();
        $this->loadDecks();
    }

    public function deleteDeck(DeleteDeck $deleteDeck, int $id): void
    {
        $deck = auth()->user()->decks()->findOrFail($id);
        $this->authorize('delete', $deck);
        $deleteDeck($deck);

        session()->flash('success', 'Deck deleted successfully.');
        $this->loadDecks();
    }

    public function showCreateForm(): void
    {
        $this->cancelForm();
        $this->showForm = true;
    }

    public function showEditForm(int $deckId): void
    {
        $deck             = Deck::whereKey($deckId)->whereBelongsTo(auth()->user())->firstOrFail();
        $this->name       = $deck->name;
        $this->public     = $deck->public;
        $this->editingId  = $deck->id;
        $this->showForm   = true;
    }

    public function cancelForm(): void
    {
        $this->reset(['name', 'public', 'editingId', 'showForm']);
    }

    public function render()
    {
        return view('livewire.decks')
            ->layout('layouts.app');
    }
}
