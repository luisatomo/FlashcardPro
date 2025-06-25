<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Flashcards;
use App\Models\Deck;
use App\Models\Flashcard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FlashcardsTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Deck $deck;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
        $this->deck = Deck::factory()->create(['user_id' => $this->user->id]);
    }

    public function test_renders_successfully()
    {
        Livewire::test(Flashcards::class, ['deck' => $this->deck])
            ->assertStatus(200);
    }

    public function test_mount_loads_flashcards()
    {
        Flashcard::factory()->count(5)->create(['deck_id' => $this->deck->id]);
        Flashcard::factory()->count(3)->create(); // From another deck

        Livewire::test(Flashcards::class, ['deck' => $this->deck])
            ->assertCount('flashcards', 5);
    }

    public function test_it_creates_flashcard_successfully()
    {
        Livewire::test(Flashcards::class, ['deck' => $this->deck])
            ->set('form.question', 'New Question')
            ->set('form.answer', 'New Answer')
            ->call('saveFlashcard');

        $this->assertDatabaseHas('flashcards', [
            'deck_id' => $this->deck->id,
            'question' => 'New Question',
            'answer' => 'New Answer',
        ]);
    }

    public function test_it_updates_flashcard_successfully()
    {
        $flashcard = Flashcard::factory()->create(['deck_id' => $this->deck->id]);

        Livewire::test(Flashcards::class, ['deck' => $this->deck])
            ->call('showEditForm', $flashcard->id)
            ->set('form.question', 'Updated Question')
            ->call('saveFlashcard');

        $this->assertDatabaseHas('flashcards', [
            'id' => $flashcard->id,
            'question' => 'Updated Question',
        ]);
    }

    public function test_it_deletes_flashcard_successfully()
    {
        $flashcard = Flashcard::factory()->create(['deck_id' => $this->deck->id]);
        $this->assertDatabaseCount('flashcards', 1);

        Livewire::test(Flashcards::class, ['deck' => $this->deck])
            ->call('deleteFlashcard', $flashcard->id);

        $this->assertDatabaseCount('flashcards', 0);
    }

    public function test_validation_fails_for_empty_fields()
    {
        Livewire::test(Flashcards::class, ['deck' => $this->deck])
            ->set('form.question', '')
            ->set('form.answer', '')
            ->call('saveFlashcard')
            ->assertHasErrors(['form.question' => 'required', 'form.answer' => 'required']);
    }

    public function test_unauthorized_user_cannot_create_flashcard()
    {
        $otherUser = User::factory()->create();
        $other_deck = Deck::factory()->create(['user_id' => $otherUser->id]);

        Livewire::test(Flashcards::class, ['deck' => $other_deck])
            ->set('form.question', 'Unauthorized Question')
            ->set('form.answer', 'Unauthorized Answer')
            ->call('saveFlashcard')
            ->assertForbidden();
    }

    public function test_unauthorized_user_cannot_update_flashcard()
    {
        $otherFlashcard = Flashcard::factory()->create();

        Livewire::test(Flashcards::class, ['deck' => $this->deck])
            ->set('form.editingId', $otherFlashcard->id)
            ->set('form.question', 'Unauthorized Update')
            ->set('form.answer', 'Unauthorized Answer')
            ->call('saveFlashcard')
            ->assertForbidden();
    }

    public function test_unauthorized_user_cannot_view_edit_form()
    {
        $otherFlashcard = Flashcard::factory()->create();

        Livewire::test(Flashcards::class, ['deck' => $this->deck])
            ->call('showEditForm', $otherFlashcard->id)
            ->assertForbidden();
    }

    public function test_unauthorized_user_cannot_delete_flashcard()
    {
        $otherFlashcard = Flashcard::factory()->create();

        Livewire::test(Flashcards::class, ['deck' => $this->deck])
            ->call('deleteFlashcard', $otherFlashcard->id)
            ->assertForbidden();
    }
}
