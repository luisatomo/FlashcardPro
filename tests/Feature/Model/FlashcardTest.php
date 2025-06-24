<?php

namespace Feature\Model;

use App\Models\Deck;
use App\Models\Flashcard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlashcardTest extends TestCase
{
    use RefreshDatabase;

    public function test_flashcard_belongs_to_deck()
    {
        $user = User::factory()->create();
        $deck = Deck::factory()->create(['user_id' => $user->id]);
        $flashcard = Flashcard::factory()->create(['deck_id' => $deck->id]);

        $this->assertInstanceOf(Deck::class, $flashcard->deck);
        $this->assertEquals($deck->id, $flashcard->deck->id);
    }

    public function test_automatically_generates_uuid_on_creation()
    {
        $user = User::factory()->create();
        $deck = Deck::factory()->create(['user_id' => $user->id]);

        $flashcard = Flashcard::create([
            'question' => 'Test question',
            'answer' => 'Test answer',
            'deck_id' => $deck->id,
        ]);

        $this->assertNotEmpty($flashcard->uuid);
        $this->assertIsString($flashcard->uuid);
    }

    public function test_uses_uuid_as_route_key()
    {
        $flashcard = new Flashcard();
        $this->assertEquals('uuid', $flashcard->getRouteKeyName());
    }

    public function test_public_scope_filters_public_flashcards()
    {
        $user = User::factory()->create();
        $deck = Deck::factory()->create(['user_id' => $user->id]);

        Flashcard::factory()->create(['deck_id' => $deck->id, 'public' => true]);
        Flashcard::factory()->create(['deck_id' => $deck->id, 'public' => false]);
        Flashcard::factory()->create(['deck_id' => $deck->id, 'public' => true]);

        $publicFlashcards = Flashcard::public()->get();

        $this->assertEquals(2, $publicFlashcards->count());
        $this->assertTrue($publicFlashcards->every(fn($card) => $card->public === true));
    }

    public function test_casts_public_to_boolean()
    {
        $user = User::factory()->create();
        $deck = Deck::factory()->create(['user_id' => $user->id]);

        $flashcard = Flashcard::create([
            'question' => 'Test',
            'answer' => 'Test',
            'public' => '1',
            'deck_id' => $deck->id,
        ]);

        $this->assertIsBool($flashcard->public);
        $this->assertTrue($flashcard->public);
    }

    public function test_fillable_attributes()
    {
        $flashcard = new Flashcard();
        $expected = ['uuid', 'question', 'answer', 'public', 'deck_id'];

        $this->assertEquals($expected, $flashcard->getFillable());
    }
}
