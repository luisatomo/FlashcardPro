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
        $this->deck = Deck::factory()->create(['user_id' => $this->user->id]);
        $this->actingAs($this->user);
    }

    public function test_renders_successfully()
    {
        Livewire::test(Flashcards::class, ['deck' => $this->deck])
            ->assertStatus(200);
    }

    public function test_loads_flashcards_on_mount()
    {
        $flashcards = Flashcard::factory()->count(3)->create(['deck_id' => $this->deck->id]);

        $component = Livewire::test(Flashcards::class, ['deck' => $this->deck]);

        $this->assertEquals(3, $component->get('flashcards')->count());
        $this->assertEquals($this->deck->id, $component->get('deck')->id);
    }

    public function test_shows_create_form()
    {
        Livewire::test(Flashcards::class, ['deck' => $this->deck])
            ->call('showCreateForm')
            ->assertSet('showForm', true)
            ->assertSet('editingId', null)
            ->assertSet('question', '')
            ->assertSet('answer', '')
            ->assertSet('public', false);
    }

    public function test_creates_new_flashcard()
    {
        // Mock the Gate to allow creation
        $this->be($this->user);

        Livewire::test(Flashcards::class, ['deck' => $this->deck])
            ->set('question', 'What is Laravel?')
            ->set('answer', 'A PHP framework')
            ->set('public', true)
            ->call('saveFlashcard')
            ->assertHasNoErrors()
            ->assertSet('showForm', false);

        // Check that the flashcard was created in the database
        $this->assertDatabaseHas('flashcards', [
            'question' => 'What is Laravel?',
            'answer' => 'A PHP framework',
            'public' => true,
            'deck_id' => $this->deck->id,
        ]);
    }

    public function test_shows_edit_form_with_existing_data()
    {
        $flashcard = Flashcard::factory()->create([
            'deck_id' => $this->deck->id,
            'question' => 'Original question',
            'answer' => 'Original answer',
            'public' => true,
        ]);

        Livewire::test(Flashcards::class, ['deck' => $this->deck])
            ->call('showEditForm', $flashcard->id)
            ->assertSet('showForm', true)
            ->assertSet('editingId', $flashcard->id)
            ->assertSet('question', 'Original question')
            ->assertSet('answer', 'Original answer')
            ->assertSet('public', true);
    }

    public function test_updates_existing_flashcard()
    {
        $flashcard = Flashcard::factory()->create([
            'deck_id' => $this->deck->id,
            'question' => 'Original question',
            'answer' => 'Original answer',
            'public' => false,
        ]);

        Livewire::test(Flashcards::class, ['deck' => $this->deck])
            ->set('editingId', $flashcard->id)
            ->set('question', 'Updated question')
            ->set('answer', 'Updated answer')
            ->set('public', true)
            ->call('saveFlashcard')
            ->assertHasNoErrors()
            ->assertSet('showForm', false);

        $this->assertDatabaseHas('flashcards', [
            'id' => $flashcard->id,
            'question' => 'Updated question',
            'answer' => 'Updated answer',
            'public' => true,
        ]);
    }

    public function test_deletes_flashcard()
    {
        $flashcard = Flashcard::factory()->create(['deck_id' => $this->deck->id]);

        Livewire::test(Flashcards::class, ['deck' => $this->deck])
            ->call('deleteFlashcard', $flashcard->id);

        $this->assertDatabaseMissing('flashcards', ['id' => $flashcard->id]);

    }

    public function test_cancels_form()
    {
        Livewire::test(Flashcards::class, ['deck' => $this->deck])
            ->set('question', 'Test question')
            ->set('answer', 'Test answer')
            ->set('public', true)
            ->set('editingId', 123)
            ->set('showForm', true)
            ->call('cancelForm')
            ->assertSet('question', '')
            ->assertSet('answer', '')
            ->assertSet('public', false)
            ->assertSet('editingId', null)
            ->assertSet('showForm', false);
    }

    public function test_validates_required_question()
    {
        Livewire::test(Flashcards::class, ['deck' => $this->deck])
            ->set('question', '')
            ->set('answer', 'Valid answer')
            ->call('saveFlashcard')
            ->assertHasErrors(['question' => 'required']);
    }

    public function test_validates_question_minimum_length()
    {
        Livewire::test(Flashcards::class, ['deck' => $this->deck])
            ->set('question', 'AB')
            ->set('answer', 'Valid answer')
            ->call('saveFlashcard')
            ->assertHasErrors(['question' => 'min']);
    }

    public function test_validates_question_maximum_length()
    {
        Livewire::test(Flashcards::class, ['deck' => $this->deck])
            ->set('question', str_repeat('A', 256))
            ->set('answer', 'Valid answer')
            ->call('saveFlashcard')
            ->assertHasErrors(['question' => 'max']);
    }

    public function test_validates_required_answer()
    {
        Livewire::test(Flashcards::class, ['deck' => $this->deck])
            ->set('question', 'Valid question')
            ->set('answer', '')
            ->call('saveFlashcard')
            ->assertHasErrors(['answer' => 'required']);
    }

    public function test_validates_answer_minimum_length()
    {
        Livewire::test(Flashcards::class, ['deck' => $this->deck])
            ->set('question', 'Valid question')
            ->set('answer', 'A')
            ->call('saveFlashcard')
            ->assertHasErrors(['answer' => 'min']);
    }



    public function test_cannot_edit_nonexistent_flashcard()
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        Livewire::test(Flashcards::class, ['deck' => $this->deck])
            ->call('showEditForm', 999);
    }

    public function test_cannot_delete_nonexistent_flashcard()
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        Livewire::test(Flashcards::class, ['deck' => $this->deck])
            ->call('deleteFlashcard', 999);
    }

    public function test_reloads_flashcards_after_operations()
    {
        $initialCount = $this->deck->flashcards()->count();

        $component = Livewire::test(Flashcards::class, ['deck' => $this->deck])
            ->set('question', 'New question')
            ->set('answer', 'New answer')
            ->call('saveFlashcard');

        $this->assertEquals($initialCount + 1, $component->get('flashcards')->count());
    }
}
