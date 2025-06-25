<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Deck;
use App\Models\Flashcard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ApiEndpointsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test user
        $this->user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123')
        ]);

        // Generate token for authenticated requests
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    /**
     * Test user login endpoint
     */
    public function test_user_can_login(): void
    {
        $loginData = [
            'email' => 'test@example.com',
            'password' => 'password123'
        ];

        $response = $this->postJson('/api/auth/login', $loginData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'token',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at'
                ],
                'message'
            ])
            ->assertJson([
                'message' => 'Login successful'
            ]);
    }

    /**
     * Test login with invalid credentials
     */
    public function test_login_fails_with_invalid_credentials(): void
    {
        $invalidData = [
            'email' => 'test@example.com',
            'password' => 'wrongpassword'
        ];

        $response = $this->postJson('/api/auth/login', $invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test last public deck endpoint (authenticated)
     */
    public function test_authenticated_user_can_get_last_public_deck(): void
    {
        // Create public decks
        $publicDeck1 = Deck::factory()->create([
            'user_id' => $this->user->id,
            'public' => true,
            'created_at' => now()->subHour()
        ]);

        $publicDeck2 = Deck::factory()->create([
            'user_id' => $this->user->id,
            'public' => true,
            'created_at' => now()
        ]);

        // Create private deck (should not be returned)
        Deck::factory()->create([
            'user_id' => $this->user->id,
            'public' => false
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->getJson('/api/last-public-deck');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'uuid',
                    'name',
                    'public',
                    'created_at',
                    'updated_at'
                ]
            ]);

        // Should return the most recent public deck
        $responseData = $response->json('data');
        $this->assertEquals($publicDeck2->uuid, $responseData['uuid']);
    }

    /**
     * Test last public deck with no public decks
     */
    public function test_last_public_deck_returns_null_when_no_public_decks(): void
    {
        // Create only private decks
        Deck::factory()->create([
            'user_id' => $this->user->id,
            'public' => false
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->getJson('/api/last-public-deck');

        $response->assertStatus(200)
            ->assertJson([
                'data' => null
            ]);
    }

    /**
     * Test last public deck requires authentication
     */
    public function test_last_public_deck_requires_authentication(): void
    {
        $response = $this->getJson('/api/last-public-deck');

        $response->assertStatus(401);
    }

    /**
     * Test public flashcards endpoint (authenticated)
     */
    public function test_authenticated_user_can_get_public_flashcards(): void
    {
        // Create decks
        $publicDeck = Deck::factory()->create([
            'user_id' => $this->user->id,
            'public' => true
        ]);

        $privateDeck = Deck::factory()->create([
            'user_id' => $this->user->id,
            'public' => false
        ]);

        // Create flashcards
        $publicFlashcard = Flashcard::factory()->create([
            'deck_id' => $publicDeck->id,
            'public' => true
        ]);

        $privateFlashcard = Flashcard::factory()->create([
            'deck_id' => $privateDeck->id,
            'public' => false
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->getJson('/api/public-flashcards');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [[
                    'uuid',
                    'question',
                    'answer',
                    'public',
                    'created_at',
                    'updated_at'
                ]]
            ]);

        // Should only return public flashcards
        $flashcards = $response->json();
        $this->assertContains($publicFlashcard->uuid, array_column($flashcards['data'], 'uuid'));
        $this->assertNotContains($privateFlashcard->uuid, array_column($flashcards['data'], 'uuid'));
    }

    /**
     * Test public flashcards requires authentication
     */
    public function test_public_flashcards_requires_authentication(): void
    {
        $response = $this->getJson('/api/public-flashcards');

        $response->assertStatus(401);
    }

    /**
     * Test last public deck with debug parameter
     */
    public function test_last_public_deck_with_debug_parameter(): void
    {
        Deck::factory()->create([
            'user_id' => $this->user->id,
            'public' => true
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->getJson('/api/last-public-deck?debug=debug');

        $response->assertStatus(200);
    }

    /**
     * Test public flashcards with debug parameter
     */
    public function test_public_flashcards_with_debug_parameter(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->getJson('/api/public-flashcards?debug=debug');

        $response->assertStatus(200);
    }
}
