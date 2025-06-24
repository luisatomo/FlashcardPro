<?php

namespace Tests\Feature\Actions;

use App\Actions\CreateDeck;
use App\Models\Deck;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class CreateDeckTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        // Mock authorization to allow creation for unit testing
        Gate::define('create', fn ($user, $model) => true);
    }

    public function test_create_deck_creates_deck_with_all_attributes(): void
    {
        $action = new CreateDeck();

        $deck = $action([
            'name' => 'Test Deck',
            'public' => true,
        ]);

        $this->assertInstanceOf(Deck::class, $deck);
        $this->assertEquals('Test Deck', $deck->name);
        $this->assertTrue($deck->public);
        $this->assertEquals($this->user->id, $deck->user_id);
        $this->assertNotNull($deck->uuid);
    }

    public function test_create_deck_creates_deck_with_minimal_attributes(): void
    {
        $action = new CreateDeck();

        $deck = $action([
            'name' => 'Minimal Deck',
        ]);

        $this->assertEquals('Minimal Deck', $deck->name);
        $this->assertFalse($deck->public);
        $this->assertEquals($this->user->id, $deck->user_id);
    }

    public function test_create_deck_assigns_current_user_as_owner(): void
    {
        $action = new CreateDeck();

        $deck = $action(['name' => 'Test Deck']);

        $this->assertEquals($this->user->id, $deck->user_id);
    }

    public function test_create_deck_generates_uuid(): void
    {
        $action = new CreateDeck();

        $deck = $action(['name' => 'Test Deck']);

        $this->assertNotNull($deck->uuid);
        $this->assertIsString($deck->uuid);
    }

    public function test_create_deck_persists_to_database(): void
    {
        $action = new CreateDeck();

        $deck = $action([
            'name' => 'Database Test Deck',
            'public' => true,
        ]);

        $this->assertDatabaseHas('decks', [
            'id' => $deck->id,
            'name' => 'Database Test Deck',
            'public' => true,
            'user_id' => $this->user->id,
        ]);
    }

    public function test_create_deck_defaults_public_to_false(): void
    {
        $action = new CreateDeck();

        $deck = $action(['name' => 'Private Deck']);

        $this->assertFalse($deck->public);
    }
}
