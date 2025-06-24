<?php

namespace Tests\Feature\Livewire;

use App\Livewire\StudyMode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class StudyModeTest extends TestCase
{
    public function test_renders_successfully()
    {
        Livewire::test(StudyMode::class)
            ->assertStatus(200);
    }
}
