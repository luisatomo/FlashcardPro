<?php

namespace Feature\Actions;

use App\Actions\UpdateFlashcard;
use App\Models\Deck;
use App\Models\Flashcard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateFlashcardTest extends TestCase
{
    use RefreshDatabase;

    public function test_updates_flashcard_with_all_data()
    {
        $user = User::factory()->create();
        $deck = Deck::factory()->create(['user_id' => $user->id]);
        $flashcard = Flashcard::factory()->create([
            'deck_id' => $deck->id,
            'question' => 'Original question',
            'answer' => 'Original answer',
            'public' => false,
        ]);

        $action = new UpdateFlashcard();
        $updatedFlashcard = $action($flashcard, [
            'question' => 'Updated question',
            'answer' => 'Updated answer',
            'public' => true,
        ]);

        $this->assertEquals('Updated question', $updatedFlashcard->question);
        $this->assertEquals('Updated answer', $updatedFlashcard->answer);
        $this->assertTrue($updatedFlashcard->public);
    }

    public function test_updates_flashcard_partially()
    {
        $user = User::factory()->create();
        $deck = Deck::factory()->create(['user_id' => $user->id]);
        $flashcard = Flashcard::factory()->create([
            'deck_id' => $deck->id,
            'question' => 'Original question',
            'answer' => 'Original answer',
            'public' => false,
        ]);

        $action = new UpdateFlashcard();
        $updatedFlashcard = $action($flashcard, [
            'question' => 'Updated question only',
        ]);

        $this->assertEquals('Updated question only', $updatedFlashcard->question);
        $this->assertEquals('Original answer', $updatedFlashcard->answer);
        $this->assertFalse($updatedFlashcard->public);
    }

    public function test_handles_public_boolean_conversion()
    {
        $user = User::factory()->create();
        $deck = Deck::factory()->create(['user_id' => $user->id]);
        $flashcard = Flashcard::factory()->create([
            'deck_id' => $deck->id,
            'public' => false,
        ]);

        $action = new UpdateFlashcard();

        // Test with string "1"
        $updatedFlashcard = $action($flashcard, ['public' => '1']);
        $this->assertTrue($updatedFlashcard->public);

        // Test with string "0"
        $updatedFlashcard = $action($flashcard, ['public' => '0']);
        $this->assertFalse($updatedFlashcard->public);
    }

    public function test_persists_changes_to_database()
    {
        $user = User::factory()->create();
        $deck = Deck::factory()->create(['user_id' => $user->id]);
        $flashcard = Flashcard::factory()->create([
            'deck_id' => $deck->id,
            'question' => 'Original',
            'answer' => 'Original',
            'public' => false,
        ]);

        $action = new UpdateFlashcard();
        $action($flashcard, [
            'question' => 'Updated in DB',
            'answer' => 'Updated in DB',
            'public' => true,
        ]);

        $this->assertDatabaseHas('flashcards', [
            'id' => $flashcard->id,
            'question' => 'Updated in DB',
            'answer' => 'Updated in DB',
            'public' => true,
        ]);
    }
}
