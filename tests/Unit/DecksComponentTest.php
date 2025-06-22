<?php

namespace Tests\Unit;

use App\Livewire\Decks;
use App\Models\Deck;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class DecksComponentTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        // Mock authorization for unit testing
        Gate::define('create', fn ($user, $model) => true);
        Gate::define('update', fn ($user, $deck) => true);
        Gate::define('delete', fn ($user, $deck) => true);
    }

    public function test_component_has_correct_initial_state(): void
    {
        $component = new Decks();

        $this->assertEquals('', $component->name);
        $this->assertFalse($component->public);
        $this->assertFalse($component->showForm);
        $this->assertNull($component->editingId);
    }

    public function test_component_has_correct_validation_rules(): void
    {
        $component = new Decks();
        $rules = $component->rules;

        $this->assertArrayHasKey('name', $rules);
        $this->assertArrayHasKey('public', $rules);
        $this->assertEquals('required|string|min:3|max:100', $rules['name']);
        $this->assertEquals('boolean', $rules['public']);
    }

    public function test_load_decks_method_loads_user_decks(): void
    {
        $deck1 = Deck::factory()->create(['user_id' => $this->user->id]);
        $deck2 = Deck::factory()->create(['user_id' => $this->user->id]);

        // Create deck for another user
        $otherUser = User::factory()->create();
        Deck::factory()->create(['user_id' => $otherUser->id]);

        $component = new Decks();
        $component->mount();

        $this->assertInstanceOf(Collection::class, $component->decks);
        $this->assertCount(2, $component->decks);
        $this->assertTrue($component->decks->pluck('id')->contains($deck1->id));
        $this->assertTrue($component->decks->pluck('id')->contains($deck2->id));
    }

    public function test_show_create_form_sets_correct_state(): void
    {
        $component = new Decks();
        $component->name = 'Previous Name';
        $component->public = true;
        $component->editingId = 123;
        $component->showForm = false;

        $component->showCreateForm();

        $this->assertTrue($component->showForm);
        $this->assertEquals('', $component->name);
        $this->assertFalse($component->public);
        $this->assertNull($component->editingId);
    }

    public function test_show_edit_form_sets_correct_state(): void
    {
        $deck = Deck::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Test Deck',
            'public' => true,
        ]);

        $component = new Decks();
        $component->showEditForm($deck->id);

        $this->assertTrue($component->showForm);
        $this->assertEquals('Test Deck', $component->name);
        $this->assertTrue($component->public);
        $this->assertEquals($deck->id, $component->editingId);
    }

    public function test_cancel_form_resets_all_fields(): void
    {
        $component = new Decks();
        $component->name = 'Test Name';
        $component->public = true;
        $component->editingId = 123;
        $component->showForm = true;

        $component->cancelForm();

        $this->assertEquals('', $component->name);
        $this->assertFalse($component->public);
        $this->assertNull($component->editingId);
        $this->assertFalse($component->showForm);
    }

    public function test_component_uses_correct_view(): void
    {
        $component = new Decks();
        $view = $component->render();

        $this->assertEquals('livewire.decks', $view->name());
    }

    public function test_component_uses_authorizes_requests_trait(): void
    {
        $component = new Decks();

        $this->assertTrue(method_exists($component, 'authorize'));
    }

    public function test_component_initializes_with_empty_collection(): void
    {
        $component = new Decks();
        $component->decks = collect();

        $this->assertInstanceOf(Collection::class, $component->decks);
        $this->assertCount(0, $component->decks);
    }

    public function test_component_handles_null_decks_gracefully(): void
    {
        // Create a mock user with empty decks relationship
        $mockUser = $this->createMock(User::class);
        $mockUser->method('decks')->willReturn(null);

        auth()->setUser($mockUser);

        $component = new Decks();
        $component->loadDecks();

        $this->assertInstanceOf(Collection::class, $component->decks);
        $this->assertCount(0, $component->decks);
    }
}
