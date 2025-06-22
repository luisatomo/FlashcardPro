<?php

namespace Tests\Unit;

use App\Actions\CreateDeck;
use App\Actions\DeleteDeck;
use App\Actions\UpdateDeck;
use App\Models\Deck;
use App\Models\Flashcard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class DeckActionsTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        // Mock authorization to allow all actions for unit testing
        Gate::define('create', fn ($user, $model) => true);
        Gate::define('update', fn ($user, $deck) => true);
        Gate::define('delete', fn ($user, $deck) => true);
    }

    public function test_create_deck_creates_deck_with_all_attributes(): void
    {
        $action = new CreateDeck();

        $deck = $action([
            'name' => 'Test Deck',
            'description' => 'Test Description',
            'public' => true,
        ]);

        $this->assertInstanceOf(Deck::class, $deck);
        $this->assertEquals('Test Deck', $deck->name);
        $this->assertEquals('Test Description', $deck->description);
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
        $this->assertNull($deck->description);
        $this->assertEquals($this->user->id, $deck->user_id);
    }

    public function test_create_deck_assigns_current_user_as_owner(): void
    {
        $action = new CreateDeck();

        $deck = $action(['name' => 'Test Deck']);

        $this->assertEquals($this->user->id, $deck->user_id);
    }

    public function test_update_deck_updates_with_provided_attributes(): void
    {
        $deck = Deck::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Original Name',
            'description' => 'Original Description',
            'public' => false,
        ]);

        $action = new UpdateDeck();

        $updatedDeck = $action($deck, [
            'name' => 'Updated Name',
            'description' => 'Updated Description',
            'public' => true,
        ]);

        $this->assertEquals('Updated Name', $updatedDeck->name);
        $this->assertEquals('Updated Description', $updatedDeck->description);
        $this->assertTrue($updatedDeck->public);
        $this->assertEquals($deck->id, $updatedDeck->id);
    }

    public function test_update_deck_updates_only_provided_attributes(): void
    {
        $deck = Deck::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Original Name',
            'description' => 'Original Description',
            'public' => false,
        ]);

        $action = new UpdateDeck();

        $updatedDeck = $action($deck, [
            'name' => 'Updated Name',
        ]);

        $this->assertEquals('Updated Name', $updatedDeck->name);
        $this->assertEquals('Original Description', $updatedDeck->description);
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

    public function test_delete_deck_deletes_successfully(): void
    {
        $deck = Deck::factory()->create(['user_id' => $this->user->id]);
        $deckId = $deck->id;

        $action = new DeleteDeck();
        $result = $action($deck);

        $this->assertTrue($result);
        $this->assertNull(Deck::find($deckId));
    }

    public function test_delete_deck_deletes_with_associated_flashcards(): void
    {
        $deck = Deck::factory()->create(['user_id' => $this->user->id]);
        $flashcard1 = Flashcard::factory()->create(['deck_id' => $deck->id]);
        $flashcard2 = Flashcard::factory()->create(['deck_id' => $deck->id]);

        $action = new DeleteDeck();
        $result = $action($deck);

        $this->assertTrue($result);
        $this->assertNull(Deck::find($deck->id));
        $this->assertNull(Flashcard::find($flashcard1->id));
        $this->assertNull(Flashcard::find($flashcard2->id));
    }

    public function test_delete_deck_returns_true_on_success(): void
    {
        $deck = Deck::factory()->create(['user_id' => $this->user->id]);

        $action = new DeleteDeck();
        $result = $action($deck);

        $this->assertTrue($result);
    }
}
