<?php

namespace Database\Seeders;

use App\Models\Deck;
use App\Models\User;
use Illuminate\Database\Seeder;

class DeckSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        // Create public educational decks
        $publicDecks = [
            [
                'name' => 'Spanish Vocabulary - Basics',
                'public' => true,
            ],
            [
                'name' => 'JavaScript Fundamentals',
                'public' => true,
            ],
            [
                'name' => 'World Capitals',
                'public' => true,
            ],
            [
                'name' => 'Basic Math Formulas',
                'public' => true,
            ],
            [
                'name' => 'English Grammar Rules',
                'public' => true,
            ],
            [
                'name' => 'Chemistry Elements',
                'public' => true,
            ],
        ];

        foreach ($publicDecks as $deckData) {
            Deck::factory()->create([
                'name' => $deckData['name'],
                'public' => $deckData['public'],
                'user_id' => $users->random()->id,
            ]);
        }

        // Create private decks for each user
        foreach ($users as $user) {
            // Each user gets 2-4 private decks
            $privateDecks = [
                'My Personal Study Notes',
                'Exam Preparation',
                'Quick Review Cards',
                'Important Concepts',
                'Work Training Material',
                'Language Practice',
            ];

            $deckCount = rand(2, 4);
            $selectedDecks = collect($privateDecks)->random($deckCount);

            foreach ($selectedDecks as $deckName) {
                Deck::factory()->create([
                    'name' => $deckName,
                    'public' => false,
                    'user_id' => $user->id,
                ]);
            }
        }

        // Create additional random decks
        Deck::factory(10)->create();
    }
}
