<?php

namespace Tests\Unit;

use App\Models\Deck;
use App\Models\User;
use Database\Factories\DeckFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class DeckFactoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_deck_factory_creates_valid_deck(): void
    {
        $deck = Deck::factory()->create();

        $this->assertInstanceOf(Deck::class, $deck);
        $this->assertNotNull($deck->uuid);
        $this->assertTrue(Str::isUuid($deck->uuid));
        $this->assertNotNull($deck->name);
        $this->assertNotNull($deck->user_id);
        $this->assertTrue(is_bool($deck->public));
    }

    public function test_deck_factory_creates_deck_with_custom_attributes(): void
    {
        $user = User::factory()->create();

        $deck = Deck::factory()->create([
            'name' => 'Custom Deck Name',
            'public' => true,
            'user_id' => $user->id,
        ]);

        $this->assertEquals('Custom Deck Name', $deck->name);
        $this->assertTrue($deck->public);
        $this->assertEquals($user->id, $deck->user_id);
    }

    public function test_deck_factory_public_state_creates_public_deck(): void
    {
        $deck = Deck::factory()->public()->create();

        $this->assertTrue($deck->public);
    }

    public function test_deck_factory_private_state_creates_private_deck(): void
    {
        $deck = Deck::factory()->private()->create();

        $this->assertFalse($deck->public);
    }

    public function test_deck_factory_with_name_state_sets_custom_name(): void
    {
        $customName = 'My Custom Deck';
        $deck = Deck::factory()->withName($customName)->create();

        $this->assertEquals($customName, $deck->name);
    }

    public function test_deck_factory_generates_unique_uuids(): void
    {
        $deck1 = Deck::factory()->create();
        $deck2 = Deck::factory()->create();

        $this->assertNotEquals($deck1->uuid, $deck2->uuid);
    }

    public function test_deck_factory_creates_deck_with_user_relationship(): void
    {
        $deck = Deck::factory()->create();

        $this->assertInstanceOf(User::class, $deck->user);
        $this->assertEquals($deck->user_id, $deck->user->id);
    }

    public function test_deck_factory_can_create_multiple_decks(): void
    {
        $decks = Deck::factory()->count(3)->create();

        $this->assertCount(3, $decks);

        foreach ($decks as $deck) {
            $this->assertInstanceOf(Deck::class, $deck);
            $this->assertNotNull($deck->uuid);
            $this->assertTrue(Str::isUuid($deck->uuid));
        }
    }

    public function test_deck_factory_definition_has_correct_structure(): void
    {
        $factory = new DeckFactory();
        $definition = $factory->definition();

        $this->assertIsArray($definition);
        $this->assertArrayHasKey('uuid', $definition);
        $this->assertArrayHasKey('name', $definition);
        $this->assertArrayHasKey('description', $definition);
        $this->assertArrayHasKey('public', $definition);
        $this->assertArrayHasKey('user_id', $definition);
    }

    public function test_deck_factory_states_can_be_chained(): void
    {
        $deck = Deck::factory()
            ->public()
            ->withName('Chained Test Deck')
            ->create();

        $this->assertTrue($deck->public);
        $this->assertEquals('Chained Test Deck', $deck->name);
    }
}
