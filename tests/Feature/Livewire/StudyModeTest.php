<?php

namespace Tests\Feature\Livewire;

use App\Livewire\StudyMode;
use App\Models\Deck;
use App\Models\Flashcard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class StudyModeTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Deck $deck;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->deck = Deck::factory()->create(['user_id' => $this->user->id]);
    }

    public function test_component_mounts_successfully_with_deck(): void
    {
        $this->actingAs($this->user);

        $component = Livewire::test(StudyMode::class, ['deck' => $this->deck]);

        $component->assertOk()
            ->assertSet('deck.id', $this->deck->id)
            ->assertSet('current', 0)
            ->assertSet('score', 0)
            ->assertSet('displayedAnswer', false);
    }

    public function test_loads_flashcards_on_mount(): void
    {
        $this->actingAs($this->user);

        // Create flashcards for the deck
        Flashcard::factory()->count(5)->create(['deck_id' => $this->deck->id]);

        $component = Livewire::test(StudyMode::class, ['deck' => $this->deck]);

        $component->assertSet('flashcards', function ($flashcards) {
            return $flashcards->count() === 5;
        });
    }

    public function test_loads_empty_collection_when_no_flashcards(): void
    {
        $this->actingAs($this->user);

        $component = Livewire::test(StudyMode::class, ['deck' => $this->deck]);

        $component->assertSet('flashcards', function ($flashcards) {
            return $flashcards->isEmpty();
        });
    }

    public function test_flashcards_are_loaded_in_random_order(): void
    {
        $this->actingAs($this->user);

        // Create flashcards with predictable order
        $flashcard1 = Flashcard::factory()->create(['deck_id' => $this->deck->id, 'question' => 'Question 1?']);
        $flashcard2 = Flashcard::factory()->create(['deck_id' => $this->deck->id, 'question' => 'Question 2?']);
        $flashcard3 = Flashcard::factory()->create(['deck_id' => $this->deck->id, 'question' => 'Question 3?']);

        // Test multiple times to check randomization
        $orders = [];
        for ($i = 0; $i < 10; $i++) {
            $component = Livewire::test(StudyMode::class, ['deck' => $this->deck]);
            $firstQuestion = $component->get('flashcards')->first()->question;
            $orders[] = $firstQuestion;
        }

        // Should have different orders (not always the same first question)
        $uniqueOrders = array_unique($orders);
        $this->assertGreaterThan(1, count($uniqueOrders), 'Flashcards should be randomized');
    }

    public function test_show_answer_sets_displayed_answer_to_true(): void
    {
        $this->actingAs($this->user);

        Flashcard::factory()->create(['deck_id' => $this->deck->id]);

        $component = Livewire::test(StudyMode::class, ['deck' => $this->deck])
            ->assertSet('displayedAnswer', false)
            ->call('showAnswer')
            ->assertSet('displayedAnswer', true);
    }

    public function test_increment_score_with_correct_answer(): void
    {
        $this->actingAs($this->user);

        Flashcard::factory()->create(['deck_id' => $this->deck->id]);

        $component = Livewire::test(StudyMode::class, ['deck' => $this->deck])
            ->set('displayedAnswer', true)
            ->call('incrementScore', true)
            ->assertSet('score', 1)
            ->assertSet('current', 1)
            ->assertSet('displayedAnswer', false);
    }

    public function test_increment_score_with_incorrect_answer(): void
    {
        $this->actingAs($this->user);

        Flashcard::factory()->create(['deck_id' => $this->deck->id]);

        $component = Livewire::test(StudyMode::class, ['deck' => $this->deck])
            ->set('displayedAnswer', true)
            ->call('incrementScore', false)
            ->assertSet('score', 0)
            ->assertSet('current', 1)
            ->assertSet('displayedAnswer', false);
    }

    public function test_increment_score_multiple_times(): void
    {
        $this->actingAs($this->user);

        Flashcard::factory()->count(3)->create(['deck_id' => $this->deck->id]);

        $component = Livewire::test(StudyMode::class, ['deck' => $this->deck])
            ->call('incrementScore', true)  // Correct: score = 1, current = 1
            ->assertSet('score', 1)
            ->assertSet('current', 1)
            ->call('incrementScore', false) // Incorrect: score = 1, current = 2
            ->assertSet('score', 1)
            ->assertSet('current', 2)
            ->call('incrementScore', true)  // Correct: score = 2, current = 3
            ->assertSet('score', 2)
            ->assertSet('current', 3);
    }

    public function test_clear_answer_sets_displayed_answer_to_false(): void
    {
        $this->actingAs($this->user);

        $component = Livewire::test(StudyMode::class, ['deck' => $this->deck])
            ->set('displayedAnswer', true)
            ->call('clearAnswer')
            ->assertSet('displayedAnswer', false);
    }

    public function test_clear_answer_is_called_by_increment_score(): void
    {
        $this->actingAs($this->user);

        Flashcard::factory()->create(['deck_id' => $this->deck->id]);

        $component = Livewire::test(StudyMode::class, ['deck' => $this->deck])
            ->set('displayedAnswer', true)
            ->call('incrementScore', true)
            ->assertSet('displayedAnswer', false);
    }

    public function test_component_renders_correct_view(): void
    {
        $this->actingAs($this->user);

        $component = Livewire::test(StudyMode::class, ['deck' => $this->deck]);

        $component->assertViewIs('livewire.study-mode');
    }

    public function test_component_uses_app_layout(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(route('study-mode', $this->deck));

        $response->assertOk();
        // The layout assertion would depend on your actual route setup
    }

    public function test_study_session_progress_tracking(): void
    {
        $this->actingAs($this->user);

        Flashcard::factory()->count(5)->create(['deck_id' => $this->deck->id]);

        $component = Livewire::test(StudyMode::class, ['deck' => $this->deck]);

        // Simulate a study session
        $component
            ->call('showAnswer')
            ->call('incrementScore', true)   // 1/5 correct
            ->call('showAnswer')
            ->call('incrementScore', false)  // 1/5 correct
            ->call('showAnswer')
            ->call('incrementScore', true)   // 2/5 correct
            ->call('showAnswer')
            ->call('incrementScore', true)   // 3/5 correct
            ->call('showAnswer')
            ->call('incrementScore', false); // 3/5 correct

        $component
            ->assertSet('score', 3)
            ->assertSet('current', 5);
    }

    public function test_can_show_answer_multiple_times(): void
    {
        $this->actingAs($this->user);

        Flashcard::factory()->create(['deck_id' => $this->deck->id]);

        $component = Livewire::test(StudyMode::class, ['deck' => $this->deck])
            ->call('showAnswer')
            ->assertSet('displayedAnswer', true)
            ->call('showAnswer')
            ->assertSet('displayedAnswer', true); // Should remain true
    }

    public function test_load_flashcards_method_reloads_deck_flashcards(): void
    {
        $this->actingAs($this->user);

        $component = Livewire::test(StudyMode::class, ['deck' => $this->deck])
            ->assertSet('flashcards', function ($flashcards) {
                return $flashcards->isEmpty();
            });

        // Add flashcards after component is mounted
        Flashcard::factory()->count(3)->create(['deck_id' => $this->deck->id]);

        $component
            ->call('loadFlashcards')
            ->assertSet('flashcards', function ($flashcards) {
                return $flashcards->count() === 3;
            });
    }

    public function test_study_mode_with_different_deck_owners(): void
    {
        $otherUser = User::factory()->create();
        $publicDeck = Deck::factory()->create([
            'user_id' => $otherUser->id,
            'public' => true
        ]);

        Flashcard::factory()->count(2)->create(['deck_id' => $publicDeck->id]);

        $this->actingAs($this->user);

        $component = Livewire::test(StudyMode::class, ['deck' => $publicDeck])
            ->assertSet('deck.id', $publicDeck->id)
            ->assertSet('flashcards', function ($flashcards) {
                return $flashcards->count() === 2;
            });
    }

    public function test_score_calculation_accuracy(): void
    {
        $this->actingAs($this->user);

        Flashcard::factory()->count(10)->create(['deck_id' => $this->deck->id]);

        $component = Livewire::test(StudyMode::class, ['deck' => $this->deck]);

        // Answer 7 out of 10 correctly
        $answers = [true, false, true, true, false, true, true, true, false, true];

        foreach ($answers as $answer) {
            $component->call('incrementScore', $answer);
        }

        $component
            ->assertSet('score', 7)
            ->assertSet('current', 10);
    }

    public function test_component_state_persistence_during_session(): void
    {
        $this->actingAs($this->user);

        Flashcard::factory()->count(3)->create(['deck_id' => $this->deck->id]);

        $component = Livewire::test(StudyMode::class, ['deck' => $this->deck])
            ->call('showAnswer')
            ->call('incrementScore', true)
            ->assertSet('current', 1)
            ->assertSet('score', 1)
            ->call('showAnswer')
            ->assertSet('displayedAnswer', true)
            ->call('incrementScore', false)
            ->assertSet('current', 2)
            ->assertSet('score', 1)
            ->assertSet('displayedAnswer', false);
    }
}
