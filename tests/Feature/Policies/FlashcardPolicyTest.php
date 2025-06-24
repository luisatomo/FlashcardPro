<?php

namespace Tests\Feature\Policies;

use App\Models\Deck;
use App\Models\Flashcard;
use App\Models\User;
use App\Policies\FlashcardPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlashcardPolicyTest extends TestCase
{
    use RefreshDatabase;

    private FlashcardPolicy $policy;
    private User $user;
    private User $otherUser;
    private Deck $userDeck;
    private Deck $otherUserDeck;

    protected function setUp(): void
    {
        parent::setUp();

        $this->policy = new FlashcardPolicy();
        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
        $this->userDeck = Deck::factory()->create(['user_id' => $this->user->id]);
        $this->otherUserDeck = Deck::factory()->create(['user_id' => $this->otherUser->id]);
    }

    public function test_view_any_allows_all_users(): void
    {
        $this->assertTrue($this->policy->viewAny($this->user));
        $this->assertTrue($this->policy->viewAny($this->otherUser));
    }

    public function test_view_allows_owner_of_private_flashcard(): void
    {
        $flashcard = Flashcard::factory()->create([
            'deck_id' => $this->userDeck->id,
            'public' => false,
        ]);

        $this->assertTrue($this->policy->view($this->user, $flashcard));
    }

    public function test_view_denies_non_owner_of_private_flashcard(): void
    {
        $flashcard = Flashcard::factory()->create([
            'deck_id' => $this->userDeck->id,
            'public' => false,
        ]);

        $this->assertFalse($this->policy->view($this->otherUser, $flashcard));
    }

    public function test_view_allows_anyone_for_public_flashcard(): void
    {
        $flashcard = Flashcard::factory()->create([
            'deck_id' => $this->userDeck->id,
            'public' => true,
        ]);

        $this->assertTrue($this->policy->view($this->user, $flashcard));
        $this->assertTrue($this->policy->view($this->otherUser, $flashcard));
    }

    public function test_view_allows_non_owner_for_public_flashcard_in_others_deck(): void
    {
        $flashcard = Flashcard::factory()->create([
            'deck_id' => $this->otherUserDeck->id,
            'public' => true,
        ]);

        $this->assertTrue($this->policy->view($this->user, $flashcard));
    }

    public function test_create_allows_deck_owner(): void
    {
        $this->assertTrue($this->policy->create($this->user, $this->userDeck));
    }

    public function test_create_denies_non_deck_owner(): void
    {
        $this->assertFalse($this->policy->create($this->user, $this->otherUserDeck));
    }

    public function test_update_allows_flashcard_owner(): void
    {
        $flashcard = Flashcard::factory()->create([
            'deck_id' => $this->userDeck->id,
        ]);

        $this->assertTrue($this->policy->update($this->user, $flashcard));
    }

    public function test_update_denies_non_flashcard_owner(): void
    {
        $flashcard = Flashcard::factory()->create([
            'deck_id' => $this->otherUserDeck->id,
        ]);

        $this->assertFalse($this->policy->update($this->user, $flashcard));
    }

    public function test_delete_allows_flashcard_owner(): void
    {
        $flashcard = Flashcard::factory()->create([
            'deck_id' => $this->userDeck->id,
        ]);

        $this->assertTrue($this->policy->delete($this->user, $flashcard));
    }

    public function test_delete_denies_non_flashcard_owner(): void
    {
        $flashcard = Flashcard::factory()->create([
            'deck_id' => $this->otherUserDeck->id,
        ]);

        $this->assertFalse($this->policy->delete($this->user, $flashcard));
    }

    public function test_restore_allows_flashcard_owner(): void
    {
        $flashcard = Flashcard::factory()->create([
            'deck_id' => $this->userDeck->id,
        ]);

        $this->assertTrue($this->policy->restore($this->user, $flashcard));
    }

    public function test_restore_denies_non_flashcard_owner(): void
    {
        $flashcard = Flashcard::factory()->create([
            'deck_id' => $this->otherUserDeck->id,
        ]);

        $this->assertFalse($this->policy->restore($this->user, $flashcard));
    }

    public function test_force_delete_allows_flashcard_owner(): void
    {
        $flashcard = Flashcard::factory()->create([
            'deck_id' => $this->userDeck->id,
        ]);

        $this->assertTrue($this->policy->forceDelete($this->user, $flashcard));
    }

    public function test_force_delete_denies_non_flashcard_owner(): void
    {
        $flashcard = Flashcard::factory()->create([
            'deck_id' => $this->otherUserDeck->id,
        ]);

        $this->assertFalse($this->policy->forceDelete($this->user, $flashcard));
    }

    public function test_authorization_chain_through_deck_relationship(): void
    {
        // Create a flashcard in user's deck
        $flashcard = Flashcard::factory()->create([
            'deck_id' => $this->userDeck->id,
        ]);

        // Ensure the relationship works correctly for authorization
        $this->assertEquals($this->user->id, $flashcard->deck->user_id);
        $this->assertTrue($this->policy->update($this->user, $flashcard));
        $this->assertTrue($this->policy->delete($this->user, $flashcard));
    }

    public function test_multiple_users_with_different_decks(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();

        $deck1 = Deck::factory()->create(['user_id' => $user1->id]);
        $deck2 = Deck::factory()->create(['user_id' => $user2->id]);

        $flashcard1 = Flashcard::factory()->create(['deck_id' => $deck1->id]);
        $flashcard2 = Flashcard::factory()->create(['deck_id' => $deck2->id]);

        // User1 can manage their own flashcard
        $this->assertTrue($this->policy->update($user1, $flashcard1));
        $this->assertTrue($this->policy->delete($user1, $flashcard1));

        // User1 cannot manage user2's flashcard
        $this->assertFalse($this->policy->update($user1, $flashcard2));
        $this->assertFalse($this->policy->delete($user1, $flashcard2));

        // User3 cannot manage anyone's flashcards
        $this->assertFalse($this->policy->update($user3, $flashcard1));
        $this->assertFalse($this->policy->update($user3, $flashcard2));

        // But user3 can create in their own deck
        $deck3 = Deck::factory()->create(['user_id' => $user3->id]);
        $this->assertTrue($this->policy->create($user3, $deck3));
        $this->assertFalse($this->policy->create($user3, $deck1));
    }

    public function test_public_vs_private_flashcard_visibility(): void
    {
        $publicFlashcard = Flashcard::factory()->create([
            'deck_id' => $this->userDeck->id,
            'public' => true,
        ]);

        $privateFlashcard = Flashcard::factory()->create([
            'deck_id' => $this->userDeck->id,
            'public' => false,
        ]);

        // Owner can view both
        $this->assertTrue($this->policy->view($this->user, $publicFlashcard));
        $this->assertTrue($this->policy->view($this->user, $privateFlashcard));

        // Non-owner can only view public
        $this->assertTrue($this->policy->view($this->otherUser, $publicFlashcard));
        $this->assertFalse($this->policy->view($this->otherUser, $privateFlashcard));
    }
}
