<?php

namespace App\Livewire\About;

use Livewire\Attributes\Title;
use Livewire\Component;

class Index extends Component
{
    #[Title('About')]
    public function render()
    {
        return view('livewire.about.index');
    }
}
