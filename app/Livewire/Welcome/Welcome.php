<?php

namespace App\Livewire\Welcome;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

class Welcome extends Component
{
    #[Layout('components.layouts.welcome')]
    #[Title('Jadwal Waktu Sholat')]
    public function render()
    {
        return view('livewire.welcome.welcome');
    }
}
