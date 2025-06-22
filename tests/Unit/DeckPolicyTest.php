<?php

namespace Tests\Unit;

use App\Models\Deck;
use App\Models\User;
use App\Policies\DeckPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeckPolicyTest extends TestCase
{
    use RefreshDatabase;

    private DeckPolicy $policy;
    private User $user;
    private User $otherUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->policy = new DeckPolicy();
        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
    }

    public function test_view_any_returns_true_for_authenticated_user(): void
    {
        $this->assertTrue($this->policy->viewAny($this->user));
    }

    public function test_view_allows_owner_to_view_their_deck(): void
    {
        $deck = Deck::factory()->create(['user_id' => $this->user->id, 'public' => false]);

        $this->assertTrue($this->policy->view($this->user, $deck));
    }

    public function test_view_allows_anyone_to_view_public_deck(): void
    {
        $deck = Deck::factory()->create(['user_id' => $this->otherUser->id, 'public' => true]);

        $this->assertTrue($this->policy->view($this->user, $deck));
    }

    public function test_view_denies_non_owner_from_viewing_private_deck(): void
    {
        $deck = Deck::factory()->create(['user_id' => $this->otherUser->id, 'public' => false]);

        $this->assertFalse($this->policy->view($this->user, $deck));
    }

    public function test_create_allows_authenticated_user(): void
    {
        $this->assertTrue($this->policy->create($this->user));
    }

    public function test_update_allows_owner(): void
    {
        $deck = Deck::factory()->create(['user_id' => $this->user->id]);

        $this->assertTrue($this->policy->update($this->user, $deck));
    }

    public function test_update_denies_non_owner(): void
    {
        $deck = Deck::factory()->create(['user_id' => $this->otherUser->id]);

        $this->assertFalse($this->policy->update($this->user, $deck));
    }

    public function test_delete_allows_owner(): void
    {
        $deck = Deck::factory()->create(['user_id' => $this->user->id]);

        $this->assertTrue($this->policy->delete($this->user, $deck));
    }

    public function test_delete_denies_non_owner(): void
    {
        $deck = Deck::factory()->create(['user_id' => $this->otherUser->id]);

        $this->assertFalse($this->policy->delete($this->user, $deck));
    }

    public function test_restore_allows_owner(): void
    {
        $deck = Deck::factory()->create(['user_id' => $this->user->id]);

        $this->assertTrue($this->policy->restore($this->user, $deck));
    }

    public function test_restore_denies_non_owner(): void
    {
        $deck = Deck::factory()->create(['user_id' => $this->otherUser->id]);

        $this->assertFalse($this->policy->restore($this->user, $deck));
    }

    public function test_force_delete_allows_owner(): void
    {
        $deck = Deck::factory()->create(['user_id' => $this->user->id]);

        $this->assertTrue($this->policy->forceDelete($this->user, $deck));
    }

    public function test_force_delete_denies_non_owner(): void
    {
        $deck = Deck::factory()->create(['user_id' => $this->otherUser->id]);

        $this->assertFalse($this->policy->forceDelete($this->user, $deck));
    }
}
