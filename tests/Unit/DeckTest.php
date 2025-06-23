<?php

namespace Tests\Unit;

use App\Models\Deck;
use App\Models\Flashcard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class DeckTest extends TestCase
{
    use RefreshDatabase;

    public function test_deck_model_has_correct_fillable_attributes(): void
    {
        $deck = new Deck();

        $this->assertEquals([
            'uuid',
            'name',
            'public',
            'user_id',
        ], $deck->getFillable());
    }

    public function test_deck_model_casts_public_attribute_to_boolean(): void
    {
        $deck = new Deck();

        $this->assertArrayHasKey('public', $deck->getCasts());
        $this->assertEquals('boolean', $deck->getCasts()['public']);
    }

    public function test_deck_generates_uuid_on_creation(): void
    {
        $user = User::factory()->create();

        $deck = Deck::create([
            'name' => 'Test Deck',
            'user_id' => $user->id,
        ]);

        $this->assertNotNull($deck->uuid);
        $this->assertTrue(Str::isUuid($deck->uuid));
    }

    public function test_deck_can_have_custom_uuid(): void
    {
        $user = User::factory()->create();
        $customUuid = Str::uuid();

        $deck = Deck::create([
            'name' => 'Test Deck',
            'uuid' => $customUuid,
            'user_id' => $user->id,
        ]);

        $this->assertEquals($customUuid->toString(), $deck->uuid);
    }

    public function test_deck_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $deck = Deck::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $deck->user);
        $this->assertEquals($user->id, $deck->user->id);
    }

    public function test_deck_has_many_flashcards(): void
    {
        $deck = Deck::factory()->create();
        $flashcard1 = Flashcard::factory()->create(['deck_id' => $deck->id]);
        $flashcard2 = Flashcard::factory()->create(['deck_id' => $deck->id]);

        $this->assertCount(2, $deck->flashcards);
        $this->assertInstanceOf(Flashcard::class, $deck->flashcards->first());
    }

    public function test_deck_uses_uuid_as_route_key(): void
    {
        $deck = new Deck();

        $this->assertEquals('uuid', $deck->getRouteKeyName());
    }

    public function test_deck_public_scope_filters_correctly(): void
    {
        $publicDeck = Deck::factory()->create(['public' => true]);
        $privateDeck = Deck::factory()->create(['public' => false]);

        $publicDecks = Deck::public()->get();

        $this->assertCount(1, $publicDecks);
        $this->assertTrue($publicDecks->first()->public);
        $this->assertEquals($publicDeck->id, $publicDecks->first()->id);
    }

    public function test_deck_flashcard_count_attribute(): void
    {
        $deck = Deck::factory()->create();
        Flashcard::factory()->count(3)->create(['deck_id' => $deck->id]);

        $this->assertEquals(3, $deck->flashcard_count);
    }

    public function test_flashcard_count_is_zero_for_empty_deck(): void
    {
        $deck = Deck::factory()->create();

        $this->assertEquals(0, $deck->flashcard_count);
    }

    public function test_deck_attributes_are_correctly_set(): void
    {
        $deck = new Deck([
            'name' => 'Test Deck',
            'public' => true,
        ]);

        $this->assertEquals('Test Deck', $deck->name);
        $this->assertTrue($deck->public);
    }

    public function test_deck_public_attribute_is_cast_to_boolean(): void
    {
        $deck = new Deck(['public' => '1']);

        $this->assertTrue($deck->public);
        $this->assertTrue(is_bool($deck->public));
    }
}
