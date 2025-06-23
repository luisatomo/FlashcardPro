<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Flashcards;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class FlashcardsTest extends TestCase
{
    public function test_renders_successfully()
    {
        Livewire::test(Flashcards::class)
            ->assertStatus(200);
    }
}
