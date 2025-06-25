<?php

namespace App\Livewire;

use App\Actions\CreateDeck;
use App\Actions\DeleteDeck;
use App\Actions\UpdateDeck;
use App\Livewire\Forms\DeckForm;
use App\Models\Deck;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Livewire\Component;

class Decks extends Component
{
    use AuthorizesRequests;

    public DeckForm $form;
    public Collection $decks;
    public bool $showForm = false;

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
        $this->form->validate();
        $sanitizedData = $this->form->getSanitizedData();

        if ($this->form->editingId) {
            $deck = Deck::findOrFail($this->form->editingId);
            $this->authorize('update', $deck);
            $update($deck, $sanitizedData);
            session()->flash('success', 'Deck updated!');
        } else {
            $this->authorize('create', Deck::class);
            $create($sanitizedData);
            session()->flash('success', 'Deck created!');
        }

        $this->cancelForm();
        $this->loadDecks();
    }

    public function deleteDeck(DeleteDeck $deleteDeck, int $id): void
    {
        $deck = Deck::findOrFail($id);
        $this->authorize('delete', $deck);
        $deleteDeck($deck);

        session()->flash('success', 'Deck deleted successfully.');
        $this->loadDecks();
    }

    public function showCreateForm(): void
    {
        $this->form->reset();
        $this->showForm = true;
    }

    public function showEditForm(int $deckId): void
    {
        $deck = Deck::findOrFail($deckId);
        $this->authorize('update', $deck);

        $this->form->fill([
            'name' => $deck->name,
            'public' => $deck->public,
            'id' => $deck->id,
        ]);

        $this->showForm = true;
    }

    public function cancelForm(): void
    {
        $this->form->reset();
        $this->showForm = false;
    }

    public function render()
    {
        return view('livewire.decks')
            ->layout('layouts.app');
    }
}
