<?php

namespace Tests\Feature\Actions;

use App\Actions\DeleteDeck;
use App\Models\Deck;
use App\Models\Flashcard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class DeleteDeckTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        // Mock authorization to allow deletion for unit testing
        Gate::define('delete', fn ($user, $deck) => true);
    }

    public function test_delete_deck_deletes_successfully(): void
    {
        $deck = Deck::factory()->create(['user_id' => $this->user->id]);
        $deckId = $deck->id;

        $action = new DeleteDeck();
        $action($deck);

        $this->assertNull(Deck::find($deckId));
    }

    public function test_delete_deck_deletes_with_associated_flashcards(): void
    {
        $deck = Deck::factory()->create(['user_id' => $this->user->id]);
        $flashcard1 = Flashcard::factory()->create(['deck_id' => $deck->id]);
        $flashcard2 = Flashcard::factory()->create(['deck_id' => $deck->id]);

        $action = new DeleteDeck();
        $action($deck);

        $this->assertNull(Deck::find($deck->id));
        $this->assertNull(Flashcard::find($flashcard1->id));
        $this->assertNull(Flashcard::find($flashcard2->id));
    }

    public function test_delete_deck_removes_from_database(): void
    {
        $deck = Deck::factory()->create(['user_id' => $this->user->id]);

        $action = new DeleteDeck();
        $action($deck);

        $this->assertDatabaseMissing('decks', ['id' => $deck->id]);
    }

    public function test_delete_deck_with_multiple_flashcards(): void
    {
        $deck = Deck::factory()->create(['user_id' => $this->user->id]);
        $flashcards = Flashcard::factory()->count(5)->create(['deck_id' => $deck->id]);

        $action = new DeleteDeck();
        $action($deck);

        $this->assertDatabaseMissing('decks', ['id' => $deck->id]);

        foreach ($flashcards as $flashcard) {
            $this->assertDatabaseMissing('flashcards', ['id' => $flashcard->id]);
        }
    }

    public function test_delete_deck_does_not_affect_other_decks(): void
    {
        $deck1 = Deck::factory()->create(['user_id' => $this->user->id]);
        $deck2 = Deck::factory()->create(['user_id' => $this->user->id]);
        $flashcard1 = Flashcard::factory()->create(['deck_id' => $deck1->id]);
        $flashcard2 = Flashcard::factory()->create(['deck_id' => $deck2->id]);

        $action = new DeleteDeck();
        $action($deck1);

        // Deck1 and its flashcard should be deleted
        $this->assertDatabaseMissing('decks', ['id' => $deck1->id]);
        $this->assertDatabaseMissing('flashcards', ['id' => $flashcard1->id]);

        // Deck2 and its flashcard should remain
        $this->assertDatabaseHas('decks', ['id' => $deck2->id]);
        $this->assertDatabaseHas('flashcards', ['id' => $flashcard2->id]);
    }

    public function test_delete_deck_handles_deck_without_flashcards(): void
    {
        $deck = Deck::factory()->create(['user_id' => $this->user->id]);

        $action = new DeleteDeck();
        $action($deck);

        $this->assertDatabaseMissing('decks', ['id' => $deck->id]);
    }
}
