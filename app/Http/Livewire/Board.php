<?php

namespace App\Http\Livewire;

use Illuminate\Support\Collection;
use Livewire\Component;

class Board extends Component
{
    public Collection $tickets;

    public function render()
    {
        return view('livewire.board');
    }
}
