<?php

namespace App\Livewire;

use App\Actions\CreateFlashcard;
use App\Actions\DeleteFlashcard;
use App\Actions\UpdateFlashcard;
use App\Models\Deck;
use App\Models\Flashcard;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Livewire\Component;

class Flashcards extends Component
{
    use AuthorizesRequests;

    public string $question = '';
    public string $answer = '';
    public bool $public = false;
    public Collection $flashcards;
    public bool $showForm = false;
    public ?int $editingId  = null;
    public Deck $deck;

    protected array $rules = [
        'question'   => 'required|string|min:3|max:255',
        'answer'   => 'required|string|min:2',
        'public' => 'boolean',
    ];

    public function mount(Deck $deck): void
    {
        $this->deck = $deck;
        $this->loadFlashcards();
    }

    public function loadFlashcards(): void
    {
        $this->flashcards = $this->deck->flashcards()->latest()->get() ?? collect();
    }

    public function saveFlashcard(CreateFlashcard $create, UpdateFlashcard $update): void
    {
        $this->validate();

        if ($this->editingId) {
            $flashcard = Flashcard::findOrFail($this->editingId);
            $this->authorize('update', $flashcard);
            $update($flashcard, [
                'question'   => $this->question,
                'answer' => $this->answer,
                'public' => $this->public,
            ]);
            session()->flash('success', 'Card updated!');
        } else {
            $this->authorize('create', [Flashcard::class, $this->deck]);
            $create([
                'question'   => $this->question,
                'answer' => $this->answer,
                'public' => $this->public,
                'deck' => $this->deck,
            ]);
            session()->flash('success', 'Card created!');
        }

        $this->cancelForm();
        $this->loadFlashcards();
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
        $this->question       = $flashcard->question;
        $this->answer       = $flashcard->answer;
        $this->public     = $flashcard->public;
        $this->editingId  = $flashcard->id;
        $this->showForm   = true;
    }

    public function cancelForm(): void
    {
        $this->reset(['question', 'public', 'answer', 'editingId', 'showForm']);
    }

    public function render()
    {
        return view('livewire.flashcards')
            ->layout('layouts.app');
    }
}
