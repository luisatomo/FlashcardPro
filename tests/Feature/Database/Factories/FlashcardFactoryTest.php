<?php

namespace Tests\Feature\Database\Factories;

use App\Models\Deck;
use App\Models\Flashcard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlashcardFactoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_flashcard_with_default_attributes(): void
    {
        $flashcard = Flashcard::factory()->create();

        $this->assertInstanceOf(Flashcard::class, $flashcard);
        $this->assertNotEmpty($flashcard->uuid);
        $this->assertNotEmpty($flashcard->question);
        $this->assertNotEmpty($flashcard->answer);
        $this->assertIsBool($flashcard->public);
        $this->assertNotNull($flashcard->deck_id);
        $this->assertInstanceOf(Deck::class, $flashcard->deck);
    }

    public function test_generates_unique_uuids(): void
    {
        $flashcard1 = Flashcard::factory()->create();
        $flashcard2 = Flashcard::factory()->create();

        $this->assertNotEquals($flashcard1->uuid, $flashcard2->uuid);
        $this->assertIsString($flashcard1->uuid);
        $this->assertIsString($flashcard2->uuid);
    }

    public function test_generates_question_ending_with_question_mark(): void
    {
        $flashcard = Flashcard::factory()->create();

        $this->assertStringEndsWith('?', $flashcard->question);
    }

    public function test_generates_paragraph_answer(): void
    {
        $flashcard = Flashcard::factory()->create();

        $this->assertNotEmpty($flashcard->answer);
        $this->assertIsString($flashcard->answer);
        // Answer should be longer than a single word (paragraph)
        $this->assertGreaterThan(10, strlen($flashcard->answer));
    }

    public function test_public_has_30_percent_chance(): void
    {
        // Create many flashcards to test the probability
        $flashcards = Flashcard::factory()->count(100)->create();

        $publicCount = $flashcards->where('public', true)->count();

        // Should be roughly 30% (allow some variance)
        $this->assertGreaterThan(15, $publicCount);
        $this->assertLessThan(45, $publicCount);
    }

    public function test_creates_associated_deck_by_default(): void
    {
        $flashcard = Flashcard::factory()->create();

        $this->assertInstanceOf(Deck::class, $flashcard->deck);
        $this->assertEquals($flashcard->deck_id, $flashcard->deck->id);
    }

    public function test_public_state_creates_public_flashcard(): void
    {
        $flashcard = Flashcard::factory()->public()->create();

        $this->assertTrue($flashcard->public);
    }

    public function test_private_state_creates_private_flashcard(): void
    {
        $flashcard = Flashcard::factory()->private()->create();

        $this->assertFalse($flashcard->public);
    }

    public function test_for_deck_state_creates_flashcard_for_specific_deck(): void
    {
        $user = User::factory()->create();
        $deck = Deck::factory()->create(['user_id' => $user->id]);

        $flashcard = Flashcard::factory()->forDeck($deck)->create();

        $this->assertEquals($deck->id, $flashcard->deck_id);
        $this->assertEquals($deck->id, $flashcard->deck->id);
    }

    public function test_simple_state_creates_short_content(): void
    {
        $flashcard = Flashcard::factory()->simple()->create();

        // Simple question should be shorter (3 words + ?)
        $questionWords = str_word_count($flashcard->question);
        $this->assertLessThanOrEqual(4, $questionWords); // 3 words + ?

        // Simple answer should be a single word
        $answerWords = str_word_count($flashcard->answer);
        $this->assertEquals(1, $answerWords);
    }

    public function test_language_state_creates_language_learning_flashcard(): void
    {
        $flashcard = Flashcard::factory()->language()->create();

        $this->assertStringStartsWith('What does "', $flashcard->question);
        $this->assertStringEndsWith('" mean?', $flashcard->question);
        $this->assertNotEmpty($flashcard->answer);
        $this->assertIsString($flashcard->answer);
    }

    public function test_can_combine_states(): void
    {
        $user = User::factory()->create();
        $deck = Deck::factory()->create(['user_id' => $user->id]);

        $flashcard = Flashcard::factory()
            ->public()
            ->forDeck($deck)
            ->create();

        $this->assertTrue($flashcard->public);
        $this->assertEquals($deck->id, $flashcard->deck_id);
    }

    public function test_can_combine_simple_and_public_states(): void
    {
        $flashcard = Flashcard::factory()
            ->simple()
            ->public()
            ->create();

        $this->assertTrue($flashcard->public);

        // Check simple state characteristics
        $questionWords = str_word_count($flashcard->question);
        $this->assertLessThanOrEqual(4, $questionWords);

        $answerWords = str_word_count($flashcard->answer);
        $this->assertEquals(1, $answerWords);
    }

    public function test_can_combine_language_and_private_states(): void
    {
        $flashcard = Flashcard::factory()
            ->language()
            ->private()
            ->create();

        $this->assertFalse($flashcard->public);
        $this->assertStringStartsWith('What does "', $flashcard->question);
        $this->assertStringEndsWith('" mean?', $flashcard->question);
    }

    public function test_creates_multiple_flashcards_for_same_deck(): void
    {
        $user = User::factory()->create();
        $deck = Deck::factory()->create(['user_id' => $user->id]);

        $flashcards = Flashcard::factory()
            ->forDeck($deck)
            ->count(5)
            ->create();

        $this->assertCount(5, $flashcards);

        foreach ($flashcards as $flashcard) {
            $this->assertEquals($deck->id, $flashcard->deck_id);
        }
    }

    public function test_creates_mix_of_public_and_private_flashcards(): void
    {
        $publicFlashcards = Flashcard::factory()->public()->count(3)->create();
        $privateFlashcards = Flashcard::factory()->private()->count(3)->create();

        $this->assertCount(3, $publicFlashcards);
        $this->assertCount(3, $privateFlashcards);

        foreach ($publicFlashcards as $flashcard) {
            $this->assertTrue($flashcard->public);
        }

        foreach ($privateFlashcards as $flashcard) {
            $this->assertFalse($flashcard->public);
        }
    }

    public function test_creates_different_types_of_flashcards(): void
    {
        $simpleFlashcard = Flashcard::factory()->simple()->create();
        $languageFlashcard = Flashcard::factory()->language()->create();
        $regularFlashcard = Flashcard::factory()->create();

        // Simple flashcard
        $this->assertLessThanOrEqual(4, str_word_count($simpleFlashcard->question));
        $this->assertEquals(1, str_word_count($simpleFlashcard->answer));

        // Language flashcard
        $this->assertStringStartsWith('What does "', $languageFlashcard->question);

        // Regular flashcard
        $this->assertStringEndsWith('?', $regularFlashcard->question);
        $this->assertNotEmpty($regularFlashcard->answer);
    }

    public function test_factory_respects_explicit_attributes(): void
    {
        $user = User::factory()->create();
        $deck = Deck::factory()->create(['user_id' => $user->id]);

        $flashcard = Flashcard::factory()->create([
            'question' => 'Custom question?',
            'answer' => 'Custom answer',
            'public' => true,
            'deck_id' => $deck->id,
        ]);

        $this->assertEquals('Custom question?', $flashcard->question);
        $this->assertEquals('Custom answer', $flashcard->answer);
        $this->assertTrue($flashcard->public);
        $this->assertEquals($deck->id, $flashcard->deck_id);
    }

    public function test_factory_generates_valid_database_records(): void
    {
        $flashcard = Flashcard::factory()->create();

        $this->assertDatabaseHas('flashcards', [
            'id' => $flashcard->id,
            'uuid' => $flashcard->uuid,
            'question' => $flashcard->question,
            'answer' => $flashcard->answer,
            'public' => $flashcard->public,
            'deck_id' => $flashcard->deck_id,
        ]);
    }

    public function test_can_make_flashcard_without_persisting(): void
    {
        $flashcard = Flashcard::factory()->make();

        $this->assertInstanceOf(Flashcard::class, $flashcard);
        $this->assertNull($flashcard->id); // Not persisted
        $this->assertNotEmpty($flashcard->question);
        $this->assertNotEmpty($flashcard->answer);
    }
}
