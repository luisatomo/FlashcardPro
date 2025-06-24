<?php

namespace Database\Factories;

use App\Models\Deck;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Flashcard>
 */
class FlashcardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid(),
            'question' => $this->faker->sentence() . '?',
            'answer' => $this->faker->paragraph(),
            'public' => $this->faker->boolean(30), // 30% chance of being public
            'deck_id' => Deck::factory(),
        ];
    }

    /**
     * Indicate that the flashcard should be public.
     */
    public function public(): static
    {
        return $this->state(fn (array $attributes) => [
            'public' => true,
        ]);
    }

    /**
     * Indicate that the flashcard should be private.
     */
    public function private(): static
    {
        return $this->state(fn (array $attributes) => [
            'public' => false,
        ]);
    }

    /**
     * Create a flashcard for a specific deck.
     */
    public function forDeck(Deck $deck): static
    {
        return $this->state(fn (array $attributes) => [
            'deck_id' => $deck->id,
        ]);
    }

    /**
     * Create a simple flashcard with short question and answer.
     */
    public function simple(): static
    {
        return $this->state(fn (array $attributes) => [
            'question' => $this->faker->sentence(3) . '?',
            'answer' => $this->faker->word(),
        ]);
    }

    /**
     * Create a language learning flashcard.
     */
    public function language(): static
    {
        return $this->state(fn (array $attributes) => [
            'question' => 'What does "' . $this->faker->word() . '" mean?',
            'answer' => $this->faker->sentence(),
        ]);
    }
}
