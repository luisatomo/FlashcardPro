<?php

namespace App\Livewire;

use App\Actions\CreateFlashcard;
use App\Actions\DeleteFlashcard;
use App\Actions\UpdateFlashcard;
use App\Livewire\Forms\FlashcardForm;
use App\Models\Deck;
use App\Models\Flashcard;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Livewire\Component;

class Flashcards extends Component
{
    use AuthorizesRequests;

    public FlashcardForm $form;
    public Collection $flashcards;
    public bool $showForm = false;
    public Deck $deck;

    public function mount(Deck $deck): void
    {
        $this->form->setDeck($deck);
        $this->deck = $deck;
        $this->loadFlashcards();
    }

    public function loadFlashcards(): void
    {
        $this->flashcards = $this->form->deck->flashcards()->latest()->get() ?? collect();
    }

    public function saveFlashcard(CreateFlashcard $create, UpdateFlashcard $update): void
    {
        $this->form->validate();
        $sanitizedData = $this->form->getSanitizedData();

        if ($this->form->editingId) {
            $flashcard = Flashcard::findOrFail($this->form->editingId);
            $this->authorize('update', $flashcard);
            $update($flashcard, $sanitizedData);
            session()->flash('success', 'Card updated!');
        } else {
            $this->authorize('create', [Flashcard::class, $this->form->deck]);
            $create($sanitizedData);
            session()->flash('success', 'Card created!');
        }

        $this->cancelForm();
        $this->loadFlashcards();
        $this->showForm = false;
    }

    public function deleteFlashcard(DeleteFlashcard $deleteFlashcard, int $id): void
    {
        $flashcard = Flashcard::findOrFail($id);
        $this->authorize('delete', $flashcard);
        $deleteFlashcard($flashcard);

        session()->flash('success', 'Flashcard deleted successfully.');
        $this->loadFlashcards();
    }

    public function showCreateForm(): void
    {
        $this->cancelForm();
        $this->showForm = true;
    }

    public function showEditForm(int $flashcardId): void
    {
        $flashcard             = Flashcard::findOrFail($flashcardId);
        $this->form->fill([
            'question' => $flashcard->question,
            'answer' => $flashcard->answer,
            'public' => $flashcard->public,
            'id' => $flashcard->id,
        ]);
        $this->showForm   = true;
    }

    public function cancelForm(): void
    {
        $this->form->reset();
        $this->form->setDeck($this->deck);
    }

    public function render()
    {
        return view('livewire.flashcards')
            ->layout('layouts.app');
    }
}
