<?php

namespace App\Livewire\Dashboard;

use Livewire\Attributes\Title;
use Livewire\Component;

class Index extends Component
{
    #[Title('Dashboard')]

    public function render()
    {
        return view('livewire.dashboard.index');
    }
}
