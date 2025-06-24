<?php

namespace App\Livewire;

use App\Models\Deck;
use App\Models\Flashcard;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Livewire\Component;

class StudyMode extends Component
{
    use AuthorizesRequests;

    public string $question = '';
    public string $answer = '';
    public int $current = 0;
    public int $score = 0;
    public Collection $flashcards;
    public bool $showAnswer = false;
    public Deck $deck;

    public function mount(Deck $deck): void
    {
        $this->deck = $deck;
        $this->loadFlashcards();
    }

    public function loadFlashcards(): void
    {
        $this->flashcards = $this->deck->flashcards()->inRandomOrder()->get() ?? collect();
    }

    public function showAnswer(): void
    {
        $this->showAnswer = true;
    }

    public function render()
    {
        return view('livewire.study-mode')
            ->layout('layouts.app');
    }
}
