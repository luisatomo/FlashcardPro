<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Decks;
use App\Models\Deck;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Tests\TestCase;

class DecksTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        // Mock authorization for unit testing
        Gate::define('create', fn (User $user, $model) => true);
        Gate::define('update', fn (User $user, Deck $deck) => $user->id === $deck->user_id);
        Gate::define('delete', fn (User $user, Deck $deck) => $user->id === $deck->user_id);
    }

    public function test_renders_successfully()
    {
        Livewire::test(Decks::class)
            ->assertStatus(200);
    }

    public function test_mount_loads_user_decks()
    {
        Deck::factory()->count(2)->create(['user_id' => $this->user->id]);
        Deck::factory()->count(3)->create(); // Other user's decks

        Livewire::test(Decks::class)
            ->assertCount('decks', 2);
    }

    public function test_it_hides_form_by_default()
    {
        Livewire::test(Decks::class)
            ->assertSet('showForm', false);
    }

    public function test_it_shows_create_form()
    {
        Livewire::test(Decks::class)
            ->call('showCreateForm')
            ->assertSet('showForm', true)
            ->assertSet('form.name', '')
            ->assertSet('form.public', false)
            ->assertSet('form.editingId', null);
    }

    public function test_it_shows_edit_form_with_data()
    {
        $deck = Deck::factory()->create(['user_id' => $this->user->id]);

        Livewire::test(Decks::class)
            ->call('showEditForm', $deck->id)
            ->assertSet('showForm', true)
            ->assertSet('form.name', $deck->name)
            ->assertSet('form.public', $deck->public)
            ->assertSet('form.editingId', $deck->id);
    }

    public function test_it_creates_deck_successfully()
    {
        Livewire::test(Decks::class)
            ->set('form.name', 'New Deck')
            ->set('form.public', true)
            ->call('saveDeck')
            ->assertSet('showForm', false);

        $this->assertDatabaseHas('decks', [
            'user_id' => $this->user->id,
            'name' => 'New Deck',
            'public' => true,
        ]);
    }

    public function test_it_updates_deck_successfully()
    {
        $deck = Deck::factory()->create(['user_id' => $this->user->id]);

        Livewire::test(Decks::class)
            ->call('showEditForm', $deck->id)
            ->set('form.name', 'Updated Deck Name')
            ->call('saveDeck')
            ->assertSet('showForm', false);

        $this->assertDatabaseHas('decks', [
            'id' => $deck->id,
            'name' => 'Updated Deck Name',
        ]);
    }

    public function test_it_deletes_deck_successfully()
    {
        $deck = Deck::factory()->create(['user_id' => $this->user->id]);
        $this->assertDatabaseCount('decks', 1);

        Livewire::test(Decks::class)
            ->call('deleteDeck', $deck->id);

        $this->assertDatabaseCount('decks', 0);
    }

    public function test_validation_fails_for_empty_name()
    {
        Livewire::test(Decks::class)
            ->set('form.name', '')
            ->call('saveDeck')
            ->assertHasErrors(['form.name' => 'required']);
    }

    public function test_unauthorized_user_cannot_update_deck()
    {
        $otherUser = User::factory()->create();
        $deck = Deck::factory()->create(['user_id' => $otherUser->id]);

        Livewire::test(Decks::class)
            // Manually set the form state to test the saveDeck authorization
            ->set('form.editingId', $deck->id)
            ->set('form.name', 'Unauthorized Update')
            ->call('saveDeck')
            ->assertForbidden();
    }

    public function test_unauthorized_user_cannot_delete_deck()
    {
        $otherUser = User::factory()->create();
        $deck = Deck::factory()->create(['user_id' => $otherUser->id]);

        Livewire::test(Decks::class)
            ->call('deleteDeck', $deck->id)
            ->assertForbidden();
    }
}
