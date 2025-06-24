<?php

namespace Feature\Actions;

use App\Actions\CreateFlashcard;
use App\Models\Deck;
use App\Models\Flashcard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateFlashcardTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_flashcard_with_required_data()
    {
        $user = User::factory()->create();
        $deck = Deck::factory()->create(['user_id' => $user->id]);
        $action = new CreateFlashcard();

        $flashcard = $action([
            'question' => 'What is PHP?',
            'answer' => 'A programming language',
            'public' => true,
            'deck' => $deck,
        ]);

        $this->assertInstanceOf(Flashcard::class, $flashcard);
        $this->assertEquals('What is PHP?', $flashcard->question);
        $this->assertEquals('A programming language', $flashcard->answer);
        $this->assertTrue($flashcard->public);
        $this->assertEquals($deck->id, $flashcard->deck_id);
        $this->assertNotEmpty($flashcard->uuid);
    }

    public function test_creates_flashcard_with_default_public_false()
    {
        $user = User::factory()->create();
        $deck = Deck::factory()->create(['user_id' => $user->id]);
        $action = new CreateFlashcard();

        $flashcard = $action([
            'question' => 'What is PHP?',
            'answer' => 'A programming language',
            'deck' => $deck,
        ]);

        $this->assertFalse($flashcard->public);
    }

    public function test_persists_flashcard_to_database()
    {
        $user = User::factory()->create();
        $deck = Deck::factory()->create(['user_id' => $user->id]);
        $action = new CreateFlashcard();

        $flashcard = $action([
            'question' => 'Test question',
            'answer' => 'Test answer',
            'public' => false,
            'deck' => $deck,
        ]);

        $this->assertDatabaseHas('flashcards', [
            'id' => $flashcard->id,
            'question' => 'Test question',
            'answer' => 'Test answer',
            'public' => false,
            'deck_id' => $deck->id,
        ]);
    }
}
