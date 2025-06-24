<?php

namespace Database\Seeders;

use App\Models\Deck;
use App\Models\Flashcard;
use Illuminate\Database\Seeder;

class FlashcardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $decks = Deck::all();

        // Predefined flashcard sets for specific decks
        $flashcardSets = [
            'Spanish Vocabulary - Basics' => [
                ['question' => 'Hello', 'answer' => 'Hola'],
                ['question' => 'Goodbye', 'answer' => 'Adiós'],
                ['question' => 'Please', 'answer' => 'Por favor'],
                ['question' => 'Thank you', 'answer' => 'Gracias'],
                ['question' => 'Yes', 'answer' => 'Sí'],
                ['question' => 'No', 'answer' => 'No'],
                ['question' => 'Water', 'answer' => 'Agua'],
                ['question' => 'Food', 'answer' => 'Comida'],
                ['question' => 'House', 'answer' => 'Casa'],
                ['question' => 'Car', 'answer' => 'Coche'],
            ],
            'JavaScript Fundamentals' => [
                ['question' => 'What does "var" do in JavaScript?', 'answer' => 'Declares a variable with function scope'],
                ['question' => 'What is the difference between == and ===?', 'answer' => '== compares values, === compares values and types'],
                ['question' => 'What is a closure?', 'answer' => 'A function that has access to variables in its outer scope'],
                ['question' => 'What does "this" refer to?', 'answer' => 'The object that owns the currently executing code'],
                ['question' => 'What is hoisting?', 'answer' => 'JavaScript\'s behavior of moving declarations to the top'],
                ['question' => 'What is the difference between null and undefined?', 'answer' => 'null is assigned, undefined means no value has been assigned'],
                ['question' => 'What is an arrow function?', 'answer' => 'A shorter way to write functions using => syntax'],
                ['question' => 'What is the DOM?', 'answer' => 'Document Object Model - representation of HTML elements'],
            ],
            'World Capitals' => [
                ['question' => 'Capital of France', 'answer' => 'Paris'],
                ['question' => 'Capital of Japan', 'answer' => 'Tokyo'],
                ['question' => 'Capital of Brazil', 'answer' => 'Brasília'],
                ['question' => 'Capital of Australia', 'answer' => 'Canberra'],
                ['question' => 'Capital of Canada', 'answer' => 'Ottawa'],
                ['question' => 'Capital of Germany', 'answer' => 'Berlin'],
                ['question' => 'Capital of India', 'answer' => 'New Delhi'],
                ['question' => 'Capital of Egypt', 'answer' => 'Cairo'],
                ['question' => 'Capital of Russia', 'answer' => 'Moscow'],
                ['question' => 'Capital of South Africa', 'answer' => 'Cape Town (legislative)'],
            ],
            'Basic Math Formulas' => [
                ['question' => 'Area of a circle', 'answer' => 'π × r²'],
                ['question' => 'Pythagorean theorem', 'answer' => 'a² + b² = c²'],
                ['question' => 'Quadratic formula', 'answer' => 'x = (-b ± √(b²-4ac)) / 2a'],
                ['question' => 'Area of a triangle', 'answer' => '½ × base × height'],
                ['question' => 'Circumference of a circle', 'answer' => '2πr or πd'],
                ['question' => 'Distance formula', 'answer' => 'd = √[(x₂-x₁)² + (y₂-y₁)²]'],
                ['question' => 'Slope formula', 'answer' => 'm = (y₂-y₁)/(x₂-x₁)'],
            ],
            'Chemistry Elements' => [
                ['question' => 'Symbol for Hydrogen', 'answer' => 'H'],
                ['question' => 'Symbol for Helium', 'answer' => 'He'],
                ['question' => 'Symbol for Carbon', 'answer' => 'C'],
                ['question' => 'Symbol for Oxygen', 'answer' => 'O'],
                ['question' => 'Symbol for Gold', 'answer' => 'Au'],
                ['question' => 'Symbol for Silver', 'answer' => 'Ag'],
                ['question' => 'Symbol for Iron', 'answer' => 'Fe'],
                ['question' => 'Symbol for Sodium', 'answer' => 'Na'],
                ['question' => 'Atomic number of Carbon', 'answer' => '6'],
                ['question' => 'Atomic number of Oxygen', 'answer' => '8'],
            ],
        ];

        foreach ($decks as $deck) {
            if (isset($flashcardSets[$deck->name])) {
                // Create predefined flashcards for specific decks
                foreach ($flashcardSets[$deck->name] as $flashcardData) {
                    Flashcard::factory()->create([
                        'deck_id' => $deck->id,
                        'question' => $flashcardData['question'],
                        'answer' => $flashcardData['answer'],
                        'public' => $deck->public,
                    ]);
                }
            } else {
                // Create random flashcards for other decks
                $flashcardCount = rand(5, 15);
                Flashcard::factory($flashcardCount)->create([
                    'deck_id' => $deck->id,
                    'public' => $deck->public,
                ]);
            }
        }
    }
}
