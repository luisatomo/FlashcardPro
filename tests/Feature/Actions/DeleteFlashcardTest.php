<?php

namespace Feature\Actions;

use App\Actions\DeleteFlashcard;
use App\Models\Deck;
use App\Models\Flashcard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteFlashcardTest extends TestCase
{
    use RefreshDatabase;

    public function test_deletes_flashcard()
    {
        $user = User::factory()->create();
        $deck = Deck::factory()->create(['user_id' => $user->id]);
        $flashcard = Flashcard::factory()->create(['deck_id' => $deck->id]);

        $action = new DeleteFlashcard();
        $action($flashcard);

        $this->assertDatabaseMissing('flashcards', ['id' => $flashcard->id]);
    }

    public function test_handles_nonexistent_flashcard_gracefully()
    {
        $user = User::factory()->create();
        $deck = Deck::factory()->create(['user_id' => $user->id]);
        $flashcard = Flashcard::factory()->create(['deck_id' => $deck->id]);

        // Delete the flashcard first
        $flashcard->delete();

        $action = new DeleteFlashcard();

        // This should not throw an exception
        $action($flashcard);

        $this->assertTrue(true); // Test passes if no exception is thrown
    }
}
