<?php

namespace App\Http\Livewire;

use Livewire\Component;
class Main extends Component
{
    public int $totalToday;

    public int $categories;

    public int $users;

    public int $alltotal;

    public $ticketsByCategory;

    public $ticketsByDate;

    public $y;

    public $m;

    public function render()
    {
        return view('livewire.main');
    }
}
