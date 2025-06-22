<?php

namespace App\Livewire;

use Livewire\Component;

class Decks extends Component
{
    public $name = '';
    public $public = false;
    public $decks;
    public function render()
    {
        return view('livewire.decks');
    }
}
