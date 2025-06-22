<?php

namespace Database\Factories;

use App\Models\Deck;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Deck>
 */
class DeckFactory extends Factory
{
    protected $model = Deck::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'name' => $this->faker->words(3, true) . ' Deck',
            'public' => $this->faker->boolean(30), // 30% chance of being public
            'user_id' => User::factory(),

        ];
    }

    /**
     * Indicate that the deck should be public.
     */
    public function public(): static
    {
        return $this->state(fn (array $attributes) => [
            'public' => true,
        ]);
    }

    /**
     * Indicate that the deck should be private.
     */
    public function private(): static
    {
        return $this->state(fn (array $attributes) => [
            'public' => false,
        ]);
    }

    /**
     * Set a custom name for the deck.
     */
    public function withName(string $name): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => $name,
        ]);
    }

    /**
     * Set a custom description for the deck.
     */
    public function withDescription(string $description): static
    {
        return $this->state(fn (array $attributes) => [
            'description' => $description,
        ]);
    }

    /**
     * Create a deck for a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Create a deck with a specific UUID.
     */
    public function withUuid(string $uuid): static
    {
        return $this->state(fn (array $attributes) => [
            'uuid' => $uuid,
        ]);
    }


}
