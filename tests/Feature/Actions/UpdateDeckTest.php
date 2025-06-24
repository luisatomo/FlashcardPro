<?php

namespace Tests\Feature\Actions;

use App\Actions\UpdateDeck;
use App\Models\Deck;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class UpdateDeckTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        // Mock authorization to allow updates for unit testing
        Gate::define('update', fn ($user, $deck) => true);
    }

    public function test_update_deck_updates_with_provided_attributes(): void
    {
        $deck = Deck::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Original Name',
            'public' => false,
        ]);

        $action = new UpdateDeck();

        $updatedDeck = $action($deck, [
            'name' => 'Updated Name',
            'public' => true,
        ]);

        $this->assertEquals('Updated Name', $updatedDeck->name);
        $this->assertTrue($updatedDeck->public);
        $this->assertEquals($deck->id, $updatedDeck->id);
    }

    public function test_update_deck_updates_only_provided_attributes(): void
    {
        $deck = Deck::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Original Name',
            'public' => false,
        ]);

        $action = new UpdateDeck();

        $updatedDeck = $action($deck, [
            'name' => 'Updated Name',
        ]);

        $this->assertEquals('Updated Name', $updatedDeck->name);
        $this->assertFalse($updatedDeck->public);
    }

    public function test_update_deck_returns_updated_deck_instance(): void
    {
        $deck = Deck::factory()->create(['user_id' => $this->user->id]);
        $action = new UpdateDeck();

        $result = $action($deck, ['name' => 'New Name']);

        $this->assertInstanceOf(Deck::class, $result);
        $this->assertEquals($deck->id, $result->id);
    }

    public function test_update_deck_preserves_uuid(): void
    {
        $deck = Deck::factory()->create(['user_id' => $this->user->id]);
        $originalUuid = $deck->uuid;

        $action = new UpdateDeck();
        $updatedDeck = $action($deck, ['name' => 'New Name']);

        $this->assertEquals($originalUuid, $updatedDeck->uuid);
    }

    public function test_update_deck_preserves_user_id(): void
    {
        $deck = Deck::factory()->create(['user_id' => $this->user->id]);

        $action = new UpdateDeck();
        $updatedDeck = $action($deck, ['name' => 'New Name']);

        $this->assertEquals($this->user->id, $updatedDeck->user_id);
    }

    public function test_update_deck_persists_changes_to_database(): void
    {
        $deck = Deck::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Original Name',
            'public' => false,
        ]);

        $action = new UpdateDeck();
        $action($deck, [
            'name' => 'Updated Name',
            'public' => true,
        ]);

        $this->assertDatabaseHas('decks', [
            'id' => $deck->id,
            'name' => 'Updated Name',
            'public' => true,
        ]);
    }

    public function test_update_deck_handles_partial_updates(): void
    {
        $deck = Deck::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Original Name',
            'public' => true,
        ]);

        $action = new UpdateDeck();
        $updatedDeck = $action($deck, ['public' => false]);

        $this->assertEquals('Original Name', $updatedDeck->name);
        $this->assertFalse($updatedDeck->public);
    }
}
